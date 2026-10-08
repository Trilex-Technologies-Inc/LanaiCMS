<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$module=new Module();
	?><span class="txtContentTitle"><?=_MODULE_SETTING; ?></span><br/><br/>
	<?=_MODULE_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=modnew" ><?=_NEW; ?></a>&nbsp;&nbsp; 
	
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
				document.getElementById("module-list-form").ac.value="mdelete";
				document.getElementById("module-list-form").requestSubmit();
			}
		}
		function chk_mactive() {
			document.getElementById("module-list-form").ac.value="mactive";
			document.getElementById("module-list-form").requestSubmit();
		}
	//-->
	</script>
<?php
	$module->getModuleList();
	
?>