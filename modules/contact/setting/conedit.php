<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
 
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$contact=new Contact();
	
	switch($_REQUEST['ac']){
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$mnu_lanai->setNewMenu($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				if (empty($_REQUEST['conFname']) OR empty($_REQUEST['conLname']) OR empty($_REQUEST['conEmail'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$contact->setNewContact($_REQUEST['conFname'],$_REQUEST['conLname'],$_REQUEST['conPosition'],$_REQUEST['conAddress1'],$_REQUEST['conAddress2'],$_REQUEST['conCity'],$_REQUEST['conState'],$_REQUEST['cntId'],$_REQUEST['conZipcode'],$_REQUEST['conPhone'],$_REQUEST['conFax'],$_REQUEST['conMobile'],$_REQUEST['conEmail'],$_REQUEST['conURL']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		case "active": 
				$contact->setContactActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$contact->getContactById($midarr[$i]);
					if ($rsdwn->fields['conActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$contact->setContactActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$contact->setDeleteContact($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		/*
		case "delete": 
				$contact->setDeleteContact($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		*/
		case "edit": 
				//edit
				$contact->setUpdateContact($_REQUEST['conId'],$_REQUEST['conFname'],$_REQUEST['conLname'],$_REQUEST['conPosition'],$_REQUEST['conAddress1'],$_REQUEST['conAddress2'],$_REQUEST['conCity'],$_REQUEST['conState'],$_REQUEST['cntId'],$_REQUEST['conZipcode'],$_REQUEST['conPhone'],$_REQUEST['conFax'],$_REQUEST['conMobile'],$_REQUEST['conEmail'],$_REQUEST['conURL']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		
	} // switch
		
?>