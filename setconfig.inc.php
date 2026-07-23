<?php

session_start();

function lanai_bootstrap_error($message)
{
    echo '<!doctype html><html><head><meta charset="utf-8"><title>LanaiCMS Configuration Error</title></head><body>';
    echo '<h3>LanaiCMS configuration error</h3>';
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</body></html>';
    exit;
}

/* sql injection ad-hoc blocking */
if (preg_match('/tbl.*/i', $_SERVER['QUERY_STRING'] ?? '')) {
    exit;
}

$configFile = __DIR__ . '/config.inc.php';
if (!file_exists($configFile)) {
    lanai_bootstrap_error('Missing config.inc.php. Run install/index.php to generate it.');
}

include_once($configFile);
include_once('include/lanai/php_compat.php');
include_once('include/adodb/adodb.inc.php');
include_once('include/adodb/adodb-pager.inc.php');
require_once('include/adodb/adodb-active-record.inc.php');
include_once('include/phptimer/class.phpTimer.php');

$requiredConfigVars = array(
    'dbtype', 'dbhost', 'dbuser', 'dbpw', 'dbname',
    'cfg_url', 'cfg_title', 'cfg_theme', 'cfg_lang', 'cfg_dir',
    'cfg_datadir', 'cfg_packagedir', 'tablepre', 'cfg_log',
    'cfg_email', 'cfg_sendmail', 'cfg_smtp_host', 'cfg_smtp_port',
    'cfg_offsettime', 'cfg_seo'
);

foreach ($requiredConfigVars as $requiredVarName) {
    if (!isset($$requiredVarName) || is_array($$requiredVarName)) {
        lanai_bootstrap_error('Invalid config.inc.php value for ' . $requiredVarName . '. Ensure config entries are scalar values.');
    }
}


$cfg['url'] = $cfg_url;
$cfg['title'] = $cfg_title;

$cfg['theme'] = $cfg_theme;
$cfg['lang'] = $cfg_lang;
$cfg['dir'] = $cfg_dir;
$cfg['datadir'] = $cfg_datadir;
$cfg['packdir'] = $cfg_packagedir;
$cfg['tablepre'] = $tablepre;
$cfg['log'] = $cfg_log;
$cfg['email'] = $cfg_email;
$cfg['sendmail'] = $cfg_sendmail;
$cfg['smtp_host'] = $cfg_smtp_host;
$cfg['smtp_port'] = $cfg_smtp_port;
$cfg['offset_time'] = $cfg_offsettime;
$cfg['seo'] = $cfg_seo;
$cfg['captcha_provider'] = isset($cfg_captcha_provider) ? $cfg_captcha_provider : 'default';
$cfg['turnstile_site_key'] = isset($cfg_turnstile_site_key) ? $cfg_turnstile_site_key : '';
$cfg['turnstile_secret_key'] = isset($cfg_turnstile_secret_key) ? $cfg_turnstile_secret_key : '';

$ADODB_CACHE_DIR = $cfg['datadir'] . "/cache/";
if (empty($dbtype)) {
    lanai_bootstrap_error("config.inc.php loaded but dbtype is empty. Ensure the file starts with '<?php' (not '<?') and includes database settings.");
}

$db = ADONewConnection(lanai_normalize_dbtype($dbtype));
$db->NConnect($dbhost, $dbuser, $dbpw, $dbname);

//$db->debug=1;

/*$charset = "SET NAMES 'utf8'"; */
/*$charset = "SET character_set_results=utf8"; */
/*$db->query($charset);*/

// load syslanai
include_once('include/lanai/class.system.php');
include_once('include/lanai/class.html.php');
include_once('include/lanai/class.pager.php');
$sys_lanai = new Systems();


/* second security level check */
if (($sys_lanai->security_check()) and (!$sys_lanai->isWin())) {
?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 10000,
        timerProgressBar: true,
    });

    Toast.fire({
        icon: 'warning',
        title: 'Security issue detected. Please review file permissions.',
    html: `
            <div style="text-align:left; font-size:13px;">
                <ul style="margin:5px 0 0 18px; padding:0;">
                    <li><b>config.inc.php</b> must NOT be writable</li>
                    <li><b>blocks</b> directory must NOT be writable</li>
                    <li><b>modules</b> directory must NOT be writable</li>
                    <li><b>theme</b> directory must NOT be writable</li>
                </ul>
            </div>
        `,
        background: '#fff3cd',
        color: '#856404',
        iconColor: '#ffb300'
    });
});
</script>
<?php
}


// EzShoping Cart
if (empty($loadlang) || $loadlang === 'yes') {
    if (file_exists('modules/ezshopingcart/cartsession.php')) {
        include_once('modules/ezshopingcart/cartsession.php');
    }
}

if ($cfg['log'] == 'yes') {
    // write click stream
    $sys_lanai->setLogs();
}

// offline
if (($cfg_off == "yes") and ($offpage != "yes") and (stripos($_SERVER['PHP_SELF'] ?? '', "setting.php") === false)) {
    $sys_lanai->go2Page("offline.php");
}

// load sys lang
if (empty($loadlang) || $loadlang === 'yes') {

    if (file_exists("language/lang-" . $cfg_lang . ".php")) {
        include_once("language/lang-" . $cfg_lang . ".php");
    } else {
        include_once("language/lang-english.php");
    }
}

// sys theme
if (file_exists("theme/" . $cfg_theme . "/theme.php")) {
    include_once("theme/" . $cfg_theme . "/theme.php");
} else {
    $cfg_theme = "default";
    include_once("theme/" . $cfg_theme . "/theme.php");
}

// smarty
# changes this value according to your uploaded smarty distribution.
# don't forget to add trailing back slash
# change 'username' to your username on web hosting account
define("SMARTY_DIR", "include/smarty/");
require_once(SMARTY_DIR . "SmartyBC.class.php");
$smarty = new SmartyBC;
$smarty->compile_dir = $cfg['datadir'] . "/cache";
$smarty->template_dir = "theme/" . $cfg_theme . "/html";
$smarty->assign("cfgTheme", $cfg_theme);

// parse request value
/*
 $INPUT= array();
     foreach ($_GET as $_var=>$_val) $INPUT[$_var]= $_val;
     foreach ($_POST as $_var=>$_val) $INPUT[$_var]= $_val;
     foreach ($_COOKIE as $_var=>$_val) $INPUT[$_var]= $_val;
 */

// load meta
include_once("modules/config/module.php");
$obMeta = new stdClass();
$previousFetchMode = $db->SetFetchMode(ADODB_FETCH_ASSOC);
$metaRow = $db->GetRow(
    "SELECT * FROM " . $cfg['tablepre'] . "meta WHERE mtaId = ?",
    array(1)
);
$db->SetFetchMode($previousFetchMode);
if (is_array($metaRow)) {
    $metaPropertyNames = array(
        'mtadescription' => 'mtaDescription',
        'mtaabstract' => 'mtaAbstract',
        'mtaauthor' => 'mtaAuthor',
        'mtadistribution' => 'mtaDistribution',
        'mtakeywords' => 'mtaKeywords',
        'mtafavicon' => 'mtaFavicon',
        'mtalogo' => 'mtaLogo',
        'mtashowsitename' => 'mtaShowSiteName'
    );
    foreach ($metaRow as $field => $value) {
        $fieldName = strtolower($field);
        $propertyName = isset($metaPropertyNames[$fieldName])
            ? $metaPropertyNames[$fieldName]
            : $field;
        $obMeta->{$propertyName} = $value;
    }
}

// setlog
if (file_exists("modules/log/module.php")) {
    include_once("modules/log/module.php");
    $obsyslog = new SysLog();
    $obsyslog->setLog();
    unset($obsyslog);
}

?>
