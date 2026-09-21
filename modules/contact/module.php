<?php
include_once("class.ContactPager.php");

class Contact
{

    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    //conFname  conLname  conPosition  conAddress1  conAddress2  conCity  conState  cntId  conZipcode  conPhone  conFax  conMobile  conEmail  conURL  conActive

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))

            $this->uid = $_SESSION['uid'];
        //$this->db->debug=true;
    }

    function setNewContact($conFname, $conLname, $conPosition, $conAddress1, $conAddress2, $conCity, $conState, $cntId, $conZipcode, $conPhone, $conFax, $conMobile, $conEmail, $conURL)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "contact 
					(conFname,conLname,conPosition,conAddress1,conAddress2,conCity,conState,cntId,conZipcode,conPhone,conFax,conMobile,conEmail,conURL,conActive) 
					VALUES (" . $this->db->qstr($conFname) . "," . $this->db->qstr($conLname) . "," . $this->db->qstr($conPosition) . "," . $this->db->qstr($conAddress1) . "," . $this->db->qstr($conAddress2) . "," . $this->db->qstr($conCity) . "," . $this->db->qstr($conState) . "," . $this->db->qstr($cntId) . "," . $this->db->qstr($conZipcode) . "," . $this->db->qstr($conPhone) . "," . $this->db->qstr($conFax) . "," . $this->db->qstr($conMobile) . "," . $this->db->qstr($conEmail) . "," . $this->db->qstr($conURL) . ",'y')";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setUpdateContact($conId, $conFname, $conLname, $conPosition, $conAddress1, $conAddress2, $conCity, $conState, $cntId, $conZipcode, $conPhone, $conFax, $conMobile, $conEmail, $conURL)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "contact 
					SET conFname=" . $this->db->qstr($conFname) . ",conLname=" . $this->db->qstr($conLname) . ",conPosition=" . $this->db->qstr($conPosition) . ",conAddress1=" . $this->db->qstr($conAddress1) . ",conAddress2=" . $this->db->qstr($conAddress2) . ",
						conCity=" . $this->db->qstr($conCity) . ",conState=" . $this->db->qstr($conState) . ",cntId=" . $this->db->qstr($cntId) . ",conZipcode=" . $this->db->qstr($conZipcode) . ",conPhone=" . $this->db->qstr($conPhone) . ",conFax=" . $this->db->qstr($conFax) . ",
						conMobile=" . $this->db->qstr($conMobile) . ",conEmail=" . $this->db->qstr($conEmail) . ",conURL=" . $this->db->qstr($conURL) . " 
					WHERE conId=" . intval($conId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeleteContact($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "contact 
					WHERE conId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setContactActive($mid, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "contact 
					SET conActive=" . $this->db->qstr($value) . "
					WHERE conId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContactById($cid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "contact WHERE conId=" . intval($cid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContact()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "contact ORDER BY conFname ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContactList($rows = 30)
    {
        $this->getContact();
        $pager = new ContactPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getContactCombo($name)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "contact WHERE conActive='y' ORDER BY conFName ASC";
        $rs = $this->db->execute($sql);
        $selectedCid = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 0;
        ?>
        <select
                name="<?= $name; ?>"
                class="form-select"
                onchange="MM_jumpMenu('parent', this, 0)"
                aria-label="<?= _CONTACT_TO; ?>"
        >
            <option value="<?= $_SERVER['PHP_SELF'] . "?modname=" . $_REQUEST['modname'] . "&cid=0"; ?>">
                -- <?= _SELECT; ?> --
            </option>
            <?php
            while (!$rs->EOF) {
                $selected = ($selectedCid == $rs->fields['conId']) ? 'selected' : '';
                ?>
                <option
                        value="<?= $_SERVER['PHP_SELF'] . "?modname=" . $_REQUEST['modname'] . "&cid=" . $rs->fields['conId']; ?>"
                    <?= $selected; ?>
                >
                    <?= $rs->fields['conFname']; ?> <?= $rs->fields['conLname']; ?>
                </option>
                <?php
                $rs->movenext();
            }
            ?>
        </select>
        <?php
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