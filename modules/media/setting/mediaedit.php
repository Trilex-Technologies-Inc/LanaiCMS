<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$media = new Media();

if (!$sys_lanai->validateCsrfToken('media', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox("Invalid request, please try again.");
    return;
}

switch ($_REQUEST['ac']) {
    case "upload":
        $altText = isset($_REQUEST['altText']) ? $_REQUEST['altText'] : '';
        if (empty($_FILES['mediaFile']['name']) || $media->saveUpload($_FILES['mediaFile'], $altText) === false) {
            $sys_lanai->getErrorBox(_MEDIA_UPLOAD_FAILED);
        } else {
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        }
        break;

    case "delete":
        $media->setDeleteMedia($_REQUEST['mediaId']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;
}
?>
