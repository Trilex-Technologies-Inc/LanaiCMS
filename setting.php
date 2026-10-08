<?php
// All legacy settings URLs continue to use this authenticated entry point.
define('LANAI_ADMIN_REQUEST', true);
ob_start();
require_once __DIR__ . '/setconfig.inc.php';
ob_end_clean();
require_once __DIR__ . '/administrator/access.php';
require_once __DIR__ . '/modules/member/module.php';
header('Cache-Control: no-store, private');
header('X-Robots-Tag: noindex, nofollow');
$mem_lanai = new User();
$mem = !empty($_SESSION['uid']) && is_numeric($_SESSION['uid']) && (int)$_SESSION['uid'] > 0
    ? $mem_lanai->getUser((int)$_SESSION['uid']) : false;
$isFullAdmin = $mem && lanai_admin_allowed($mem->fields);
$staffModules = array();
if ($mem && !$isFullAdmin) {
    $staffUid = (int)$mem->fields['userId'];
    $staffModules = lanai_staff_modules($mem->fields, static function ($capability) use ($sys_lanai, $staffUid) {
        return $sys_lanai->userHasCapability($capability, $staffUid);
    });
}
if (!$mem || (!$isFullAdmin && !$staffModules)) {
    http_response_code(403);
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Administrator access required</title><body><h1>Administrator access required</h1><p><a href="module.php?modname=member&amp;mf=memloginform">Sign in</a></p></body></html>';
    exit;
}
$modname = $_REQUEST['modname'] ?? '';
$mf = $_REQUEST['mf'] ?? '';
$route = lanai_admin_route(__DIR__, $modname, $mf);
if (!$isFullAdmin && $route) {
    if ($route['module'] === 'setting' && basename($route['file']) === 'dashboard.php') {
        // The overview is for administrators; staff land on their first permitted area.
        header('Location: setting.php?modname=' . $staffModules[0], true, 302);
        exit;
    }
    if (!in_array($route['module'], $staffModules, true)) {
        $route = false;
        http_response_code(403);
        $forbidden = true;
    }
}
$settingUserName = trim($mem->fields['userFname'] . ' ' . $mem->fields['userLname']) ?: $mem->fields['userLogin'];
$isSettingsDashboard = $route && $route['module'] === 'setting' && basename($route['file']) === 'dashboard.php';
$settingModule = '';
$adminHead = '';
if (!$route) {
    if (empty($forbidden)) http_response_code(404);
    $settingModule = empty($forbidden)
        ? '<div class="alert alert-warning" role="alert">The requested administration page was not found.</div>'
        : '<div class="alert alert-danger" role="alert">Your role does not include access to this page.</div>';
} else {
    $modname = $route['module'];
    $language = preg_match('/^[a-zA-Z0-9_-]+$/D', $cfg['lang']) ? $cfg['lang'] : 'english';
    $languageFile = __DIR__ . '/modules/' . $modname . '/language/lang-' . $language . '.php';
    if (!is_file($languageFile)) $languageFile = __DIR__ . '/modules/' . $modname . '/language/lang-english.php';
    if (is_file($languageFile)) include_once $languageFile;
    $moduleFile = __DIR__ . '/modules/' . $modname . '/module.php';
    if (is_file($moduleFile)) include_once $moduleFile;
    // Preserve legacy AJAX, editor, calendar, and form helpers after authorization.
    ob_start();
    $sys_lanai->loadAjaxFunction($modname);
    $sys_lanai->loadAjaxCode($modname);
    include_once __DIR__ . '/include/mmscript/mm_script.js';
    $adminHead = ob_get_clean();
    ob_start();
    include $route['file'];
    $settingModule = ob_get_clean();
}
$smarty->assign('settingUserName', htmlspecialchars($settingUserName, ENT_QUOTES, 'UTF-8'));
$smarty->assign('adminLabels', lanai_interface_labels());
$smarty->assign('adminLocale', lanai_language_locale($cfg['lang'] ?? 'english'));
$smarty->assign('isSettingsDashboard', $isSettingsDashboard);
$smarty->assign('adminFull', (bool)$isFullAdmin);
$smarty->assign('adminModules', array(
    'content' => in_array('content', $staffModules, true),
    'media' => in_array('media', $staffModules, true),
));
$smarty->assign('setModule', $settingModule);
$smarty->assign('adminHead', $adminHead);
$smarty->assign('adminBase', htmlspecialchars(rtrim($cfg['url'], '/') . '/', ENT_QUOTES, 'UTF-8'));
$smarty->assign('adminSiteTitle', htmlspecialchars($cfg['title'], ENT_QUOTES, 'UTF-8'));
$smarty->display(__DIR__ . '/administrator/templates/layout.tpl');
