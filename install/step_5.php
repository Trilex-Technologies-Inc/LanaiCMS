<?php
$configFile = "../config.inc.php";

// Overwrite config file if content is posted
if (isset($_POST['config_content'])) {
    $content = $_POST['config_content'];
    if (file_put_contents($configFile, $content) !== false) {
        $cfgSaved = true;
    } else {
        $cfgSaved = false;
    }
}

// Check if config exists
if (file_exists($configFile)) {
    $cfg = true;
    $statusLabel = '<span class="text-success">' . _SETUP_OK . '</span>';
} else {
    $cfg = false;
    $statusLabel = '<span class="text-danger">' . _SETUP_FAILD . '</span>';
}
?>
<div class="container mt-4">
    <div class="card shadow-sm p-4">
        <h5 class="mb-3"><?= _SETUP_CHECK_CONFIG; ?></h5>

        <?php if (isset($cfgSaved)) : ?>
            <p>
                <?= $cfgSaved 
                    ? '<span class="text-success">' . _SETUP_OK . ' - Config file saved successfully.</span>'
                    : '<span class="text-danger">' . _SETUP_FAILD . ' - Could not save config file. Check permissions.</span>'; ?>
            </p>
        <?php endif; ?>

        <p>
            <?= isset($title) ? $title : ''; ?>
            &nbsp;&nbsp;[<?= isset($statusLabel) ? $statusLabel : ''; ?>]
        </p>

        <div class="d-flex justify-content-between mt-4">
            <!-- Back Button -->
            <button type="button" class="btn btn-outline-secondary" onclick="history.back();">
                &lt; <?= _SETUP_BACK; ?>
            </button>

            <!-- Next Button (only if config exists) -->
            <?php if ($cfg) : ?>
                <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="step" value="<?= ($_REQUEST['step'] + 1); ?>">
                    <button type="submit" class="btn btn-primary">
                        <?= _SETUP_CLEANUP; ?> &gt;
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
