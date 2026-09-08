<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die ("You can't access this file directly...");
}

$objbanner = new banner();
$id = isset($_REQUEST['id']) && !is_array($_REQUEST['id'])
    ? (int) $_REQUEST['id']
    : 0;

/*
 * Do not use Active Record properties to populate this form. Their casing
 * depends on the database driver (banTitle, bantitle, or BANTITLE), which
 * caused the edit page to render empty values on some PHP/MySQL versions.
 */
$previousFetchMode = $db->SetFetchMode(ADODB_FETCH_ASSOC);
$bannerRow = $id > 0
    ? $db->GetRow("SELECT * FROM {$objbanner->_table} WHERE banId = {$id}")
    : false;
$bannerQueryError = $db->ErrorMsg();
$db->SetFetchMode($previousFetchMode);

$banner = array();
if (is_array($bannerRow)) {
    foreach ($bannerRow as $field => $value) {
        $banner[strtolower($field)] = $value;
    }
}

if (empty($banner)) {
    $message = $bannerQueryError !== ''
        ? "Unable to load carousel: " . htmlspecialchars($bannerQueryError, ENT_QUOTES, 'UTF-8')
        : "Data not found for carousel ID " . $id . ".";
    $sys_lanai->getErrorBox($message);
} else {
    if (!empty($_REQUEST['ac']) && $_REQUEST['ac'] == "edit") {

        $result = $objbanner->saveBanner($_REQUEST);

        if (!$result) {
            $sys_lanai->getErrorBox("Save failed!");
        } else {
            $sys_lanai->go2Page("setting.php?modname=carousel");
        }

    } else {
        ?>
        <span class="txtContentTitle"><?=_BANN_EDIT_ITEM; ?></span><br><br>
        <?=_BANN_EDIT_INSTRUCTION; ?><br/><br/>
        <img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
        <a href="setting.php?modname=carousel"><?=_BACK; ?></a><br><br>

        <form name="addform" method="post" action="setting.php">
            <table>
                <?php $positions = $objbanner->getPositionOptions(); ?>
                <input type="hidden" name="modname" value="carousel">
                <input type="hidden" name="mf" value="edit">
                <input type="hidden" name="ac" value="edit">
                <input type="hidden" name="banId" value="<?=$id; ?>">
                <input type="hidden" name="id" value="<?=$id; ?>">

                <tr>
                    <td><?=_BANN_TITLE; ?></td>
                    <td><input type="text" id="banTitle" name="banTitle" value="<?=htmlspecialchars((string)$banner['bantitle'], ENT_QUOTES, 'UTF-8'); ?>" size="30">*</td>
                </tr>

                <tr>
                    <td valign="top"><?=_BANN_DES; ?></td>
                    <td><textarea id="banDescription" name="banDescription" cols="30" rows="5"><?=htmlspecialchars((string)$banner['bandescription'], ENT_QUOTES, 'UTF-8'); ?></textarea>*</td>
                </tr>

                <tr>
                    <td><?=_BANN_IMAGE_URL; ?></td>
                    <td><input type="text" id="banImage" name="banImage" value="<?=htmlspecialchars((string)$banner['banimage'], ENT_QUOTES, 'UTF-8'); ?>" size="50" onblur="loadImage()">*</td>
                </tr>

                <tr>
                    <td><?=_BANN_URL; ?></td>
                    <td><input type="text" id="banURL" name="banURL" value="<?=htmlspecialchars((string)$banner['banurl'], ENT_QUOTES, 'UTF-8'); ?>" size="40">*</td>
                </tr>

                <tr>
                    <td><?=_BANN_POSITION; ?></td>
                    <td><select id="banPosition" name="banPosition"><?php foreach ($positions as $key => $label) { ?><option value="<?=$key; ?>"<?=isset($banner['banposition']) && $banner['banposition'] == $key ? ' selected' : ''; ?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8'); ?></option><?php } ?></select>*</td>
                </tr>

                <tr>
                    <td><?=_BANN_ACTIVE; ?></td>
                    <td><select id="banActive" name="banActive"><option value="y"<?=!isset($banner['banactive']) || $banner['banactive'] !== 'n' ? ' selected' : ''; ?>><?=_YES; ?></option><option value="n"<?=isset($banner['banactive']) && $banner['banactive'] === 'n' ? ' selected' : ''; ?>><?=_NO; ?></option></select></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><img src="<?=htmlspecialchars((string)$banner['banimage'], ENT_QUOTES, 'UTF-8'); ?>" name="banView" alt=""></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" value="<?=_SAVE; ?>" class="inputButton">
                        <input type="reset" value="<?=_RESET; ?>" class="inputButton">
                    </td>
                </tr>

            </table>
        </form>

        <script>
            function loadImage() {
                document.addform.banView.src = document.addform.banImage.value;
            }
        </script>

        <script src="include/jsvalidator/gen_validatorv2.js"></script>
        <script>
            var frmvalidator  = new Validator("addform");
            frmvalidator.addValidation("banTitle","req","<?=_BANN_TITLE_EMPTY; ?>");
            frmvalidator.addValidation("banDescription","req","<?=_BANN_DES_EMPTY; ?>");
            frmvalidator.addValidation("banImage","req","<?=_BANN_IMAGE_URL_EMPTY; ?>");
            frmvalidator.addValidation("banURL","req","<?=_BANN_URL_EMPTY; ?>");
            frmvalidator.addValidation("banPosition","req","<?=_BANN_POSITION_EMPTY; ?>");
        </script>
        <?php
    }
}

?>
