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

/* Meta values */
$mtaLogo          = !empty($objMeta->MTALOGO) ? $objMeta->MTALOGO : '';
$mtaFavicon       = !empty($objMeta->MTAFAVICON) ? $objMeta->MTAFAVICON : '';
$mtaShowSiteName  = isset($objMeta->MTASHOWSITENAME) ? (int)$objMeta->MTASHOWSITENAME : 1;

/* Site title */
$cfg_title = isset($cfg['title']) ? $cfg['title'] : '';
$captchaProvider = isset($cfg['captcha_provider']) ? $cfg['captcha_provider'] : 'default';
if ($captchaProvider !== 'cloudflare') {
    $captchaProvider = 'default';
}
$turnstileSiteKey = isset($cfg['turnstile_site_key']) ? $cfg['turnstile_site_key'] : '';
$turnstileSecretKey = isset($cfg['turnstile_secret_key']) ? $cfg['turnstile_secret_key'] : '';
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

            <!-- Site Title -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">
                    <?php echo defined('_CFG_SITE_TITLE') ? _CFG_SITE_TITLE : 'Site Title'; ?>
                </label>
                <div class="col-sm-9">
                    <input type="text" name="cfg_title" class="form-control"
                           value="<?php echo htmlspecialchars($cfg_title, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Show Site Name (TRUE/FALSE) -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">
                    Show Site Name
                </label>
                <div class="col-sm-9">
                    <div class="form-check ">
                        <input class="form-check-input"
                               type="checkbox"
                               name="mtaShowSiteName"
                               value="1"
                            <?php echo ($mtaShowSiteName === 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label">
                            Enable site name display
                        </label>
                    </div>
                </div>
            </div>

            <!-- Offline -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_OFFLINE; ?></label>
                <div class="col-sm-9">
                    <select name="cfgStatus" class="form-select">
                        <option value="no" <?php echo $varno; ?>><?php echo _NO; ?></option>
                        <option value="yes" <?php echo $varyes; ?>><?php echo _YES; ?></option>
                    </select>
                </div>
            </div>

            <!-- Keywords -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_KEYWORDS; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaKeywords" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTAKEYWORDS, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Description -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_DESCRIPTION; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaDescription" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTADESCRIPTION, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Abstract -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_ABSTRACT; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaAbstract" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTAABSTRACT, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Author -->
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

            <!-- Distribution -->
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

            <!-- Copyright -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label"><?php echo _CFG_COPY; ?></label>
                <div class="col-sm-9">
                    <input type="text" name="mtaCopyright" class="form-control"
                           value="<?php echo htmlspecialchars($objMeta->MTACOPYRIGHT, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Logo -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Site Logo</label>
                <div class="col-sm-9">
                    <input type="text" name="mtaLogo" class="form-control"
                           value="<?php echo htmlspecialchars($mtaLogo, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Favicon -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Favicon</label>
                <div class="col-sm-9">
                    <input type="text" name="mtaFavicon" class="form-control"
                           value="<?php echo htmlspecialchars($mtaFavicon, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Captcha Provider -->
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Captcha Provider</label>
                <div class="col-sm-9">
                    <select id="cfg_captcha_provider" name="cfg_captcha_provider" class="form-select">
                        <option value="default" <?php echo ($captchaProvider === 'default') ? 'selected' : ''; ?>>
                            System Default Captcha
                        </option>
                        <option value="cloudflare" <?php echo ($captchaProvider === 'cloudflare') ? 'selected' : ''; ?>>
                            Cloudflare Turnstile
                        </option>
                    </select>
                </div>
            </div>

            <!-- Cloudflare Turnstile Site Key -->
            <div id="turnstileSiteKeyRow" class="mb-3 row">
                <label class="col-sm-3 col-form-label">Turnstile Site Key</label>
                <div class="col-sm-9">
                    <input type="text" name="cfg_turnstile_site_key" class="form-control"
                           value="<?php echo htmlspecialchars($turnstileSiteKey, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <!-- Cloudflare Turnstile Secret Key -->
            <div id="turnstileSecretKeyRow" class="mb-3 row">
                <label class="col-sm-3 col-form-label">Turnstile Secret Key</label>
                <div class="col-sm-9">
                    <input type="password" name="cfg_turnstile_secret_key" class="form-control"
                           value="<?php echo htmlspecialchars($turnstileSecretKey, ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="off">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> <?php echo _SAVE; ?>
                </button>
            </div>

        </form>
        <script type="text/javascript">
            (function () {
                var providerEl = document.getElementById('cfg_captcha_provider');
                var siteRow = document.getElementById('turnstileSiteKeyRow');
                var secretRow = document.getElementById('turnstileSecretKeyRow');

                function toggleTurnstileRows() {
                    var isCloudflare = providerEl && providerEl.value === 'cloudflare';
                    var displayStyle = isCloudflare ? '' : 'none';
                    if (siteRow) siteRow.style.display = displayStyle;
                    if (secretRow) secretRow.style.display = displayStyle;
                }

                if (providerEl) {
                    providerEl.addEventListener('change', toggleTurnstileRows);
                    toggleTurnstileRows();
                }
            })();
        </script>
    <?php endif; ?>
</div>
