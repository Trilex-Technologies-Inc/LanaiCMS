# Privacy & Compliance module

Open **Administration → Site → Privacy & Compliance** (`setting.php?modname=privacy`). The module provides cookie controls, policy pages, account exports, privacy requests, reviewed account erasure, consent evidence and scheduled retention cleanup. It does not certify GDPR compliance, provide a SOC 2 report, or establish HIPAA compliance.

## Setup

1. Save the privacy settings once on an existing installation. This creates `<prefix>privacy_settings`, `privacy_request`, `privacy_audit` and `privacy_consent` and requires CREATE permission once. Fresh installations include these tables. Public requests never create privacy tables. Preference saves fail if consent evidence cannot be stored; an installation without settings storage denies optional categories.
2. Publish the site's privacy notice and cookie information, or link to existing policy pages. The privacy notice is intentionally empty initially; supply the actual operator identity, contact details, purposes, retention, recipients, and other information appropriate to the site.
3. Register optional scripts by category instead of embedding them directly in a theme. Use `Name | https://script-address`, one per line. Scripts needing inline initialization require a developer integration using `data-lanai-consent` and the same category. Do not register necessary login/CAPTCHA scripts.
4. List first-party optional cookie names if they should be cleared when declined. List exact names, not wildcard patterns. Describe vendor cookies and storage durations in the cookie policy.
5. Verify the deployed site in a browser before and after each choice, including all installed extensions and custom content.
6. Review retention periods and schedule the cleanup command below. Ensure the configured data directory is accessible for avatar export/erasure. Account erasure requires InnoDB for affected tables; legacy nontransactional tables must be migrated by the operator first.
7. Assign an operator to monitor the request queue and contact mailbox. There are no automatic emails. Configure the site's contact email and publish a route for people without an account in the privacy notice.

## Visitor behavior

The public home and module pages show Accept optional cookies, Reject optional cookies, and Manage preferences. A persistent Cookie settings button reopens the panel. Categories are Analytics, External media, and Marketing; all default to off. Necessary login, form-security, and consent storage remain available.

A server-set HttpOnly/SameSite=Lax preference cookie remembers choices for 180 days. HTTPS installations also use Secure. Its name and path distinguish subdirectory installations. Choices contain categories, a timestamp, policy revision and a new random receipt for each save. The database stores the receipt's SHA-256 hash, the choices and a snapshot of the policy configuration. No IP address or user agent is added to the consent record. Signed-in choices link to the account; anonymous receipts are not linked together. External policy URLs are captured, but their remote contents must be archived separately by the operator. Saving admin settings increments the revision and asks visitors to choose again. A server-side CSRF check and current policy revision are required for preference changes.

The preferences page at `module.php?modname=privacy` works without JavaScript. Built-in policy pages use `view=privacy` and `view=cookie`. Visitor controls and policy descriptions support English and Thai; the administration screen follows the existing English admin interface.

## Enforcement and boundaries

- Lanai analytics writes no visit records without Analytics consent. Member, privacy, administration, and non-public endpoints are excluded from analytics. Existing analytics data is not erased by withdrawal.
- Before public HTML is delivered, the server removes unconsented external script tags, external iframe/object/embed resources, and external preconnect/preload hints. Media placeholders open preferences. Registered scripts retain their category even if already present in the page.
- `data-lanai-consent="analytics"`, `"external"`, or `"marketing"` gates explicit script/media tags, including inline scripts. Registered integrations load after consent on a fresh page.
- Necessary local scripts and Cloudflare Turnstile scripts remain available. Turnstile contacts Cloudflare when configured protected forms load; it is disclosed in the built-in cookie page.
- Saving preferences reloads the page so previously running optional scripts are unloaded. Listed host-only first-party cookies are expired at the site path and root. Cookies on other domains, parent-domain cookies, vendor localStorage, and other paths cannot be reliably removed by this mechanism.
- **Unmarked inline JavaScript, remote images/stylesheets, dynamically injected resources, HTTP tracking headers, and third-party server calls are not automatically classified or blocked.** Review custom integrations and move optional code behind the category controls. This is not a universal tracker scanner or firewall.
- Do not bypass the generated `Cache-Control: no-store, private` headers with shared full-page caching: these responses contain session CSRF tokens and preference-dependent resources.
- Administrative pages, standalone custom endpoints, feeds, and APIs do not get the public banner or HTML resource filter. Add explicit integration when extending those surfaces.

Analytics statistics will decline until visitors opt in. The activity log covers privacy operations, not all security events. SOC 2 evidence workflows and HIPAA safeguards are outside this module.

## Account data and requests

Members open **Personal data** from their account navigation or `module.php?modname=privacy&view=data`. Downloads and submissions use POST, a scoped CSRF token, the session's account ID, current-password verification and a fresh MFA/recovery code when enabled. Verification is rate-limited using the persistent MFA attempt counter, including for accounts without MFA. Requests cannot select another account by posting an ID.

The JSON download contains the account profile, authored content, custom content fields, media metadata, avatar bytes, API-token metadata, account-linked consent records and privacy requests. It excludes password hashes, activation tokens, MFA secrets/recovery codes and API-token hashes. Downloads are generated on demand with private/no-store headers, never placed in a public directory. The export fails rather than returning a silently incomplete result when a core table cannot be read.

Members can request access/export, erasure, rectification, restriction, objection or other assistance and read the operator's responses privately. Duplicate open requests of the same type return the existing request. Due dates are one calendar month after receipt, clamped to the last day of the following month when necessary; the queue shows overdue requests. Open requests are never removed by retention cleanup. The queue and logs paginate in groups of 50.

Administrators verify their own password and MFA before changing request status. Completion requires confirmation of identity, data inventory, external-copy review and delivery of the response. Access requests cover the additional records/media files not included in the immediate account download; deliver these through an agreed secure channel. Rectification, restriction and objection requests are handled by the operator in the relevant systems before being marked complete. Recording a request alone does not change those systems.

### Erasure

Completing an erasure request scrubs the account profile and credentials, disables the account, removes MFA and API tokens, removes the avatar, deletes linked consent records, detaches content/media ownership, and scrubs the account's request text and identity references in the privacy log. Other pending requests are closed as cancelled. An inactive account ID remains for referential integrity. Existing browser sessions lose access on their next bootstrap. Admin accounts cannot be erased until responsibilities have been transferred and the account has been demoted; administrators cannot erase themselves.

Database changes and request completion run in a transaction. A database error rolls them back and leaves the request open for retry. Avatar removal is a filesystem operation and cannot be rolled back if the subsequent database commit fails; retrying is safe. Unsafe avatar paths fail closed for operator review.

Published text, custom-field values, shared media files/metadata and comments require an explicit operator review before completion. Comment email addresses are unverified and must not be used as automatic proof of ownership. Contact-form mail, extensions, processor copies, server logs and backups require review in their own systems. Anonymous analytics/consents cannot be reliably matched to an account. Apply retention and ensure backups cannot restore erased personal information. These boundaries are displayed in the admin erasure checklist and member export.

## Retention task

Defaults are 90 days for analytics, 7 days for poll IP cooldown records, and 365 days for consent receipts, privacy activity and closed requests. These are configurable product defaults, not legal retention requirements; choose periods appropriate for the site. Saving settings does not delete data.

Run from the site's directory:

```text
php modules/privacy/cleanup.php --dry-run
php modules/privacy/cleanup.php --apply
```

Schedule `--apply` daily using cron or Windows Task Scheduler with an absolute PHP executable and script path. The command sets its working directory itself, reads the normal configuration, and never starts a web session. HTTP access is rejected. Missing mode/configuration or a database failure returns a nonzero exit status. Capture output and alert on failures. Cleanup is idempotent; if a later table fails, earlier successful removals remain removed and the next run resumes safely. Expired rows are deleted using configured cutoffs; open requests remain untouched. This job does not delete authored content or accounts.

## Checks

```text
php -d extension=pdo_sqlite modules/privacy/tests/privacy_test.php
php -d extension=pdo_sqlite modules/privacy/tests/data_test.php
php modules/privacy/tests/data_test.php --mysql
php administrator/tests/admin_test.php
php modules/member/tests/account_test.php
php -d extension=pdo_sqlite modules/member/tests/mfa_test.php
```

The consent tests cover defaults, expiry, stale policy versions, request validation, analytics writes, independent categories, external resources, subdirectory names, rendering, and the actual output-buffer callback. Data tests cover password/MFA/CSRF verification, account isolation, secret-free exports, requests, deadline boundaries, consent snapshots, erasure rollback/protection and retention. The MySQL/MariaDB run creates and drops only randomly prefixed `privacy_test_<random>_*` fixture tables. Set `LANAI_PRIVACY_TEST_CONFIG` to a separate PHP configuration file to test an isolated database without changing site configuration.

For the isolated banner browser fixture, run PHP's local server with `LANAI_PRIVACY_BROWSER_TEST=1` at `127.0.0.1:8879` and open `/modules/privacy/tests/browser.php`. This verifies initial blocking, category controls, rejection requests and retry handling. Still inspect network requests/cookies on the configured deployment, including every installed integration.
