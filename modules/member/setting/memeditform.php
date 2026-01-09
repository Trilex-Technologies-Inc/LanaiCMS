<?

if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$member = new User();

$rs = $member->getUser($_REQUEST['mid']);

?><span class="txtContentTitle"><?= _MEMBER_EDIT; ?></span><br/><br/>
<?= _MEMBER_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?= $cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?= _SAVE; ?></a>&nbsp;&nbsp;

<img src="theme/<?= $cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?= _BACK; ?></a>
<br><br>

<table border="0" cellspacing="2" cellpadding="3">
    <form name="form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" ENCTYPE="multipart/form-data">
        <input type="hidden" name="mf" value="memedit">
        <input type="hidden" name="modname" value="<?= $module_name; ?>">
        <input type="hidden" name="ac" value="edit">
        <input type="hidden" name="mid" value="<?= $rs->fields['userId'] ?>">
        <tr>
            <td><?= _USER_FNAME; ?></td>
            <td><input type="text" name="userFname" value="<?= $rs->fields['userFname'] ?>">*</td>
        </tr>
        <tr>
            <td><?= _USER_LNAME; ?></td>
            <td><input type="text" name="userLname" value="<?= $rs->fields['userLname'] ?>">*</td>
        </tr>
        <tr>
            <td><?= _USER_ADDRESS1; ?></td>
            <td><input type="text" name="userAddress1" size="40" value="<?= $rs->fields['userAddress1'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_ADDRESS2; ?></td>
            <td><input type="text" name="userAddress2" size="40" value="<?= $rs->fields['userAddress2'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_CITY; ?></td>
            <td><input type="text" name="userCity" value="<?= $rs->fields['userCity'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_STATE; ?></td>
            <td><input type="text" name="userState" value="<?= $rs->fields['userState'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_COUNTRY; ?></td>
            <td><?= $member->setCountryCombo($rs->fields['cntId'], "cntId"); ?></td>
        </tr>
        <tr>
            <td><?= _USER_ZIPCODE; ?></td>
            <td><input type="text" name="userZipcode" value="<?= $rs->fields['userZipcode'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_PHONE; ?></td>
            <td><input type="text" name="userPhone" value="<?= $rs->fields['userPhone'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_FAX; ?></td>
            <td><input type="text" name="userFax" value="<?= $rs->fields['userFax'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_MOBILE; ?></td>
            <td><input type="text" name="userMobile" value="<?= $rs->fields['userMobile'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_EMAIL; ?></td>
            <td><input type="text" name="userEmail" value="<?= $rs->fields['userEmail'] ?>">*</td>
        </tr>
        <tr>
            <td><?= _USER_URL; ?></td>
            <td><input type="text" name="userURL" value="<?= $rs->fields['userURL'] ?>"></td>
        </tr>
        <tr>
            <td><?= _USER_LOGIN; ?></td>
            <td><input type="text" name="userLogin" value="<?= $rs->fields['userLogin'] ?>">*</td>
        </tr>
        <tr>
            <td><?= _USER_PASSWORD; ?></td>
            <td><input type="password" name="userPassword1" value="">* <?= _USER_LEAVE_BLANK; ?></td>
        </tr>
        <tr>
            <td><?= _USER_RE_PASSWORD; ?></td>
            <td><input type="password" name="userPassword2" value="">* <?= _USER_LEAVE_BLANK; ?></td>
        </tr>
        <tr>
            <td><?= _USER_PRIVILEGE; ?></td>
            <td>
                <?
                $aPri = "";
                $uPri = "";
                if ($rs->fields['userPrivilege'] == "a") {
                    $aPri = "selected";
                } else if ($rs->fields['userPrivilege'] == "u") {
                    $uPri = "selected";
                }

                ?>
                <select name="userPrivilege">
                    <option value="u" <?= $uPri; ?> ><?= _USER; ?></option>
                    <option value="a" <?= $aPri; ?> ><?= _ADMIN; ?></option>
                </select>
            </td>
        </tr>
        <?
        if (is_writable($cfg['datadir'] . $sys_lanai->getPath() . "uimage")) {
            ?>
            <tr>
                <td><?= _USER_AVATAR; ?></td>
                <td><input type="file" name="userAvatar"></td>
            </tr>
            <?
        }
        ?>

    </form>
</table>