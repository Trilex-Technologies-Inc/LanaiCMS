<?php
	$obLog=new SysLog();
	$rs=$obLog->loadLastLog(20);
?>
<span class="txtContentTitle"><?=_LOG_STAT; ?></span><br/><br/>
<img src="modules/log/graph.php" ><br><br>
<span class="txtContentTitle"><?=_LOG_DETAIL; ?></span><br/><br/>
<table class="tblForumTable" cellpadding="3" cellspacing="1" width="100%">
<tr class="tblForumTop">
	<td ><?=_LOG_DATE; ?></td>
	<td ><?=_LOG_IP; ?></td>
	<td ><?=_LOG_URI; ?></td>
</tr>
<?php
	while (!$rs->EOF) {
?>
<tr >
	<td bgcolor="#FFFFFF" width="150"><?=adodb_date2("d M Y H:i",$rs->fields['logDatetime']); ?></td>
	<td bgcolor="#FFFFFF" width="120"><?=$rs->fields['logIP'];?></td>
	<td bgcolor="#FFFFFF" ><?=$rs->fields['pagUrl'];?></td>
</tr>
<?php
		$rs->movenext();
	}
?>
</table>