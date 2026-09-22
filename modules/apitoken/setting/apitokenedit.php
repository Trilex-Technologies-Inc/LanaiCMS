<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$apiToken = new ApiToken();

if (!$sys_lanai->validateCsrfToken('apitoken', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox("Invalid request, please try again.");
    return;
}

switch ($_REQUEST['ac']) {
    case "generate":
        $plainToken = $apiToken->generateToken($_REQUEST['userId'], $_REQUEST['label']);
        if ($plainToken !== false) {
            $_SESSION['apitoken_new_plain'] = $plainToken;
        }
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;

    case "revoke":
        $apiToken->revokeToken($_REQUEST['tokenId']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;
}
?>
