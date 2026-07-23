<?php
	include_once("modules/carousel/module.php");
	$oban=new banner();
	$banarr=$oban->randomeBann();
?>
<div align="center" >
<img src="images/ad_dec.gif" ><br>
<a href="module.php?modname=carousel&mf=redirect&id=<?=$banarr[0]; ?>" target="_blank" ><img src="<?=$banarr[2]; ?>" border="0" class="noneStyle"></a>
</div>