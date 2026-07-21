<?php

if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);
	
	$content = new News();
	
	// load read class
	include_once("include/lanai/class.comment.php");
	$commOjb=new Comment();

	if ((!empty($_REQUEST['comAuthor'])) AND (!empty($_REQUEST['comEmail'])) AND (!empty($_REQUEST['comDetail']))) {
		$commOjb->addComment("news",$_REQUEST['cid'],$_REQUEST['comDetail'],$_REQUEST['comAuthor'],$_REQUEST['comEmail']);
		$sys_lanai->go2Page("module.php?modname=news&mf=nwsview&cid=".$_REQUEST['cid']);
	} else {
		$sys_lanai->getErrorBox(_NWS_REQUIRE_FIELDS.", <a href=\"javascript:history.back();\">".strtolower(_BACK)."</a>");
	}

?>
