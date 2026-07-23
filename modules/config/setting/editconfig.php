<?php

if (!preg_match('/setting\.php/i', $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$objStatus = new SysConfig();

global $db;
$metaTable = $cfg['tablepre'] . "meta";
$objConfig = new Meta($metaTable);

$rowReady = $db->Execute(
    "INSERT INTO {$metaTable} (mtaId) VALUES (?) ON DUPLICATE KEY UPDATE mtaId = mtaId",
    array(1)
);

if (!$rowReady) {
    $sys_lanai->getErrorBox("Unable to initialize metadata: " . $db->ErrorMsg());
    return;
}

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
$captchaProvider = isset($_POST['cfg_captcha_provider']) ? $_POST['cfg_captcha_provider'] : 'default';
if ($captchaProvider !== 'cloudflare') {
    $captchaProvider = 'default';
}

$objStatus->setUpdateStatus($_REQUEST['cfgStatus']);
$objStatus->setSiteTitle($_REQUEST['cfg_title']);
$objStatus->setCaptchaProvider($captchaProvider);
$objStatus->setTurnstileSiteKey(trim(isset($_POST['cfg_turnstile_site_key']) ? $_POST['cfg_turnstile_site_key'] : ''));
$objStatus->setTurnstileSecretKey(trim(isset($_POST['cfg_turnstile_secret_key']) ? $_POST['cfg_turnstile_secret_key'] : ''));

if (!$result) {
    $sys_lanai->getErrorBox($db->ErrorMsg());
} else {
    $sys_lanai->go2Page("setting.php?modname=config&mf=confirm");
}
?>
