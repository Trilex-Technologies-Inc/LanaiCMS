<?php

include_once("include/feedcreater/feedcreator.class.php");
include_once("include/lanai/class.navigator.php");
$loadlang="no";
include_once("setconfig.inc.php");
include_once('modules/sitemap/module.php');

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = $cfg['title'];
$rss->description = "Site Map ".$cfg['title'];
$rss->link = $cfg['url']."/module.php?modname=sitemap";
$rss->syndicationURL = $cfg['url'].(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/sitemap.php');

$image = new FeedImage();
$image->title = $cfg['title']." logo";
$image->url = $cfg['url']."/images/xmlcoffeemug.gif";
$image->link = $cfg['url'];
$image->description = "Feed provided by ".$cfg['title']." Click to visit.";
$rss->image = $image;

 $site= new SiteMap();
 list($menuitem,$menulink)=$site->feed_render();
 $i=0;
foreach ($menuitem as $mitem) {
    $item = new FeedItem();
    $item->title = $mitem;
    $item->link =$menulink[$i];
    $item->description = "";
    $item->date = date("r");
    $item->source = $cfg['url'];
    $item->author = "";
    $rss->addItem($item);
    $i++;
}

$rss->saveFeed("ATOM",$cfg['datadir']."/sitemap.xml");

?>
