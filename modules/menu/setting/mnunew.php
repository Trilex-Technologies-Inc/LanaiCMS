<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$mnu_lanai=new Menu();
	?><span class="txtContentTitle"><?=_MENU_SETTING; ?></span><br/><br/>
	<?=_MENU_TYPE_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_NEW; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="setting.php?modname=menu"><?=_BACK; ?></a>
	<br><br>
	
	<table>
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="mnunewform">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="ac" value="new">
	<tr>
		<td valign="top"><?=_MENU_TYPE; ?></td>
		<td>
			<select name="m" size="5" style="width:200px;">
				<option value="c"><?=_MENU_CONTENT; ?></option>
				<option value="m"><?=_MENU_MODULE; ?></option>
				<option value="l" selected><?=_MENU_LINK; ?></option>
			</select>
		</td>	
	</tr>
	</table>