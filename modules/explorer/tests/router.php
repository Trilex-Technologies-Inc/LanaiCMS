<?php
if (PHP_SAPI !== 'cli-server' || getenv('LANAI_EXPLORER_TEST') !== '1') { http_response_code(404); exit; }
require __DIR__ . '/support.php';
$base = getenv('LANAI_EXPLORER_TEST_DIR');
if (!$base || !is_dir($base)) throw new RuntimeException('Missing isolated test folder.');
$files = explorerTestSetup($base, $base . '/test.sqlite');
define('LANAI_EXPLORER_READY', true);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/modules/explorer/api.php') { require 'modules/explorer/actions.php'; return; }
if ($path === '/modules/explorer/download.php') {
    try { ExplorerResponse::stream($files->path($_GET['root'], $_GET['path']), basename($_GET['path']), !empty($_GET['preview'])); }
    catch (Throwable $e) { http_response_code(404); echo 'Not found.'; }
    return;
}
if (in_array($path, array('/modules/explorer/explorer.js','/modules/explorer/explorer.css'), true)) {
    header('Content-Type: ' . (str_ends_with($path, '.js') ? 'text/javascript' : 'text/css')); readfile('.' . $path); return;
}
$_SERVER['PHP_SELF'] = '/setting.php';
echo '<!doctype html><html><head><meta charset="utf-8"><title>Explorer test</title></head><body>';
include 'modules/explorer/setting/index.php';
echo '</body></html>';
