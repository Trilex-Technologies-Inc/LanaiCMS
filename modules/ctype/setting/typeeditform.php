<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();
$rs = $ctype->getTypeById($_REQUEST['ctpId']);
?>
<span class="txtContentTitle"><?=_CTYPE_EDIT; ?></span><br/><br/>
<?=_CTYPE_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=fields&ctpId=<?=$rs->fields['ctpId'];?>"><?=_CTYPE_MANAGE_FIELDS; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
<br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="mf" value="typeedit">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="ctpId" value="<?=$rs->fields['ctpId'];?>">
    <input type="hidden" name="ac" value="edit">
    <?php $sys_lanai->renderCsrfField('ctype'); ?>
    <table cellpadding="3" cellspacing="1" border="0">
        <tr>
            <td><?=_CTYPE_TITLE; ?></td>
            <td><input type="text" name="ctpTitle" size="40" value="<?=htmlspecialchars($rs->fields['ctpTitle']);?>">*</td>
        </tr>
        <tr>
            <td><?=_CTYPE_SLUG; ?></td>
            <td><input type="text" name="ctpSlug" size="40" value="<?=htmlspecialchars($rs->fields['ctpSlug']);?>">*</td>
        </tr>
    </table>
</form>
