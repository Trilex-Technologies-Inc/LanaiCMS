<?php
	// modified seo link
	// 22/11/2006

	include_once('modules/news/module.php');
	include_once("theme/".$cfg['theme']."/theme.php");
	$news=new News();
	
	$rs=$news->getNewsVisible(5);
	$theme=new Theme();
	while(!$rs->EOF){
		?>
		<TABLE>
		<TR>
			<TD>
			<?php
					$link=$sys_lanai->getSEOLink("module.php?modname=news&mf=nwsview&cid=".$rs->fields['nwsId']);
			?>
			 <span class="txtContentTitle">
			<?=$rs->fields['nwsTitle']; ?>
			<span>
			 
			</TD>
		</TR>
		<TR>
			<TD class="txtDateTime"><?=adodb_date2("l,d F Y",$rs->fields['nwsCreate']); ?></TD>
		</TR>
		<tr>
		<!--<td>
			<?php //$news->getLinkBlogPost($rs->fields['nwsTitle'],$cfg['url']."/module.php?modname=news&mf=nwsview&cid=".$rs->fields['nwsId']); ?>
			<br/><br/>
		</td>-->
		</tr>
		<TR>
			<TD><?=$rs->fields['nwsPreface']; ?></TD>
		</TR>
		<TR>
			<TD>
			<a href="<?=$link; ?>">
			<?=_MORE; ?>
			</a>
			</TD>
		</TR>
		</TABLE><BR>
		<?php
		$rs->movenext();
	} // while
	
?>