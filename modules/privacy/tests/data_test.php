<?php
if (PHP_SAPI!=='cli') exit;
chdir(dirname(__DIR__,3));
require 'modules/privacy/module.php';
require 'include/lanai/class.mfa.php';
class DataTestRows {
    public $fields; public $EOF; private $rows; private $position=0;
    function __construct($rows) { $this->rows=$rows; $this->sync(); }
    private function sync() { $this->EOF=!isset($this->rows[$this->position]); $this->fields=$this->rows[$this->position]??array(); }
    function MoveNext() { $this->position++; $this->sync(); }
}
class DataTestDb {
    public $databaseType; public $native; public $fail='';
    function __construct($native,$type) { $this->native=$native; $this->databaseType=$type; }
    function Execute($sql) {
        if ($this->fail!=='' && strpos($sql,$this->fail)!==false) throw new RuntimeException('Injected database failure.');
        if ($this->databaseType!=='sqlite3') return $this->native->Execute($sql);
        $sql=str_replace(array(' ENGINE=InnoDB','INSERT IGNORE'),array('','INSERT OR IGNORE'),$sql);
        $sql=preg_replace('/, KEY [a-z_]+ \([^)]*\)/','',$sql);
        $rs=$this->native->query($sql);
        return new DataTestRows($rs->columnCount()?$rs->fetchAll(PDO::FETCH_ASSOC):array());
    }
    function qstr($value) { return $this->databaseType==='sqlite3'?$this->native->quote((string)$value):$this->native->qstr($value); }
    function BeginTrans() { return $this->databaseType==='sqlite3'?$this->native->beginTransaction():$this->native->BeginTrans(); }
    function CommitTrans() { return $this->databaseType==='sqlite3'?$this->native->commit():$this->native->CommitTrans(); }
    function RollbackTrans() { return $this->databaseType==='sqlite3'?$this->native->rollBack():$this->native->RollbackTrans(); }
    function Affected_Rows() { return $this->databaseType==='sqlite3'?(int)$this->native->query('SELECT changes()')->fetchColumn():$this->native->Affected_Rows(); }
}
class DataTestSystem {
    function validateCsrfToken($scope,$value) { return $scope==='privacy_data' && $value==='test-token'; }
    function verifyPassword($password,$hash) { return password_verify($password,$hash); }
}
function checkData($value,$label) { if (!$value) throw new RuntimeException($label); }
function deniedData($callback,$label) { try { $callback(); } catch (Throwable $error) { return; } throw new RuntimeException($label); }
$mysql=in_array('--mysql',$argv,true);
if ($mysql) {
    require (getenv('LANAI_PRIVACY_TEST_CONFIG')?:'config.inc.php'); require 'include/lanai/php_compat.php'; require 'include/adodb/adodb.inc.php';
    if (!isset($dbtype,$dbhost,$dbuser,$dbpw,$dbname) || !$dbtype) { fwrite(STDERR,"Configure a test database using LANAI_PRIVACY_TEST_CONFIG or config.inc.php.\n"); exit(2); }
    $native=ADONewConnection(lanai_normalize_dbtype($dbtype));
    if (!$native->NConnect($dbhost,$dbuser,$dbpw,$dbname)) throw new RuntimeException('Test database unavailable.');
    $native->SetFetchMode(ADODB_FETCH_ASSOC);
    $db=new DataTestDb($native,$native->databaseType);
} else {
    $pdo=new PDO('sqlite::memory:'); $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $db=new DataTestDb($pdo,'sqlite3');
}
// Every table belongs to this random test prefix. Never use the installation prefix.
$prefix='privacy_test_'.bin2hex(random_bytes(6)).'_';
$tables=array('user','user_mfa','content','citem','cvalue','media','api_token','analytics_event','poll_stat','privacy_request','privacy_audit','privacy_consent');
$temp=sys_get_temp_dir().DIRECTORY_SEPARATOR.$prefix;
mkdir($temp); mkdir($temp.'/uimage');
try {
    $columns=array(); foreach (explode(',',LanaiPrivacyData::PROFILE) as $field) $columns[]=$field.($field==='userId'?' INT PRIMARY KEY':' VARCHAR(255) NULL');
    $db->Execute('CREATE TABLE '.$prefix.'user ('.implode(',',$columns).',userPassword VARCHAR(255),userActivationToken VARCHAR(64)) ENGINE=InnoDB');
    $db->Execute(LanaiMfa::schema($prefix.'user_mfa'));
    foreach (array('content'=>'conId INT PRIMARY KEY,userId INT,conDetail TEXT','citem'=>'citId INT PRIMARY KEY,userId INT,citTitle TEXT','cvalue'=>'citId INT,cfdId INT,cvalText TEXT',
        'media'=>'mediaId INT PRIMARY KEY,userId INT,origName TEXT,mediaType TEXT,mimeType TEXT,fileSize INT,width INT,height INT,altText TEXT,createdAt TEXT',
        'api_token'=>'tokenId INT PRIMARY KEY,userId INT,label TEXT,createdAt TEXT,lastUsedAt TEXT,tokenHash TEXT',
        'analytics_event'=>'eventTime DATETIME','poll_stat'=>'pstTime BIGINT') as $table=>$fields) $db->Execute('CREATE TABLE '.$prefix.$table.' ('.$fields.') ENGINE=InnoDB');
    $service=new LanaiPrivacyData($db,$prefix,$temp); $service->install(); $service->install();
    foreach (array(1=>'a',2=>'u',3=>'u') as $uid=>$role) {
        $db->Execute('INSERT INTO '.$prefix.'user (userId,userLogin,userEmail,userFname,userActive,userPrivilege,userPassword,userActivationToken) VALUES ('.$uid.",'person".$uid."','person".$uid."@example.test','Private Name','y',".$db->qstr($role).','.$db->qstr(password_hash('correct-password',PASSWORD_BCRYPT)).",'activation-secret')");
    }
    $db->Execute("INSERT INTO {$prefix}content VALUES (1,2,'My content'),(2,3,'Other private content')");
    $db->Execute("INSERT INTO {$prefix}citem VALUES (1,2,'My custom item')");
    $db->Execute("INSERT INTO {$prefix}cvalue VALUES (1,1,'My custom field')");
    $db->Execute("INSERT INTO {$prefix}api_token (tokenId,userId,tokenHash) VALUES (1,2,'api-secret')");
    file_put_contents($temp.'/uimage/u2.gif','GIF89a-test');
    $mfa=new LanaiMfa($db,$prefix,str_repeat('a',64)); $system=new DataTestSystem();
    $post=array('csrf_token'=>'test-token','currentPassword'=>'correct-password','userId'=>'3');
    checkData($service->authorize('POST',$post,array('uid'=>2),$system,$mfa)['userId']==2,'Posted identity cannot override session');
    deniedData(function() use ($service,$post,$system,$mfa) { $service->authorize('GET',$post,array('uid'=>2),$system,$mfa); },'GET accepted');
    deniedData(function() use ($service,$post,$system,$mfa) { $post['csrf_token']='bad'; $service->authorize('POST',$post,array('uid'=>2),$system,$mfa); },'CSRF accepted');
    deniedData(function() use ($service,$post,$system,$mfa) { $post['currentPassword']='wrong'; $service->authorize('POST',$post,array('uid'=>2),$system,$mfa); },'Bad password accepted');
    deniedData(function() use ($service,$post,$system,$mfa) { $service->authorize('POST',$post,array(),$system,$mfa); },'Anonymous export accepted');
    deniedData(function() use ($service) { $service->requests(2,true); },'Member read admin queue');
    $mfa->provision(3);
    $secret=LanaiMfa::secret();
    $db->Execute('UPDATE '.$prefix.'user_mfa SET mfaEnabled=1,mfaSecret='.$db->qstr($mfa->encrypt($secret,3)).',mfaVersion='.$db->qstr('version').' WHERE userId=3');
    deniedData(function() use ($service,$post,$system,$mfa) { $service->authorize('POST',$post,array('uid'=>3),$system,$mfa); },'Enabled MFA bypassed');
    $factorPost=$post; $factorPost['mfaCode']=LanaiMfa::code($secret,intdiv(time(),30));
    checkData($service->authorize('POST',$factorPost,array('uid'=>3),$system,$mfa)['userId']==3,'Valid MFA did not authenticate');
    $db->Execute('UPDATE '.$prefix.'user_mfa SET mfaFailures=0,mfaWindow=0 WHERE userId=3');
    deniedData(function() use ($service,$factorPost,$system,$mfa) { $service->authorize('POST',$factorPost,array('uid'=>3),$system,$mfa); },'MFA code replay accepted');
    $id=$service->createRequest(2,'erase','Please erase me.');
    $otherPending=$service->createRequest(2,'export','Another pending request');
    checkData($service->createRequest(2,'erase','Duplicate')===$id,'Duplicate open request not coalesced');
    checkData(count($service->requests(3))===0,'Another member can see request');
    checkData(LanaiPrivacyData::dueDate(strtotime('2024-01-31 12:00 UTC'))===strtotime('2024-02-29 12:00 UTC'),'Calendar month clamps leap-year deadline');
    deniedData(function() use ($service) { $service->createRequest(2,'bogus',''); },'Invalid request type accepted');
    $selection=array('revision'=>2,'time'=>time(),'analytics'=>true,'external'=>false,'marketing'=>false);
    $receipt=$service->recordConsent($selection,array('privacyText'=>'Version 2'),2);
    checkData(strlen($receipt)===64,'Opaque receipt generated');
    $consents=$service->events(1,'consent');
    checkData($consents[0]['receiptHash']===hash('sha256',$receipt) && strpos($consents[0]['policySnapshot'],'Version 2')!==false,'Receipt and policy evidence stored');
    $export=$service->export(2); $json=json_encode($export);
    checkData(count($export['content'])===1 && count($export['cvalue'])===1 && $export['avatar']['base64']===base64_encode('GIF89a-test'),'Owned data and avatar exported');
    foreach (array('userPassword','activation-secret','api-secret','mfaSecret','Other private content') as $secret) checkData(strpos($json,$secret)===false,'Export leaked '.$secret);
    $service->review(1,$id,'reviewing','Review in progress');
    checkData($db->Execute('SELECT responseText FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($id))->fields['responseText']==='Review in progress','Member sees admin response');
    deniedData(function() use ($service,$id) { $service->review(2,$id,'completed','done',true); },'Member processed erasure');
    deniedData(function() use ($service,$id) { $service->review(1,$id,'completed','done'); },'Unreviewed erasure accepted');
    $db->fail='DELETE FROM '.$prefix.'api_token';
    deniedData(function() use ($service,$id) { $service->review(1,$id,'completed','done',true); },'Failure did not abort erasure');
    $db->fail='';
    checkData($service->user(2)['userFname']==='Private Name' && $service->requests(2)[0]['closedAt']==0,'Erasure failure rolled back profile and request');
    if ($mysql) {
        $db->Execute('ALTER TABLE '.$prefix.'api_token ENGINE=MyISAM');
        deniedData(function() use ($service,$id) { $service->review(1,$id,'completed','done',true); },'Nontransactional erasure allowed');
        checkData($service->user(2)['userFname']==='Private Name','Nontransactional check mutated profile');
        $db->Execute('ALTER TABLE '.$prefix.'api_token ENGINE=InnoDB');
    }
    $service->review(1,$id,'completed','Review complete',true);
    $row=$db->Execute('SELECT * FROM '.$prefix.'user WHERE userId=2')->fields;
    checkData($row['userActive']==='n' && $row['userEmail']===null && $row['userPassword']===null && $row['userActivationToken']===null,'Erasure cleared identity and credentials');
    foreach (array('api_token','user_mfa','privacy_consent') as $table) checkData($db->Execute('SELECT * FROM '.$prefix.$table.' WHERE userId=2')->EOF,'Erasure left '.$table);
    checkData(!file_exists($temp.'/uimage/u2.gif'),'Avatar not erased');
    checkData($db->Execute('SELECT userId FROM '.$prefix.'content WHERE conId=1')->fields['userId']==0,'Content ownership not cleared');
    checkData($db->Execute('SELECT userId FROM '.$prefix.'content WHERE conId=2')->fields['userId']==3,'Another user changed');
    checkData($db->Execute('SELECT * FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($id))->fields['requestDetails']==='','Request details not scrubbed');
    checkData($db->Execute('SELECT * FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($otherPending))->fields['requestStatus']==='cancelled','Erasure incorrectly marked another request fulfilled');
    deniedData(function() use ($service) { $service->export(2); },'Erased account can export');
    deniedData(function() use ($service,$id) { $service->review(1,$id,'completed','again',true); },'Closed erasure reran');
    $adminRequest=$service->createRequest(1,'erase','');
    deniedData(function() use ($service,$adminRequest) { $service->review(1,$adminRequest,'completed','done',true); },'Admin self-erasure allowed');
    $open=$service->createRequest(3,'rectify','Correct me');
    $old=time()-500*86400;
    $db->Execute('UPDATE '.$prefix.'privacy_request SET closedAt='.$old.' WHERE requestId='.$db->qstr($id));
    $db->Execute('UPDATE '.$prefix.'privacy_request SET createdAt='.$old.',dueAt='.($old+30*86400).' WHERE requestId='.$db->qstr($open));
    $db->Execute('INSERT INTO '.$prefix.'analytics_event VALUES ('.$db->qstr(date('Y-m-d H:i:s',$old)).'),('.$db->qstr(date('Y-m-d H:i:s')).')');
    $db->Execute('INSERT INTO '.$prefix.'poll_stat VALUES ('.$old.'),('.time().')');
    $dry=$service->retention(LanaiPrivacy::defaults());
    checkData($dry['privacy_request']===1 && $dry['analytics_event']===1 && $dry['poll_stat']===1,'Retention counts incorrect');
    checkData(!$db->Execute('SELECT * FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($id))->EOF,'Dry run deleted data');
    $service->retention(LanaiPrivacy::defaults(),true);
    checkData($db->Execute('SELECT * FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($id))->EOF,'Old closed request retained');
    checkData(!$db->Execute('SELECT * FROM '.$prefix.'privacy_request WHERE requestId='.$db->qstr($open))->EOF,'Open request deleted');
    checkData($service->retention(LanaiPrivacy::defaults())['analytics_event']===0,'Retention not idempotent');
    // Render the actual member and administrator fragments with hostile request text.
    require 'include/lanai/class.system.php';
    $sys_lanai=new Systems(); $lanaiPrivacy=(object)array();
    $cfg=array('tablepre'=>$prefix,'datadir'=>$temp,'email'=>'privacy@example.test');
    $e=static function($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); };
    $t=static function($en,$th) { return $en; };
    $_SESSION=array('uid'=>3); $_GET=array();
    $service->review(1,$open,'reviewing','<script>alert(1)</script>');
    ob_start(); include 'modules/privacy/personal.php'; $memberHtml=ob_get_clean();
    checkData(strpos($memberHtml,'action="privacy-data.php"')!==false && strpos($memberHtml,'name="currentPassword"')!==false && strpos($memberHtml,'&lt;script&gt;')!==false && strpos($memberHtml,'<script>alert')===false,'Member controls or response escaping failed');
    $_SESSION=array('uid'=>1); define('LANAI_ADMIN_REQUEST',true); $privacyData=$service; $privacyEscape=$e;
    ob_start(); include 'modules/privacy/setting/requests.php'; $adminHtml=ob_get_clean();
    checkData(strpos($adminHtml,'name="reviewed"')!==false && strpos($adminHtml,'OVERDUE')!==false && strpos($adminHtml,'<script>alert')===false,'Admin queue controls, overdue state or escaping failed');
    for ($attempt=0;$attempt<5;$attempt++) deniedData(function() use ($service,$post,$system,$mfa) { $post['currentPassword']='wrong'; $service->authorize('POST',$post,array('uid'=>1),$system,$mfa); },'Wrong password accepted');
    deniedData(function() use ($service,$post,$system,$mfa) { $service->authorize('POST',$post,array('uid'=>1),$system,$mfa); },'New session bypassed persistent verification throttle');
    echo 'PASS ('.($mysql?'MySQL/MariaDB':'SQLite')."): authentication, CSRF, identity isolation, private export, request review, deadlines, consent evidence, erasure rollback, account protection and retention.\n";
} finally {
    if (!preg_match('/^privacy_test_[a-f0-9]{12}_$/D',$prefix)) throw new RuntimeException('Unsafe cleanup prefix.');
    foreach (array_reverse($tables) as $table) $db->Execute('DROP TABLE IF EXISTS '.$prefix.$table);
    if (file_exists($temp.'/uimage/u2.gif')) unlink($temp.'/uimage/u2.gif');
    rmdir($temp.'/uimage'); rmdir($temp);
}
