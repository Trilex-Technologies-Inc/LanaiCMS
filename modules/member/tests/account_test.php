<?php
// Run with: php modules/member/tests/account_test.php
define('LANAI_MEMBER_AREA', true);
require dirname(__DIR__) . '/account.php';
require dirname(__DIR__, 3) . '/include/lanai/class.system.php';
$system = new Systems();
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } }
$user = array('userPassword' => password_hash('existing-password', PASSWORD_BCRYPT));
$profile = array('userFname'=>'Jane', 'userLname'=>'Doe', 'cntId'=>'TH', 'userURL'=>'https://example.com', 'userPrivilege'=>'a', 'userRoleId'=>'1', 'userId'=>'99');
list($values, $errors) = memberAreaValidate($profile, $user, 'profile', $system);
check(!$errors, 'Valid profile accepted');
check(!isset($values['userPrivilege'], $values['userRoleId'], $values['userId']), 'Profile must not change identity or privileges');
$profile['userURL'] = 'javascript:alert(1)';
check(in_array('url', memberAreaValidate($profile, $user, 'profile', $system)[1]), 'Unsafe website rejected');
$profile['userFname'] = array('unexpected');
check(in_array('required', memberAreaValidate($profile, $user, 'profile', $system)[1]), 'Array input rejected');
$account = array('userEmail'=>'jane@example.com','userLogin'=>'jane','currentPassword'=>'existing-password','userPassword1'=>'new-password-123','userPassword2'=>'new-password-123');
list($values, $errors) = memberAreaValidate($account, $user, 'security', $system);
check(!$errors && password_verify('new-password-123', $values['userPassword']), 'Password is hashed');
$account['currentPassword'] = 'wrong';
list($values, $errors) = memberAreaValidate($account, $user, 'security', $system);
check(in_array('current', $errors) && !isset($values['userPassword']), 'Current password required');
$account['currentPassword'] = 'existing-password';
$account['userPassword2'] = 'different-password';
check(in_array('match', memberAreaValidate($account, $user, 'security', $system)[1]), 'Mismatch rejected');
$account['userPassword1'] = $account['userPassword2'] = '';
list($values, $errors) = memberAreaValidate($account, $user, 'security', $system);
check(!$errors && !isset($values['userPassword']), 'Blank password preserves existing hash');
$user['userPassword'] = md5('existing-password');
check(!memberAreaValidate($account, $user, 'security', $system)[1], 'Legacy password verification supported');
$account['userEmail'] = 'invalid';
check(in_array('email', memberAreaValidate($account, $user, 'security', $system)[1]), 'Invalid email rejected');
$english = require dirname(__DIR__) . '/language/account-english.php';
$thai = require dirname(__DIR__) . '/language/account-thai.php';
check(array_keys($english) === array_keys($thai), 'Translations cover the same messages');
echo "Member account checks passed.\n";

// Exercise the real controller with an isolated database double.
class ADODB_Pager {}
require dirname(__DIR__) . '/module.php';
require dirname(__DIR__) . '/language/lang-english.php';
define('_SAVE', 'Save');
class AccountTestRows {
    public $EOF;
    public $fields;
    function __construct($fields = array()) { $this->fields = $fields; $this->EOF = !$fields; }
    function moveNext() { $this->EOF = true; }
}
class AccountTestDb {
    public $writes = array();
    public $user;
    function qstr($value) { return "'" . str_replace("'", "''", $value) . "'"; }
    function execute($sql) {
        if (strpos($sql, 'UPDATE ') === 0) { $this->writes[] = $sql; return true; }
        if (strpos($sql, 'SELECT cntId') === 0) { return new AccountTestRows(array('cntId'=>'TH','cntName'=>'Thailand')); }
        if (strpos($sql, 'SELECT userId') === 0) { return new AccountTestRows(); }
        return new AccountTestRows($this->user);
    }
}
$db = new AccountTestDb();
$db->user = array('userId'=>7, 'userFname'=>'<script>alert(1)</script>', 'userLname'=>'Member', 'userEmail'=>'jane@example.com', 'userLogin'=>'jane', 'userCreated'=>'2026-01-01', 'userActive'=>'y', 'userPrivilege'=>'u', 'userPassword'=>password_hash('existing-password', PASSWORD_BCRYPT), 'cntId'=>'TH');
$cfg = array('tablepre'=>'test_', 'lang'=>'english');
$cfg_datadir = sys_get_temp_dir() . '/lanai-account-test-no-avatar';
$sys_lanai = $system;
$_SERVER['PHP_SELF'] = '/module.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SESSION = array();
$_GET = $_POST = $_REQUEST = $_FILES = array();
function renderAccount() {
    global $db, $cfg, $cfg_datadir, $sys_lanai;
    ob_start();
    include dirname(__DIR__) . '/meminfo.php';
    return ob_get_clean();
}
check(strpos(renderAccount(), 'memloginform') !== false && !$db->writes, 'Guest gets login link');
$_SESSION['uid'] = 7;
$html = renderAccount();
check(strpos($html, '&lt;script&gt;') !== false && strpos($html, '<script>') === false, 'Profile output escaped');
check(strpos($html, 'href="setting.php"') === false, 'Member cannot see admin link');
$db->user['userPrivilege'] = 'a';
check(strpos(renderAccount(), 'href="setting.php"') !== false, 'Administrator gets admin link');
$_GET['section'] = 'profile';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('userFname'=>'Jane', 'userLname'=>'Doe', 'cntId'=>'TH');
renderAccount();
check(!$db->writes, 'Missing CSRF token blocks all writes');
$_POST['csrf_token'] = $sys_lanai->getCsrfToken('member');
$_POST['userId'] = 99;
$_POST['userRoleId'] = 1;
$_POST['userPrivilege'] = 'a';
renderAccount();
check(count($db->writes) === 1 && strpos($db->writes[0], 'WHERE userId=7') !== false, 'Only current user updated');
check(strpos($db->writes[0], 'userRoleId') === false && strpos($db->writes[0], 'userPrivilege') === false, 'Saving preserves role and privilege');
$_GET['section'] = 'security';
$_POST = array('csrf_token'=>$sys_lanai->getCsrfToken('member'), 'userLogin'=>'jane', 'userEmail'=>'new@example.com', 'currentPassword'=>'wrong');
renderAccount();
check(count($db->writes) === 1, 'Wrong current password blocks account changes');
$_POST['currentPassword'] = 'existing-password';
renderAccount();
check(count($db->writes) === 2, 'Valid account change saved');
echo "Member controller checks passed.\n";
