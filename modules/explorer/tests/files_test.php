<?php
require __DIR__ . '/support.php';
function verify($condition, $label) { if (!$condition) throw new RuntimeException($label); echo 'PASS: ' . $label . PHP_EOL; }
function rejects($callback, $label) { try { $callback(); } catch (RuntimeException $e) { verify(true, $label); return; } throw new RuntimeException($label); }
$base = str_replace('\\', '/', sys_get_temp_dir()) . '/lanai-explorer-test-' . bin2hex(random_bytes(8));
mkdir($base);
try {
    $files = explorerTestSetup($base);
    $bridge = new ExplorerMedia($db, $cfg);
    $files->locked(function () use ($files, $bridge, $base) {
        global $db;
        $files->createFolder('data', '', 'Reports');
        $files->createFolder('data', '', 'Archive');
        file_put_contents($files->path('data', 'Reports/one.txt', false), 'one');
        file_put_contents($files->path('data', 'Reports/two.txt', false), 'two two');
        touch($files->path('data', 'Reports/one.txt'), strtotime('2026-09-23 23:59:59'));
        touch($files->path('data', 'Reports/two.txt'), strtotime('2026-09-24 00:00:00'));
        clearstatcache();
        $rows = $files->entries('data', '', array('recursive' => true, 'q' => 'one', 'from' => '2026-09-23', 'to' => '2026-09-23', 'type' => 'text'));
        verify(count($rows['items']) === 1 && $rows['items'][0]['path'] === 'Reports/one.txt', 'Recursive search combines filename, type, and inclusive dates');
        $rows = $files->entries('data', 'Reports', array('sort' => 'size', 'order' => 'desc'));
        verify($rows['items'][0]['name'] === 'two.txt', 'Numeric size sorting');
        foreach (array('../outside', '/etc/passwd', 'Reports/../one.txt', 'Reports\\one.txt', '.hidden', 'cache/test.txt', 'Reports/code.php', 'Reports/shell.php.txt', 'Reports/CON.txt') as $invalid) rejects(function () use ($files, $invalid) { $files->path('data', $invalid, false); }, 'Reject unsafe path: ' . $invalid);
        rejects(function () use ($files) { $files->trash('data', ''); }, 'Cannot trash a content root');
        $id = $bridge->register($files, 'data', 'Reports/one.txt', 1);
        verify($bridge->register($files, 'data', 'Reports/one.txt', 1) === $id, 'Registering twice reuses the Media entry');
        $original = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        $files->relocate('data', 'Reports', 'data', 'Documents');
        $row = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        verify($row['explorerPath'] === 'Documents/one.txt' && $row['filePath'] === $original['filePath'], 'Folder rename preserves public Media URL and updates source path');
        $db->failUpdates = true;
        rejects(function () use ($files) { $files->relocate('data', 'Documents/one.txt', 'images', 'one.txt'); }, 'Database failure rejects move');
        verify(is_file($files->path('data', 'Documents/one.txt')) && !file_exists($files->path('images', 'one.txt', false)), 'Failed Media update rolls filesystem move back');
        $files->relocate('data', 'Documents/one.txt', 'images', 'one.txt');
        $row = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        verify($row['explorerRoot'] === 'images' && $row['filePath'] === $original['filePath'], 'Cross-root move preserves public Media URL');
        $trashId = $files->trash('images', 'one.txt');
        $row = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        verify($row['explorerTrashId'] === $trashId && count($files->trashList()) === 1, 'Trash records original location and hides Media registration');
        file_put_contents($files->path('images', 'one.txt', false), 'new');
        rejects(function () use ($files, $trashId) { $files->restore($trashId, 'skip'); }, 'Restore never silently overwrites a new file');
        $restored = $files->restore($trashId, 'keep');
        $row = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        verify($restored === 'one (2).txt' && $row['explorerTrashId'] === null && $row['explorerPath'] === $restored, 'Keep-both restore updates Media reference');
        file_put_contents($files->path('images', 'replacement.txt', false), 'replacement bytes');
        $files->relocate('images', 'replacement.txt', 'images', $restored, 'replace');
        $row = $db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->fields;
        verify($row['explorerTrashId'] === null && $row['filePath'] === $original['filePath'] && (int)$row['fileSize'] === 17, 'Replace preserves target Media URL and updates size');
        verify(count($files->trashList()) === 1, 'Replaced version remains recoverable in Trash');
        $archive = ExplorerResponse::archive($files, 'images', array('one.txt', $restored));
        $zip = new ZipArchive(); $zip->open($archive);
        verify($zip->numFiles === 2 && $zip->getFromName($restored) === 'replacement bytes', 'Bulk ZIP contains selected file contents');
        $zip->close(); unlink($archive);
        $trashId = $files->trash('images', $restored); $files->purge($trashId);
        verify($db->Execute('SELECT * FROM test_media WHERE mediaId=' . $id)->EOF, 'Permanent deletion removes the trashed registration');
        rejects(function () use ($files) { $files->relocate('data', 'Documents', 'data', 'Documents/nested'); }, 'Folder cannot be moved into itself');
        $files->createFolder('data', '', 'Many');
        for ($n = 0; $n < 103; $n++) file_put_contents($files->path('data', 'Many/' . $n . '.txt', false), 'x');
        $rows = $files->entries('data', 'Many', array('page' => 2));
        verify($rows['total'] === 103 && count($rows['items']) === 3, 'Large folders paginate correctly');
        if (@symlink($base, $files->roots['data']['path'] . '/escape')) rejects(function () use ($files) { $files->path('data', 'escape'); }, 'Symlink escape blocked');
    });
    echo "All Explorer filesystem checks passed.\n";
} finally { explorerTestRemove($base, $base); }
