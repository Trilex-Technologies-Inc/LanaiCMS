<?
include_once("class.PollPager.php");


/**
 * Poll
 *
 * @package Download Module
 * @author Anoochit Chalothorn (anoochit_c@hotmail.com)
 * @copyright Copyright (c) 2006
 * @version $Id: module.php,v 1.1 2007/03/23 12:37:37 redlinesoft Exp $
 * @access public
 **/
class Poll
{

    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    function Poll()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
        //$this->db->debug=true;
    }

    function getPoll()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll," . $this->cfg['tablepre'] . "poll_option 
					WHERE " . $this->cfg['tablepre'] . "poll.pllId=" . $this->cfg['tablepre'] . "poll_option.pllId 
					GROUP BY " . $this->cfg['tablepre'] . "poll.pllId 
					ORDER BY pllTitle ASC ";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollItemById($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollItemShow()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll," . $this->cfg['tablepre'] . "poll_option 
					WHERE " . $this->cfg['tablepre'] . "poll.pllId=" . $this->cfg['tablepre'] . "poll_option.pllId 
						AND " . $this->cfg['tablepre'] . "poll.pllActive='y' 
					GROUP BY " . $this->cfg['tablepre'] . "poll.pllId 
					ORDER BY " . $this->cfg['tablepre'] . "poll.pllId  DESC ";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollOptionItemShow($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_option 
					WHERE pllId=" . $mid . " AND ppoTitle!='' 					
					ORDER BY ppoId ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollOptionItemById($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_option
					WHERE pllId=" . $mid . " ORDER BY ppoId ASC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewPollItem($pllTitle, $pllLag)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll
					(pllTitle,pllLag,pllActive,pllCreate)
					VALUES ('" . $pllTitle . "'," . $pllLag . ",'y',NOW())";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewPollOption($mid, $ppoItem)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll_option
					(pllId,ppoTitle,ppoScore)
					VALUES (" . $mid . ",'" . $ppoItem . "',0)";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditPollItem($mid, $pllTitle, $pllLag)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll 
					SET pllTitle='" . $pllTitle . "',pllLag=" . $pllLag . " 
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditPollOption($mid, $ppoItem)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll_option 
					SET ppoTitle='" . $ppoItem . "' 
					WHERE ppoId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollOptionItemCount($option)
    {
        $x = 0;
        for ($i = 0; $i < count($option); $i++) {
            $pollOptionItem = $option[$i];
            $pollOptionItem = trim($pollOptionItem);
            if ((!empty($pollOptionItem)) or ($pollOptionItem != "")) {
                $x++;
            }
        }
        return $x;
    }

    function getPollItemIdByTitle($pllTitle)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll
					WHERE pllTitle LIKE '" . $pllTitle . "'";
        $rs = $this->db->execute($sql);
        return ($rs->fields['pllId']);
    }

    function setDeletePollItem()
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "poll 
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeletePollOptionItem($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "poll_option  
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setPollItemActive($mid, $value)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll  
					SET pllActive='" . $value . "'
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getLastVoteTimestamp($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_stat 
					WHERE pllId=" . $mid . " AND pstIP LIKE '" . $_SERVER['REMOTE_ADDR'] . "' ";
        $rs = $this->db->execute($sql);
        return ($rs->fields['pstTime']);
    }

    function setVotePollOptionItem($mid, $voteChoice)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_option 
					WHERE ppoId=" . $voteChoice . " AND pllId=" . $mid;
        $rs = $this->db->execute($sql);
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll_option 
					SET ppoScore=" . (($rs->fields['ppoScore']) + 1) . "
					WHERE pllId=" . $mid . " AND ppoId=" . $voteChoice;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getVoteTotal($mid)
    {
        $sql = "SELECT SUM(ppoScore) FROM " . $this->cfg['tablepre'] . "poll_option 
					WHERE pllId=" . $mid;
        $rs = $this->db->execute($sql);
        return ($rs->fields[0]);
    }

    function setVoteTimeStamp($mid)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll_stat  
					(pllId,pstIP,pstTime)
					VALUES (" . $mid . ",'" . $_SERVER['REMOTE_ADDR'] . "'," . time() . ") ";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollList($rows = 20)
    {
        $this->getPoll();
        $pager = new PollPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }


}

?>