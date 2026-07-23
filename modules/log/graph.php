<?php

include_once('../../config.inc.php');
include_once('../../include/lanai/php_compat.php');
include_once('../../include/adodb/adodb.inc.php');
include_once ("../../include/jpgraph2/jpgraph.php");
include_once ("../../include/jpgraph2/jpgraph_bar.php");
$ADODB_CACHE_DIR=$cfg_dir."/cache/";
$db=ADONewConnection(lanai_normalize_dbtype($dbtype));
$db->NConnect($dbhost, $dbuser, $dbpw, $dbname);
$cfg['tablepre']=$tablepre;
include_once("module.php");

// Some data
$ydata1 = array();
$ydata2 = array();
$datax = array();

$oblog=new SysLog();
$rs=$oblog->loadLastStat(7);

while (!$rs->EOF) {
	array_push($ydata2,$rs->fields['statVisit']);
	array_push($ydata1,$rs->fields['statHit']);
	array_push($datax,adodb_date2("d M y",$rs->fields['statDate']));
	$rs->movenext();
}

// Create the graph. These two calls are always required
$graph = new Graph(640,280,"auto");    
$graph->SetScale("textlin");
$graph->SetScale("textlin");
$graph->SetMarginColor('white');
$graph->SetFrame(false);

$graph->img->SetMargin(50,10,10,55);

// Create the bar plots
$b1plot = new BarPlot($ydata1);
$b1plot->SetFillColor("orange");
$b2plot = new BarPlot($ydata2);
$b2plot->SetFillColor("blue");

// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
//$gbplot->SetWidth(0.2);
// ...and add it to the graPH
$graph->Add($gbplot);

$b1plot->SetLegend("Hits"); 
$b2plot->SetLegend("Unique Sessions"); 

// Legent Position
$graph->legend->Pos(0.30,0.90,"right" ,"top");
$graph->SetFrame(false);
$graph->legend->SetColumns(3);
$graph->legend->SetShadow(false); 
$graph->legend->SetFillColor('white');
$graph->legend->SetFrameWeight(0);

$graph->xaxis->SetTickLabels($datax);
//$graph->title->Set("Adjusting the width");
//$graph->xaxis->title->Set("X-title");
//$graph->yaxis->title->Set("Y-title");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Display the graph
$graph->Stroke();
?>
