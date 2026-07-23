<?php
		include_once("../config.inc.php");
?>
<SCRIPT LANGUAGE="JavaScript">
<?php	
		if ($cfg_seo=="yes") {  
?>
		location.href="<?=$cfg_url; ?>/member.mf.memloginform.htm";
<?php	} else { 
?>	
		location.href="<?=$cfg_url; ?>/module.php?modname=member&mf=memloginform";
<?php
		}	
?>
</SCRIPT>