<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$thm_lanai=new Themes();
	$thm_lanai->deleteCache();
	if (!is_writeable('config.inc.php')) {
		$sys_lanai->getErrorBox(_CONFIG_CANNOT_WRITE);
	} else {
	
	
	?><span class="txtContentTitle"><?=_THEME_SETTING; ?></span><br/><br/>
	<?=_THEME_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="module.php?modname=setting" ><?=_BACK; ?></a>
	<br><br>
	
	<?php
		$xtheme=$thm_lanai->getTheme();


	?>
	<table cellpadding="3" cellspacing="1">
	<form name="form" method="get"  action="<?=$_SERVER['PHP_SELF']; ?>">	
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mf" value="thmedit">
	<input type="hidden" name="ac" value="save">
	<tr>
		<td valign="top">
		<?=_THEME_NAME; ?>
		</td>
		<td>
		<select name="thmname" size="5" style="width:200px;">
		<?php
			foreach ($xtheme as $value) {
				if (($thm_lanai->getCurrentTheme())==$value) {
				    $selected="selected";
				} else {
					  $selected="";
				}
		  	  ?>
			  	<option value="<?=$value; ?>" <?=$selected; ?> ><?=ucwords($value); ?></option>
			  <?php
			}	
		?>
		</select >		
 		</td>
	</tr>
	</form>
	</table>
	<?php
			} // check writable config 
		
	 ?>