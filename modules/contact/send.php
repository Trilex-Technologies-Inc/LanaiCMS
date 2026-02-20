<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

require_once("include/phpmailer/class.phpmailer.php");


$turnstile_secret_key = 'your secret key';


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
            // ---- CLOUDFLARE TURNSTILE CHECK ----
            $turnstile_response = '';
            if (isset($_POST['cf-turnstile-response'])) {
                $turnstile_response = $_POST['cf-turnstile-response'];
            }
            
            if (empty($turnstile_response)) {
                ?>
                <div class="alert alert-danger">
                    <img src="theme/<?php echo $cfg['theme']; ?>/images/worning.gif" align="absmiddle" />
                    Security verification is required. Please complete the Turnstile challenge.
                </div>
                <?php
                exit;
            }
            
            // Verify with Cloudflare
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For PHP 5 compatibility
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                ?>
                <div class="alert alert-danger">
                    <img src="theme/<?php echo $cfg['theme']; ?>/images/worning.gif" align="absmiddle" />
                    Verification service error. Please try again later.
                </div>
                <?php
                exit;
            }
            
            curl_close($ch);
            
            $result = json_decode($response, true);
            
            if (!$result['success']) {
                ?>
                <div class="alert alert-danger">
                    <img src="theme/<?php echo $cfg['theme']; ?>/images/worning.gif" align="absmiddle" />
                    Security verification failed. Please try again.
                </div>
                <?php
                exit;
            }

            // ---- SEND EMAIL ----
            $cnt = new Contact();
            $rs = $cnt->getContactById($cid);

            $mail = new phpmailer();
            $mail->Mailer = "smtp";
            $mail->Host = $cfg['smtp_host'];
            $mail->Port = $cfg['smtp_port'];
            
            if (!empty($cfg['smtp_user'])) {
                $mail->SMTPAuth = true;
                $mail->Username = $cfg['smtp_user'];
                $mail->Password = $cfg['smtp_pass'];
            }

            $mail->From = $email;
            $mail->FromName = $name;
            $mail->Subject = $title;
            $mail->Body = $message;
            $mail->IsHTML(false);

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
                <div class="alert alert-danger">
                    <img src="theme/<?php echo $cfg['theme']; ?>/images/worning.gif" border="0" align="absmiddle" />
                    <?php echo _CANNOT_SEND; ?>
                </div>
                <?php
            } else {
                ?>
                <div class="alert alert-success">
                    <img src="theme/<?php echo $cfg['theme']; ?>/images/ok.gif"
                         style="width:40px; height:auto; vertical-align:middle;" border="0" />
                    <?php echo _SEND_COMPLETE; ?>
                </div>
                <?php
            }

            $mail->ClearAddresses();
            ?>
        </div>
    </div>
</div>