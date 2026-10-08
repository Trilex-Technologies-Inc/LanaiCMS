<?php
ob_start();
$offpage='yes';
require_once __DIR__.'/setconfig.inc.php';
ob_end_clean();
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex, nofollow');
try {
    $service=new LanaiPrivacyData($db,$tablepre,$cfg['datadir']);
    $user=$service->authorize($_SERVER['REQUEST_METHOD']??'',$_POST,$_SESSION,$sys_lanai,lanai_mfa_service());
    $action=$_POST['action']??'';
    if ($action==='export') {
        $export=$service->export((int)$user['userId']);
        $json=json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE|JSON_THROW_ON_ERROR);
        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Disposition: attachment; filename="personal-data.json"');
        echo $json;
        exit;
    }
    if ($action!=='request') throw new InvalidArgumentException('Invalid action.');
    $service->createRequest((int)$user['userId'],$_POST['requestType']??'',$_POST['details']??'');
    $_SESSION['privacy_data_message']='Your request has been recorded. You can follow its progress here.';
} catch (Throwable $error) {
    // Do not expose database errors, credentials, or stack traces to a requester.
    $_SESSION['privacy_data_message']='The action could not be completed. Check your password and authenticator code, or wait five minutes after repeated attempts. Contact the site operator if the problem continues.';
}
header('Location: '.rtrim($cfg['url'],'/').'/module.php?modname=privacy&view=data',true,303);
