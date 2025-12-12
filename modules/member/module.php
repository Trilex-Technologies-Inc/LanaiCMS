<?
include_once("class.MemberPager.php");

/**
 * User
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2006
 * @version $Id: module.php,v 1.1 2007/03/23 12:37:35 redlinesoft Exp $
 * @access public
 **/
class User
{

    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    function User()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
        //$this->db->debug=true;
    }


    function getUserList($rows = 30)
    {
        $this->getUser(0);
        $pager = new MemberPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }


    function getUser($mid)
    {
        if ($mid == "0") {
            $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user";
        } else {
            $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userId=$mid";
        }
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getUserLogin($login)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userLogin='" . $login . "'";
        $rs = $this->db->execute($sql);
        return $rs;
    }


    function getUserPrivilege($mid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user WHERE userId=$mid";
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs->fields['userPrivilege'];
    }

    function getUserIdByLogin($login)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userLogin='" . $login . "'";
        $rs = $this->db->execute($sql);
        return ($rs->fields['userId']);
    }

    function setUserActive($mid, $value)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "user 
					SET userActive='" . $value . "'
					WHERE userId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }


    function setDeleteUser($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "user WHERE userId=$mid";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewUser($userFname, $userLname, $userAddress1, $userAddress2, $userCity, $userState, $cntId, $userZipcode, $userPhone, $userFax, $userMobile, $userEmail, $userURL, $userLogin, $userPassword, $userPrivilege)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "user 
					(userFname,userLname,userAddress1,userAddress2,userCity,userState,cntId,userZipcode,userPhone,userFax,userMobile,userEmail,userURL,userLogin,userPassword,userPrivilege,userCreated,userActive) 
					VALUES ('" . $userFname . "','" . $userLname . "','" . $userAddress1 . "','" . $userAddress2 . "','" . $userCity . "',
					'" . $userState . "','" . $cntId . "','" . $userZipcode . "','" . $userPhone . "','" . $userFax . "','" . $userMobile . "',
					'" . $userEmail . "','" . $userURL . "','" . $userLogin . "','" . md5($userPassword) . "','" . $userPrivilege . "',NOW(),'y')";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setUserRegister($userFname, $userLname, $userEmail, $userLogin, $userPassword)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "user 
					(userFname,userLname,userEmail,userLogin,userPassword,userCreated,userActive)
					VALUES ('" . $userFname . "','" . $userLname . "','" . $userEmail . "','" . $userLogin . "','" . md5($userPassword) . "',NOW(),'n')";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setUpdateUser($uid, $userFname, $userLname, $userAddress1, $userAddress2, $userCity, $userState, $cntId, $userZipcode, $userPhone, $userFax, $userMobile, $userEmail, $userURL, $userLogin, $userPrivilege)
    {
        global $db, $tablepre;
        $sql = "UPDATE " . $tablepre . "user 
					SET userFname='" . $userFname . "', userLname='" . $userLname . "', userAddress1='" . $userAddress1 . "', userAddress2='" . $userAddress2 . "', userCity='" . $userCity . "', userState='" . $userState . "', cntId='" . $cntId . "',
						userZipcode='" . $userZipcode . "', userPhone='" . $userPhone . "', userFax='" . $userFax . "', userMobile='" . $userMobile . "', userEmail='" . $userEmail . "', userURL='" . $userURL . "', userLogin='" . $userLogin . "',userPrivilege='" . $userPrivilege . "' 
					WHERE userId=$uid";
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs;
    }

    function setUpdateUserPassword($uid, $userPassword1)
    {
        global $db, $tablepre;
        $sql = "UPDATE " . $tablepre . "user 
					SET userPassword='" . $userPassword1 . "' 
					WHERE userId=$uid";
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs;
    }

    function getUserActivate($u, $p)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user
                    WHERE userLogin='" . $u . "' AND userPassword='" . $p . "' ";
        $rs = $this->db->execute($sql);
        return ($rs);
    }

    function isUserExist($mid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user WHERE userId=$mid";
        $rs = $db->execute($sql);
        if ($rs->recordcount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function isUserImageExist($mid)
    {
        global $cfg_datadir, $sys_lanai;
        if (file_exists($cfg_datadir . $sys_lanai->getPath() . "uimage" . $sys_lanai->getPath() . "u" . $mid . ".gif")) {
            return true;
        } else {
            return false;
        }
    }

    function getCountry($cntid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "country WHERE cntId='$cntid'";
        $rs = $db->execute($sql);
        return $rs->fields['cntName'];
    }

    function setCountryCombo($cntid, $name)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "country ORDER BY cntName ASC";
        $rs = $db->execute($sql);
        ?>
        <select name="<?= $name; ?>">
            <?
            while (!$rs->EOF) {
                if ($cntid == $rs->fields['cntId']) {
                    $select = "selected";
                } else {
                    $select = "";
                }
                ?>
                <option value="<?= $rs->fields['cntId']; ?>" <?= $select; ?>><?= $rs->fields['cntName']; ?></option>
                <?
                $rs->movenext();
            } // while
            ?>
        </select>
        <?
    }

}


?>
