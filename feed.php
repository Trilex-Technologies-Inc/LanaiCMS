<?php

include_once("include/feedcreater/feedcreator.class.php");
$loadlang="no";
include_once('setconfig.inc.php');
include_once('modules/news/module.php');

$nws=new News();

$rss = new UniversalFeedCreator();
$rss->encoding="utf-8";
$rss->useCached();
$rss->title = $cfg['title'];
$rss->description = "Daily News from ".$cfg['title'];
$rss->link = $cfg['url']."/module.php?modname=news";
$rss->syndicationURL = $cfg['url'].(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/feed.php');

$image = new FeedImage();
$image->title = $cfg['title']." logo";
$image->url = $cfg['url']."/images/xmlcoffeemug.gif";
$image->link = $cfg['url'];
$image->description = "Feed provided by ".$cfg['title']." Click to visit.";
$rss->image = $image;

$news=$nws->getShowNews(10);

while(!$news->EOF){
    $item = new FeedItem();
    $item->title = $news->fields['nwsTitle'];
    $item->link = $cfg['url'].(("/module.php?modname=news&mf=nwsview&cid=")).$news->fields['nwsId'];
    $words = $news->fields['nwsPreface'];
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
    $item->date = adodb_date2("r",$news->fields['nwsCreate']);
    $item->source = $cfg['url'];
    $item->author = "";
    $rss->addItem($item);
    $news->movenext();
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
