<?php
	//include_once("modules/massmail/module.php");
	include_once("modules/massmail/language/lang-".$cfg['lang'].".php");
?>
<table>
<form name="addform" method="post" action="module.php">
<input type="hidden" name="modname" value="massmail">
<input type="hidden" name="ac" value="add">
<tr><td><?=_MASSMAIL_SUBSCRIBER; ?></td></tr>
<tr><td><input type="text" id="subName" name="subName" size="20">*</td></tr>
<tr><td><?=_MASSMAIL_EMAIL; ?></td></tr>
<tr><td><input type="text" id="subEmail" name="subEmail" >*</td></tr>
<tr><td> </td></tr>
<tr><td><input type="submit" value="<?=_SUBSCRIBE; ?>" class="inputButton">&nbsp;<input type="reset" value="<?=_RESET; ?>" class="inputButton"></td></tr>
</form>
</table>
<script language="JavaScript" src="include/jsvalidator/gen_validatorv2.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	//You should create the validator only after the definition of the HTML form
	var frmvalidator  = new Validator("addform");
	frmvalidator.addValidation("subName","req","<?=_MASSMAIL_SUBSCRIBER_EMPTY; ?>");
	frmvalidator.addValidation("subEmail","req","<?=_MASSMAIL_EMAIL_EMPTY; ?>");
</script>