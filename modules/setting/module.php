<?php
	class setting {
	
		function getModuleSetting(){
			global $db,$tablepre;
			$sql="SELECT * FROM ".$tablepre."module WHERE modSetting='y' ORDER BY modName ASC";
			$rs=$db->execute($sql);
			return $rs;
		}
		
		function getModuleSettingByPrivilege($group){
			global $db,$tablepre;
			$sql="SELECT * FROM ".$tablepre."privilege,".$tablepre."module  
						WHERE ".$tablepre."module.modId=".$tablepre."privilege.modId 
								AND ".$tablepre."privilege.modAccess='Y' 
								AND ".$tablepre."module.modSetting='y' 
								AND ".$tablepre."module.modActive='y' 
								AND ".$tablepre."privilege.userPrivilege='$group'
						ORDER BY modName ASC";
			//$db->debug=true;
			$rs=$db->execute($sql);
			return $rs;
			
		}
		
		
	}
?>