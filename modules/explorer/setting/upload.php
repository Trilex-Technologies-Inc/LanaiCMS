<?
	if ( !eregi( "setting.php", $_SERVER['PHP_SELF'] ) ) {
	    die ( "You can't access this file directly..." );
	}

	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction );

    $objExpl=new Explorer();

    if (!empty($_REQUEST["ac"]) && $_REQUEST["ac"]=="upload") {
        foreach ($_FILES["userfile"]["error"] as $key => $error) {
    	    if ($error == UPLOAD_ERR_OK) {
    	        $tmp_name = $_FILES["userfile"]["tmp_name"][$key];
    	        $name = $_FILES["userfile"]["name"][$key];
    	        move_uploaded_file($tmp_name,$objExpl->_dir($_REQUEST['dir']).$sys_lanai->getPath().$name);
    	    }
	    }
	    $sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name."&dir=".$_REQUEST['dir']);

    } else {
?>
<span class="txtContentTitle"><?=_EXPLORER_UPLOAD_SETTING; ?></span><br/><br/>
<?=_EXPLORER_UPLOAD_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="javascript:document.post.submit();" >
<?=_SAVE; ?></a>&nbsp;&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=explorer" >
<?=_CANCEL; ?></a>&nbsp;&nbsp;
<br /><br />
<table>
<form name="post" action="setting.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="modname" value="explorer">
<input type="hidden" name="mf" value="upload">
<input type="hidden" name="dir" value="<?=$_REQUEST['dir']; ?>">
<input type="hidden" name="ac" value="upload">
<?
    for ($i=0;$i<10;$i++) {
?>
  <tr>
    <td><?=_FILE; ?> <?=($i+1); ?></td>
    <td><input name="userfile[]" type="file" /></td>
  </tr>
<?
    }
?>
</form>
</table>
<?
    }

?>
