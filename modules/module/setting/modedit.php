<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$mod_lanai=new Module();
	
	switch($_REQUEST['ac']){
		case "new":
				$prefix=substr(md5(rand(1000,9999)),0,20);
				$mod_lanai->setNewModule($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
			break;
		case "active": 
				$mod_lanai->setModuleActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->goBack();
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$mod_lanai->getModuleById($midarr[$i]);
					if ($rsdwn->fields['modActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$mod_lanai->setModuleActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$mod_lanai->setDeleteModule($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		case "doedit": 
				if (empty($_REQUEST['modTitle'])) {
					   	$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
				} else {
					$mod_lanai->setEditModule($_REQUEST['mid'],$_REQUEST['modTitle']);
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
			/*
		case "delete": 
				$mod_lanai->setDeleteModule($_REQUEST['mid']);
				$sys_lanai->goBack();
			break;
			*/
		case "edit": 
				$rs=$mod_lanai->getModuleById($_REQUEST['mid']);
	?>
	<span class="txtContentTitle"><?=_MODULE_EDIT; ?></span><br/><br/>
	<?=_MODULE_EDIT_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();" ><?=_BACK; ?></a>
	<br><br>
	<table >
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>"  ENCTYPE="multipart/form-data" >
	<input type="hidden" name="mf" value="<?=$_REQUEST['mf']; ?>">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mid" value="<?=$_REQUEST['mid']; ?>">
	<input type="hidden" name="ac" value="doedit">
	<tr>
		<td><?=_MODULE_TITLE; ?></td>
		<td><input type="text" name="modTitle" value="<?=$rs->fields['modTitle']?>">*</td>
	</tr>
	<tr>
		<td><?=_MODULE_NAME; ?></td>
		<td><?=$rs->fields['modName']?></td>
	</tr>
	<!-- <tr>
		<td><?=_MODULE_ACTIVE; ?></td>
		<td>
		<?php
			if ($rs->fields['modActive']=='y') {
			    ?>
				<a href="<?=$_SERVER['PHP_SELF']."?modname=".$_REQUEST['modname']; ?>&mf=modedit&v=n&ac=active&mid=<?=$_REQUEST['mid']; ?>">
				<img src="theme/<?=$mod_lanai->cfg['theme'];?>/images/ok.gif" border="0" align="absmiddle">
				</a>
				<?php
			} else {
				?>
				<a href="<?=$_SERVER['PHP_SELF']."?modname=".$_REQUEST['modname']; ?>&mf=modedit&v=y&ac=active&mid=<?=$_REQUEST['mid']; ?>">
				<img src="theme/<?=$mod_lanai->cfg['theme'];?>/images/cancel.gif" border="0" align="absmiddle">
				</a>
				<?php
			}
		?>					
		</td>
		</td>
	</tr> -->
	</form>
	</table>
	<?php
			break;
		
	} // switch
		
?>