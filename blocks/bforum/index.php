<?php
	include_once("modules/forum/module.php");
	include_once("modules/forum/language/lang-".$cfg['lang'].".php");
	
	$objforum=new Forum();
	
	$rsforum=$objforum->getForumItems();
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" class="tblForumTable">
  <tr  class="tblForumTop">
    <th width="80%" height="40" class="tblForumTop"><?=_FORUM_FORUM; ?></th>
    <th colspan="2" ><?=_FORUM_LASTPOST; ?></th>
  </tr>
<?php
	$num=0;
	while ((!$rsforum->EOF) AND ($num<10)) {
?>
<tr>
	<td bgcolor="#FFFFFF" height="30">
	<?php
		if ($rsforum->fields['fitParentId']==0) {
	?>
	<a href="module.php?modname=forum&mf=viewitem&fid=<?=$rsforum->fields['fitId']; ?>&fgid=<?=$rsforum->fields['fctId']; ?>">
	<?php
		} else {
	?>
	<a href="module.php?modname=forum&mf=viewitem&fid=<?=$rsforum->fields['fitParentId']; ?>&fgid=<?=$rsforum->fields['fctId']; ?>">
	<?php	
		}
	?>
	<?=$rsforum->fields['fitTitle']; ?>
	</a>
	</td>
	<td bgcolor="#FFFFFF" height="30">
	<span class="tblForumLastPost">
	<?=adodb_date2("d M Y H:i",$rsforum->fields['fitCreate'])." "._FORUM_BY." "; ?>
	<?php
	 if ($rsforum->fields['userId']==0) {
                  echo $rsforum->fields['fitName'];
              } else {
                  echo $objforum->getMemberNameById($rsforum->fields['userId']);
              }
	?>
	</span>
	</td>
</tr>
<?php
		$rsforum->movenext();
		$num++;
	}
	
?>
</table>