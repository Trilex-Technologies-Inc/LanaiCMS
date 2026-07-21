<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
		die ("You can't access this file directly...");
}

?>
<span class="txtContentTitle"><?=_JPOP_NEW_SETTING; ?></span><br><br>
<a href="#" onclick="javascript:history.back();"><?=_JPOP_BACK; ?></a><br><br>
<table>
<form name="addform" method="get" action="setting.php">
<input type="hidden" name="modname" value="jpop">
<input type="hidden" name="mf" value="popadd">
<input type="hidden" name="ac" value="add">
<tr><td><?=_JPOP_TITLE; ?> : </td><td><input type="text" id="popTitle" size="50" name="popTitle" ></td></tr>
<tr><td valign="top"><?=_JPOP_DES; ?> : </td>
<td>
	<?php 
				$sBasePath = "include/fckeditor/";
				$oFCKeditor1 = new FCKeditor('popDescription') ;
				$oFCKeditor1->ToolbarSet	= "MyToolbar" ;
				$oFCKeditor1->Width ="620";	
				$oFCKeditor1->Height ="240";			
				$oFCKeditor1->BasePath	= $sBasePath ;
				$oFCKeditor1->Value		= '' ;
				$oFCKeditor1->Create() ;
	?>
</td>
</tr>
<tr><td><?=_JPOP_BG_TITLE; ?> : </td><td><input type="text" id="popBgTitle" name="popBgTitle" maxlength="7" value="#990000"></td></tr>
<tr><td><?=_JPOP_BG_DES; ?> : </td><td><input type="text" id="popBgDes" name="popBgDes" maxlength="7" value="#FFFFFF"></td></tr>
<tr><td><?=_JPOP_BG_BORDER; ?> : </td><td><input type="text" id="popBgBorder" name="popBgBorder" maxlength="7" value="#990000"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="<?=_SAVE; ?>" class="inputButton">&nbsp;<input type="reset" value="<?=_RESET; ?>" class="inputButton"></td></tr>
</form>
</table>
<?php
if ($_REQUEST['ac']=="add") {
$objJpop=new Jpop();
$objJpop->_table=$cfg['tablepre']."jpop";
$objJpop->poptitle=$_REQUEST['popTitle'];
$objJpop->popdescription=$_REQUEST['popDescription'];
$objJpop->popbgtitle=$_REQUEST['popBgTitle'];
$objJpop->popbgdes=$_REQUEST['popBgDes'];
$objJpop->popbgborder=$_REQUEST['popBgBorder'];
$objJpop->popactive="n";

$result=$objJpop->save();
$sys_lanai->go2Page("setting.php?modname=jpop");
}
?>