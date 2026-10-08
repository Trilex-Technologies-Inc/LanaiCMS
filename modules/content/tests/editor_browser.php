<?php
if (PHP_SAPI!=='cli-server' || getenv('LANAI_CONTENT_BROWSER_TEST')!=='1') { http_response_code(404); exit; }
if (isset($_GET['image'])) { header('Content-Type: image/png'); echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a7XsAAAAASUVORK5CYII='); exit; }
putenv('LANAI_EXPLORER_TEST=1');
require dirname(__DIR__,2).'/explorer/tests/support.php';
require 'modules/content/module.php';
require 'modules/content/language/lang-english.php';
define('LANAI_ADMIN_REQUEST',true);
class EditorBrowserSystem extends ExplorerTestSystem {
    public $redirect='';
    function go2Page($url) { $this->redirect=$url; }
    function userCanActOnContent($owner,$permission) { return true; }
    function getCsrfToken($scope) { return 'test-token'; }
    function setPageTitle($title) { return ''; }
}
function adodb_date2($format,$value) { return date($format,strtotime($value)); }
$db=new ExplorerTestDb(); $sys_lanai=new EditorBrowserSystem();
$cfg=array('tablepre'=>'test_','lang'=>'english','theme'=>'bootstrap'); $_SESSION=array('uid'=>1);
$db->Execute('CREATE TABLE test_content (conId INTEGER PRIMARY KEY AUTOINCREMENT,userId INT,conTitle TEXT,conBody1 TEXT,conBody2 TEXT,conAllowComments TEXT,conModified TEXT,conActive TEXT)');
$db->Execute('CREATE TABLE test_menu (mnuId INTEGER PRIMARY KEY AUTOINCREMENT,mnuParentId INT,mnuTitle TEXT,conId INT,mnuType TEXT,mnuActive TEXT,mnuOrder INT)');
$db->Execute('CREATE TABLE test_poll (pllId INT,pllTitle TEXT,pllLag INT,pllActive TEXT)');
$db->Execute("INSERT INTO test_poll VALUES (1,'How did you find Trilex?',60,'y')");
$db->Execute('CREATE TABLE test_poll_option (ppoId INT,pllId INT,ppoTitle TEXT,ppoScore INT)');
$db->Execute("INSERT INTO test_poll_option VALUES (1,1,'Search',0)");
$db->Execute('CREATE TABLE test_contact (conId INT,conFname TEXT,conLname TEXT,conActive TEXT)');
$db->Execute('CREATE TABLE test_media (mediaId INTEGER PRIMARY KEY AUTOINCREMENT,filePath TEXT,mediaType TEXT,origName TEXT,altText TEXT,createdAt TEXT,fileName TEXT,thumbPath TEXT,mimeType TEXT,fileSize INT,width INT,height INT,userId INT)');
$db->Execute("INSERT INTO test_media (mediaId,filePath,mediaType,origName,altText,createdAt) VALUES (1,'modules/content/tests/editor_browser.php?image=1','image','Trilex image','Our team','2026-01-01')");
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_GET['upload'])) {
    $storage=sys_get_temp_dir().'/lanai-content-upload-'.bin2hex(random_bytes(8));
    mkdir($storage); $cfg['datadir']=$storage;
    try {
        $media=new Media(); $id=$media->saveUpload($_FILES['file']??array());
        $record=$id?$media->getMediaById($id)->fields:array();
        header('Content-Type: application/json');
        echo json_encode(array('saved'=>(bool)$id,'name'=>$record['origName']??'','mime'=>$record['mimeType']??''));
    } finally {
        foreach (glob($storage.'/media/file/*')?:array() as $file) unlink($file);
        if (is_dir($storage.'/media/file')) rmdir($storage.'/media/file');
        if (is_dir($storage.'/media')) rmdir($storage.'/media');
        rmdir($storage);
    }
    exit;
}
$_SERVER['PHP_SELF']='/setting.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ob_start(); include 'modules/content/setting/conedit.php'; $controllerHtml=ob_get_clean();
    $row=$db->Execute('SELECT * FROM test_content ORDER BY conId DESC')->fields;
    $_REQUEST=array('cid'=>$row['conId']??0); $_SERVER['PHP_SELF']='/module.php';
    ob_start(); include 'modules/content/index.php'; $publicHtml=ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode(array('redirect'=>$sys_lanai->redirect,'controller'=>$controllerHtml,'page'=>$publicHtml,'content'=>$row,'menu'=>$db->Execute('SELECT * FROM test_menu')->fields)); exit;
}
?><!doctype html><html><head><meta charset="utf-8"><base href="/"><title>Content editor test</title><link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css"></head><body>
<?php include 'modules/content/setting/connew.php'; ?>
<pre id="editor-test-result">RUNNING</pre>
<script>
window.addEventListener('load',async function () {
    const result=document.getElementById('editor-test-result');
    try {
        for (let n=0;n<100 && (!window.tinymce || !tinymce.get('content-body1')?.initialized || !tinymce.get('content-body2')?.initialized);n++) await new Promise(resolve=>setTimeout(resolve,100));
        const editor=tinymce.get('content-body1');
        if (!editor?.initialized) throw new Error('Visual editor did not initialize');
        if (!document.querySelector('[data-mce-name="lanaipoll"]') || !document.querySelector('[data-mce-name="lanaimedia"]')) throw new Error('Toolbar controls missing');
        document.getElementById('content-title').value='About Trilex';
        document.querySelector('[name="conMenu"]').checked=true;
        editor.setContent('<p>About Trilex and our team.</p>');
        document.querySelector('[data-content-insert="poll"]').click();
        document.querySelector('[data-content-insert="media"]').click();
        if (!editor.getContent().includes('data-lanai-embed="poll"') || !editor.getContent().includes('<img')) throw new Error('Poll or media insertion failed');
        const form=document.getElementById('content-form');
        // Exercise the form submit hook that synchronizes editor contents before posting.
        form.addEventListener('submit',event=>event.preventDefault());
        form.requestSubmit();
        const response=await fetch(location.pathname,{method:'POST',body:new FormData(form)});
        const saved=await response.json();
        if (!saved.redirect.includes('saved=1') || saved.content.conId!=saved.menu.conId) throw new Error('Page/menu save failed: '+saved.controller);
        if (!saved.page.includes('About Trilex and our team.') || !saved.page.includes('How did you find Trilex?') || !saved.page.includes('<img')) throw new Error('Public page is blank or missing embeds');
        // The visible controls must also work if the rich editor is unavailable.
        tinymce.remove();
        document.querySelector('[data-content-insert="poll"]').click();
        if (!document.getElementById('content-body1').value.includes('data-lanai-embed="poll"')) throw new Error('Plain editor fallback failed');
        const upload=new FormData(); upload.append('file',new Blob(['Trilex document'],{type:'text/plain'}),'trilex.txt');
        const uploaded=await (await fetch(location.pathname+'?upload=1',{method:'POST',body:upload})).json();
        if (!uploaded.saved || uploaded.name!=='trilex.txt' || uploaded.mime!=='text/plain') throw new Error('Media upload did not save its database record');
        result.textContent='PASS: visible controls, TinyMCE initialization, poll/image insertion, synchronized save, correct menu link, public page rendering, plain-text fallback and actual media upload';
    } catch (error) { result.textContent='FAIL: '+error.message; }
});
</script></body></html>
