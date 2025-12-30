<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);
$cnt = new Contact();
?>

<script>
    function validate(frmObj) {
        if (frmObj.name.value.trim() === "") {
            alert('<?=_REQUIRE_FIELDS;?> <?=_CONTACT_NAME;?>');
            return false;
        }
        if (frmObj.email.value.trim() === "") {
            alert('<?=_REQUIRE_FIELDS;?> <?=_CONTACT_EMAIL;?>');
            return false;
        }
        if (frmObj.message.value.trim() === "") {
            alert('<?=_REQUIRE_FIELDS;?> <?=_CONTACT_MESSAGE;?>');
            return false;
        }
        if (frmObj.captcha.value.trim() === "") {
            alert('<?=_REQUIRE_FIELDS;?> <?=_VERIFY;?>');
            return false;
        }
        return true;
    }
    function MM_jumpMenu(targ,selObj,restore){ //v3.0
        eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
        if (restore) selObj.selectedIndex=0;
    }
</script>

<div class="col-md-8">
    <div class="article-content bg-white p-4 rounded shadow-sm">
        <div class="container my-4">
            <h3 class="mb-3 text-primary"><?= _CONTACT; ?></h3>
            <p class="text-muted"><?= _CONTACT_INSTRUCTION; ?></p>

            <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onsubmit="return validate(this)" class="needs-validation" novalidate>
                <input type="hidden" name="modname" value="<?= $module_name; ?>">
                <input type="hidden" name="mf" value="send">
                <input type="hidden" name="cid" value="<?= isset($_REQUEST['cid']) ? $_REQUEST['cid'] : '' ?>">

                <input type="hidden" name="ac" value="send">

                <div class="mb-3">
                    <label class="form-label"><?= _CONTACT_TO; ?></label>
                    <?= $cnt->getContactCombo("to"); ?>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= _CONTACT_NAME; ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= _CONTACT_EMAIL; ?> <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= _CONTACT_TITLE; ?></label>
                    <input type="text" name="title" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= _CONTACT_MESSAGE; ?> <span class="text-danger">*</span></label>
                    <textarea name="message" rows="5" class="form-control" required></textarea>
                </div>

                <!-- CAPTCHA -->
                <div class="mb-3">
                    <label class="form-label"><?= _VERIFY; ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="captcha" class="form-control" required>
                        <span class="input-group-text p-0">
                            <img src="images/captcha.php" alt="captcha"
                                 onclick="this.src='images/captcha.php?'+Math.random();"
                                 style="height:38px; cursor:pointer;">
                        </span>
                    </div>
                    <small class="text-muted">Click image to refresh</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><?= _SEND; ?></button>
                    <button type="reset" class="btn btn-secondary"><?= _RESET; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="sidebar">
        <?php
        settype($_REQUEST['cid'], "integer");
        if (!empty($_REQUEST['cid'])) {
            $cndetail = $cnt->getContactById($_REQUEST['cid']);
            if ($cndetail->recordcount() > 0) {
                ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <?= $cndetail->fields['conFname'] ?> <?= $cndetail->fields['conLname'] ?>
                        </h5>
                        <p class="card-text mb-1"><?= $cndetail->fields['conPosition'] ?></p>
                        <p class="card-text small text-muted mb-2">
                            <?= $cndetail->fields['conAddress1'] ?><br>
                            <?= $cndetail->fields['conAddress2'] ?><br>
                            <?= $cndetail->fields['conCity'] . " " . $cndetail->fields['conState'] . " " . $cndetail->fields['conZipcode'] . " " . $cndetail->fields['cntId']; ?>
                        </p>
                        <p class="mb-0">
                            <strong><?= _PHONE; ?>:</strong> <?= $cndetail->fields['conPhone'] ?><br>
                            <strong><?= _FAX; ?>:</strong> <?= $cndetail->fields['conFax'] ?><br>
                            <strong><?= _MOBILE; ?>:</strong> <?= $cndetail->fields['conMobile'] ?><br>
                            <strong><?= _EMAIL; ?>:</strong> <?= $cndetail->fields['conEmail'] ?><br>
                            <strong><?= _HOMEPAGE; ?>:</strong> <?= $cndetail->fields['conURL'] ?><br>
                        </p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
