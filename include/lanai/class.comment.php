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
					VALUES ('".$catTitle."', ".$catId.", '".$comText."', '".$comAuthor."', '".$comEmail."') ";
		$rs=$this->db->execute($sql);
	}
	/*	comment total */
	function getCommentTotal($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle LIKE '".$catTitle."' AND catId=".$catId;
		$rs=$this->db->execute($sql);
		return ($rs->recordcount());
	}
	
	/*	*/
	function getComment($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle LIKE '".$catTitle."' AND catId=".$catId;
		$rs=$this->db->execute($sql);
		return $rs;
	}
	/*	if comment exist */
	function isCommentExist($catTitle,$catId){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."comment 
					WHERE catTitle LIKE '".$catTitle."' AND catId=".$catId;
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