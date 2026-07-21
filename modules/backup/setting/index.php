<?php
	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );

?>
<span class="txtContentTitle"><?=_BACKUP_SETTING; ?></span><br/><br/>
<?=_BACKUP_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=<?=$module_name; ?>&mf=table"><?=_NEW; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="module.php?modname=setting"><?=_BACK; ?></a><br/><br/>
<?php
  $bup=new DBBackup();
?>
<script language="JavaScript" type="text/javascript">
    function ckdelete(id){
        if (confirm("<?=_DELETE_QUESTION; ?>")){
		    location.href="<?=$cfg['url']; ?>"+"/setting.php?modname=backup&mf=delete&f="+id;
		}
    }
    function ckrestore(id){
        if (confirm("<?=_RESTORE_QUESTION; ?>")){
		    location.href="<?=$cfg['url']; ?>"+"/setting.php?modname=backup&mf=restore&f="+id;
		}
    }
</script>
<table cellpadding="3" cellspacing="1" width="100%">
<tr>
<th class="tblRowSolidTopDown"><?=_FILENAME; ?></th>
<th class="tblRowSolidTopDown"><?=_SIZE; ?></th>
<th class="tblRowSolidTopDown"><?=_MODIFIED; ?></th>
<th class="tblRowSolidTopDown"><?=_DOWNLOAD; ?></th>
<th class="tblRowSolidTopDown"><?=_RESTORE; ?></th>
<th class="tblRowSolidTopDown"><?=_DELETE; ?></th>
</tr>
<?php
    $fArr=$bup->getBackupFile();
    if ((count($fArr)) >0){
    foreach ($fArr as $item) {
?>
<tr>
<td class="tblRowDash">
	<img src="theme/<?=$cfg['theme'];?>/images/file.gif" border="0" align="absmiddle">
	<?=$item; ?>
</td>
<td class="tblRowDash">
	<?=$bup->getFileSize($item); ?>
</td>
<td class="tblRowDash">
	<?=date("Y-m-d H:i:s",$bup->getModifiedTime($item)); ?>
</td>
<td class="tblRowDash" align="center">
	<a href="modules/backup/download.php?f=<?=$item; ?>"><img src="theme/<?=$cfg['theme'];?>/images/save.gif" border="0" align="absmiddle"></a>
</td>
<td class="tblRowDash" align="center">
	<a href="javascript:ckrestore('<?=$item; ?>')"><img src="theme/<?=$cfg['theme'];?>/images/ok.gif" border="0" align="absmiddle"></a>
</td>
<td class="tblRowDash" align="center">
	<a href="javascript:ckdelete('<?=$item; ?>')"><img src="theme/<?=$cfg['theme'];?>/images/delete.gif" border="0" align="absmiddle"></a>
</td>
</tr>
<?php
    } } // for each
?>
</table>
