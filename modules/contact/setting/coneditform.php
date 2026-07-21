<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$contact=new Contact();
	$rs=$contact->getContactById($_REQUEST['mid']);
	
	?><span class="txtContentTitle"><?=_CONTACT_SETTING; ?></span><br/><br/>
	<?=_CONTACT_EDIT_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
	<br><br>
	<?php
	//conFname  conLname  conPosition  conAddress1  conAddress2  conCity  conState  cntId  conZipcode  conPhone  conFax  conMobile  conEmail  conURL  conActive 
		
	?>
	<table cellpadding="3" cellspacing="1" >
	<form name="form" method="get"  action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="conedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="ac" value="edit">
	<input type="hidden" name="conId" value="<?=$_REQUEST['mid']; ?>">
	<tr>
		<td valign="top"><?=_USER_FNAME; ?></td>
		<td><input type="text" name="conFname" value="<?=$rs->fields['conFname']?>" >*</td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_LNAME; ?></td>
		<td><input type="text" name="conLname" value="<?=$rs->fields['conLname'];?>">*</td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_POSITION; ?></td>
		<td><input type="text" name="conPosition" value="<?=$rs->fields['conPosition'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_ADDRESS1; ?></td>
		<td><input type="text" name="conAddress1" size="40" value="<?=$rs->fields['conAddress1'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_ADDRESS2; ?></td>
		<td><input type="text" name="conAddress2" size="40" value="<?=$rs->fields['conAddress2'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_CITY; ?></td>
		<td><input type="text" name="conCity" value="<?=$rs->fields['conCity'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_STATE; ?></td>
		<td><input type="text" name="conState" value="<?=$rs->fields['conState'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_COUNTRY; ?></td>
		<td><?=$contact->setCountryCombo($rs->fields['cntId'],"cntId"); ?></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_ZIPCODE; ?></td>
		<td><input type="text" name="conZipcode" value="<?=$rs->fields['conZipcode'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_PHONE; ?></td>
		<td><input type="text" name="conPhone" value="<?=$rs->fields['conPhone'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_FAX; ?></td>
		<td><input type="text" name="conFax" value="<?=$rs->fields['conFax'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_MOBILE; ?></td>
		<td><input type="text" name="conMobile" value="<?=$rs->fields['conMobile'];?>"></td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_EMAIL; ?></td>
		<td><input type="text" name="conEmail" value="<?=$rs->fields['conEmail'];?>">*</td>	
	</tr>
	<tr>
		<td valign="top"><?=_USER_URL; ?></td>
		<td><input type="text" name="conURL" value="<?=$rs->fields['conURL'];?>"></td>	
	</tr>
	</table>