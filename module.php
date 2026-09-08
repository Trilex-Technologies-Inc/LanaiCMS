<?php

// Some legacy includes close their PHP tags and emit whitespace. Buffer that
// output so the HTML5 doctype in header.inc.php remains the first response data.
ob_start();
include_once('setconfig.inc.php');
$bootstrapOutput = ob_get_clean();
// setconfig.inc.php may emit the permission-warning script. Suppress it here so
// bootstrap output cannot precede the document doctype or pollute the error log.
include_once('include/header.inc.php');

$theme = new Theme();
$smarty->assign("getLogoHeader", $theme->getLogoHeader());
$smarty->assign ("siteName", $cfg['title']);
$smarty->assign("logo", $obMeta->mtaLogo);
$smarty->assign("showSiteName", $obMeta->mtaShowSiteName);
$smarty->assign("getFooter", $theme->getFooter());
$smarty->assign("setBlockLeft", $theme->setBlock("l"));
$smarty->assign("setBlockRight", $theme->setBlock("r"));
$modname = isset($_REQUEST['modname']) && !is_array($_REQUEST['modname']) ? trim((string)$_REQUEST['modname']) : '';
$mf      = isset($_REQUEST['mf']) && !is_array($_REQUEST['mf']) ? trim((string)$_REQUEST['mf']) : '';

$smarty->assign("setModule", $theme->getModule($modname, $mf));

$smarty->assign("nameModule", $modname);
$smarty->assign("mf", $mf);

//$smarty->assign ("setBlockCenter", $theme->setBlock("c"));
$smarty->assign("setBlockTop", $theme->setBlock("t"));
$smarty->assign("setBlockBottom", $theme->setBlock("b"));
$smarty->display('module.tpl');

include_once('include/footer.inc.php');
?>
