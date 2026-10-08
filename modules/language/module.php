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
			return array_map('basename', glob(__DIR__.'/../../language/lang-*.php') ?: array());
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
			if (!is_string($tname) || !preg_match('/^[a-zA-Z0-9_-]+$/D', $tname)
				|| !in_array('lang-'.$tname.'.php', $this->getLanguage(), true)) {
				throw new InvalidArgumentException('Unknown interface language.');
			}
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
