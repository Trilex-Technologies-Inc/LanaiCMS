<?php
/** RFC 6238 authenticator codes and persistent single-use recovery codes. */
class LanaiMfa
{
    private $db;
    private $table;
    private $key;
    public function __construct($db, $prefix, $key = '') {
        $this->db = $db;
        $this->table = $prefix . 'user_mfa';
        $this->key = is_string($key) && preg_match('/^[a-f0-9]{64}$/iD', $key) ? hex2bin($key) : null;
    }
    public function ready() { return $this->key !== null && function_exists('openssl_encrypt'); }
    public function ensureSchema() {
        if ($this->db->Execute('SELECT userId FROM ' . $this->table . ' WHERE 1=0') !== false) return;
        $this->query(self::schema($this->table));
    }
    public static function schema($table) { return 'CREATE TABLE IF NOT EXISTS ' . $table . ' (userId INT NOT NULL PRIMARY KEY, mfaEnabled INT NOT NULL DEFAULT 0, mfaSecret TEXT NOT NULL, mfaRecovery TEXT NOT NULL, mfaLastCounter BIGINT NOT NULL DEFAULT -1, mfaFailures INT NOT NULL DEFAULT 0, mfaWindow BIGINT NOT NULL DEFAULT 0, mfaVersion VARCHAR(64) NOT NULL DEFAULT \'\') ENGINE=InnoDB'; }
    private function query($sql) {
        $result = $this->db->Execute($sql);
        if ($result === false) throw new RuntimeException('MFA storage unavailable.');
        return $result;
    }
    public function state($uid) {
        $result = $this->query('SELECT * FROM ' . $this->table . ' WHERE userId=' . (int)$uid);
        return $result->EOF ? null : $result->fields;
    }
    public function provision($uid) {
        $this->query('INSERT IGNORE INTO ' . $this->table . " (userId,mfaSecret,mfaRecovery) VALUES (" . (int)$uid . ",'','[]')");
    }
    public function attempt($uid, $now = null) {
        $now = $now ?? time();
        $cutoff = $now - 300;
        $this->query('UPDATE ' . $this->table . ' SET mfaFailures=CASE WHEN mfaWindow<=' . $cutoff . ' THEN 1 ELSE mfaFailures+1 END, mfaWindow=CASE WHEN mfaWindow<=' . $cutoff . ' THEN ' . $now . ' ELSE mfaWindow END WHERE userId=' . (int)$uid);
        $row = $this->state($uid);
        return $row && (int)$row['mfaFailures'] <= 5;
    }
    public static function secret() {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $out = '';
        foreach (str_split(random_bytes(32)) as $byte) $out .= $alphabet[ord($byte) & 31];
        return $out;
    }
    /** Provisioning URI that authenticator apps read from a QR code. */
    public static function otpauthUri($secret, $issuer, $account) {
        // Long names are trimmed so the QR code stays small enough to scan; SHA-1, six digits and 30 seconds are the app defaults.
        $issuer = mb_substr(trim((string)$issuer), 0, 40);
        if ($issuer === '') $issuer = 'LanaiCMS';
        $account = mb_substr((string)$account, 0, 60);
        $uri = 'otpauth://totp/' . rawurlencode($issuer . ':' . $account) . '?secret=' . rawurlencode($secret) . '&issuer=' . rawurlencode($issuer);
        // Percent-encoded non-ASCII names are long; the issuer parameter alone is enough for apps to label the account.
        if (strlen($uri) > 170) $uri = 'otpauth://totp/' . rawurlencode($account) . '?secret=' . rawurlencode($secret) . '&issuer=' . rawurlencode($issuer);
        return $uri;
    }
    public static function decode($secret) {
        if (!is_string($secret) || !preg_match('/^[A-Z2-7]+$/D', $secret)) throw new InvalidArgumentException('Invalid secret');
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; $bits = '';
        foreach (str_split($secret) as $char) $bits .= str_pad(decbin(strpos($alphabet, $char)), 5, '0', STR_PAD_LEFT);
        $raw = '';
        for ($i=0; $i+8<=strlen($bits); $i+=8) $raw .= chr(bindec(substr($bits, $i, 8)));
        return $raw;
    }
    public static function code($secret, $counter, $digits = 6) {
        $binary = pack('N2', intdiv($counter, 4294967296), $counter % 4294967296);
        $hash = hash_hmac('sha1', $binary, self::decode($secret), true);
        $offset = ord($hash[19]) & 15;
        $number = unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff;
        return str_pad((string)($number % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }
    public static function counter($secret, $code, $last = -1, $now = null) {
        if (!is_string($code) || !preg_match('/^[0-9]{6}$/D', $code)) return false;
        $step = intdiv($now ?? time(), 30);
        for ($i=-1; $i<=1; $i++) {
            if ($step+$i > $last && hash_equals(self::code($secret, $step+$i), $code)) return $step+$i;
        }
        return false;
    }
    public function encrypt($secret, $uid) {
        if (!$this->ready()) throw new RuntimeException('MFA encryption is not configured.');
        $iv = random_bytes(12); $tag = '';
        $cipher = openssl_encrypt($secret, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag, (string)(int)$uid);
        if ($cipher === false) throw new RuntimeException('MFA encryption failed.');
        return base64_encode($iv . $tag . $cipher);
    }
    public function decrypt($value, $uid) {
        if (!$this->ready()) throw new RuntimeException('MFA encryption is not configured.');
        $raw = base64_decode($value, true);
        if ($raw === false || strlen($raw)<29) throw new RuntimeException('Invalid MFA secret.');
        $secret = openssl_decrypt(substr($raw,28), 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, substr($raw,0,12), substr($raw,12,16), (string)(int)$uid);
        if ($secret === false) throw new RuntimeException('MFA secret could not be decrypted.');
        return $secret;
    }
    public static function recoveryCodes() {
        $codes = array();
        for ($i=0; $i<10; $i++) $codes[] = implode('-', str_split(bin2hex(random_bytes(10)), 5));
        return $codes;
    }
    private static function recoveryHash($code) { return hash('sha256', strtolower(str_replace('-', '', $code))); }
    public function enable($uid, $secret, $code) {
        if (!$this->attempt($uid)) return false;
        $counter = self::counter($secret, $code);
        if ($counter === false) return false;
        $codes = self::recoveryCodes();
        $hashes = array_map(array(self::class,'recoveryHash'), $codes);
        $this->query('UPDATE ' . $this->table . ' SET mfaEnabled=1,mfaSecret=' . $this->db->qstr($this->encrypt($secret,$uid)) . ',mfaRecovery=' . $this->db->qstr(json_encode($hashes)) . ',mfaLastCounter=' . $counter . ',mfaVersion=' . $this->db->qstr(bin2hex(random_bytes(16))) . ' WHERE userId=' . (int)$uid . ' AND mfaEnabled=0');
        return $this->db->Affected_Rows() === 1 ? $codes : false;
    }
    public function verify($uid, $code) {
        if (!$this->attempt($uid)) return false;
        $row = $this->state($uid);
        if (!$row || !$row['mfaEnabled'] || !is_string($code)) return false;
        if (preg_match('/^[0-9]{6}$/D', $code)) {
            $counter = self::counter($this->decrypt($row['mfaSecret'],$uid), $code, (int)$row['mfaLastCounter']);
            if ($counter === false) return false;
            $this->query('UPDATE ' . $this->table . ' SET mfaLastCounter=' . $counter . ' WHERE userId=' . (int)$uid . ' AND mfaEnabled=1 AND mfaVersion=' . $this->db->qstr($row['mfaVersion']) . ' AND mfaLastCounter<' . $counter);
        } else {
            $hashes = json_decode($row['mfaRecovery'], true);
            if (!is_array($hashes)) return false;
            $match = false;
            foreach ($hashes as $key=>$hash) if (hash_equals($hash, self::recoveryHash($code))) { unset($hashes[$key]); $match=true; break; }
            if (!$match) return false;
            $this->query('UPDATE ' . $this->table . ' SET mfaRecovery=' . $this->db->qstr(json_encode(array_values($hashes))) . ' WHERE userId=' . (int)$uid . ' AND mfaEnabled=1 AND mfaVersion=' . $this->db->qstr($row['mfaVersion']) . ' AND mfaRecovery=' . $this->db->qstr($row['mfaRecovery']));
        }
        return $this->db->Affected_Rows() === 1;
    }
    public function disable($uid, $version) {
        $this->query('UPDATE ' . $this->table . " SET mfaEnabled=0,mfaSecret='',mfaRecovery='[]',mfaLastCounter=-1,mfaVersion='' WHERE userId=" . (int)$uid . ' AND mfaVersion=' . $this->db->qstr($version));
        return $this->db->Affected_Rows() === 1;
    }
}
function lanai_mfa_service() {
    global $db, $cfg, $cfg_mfa_key;
    $service = new LanaiMfa($db, $cfg['tablepre'], $cfg_mfa_key ?? (getenv('LANAI_MFA_KEY') ?: ''));
    $service->ensureSchema();
    return $service;
}
function lanai_begin_login($uid) {
    $service = lanai_mfa_service();
    $row = $service->state($uid);
    session_regenerate_id(true);
    unset($_SESSION['uid'], $_SESSION['mfa_verified'], $_SESSION['mfa_pending'], $_SESSION['mfa_setup']);
    if ($row && $row['mfaEnabled']) {
        $_SESSION['mfa_pending'] = array('uid'=>(int)$uid, 'expires'=>time()+300, 'version'=>$row['mfaVersion']);
        return 'module.php?modname=member&mf=memmfa';
    }
    $_SESSION['uid'] = (int)$uid;
    return 'module.php?modname=member&mf=meminfo';
}
