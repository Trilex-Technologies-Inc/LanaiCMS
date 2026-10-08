<?php
if (basename($_SERVER['PHP_SELF']) !== 'setting.php') {
    die("You can't access this file directly...");
}
$infoView = isset($_GET['view']) && is_string($_GET['view']) ? $_GET['view'] : 'product';
if (!in_array($infoView, array('product', 'server', 'license'), true)) {
    $infoView = 'product';
}
$infoEscape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$infoIni = static function ($name) {
    $value = ini_get($name);
    return $value === false || $value === '' ? 'Not set' : $value;
};
$infoRoot = dirname(__DIR__, 3);
?>
<style>
.product-info-nav { display:flex; flex-wrap:wrap; gap:8px; margin:20px 0 24px; }
.product-info-nav a { padding:9px 14px; border:1px solid #e5e7eb; border-radius:7px; color:#475569; text-decoration:none; }
.product-info-nav a[aria-current="page"] { background:#edf6f0; border-color:#bed9c7; color:#285a3b; font-weight:600; }
.product-info-nav a:focus-visible { outline:2px solid #397b57; outline-offset:3px; }
.product-info-panel { padding:24px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; min-width:0; }
.product-info-panel h2 { margin:0 0 18px; font-size:18px; }
.product-info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
.product-info-panel dl { margin:0; }
.product-info-row { display:grid; grid-template-columns:minmax(110px,1fr) minmax(0,1.3fr); gap:16px; padding:12px 0; border-bottom:1px solid #f0f1f3; }
.product-info-row:last-child { border:0; }
.product-info-row dt { color:#64748b; font-weight:500; }
.product-info-row dd { margin:0; overflow-wrap:anywhere; }
.product-info-extensions { display:flex; flex-wrap:wrap; gap:8px; padding:0; margin:0; list-style:none; }
.product-info-extensions li { padding:5px 10px; border-radius:6px; background:#f1f5f9; font-size:12px; }
.product-info-license { white-space:pre-wrap; overflow-wrap:anywhere; margin:0; font-size:13px; line-height:1.65; }
@media(max-width:700px) { .product-info-grid { grid-template-columns:1fr; } .product-info-panel { padding:18px; } }
</style>
<span class="txtContentTitle"><?=_INFO_SETTING; ?></span>
<p><?=_INFO_SETTING_INSTRUCTION; ?></p>
<nav class="product-info-nav" aria-label="Product information">
<?php foreach (array('product' => _INFO_SETTING, 'server' => _INFO, 'license' => _LICENSE) as $view => $label) { ?>
<a href="setting.php?modname=info&amp;view=<?=$view; ?>"<?=$infoView === $view ? ' aria-current="page"' : ''; ?>><?=$infoEscape($label); ?></a>
<?php } ?>
<a href="https://www.lanaicms.com/" target="_blank" rel="noopener"><?=_CREDIT; ?></a>
<a href="setting.php"><?=_BACK; ?></a>
</nav>

<?php if ($infoView === 'server') {
    $serverGroups = array(
        'Server & PHP' => array(
            'PHP version' => PHP_VERSION,
            'Operating system' => PHP_OS_FAMILY,
            'Web server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unavailable',
            'PHP interface' => PHP_SAPI,
            'Architecture' => (PHP_INT_SIZE * 8) . '-bit',
            'Timezone' => date_default_timezone_get()
        ),
        'Resource limits' => array(
            'Memory limit' => ini_get('memory_limit') === '-1' ? 'Unlimited' : $infoIni('memory_limit'),
            'Execution time' => ini_get('max_execution_time') === '0' ? 'Unlimited' : $infoIni('max_execution_time') . ' seconds',
            'File uploads' => filter_var(ini_get('file_uploads'), FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Disabled',
            'Maximum upload size' => $infoIni('upload_max_filesize'),
            'Maximum POST size' => $infoIni('post_max_size'),
            'Files per upload' => $infoIni('max_file_uploads'),
            'Maximum input variables' => $infoIni('max_input_vars')
        )
    );
?>
<div class="product-info-grid">
<?php foreach ($serverGroups as $heading => $rows) { ?>
<section class="product-info-panel"><h2><?=$infoEscape($heading); ?></h2><dl>
<?php foreach ($rows as $label => $value) { ?>
<div class="product-info-row"><dt><?=$infoEscape($label); ?></dt><dd><?=$infoEscape($value); ?></dd></div>
<?php } ?>
</dl></section>
<?php } ?>
</div>
<section class="product-info-panel" style="margin-top:18px;"><h2>Loaded PHP extensions</h2>
<ul class="product-info-extensions">
<?php $extensions = get_loaded_extensions(); natcasesort($extensions); foreach ($extensions as $extension) { ?>
<li><?=$infoEscape($extension); ?></li>
<?php } ?>
</ul></section>

<?php } elseif ($infoView === 'license') {
    $licensePath = $infoRoot . '/license.txt';
    $licenseText = is_readable($licensePath) ? file_get_contents($licensePath) : false;
?>
<section class="product-info-panel"><h2><?=_LICENSE; ?></h2>
<?php if ($licenseText !== false) { ?>
<pre class="product-info-license"><?=$infoEscape($licenseText); ?></pre>
<?php } else { ?>
<p role="status"><?=_LICENSE_UNAVAILABLE; ?></p>
<?php } ?>
</section>

<?php } else {
    $versionPath = $infoRoot . '/version.txt';
    $versionLines = is_readable($versionPath) ? file($versionPath, FILE_IGNORE_NEW_LINES) : array();
    $serial = 'preview version';
    $licensedTo = 'none';
    $licenseId = $infoRoot . '/modules/info/license.id.php';
    if (is_readable($licenseId)) {
        $licenseLines = file($licenseId, FILE_IGNORE_NEW_LINES);
        $licenseParts = explode(':', $licenseLines[0] ?? '', 2);
        $serial = trim($licenseParts[0]) ?: $serial;
        $licensedTo = trim($licenseParts[1] ?? '') ?: $licensedTo;
    }
?>
<section class="product-info-panel"><h2><?=_INFO_SETTING; ?></h2><dl>
<?php foreach (array(_VERSION => $versionLines[0] ?? 'Unavailable', _SERIAL => $serial, _LICENSED_TO => $licensedTo) as $label => $value) { ?>
<div class="product-info-row"><dt><?=$infoEscape($label); ?></dt><dd><?=$infoEscape($value); ?></dd></div>
<?php } ?>
</dl></section>
<?php } ?>