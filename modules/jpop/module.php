<?php

require_once('include/adodb/adodb-active-record.inc.php');
global $db;

ADOdb_Active_Record::SetDatabaseAdapter($db);

/*	Class JpopX	*/
class JpopX {
	var $_table = 'tbl_ln_jpop';
	function getPOP() {
		global $db;
		$sql="SELECT * FROM  ".$this->_table." WHERE popActive='y' ";
		$xrs=$db->execute($sql);
		return $xrs;
	}
} 

/*	Class Jpop	*/
class Jpop extends ADOdb_Active_Record{
	var $_table = 'tbl_ln_jpop';
	
	function popDisableAll() {
		global $db;
		$sql="UPDATE ".$this->_table." SET popActive='n' ";					
		$xrs=$db->execute($sql);
		return $xrs;
	}
		
	function popActive($id,$v) {
		global $db;
		$sql="UPDATE ".$this->_table." SET popActive='".$v."' WHERE popId=".$id;					
		$xrs=$db->execute($sql);
		return $xrs;
	}
	
	
	
}

/*	Class JPOPSettingPager	*/
class JPOPSettingPager extends Pager {
	
	/* render grid header */
	function renderGridHeader() {
	    ob_start();
	    ?>
	    <table class="dataTable" >
	    <?php
	    $s = ob_get_contents();
	    ob_end_clean();
	    return $s;
	}
	
	/* reder page  */
	function renderGrid() {
		ob_start();
		while (!$this->rs->EOF){
			?>
			<tr>
			<?php
				for ($i=0;$i<2;$i++) {
					if (!empty($this->rs->fields['popTitle'])) {
			?>
			<td width="350px">
			 <table width="100%"  border="0" cellpadding="3" cellspacing="2" bgcolor="<?=$this->rs->fields['popBgBorder']; ?>">
			    <tr bgcolor="<?=$this->rs->fields['popBgTitle']; ?>">
			      <td width="100%"><span style="color: #FFFFFF; text-decoration:none;" ><?=stripslashes($this->rs->fields['popTitle']); ?></span></td>
			      <td><a href="#" style="font-family: Arial; color: #FFFFFF; text-decoration:none;">x</a></td>
			    </tr>
			    <tr valign="top" bgcolor="<?=$this->rs->fields['popBgDes']; ?>">
			      <td colspan="2" ><?=stripslashes($this->rs->fields['popDescription']); ?></td>
			    </tr>
			  </table>
			</td>
			<td valign="top" width="150">
			&nbsp;&nbsp;<a href="setting.php?modname=jpop&mf=popedi&i=<?=$this->rs->fields['popId']; ?>"><?=_EDIT; ?></a><br>
			&nbsp;&nbsp;<a href="setting.php?modname=jpop&mf=popdel&i=<?=$this->rs->fields['popId']; ?>"><?=_DELETE; ?></a><br>
			<?php
				if ($this->rs->fields['popActive']=='y') {
			?>
			&nbsp;&nbsp;<a href="setting.php?modname=jpop&mf=popactive&v=n&i=<?=$this->rs->fields['popId']; ?>"><?=_ACTIVED; ?></a><br>
			<?php
				} else {
			?>
			&nbsp;&nbsp;<a href="setting.php?modname=jpop&mf=popactive&v=y&i=<?=$this->rs->fields['popId']; ?>"><?=_DEACTIVED; ?></a><br>
			<?php	
				}
			?>
			</td>
			<?php
					$this->rs->movenext();
					} // if
				} // for
				
			?>
			</tr>
			<?php
			
		}
		
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	
	
}
?>