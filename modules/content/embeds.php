<?php
/** Render only explicit embed placeholders; never evaluate arbitrary module paths. */
class LanaiContentEmbeds
{
    private $db; private $prefix; private $system; private $thai;
    public function __construct($db, $prefix, $system, $thai = false) { $this->db=$db; $this->prefix=$prefix; $this->system=$system; $this->thai=$thai; }
    private function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
    private function text($en,$th) { return $this->thai ? $th : $en; }
    public function item($type, $id, $cid = 0) {
        if (!in_array($type,array('poll','contact'),true) || !ctype_digit((string)$id) || (int)$id<1) return '';
        $id=(int)$id;
        if ($type==='contact') {
            $rs=$this->db->Execute('SELECT * FROM '.$this->prefix.'contact WHERE conId='.$id." AND conActive='y'");
            if (!$rs || $rs->EOF) return '';
            $r=$rs->fields;
            $html='<aside class="card my-3 lanai-contact"><div class="card-body"><h3 class="h5">'.$this->e(trim($r['conFname'].' '.$r['conLname'])).'</h3>';
            foreach (array('conPosition','conAddress1','conAddress2','conCity','conState','conZipcode','conPhone','conMobile','conFax') as $field) {
                if (!empty($r[$field])) $html.='<div>'.$this->e($r[$field]).'</div>';
            }
            if (filter_var($r['conEmail'] ?? '',FILTER_VALIDATE_EMAIL)) $html.='<div><a href="mailto:'.$this->e($r['conEmail']).'">'.$this->e($r['conEmail']).'</a></div>';
            $url=$r['conURL'] ?? '';
            if (filter_var($url,FILTER_VALIDATE_URL) && in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),array('http','https'),true)) $html.='<a href="'.$this->e($url).'" rel="noopener">'.$this->e($url).'</a>';
            return $html.'</div></aside>';
        }
        $rs=$this->db->Execute('SELECT * FROM '.$this->prefix.'poll WHERE pllId='.$id." AND pllActive='y'");
        if (!$rs || $rs->EOF) return '';
        $options=$this->db->Execute('SELECT ppoId,ppoTitle,ppoScore FROM '.$this->prefix.'poll_option WHERE pllId='.$id." AND ppoTitle<>'' ORDER BY ppoId");
        if (!$options || $options->EOF) return '';
        $html='<section class="card my-3 lanai-poll"><div class="card-body"><h3 class="h5">'.$this->e($rs->fields['pllTitle']).'</h3><form method="post" action="module.php">';
        $html.='<input type="hidden" name="modname" value="poll"><input type="hidden" name="mf" value="pllvote"><input type="hidden" name="mid" value="'.$id.'"><input type="hidden" name="cid" value="'.(int)$cid.'"><input type="hidden" name="csrf_token" value="'.$this->e($this->system->getCsrfToken('poll_vote')).'">';
        $results='';
        while (!$options->EOF) {
            $r=$options->fields;
            $html.='<label class="d-block mb-2"><input type="radio" name="voteChoice" value="'.(int)$r['ppoId'].'" required> '.$this->e($r['ppoTitle']).'</label>';
            $results.='<li>'.$this->e($r['ppoTitle']).': '.(int)$r['ppoScore'].'</li>';
            $options->moveNext();
        }
        return $html.'<button class="btn btn-primary" type="submit">'.$this->text('Vote','โหวต').'</button></form><details class="mt-3"><summary>'.$this->text('Results','ผลโหวต').'</summary><ul>'.$results.'</ul></details></div></section>';
    }
    public function render($html, $cid=0) {
        if (strpos($html,'data-lanai-embed')===false) return $html;
        $previous=libxml_use_internal_errors(true);
        try {
            $doc=new DOMDocument();
            $doc->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>',LIBXML_NONET);
            $xpath=new DOMXPath($doc);
            $nodes=$xpath->query('//div[@data-lanai-embed]');
            foreach (iterator_to_array($nodes) as $node) {
                $rendered=$this->item($node->getAttribute('data-lanai-embed'),$node->getAttribute('data-lanai-id'),$cid);
                $fragment=$doc->createDocumentFragment();
                if ($rendered!=='') {
                    $other=new DOMDocument();
                    $other->loadHTML('<?xml encoding="UTF-8"><html><body>'.$rendered.'</body></html>',LIBXML_NONET);
                    foreach (iterator_to_array($other->getElementsByTagName('body')->item(0)->childNodes) as $child) $fragment->appendChild($doc->importNode($child,true));
                }
                $node->parentNode->replaceChild($fragment,$node);
            }
            $out='';
            foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $child) $out.=$doc->saveHTML($child);
            return $out;
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
    }
}
