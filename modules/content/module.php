<?php

include_once("class.ContentPager.php");

/**
 * Content
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2006
 * @version $Id: module.php,v 1.2 2007/05/07 05:25:45 redlinesoft Exp $
 * @access public
 **/
class Content
{
    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
        //$this->db->debug=true;
    }

    function getContent()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "content ORDER BY conId ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContentById($cid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "content 
					WHERE conId=" . intval($cid);
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditContent($conId, $conTitle, $conBody1, $conBody2, $allowComments = 'n')
    {
        $conId = (int)$conId;
        $uid = (int)$this->uid;

        $conTitle = $this->db->qstr($conTitle);
        $conBody1 = $this->db->qstr($conBody1);
        $conBody2 = $this->db->qstr($conBody2);
        $allowComments = $this->db->qstr($allowComments === 'y' ? 'y' : 'n');

        $sql = "
        UPDATE {$this->cfg['tablepre']}content
        SET
            userId      = $uid,
            conTitle    = $conTitle,
            conBody1    = $conBody1,
            conBody2    = $conBody2,
            conAllowComments = $allowComments,
            conModified = NOW()
        WHERE conId = $conId
    ";

        $rs = $this->db->Execute($sql);

        if (!$rs) {
            echo 'DB Error: ' . $this->db->ErrorMsg();
            return false;
        }

        return true;
    }


    //conId  userId  conTitle  conBody1  conBody2  conModified  conActive
    function setNewContent($conTitle, $conBody1, $conBody2, $allowComments = 'n')
    {
        $uid = (int)$this->uid;

        $conTitle = $this->db->qstr($conTitle);
        $conBody1 = $this->db->qstr($conBody1);
        $conBody2 = $this->db->qstr($conBody2);
        $allowComments = $this->db->qstr($allowComments === 'y' ? 'y' : 'n');

        $sql = "
        INSERT INTO {$this->cfg['tablepre']}content
        (userId, conTitle, conBody1, conBody2, conAllowComments, conModified, conActive)
        VALUES ($uid, $conTitle, $conBody1, $conBody2, $allowComments, NOW(), 'y')
    ";

        $rs = $this->db->Execute($sql);

        if (!$rs) {
            echo 'DB Error: ' . $this->db->ErrorMsg();
            return false;
        }

        return true;
    }


    function setDeleteContent($mid)
    {
        $this->db->execute("DELETE FROM " . $this->cfg['tablepre'] . "comment WHERE catTitle='content' AND catId=" . intval($mid));
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "content 
					WHERE conId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setContentActive($mid, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "content  
					SET conActive=" . $this->db->qstr($value) . "
					WHERE conId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContentList($rows = 30)
    {
        $this->getContent();
        $pager = new ContentPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getContentIdByTitle($title)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "content 
						WHERE conTitle LIKE " . $this->db->qstr($title) . " 
						ORDER BY conId DESC";
        $rs = $this->db->execute($sql);
        return ($rs->fields['conId']);
    }

    function setContentMenu($conid, $title)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "menu 
					(mnuParentId,mnuTitle,conId,mnuType,mnuActive,mnuOrder)
					VALUES (0," . $this->db->qstr($title) . "," . intval($conid) . ",'c','y'," . $this->getMaxMenuWeight() . ")";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getMaxMenuWeight()
    {
        $sql = "SELECT MAX(mnuOrder) FROM " . $this->cfg['tablepre'] . "menu ";
        $rs = $this->db->execute($sql);
        return ($rs->fields[0]);
    }

}


?>
