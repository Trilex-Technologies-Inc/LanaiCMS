<?php
if (basename($_SERVER['PHP_SELF']) !== 'setting.php') {
    die("You can't access this file directly...");
}

$dashboardNumber = static function ($value) {
    return is_numeric($value) ? number_format((int) $value) : 'Unavailable';
};
$dashboardEscape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$dashboardMembers = $db->GetOne('SELECT COUNT(*) FROM ' . $cfg['tablepre'] . 'user');
$dashboardContent = $db->GetOne('SELECT COUNT(*) FROM ' . $cfg['tablepre'] . 'content');
$dashboardDaily = $db->GetAll(
    'SELECT DATE(eventTime) AS eventDate, COUNT(*) AS views, COUNT(DISTINCT visitorHash) AS visitors'
    . ' FROM ' . $cfg['tablepre'] . 'analytics_event WHERE eventTime >= ? AND eventTime < ?'
    . ' GROUP BY DATE(eventTime) ORDER BY eventDate ASC',
    array(date('Y-m-d 00:00:00', strtotime('-6 days')), date('Y-m-d 00:00:00', strtotime('+1 day')))
);
$dashboardDays = array();
for ($offset = 6; $offset >= 0; $offset--) {
    $date = date('Y-m-d', strtotime('-' . $offset . ' days'));
    $dashboardDays[$date] = array('views' => 0, 'visitors' => 0);
}
foreach (is_array($dashboardDaily) ? $dashboardDaily : array() as $day) {
    if (isset($dashboardDays[$day['eventDate']])) {
        $dashboardDays[$day['eventDate']] = array('views' => (int) $day['views'], 'visitors' => (int) $day['visitors']);
    }
}
$dashboardToday = $dashboardDays[date('Y-m-d')];
$dashboardWeekViews = array_sum(array_column($dashboardDays, 'views'));
$dashboardMax = max(1, max(array_column($dashboardDays, 'views')));
$dashboardCards = array(
    array('Members', $dashboardMembers, 'Your growing community', 'member', 'people', 'violet'),
    array('Content pages', $dashboardContent, 'All pages in your library', 'content', 'file-earmark-text', 'blue'),
    array('Views today', is_array($dashboardDaily) ? $dashboardToday['views'] : false, 'Recorded page views', 'statistics', 'eye', 'green'),
    array('Visitors today', is_array($dashboardDaily) ? $dashboardToday['visitors'] : false, 'Estimated unique visitors', 'statistics', 'person-check', 'amber')
);
?>
<link rel="stylesheet" href="assets/settings-dashboard.css">
<div class="site-dashboard">
  <div class="dashboard-welcome">
    <div><span class="dashboard-eyebrow">YOUR SITE AT A GLANCE</span><h2>Welcome back, <?=$dashboardEscape($settingUserName); ?>.</h2><p>A home for your ideas. A place for your community.</p></div>
    <a class="dashboard-visit" href="index.php" target="_blank" rel="noopener">Visit your site <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
  </div>
  <div class="dashboard-metrics">
    <?php foreach ($dashboardCards as $card) { ?>
    <a class="dashboard-metric" href="setting.php?modname=<?=$card[3]; ?>">
      <span class="dashboard-metric-top"><span><?=$card[0]; ?></span><i class="dashboard-icon <?=$card[5]; ?> bi bi-<?=$card[4]; ?>" aria-hidden="true"></i></span>
      <strong><?=$dashboardNumber($card[1]); ?></strong><small><?=$card[2]; ?></small>
    </a>
    <?php } ?>
  </div>
  <div class="dashboard-columns">
    <section class="dashboard-panel" aria-labelledby="dashboard-traffic-title">
      <div class="dashboard-panel-heading"><div><h2 id="dashboard-traffic-title">A week on your site</h2><p>Page views over the last 7 days</p></div><a href="setting.php?modname=statistics">Full statistics <span aria-hidden="true">&rarr;</span></a></div>
      <?php if (!is_array($dashboardDaily)) { ?>
        <p class="dashboard-notice">Visit statistics are currently unavailable.</p>
      <?php } else { ?>
        <p class="dashboard-total"><strong><?=number_format($dashboardWeekViews); ?></strong> views this week</p>
        <div class="dashboard-chart" role="list" aria-label="Daily page views">
          <?php foreach ($dashboardDays as $date => $day) { ?>
          <div class="dashboard-day" role="listitem" aria-label="<?=$dashboardEscape(date('M j', strtotime($date)) . ': ' . $day['views'] . ' views'); ?>">
            <span class="dashboard-day-count"><?=number_format($day['views']); ?></span>
            <div class="dashboard-track" aria-hidden="true"><div class="dashboard-bar" style="height: <?=round($day['views'] / $dashboardMax * 100, 2); ?>%"></div></div>
            <span class="dashboard-day-label"><?=$dashboardEscape(date('D', strtotime($date))); ?></span>
          </div>
          <?php } ?>
        </div>
        <?php if ($dashboardWeekViews === 0) { ?><p class="dashboard-notice">Your next chapter starts here. Visits will appear as people explore your site.</p><?php } ?>
      <?php } ?>
    </section>
    <section class="dashboard-panel dashboard-shortcuts" aria-labelledby="dashboard-shortcuts-title">
      <div class="dashboard-panel-heading"><div><h2 id="dashboard-shortcuts-title">Make something happen</h2><p>A few good places to start</p></div></div>
      <a href="setting.php?modname=content&amp;mf=connew"><i class="bi bi-pencil-square" aria-hidden="true"></i><span><strong>Create a page</strong><small>Share something new</small></span><span aria-hidden="true">&rarr;</span></a>
      <a href="setting.php?modname=member"><i class="bi bi-people" aria-hidden="true"></i><span><strong>Manage members</strong><small>Look after your community</small></span><span aria-hidden="true">&rarr;</span></a>
      <a href="setting.php?modname=theme"><i class="bi bi-palette" aria-hidden="true"></i><span><strong>Refresh your look</strong><small>Choose your site's theme</small></span><span aria-hidden="true">&rarr;</span></a>
      <a href="setting.php?modname=config&amp;mf=mfa"><i class="bi bi-shield-lock" aria-hidden="true"></i><span><strong>Secure your sign-in</strong><small>Set up two-factor authentication</small></span><span aria-hidden="true">&rarr;</span></a>
      <a href="setting.php?modname=backup"><i class="bi bi-hdd-stack" aria-hidden="true"></i><span><strong>Back up your site</strong><small>Keep a copy of your work</small></span><span aria-hidden="true">&rarr;</span></a>
    </section>
  </div>
</div>
