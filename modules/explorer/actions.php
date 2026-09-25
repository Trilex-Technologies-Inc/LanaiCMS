<?php
if (!defined('LANAI_EXPLORER_READY')) { http_response_code(403); exit; }
require_once __DIR__ . '/request.php';
explorer_admin();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $media = new Media();
    if (!$media->ensureLibraryFields()) throw new RuntimeException('Unable to prepare Media reference fields.');
    $bridge = new ExplorerMedia($db, $cfg);
    $files = new ExplorerFiles($cfg, $bridge);
    $input = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    $action = explorer_string($input, 'action', 'list');
    $root = explorer_string($input, 'root', array_key_first($files->roots));
    $dir = explorer_string($input, 'dir');
    $result = array();
    if ($action === 'list') {
        $filters = array();
        foreach (array('q','type','from','to','recursive','sort','order','page') as $key) $filters[$key] = explorer_string($_GET, $key);
        $result = $files->entries($root, $dir, $filters);
        $registered = array();
        foreach ($bridge->rows() as $row) if ($row['explorerRoot'] === $root && $row['explorerTrashId'] === null) $registered[$row['explorerPath']] = (int)$row['mediaId'];
        foreach ($result['items'] as &$item) $item['mediaId'] = $registered[$item['path']] ?? null;
        unset($item);
    } elseif ($action === 'trashList') {
        $result = array('items' => $files->trashList());
    } elseif ($action === 'preview') {
        $path = $files->path($root, explorer_string($_GET, 'path'));
        $type = $files->kind($path);
        if ($type === 'text' && is_file($path)) {
            $text = file_get_contents($path, false, null, 0, 100001);
            $result = array('type' => 'text', 'text' => mb_convert_encoding(substr($text, 0, 100000), 'UTF-8', 'UTF-8'), 'truncated' => strlen($text) > 100000);
        } elseif (in_array($type, array('image','pdf'))) $result = array('type' => $type);
        else $result = array('type' => 'unsupported');
    } else {
        explorer_csrf();
        $paths = $_POST['paths'] ?? array();
        if (!is_array($paths) || count($paths) > 100) throw new RuntimeException('Select at most 100 items.');
        foreach ($paths as $path) if (!is_string($path) || $path === '') throw new RuntimeException('Invalid selection.');
        $paths = array_values(array_unique($paths));
        // A selected parent already includes its children.
        $paths = array_values(array_filter($paths, function ($path) use ($paths) { foreach ($paths as $parent) if ($parent !== $path && str_starts_with($path, $parent . '/')) return false; return true; }));
        $policy = explorer_string($_POST, 'collision', 'skip');
        if ($action === 'zip') {
            if (!$paths) throw new RuntimeException('Select files to download.');
            $temp = $files->locked(function () use ($files, $root, $paths) { return ExplorerResponse::archive($files, $root, $paths); });
            try { ExplorerResponse::stream($temp, 'files.zip'); } finally { unlink($temp); }
            exit;
        }
        $result = $files->locked(function () use ($files, $bridge, $root, $dir, $paths, $policy, $action) {
            if ($action === 'mkdir') return array('message' => 'Created ' . $files->createFolder($root, $dir, explorer_string($_POST, 'name')));
            if ($action === 'rename') {
                if (count($paths) !== 1) throw new RuntimeException('Select one item to rename.');
                $parent = dirname($paths[0]); $target = ($parent === '.' ? '' : $parent . '/') . $files->name(explorer_string($_POST, 'name'));
                return array('message' => 'Renamed to ' . $files->relocate($root, $paths[0], $root, $target, $policy));
            }
            if ($action === 'upload') {
                if (!isset($_FILES['file'])) throw new RuntimeException('No file received. Check the upload size limit.');
                return array('message' => 'Uploaded ' . $files->upload($root, $dir, $_FILES['file'], $policy));
            }
            if (!in_array($action, array('move','trash','register','restore','purge'), true)) throw new RuntimeException('Unknown Explorer action.');
            if (!$paths) throw new RuntimeException('Select at least one item.');
            if ($action === 'purge' && explorer_string($_POST, 'confirm') !== 'permanent') throw new RuntimeException('Confirm permanent deletion.');
            $results = array();
            foreach ($paths as $path) {
                try {
                    if ($action === 'move') {
                        $targetDir = explorer_string($_POST, 'targetDir');
                        $target = ($targetDir === '' ? '' : $files->normal($targetDir) . '/') . basename($path);
                        $files->relocate($root, $path, explorer_string($_POST, 'targetRoot', $root), $target, $policy);
                    } elseif ($action === 'trash') $files->trash($root, $path);
                    elseif ($action === 'register') $bridge->register($files, $root, $path, $_SESSION['uid']);
                    elseif ($action === 'restore') $files->restore($path, $policy);
                    elseif ($action === 'purge') $files->purge($path);
                    $results[] = array('path' => $path, 'ok' => true);
                } catch (Throwable $e) { $results[] = array('path' => $path, 'ok' => false, 'error' => $e->getMessage()); }
            }
            return array('results' => $results);
        });
    }
    echo json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    if (http_response_code() < 400) http_response_code(400);
    echo json_encode(array('error' => $e instanceof RuntimeException ? $e->getMessage() : 'Operation failed. Check server logs and try again.'), JSON_INVALID_UTF8_SUBSTITUTE);
}
