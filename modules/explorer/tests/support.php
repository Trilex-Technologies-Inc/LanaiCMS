<?php
if (PHP_SAPI !== 'cli' && !(PHP_SAPI === 'cli-server' && getenv('LANAI_EXPLORER_TEST') === '1')) { http_response_code(404); exit; }
chdir(dirname(__DIR__, 3));
class ADODB_Pager {}
class ExplorerTestRecord {
    public $fields = array(); public $EOF = true; private $rows; private $position = 0;
    function __construct($rows) { $this->rows = $rows; $this->sync(); }
    private function sync() { $this->EOF = !isset($this->rows[$this->position]); $this->fields = $this->rows[$this->position] ?? array(); }
    function MoveNext() { $this->position++; $this->sync(); }
    function recordcount() { return count($this->rows); }
}
class ExplorerTestDb {
    public $pdo; public $failUpdates = false;
    function __construct($file = ':memory:') { $this->pdo = new PDO('sqlite:' . $file); $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); $this->pdo->sqliteCreateFunction('NOW', function () { return date('Y-m-d H:i:s'); }); }
    function Execute($sql) {
        if ($this->failUpdates && str_starts_with($sql, 'UPDATE')) { $this->failUpdates = false; return false; }
        $statement = $this->pdo->query($sql);
        return new ExplorerTestRecord($statement->columnCount() ? $statement->fetchAll(PDO::FETCH_ASSOC) : array());
    }
    function qstr($value) { return $this->pdo->quote((string)$value); }
    function Insert_ID() { return $this->pdo->lastInsertId(); }
    function MetaColumns($table) { $columns = array(); foreach ($this->pdo->query('PRAGMA table_info(' . $table . ')') as $row) $columns[strtoupper($row['name'])] = (object)$row; return $columns; }
    function SelectLimit($sql, $limit, $offset) { return $this->Execute($sql . ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset); }
}
class ExplorerTestSystem {
    function validateCsrfToken($scope, $token) { return $token === 'test-token'; }
    function renderCsrfField($scope) { echo '<input type="hidden" name="csrf_token" value="test-token">'; }
    function getErrorBox($message) { echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); }
    function go2Page($page) {}
}
require_once 'modules/member/module.php';
require_once 'modules/media/module.php';
require_once 'modules/explorer/class.ExplorerFiles.php';
require_once 'modules/explorer/class.ExplorerMedia.php';
require_once 'modules/explorer/class.ExplorerResponse.php';
function explorerTestSetup($base, $database = ':memory:') {
    global $cfg, $db, $tablepre, $sys_lanai;
    foreach (array('site', 'site/data', 'site/images') as $dir) if (!is_dir($base . '/' . $dir)) mkdir($base . '/' . $dir, 0755, true);
    $cfg = array('dir' => $base . '/site', 'datadir' => $base . '/site/data', 'tablepre' => 'test_', 'explorer_trash' => $base . '/trash');
    $tablepre = 'test_'; $db = new ExplorerTestDb($database); $sys_lanai = new ExplorerTestSystem();
    $db->Execute("CREATE TABLE IF NOT EXISTS test_media (mediaId INTEGER PRIMARY KEY AUTOINCREMENT, fileName TEXT, origName TEXT, filePath TEXT, thumbPath TEXT, mediaType TEXT, mimeType TEXT, fileSize INTEGER, width INTEGER, height INTEGER, altText TEXT, userId INTEGER, createdAt TEXT)");
    $db->Execute("CREATE TABLE IF NOT EXISTS test_user (userId INTEGER PRIMARY KEY, userPrivilege TEXT)");
    $db->Execute("INSERT OR IGNORE INTO test_user VALUES (1,'a')");
    $_SESSION = array('uid' => 1);
    $media = new Media(); if (!$media->ensureLibraryFields()) throw new RuntimeException('Test schema failed.');
    return new ExplorerFiles($cfg, new ExplorerMedia($db, $cfg));
}
function explorerTestRemove($path, $base) {
    // Remove only the disposable directory created by this test invocation.
    if ($path !== $base && !str_starts_with($path, $base . '/')) throw new RuntimeException('Unsafe test cleanup.');
    if (is_link($path) || is_file($path)) unlink($path);
    elseif (is_dir($path)) { foreach (scandir($path) as $name) if ($name !== '.' && $name !== '..') explorerTestRemove($path . '/' . $name, $base); rmdir($path); }
}
