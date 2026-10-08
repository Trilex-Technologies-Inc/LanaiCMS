<?php
// Isolated browser fixture. Never available from the production PHP server.
if (PHP_SAPI!=='cli-server' || getenv('LANAI_PRIVACY_BROWSER_TEST')!=='1') { http_response_code(404); exit; }
require dirname(__DIR__).'/module.php';
class PrivacyBrowserDb {
    function Execute($sql) { return (object)array('EOF'=>true,'fields'=>array()); }
    function qstr($value) { return "'".str_replace("'","''",$value)."'"; }
}
class PrivacyBrowserSystem {
    function getCsrfToken($key) { return 'browser-test-token'; }
    function validateCsrfToken($key,$value) { return $key==='privacy' && $value==='browser-test-token'; }
}
$cfg=array('url'=>'http://127.0.0.1:8879','lang'=>'english'); $sys_lanai=new PrivacyBrowserSystem();
$lanaiPrivacy=new LanaiPrivacy(new PrivacyBrowserDb(),'test_',$cfg['url']);
if (isset($_GET['save'])) {
    header('Content-Type: application/json');
    $selection=$lanaiPrivacy->validateRequest($_SERVER['REQUEST_METHOD'],$_POST,$sys_lanai);
    echo json_encode(array('saved'=>$lanaiPrivacy->store($selection)));
    exit;
}
if (isset($_GET['inspect'])) {
    header('Content-Type: application/json');
    echo json_encode($lanaiPrivacy->choices()); exit;
}
$_COOKIE=array();
$markup= <<<'HTML'
<!doctype html><html><head><meta charset="utf-8"><base href="/"><title>Privacy UI test</title></head><body><h1>Isolated privacy UI check</h1>
<iframe src="https://example.invalid/tracker"></iframe><script data-lanai-consent="analytics">window.optionalTrackerRan=true;</script>
<pre id="browser-result">RUNNING</pre>
<script>
window.addEventListener('DOMContentLoaded', async function () {
    const result=document.getElementById('browser-result');
    try {
        if (document.querySelector('iframe') || window.optionalTrackerRan) throw new Error('Optional resource escaped blocking');
        const panel=document.getElementById('privacy-panel');
        if (panel.hidden) throw new Error('Initial banner hidden');
        document.getElementById('privacy-manage').click();
        if (document.getElementById('privacy-options').hidden) throw new Error('Preferences did not open');
        if (document.querySelector('#privacy-options input:checked')) throw new Error('Optional category preselected');
        const realFetch=window.fetch;
        let submitted;
        window.fetch=async function(url,options) { submitted=new URLSearchParams(options.body); return {ok:false,json:async function(){ return {saved:false}; }}; };
        document.querySelector('[data-privacy-choice="reject"]').click();
        await new Promise(function(resolve){setTimeout(resolve,100);});
        if (!submitted || ['analytics','external','marketing'].some(function(key){return submitted.get(key)!=='0';})) throw new Error('Reject did not send all categories off');
        if (submitted.get('csrf_token')!=='browser-test-token' || submitted.get('revision')!=='1') throw new Error('Request missing validation fields');
        if (!document.getElementById('privacy-status').textContent || document.querySelector('[data-privacy-choice="reject"]').disabled) throw new Error('Failure state prevents retry');
        panel.hidden=true; document.getElementById('privacy-reopen').click();
        if (panel.hidden) throw new Error('Reopen failed');
        window.fetch=realFetch;
        const body=new URLSearchParams({csrf_token:'browser-test-token',revision:'1',analytics:'1',external:'0',marketing:'0'});
        let saved=await fetch(location.pathname+'?save=1',{method:'POST',body:body});
        if (!(await saved.json()).saved) throw new Error('Server cookie save failed');
        let choices=await (await fetch(location.pathname+'?inspect=1')).json();
        if (!choices.analytics || choices.external || choices.marketing || !choices.receipt) throw new Error('Preference cookie round trip failed');
        if (document.cookie.includes('lanai_consent_')) throw new Error('Consent cookie is not HttpOnly');
        body.set('analytics','0');
        await fetch(location.pathname+'?save=1',{method:'POST',body:body});
        choices=await (await fetch(location.pathname+'?inspect=1')).json();
        if (choices.analytics || choices.external || choices.marketing) throw new Error('Withdrawal was not stored');
        result.textContent='PASS: resource blocking, banner, preference controls, reject request, retry, reopen, HttpOnly cookie round trip and withdrawal';
    } catch(error) { result.textContent='FAIL: '+error.message; }
});
</script></body></html>
HTML;
echo lanai_privacy_output($markup);
