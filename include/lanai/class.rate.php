<?php

class Rate {
	var $db;
	var $cfg;
	
	function __construct() {
		global $db,$cfg;
		$this->db=$db;
		$this->cfg=$cfg;
	}
	
/*	add rating 	*/
	function addRate($catTite,$catId,$value) {
		setcookie("rate",1,time()+(60*24));
	}
/*	is rating data exist */
	function isRateExist($catTite,$catId) {
		$sql="SELECT * FROM ".$this->cfg['tablepre']."rate 
					WHERE catTitle='".$catTite."' AND catId=".$catId;
		$rs=$this->db->execute($sql);
		if (($rs->recordcount()) > 0) {
			$result=true;
		} else {
			$result=false;
		}
		return $result;
	}
/*	update rate */
	function updateRate($catTite,$catId,$value) {
		setcookie("rate",1,time()+(60*24));
	}
/*	show rate */
	function getRate($catTite,$catId) {
		$sql="SELECT * FROM ".$this->cfg['tablepre']."rate 
					WHERE catTitle='".$catTite."' AND catId=".$catId;
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
}