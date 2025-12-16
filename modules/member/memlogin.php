<?
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);
	if ($_REQUEST['captext']==$_SESSION['captcha']) {
		$xuid=$sys_lanai->getUserAuthentication($_REQUEST['username'],$_REQUEST['password']);
	}
	if (isset($xuid) && $xuid>0) {
	    $_SESSION['uid']=$xuid;
		$sys_lanai->go2Page("module.php?modname=member&mf=memloginform");
	} else {
		$sys_lanai->getErrorBox(_LOGIN_FAIL);
	}
?>
