<?php
	include_once("modules/log/module.php");
	include_once("modules/log/language/lang-".$cfg['lang'].".php");
?>
<img src="blocks/bcounter/images/icon.gif" align="absmiddle"> <b><?=_LOG_COUNTER; ?></b><span style="font-family: Verdana; font-size: 12px; font-weight: bold; color:red;">
<?php
	include_once("modules/log/module.php");
	$olog=new SysLog();
	echo sprintf("%d",$olog->getCounter());
?>
</span>