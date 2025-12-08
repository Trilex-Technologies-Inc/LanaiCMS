<?php

if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

$objbanner = new banner();
$rs = $objbanner->Load("banId=" . $_REQUEST['id']);

if (!$rs) {
    $sys_lanai->getErrorBox("Data not found!");
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

        <table>
            <form name="addform" method="get" action="setting.php">
                <input type="hidden" name="modname" value="carousel">
                <input type="hidden" name="mf" value="edit">
                <input type="hidden" name="ac" value="edit">
                <input type="hidden" name="banId" value="<?=$_REQUEST['id']; ?>">
                <input type="hidden" name="id" value="<?=$_REQUEST['id']; ?>">

                <tr>
                    <td><?=_BANN_TITLE; ?></td>
                    <td><input type="text" id="banTitle" name="banTitle" value="<?=$objbanner->BANTITLE;?>" size="30">*</td>
                </tr>

                <tr>
                    <td valign="top"><?=_BANN_DES; ?></td>
                    <td><textarea id="banDescription" name="banDescription" cols="30" rows="5"><?=$objbanner->BANDESCRIPTION;?></textarea>*</td>
                </tr>

                <tr>
                    <td><?=_BANN_IMAGE_URL; ?></td>
                    <td><input type="text" id="banImage" name="banImage" value="<?=$objbanner->BANIMAGE;?>" size="50" onblur="javacript:loadImage()">*</td>
                </tr>

                <tr>
                    <td><?=_BANN_URL; ?></td>
                    <td><input type="text" id="banURL" name="banURL" value="<?=$objbanner->BANURL;?>" size="40">*</td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><img src="<?=$objbanner->BANIMAGE;?>" name="banView"></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" value="<?=_SAVE; ?>" class="inputButton">
                        <input type="reset" value="<?=_RESET; ?>" class="inputButton">
                    </td>
                </tr>

            </form>
        </table>

        <script src="include/jsvalidator/gen_validatorv2.js"></script>
        <script>
            var frmvalidator  = new Validator("addform");
            frmvalidator.addValidation("banTitle","req","<?=_BANN_TITLE_EMPTY; ?>");
            frmvalidator.addValidation("banDescription","req","<?=_BANN_DES_EMPTY; ?>");
            frmvalidator.addValidation("banImage","req","<?=_BANN_IMAGE_URL_EMPTY; ?>");
            frmvalidator.addValidation("banURL","req","<?=_BANN_URL_EMPTY; ?>");
        </script>
        <?php
    }
}

?>
