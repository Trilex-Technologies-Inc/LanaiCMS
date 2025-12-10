<?php
$timer = new phpTimer();
$timer->start('main');

$modname = isset($_REQUEST['modname']) ? $_REQUEST['modname'] : null;

$description = is_array($obMeta->mtadescription) ? implode(', ', $obMeta->mtadescription) : $obMeta->mtadescription;
$abstract = is_array($obMeta->mtaabstract) ? implode(', ', $obMeta->mtaabstract) : $obMeta->mtaabstract;
$author = is_array($obMeta->mtaauthor) ? implode(', ', $obMeta->mtaauthor) : $obMeta->mtaauthor;
$distribution = is_array($obMeta->mtadistribution) ? implode(', ', $obMeta->mtadistribution) : $obMeta->mtadistribution;
$keywords = is_array($obMeta->mtakeywords) ? implode(', ', $obMeta->mtakeywords) : $obMeta->mtakeywords;

$sys_lanai->loadAjaxFunction($modname);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= _CHARSET; ?>" />
    <meta http-equiv="expires" content="0">
    <meta name="description" content="<?= $description ?>" />
    <meta name="abstract" content="<?= $abstract ?>" />
    <meta name="author" content="<?= $author ?>" />
    <meta name="distribution" content="<?= $distribution ?>" />
    <meta name="keywords" content="<?= $keywords ?>" />
    <meta name="copyright" content="Copyright 2007 redline software">
    <meta name="generator" content="Lanai Core - Copyright 2006 Lanai Core Content Management Framework.  All rights reserved." />
    <meta name="robots" content="FOLLOW,INDEX">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="alternate" type="application/rss+xml" title="<?= $cfg['title']; ?> - RSS Feed" href="<?= $cfg['url']; ?>/feed.php" />
    <link rel="alternate" type="application/atom+xml" title="<?= $cfg['title']; ?> - Atom" href="<?= $cfg['url']; ?>/feed.php?feed=ATOM"  />
    <title><?= $cfg_title; ?></title>
    <link href="theme/<?= $cfg_theme; ?>/style/style.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <?php
    $sys_lanai->loadAjaxCode($modname);
    include_once("include/mmscript/mm_script.js");
    ?>

    <link rel="stylesheet" type="text/css" media="all" href="include/jscalendar/calendar-brown.css" title="win2k-cold-1" />
    <script type="text/javascript" src="include/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="include/jscalendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="include/jscalendar/calendar-setup.js"></script>
</head>
<body>
