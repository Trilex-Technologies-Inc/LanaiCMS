<?

	if ( !eregi( "setting.php", $_SERVER['PHP_SELF'] ) ) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$member=new User();
	
	switch($_REQUEST['ac']){
		case "new":
				//$prefix=substr(md5(rand(1000,9999)),0,20);
				//$member->setNewModule($_REQUEST['method'],$prefix,$_REQUEST['userfile'],$_REQUEST['zippath']);
				//Find member unique login
				$rslogin=$member->getUserLogin($_REQUEST['userLogin']);
				if (($rslogin->recordcount())>0) {
				    $sys_lanai->getErrorBox(_LOGIN_EXIST." <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
				} else {
					if (empty($_REQUEST['userFname']) OR empty($_REQUEST['userLname']) OR empty($_REQUEST['userLogin'])  OR empty($_REQUEST['userEmail'])) {
					   	$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
					} else {
						if (($_REQUEST['userPassword1'])==($_REQUEST['userPassword2'])){
					 		//$mem_lanai->setUpdateUser($_SESSION['uid'],$_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin']);
							//$mem_lanai->setUpdateUserPassword($_SESSION['uid'],$_REQUEST['userPassword1']);
							//$sys_lanai->go2Page("?modname=member&mf=meminfo");
							$member->setNewUser($_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin'],$_REQUEST['userPassword1'],$_REQUEST['userPrivilege']);
						} else {
							$sys_lanai->getErrorBox(_PASSWORD_NOT_EQUAL." <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
						}
					}
				}
				
				// Upload Image
            if (!empty($_FILES['userAvatar']['name'])) {

                $tmpFile = $_FILES['userAvatar']['tmp_name'];
                $imgInfo = getimagesize($tmpFile);

                if ($imgInfo === false) {
                    $sys_lanai->getErrorBox(_WRONG_IMAGE_TYPE);
                    exit;
                }

                $allowedTypes = array(
                    IMAGETYPE_GIF  => 'gif',
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG  => 'png'
                );

                if (!isset($allowedTypes[$imgInfo[2]])) {
                    $sys_lanai->getErrorBox(_WRONG_IMAGE_TYPE);
                    exit;
                }

                $ext = $allowedTypes[$imgInfo[2]];

                global $cfg_datadir;
                $uploaddir = $cfg_datadir;
                $uploadfile = $uploaddir
                    . $sys_lanai->getPath()
                    . "uimage"
                    . $sys_lanai->getPath()
                    . "u" . intval($member->getUserIdByLogin($_REQUEST['userLogin'])) . "." . $ext;

                if (move_uploaded_file($tmpFile, $uploadfile)) {
                    $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
                } else {
                    $sys_lanai->getErrorBox(_CANNOT_UPLOAD_FILE);
                }
            }
            else {
					$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				}
			break;
		case "active": 
				$member->setUserActive($_REQUEST['mid'],$_REQUEST['v']);
				$sys_lanai->goBack();
			break;
		case "mactive": 
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$rsdwn=$member->getUser($midarr[$i]);
					if ($rsdwn->fields['userActive']=='y') {
					    $value="n";
					} else {
						$value="y";
					}
					$member->setUserActive($midarr[$i],$value);
				}				
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
			break;
		case "mdelete":
				
				$midarr=$_REQUEST['mid'];
				for ($i=0;$i<count($midarr);$i++) {
					$member->setDeleteUser($midarr[$i]);					
				}
				$sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=".$module_name);
				
			break;
		/*
		case "delete": 
				$member->setDeleteUser($_REQUEST['mid']);
				$sys_lanai->goBack();
			break;
			*/
		case "edit": 
				
				if (empty($_REQUEST['userFname']) OR empty($_REQUEST['userLname']) OR empty($_REQUEST['userLogin'])  OR empty($_REQUEST['userEmail'])) {
			   	$sys_lanai->getErrorBox(_REQUIRE_FIELDS." <a href=\"#\" onClick=\"javascript:history.back();\">_BACK</a>");
				} else {
					// update info
					if ((empty($_REQUEST['userPassword1']) AND empty($_REQUEST['userPassword2']))) {
					    $member->setUpdateUser($_REQUEST['mid'],$_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin'],$_REQUEST['userPrivilege']);
						//$sys_lanai->go2Page("?modname=member&mf=meminfo");
					} else {
						if (($_REQUEST['userPassword1'])==($_REQUEST['userPassword2'])){
						 	$member->setUpdateUser($_REQUEST['mid'],$_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin'],$_REQUEST['userPrivilege']);
							$member->setUpdateUserPassword($_REQUEST['mid'],md5($_REQUEST['userPassword1']));
							//$sys_lanai->go2Page("?modname=member&mf=meminfo");
						} else {
							$sys_lanai->getErrorBox(_PASSWORD_NOT_EQUAL_BACK);
						}
					}
				}

            if (!empty($_FILES['userAvatar']['name'])) {

                $tmpFile = $_FILES['userAvatar']['tmp_name'];
                $imgInfo = getimagesize($tmpFile);

                if ($imgInfo === false) {
                    $sys_lanai->getErrorBox(_WRONG_IMAGE_TYPE);
                    exit;
                }

                $allowedTypes = array(
                    IMAGETYPE_GIF  => 'gif',
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG  => 'png'
                );

                if (!isset($allowedTypes[$imgInfo[2]])) {
                    $sys_lanai->getErrorBox(_WRONG_IMAGE_TYPE);
                    exit;
                }

                $ext = $allowedTypes[$imgInfo[2]];

                global $cfg_datadir;
                $uploaddir = $cfg_datadir;
                $uploadfile = $uploaddir
                    . $sys_lanai->getPath()
                    . "uimage"
                    . $sys_lanai->getPath()
                    . "u" . intval($_REQUEST['mid']) . "." . $ext;

                if (move_uploaded_file($tmpFile, $uploadfile)) {
                    $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
                } else {
                    $sys_lanai->getErrorBox(_CANNOT_UPLOAD_FILE);
                }

            } else {
                $sys_lanai->go2Page($_SERVER['PHP_SELF'] . "?modname=" . $module_name);
            }

            break;
		
	} // switch
		
?>