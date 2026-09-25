<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary mb-3">
                <?=_SETUP_COPY_CODE; ?> <code>config.inc.php</code>
            </h5>

            <div class="mb-3">
                <label for="configCode" class="form-label fw-semibold">Generated Configuration</label>
                <textarea id="configCode" class="form-control" rows="15" readonly>
&lt;?php

    ## Config your database server
    $dbname="<?=$_SESSION['dbname']; ?>";
    $dbhost="<?=$_SESSION['dbhost']; ?>";
    $dbuser="<?=$_SESSION['dbuser']; ?>";
    $dbpw="<?=$_SESSION['dbpw']; ?>";
    $tablepre="<?=$_SESSION['tablepre']; ?>";
    $dbtype="mysqli";

    ## Site
    $cfg_title="<?=$_SESSION['cfg_title']; ?>";
    $cfg_footer="&amp;reg; Power by La-Nai Content Management System.<br/><a href=\"http://la-nai.sourceforge.net\" target=\"_blank\">La-Nai</a> is Free Software released under the <a href=\"license.txt\" title=\"GNU/GPL License\" target=\"_blank\">GNU/GPL license</a>.";
    $cfg_url=<?=htmlspecialchars(var_export(rtrim($_SESSION['cfg_url'], '/'), true), ENT_QUOTES, 'UTF-8'); ?>;
    $cfg_datadir=<?=htmlspecialchars(var_export(rtrim($_SESSION['cfg_dir'], '/\\') . DIRECTORY_SEPARATOR . 'datacenter', true), ENT_QUOTES, 'UTF-8'); ?>;
    $cfg_packagedir=<?=htmlspecialchars(var_export(rtrim($_SESSION['cfg_dir'], '/\\') . DIRECTORY_SEPARATOR . 'datacenter' . DIRECTORY_SEPARATOR . 'package', true), ENT_QUOTES, 'UTF-8'); ?>;
    $cfg_dir=<?=htmlspecialchars(var_export(rtrim($_SESSION['cfg_dir'], '/\\'), true), ENT_QUOTES, 'UTF-8'); ?>;
    $cfg_email="<?=$_SESSION['cfg_email']; ?>";
    $cfg_theme="<?=$_SESSION['cfg_theme']; ?>";

    ## Misc
    $cfg_off="no";
    $cfg_offsettime=<?=$_SESSION['cfg_offsettime']; ?>;
    $cfg_sendmail="<?=$_SESSION['cfg_sendmail']; ?>";
    ## smtp
    $cfg_smtp_host="<?=$_SESSION['smtp_host']; ?>";
    $cfg_smtp_port="<?=$_SESSION['smtp_port']; ?>";

    ## Language
    $cfg_lang="<?=$_SESSION['cfg_lang']; ?>";

    ## SEO
    $cfg_seo="no";

    ## Captcha
    $cfg_captcha_provider="default";
    $cfg_turnstile_site_key="";
    $cfg_turnstile_secret_key="";

?&gt;
                </textarea>
            </div>

            <form method="POST" action="<?=$_SERVER['PHP_SELF']; ?>" class="d-flex justify-content-between" id="configForm">
                <input type="hidden" name="step" value="<?=($_REQUEST['step']-1)?>">
                
                <!-- Hidden input to send config content -->
                <input type="hidden" name="config_content" id="configContent">

                <button type="button" class="btn btn-outline-secondary" onclick="history.back();">
                    &lt; <?=_SETUP_BACK; ?>
                </button>

                <input type="hidden" name="step" value="<?=($_REQUEST['step']+1)?>">
                <button type="submit" class="btn btn-primary">
                    <?=_SETUP_VERIFY_CONFIG; ?> &gt;
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('configForm').addEventListener('submit', function() {
    // Copy textarea content to hidden input
    document.getElementById('configContent').value = document.getElementById('configCode').value;
});
</script>
