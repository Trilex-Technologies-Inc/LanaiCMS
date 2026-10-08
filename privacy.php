<?php
ob_start();
$offpage='yes';
require_once __DIR__.'/setconfig.inc.php';
ob_end_clean();
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
$accept=$_SERVER['HTTP_ACCEPT']??'';
$json=strpos($accept,'application/json')!==false;
try {
    $selection=$lanaiPrivacy->validateRequest($_SERVER['REQUEST_METHOD']??'',$_POST,$sys_lanai);
    if (!$lanaiPrivacy->store($selection,(int)($_SESSION['uid']??0))) throw new RuntimeException('Preferences could not be saved.');
    if ($json) { header('Content-Type: application/json; charset=UTF-8'); echo json_encode(array('saved'=>true)); }
    else { header('Location: '.rtrim($cfg['url'],'/').'/module.php?modname=privacy&saved=1',true,303); }
} catch (Throwable $error) {
    http_response_code(in_array($error->getCode(),array(403,405,409),true)?$error->getCode():400);
    if (http_response_code()===405) header('Allow: POST');
    if ($json) { header('Content-Type: application/json; charset=UTF-8'); echo json_encode(array('saved'=>false)); }
    else { header('Content-Type: text/html; charset=UTF-8'); echo '<p>Preferences could not be saved. Please return to the site and reload the page.</p>'; }
}
