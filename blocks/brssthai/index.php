<?php
	include_once('modules/rssthai/module.php');
	include_once('modules/rssthai/libs/rssthaireader.php');
	
	$lanai_rss=new LRSSThai();
	$rssx=$lanai_rss->getShowRSS();
	
	while(!$rssx->EOF){
		if ($rssx->fields['rssView']=="horz") {
		    ?><marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="20" truespeed="true"><?php
		}
		
		$rssreader = new rssthai;
		$rssreader->imagedir="./datacenter/rssimages".$sys_lanai->getPath();
		$rssreader->cachedir="./datacenter/rssimages".$sys_lanai->getPath();
		$rssreader->rssuri=$rssx->fields['rssURL'];
		$rssreader->cachetime=$rssx->fields['rssReload'];
		if ($rssx->fields['rssShowDescription']=="y") {
			$rssreader->item_descr=1;
		} else {
			$rssreader->item_descr=0;
		}
		$rssreader->viewas=$rssx->fields['rssView'];		
		$rssreader->rowcount=$rssx->fields['rssItemCount'];
		$rssreader->columncount=$rssx->fields['rssNumColumn'];
		if (!empty($rssx->fields['rssFixedImage'])) {
		    $rssreader->imageuri=$rssx->fields['rssFixedImage'];
		}
		if (!empty($rssx->fields['rssFixedImage'])) {
			$rssreader->imagealter= $rssx->fields['rssAlterImage'];
		}
		$rssreader->imagecount=$rssx->fields['rssNumImage'];
		$rssreader->imagewidth=$rssx->fields['rssImageWidth'];
		$rssreader->imageheight=$rssx->fields['rssImageHeight'];
		$rssreader->imagealign=$rssx->fields['rssImageAlign'];
		$rssreader->feed();
		if ($rssx->fields['rssView']=="horz") {
		    ?></marquee><?php
		}
		$rssx->movenext();
	} // while
	
?>