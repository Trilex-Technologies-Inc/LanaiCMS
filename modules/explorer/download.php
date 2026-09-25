<?php
require __DIR__ . '/bootstrap.php';
explorer_admin();
try {
    $files = new ExplorerFiles($cfg);
    $root = explorer_string($_GET, 'root', array_key_first($files->roots));
    $relative = explorer_string($_GET, 'path', explorer_string($_GET, 'f'));
    $path = $files->path($root, $relative);
    ExplorerResponse::stream($path, basename($path), explorer_string($_GET, 'preview') === '1');
} catch (Throwable $e) { http_response_code(404); header('Content-Type: text/plain'); echo 'File not available.'; }
