<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();
$ctpId = intval($_REQUEST['ctpId']);
$type = $ctype->getTypeById($ctpId);

if ($type->recordcount() < 1) {
    $sys_lanai->getErrorBox(_CTYPE_NOT_FOUND);
    return;
}
?>
<span class="txtContentTitle"><?=_CTYPE_MANAGE_FIELDS; ?> — <?=htmlspecialchars($type->fields['ctpTitle']);?></span><br/><br/>
<?=_CTYPE_FIELDS_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>"><?=_BACK; ?></a>&nbsp;&nbsp;

<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=items&ctpId=<?=$ctpId;?>"><?=_CTYPE_MANAGE_ITEMS; ?></a>
<br><br>

<table cellpadding="3" cellspacing="1" width="100%">
    <tr>
        <th class="tblRowSolidTopDown"><?=_CTYPE_FIELD_LABEL; ?></th>
        <th class="tblRowSolidTopDown"><?=_CTYPE_FIELD_TYPE; ?></th>
        <th class="tblRowSolidTopDown"><?=_CTYPE_FIELD_REQUIRED; ?></th>
        <th class="tblRowSolidTopDown"><?=_DELETE; ?></th>
    </tr>
    <?php
    $rsf = $ctype->getFields($ctpId);
    while (!$rsf->EOF) {
        ?>
        <tr>
            <td class="tblRowDash"><?=htmlspecialchars($rsf->fields['cfdLabel']);?></td>
            <td class="tblRowDash"><?=htmlspecialchars($rsf->fields['cfdType']);?></td>
            <td class="tblRowDash" align="center"><?=$rsf->fields['cfdRequired'] == 'y' ? _YES : _NO;?></td>
            <td class="tblRowDash" align="center">
                <form method="post" action="<?=$_SERVER['PHP_SELF'];?>" style="margin:0;" onsubmit="return confirm('<?=_DELETE_QUESTION;?>');">
                    <input type="hidden" name="modname" value="<?=$module_name;?>">
                    <input type="hidden" name="mf" value="fieldedit">
                    <input type="hidden" name="ac" value="delete">
                    <input type="hidden" name="ctpId" value="<?=$ctpId;?>">
                    <input type="hidden" name="cfdId" value="<?=$rsf->fields['cfdId'];?>">
                    <?php $sys_lanai->renderCsrfField('ctype'); ?>
                    <button type="submit" style="border:0;background:none;padding:0;cursor:pointer;">
                        <img src="theme/<?=$cfg['theme'];?>/images/delete.gif" border="0" align="absmiddle">
                    </button>
                </form>
            </td>
        </tr>
        <?php
        $rsf->movenext();
    }
    ?>
</table>
<br>

<b><?=_CTYPE_FIELD_NEW; ?></b><br/><br/>
<form name="fieldform" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="mf" value="fieldedit">
    <input type="hidden" name="ac" value="new">
    <input type="hidden" name="ctpId" value="<?=$ctpId;?>">
    <?php $sys_lanai->renderCsrfField('ctype'); ?>
    <table cellpadding="3" cellspacing="1" border="0">
        <tr>
            <td><?=_CTYPE_FIELD_LABEL; ?></td>
            <td><input type="text" name="cfdLabel" size="30" placeholder="e.g. Price">*</td>
        </tr>
        <tr>
            <td><?=_CTYPE_FIELD_TYPE; ?></td>
            <td>
                <select name="cfdType">
                    <?php foreach (ContentType::$fieldTypes as $t) { ?>
                        <option value="<?=$t;?>"><?=$t;?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?=_CTYPE_FIELD_OPTIONS; ?></td>
            <td><input type="text" name="cfdOptions" size="40" placeholder="comma,separated,for,select"></td>
        </tr>
        <tr>
            <td><?=_CTYPE_FIELD_REQUIRED; ?></td>
            <td><input type="checkbox" name="cfdRequired" value="y"></td>
        </tr>
        <tr>
            <td></td>
            <td><button type="submit"><?=_SAVE;?></button></td>
        </tr>
    </table>
</form>
