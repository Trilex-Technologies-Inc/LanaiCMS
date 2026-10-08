<?php
	if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
			die ("You can't access this file directly...");
	}
	
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$sys_lanai->validateCsrfToken('login', isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) { $sys_lanai->getErrorBox('Invalid sign-in request.'); return; }
    foreach (array('username','password','captext','cf-turnstile-response') as $key) { if (isset($_POST[$key]) && !is_string($_POST[$key])) { return; } }
    $module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);

	$captcha_provider = isset($cfg['captcha_provider']) ? $cfg['captcha_provider'] : 'default';
	if ($captcha_provider !== 'cloudflare') {
		$captcha_provider = 'default';
	}

	$turnstile_site_key = isset($cfg['turnstile_site_key']) ? trim($cfg['turnstile_site_key']) : '';
	$turnstile_secret_key = isset($cfg['turnstile_secret_key']) ? trim($cfg['turnstile_secret_key']) : '';
	$turnstile_enabled = ($captcha_provider === 'cloudflare' && $turnstile_site_key !== '' && $turnstile_secret_key !== '');
	$captcha_ok = false;

	if ($captcha_provider === 'default') {
		$captext = isset($_POST['captext']) ? trim($_POST['captext']) : '';
		$sessionCaptcha = isset($_SESSION['captcha']) ? trim($_SESSION['captcha']) : '';
		$captcha_ok = ($captext !== '' && $sessionCaptcha !== '' && strcasecmp($captext, $sessionCaptcha) === 0);
	} elseif ($turnstile_enabled) {
		$turnstile_response = isset($_POST['cf-turnstile-response']) ? trim($_POST['cf-turnstile-response']) : '';
		if ($turnstile_response !== '') {
			$verify_data = array(
				'secret' => $turnstile_secret_key,
				'response' => $turnstile_response,
				'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			$ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verify_data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			$response = curl_exec($ch);
			if (!curl_errno($ch)) {
				$result = json_decode($response, true);
				$captcha_ok = !empty($result['success']);
			}
			curl_close($ch);
		}
	} else {
		$sys_lanai->getErrorBox("Turnstile is not fully configured. Please set both Site Key and Secret Key in Config.");
		return;
	}

	unset($_SESSION['captcha']);
	if ($captcha_ok) {
		$xuid=$sys_lanai->getUserAuthentication(($_POST['username'] ?? ''),($_POST['password'] ?? ''));
	}
	if (isset($xuid) && $xuid>0) {
        require_once 'include/lanai/class.mfa.php';
        try { $sys_lanai->go2Page(lanai_begin_login($xuid)); }
        catch (Throwable $error) { unset($_SESSION['uid']); $sys_lanai->getErrorBox('Sign-in is temporarily unavailable. Please contact the site administrator.'); }
	} else {
		$sys_lanai->getErrorBox(_LOGIN_FAIL);
	}
?>
