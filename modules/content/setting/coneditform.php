<?php
if (!defined('LANAI_ADMIN_REQUEST')) { http_response_code(403); exit; }
require_once dirname(__DIR__).'/module.php';
require_once dirname(__DIR__).'/editor.php';
$content = new Content();
$contentAction = 'edit';
$contentNeedsReview = lanai_content_needs_review($sys_lanai);
try {
    if (!$content->ensureEditorSchema()) throw new RuntimeException();
    $record = $content->getContentById(is_scalar($_GET['mid']??null)?(int)$_GET['mid']:0);
    if (!$record || $record->EOF || !lanai_content_can_edit($sys_lanai,$record->fields)) {
        $sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION); return;
    }
    $contentValues = $record->fields;
} catch (Throwable $error) {
    $sys_lanai->getErrorBox('The content could not be loaded. Check database access and the content schema.'); return;
}
include __DIR__.'/editor_form.php';
