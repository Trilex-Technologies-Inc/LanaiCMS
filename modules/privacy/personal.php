<?php
if (!isset($lanaiPrivacy,$sys_lanai,$cfg,$e,$t)) { http_response_code(403); exit; }
global $db;
$service=new LanaiPrivacyData($db,$cfg['tablepre'],$cfg['datadir']);
?>
<h1><?= $e($t('Your personal data','ข้อมูลส่วนบุคคลของคุณ')) ?></h1>
<p><?= $e($t('Download your account data or ask the site operator to act on a privacy request. Published content, uploaded files, comments, emails and external services may require a separate review.','ดาวน์โหลดข้อมูลบัญชีหรือส่งคำขอเกี่ยวกับข้อมูลส่วนบุคคลถึงผู้ดูแลเว็บไซต์ เนื้อหา ไฟล์ ความคิดเห็น อีเมล และบริการภายนอกอาจต้องตรวจสอบเพิ่มเติม')) ?></p>
<?php if (filter_var($cfg['email']??'',FILTER_VALIDATE_EMAIL)): ?><p><a href="mailto:<?= $e($cfg['email']) ?>"><?= $e($t('Contact the privacy team','ติดต่อผู้ดูแลด้านความเป็นส่วนตัว')) ?></a></p><?php endif; ?>
<?php if (empty($_SESSION['uid'])): ?>
<p><?= $e($t('If you do not have an account, contact the site operator using the privacy notice. Please do not send passwords or identity documents unless a secure verification method has been arranged.','หากไม่มีบัญชี โปรดติดต่อผู้ดูแลตามประกาศความเป็นส่วนตัว อย่าส่งรหัสผ่านหรือเอกสารประจำตัวหากยังไม่ได้ตกลงวิธีตรวจสอบที่ปลอดภัย')) ?></p>
<p><a href="module.php?modname=member&amp;mf=memloginform"><?= $e($t('Sign in to manage account data','เข้าสู่ระบบเพื่อจัดการข้อมูลบัญชี')) ?></a></p>
<?php return; endif;
try {
    $service->user($_SESSION['uid']);
    $offset=max(0,min(100000,(int)($_GET['offset']??0)));
    $requests=$service->requests($_SESSION['uid'],false,$offset);
} catch (Throwable $error) { echo '<p role="alert">'.$e($t('Personal-data tools are unavailable. Please contact the site operator.','เครื่องมือข้อมูลส่วนบุคคลไม่พร้อมใช้งาน โปรดติดต่อผู้ดูแลเว็บไซต์')).'</p>'; return; }
if (isset($_SESSION['privacy_data_message'])) { echo '<p role="status">'.$e($_SESSION['privacy_data_message']).'</p>'; unset($_SESSION['privacy_data_message']); }
$types=array('export'=>$t('Full access / export request','ขอเข้าถึงหรือส่งออกข้อมูลทั้งหมด'),'erase'=>$t('Erase my account and personal data','ขอลบบัญชีและข้อมูลส่วนบุคคล'),'rectify'=>$t('Correct my data','ขอแก้ไขข้อมูล'),'restrict'=>$t('Restrict processing','ขอจำกัดการประมวลผล'),'object'=>$t('Object to processing','คัดค้านการประมวลผล'),'other'=>$t('Other privacy request','คำขออื่นเกี่ยวกับความเป็นส่วนตัว'));
?>
<form method="post" action="privacy-data.php">
<?php $sys_lanai->renderCsrfField('privacy_data'); ?>
<p><?= $e($t('Confirm your current password for either action. If MFA is enabled, enter a fresh authenticator or recovery code.','ยืนยันรหัสผ่านปัจจุบัน หากเปิด MFA ให้กรอกรหัสยืนยันหรือรหัสกู้คืนใหม่')) ?></p>
<label class="d-block mb-3"><?= $e($t('Current password','รหัสผ่านปัจจุบัน')) ?><input class="form-control" type="password" name="currentPassword" autocomplete="current-password" required maxlength="255"></label>
<label class="d-block mb-3"><?= $e($t('Authenticator or recovery code (if enabled)','รหัสยืนยันหรือรหัสกู้คืน (หากเปิดใช้งาน)')) ?><input class="form-control" name="mfaCode" autocomplete="one-time-code" maxlength="64"></label>
<button class="btn btn-primary" name="action" value="export" type="submit"><?= $e($t('Download account data (JSON)','ดาวน์โหลดข้อมูลบัญชี (JSON)')) ?></button>
<p><?= $e($t('The download contains account-linked records and your avatar. For media file copies, comments or other records, submit a full access request below.','ไฟล์ดาวน์โหลดประกอบด้วยข้อมูลที่เชื่อมโยงกับบัญชีและรูปประจำตัว หากต้องการไฟล์สื่อ ความคิดเห็น หรือข้อมูลอื่น โปรดส่งคำขอด้านล่าง')) ?></p>
<hr>
<label class="d-block mb-3"><?= $e($t('Request type','ประเภทคำขอ')) ?><select class="form-select" name="requestType"><?php foreach ($types as $key=>$label): ?><option value="<?= $key ?>"><?= $e($label) ?></option><?php endforeach; ?></select></label>
<label class="d-block mb-3"><?= $e($t('Details (optional; do not include passwords)','รายละเอียด (ไม่บังคับ อย่าใส่รหัสผ่าน)')) ?><textarea class="form-control" name="details" maxlength="4000" rows="4"></textarea></label>
<p><?= $e($t('Erasure is reviewed before it happens. Once completed, your account is disabled and its personal profile, avatar and credentials are removed. The operator reviews published material and other copies separately.','คำขอลบจะได้รับการตรวจสอบก่อนดำเนินการ เมื่อเสร็จสิ้น บัญชีจะถูกปิดใช้งานและข้อมูลส่วนตัว รูปประจำตัว และข้อมูลรับรองจะถูกลบ ผู้ดูแลจะตรวจสอบเนื้อหาและสำเนาอื่นแยกต่างหาก')) ?></p>
<button class="btn btn-outline-primary" name="action" value="request" type="submit"><?= $e($t('Submit privacy request','ส่งคำขอเกี่ยวกับความเป็นส่วนตัว')) ?></button>
</form>
<h2 class="mt-4"><?= $e($t('Your requests','คำขอของคุณ')) ?></h2>
<?php if (!$requests): ?><p><?= $e($t('No requests on this page.','ไม่มีคำขอในหน้านี้')) ?></p><?php endif; ?>
<?php foreach ($requests as $request): ?><article class="border rounded p-3 mb-3">
<h3 class="h5"><?= $e($types[$request['requestType']]??$request['requestType']) ?></h3>
<p><?= $e($request['requestStatus']) ?> · <?= $e($t('Due','กำหนดตอบกลับ')) ?> <?= $e(gmdate('Y-m-d',(int)$request['dueAt'])) ?> UTC</p>
<p><?= nl2br($e($request['requestDetails'])) ?></p><p><?= nl2br($e($request['responseText'])) ?></p>
</article><?php endforeach; ?>
<?php if ($offset>0): ?><a href="module.php?modname=privacy&amp;view=data&amp;offset=<?= max(0,$offset-50) ?>"><?= $e($t('Previous','ก่อนหน้า')) ?></a><?php endif; ?>
<?php if (count($requests)===50): ?><a href="module.php?modname=privacy&amp;view=data&amp;offset=<?= $offset+50 ?>"><?= $e($t('Next','ถัดไป')) ?></a><?php endif; ?>
