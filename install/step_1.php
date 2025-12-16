<h5 class="mb-4">
    <i class="bi bi-folder-check text-primary me-2"></i>
    <?= _SETUP_CHANGEPMSS; ?>
</h5>

<ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>config.inc.php</span>
        <?php if (is_writable("../config.inc.php")): ?>
            <span class="badge bg-success"><?= _SETUP_WRITEABLE; ?></span>
        <?php else: ?>
            <span class="badge bg-danger"><?= _SETUP_CREATE_LATER; ?></span>
        <?php endif; ?>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>/blocks</span>
        <?php if (is_writable("../blocks")): $d1 = true; ?>
            <span class="badge bg-success"><?= _SETUP_WRITEABLE; ?></span>
        <?php else: $d1 = false; ?>
            <span class="badge bg-danger"><?= _SETUP_CANNOTWRITE; ?></span>
        <?php endif; ?>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>/modules</span>
        <?php if (is_writable("../modules")): $d2 = true; ?>
            <span class="badge bg-success"><?= _SETUP_WRITEABLE; ?></span>
        <?php else: $d2 = false; ?>
            <span class="badge bg-danger"><?= _SETUP_CANNOTWRITE; ?></span>
        <?php endif; ?>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>/theme</span>
        <?php if (is_writable("../theme")): $d3 = true; ?>
            <span class="badge bg-success"><?= _SETUP_WRITEABLE; ?></span>
        <?php else: $d3 = false; ?>
            <span class="badge bg-danger"><?= _SETUP_CANNOTWRITE; ?></span>
        <?php endif; ?>
    </li>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>/datacenter</span>
        <?php if (is_writable("../datacenter")): $d4 = true; ?>
            <span class="badge bg-success"><?= _SETUP_WRITEABLE; ?></span>
        <?php else: $d4 = false; ?>
            <span class="badge bg-danger"><?= _SETUP_CANNOTWRITE; ?></span>
        <?php endif; ?>
    </li>
</ul>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="d-flex gap-2">
        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="step" value="b">
            <button type="submit" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> <?php echo _SETUP_BACK; ?>
            </button>
        </form>

        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="step" value="<?php echo isset($_GET['step']) ? $_GET['step'] : '1'; ?>">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
                <?php echo defined('_SETUP_REFRESH') ? _SETUP_REFRESH : 'Refresh'; ?>
            </button>
        </form>
    </div>

    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="step" value="2">
        <?php if ($d1 && $d2 && $d3 && $d4): ?>
            <button type="submit" class="btn btn-primary">
                <?php echo _SETUP_CONFIG; ?> <i class="bi bi-arrow-right"></i>
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-outline-secondary" disabled>
                <?php echo _SETUP_CONFIG; ?> <i class="bi bi-arrow-right"></i>
            </button>
        <?php endif; ?>
    </form>
</div>
