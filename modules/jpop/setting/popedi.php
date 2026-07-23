<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
		die ("You can't access this file directly...");
}

/* load data to edit */
$objJpop=new Jpop();
$objJpop->_table=$cfg['tablepre']."jpop";
$rs=$objJpop->Load("popId=".$_REQUEST['i']);
if (!$rs) {
	/* no data to edit - show error message*/
	$sys_lanai->getErrorBox("Data not found!");
}  else {
	if ($_REQUEST['ac']=="edit") {
		$objJpop->poptitle=$_REQUEST['popTitle'];
		$objJpop->popdescription=$_REQUEST['popDescription'];
		$objJpop->popbgtitle=$_REQUEST['popBgTitle'];
		$objJpop->popbgdes=$_REQUEST['popBgDes'];
		$objJpop->popbgborder=$_REQUEST['popBgBorder'];

		$result =$objJpop->save();
		$sys_lanai->go2Page("setting.php?modname=jpop");
	} else {
	/* found data then fill in form */
?>

<span class="txtContentTitle"><?=_JPOP_EDIT_SETTING; ?></span><br><br>
<a href="#" onclick="javascript:history.back();"><?=_JPOP_BACK; ?></a><br><br>

	<table>
	<form name="addform" method="get" action="setting.php">
	<input type="hidden" name="modname" value="jpop">
	<input type="hidden" name="mf" value="popedi">
	<input type="hidden" name="ac" value="edit">
	<input type="hidden" name="i" value="<?=$_REQUEST['i']; ?>">
	<tr><td><?=_JPOP_TITLE; ?> : </td><td><input type="text" id="popTitle" name="popTitle" size="40" value="<?=$objJpop->poptitle;?>"></td></tr>
	<tr valign="top"><td><?=_JPOP_DES; ?> : </td>
	<td>
	<?php 
				$sBasePath = "include/fckeditor/";
				$oFCKeditor1 = new FCKeditor('popDescription') ;
				$oFCKeditor1->ToolbarSet	= "MyToolbar" ;
				$oFCKeditor1->Width ="620";	
				$oFCKeditor1->Height ="240";			
				$oFCKeditor1->BasePath	= $sBasePath ;
				$oFCKeditor1->Value		= stripslashes($objJpop->popdescription) ;
				$oFCKeditor1->Create() ;
	?>
	</td>
	</tr>
	<tr><td><?=_JPOP_BG_TITLE; ?> : </td><td><input type="text" id="popBgTitle" name="popBgTitle" maxlength="7" value="<?=$objJpop->popbgtitle;?>" ></td></tr>
	<tr><td><?=_JPOP_BG_DES; ?> : </td><td><input type="text" id="popBgDes" name="popBgDes" maxlength="7" value="<?=$objJpop->popbgdes;?>"></td></tr>
	<tr><td><?=_JPOP_BG_BORDER; ?> : </td><td><input type="text" id="popBgBorder" name="popBgBorder" maxlength="7" value="<?=$objJpop->popbgborder;?>"></td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" value="<?=_SAVE; ?>" class="inputButton">&nbsp;<input type="reset" value="<?=_RESET; ?>" class="inputButton"></td></tr>
	</form>
	</table>
<?php
	} // edit action
} // data found
?>