<?php
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__,3).'/include/lanai/class.mfa.php';
session_start();
function mfaCheck($ok,$label) { if (!$ok) throw new RuntimeException($label); }
class MfaRows {
    public $fields; public $EOF;
    public function __construct($row=false) { $this->fields=$row ?: array(); $this->EOF=$row===false; }
}
class MfaDb {
    public $pdo; private $affected=0;
    public function __construct() { $this->pdo=new PDO('sqlite::memory:'); $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); }
    public function qstr($v) { return $this->pdo->quote($v); }
    public function Execute($sql) {
        $sql=str_replace(array(' ENGINE=InnoDB','INSERT IGNORE'),array('','INSERT OR IGNORE'),$sql);
        try { $stmt=$this->pdo->query($sql); $this->affected=$stmt->rowCount(); return new MfaRows($stmt->fetch(PDO::FETCH_ASSOC)); }
        catch (PDOException $e) { return false; }
    }
    public function Affected_Rows() { return $this->affected; }
}
$secret='GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
foreach (array(59=>'94287082',1111111109=>'07081804',1111111111=>'14050471',1234567890=>'89005924',2000000000=>'69279037',20000000000=>'65353130') as $time=>$expected) {
    mfaCheck(LanaiMfa::code($secret,intdiv($time,30),8)===$expected,'RFC 6238 SHA-1 vector '.$time);
}
$db=new MfaDb(); $cfg=array('tablepre'=>'test_', 'title'=>'Test site', 'lang'=>'english'); $cfg_mfa_key=bin2hex(random_bytes(32));
$mfa=new LanaiMfa($db,'test_',$cfg_mfa_key); $mfa->ensureSchema(); $mfa->provision(1);
$encrypted=$mfa->encrypt($secret,1);
mfaCheck($encrypted!==$secret && $mfa->decrypt($encrypted,1)===$secret,'Encrypted secret roundtrip');
try { $mfa->decrypt($encrypted,2); throw new LogicException('Wrong account accepted'); } catch (RuntimeException $expected) {}
try { (new LanaiMfa($db,'test_',str_repeat('a',64)))->decrypt($encrypted,1); throw new LogicException('Wrong key accepted'); } catch (RuntimeException $expected) {}
$step=intdiv(time(),30);
mfaCheck($mfa->enable(1,$secret,'wrong')===false,'Enrollment requires valid code');
$codes=$mfa->enable(1,$secret,LanaiMfa::code($secret,$step));
mfaCheck(is_array($codes) && count($codes)===10,'Enrollment returns recovery codes');
$row=$mfa->state(1);
mfaCheck(strpos($row['mfaRecovery'],$codes[0])===false,'Recovery codes are not stored in plaintext');
mfaCheck(!$mfa->verify(1,LanaiMfa::code($secret,$step)),'Enrollment code cannot be replayed');
mfaCheck($mfa->verify(1,$codes[0]),'Recovery code accepted');
mfaCheck(!$mfa->verify(1,$codes[0]),'Recovery code cannot be replayed');
mfaCheck(!$mfa->verify(1,$codes[1]),'Persistent attempt limit enforced');
$db->Execute('UPDATE test_user_mfa SET mfaWindow=0');
mfaCheck($mfa->verify(1,$codes[1]),'Attempts resume after cooldown');
$db->Execute('UPDATE test_user_mfa SET mfaWindow=0');
$_SESSION=array('uid'=>99);
$url=lanai_begin_login(1);
mfaCheck(!isset($_SESSION['uid']) && $_SESSION['mfa_pending']['uid']===1 && strpos($url,'memmfa')!==false,'Password step does not grant identity');
// Exercise the challenge controller, including CSRF and expiry.
class ADODB_Pager {}
require dirname(__DIR__,3).'/include/lanai/class.system.php';
class MfaSystem extends Systems {
    public $redirect='';
    public function go2Page($url) { $this->redirect=$url; }
}
$sys_lanai=new MfaSystem();
$db->Execute('CREATE TABLE test_user (userId INT,userActive TEXT,userPassword TEXT,userLogin TEXT)');
$db->Execute("INSERT INTO test_user VALUES (1,'y','hash','member')");
$_SERVER['PHP_SELF']='/module.php'; $_SERVER['REQUEST_METHOD']='POST';
$_POST=array('action'=>'verify','code'=>$codes[2]);
function renderMfa() { global $db,$cfg,$sys_lanai; ob_start(); include dirname(__DIR__).'/memmfa.php'; return ob_get_clean(); }
renderMfa(); mfaCheck(empty($_SESSION['uid']),'CSRF failure does not grant identity');
$_POST['csrf_token']=$sys_lanai->getCsrfToken('mfa');
renderMfa(); mfaCheck(($_SESSION['uid']??0)===1 && !isset($_SESSION['mfa_pending']) && $_SESSION['mfa_verified']===$row['mfaVersion'],'Challenge grants identity only after valid factor');
lanai_begin_login(1); $_SESSION['mfa_pending']['expires']=time()-1; $_POST['code']=$codes[3];
renderMfa(); mfaCheck(empty($_SESSION['uid']) && !isset($_SESSION['mfa_pending']),'Expired challenge rejected');
lanai_begin_login(1); $db->Execute("UPDATE test_user SET userActive='n'");
renderMfa(); mfaCheck(empty($_SESSION['uid']),'Inactive account cannot complete challenge');
mfaCheck(!$mfa->disable(1,'wrong-version') && $mfa->state(1)['mfaEnabled'],'Stale disable rejected');
mfaCheck($mfa->disable(1,$row['mfaVersion']) && !$mfa->state(1)['mfaEnabled'],'Disable clears enrollment');
// Setup shows a scannable QR code and keeps the manual key available.
require_once dirname(__DIR__, 3) . '/include/lanai/qr_svg.php';
$uri = LanaiMfa::otpauthUri('JBSWY3DPEHPK3PXP', 'My Site: A&B', 'ann@example.com');
mfaCheck(strpos($uri, 'otpauth://totp/') === 0 && strpos($uri, 'secret=JBSWY3DPEHPK3PXP') !== false && strpos($uri, 'issuer=My%20Site%3A%20A%26B') !== false, 'Provisioning URI carries secret and encoded issuer');
$svg = lanai_qr_svg($uri, 'QR "label"');
mfaCheck(strpos($svg, '<svg') === 0 && strpos($svg, 'role="img"') !== false && strpos($svg, 'aria-label="QR &quot;label&quot;"') !== false && strpos($svg, '<path') !== false && strpos($svg, 'http') === strpos($svg, 'http://www.w3.org/2000/svg'), 'QR code is inline SVG with no external references');
mfaCheck(lanai_qr_svg(str_repeat('x', 400)) === '', 'Oversized QR payload degrades to no image');
$db->Execute("UPDATE test_user SET userActive='y'");
$_SESSION = array('uid' => 1, 'mfa_setup' => array('uid' => 1, 'secret' => 'KRSXG5CTMVRXEZLU', 'expires' => time() + 600));
$_SERVER['REQUEST_METHOD'] = 'GET'; $_POST = array();
$html = renderMfa();
mfaCheck(strpos($html, '<svg') !== false && strpos($html, 'KRSXG5CTMVRXEZLU') !== false && strpos($html, '<details') !== false, 'Setup page shows QR code and manual key');
mfaCheck(strpos($html, 'api.qrserver') === false && strpos($html, 'chart.googleapis') === false, 'QR code is not requested from an external service');echo "MFA algorithm, encryption, recovery, replay, throttling, and login checks passed.\n";
