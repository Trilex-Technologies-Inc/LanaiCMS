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

    function getContent($filter = '')
    {
        global $sys_lanai;
        $where = array();
        // Staff without edit_content only see the pages they wrote.
        if (!$sys_lanai->userHasCapability('edit_content')) {
            $where[] = 'userId=' . (int)$this->uid;
        }
        if ($filter === 'pending') {
            $where[] = "conPending='y'";
        }
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "content"
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY conId ASC";
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

    function ensureEditorSchema()
    {
        $table = $this->cfg['tablepre'] . 'content';
        $columns = $this->db->MetaColumns($table);
        if (!$columns) return false;
        $names = array_map('strtolower', array_keys($columns));
        if (!in_array('conallowcomments', $names, true)
            && !$this->db->Execute("ALTER TABLE $table ADD COLUMN conAllowComments CHAR(1) NOT NULL DEFAULT 'n'")) {
            return false;
        }
        // 'y' while a page written by a contributor waits for an editor to publish it.
        if (!in_array('conpending', $names, true)
            && !$this->db->Execute("ALTER TABLE $table ADD COLUMN conPending CHAR(1) NOT NULL DEFAULT 'n'")) {
            return false;
        }
        return true;
    }

    function countPending()
    {
        $rs = $this->db->Execute("SELECT COUNT(*) AS n FROM " . $this->cfg['tablepre'] . "content WHERE conPending='y'");
        return $rs && !$rs->EOF ? (int)$rs->fields['n'] : 0;
    }

    function setEditContent($conId, $conTitle, $conBody1, $conBody2, $allowComments = 'n', $submitForReview = false)
    {
        $conId = (int)$conId;
        $uid = (int)$this->uid;

        $conTitle = $this->db->qstr($conTitle);
        $conBody1 = $this->db->qstr($conBody1);
        $conBody2 = $this->db->qstr($conBody2);
        $allowComments = $this->db->qstr($allowComments === 'y' ? 'y' : 'n');
        $review = $submitForReview ? "conActive = 'n', conPending = 'y'," : '';

        $sql = "
        UPDATE {$this->cfg['tablepre']}content
        SET
            $review
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
            return false;
        }

        return true;
    }


    //conId  userId  conTitle  conBody1  conBody2  conModified  conActive
    function setNewContent($conTitle, $conBody1, $conBody2, $allowComments = 'n', $pendingReview = false)
    {
        $uid = (int)$this->uid;

        $conTitle = $this->db->qstr($conTitle);
        $conBody1 = $this->db->qstr($conBody1);
        $conBody2 = $this->db->qstr($conBody2);
        $allowComments = $this->db->qstr($allowComments === 'y' ? 'y' : 'n');
        $active = $pendingReview ? "'n'" : "'y'";
        $pending = $pendingReview ? "'y'" : "'n'";

        $sql = "
        INSERT INTO {$this->cfg['tablepre']}content
        (userId, conTitle, conBody1, conBody2, conAllowComments, conModified, conActive, conPending)
        VALUES ($uid, $conTitle, $conBody1, $conBody2, $allowComments, NOW(), $active, $pending)
    ";

        $rs = $this->db->Execute($sql);

        if (!$rs) {
            return false;
        }

        return (int)$this->db->Insert_ID();
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
        // Publishing is the approval step, so it also clears the review flag.
        $pendingSql = $value === 'y' ? ", conPending='n'" : '';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "content  
					SET conActive=" . $this->db->qstr($value) . $pendingSql . "
					WHERE conId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getContentList($rows = 30, $filter = '')
    {
        $this->getContent($filter);
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
        $sql = "SELECT COALESCE(MAX(mnuOrder),0) AS maxOrder FROM " . $this->cfg['tablepre'] . "menu ";
        $rs = $this->db->execute($sql);
        return $rs ? (int)$rs->fields['maxOrder'] + 1 : 1;
    }

}


?>
