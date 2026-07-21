#!/usr/bin/php -q
<?php


/* Load config */
@set_time_limit(900);
include_once('config.inc.php');
include_once('include/lanai/php_compat.php');
include_once('include/adodb/adodb.inc.php');
$db = NewADOConnection(lanai_normalize_dbtype($dbtype) . "://" . $dbuser . ":" . $dbpw . "@" . $dbhost . "/" . $dbname);
if (!$db) die("Connection failed");   
/* Greeting welcome to module */
echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
echo "\n";
echo " Make Lanai Core 0.5 Module Generator \n\n";
echo " Filename : lamud.cli.php \n";
echo " Version  : 1.2 \n";
echo " Modified : 2006-12-15\n";
echo " Author   : Anoochit Chalothorn <anoochit@gmail.com> \n";
echo " License  : GNU/GPL.\n";
echo "\n";
echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";

/* Define STDIN if not already done so */
if(!defined("STDIN")) {
define("STDIN", fopen('php://stdin','r'));
}

/* do argument */
switch ($_SERVER['argv'][1]) {
  case "-m" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makemod($modname);
  break;
  case "-r" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makelist($modname);
  break;
  case "-c" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makecreate($modname);
  break;
  case "-i" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makeinstall($modname);
  break;
  case "-u" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makeupdate($modname);
  break;
  case "-d" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_makedelete($modname);
  break;
   case "-j" :
    $modname=$_SERVER['argv'][2];
    $modname=trim($modname);
    lamud_make_ajax_function($modname);
  break;
  default:
    echo "lamud.cli.php [Options]\n\nOptions:\n\n-m <module name>\tCreate module directory and necessary files.\n-c <module name>\tCreate add script file.\n-r <module name>\tCreate list script file.\n-u <module name>\tCreate update script file.\n-d <module name>\tCreate delete script file.\n-j <module name>\tCreate ajax function script file.\n-i <module name>\tCreate installation script file.\n";
}

/* make ajax function */
function lamud_make_ajax_function($modname){
	global $db;
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024);
      $modname=trim($modname);
    }    
    	/* load pattern file*/
	$pattstr="";
	$lines = file("lamud/ajaxfunction.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
    	/* replace pattern */
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$fp=fopen("modules/".$modname."/ajaxfunction.class.php","w+");
	fputs($fp,$pattstr);
	fclose($fp);
	echo " -> Ajax Function script created.\n";
}
/* make delete file */
function lamud_makedelete($modname){
	global $db;
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024);
      $modname=trim($modname);
    }
	echo "Enter your table name : ";
	$tblname = fread(STDIN, 1024);
	$tblname=trim($tblname);
	echo "Enter your class name : ";
	$clssname = fread(STDIN, 1024); 
	$clssname=trim($clssname);
	echo "Enter primary key column name : ";
	$prikey = fread(STDIN, 1024);
	$prikey=trim($prikey);
	/* load pattern file*/
	$pattstr="";
	$lines = file("lamud/delete.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
	/* replace pattern */
	$pattstr=str_replace("%CLASS%",$clssname, $pattstr);
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$pattstr=str_replace("%PRIKEY%",$prikey, $pattstr);
	$fp=fopen("modules/".$modname."/delete.php","w+");
	fputs($fp,$pattstr);
	fclose($fp);
	echo " -> Delete script created.\n";
}
/* make update file */
function lamud_makeupdate($modname){
	global $db;
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024);
      $modname=trim($modname);
    }
	echo "Enter your table name : ";
	$tblname = fread(STDIN, 1024);
	$tblname=trim($tblname);
	echo "Enter primary key column name : ";
	$prikey = fread(STDIN, 1024);
	$prikey=trim($prikey);
	echo "Enter your class name : ";
	$clssname = fread(STDIN, 1024); 
	$clssname=trim($clssname);
	/* create value */
	$rs=$db->execute("SELECT * FROM ".$tblname." LIMIT 1");
	if (!$rs) die("Query failed");   
	$fieldCount=$rs->FieldCount();
	$fieldArr=array();
	for ($i=0;$i<$fieldCount;$i++) {
		$field = $rs->FetchField($i);
		$formFields.="<tr><td>".$field->name." : </td><td><input type=\"text\" id=\"".$field->name."\" name=\"".$field->name."\" value=\"<?="."$"."obj".$clssname."->".strtolower($field->name).";?>\"></td></tr>\n";
		$objectFields.="$"."obj".$clssname."->".strtolower($field->name)."=$"."_REQUEST['".$field->name."'];\n";
	}
	/* load pattern file*/
	$pattstr="";
	$lines = file("lamud/edit.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
	/* replace pattern */
	$pattstr=str_replace("%CLASS%",$clssname, $pattstr);
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$pattstr=str_replace("%FORMFIELDS%",$formFields, $pattstr);
	$pattstr=str_replace("%OBJFIELDS%",$objectFields, $pattstr);
	$pattstr=str_replace("%PRIKEY%",$prikey, $pattstr);
	$fp=fopen("modules/".$modname."/edit.php","w+");
	fputs($fp,$pattstr);
	fclose($fp);
	echo " -> Edit script created.\n";
}
/* make install file */
function lamud_makeinstall($modname){
	global $db;
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024);
      $modname=trim($modname);
    }
    echo "Enter menu title for ".$modname." module : ";
	$modtitle = fread(STDIN, 1024);
	$modtitle=trim($modtitle);
    /* load pattern file*/
	$pattstr="";
	$lines = file("lamud/install.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
    /* replace pattern */
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$pattstr=str_replace("%MODULEMENU%",$modtitle, $pattstr);
    if (file_exists("modules/".$modname."/setting")) {
        $fp=fopen("modules/".$modname."/setting/install.php","w+");
    	fputs($fp,$pattstr);
    	fclose($fp);
        echo " -> Installation script created.\n";
    } else {
        echo " -> Module 'setting' directory may not exist!\n";
    }

}
/* make create file */
function lamud_makecreate($modname){
	global $db;
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024);
      $modname=trim($modname);
    }
	echo "Enter your table name : ";
	$tblname = fread(STDIN, 1024);
	$tblname=trim($tblname);
	echo "Enter your class name : ";
	$clssname = fread(STDIN, 1024); 
	$clssname=trim($clssname);
	$rs=$db->execute("SELECT * FROM ".$tblname." LIMIT 1");
	if (!$rs) die("Query failed");   
	$fieldCount=$rs->FieldCount();
	$fieldArr=array();
	for ($i=0;$i<$fieldCount;$i++) {
		$field = $rs->FetchField($i);
		$formFields.="<tr><td>".$field->name." : </td><td><input type=\"text\" id=\"".$field->name."\" name=\"".$field->name."\" ></td></tr>\n";
		$objectFields.="$"."obj".$clssname."->".strtolower($field->name)."=$"."_REQUEST['".$field->name."'];\n";
	}
	/* load pattern file*/
	$pattstr="";
	$lines = file("lamud/create.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
	/* replace pattern */
	$pattstr=str_replace("%CLASS%",$clssname, $pattstr);
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$pattstr=str_replace("%FORMFIELDS%",$formFields, $pattstr);
	$pattstr=str_replace("%OBJFIELDS%",$objectFields, $pattstr);
	$fp=fopen("modules/".$modname."/add.php","w+");
	fputs($fp,$pattstr);
	fclose($fp);
	echo " -> Add script created.\n";
}

/* make list file */
function lamud_makelist($modname){
	if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024); 
      $modname=trim($modname);
    }
	echo "Enter your class name : ";
	$clsname = fread(STDIN, 1024); 
	$clsname=trim($clsname);
	/* load pattern file*/
	$pattstr="";
	$lines = file("lamud/list.pat");
	foreach ($lines as $item) {
		$pattstr.=$item;
	}
	/* replace pattern */
	$pattstr=str_replace("%CLASS%",$clsname, $pattstr);
	$pattstr=str_replace("%MODULE%",$modname, $pattstr);
	$fp=fopen("modules/".$modname."/list.php","w+");
	fputs($fp,$pattstr);
	fclose($fp);
}

/* make module function */
function lamud_makemod($modname) {
    global $cfg_lang;
    if (empty($modname)) {
      echo "Enter your module name : ";
      $modname = fread(STDIN, 1024); // Read up to 1024 characters or a newline
      $modname=trim($modname);
    }
	if (!file_exists("modules/".$modname)) {

        echo " -> Make module directory '".$modname."'";
        mkdir("modules/".trim($modname),0755);

        echo " -> Make index file '".$modname."/index.php' \n";
        touch("modules/".$modname."/index.php");

        echo " -> Make base-class file '".$modname."/module.php' \n";
        touch("modules/".$modname."/module.php");

        echo " -> Make setting directory '".$modname."' \n";
        mkdir("modules/".$modname."/setting",0755);

        echo " -> Make setting index file '".$modname."/setting/index.php' \n";
        touch("modules/".$modname."/setting/index.php");

        echo " -> Make language directory '".$modname."/language' \n";
        mkdir("modules/".$modname."/language",0755);

        echo " -> Make lang-english file '".$modname."/language/lang-".$cfg_lang.".php' \n";
        touch("modules/".$modname."/language/lang-".$cfg_lang.".php");

        echo "Do you want to create active record ? (y,n) : ";
        $ans = fread(STDIN, 3);
        $ans=trim($ans);

        if ($ans=="y") {
          echo " -> Create active record' \n";
          // create active object
          $ao="<?php\n\nrequire_once('include/adodb/adodb-active-record.inc.php');\nglobal "."$"."db;\n\nADOdb_Active_Record::SetDatabaseAdapter("."$"."db);\n";
          $fp=fopen("modules/".$modname."/module.php","a+");
          fputs($fp,$ao);
          while ($aitem!="n") {
            echo "Enter table name : ";
            $atbale = fread(STDIN, 50);
            $atbale=trim($atbale);
            echo "Enter class name : ";
            $aclass = fread(STDIN, 50);
            $aclass=trim($aclass);
            fputs($fp,"\n/*\tClass ".$aclass."\t*/\n");
            $at="class ".$aclass." extends ADOdb_Active_Record{\n\tvar "."$"."_table = '".$atbale."';\n}\n";
            fputs($fp,$at);
            echo "Do you have other tables ? (y,n) : ";
            $aitem= fread(STDIN, 3);
            $aitem=trim($aitem);
          }
          fputs($fp,"?>");
          fclose($fp);
        }

    } else {
        echo "Module exist!";
    }
}


?>
