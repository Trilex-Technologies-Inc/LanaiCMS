<?php
require __DIR__ . '/bootstrap.php';
// Only explicitly registered, non-trashed Media records are public.
try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) throw new RuntimeException('Not found.');
    $record = $db->Execute('SELECT * FROM ' . $cfg['tablepre'] . 'media WHERE mediaId=' . $id);
    if (!$record || $record->EOF || empty($record->fields['explorerRoot']) || !empty($record->fields['explorerTrashId'])) throw new RuntimeException('Not found.');
    $row = $record->fields;
    $files = new ExplorerFiles($cfg);
    ExplorerResponse::stream($files->path($row['explorerRoot'], $row['explorerPath']), $row['origName'], true);
} catch (Throwable $e) { http_response_code(404); header('Content-Type: text/plain'); echo 'File not found.'; }
