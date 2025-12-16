<?

/**
 *  System Class (Sys Lanai)
 *
 */
class Systems
{

    var $ver = "2.0";

    /**
     *  Set Page Title
     *
     * @param string $title
     */
    function setPageTitle($title = "Lanai Core")
    {
        ?>
        <SCRIPT LANGUAGE="JavaScript">
            <!--
            document.title = "<?=$title; ?>";
            //-->
        </SCRIPT>
        <?
    }

    /**
     * Encode URL
     *
     * @param srting $url
     * @return string
     */
    function getSEOLink($url)
    {
        global $cfg;

        if ($cfg['seo'] == "yes") {
            // Example input: module.php?modname=news&mf=nwsview&cid=8
            $parts = parse_url($url);

            // If URL has no query string, just return it
            if (empty($parts['query'])) {
                return $url;
            }

            parse_str($parts['query'], $params);

            // Only apply SEO for news module
            if (!isset($params['modname']) || $params['modname'] !== 'news') {
                return $url;
            }

            // Build SEO-friendly link for news
            $link = 'news';

            if (!empty($params['mf'])) {
                $link .= '/' . $params['mf'];
            }

            if (!empty($params['cid'])) {
                $link .= '/' . $params['cid'];
            }

            // Return clean URL (without .htm)
            return $link;
        }

        // SEO disabled → return original
        return $url;
    }


    /**
     * Retrive Menu Form Menu Table
     *
     * @return  adodb_recordset
     */
    function getMenu()
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "menu 
						WHERE mnuActive='y' ORDER BY mnuOrder ASC ";
        $rs = $db->execute($sql);
        return $rs;
    }

    /**
     * Retrive Module Form Module Table
     *
     * @param integer $mid
     * @return adodb_recordset
     */
    function getModule($mid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "module WHERE modId=$mid";
        $rs = $db->execute($sql);
        return $rs;
    }

    /**
     * Retrive Block From Block Table
     *
     * @param string $pos
     * @return adodb_recordset
     */
    function getBlock($pos)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "block 
					WHERE blcActive='y' 
					AND blcPosition='" . $pos . "'
					ORDER BY blcOrder ASC";
        $rs = $db->execute($sql);
        return $rs;
    }

    function getBanners()
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "banner 
				
					ORDER BY banId  ASC";
        $rs = $db->execute($sql);
        return $rs;
    }

    /**
     * Feed RSS Url
     *
     * @param string $url
     * @return  array
     */
    function getRSSFeed($url)
    {
        $feedfile = preg_replace("/\.\./", "donthackthis", $url);
        $feedfile = preg_replace("/^\//", "ummmmno", $feedfile);
        // Create a need feedParser object
        $p = new feedParser();
        // Read in our sample feed file
        $data = @implode("", @file($feedfile));
        // Tell feedParser to parse the data
        return $p->parseFeed($data);

    }

    function getUserAuthentication($username = "", $password = "")
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user WHERE userLogin='$username' AND userPassword='" . md5($password) . "' AND userActive='y'";
        //$db->debug=true;
        $rs = $db->execute($sql);
        if ($rs->recordcount() > 0) {
            //return $rs->fields['userId'].$rs->fields['userPrivilege'];
            return $rs->fields['userId'];
        } else {
            return 0;
        }
    }

    function setMail2($name, $from, $message, $subject)
    {
        global $cfg_title, $cfg_email;
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html;\r\n";
        $headers .= "From: " . $from;
        $msg = $message . "\r\n" . $name;
        return mail($cfg_email, $subject, nl2br($msg), $headers);
    }

    function setMail($name, $to, $from, $message, $subject)
    {
        global $cfg_title, $cfg_email;
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html;\r\n";
        $headers .= "From: " . $from;
        $msg = $message . "\r\n" . $name;
        return mail($to, $subject, nl2br($msg), $headers);
    }

    function isUserExist($uid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user 
						WHERE userId=$uid ";
        $rs = $db->execute($sql);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    function isUserLogin()
    {
        if ((!empty($_SESSION['uid'])) and ($_SESSION['uid'] > 0)) {
            return true;
        } else {
            return false;
        }
    }

    // getRealUid($_SESSION['uid']);
    function getRealUid($upid)
    {
        $uid = substr($upid, 0, strlen($upid) - 1);
        return $uid;
    }

    function getPriUid()
    {
        $uid = substr($_SESSION['uid'], strlen($_SESSION['uid']) - 1, strlen($_SESSION['uid']));
        return $uid;
    }

    /*
    function getUserInfo() {
        global $db,$tablepre;

        $sql="SELECT * FROM ".$tablepre."user WHERE userId=".$this->getRealUid($_SESSION['uid']);
        //$db->debug=true;
        $rs=$db->execute($sql);
        if ($rs) {
            return $rs->fields['userFname'];
        } else {
            return 0;
        }
    }
    */

    function getLoginStatus()
    {
        global $cfg_lang;
        if ((!$_SESSION['uid'])) {
            ?>[<a href="module.php?m=member&f=login" class="txtIcon"><?= _NOT_LOGIN; ?></a>] [<a
                    href="module.php?m=member&f=signup" class="txtIcon"><?= _SIGNUP; ?></a>]<?
        } else {
            ?><?= _LOGIN_AS; ?> <a href="module.php?m=member" class="txtIcon"><?= $this->getUserInfo(); ?></a>
            [<a href="module.php?m=member&f=logout" class="txtIcon"><?= _LOGOUT; ?></a>]<?
        }
    }

    function getErrorBox($errmessage = "")
    {
        global $cfg;
        ?>
        <table>
            <tr>
                <td><img src="theme/<?= $cfg['theme']; ?>/images/worning.gif"></td>
                <td><span class="txtWorning"><?= $errmessage; ?></span></td>
            </tr>
        </table>
        <?
    }

    function getInfoBox($errmessage = "")
    {
        global $cfg;
        ?>
        <table>
            <tr>
                <td><img src="theme/<?= $cfg['theme']; ?>/images/ok.gif"></td>
                <td><span class="txtWorning"><?= $errmessage; ?></span></td>
            </tr>
        </table>
        <?
    }

    function getErrorAlertBox($errmessage = "")
    {
        global $cfg;
        ?>
        <SCRIPT LANGUAGE="JavaScript">
            <!--
            alert('<?=$errmessage; ?>')
            //-->
        </SCRIPT>
        <?
    }

    function go2Page($page)
    {
        ?>
        <script> location.href = "<?=$page; ?>";   </script><?
    }

    function goBack($page = 1)
    {
        ?>
        <script> history.back(<?=$page; ?>);   </script><?
    }

    function setLogs()
    {
        global $db, $tablepre, $cfg_offsettime;
        $time = time();
        $time2 = gmdate("YmdHis", time() + ($cfg_offsettime * 3600));
        if ($_SESSION['uid'] == "") {
            $uid = 0;
        } else {
            $uid = $_SESSION['uid'];
        }
        //$db->debug=TRUE;
        $sql = "SELECT * FROM " . $tablepre . "logs WHERE userId=" . $uid . " AND logModified > " . ($time - 10) . " ORDER BY logModified DESC";

        $rs = $db->execute($sql);
        if (($rs->recordcount()) > 0) {
            $visit = 0;
        } else {
            $visit = 1;
        }
        $sql = "INSERT INTO " . $tablepre . "logs VALUES (null," . $uid . ",'" . $this->getCountryByIp($_SERVER['REMOTE_ADDR']) . "','" . $_SERVER['HTTP_USER_AGENT'] . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $_SERVER['REQUEST_URI'] . "'," . $visit . ",'" . $time . "',$time2)";
        //$db->debug=TRUE;
        $rs = $db->execute($sql);
    }


    function getCountryByIp($ip)
    {
        global $cfg;
        include_once($cfg['dir'] . "/include/geoip/geoip.inc");
        $gi = geoip_open($cfg['dir'] . "/include/geoip/GeoIP.dat", 1);
        $cnid = geoip_country_code_by_addr($gi, $ip);
        geoip_close($gi);
        return $cnid;
    }

    /*
    function getCountryByIp($ip){
        global $db,$tablepre;
        $long=sprintf("%u",ip2long($ip));
        $sql="SELECT * FROM ".$tablepre."ip WHERE $long BETWEEN start AND end";
        $rs=$db->execute($sql);
        return $rs->fields[0];
    }
    */

    function isWin()
    {
        if ((strtolower(substr(getenv("OS"), 0, 3))) == "win") {
            $x = TRUE;
        } else {
            $x = FALSE;
        }
        return $x;
    }

    function getPath()
    {
        if ($this->isWin()) {
            return "\\";
        } else {
            return "/";
        }
    }

    function getMySQLVer()
    {
        global $db;
        $aver = $db->ServerInfo();
        return ($aver['version']);
    }

    var $ajax;

    function loadAjaxFunction($module)
    {
        if (!empty($module)) {
            if (file_exists("modules/" . $module . "/ajaxfunction.class.php")) {
                require_once("modules/" . $module . "/ajaxfunction.class.php"); // include our class
                $this->ajax = new AjaxFunctions();
            }

        }
    }

    function loadAjaxCode($module)
    {
        if (!empty($module)) {
            if (file_exists("modules/" . $module . "/ajaxfunction.class.php")) {
                ?>
                <script type="text/javascript"
                        src="include/ajaxcore/prototype.js"></script> <!-- include stantard prototype library -->
                <script type="text/javascript"
                        src="include/ajaxcore/AjaxCore.js"></script> <!-- include AjaxCore library -->
                <?
                echo $this->ajax->getJSCode();
            }
        }
    }

    function loadWYSIWYG($name, $toolname = "MyToolbar", $width = "640", $height = "300")
    {
        $sBasePath = "include/fckeditor/";
        $oFCKeditor1 = new FCKeditor($name);
        $oFCKeditor1->ToolbarSet = "MyToolbar";
        $oFCKeditor1->Width = "640";
        $oFCKeditor1->Height = "300";
        $oFCKeditor1->BasePath = $sBasePath;
        $oFCKeditor1->Value = $tempstr;
        $oFCKeditor1->Create();
    }

    function security_check()
    {
        $install_dir = file_exists("install");
        $write_config = is_writeable("config.inc.php");
        $write_blocdir = is_writeable("blocks");
        $write_moddir = is_writeable("modules");
        $write_themedir = is_writeable("theme");
        $result = ($install_dir or $write_blocdir or $write_moddir or $write_themedir);
        return $result;
    }

    function asc_shift($string, $amount)
    {
        $key = substr($string, 0, 1);
        if (strlen($string) == 1) {
            return chr(ord($key) + $amount);
        } else {
            return chr(ord($key) + $amount) . asc_shift(substr($string, 1, strlen($string) - 1), $amount);
        }
    }

}

?>