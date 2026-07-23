<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
		die ("You can't access this file directly...");
}

/* load data to delete */
$objJpop=new Jpop();
$objJpop->_table=$cfg['tablepre']."jpop";
$rs=$objJpop->Load("popId=".$_REQUEST['i']);
if (!$rs) {
	/* no data to delete - show error message*/
	$sys_lanai->getErrorBox("Data not found!");
}  else {
	/* perform delete */
	$objJpop->delete();
	$sys_lanai->goBack(1);
}
?>