<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();
?>
<span class="txtContentTitle"><?=_CTYPE_NEW; ?></span><br/><br/>
<?=_CTYPE_NEW_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
<br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="mf" value="typeedit">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="ac" value="new">
    <?php $sys_lanai->renderCsrfField('ctype'); ?>
    <table cellpadding="3" cellspacing="1" border="0">
        <tr>
            <td><?=_CTYPE_TITLE; ?></td>
            <td><input type="text" name="ctpTitle" size="40" placeholder="e.g. Product">*</td>
        </tr>
    </table>
</form>
