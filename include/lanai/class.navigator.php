<?php
		class Navigator {
		
				function getIsParentMenu($mnuParentId) {
					global $db,$tablepre,$sys_lanai;
					$sql="SELECT * FROM ".$tablepre."menu WHERE mnuParentId=$mnuParentId AND mnuActive='y' ";
					$rs=$db->execute($sql);
					if (($rs->recordcount())>0) {
						return true;
					} else {
						return false;
					}
				}
				
				function getMenuRealLink($rsmx){
					global $sys_lanai,$cfg;
					if ($rsmx->fields['mnuType']=="m") {
						$rsmod=$sys_lanai->getModule($rsmx->fields['modId']);
						if ($cfg['seo']=="yes") { 
							$prelink="/".$rsmod->fields['modName'];
						} else {
							$prelink="module.php?modname=".$rsmod->fields['modName'];
						}
					} else if ($rsmx->fields['mnuType']=="c"){
							$prelink= "module.php?modname=content&cid=".$rsmx->fields['conId'];
					} else {
						$prelink=$rsmx->fields['mnuUrl'];
					}
					return $prelink;
				}
		}
?>