<?php

class SysLog {
	var $cfg;
	var $db;
	
	function __construct(){
		global $db,$cfg;
		$this->db=$db;
		$this->cfg=$cfg;
		//$this->db->debug=true;
	}
	
	function getCounter(){
		$sql="SELECT SUM(statVisit) FROM  ".$this->cfg['tablepre']."log_stat ";
		$rs=$this->db->execute($sql);
		return ($rs->fields[0]);
	}
	
	function loadLastStat($day) {
		$sql="SELECT * FROM  ".$this->cfg['tablepre']."log_stat WHERE statDate BETWEEN '".date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-$day, date("Y")))."' AND '".date("Y-m-d")."'";
		$rs=$this->db->execute($sql);
		return $rs;
	}
		
	function loadLastLog($limit) {
		$sql="SELECT * FROM  ".$this->cfg['tablepre']."log,".$this->cfg['tablepre']."log_page 
					WHERE  ".$this->cfg['tablepre']."log.pagId=".$this->cfg['tablepre']."log_page.pagId 
					ORDER BY logDatetime DESC 
					LIMIT ".$limit;
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	function setLog() {
		// get date
		$datestr=date("Y-m-d H:i:s");
		$datestr2=date("Y-m-d");
		// check page exist
		if (!$this->pageExist($_SERVER['REQUEST_URI'])) {
			// add page
			if (stripos($_SERVER['REQUEST_URI'], "setting.php") === false) {
				$this->addPage($this->cfg['title'],$_SERVER['REQUEST_URI']);
			}
		}
		// add log 2 file
		global $cfg;
		$fp=fopen($cfg['datadir']."/log/".date("Ymd")."_access.log","a+");
		fwrite($fp,date("YmdHis")."\t".$_SERVER['REMOTE_ADDR']."\t".$_SERVER['REQUEST_URI']."\n");
		fclose($fp);
		// add log
		if (stripos($_SERVER['REQUEST_URI'], "setting.php") === false) {
				// get page id
				$pageid=$this->getPageID($_SERVER['REQUEST_URI']);
				// get log stat
				$logstat=$this->logStat($datestr);
				$this->addLog($datestr,$pageid,$logstat);
				// update
				$this->updateStat($logstat,$datestr2);
		}
	}
	
	function updateStat($state,$datestr2) {
		if (!$this->isStatExist($datestr2)){
			$this->addStat($datestr2);
		}
		if ($state=="h") {
			// if state exist get count hit
			$count=$this->getStatHit($datestr2);
			$sql="UPDATE  ".$this->cfg['tablepre']."log_stat 
						SET  statHit=".($count+1)."
						WHERE statDate='".$datestr2."'";
			$rs=$this->db->execute($sql);
		} else {
			// if state exist get count visit
			$count=$this->getStatVisit($datestr2);
			$sql="UPDATE  ".$this->cfg['tablepre']."log_stat 
						SET  statVisit=".($count+1)."
						WHERE statDate='".$datestr2."'";
			$rs=$this->db->execute($sql);
		}
	}
	
	function addStat($date) {
		$sql="INSERT INTO ".$this->cfg['tablepre']."log_stat  (statDate, statHit, statVisit) 
					VALUES ('".$date."',0,0)";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	function isStatExist($date){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."log_stat 
					WHERE statDate='".$date."'";
		$rs=$this->db->execute($sql);
		if (($rs->recordcount())>0) {
			return true;
		} else {
			return false;
		}
	}
	
	function getStatVisit($date){
		$sql="SELECT statVisit FROM ".$this->cfg['tablepre']."log_stat 
					WHERE statDate='".$date."'";
		$rs=$this->db->execute($sql);
		return ($rs->fields[0]);
	}
	
	function getStatHit($date) {
		$sql="SELECT statHit FROM ".$this->cfg['tablepre']."log_stat 
					WHERE statDate='".$date."'";
		$rs=$this->db->execute($sql);
		return ($rs->fields[0]);
	}
	
	function logStat($datefrom){
		// get last log
		$rs=$this->getLastLog();
		// load class datetime
		include_once("include/lanai/class.datetime.php");
		$obdate=new SysDateTime();
		if (($obdate->dateDiff("n",$rs->fields[0],$datefrom))>30){
			$state="v";
		} else {
			$state="h";
		}
		return $state;
	}
	
	function getLastLog() {
		$sql="SELECT MAX(logDatetime) FROM ".$this->cfg['tablepre']."log 
					WHERE logIP='".$_SERVER['REMOTE_ADDR']."' LIMIT 1";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	function pageExist($uri){
		$sql="SELECT * FROM ".$this->cfg['tablepre']."log_page WHERE pagURL='".$uri."' ";
		$rs=$this->db->execute($sql);
		if ($rs->recordcount($sql)>0) {
			return true;
		} else {
			return false;
		}		
	}
	
	function addPage($title,$uri) {
		$sql="INSERT INTO ".$this->cfg['tablepre']."log_page (pagTitle, pagUrl) 
				VALUES ('".$title."','".$uri."')";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	function getPageID($uri) {
		$sql="SELECT * FROM ".$this->cfg['tablepre']."log_page WHERE pagURL='".$uri."' ";
		$rs=$this->db->execute($sql);
		return ($rs->fields['pagId']);
	}
	
	function addLog($date,$pageid,$logstat){
		$sql="INSERT INTO ".$this->cfg['tablepre']."log (logDatetime, logUAgent, logIP, pagId, logState)
				VALUES ('".$date."','".$_SERVER['HTTP_USER_AGENT']."','".$_SERVER['REMOTE_ADDR']."',".$pageid.",'".$logstat."')";
		$rs=$this->db->execute($sql);
		return $rs;
	}
	
	
}

?>