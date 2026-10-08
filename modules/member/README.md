# Member area

Open `module.php?modname=member&mf=meminfo`. Successful sign-in and the existing signed-in login page now open this area.

- Overview shows the member name, username, email, join date, and account shortcuts.
- Profile edits the existing name, website, contact/address fields, and GIF avatar.
- Account & security edits username/email and optionally the password, requiring the current password.
- Administrators also see a Site administration link.

The area uses the existing user table, authentication, and public site shell. No database migration is required. English and Thai labels are provided. Existing edit links open Profile; older activation links remain supported.

New sections can be added to the explicit section registry in `meminfo.php`, with their own rendering and allowed save fields. Account updates never accept a role, privilege, status, or target user ID from the form. Future display names, biographies, notification preferences, or session management will need their own storage and behavior.

Run `php modules/member/tests/account_test.php` for isolated validation and controller checks. These use a database double and do not change site data. On a configured site, also verify mobile layout, login/logout, both form saves, GIF upload, and English/Thai rendering with member and administrator accounts.
