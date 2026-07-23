<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$mnu_lanai=new Menu();
	
	switch($_REQUEST['ac']){
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$mnu_lanai->setNewMenu($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				if ((empty($_REQUEST['mnuTitle']) OR (trim($_REQUEST['mnuTitle'])==""))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." "._MENU_TITLE." <a href=\"javascript:history.back();\">"._BACK2FILL."</a>");
				} else {
					switch($_REQUEST['m']){
						case 'c': 
							// add content
							$mnu_lanai->setNewMenu($_REQUEST['mnuTitle'],$_REQUEST['mnuParentId'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],$_REQUEST['conId'],0,$_REQUEST['m']);
							break;
						case 'm': 
							// add module
							$mnu_lanai->setNewMenu($_REQUEST['mnuTitle'],$_REQUEST['mnuParentId'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],0,$_REQUEST['modId'],$_REQUEST['m']);
							break;
						case 'l': 
							// add link
							$mnu_lanai->setNewMenu($_REQUEST['mnuTitle'],$_REQUEST['mnuParentId'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],0,0,$_REQUEST['m']);
							break;
					} // switch
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		case "order" :
				$rs=$mnu_lanai->getMenu();
				$arrId=array();
				$arrOrder=array();
				$i=0;
				while(!$rs->EOF){	
					$arrId[$i]=$rs->fields['mnuId'];
					$arrOrder[$i]=$rs->fields['mnuOrder'];
					if ($rs->fields['mnuId']==$_REQUEST['mid']) {
					    $index=$i;
					}
					$i++;
					$rs->movenext();
				} // while
				if ($_REQUEST['v']=="dn") {
				    $tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index+1];
					$mnu_lanai->setMnuOrder($arrId[$index],$arrOrder[$index+1]);
					$arrOrder[$index+1]=$tmp;
					$mnu_lanai->setMnuOrder($arrId[$index+1],$tmp);
				} else {
				 	$tmp=$arrOrder[$index];
					$arrOrder[$index]=$arrOrder[$index-1];
					$mnu_lanai->setMnuOrder($arrId[$index],$arrOrder[$index-1]);
					$arrOrder[$index-1]=$tmp;
					$mnu_lanai->setMnuOrder($arrId[$index-1],$tmp);
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "active": 
				$mnu_lanai->setMenuActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$mnu_lanai->getMenuById($midarr[$i]);
					if ($rsdwn->fields['mnuActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$mnu_lanai->setMenuActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$mnu_lanai->setDeleteMenu($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		case "mweight":				
				$midarr=$_REQUEST['mnuOrderId'];
				$valarr=$_REQUEST['mnuOrder'];
				for ($i=0;$i<count($midarr);$i++) {
					$mnu_lanai->setMnuOrder($midarr[$i],$valarr[$i]);			
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		/*
		case "delete": 
				$mnu_lanai->setDeleteMenu($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		*/
		case "edit": 
				//edit
				if ((empty($_REQUEST['mnuTitle']) OR (trim($_REQUEST['mnuTitle'])==""))) {
				     $sys_lanai->getErrorBox(_REQUIRE_FIELDS." "._MENU_TITLE." <a href=\"javascript:history.back();\">"._BACK2FILL."</a>");
				} else {
					switch($_REQUEST['m']){
						case 'c': 
							// add content
							$mnu_lanai->setEditMenu($_REQUEST['mnid'],$_REQUEST['mnuParentId'],$_REQUEST['mnuTitle'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],$_REQUEST['conId'],0,$_REQUEST['m']);
							break;
						case 'm': 
							// add module
							$mnu_lanai->setEditMenu($_REQUEST['mnid'],$_REQUEST['mnuParentId'],$_REQUEST['mnuTitle'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],0,$_REQUEST['modId'],$_REQUEST['m']);
							break;
						case 'l': 
							// add link
							$mnu_lanai->setEditMenu($_REQUEST['mnid'],$_REQUEST['mnuParentId'],$_REQUEST['mnuTitle'],$_REQUEST['mnuUrl'],$_REQUEST['mnuTarget'],0,0,$_REQUEST['m']);
							break;
					} // switch
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		
	} // switch
		
?>