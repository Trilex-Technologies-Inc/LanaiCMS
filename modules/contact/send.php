<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}


$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

require_once("include/phpmailer/class.phpmailer.php");

$name = strip_tags($_REQUEST['name']);
$email = filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL);
$title = strip_tags($_REQUEST['title']);
$message = strip_tags($_REQUEST['message']);
$cid = intval($_REQUEST['cid']);
?>
<div class="col-md-8">
    <div class="article-content bg-white p-4 rounded shadow-sm">
        <div class="container my-4">
            <?php
            // ---- CAPTCHA CHECK ----
            if (
                empty($_REQUEST['captcha']) ||
                !isset($_SESSION['captcha']) ||
                strtoupper(trim($_REQUEST['captcha'])) !== strtoupper($_SESSION['captcha'])
            ) {
                ?>
                <img src="theme/<?= $cfg['theme']; ?>/images/worning.gif" align="absmiddle"/>
                <?= _VERIFY; ?> is incorrect
                <?php
                exit;
            }

            // remove captcha from session to prevent reuse
            unset($_SESSION['captcha']);

            // ---- SEND EMAIL ----
            $cnt = new Contact();
            $rs = $cnt->getContactById($cid);

            $mail = new phpmailer();
            $mail->Mailer = "smtp";
            $mail->Host = $cfg['smtp_host'];
            $mail->Port = $cfg['smtp_port'];

            $mail->From = $email;
            $mail->FromName = $name;
            $mail->Subject = $title;
            $mail->Body = $message;

            if ($cid > 0 && $rs && $rs->recordcount() > 0) {
                $mail->AddAddress(
                    $rs->fields['conEmail'],
                    $rs->fields['conFname'] . " " . $rs->fields['conLname']
                );
            } else {
                $mail->AddAddress($cfg['email'], "Administrator");
            }

            if (!$mail->Send()) {
                ?>
                <img src="theme/<?= $cfg['theme']; ?>/images/worning.gif" border="0" align="absmiddle"/>
                <?= _CANNOT_SEND; ?>
                <?php
            } else {
                ?>
                <img src="theme/<?= $cfg['theme']; ?>/images/ok.gif"
                     style="width:40px; height:auto; vertical-align:middle;" border="0">

                <?= _SEND_COMPLETE; ?>
                <?php
            }

            $mail->ClearAddresses();
            ?>
        </div>
    </div>
</div>
