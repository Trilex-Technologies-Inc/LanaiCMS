<?php
// Included by the member module after authentication; no standalone endpoint.
if (!defined('LANAI_MEMBER_AREA')) { http_response_code(403); exit; }

function memberAreaInput(array $source, $key)
{
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
}

function memberAreaValidate(array $post, array $user, $section, $system)
{
    $values = array();
    $errors = array();
    $limits = $section === 'profile'
        ? array('userFname'=>100, 'userLname'=>100, 'userAddress1'=>150, 'userAddress2'=>150,
            'userCity'=>100, 'userState'=>100, 'cntId'=>2, 'userZipcode'=>15,
            'userPhone'=>20, 'userFax'=>20, 'userMobile'=>20, 'userURL'=>255)
        : array('userEmail'=>254, 'userLogin'=>50);
    foreach ($limits as $field => $limit) {
        $values[$field] = memberAreaInput($post, $field);
        if (mb_strlen($values[$field]) > $limit) { $errors[] = 'length'; }
    }
    foreach ($section === 'profile' ? array('userFname','userLname') : array('userEmail','userLogin') as $field) {
        if ($values[$field] === '') { $errors[] = 'required'; }
    }
    if ($section === 'profile') {
        if ($values['userURL'] !== '' && (!filter_var($values['userURL'], FILTER_VALIDATE_URL)
            || !in_array(strtolower((string)parse_url($values['userURL'], PHP_URL_SCHEME)), array('http','https'), true))) {
            $errors[] = 'url';
        }
        if (!preg_match('/^[A-Z]{2}$/', $values['cntId'])) { $errors[] = 'country'; }
    } else {
        if (!filter_var($values['userEmail'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'email'; }
        $current = isset($post['currentPassword']) && is_string($post['currentPassword']) ? $post['currentPassword'] : '';
        if (!$system->verifyPassword($current, $user['userPassword'])) { $errors[] = 'current'; }
        $password = isset($post['userPassword1']) && is_string($post['userPassword1']) ? $post['userPassword1'] : '';
        $confirmation = isset($post['userPassword2']) && is_string($post['userPassword2']) ? $post['userPassword2'] : '';
        if ($password !== '' || $confirmation !== '') {
            if ($password !== $confirmation) { $errors[] = 'match'; }
            if (mb_strlen($password) < 12 || strlen($password) > 72) { $errors[] = 'password'; }
            if (!$errors) { $values['userPassword'] = $system->hashPassword($password); }
        }
    }
    return array($values, array_unique($errors));
}
