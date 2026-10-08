# Authenticator MFA

Members can open **My account → Account & security → Manage two-factor authentication**. Enrollment requires their current password and a valid six-digit authenticator code. The setup screen shows a QR code to scan, with the manual setup key available underneath. The QR code is generated on the server as an inline SVG (bundled MIT-licensed encoder in `include/qrcode`), so the key is never sent to an external QR-code service. Ten single-use recovery codes are shown once after enrollment. Disabling MFA requires the current password and an authenticator or recovery code.

## Site setup

Set `LANAI_MFA_KEY` in the PHP server environment to a securely generated 64-character hexadecimal key. Generate one locally with:

```text
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Keep this key out of source control and backups of the database. Back it up separately and keep it stable: replacing it prevents existing authenticator secrets from being decrypted. An optional `$cfg_mfa_key` in `config.inc.php` is also supported, but the environment variable is preferable because the configuration editor may rewrite that file. PHP OpenSSL is required. Serve login and account pages over HTTPS and keep the server clock synchronized.

Fresh installations create `<prefix>user_mfa`. Existing installations create it on the first authentication/account request, requiring CREATE permission once. Administrators can provision it ahead of time with `php administrator/migrate-mfa.php`. After provisioning, the runtime account needs SELECT, INSERT, and UPDATE on this table. Storage errors deny login rather than bypassing MFA.

Without an encryption key, existing non-MFA accounts can still log in, but enrollment is unavailable. Never remove the table or key to disable MFA. Recovery codes remain usable if the encryption key is unavailable; members must still supply their password. Retain the encrypted secrets, table data, and separately backed-up key during site migration.

## Behavior

- Password verification creates a pending session for enrolled users, without assigning a logged-in user ID. The challenge expires after five minutes.
- Authenticator codes use SHA-1, six digits, and 30-second periods, with one period of clock tolerance. Accepted codes cannot be replayed.
- Recovery codes contain 80 random bits and are stored as SHA-256 hashes. Database conditional updates enforce single use.
- Verification is limited to five attempts per account per five-minute window, including successful verifications. Starting enrollment and incorrect setup passwords also consume attempts.
- Session IDs rotate on login and enrollment. Existing sessions without proof of the current enrollment are signed out. Logout clears pending login and setup state.
- Enabled accounts must also verify a factor when changing their username, email, or password. Password reset does not remove MFA.
- The legacy theme login form now links to the same protected login page.

MFA is optional per member in this release. Email OTP, passwordless login, mandatory-admin enrollment, and recovery-code regeneration are not included. A member who uses recovery to regain access can disable and re-enroll to replace their code set.

## Checks

```text
php -d extension=pdo_sqlite modules/member/tests/mfa_test.php
php modules/member/tests/account_test.php
```

The MFA checks use an isolated SQLite adapter for the database operations, including one-time updates and limits. Deployment still needs a MySQL/MariaDB login/enrollment test, an authenticator app, and browser verification. Algorithm test vectors come from [RFC 6238](https://www.rfc-editor.org/rfc/rfc6238); account-recovery design references the [OWASP MFA guidance](https://cheatsheetseries.owasp.org/cheatsheets/Multifactor_Authentication_Cheat_Sheet.html).
