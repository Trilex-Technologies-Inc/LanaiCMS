<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$news=new News();
	
	switch($_REQUEST['ac']){
		case "new":
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				if (empty($_REQUEST['nwsTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$news->setNewNews($_REQUEST['chnId'],$_REQUEST['nwsTitle'],$_REQUEST['nwsPreface'],$_REQUEST['nwsBody']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}				
			break;
		case "gnew":
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				if (empty($_REQUEST['chnTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$news->setNewGroup($_REQUEST['chnTitle'],$_REQUEST['chnDescription']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");
				}				
			break;
		case "active": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$news->setNewsActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$news->getNewsById($midarr[$i]);
					if ($rsdwn->fields['nwsActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$news->setNewsActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":				
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$news->setDeleteNews($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);				
			break;
		case "gactive": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$news->setGroupActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");
			break;
		case "mgactive": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$news->getGroupById($midarr[$i]);
					if ($rsdwn->fields['chnActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$news->setGroupActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");
			break;
		case "mgdelete":				
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$news->setDeleteNewsGroup($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");				
			break;
		/*
		case "delete": 
				$news->setDeleteNews($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "gdelete": 
				$news->setDeleteNewsGroup($_REQUEST['mid']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");
			break;
		*/
		case "edit": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				if (empty($_REQUEST['nwsTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$news->setEditNews($_REQUEST['mid'],$_REQUEST['chnId'],$_REQUEST['nwsTitle'],$_REQUEST['nwsPreface'],$_REQUEST['nwsBody']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}				
			break;
		case "gedit": 
				if (!$sys_lanai->validateCsrfToken('news', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				if (empty($_REQUEST['chnTitle'])) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					$news->setEditGroup($_REQUEST['mid'],$_REQUEST['chnTitle'],$_REQUEST['chnDescription']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsgroup");
				}				
			break;
		
	} // switch
		
?>