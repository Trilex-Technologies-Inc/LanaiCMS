<span class="txtContentTitle"><?=_INFO_SETTING; ?></span><br><br>
<?=_INFO_SETTING_INSTRUCTION; ?><br/><br/>
<img src="theme/<?=$cfg['theme']; ?>/images/html.gif" border="0" align="absmiddle"/>
<a href="#" ><?=_INFO; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/file.gif" border="0" align="absmiddle"/>
<a href="license.txt" target="_blank"><?=_LICENSE; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/config.gif" border="0" align="absmiddle"/>
<a href="http://www.redlinesoft.net" target="_blank"><?=_CREDIT; ?></a>&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="module.php?modname=setting" ><?=_BACK; ?></a><br><br>
<?php
	$lanai_ver=file("version.txt");
?>
<table>
<tr>
<td><?=_VERSION; ?></td>
<td>
<?php
		echo $lanai_ver[0];
?>
</td>
</tr>
<?php
		if (file_exists("modules/info/license.id.php")) {
			$lanai_license=file("modules/info/license.id.php");
			list($serial,$to)=explode(":",$lanai_license[0],2);
		}
?>
<tr>
<td><?=_SERIAL; ?></td>
<td>
<?php
	if (empty($serial)) {
		echo "preview version";
	} else {
		echo $serial;
	}
?>
</td>
</tr>
<tr>
<td><?=_LICENSED_TO; ?></td>
<td>
<?php
	if (empty($to)) {
		echo "none";
	} else {
		echo $to;
	}
?>
</td>
</tr>
</table>
<br><br>
