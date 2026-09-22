<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$content=new Content();
	
	switch($_REQUEST['ac']){
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$mnu_lanai->setNewMenu($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				if (!$sys_lanai->validateCsrfToken('content', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				if (empty($_REQUEST['conTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$content->setNewContent($_REQUEST['conTitle'],$_REQUEST['conBody1'],$_REQUEST['conBody2']);
					$id=$content->getContentIdByTitle($_REQUEST['conTitle']);
					if (($id>0) AND ($_REQUEST['conMenu']=="yes")) {
						$content->setContentMenu($id,$_REQUEST['conTitle']);
					}
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		case "active": 
				if (!$sys_lanai->validateCsrfToken('content', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$rsown=$content->getContentById($_REQUEST['mid']);
				if ($rsown->recordcount()<1 || !$sys_lanai->userCanActOnContent($rsown->fields['userId'],'publish_content')) {
					$sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION);
					break;
				}
				$content->setContentActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				if (!$sys_lanai->validateCsrfToken('content', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$content->getContentById($midarr[$i]);
					if ($rsdwn->recordcount()<1 || !$sys_lanai->userCanActOnContent($rsdwn->fields['userId'],'publish_content')) {
						continue;
					}
					if ($rsdwn->fields['conActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$content->setContentActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				if (!$sys_lanai->validateCsrfToken('content', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdel=$content->getContentById($midarr[$i]);
					if ($rsdel->recordcount()<1 || !$sys_lanai->userCanActOnContent($rsdel->fields['userId'],'delete_content')) {
						continue;
					}
					$content->setDeleteContent($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
			/*
		case "delete": 
				$content->setDeleteContent($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
			*/
		case "edit": 
				//edit
				//$contact->setUpdateContact($_REQUEST['conId'],$_REQUEST['conFname'],$_REQUEST['conLname'],$_REQUEST['conPosition'],$_REQUEST['conAddress1'],$_REQUEST['conAddress2'],$_REQUEST['conCity'],$_REQUEST['conState'],$_REQUEST['cntId'],$_REQUEST['conZipcode'],$_REQUEST['conPhone'],$_REQUEST['conFax'],$_REQUEST['conMobile'],$_REQUEST['conEmail'],$_REQUEST['conURL']);
				if (!$sys_lanai->validateCsrfToken('content', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$rsedit=$content->getContentById($_REQUEST['mid']);
				if ($rsedit->recordcount()<1 || !$sys_lanai->userCanActOnContent($rsedit->fields['userId'],'edit_content')) {
					$sys_lanai->getErrorBox(_CONTENT_NO_PERMISSION);
					break;
				}
				if (empty($_REQUEST['conTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$content->setEditContent($_REQUEST['mid'],$_REQUEST['conTitle'],$_REQUEST['conBody1'],$_REQUEST['conBody2']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
				
			break;
		
	} // switch
		
?>