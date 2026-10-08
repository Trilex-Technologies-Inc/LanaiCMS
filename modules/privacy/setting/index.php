<?php
if (!defined('LANAI_ADMIN_REQUEST')) { http_response_code(403); exit; }
global $lanaiPrivacy,$sys_lanai,$db,$cfg;
$privacyData=new LanaiPrivacyData($db,$cfg['tablepre'],$cfg['datadir']);
$privacyMessage='';
if (($_SERVER['REQUEST_METHOD']??'')==='POST') {
    try {
        if (($_POST['action']??'')==='review') {
            $actor=$privacyData->authorize('POST',$_POST,$_SESSION,$sys_lanai,lanai_mfa_service(),true);
            $privacyData->review((int)$actor['userId'],$_POST['requestId']??'',$_POST['status']??'',$_POST['response']??'',($_POST['reviewed']??'')==='1');
            $privacyMessage='Request updated.';
        } else {
        if (!is_string($_POST['csrf_token']??null) || !$sys_lanai->validateCsrfToken('privacy_admin',$_POST['csrf_token'])) throw new InvalidArgumentException('Invalid request. Reload this page and try again.');
        $lanaiPrivacy->saveConfig($_POST);
        $privacyData->audit((int)$_SESSION['uid'],0,'','settings_saved');
        $privacyMessage='Saved. Visitors will be asked to review their preferences again.';
        }
    } catch (Throwable $error) { $privacyMessage=$error instanceof PDOException?'Privacy storage is unavailable.':$error->getMessage(); }
}
$privacyConfig=$lanaiPrivacy->config();
$privacyEscape=static function($v) { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); };
?>
<h2>Privacy &amp; Compliance</h2>
<p>Manage cookie preferences, optional integrations, and your site's privacy information.</p>
<?php if ($privacyMessage): ?><p role="status" class="alert alert-info"><?= $privacyEscape($privacyMessage) ?></p><?php endif; ?>
<p><a href="module.php?modname=privacy" target="_blank" rel="noopener">Preview visitor preferences</a></p>
<form method="post" action="setting.php?modname=privacy">
<?php $sys_lanai->renderCsrfField('privacy_admin'); ?>
<fieldset class="mb-4"><legend>Policies</legend>
<p>Publish your site's own notice, including who operates the site, how to contact you, and how personal information is used. Blank policy links use the pages supplied by this module. Policy text is displayed as plain text.</p>
<?php foreach (array('privacyText'=>'Privacy notice','cookieText'=>'Additional cookie policy information','privacyUrl'=>'Privacy notice link (optional)','cookieUrl'=>'Cookie policy link (optional)') as $name=>$label): ?>
<label class="d-block mb-3"><?= $privacyEscape($label) ?>
<?php if (substr($name,-4)==='Text'): ?><textarea class="form-control" name="<?= $name ?>" rows="7" maxlength="30000"><?= $privacyEscape($privacyConfig[$name]) ?></textarea>
<?php else: ?><input class="form-control" type="text" name="<?= $name ?>" value="<?= $privacyEscape($privacyConfig[$name]) ?>" maxlength="2000"><?php endif; ?></label>
<?php endforeach; ?></fieldset>
<fieldset class="mb-4"><legend>Optional integrations</legend>
<p>List scripts here instead of adding them directly to a theme or content. Use one <code>Name | https://script-address</code> per line. A script loads only after its category is accepted. Do not list login, CAPTCHA, or other necessary scripts.</p>
<?php foreach (LanaiPrivacy::CATEGORIES as $category):
    $scripts=array(); $cookies=array();
    foreach ($privacyConfig['scripts'] as $script) if ($script['category']===$category) $scripts[]=$script['name'].' | '.$script['url'];
    foreach ($privacyConfig['cookies'] as $cookie) if ($cookie['category']===$category) $cookies[]=$cookie['name'];
?>
<h3 class="h5"><?= ucfirst($category) ?></h3>
<label class="d-block mb-2">Scripts<textarea class="form-control" name="<?= $category ?>Scripts" rows="3" maxlength="16000"><?= $privacyEscape(implode("\n",$scripts)) ?></textarea></label>
<label class="d-block mb-3">First-party cookie names to clear when declined<input class="form-control" name="<?= $category ?>Cookies" value="<?= $privacyEscape(implode(', ',$cookies)) ?>" maxlength="4000"></label>
<?php endforeach; ?>
<p>Only listed host cookies at the site path and root can be cleared here. A website cannot remove another domain's cookies. Audit custom inline code, remote images, stylesheets, and dynamically created resources before publishing integrations.</p>
</fieldset>
<fieldset class="mb-4"><legend>Data retention</legend>
<p>Set the storage periods appropriate for this site. Cleanup removes only records older than these limits; open requests are retained. Schedule the CLI cleanup task described in the privacy guide. Saving these values does not run deletion.</p>
<?php foreach (array('analyticsDays'=>'Analytics events','pollDays'=>'Poll IP cooldown records','consentDays'=>'Consent receipts','auditDays'=>'Privacy activity log','requestDays'=>'Closed privacy requests') as $key=>$label): ?>
<label class="d-block mb-3"><?= $privacyEscape($label) ?> (days)<input class="form-control" type="number" name="<?= $key ?>" min="1" max="3650" required value="<?= (int)$privacyConfig[$key] ?>"></label>
<?php endforeach; ?></fieldset>
<button class="btn btn-primary" type="submit">Save privacy settings</button>
</form>
<hr><h3 class="h5">Current controls</h3>
<ul><li>Lanai visit analytics require Analytics consent.</li><li>External scripts and embedded media in the initial public HTML require External media consent, unless categorized explicitly. Cloudflare Turnstile remains available for protected forms.</li><li>Necessary session storage and administrator sidebar preferences remain available.</li><li>Choices expire after <?= LanaiPrivacy::DAYS ?> days or when these settings change.</li></ul>
<p>This module supplies privacy controls. Organizational policies, security reviews, SOC 2 examinations, and HIPAA requirements are separate work.</p>
<?php include __DIR__.'/requests.php'; ?>
