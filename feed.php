<?php

include_once("include/feedcreater/feedcreator.class.php");
$loadlang="no";
include_once('setconfig.inc.php');
include_once('modules/content/module.php');

$content=new Content();

$rss = new UniversalFeedCreator();
$rss->encoding="utf-8";
$rss->useCached();
$rss->title = $cfg['title'];
$rss->description = "Content from ".$cfg['title'];
$rss->link = $cfg['url'];
$rss->syndicationURL = $cfg['url'].(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/feed.php');

$image = new FeedImage();
$image->title = $cfg['title']." logo";
$image->url = $cfg['url']."/images/xmlcoffeemug.gif";
$image->link = $cfg['url'];
$image->description = "Feed provided by ".$cfg['title']." Click to visit.";
$rss->image = $image;

$items=$db->SelectLimit("SELECT * FROM ".$cfg['tablepre']."content WHERE conActive='y' ORDER BY conModified DESC", 10, 0);

while($items && !$items->EOF){
    $item = new FeedItem();
    $item->title = $items->fields['conTitle'];
    $item->link = $cfg['url']."/module.php?modname=content&cid=".$items->fields['conId'];
    $words = $items->fields['conBody1'];
	$words = preg_replace("'<script[^>]*>.*?</script>'si","",$words);
	$words = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2 (\1)', $words);
	$words = preg_replace('/<!--.+?-->/','',$words);
	$words = preg_replace('/{.+?}/','',$words);
	$words = preg_replace('/&nbsp;/',' ',$words);
	$words = preg_replace('/&amp;/',' ',$words);
	$words = preg_replace('/&quot;/',' ',$words);
	$words = strip_tags($words);
	$words = htmlspecialchars($words);
    $item->description = $words;
    $item->date = adodb_date2("r",$items->fields['conModified']);
    $item->source = $cfg['url'];
    $item->author = "";
    $rss->addItem($item);
    $items->movenext();
}

switch ($_REQUEST['feed']) {
    case "RSS0.91" :
            $rss->saveFeed("RSS0.91",$cfg['datadir']."/feed.xml");
    break;
    case "RSS1.0" :
            $rss->saveFeed("RSS1.0",$cfg['datadir']."/feed.xml");
    break;
    case "RSS2.0" :
            $rss->saveFeed("RSS2.0",$cfg['datadir']."/feed.xml");
    break;
    case "OPML" :
            $rss->saveFeed("OPML",$cfg['datadir']."/opml.xml");
    break;
    case "ATOM" :
            $rss->saveFeed("ATOM",$cfg['datadir']."/atom.xml");
    break;
    default :
        $rss->saveFeed("RSS2.0",$cfg['datadir']."/feed.xml");
}





?>
