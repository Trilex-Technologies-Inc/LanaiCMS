<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);
$cnt = new Contact();

$captcha_provider = isset($cfg['captcha_provider']) ? $cfg['captcha_provider'] : 'default';
if ($captcha_provider !== 'cloudflare') {
    $captcha_provider = 'default';
}

$turnstile_site_key = isset($cfg['turnstile_site_key']) ? trim($cfg['turnstile_site_key']) : '';
$turnstile_secret_key = isset($cfg['turnstile_secret_key']) ? trim($cfg['turnstile_secret_key']) : '';
$turnstile_enabled = ($captcha_provider === 'cloudflare' && $turnstile_site_key !== '' && $turnstile_secret_key !== '');
?>

<?php if ($turnstile_enabled) { ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php } ?>
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

        <?php if ($captcha_provider === 'default') { ?>
        if (frmObj.captext.value.trim() === "") {
            alert('<?=_REQUIRE_FIELDS;?> Captcha');
            return false;
        }
        <?php } ?>
        
        <?php if ($turnstile_enabled) { ?>
        // Check if Turnstile widget is loaded and completed
        if (typeof turnstile !== 'undefined') {
            const response = turnstile.getResponse();
            if (!response) {
                alert('<?=_REQUIRE_FIELDS;?> Please complete the security verification');
                return false;
            }
        }
        <?php } ?>
        
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

                <?php if ($captcha_provider === 'default') { ?>
                    <div class="mb-3">
                        <label class="form-label">Captcha <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" name="captext" class="form-control w-auto" size="12" maxlength="5" placeholder="Enter captcha">
                            <img src="images/captcha.php?hash=<?= md5(time()); ?>" alt="Captcha" class="border rounded">
                        </div>
                    </div>
                <?php } elseif ($turnstile_enabled) { ?>
                    <!-- Cloudflare Turnstile -->
                    <div class="mb-3">
                        <label class="form-label">Security Verification <span class="text-danger">*</span></label>
                        <div class="cf-turnstile" 
                             data-sitekey="<?= $turnstile_site_key; ?>"
                             data-theme="light"
                             data-language="en">
                        </div>
                        <small class="text-muted">Complete the verification to submit the form</small>
                    </div>
                <?php } elseif ($turnstile_site_key !== '' || $turnstile_secret_key !== '') { ?>
                    <div class="alert alert-warning">
                        Turnstile is not fully configured. Please set both Site Key and Secret Key in Config.
                    </div>
                <?php } ?>

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
