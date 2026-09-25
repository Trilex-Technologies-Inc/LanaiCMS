<?php

class Comment {
	var $db;
	var $cfg;
	
	function __construct() {
		global $db,$cfg;
		$this->db=$db;
		$this->cfg=$cfg;
	}
	/*	add comment	*/
	function addComment($catTitle,$catId,$comText,$comAuthor,$comEmail){
		$sql="INSERT INTO ".$this->cfg['tablepre']."comment 
					(catTitle, catId, comDetail,comAuthor,comEmail) 
					VALUES (".$this->db->qstr($catTitle).", ".intval($catId).", ".$this->db->qstr($comText).", ".$this->db->qstr($comAuthor).", ".$this->db->qstr($comEmail).") ";
		return $this->db->execute($sql);
	}
	/*	comment total */
	function getCommentTotal($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle=".$this->db->qstr($catTitle)." AND catId=".intval($catId);
		$rs=$this->db->execute($sql);
		return ($rs->recordcount());
	}
	
	/*	*/
	function getComment($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle=".$this->db->qstr($catTitle)." AND catId=".intval($catId)." ORDER BY comDate ASC, comId ASC";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	/*	if comment exist */
	function isCommentExist($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle=".$this->db->qstr($catTitle)." AND catId=".intval($catId);
		$rs=$this->db->execute($sql);
		if (($rs->recordcount())>0) {
			$rsx=true;
		} else {
			$rsx=false;
		}
		return $rsx;
	}
}

?>
