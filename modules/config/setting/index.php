<?php
/* Prevent direct access */
if (!preg_match('/setting\.php/i', $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$objConfig = new SysConfig();
$status = $objConfig->getCurrentStatus();

/* Load Meta */
$objMeta = new Meta();
$objMeta->_table = $cfg['tablepre'] . "meta";
$objMeta->mtaId = 1;
$objMeta->Load("mtaId=1");

$mtaLogo    = !empty($objMeta->MTALOGO) ? $objMeta->MTALOGO : '';
$mtaFavicon = !empty($objMeta->MTAFAVICON) ? $objMeta->MTAFAVICON : '';

/* Site title */
if (!isset($cfg['title'])) {
    $cfg_title = '';
} else {
    $cfg_title = $cfg['title'];
}
?>

<div class="container mt-4">
    <h3 class="mb-4"><?php echo _CFG_SETTING; ?></h3>

    <?php if (!$objConfig->configIsWrite()) : ?>
        <div class="alert alert-danger">
            <?php echo _CFG_CANNOT_WRITE; ?>
        </div>
    <?php else : ?>

        <div class="mb-3">
            <button type="submit" form="configForm" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> <?php echo _SAVE; ?>
            </button>
            <a href="module.php?modname=setting" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> <?php echo _BACK; ?>
            </a>
        </div>

        <form id="configForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="card p-4 shadow-sm">
            <input type="hidden" name="modname" value="config">
            <input type="hidden" name="mf" value="editconfig">

            <?php
            $varno  = ($status == "no") ? "selected" : "";
            $varyes = ($status == "yes") ? "selected" : "";
            ?>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">
                    <?php echo defined('_CFG_SITE_TITLE') ? _CFG_SITE_TITLE : 'Site Title'; ?>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="cfg_title" class="form-control"
                           value="<?php echo htmlspecialchars($cfg_title, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_OFFLINE; ?></label>
                <div class="col-sm-9">
                    <select name="cfgStatus" class="form-select">
                        <option value="no" <?php echo $varno; ?>><?php echo _NO; ?></option>
                        <option value="yes" <?php echo $varyes; ?>><?php echo _YES; ?></option>
                    </select>
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_KEYWORDS; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaKeywords" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTAKEYWORDS, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_DESCRIPTION; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaDescription" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTADESCRIPTION, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_ABSTRACT; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaAbstract" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTAABSTRACT, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_AUTHOR; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaAuthor" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTAAUTHOR, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <?php
            $v1 = $v2 = $v3 = "";
            if ($objMeta->MTADISTRIBUTION == "Global") $v1 = "selected";
            else if ($objMeta->MTADISTRIBUTION == "Local") $v2 = "selected";
            else $v3 = "selected";
            ?>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_DISTRIBUTION; ?></label>
                <div class="col-sm-9">
                    <select name="mtaDistribution" class="form-select">
                        <option value="Global" <?php echo $v1; ?>>Global</option>
                        <option value="Local" <?php echo $v2; ?>>Local</option>
                        <option value="Internal Use" <?php echo $v3; ?>>Internal Use</option>
                    </select>
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_COPY; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaCopyright" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTACOPYRIGHT, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Site Logo</label>
                <div class="col-sm-9">
                    <input type="text" name="mtaLogo" class="form-control"

                           value="<?php echo htmlspecialchars($mtaLogo, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>


            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Favicon</label>
                <div class="col-sm-9">
                    <input type="text" name="mtaFavicon" class="form-control"

                           value="<?php echo htmlspecialchars($mtaFavicon, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> <?php echo _SAVE; ?>
                </button>
            </div>

        </form>
    <?php endif; ?>
</div>
