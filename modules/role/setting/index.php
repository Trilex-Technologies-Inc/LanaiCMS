<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$role = new Role();
?>
<span class="txtContentTitle"><?=_ROLE_SETTING; ?></span><br/><br/>
<?=_ROLE_SETTING_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=rolenewform"><?=_NEW; ?></a>
<br><br>
<?php
$role->getRoleList();
?>
