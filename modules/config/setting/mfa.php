<?php
if (!preg_match('/setting\.php/i', $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

global $db, $cfg, $sys_lanai, $cfg_mfa_key;
$e = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$validKey = static function ($value) { return is_string($value) && preg_match('/^[a-f0-9]{64}$/iD', $value) === 1; };

$objConfig = new SysConfig();
$envKey = getenv('LANAI_MFA_KEY');
// Mirrors lanai_mfa_service(): a defined $cfg_mfa_key takes precedence over the environment.
if (isset($cfg_mfa_key)) {
    $keySource = $validKey($cfg_mfa_key) ? 'config' : 'invalid';
} else {
    $keySource = $validKey($envKey) ? 'environment' : 'none';
}
$opensslReady = function_exists('openssl_encrypt');
$canWrite = $objConfig->configIsWrite();

$generatedKey = '';
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!$sys_lanai->validateCsrfToken('config', isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Invalid request, please try again.';
    } elseif ($keySource !== 'none') {
        // Replacing a working key would make every enrolled authenticator unreadable.
        $error = 'An encryption key is already set. It cannot be replaced from the dashboard.';
    } elseif (!$canWrite) {
        $error = 'config.inc.php is not writable, so the key cannot be saved. Set LANAI_MFA_KEY in the server environment instead.';
    } else {
        $candidate = bin2hex(random_bytes(32));
        $objConfig->_set_config_var('cfg_mfa_key', $candidate);
        $saved = false;
        foreach (file('config.inc.php') as $line) {
            if (strpos($line, $candidate) !== false) { $saved = true; break; }
        }
        if ($saved) {
            $generatedKey = $candidate;
            $keySource = 'config';
        } else {
            $error = 'The key could not be written to config.inc.php.';
        }
    }
}

$enrolled = null;
try {
    $result = $db->Execute('SELECT COUNT(*) AS n FROM ' . $cfg['tablepre'] . 'user_mfa WHERE mfaEnabled=1');
    if ($result && !$result->EOF) $enrolled = (int)$result->fields['n'];
} catch (Throwable $exception) {
    $enrolled = null;
}
$ready = $keySource !== 'none' && $keySource !== 'invalid' && $opensslReady;
?>
<div class="container mt-4">
    <h3 class="mb-4">Two-factor authentication</h3>

    <?php if ($error !== '') : ?>
        <div class="alert alert-danger" role="alert"><?= $e($error) ?></div>
    <?php endif; ?>

    <?php if ($generatedKey !== '') : ?>
        <div class="alert alert-warning" role="alert">
            <strong>Encryption key created and saved to config.inc.php.</strong>
            Copy it somewhere safe now. It is shown only this once. If it is lost or changed, enrolled authenticators can no longer be decrypted.
            <pre class="mt-2 mb-0 user-select-all"><?= $e($generatedKey) ?></pre>
        </div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm mb-4">
        <h5>Status</h5>
        <ul class="list-unstyled mb-0">
            <li><?= $opensslReady ? '&#10003;' : '&#10007;' ?> PHP OpenSSL extension <?= $opensslReady ? 'available' : 'missing (required)' ?></li>
            <li>
                <?= ($keySource === 'config' || $keySource === 'environment') ? '&#10003;' : '&#10007;' ?>
                Encryption key:
                <?php if ($keySource === 'config') : ?>set in config.inc.php
                <?php elseif ($keySource === 'environment') : ?>set in the server environment (LANAI_MFA_KEY)
                <?php elseif ($keySource === 'invalid') : ?>present in config.inc.php but invalid (it must be 64 hexadecimal characters)
                <?php else : ?>not set
                <?php endif; ?>
            </li>
            <li><?= $enrolled === null ? '&#8226; Storage table not created yet; it is created the first time someone opens the setup page.' : '&#10003; ' . $enrolled . ' member(s) have two-factor authentication enabled.' ?></li>
        </ul>
    </div>

    <?php if ($keySource === 'none') : ?>
        <div class="card p-4 shadow-sm mb-4">
            <h5>Create the encryption key</h5>
            <p>Members cannot enroll until the site has an encryption key, which protects each authenticator secret in the database. The key is saved in config.inc.php; back it up separately from your database backups.</p>
            <?php if ($canWrite && $opensslReady) : ?>
                <form method="post" action="setting.php">
                    <input type="hidden" name="modname" value="config">
                    <input type="hidden" name="mf" value="mfa">
                    <?php $sys_lanai->renderCsrfField('config'); ?>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-key"></i> Generate and save key</button>
                </form>
            <?php else : ?>
                <div class="alert alert-info mb-0">
                    <?= !$opensslReady ? 'Enable the PHP OpenSSL extension first. ' : 'config.inc.php is not writable. ' ?>
                    Alternatively set <code>LANAI_MFA_KEY</code> in the server environment to a 64-character hexadecimal value.
                </div>
            <?php endif; ?>
        </div>
    <?php elseif ($keySource === 'invalid') : ?>
        <div class="alert alert-danger">The <code>$cfg_mfa_key</code> value in config.inc.php is not a 64-character hexadecimal key. Correct it manually; it is not replaced automatically because that could break existing enrollments.</div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm mb-4">
        <h5>Your own account</h5>
        <p class="mb-3">Each member, including administrators, turns on two-factor authentication from their own account. You will need your password and an authenticator app (the setup page shows a manual setup key; no QR image is used).</p>
        <?php if ($ready) : ?>
            <a class="btn btn-primary" href="module.php?modname=member&amp;mf=memmfa"><i class="bi bi-shield-lock"></i> Set up my authenticator</a>
        <?php else : ?>
            <button class="btn btn-primary" type="button" disabled><i class="bi bi-shield-lock"></i> Set up my authenticator</button>
            <span class="text-muted ms-2">Available once the encryption key is set.</span>
        <?php endif; ?>
    </div>

    <a href="setting.php?modname=config" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> <?= defined('_BACK') ? $e(_BACK) : 'Back' ?></a>
</div>
