<?php
/** Private data workflows. Callers supply an authenticated session, never a posted user ID. */
class LanaiPrivacyData
{
    private $db;
    private $prefix;
    private $dataDir;
    const TYPES = array('export', 'erase', 'rectify', 'restrict', 'object', 'other');
    const PROFILE = 'userId,userFname,userLname,userAddress1,userAddress2,userCity,userState,cntId,userZipcode,userPhone,userFax,userMobile,userEmail,userURL,userLogin,userPrivilege,userRoleId,userCreated,userActive';

    public function __construct($db, $prefix, $dataDir = '') {
        if (!preg_match('/^[a-zA-Z0-9_]*$/D', $prefix)) throw new InvalidArgumentException('Invalid table prefix.');
        $this->db = $db; $this->prefix = $prefix; $this->dataDir = $dataDir;
    }
    public static function schemas($prefix) {
        return array(
            'privacy_request' => 'CREATE TABLE IF NOT EXISTS '.$prefix.'privacy_request (requestId CHAR(32) NOT NULL PRIMARY KEY, userId INT NOT NULL, requestType VARCHAR(16) NOT NULL, requestStatus VARCHAR(16) NOT NULL, requestDetails TEXT NOT NULL, responseText TEXT NOT NULL, createdAt BIGINT NOT NULL, dueAt BIGINT NOT NULL, closedAt BIGINT NOT NULL DEFAULT 0, KEY privacy_request_account (userId,closedAt), KEY privacy_request_due (closedAt,dueAt)) ENGINE=InnoDB',
            'privacy_audit' => 'CREATE TABLE IF NOT EXISTS '.$prefix.'privacy_audit (eventId CHAR(32) NOT NULL PRIMARY KEY, actorId INT NOT NULL, subjectId INT NOT NULL, requestId VARCHAR(32) NOT NULL, eventType VARCHAR(40) NOT NULL, createdAt BIGINT NOT NULL, KEY privacy_audit_time (createdAt), KEY privacy_audit_subject (subjectId)) ENGINE=InnoDB',
            'privacy_consent' => 'CREATE TABLE IF NOT EXISTS '.$prefix.'privacy_consent (receiptHash CHAR(64) NOT NULL PRIMARY KEY, userId INT NOT NULL, policyRevision BIGINT NOT NULL, choices TEXT NOT NULL, policySnapshot TEXT NOT NULL, createdAt BIGINT NOT NULL, KEY privacy_consent_account (userId), KEY privacy_consent_time (createdAt)) ENGINE=InnoDB'
        );
    }
    public function install() { foreach (self::schemas($this->prefix) as $sql) $this->query($sql); }
    private function query($sql) {
        $result = $this->db->Execute($sql);
        if ($result === false) throw new RuntimeException('Privacy storage is unavailable. Save privacy settings or contact the administrator.');
        return $result;
    }
    private function rows($sql) {
        $rs = $this->query($sql); $rows = array();
        while (!$rs->EOF) {
            $rows[] = array_filter($rs->fields, static function($key) { return is_string($key); }, ARRAY_FILTER_USE_KEY);
            $rs->MoveNext();
        }
        return $rows;
    }
    private function q($value) { return $this->db->qstr((string)$value); }
    private function transaction($callback) {
        if (!$this->db->BeginTrans()) throw new RuntimeException('Transactional storage is required.');
        try {
            $result = $callback();
            if (!$this->db->CommitTrans()) throw new RuntimeException('Privacy changes could not be committed.');
            return $result;
        } catch (Throwable $error) { $this->db->RollbackTrans(); throw $error; }
    }
    private function lockUser($uid) {
        $suffix = ($this->db->databaseType ?? '') === 'sqlite3' ? '' : ' FOR UPDATE';
        $this->query('SELECT userId FROM '.$this->prefix.'user WHERE userId='.(int)$uid.$suffix);
    }
    public function user($uid, $admin = false) {
        if (!is_numeric($uid) || (int)$uid < 1) throw new RuntimeException('Sign in to manage personal data.');
        $rows = $this->rows('SELECT '.self::PROFILE.' FROM '.$this->prefix.'user WHERE userId='.(int)$uid);
        $user = $rows[0] ?? null;
        if (!$user || $user['userActive'] !== 'y' || ($admin && $user['userPrivilege'] !== 'a')) throw new RuntimeException('Account access denied.');
        return $user;
    }
    public function authorize($method, $post, $session, $system, $mfa = null, $admin = false) {
        if ($method !== 'POST') throw new InvalidArgumentException('POST required.', 405);
        if (!is_string($post['csrf_token'] ?? null) || !$system->validateCsrfToken('privacy_data', $post['csrf_token'])) throw new InvalidArgumentException('Reload the page and try again.', 403);
        $user = $this->user($session['uid'] ?? 0, $admin);
        // Persistent throttling works across sessions and includes password failures.
        if ($mfa) $mfa->provision((int)$user['userId']);
        if (!$mfa || !$mfa->attempt((int)$user['userId'])) throw new RuntimeException('Verification unavailable or too many attempts. Try again in five minutes.');
        $row = $this->rows('SELECT userPassword FROM '.$this->prefix.'user WHERE userId='.(int)$user['userId'])[0];
        if (!is_string($post['currentPassword'] ?? null) || !$system->verifyPassword($post['currentPassword'], $row['userPassword'])) throw new RuntimeException('Account verification failed.');
        $state = $mfa->state((int)$user['userId']);
        if ($state && $state['mfaEnabled']) {
            if (!is_string($post['mfaCode'] ?? null) || !$mfa->verify((int)$user['userId'], $post['mfaCode'])) throw new RuntimeException('Account verification failed.');
        }
        return $user;
    }
    public function audit($actor, $subject, $request, $event) {
        $this->query('INSERT INTO '.$this->prefix.'privacy_audit (eventId,actorId,subjectId,requestId,eventType,createdAt) VALUES ('.$this->q(bin2hex(random_bytes(16))).','.(int)$actor.','.(int)$subject.','.$this->q($request).','.$this->q($event).','.time().')');
    }
    public function recordConsent($selection, $config, $uid = 0) {
        $receipt = bin2hex(random_bytes(32));
        $this->query('INSERT INTO '.$this->prefix.'privacy_consent (receiptHash,userId,policyRevision,choices,policySnapshot,createdAt) VALUES ('.$this->q(hash('sha256', $receipt)).','.(int)$uid.','.(int)$selection['revision'].','.$this->q(json_encode($selection, JSON_THROW_ON_ERROR)).','.$this->q(json_encode($config, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)).','.(int)$selection['time'].')');
        return $receipt;
    }
    public static function dueDate($now) {
        $date = (new DateTimeImmutable('@'.$now))->setTimezone(new DateTimeZone('UTC'));
        $next = $date->modify('first day of next month');
        return $next->setDate((int)$next->format('Y'), (int)$next->format('m'), min((int)$date->format('d'), (int)$next->format('t')))->getTimestamp();
    }
    public function createRequest($uid, $type, $details) {
        $this->user($uid);
        if (!is_string($type) || !in_array($type, self::TYPES, true) || !is_string($details) || strlen($details) > 4000) throw new InvalidArgumentException('Choose a request type and use at most 4,000 bytes of details.');
        return $this->transaction(function() use ($uid, $type, $details) {
            $this->lockUser($uid);
            $this->user($uid);
            $existing = $this->rows('SELECT requestId FROM '.$this->prefix.'privacy_request WHERE userId='.(int)$uid.' AND requestType='.$this->q($type)." AND closedAt=0");
            if ($existing) return $existing[0]['requestId'];
            $id = bin2hex(random_bytes(16)); $now = time();
            $this->query('INSERT INTO '.$this->prefix.'privacy_request (requestId,userId,requestType,requestStatus,requestDetails,responseText,createdAt,dueAt,closedAt) VALUES ('.$this->q($id).','.(int)$uid.','.$this->q($type).",'open',".$this->q(trim($details)).",'',".$now.','.self::dueDate($now).',0)');
            $this->audit($uid, $uid, $id, 'request_created');
            return $id;
        });
    }
    public function requests($uid, $admin = false, $offset = 0) {
        $this->user($uid, $admin);
        return $this->rows('SELECT * FROM '.$this->prefix.'privacy_request'.($admin ? '' : ' WHERE userId='.(int)$uid).' ORDER BY closedAt ASC,dueAt ASC,requestId ASC LIMIT 50 OFFSET '.max(0, (int)$offset));
    }
    public function events($uid, $kind, $offset = 0) {
        $this->user($uid, true);
        if (!in_array($kind, array('audit','consent'), true)) throw new InvalidArgumentException('Invalid log.');
        return $this->rows('SELECT * FROM '.$this->prefix.'privacy_'.$kind.' ORDER BY createdAt DESC LIMIT 50 OFFSET '.max(0, (int)$offset));
    }
    public function export($uid) {
        $user = $this->user($uid);
        $out = array('generatedAt'=>gmdate('c'), 'profile'=>$user);
        foreach (array('content'=>'*', 'citem'=>'*', 'media'=>'mediaId,origName,mediaType,mimeType,fileSize,width,height,altText,createdAt', 'api_token'=>'tokenId,label,createdAt,lastUsedAt') as $table=>$fields) {
            $out[$table] = $this->rows('SELECT '.$fields.' FROM '.$this->prefix.$table.' WHERE userId='.(int)$uid);
        }
        $out['cvalue'] = $this->rows('SELECT v.* FROM '.$this->prefix.'cvalue v INNER JOIN '.$this->prefix.'citem i ON i.citId=v.citId WHERE i.userId='.(int)$uid);
        $out['requests'] = $this->rows('SELECT * FROM '.$this->prefix.'privacy_request WHERE userId='.(int)$uid);
        $out['consents'] = $this->rows('SELECT policyRevision,choices,policySnapshot,createdAt FROM '.$this->prefix.'privacy_consent WHERE userId='.(int)$uid);
        $avatar = $this->avatarPath($uid);
        if ($avatar && is_file($avatar)) {
            if (filesize($avatar) > 2097152 || ($bytes = file_get_contents($avatar)) === false) throw new RuntimeException('Avatar could not be exported.');
            $out['avatar'] = array('mimeType'=>'image/gif', 'base64'=>base64_encode($bytes));
        }
        $out['scope'] = 'Account profile, authored content and custom fields, media metadata, avatar, API-token metadata, account-linked consents and privacy requests. No passwords, MFA secrets or token hashes. Comments use unverified email addresses and must be reviewed separately. Media file copies, contact emails, backups, access logs, extensions, third parties and anonymous analytics/consents require operator review; they are not reliably attributable to an account.';
        $this->audit($uid, $uid, '', 'data_exported');
        return $out;
    }
    private function avatarPath($uid) {
        if ($this->dataDir === '') return null;
        $base = realpath($this->dataDir);
        if (!$base) throw new RuntimeException('Data directory is unavailable.');
        $dir = $base.DIRECTORY_SEPARATOR.'uimage';
        if (!file_exists($dir)) return null;
        $resolved = realpath($dir);
        if (!$resolved || is_link($dir) || strpos(str_replace('\\','/',$resolved).'/', rtrim(str_replace('\\','/',$base),'/').'/') !== 0) throw new RuntimeException('Avatar directory requires manual review.');
        $path = $dir.DIRECTORY_SEPARATOR.'u'.(int)$uid.'.gif';
        if (is_link($path)) throw new RuntimeException('Avatar link requires manual review.');
        return $path;
    }
    private function assertTransactionalTables() {
        if (($this->db->databaseType ?? '') === 'sqlite3') return;
        foreach (array('user','user_mfa','api_token','content','citem','media','privacy_request','privacy_audit','privacy_consent') as $table) {
            $rows = $this->rows('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='.$this->q($this->prefix.$table));
            if (!$rows || strcasecmp((string)($rows[0]['ENGINE'] ?? ''), 'InnoDB') !== 0) throw new RuntimeException('Account erasure requires InnoDB for all affected tables. Ask the operator to migrate legacy tables first.');
        }
    }
    public function review($actor, $id, $status, $response, $reviewed = false) {
        $this->user($actor, true);
        if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D', $id) || !in_array($status, array('reviewing','completed','rejected'), true) || !is_string($response) || trim($response) === '' || strlen($response)>4000) throw new InvalidArgumentException('A valid request, status and response (up to 4,000 bytes) are required.');
        if ($status === 'completed' && !$reviewed) throw new InvalidArgumentException('Confirm the identity, data inventory and external-copy review before completing a request.');
        return $this->transaction(function() use ($actor, $id, $status, $response) {
            $suffix = ($this->db->databaseType ?? '') === 'sqlite3' ? '' : ' FOR UPDATE';
            $rows = $this->rows('SELECT * FROM '.$this->prefix.'privacy_request WHERE requestId='.$this->q($id).$suffix);
            $request = $rows[0] ?? null;
            if (!$request || (int)$request['closedAt'] !== 0) throw new RuntimeException('Request is missing or already closed.');
            $uid = (int)$request['userId'];
            if ($request['requestType'] === 'erase' && $status === 'completed') {
                $this->assertTransactionalTables();
                $this->lockUser($uid);
                $user = $this->user($uid);
                if ($uid === (int)$actor || $user['userPrivilege'] === 'a') throw new RuntimeException('An administrator must transfer responsibilities and be demoted before account erasure.');
                $assignments = array();
                foreach (explode(',', self::PROFILE) as $field) {
                    if (in_array($field, array('userId','userPrivilege','userActive','userLogin'), true)) continue;
                    $assignments[] = $field.'=NULL';
                }
                $assignments[] = "userPrivilege='u'"; $assignments[] = "userActive='n'";
                $assignments[] = 'userLogin='.$this->q('erased-'.bin2hex(random_bytes(16)));
                $assignments[] = 'userPassword=NULL'; $assignments[] = 'userActivationToken=NULL';
                $this->query('UPDATE '.$this->prefix.'user SET '.implode(',', $assignments).' WHERE userId='.$uid);
                foreach (array('user_mfa','api_token','privacy_consent') as $table) $this->query('DELETE FROM '.$this->prefix.$table.' WHERE userId='.$uid);
                // Publication/file contents are explicitly reviewed by the operator before this step.
                foreach (array('content','citem','media') as $table) $this->query('UPDATE '.$this->prefix.$table.' SET userId=0 WHERE userId='.$uid);
                $this->query('UPDATE '.$this->prefix.'privacy_request SET userId=0,requestDetails=\'\',responseText=\'Account erased after operator review.\',requestStatus=CASE WHEN requestId='.$this->q($id).' THEN \'completed\' WHEN closedAt=0 THEN \'cancelled\' ELSE requestStatus END,closedAt=CASE WHEN closedAt=0 THEN '.time().' ELSE closedAt END WHERE userId='.$uid);
                $this->query('UPDATE '.$this->prefix.'privacy_audit SET subjectId=CASE WHEN subjectId='.$uid.' THEN 0 ELSE subjectId END,actorId=CASE WHEN actorId='.$uid.' THEN 0 ELSE actorId END WHERE subjectId='.$uid.' OR actorId='.$uid);
                $avatar = $this->avatarPath($uid);
                if ($avatar && file_exists($avatar) && !unlink($avatar)) throw new RuntimeException('Avatar could not be removed; erasure has not completed.');
                $this->audit($actor, 0, $id, 'account_erased');
            } else {
                $this->query('UPDATE '.$this->prefix.'privacy_request SET requestStatus='.$this->q($status).',responseText='.$this->q(trim($response)).',closedAt='.($status==='reviewing' ? 0 : time()).' WHERE requestId='.$this->q($id));
                $this->audit($actor, $uid, $id, 'request_'.$status);
            }
        });
    }
    public function retention($config, $apply = false, $now = null) {
        $now = $now ?? time(); $report = array();
        $rules = array('analytics_event'=>array('eventTime','analyticsDays',true), 'poll_stat'=>array('pstTime','pollDays',false), 'privacy_consent'=>array('createdAt','consentDays',false), 'privacy_audit'=>array('createdAt','auditDays',false), 'privacy_request'=>array('closedAt','requestDays',false));
        foreach ($rules as $table=>$rule) {
            $days = $config[$rule[1]] ?? 0;
            if (!is_int($days) || $days<1 || $days>3650) throw new RuntimeException('Save valid retention settings before running cleanup.');
            $cutoff = $now-$days*86400;
            $where = $rule[0].'<'.($rule[2] ? $this->q(date('Y-m-d H:i:s', $cutoff)) : $cutoff);
            if ($table === 'privacy_request') $where .= ' AND closedAt>0';
            $report[$table] = (int)$this->rows('SELECT COUNT(*) AS total FROM '.$this->prefix.$table.' WHERE '.$where)[0]['total'];
            if ($apply) $this->query('DELETE FROM '.$this->prefix.$table.' WHERE '.$where);
        }
        return $report;
    }
}
