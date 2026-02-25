<?
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

// get userinfo
$mem_lanai = new User();
$captcha_provider = isset($cfg['captcha_provider']) ? $cfg['captcha_provider'] : 'default';
if ($captcha_provider !== 'cloudflare') {
    $captcha_provider = 'default';
}
$turnstile_site_key = isset($cfg['turnstile_site_key']) ? trim($cfg['turnstile_site_key']) : '';
$turnstile_secret_key = isset($cfg['turnstile_secret_key']) ? trim($cfg['turnstile_secret_key']) : '';
$turnstile_enabled = ($captcha_provider === 'cloudflare' && $turnstile_site_key !== '' && $turnstile_secret_key !== '');

if (isset($_REQUEST['ac']) && $_REQUEST['ac'] == "lostpass") {

    if (empty($_REQUEST['userLogin'])) {
        $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\">_BACK</a>");
    } else {

        $rs = $mem_lanai->getUserLogin($_REQUEST['userLogin']);
        $captchaOk = false;

        if ($captcha_provider === 'default') {
            $captext = isset($_REQUEST['captext']) ? trim($_REQUEST['captext']) : '';
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

        if (($rs->recordcount() > 0) && $captchaOk) {

            require_once("include/phpmailer/class.phpmailer.php");

            $mail = new phpmailer();
            $passwd = substr(md5(date("hms")), 0, 6);

            $mail->Host   = $cfg['smtp_host'];
            $mail->Port   = $cfg['smtp_port'];
            $mail->Mailer = "smtp";

            $mail->From     = $rs->fields['userEmail'];
            $mail->FromName = $rs->fields['userFname'] . " " . $rs->fields['userLname'];
            $mail->Subject  = _CHANGE_PASSWORD;
            $mail->Body     = _CHANGE_PASSWORD_MESSAGE . " " . $passwd;

            $mail->AddAddress(
                $rs->fields['userEmail'],
                $rs->fields['userFname'] . " " . $rs->fields['userLname']
            );

            if (!$mail->Send()) {
                ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <img src="theme/<?=$cfg['theme'];?>/images/worning.gif" alt="">
                    <?=_LOSTPASS_CANNOT_SEND;?>
                </div>
                <?
            } else {
                $mem_lanai->setUpdateUserPassword(
                    $rs->fields['userId'],
                    md5($passwd)
                );
                ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <img src="theme/<?=$cfg['theme'];?>/images/ok.gif" alt="">
                    <?=_LOSTPASS_SEND_COMPLETE;?>
                </div>
                <?
            }

            $mail->ClearAddresses();

        } else {
            $sys_lanai->getErrorBox(
                _LOGIN_NOTEXIST .
                " <a href=\"#\" onClick=\"history.back();\">_BACK</a>"
            );
        }
    }

} else {
    ?>
    <? if ($turnstile_enabled) { ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <? } ?>
    <div class="container mt-4">
        <h4 class="mb-3"><?=_USER_LOSTPASS;?></h4>
        <p class="text-muted"><?=_USER_LOSTPASS_INSTRUCTION;?></p>

        <form name="form" method="post" action="<?=$_SERVER['PHP_SELF']?>" class="card p-4 shadow-sm">
            <input type="hidden" name="modname" value="member">
            <input type="hidden" name="mf" value="memlostpass">
            <input type="hidden" name="ac" value="lostpass">

            <div class="mb-3">
                <label class="form-label"><?=_USER_LOGIN;?></label>
                <input type="text" name="userLogin" class="form-control" required>
            </div>

            <? if ($captcha_provider === 'default') { ?>
                <div class="mb-3">
                    <label class="form-label"><?=_MEMBER_CAPTEXT;?></label>
                    <input type="text" name="captext" class="form-control w-50" maxlength="5" required placeholder="<?= defined('_ENTER_CAPTCHA') ? _ENTER_CAPTCHA : 'Enter captcha'; ?>">
                </div>

                <div class="mb-3">
                    <img src="images/captcha.php?hash=<?=md5(time());?>" alt="captcha">
                </div>
            <? } elseif ($turnstile_enabled) { ?>
                <div class="mb-3">
                    <label class="form-label"><?=_MEMBER_CAPTEXT;?></label>
                    <div class="cf-turnstile" data-sitekey="<?= $turnstile_site_key; ?>" data-theme="light" data-language="en"></div>
                </div>
            <? } else { ?>
                <div class="alert alert-warning">
                    Turnstile is not fully configured. Please set both Site Key and Secret Key in Config.
                </div>
            <? } ?>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                    <?=_SAVE;?>
                </button>

                <a href="module.php?modname=member&mf=memloginform" class="btn btn-secondary">
                    <?=_BACK;?>
                </a>
            </div>
        </form>
    </div>
    <?
}
?>
