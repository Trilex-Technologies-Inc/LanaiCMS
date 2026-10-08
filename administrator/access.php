<?php
/** Shared checks for the administrator entry point and route dispatcher. */
function lanai_admin_allowed($user)
{
    return is_array($user) && !empty($user['userId'])
        && ($user['userPrivilege'] ?? '') === 'a' && ($user['userActive'] ?? '') === 'y';
}

/** Settings modules that role-based staff may open, and the capabilities that unlock each. */
function lanai_staff_module_capabilities()
{
    return array(
        'content' => array('edit_content', 'edit_own_content'),
        'media' => array('manage_media'),
    );
}

/** Modules a non-administrator may open. $hasCapability is a callable taking a capability name. */
function lanai_staff_modules($user, $hasCapability)
{
    if (!is_array($user) || empty($user['userId']) || ($user['userActive'] ?? '') !== 'y'
        || ($user['userPrivilege'] ?? '') === 'a' || empty($user['userRoleId']) || !$hasCapability('access_admin')) {
        return array();
    }
    $allowed = array();
    foreach (lanai_staff_module_capabilities() as $module => $capabilities) {
        foreach ($capabilities as $capability) {
            if ($hasCapability($capability)) { $allowed[] = $module; break; }
        }
    }
    return $allowed;
}

function lanai_admin_route($root, $module, $action)
{
    if (!is_string($module) || !is_string($action)
        || ($module !== '' && !preg_match('/^[a-zA-Z0-9_]+$/D', $module))
        || ($action !== '' && !preg_match('/^[a-zA-Z0-9_]+$/D', $action))) {
        return false;
    }
    if ($module === '' || ($module === 'setting' && $action === '')) {
        if ($action !== '') return false;
        $module = 'setting';
        $action = 'dashboard';
    }
    $base = realpath($root . '/modules/' . $module . '/setting');
    $file = realpath($root . '/modules/' . $module . '/setting/' . ($action ?: 'index') . '.php');
    if (!$base || !$file || dirname($file) !== $base || !is_file($file)) return false;
    return array('module' => $module, 'file' => $file);
}
