<?php
if (basename($_SERVER['PHP_SELF']??'')!=='module.php') { http_response_code(403); exit; }
global $lanaiPrivacy,$sys_lanai,$cfg;
$e=static function($v) { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); };
require_once __DIR__.'/../../include/lanai/localization.php';
$thai=($cfg['lang']??'')==='thai'; $t=static function($en,$th) { return lanai_translate($en, null, $th); };
$view=is_string($_GET['view']??null)?$_GET['view']:'';
$config=$lanaiPrivacy->config(); $choices=$lanaiPrivacy->choices();
?>
<section class="container my-4">
<?php if ($view==='data'): include __DIR__.'/personal.php'; ?>
<?php elseif ($view==='privacy'): ?>
<h1><?= $e($t('Privacy notice','ประกาศความเป็นส่วนตัว')) ?></h1>
<p><?= $config['privacyText']!=='' ? nl2br($e($config['privacyText'])) : $e($t('The site operator has not published a privacy notice yet. Please contact the site operator for information about how your personal information is handled.','ผู้ดูแลเว็บไซต์ยังไม่ได้เผยแพร่ประกาศความเป็นส่วนตัว กรุณาติดต่อผู้ดูแลเพื่อสอบถามการจัดการข้อมูลส่วนบุคคล')) ?></p>
<?php elseif ($view==='cookie'): ?>
<h1><?= $e($t('Cookie policy','นโยบายคุกกี้')) ?></h1>
<p><?= $e($t('This page describes storage and optional services managed by LanaiCMS.','หน้านี้อธิบายการจัดเก็บข้อมูลและบริการเสริมที่จัดการโดย LanaiCMS')) ?></p>
<table class="table"><thead><tr><th><?= $e($t('Storage','การจัดเก็บ')) ?></th><th><?= $e($t('Purpose','วัตถุประสงค์')) ?></th><th><?= $e($t('Duration','ระยะเวลา')) ?></th></tr></thead><tbody>
<tr><td><?= $e(session_name()) ?></td><td><?= $e($t('Necessary session, login, form security and anti-abuse state','สถานะเซสชัน การเข้าสู่ระบบ ความปลอดภัยของแบบฟอร์มและการป้องกันการใช้งานผิดประเภท')) ?></td><td><?= $e($t('Session, subject to browser and server settings','ตามเซสชันและการตั้งค่าของเบราว์เซอร์และเซิร์ฟเวอร์')) ?></td></tr>
<tr><td><?= $e($lanaiPrivacy->cookieName()) ?></td><td><?= $e($t('Remembers your privacy choices','จดจำตัวเลือกความเป็นส่วนตัว')) ?></td><td><?= LanaiPrivacy::DAYS ?> <?= $e($t('days','วัน')) ?></td></tr>
</tbody></table>
<p><?= $e($t('Optional Lanai analytics record the visited page, time, country when supplied, and a daily hash derived from IP address and browser user agent. They do not set a separate analytics cookie. Declining Analytics prevents new analytics records.','การวิเคราะห์เสริมของ Lanai บันทึกหน้าที่เข้าชม เวลา ประเทศเมื่อมีข้อมูล และแฮชรายวันที่สร้างจากที่อยู่ IP และข้อมูลเบราว์เซอร์ โดยไม่ตั้งคุกกี้วิเคราะห์แยก การปฏิเสธการวิเคราะห์จะป้องกันการบันทึกข้อมูลใหม่')) ?></p>
<?php if (($cfg['captcha_provider']??'')==='cloudflare'): ?><p><?= $e($t('Protected forms use Cloudflare Turnstile for abuse prevention. Loading a protected form contacts Cloudflare.','แบบฟอร์มที่มีการป้องกันใช้ Cloudflare Turnstile เพื่อป้องกันการใช้งานผิดประเภท และจะเชื่อมต่อกับ Cloudflare เมื่อโหลดแบบฟอร์ม')) ?></p><?php endif; ?>
<p><?= $e($t('Saved choices also create a consent receipt with the policy version and settings. Signed-in choices link to your account. Consent records are retained for the configured period below; no IP address or browser user agent is stored in these receipts.','การบันทึกตัวเลือกจะสร้างบันทึกความยินยอมพร้อมรุ่นนโยบายและการตั้งค่า หากเข้าสู่ระบบจะเชื่อมโยงกับบัญชี บันทึกนี้ไม่เก็บที่อยู่ IP หรือข้อมูลเบราว์เซอร์')) ?></p>
<p><?= $e($t('Configured retention (days): analytics','ระยะเวลาเก็บข้อมูล (วัน): การวิเคราะห์')) ?> <?= (int)$config['analyticsDays'] ?>; <?= $e($t('consent receipts','บันทึกความยินยอม')) ?> <?= (int)$config['consentDays'] ?>; <?= $e($t('closed requests','คำขอที่ปิดแล้ว')) ?> <?= (int)$config['requestDays'] ?>. <?= $e($t('Removal runs on the operator’s cleanup schedule.','การลบจะทำงานตามกำหนดการของผู้ดูแล')) ?></p>
<?php if ($config['cookieText']!==''): ?><p><?= nl2br($e($config['cookieText'])) ?></p><?php endif; ?>
<?php if ($config['scripts']): ?><h2><?= $e($t('Configured optional services','บริการเสริมที่ตั้งค่าไว้')) ?></h2><ul><?php foreach ($config['scripts'] as $script): ?><li><?= $e($script['name'].' — '.$script['category'].' — '.parse_url($script['url'],PHP_URL_HOST)) ?></li><?php endforeach; ?></ul><?php endif; ?>
<p><?= $e($t('Changing preferences stops future optional loading after reload. It does not erase data already collected. Other domains control their own cookies.','การเปลี่ยนตัวเลือกจะหยุดการโหลดบริการเสริมในอนาคตหลังโหลดหน้าใหม่ แต่ไม่ลบข้อมูลที่เก็บไปแล้ว คุกกี้ของโดเมนอื่นอยู่ภายใต้การควบคุมของโดเมนนั้น')) ?></p>
<?php else: ?>
<h1><?= $e($t('Cookie preferences','ตัวเลือกคุกกี้')) ?></h1>
<?php if (isset($_GET['saved']) && $choices): ?><p role="status"><?= $e($t('Your preferences have been saved.','บันทึกตัวเลือกของคุณแล้ว')) ?></p><?php endif; ?>
<p><?= $e($t('Necessary storage remains available. Choose which optional categories to allow.','การจัดเก็บที่จำเป็นยังคงพร้อมใช้งาน เลือกหมวดหมู่เสริมที่ต้องการอนุญาต')) ?></p>
<form method="post" action="privacy.php">
<?php $sys_lanai->renderCsrfField('privacy'); ?><input type="hidden" name="revision" value="<?= (int)$config['revision'] ?>">
<?php foreach (array('analytics'=>$t('Analytics','การวิเคราะห์'),'external'=>$t('External media','สื่อภายนอก'),'marketing'=>$t('Marketing','การตลาด')) as $key=>$label): ?>
<label class="d-block mb-3"><?= $e($label) ?><select class="form-select" name="<?= $key ?>"><option value="0"><?= $e($t('Do not allow','ไม่อนุญาต')) ?></option><option value="1"<?= !empty($choices[$key])?' selected':'' ?>><?= $e($t('Allow','อนุญาต')) ?></option></select></label>
<?php endforeach; ?><button class="btn btn-primary" type="submit"><?= $e($t('Save preferences','บันทึกตัวเลือก')) ?></button></form>
<?php endif; ?>
<p><a href="module.php?modname=privacy"><?= $e($t('Cookie settings','ตั้งค่าคุกกี้')) ?></a> · <a href="<?= $e($lanaiPrivacy->policyUrl('privacy')) ?>"><?= $e($t('Privacy notice','ประกาศความเป็นส่วนตัว')) ?></a> · <a href="<?= $e($lanaiPrivacy->policyUrl('cookie')) ?>"><?= $e($t('Cookie policy','นโยบายคุกกี้')) ?></a></p>
</section>
<p class="container"><a href="module.php?modname=privacy&amp;view=data"><?= $e($t('Manage personal data and privacy requests','จัดการข้อมูลส่วนบุคคลและคำขอ')) ?></a></p>
