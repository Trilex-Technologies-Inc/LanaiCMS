<?php

if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
		die ("You can't access this file directly...");
}

?>
<script language="javascript">
	<!--
		function chk_mdelete() {
			if (confirm("<?=_DELETE_QUESTION; ?>")){
				document.form.ac.value="mdelete";
				document.form.submit();
			}
		}
	-->
</script>
<span class="txtContentTitle"><?=_BANN_SETTING; ?></span><br><br>
<?=_BANN_SETTING_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=carousel&mf=add" ><?=_NEW; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
<a href="javascript:chk_mdelete();" ><?=_DELETE; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="module.php?modname=setting" ><?=_BACK; ?></a><br><br>
<form name="form" method="get" action="setting.php">
<input type="hidden" name="modname" value="carousel">
<input type="hidden" name="mf" value="bannedit">
<input type="hidden" name="ac" value="">
<?
$objbanner=new banner();
$sql="SELECT * FROM ".$objbanner->_table." ORDER BY banPosition ASC, banTitle ASC";
$pager=new bannerPager($db,$sql,30);
$pager->link="setting.php?modname=carousel&";
$pager->renderPage();

?>
</form>
