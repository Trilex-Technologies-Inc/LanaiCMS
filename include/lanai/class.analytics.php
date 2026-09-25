<?php

class LanaiAnalytics
{
    private $db;
    private $table;

    public function __construct($db, $tablePrefix)
    {
        $this->db = $db;
        $this->table = $tablePrefix . 'analytics_event';
    }

    public function trackRequest()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }

        $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if (in_array($script, array('setting.php', 'api.php'), true)) {
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';
        $page = $path;
        foreach (array('modname', 'mf', 'conid') as $parameter) {
            if (isset($_GET[$parameter]) && !is_array($_GET[$parameter])) {
                $page .= (strpos($page, '?') === false ? '?' : '&')
                    . $parameter . '=' . rawurlencode(substr((string) $_GET[$parameter], 0, 100));
            }
        }

        $country = $this->countryCode();
        $visitor = hash('sha256', date('Y-m-d') . '|' . ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        $this->db->Execute(
            'INSERT INTO ' . $this->table . ' (eventTime, eventPage, eventCountry, visitorHash) VALUES (?, ?, ?, ?)',
            array(date('Y-m-d H:i:s'), substr($page, 0, 500), $country, $visitor)
        );
    }

    public function getDashboard()
    {
        $today = date('Y-m-d');
        $since = date('Y-m-d 00:00:00', strtotime('-29 days'));
        $week = date('Y-m-d 00:00:00', strtotime('-6 days'));

        return array(
            'todayViews' => (int) $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->table . ' WHERE eventTime >= ?', array($today . ' 00:00:00')),
            'todayVisitors' => (int) $this->db->GetOne('SELECT COUNT(DISTINCT visitorHash) FROM ' . $this->table . ' WHERE eventTime >= ?', array($today . ' 00:00:00')),
            'totalViews' => (int) $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->table),
            'topPages' => $this->db->GetAll('SELECT eventPage, COUNT(*) AS views FROM ' . $this->table . ' WHERE eventTime >= ? GROUP BY eventPage ORDER BY views DESC LIMIT 10', array($since)),
            'countries' => $this->db->GetAll('SELECT eventCountry, COUNT(DISTINCT visitorHash) AS visitors FROM ' . $this->table . ' WHERE eventTime >= ? GROUP BY eventCountry ORDER BY visitors DESC LIMIT 10', array($since)),
            'daily' => $this->db->GetAll('SELECT DATE(eventTime) AS eventDate, COUNT(*) AS views, COUNT(DISTINCT visitorHash) AS visitors FROM ' . $this->table . ' WHERE eventTime >= ? GROUP BY DATE(eventTime) ORDER BY eventDate ASC', array($week))
        );
    }

    private function countryCode()
    {
        foreach (array('HTTP_CF_IPCOUNTRY', 'HTTP_X_COUNTRY_CODE') as $header) {
            $value = strtoupper(trim($_SERVER[$header] ?? ''));
            if (preg_match('/^[A-Z]{2}$/', $value)) {
                return $value;
            }
        }
        return 'Unknown';
    }
}

