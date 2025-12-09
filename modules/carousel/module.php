<?php

require_once('include/adodb/adodb-active-record.inc.php');
global $db;

ADOdb_Active_Record::SetDatabaseAdapter($db);

/*	Class carousel	*/
class banner extends ADOdb_Active_Record {
    var $_table = 'tbl_ln_banner';

    function getMax(){
        global $db;
        $sql="SELECT MAX(banId) FROM ".$this->_table;
        $rs=$db->execute($sql);
        return ($rs->fields[0]);
    }
    function getMin(){
        global $db;
        $sql="SELECT MIN(banId) FROM ".$this->_table;
        $rs=$db->execute($sql);
        return ($rs->fields[0]);
    }
    function randomeBann() {
        $max=$this->getMax();
        $min=$this->getMin();
        $value=rand($min,$max);
        $rs=$this->Load("banId=".$value);
        if ($rs) {
            $x=array($value,$this->bantitle,$this->banimage);
            $show=($this->banshow)+1;
            $this->banshow=$show;
            $this->save();
            return $x;
        }
    }
    function deleteBanner($id) {
        global $db;

        $id = intval($id);

        $exists = $this->Load("banId = $id");
        if (!$exists) {
            return false;
        }

        $sql = "DELETE FROM " . $this->_table . " WHERE banId = $id";
        $result = $db->Execute($sql);

        if ($result) {
            return true;
        }

        return false;
    }
    function saveBanner($data) {
        global $db;

        $id = intval($data['banId']);
        $banDate = !empty($data['banDate']) ? $data['banDate'] : date("Y-m-d H:i:s");


        $sql = "
        UPDATE {$this->_table}
        SET 
            bantitle      = " . $db->qstr($data['banTitle']) . ",
            bandescription = " . $db->qstr($data['banDescription']) . ",
            banimage      = " . $db->qstr($data['banImage']) . ",
            banurl        = " . $db->qstr($data['banURL']) . ",
            bandate       = " . $db->qstr($banDate) . ",
            banshow       = " . intval($data['banShow']) . ",
            banclick      = " . intval($data['banClick']) . "
        WHERE banId = $id
    ";

        $result = $db->Execute($sql);
        if ($result === false) {

            return false;
        }

        return true;


    }


}

class bannerPager extends Pager {
	
	function bannerPager($db,$sql,$offset) {
		Pager::Pager($db,$sql,$offset);
		$this->pageStr=_PAGE;
		$this->nextStr=_NEXT;
		$this->prevStr=_PREV;
		$this->firstStr=_FIRST;
		$this->lastStr=_LAST;
	}
	
    /* render grid header */
    function renderGridHeader() {
        ob_start();
        ?>
	<script language="javascript" type="text/javascript"> 
	function selectall(obj) { 
		var checkBoxes = document.getElementsByTagName('input'); 
		for (i = 0; i < checkBoxes.length; i++) { 
			if (obj.checked == true) { 
				checkBoxes[i].checked = true; // this checks all the boxes 
			} else { 
				checkBoxes[i].checked = false; // this unchecks all the boxes 
			} 
		} 
	} 	
	</script> 
        <table class="dataTable" cellpadding="3" cellspacing="1">
		<tr class="dataRowHeader">
		<td class="dataColumnHeader"><input type="checkbox" value="select_all" onclick="selectall(this);" class="radioButton" /></td>
		<td class="dataColumnHeader" width="30%" align="center"><?=_BANN_TITLE; ?></td>
		<td class="dataColumnHeader" width="60%" align="center"><?=_BANN_DESCRIPTION; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_SHOW; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_CLICK; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_EDIT; ?></td>
        	</tr>
        <?
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }
    
     /* reder page  */
    function renderGrid() {
    	global $cfg;
      ob_start();
      while (!$this->rs->EOF){
      	?>
           <tr class="dataRow">
           <td class="dataColumn">
           <input type="checkbox"  name="midId[]" value="<?=$this->rs->fields['banId']; ?>" >
           <input type="hidden" name="banId[]" value="<?=$this->rs->fields['banId']; ?>" >
           </td>
           <td class="dataColumn"><?=$this->rs->fields['banTitle']; ?></td>
           <td class="dataColumn"><?=$this->rs->fields['banDescription']; ?></td>
           <td class="dataColumn" align="center">
           <? 
           		if($this->rs->fields['banShow']==null) {
           			echo "0";
           		} else {
           			echo $this->rs->fields['banShow'];
           		}
           ?>
           </td>
           <td class="dataColumn" align="center">
          <? 
           		if($this->rs->fields['banClick']==null) {
           			echo "0";
           		}else {
           			echo $this->rs->fields['banClick'];
           		}
           ?>
           <td class="dataColumn" align="center">
          <a href="setting.php?modname=carousel&mf=edit&id=<?=$this->rs->fields['banId']; ?>" ><img src="theme/<?=$cfg['theme']; ?>/images/edit.gif" border="0" align="absmiddle"/></a>
           </tr>
           <?
          $this->rs->movenext();
      }
      $s = ob_get_contents();
      ob_end_clean();
      return $s;
    }
    
} // class
?>