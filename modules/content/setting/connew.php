<?php
if (!defined('LANAI_ADMIN_REQUEST')) { http_response_code(403); exit; }
require_once dirname(__DIR__).'/module.php';
require_once dirname(__DIR__).'/editor.php';
$content = new Content();
if (!$sys_lanai->userHasCapability('edit_content') && !$sys_lanai->userHasCapability('edit_own_content')) {
    $sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION); return;
}
$contentNeedsReview = lanai_content_needs_review($sys_lanai);
$contentAction = 'new';
$contentValues = array('conTitle'=>'','conBody1'=>'','conBody2'=>'','conAllowComments'=>'n','conMenu'=>'no');
try {
    if (!$content->ensureEditorSchema()) throw new RuntimeException();
} catch (Throwable $error) {
    $contentError = 'Content storage needs an upgrade. The database account must be able to add the comments column before saving.';
}
include __DIR__.'/editor_form.php';
