<?php

require_once('include/adodb/adodb-active-record.inc.php');
global $db;

ADOdb_Active_Record::SetDatabaseAdapter($db);

/*	Class carousel	*/
class banner extends ADOdb_Active_Record {
    var $_table = 'tbl_ln_banner';

    function getPositionOptions() {
        return array(
            'l' => _BANN_POSITION_LEFT,
            'r' => _BANN_POSITION_RIGHT,
            'c' => _BANN_POSITION_CENTER,
            't' => _BANN_POSITION_TOP,
            'b' => _BANN_POSITION_BOTTOM
        );
    }

    function normalizePosition($position) {
        $positions = $this->getPositionOptions();
        return isset($positions[$position]) ? $position : 'l';
    }

    function getPositionLabel($position) {
        $positions = $this->getPositionOptions();
        return isset($positions[$position]) ? $positions[$position] : $positions['l'];
    }

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
        if ($id < 1) {
            return false;
        }

        // Delete directly. Loading an Active Record first is unnecessary and
        // can fail when a database driver changes the case of column names.
        $result = $db->Execute(
            "DELETE FROM " . $this->_table . " WHERE banId = ?",
            array($id)
        );

        return $result !== false;
    }
    function setBannerActive($id, $active) {
        global $db;

        $id = intval($id);
        $active = $active === 'n' ? 'n' : 'y';
        return $db->Execute(
            "UPDATE " . $this->_table .
            " SET banActive = " . $db->qstr($active) .
            " WHERE banId = " . $id
        ) !== false;
    }
    function saveBanner($data) {
        global $db;

        $id = intval($data['banId']);
        $banDate = !empty($data['banDate']) ? $data['banDate'] : date("Y-m-d H:i:s");
        $banPosition = $this->normalizePosition(isset($data['banPosition']) ? $data['banPosition'] : 'l');
        $banShow = isset($data['banShow']) ? intval($data['banShow']) : 0;
        $banClick = isset($data['banClick']) ? intval($data['banClick']) : 0;
        $banActive = isset($data['banActive']) && $data['banActive'] === 'n' ? 'n' : 'y';


        $sql = "
        UPDATE {$this->_table}
        SET 
            bantitle      = " . $db->qstr($data['banTitle']) . ",
            bandescription = " . $db->qstr($data['banDescription']) . ",
            banimage      = " . $db->qstr($data['banImage']) . ",
            banurl        = " . $db->qstr($data['banURL']) . ",
            banposition   = " . $db->qstr($banPosition) . ",
            bandate       = " . $db->qstr($banDate) . ",
            banshow       = " . $banShow . ",
            banclick      = " . $banClick . ",
            banactive     = " . $db->qstr($banActive) . "
        WHERE banId = $id
    ";

        $result = $db->Execute($sql);
        if ($result === false) {

            return false;
        }

        return true;


    }

    function createBanner($data) {
        global $db;

        $banDate = !empty($data['banDate']) ? $data['banDate'] : date("Y-m-d H:i:s");
        $banPosition = $this->normalizePosition(isset($data['banPosition']) ? $data['banPosition'] : 'l');
        $banShow = isset($data['banShow']) ? intval($data['banShow']) : 0;
        $banClick = isset($data['banClick']) ? intval($data['banClick']) : 0;
        $banActive = isset($data['banActive']) && $data['banActive'] === 'n' ? 'n' : 'y';

        $sql = "
        INSERT INTO {$this->_table}
            (banTitle, banDescription, banImage, banURL, banPosition, banDate, banShow, banClick, banActive)
        VALUES
            (" . $db->qstr($data['banTitle']) . ",
             " . $db->qstr($data['banDescription']) . ",
             " . $db->qstr($data['banImage']) . ",
             " . $db->qstr($data['banURL']) . ",
             " . $db->qstr($banPosition) . ",
             " . $db->qstr($banDate) . ",
             " . $banShow . ",
             " . $banClick . ",
             " . $db->qstr($banActive) . ")
        ";

        $result = $db->Execute($sql);
        return ($result !== false);
    }


}

class bannerPager extends Pager {

	function getPositionOptions() {
		return array(
			'l' => _BANN_POSITION_LEFT,
			'r' => _BANN_POSITION_RIGHT,
			'c' => _BANN_POSITION_CENTER,
			't' => _BANN_POSITION_TOP,
			'b' => _BANN_POSITION_BOTTOM
		);
	}

	function getPositionLabel($position) {
		$positions = $this->getPositionOptions();
		return isset($positions[$position]) ? $positions[$position] : $positions['l'];
	}
	
	function __construct($db,$sql,$offset) {
		parent::__construct($db, $sql, $offset);
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
			if (checkBoxes[i].type === 'checkbox' && checkBoxes[i].name === 'midId[]') {
				checkBoxes[i].checked = obj.checked;
			} 
		} 
	}
	function prepareCarouselAction(id, value) {
		var midField = document.getElementById('carousel-mid');
		var valueField = document.getElementById('carousel-v');
		if (midField) {
			midField.value = id;
		}
		if (valueField) {
			valueField.value = value;
		}
		return true;
	}
	</script> 
        <table class="dataTable" cellpadding="3" cellspacing="1">
		<tr class="dataRowHeader">
		<td class="dataColumnHeader"><input type="checkbox" value="select_all" onclick="selectall(this);" class="radioButton" /></td>
		<td class="dataColumnHeader" width="30%" align="center"><?=_BANN_TITLE; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_POSITION; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_ACTIVE; ?></td>
		<td class="dataColumnHeader" width="60%" align="center"><?=_BANN_DESCRIPTION; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_SHOW; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_CLICK; ?></td>
		<td class="dataColumnHeader" align="center"><?=_BANN_EDIT; ?></td>
        	</tr>
        <?php
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
           <input type="checkbox" name="midId[]" value="<?=$this->rs->fields['banId']; ?>" >
           </td>
           <td class="dataColumn"><?=$this->rs->fields['banTitle']; ?></td>
           <td class="dataColumn" align="center"><?=$this->getPositionLabel($this->rs->fields['banPosition']); ?></td>
           <td class="dataColumn" align="center">
           <?php if ($this->rs->fields['banActive'] === 'n') { ?>
               <button type="submit" name="ac" value="active" onclick="return prepareCarouselAction('<?=$this->rs->fields['banId']; ?>', 'y');" style="border:0;background:none;padding:0;cursor:pointer;">
                   <img src="theme/<?=$cfg['theme']; ?>/images/cancel.gif" border="0" align="absmiddle" alt="<?=_NO; ?>" title="<?=_NO; ?>">
               </button>
           <?php } else { ?>
               <button type="submit" name="ac" value="active" onclick="return prepareCarouselAction('<?=$this->rs->fields['banId']; ?>', 'n');" style="border:0;background:none;padding:0;cursor:pointer;">
                   <img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle" alt="<?=_YES; ?>" title="<?=_YES; ?>">
               </button>
           <?php } ?>
           </td>
           <td class="dataColumn"><?=$this->rs->fields['banDescription']; ?></td>
           <td class="dataColumn" align="center">
           <?php 
           		if($this->rs->fields['banShow']==null) {
           			echo "0";
           		} else {
           			echo $this->rs->fields['banShow'];
           		}
           ?>
           </td>
           <td class="dataColumn" align="center">
          <?php 
           		if($this->rs->fields['banClick']==null) {
           			echo "0";
           		}else {
           			echo $this->rs->fields['banClick'];
           		}
           ?>
           <td class="dataColumn" align="center">
          <a href="setting.php?modname=carousel&mf=edit&id=<?=$this->rs->fields['banId']; ?>" ><img src="theme/<?=$cfg['theme']; ?>/images/edit.gif" border="0" align="absmiddle"/></a>
           </tr>
           <?php
          $this->rs->movenext();
      }
      $s = ob_get_contents();
      ob_end_clean();
      return $s;
    }
    
} // class
?>
