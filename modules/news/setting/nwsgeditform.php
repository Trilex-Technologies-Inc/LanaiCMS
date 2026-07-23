<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$news=new News();
	$rs=$news->getGroupById($_REQUEST['mid']);
	
	?><span class="txtContentTitle"><?=_NEWS_GROUP_SETTING; ?></span><br/><br/>
	<?=_NEWS_GROUP_EDIT_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
	<br><br>
	<table cellpadding="3" cellspacing="1" >
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="nwsedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mid" value="<?=$_REQUEST['mid']; ?>">
	<input type="hidden" name="ac" value="gedit">
	<tr>
		<td ><?=_NEWS_GROUP_TITLE; ?></td>
		<td width="100%" ><input type="text" name="chnTitle" size="40" value="<?=$rs->fields['chnTitle']; ?>">*</td>	
	</tr>
	<tr>
		<td valign="top"><?=_NEWS_GROUP_DESCTIPRION; ?></td>
		<td width="100%" ><textarea name="chnDescription" cols="40" rows="5" ><?=$rs->fields['chnDescription']; ?></textarea></td>	
	</tr>
	</form>
	</table>