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

function ctype_collect_values($ctype, $ctpId)
{
    $values = array();
    $rsf = $ctype->getFields($ctpId);
    while (!$rsf->EOF) {
        $cfdId = $rsf->fields['cfdId'];
        $fieldName = 'cfd_' . $cfdId;
        $type = $rsf->fields['cfdType'];
        if ($type === 'checkbox') {
            $values[$cfdId] = isset($_REQUEST[$fieldName]) ? '1' : '0';
        } elseif (in_array($type, ContentType::$uploadFieldTypes, true)) {
            // hidden field carries the existing path; an uploaded file replaces it
            $uploadName = 'cfd_upload_' . $cfdId;
            $newPath = false;
            if (!empty($_FILES[$uploadName]['tmp_name'])) {
                $newPath = $ctype->saveUploadedFile($type, $_FILES[$uploadName]);
            }
            $values[$cfdId] = $newPath !== false ? $newPath : (isset($_REQUEST[$fieldName]) ? $_REQUEST[$fieldName] : '');
        } else {
            $values[$cfdId] = isset($_REQUEST[$fieldName]) ? $_REQUEST[$fieldName] : '';
        }
        $rsf->movenext();
    }
    return $values;
}

/**
 * A user may act on an item if they have the blanket capability, or if
 * they own the item and hold the "own content only" capability.
 */
function ctype_can_act($item, $blanketCap, $ownCap)
{
    global $sys_lanai;
    $ownerId = isset($item['userId']) ? $item['userId'] : 0;
    return $sys_lanai->userCanActOnContent($ownerId, $blanketCap, $ownCap);
}

switch ($_REQUEST['ac']) {
    case "new":
        if (empty($_REQUEST['citTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $values = ctype_collect_values($ctype, $ctpId);
            $ctype->setSaveItem(null, $ctpId, $_REQUEST['citTitle'], $values);
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=items&ctpId=" . $ctpId);
        }
        break;

    case "edit":
        $existing = $ctype->getItemById($_REQUEST['citId']);
        if ($existing->recordcount() < 1 || !ctype_can_act($existing->fields, 'edit_content', 'edit_own_content')) {
            $sys_lanai->getErrorBox(_CTYPE_NO_PERMISSION);
            break;
        }
        if (empty($_REQUEST['citTitle'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS . " <a href=\"#\" onClick=\"javascript:history.back();\">" . _BACK . "</a>");
        } else {
            $values = ctype_collect_values($ctype, $ctpId);
            $ctype->setSaveItem($_REQUEST['citId'], $ctpId, $_REQUEST['citTitle'], $values);
            $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=items&ctpId=" . $ctpId);
        }
        break;

    case "active":
        $existing = $ctype->getItemById($_REQUEST['citId']);
        if ($existing->recordcount() < 1 || !ctype_can_act($existing->fields, 'publish_content', 'edit_own_content')) {
            $sys_lanai->getErrorBox(_CTYPE_NO_PERMISSION);
            break;
        }
        $ctype->setItemActive($_REQUEST['citId'], $_REQUEST['v']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=items&ctpId=" . $ctpId);
        break;

    case "mactive":
        $idarr = (array) $_REQUEST['citId'];
        foreach ($idarr as $id) {
            $rs = $ctype->getItemById($id);
            if ($rs->recordcount() < 1 || !ctype_can_act($rs->fields, 'publish_content', 'edit_own_content')) {
                continue;
            }
            $value = ($rs->fields['citActive'] == 'y') ? 'n' : 'y';
            $ctype->setItemActive($id, $value);
        }
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=items&ctpId=" . $ctpId);
        break;

    case "mdelete":
        $idarr = (array) $_REQUEST['citId'];
        foreach ($idarr as $id) {
            $rs = $ctype->getItemById($id);
            if ($rs->recordcount() < 1 || !ctype_can_act($rs->fields, 'delete_content', 'edit_own_content')) {
                continue;
            }
            $ctype->setDeleteItem($id);
        }
        $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name . "&mf=items&ctpId=" . $ctpId);
        break;
}
?>
