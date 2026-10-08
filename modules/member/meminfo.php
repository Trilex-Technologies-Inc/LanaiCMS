<?php
if (stripos($_SERVER['PHP_SELF'], 'module.php') === false) { http_response_code(403); exit; }
global $db, $cfg, $cfg_datadir, $sys_lanai;
include_once __DIR__ . '/module.php';
// Keep older activation links working independently of the account area.
if (isset($_GET['ac']) && $_GET['ac'] === 'activate') { include __DIR__ . '/memactivate.php'; return; }
if (empty($_SESSION['uid']) || !is_numeric($_SESSION['uid']) || (int)$_SESSION['uid'] <= 0) {
    echo '<p><a href="module.php?modname=member&amp;mf=memloginform">' . _SIGNIN . '</a></p>';
    return;
}
$member = new User();
$record = $member->getUser((int)$_SESSION['uid']);
if (!$record || $record->EOF || $record->fields['userActive'] !== 'y') { return; }
$user = $record->fields;
if (!defined('LANAI_MEMBER_AREA')) { define('LANAI_MEMBER_AREA', true); }
require_once __DIR__ . '/account.php';
$labels = require __DIR__ . '/language/account-' . (($cfg['lang'] ?? '') === 'thai' ? 'thai' : 'english') . '.php';
require_once __DIR__.'/../../include/lanai/localization.php';
if (($cfg['lang'] ?? '') !== 'thai') $labels = array_map('lanai_translate', $labels);
$escape = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
// This allowlist is the extension point for future account sections.
$sections = array('overview' => $labels['overview'], 'profile' => $labels['profile'], 'security' => $labels['security']);
$section = memberAreaInput($_GET, 'section');
if ($section === '' && memberAreaInput($_REQUEST, 'ac') === 'edit') { $section = 'profile'; }
if (!isset($sections[$section])) { $section = 'overview'; }
$base = 'module.php?modname=member&mf=meminfo';
$errors = array();
$saved = false;
$values = $user;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($section, array('profile','security'), true)) {
    if (!$sys_lanai->validateCsrfToken('member', memberAreaInput($_POST, 'csrf_token'))) {
        $errors[] = 'csrf';
    } else {
        list($changes, $errors) = memberAreaValidate($_POST, $user, $section, $sys_lanai);
        if ($section === 'security' && !empty($_SESSION['mfa_verified']) && !$errors) {
            try { if (!lanai_mfa_service()->verify((int)$user['userId'], memberAreaInput($_POST, 'mfaCode'))) $errors[] = 'mfa'; }
            catch (Throwable $error) { $errors[] = 'mfa'; }
        }
        $values = array_merge($user, $changes);
        if ($section === 'security') {
            $duplicate = $db->execute('SELECT userId FROM ' . $cfg['tablepre'] . 'user WHERE userId <> ' . (int)$user['userId']
                . ' AND (userLogin=' . $db->qstr($changes['userLogin']) . ' OR userEmail=' . $db->qstr($changes['userEmail']) . ')');
            if (!$duplicate) { $errors[] = 'save'; }
            elseif (!$duplicate->EOF) { $errors[] = 'duplicate'; }
        }
        $avatar = $_FILES['userAvatar'] ?? null;
        $upload = $section === 'profile' && is_array($avatar) && ($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($upload) {
            $info = is_string($avatar['tmp_name'] ?? null) ? @getimagesize($avatar['tmp_name']) : false;
            if (($avatar['error'] ?? -1) !== UPLOAD_ERR_OK || !$info || $info[2] !== IMAGETYPE_GIF
                || ($avatar['size'] ?? 0) > 2097152 || $info[0] > 2048 || $info[1] > 2048
                || !is_uploaded_file($avatar['tmp_name'])) { $errors[] = 'avatar'; }
        }
        if (!$errors) {
            $assignments = array();
            foreach ($changes as $field => $value) { $assignments[] = $field . '=' . $db->qstr($value); }
            // The validated fields never include privilege, role, status, or another user's ID.
            $saved = (bool)$db->execute('UPDATE ' . $cfg['tablepre'] . 'user SET ' . implode(', ', $assignments) . ' WHERE userId=' . (int)$user['userId']);
            if (!$saved) { $errors[] = 'save'; }
            if ($saved && $upload) {
                $target = rtrim($cfg_datadir, '/\\') . DIRECTORY_SEPARATOR . 'uimage' . DIRECTORY_SEPARATOR . 'u' . (int)$user['userId'] . '.gif';
                if (!move_uploaded_file($avatar['tmp_name'], $target)) { $errors[] = 'upload'; }
            }
            if ($saved) { $user = array_merge($user, $changes); }
        }
    }
}
$field = static function ($name, $label, $type = 'text', $required = false, $limit = 255) use ($escape, &$values) {
    echo '<label class="member-field" for="ma-' . $escape($name) . '"><span>' . $escape($label) . ($required ? ' *' : '') . '</span>';
    echo '<input id="ma-' . $escape($name) . '" name="' . $escape($name) . '" type="' . $type . '" maxlength="' . (int)$limit . '"'
        . ($required ? ' required' : '') . ($type === 'password' ? ' autocomplete="' . ($name === 'currentPassword' ? 'current-password' : 'new-password') . '"' : '')
        . ' value="' . ($type === 'password' ? '' : $escape($values[$name] ?? '')) . '"></label>';
};
?>
<link rel="stylesheet" href="assets/member-area.css">
<section class="member-area" aria-labelledby="member-title">
    <header class="member-header">
        <div class="member-avatar" aria-hidden="true"><?php if ($member->isUserImageExist((int)$user['userId'])): ?>
            <img src="datacenter/uimage/u<?= (int)$user['userId'] ?>.gif?v=<?= $saved ? time() : 0 ?>" alt="">
        <?php else: ?><?= $escape(mb_substr($user['userFname'] ?: $user['userLogin'], 0, 1)) ?><?php endif; ?></div>
        <div><h1 id="member-title"><?= $escape($labels['title']) ?></h1><p><?= $escape(trim($user['userFname'] . ' ' . $user['userLname'])) ?></p></div>
    </header>
    <?php
    // Role-based staff (for example contributors) get the same entry point as administrators.
    $memberIsStaff = false;
    if (($user['userPrivilege'] ?? '') !== 'a' && isset($sys_lanai) && method_exists($sys_lanai, 'userHasCapability')) {
        require_once __DIR__ . '/../../administrator/access.php';
        $memberIsStaff = (bool)lanai_staff_modules($user, static function ($capability) use ($sys_lanai, $user) {
            return $sys_lanai->userHasCapability($capability, (int)$user['userId']);
        });
    }
    ?>
    <div class="member-layout">
        <nav class="member-nav" aria-label="<?= $escape($labels['title']) ?>">
            <a href="module.php?modname=privacy&amp;view=data"><?= $escape(($cfg['lang']??'')==='thai'?'ข้อมูลส่วนบุคคล':'Personal data') ?></a>
            <?php foreach ($sections as $key => $label): ?><a href="<?= $escape($base . '&section=' . $key) ?>" <?= $section === $key ? 'aria-current="page"' : '' ?>><?= $escape($label) ?></a><?php endforeach; ?>
            <?php if ($user['userPrivilege'] === 'a' || $memberIsStaff): ?><a href="setting.php"><?= $escape($labels['admin']) ?></a><?php endif; ?>
            <a href="module.php?modname=member&amp;mf=memlogout"><?= _USER_LOGOUT ?></a>
        </nav>
        <div class="member-panel">
            <h2><?= $escape($sections[$section]) ?></h2>
            <?php foreach (array_unique($errors) as $error): ?><p class="member-error" role="alert"><?= $escape($labels[$error]) ?></p><?php endforeach; ?>
            <?php if ($saved): ?><p class="member-success" role="status"><?= $escape($labels['saved']) ?></p><?php endif; ?>
            <?php if ($section === 'overview'): ?>
                <p><?= $escape($labels['intro']) ?></p>
                <dl class="member-details"><dt><?= _USER_LOGIN ?></dt><dd><?= $escape($user['userLogin']) ?></dd><dt><?= _USER_EMAIL ?></dt><dd><?= $escape($user['userEmail']) ?></dd><dt><?= $escape($labels['joined']) ?></dt><dd><?= $escape($user['userCreated']) ?></dd></dl>
                <div class="member-cards"><?php foreach (array('profile','security') as $key): ?><a href="<?= $escape($base . '&section=' . $key) ?>"><strong><?= $escape($sections[$key]) ?></strong><span><?= $escape($labels[$key . '_hint']) ?></span></a><?php endforeach; ?></div>
            <?php else: ?>
                <p><?= $escape($labels[$section . '_hint']) ?></p>
                <form method="post" action="<?= $escape($base . '&section=' . $section) ?>" enctype="multipart/form-data">
                    <?php $sys_lanai->renderCsrfField('member'); ?>
                    <div class="member-fields">
                    <?php if ($section === 'profile'):
                        $field('userFname', _USER_FNAME, 'text', true, 100);
                        $field('userLname', _USER_LNAME, 'text', true, 100);
                        $field('userURL', _USER_URL, 'url', false, 255);
                        $field('userPhone', _USER_PHONE, 'tel', false, 20);
                        $field('userMobile', _USER_MOBILE, 'tel', false, 20);
                        $field('userFax', _USER_FAX, 'tel', false, 20);
                        $field('userAddress1', _USER_ADDRESS1, 'text', false, 150);
                        $field('userAddress2', $labels['address2'], 'text', false, 150);
                        $field('userCity', _USER_CITY, 'text', false, 100);
                        $field('userState', _USER_STATE, 'text', false, 100);
                        $field('userZipcode', _USER_ZIPCODE, 'text', false, 15);
                    ?>
                        <label class="member-field"><span><?= _USER_COUNTRY ?></span><select name="cntId" required><?php
                        $countries = $db->execute('SELECT cntId, cntName FROM ' . $cfg['tablepre'] . 'country ORDER BY cntName');
                        if ($countries) { while (!$countries->EOF) { ?><option value="<?= $escape($countries->fields['cntId']) ?>" <?= $values['cntId'] === $countries->fields['cntId'] ? 'selected' : '' ?>><?= $escape($countries->fields['cntName']) ?></option><?php $countries->moveNext(); } }
                        ?></select></label>
                        <label class="member-field"><span><?= _USER_AVATAR ?></span><input type="file" name="userAvatar" accept="image/gif"><small><?= $escape($labels['avatar_hint']) ?></small></label>
                    <?php else:
                        $field('userLogin', _USER_LOGIN, 'text', true, 50);
                        $field('userEmail', _USER_EMAIL, 'email', true, 254);
                        $field('currentPassword', $labels['current_label'], 'password', true, 255);
                        $field('userPassword1', $labels['new_password'], 'password', false, 72);
                        $field('userPassword2', $labels['confirm_password'], 'password', false, 72);
                    endif; ?>
                    </div>
                    <?php if ($section === 'security' && !empty($_SESSION['mfa_verified'])) { $field('mfaCode', $labels['mfa_code'], 'text', true, 40); } ?>
                    <?php if ($section === 'security'): ?><p><a href="module.php?modname=member&amp;mf=memmfa"><?= $escape($labels['mfa_manage']) ?></a></p><p><?= $escape($labels['password_hint']) ?></p><?php endif; ?>
                    <button class="member-save" type="submit"><?= _SAVE ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>
