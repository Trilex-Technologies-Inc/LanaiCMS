<?php

include_once('setconfig.inc.php');
include_once('include/header.inc.php');

$theme = new Theme();
$smarty->assign("getLogoHeader", $theme->getLogoHeader());
$smarty->assign ("siteName", $cfg['title']);
$smarty->assign("logo", $obMeta->MTALOGO);
$smarty->assign("getFooter", $theme->getFooter());
$smarty->assign("setBlockLeft", $theme->setBlock("l"));
$smarty->assign("setBlockRight", $theme->setBlock("r"));
$modname = isset($_REQUEST['modname']) ? $_REQUEST['modname'] : '';
$mf      = isset($_REQUEST['mf']) ? $_REQUEST['mf'] : '';

$smarty->assign("setModule", $theme->getModule($modname, $mf));

$smarty->assign("nameModule", $_REQUEST['modname']);
$smarty->assign("mf", isset($_REQUEST['mf']) ? $_REQUEST['mf'] : null);

//$smarty->assign ("setBlockCenter", $theme->setBlock("c"));
$smarty->assign("setBlockTop", $theme->setBlock("t"));
$smarty->assign("setBlockBottom", $theme->setBlock("b"));
$smarty->display('module.tpl');

include_once('include/footer.inc.php');
?>
