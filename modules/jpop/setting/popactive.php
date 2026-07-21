<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
		die ("You can't access this file directly...");
}

/* load data to edit */
$objJpop=new Jpop();
$objJpop->_table=$cfg['tablepre']."jpop";
$rs=$objJpop->Load("popId=".$_REQUEST['i']);
if (!$rs) {
	/* no data to edit - show error message*/
	$sys_lanai->getErrorBox("Data not found!");
	
}  else {
	//if ($_REQUEST['ac']=="edit") {
		if ($_REQUEST['v']=="y") {
			$objJpop->popDisableAll();
			$objJpop->popActive($_REQUEST['i'],$_REQUEST['v']);
		} else {
			$objJpop->popActive($_REQUEST['i'],$_REQUEST['v']);
		}
		$sys_lanai->goBack(1);
	//} // edit action
} // data found
?>