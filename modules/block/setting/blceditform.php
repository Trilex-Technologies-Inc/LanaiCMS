<?php
if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$blc_lanai = new Block();
?>
<span class="txtContentTitle"><?=_BLOCK_SETTING; ?></span><br/><br/>
<?=_BLOCK_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a><br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="mf" value="blcedit">
    <input type="hidden" name="ac" value="edit">
    <input type="hidden" name="blcid" value="<?=$_REQUEST['mid']?>">
    <input type="hidden" name="m" value="<?=$_REQUEST['m']?>">

    <table>
    <?php
    $rs = $blc_lanai->getBlockById($_REQUEST['mid']);
    $positions = ['l'=>'_LEFT','r'=>'_RIGHT','c'=>'_CENTER','t'=>'_TOP','b'=>'_BOTTOM'];

    switch($_REQUEST['m']) {
        case 'c': // content type
    ?>
        <tr>
            <td><?=_BLOCK_TITLE; ?></td>
            <td><input type="text" name="blcTitle" size="50" value="<?=htmlspecialchars($rs->fields['blcTitle']); ?>">*</td>
        </tr>
        <tr>
            <td valign="top"><?=_BLOCK_CONTENT; ?></td>
            <td>
                <!-- TinyMCE textarea -->
                <textarea id="blcContent" name="blcContent" rows="20" cols="80"><?=htmlspecialchars($rs->fields['blcContent']); ?></textarea>

                <!-- TinyMCE with API key and full HTML support -->
                <script src="include/tinymce/js/tinymce/tinymce.min.js"></script>

                <script>
                tinymce.init({
                    selector: '#blcContent',
                    license_key: 'gpl',
                    height: 500,
                    menubar: true,
                     plugins:  "print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",

    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                    paste_as_text: false, // allow full HTML
                    valid_elements: '*[*]', // allow any tag and attribute
                    extended_valid_elements: '*[*]',
                    verify_html: false,
                    cleanup: false,
                    height: 400,
                    code_dialog_height: 500,
                    code_dialog_width: 800,
                    toolbar_mode: 'sliding',
                    setup: function(editor) {
                        editor.on('PastePreProcess', function(e) {
                            // allow raw HTML paste
                            e.content = e.content;
                        });
                    },
                    content_css: false // keep user CSS classes intact
                });
                </script>
            </td>
        </tr>
        <tr>
            <td><?=_BLOCK_POSITION; ?></td>
            <td>
                <select name="blcPosition">
                    <?php
                        foreach ($positions as $key => $label) {
                            $selected = ($rs->fields['blcPosition'] == $key) ? 'selected' : '';
                            echo "<option value='$key' $selected>$label</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
    <?php
        break;

        case 'b': // upload file
    ?>
        <tr>
            <td><?=_BLOCK_TITLE; ?></td>
            <td><input type="text" name="blcTitle" size="30" value="<?=htmlspecialchars($rs->fields['blcTitle']); ?>">*</td>
        </tr>
        <tr>
            <td><?=_BLOCK_POSITION; ?></td>
            <td>
                <input type="hidden" name="blcName" value="<?=$rs->fields['blcName']; ?>">
                <select name="blcPosition">
                    <?php
                        foreach ($positions as $key => $label) {
                            $selected = ($rs->fields['blcPosition'] == $key) ? 'selected' : '';
                            echo "<option value='$key' $selected>$label</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
    <?php
        break;

        case 'r': // RSS / URL
    ?>
        <tr>
            <td><?=_BLOCK_TITLE; ?></td>
            <td><input type="text" name="blcTitle" size="30" value="<?=htmlspecialchars($rs->fields['blcTitle']); ?>">*</td>
        </tr>
        <tr>
            <td><?=_BLOCK_URL; ?></td>
            <td><input type="text" name="blcRssUrl" size="50" value="<?=htmlspecialchars($rs->fields['blcRssUrl']); ?>">*</td>
        </tr>
        <tr>
            <td><?=_BLOCK_REFRESH; ?></td>
            <td><input type="text" name="blcRssRefesh" size="5" value="<?=htmlspecialchars($rs->fields['blcRssRefesh']); ?>"></td>
        </tr>
        <tr>
            <td><?=_BLOCK_POSITION; ?></td>
            <td>
                <select name="blcPosition">
                    <?php
                        foreach ($positions as $key => $label) {
                            $selected = ($rs->fields['blcPosition'] == $key) ? 'selected' : '';
                            echo "<option value='$key' $selected>$label</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
    <?php
        break;
    } // switch
    ?>
    </table>
</form>
