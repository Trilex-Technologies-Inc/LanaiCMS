<?php
	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}

	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction );

    $objExpl=new Explorer();
    unlink($objExpl->_dir($_REQUEST['dir']).$sys_lanai->getPath().$_REQUEST['f']);
    $sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&dir=".$_REQUEST['dir']);

?>
