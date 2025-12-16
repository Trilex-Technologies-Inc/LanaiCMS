<?php
if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$objConfig = new SysConfig();
$status = $objConfig->getCurrentStatus();

$objMeta = new Meta();
$objMeta->_table = $cfg['tablepre'] . "meta";
$objMeta->mtaId=1;
$objMeta->Load("mtaId=1");
if (!isset($cfg['title'])) {
    $cfg_title = '';
}
else
    $cfg_title= $cfg['title'];

?>

<div class="container mt-4">
    <h3 class="mb-4"><?=_CFG_SETTING; ?></h3>

    <?php if (!$objConfig->configIsWrite()) : ?>
        <div class="alert alert-danger" role="alert">
            <?=_CFG_CANNOT_WRITE; ?>
        </div>
    <?php else : ?>

        <div class="mb-3">
            <button type="submit" form="configForm" class="btn btn-primary me-2">
                <i class="bi bi-save"></i> <?=_SAVE; ?>
            </button>
            <a href="module.php?modname=setting" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> <?=_BACK; ?>
            </a>
        </div>

        <form name="form" id="configForm" method="post" action="<?=$_SERVER['PHP_SELF']; ?>" class="card p-4 shadow-sm">
            <input type="hidden" name="modname" value="config">
            <input type="hidden" name="mf" value="editconfig">

            <?php
            $varno = ($status == "no") ? "selected" : "";
            $varyes = ($status == "yes") ? "selected" : "";
            ?>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">
                    <?php echo defined('_CFG_SITE_TITLE') ? _CFG_SITE_TITLE : 'Site Title'; ?>
                </label>
                <div class="col-sm-9">
                    <input type="text"
                           name="cfg_title"
                           maxlength="255"
                           class="form-control"
                           value="<?php echo htmlspecialchars($cfg_title, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_OFFLINE; ?></label>
                <div class="col-sm-9">
                    <select name="cfgStatus" class="form-select">
                        <option value="no" <?=$varno; ?>><?=_NO; ?></option>
                        <option value="yes" <?=$varyes; ?>><?=_YES; ?></option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_KEYWORDS; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaKeywords" maxlength="255" class="form-control"
                           value="<?=$objMeta->MTAKEYWORDS; ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_DESCRIPTION; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaDescription" maxlength="255" class="form-control"
                           value="<?=$objMeta->MTADESCRIPTION; ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_ABSTRACT; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaAbstract" maxlength="100" class="form-control"
                           value="<?=$objMeta->MTAABSTRACT; ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_AUTHOR; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaAuthor" maxlength="75" class="form-control"
                           value="<?=$objMeta->MTAAUTHOR; ?>">
                </div>
            </div>

            <?php
            $v1 = $v2 = $v3 = "";
            if ($objMeta->MTADESCRIPTION == "Global") $v1 = "selected";
            else if ($objMeta->MTADESCRIPTION == "Local") $v2 = "selected";
            else $v3 = "selected";
            ?>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_DISTRIBUTION; ?></label>
                <div class="col-sm-9">
                    <select name="mtaDistribution" class="form-select">
                        <option value="Global" <?=$v1; ?>>Global</option>
                        <option value="Local" <?=$v2; ?>>Local</option>
                        <option value="Internal Use" <?=$v3; ?>>Internal Use</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?=_CFG_COPY; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaCopyright" maxlength="255" class="form-control"
                           value="<?=$objMeta->MTACOPYRIGHT; ?>">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save2"></i> <?=_SAVE; ?>
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

