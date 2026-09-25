<?php
$analytics = new LanaiAnalytics($db, $cfg['tablepre']);
$statistics = $analytics->getDashboard();
$maxDailyViews = 1;
foreach ($statistics['daily'] as $day) {
    $maxDailyViews = max($maxDailyViews, (int) $day['views']);
}
?>
<div class="analytics-grid">
  <div class="analytics-card"><span>Views today</span><strong><?=number_format($statistics['todayViews']); ?></strong></div>
  <div class="analytics-card"><span>Visitors today</span><strong><?=number_format($statistics['todayVisitors']); ?></strong></div>
  <div class="analytics-card"><span>All-time views</span><strong><?=number_format($statistics['totalViews']); ?></strong></div>
</div>

<div class="analytics-panel">
  <h2>Last 7 days</h2>
  <?php if (empty($statistics['daily'])) { ?>
    <p class="analytics-muted">No visits have been recorded yet.</p>
  <?php } else { ?>
    <div class="analytics-chart">
      <?php foreach ($statistics['daily'] as $day) { ?>
        <div class="analytics-bar-column" title="<?=htmlspecialchars($day['eventDate'].' — '.$day['views'].' views', ENT_QUOTES, 'UTF-8'); ?>">
          <div class="analytics-bar" style="height: <?=max(6, round(((int) $day['views'] / $maxDailyViews) * 120)); ?>px"></div>
          <small><?=htmlspecialchars(date('D', strtotime($day['eventDate'])), ENT_QUOTES, 'UTF-8'); ?></small>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</div>

<div class="analytics-columns">
  <div class="analytics-panel"><h2>Popular pages <small>30 days</small></h2><table class="table"><tbody>
    <?php foreach ($statistics['topPages'] as $page) { ?><tr><td><?=htmlspecialchars($page['eventPage'], ENT_QUOTES, 'UTF-8'); ?></td><td class="text-end"><?=number_format($page['views']); ?></td></tr><?php } ?>
    <?php if (empty($statistics['topPages'])) { ?><tr><td class="analytics-muted">No page data yet.</td></tr><?php } ?>
  </tbody></table></div>
  <div class="analytics-panel"><h2>Countries <small>30 days</small></h2><table class="table"><tbody>
    <?php foreach ($statistics['countries'] as $country) { ?><tr><td><?=htmlspecialchars($country['eventCountry'], ENT_QUOTES, 'UTF-8'); ?></td><td class="text-end"><?=number_format($country['visitors']); ?></td></tr><?php } ?>
    <?php if (empty($statistics['countries'])) { ?><tr><td class="analytics-muted">No country data yet.</td></tr><?php } ?>
  </tbody></table></div>
</div>
