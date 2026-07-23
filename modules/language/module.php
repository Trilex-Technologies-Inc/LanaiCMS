<?php
/**
	 * Module
	 * 
	 * @package 
	 * @author Administrator
	 * @copyright Copyright (c) 2006
	 * @version $Id: module.php,v 1.1 2007/03/23 12:37:35 redlinesoft Exp $
	 * @access public
	 **/
	class Language {
		
		var $uid;
		var $db;
		var $cfg;
		var $_sql;		
		
		
		function __construct() {
			global $db,$cfg;
			$this->db=$db;
			$this->cfg=$cfg;
			$this->uid=$_SESSION['uid'];
			//$this->db->debug=true;		
		}
		
		function getLanguage() {
			if ($handle = opendir('language/')) {
				$i=0;
			   while (false !== ($file = readdir($handle))) { 
			  		//if ($file != "." && $file != ".."  && !is_file($file) && file_exists("language/".$file."/theme.php")) {
			   		if ($file != "." && $file != ".."  && !is_file($file)) {
						$arTheme[$i]=$file;
						$i++;
					}
			   }
			   closedir($handle); 
			}
			return ($arTheme);
		}
		
		function getCurrentLanguage() {
			$lines = file('config.inc.php');
			foreach ($lines as $line) {
			    if (stripos($line, 'cfg_lang=') !== false) {
					list($key,$value)=explode("=",$line,2);
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
				if (stripos($line, 'cfg_lang=') !== false) {
					return $i;
				}
			}
		}
		
		function setUpdateLanguage($tname){
			$lines = file('config.inc.php');			
			$lines[$this->_get_line()]="\t$"."cfg_lang=\"".$tname."\"".";\n";
			$handle = fopen('config.inc.php', "w+");
			foreach ($lines as $i => $line) {
				fwrite($handle, $line);
			}
			fclose($handle);

			//echo "$"."cfg_theme=\"default\"".";";
		}
		

	}

?>