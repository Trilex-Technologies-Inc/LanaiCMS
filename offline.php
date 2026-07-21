<?php
	$offpage="yes";		
	include_once('setconfig.inc.php');
	if ($cfg_off=="no") {
	   $sys_lanai->go2Page("index.php");
	}
	//include_once('include/header.inc.php');	
?>
<html>
<title><?=$cfg_title;?></title>
<body>
<div align="center">
<h1>Tempolary Out of Services</h1>
<h2>
<?=$cfg_title; ?>&nbsp;&nbsp;
<?=$cfg_url; ?>
</h2>
</div>
<?php
	include_once('include/footer.inc.php');
		
?>