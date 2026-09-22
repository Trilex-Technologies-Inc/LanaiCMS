<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();

if (!$sys_lanai->validateCsrfToken('ctype', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
    $sys_lanai->getErrorBox("Invalid request, please try again.");
    return;
}

switch ($_REQUEST['ac']) {
    case "new":
        if (empty($_REQUEST['ctpTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $ctype->setNewType($_REQUEST['ctpTitle']);
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        }
        break;

    case "edit":
        if (empty($_REQUEST['ctpTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $ctype->setEditType($_REQUEST['ctpId'], $_REQUEST['ctpTitle'], $_REQUEST['ctpSlug']);
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        }
        break;

    case "active":
        $ctype->setTypeActive($_REQUEST['ctpId'], $_REQUEST['v']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;

    case "mactive":
        $idarr = (array) $_REQUEST['ctpId'];
        foreach ($idarr as $id) {
            $rs = $ctype->getTypeById($id);
            $value = ($rs->fields['ctpActive'] == 'y') ? 'n' : 'y';
            $ctype->setTypeActive($id, $value);
        }
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;

    case "mdelete":
        $idarr = (array) $_REQUEST['ctpId'];
        foreach ($idarr as $id) {
            $ctype->setDeleteType($id);
        }
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
        break;
}
?>
