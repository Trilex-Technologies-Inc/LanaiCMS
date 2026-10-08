<?php
if (!defined('LANAI_ADMIN_REQUEST') || !isset($privacyData,$privacyEscape)) { http_response_code(403); exit; }
$esc=$privacyEscape;
$offset=max(0,min(100000,(int)($_GET['offset']??0)));
try {
    $queue=$privacyData->requests((int)$_SESSION['uid'],true,$offset);
    $audit=$privacyData->events((int)$_SESSION['uid'],'audit',$offset);
    $consents=$privacyData->events((int)$_SESSION['uid'],'consent',$offset);
} catch (Throwable $error) { echo '<p role="alert">Save privacy settings to initialize the request and audit tables. If this persists, check database access.</p>'; return; }
?>
<hr><h2>Personal-data requests</h2>
<p>Requests are due one calendar month after receipt (UTC). Review the queue regularly; this module does not send email notifications. Responses appear in the member's private request history. Deliver full exports through an agreed secure channel before marking access requests completed.</p>
<details class="mb-3"><summary>Data inventory and erasure checklist</summary>
<ul>
<li>Verify the requester and the scope. Review any applicable retention obligations before erasure.</li>
<li>Review authored content, custom content fields, media files and metadata. Remove personal information or document a justified exception before confirming completion. Account erasure clears ownership links; it does not delete published material or shared media files.</li>
<li>Review comments individually: comment email addresses are unverified and cannot safely establish ownership. Review contact-form email in the recipient mailbox.</li>
<li>Handle server logs, analytics, poll IP records, plugin tables, backups and processor copies. Analytics and anonymous consent records cannot be reliably matched to an account. Apply retention and prevent erased data from being restored from backups.</li>
<li>Account erasure removes the profile, password, activation token, MFA, API tokens, avatar and linked consent history. It disables the account, removes attribution, and scrubs the account's request details and subject references in the privacy log. An inactive account ID remains for referential integrity.</li>
<li>Transfer an administrator's responsibilities and demote the account before erasure. Confirm the response has been delivered before disabling the account; it will no longer have access to request history.</li>
</ul></details>
<?php if (!$queue): ?><p>No requests on this page.</p><?php endif; ?>
<?php foreach ($queue as $request): ?>
<article class="border rounded p-3 mb-3">
<h3 class="h5"><?= $esc(ucfirst($request['requestType'])) ?> · Account <?= (int)$request['userId'] ?></h3>
<p><code><?= $esc($request['requestId']) ?></code> · <?= $esc($request['requestStatus']) ?> · Due <?= $esc(gmdate('Y-m-d',(int)$request['dueAt'])) ?> UTC<?= !$request['closedAt'] && (int)$request['dueAt']<time()?' · OVERDUE':'' ?></p>
<p><?= nl2br($esc($request['requestDetails'])) ?></p><p><?= nl2br($esc($request['responseText'])) ?></p>
<?php if (!(int)$request['closedAt']): ?>
<form method="post" action="setting.php?modname=privacy">
<?php $sys_lanai->renderCsrfField('privacy_data'); ?>
<input type="hidden" name="action" value="review"><input type="hidden" name="requestId" value="<?= $esc($request['requestId']) ?>">
<label class="d-block mb-2">Status<select class="form-select" name="status"><option value="reviewing">Under review</option><option value="completed">Complete<?= $request['requestType']==='erase'?' and erase account':'' ?></option><option value="rejected">Decline with explanation</option></select></label>
<label class="d-block mb-2">Response to the requester<textarea class="form-control" name="response" maxlength="4000" rows="3" required></textarea></label>
<label class="d-block mb-2"><input type="checkbox" name="reviewed" value="1"> I have verified the requester, completed the data inventory and external-copy review, and delivered the response. Required for completion. Erasure cannot be undone.</label>
<label class="d-block mb-2">Your current password<input class="form-control" type="password" name="currentPassword" autocomplete="current-password" maxlength="255" required></label>
<label class="d-block mb-2">Your authenticator or recovery code (if enabled)<input class="form-control" name="mfaCode" autocomplete="one-time-code" maxlength="64"></label>
<button class="btn btn-primary" type="submit">Update request</button>
</form><?php endif; ?></article>
<?php endforeach; ?>
<h2>Privacy activity</h2>
<p>This log records privacy settings changes, exports and request handling. It is not a general security log.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Time (UTC)</th><th>Action</th><th>Actor</th><th>Subject</th><th>Request</th></tr></thead><tbody>
<?php foreach ($audit as $event): ?><tr><td><?= $esc(gmdate('Y-m-d H:i:s',(int)$event['createdAt'])) ?></td><td><?= $esc($event['eventType']) ?></td><td><?= (int)$event['actorId'] ?></td><td><?= (int)$event['subjectId'] ?></td><td><?= $esc($event['requestId']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<h2>Consent receipts</h2>
<p>Each saved choice records its categories, policy revision, policy configuration and time, without an IP address or user agent. Signed-in choices link to the account. External policy links must be archived separately by the operator.</p>
<?php foreach ($consents as $consent): ?><details class="mb-2"><summary><?= $esc(gmdate('Y-m-d H:i:s',(int)$consent['createdAt'])) ?> UTC · Revision <?= (int)$consent['policyRevision'] ?> · Account <?= (int)$consent['userId'] ?></summary><pre class="text-wrap"><?= $esc($consent['choices']) ?></pre><pre class="text-wrap"><?= $esc($consent['policySnapshot']) ?></pre></details><?php endforeach; ?>
<nav aria-label="Privacy records pages">
<?php if ($offset>0): ?><a href="setting.php?modname=privacy&amp;offset=<?= max(0,$offset-50) ?>">Previous 50</a><?php endif; ?>
<?php if (max(count($queue),count($audit),count($consents))===50): ?><a href="setting.php?modname=privacy&amp;offset=<?= $offset+50 ?>">Next 50</a><?php endif; ?>
</nav>
