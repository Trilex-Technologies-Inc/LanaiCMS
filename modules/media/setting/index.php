<?php
if (stripos($_SERVER['PHP_SELF'], 'setting.php') === false) die('Direct access denied.');
include_once('modules/media/module.php');
$media = new Media();
$db = $media->db;
if (!$media->canManage()) { $sys_lanai->getErrorBox('Administrator access required.'); return; }
if (!$media->ensureLibraryFields()) {
    $sys_lanai->getErrorBox('Unable to prepare media details. The database account needs permission to add the title and caption columns.');
    return;
}
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$filters = array();
foreach (array('q', 'type', 'from', 'to') as $key) $filters[$key] = isset($_GET[$key]) && is_string($_GET[$key]) ? trim($_GET[$key]) : '';
$where = $media->libraryWhere($filters);
$table = $cfg['tablepre'] . 'media';
$count = $db->Execute('SELECT COUNT(*) AS total FROM ' . $table . ' WHERE ' . $where);
if (!$count) { $sys_lanai->getErrorBox('Unable to load files.'); return; }
$total = (int) $count->fields['total'];
$pages = max(1, (int) ceil($total / 30));
$page = min($pages, max(1, isset($_GET['page']) && is_scalar($_GET['page']) ? (int) $_GET['page'] : 1));
$files = $db->SelectLimit('SELECT * FROM ' . $table . ' WHERE ' . $where . ' ORDER BY createdAt DESC, mediaId DESC', 30, ($page - 1) * 30);
if (!$files) { $sys_lanai->getErrorBox('Unable to load files.'); return; }
?>
<link rel="stylesheet" href="modules/media/library.css">
<section class="media-library" aria-labelledby="media-heading">
<h2 id="media-heading">Files</h2>
<p>Upload files, find existing items, and edit their details.</p>
<?php if (!empty($_SESSION['media_notice'])) { ?>
<div role="status" class="alert alert-info"><?= $escape($_SESSION['media_notice']); ?></div>
<?php unset($_SESSION['media_notice']); } ?>
<?php if (!$media->isStorageWritable()) { ?><p class="alert alert-danger"><?= $escape(_MEDIA_STORAGE_NOT_WRITABLE); ?></p><?php } ?>
<form id="media-upload" method="post" action="setting.php?modname=media&amp;mf=mediaedit" enctype="multipart/form-data" data-max-files="<?= (int) ini_get('max_file_uploads'); ?>">
<input type="hidden" name="modname" value="media"><input type="hidden" name="mf" value="mediaedit"><input type="hidden" name="ac" value="upload">
<?php $sys_lanai->renderCsrfField('media'); ?>
<div id="media-drop" class="media-drop">
<label for="media-files"><strong>Drop files here or select files</strong></label>
<input id="media-files" type="file" name="mediaFiles[]" multiple required accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
<button type="submit" class="btn btn-primary">Upload selected files</button>
<small>Images, PDF, Word, Excel, ZIP and text files. Server limits: <?= $escape(ini_get('upload_max_filesize')); ?> per file; <?= $escape(ini_get('post_max_size')); ?> per batch; <?= (int) ini_get('max_file_uploads'); ?> files per request.</small>
<div id="media-upload-status" role="status" aria-live="polite"></div>
</div>
</form>
<form method="get" action="setting.php" class="media-filters">
<input type="hidden" name="modname" value="media">
<label>Search <input type="search" name="q" value="<?= $escape($filters['q']); ?>" placeholder="Filename or details"></label>
<label>Type <select name="type">
<?php foreach (array('' => 'All types', 'image' => 'Images', 'file' => 'Documents and other files', 'pdf' => 'PDF') as $value => $label) { ?>
<option value="<?= $value; ?>" <?= $filters['type'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
<?php } ?></select></label>
<label>Uploaded from <input type="date" name="from" value="<?= $escape($filters['from']); ?>"></label>
<label>Through <input type="date" name="to" value="<?= $escape($filters['to']); ?>"></label>
<button class="btn btn-secondary" type="submit">Filter</button><a href="setting.php?modname=media">Clear</a>
</form>
<p><?= $total; ?> file<?= $total === 1 ? '' : 's'; ?> &middot; Page <?= $page; ?> of <?= $pages; ?></p>
<div class="media-table-wrap"><table class="media-table">
<thead><tr><th scope="col">Filename / title</th><th scope="col">Type</th><th scope="col">Size</th><th scope="col">Uploaded</th><th scope="col">Actions</th></tr></thead><tbody>
<?php if (!$total) { ?><tr><td colspan="5">No files match these filters.</td></tr><?php } ?>
<?php while (!$files->EOF) { $file = $files->fields; $id = (int) $file['mediaId']; ?>
<tr>
<td><a href="setting.php?modname=media&amp;mf=details&amp;mediaId=<?= $id; ?>"><?= $escape($file['origName']); ?></a><?php if (!empty($file['title'])) { ?><small class="media-title"><?= $escape($file['title']); ?></small><?php } ?></td>
<td><?= $escape(strtoupper(pathinfo($file['origName'], PATHINFO_EXTENSION))); ?></td>
<td class="media-nowrap"><?= number_format($file['fileSize'] / 1024, 1); ?> KB</td>
<td class="media-nowrap"><?= $escape($file['createdAt']); ?></td>
<td><a href="setting.php?modname=media&amp;mf=details&amp;mediaId=<?= $id; ?>">Edit details</a></td>
</tr>
<?php $files->MoveNext(); } ?>
</tbody></table></div>
<nav aria-label="File list pages" class="media-pagination">
<?php foreach (array($page - 1 => 'Previous', $page + 1 => 'Next') as $number => $label) { if ($number >= 1 && $number <= $pages) { ?>
<a href="setting.php?<?= $escape(http_build_query(array_merge($filters, array('modname' => 'media', 'page' => $number)))); ?>"><?= $label; ?></a>
<?php } } ?></nav>
</section>
<script src="modules/media/library.js" defer></script>
