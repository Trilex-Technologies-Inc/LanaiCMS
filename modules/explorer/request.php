<?php
function explorer_admin()
{
    $user = new User();
    if (empty($_SESSION['uid']) || $user->getUserPrivilege($_SESSION['uid']) !== 'a') {
        http_response_code(403); header('Content-Type: application/json'); echo json_encode(array('error' => 'Administrator access required.')); exit;
    }
}

function explorer_string($data, $key, $default = '')
{
    if (!isset($data[$key])) return $default;
    if (!is_string($data[$key])) throw new RuntimeException('Invalid ' . $key . '.');
    return $data[$key];
}

function explorer_csrf()
{
    global $sys_lanai;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$sys_lanai->validateCsrfToken('explorer', explorer_string($_POST, 'csrf_token'))) {
        http_response_code(403); throw new RuntimeException('Request expired or upload exceeded the server limit. Reload Explorer and try again.');
    }
}
