<?

session_start();

/* sql injection ad-hoc blocking */
if (ereg("tbl*", $_SERVER['QUERY_STRING'])) {
    exit;
}

include_once('config.inc.php');
include_once('include/adodb/adodb.inc.php');
include_once('include/adodb/adodb-pager.inc.php');
require_once('include/adodb/adodb-active-record.inc.php');
include_once('include/phptimer/class.phpTimer.php');


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

$ADODB_CACHE_DIR = $cfg['datadir'] . "/cache/";
$db =& ADONewConnection($dbtype);
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

/* secound security level check */
if (($sys_lanai->security_check()) and (!$sys_lanai->isWin())) {
    ?>
    <div style="position:fixed; left:0px; top:0px; width:100%;	height:auto; z-index:1; background-color: #66CCCC; ">
        <table cellpadding="5" cellspacing="2" width="100%">
            <tr>
                <td>
                    <h2>Security check list</h2>
                    <ul>
                        <li>your configuration file 'config.inc.php' MUST NOT WRITEABLE</li>
                        <li>directory 'blocks' MUST NOT WRITEABLE</li>
                        <li>directory 'modules' MUST NOT WRITEABLE</li>
                        <li>directory 'theme' MUST NOT WRITEABLE</li>
                    </ul>
                    <p>* Please backup your data every day or every week! &nbsp;And offend change your administrator
                        username and password.
                        <br>** This software is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY;
                        without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
                        the GNU Lesser General Public License for more details.
                    </p>
                </td>
            <tr>
        </table>
    </div>
    <?
    //exit;
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
if (($cfg_off == "yes") and ($offpage != "yes") and (!eregi("setting.php", $_SERVER['PHP_SELF']))) {
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
$obMeta = new Meta();
$obMeta->Load("mtaId=1");

// setlog
if (file_exists("modules/log/module.php")) {
    include_once("modules/log/module.php");
    $obsyslog = new SysLog();
    $obsyslog->setLog();
    unset($obsyslog);
}

?>
