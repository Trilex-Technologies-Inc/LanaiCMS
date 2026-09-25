<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$pll=new Poll();
	
	switch($_REQUEST['ac']){
		case "new":
				if (!$sys_lanai->validateCsrfToken('poll', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$ppoItem=$_REQUEST['ppoTitle'];
				if ((empty($_REQUEST['pllTitle'])) OR (($pll->getPollOptionItemCount($ppoItem)) < 2 )) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					// set poll
					$pll->setNewPollItem($_REQUEST['pllTitle'],$_REQUEST['pllLag']);
					// select poll id
					$mid=$pll->getPollItemIdByTitle($_REQUEST['pllTitle']);
					// set poll option
					$ppoItem=$_REQUEST['ppoTitle'];
					for ($i=0;$i<(count($ppoItem));$i++){
						$pll->setNewPollOption($mid,$ppoItem[$i]);
					}
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}				
				
			break;
		case "active": 
				if (!$sys_lanai->validateCsrfToken('poll', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$pll->setPollItemActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mactive": 
				if (!$sys_lanai->validateCsrfToken('poll', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$pll->getPollItemById($midarr[$i]);
					if ($rsdwn->fields['pllActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$pll->setPollItemActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		
		case "mdelete":
				if (!$sys_lanai->validateCsrfToken('poll', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$pll->setDeletePollItem($midarr[$i]);
					$pll->setDeletePollOptionItem($midarr[$i]);
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		
		case "edit": 
				if (!$sys_lanai->validateCsrfToken('poll', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
					$sys_lanai->getErrorBox("Invalid request, please try again.");
					break;
				}
		 		$ppoItem=$_REQUEST['ppoTitle'];
				if ((empty($_REQUEST['pllTitle'])) OR (($pll->getPollOptionItemCount($ppoItem)) < 2 )) {
			   		$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">"._BACK."</a>");
				} else {
					// set poll
					$pll->setEditPollItem($_REQUEST['mid'],$_REQUEST['pllTitle'],$_REQUEST['pllLag']);
					// set poll option
					$ppoItem=$_REQUEST['ppoTitle'];
					$ppoItemId=$_REQUEST['ppoId'];
					for ($i=0;$i<(count($ppoItem));$i++){
						$pll->setEditPollOption($ppoItemId[$i],$ppoItem[$i]);
					}
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);	
				}
				
			break;
			
	} // switch
		
?>