<?php
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

    function __construct()
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
					WHERE pllId=" . intval($mid);
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
					WHERE pllId=" . intval($mid) . " AND ppoTitle!='' 					
					ORDER BY ppoId ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getPollOptionItemById($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_option
					WHERE pllId=" . intval($mid) . " ORDER BY ppoId ASC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewPollItem($pllTitle, $pllLag)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll
					(pllTitle,pllLag,pllActive,pllCreate)
					VALUES (" . $this->db->qstr($pllTitle) . "," . intval($pllLag) . ",'y',NOW())";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewPollOption($mid, $ppoItem)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll_option
					(pllId,ppoTitle,ppoScore)
					VALUES (" . intval($mid) . "," . $this->db->qstr($ppoItem) . ",0)";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditPollItem($mid, $pllTitle, $pllLag)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll 
					SET pllTitle=" . $this->db->qstr($pllTitle) . ",pllLag=" . intval($pllLag) . " 
					WHERE pllId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setEditPollOption($mid, $ppoItem)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll_option 
					SET ppoTitle=" . $this->db->qstr($ppoItem) . " 
					WHERE ppoId=" . intval($mid);
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
					WHERE pllTitle LIKE " . $this->db->qstr($pllTitle);
        $rs = $this->db->execute($sql);
        return ($rs->fields['pllId']);
    }

    function setDeletePollItem($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "poll 
					WHERE pllId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeletePollOptionItem($mid)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "poll_option  
					WHERE pllId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setPollItemActive($mid, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "poll  
					SET pllActive=" . $this->db->qstr($value) . "
					WHERE pllId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getLastVoteTimestamp($mid)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "poll_stat 
					WHERE pllId=" . intval($mid) . " AND pstIP LIKE " . $this->db->qstr($_SERVER['REMOTE_ADDR']) . " ";
        $rs = $this->db->execute($sql);
        return ($rs->fields['pstTime']);
    }

    function setVotePollOptionItem($mid, $voteChoice)
    {
        $mid = intval($mid);
        $voteChoice = intval($voteChoice);
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
					WHERE pllId=" . intval($mid);
        $rs = $this->db->execute($sql);
        return ($rs->fields[0]);
    }

    function setVoteTimeStamp($mid)
    {
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "poll_stat  
					(pllId,pstIP,pstTime)
					VALUES (" . intval($mid) . "," . $this->db->qstr($_SERVER['REMOTE_ADDR']) . "," . time() . ") ";
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