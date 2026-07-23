<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$mnu_lanai=new Menu();
	?>
	<span class="txtContentTitle"><?=_MENU_SETTING; ?></span><br/><br/>
	<?=_MENU_EDIT_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a><br><br>
	<table >
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">	
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mf" value="mnuedit">
	<input type="hidden" name="ac" value="edit">	
	<input type="hidden" name="mnid" value="<?=$_REQUEST['mid']?>">	
	<input type="hidden" name="m" value="<?=$_REQUEST['m']?>">
	<?php 
		$rs=$mnu_lanai->getMenuById($_REQUEST['mid']);
		switch($_REQUEST['m']){
			case 'c': 
				// content type
	?>
	<tr>
		<td><?=_MENU_TITLE; ?></td>
		<td><input type="text" name="mnuTitle" size="30" value="<?=$rs->fields['mnuTitle']; ?>">*</td>
	</tr>
	<tr>
		<td><?=_MENU_CONTENT; ?></td>
		<td><?=$mnu_lanai->getContentCombo("conId",$rs->fields['conId']); ?>*</td>
	</tr>
	<tr>
		<td><?=_MENU_TARGET; ?></td>
		<td><?=$mnu_lanai->getTargetCombo("mnuTarget",$rs->fields['mnuTarget']); ?></td>
	</tr>
	<tr>
		<td><?=_MENU_PARENT; ?></td>
		<td><?=$mnu_lanai->getMenuParentCombo("mnuParentId",$rs->fields['mnuParentId']); ?></td>
	</tr>	
	<?php
				break;
			case 'm': 
				// module
	?>
	<tr>
		<td><?=_MENU_TITLE; ?></td>
		<td><input type="text" name="mnuTitle" size="30" value="<?=$rs->fields['mnuTitle']; ?>">*</td>
	</tr>
	<tr>
		<td><?=_MENU_MODNAME; ?></td>
		<td><?=$mnu_lanai->getModuleCombo("modId",$rs->fields['modId']); ?>*</td>
	</tr>
	<tr>
		<td><?=_MENU_TARGET; ?></td>
		<td><?=$mnu_lanai->getTargetCombo("mnuTarget",$rs->fields['mnuTarget']); ?></td>
	</tr>
	<tr>
		<td><?=_MENU_PARENT; ?></td>
		<td><?=$mnu_lanai->getMenuParentCombo("mnuParentId",$rs->fields['mnuParentId']); ?></td>
	</tr>	
	<?php
				break;
			case 'l':
				// like
	?>
	<tr>
		<td><?=_MENU_TITLE; ?></td>
		<td><input type="text" name="mnuTitle" size="30" value="<?=$rs->fields['mnuTitle']; ?>">*</td>
	</tr>
	<tr>
		<td><?=_MENU_URL; ?></td>
		<td><input type="text" name="mnuUrl" size="50" value="<?=$rs->fields['mnuUrl']; ?>">*</td>
	</tr>
	<tr>
		<td><?=_MENU_TARGET; ?></td>
		<td><?=$mnu_lanai->getTargetCombo("mnuTarget",$rs->fields['mnuTarget']); ?></td>
	</tr>
	<tr>
		<td><?=_MENU_PARENT; ?></td>
		<td><?=$mnu_lanai->getMenuParentCombo("mnuParentId",$rs->fields['mnuParentId']); ?></td>
	</tr>	
	<?php
				break;
		} // switch
	?>	
	</table>