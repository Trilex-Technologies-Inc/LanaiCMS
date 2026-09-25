<?php

if (stripos($_SERVER['PHP_SELF'], 'module.php') === false) {
    die("You can't access this file directly...");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$sys_lanai->validateCsrfToken('content_comment', isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox(_CONTENT_COMMENT_FAILED);
    return;
}
$content = new Content();
$contentId = isset($_POST['cid']) ? (int) $_POST['cid'] : 0;
$rs = $content->getContentById($contentId);

if (!$rs || $rs->recordcount() < 1 || $rs->fields['conActive'] !== 'y' || $rs->fields['conAllowComments'] !== 'y') {
    $sys_lanai->getErrorBox(_CONTENT_NOT_FOUND);
    return;
}

$author = isset($_POST['comAuthor']) && is_string($_POST['comAuthor']) ? trim($_POST['comAuthor']) : '';
$email = isset($_POST['comEmail']) && is_string($_POST['comEmail']) ? trim($_POST['comEmail']) : '';
$detail = isset($_POST['comDetail']) && is_string($_POST['comDetail']) ? trim($_POST['comDetail']) : '';
$captcha = isset($_POST['txtVerify']) && is_string($_POST['txtVerify']) ? trim($_POST['txtVerify']) : '';
$sessionCaptcha = isset($_SESSION['captcha']) ? trim((string) $_SESSION['captcha']) : '';
unset($_SESSION['captcha']);

if ($author === '' || mb_strlen($author) > 80 || $detail === '' || strlen($detail) > 60000 || strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $sys_lanai->getErrorBox(_CONTENT_COMMENT_REQUIRED);
    return;
}
if ($captcha === '' || $sessionCaptcha === '' || strcasecmp($captcha, $sessionCaptcha) !== 0) {
    $sys_lanai->getErrorBox(_CONTENT_CAPTCHA_INVALID);
    return;
}

include_once('include/lanai/class.comment.php');
$comments = new Comment();
if (!$comments->addComment('content', $contentId, $detail, $author, $email)) {
    $sys_lanai->getErrorBox(_CONTENT_COMMENT_FAILED);
    return;
}
$sys_lanai->go2Page('module.php?modname=content&cid=' . $contentId . '#comments');

?>
