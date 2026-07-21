#!/usr/bin/php -q
<?php
/* Load config */
@set_time_limit(900);
include_once('../../config.inc.php');
include_once('../../include/adodb/adodb.inc.php');
include_once('module.php');
$db = NewADOConnection("mysqli://".$dbuser.":".$dbpw."@".$dbhost."/".$dbname);
if (!$db) die("Connection failed");   

$bup=new DBBackup();

$tablearr=fetchTables();
if ((count($tablearr)) > 0) {
        for ($i=0;$i<(count($tablearr));$i++) {
          $sqlStr.=$bup->BackUpTable($tablearr[$i],true,true);
        }
        saveBackup($sqlStr);
}


function saveBackup($data) {
	global $cfg_datadir,$sys_lanai;
	$filename=date("YmdHis");
	$fullname=$cfg_datadir."/backup/".$filename;
	$fp=fopen($fullname,"w");
	fwrite($fp,$data);
	fclose($fp);
}

function fetchTables() {
	global $db;
	//$db->debug=1;
	$sql="SHOW tables";
	$rs=$db->execute($sql);
	$tablearr=array();
	while (!$rs->EOF) {
		array_push($tablearr,$rs->fields[0]);
		$rs->movenext();
	}
	return $tablearr;
}

