<?
	class Theme {
			
  	function getOffLineMessage(){
			ob_start();
				?>
					<h1>Tempolary Out of Services</h1>
				<?
			$s = ob_get_contents();
			ob_end_clean();
			return $s;
  	}
  	
  	function getLogoHeader() {
        if (!isset($s)) {
            $s = '';
        }
		return $s;
  	}
  	
  	function getFooter() {
			ob_start();
  	?>
		<? global $cfg_footer; ?>
		<?=$cfg_footer; ?> 		
		<? 
  			global $timer;
  			$timer->stop('main');
  			printf(_PAGE_EXECUTION." %s "._SEC,$timer->get_current('main'));
  		?>
  	<?
  			$s = ob_get_contents();
			ob_end_clean();
			return $s;
  	}
 		
  	function setBlock($position){
  		global $sys_lanai,$db,$cfg;
		$blcArray=array();	
		$blcArrayTitle=array();		
		$rsblock=$sys_lanai->getBlock($position);
  		while(!$rsblock->EOF) {	
			ob_start();
  			$case=$rsblock->fields['blcType'];
  			if ($case=='b') {
  				// insert template for block
				if (file_exists("blocks/".$rsblock->fields['blcName']."/index.php")) {
				    require("blocks/".$rsblock->fields['blcName']."/index.php");
				} 
  			} else if ($case=='c') {
  				// insert template for block
  			 	?><?=$rsblock->fields['blcContent']; ?><?
  			} else if ($case=='r'){
  				// rss feed
  				// insert template for block
  				if ((time()-$rs->fields['blcRssTime'])>$rs->fields['blcRssRefesh']) {
  				    $info=$sys_lanai->getRSSFeed($rsblock->fields['blcRssUrl']);
  					if (!empty($info['warning'])) {
  					   ?><?=$rs->fields['blcContent']; ?><?
  					} else {
  						$this->setBlock2Db($info,$rsblock->fields['blcId']);
  					}
  				} 
  				?><?=$rsblock->fields['blcContent']; ?><?
  			}  			

			$s = ob_get_contents();
			ob_end_clean();		
			array_push($blcArray,$s);
			array_push($blcArrayTitle,$rsblock->fields['blcTitle']);
			
			$rsblock->movenext();	
  			} // while 		
			return array($blcArray,$blcArrayTitle);
  	}
        function getBanners() {
            global $sys_lanai;

            $banners = array();
            $rs=$sys_lanai->getBanners();

            while (!$rs->EOF) {
                $banners[] = array(
                    'id' => $rs->fields['banId'],
                    'title' => $rs->fields['banTitle'],
                    'description' => $rs->fields['banDescription'],
                    'image' => $rs->fields['banImage'],
                    'url' => $rs->fields['banURL']
                );
                $rs->movenext();
            }

            return $banners;
        }

        function setBlock2Db($info,$blcId) {
  		global $sys_lanai,$db,$cfg;
  		$strx="<TABLE cellpadding=\"3\" cellspacing=\"1\"  width=\"100%\">";
					
  		foreach ($info['item'] as $item ) {
  			$strx.="<TR>";
  			$strx.="<TD class=\"\" >";
  			$strx.="<IMG SRC=\"theme/".$cfg['theme']."/images/html.gif\" BORDER=0 ALT=\"".$item['title']."\" align=\"absmiddle\">&nbsp;";
  			$strx.="<span class=\"txtContentTitleNormal\" >";
  			$strx.=$item['title'];
  			$strx.="</span>";
  			$strx.="</TD>";
  			$strx.="</TR>";
			$strx.="<TR>";
  			$strx.="<TD>";
  			$strx.=$item['description'];
  			$strx.="</TD>";
  			$strx.="</TR>";	
  			$strx.="<TR>";
  			$strx.="<TD align=\"right\">";
  			$strx.="<A HREF=\"".$item['link']."\" TARGET=\"_blank\">";	
  			$strx.=_MORE;
  			$strx.="</A>";
  			$strx.="</TD>";
  			$strx.="</TR>";
  			
  		}
  		$strx.="</TABLE>";
  		$strx=addslashes($strx);
  		$sql="UPDATE ".$cfg['tablepre']."block SET blcContent='$strx',blcRssTime=".time()." WHERE blcId=$blcId";
  		$db->Execute($sql);
  	}

  	function getModule($mname,$mfile=null) {
  		global $sys_lanai,$cfg;
		 ob_start();
	if (!empty($mname)) {
		 // load lang
		if (file_exists("modules/".$mname."/language/lang-".$cfg['lang'].".php")) {  								
				include_once("modules/".$mname."/language/lang-".$cfg['lang'].".php");
		} else {
				$sys_lanai->getErrorBox(_NO_LANG_FILE." : "."modules/".$mname."/language/lang-".$cfg['lang'].".php");
		}
		// load base class
		if (file_exists("modules/".$mname."/module.php")) {  								
				include_once("modules/".$mname."/module.php");
		} else {
				$sys_lanai->getErrorBox(_NO_BASE_CLASS_FILE." : "."modules/".$mname."/module.php");
		}
		// load module
		if (empty($mfile)) {
			// mod dir exist
			if (file_exists("modules/".$mname."/index.php"))
			{
				require_once("modules/".$mname."/index.php");
			} else {
				?><?=$sys_lanai->getErrorBox(_NO_MOD_FILE." : "."modules/".$mname."/index.php"); ?><?
			}
		} else {
			// mod dir exist ?
			if (file_exists("modules/".$mname."/".$mfile.".php"))
			{
				require_once("modules/".$mname."/".$mfile.".php");
			} else {
				?><?=$sys_lanai->getErrorBox(_NO_FILE." : "."modules/".$mname."/".$mfile.".php"); ?><?
			}
		}
	} else {
		$sys_lanai->getErrorBox(_NO_MOD_FILE);		
	}
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
  	}


  	function getSettingModule($mname,$mfile=null) {
  		global $sys_lanai,$cfg;
  		 ob_start();
  				if (!$sys_lanai->isUserLogin()) {
  				    $sys_lanai->getErrorBox(_REQUIRE_LOGIN);
  				} else {
	if (!empty($mname)) {
		 // load lang
		if (file_exists("modules/".$mname."/language/lang-".$cfg['lang'].".php")) {  								
				include_once("modules/".$mname."/language/lang-".$cfg['lang'].".php");
		} else {
				$sys_lanai->getErrorBox(_NO_LANG_FILE." : "."modules/".$mname."/language/lang-".$cfg['lang'].".php");
		}
		// load base class
		if (file_exists("modules/".$mname."/module.php")) {  								
				include_once("modules/".$mname."/module.php");
		} else {
				$sys_lanai->getErrorBox(_NO_BASE_CLASS_FILE." : "."modules/".$mname."/module.php");
		}
		// load module
		if (empty($mfile)) {
			// mod dir exist
			if (file_exists("modules/".$mname."/setting/index.php"))
			{
				require_once("modules/".$mname."/setting/index.php");
			} else {
				?><?=$sys_lanai->getErrorBox(_NO_MOD_FILE." : "."modules/".$mname."/setting/index.php"); ?><?
			}
		} else {
			// mod dir exist ?
			if (file_exists("modules/".$mname."/setting/".$mfile.".php"))
			{
				require_once("modules/".$mname."/setting/".$mfile.".php");
			} else {
				?><?=$sys_lanai->getErrorBox(_NO_FILE." : "."modules/".$mname."/setting/".$mfile.".php"); ?><?
			}
		}
	} else {
		$sys_lanai->getErrorBox(_NO_MOD_FILE);		
	}
  				}// check user login
  				$s = ob_get_contents();
				ob_end_clean();
				return $s;
  		}
	
	}
?>
