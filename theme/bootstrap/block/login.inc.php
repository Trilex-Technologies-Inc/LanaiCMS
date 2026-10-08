<?php
global $sys_lanai, $cfg;
require_once 'include/lanai/class.system.php';
if (!isset($sys_lanai)) $sys_lanai = new Systems();
$accountPanelUser = null;
if (!empty($_SESSION['uid']) && is_numeric($_SESSION['uid']) && (int)$_SESSION['uid'] > 0) {
    require_once 'modules/member/module.php';
    $accountPanelMember = new User();
    $accountPanelRecord = $accountPanelMember->getUser((int)$_SESSION['uid']);
    if ($accountPanelRecord && !$accountPanelRecord->EOF && ($accountPanelRecord->fields['userActive'] ?? '') === 'y') {
        $accountPanelUser = $accountPanelRecord->fields;
    }
}
$accountPanelStaff = false;
if ($accountPanelUser && ($accountPanelUser['userPrivilege'] ?? '') !== 'a') {
    require_once __DIR__ . '/../../../administrator/access.php';
    $accountPanelStaff = (bool)lanai_staff_modules($accountPanelUser, static function ($capability) use ($sys_lanai, $accountPanelUser) {
        return $sys_lanai->userHasCapability($capability, (int)$accountPanelUser['userId']);
    });
}
$accountPanelEscape = static function($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$accountPanelThai = ($cfg['lang'] ?? '') === 'thai';
?>
<?php if (!$accountPanelUser): ?>
<div class="login-form">
    <a class="btn btn-light" href="module.php?modname=member&amp;mf=memloginform"><?= $accountPanelThai ? 'เข้าสู่ระบบ' : 'Sign in' ?></a>
    <a class="btn btn-light" href="module.php?modname=member&amp;mf=memsignup"><?= $accountPanelThai ? 'สมัครสมาชิก' : 'Sign up' ?></a>
</div>
<?php else: ?>
<div class="user-info d-flex flex-wrap justify-content-between align-items-center gap-3 text-white">
    <div>
        <i class="fas fa-user-circle me-2" aria-hidden="true"></i>
        <span><?= $accountPanelThai ? 'ยินดีต้อนรับ' : 'Welcome,' ?> <?= $accountPanelEscape(trim($accountPanelUser['userFname'].' '.$accountPanelUser['userLname']) ?: $accountPanelUser['userLogin']) ?></span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-light btn-sm" href="module.php?modname=member&amp;mf=meminfo"><?= $accountPanelThai ? 'บัญชีของฉัน' : 'My account' ?></a>
        <?php if ($accountPanelUser['userPrivilege'] === 'a' || $accountPanelStaff): ?>
        <a class="btn btn-light btn-sm" href="setting.php"><?= $accountPanelThai ? 'ตั้งค่าเว็บไซต์' : 'Site settings' ?></a>
        <?php endif; ?>
        <a class="btn btn-outline-light btn-sm" href="module.php?modname=member&amp;mf=memlogout"><?= $accountPanelThai ? 'ออกจากระบบ' : 'Sign out' ?></a>
    </div>
</div>
<?php endif; ?>
