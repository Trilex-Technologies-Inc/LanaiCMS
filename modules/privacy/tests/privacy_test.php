<?php
if (PHP_SAPI!=='cli') exit;
require dirname(__DIR__,2).'/explorer/tests/support.php';
require dirname(__DIR__).'/module.php';
require dirname(__DIR__,3).'/include/lanai/class.analytics.php';
function privacyCheck($ok,$label) { if (!$ok) throw new RuntimeException($label); }
class PrivacyDb extends ExplorerTestDb {
    public $writes=0;
    function Execute($sql,$params=array()) {
        if (strpos($sql,'INSERT INTO test_analytics_event')===0) { $this->writes++; return true; }
        $sql=str_replace(' ENGINE=InnoDB','',$sql);
        $sql=preg_replace('/, KEY [a-z_]+ \([^)]*\)/','',$sql);
        $sql=preg_replace('/ON DUPLICATE KEY UPDATE privacyRevision=privacyRevision\+1,privacyConfig=.*$/','ON CONFLICT(privacyId) DO UPDATE SET privacyRevision=privacyRevision+1,privacyConfig=excluded.privacyConfig',$sql);
        try { return parent::Execute($sql); } catch (PDOException $e) { return false; }
    }
}
class PrivacySystem extends ExplorerTestSystem { function getCsrfToken($scope) { return 'test-token'; } }
$db=new PrivacyDb(); $sys_lanai=new PrivacySystem(); $cfg=array('url'=>'https://example.com/cms','lang'=>'english'); $_COOKIE=array();
$lanaiPrivacy=new LanaiPrivacy($db,'test_',$cfg['url']);
privacyCheck(!$lanaiPrivacy->allows('analytics') && $lanaiPrivacy->choices()===null,'No consent denies tracking even before schema setup');
$db->Execute(LanaiPrivacy::schema('test_privacy_settings'));
$lanaiPrivacy=new LanaiPrivacy($db,'test_',$cfg['url']);
$_SERVER=array('REQUEST_METHOD'=>'GET','SCRIPT_NAME'=>'/cms/index.php','REQUEST_URI'=>'/cms/','REMOTE_ADDR'=>'127.0.0.1','HTTP_USER_AGENT'=>'Test'); $_GET=array();
$analytics=new LanaiAnalytics($db,'test_'); $analytics->trackRequest();
privacyCheck($db->writes===0,'Analytics does not write before consent');
$input=array('revision'=>'1','csrf_token'=>'test-token','analytics'=>'1','external'=>'0','marketing'=>'0');
$selection=$lanaiPrivacy->validateRequest('POST',$input,$sys_lanai);
$_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection);
$analytics->trackRequest(); privacyCheck($db->writes===1,'Analytics writes after opt-in');
foreach (array(array('GET',$input,405),array('POST',array_merge($input,array('csrf_token'=>'bad')),403),array('POST',array_merge($input,array('revision'=>'99')),409)) as $case) {
    try { $lanaiPrivacy->validateRequest($case[0],$case[1],$sys_lanai); throw new LogicException('Invalid request accepted'); }
    catch (InvalidArgumentException $e) { privacyCheck($e->getCode()===$case[2],'Method, CSRF, and stale revision rejected'); }
}
foreach (array(array('analytics'=>array('1')),array('external'=>'true')) as $bad) {
    try { $lanaiPrivacy->selection(array_merge($input,$bad)); throw new LogicException('Invalid category accepted'); } catch (InvalidArgumentException $e) {}
}
$selection['analytics']=false; $_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection); $analytics->trackRequest();
privacyCheck($db->writes===1,'Withdrawal prevents further writes');
$selection['time']=time()-LanaiPrivacy::DAYS*86400-1; $_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection);
privacyCheck($lanaiPrivacy->choices()===null,'Expired preferences require new choice');
$config=LanaiPrivacy::defaults();
foreach (LanaiPrivacy::CATEGORIES as $category) { $config[$category.'Scripts']=''; $config[$category.'Cookies']=''; }
$config['analyticsScripts']='Analytics provider | https://analytics.example/tracker.js'; $config['analyticsCookies']='_example';
$lanaiPrivacy->saveConfig($config);
privacyCheck($lanaiPrivacy->config()['revision']===2 && !$lanaiPrivacy->allows('analytics'),'Config save invalidates old consent');
$selection=$lanaiPrivacy->selection(array('analytics'=>'0','external'=>'0','marketing'=>'0')); $_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection);
$source='<!doctype html><html><head><script src="assets/core.js"></script><script src="https://analytics.example/tracker.js"></script><script data-lanai-consent="marketing">window.marketing=true;</script><script src="https://challenges.cloudflare.com/turnstile/v0/api.js"></script><link rel="preconnect" href="https://video.example"></head><body><p>ไทย &amp; content</p><iframe src="https://video.example/embed"></iframe><iframe srcdoc="&lt;script src=&quot;https://tracker.example/x.js&quot;&gt;&lt;/script&gt;"></iframe></body></html>';
$filtered=$lanaiPrivacy->filterHtml($source);
privacyCheck(strpos($filtered,'tracker.js')===false && strpos($filtered,'window.marketing')===false && strpos($filtered,'<iframe')===false,'Optional external and inline resources removed before delivery');
privacyCheck(strpos($filtered,'preconnect')===false && strpos($filtered,'data-privacy-open')!==false,'Preconnect blocked and media placeholder shown');
privacyCheck(strpos($filtered,'assets/core.js')!==false && strpos($filtered,'turnstile/v0/api.js')!==false && strpos(html_entity_decode($filtered,ENT_QUOTES,'UTF-8'),'ไทย')!==false,'Necessary resources and UTF-8 content preserved');
$selection['external']=true; $_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection); $filtered=$lanaiPrivacy->filterHtml($source);
privacyCheck(strpos($filtered,'video.example/embed')!==false && strpos($filtered,'tracker.js')===false,'External media opt-in does not allow registered analytics');
$selection['analytics']=true; $_COOKIE[$lanaiPrivacy->cookieName()]=json_encode($selection); $filtered=$lanaiPrivacy->filterHtml($source);
privacyCheck(strpos($filtered,'tracker.js')!==false && strpos($filtered,'window.marketing')===false,'Categories enforced independently');
$banner=include dirname(__DIR__).'/banner.php';
privacyCheck(strpos($banner,'data-privacy-choice="accept"')!==false && strpos($banner,'data-privacy-choice="reject"')!==false && strpos($banner,'<noscript>')!==false,'Banner provides accept/reject and no-JS link');
$out=lanai_privacy_output($source); privacyCheck(substr_count($out,'id="lanai-privacy"')===1,'Public output contains one banner');
privacyCheck($lanaiPrivacy->path()==='/cms/' && $lanaiPrivacy->cookieName()!==(new LanaiPrivacy($db,'test_','https://example.com/other'))->cookieName(),'Subdirectory sites have isolated preference cookies');
foreach (array('javascript:alert(1)','//attacker.example','https://user:pass@example.com','/\\evil.example') as $bad) privacyCheck(!LanaiPrivacy::validUrl($bad,true),'Unsafe policy URL rejected');
$_SERVER['SCRIPT_NAME']='/cms/module.php'; $_GET=array('modname'=>'member'); $analytics->trackRequest(); privacyCheck($db->writes===1,'Member pages excluded from analytics');
echo "Privacy consent, CSRF, versioning, analytics enforcement, resource blocking, and banner checks passed.\n";

// Exercise the actual output-buffer callback, not just the renderer.
ob_start(); ob_start('lanai_privacy_output'); echo $source; ob_end_flush(); $buffered=ob_get_clean();
privacyCheck(substr_count($buffered,'id="lanai-privacy"')===1 && strpos($buffered,'content')!==false,'Output callback retains page and inserts one banner');
$_SERVER['PHP_SELF']='/cms/module.php'; $_REQUEST=array('modname'=>'privacy'); $_GET=array();
function privacyPage() { global $lanaiPrivacy,$sys_lanai,$cfg; ob_start(); include dirname(__DIR__).'/index.php'; return ob_get_clean(); }
$page=privacyPage();
privacyCheck(strpos($page,'action="privacy.php"')!==false && strpos($page,'test-token')!==false && strpos($page,'name="revision"')!==false,'No-JavaScript form includes endpoint, token, and policy revision');
$config['privacyText']='<script>bad()</script>';
$lanaiPrivacy->saveConfig($config); $_GET=array('view'=>'privacy');
privacyCheck(strpos(privacyPage(),'&lt;script&gt;bad()&lt;/script&gt;')!==false,'Policy text is escaped');
$badConfig=$config; $badConfig['analyticsCookies']=session_name();
try { $lanaiPrivacy->saveConfig($badConfig); throw new LogicException('Session cookie classified optional'); } catch (InvalidArgumentException $e) {}
$badConfig=$config; $badConfig['marketingScripts']='Bad | javascript:alert(1)';
try { $lanaiPrivacy->saveConfig($badConfig); throw new LogicException('Unsafe script accepted'); } catch (InvalidArgumentException $e) {}
echo "Privacy public page, output-buffer integration, and configuration validation checks passed.\n";
