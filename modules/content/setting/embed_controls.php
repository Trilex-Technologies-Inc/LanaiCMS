<?php
if (!defined('LANAI_ADMIN_REQUEST')) { http_response_code(403); exit; }
global $db,$cfg;
$embedChoices=array('poll'=>array(),'contact'=>array(),'media'=>array());
foreach (array('poll'=>"SELECT pllId AS id,pllTitle AS label FROM ".$cfg['tablepre']."poll WHERE pllActive='y' ORDER BY pllTitle", 'contact'=>"SELECT conId AS id,conFname,conLname FROM ".$cfg['tablepre']."contact WHERE conActive='y' ORDER BY conFname", 'media'=>"SELECT * FROM ".$cfg['tablepre']."media ORDER BY createdAt DESC,mediaId DESC") as $type=>$sql) {
    try { $rows=$db->Execute($sql); } catch (Throwable $error) { $rows=false; }
    if ($rows) while (!$rows->EOF) {
        $item=$rows->fields;
        if ($type==='media') {
            $path=(string)($item['filePath']??'');
            $scheme=parse_url($path,PHP_URL_SCHEME);
            if (empty($item['explorerTrashId']) && $path!=='' && strpos($path,'//')!==0 && strpos($path,'\\')===false && (!$scheme || in_array(strtolower($scheme),array('http','https'),true))) {
                $embedChoices[$type][]=array('value'=>(string)(int)$item['mediaId'],'text'=>($item['title']??'')?:$item['origName'],'url'=>$path,'kind'=>$item['mediaType'],'alt'=>$item['altText']??'');
            }
        } else {
            $embedChoices[$type][]=array('value'=>(string)(int)$item['id'],'text'=>$type==='poll'?$item['label']:trim($item['conFname'].' '.$item['conLname']));
        }
        $rows->MoveNext();
    }
}
$embedEscape=static function($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); };
$embedThai=($cfg['lang']??'')==='thai';
require_once __DIR__.'/../../../include/lanai/localization.php';
$embedLabel=static function($en,$th) { return lanai_translate($en, null, $th); };
?>
<fieldset class="border rounded p-3 mb-3" id="content-insert-controls">
<legend class="h5"><?= $embedEscape($embedLabel('Insert into your page','แทรกลงในหน้า')) ?></legend>
<label class="d-block mb-2"><?= $embedEscape($embedLabel('Insert into','แทรกใน')) ?>
<select id="content-insert-target" class="form-select"><option value="content-body1"><?= $embedEscape($embedLabel('Page content','เนื้อหาหน้า')) ?></option><option value="content-body2"><?= $embedEscape($embedLabel('Additional content','เนื้อหาเพิ่มเติม')) ?></option></select></label>
<?php foreach (array('poll'=>$embedLabel('Insert poll','แทรกโพล'),'media'=>$embedLabel('Insert media','แทรกสื่อ'),'contact'=>$embedLabel('Insert contact','แทรกข้อมูลติดต่อ')) as $type=>$label): ?>
<div class="d-flex flex-wrap align-items-end gap-2 mb-2">
<label class="flex-grow-1"><?= $embedEscape($label) ?><select class="form-select" id="content-choice-<?= $type ?>">
<?php if (!$embedChoices[$type]): ?><option value=""><?= $embedEscape($embedLabel('No items available yet','ยังไม่มีรายการ')) ?></option><?php endif; ?>
<?php foreach ($embedChoices[$type] as $choice): ?><option value="<?= $choice['value'] ?>"><?= $embedEscape($choice['text']) ?></option><?php endforeach; ?>
</select></label>
<button class="btn btn-outline-primary" type="button" data-content-insert="<?= $type ?>"<?= !$embedChoices[$type]?' disabled':'' ?>><?= $embedEscape($label) ?></button>
<a href="setting.php?modname=<?= $type ?>" target="_blank" rel="noopener"><?= $embedEscape($embedLabel($type==='media'?'Upload / manage media':'Manage '.$type,$type==='media'?'อัปโหลด / จัดการสื่อ':'จัดการรายการ')) ?></a>
</div><?php endforeach; ?>
<p class="mb-0"><?= $embedEscape($embedLabel('Choose an existing poll, image, document or contact and click Insert. Create items using the links above, then save and reopen this page to refresh the choices.','เลือกรายการแล้วคลิกแทรก สร้างรายการจากลิงก์ด้านบน จากนั้นบันทึกและเปิดหน้านี้อีกครั้งเพื่อโหลดรายการใหม่')) ?></p>
<noscript><p><?= $embedEscape($embedLabel('Enable JavaScript to use insert controls. You can still enter and save HTML below.','เปิด JavaScript เพื่อแทรกรายการ หรือกรอกและบันทึก HTML ด้านล่าง')) ?></p></noscript>
</fieldset>
<script>window.lanaiEmbedChoices=<?= json_encode($embedChoices,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE) ?>; window.lanaiEmbedThai=<?= $embedThai?'true':'false' ?>;</script>
<script src="assets/js/content-embeds.js?v=2"></script>
