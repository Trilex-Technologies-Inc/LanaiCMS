<?php

if (!preg_match('/setting\.php/i', $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$objStatus = new SysConfig();

$objConfig = new Meta();
$objConfig->_table = $cfg['tablepre'] . "meta";
$rs = $objConfig->Load("mtaId=1");

if (!$rs) {
    global $db;
    $metaTable = $cfg['tablepre'] . "meta";
    $siteTitle = isset($cfg['title']) ? $cfg['title'] : '';
    $columns = array();
    $values = array();
    $rsColumns = $db->Execute("SHOW COLUMNS FROM {$metaTable}");

    if ($rsColumns) {
        while (!$rsColumns->EOF) {
            $field = $rsColumns->fields['Field'];
            $columns[] = $field;
            switch (strtolower($field)) {
                case 'mtaid':
                    $values[] = 1;
                    break;
                case 'mtasitename':
                    $values[] = $siteTitle;
                    break;
                case 'mtashowsitename':
                    $values[] = 1;
                    break;
                case 'mtadistribution':
                    $values[] = 'Global';
                    break;
                default:
                    $values[] = null;
                    break;
            }
            $rsColumns->MoveNext();
        }
    }

    if (!empty($columns)) {
        $placeholders = implode(", ", array_fill(0, count($columns), '?'));
        $db->Execute(
            "INSERT IGNORE INTO {$metaTable} (" . implode(", ", $columns) . ") VALUES ({$placeholders})",
            $values
        );
    }
    $rs = $objConfig->Load("mtaId=1");
}

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
    $captchaProvider = isset($_POST['cfg_captcha_provider']) ? $_POST['cfg_captcha_provider'] : 'default';
    if ($captchaProvider !== 'cloudflare') {
        $captchaProvider = 'default';
    }

    $objStatus->setUpdateStatus($_REQUEST['cfgStatus']);
    $objStatus->setSiteTitle($_REQUEST['cfg_title']);
    $objStatus->setCaptchaProvider($captchaProvider);
    $objStatus->setTurnstileSiteKey(trim(isset($_POST['cfg_turnstile_site_key']) ? $_POST['cfg_turnstile_site_key'] : ''));
    $objStatus->setTurnstileSecretKey(trim(isset($_POST['cfg_turnstile_secret_key']) ? $_POST['cfg_turnstile_secret_key'] : ''));

    sleep(2);

    if (!$result) {
        $sys_lanai->getErrorBox($objConfig->ErrorMsg());
    } else {
        $sys_lanai->go2Page("setting.php?modname=config&mf=confirm");
    }
}
?>
