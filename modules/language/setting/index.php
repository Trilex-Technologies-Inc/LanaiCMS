<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$lng_lanai=new Language();
	
	if (!is_writeable('config.inc.php')) {
		$sys_lanai->getErrorBox(_CONFIG_CANNOT_WRITE);
	} else {
	
	
	?><span class="txtContentTitle"><?=_LANGUAGE_SETTING; ?></span><br/><br/>
	<?=_LANGUAGE_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="module.php?modname=setting" ><?=_BACK; ?></a>
	<br><br>
	
	<?php
		$xlang=$lng_lanai->getLanguage();
	?>
	<table cellpadding="3" cellspacing="1">
	<form name="form" method="get"  action="<?=$_SERVER['PHP_SELF']; ?>">	
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mf" value="lngedit">
	<input type="hidden" name="ac" value="save">
	<tr>
		<td valign="top">
		<?=_LANGUAGE_NAME; ?>
		</td>
		<td>
		<select name="lngname" size="5" style="width:300px;">
		<?php
			
			foreach ($xlang as $value) {
				$xvalue=substr($value,5,strlen($value));
				$xvalue=substr($xvalue,0,strlen($xvalue)-4);
				if (($lng_lanai->getCurrentLanguage())==$xvalue) {
				    $selected="selected";
				} else {
					  $selected="";
				}				
		  	  ?>
			  	<option value="<?=$xvalue; ?>" <?=$selected; ?> ><?=ucwords($xvalue); ?></option>
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