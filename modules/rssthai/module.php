<?php
include_once("class.LRSSThaiPager.php");

/**
 * LRSSThai
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2006
 * @version $Id: module.php,v 1.1 2007/03/23 12:37:38 redlinesoft Exp $
 * @access public
 **/
class LRSSThai
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

    function getRSS()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "rss 
					ORDER BY rssOrder ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getShowRSS()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "rss 
					WHERE rssActive='y' ORDER BY rssOrder ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getRSSById($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "rss 
					WHERE rssId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getRSSMaxOrder()
    {
        $sql = "SELECT MAX(rssOrder) FROM " . $this->cfg['tablepre'] . "rss ";
        $rs = $this->db->execute($sql);
        return ($rs->fields[0]);
    }

    function setNewRSS($rssTitle, $rssURL, $rssReload, $rssView, $rssItemCount, $rssShowDescription, $rssNumColumn, $rssNumImage, $rssFixedImage, $rssAlterImage, $rssImageWidth, $rssImageHeight, $rssImageAlign, $rssTarget)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "rss 
					(rssTitle,rssURL,rssReload,rssView,rssItemCount,rssShowDescription,rssNumColumn,rssNumImage,rssFixedImage,rssAlterImage,rssImageWidth,rssImageHeight,rssImageAlign,rssTarget,rssOrder) 
					VALUES (" . $this->db->qstr($rssTitle) . "," . $this->db->qstr($rssURL) . "," . intval($rssReload) . "," . $this->db->qstr($rssView) . "," . intval($rssItemCount) . "," . $this->db->qstr($rssShowDescription) . "," . intval($rssNumColumn) . "," . intval($rssNumImage) . "," . $this->db->qstr($rssFixedImage) . "," . $this->db->qstr($rssAlterImage) . "," . intval($rssImageWidth) . "," . intval($rssImageHeight) . "," . $this->db->qstr($rssImageAlign) . "," . $this->db->qstr($rssTarget) . "," . (($this->getRSSMaxOrder()) + 1) . ") ";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditRSS($rssId, $rssTitle, $rssURL, $rssReload, $rssView, $rssItemCount, $rssShowDescription, $rssNumColumn, $rssNumImage, $rssFixedImage, $rssAlterImage, $rssImageWidth, $rssImageHeight, $rssImageAlign, $rssTarget)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss SET 
					rssTitle=" . $this->db->qstr($rssTitle) . ",rssURL=" . $this->db->qstr($rssURL) . ",rssReload=" . intval($rssReload) . ",rssView=" . $this->db->qstr($rssView) . ",
					rssItemCount=" . intval($rssItemCount) . ",rssShowDescription=" . $this->db->qstr($rssShowDescription) . ",rssNumColumn=" . intval($rssNumColumn) . ",
					rssNumImage=" . intval($rssNumImage) . ",rssFixedImage=" . $this->db->qstr($rssFixedImage) . ",rssAlterImage=" . $this->db->qstr($rssAlterImage) . ",
					rssImageWidth=" . intval($rssImageWidth) . ",rssImageHeight=" . intval($rssImageHeight) . ",rssImageAlign=" . $this->db->qstr($rssImageAlign) . ",
					rssTarget=" . $this->db->qstr($rssTarget) . "
					WHERE rssId=" . intval($rssId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setRSSOrder($mid, $order)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss 
					SET rssOrder=" . intval($order) . "
					WHERE rssId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setRSSActive($mid, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss 
					SET rssActive=" . $this->db->qstr($value) . "
					WHERE rssId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeleteRSS($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "rss 
					WHERE rssId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getRSSList()
    {
        $this->getRSS();
        $pager = new LRSSThaiPager($this->db, $this->_sql, true);
        if (isset($rows))
            $pager->Render($rows);
        else
            $pager->Render();
    }

}


?>