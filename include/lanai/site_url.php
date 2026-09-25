<?php
function lanai_request_base_path($server, $siteRoot)
{
    $root = rtrim(str_replace('\\', '/', $siteRoot), '/');
    $filename = str_replace('\\', '/', $server['SCRIPT_FILENAME'] ?? '');
    $script = $server['SCRIPT_NAME'] ?? '';
    if (!str_starts_with($filename, $root . '/') || !is_string($script)) return '';
    $relative = substr($filename, strlen($root) + 1);
    if ($relative === '' || !str_ends_with($script, '/' . $relative)) return '';
    $base = substr($script, 0, -strlen($relative) - 1);
    return '/' . trim($base, '/');
}

function lanai_install_url($server, $siteRoot)
{
    $https = !empty($server['HTTPS']) && strtolower((string)$server['HTTPS']) !== 'off';
    $host = $server['HTTP_HOST'] ?? $server['SERVER_NAME'] ?? 'localhost';
    if (!preg_match('/^(?:[a-z0-9.-]+|\[[a-f0-9:]+\])(?::[0-9]+)?$/iD', $host)) $host = 'localhost';
    return ($https ? 'https://' : 'http://') . $host . rtrim(lanai_request_base_path($server, $siteRoot), '/');
}

function lanai_effective_site_url($configured, $server, $siteRoot)
{
    $configured = rtrim($configured, '/');
    // Repair the legacy installer's host-only default for subfolder installs.
    if (empty(parse_url($configured, PHP_URL_PATH))) {
        $base = rtrim(lanai_request_base_path($server, $siteRoot), '/');
        if ($base !== '') $configured .= $base;
    }
    return $configured;
}
