<?php

global $db;

ADOdb_Active_Record::SetDatabaseAdapter($db);

/*	Class Meta	*/
class Meta extends ADOdb_Active_Record {
	var $_table = 'tbl_ln_meta';
    function updateSetting($data = []) {
        global $db; // ADOdb connection


        $setParts = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $params[] = $value;
        }


        if (empty($setParts)) {
            return false;
        }
        $params[] = 1; // mtaId=1

        $sql = "
            UPDATE {$this->_table}
            SET " . implode(", ", $setParts) . "
            WHERE mtaId = ?
        ";

        $result = $db->Execute($sql, $params);

        return $result ? true : false;
    }
}

class SysConfig {
	
	function getCurrentStatus() {
			$lines = file('config.inc.php');
			foreach ($lines as $line) {
			    if (eregi('cfg_off=', $line)) {
					list($key,$value)=split("=",$line,2);
					$value=trim($value);
					$valuex=ltrim($value,"\"");
					$valuex=substr($valuex,0,strlen($valuex)-2);
					return $valuex;
				}
			}
	}
	
	function _get_line(){
		$lines = file('config.inc.php');			
		foreach ($lines as $i => $line) {
			if (eregi('cfg_off=', $line)) {
				return $i;
			}
		}
	}
    function _get_title_line(){
        $lines = file('config.inc.php');
        foreach ($lines as $i => $line) {
            if (strpos($line, '$cfg_title') !== false) {
                return $i;
            }
        }
        return false;
    }
    function setSiteTitle($title){
        $lines = file('config.inc.php');
        $lineNumber = $this->_get_title_line();

        $lines[$lineNumber] = "\t$"."cfg_title=\"".$title."\";\n";

        $handle = fopen('config.inc.php', "w+");
        foreach ($lines as $line) {
            fwrite($handle, $line);
        }
        fclose($handle);
    }

    function setUpdateStatus($tname){
		$lines = file('config.inc.php');
		$lines[$this->_get_line()]="\t$"."cfg_off=\"".$tname."\"".";\n";
		$handle = fopen('config.inc.php', "w+");
		foreach ($lines as $i => $line) {
			fwrite($handle, $line);
		}
		fclose($handle);
	}

	function configIsWrite(){
		if (is_writable('config.inc.php')) {
			return true;
		} else {
			return false;
		}		
	}
	
}

?>