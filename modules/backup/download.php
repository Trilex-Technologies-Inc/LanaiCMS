<?php

include_once("../../config.inc.php");
session_start();
if (($_SESSION['uid']>0))  {
	if (file_exists($cfg_datadir."/backup/".$_REQUEST['f'])) {
		$fp=fopen($cfg_datadir."/backup/".$_REQUEST['f'],"r");
		$content=fread($fp,filesize($cfg_datadir."/backup/".$_REQUEST['f']));
		fclose($fp);
		Header("Content-type: application/application/x-unknown");
		Header("Content-Disposition: attachment; filename=".$_REQUEST['f']);
		echo $content;
		exit;
	}
}


?>
