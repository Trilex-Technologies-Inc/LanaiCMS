<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);
	
	$poll = new Poll();
	
	$rsPoll=$poll->getPollItemById($_REQUEST['mid']);
	
	if ((time()-($poll->getLastVoteTimestamp($_REQUEST['mid']))) >= ($rsPoll->fields['pllLag'])) {
	    $poll->setVotePollOptionItem($_REQUEST['mid'],$_REQUEST['voteChoice']);
		$poll->setVoteTimeStamp($_REQUEST['mid']);
	}
	
	$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&mid=".$_REQUEST['mid']);	
	
	
?>
