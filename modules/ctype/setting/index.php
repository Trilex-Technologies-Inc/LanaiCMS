<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();
?>
<span class="txtContentTitle"><?=_CTYPE_SETTING; ?></span><br/><br/>
<?=_CTYPE_SETTING_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=typenewform"><?=_NEW; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
<button type="button" onclick="chk_active();"><?=_ACTIVE; ?></button>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
<button type="button" onclick="chk_delete();"><?=_DELETE; ?></button>
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
$ctype->getTypeList();
?>
