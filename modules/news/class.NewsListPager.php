<?php
  
/**
	 * NewsListPager
	 * 
	 * @package 
	 * @author Administrator
	 * @copyright Copyright (c) 2006
	 * @version $Id: class.NewsListPager.php,v 1.1 2007/03/23 12:37:36 redlinesoft Exp $
	 * @access public
	 **/
	class NewsListPager extends NewsPager {
	
		function __construct(&$db,$sql,$id = 'adodb', $showPageLinks = false){
			parent::__construct($db, $sql, $id, $showPageLinks);
			$this->page=_PAGE;
		}
		
		
		//--------------------------------------------------------
		// Simply rendering of grid. You should override this for
		// better control over the format of the grid
		//
		// We use output buffering to keep code clean and readable.
		function RenderGrid()
		{
			//global $gSQLBlockRows; // used by rs2html to indicate how many rows to display
			//include_once(ADODB_DIR.'/tohtml.inc.php');
			ob_start();
			$gSQLBlockRows = $this->rows;
			//rs2html($this->rs,$this->gridAttributes,$this->gridHeader,$this->htmlSpecialChars);
			$mod_lanai=new News();
			?>
			<table cellpadding="3" cellspacing="1" width="100%">			
			<?php
			while(!$this->rs->EOF){
			?>
			<tr>
				<td>
					<img src="theme/<?=$mod_lanai->cfg['theme'];?>/images/file.gif" border="0" align="absmiddle">
					<?php
							global $sys_lanai;
							$link=$sys_lanai->getSEOLink($_SERVER['PHP_SELF']."?modname=news&mf=nwsview&cid=".$this->rs->fields['nwsId']);	
					?>
					<a href="<?=$link; ?>"><?=$this->rs->fields['nwsTitle']; ?></a>
					- <span class="txtDateTime"><?=adodb_date2("l,d F Y",$this->rs->fields['nwsCreate'])?></span>
				</td>
			</tr>
			<?php
				$this->rs->movenext();
			} // while{
			?></table><?php
			$s = ob_get_contents();
			ob_end_clean();
			return $s;
		}
		
	}
  
?>
