<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$content = new News();

settype($_REQUEST['cid'], "integer");
$rs = $content->getNewsById($_REQUEST['cid']);

include_once("include/lanai/class.read.php");
include_once("include/lanai/class.comment.php");
$readObj = new ReadTotal("news", $_REQUEST['cid']);
$commObj = new Comment();

?>


<?php
if ($rs->recordcount() > 0) {
?>
<section class="article-hero">
    <div class="container">
        <h1 class="display-5 fw-bold"><?=$rs->fields['nwsTitle'];?></h1>
        <div class="article-meta mt-2">
            <i class="fas fa-calendar-alt"></i> October 25, 2025 &nbsp;|&nbsp;
            <i class="fas fa-user"></i> By Admin &nbsp;|&nbsp;
            <i class="fas fa-folder"></i> Tech
        </div>
    </div>
</section>

<!-- Main Article Content -->
<div class="container my-5">
    <div class="row g-5">
        <div class="col-md-12">

            <!-- Article Section -->
            <article class="mb-5">
                <h2 class="text-primary fw-bold mb-2"><?=$rs->fields['nwsTitle'];?></h2>
                <?=$sys_lanai->setPageTitle($rs->fields['nwsTitle']);?>

                <p class="text-muted small mb-4">
                    <i class="bi bi-calendar3 me-1"></i>
                    <?=adodb_date2("l, d F Y", $rs->fields['nwsCreate']);?>
                </p>

                <div class="lead mb-3"><?=$rs->fields['nwsPreface'];?></div>
                <div class="mb-4"><?=$rs->fields['nwsBody'];?></div>

                <div class="border-top pt-3 text-muted small">
                    <?=$readObj->getReadTotal("news", $_REQUEST['cid']);?> <?=_NEWS_READ_TOTAL;?>
                    <?php
                    $numComment = $commObj->getCommentTotal("news", $_REQUEST['cid']);
                    if ($numComment > 0) {
                        echo " | $numComment " . _NWS_COMMENT_TOTAL;
                    }
                    ?>
                    <a href="#commentForm" class="ms-2 text-decoration-none"><?=_POST_COMMENT;?></a>
                </div>
            </article>

            <!-- Comments Section -->
            <section id="comments" class="mb-5">
                <h4 class="mb-4 border-bottom pb-2"><?=_NWS_COMMENT_TOTAL;?></h4>
                <?php
                $rscom = $commObj->getComment("news", $_REQUEST['cid']);
                if ($rscom->recordcount() > 0) {
                    while (!$rscom->EOF) {
                        ?>
                        <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body">
                                <h6 class="card-title mb-1">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <a href="mailto:<?=$rscom->fields['comEmail'];?>" class="text-decoration-none">
                                        <?=$rscom->fields['comAuthor'];?>
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-clock me-1"></i>
                                    <?=adodb_date2("l, d F Y - H:i:s", $rscom->fields['comDate']);?>
                                </p>
                                <p class="card-text"><?=nl2br($rscom->fields['comDetail']);?></p>
                            </div>
                        </div>
                        <?php
                        $rscom->movenext();
                    }
                } else {
                    ?>
                    <p class="text-muted"></p>
                <?php } ?>
            </section>

            <!-- Post Comment Form -->
            <section  class="mb-5">
                <h4 class="mb-4 text-primary"><?=_POST_COMMENT;?></h4>

                <form id="commentForm" name="form" method="POST" action="<?=$_SERVER['PHP_SELF'];?>" class="needs-validation" novalidate>
                    <input type="hidden" name="modname" value="news">
                    <input type="hidden" name="mf" value="nwscomment">
                    <input type="hidden" name="cid" value="<?=$_REQUEST['cid'];?>">

                    <div class="mb-3">
                        <label class="form-label"><?=_NAME;?> <span class="text-danger">*</span></label>
                        <input type="text" name="comAuthor" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?=_EMAIL;?> <span class="text-danger">*</span></label>
                        <input type="email" name="comEmail" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?=_VERIFY;?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="txtVerify" class="form-control" required>
                            <span class="input-group-text p-0">
                        <img src="images/captcha.php" alt="captcha" style="height:38px;">
                    </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?=_COMMENT_INTR;?> <span class="text-danger">*</span></label>
                        <textarea name="comDetail" rows="5" class="form-control" required></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><?=_NWS_POST_COMMENT;?></button>
                        <button type="reset" class="btn btn-secondary"><?=_CLEAR;?></button>
                    </div>
                </form>
            </section>

            <script src="include/jsvalidator/gen_validatorv2.js"></script>
            <script>
                var frmvalidator = new Validator("commentForm"); 

                frmvalidator.addValidation("comAuthor", "req", "<?=_NWS_AUTHOR_EMPTY;?>");
                frmvalidator.addValidation("comEmail", "req", "<?=_NWS_EMAIL_EMPTY;?>");
                frmvalidator.addValidation("comEmail", "email", "<?=_NWS_EMAIL_INVALID;?>");
                frmvalidator.addValidation("comDetail", "req", "<?=_NWS_COMMENT_EMPTY;?>");
                frmvalidator.addValidation("txtVerify", "req", "<?=_NWS_VERIFY_EMPTY;?>");
            </script>

            <?php
            } else {
                $sys_lanai->getErrorBox(_NWS_NOT_FOUND);
            }
            ?>
        </div>

      
       
    </div>
</div>



