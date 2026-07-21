<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$lanai_rss=new LRSSThai();
	
	switch($_REQUEST['ac']){
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$mnu_lanai->setNewMenu($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				if ((empty($_REQUEST['rssTitle'])) OR (empty($_REQUEST['rssURL']))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$lanai_rss->setNewRSS($_REQUEST['rssTitle'],$_REQUEST['rssURL'],$_REQUEST['rssReload'],$_REQUEST['rssView'],$_REQUEST['rssItemCount'],$_REQUEST['rssShowDescription'],$_REQUEST['rssNumColumn'],$_REQUEST['rssNumImage'],$_REQUEST['rssFixedImage'],$_REQUEST['rssAlterImage'],$_REQUEST['rssImageWidth'],$_REQUEST['rssImageHeight'],$_REQUEST['rssImageAlign'],$_REQUEST['rssTarget']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		case "order" :
				
				$rs=$lanai_rss->getRSS();
				$arrId=array();
				$arrOrder=array();
				$i=0;
				while(!$rs->EOF){	
					$arrId[$i]=$rs->fields['rssId'];
					$arrOrder[$i]=$rs->fields['rssOrder'];
					if ($rs->fields['rssId']==$_REQUEST['mid']) {
					    $index=$i;
					}
					$i++;
					$rs->movenext();
				} // while
				if ($_REQUEST['v']=="dn") {
				    $tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index+1];
					$lanai_rss->setRSSOrder($arrId[$index],$arrOrder[$index+1]);
					$arrOrder[$index+1]=$tmp;
					$lanai_rss->setRSSOrder($arrId[$index+1],$tmp);
				} else {
				 	$tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index-1];
					$lanai_rss->setRSSOrder($arrId[$index],$arrOrder[$index-1]);
					$arrOrder[$index-1]=$tmp;
					$lanai_rss->setRSSOrder($arrId[$index-1],$tmp);
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		case "active": 
				$lanai_rss->setRSSActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$lanai_rss->getRSSById($midarr[$i]);
					if ($rsdwn->fields['rssActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$lanai_rss->setRSSActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$lanai_rss->setDeleteRSS($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		case "mweight":
				
				$midarr=$_REQUEST['rssOrderId'];
				$valarr=$_REQUEST['rssOrder'];
				for ($i=0;$i<count($midarr);$i++) {
					$lanai_rss->setRSSOrder($midarr[$i],$valarr[$i]);			
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		/*
		case "delete": 
				$lanai_rss->setDeleteRSS($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		*/
		case "edit": 
				//edit
				if ((empty($_REQUEST['rssTitle'])) OR (empty($_REQUEST['rssURL']))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$lanai_rss->setEditRSS($_REQUEST['mid'],$_REQUEST['rssTitle'],$_REQUEST['rssURL'],$_REQUEST['rssReload'],$_REQUEST['rssView'],$_REQUEST['rssItemCount'],$_REQUEST['rssShowDescription'],$_REQUEST['rssNumColumn'],$_REQUEST['rssNumImage'],$_REQUEST['rssFixedImage'],$_REQUEST['rssAlterImage'],$_REQUEST['rssImageWidth'],$_REQUEST['rssImageHeight'],$_REQUEST['rssImageAlign'],$_REQUEST['rssTarget']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		
	} // switch
		
?>