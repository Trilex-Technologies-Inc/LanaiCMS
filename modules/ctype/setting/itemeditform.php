<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);
include_once(__DIR__ . "/_fieldinput.inc.php");

$ctype = new ContentType();
$ctpId = intval($_REQUEST['ctpId']);
$citId = intval($_REQUEST['citId']);
$item = $ctype->getItemById($citId);

if ($item->recordcount() < 1) {
    $sys_lanai->getErrorBox(_CTYPE_NOT_FOUND);
    return;
}
?>
<script src="include/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({ selector: 'textarea.tinymce', license_key: 'gpl', height: 300 });
</script>

<span class="txtContentTitle"><?=_CTYPE_ITEM_EDIT; ?></span><br/><br/>
<?=_CTYPE_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
<br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    <input type="hidden" name="mf" value="itemedit">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="ctpId" value="<?=$ctpId;?>">
    <input type="hidden" name="citId" value="<?=$citId;?>">
    <input type="hidden" name="ac" value="edit">
    <?php $sys_lanai->renderCsrfField('ctype'); ?>
    <table cellpadding="3" cellspacing="1" border="0">
        <tr>
            <td><?=_CTYPE_ITEM_TITLE; ?></td>
            <td><input type="text" name="citTitle" size="40" value="<?=htmlspecialchars($item->fields['citTitle']);?>">*</td>
        </tr>
        <?php
        $rsv = $ctype->getItemValues($citId);
        while (!$rsv->EOF) {
            $value = ($rsv->fields['cfdType'] === 'number') ? $rsv->fields['cvalNumber'] : $rsv->fields['cvalText'];
            ctype_render_field_input($rsv->fields, $value);
            $rsv->movenext();
        }
        ?>
    </table>
</form>
