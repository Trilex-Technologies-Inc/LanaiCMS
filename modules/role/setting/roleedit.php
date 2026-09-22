<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$role = new Role();

if (!$sys_lanai->validateCsrfToken('role', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox("Invalid request, please try again.");
    return;
}

switch ($_REQUEST['ac']) {
    case "new":
        if (empty($_REQUEST['roleTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $role->setNewRole($_REQUEST['roleTitle'], isset($_REQUEST['capId']) ? $_REQUEST['capId'] : array());
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        }
        break;

    case "edit":
        if (empty($_REQUEST['roleTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $role->setEditRole($_REQUEST['roleId'], $_REQUEST['roleTitle'], isset($_REQUEST['capId']) ? $_REQUEST['capId'] : array());
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        }
        break;

    case "delete":
        $role->setDeleteRole($_REQUEST['roleId']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;
}
?>
