<?php

/**
 * ApiToken — issues and revokes bearer tokens used by api.php to
 * authenticate write requests on behalf of a user account.
 */
class ApiToken
{
    var $db;
    var $cfg;

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
    }

    function getTokens()
    {
        $sql = "SELECT t.*, u.userLogin FROM " . $this->cfg['tablepre'] . "api_token t
                LEFT JOIN " . $this->cfg['tablepre'] . "user u ON u.userId = t.userId
                ORDER BY t.createdAt DESC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    /**
     * Create a token for $userId. Returns the plaintext token (shown once
     * to the admin) — only its SHA-256 hash is stored.
     */
    function generateToken($userId, $label)
    {
        $plainToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plainToken);
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "api_token (userId, tokenHash, label, createdAt)
                VALUES (" . intval($userId) . ", " . $this->db->qstr($hash) . ", " . $this->db->qstr($label) . ", NOW())";
        if (!$this->db->Execute($sql)) {
            return false;
        }
        return $plainToken;
    }

    function revokeToken($tokenId)
    {
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "api_token WHERE tokenId=" . intval($tokenId);
        return $this->db->Execute($sql);
    }

    /**
     * Resolve a bearer token to a userId, or false if invalid. Updates
     * lastUsedAt as a side effect.
     */
    static function authenticate($token)
    {
        global $db, $tablepre;
        $token = trim((string) $token);
        if ($token === '') {
            return false;
        }
        $hash = hash('sha256', $token);
        $rs = $db->execute("SELECT * FROM " . $tablepre . "api_token WHERE tokenHash=" . $db->qstr($hash));
        if (!$rs || $rs->recordcount() < 1) {
            return false;
        }
        $db->Execute("UPDATE " . $tablepre . "api_token SET lastUsedAt=NOW() WHERE tokenId=" . intval($rs->fields['tokenId']));
        return (int) $rs->fields['userId'];
    }
}

?>
