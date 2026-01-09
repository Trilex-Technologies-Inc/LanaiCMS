<?php

if (!preg_match('/setting\.php/i', $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$objStatus = new SysConfig();

$objConfig = new Meta();
$objConfig->_table = $cfg['tablepre'] . "meta";
$rs = $objConfig->Load("mtaId=1");

if (!$rs) {

    $sys_lanai->getErrorBox("Data not found!");

} else {


    $mtaLogo         = !empty($_POST['mtaLogo']) ? $_POST['mtaLogo'] : null;
    $mtaFavicon      = !empty($_POST['mtaFavicon']) ? $_POST['mtaFavicon'] : null;
    $mtaShowSiteName = isset($_POST['mtaShowSiteName']) ? 1 : 0;

    $result = $objConfig->updateSetting(array(
        'mtakeywords'      => $_POST['mtaKeywords'],
        'mtadescription'   => $_POST['mtaDescription'],
        'mtaabstract'      => $_POST['mtaAbstract'],
        'mtaauthor'        => $_POST['mtaAuthor'],
        'mtadistribution'  => $_POST['mtaDistribution'],
        'mtacopyright'     => $_POST['mtaCopyright'],
        'mtalogo'          => $mtaLogo,
        'mtafavicon'       => $mtaFavicon,
        'mtashowsitename'  => $mtaShowSiteName
    ));

    /* System config */
    $objStatus->setUpdateStatus($_REQUEST['cfgStatus']);
    $objStatus->setSiteTitle($_REQUEST['cfg_title']);

    sleep(2);

    if (!$result) {
        $sys_lanai->getErrorBox($objConfig->ErrorMsg());
    } else {
        $sys_lanai->go2Page("setting.php?modname=config&mf=confirm");
    }
}
?>
