<?php

class DBBackup {
	var $db;
	var $cfg;
	var $end="\n";

	function __construct() {
		global $cfg,$db;
		$this->db=$db;
		$this->cfg=$cfg;
		//$this->db->debug=true;
	}

	function isLanaiTable($table) {
	    return strstr($table,$this->cfg['tablepre']);
	}

	function ListTable(){
    	$sql="SHOW TABLES";
    	$rs=$this->db->execute($sql);
    	return $rs;
	}
	
	function BackUpTable($table,$schema=false,$value=false) {
      $dumpSQL="";
      if ($schema) {
        // create with schema
        $tableSQL=$this->_CreateTable($table);
        $dumpSQL.=$tableSQL;
      }
      if ($value){
        // create only insert value
        $insertSQL=$this->_InsertTable($table);
        $dumpSQL.=$insertSQL;
      }
	  return $dumpSQL;
	}

    function _CreateTable($table){
        $this->db->execute("LOCK TABLES ".$table." WRITE");
		$getDumpTable .= "<query>DROP TABLE IF EXISTS ".$table.$this->end;
		$result = $this->db->execute("SHOW CREATE TABLE ".$table);
        $getDumpTable .= "<query>".str_replace("\n",$this->end, $result->fields[1]).$this->end;
        $this->db->execute("UNLOCK TABLES");
        return $getDumpTable;
    }

    function _InsertTable($table) {
        $this->db->execute("LOCK TABLES ".$table." WRITE");
        $rs=$this->db->execute("SELECT * FROM ".$table);
        $fcnt = $rs->FieldCount();
        $insertSQL="";
        while (!$rs->EOF) {
            $fields="";
            for ($i=0;$i<$fcnt;$i++) {
                $fields.=$this->db->qstr($rs->fields[$i]).", ";
            }
            $fields = substr($fields, 0, -2);
            $insertSQL.="<query>INSERT INTO ".$table." VALUES (".$fields.")".$this->end;
            $rs->movenext();
        }
        $this->db->execute("UNLOCK TABLES");
        return $insertSQL;
    }

    function SaveFile($sqlData,$filename){
        $fp=fopen($this->cfg['datadir']."/backup/".$filename,"w+");
        fputs($fp,$sqlData);
        fclose($fp);
    }

    function getBackupFile() {
    	if ($handle = opendir($this->cfg['datadir']."/backup/")) {
    		$i=0;
    	   while (false !== ($file = readdir($handle))) {
    	   		if ($file != "." && $file != ".." && !is_dir($file) ) {
    				$arFile[$i]=$file;
    				$i++;
    			}
    	   }
    	   closedir($handle);
    	}
    	return ($arFile);
    }

    function getFileSize($file){
		$file_size = filesize($this->cfg['datadir']."/backup/".$file);
		if($file_size >= 1073741824)
			{
				$file_size = round($file_size / 1073741824 * 100) / 100 . "g";
			}
		elseif($file_size >= 1048576)
			{
				$file_size = round($file_size / 1048576 * 100) / 100 . "m";
			}
		elseif($file_size >= 1024)
			{
				$file_size = round($file_size / 1024 * 100) / 100 . "k";
			}
		else{
				$file_size = $file_size . "b";
			}
		return $file_size;
	}

    function getModifiedTime($file){
        return filemtime($this->cfg['datadir']."/backup/".$file);
    }


}

class DBRestore {
	var $db;
	var $cfg;

    function __construct ($fname) {
        global $cfg,$db;
		$this->db=$db;
		$this->cfg=$cfg;
        //$this->db->debug=true;

        $this->_restore($fname);

    }

    function _Restore($fname){
        $filename=$this->cfg['datadir']."/backup/".$fname;
        $fp = fopen($filename, "r");
        $contents = fread($fp, filesize($filename));
        $contents = str_replace("\r\n","", $contents);
        $exploe = explode("<query>",$contents);
        $nExploe = sizeof($exploe);
        for($n=1;$n<$nExploe;$n++){
		    //$mysql_escape = mysql_escape_string($exploe[$n]);
            $query=$this->db->execute($exploe[$n]);
            if($query) $a = $a+1;
			if(!$query) {
                $b = $b+1;
                echo $this->db->ErrorMsg()."<br/>";
            }
        }
        echo _EXECUTE." ".$n." "._ROWS;
        if ($b>0) echo " : "._ERROR." ".$b." "._ROWS;
    }

}




?>
