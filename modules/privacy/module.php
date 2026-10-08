<?php
require_once __DIR__.'/data.php';
/** Privacy preferences and configuration; no compliance certification implied. */
class LanaiPrivacy
{
    const DAYS = 180;
    const CATEGORIES = array('analytics', 'external', 'marketing');
    private $db; private $table; private $url; private $config; private $available=false; private $data;
    public function __construct($db, $prefix, $url) {
        $this->db=$db; $this->table=$prefix.'privacy_settings'; $this->url=rtrim($url,'/');
        $this->data=new LanaiPrivacyData($db,$prefix);
        $this->config=self::defaults();
        $rs=$db->Execute('SELECT * FROM '.$this->table.' WHERE privacyId=1');
        $this->available=(bool)$rs;
        if ($rs && !$rs->EOF) {
            $data=json_decode($rs->fields['privacyConfig'],true);
            if (is_array($data)) $this->config=array_merge($this->config,$data);
            $this->config['revision']=(int)$rs->fields['privacyRevision'];
        }
    }
    public static function defaults() { return array('revision'=>1,'privacyText'=>'','cookieText'=>'','privacyUrl'=>'','cookieUrl'=>'','scripts'=>array(),'cookies'=>array(),'analyticsDays'=>90,'pollDays'=>7,'consentDays'=>365,'auditDays'=>365,'requestDays'=>365); }
    public static function schema($table) { return 'CREATE TABLE IF NOT EXISTS '.$table.' (privacyId INT NOT NULL PRIMARY KEY, privacyRevision BIGINT NOT NULL, privacyConfig TEXT NOT NULL) ENGINE=InnoDB'; }
    public function config() { return $this->config; }
    public function path() { return rtrim((string)parse_url($this->url,PHP_URL_PATH),'/').'/'; }
    public function cookieName() { return 'lanai_consent_'.substr(hash('sha256',$this->path()),0,10); }
    public function choices($cookies=null, $now=null) {
        if (!$this->available) return null;
        $now=$now??time(); $cookies=$cookies??$_COOKIE;
        $raw=$cookies[$this->cookieName()]??'';
        if (!is_string($raw) || strlen($raw)>1000) return null;
        $data=json_decode($raw,true);
        if (!is_array($data) || ($data['revision']??null)!==$this->config['revision'] || !is_int($data['time']??null)
            || $data['time']>$now || $data['time']<$now-self::DAYS*86400) return null;
        foreach (self::CATEGORIES as $category) if (!is_bool($data[$category]??null)) return null;
        return $data;
    }
    public function allows($category) { return in_array($category,self::CATEGORIES,true) && (($this->choices()[$category]??false)===true); }
    public function selection($input, $now=null) {
        $out=array('revision'=>$this->config['revision'],'time'=>$now??time());
        foreach (self::CATEGORIES as $category) {
            if (!isset($input[$category]) || !in_array($input[$category],array('0','1'),true)) throw new InvalidArgumentException('Invalid preferences.');
            $out[$category]=$input[$category]==='1';
        }
        return $out;
    }
    public function validateRequest($method, $input, $system) {
        if (!$this->available) throw new RuntimeException('Privacy settings are unavailable.');
        if ($method!=='POST') throw new InvalidArgumentException('POST required.',405);
        if (!is_string($input['csrf_token']??null) || !$system->validateCsrfToken('privacy',$input['csrf_token'])) throw new InvalidArgumentException('Invalid request.',403);
        if (!is_string($input['revision']??null) || $input['revision']!==(string)$this->config['revision']) throw new InvalidArgumentException('Policy changed.',409);
        return $this->selection($input);
    }
    public function store($selection, $uid=0) {
        if (headers_sent()) return false;
        // Store evidence before allowing resources. Each choice has a fresh, unlinked receipt.
        $selection['receipt']=$this->data->recordConsent($selection,$this->config,$uid);
        $options=array('expires'=>time()+self::DAYS*86400,'path'=>$this->path(),'secure'=>parse_url($this->url,PHP_URL_SCHEME)==='https','httponly'=>true,'samesite'=>'Lax');
        if (!setcookie($this->cookieName(),json_encode($selection),$options)) return false;
        $_COOKIE[$this->cookieName()]=json_encode($selection);
        // Clear listed first-party cookies when the category is declined.
        foreach ($this->config['cookies'] as $cookie) {
            if (empty($selection[$cookie['category']])) {
                $expired=$options; $expired['expires']=1; $expired['httponly']=false;
                setcookie($cookie['name'],'',$expired);
                if ($expired['path']!=='/') { $expired['path']='/'; setcookie($cookie['name'],'',$expired); }
                unset($_COOKIE[$cookie['name']]);
            }
        }
        return true;
    }
    public static function validUrl($url, $local=false) {
        if ($url==='') return $local;
        if (preg_match('/[\x00-\x20\\\\]/',$url)) return false;
        if ($local && preg_match('~^(?:module\.php\?|index\.php(?:\?|$)|/(?!/))~',$url)) return true;
        return filter_var($url,FILTER_VALIDATE_URL)!==false && parse_url($url,PHP_URL_SCHEME)==='https' && !parse_url($url,PHP_URL_USER) && !parse_url($url,PHP_URL_PASS);
    }
    public function saveConfig($input) {
        $new=self::defaults();
        foreach (array('analyticsDays','pollDays','consentDays','auditDays','requestDays') as $field) {
            $value=$input[$field]??$this->config[$field];
            if ((!is_string($value) && !is_int($value)) || !preg_match('/^[0-9]{1,4}$/D',(string)$value) || (int)$value<1 || (int)$value>3650) throw new InvalidArgumentException('Retention must be between 1 and 3650 days.');
            $new[$field]=(int)$value;
        }
        foreach (array('privacyText','cookieText','privacyUrl','cookieUrl') as $field) {
            if (!isset($input[$field]) || !is_string($input[$field]) || strlen($input[$field])>30000) throw new InvalidArgumentException('Invalid policy field.');
            $new[$field]=trim($input[$field]);
        }
        if (!self::validUrl($new['privacyUrl'],true) || !self::validUrl($new['cookieUrl'],true)) throw new InvalidArgumentException('Policy links must be local URLs or HTTPS URLs.');
        foreach (self::CATEGORIES as $category) {
            $value=$input[$category.'Scripts']??'';
            if (!is_string($value) || strlen($value)>16000) throw new InvalidArgumentException('Invalid script list.');
            foreach (preg_split('/\R/',$value) as $line) {
                if (trim($line)==='') continue;
                $parts=explode('|',$line,2);
                if (count($parts)!==2 || trim($parts[0])==='' || !self::validUrl(trim($parts[1]))) throw new InvalidArgumentException('Each script must be Name | https://address.');
                $new['scripts'][]=array('name'=>trim($parts[0]),'url'=>trim($parts[1]),'category'=>$category);
            }
            $value=$input[$category.'Cookies']??'';
            if (!is_string($value) || strlen($value)>4000) throw new InvalidArgumentException('Invalid cookie list.');
            foreach (preg_split('/[\s,]+/',trim($value)) as $name) {
                if ($name==='') continue;
                if (!preg_match('/^[a-zA-Z0-9_.-]{1,80}$/D',$name) || $name===session_name() || strpos($name,'lanai_consent_')===0) throw new InvalidArgumentException('Invalid optional cookie name.');
                $new['cookies'][]=array('name'=>$name,'category'=>$category);
            }
        }
        if (!$this->db->Execute(self::schema($this->table))) throw new RuntimeException('Privacy settings storage unavailable.');
        $this->data->install();
        unset($new['revision']);
        $json=$this->db->qstr(json_encode($new,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
        $sql='INSERT INTO '.$this->table.' (privacyId,privacyRevision,privacyConfig) VALUES (1,2,'.$json.') ON DUPLICATE KEY UPDATE privacyRevision=privacyRevision+1,privacyConfig='.$json;
        if (!$this->db->Execute($sql)) throw new RuntimeException('Privacy settings could not be saved.');
        $rs=$this->db->Execute('SELECT privacyRevision FROM '.$this->table.' WHERE privacyId=1');
        if (!$rs || $rs->EOF) throw new RuntimeException('Privacy settings could not be read.');
        $new['revision']=(int)$rs->fields['privacyRevision']; $this->config=$new; $this->available=true;
    }
    public function policyUrl($kind) { return $this->config[$kind.'Url'] ?: 'module.php?modname=privacy&view='.$kind; }
    private function externalUrl($url) {
        $url=trim($url);
        if (strpos($url,'//')===0) $url='https:'.$url;
        $host=parse_url($url,PHP_URL_HOST);
        return $host && (strtolower($host)!==strtolower((string)parse_url($this->url,PHP_URL_HOST)) || parse_url($url,PHP_URL_PORT)!==parse_url($this->url,PHP_URL_PORT));
    }
    /** Runs before public HTML is sent; unconsented resources never reach the browser. */
    public function filterHtml($html) {
        if (!preg_match('/<(?:script|iframe|object|embed|link)\b/i',$html)) return $html;
        $old=libxml_use_internal_errors(true);
        try {
            $doc=new DOMDocument();
            if (!$doc->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET)) return '';
            $xpath=new DOMXPath($doc);
            foreach (iterator_to_array($xpath->query('//script|//iframe|//object|//embed|//link[@rel="preconnect" or @rel="dns-prefetch" or @rel="preload" or @rel="modulepreload"]')) as $node) {
                $tag=strtolower($node->nodeName);
                $url=$node->getAttribute($tag==='object'?'data':($tag==='link'?'href':'src'));
                $category=$node->getAttribute('data-lanai-consent');
                foreach ($this->config['scripts'] as $script) if ($script['url']===$url) $category=$script['category'];
                // Cloudflare's configured anti-abuse challenge is necessary for protected forms.
                $security=$tag==='script' && parse_url($url,PHP_URL_HOST)==='challenges.cloudflare.com' && strpos((string)parse_url($url,PHP_URL_PATH),'/turnstile/')===0;
                if ($category==='' && ($this->externalUrl($url) || ($tag==='iframe' && $node->hasAttribute('srcdoc'))) && !$security) $category='external';
                if ($category!=='' && !$this->allows($category)) {
                    if (in_array($tag,array('iframe','object','embed'),true)) {
                        $placeholder=$doc->createElement('div'); $placeholder->setAttribute('class','lanai-privacy-placeholder');
                        $button=$doc->createElement('button','External media is paused. Change cookie settings to load it.');
                        $button->setAttribute('type','button'); $button->setAttribute('data-privacy-open','');
                        $placeholder->appendChild($button); $node->parentNode->replaceChild($placeholder,$node);
                    } else $node->parentNode->removeChild($node);
                }
            }
            $out=$doc->saveHTML();
            return preg_replace('/<\?xml encoding="UTF-8"\s*\??>\s*/','',$out,1);
        } finally { libxml_clear_errors(); libxml_use_internal_errors($old); }
    }
}
function lanai_privacy_output($html) {
    global $lanaiPrivacy,$cfg,$sys_lanai;
    if (!isset($lanaiPrivacy) || stripos($html,'<body')===false) return $html;
    $html=$lanaiPrivacy->filterHtml($html);
    $banner=include __DIR__.'/banner.php';
    $position=strripos($html,'</body>');
    return $position===false ? $html.$banner : substr_replace($html,$banner,$position,0);
}
