<?php
// Isolated checks; never bootstrap or connect to a real installation.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__, 3));
class ADODB_Pager {}
class User {
    public static $role = 'a';
    function getUserPrivilege($id) { return self::$role; }
}
class MediaTestRecord {
    public $fields;
    public $EOF = false;
    function __construct($fields) { $this->fields = $fields; }
    function MoveNext() { $this->EOF = true; }
}
class MediaTestDb {
    public $queries = array();
    public $columns = array('MEDIAID' => true, 'ALTTEXT' => true);
    public $file;
    function qstr($text) { return "'" . str_replace("'", "''", (string) $text) . "'"; }
    function MetaColumns($table) { return $this->columns; }
    function Execute($sql) {
        $this->queries[] = $sql;
        if (preg_match('/ADD COLUMN (\w+)/', $sql, $m)) $this->columns[strtoupper($m[1])] = true;
        if (str_contains($sql, 'COUNT(*)')) return new MediaTestRecord(array('total' => 61));
        if (str_starts_with($sql, 'SELECT')) return new MediaTestRecord($this->file);
        return true;
    }
    function SelectLimit($sql, $limit, $offset) {
        $this->queries[] = $sql . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        return new MediaTestRecord($this->file);
    }
}
class MediaTestSystem {
    public $errors = array();
    function validateCsrfToken($scope, $token) { return $token === 'valid'; }
    function getErrorBox($message) { $this->errors[] = $message; }
    function renderCsrfField($scope) { echo '<input name="csrf_token" value="valid" type="hidden">'; }
    function go2Page($url) {}
}
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
    echo 'PASS: ' . $message . PHP_EOL;
}
$db = new MediaTestDb();
$cfg = array('tablepre' => 'test_', 'datadir' => sys_get_temp_dir());
$_SESSION = array('uid' => 1);
$sys_lanai = new MediaTestSystem();
require 'modules/media/module.php';
define('_MEDIA_STORAGE_NOT_WRITABLE', 'Storage not writable.');
$media = new Media();
check($media->ensureLibraryFields(), 'Existing library can gain metadata columns');
$queries = count($db->queries);
check($media->ensureLibraryFields() && count($db->queries) === $queries, 'Schema upgrade is idempotent');
$where = $media->libraryWhere(array('q' => "50%_! O'Reilly", 'type' => 'pdf', 'from' => '2026-09-01', 'to' => '2026-09-23'));
check(str_contains($where, "50!%!_!! O''Reilly"), 'Search quotes SQL and treats wildcard characters literally');
check(str_contains($where, "mimeType='application/pdf'") && str_contains($where, "createdAt < '2026-09-24'"), 'Type and inclusive end-date filters combine');
$where = $media->libraryWhere(array('q' => '', 'type' => 'invalid', 'from' => "bad\0date", 'to' => '2026-02-30'));
check($where === 'explorerTrashId IS NULL', 'Malformed filters are ignored and trashed registrations are hidden');
check(!$media->saveUpload(array('name' => array(), 'error' => 0)), 'Malformed upload is rejected');
check(!$media->saveUpload(array('name' => 'a.txt', 'tmp_name' => '/fake', 'size' => 5, 'error' => UPLOAD_ERR_PARTIAL)), 'Partial upload is rejected');
$db->file = array('mediaId' => 1, 'origName' => '<script>alert(1)</script>.txt', 'title' => '<b>Title</b>', 'caption' => '</textarea><script>x</script>', 'altText' => '" onfocus="x', 'filePath' => 'datacenter/media/file/test.txt', 'mimeType' => 'text/plain', 'fileSize' => 1024, 'width' => null, 'height' => null, 'createdAt' => '2026-09-23 10:00:00');
$_SERVER['PHP_SELF'] = '/setting.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = array('page' => 999, 'q' => 'report');
// Match the theme's include scope: it supplies cfg/system but no local db.
function renderMediaScreen($path) {
    global $cfg, $sys_lanai;
    ob_start(); include $path; return ob_get_clean();
}
$html = renderMediaScreen('modules/media/setting/index.php');
check(str_contains($html, 'Page 3 of 3') && str_contains(end($db->queries), 'OFFSET 60'), 'Pagination clamps to the last available page');
check(str_contains($html, '&lt;script&gt;') && !str_contains($html, '<script>alert(1)'), 'File list escapes uploaded filenames');
check(str_contains($html, 'q=report') && !str_contains($html, '<img'), 'Pagination retains filters and list contains no thumbnails');
$_GET = array('mediaId' => 1);
$html = renderMediaScreen('modules/media/setting/details.php');
check(str_contains($html, '&lt;/textarea&gt;') && !str_contains($html, '<script>x'), 'Metadata editor escapes stored content');
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('ac' => 'details', 'mediaId' => 1, 'csrf_token' => 'invalid');
$queries = count($db->queries);
renderMediaScreen('modules/media/setting/mediaedit.php');
check(count($db->queries) === $queries && count($sys_lanai->errors) === 1, 'Invalid CSRF cannot mutate metadata');
$_POST = array('ac' => 'details', 'mediaId' => 1, 'csrf_token' => 'valid', 'title' => "A title's text", 'caption' => 'Caption', 'altText' => 'Description');
renderMediaScreen('modules/media/setting/mediaedit.php');
check(str_contains(end($db->queries), "title='A title''s text'") && $_SESSION['media_notice'] === 'File details saved.', 'Valid metadata submission saves all details');
$_POST['title'] = str_repeat('x', 256);
renderMediaScreen('modules/media/setting/mediaedit.php');
check(str_starts_with(end($db->queries), 'SELECT') && count($sys_lanai->errors) === 2, 'Overlong metadata is rejected before update');
User::$role = 'm';
$queries = count($db->queries);
renderMediaScreen('modules/media/setting/mediaedit.php');
check(count($db->queries) === $queries && end($sys_lanai->errors) === 'Administrator access required.', 'Non-administrators cannot mutate media');
echo "All media library checks passed.\n";
