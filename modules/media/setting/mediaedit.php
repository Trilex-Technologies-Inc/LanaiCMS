<?php
if (stripos($_SERVER['PHP_SELF'], 'setting.php') === false) die('Direct access denied.');
include_once('modules/media/module.php');
$media = new Media();
if (!$media->canManage()) { $sys_lanai->getErrorBox('Administrator access required.'); return; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$sys_lanai->validateCsrfToken('media', isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox('Invalid request, or the upload exceeded the server batch limit. Please return to Files and try again with a smaller batch.');
    return;
}
$action = isset($_POST['ac']) && is_string($_POST['ac']) ? $_POST['ac'] : '';
$id = isset($_POST['mediaId']) && is_scalar($_POST['mediaId']) ? (int) $_POST['mediaId'] : 0;
switch ($action) {
    case 'upload':
        $uploads = array();
        if (isset($_FILES['mediaFiles']['name']) && is_array($_FILES['mediaFiles']['name'])) {
            foreach ($_FILES['mediaFiles']['name'] as $key => $name) {
                $file = array();
                foreach (array('name', 'tmp_name', 'error', 'size') as $field) $file[$field] = isset($_FILES['mediaFiles'][$field][$key]) ? $_FILES['mediaFiles'][$field][$key] : null;
                $uploads[] = $file;
            }
        } elseif (isset($_FILES['mediaFile'])) {
            $uploads[] = $_FILES['mediaFile'];
        }
        $success = 0;
        $failed = array();
        $alt = isset($_POST['altText']) && is_string($_POST['altText']) ? mb_substr($_POST['altText'], 0, 255) : '';
        foreach ($uploads as $file) {
            if ($media->saveUpload($file, $alt) !== false) $success++;
            else $failed[] = is_string($file['name']) ? $file['name'] : 'Invalid file';
        }
        $_SESSION['media_notice'] = $success . ' file(s) uploaded.';
        if ($failed) $_SESSION['media_notice'] .= ' Failed: ' . implode(', ', $failed) . '. Check file types, sizes, and storage permissions.';
        if (!$uploads) $_SESSION['media_notice'] = 'No files received. Select files and check the server upload limits.';
        break;
    case 'details':
        if (!$media->ensureLibraryFields()) { $sys_lanai->getErrorBox('Unable to prepare media details.'); return; }
        $record = $media->getMediaById($id);
        if (!$record || $record->EOF) { $sys_lanai->getErrorBox('File not found.'); return; }
        $values = array();
        foreach (array('title' => 255, 'caption' => 10000, 'altText' => 255) as $field => $limit) {
            if (!isset($_POST[$field]) || !is_string($_POST[$field]) || mb_strlen($_POST[$field]) > $limit) {
                $sys_lanai->getErrorBox('Invalid or overly long file details. Please go back and check the fields.'); return;
            }
            $values[$field] = trim($_POST[$field]);
        }
        if (!$media->updateDetails($id, $values['title'], $values['caption'], $values['altText'])) {
            $sys_lanai->getErrorBox('Details could not be saved. Please go back and try again.'); return;
        }
        $_SESSION['media_notice'] = 'File details saved.';
        break;
    case 'delete':
        try {
            $record = $media->getMediaById($id);
            $registered = $record && !$record->EOF && !empty($record->fields['explorerRoot']);
            $_SESSION['media_notice'] = $media->setDeleteMedia($id) ? ($registered ? 'File moved to Explorer Trash.' : 'File deleted.') : 'The file could not be deleted.';
        } catch (RuntimeException $e) { $sys_lanai->getErrorBox(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')); return; }
        break;
    default:
        $sys_lanai->getErrorBox('Unknown file action.'); return;
}
echo '<p role="status">' . htmlspecialchars($_SESSION['media_notice'], ENT_QUOTES, 'UTF-8') . '</p><p><a href="setting.php?modname=media">Back to files</a></p>';
$sys_lanai->go2Page('setting.php?modname=media');
