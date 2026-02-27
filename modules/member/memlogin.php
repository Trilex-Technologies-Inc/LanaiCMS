<?
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
			die ("You can't access this file directly...");
	}
	
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
		$captext = isset($_REQUEST['captext']) ? trim($_REQUEST['captext']) : '';
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
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

	if ($captcha_ok) {
		$xuid=$sys_lanai->getUserAuthentication($_REQUEST['username'],$_REQUEST['password']);
	}
	if (isset($xuid) && $xuid>0) {
	    $_SESSION['uid']=$xuid;
		$sys_lanai->go2Page("module.php?modname=member&mf=memloginform");
	} else {
		$sys_lanai->getErrorBox(_LOGIN_FAIL);
	}
?>
