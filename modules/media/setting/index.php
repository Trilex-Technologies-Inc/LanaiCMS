<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$media = new Media();
?>
<span class="txtContentTitle"><?=_MEDIA_SETTING; ?></span><br/><br/>
<?=_MEDIA_SETTING_INSTRUCTION; ?><br/><br/>

<?php if (!$media->isStorageWritable()) { ?>
    <div class="alert alert-danger"><?=_MEDIA_STORAGE_NOT_WRITABLE;?></div>
<?php } ?>

<form name="uploadform" method="post" action="<?=$_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="modname" value="media">
    <input type="hidden" name="mf" value="mediaedit">
    <input type="hidden" name="ac" value="upload">
    <?php $sys_lanai->renderCsrfField('media'); ?>
    <div class="d-flex gap-2 align-items-center">
        <input type="file" name="mediaFile" class="form-control" style="max-width:400px;" required>
        <input type="text" name="altText" class="form-control" placeholder="<?=_MEDIA_ALT_TEXT;?>" style="max-width:250px;">
        <button type="submit" class="btn btn-primary"><?=_MEDIA_UPLOAD;?></button>
    </div>
</form>

<?php
$media->getMediaList();
?>
