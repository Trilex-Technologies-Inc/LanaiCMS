<?
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction="modules/$module_name/module.php";
include_once($modfunction);

// get userinfo
$mem_lanai=new User();
$rs=$mem_lanai->getUser($_SESSION['uid']);
$ac = isset($_REQUEST['ac']) ? $_REQUEST['ac'] : '';

switch($ac){
    case "activate" :
        $mitem=$mem_lanai->getUserActivate($_REQUEST['u'],$_REQUEST['p']);
        if ($mitem->recordcount() > 0) {
            $mem_lanai->setUserActive($mitem->fields['userId'],"y");
            ?>
            <img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
            <?=_MEMBER_ACTIVATE_COMPLETE; ?>
            <?
        } else {
            ?>
            <img src="theme/<?=$cfg['theme']; ?>/images/worning.gif" border="0" align="absmiddle"/>
            <?=_MEMBER_CANNOT_ACTIVATE; ?>
            <?
        }
        break;
    case "doedit":
        $userPri=$mem_lanai->getUserPrivilege($_SESSION['uid']);
        if (empty($_REQUEST['userFname']) OR empty($_REQUEST['userLname']) OR empty($_REQUEST['userLogin'])  OR empty($_REQUEST['userEmail'])) {
            $sys_lanai->getErrorBox(_REQUIRE_FIELDS_BACK);
        } else {
            // update info
            if ((empty($_REQUEST['userPassword1']) AND empty($_REQUEST['userPassword2']))) {
                $mem_lanai->setUpdateUser($_SESSION['uid'],$_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin'],$userPri);
                //$sys_lanai->go2Page("?modname=member&mf=meminfo");
            } else {
                if (($_REQUEST['userPassword1'])==($_REQUEST['userPassword2'])){
                    $mem_lanai->setUpdateUser($_SESSION['uid'],$_REQUEST['userFname'],$_REQUEST['userLname'],$_REQUEST['userAddress1'],$_REQUEST['userAddress2'],$_REQUEST['userCity'],$_REQUEST['userState'],$_REQUEST['cntId'],$_REQUEST['userZipcode'],$_REQUEST['userPhone'],$_REQUEST['userFax'],$_REQUEST['userMobile'],$_REQUEST['userEmail'],$_REQUEST['userURL'],$_REQUEST['userLogin'],$userPri);
                    $mem_lanai->setUpdateUserPassword($_SESSION['uid'],md5($_REQUEST['userPassword1']));
                    //$sys_lanai->go2Page("?modname=member&mf=meminfo");
                } else {
                    $sys_lanai->getErrorBox(_PASSWORD_NOT_EQUAL_BACK);
                }
            }
        }

        if (!empty($_FILES['userAvatar']['name'])) {
            if (strtolower(substr($_FILES['userAvatar']['name'],strlen($_FILES['userAvatar']['name'])-3,3))!="gif") {
                $sys_lanai->getErrorBox(_WRONG_IMAGE_TYPE);
            } else {
                // upload file
                global $cfg_datadir;
                $uploaddir = $cfg_datadir;
                $uploadfile = $uploaddir .$sys_lanai->getPath()."uimage".$sys_lanai->getPath(). "u".$_SESSION['uid'].".gif";
                //echo $uploadfile;
                if (move_uploaded_file($_FILES['userAvatar']['tmp_name'], $uploadfile)) {
                    $sys_lanai->go2Page("?modname=member&mf=meminfo");
                } else {
                    $sys_lanai->getErrorBox(_CANNOT_UPLOAD_FILE);
                }
            }
        } else {
            $sys_lanai->go2Page("?modname=member&mf=meminfo");
        }

        break;
    case "edit":
        // edit form
        ?><span class="txtContentTitle"><?=_MEMBER_EDIT; ?></span><br/><br/>
        <?=_MEMBER_EDIT_INSTRUCTION; ?><br/><br/>

        <img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
        <a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

        <img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
        <a href="#" onClick="javascript:history.back();" ><?=_BACK; ?></a>
        <br><br>
        <table cellpadding="2">
            <form method="post" enctype="multipart/form-data" name="form" action="<?=$_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="modname" value="member"/>
                <input type="hidden" name="mf" value="meminfo"/>
                <input type="hidden" name="ac" value="doedit"/>
                <tr>
                    <td><?=_USER_FNAME; ?></td><td><input type="text" name="userFname" value="<?=$rs->fields['userFname']?>">*</td>
                </tr>
                <tr>
                    <td><?=_USER_LNAME; ?></td><td><input type="text" name="userLname" value="<?=$rs->fields['userLname']?>">*</td>
                </tr>
                <tr>
                    <td><?=_USER_ADDRESS1; ?></td><td><input type="text" name="userAddress1" size="40" value="<?=$rs->fields['userAddress1']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_ADDRESS2; ?></td><td><input type="text" name="userAddress2" size="40" value="<?=$rs->fields['userAddress2']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_CITY; ?></td><td><input type="text" name="userCity" value="<?=$rs->fields['userCity']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_STATE; ?></td><td><input type="text" name="userState" value="<?=$rs->fields['userState']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_COUNTRY; ?></td><td><?=$mem_lanai->setCountryCombo($rs->fields['cntId'],"cntId"); ?></td>
                </tr>
                <tr>
                    <td><?=_USER_ZIPCODE; ?></td><td><input type="text" name="userZipcode" value="<?=$rs->fields['userZipcode']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_PHONE; ?></td><td><input type="text" name="userPhone" value="<?=$rs->fields['userPhone']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_FAX; ?></td><td><input type="text" name="userFax" value="<?=$rs->fields['userFax']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_MOBILE; ?></td><td><input type="text" name="userMobile" value="<?=$rs->fields['userMobile']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_EMAIL; ?></td><td><input type="text" name="userEmail" value="<?=$rs->fields['userEmail']?>">*</td>
                </tr>
                <tr>
                    <td><?=_USER_URL; ?></td><td><input type="text" name="userURL" value="<?=$rs->fields['userURL']?>"></td>
                </tr>
                <tr>
                    <td><?=_USER_LOGIN; ?></td><td><input type="text" name="userLogin" value="<?=$rs->fields['userLogin']?>">*</td>
                </tr>
                <tr>
                    <td><?=_USER_PASSWORD; ?></td><td><input type="password" name="userPassword1" value="">* <?=_USER_LEAVE_BLANK; ?></td>
                </tr>
                <tr>
                    <td><?=_USER_RE_PASSWORD; ?></td><td><input type="password" name="userPassword2" value="">* <?=_USER_LEAVE_BLANK; ?></td>
                </tr>
                <tr>
                    <td><?=_USER_AVATAR; ?></td><td><input type="file" name="userAvatar"></td>
                </tr>
            </form>
        </table>
        <?
        break;
    default:
        // show data
        ?>
        <span class="txtContentTitle"><?=_MEMBER_INFORMATION; ?></span><br/><br/>
        <?=_MEMBER_INFO_INSTRUCTION; ?><br/><br/>

        <img src="theme/<?=$cfg['theme']; ?>/images/edit.gif" border="0" align="absmiddle"/>
        <a href="#" onClick="javascript:document.form.submit();"><?=_EDIT; ?></a>&nbsp;&nbsp;
        <img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
        <a href="#" onClick="javascript:history.back();" ><?=_BACK; ?></a>
        <br/><br/>
        <table cellpadding="2">
            <form method="post" name="form" action="<?=$_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="modname" value="member"/>
                <input type="hidden" name="mf" value="meminfo"/>
                <input type="hidden" name="ac" value="edit"/>
                <tr>
                    <td><?=_USER_FNAME; ?></td><td><?=$rs->fields['userFname']?></td>
                </tr>
                <tr>
                    <td><?=_USER_LNAME; ?></td><td><?=$rs->fields['userLname']?></td>
                </tr>
                <tr>
                    <td><?=_USER_ADDRESS1; ?></td><td><?=$rs->fields['userAddress1']?></td>
                </tr>
                <tr>
                    <td><?=_USER_ADDRESS2; ?></td><td><?=$rs->fields['userAddress2']?></td>
                </tr>
                <tr>
                    <td><?=_USER_CITY; ?></td><td><?=$rs->fields['userCity']?></td>
                </tr>
                <tr>
                    <td><?=_USER_STATE; ?></td><td><?=$rs->fields['userState']?></td>
                </tr>
                <tr>
                    <td><?=_USER_COUNTRY; ?></td><td><?=$mem_lanai->getCountry($rs->fields['cntId']); ?></td>
                </tr>
                <tr>
                    <td><?=_USER_ZIPCODE; ?></td><td><?=$rs->fields['userZipcode']?></td>
                </tr>
                <tr>
                    <td><?=_USER_PHONE; ?></td><td><?=$rs->fields['userPhone']?></td>
                </tr>
                <tr>
                    <td><?=_USER_FAX; ?></td><td><?=$rs->fields['userFax']?></td>
                </tr>
                <tr>
                    <td><?=_USER_MOBILE; ?></td><td><?=$rs->fields['userMobile']?></td>
                </tr>
                <tr>
                    <td><?=_USER_EMAIL; ?></td><td><?=$rs->fields['userEmail']?></td>
                </tr>
                <tr>
                    <td><?=_USER_URL; ?></td><td><?=$rs->fields['userURL']?></td>
                </tr>
                <!--
	<tr>
		<td><?=_USER_LOGIN; ?></td><td><?=$rs->fields['userLogin']?></td>
	</tr>
	<tr>
		<td><?=_USER_PASSWORD; ?></td><td><?=$rs->fields['userPassword']?></td>
	</tr>
	-->
            </form>
        </table>
    <?
} // switch

?>
