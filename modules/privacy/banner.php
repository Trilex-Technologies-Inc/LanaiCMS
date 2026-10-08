<?php
if (!isset($lanaiPrivacy,$sys_lanai)) return '';
$e=static function($v) { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); };
$thai=($cfg['lang']??'')==='thai';
require_once __DIR__.'/../../include/lanai/localization.php';
$t=static function($en,$th) { return lanai_translate($en, null, $th); };
$choices=$lanaiPrivacy->choices();
$data=array('endpoint'=>rtrim($cfg['url'],'/').'/privacy.php','csrf'=>$sys_lanai->getCsrfToken('privacy'),'revision'=>$lanaiPrivacy->config()['revision'],'choices'=>$choices,
    'error'=>$t('Preferences could not be saved. Reload the page and try again.','ไม่สามารถบันทึกได้ กรุณาโหลดหน้าใหม่แล้วลองอีกครั้ง'));
$privacyMarkup='<link rel="stylesheet" href="assets/privacy.css"><div id="lanai-privacy" data-config="'.$e(json_encode($data)).'">';
$privacyMarkup.='<button type="button" id="privacy-reopen" data-privacy-open>'.$e($t('Cookie settings','ตั้งค่าคุกกี้')).'</button>';
$privacyMarkup.='<section id="privacy-panel" role="region" aria-labelledby="privacy-title"'.(($choices || ($_REQUEST['modname']??'')==='privacy')?' hidden':'').' tabindex="-1"><h2 id="privacy-title">'.$e($t('Your privacy choices','ตัวเลือกความเป็นส่วนตัว')).'</h2>';
$privacyMarkup.='<p>'.$e($t('Necessary storage supports sign-in, security, and your saved choices. Optional analytics, external media, and marketing stay off until you choose to allow them.','พื้นที่จัดเก็บที่จำเป็นรองรับการเข้าสู่ระบบ ความปลอดภัย และการจดจำตัวเลือกของคุณ การวิเคราะห์ สื่อภายนอก และการตลาดจะปิดไว้จนกว่าคุณจะอนุญาต')).'</p>';
$privacyMarkup.='<p><a href="'.$e($lanaiPrivacy->policyUrl('privacy')).'">'.$e($t('Privacy notice','ประกาศความเป็นส่วนตัว')).'</a> · <a href="'.$e($lanaiPrivacy->policyUrl('cookie')).'">'.$e($t('Cookie policy','นโยบายคุกกี้')).'</a></p>';
$privacyMarkup.='<div class="privacy-actions"><button type="button" data-privacy-choice="accept">'.$e($t('Accept optional cookies','ยอมรับคุกกี้เสริม')).'</button><button type="button" data-privacy-choice="reject">'.$e($t('Reject optional cookies','ปฏิเสธคุกกี้เสริม')).'</button><button type="button" id="privacy-manage" aria-expanded="false" aria-controls="privacy-options">'.$e($t('Manage preferences','จัดการตัวเลือก')).'</button></div>';
$privacyMarkup.='<form id="privacy-options" hidden><fieldset><legend>'.$e($t('Choose what to allow','เลือกสิ่งที่อนุญาต')).'</legend><p>'.$e($t('Necessary: always available for requested site functions.','จำเป็น: พร้อมใช้งานสำหรับฟังก์ชันของเว็บไซต์ที่คุณร้องขอ')).'</p>';
foreach (array('analytics'=>array('Analytics — site visit statistics','การวิเคราะห์ — สถิติการเข้าชมเว็บไซต์'),'external'=>array('External media — optional embeds and external scripts','สื่อภายนอก — สื่อฝังและสคริปต์ภายนอกที่เป็นตัวเลือก'),'marketing'=>array('Marketing — optional advertising integrations','การตลาด — บริการโฆษณาที่เป็นตัวเลือก')) as $key=>$label) {
    $privacyMarkup.='<label><input type="checkbox" name="'.$key.'"'.(!empty($choices[$key])?' checked':'').'> '.$e($t($label[0],$label[1])).'</label>';
}
$privacyMarkup.='</fieldset><p>'.$e($t('Changes take effect after the page reloads. You can change your choices at any time.','การเปลี่ยนแปลงมีผลเมื่อโหลดหน้าใหม่ คุณเปลี่ยนตัวเลือกได้ทุกเมื่อ')).'</p><button type="submit">'.$e($t('Save preferences','บันทึกตัวเลือก')).'</button></form><p id="privacy-status" role="status" aria-live="polite"></p><noscript><a href="module.php?modname=privacy">'.$e($t('Manage preferences without JavaScript','จัดการตัวเลือกโดยไม่ใช้ JavaScript')).'</a></noscript></section></div><script src="assets/js/privacy.js" defer></script>';
foreach ($lanaiPrivacy->config()['scripts'] as $script) if ($lanaiPrivacy->allows($script['category'])) $privacyMarkup.='<script src="'.$e($script['url']).'" defer data-lanai-consent="'.$e($script['category']).'"></script>';
return $privacyMarkup;
