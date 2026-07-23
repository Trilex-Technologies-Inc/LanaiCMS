<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$blc_lanai=new Block();
	
	switch($_REQUEST['ac']){
	
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$mnu_lanai->setNewMenu($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				if ((empty($_REQUEST['blcTitle']) OR (trim($_REQUEST['blcTitle'])==""))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." "._BLOCK_TITLE." <a href=\"javascript:history.back();\">"._BACK2FILL."</a>");
				} else {
					switch($_REQUEST['m']){
						case 'c': 
							// add content							
							$blc_lanai->setNewBlock($_REQUEST['blcTitle'],strtolower($_REQUEST['blcTitle']),$_REQUEST['blcContent'],"",0,$_REQUEST['blcPosition'],$_REQUEST['m']);
							$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
							break;
						case 'b': 
							// add block file
							$blockname=substr($_FILES['userfile']['name'],0,(strlen($_FILES['userfile']['name'])-4));
							if ($blc_lanai->setBlockUpload($_REQUEST['userfile'])) {
							    $blc_lanai->setNewBlock($_REQUEST['blcTitle'],$blockname,"","",0,$_REQUEST['blcPosition'],$_REQUEST['m']);
								$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
							} else {
								?><?=$sys_lanai->getErrorBox(_BLOCK_CANNOT_UPLOAD); ?><?php
							}
							break;
						case 'r': 
							// add rss
							$blc_lanai->setNewBlock($_REQUEST['blcTitle'],$_REQUEST['blcTitle'],"",$_REQUEST['blcRssUrl'],$_REQUEST['blcRssRefesh'],$_REQUEST['blcPosition'],$_REQUEST['m']);
							$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
							break;
						case 'p': 
							// add extracted path
							if (file_exists("blocks".$sys_lanai->getPath().$_REQUEST['blcPath'])) {
							    $blc_lanai->setNewBlock($_REQUEST['blcTitle'],$_REQUEST['blcPath'],"","",0,$_REQUEST['blcPosition'],'b');
								$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
							} else {
								$sys_lanai->getErrorBox(_BLOCK_PATH_NOT_EXIST." <a href=\"javascript:history.back();\">"._BACK2FILL."</a>");
							}							
							break;
					} // switch
					
				}
			break;
		
		case "order" :
				$rs=$blc_lanai->getBlock();
				$arrId=array();
				$arrOrder=array();
				$i=0;
				while(!$rs->EOF){	
					$arrId[$i]=$rs->fields['blcId'];
					$arrOrder[$i]=$rs->fields['blcOrder'];
					if ($rs->fields['blcId']==$_REQUEST['mid']) {
					    $index=$i;
					}
					$i++;
					$rs->movenext();
				} // while
				if ($_REQUEST['v']=="dn") {
				    $tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index+1];
					$blc_lanai->setBlockOrder($arrId[$index],$arrOrder[$index+1]);
					$arrOrder[$index+1]=$tmp;
					$blc_lanai->setBlockOrder($arrId[$index+1],$tmp);
				} else {
				 	$tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index-1];
					$blc_lanai->setBlockOrder($arrId[$index],$arrOrder[$index-1]);
					$arrOrder[$index-1]=$tmp;
					$blc_lanai->setBlockOrder($arrId[$index-1],$tmp);
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
			
		case "active": 
				$blc_lanai->setBlockActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$blc_lanai->getBlockById($midarr[$i]);
					if ($rsdwn->fields['blcActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$blc_lanai->setBlockActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$blc_lanai->setDeleteBlock($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		case "mweight":
				
				$midarr=$_REQUEST['blcOrderId'];
				$valarr=$_REQUEST['blcOrder'];
				for ($i=0;$i<count($midarr);$i++) {
					$blc_lanai->setBlockOrder($midarr[$i],$valarr[$i]);			
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
			/*
		case "delete": 
				$blc_lanai->setDeleteBlock($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		*/
		case "edit": 
				//edit
				if ((empty($_REQUEST['blcTitle']) OR (trim($_REQUEST['blcTitle'])==""))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." "._BLOCK_TITLE." <a href=\"javascript:history.back();\">"._BACK2FILL."</a>");
				} else {
					switch($_REQUEST['m']){
						case 'c': 
							// edit content
							$blc_lanai->setEditBlock($_REQUEST['blcid'],$_REQUEST['blcTitle'],$_REQUEST['blcTitle'],$_REQUEST['blcContent'],"",0,$_REQUEST['blcPosition'],$_REQUEST['m']);
							break;
						case 'b': 
							// edit block
							$blc_lanai->setEditBlock($_REQUEST['blcid'],$_REQUEST['blcTitle'],$_REQUEST['blcName'],"","",0,$_REQUEST['blcPosition'],$_REQUEST['m']);
							break;
						case 'r': 
							// edit rss
							$blc_lanai->setEditBlock($_REQUEST['blcid'],$_REQUEST['blcTitle'],$_REQUEST['blcTitle'],"",$_REQUEST['blcRssUrl'],$_REQUEST['blcRssRefesh'],$_REQUEST['blcPosition'],$_REQUEST['m']);
							break;
					} // switch
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		
	} // switch
		
?>