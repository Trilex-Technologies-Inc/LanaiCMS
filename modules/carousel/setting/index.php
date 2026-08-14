<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
		die ("You can't access this file directly...");
}

?>
<span class="txtContentTitle"><?=_BANN_SETTING; ?></span><br><br>
<?=_BANN_SETTING_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=carousel&mf=add" ><?=_NEW; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="module.php?modname=setting" ><?=_BACK; ?></a><br><br>
<form id="carousel-list-form" name="carousel-list-form" method="post" action="setting.php">
<input type="hidden" name="modname" value="carousel">
<input type="hidden" name="mf" value="bannedit">
<button type="submit" name="ac" value="mactive" class="btn btn-sm btn-outline-success">
    <img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle" alt="">
    <?=_BANN_ACTIVE; ?>
</button>
<button type="submit" name="ac" value="mdelete" class="btn btn-sm btn-outline-danger"
        onclick="return confirm(<?=htmlspecialchars(json_encode(_DELETE_QUESTION), ENT_QUOTES, 'UTF-8'); ?>);">
    <img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle" alt="">
    <?=_DELETE; ?>
</button>
<br><br>
<?php
$objbanner=new banner();
$sql="SELECT * FROM ".$objbanner->_table." ORDER BY banPosition ASC, banTitle ASC";
$pager=new bannerPager($db,$sql,30);
$pager->link="setting.php?modname=carousel&";
$pager->renderPage();

?>
</form>
