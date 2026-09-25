<?php
if (stripos($_SERVER['PHP_SELF'], 'setting.php') === false) die('Direct access denied.');
include_once('modules/media/module.php');
$media = new Media();
if (!$media->canManage()) { $sys_lanai->getErrorBox('Administrator access required.'); return; }
if (!$media->ensureLibraryFields()) { $sys_lanai->getErrorBox('Unable to prepare file details.'); return; }
$id = isset($_GET['mediaId']) && is_scalar($_GET['mediaId']) ? (int) $_GET['mediaId'] : 0;
$record = $media->getMediaById($id);
if (!$record || $record->EOF) { $sys_lanai->getErrorBox('File not found.'); return; }
$file = $record->fields;
if (!empty($file['explorerTrashId'])) { echo '<p>This file is in Explorer Trash. Restore it there before editing.</p><p><a href="setting.php?modname=explorer">Open Explorer</a></p>'; return; }
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<link rel="stylesheet" href="modules/media/library.css">
<section class="media-details">
<h2>Edit file details</h2>
<p><a href="setting.php?modname=media">Back to files</a></p>
<p><strong><?= $escape($file['origName']); ?></strong><br>
<?= $escape($file['mimeType']); ?> &middot; <?= number_format($file['fileSize'] / 1024, 1); ?> KB
<?php if ($file['width'] && $file['height']) { ?> &middot; <?= (int) $file['width']; ?> &times; <?= (int) $file['height']; ?> pixels<?php } ?><br>
Uploaded <?= $escape($file['createdAt']); ?></p>
<label>File path <input type="text" readonly value="<?= $escape($file['filePath']); ?>" onclick="this.select()"></label>
<form method="post" action="setting.php">
<input type="hidden" name="modname" value="media"><input type="hidden" name="mf" value="mediaedit"><input type="hidden" name="ac" value="details"><input type="hidden" name="mediaId" value="<?= $id; ?>">
<?php $sys_lanai->renderCsrfField('media'); ?>
<label>Title <input type="text" name="title" maxlength="255" value="<?= $escape($file['title']); ?>"></label>
<label>Caption <textarea name="caption" rows="4" maxlength="10000"><?= $escape($file['caption']); ?></textarea></label>
<label>Alternative text <input type="text" name="altText" maxlength="255" value="<?= $escape($file['altText']); ?>" aria-describedby="media-alt-help"></label>
<p id="media-alt-help">For images, describe what matters to someone who cannot see them. Leave blank for decorative images.</p>
<p>These details are saved in the library. Existing content with copied image HTML is not updated automatically.</p>
<button type="submit" class="btn btn-primary">Save details</button>
</form>
<hr>
<form method="post" action="setting.php" onsubmit="return confirm('Remove this file? Explorer registrations go to Trash; other Media uploads are permanently deleted.');">
<input type="hidden" name="modname" value="media"><input type="hidden" name="mf" value="mediaedit"><input type="hidden" name="ac" value="delete"><input type="hidden" name="mediaId" value="<?= $id; ?>">
<?php $sys_lanai->renderCsrfField('media'); ?>
<button type="submit" class="btn btn-outline-danger">Delete file</button>
</form>
</section>
