<?php
if (PHP_SAPI!=='cli') exit;
require dirname(__DIR__,2).'/explorer/tests/support.php';
require 'modules/content/module.php';
require 'modules/content/language/lang-english.php';
define('LANAI_ADMIN_REQUEST',true);
class EditorTestDb extends ExplorerTestDb {
    public $failContent=false; public $failMenu=false;
    function Execute($sql) {
        if ($this->failContent && preg_match('/(?:INSERT INTO|UPDATE)\s+test_content\b/i',$sql)) return false;
        if ($this->failMenu && strpos($sql,'INSERT INTO test_menu')!==false) return false;
        return parent::Execute($sql);
    }
}
class EditorTestSystem extends ExplorerTestSystem {
    public $redirect=''; public $errors=array(); public $allowed=true; public $caps=array('*');
    function go2Page($url) { $this->redirect=$url; }
    function getErrorBox($error) { $this->errors[]=$error; }
    function userHasCapability($capability, $uid=null) { return in_array('*',$this->caps,true) || in_array($capability,$this->caps,true); }
    // Mirrors Systems::userCanActOnContent(): a blanket capability, or ownership plus edit_own_content.
    function userCanActOnContent($owner,$capability) {
        return $this->allowed && ($this->userHasCapability($capability) || ((int)$owner===1 && $this->userHasCapability('edit_own_content')));
    }
}
function checkEditor($ok,$message) { if (!$ok) throw new RuntimeException($message); }
function submitEditor($post) {
    global $db,$cfg,$sys_lanai;
    $_POST=$post; $_REQUEST=$post; $sys_lanai->redirect='';
    ob_start(); include 'modules/content/setting/conedit.php'; return ob_get_clean();
}
$db=new EditorTestDb(); $sys_lanai=new EditorTestSystem();
$cfg=array('tablepre'=>'test_','lang'=>'english','theme'=>'bootstrap'); $_SESSION=array('uid'=>1);
$_SERVER['PHP_SELF']='/setting.php'; $_SERVER['REQUEST_METHOD']='POST';
// Start with the legacy schema to exercise the additive upgrade.
$db->Execute('CREATE TABLE test_content (conId INTEGER PRIMARY KEY AUTOINCREMENT,userId INT,conTitle TEXT,conBody1 TEXT,conBody2 TEXT,conModified TEXT,conActive TEXT)');
$db->Execute('CREATE TABLE test_menu (mnuId INTEGER PRIMARY KEY AUTOINCREMENT,mnuParentId INT,mnuTitle TEXT,conId INT,mnuType TEXT,mnuActive TEXT,mnuOrder INT)');
$post=array('ac'=>'new','csrf_token'=>'test-token','conTitle'=>'About Trilex','conBody1'=>'<p>About our company.</p>','conBody2'=>'','conMenu'=>'yes');
submitEditor($post);
$content=$db->Execute('SELECT * FROM test_content')->fields;
$menu=$db->Execute('SELECT * FROM test_menu')->fields;
checkEditor($content['conBody1']===$post['conBody1'] && $content['conAllowComments']==='n','Page body or comments migration failed');
checkEditor($menu['conId']===$content['conId'] && $menu['mnuOrder']==1,'Menu does not reference new content');
checkEditor(strpos($sys_lanai->redirect,'mf=coneditform&mid=1&saved=1')!==false,'Save does not open saved page');
$post['conBody1']='<p>Second page with the same title.</p>'; submitEditor($post);
checkEditor($db->Execute('SELECT * FROM test_menu ORDER BY mnuId DESC')->fields['conId']==2,'Duplicate title linked to wrong page');
$db->failContent=true; $html=submitEditor($post); $db->failContent=false;
checkEditor($sys_lanai->redirect==='' && strpos($html,'Second page with the same title.')!==false && strpos($html,'could not be saved')!==false,'Insert failure loses text or reports success');
checkEditor($db->Execute('SELECT * FROM test_menu')->recordcount()===2,'Failed content insert created a menu');
$blank=$post; $blank['conBody1']='<p>&nbsp;<br></p>'; $html=submitEditor($blank);
checkEditor($db->Execute('SELECT * FROM test_content')->recordcount()===2 && strpos($html,'Add page text')!==false,'Blank content saved');
$invalid=$post; $invalid['csrf_token']='bad'; submitEditor($invalid);
checkEditor($db->Execute('SELECT * FROM test_content')->recordcount()===2,'Invalid CSRF saved content');
$db->failMenu=true; submitEditor($post); $db->failMenu=false;
checkEditor($db->Execute('SELECT * FROM test_content')->recordcount()===3 && strpos($sys_lanai->redirect,'menu_failed=1')!==false,'Menu failure lost saved page or falsely reported full success');
$edit=$post; $edit['ac']='edit'; $edit['mid']=1; $edit['conTitle']='Updated Trilex'; $edit['conBody1']='<p>Updated page.</p>';
submitEditor($edit);
checkEditor($db->Execute('SELECT * FROM test_content WHERE conId=1')->fields['conBody1']===$edit['conBody1'],'Edited body not saved');
$sys_lanai->allowed=false; $edit['conBody1']='<p>Forbidden</p>'; submitEditor($edit);
checkEditor($db->Execute('SELECT * FROM test_content WHERE conId=1')->fields['conBody1']!=='<p>Forbidden</p>','Unauthorized edit saved');
$sys_lanai->allowed=true; $db->failContent=true; $html=submitEditor($edit); $db->failContent=false;
checkEditor($sys_lanai->redirect==='' && strpos($html,'Forbidden')!==false,'Update failure loses entered text');
// Review workflow: a contributor (edit_own_content only) cannot publish.
$sys_lanai->caps=array('access_admin','edit_own_content');
$contrib=array('ac'=>'new','csrf_token'=>'test-token','conTitle'=>'Contributor page','conBody1'=>'<p onclick="x()">Draft text</p><script>alert(1)</script><a href="javascript:alert(1)">bad</a>','conBody2'=>'','conMenu'=>'yes');
$menusBefore=$db->Execute('SELECT * FROM test_menu')->recordcount();
submitEditor($contrib);
$draft=$db->Execute("SELECT * FROM test_content WHERE conTitle='Contributor page'")->fields;
checkEditor($draft['conActive']==='n' && $draft['conPending']==='y','Contributor page was not held for review');
checkEditor(strpos($draft['conBody1'],'<script')===false && strpos($draft['conBody1'],'onclick')===false && strpos($draft['conBody1'],'javascript:')===false && strpos($draft['conBody1'],'Draft text')!==false,'Contributor HTML was not sanitized');
checkEditor($db->Execute('SELECT * FROM test_menu')->recordcount()===$menusBefore,'Contributor created a menu link');
checkEditor(strpos($sys_lanai->redirect,'review=1')!==false,'Contributor is not told the page awaits review');
$draftId=(int)$draft['conId'];
submitEditor(array('ac'=>'active','v'=>'y','mid'=>$draftId,'csrf_token'=>'test-token'));
checkEditor($db->Execute("SELECT conActive FROM test_content WHERE conId=$draftId")->fields['conActive']==='n','Contributor published their own page');
submitEditor(array('ac'=>'mactive','mid'=>array($draftId),'csrf_token'=>'test-token'));
checkEditor($db->Execute("SELECT conActive FROM test_content WHERE conId=$draftId")->fields['conActive']==='n','Contributor published through bulk activation');
$resubmit=array('ac'=>'edit','mid'=>$draftId,'csrf_token'=>'test-token','conTitle'=>'Contributor page','conBody1'=>'<p>Revised draft</p>','conBody2'=>'');
submitEditor($resubmit);
$row=$db->Execute("SELECT * FROM test_content WHERE conId=$draftId")->fields;
checkEditor($row['conBody1']==='<p>Revised draft</p>' && $row['conActive']==='n' && $row['conPending']==='y','Contributor cannot revise a pending page');
// An editor approves it; the review flag clears and the contributor can no longer change the live page.
$sys_lanai->caps=array('*');
submitEditor(array('ac'=>'active','v'=>'y','mid'=>$draftId,'csrf_token'=>'test-token'));
$row=$db->Execute("SELECT * FROM test_content WHERE conId=$draftId")->fields;
checkEditor($row['conActive']==='y' && $row['conPending']==='n','Approval did not publish and clear the review flag');
$db->Execute('CREATE TABLE test_comment (comId INTEGER PRIMARY KEY AUTOINCREMENT,catTitle TEXT,catId INT)');
$sys_lanai->caps=array('access_admin','edit_own_content');
$resubmit['conBody1']='<p>Sneaky live edit</p>'; submitEditor($resubmit);
checkEditor($db->Execute("SELECT conBody1 FROM test_content WHERE conId=$draftId")->fields['conBody1']==='<p>Revised draft</p>','Contributor edited a published page');
submitEditor(array('ac'=>'mdelete','mid'=>array($draftId),'csrf_token'=>'test-token'));
checkEditor($db->Execute("SELECT * FROM test_content WHERE conId=$draftId")->recordcount()===1,'Contributor deleted a published page');
submitEditor(array('ac'=>'new','csrf_token'=>'test-token','conTitle'=>'Second draft','conBody1'=>'<p>More</p>','conBody2'=>''));
$secondId=(int)$db->Execute("SELECT conId FROM test_content WHERE conTitle='Second draft'")->fields['conId'];
submitEditor(array('ac'=>'mdelete','mid'=>array($secondId),'csrf_token'=>'test-token'));
checkEditor($db->Execute("SELECT * FROM test_content WHERE conId=$secondId")->recordcount()===0,'Contributor cannot remove their own pending draft');
$sys_lanai->caps=array('access_admin'); $before=$db->Execute('SELECT * FROM test_content')->recordcount();
submitEditor(array('ac'=>'new','csrf_token'=>'test-token','conTitle'=>'Subscriber page','conBody1'=>'<p>x</p>','conBody2'=>''));
checkEditor($db->Execute('SELECT * FROM test_content')->recordcount()===$before,'A role without content capabilities created a page');
$sys_lanai->caps=array('*');
echo "PASS: review workflow (pending contributor pages, sanitizing, no self-publish, approval, live-page protection).\n";
echo "PASS: create/edit, body persistence, legacy schema upgrade, exact menu linkage, blank-page rejection, CSRF, permissions, and database failure recovery.\n";
