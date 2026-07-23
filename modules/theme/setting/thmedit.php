<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$thm_lanai=new Themes();
	
	$thm_lanai->setUpdateTheme($_REQUEST['thmname']);	
	$thm_lanai->deleteCache();
	$sys_lanai->goBack();
	
?>