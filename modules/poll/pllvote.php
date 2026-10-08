<?php
if (basename($_SERVER['PHP_SELF'] ?? '') !== 'module.php') { http_response_code(403); exit; }
global $db,$cfg,$sys_lanai;
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !$sys_lanai->validateCsrfToken('poll_vote', isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox('Invalid vote request. Please reload the page and try again.'); return;
}
foreach (array('mid','voteChoice','cid') as $key) {
    if (isset($_POST[$key]) && (!is_string($_POST[$key]) || !ctype_digit($_POST[$key]))) { $sys_lanai->getErrorBox('Invalid vote.'); return; }
}
require_once __DIR__ . '/voting.php';
$mid=(int)($_POST['mid']??0); $cid=(int)($_POST['cid']??0);
try {
    $voting=new LanaiPollVoting($db,$cfg['tablepre']);
    $saved=$voting->vote($mid,(int)($_POST['voteChoice']??0),$_SERVER['REMOTE_ADDR']??'');
} catch (Throwable $error) { $saved=false; }
if ($saved) {
    $sys_lanai->go2Page($cid>0 ? 'module.php?modname=content&cid='.$cid : 'module.php?modname=poll&mid='.$mid);
} else {
    $sys_lanai->getErrorBox(($cfg['lang']??'')==='thai' ? 'ไม่สามารถบันทึกโหวตได้ คุณอาจโหวตไปแล้ว หรือโพลไม่เปิดใช้งาน' : 'The vote could not be saved. You may have voted recently, or this poll is unavailable.');
    echo '<p><a href="module.php?modname='.($cid>0 ? 'content&amp;cid='.$cid : 'poll&amp;mid='.$mid).'">Back</a></p>';
}
