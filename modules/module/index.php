<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);

?>
