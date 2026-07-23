<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	$blc_lanai=new Block();
	?><span class="txtContentTitle"><?=_BLOCK_SETTING; ?></span><br/><br/>
	<?=_BLOCK_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=blcnew" ><?=_NEW; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="javascript:chk_mweight();" ><?=_SAVEWEIGHT; ?></a>&nbsp;&nbsp;	
	
	<img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
	<a href="javascript:chk_mactive();" ><?=_ACTIVE; ?></a>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
	<a href="javascript:chk_mdelete();" ><?=_DELETE; ?></a>&nbsp;&nbsp;		
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="module.php?modname=setting" ><?=_BACK; ?></a>
	<br><br>
	<script language="javascript">
	<!--
		function chk_mdelete() {
			if (confirm("<?=_DELETE_QUESTION; ?>")){
				document.form.ac.value="mdelete";
				document.form.submit();
			}
		}
		function chk_mactive() {
			document.form.ac.value="mactive";
			document.form.submit();
		}
		function chk_mweight() {
			document.form.ac.value="mweight";
			document.form.submit();
		}
	//-->
	</script>
<?php
	$blc_lanai->getBlockList();
	
?>