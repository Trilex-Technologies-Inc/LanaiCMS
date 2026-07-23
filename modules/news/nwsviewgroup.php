<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);
?><span class="txtContentTitle"><?=_NWS_LIST; ?></span><br/><br/><?php
	$news = new News();
	
	$news->getShowNewsByGroup($rows=20,$_REQUEST['mid']);

?>
