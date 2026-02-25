<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

// get user info
$mem_lanai = new User();
$captcha_provider = isset($cfg['captcha_provider']) ? $cfg['captcha_provider'] : 'default';
if ($captcha_provider !== 'cloudflare') {
    $captcha_provider = 'default';
}
$turnstile_site_key = isset($cfg['turnstile_site_key']) ? trim($cfg['turnstile_site_key']) : '';
$turnstile_secret_key = isset($cfg['turnstile_secret_key']) ? trim($cfg['turnstile_secret_key']) : '';
$turnstile_enabled = ($captcha_provider === 'cloudflare' && $turnstile_site_key !== '' && $turnstile_secret_key !== '');

// use isset() to avoid undefined index
$userLogin = isset($_REQUEST['userLogin']) ? $_REQUEST['userLogin'] : '';
$ac = isset($_REQUEST['ac']) ? $_REQUEST['ac'] : '';

$rslogin = $mem_lanai->getUserLogin($userLogin);

if ($rslogin->recordcount() > 0) {
    $sys_lanai->getErrorBox(_LOGIN_EXIST . " <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
} else {
    if ($ac == "lostpass") {
        // use isset for all fields
        $userFname = isset($_REQUEST['userFname']) ? $_REQUEST['userFname'] : '';
        $userLname = isset($_REQUEST['userLname']) ? $_REQUEST['userLname'] : '';
        $userEmail = isset($_REQUEST['userEmail']) ? $_REQUEST['userEmail'] : '';
        $userPassword1 = isset($_REQUEST['userPassword1']) ? $_REQUEST['userPassword1'] : '';
        $userPassword2 = isset($_REQUEST['userPassword2']) ? $_REQUEST['userPassword2'] : '';
        $captext = isset($_REQUEST['captext']) ? trim($_REQUEST['captext']) : '';
        $captchaOk = false;

        if ($captcha_provider === 'default') {
            $sessionCaptcha = isset($_SESSION['captcha']) ? trim($_SESSION['captcha']) : '';
            $captchaOk = ($captext !== '' && $sessionCaptcha !== '' && strcasecmp($captext, $sessionCaptcha) === 0);
        } elseif ($turnstile_enabled) {
            $turnstile_response = isset($_POST['cf-turnstile-response']) ? trim($_POST['cf-turnstile-response']) : '';
            if ($turnstile_response !== '') {
                $verify_data = array(
                    'secret' => $turnstile_secret_key,
                    'response' => $turnstile_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                );
                $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verify_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                if (!curl_errno($ch)) {
                    $result = json_decode($response, true);
                    $captchaOk = !empty($result['success']);
                }
                curl_close($ch);
            }
        } else {
            $sys_lanai->getErrorBox("Turnstile is not fully configured. Please set both Site Key and Secret Key in Config.");
            return;
        }

        if (empty($userFname) || empty($userLname) || empty($userEmail) || empty($userLogin) || empty($userPassword1) || empty($userPassword2)) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
        } else {
            if ($userPassword1 == $userPassword2 && $captchaOk) {
                $rs = $mem_lanai->setUserRegister($userFname, $userLname, $userEmail, $userLogin, $userPassword1);
                if (empty($rs)) {
                    $sys_lanai->getErrorBox(_CANNOT_REGISTER . " <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
                } else {
                    // success message
                    ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" class="me-2" />
                        <div><?=_REG_COMPLETE;?></div>
                    </div>
                    <?php
                }
            } else {
                $sys_lanai->getErrorBox("Invalid captcha. Please try again.");
            }
        }
    } else {
        ?>
        <?php if ($turnstile_enabled) { ?>
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <?php } ?>
        <h5 class="mb-2 fw-bold text-primary"><?=_USER_SIGNUP;?></h5>

        <p class="mb-4 text-muted small"><?=_USER_SIGNUP_INSTRUCTION;?></p>

        <form method="post" action="<?=$_SERVER['PHP_SELF']?>" class="row g-3 mb-5">
            <input type="hidden" name="modname" value="member"/>
            <input type="hidden" name="mf" value="memsignup"/>
            <input type="hidden" name="ac" value="lostpass"/>

            <div class="col-md-6">
                <label for="userFname" class="form-label"><?=_USER_FNAME;?></label>
                <input type="text" class="form-control" id="userFname" name="userFname">
            </div>

            <div class="col-md-6">
                <label for="userLname" class="form-label"><?=_USER_LNAME;?></label>
                <input type="text" class="form-control" id="userLname" name="userLname">
            </div>

            <div class="col-12">
                <label for="userEmail" class="form-label"><?=_USER_EMAIL;?></label>
                <input type="email" class="form-control" id="userEmail" name="userEmail">
            </div>

            <div class="col-12">
                <label for="userLogin" class="form-label"><?=_USER_LOGIN;?></label>
                <input type="text" class="form-control" id="userLogin" name="userLogin">
            </div>

            <div class="col-md-6">
                <label for="userPassword1" class="form-label"><?=_USER_PASSWORD;?></label>
                <input type="password" class="form-control" id="userPassword1" name="userPassword1">
            </div>

            <div class="col-md-6">
                <label for="userPassword2" class="form-label"><?=_USER_RE_PASSWORD;?></label>
                <input type="password" class="form-control" id="userPassword2" name="userPassword2">
            </div>

            <?php if ($captcha_provider === 'default') { ?>
                <div class="col-md-6">
                    <label for="captext" class="form-label"><?=_MEMBER_CAPTEXT;?></label>
                    <input type="text" class="form-control" id="captext" name="captext" maxlength="5" placeholder="<?= defined('_ENTER_CAPTCHA') ? _ENTER_CAPTCHA : 'Enter captcha'; ?>">
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <img src="images/captcha.php?hash=<?=md5(time()); ?>" alt="captcha" class="img-fluid">
                </div>
            <?php } elseif ($turnstile_enabled) { ?>
                <div class="col-12">
                    <label class="form-label"><?=_MEMBER_CAPTEXT;?></label>
                    <div class="cf-turnstile" data-sitekey="<?= $turnstile_site_key; ?>" data-theme="light" data-language="en"></div>
                </div>
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        Turnstile is not fully configured. Please set both Site Key and Secret Key in Config.
                    </div>
                </div>
            <?php } ?>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <img src="theme/<?=$cfg['theme']; ?>/images/save.gif" class="me-1"/><?=_SAVE;?>
                </button>
                <button type="button" class="btn btn-secondary" onclick="history.back();">
                    <img src="theme/<?=$cfg['theme']; ?>/images/back.gif" class="me-1"/><?=_BACK;?>
                </button>
            </div>
        </form>
        <?php
    }
}
?>
