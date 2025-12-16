<?php

if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
		die ("You can't access this file directly...");
}

/* load data to edit */
$objStatus=new SysConfig();

$objConfig=new Meta();
$objConfig->_table=$cfg['tablepre']."meta";
$rs=$objConfig->Load("mtaId=1");

if (!$rs) {
	/* no data to edit - show error message*/
	$sys_lanai->getErrorBox("Data not found!");
}  else {
    $result = $objConfig->updateSetting([
        'mtakeywords'     => $_POST['mtaKeywords'],
        'mtadescription'  => $_POST['mtaDescription'],
        'mtaabstract'     => $_POST['mtaAbstract'],
        'mtaauthor'       => $_POST['mtaAuthor'],
        'mtadistribution' => $_POST['mtaDistribution'],
        'mtacopyright'    => $_POST['mtaCopyright']
    ]);
    $objStatus->setUpdateStatus($_REQUEST['cfgStatus']);
    $objStatus->setSiteTitle($_REQUEST['cfg_title']);
    sleep(2);
    if (!$result) $sys_lanai->getErrorBox($objConfig->ErrorMsg()); else $sys_lanai->go2Page("setting.php?modname=config&mf=confirm");
}

?>