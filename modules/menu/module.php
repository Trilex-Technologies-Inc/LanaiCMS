<?php
	include_once("class.MenuPager.php");
	
	/**
	 * Menu
	 * 
	 * @package 
	 * @author Administrator
	 * @copyright Copyright (c) 2006
	 * @version $Id: module.php,v 1.1 2007/03/23 12:37:36 redlinesoft Exp $
	 * @access public
	 **/
	class Menu {
		
		var $uid;
		var $db;
		var $cfg;
		var $_sql;		
		
		
		
		function __construct() {
			global $db,$cfg;
			$this->db=$db;
			$this->cfg=$cfg;
			$this->uid=$_SESSION['uid'];
			//$this->db->debug=true;		
		}
		
		function getMenu(){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."menu 
					ORDER BY mnuOrder ASC";
			$this->_sql=$sql;			
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function getMenuById($mid){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."menu 
					WHERE mnuId=$mid";
			$this->_sql=$sql;			
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function getMenuList($rows=30){
			$this->getMenu();
			$pager=new MenuPager($this->db,$this->_sql,true);
			$pager->Render($rows);
		}

		function getMaxOrderValue(){
			$sql="SELECT MAX(mnuOrder) FROM ".$this->cfg['tablepre']."menu ";
			$rs=$this->db->execute($sql);	
			return ($rs->fields[0]);
		}
		
		function setEditMenu($mnuid,$mnuParentId,$mnuTitle,$mnuUrl,$mnuTarget,$conId,$modId,$mnuType='l'){
			$sql="UPDATE ".$this->cfg['tablepre']."menu SET
					mnuTitle='".$mnuTitle."',mnuParentId=".$mnuParentId.",mnuUrl='".$mnuUrl."',mnuTarget='".$mnuTarget."',
					conId=".$conId.",modId=".$modId.",mnuType='".$mnuType."'
					WHERE mnuId=$mnuid";
			$rs=$this->db->execute($sql);
			return $rs;
		}
		
		function setNewMenu($mnuTitle,$mnuParentId,$mnuUrl,$mnuTarget,$conId,$modId,$mnuType='l') {
			$sql="INSERT INTO ".$this->cfg['tablepre']."menu 
					(mnuParentId,mnuTitle,mnuUrl,mnuTarget,conId,modId,mnuType,mnuActive,mnuOrder) 
					VALUES (".$mnuParentId.",'".$mnuTitle."','".$mnuUrl."','".$mnuTarget."',".$conId.",".$modId.",'".$mnuType."','y',".($this->getMaxOrderValue()+1).")";
			$rs=$this->db->execute($sql);
			return $rs;
		}
		
		function setDeleteMenu($mid){
			$sql="DELETE FROM ".$this->cfg['tablepre']."menu
					WHERE mnuId=".$mid;
			$rs=$this->db->execute($sql);
			return $rs;
		}
		
		function setMnuOrder($mid,$order){
			$sql="UPDATE ".$this->cfg['tablepre']."menu
					SET mnuOrder=$order
					WHERE mnuId=".$mid;
			$rs=$this->db->execute($sql);
			return $rs;
		}
		
		
		function setMenuActive($mid,$value){
			$sql="UPDATE ".$this->cfg['tablepre']."menu
					SET mnuActive='".$value."'
					WHERE mnuId=".$mid;
			$rs=$this->db->execute($sql);
			return $rs;
		}
		
		// target combo 
		function getTargetCombo($name,$value){
			if ($value=="") {
			    $none="selected";
			} else if ($value=="_blank") {
			    $blank="selected";
			} else if ($value=="_parent") {
			    $parent="selected";
			} else if ($value=="_self") {
			    $self="selected";
			} else if ($value=="_top") {
			    $top="selected";
			}
		?>
		<select name="<?=$name; ?>" style="width:150px">
			<option value="" <?=$none;?> ><?=_NONE; ?></option>
			<option value="_blank" <?=$blank;?> >_blank</option>
			<option value="_parent" <?=$parent;?> >_parent</option>
			<option value="_self" <?=$self;?> >_self</option>
			<option value="_top" <?=$top;?> >_top</option>
		</select>
		<?php
		}
		
		// menu combo		
		function getMenuCombo($name,$value){
			$rs=$this->getMenu();
			?>
			<select name="<?=$name; ?>" style="width:150px">
				<option value="0"><?=_NONE; ?></option>
			<?php
			while(!$rs->EOF){
				if ($value==$rs->fields['mnuParentId']) {
				    $select="selected";
				} else {
					$select="";
				}
				?><option value="<?=$rs->fileds['mnuId']; ?>" <?=$select; ?>><?=$rs->fields['mnuTitle']; ?></option><?php
				$rs->movenext();
			} // while
			?></select><?php
		}
		
		// module combo
		
		function getMenuParentCombo($name,$value){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."menu 
					WHERE mnuParentId=0 
					ORDER BY mnuOrder ASC";
			$rs=$this->db->execute($sql);	
			?>
			<select name="<?=$name; ?>" style="width:150px">
				<option value="0"><?=_NONE; ?></option>
			<?php
			while(!$rs->EOF){
				if ($rs->fields['mnuId']==$value) {
				    $select="selected";
				} else {
					$select="";
				}
				?><option value="<?=$rs->fields['mnuId']; ?>" <?=$select; ?>><?=$rs->fields['mnuTitle']; ?></option><?php
				$rs->movenext();
			} // while
			?></select><?php
		}
		
		// module combo
		function getModuleCombo($name,$value){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."module 
					WHERE modActive='y'
					ORDER By modTitle ASC";
			$rs=$this->db->execute($sql);	
			?><select name="<?=$name; ?>" style="width:150px"><?php
			while(!$rs->EOF){
				if ($rs->fields['modId']==$value) {
				    $select="selected";
				} else {
					$select="";
				}
				?><option value="<?=$rs->fields['modId']; ?>" <?=$select; ?>><?=$rs->fields['modTitle']; ?></option><?php
				$rs->movenext();
			} // while
			?></select><?php
		}
		
		// content combo
		function getContentCombo($name,$value){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."content 
					WHERE conActive='y'";
			$rs=$this->db->execute($sql);	
			?><select name="<?=$name; ?>" style="width:150px"><?php
			while(!$rs->EOF){
				if ($rs->fields['conId']==$value) {
				    $select="selected";
				} else {
					$select="";
				}
				?><option value="<?=$rs->fields['conId']; ?>" <?=$select; ?>><?=$rs->fields['conTitle']; ?></option><?php
				$rs->movenext();
			} // while
			?></select><?php
		}
		
		
	}

?>
