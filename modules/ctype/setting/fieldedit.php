<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();
$ctpId = intval($_REQUEST['ctpId']);

if (!$sys_lanai->validateCsrfToken('ctype', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox("Invalid request, please try again.");
    return;
}

switch ($_REQUEST['ac']) {
    case "new":
        if (empty($_REQUEST['cfdLabel'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $ctype->setNewField(
                $ctpId,
                $_REQUEST['cfdLabel'],
                $_REQUEST['cfdType'],
                isset($_REQUEST['cfdRequired']) ? 'y' : 'n',
                isset($_REQUEST['cfdOptions']) ? $_REQUEST['cfdOptions'] : ''
            );
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=fields&ctpId=" . $ctpId);
        }
        break;

    case "delete":
        $ctype->setDeleteField($_REQUEST['cfdId']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=fields&ctpId=" . $ctpId);
        break;
}
?>
