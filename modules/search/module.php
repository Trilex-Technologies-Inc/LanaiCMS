<?php
//require_once('include/adodb/adodb-active-record.inc.php');
global $db;


class LanaiSeach {
	
	function loadSchema() {
		return (file("modules/search/schema.syntax"));
	}
	
	function getSchema($item) {
		$scmarr=file("modules/search/schema.syntax");
		foreach ($scmarr as $val) {
			list($itemschema,$schama)=explode("#",$val,2);
			if ($itemschema==$item) {
				return $schama;
			}
		}
	}
	
}

class SearchPage extends Pager {
	var $item="news";
	
	function __construct ($db,$sql,$offset) {
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
        <?=_FOUND; ?>&nbsp;<?=$this->rs->recordcount(); ?>&nbsp;<?=_ITEMS; ?>
        <br><br>
         <table class="dataTable" cellpadding="0" cellspacing="0" width="100%">
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
			require("modules/search/".$this->item.".view.php");
			$this->rs->movenext();
		}
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
    

}

?>
