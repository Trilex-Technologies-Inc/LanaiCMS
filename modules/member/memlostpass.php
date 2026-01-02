<?
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

// get userinfo
$mem_lanai = new User();

if (isset($_REQUEST['ac']) && $_REQUEST['ac'] == "lostpass") {

    if (empty($_REQUEST['userLogin'])) {
        $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\">_BACK</a>");
    } else {

        $rs = $mem_lanai->getUserLogin($_REQUEST['userLogin']);

        if (($rs->recordcount() > 0) && ($_REQUEST['captext'] == $_SESSION['captcha'])) {

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

            <div class="mb-3">
                <label class="form-label"><?=_MEMBER_CAPTEXT;?></label>
                <input type="text" name="captext" class="form-control w-50" maxlength="5" required>
            </div>

            <div class="mb-3">
                <img src="images/captcha.php?hash=<?=md5(time());?>" alt="captcha">
            </div>

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
