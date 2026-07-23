<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$blc_lanai=new Block();
	?><span class="txtContentTitle"><?=_BLOCK_SETTING; ?></span><br/><br/>
	<?=_BLOCK_TYPE_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_NEW; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
	<br><br>
	
	<table>
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">	
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mf" value="blcnewform">
	<input type="hidden" name="ac" value="new">
	<tr>
		<td valign="top"><?=_BLOCK_TYPE; ?></td>
		<td>
			<select name="m" size="5" >
				<option value="c" selected><?=_BLOCK_CONTENT_TYPE; ?></option>				
				<!-- <option value="r"><?=_BLOCK_RSS; ?></option> -->
				<option value="b"><?=_BLOCK_UPLOAD; ?></option>
				<option value="p"><?=_BLOCK_EXTRACTED_PATH; ?></option>
			</select>
		</td>	
	</tr>
	</table>