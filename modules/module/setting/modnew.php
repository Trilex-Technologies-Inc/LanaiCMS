<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$module=new Module();
	
	?><span class="txtContentTitle"><?=_MODULE_NEW; ?></span><br/><br/>
	<?=_MODULE_NEW_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();" ><?=_BACK; ?></a>
	<br><br>
	
	<table border="0" cellspacing="2" cellpadding="3">
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>"  ENCTYPE="multipart/form-data" >
	<input type="hidden" name="mf" value="modedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="ac" value="new">
	<tr>
		<td valign="top"><input type="radio" name="method" value="3" class="radioButton" checked></td>
		<td><?=_MODULE_DIR_REMOTE; ?><br><br><input type="text" size="40" name="modpath" value=""></td>
	</tr>
	<tr>
		<td valign="top"><input type="radio" name="method" value="1" class="radioButton" ></td>
		<td><?=_MODULE_ZIPFILE_UPLOAD; ?><br><br><input type="file" size="30" name="userfile"></td>
	</tr>
	<tr>
		<td valign="top"><input type="radio" name="method" value="2" class="radioButton" ></td>
		<td><?=_MODULE_ZIPFILE_REMOTE; ?><br><br><input type="text" size="40" name="zippath" value="<?=$cfg['datadir']; ?>"></td>
	</tr>
	</form>
	</table>
<?php

?>