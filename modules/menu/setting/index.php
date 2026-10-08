<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$mnu_lanai=new Menu();
	?><span class="txtContentTitle"><?=_MENU_SETTING; ?></span><br/><br/>
	<?=_MENU_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=mnunew" ><?=_NEW; ?></a>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<button type="button" onclick="chk_mweight();"><?=_SAVEWEIGHT; ?></button>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
	<button type="button" onclick="chk_mactive();"><?=_ACTIVE; ?></button>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
	<button type="button" onclick="chk_mdelete();"><?=_DELETE; ?></button>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="module.php?modname=setting" ><?=_BACK; ?></a>
	<br><br>
	<script language="javascript">
	<!--
		function chk_mdelete() {
			if (confirm("<?=_DELETE_QUESTION; ?>")){
				document.getElementById("menu-list-form").ac.value="mdelete";
				document.getElementById("menu-list-form").requestSubmit();
			}
		}
		function chk_mactive() {
			document.getElementById("menu-list-form").ac.value="mactive";
			document.getElementById("menu-list-form").requestSubmit();
		}
		function chk_mweight() {
			document.getElementById("menu-list-form").ac.value="mweight";
			document.getElementById("menu-list-form").requestSubmit();
		}
	//-->
	</script>
<?php
	$mnu_lanai->getMenuList();
	
?>
