<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}

	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);

    $site= new SiteMap();
?>
<span class="txtContentTitle"><?=_SITE_MAP; ?></span><br/>
<?php
    $site->render();
?>
<br/>
<!-- <div align="left">
<a href="<?=$site->cfg['url']."/sitemap.php"; ?>" target="_blank">
<img src="modules/<?=$module_name; ?>/images/google-sitemap.png" alt="google site map" border="0" align="absmiddle"/>
</a>
<?=_SITE_MAP_GOOGLE_LINK; ?>
</div> -->
