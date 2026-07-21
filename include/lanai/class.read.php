<?php

class ReadTotal {
	var $db;
	var $cfg;
	
	function __construct($cat,$id) {
		global $db,$cfg;
		$this->cfg=$cfg;
		$this->db=$db;
		//$this->db->debug=true;	
		if ($this->isReadItemExist($cat,$id)) {
			$this->updateReadItem($cat,$id);			
		} else {
			$this->addNewReadItem($cat,$id);
		}
			
	}
	
	function isReadItemExist($cat,$id){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."read 
					WHERE catTitle='".$cat."' AND redId=".$id;
		$rs=$this->db->execute($sql);
		if (($rs->recordcount())>0) {
			$result=true;
		} else {
			$result=false;
		}
		return $result;
	}
	
	function addNewReadItem($cat,$id){ 
		$sql="INSERT INTO ".$this->cfg['tablepre']."read 
					(catTitle,redId,redTotal) 
					VALUES ('".$cat."',".$id.",1)";
		$rs=$this->db->execute($sql);		
	}
	
	function updateReadItem($cat,$id){ 
		$sql="SELECT * FROM ".$this->cfg['tablepre']."read 
					WHERE catTitle='".$cat."' AND redId=".$id;
		$rs=$this->db->execute($sql);
		$num=($rs->fields['redTotal']+1);
		$sql="UPDATE  ".$this->cfg['tablepre']."read 
					SET redTotal=".$num." 
					WHERE catTitle='".$cat."' AND redId=".$id;
		$rs=$this->db->execute($sql);
	}
	
	function getReadTotal($cat,$id){ 
		$sql="SELECT * FROM ".$this->cfg['tablepre']."read 
					WHERE catTitle='".$cat."' AND redId=".$id;
		$rs=$this->db->execute($sql);
		$num=$rs->fields['redTotal'];
		return $num;
	}
	
}

?>