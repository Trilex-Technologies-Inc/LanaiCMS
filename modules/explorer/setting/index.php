<?php
if (stripos($_SERVER['PHP_SELF'], 'setting.php') === false) die('Direct access denied.');
require_once 'modules/member/module.php';
require_once 'modules/media/module.php';
require_once 'modules/explorer/class.ExplorerFiles.php';
$media = new Media();
if (!$media->canManage()) { $sys_lanai->getErrorBox('Administrator access required.'); return; }
try { $explorerFiles = new ExplorerFiles($cfg); }
catch (Throwable $e) { $sys_lanai->getErrorBox(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')); return; }
$explorerRoots = array();
foreach ($explorerFiles->roots as $id => $root) $explorerRoots[$id] = $root['label'];
$explorerEscape = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
?>
<link rel="stylesheet" href="modules/explorer/explorer.css">
<section id="explorer" aria-labelledby="explorer-title">
<h2 id="explorer-title">Explorer</h2>
<p>Browse folders, organize files, and register selected files in Media.</p>
<noscript><p>Explorer needs JavaScript for folder navigation and upload progress. Enable JavaScript to use this screen.</p></noscript>
<div hidden id="explorer-token"><?php $sys_lanai->renderCsrfField('explorer'); ?></div>
<script type="application/json" id="explorer-config"><?= json_encode(array('roots' => $explorerRoots, 'uploadLimit' => ini_get('upload_max_filesize'), 'postLimit' => ini_get('post_max_size')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<div class="explorer-toolbar">
<label>Location <select id="explorer-root"><?php foreach ($explorerRoots as $id => $label) { ?><option value="<?= $explorerEscape($id); ?>"><?= $explorerEscape($label); ?></option><?php } ?></select></label>
<button type="button" id="explorer-up">Up</button>
<button type="button" id="explorer-refresh">Refresh</button>
<button type="button" id="explorer-trash-view" aria-pressed="false">Trash</button>
</div>
<nav id="explorer-breadcrumbs" aria-label="Folder path"></nav>
<form id="explorer-filters" class="explorer-toolbar">
<label>Search <input name="q" type="search" placeholder="Filename"></label>
<label>Type <select name="type"><option value="">All types</option><option value="folder">Folders</option><option value="image">Images</option><option value="pdf">PDF</option><option value="text">Text</option><option value="document">Documents / other</option><option value="audio/video">Audio / video</option><option value="archive">Archives</option></select></label>
<label>Modified from <input name="from" type="date"></label>
<label>Through <input name="to" type="date"></label>
<label class="explorer-check"><input name="recursive" type="checkbox"> Include subfolders</label>
<button type="submit">Search</button><button type="reset">Clear</button>
</form>
<div id="explorer-drop" tabindex="0" aria-label="File upload area">
<label for="explorer-upload"><strong>Drop files here or choose multiple files</strong></label>
<input id="explorer-upload" type="file" multiple accept="<?= $explorerEscape(implode(',', array_map(function ($extension) { return '.' . $extension; }, ExplorerFiles::UPLOAD_TYPES))); ?>">
<small>Uploads start after selection, one file at a time. Server limits: <?= $explorerEscape(ini_get('upload_max_filesize')); ?> per file, <?= $explorerEscape(ini_get('post_max_size')); ?> per request.</small>
</div>
<div class="explorer-toolbar">
<label>If a name exists <select id="explorer-collision"><option value="keep">Keep both</option><option value="skip">Skip</option><option value="replace">Replace (old file goes to Trash)</option></select></label>
<span id="explorer-selection">0 selected</span>
</div>
<div id="explorer-actions" class="explorer-toolbar">
<button type="button" data-action="mkdir">New folder</button>
<button type="button" data-action="rename">Rename</button>
<button type="button" data-action="move">Move selected</button>
<button type="button" data-action="zip">Download selected ZIP</button>
<button type="button" data-action="register">Register in Media</button>
<button type="button" data-action="trash">Move to Trash</button>
</div>
<div id="explorer-trash-actions" class="explorer-toolbar" hidden>
<button type="button" data-action="restore">Restore selected</button>
<button type="button" data-action="purge">Delete permanently</button>
<span>Restore uses Skip or Keep both. Replaced files can also be recovered here.</span>
</div>
<div id="explorer-status" role="status" aria-live="polite"></div>
<ul id="explorer-upload-results" aria-label="Upload results"></ul>
<div class="explorer-workspace">
<div class="explorer-list">
<div class="explorer-table-wrap"><table id="explorer-table">
<thead><tr><th scope="col"><input type="checkbox" id="explorer-select-all" aria-label="Select all items on this page"></th><th scope="col" data-sort="name"><button type="button">Name</button></th><th scope="col" data-sort="type"><button type="button">Type</button></th><th scope="col" data-sort="size"><button type="button">Size</button></th><th scope="col" data-sort="modified"><button type="button">Modified</button></th><th scope="col">Actions</th></tr></thead>
<tbody id="explorer-rows"></tbody>
</table></div>
<div class="explorer-toolbar"><button id="explorer-previous" type="button">Previous</button><span id="explorer-page"></span><button id="explorer-next" type="button">Next</button></div>
</div>
<aside id="explorer-preview" hidden aria-labelledby="explorer-preview-title"><button type="button" id="explorer-preview-close">Close preview</button><h3 id="explorer-preview-title">Preview</h3><div id="explorer-preview-body"></div></aside>
</div>
<dialog id="explorer-name-dialog"><form method="dialog"><h3 id="explorer-name-title">Name</h3><label>Name <input id="explorer-name" maxlength="240" required></label><div class="explorer-toolbar"><button value="cancel" formnovalidate>Cancel</button><button value="save">Save</button></div></form></dialog>
<dialog id="explorer-move-dialog"><form method="dialog"><h3>Choose destination folder</h3><label>Location <select id="explorer-move-root"><?php foreach ($explorerRoots as $id => $label) { ?><option value="<?= $explorerEscape($id); ?>"><?= $explorerEscape($label); ?></option><?php } ?></select></label><div class="explorer-toolbar"><button id="explorer-move-up" type="button">Up</button><span id="explorer-move-path"></span></div><div id="explorer-move-folders"></div><div class="explorer-toolbar"><button id="explorer-move-previous" type="button">Previous</button><span id="explorer-move-page"></span><button id="explorer-move-next" type="button">Next</button></div><div class="explorer-toolbar"><button value="cancel">Cancel</button><button value="move">Move here</button></div></form></dialog>
<p class="explorer-note">Registering a file makes it available through a public Media URL. That URL stays the same through moves and renames. Links that use the original folder path directly will still need updating.</p>
</section>
<script src="modules/explorer/explorer.js" defer></script>
