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
<span class="txtContentTitle"><?=_CTYPE_MANAGE_ITEMS; ?> — <?=htmlspecialchars($type->fields['ctpTitle']);?></span><br/><br/>
<?=_CTYPE_ITEMS_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=itemnewform&ctpId=<?=$ctpId;?>"><?=_NEW; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
<button type="button" onclick="chk_active();"><?=_ACTIVE; ?></button>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
<button type="button" onclick="chk_delete();"><?=_DELETE; ?></button>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>"><?=_BACK; ?></a>
<br><br>
<script language="javascript">
    function chk_delete() {
        if (confirm("<?=_DELETE_QUESTION; ?>")) {
            document.getElementById("ctype-list-form").ac.value = "mdelete";
            document.getElementById("ctype-list-form").requestSubmit();
        }
    }
    function chk_active() {
        document.getElementById("ctype-list-form").ac.value = "mactive";
        document.getElementById("ctype-list-form").requestSubmit();
    }
</script>
<?php
$ctype->getItemList($ctpId);
?>
