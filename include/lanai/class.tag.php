<?php
/**
 * Class SYSTag is tag Manipulate class for Lanai Core 6  
 * 
 * How to add tag
 * ----------------------
 * 1. get your item number form any table that you want to add tag. 
 * 2. parse tag variable from http request into array by using method parseTag()  eg : $arrTag=parseTag($_REQUEST['txtTag');
 * 3. add tag to table tag using method addTag() eg : addTag($arrTags,"news",12);
 * 
 * CREATE TABLE tbl_ln_item_tag (
 * tagId int(10) unsigned NOT NULL,  
 * itmId int(10) unsigned NOT NULL,
 * itmType varchar(20) NOT NULL,
 * KEY tbl_ln_item_tag_FKIndex1 (tagId)
 * ) TYPE=MyISAM;
 * 
 * CREATE TABLE tbl_ln_tag (
 *   tagId int(10) unsigned NOT NULL auto_increment,
 *   tagWord varchar(40) NOT NULL,
 *   PRIMARY KEY  (tagId)
 * ) TYPE=MyISAM;
 * 
 */

class SYSTag {
	
	var $db;
	var $cfg;
	
	/**
	 * Constructor
	 *
	 * @return SYSTag
	 */
	function __construct () {
		global $cfg,$db;
		$this->db=$db;
		$this->cfg=$cfg;
		$this->db->debug=true;		
	}
	
	/**
	 * Split variable of tag to array
	 *
	 * @param unknown_type $tags
	 * @return unknown
	 */
	function parseTag($tags) {
		$arrTag=array();
		$arrTag=explode(",",$tags);	
		return 	$arrTag;
	}
	
	/**
	 * Check tag word exist
	 *
	 * @param unknown_type $tag
	 * @return unknown
	 */
	function existTag($tag) {
		$sql="SELECT COUNT(*) FROM ".$this->cfg['tablepre']."tag 
				WHERE tagWord LIKE '".$tag."' ";
		$rs=$this->db->execute($sql);
		if ($rs->fields[0]>0) {
			$result=true;
		} else {
			$result=false;
		}
		return $result;
	}
	
	/**
	 * add new tag word
	 *
	 * @param unknown_type $tag
	 * @return unknown
	 */
	function addNewTag($tag){
		$sql="INSERT INTO ".$this->cfg['tablepre']."tag  (tagWord) 
					VALUES ('".$tag."')";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	/**
	 * Get Tag Id
	 *
	 * @param unknown_type $tag
	 * @return unknown
	 */
	function getTagId($tag) {
		$sql="SELECT * FROM ".$this->cfg['tablepre']."tag 
					WHERE tagWord LIKE '".$tag."' ";
		$rs=$this->db->execute($sql);
		return ($rs->fields['tagId']);
	}
	
	/**
	 * add tag item
	 *
	 * @param unknown_type $tid
	 * @param unknown_type $ittype
	 * @param unknown_type $itid
	 * @return unknown
	 */
	function addTagItem($tid,$ittype,$itid){
		$sql="INSERT INTO ".$this->cfg['tablepre']."item_tag 
					(tagId, itmId, itmType) 
					VALUES ($tid,$itid,'".$ittype."')";
		$rs=$this->db->execute($sql);
		return $rs;
	}
		
	/**
	 * Add tags to database
	 *
	 * @param unknown_type $arrTags
	 * @param unknown_type $itype
	 */
	function addTag($arrTags,$itype,$itid) {
		foreach ($arrTags as $item) {
			$item=trim($item);
			if (!empty($item)) { 
				if (!$this->existTag($tag)) {
					// add new tag
					$this->addNewTag($tag);
					$tid=$this->getTagId($tag);
					$this->addTagItem($tid,$itype,$itid);
				}  else {
					// add new item use exist word
					$tid=$this->getTagId($tag);
					$this->addTagItem($tid,$itype,$itid);
				}
			}// empty
		}		
	}
	
	function makeTagCloud($itype) {
		$sql="SELECT t.tagWord,COUNT(*) as Total
					FROM ".$this->cfg['tablepre']."item_tag i,".$this->cfg['tablepre']."tag t 
					WHERE i.tagId=t.tagId AND i.itmType LIKE '".$itype."'
					GROUP BY t.tagId
					ORDER BY Total";
		$rs=$this->db->execute($sql);
		$rows=$rs->recordcount();
		ob_start();
		while (!$rs->EOF) {
			?><span style="font-size:<?=$this->getScale($rs->fields['Total'])?>;"><?=$rs->fields['tagWord']; ?></span> <?php
			$rs->movenext();
		}
		$tCloud = ob_get_contents();
		ob_end_clean();
		return $tCloud;
	}
	
	function getScale($total){
		$scale=4;
		$ascale=round(($total/$scale));
		if ($ascale>4) {
			$size="+6";
		} elseif ($ascale<=4) {
			$size="+4";
		} elseif ($ascale<=2) {
			$size="+2";
		} elseif ($ascale<=1) {
			$size="+1";
		}
		return $size;
	}
	
	function getTagItemId($itid,$itype){
		$sql="SELECT * 
					FROM ".$this->cfg['tablepre']."item_tag i,".$this->cfg['tablepre']."tag t  
					WHERE  i.tagId=t.tagId AND 
								i.itmType LIKE '".$itype."' AND
								i.itmId=$itid";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
}

?>