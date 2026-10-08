<?php
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__,2).'/explorer/tests/support.php';
require dirname(__DIR__).'/embeds.php';
require dirname(__DIR__,2).'/poll/voting.php';
function embedCheck($ok,$label) { if (!$ok) throw new RuntimeException($label); }
class EmbedDb extends ExplorerTestDb {
    public function GetOne($sql) {
        if (strpos($sql,'GET_LOCK')!==false || strpos($sql,'RELEASE_LOCK')!==false) return 1;
        $stmt=$this->pdo->query($sql); $value=$stmt->fetchColumn(); return $value===false ? null : $value;
    }
    public function Affected_Rows() { return (int)$this->pdo->query('SELECT changes()')->fetchColumn(); }
}
class EmbedSystem extends ExplorerTestSystem {
    public $redirect='';
    public function getCsrfToken($scope) { return 'test-token'; }
    public function go2Page($url) { $this->redirect=$url; }
}
$db=new EmbedDb(); $sys_lanai=new EmbedSystem(); $cfg=array('tablepre'=>'test_', 'lang'=>'english');
$db->Execute('CREATE TABLE test_contact (conId INT,conFname TEXT,conLname TEXT,conEmail TEXT,conURL TEXT,conActive TEXT)');
$db->Execute("INSERT INTO test_contact VALUES (1,'<script>alert(1)</script>','Contact','hello@example.com','javascript:alert(1)','y')");
$db->Execute('CREATE TABLE test_poll (pllId INT,pllTitle TEXT,pllLag INT,pllActive TEXT)');
$db->Execute("INSERT INTO test_poll VALUES (1,'Choose <one>',60,'y'),(2,'Hidden',60,'n')");
$db->Execute('CREATE TABLE test_poll_option (ppoId INT,pllId INT,ppoTitle TEXT,ppoScore INT)');
$db->Execute("INSERT INTO test_poll_option VALUES (1,1,'First <option>',0),(2,2,'Other poll',0)");
$db->Execute('CREATE TABLE test_poll_stat (pllId INT,pstIP TEXT,pstTime INT)');
$embeds=new LanaiContentEmbeds($db,'test_',$sys_lanai);
$plain='<p>Unchanged content</p>';
embedCheck($embeds->render($plain)===$plain,'Plain content unchanged');
$markup='<p>Before ไทย</p><div class="mceNonEditable" data-lanai-embed="contact" data-lanai-id="1">Old name</div><div data-lanai-embed="poll" data-lanai-id="1">Poll</div><p>After</p>';
$html=$embeds->render($markup,5);
embedCheck(strpos($html,'&lt;script&gt;')!==false && strpos($html,'<script>')===false,'Contact text escaped');
embedCheck(strpos($html,'javascript:')===false,'Unsafe contact URL omitted');
embedCheck(strpos($html,'hello@example.com')!==false && strpos($html,'First &lt;option&gt;')!==false,'Contact and poll rendered');
embedCheck(strpos($html,'test-token')!==false && strpos($html,'name="cid" value="5"')!==false,'Vote includes CSRF and return content');
embedCheck(strpos($html,'ไทย')!==false,'UTF-8 content preserved');
embedCheck($embeds->item('../member',1)==='' && $embeds->item('poll','1 OR 1')==='' && $embeds->item('poll',2)==='','Invalid and inactive references omitted');
$db->Execute("UPDATE test_contact SET conFname='Updated' WHERE conId=1");
embedCheck(strpos($embeds->render($markup),'Updated Contact')!==false,'Shared contact updates appear automatically');
$db->Execute("UPDATE test_contact SET conActive='n' WHERE conId=1");
embedCheck($embeds->item('contact',1)==='','Inactive contact hidden');
$voting=new LanaiPollVoting($db,'test_');
embedCheck(!$voting->vote(1,2,'127.0.0.1',1000),'Foreign option rejected');
embedCheck(!$voting->vote(2,2,'127.0.0.1',1000),'Inactive poll rejected');
embedCheck($voting->vote(1,1,'127.0.0.1',1000),'Valid vote saved');
embedCheck(!$voting->vote(1,1,'127.0.0.1',1001),'Poll cooldown enforced');
embedCheck($voting->vote(1,1,'127.0.0.1',1061),'Vote allowed after cooldown');
embedCheck((int)$db->GetOne('SELECT ppoScore FROM test_poll_option WHERE ppoId=1')===2,'Score increments without lost updates');
$_SERVER['PHP_SELF']='/module.php'; $_SERVER['REQUEST_METHOD']='POST'; $_SERVER['REMOTE_ADDR']='127.0.0.2';
$_POST=array('mid'=>'1','voteChoice'=>'1','cid'=>'5');
function submitEmbedVote() { global $db,$cfg,$sys_lanai; ob_start(); include dirname(__DIR__,2).'/poll/pllvote.php'; return ob_get_clean(); }
submitEmbedVote();
embedCheck((int)$db->GetOne('SELECT ppoScore FROM test_poll_option WHERE ppoId=1')===2,'Missing CSRF prevents vote');
$_POST['csrf_token']='test-token'; submitEmbedVote();
embedCheck($sys_lanai->redirect==='module.php?modname=content&cid=5','Vote returns to article');
echo "Content embed rendering, escaping, references, voting, and CSRF checks passed.\n";
