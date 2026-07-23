<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$dwn=new Language();
?>
<span class="txtContentTitle">Install</span><br/><br/>
<OL>
<?php
	global $cfg,$db;
	switch($_REQUEST['step']){
		case "1": 			
			// create nessary tables
			?><LI>Create Table <?=$cfg['tablepre']."carousel" ?> <?php
			
			// create table script
			$sql="DROP TABLE IF EXISTS ".$cfg['tablepre']."carousel;";
			$db->execute($sql);
			
			$sql="CREATE TABLE ".$cfg['tablepre']."carousel (
					  bnnId INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					  bnnTitle VARCHAR(80) NULL,
					  bnnURL VARCHAR(80) NULL,
					  bnnContent TEXT NULL,
					  bnnCreate TIMESTAMP NULL,
					  bnnActive ENUM('y','n') NULL DEFAULT 'y',
					  PRIMARY KEY(bnnId)
					)";
			$rs1=$db->execute($sql);
			
			if (empty($rs1)) {
				?><span style="color:red;">Error!</span><?php
			} else {
				?><span style="color:green;">OK</span><?php
			}
					
			?><LI>Create Table <?=$cfg['tablepre']."banner_stat" ?> <?php
			$sql="DROP TABLE IF EXISTS ".$cfg['tablepre']."banner_stat;";
			$db->execute($sql);			
			$sql="CREATE TABLE ".$cfg['tablepre']."banner_stat (
					  bnnId INTEGER UNSIGNED NOT NULL,
					  bnnState ENUM('c','i') NOT NULL DEFAULT 'i',
					  bnnDate TIMESTAMP NULL
					)";
			$rs2=$db->execute($sql);
			
			if (empty($rs2)) {
				?><span style="color:red;">Error!</span><?php
			} else {
				?><span style="color:green;">OK</span><?php
			}
			
			if ((!empty($rs1)) AND (!empty($rs2))) {
				?><br><br><input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=2';"><?php
			}
			
			break;
		case "2":
			// create module data and menu
			// check module data exist!
			$sql="SELECT COUNT(*),modId FROM ".$cfg['tablepre']."module 
					WHERE modName='language' GROUP BY modId";
			$rs=$db->execute($sql);
			if (($rs->fields[0])>0) {
				// delete from module table
				$sql="DELETE FROM ".$cfg['tablepre']."module 
					WHERE modId=".$rs->fields[1];
				$db->execute($sql);
				// delete from menu table
				$sql="DELETE FROM ".$cfg['tablepre']."menu 
					WHERE modId=".$rs->fields[1]." AND mnuType='m'";
				$db->execute($sql);
				// delete from privilege table
				$sql="DELETE FROM ".$cfg['tablepre']."privilege 
					WHERE modId=".$rs->fields[1];
				$db->execute($sql);
			}
			// select for max order 
			// select module data
			$sql="SELECT MAX(modOrder) FROM ".$cfg['tablepre']."module ";
			$rsOModule=$db->execute($sql);
			?><LI>Create module data<?php
			// create module data
			$sql="INSERT INTO ".$cfg['tablepre']."module 
					(modTitle,modName,modActive,modOrder,modSetting) 
					VALUES ('language','language','y',".(($rsOModule->fields[0])+1).",'y')";
			$rs1=$db->execute($sql);						
			// select module data
			$sql="SELECT COUNT(*),modId FROM ".$cfg['tablepre']."module 
					WHERE modName='language' GROUP BY modId";
			$rsIModule=$db->execute($sql);			
			?><LI>Create privilege data<?php
			// create privilege data
			$sql="INSERT INTO ".$cfg['tablepre']."privilege 
					(modAccess,modId,userPrivilege)
					VALUES ('y',".$rsIModule->fields[1].",'a')";
			$rs3=$db->execute($sql);			
			if ((!empty($rs1))  AND (!empty($rs3))) {
				?><br><br><input type="button" class="inputButton" value="Install Complete Click to Setting" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>';"><?php
			}
			break;
		default:
			// check nessary environment			
			// 1 check safe mode
			?><LI>PHP Safe Mode is <?php
			if (ini_get('safe_mode')) {
			    ?><span style="color:green;">ON</span><?php
			} else {
				?><span style="color:red;">OFF</span><?php
			}
			// 2 check dir exist
			?> 
			<LI>Module Directory is 
			<?php
			
			if ((is_writable($cfg['dir'].$sys_lanai->getPath()."modules")) AND (is_writable($cfg['dir'].$sys_lanai->getPath()."modules"))) {
				?>
				<span style="color:green;">WRITABLE</span><br/><br/>
				<input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=2';">
				<?php
			} 		
			
	} // switch
	
	
?>
</OL>