<?php
if (!defined('LANAI_ADMIN_REQUEST') || !isset($contentValues,$contentAction)) { http_response_code(403); exit; }
$contentEscape = static function($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); };
require_once __DIR__.'/../editor.php';
$contentNeedsReview = $contentNeedsReview ?? lanai_content_needs_review($sys_lanai);
$contentCanMenu = $contentAction==='new' && !$contentNeedsReview && $sys_lanai->userHasCapability('manage_options');
$contentThai = ($cfg['lang']??'')==='thai';
require_once __DIR__.'/../../../include/lanai/localization.php';
$contentLabel = static function($en,$th) { return lanai_translate($en, null, $th); };
?>
<section class="content-editor">
<h2><?= $contentEscape($contentAction==='new'?$contentLabel('New content','สร้างเนื้อหา'):$contentLabel('Edit content','แก้ไขเนื้อหา')) ?></h2>
<?php if (!empty($contentError)): ?><p class="alert alert-danger" role="alert"><?= $contentEscape($contentError) ?></p><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><p class="alert alert-success" role="status"><?= $contentEscape($contentLabel('Your content has been saved.','บันทึกเนื้อหาแล้ว')) ?></p><?php endif; ?>
<?php if ($contentNeedsReview): ?><p class="alert alert-info" role="status"><?= $contentEscape($contentLabel('Pages you write are sent to an editor for review and appear on the site once approved. Only basic formatting, links and images are kept.','Pages you write are sent to an editor for review and appear on the site once approved. Only basic formatting, links and images are kept.')) ?></p><?php endif; ?>
<?php if (isset($_GET['review'])): ?><p class="alert alert-success" role="status"><?= $contentEscape($contentLabel('Submitted for review. An editor will publish it once approved.','Submitted for review. An editor will publish it once approved.')) ?></p><?php endif; ?>
<?php if ($contentAction==='edit' && ($contentValues['conPending']??'n')==='y'): ?><p class="alert alert-warning" role="status"><?= $contentEscape($contentLabel('Pending review: this page is not visible to the public yet.','Pending review: this page is not visible to the public yet.')) ?></p><?php endif; ?>
<?php if (isset($_GET['menu_failed'])): ?><p class="alert alert-warning" role="alert"><?= $contentEscape($contentLabel('The page was saved, but its menu link could not be created. Add a content link from Menus.','บันทึกหน้าแล้ว แต่สร้างเมนูไม่สำเร็จ โปรดเพิ่มลิงก์ในเมนู')) ?></p><?php endif; ?>
<form id="content-form" method="post" action="setting.php">
<input type="hidden" name="modname" value="content"><input type="hidden" name="mf" value="conedit"><input type="hidden" name="ac" value="<?= $contentAction ?>">
<?php if ($contentAction==='edit'): ?><input type="hidden" name="mid" value="<?= (int)$contentValues['conId'] ?>"><?php endif; ?>
<?php $sys_lanai->renderCsrfField('content'); ?>
<label class="d-block mb-3" for="content-title"><?= $contentEscape($contentLabel('Page title','ชื่อหน้า')) ?></label>
<input class="form-control mb-3" id="content-title" name="conTitle" maxlength="200" required value="<?= $contentEscape($contentValues['conTitle']) ?>">
<?php if ($contentCanMenu): ?><label class="d-block mb-3"><input type="checkbox" name="conMenu" value="yes"<?= ($contentValues['conMenu']??'no')==='yes'?' checked':'' ?>> <?= $contentEscape($contentLabel('Also add a menu link to this page','เพิ่มลิงก์เมนูไปยังหน้านี้ด้วย')) ?></label><?php endif; ?>
<label class="d-block mb-3"><input type="checkbox" name="conAllowComments" value="y"<?= ($contentValues['conAllowComments']??'n')==='y'?' checked':'' ?>> <?= $contentEscape($contentLabel('Allow comments','อนุญาตความคิดเห็น')) ?></label>
<?php if (!$contentNeedsReview) include __DIR__.'/embed_controls.php'; ?>
<label class="d-block mb-2" for="content-body1"><?= $contentEscape($contentLabel('Page content','เนื้อหาหน้า')) ?></label>
<textarea id="content-body1" name="conBody1" class="tinymce form-control mb-3" rows="14"><?= $contentEscape($contentValues['conBody1']) ?></textarea>
<label class="d-block mt-3 mb-2" for="content-body2"><?= $contentEscape($contentLabel('Additional content (optional)','เนื้อหาเพิ่มเติม (ไม่บังคับ)')) ?></label>
<textarea id="content-body2" name="conBody2" class="tinymce form-control mb-3" rows="6"><?= $contentEscape($contentValues['conBody2']) ?></textarea>
<p id="content-editor-status" role="status" aria-live="polite" data-fallback="<?= $contentEscape(lanai_translate('The visual editor could not load. You can still edit the HTML and use the insert controls above.')) ?>"></p>
<div class="d-flex flex-wrap gap-3 mt-3">
<button class="btn btn-primary" type="submit"><?= $contentEscape($contentLabel('Save content','บันทึกเนื้อหา')) ?></button>
<?php if ($contentAction==='edit'): ?><a class="btn btn-outline-primary" href="module.php?modname=content&amp;cid=<?= (int)$contentValues['conId'] ?>" target="_blank" rel="noopener"><?= $contentEscape($contentLabel('View page','ดูหน้า')) ?></a><?php endif; ?>
<a class="btn btn-outline-secondary" href="setting.php?modname=content"><?= $contentEscape($contentLabel('Back to content','กลับไปยังเนื้อหา')) ?></a>
</div>
</form>
<?php if ($contentAction==='edit' && isset($contentValues['userId']) && ($contentValues['conActive']??'n')!=='y' && lanai_content_can_publish($sys_lanai,$contentValues)): ?>
<form method="post" action="setting.php" class="mt-3">
<input type="hidden" name="modname" value="content"><input type="hidden" name="mf" value="conedit"><input type="hidden" name="ac" value="active"><input type="hidden" name="v" value="y"><input type="hidden" name="mid" value="<?= (int)$contentValues['conId'] ?>">
<?php $sys_lanai->renderCsrfField('content'); ?>
<button class="btn btn-success" type="submit"><?= $contentEscape($contentLabel('Approve and publish','Approve and publish')) ?></button>
</form>
<?php endif; ?>
</section>
<script src="include/tinymce/js/tinymce/tinymce.min.js"></script>
<script src="assets/js/content-editor.js"></script>
