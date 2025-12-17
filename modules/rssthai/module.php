<?
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

    function LRSSThai()
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
					WHERE rssId=$mid";
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
					VALUES ('" . $rssTitle . "','" . $rssURL . "'," . $rssReload . ",'" . $rssView . "'," . $rssItemCount . ",'" . $rssShowDescription . "'," . $rssNumColumn . "," . $rssNumImage . ",'" . $rssFixedImage . "','" . $rssAlterImage . "'," . $rssImageWidth . "," . $rssImageHeight . ",'" . $rssImageAlign . "','" . $rssTarget . "'," . (($this->getRSSMaxOrder()) + 1) . ") ";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditRSS($rssId, $rssTitle, $rssURL, $rssReload, $rssView, $rssItemCount, $rssShowDescription, $rssNumColumn, $rssNumImage, $rssFixedImage, $rssAlterImage, $rssImageWidth, $rssImageHeight, $rssImageAlign, $rssTarget)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss SET 
					rssTitle='" . $rssTitle . "',rssURL='" . $rssURL . "',rssReload=" . $rssReload . ",rssView='" . $rssView . "',
					rssItemCount=" . $rssItemCount . ",rssShowDescription='" . $rssShowDescription . "',rssNumColumn=" . $rssNumColumn . ",
					rssNumImage=" . $rssNumImage . ",rssFixedImage='" . $rssFixedImage . "',rssAlterImage='" . $rssAlterImage . "',
					rssImageWidth=" . $rssImageWidth . ",rssImageHeight=" . $rssImageHeight . ",rssImageAlign='" . $rssImageAlign . "',
					rssTarget='" . $rssTarget . "'
					WHERE rssId=$rssId";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setRSSOrder($mid, $order)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss 
					SET rssOrder=$order
					WHERE rssId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setRSSActive($mid, $value)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "rss 
					SET rssActive='" . $value . "'
					WHERE rssId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeleteRSS($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "rss 
					WHERE rssId=" . $mid;
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