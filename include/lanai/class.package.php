<?php


class Package {

var $db;
var $cfg;
var $mid;
var $syslanai;

/* constructore */
function __construct() {
    global $db,$cfg,$sys_lanai;
    $this->db=$db;
    $this->cfg=$cfg;
    $this->syslanai=$sys_lanai;
}

/* create table */
function execQuery($sql) {
    if (!empty($sql)){
        $rs=$this->db->execute($sql);
        return $rs;
    }
}

/* insert module */
function setupModule($modname) {
    /* is module exist delete all data */
    $sql="SELECT COUNT(*),modId FROM ".$this->cfg['tablepre']."module
            WHERE modName='".$modname."' GROUP BY modId";
    $rs=$this->db->execute($sql);
    if (($rs->fields[0])>0) {
    // delete from module table
    $sql="DELETE FROM ".$this->cfg['tablepre']."module
            WHERE modId=".$rs->fields[1];
    $this->db->execute($sql);
    // delete from menu table
    $sql="DELETE FROM ".$this->cfg['tablepre']."menu
            WHERE modId=".$rs->fields[1]." AND mnuType='m'";
    $this->db->execute($sql);
    // delete from privilege table
    $sql="DELETE FROM ".$this->cfg['tablepre']."privilege
            WHERE modId=".$rs->fields[1];
    $this->db->execute($sql);
    }
    /* check max order */
  	$sql="SELECT MAX(modOrder) FROM ".$this->cfg['tablepre']."module ";
	$rsOModule=$this->db->execute($sql);
    /* insert data */
    $sql="INSERT INTO ".$this->cfg['tablepre']."module
					(modTitle,modName,modActive,modOrder,modSetting)
					VALUES ('".$modname."','".$modname."','y',".(($rsOModule->fields[0])+1).",'y')";
	$rs1=$this->db->execute($sql);
    $this->errExecute("Insert module data '".$modname."'.",$rs1);
    /* select max module */
    $sql="SELECT COUNT(*),modId FROM ".$this->cfg['tablepre']."module
    	    WHERE modName='".$modname."' GROUP BY modId";
    $rsIModule=$this->db->execute($sql);
    $this->mid=$rsIModule->fields[1];
    return $rs1;
}

/* insert menu */
function setupMenu($modname,$menutitle) {
    /* select max menu */
    $sql="SELECT COUNT(*),modId FROM ".$this->cfg['tablepre']."module
    	    WHERE modName='".$modname."' GROUP BY modId";
    $rsIModule=$this->db->execute($sql);
    if (($rsIModule->recordcount())>0){
      $this->mid=$rsIModule->fields[1];
      /* select max menu */
      $sql="SELECT MAX(mnuOrder) FROM ".$this->cfg['tablepre']."menu ";
      $rsOMenu=$this->db->execute($sql);
      /* insert data */
      $sql="INSERT INTO ".$this->cfg['tablepre']."menu
          (mnuParentId,mnuTitle,modId,mnuType,mnuActive,mnuOrder)
          VALUES (0,'".$modname."',".$rsIModule->fields[1].",'m','y',".(($rsOMenu->fields[0])+1).")";
      $rs2=$this->db->execute($sql);
      $this->errExecute("Insert menu '".$modname."'.",$rs2);
      return $rs2;
    }

}

/* insert privilege */
function setupPrivilege($group="a") {
    $sql="INSERT INTO ".$this->cfg['tablepre']."privilege
    	    (modAccess,modId,userPrivilege)
    	    VALUES ('y',".$this->mid.",'".$group."')";
    $rs3=$this->db->execute($sql);
    $this->errExecute("Insert Privilege for '".$group."' group.",$rs3);
    return $rs3;
}

function errExecute($message,$rs){
    if (!empty($rs)) {
        $this->syslanai->getInfoBox($message);
    } else {
        $this->syslanai->getErrorBox(" WORNING : ".$message);
    }
}

}

?>
