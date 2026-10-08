<?php
if (basename($_SERVER['PHP_SELF'] ?? '') !== 'module.php') { http_response_code(403); exit; }
global $db, $cfg, $sys_lanai;
require_once __DIR__ . '/module.php';
require_once __DIR__ . '/../../include/lanai/class.mfa.php';
$thai = ($cfg['lang'] ?? '') === 'thai';
$t = static function($en, $th) use ($thai) { return $thai ? $th : $en; };
$e = static function($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$input = static function($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; };
$pending = $_SESSION['mfa_pending'] ?? null;
$uid = (int)($_SESSION['uid'] ?? ($pending['uid'] ?? 0));
$message = ''; $codes = false; $row = null; $setup = null;
try {
    $service = lanai_mfa_service();
    if ($uid < 1) throw new RuntimeException('login');
    $member = new User(); $record = $member->getUser($uid);
    if (!$record || $record->EOF || $record->fields['userActive'] !== 'y') throw new RuntimeException('login');
    $row = $service->state($uid);
    if (empty($_SESSION['uid'])) {
        if (!$pending || $pending['expires'] < time() || !$row || !$row['mfaEnabled'] || !hash_equals($row['mfaVersion'], $pending['version'])) {
            unset($_SESSION['mfa_pending']); throw new RuntimeException('login');
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (!$sys_lanai->validateCsrfToken('mfa', $input('csrf_token'))) throw new RuntimeException('request');
            if ($input('action') === 'cancel') { unset($_SESSION['mfa_pending']); $sys_lanai->go2Page('module.php?modname=member&mf=memloginform'); return; }
            if ($service->verify($uid, $input('code'))) {
                session_regenerate_id(true);
                $_SESSION['uid'] = $uid; $_SESSION['mfa_verified'] = $row['mfaVersion'];
                unset($_SESSION['mfa_pending']);
                $sys_lanai->go2Page('module.php?modname=member&mf=meminfo'); return;
            }
            $message = $t('Invalid or already used code, or too many attempts. After five attempts, wait five minutes and sign in again.', 'รหัสไม่ถูกต้อง ถูกใช้แล้ว หรือพยายามมากเกินไป หลังลองห้าครั้ง กรุณารอห้านาทีแล้วเข้าสู่ระบบใหม่');
        }
    } else {
        $service->provision($uid); $row = $service->state($uid);
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (!$sys_lanai->validateCsrfToken('mfa', $input('csrf_token'))) throw new RuntimeException('request');
            $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
            if (!$sys_lanai->verifyPassword($password, $record->fields['userPassword'])) {
                $service->attempt($uid); throw new RuntimeException('verification');
            }
            $action = $input('action');
            if ($action === 'start' && !$row['mfaEnabled']) {
                if (!$service->ready() || !$service->attempt($uid)) throw new RuntimeException('verification');
                $_SESSION['mfa_setup'] = array('uid'=>$uid, 'secret'=>LanaiMfa::secret(), 'expires'=>time()+600);
            } elseif ($action === 'enable' && !$row['mfaEnabled']) {
                $setup = $_SESSION['mfa_setup'] ?? null;
                if (!$setup || $setup['uid'] !== $uid || $setup['expires'] < time()) throw new RuntimeException('verification');
                $codes = $service->enable($uid, $setup['secret'], $input('code'));
                if (!$codes) throw new RuntimeException('verification');
                unset($_SESSION['mfa_setup']);
                $row = $service->state($uid);
                $_SESSION['mfa_verified'] = $row['mfaVersion'];
                session_regenerate_id(true);
            } elseif ($action === 'disable' && $row['mfaEnabled']) {
                if (!$service->verify($uid, $input('code')) || !$service->disable($uid, $row['mfaVersion'])) throw new RuntimeException('verification');
                unset($_SESSION['mfa_setup'], $_SESSION['mfa_verified']);
                $row = $service->state($uid);
                $message = $t('Authenticator protection has been disabled.', 'ปิดการยืนยันตัวตนสองขั้นตอนแล้ว');
            }
        }
        $setup = $_SESSION['mfa_setup'] ?? null;
        if ($setup && ($setup['uid'] !== $uid || $setup['expires'] < time())) { unset($_SESSION['mfa_setup']); $setup = null; }
    }
} catch (Throwable $error) {
    $message = $t('Verification could not be completed. Check your current password and code, or wait five minutes before trying again. If this continues, contact the site administrator.', 'ไม่สามารถยืนยันได้ กรุณาตรวจสอบรหัสผ่านและรหัสยืนยัน หรือรอห้านาทีแล้วลองอีกครั้ง หากยังมีปัญหาให้ติดต่อผู้ดูแลเว็บไซต์');
    if ($error->getMessage() === 'login') $message = $t('Please sign in again to continue.', 'กรุณาเข้าสู่ระบบใหม่เพื่อดำเนินการต่อ');
}
?>
<link rel="stylesheet" href="assets/member-area.css">
<section class="member-area"><div class="member-panel">
<h1><?= $e($t('Two-factor authentication', 'การยืนยันตัวตนสองขั้นตอน')) ?></h1>
<?php if ($message): ?><p role="alert"><?= $e($message) ?></p><?php endif; ?>
<?php if ($codes): ?>
<h2><?= $e($t('Save your recovery codes now', 'เก็บรหัสกู้คืนของคุณตอนนี้')) ?></h2>
<p><?= $e($t('These codes are shown only once. Store them safely offline. Each code can be used once instead of an authenticator code; you still need your password.', 'รหัสเหล่านี้แสดงเพียงครั้งเดียว กรุณาเก็บไว้ในที่ปลอดภัย แต่ละรหัสใช้แทนรหัสจากแอปได้หนึ่งครั้ง โดยยังต้องใช้รหัสผ่าน')) ?></p>
<pre><?= $e(implode("\n", $codes)) ?></pre>
<?php elseif (empty($_SESSION['uid']) && $row && $pending && $pending['expires'] >= time()): ?>
<p><?= $e($t('Enter the six-digit code from your authenticator app, or a recovery code.', 'กรอกรหัสหกหลักจากแอปยืนยันตัวตน หรือรหัสกู้คืน')) ?></p>
<form method="post" action="module.php?modname=member&amp;mf=memmfa">
<?php $sys_lanai->renderCsrfField('mfa'); ?>
<label class="member-field"><?= $e($t('Verification code', 'รหัสยืนยัน')) ?><input name="code" autocomplete="one-time-code" maxlength="40" required autofocus></label>
<button class="member-save" name="action" value="verify"><?= $e($t('Verify and sign in', 'ยืนยันและเข้าสู่ระบบ')) ?></button>
<button name="action" value="cancel" formnovalidate><?= $e($t('Cancel sign-in', 'ยกเลิกการเข้าสู่ระบบ')) ?></button>
</form>
<?php elseif (!empty($_SESSION['uid']) && $row): ?>
<p><?= $e($row['mfaEnabled'] ? $t('Authenticator protection is enabled.', 'เปิดการยืนยันตัวตนสองขั้นตอนแล้ว') : $t('Authenticator protection is not enabled.', 'ยังไม่ได้เปิดการยืนยันตัวตนสองขั้นตอน')) ?></p>
<?php if (!$row['mfaEnabled'] && !$service->ready()): ?>
<p><?= $e($t('The site administrator needs to configure secure MFA storage before enrollment is available.', 'ผู้ดูแลเว็บไซต์ต้องตั้งค่าการจัดเก็บข้อมูล MFA อย่างปลอดภัยก่อนเปิดใช้งาน')) ?></p>
<?php else: ?>
<?php if ($setup && !$row['mfaEnabled']): ?>
<?php require_once __DIR__ . '/../../include/lanai/qr_svg.php'; $otpUri = LanaiMfa::otpauthUri($setup['secret'], $cfg['title'], $record->fields['userLogin']); ?>
<p><?= $e($t('Scan this QR code with your authenticator app, such as Google Authenticator, Microsoft Authenticator or Authy.', 'สแกนคิวอาร์โค้ดนี้ด้วยแอปยืนยันตัวตนของคุณ เช่น Google Authenticator, Microsoft Authenticator หรือ Authy')) ?></p>
<?php $qrSvg = lanai_qr_svg($otpUri, $t('QR code for your authenticator app', 'คิวอาร์โค้ดสำหรับแอปยืนยันตัวตน')); ?>
<?php if ($qrSvg !== ''): ?><div class="mfa-qr" style="max-width:240px;margin:1rem 0"><?= $qrSvg ?></div><?php endif; ?>
<details<?= $qrSvg === '' ? ' open' : '' ?>><summary><?= $e($t('Can\'t scan? Enter the setup key manually', 'สแกนไม่ได้? ป้อนรหัสตั้งค่าด้วยตนเอง')) ?></summary>
<p><?= $e($t('Add an account manually in your authenticator app: time-based, six digits, SHA-1, 30 seconds. Enter this setup key:', 'เพิ่มบัญชีในแอปยืนยันตัวตนด้วยตนเอง: แบบอิงเวลา หกหลัก SHA-1 ทุก 30 วินาที โดยใช้คีย์นี้:')) ?></p>
<pre><?= $e($setup['secret']) ?></pre>
<p><?= $e($cfg['title'] . ': ' . $record->fields['userLogin']) ?></p>
</details>
<?php endif; ?>
<form method="post" action="module.php?modname=member&amp;mf=memmfa">
<?php $sys_lanai->renderCsrfField('mfa'); ?>
<label class="member-field"><?= $e($t('Current password', 'รหัสผ่านปัจจุบัน')) ?><input name="password" type="password" autocomplete="current-password" required></label>
<?php if ($row['mfaEnabled'] || $setup): ?><label class="member-field"><?= $e($t('Authenticator code (or recovery code when disabling)', 'รหัสจากแอป (หรือรหัสกู้คืนสำหรับปิดใช้งาน)')) ?><input name="code" autocomplete="one-time-code" maxlength="40" required></label><?php endif; ?>
<button class="member-save" name="action" value="<?= $row['mfaEnabled'] ? 'disable' : ($setup ? 'enable' : 'start') ?>"><?= $e($row['mfaEnabled'] ? $t('Disable MFA', 'ปิด MFA') : ($setup ? $t('Verify and enable', 'ยืนยันและเปิดใช้งาน') : $t('Set up authenticator', 'ตั้งค่าแอปยืนยันตัวตน'))) ?></button>
</form>
<?php endif; endif; ?>
<p><a href="module.php?modname=member&amp;mf=<?= empty($_SESSION['uid']) ? 'memloginform' : 'meminfo&amp;section=security' ?>"><?= $e($t('Back to account / sign in', 'กลับไปบัญชี / เข้าสู่ระบบ')) ?></a></p>
</div></section>
