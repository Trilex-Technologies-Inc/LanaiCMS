<?php
include_once("include/lanai/class.navigator.php");

global $db, $tablepre, $sys_lanai;
$sys_nav = new Navigator();

$sql = "SELECT * FROM {$tablepre}menu WHERE mnuActive='y' AND mnuParentId=0 ORDER BY mnuOrder ASC";
$rs = $db->execute($sql);
?>

<!-- Right-side icons (mobile & desktop) -->
<div class="d-flex d-lg-none align-items-center">
    <button class="btn btn-outline-light btn-sm me-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse">
        <i class="fas fa-search"></i>
    </button>
    <button class="btn btn-outline-light btn-sm me-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#loginCollapse" aria-expanded="false" aria-controls="loginCollapse">
        <i class="fas fa-user"></i>
    </button>
    <!-- Navbar toggler (hamburger) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarToggler" aria-controls="navbarToggler"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</div>

<!-- Navbar content -->
<div class="collapse navbar-collapse" id="navbarToggler">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <?php while (!$rs->EOF): ?>
            <?php
            $prelink = $sys_nav->getMenuRealLink($rs);
            $sqlsub = "SELECT * FROM {$tablepre}menu WHERE mnuParentId=" . $rs->fields['mnuId'] .
                " AND mnuActive='y' ORDER BY mnuOrder ASC";
            $rssub = $db->execute($sqlsub);
            $hasSubmenu = $rssub->recordcount() > 0;
            ?>

            <li class="nav-item <?= $hasSubmenu ? 'dropdown' : '' ?> d-flex align-items-center">

                <!-- Parent link ALWAYS clickable -->
                <a class="nav-link"
                   href="<?= $prelink; ?>"
                   target="<?= $rs->fields['mnuTarget']; ?>">
                    <?= htmlspecialchars($rs->fields['mnuTitle']); ?>
                </a>

                <!-- Separate dropdown arrow (only if submenu exists) -->
                <?php if ($hasSubmenu): ?>
                    <a class="nav-link dropdown-toggle px-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark border-0 shadow-sm">

                        <?php while (!$rssub->EOF): ?>
                            <?php $subLink = $sys_nav->getMenuRealLink($rssub); ?>
                            <li>
                                <a class="dropdown-item"
                                   href="<?= $subLink; ?>"
                                   target="<?= $rssub->fields['mnuTarget']; ?>">
                                    <?= htmlspecialchars($rssub->fields['mnuTitle']); ?>
                                </a>
                            </li>
                            <?php $rssub->movenext(); ?>
                        <?php endwhile; ?>

                    </ul>
                <?php endif; ?>

            </li>

            <?php $rs->movenext(); ?>
        <?php endwhile; ?>

    </ul>


    <div class="d-none d-lg-flex">
        <button class="btn btn-outline-light me-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse">
            <i class="fas fa-search"></i>
        </button>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#loginCollapse" aria-expanded="false" aria-controls="loginCollapse">
            <i class="fas fa-user"></i>
        </button>
    </div>

</div>
