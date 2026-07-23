<?php
ob_start();
include_once('setconfig.inc.php');
$bootstrapOutput = ob_get_clean();
if (trim($bootstrapOutput) !== '') {
    error_log('Unexpected output during setting bootstrap: ' . strip_tags($bootstrapOutput));
}
include_once('include/header.inc.php');
include_once("modules/member/module.php");
$modname = isset($_REQUEST['modname']) && !is_array($_REQUEST['modname']) ? trim((string)$_REQUEST['modname']) : '';
$mf = isset($_REQUEST['mf']) && !is_array($_REQUEST['mf']) ? trim((string)$_REQUEST['mf']) : '';
$mem_lanai = new User();
if (empty($_SESSION['uid']) || $_SESSION['uid'] <= 0) {
    $sys_lanai->go2Page("index.php");
}else {
    $mem = $mem_lanai->getUser($_SESSION['uid']);
    if ($mem_lanai->getUserPrivilege($_SESSION['uid']) != "a")
        $sys_lanai->go2Page("index.php");

    $mem = $mem_lanai->getUser($_SESSION['uid']);
    $theme = new Theme();
    $smarty->assign("getLogoHeader", $theme->getLogoHeader());
    $smarty->assign("getFooter", $theme->getFooter());
    $smarty->assign("setBlockLeft", $theme->setBlock("l"));
    $smarty->assign("setBlockRight", $theme->setBlock("r"));
    $smarty->assign("setModule", $theme->getSettingModule(
        $modname,
        $mf
    ));

//$smarty->assign ("setBlockCenter", $theme->setBlock("c"));
    $smarty->assign("setBlockTop", $theme->setBlock("t"));
    $smarty->assign("setBlockBottom", $theme->setBlock("b"));
    $smarty->display('setting.tpl');

    include_once('include/footer.inc.php');
}
?>
