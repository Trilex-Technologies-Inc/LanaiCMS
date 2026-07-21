<?php
	include_once("class.ModulePager.php");
	
	/**
	 * Module
	 * 
	 * @package 
	 * @author Administrator
	 * @copyright Copyright (c) 2006
	 * @version $Id: module.php,v 1.1 2007/03/23 12:37:36 redlinesoft Exp $
	 * @access public
	 **/
	class Module {
		
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
		
		function getModule(){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."module 
					ORDER BY modTitle ASC";
			$this->_sql=$sql;			
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function getModuleById($mid){
			$sql="SELECT * FROM ".$this->cfg['tablepre']."module 
					WHERE modId=$mid";
			$rs=$this->db->execute($sql);	
			return $rs;
		}
		
		function setModuleActive($mid,$value){
			$sql="UPDATE ".$this->cfg['tablepre']."module 
					SET modActive='".$value."'
					WHERE modId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;		
		}
		
		function getModuleList($rows=30){
			$this->getModule();
			$pager=new ModulePager($this->db,$this->_sql,true);
			$pager->Render($rows);
		}
		
		function setDeleteModule($mid){
			$sql="DELETE FROM ".$this->cfg['tablepre']."module 
					WHERE modId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;	
		}
		
		function setEditModule($mid,$modTitle){
			$sql="UPDATE ".$this->cfg['tablepre']."module 
					SET modTitle='".$modTitle."' 
					WHERE modId=".$mid;
			$rs=$this->db->execute($sql);	
			return $rs;	
		}
		
		function setNewModule($method,$prefix,$userfile,$zippath){
			//global $db,$table_modules,$modname,$themesel,$cfgBasePath,$cfgPackagePath;
			global $sys_lanai;
			
			$modpath=$this->cfg['dir'].$sys_lanai->getPath()."modules/";
			
			$cfgPackagePath=$this->cfg['packdir'].$sys_lanai->getPath();
			//echo $modpath."<br>";
			//echo $cfgPackagePath.$prefix.$_FILES['userfile']['name']."<br>";
			
			switch($method){
				case 1: 
					if (move_uploaded_file($_FILES['userfile']['tmp_name'], $cfgPackagePath.$prefix.$_FILES['userfile']['name'])) {
						//echo "uploaded";
						include('include/pclzip/pclzip.lib.php');
						$moddir=substr($_FILES['userfile']['name'],0,(strlen($_FILES['userfile']['name'])-4));
						$archive = new PclZip($cfgPackagePath.$prefix.$_FILES['userfile']['name']);
						if ($archive->extract(PCLZIP_OPT_PATH, $modpath)){
						 	$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=$moddir&mf=install") ;
						} else {
							echo "Cannot extract file to module directory, please check directory permission.";
						}						
						
						unlink ($cfgPackagePath.$prefix.$_FILES['userfile']['name']);
						
		
					} else {
						echo "cannot upload";
					}					
					break;
				case 2: 
					include('include/pclzip/pclzip.lib.php');
						$moddir=basename($zippath, ".zip"); 
						$archive = new PclZip($zippath);
						if ($archive->extract(PCLZIP_OPT_PATH, $modpath)){
						 	$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=$moddir&mf=install");
						} else {
							echo "Cannot extract file to module directory, please check directory permission.";
						}				
						unlink ($zippath);
					break;		
				case 3: 
					$moddir=$_REQUEST['modpath'];
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=$moddir&mf=install");
					break;				
			} // switch
			
		}
		
		
		
		
	}
?>