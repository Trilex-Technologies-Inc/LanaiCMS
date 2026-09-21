<?php
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
    var $activationColumnEnsured = false;

    function __construct()
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
            $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userId=" . intval($mid);
        }
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getUserLogin($login)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userLogin=" . $this->db->qstr($login);
        $rs = $this->db->execute($sql);
        return $rs;
    }


    function getUserPrivilege($mid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user WHERE userId=" . intval($mid);
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs->fields['userPrivilege'];
    }

    function getUserIdByLogin($login)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user WHERE userLogin=" . $this->db->qstr($login);
        $rs = $this->db->execute($sql);
        return ($rs->fields['userId']);
    }

    function setUserActive($mid, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "user 
					SET userActive=" . $this->db->qstr($value) .
            ($value === 'y' ? ", userActivationToken=NULL" : "") . "
					WHERE userId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }


    function setDeleteUser($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "user WHERE userId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function ensureActivationTokenColumn()
    {
        if ($this->activationColumnEnsured) {
            return;
        }
        $table = $this->cfg['tablepre'] . 'user';
        $columns = $this->db->MetaColumns($table);
        if ($columns !== false && !isset($columns['USERACTIVATIONTOKEN'])) {
            $this->db->Execute("ALTER TABLE " . $table . " ADD userActivationToken VARCHAR(64) DEFAULT NULL");
        }
        $this->activationColumnEnsured = true;
    }

    function setNewUser($userFname, $userLname, $userAddress1, $userAddress2, $userCity, $userState, $cntId, $userZipcode, $userPhone, $userFax, $userMobile, $userEmail, $userURL, $userLogin, $userPassword, $userPrivilege)
    {
        global $sys_lanai;
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "user 
					(userFname,userLname,userAddress1,userAddress2,userCity,userState,cntId,userZipcode,userPhone,userFax,userMobile,userEmail,userURL,userLogin,userPassword,userPrivilege,userCreated,userActive) 
					VALUES (" . $this->db->qstr($userFname) . "," . $this->db->qstr($userLname) . "," . $this->db->qstr($userAddress1) . "," . $this->db->qstr($userAddress2) . "," . $this->db->qstr($userCity) . ",
					" . $this->db->qstr($userState) . "," . $this->db->qstr($cntId) . "," . $this->db->qstr($userZipcode) . "," . $this->db->qstr($userPhone) . "," . $this->db->qstr($userFax) . "," . $this->db->qstr($userMobile) . ",
					" . $this->db->qstr($userEmail) . "," . $this->db->qstr($userURL) . "," . $this->db->qstr($userLogin) . "," . $this->db->qstr($sys_lanai->hashPassword($userPassword)) . "," . $this->db->qstr($userPrivilege) . ",NOW(),'y')";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    /**
     * Register a new (inactive) user and return the activation token to email, or false on failure.
     */
    function setUserRegister($userFname, $userLname, $userEmail, $userLogin, $userPassword)
    {
        global $sys_lanai;
        $this->ensureActivationTokenColumn();
        $activationToken = bin2hex(random_bytes(20));
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "user 
					(userFname,userLname,userEmail,userLogin,userPassword,userActivationToken,userCreated,userActive)
					VALUES (" . $this->db->qstr($userFname) . "," . $this->db->qstr($userLname) . "," . $this->db->qstr($userEmail) . "," . $this->db->qstr($userLogin) . "," . $this->db->qstr($sys_lanai->hashPassword($userPassword)) . "," . $this->db->qstr($activationToken) . ",NOW(),'n')";
        $rs = $this->db->execute($sql);
        return $rs ? $activationToken : false;
    }

    function setUpdateUser($uid, $userFname, $userLname, $userAddress1, $userAddress2, $userCity, $userState, $cntId, $userZipcode, $userPhone, $userFax, $userMobile, $userEmail, $userURL, $userLogin, $userPrivilege)
    {
        global $db, $tablepre;
        $sql = "UPDATE " . $tablepre . "user 
					SET userFname=" . $db->qstr($userFname) . ", userLname=" . $db->qstr($userLname) . ", userAddress1=" . $db->qstr($userAddress1) . ", userAddress2=" . $db->qstr($userAddress2) . ", userCity=" . $db->qstr($userCity) . ", userState=" . $db->qstr($userState) . ", cntId=" . $db->qstr($cntId) . ",
						userZipcode=" . $db->qstr($userZipcode) . ", userPhone=" . $db->qstr($userPhone) . ", userFax=" . $db->qstr($userFax) . ", userMobile=" . $db->qstr($userMobile) . ", userEmail=" . $db->qstr($userEmail) . ", userURL=" . $db->qstr($userURL) . ", userLogin=" . $db->qstr($userLogin) . ",userPrivilege=" . $db->qstr($userPrivilege) . " 
					WHERE userId=" . intval($uid);
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs;
    }

    /**
     * Set a user's password. Accepts a plaintext password and hashes it internally.
     */
    function setUpdateUserPassword($uid, $userPassword)
    {
        global $db, $tablepre, $sys_lanai;
        $sql = "UPDATE " . $tablepre . "user 
					SET userPassword=" . $db->qstr($sys_lanai->hashPassword($userPassword)) . " 
					WHERE userId=" . intval($uid);
        //$db->debug=true;
        $rs = $db->execute($sql);
        return $rs;
    }

    /**
     * Look up a pending activation by login + activation token.
     */
    function getUserActivate($userLogin, $token)
    {
        $this->ensureActivationTokenColumn();
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "user
                    WHERE userLogin=" . $this->db->qstr($userLogin) . " AND userActivationToken=" . $this->db->qstr($token) . " ";
        $rs = $this->db->execute($sql);
        return ($rs);
    }

    function isUserExist($mid)
    {
        global $db, $tablepre;
        $sql = "SELECT * FROM " . $tablepre . "user WHERE userId=" . intval($mid);
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
        $sql = "SELECT * FROM " . $tablepre . "country WHERE cntId=" . $db->qstr($cntid);
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
            <?php
            while (!$rs->EOF) {
                if ($cntid == $rs->fields['cntId']) {
                    $select = "selected";
                } else {
                    $select = "";
                }
                ?>
                <option value="<?= $rs->fields['cntId']; ?>" <?= $select; ?>><?= $rs->fields['cntName']; ?></option>
                <?php
                $rs->movenext();
            } // while
            ?>
        </select>
        <?php
    }

}


?>
