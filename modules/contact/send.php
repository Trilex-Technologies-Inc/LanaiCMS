<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

require_once("include/phpmailer/class.phpmailer.php");



$turnstileSecret = '0x4AAAAAACICBNSl86XDmJJ2g8NcLZ18ft0';

$turnstileResponse = '';
if (isset($_POST['cf-turnstile-response'])) {
    $turnstileResponse = $_POST['cf-turnstile-response'];
}

if ($turnstileResponse == '') {
    die('Bot verification failed.');
}

$verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

$data = array(
    'secret'   => $turnstileSecret,
    'response' => $turnstileResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR']
);

$options = array(
    'http' => array(
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'timeout' => 10
    )
);

$context = stream_context_create($options);
$result  = file_get_contents($verifyUrl, false, $context);

$response = json_decode($result, true);

if (!isset($response['success']) || $response['success'] !== true) {
    die('Cloudflare verification failed.');
}


$name    = strip_tags($_REQUEST['name']);
$email   = filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL);
$title   = strip_tags($_REQUEST['title']);
$message = strip_tags($_REQUEST['message']);
$cid     = intval($_REQUEST['cid']);



$cnt = new Contact();
$rs  = $cnt->getContactById($cid);



$mail = new phpmailer();
$mail->Mailer = "smtp";
$mail->Host   = $cfg['smtp_host'];
$mail->Port   = $cfg['smtp_port'];

$mail->From     = $email;
$mail->FromName = $name;
$mail->Subject  = $title;
$mail->Body     = $message;

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
    <img src="theme/<?= $cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
    <?= _SEND_COMPLETE; ?>
    <?php
}

$mail->ClearAddresses();
?>
