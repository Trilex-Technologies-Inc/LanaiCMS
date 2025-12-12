<?php

if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
		die ("You can't access this file directly...");
}


if (!empty($_REQUEST['ac']) && $_REQUEST['ac']=="add") {
	
$objbanner=new banner();
$objbanner->banTitle=$_REQUEST['banTitle'];
$objbanner->banDescription=$_REQUEST['banDescription'];
$objbanner->banImage=$_REQUEST['banImage'];
$objbanner->banURL=$_REQUEST['banURL'];
$objbanner->banDate=date("Y-m-d H:i:s");

$result=$objbanner->save();

if (!$result) { 
	$sys_lanai->getErrorBox($objbanner->ErrorMsg());
} else {
	$sys_lanai->go2Page("setting.php?modname=carousel");
}

} else {
?>
<span class="txtContentTitle"><?=_BANN_NEW_ITEM; ?></span><br><br>
<?=_BANN_NEW_INSTRUCTION; ?><br/><br/>
<!--<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.addform.submit();"><?=_BANN_SAVE; ?></a> -->
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=carousel" ><?=_BACK; ?></a><br><br>
<script>
// load image
	function loadImage() {
		//if (document.addform.banImage.value!="") {
			document.addform.banView.src=document.addform.banImage.value;
		//}
	}
</script>
<table>
<form name="addform" method="get" action="setting.php">
<input type="hidden" name="modname" value="carousel">
<input type="hidden" name="mf" value="add">
<input type="hidden" name="ac" value="add">
<tr><td><?=_BANN_TITLE; ?></td><td><input type="text" id="banTitle" name="banTitle" size="30">*</td></tr>
<tr><td valign="top"><?=_BANN_DES; ?></td><td><textarea name="banDescription" cols="30" rows="5"></textarea>*</td></tr>
<tr><td><?=_BANN_IMAGE_URL; ?></td><td><input type="text" id="banImage" name="banImage" size="50" onblur="javacript:loadImage()">*</td></tr>
<tr><td><?=_BANN_URL; ?></td><td><input type="text" id="banURL" name="banURL" size="40">*</td></tr>
<tr><td>&nbsp;</td><td><img src="modules/carousel/images/space.gif" name="banView" ></td></tr>
<tr><td>&nbsp;</td><td><input  type="submit" value="<?=_SAVE; ?>" class="inputButton"> <input  type="reset" value="<?=_RESET; ?>" class="inputButton"></td></tr>
</form>
</table>
<script language="JavaScript" src="include/jsvalidator/gen_validatorv2.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	//You should create the validator only after the definition of the HTML form
	var frmvalidator  = new Validator("addform");
	frmvalidator.addValidation("banTitle","req","<?=_BANN_TITLE_EMPTY; ?>");
	frmvalidator.addValidation("banDescription","req","<?=_BANN_DES_EMPTY; ?>");
	frmvalidator.addValidation("banImage","req","<?=_BANN_IMAGE_URL_EMPTY; ?>");
	frmvalidator.addValidation("banURL","req","<?=_BANN_URL_EMPTY; ?>");
</script>
<?
	} // check add
?>