<?php
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__, 2) . '/explorer/tests/support.php';
require 'include/lanai/site_url.php';
require 'modules/content/module.php';
require 'modules/content/language/lang-english.php';
class ContentTestSystem extends ExplorerTestSystem {
    public $errors = array(); public $redirect = null;
    function getErrorBox($message) { $this->errors[] = $message; }
    function go2Page($url) { $this->redirect = $url; }
    function setPageTitle($title) { return ''; }
}
class ContentTestDb extends ExplorerTestDb {
    public $rejectComment = false;
    function Execute($sql) { if ($this->rejectComment && str_contains($sql, 'INSERT INTO test_comment')) return false; return parent::Execute($sql); }
}
function adodb_date2($format, $value) { return date($format, strtotime($value)); }
function contentCheck($ok, $label) { if (!$ok) throw new RuntimeException($label); echo 'PASS: ' . $label . PHP_EOL; }
function renderContent($file) { global $sys_lanai, $cfg; ob_start(); include $file; return ob_get_clean(); }
$db = new ContentTestDb(); $cfg = array('tablepre' => 'test_'); $_SESSION = array();
$db->Execute("CREATE TABLE test_content (conId INTEGER, conActive TEXT, conAllowComments TEXT, conTitle TEXT, conModified TEXT, conBody1 TEXT, conBody2 TEXT)");
$db->Execute("INSERT INTO test_content VALUES (1,'y','y','Example','2026-09-23','<p>Content</p>','')");
$db->Execute("CREATE TABLE test_comment (comId INTEGER PRIMARY KEY AUTOINCREMENT, catTitle TEXT, catId INTEGER, comDetail TEXT, comAuthor TEXT, comEmail TEXT, comDate TEXT DEFAULT CURRENT_TIMESTAMP)");
$sys_lanai = new ContentTestSystem();
$_SERVER['PHP_SELF'] = '/lanaicms/module.php'; $_SERVER['REQUEST_METHOD'] = 'GET'; $_REQUEST = array('cid' => 1);
$html = renderContent('modules/content/index.php');
contentCheck(str_contains($html, 'Verification code') && str_contains($html, '</form>') && str_contains($html, 'name="csrf_token"'), 'Content with comments renders completely without missing language constants');
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('cid' => 1, 'csrf_token' => 'test-token', 'comAuthor' => '<script>name</script>', 'comEmail' => 'reader@example.com', 'comDetail' => 'Test comment', 'txtVerify' => 'abcde');
$_SESSION['captcha'] = 'ABCDE'; renderContent('modules/content/concomment.php');
contentCheck($db->Execute('SELECT * FROM test_comment')->recordcount() === 1 && str_ends_with($sys_lanai->redirect, '#comments'), 'Valid comment saves and returns to the comments section');
$html = renderContent('modules/content/index.php');
contentCheck(str_contains($html, '&lt;script&gt;name') && str_contains($html, 'Test comment'), 'Saved comments display with escaped author text');
$_SESSION['captcha'] = 'wrong'; $sys_lanai->redirect = null; renderContent('modules/content/concomment.php');
contentCheck($db->Execute('SELECT * FROM test_comment')->recordcount() === 1 && $sys_lanai->redirect === null, 'Invalid captcha does not save or redirect as success');
$db->rejectComment = true; $_SESSION['captcha'] = 'ABCDE'; renderContent('modules/content/concomment.php');
contentCheck(end($sys_lanai->errors) === _CONTENT_COMMENT_FAILED && $sys_lanai->redirect === null, 'Database failure shows a comment error');
$_POST['csrf_token'] = 'bad'; $count = count($sys_lanai->errors); renderContent('modules/content/concomment.php');
contentCheck(count($sys_lanai->errors) === $count + 1, 'Invalid comment token rejected');
$server = array('HTTPS' => 'on', 'HTTP_HOST' => 'dev.trilex.net', 'SCRIPT_NAME' => '/lanaicms/install/index.php', 'SCRIPT_FILENAME' => '/var/www/lanaicms/install/index.php');
contentCheck(lanai_install_url($server, '/var/www/lanaicms') === 'https://dev.trilex.net/lanaicms', 'Installer detects HTTPS subfolder URL');
$server['SCRIPT_NAME'] = '/install/index.php';
contentCheck(lanai_install_url($server, '/var/www/lanaicms') === 'https://dev.trilex.net', 'Installer supports root installs');
$server['SCRIPT_NAME'] = '/nested/cms/install/index.php'; $server['HTTPS'] = 'off'; $server['HTTP_HOST'] = 'localhost:8080';
contentCheck(lanai_install_url($server, '/var/www/lanaicms') === 'http://localhost:8080/nested/cms', 'Installer preserves nested paths, HTTP, and port');
$server['SCRIPT_NAME'] = '/lanaicms/modules/explorer/api.php'; $server['SCRIPT_FILENAME'] = '/var/www/lanaicms/modules/explorer/api.php';
contentCheck(lanai_effective_site_url('https://dev.trilex.net', $server, '/var/www/lanaicms') === 'https://dev.trilex.net/lanaicms', 'Legacy host-only configuration repaired for nested endpoints');
contentCheck(lanai_effective_site_url('https://dev.trilex.net/custom', $server, '/var/www/lanaicms') === 'https://dev.trilex.net/custom', 'Explicit configured URL path is preserved');
foreach (array('_SETUP_COPY_CODE','_SETUP_BACK','_SETUP_VERIFY_CONFIG') as $constant) define($constant, 'Test');
$_SESSION = array_fill_keys(array('dbname','dbhost','dbuser','dbpw','tablepre','cfg_title','cfg_email','cfg_theme','cfg_sendmail','smtp_host','smtp_port','cfg_lang'), 'test');
$_SESSION['cfg_offsettime'] = 0; $_SESSION['cfg_url'] = 'https://dev.trilex.net/lanaicms/'; $_SESSION['cfg_dir'] = 'C:\\test\\Lanai CMS'; $_REQUEST['step'] = 4;
ob_start(); include 'install/step_4.php'; $installHtml = ob_get_clean();
preg_match('/<textarea[^>]*>(.*?)<\/textarea>/s', $installHtml, $match);
$generated = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
$readConfig = function ($code) { ob_start(); eval('?>' . $code); ob_end_clean(); return array($cfg_dir, $cfg_url, $cfg_datadir); };
$values = $readConfig($generated);
contentCheck($values[0] === $_SESSION['cfg_dir'] && $values[1] === 'https://dev.trilex.net/lanaicms' && str_ends_with($values[2], 'datacenter'), 'Generated configuration preserves filesystem backslashes, spaces, and subfolder URL');
echo "Content, comment, and installer checks passed.\n";
