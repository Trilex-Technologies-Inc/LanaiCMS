<?php

include_once("../../config.inc.php");
session_start();
if (($_SESSION['uid']>0))  {
	if (file_exists($cfg_dir."/".$_REQUEST['f'])) {
		$fp=fopen($cfg_dir."/".$_REQUEST['f'],"r");
		$content=fread($fp,filesize($cfg_dir."/".$_REQUEST['f']));
		fclose($fp);
		Header("Content-type: application/application/x-unknown");
		Header("Content-Disposition: attachment; filename=".$_REQUEST['f']);
		echo $content;
		exit;
	}
}

?>