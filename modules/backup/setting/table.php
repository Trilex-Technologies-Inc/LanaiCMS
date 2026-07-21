<?php
    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );

?>
<span class="txtContentTitle"><?=_BACKUP_CHOOSE_TABLE_SETTING; ?></span><br/><br/>
<?=_BACKUP_CHOOSE_TABLE_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="javascript:document.backup.submit();"><?=_SAVE; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="setting.php?modname=<?=$module_name; ?>"><?=_BACK; ?></a><br/><br/>
<?php
  $bup=new DBBackup();
  $rs=$bup->ListTable();
?>
<table>
<form name="backup" method="post" action="setting.php">
<input type="hidden" name="modname" value="backup">
<input type="hidden" name="mf" value="backup">
  <tr>
    <td>
        <select name="table[]" size="10" multiple="multiple">
        <?php
        while (!$rs->EOF) {
          if ($bup->isLanaiTable($rs->fields[0])) {
            ?><option value="<?=$rs->fields[0]; ?>" selected><?=$rs->fields[0]; ?></option><?php
          }
          $rs->movenext();
        }
        ?>
        </select>
        <br/><br/>
        <input type="checkbox" name="schema" value="s" checked="checked" />&nbsp;<?=_BACKUP_SCHEMA; ?><br/>
        <input type="checkbox" name="value" value="v" checked="checked" />&nbsp;<?=_BACKUP_VALUE; ?>
    </td>
  </tr>
</table>
