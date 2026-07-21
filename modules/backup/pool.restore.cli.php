#!/usr/bin/php -q
<?php
/* Load config */
@set_time_limit(900);
include_once('../../config.inc.php');
include_once('../../include/adodb/adodb.inc.php');
include_once('module.php');
$db = NewADOConnection("mysqli://".$dbuser.":".$dbpw."@".$dbhost."/".$dbname);
if (!$db) die("Connection failed");   

/*  replace your data backup file here!!! */
$datafile="";

$filename= $cfg_datadir."\\backup\\".$datafile;

$fp = fopen($filename, "r");
$contents = fread($fp, filesize($filename));
$contents = str_replace("\r\n","", $contents);
$exploe = explode("<query>",$contents);
$nExploe = sizeof($exploe);
for($n=1;$n<$nExploe;$n++){
	//$mysql_escape = mysql_escape_string($exploe[$n]);
	$query=$db->execute($exploe[$n]);
	if($query) $a = $a+1;
		if(!$query) {
		      $b = $b+1;
		      echo $db->ErrorMsg()."<br/>";
		  }
	}
echo "Execute : ".$n." rows";
if ($b>0) echo " Error : ".$b." rows";


?>
