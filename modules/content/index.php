<?php

if (stripos($_SERVER['PHP_SELF'], 'module.php') === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
include_once("modules/$module_name/module.php");

$content = new Content();
$contentId = isset($_REQUEST['cid']) ? (int) $_REQUEST['cid'] : 0;
$rs = $content->getContentById($contentId);
if (!$rs || $rs->recordcount() < 1 || $rs->fields['conActive'] !== 'y') {
    $sys_lanai->getErrorBox(_CONTENT_NOT_FOUND);
    return;
}

$allowComments = $rs->fields['conAllowComments'] === 'y';
if ($allowComments) {
    include_once('include/lanai/class.comment.php');
    $comments = new Comment();
}
?>
<?=$sys_lanai->setPageTitle($rs->fields['conTitle']);?>

<section class="article-hero">
    <div class="container">
        <h1 class="display-5 fw-bold"><?=htmlspecialchars($rs->fields['conTitle'], ENT_QUOTES, 'UTF-8');?></h1>
        <?php if (!empty($rs->fields['conModified'])) { ?>
            <div class="article-meta mt-2"><i class="bi bi-calendar3 me-1"></i><?=adodb_date2('F j, Y', $rs->fields['conModified']);?></div>
        <?php } ?>
    </div>
</section>

<div class="container my-5">
    <article class="article-content bg-white p-4 rounded shadow-sm">
        <?=$rs->fields['conBody1'];?>
        <?=$rs->fields['conBody2'];?>
    </article>

    <?php if ($allowComments) { ?>
        <section id="comments" class="mt-5">
            <?php $commentTotal = $comments->getCommentTotal('content', $contentId); ?>
            <h2 class="h4 mb-4 border-bottom pb-2"><?=_CONTENT_COMMENTS;?> <span class="text-muted">(<?=$commentTotal;?>)</span></h2>
            <?php
            $commentRows = $comments->getComment('content', $contentId);
            while ($commentRows && !$commentRows->EOF) {
                ?>
                <div class="card mb-3 border-0 border-start border-4 border-primary shadow-sm">
                    <div class="card-body">
                        <h3 class="h6 mb-1"><i class="bi bi-person-circle me-1"></i><?=htmlspecialchars($commentRows->fields['comAuthor'], ENT_QUOTES, 'UTF-8');?></h3>
                        <p class="text-muted small mb-2"><i class="bi bi-clock me-1"></i><?=adodb_date2('F j, Y - H:i', $commentRows->fields['comDate']);?></p>
                        <p class="card-text mb-0"><?=nl2br(htmlspecialchars($commentRows->fields['comDetail'], ENT_QUOTES, 'UTF-8'));?></p>
                    </div>
                </div>
                <?php
                $commentRows->movenext();
            }
            ?>

            <div class="mt-5">
                <h2 class="h4 mb-4"><?=_CONTENT_POST_COMMENT;?></h2>
                <form method="post" action="module.php" class="needs-validation">
                    <input type="hidden" name="modname" value="content">
                    <input type="hidden" name="mf" value="concomment">
                    <input type="hidden" name="cid" value="<?=$contentId;?>">
                    <?php $sys_lanai->renderCsrfField('content_comment'); ?>
                    <div class="mb-3">
                        <label class="form-label" for="comAuthor"><?=_CONTENT_COMMENT_NAME;?></label>
                        <input class="form-control" id="comAuthor" name="comAuthor" type="text" maxlength="80" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="comEmail"><?=_CONTENT_COMMENT_EMAIL;?></label>
                        <input class="form-control" id="comEmail" name="comEmail" type="email" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="txtVerify"><?=_CONTENT_COMMENT_VERIFY;?></label>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <input class="form-control" id="txtVerify" name="txtVerify" type="text" maxlength="5" style="max-width:180px" required>
                            <img src="images/captcha.php?hash=<?=md5((string) microtime(true));?>" alt="Captcha" class="border rounded">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="comDetail"><?=_CONTENT_COMMENTS;?></label>
                        <textarea class="form-control" id="comDetail" name="comDetail" rows="5" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-send me-1"></i><?=_CONTENT_POST_COMMENT;?></button>
                </form>
            </div>
        </section>
    <?php } ?>
</div>
