<?
include_once("class.ContactPager.php");

class Contact
{

    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    //conFname  conLname  conPosition  conAddress1  conAddress2  conCity  conState  cntId  conZipcode  conPhone  conFax  conMobile  conEmail  conURL  conActive

    function Contact()
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
					VALUES ('" . $conFname . "','" . $conLname . "','" . $conPosition . "','" . $conAddress1 . "','" . $conAddress2 . "','" . $conCity . "','" . $conState . "','" . $cntId . "','" . $conZipcode . "','" . $conPhone . "','" . $conFax . "','" . $conMobile . "','" . $conEmail . "','" . $conURL . "','y')";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setUpdateContact($conId, $conFname, $conLname, $conPosition, $conAddress1, $conAddress2, $conCity, $conState, $cntId, $conZipcode, $conPhone, $conFax, $conMobile, $conEmail, $conURL)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "contact 
					SET conFname='" . $conFname . "',conLname='" . $conLname . "',conPosition='" . $conPosition . "',conAddress1='" . $conAddress1 . "',conAddress2='" . $conAddress2 . "',
						conCity='" . $conCity . "',conState='" . $conState . "',cntId='" . $cntId . "',conZipcode='" . $conZipcode . "',conPhone='" . $conPhone . "',conFax='" . $conFax . "',
						conMobile='" . $conMobile . "',conEmail='" . $conEmail . "',conURL='" . $conURL . "' 
					WHERE conId=$conId";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeleteContact($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "contact 
					WHERE conId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setContactActive($mid, $value)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "contact 
					SET conActive='" . $value . "'
					WHERE conId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContactById($cid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "contact WHERE conId=$cid";
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
                $selected = ($_REQUEST['cid'] == $rs->fields['conId']) ? 'selected' : '';
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