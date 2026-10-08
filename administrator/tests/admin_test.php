<?php
require dirname(__DIR__) . '/access.php';
function adminCheck($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$root = dirname(__DIR__, 2);
adminCheck(!lanai_admin_allowed(null), 'Guest must be denied');
adminCheck(!lanai_admin_allowed(array('userId'=>1, 'userPrivilege'=>'u', 'userActive'=>'y')), 'Member must be denied');
adminCheck(!lanai_admin_allowed(array('userId'=>1, 'userPrivilege'=>'a', 'userActive'=>'n')), 'Inactive administrator must be denied');
adminCheck(lanai_admin_allowed(array('userId'=>1, 'userPrivilege'=>'a', 'userActive'=>'y')), 'Active administrator allowed');
foreach (array(array('../member','index'), array('content','../module'), array(array('content'),''), array('content',array('index')), array('content','index.php'), array('content',"index\0"), array('','index'), array('missing_module','')) as $input) {
    adminCheck(lanai_admin_route($root, $input[0], $input[1]) === false, 'Invalid route rejected');
}
foreach (array(array('',''), array('setting',''), array('content',''), array('member','memeditform'), array('statistics',''), array('media',''), array('explorer','')) as $input) {
    adminCheck(lanai_admin_route($root, $input[0], $input[1]) !== false, 'Existing settings route preserved');
}
require $root . '/include/smarty/SmartyBC.class.php';
$smarty = new SmartyBC();
$compile = sys_get_temp_dir() . '/lanai-admin-template-' . bin2hex(random_bytes(6));
mkdir($compile);
$smarty->setCompileDir($compile);
$smarty->assign('settingUserName', 'Admin &lt;Test&gt;');
$smarty->assign('isSettingsDashboard', true);
$smarty->assign('adminFull', true);
$smarty->assign('adminModules', array('content'=>false, 'media'=>false));
$smarty->assign('setModule', '<p>Dashboard test</p>');
$smarty->assign('adminHead', '');
$smarty->assign('adminBase', 'https://example.com/cms/');
$smarty->assign('adminSiteTitle', 'Test site');
require_once $root.'/include/lanai/localization.php';
$cfg = array('lang'=>'english');
$smarty->assign('adminLabels', lanai_interface_labels());
$smarty->assign('adminLocale', 'en');
$html = $smarty->fetch($root . '/administrator/templates/layout.tpl');
adminCheck(substr_count(strtolower($html), '<!doctype html>') === 1, 'Single document');
adminCheck(strpos($html, 'theme/') === false, 'Admin layout has no public theme dependencies');
adminCheck(strpos($html, 'assets/admin.css') !== false && strpos($html, 'assets/js/admin.js') !== false, 'Dedicated admin assets');
adminCheck(strpos($html, 'Dashboard test') !== false, 'Module content rendered');
adminCheck(strpos($html, 'Admin &lt;Test&gt;') !== false, 'Escaped account name preserved');
adminCheck(strpos($html, 'https://example.com/cms/') !== false, 'Subdirectory base URL preserved');
adminCheck(strpos($html, 'mf=meminfo') !== false, 'Member account link provided');
adminCheck(strpos($html, 'modname=config') !== false && strpos($html, 'modname=member"') !== false, 'Administrator sees every area');
foreach (array('spanish','german','french','portuguese_brazil','japanese') as $language) {
    $cfg['lang'] = $language;
    $smarty->assign('adminLabels', lanai_interface_labels());
    $smarty->assign('adminLocale', lanai_language_locale($language));
    $translatedHtml = $smarty->fetch($root.'/administrator/templates/layout.tpl');
    adminCheck(strpos($translatedHtml, 'lang="'.lanai_language_locale($language).'"') !== false, 'Translated document language');
    adminCheck(strpos($translatedHtml, '>'.$cfg['lang'].'<') === false, 'No raw language identifier');
    adminCheck(strpos($translatedHtml, '>'.lanai_translate('View site').'<') !== false, 'Translated navigation label');
    adminCheck(strpos($translatedHtml, 'data-manage-description="'.htmlspecialchars(lanai_translate('Manage {section} for your site.'), ENT_QUOTES, 'UTF-8').'"') !== false, 'Translated JavaScript description');
}
$cfg['lang'] = 'english';
$smarty->assign('adminLabels', lanai_interface_labels());
$smarty->assign('adminLocale', 'en');

// Role-based staff only reach the modules their capabilities unlock.
$staff = array('userId'=>7, 'userPrivilege'=>'u', 'userActive'=>'y', 'userRoleId'=>4);
$caps = static function (array $granted) { return static function ($capability) use ($granted) { return in_array($capability, $granted, true); }; };
adminCheck(lanai_staff_modules($staff, $caps(array('access_admin', 'edit_own_content'))) === array('content'), 'Contributor reaches content only');
adminCheck(lanai_staff_modules($staff, $caps(array('access_admin', 'edit_own_content', 'manage_media'))) === array('content', 'media'), 'Media needs manage_media');
adminCheck(lanai_staff_modules($staff, $caps(array('edit_own_content'))) === array(), 'access_admin is required');
adminCheck(lanai_staff_modules($staff, $caps(array('access_admin'))) === array(), 'Subscriber has no module');
adminCheck(lanai_staff_modules($staff, $caps(array('access_admin', 'manage_options', 'manage_users'))) === array(), 'Site and member administration stay administrator-only');
adminCheck(lanai_staff_modules(array_merge($staff, array('userActive'=>'n')), $caps(array('access_admin', 'edit_own_content'))) === array(), 'Inactive staff denied');
adminCheck(lanai_staff_modules(array_merge($staff, array('userRoleId'=>null)), $caps(array('access_admin', 'edit_own_content'))) === array(), 'Staff need an assigned role');
adminCheck(lanai_staff_modules(null, $caps(array('access_admin', 'edit_own_content'))) === array(), 'Guest is not staff');
$smarty->assign('adminFull', false);
$smarty->assign('adminModules', array('content'=>true, 'media'=>false));
$staffHtml = $smarty->fetch($root . '/administrator/templates/layout.tpl');
adminCheck(strpos($staffHtml, 'data-module="content"') !== false, 'Staff see Content');
foreach (array('config', 'member', 'role', 'media', 'menu', 'backup', 'explorer', 'setting', 'ctype', 'privacy') as $hidden) {
    adminCheck(strpos($staffHtml, 'data-module="' . $hidden . '"') === false, "Staff do not see $hidden");
}
echo "Administrator access, routing, and template checks passed.\n";

// Invalid public routes must stop before bootstrap or database access.
foreach (array('setting/index', '../setting', array('index')) as $action) {
    $code = '$_REQUEST = ' . var_export(array('modname'=>'member', 'mf'=>$action), true) . '; require ' . var_export($root . '/module.php', true) . ';';
    $process = proc_open(array(PHP_BINARY, '-r', $code), array(1=>array('pipe','w'), 2=>array('pipe','w')), $pipes);
    adminCheck(is_resource($process), 'Public route test process started');
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
    adminCheck($output === 'Invalid module route.' && $error === '', 'Public route cannot load settings files');
}
echo "Public route isolation checks passed.\n";
