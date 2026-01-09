<?
include_once('setconfig.inc.php');
include_once('include/header.inc.php');
include_once("modules/member/module.php");
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
        isset($_REQUEST['modname']) ? $_REQUEST['modname'] : '',
        isset($_REQUEST['mf']) ? $_REQUEST['mf'] : ''
    ));

//$smarty->assign ("setBlockCenter", $theme->setBlock("c"));
    $smarty->assign("setBlockTop", $theme->setBlock("t"));
    $smarty->assign("setBlockBottom", $theme->setBlock("b"));
    $smarty->display('setting.tpl');

    include_once('include/footer.inc.php');
}
?>