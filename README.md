# LanaiCMS

Lanai Content Management System

## Runtime requirements

- PHP 8.3 with the `mysqli`, `mbstring`, `xml`, and `gd` extensions enabled.
- MySQL or MariaDB, configured through the `mysqli` ADOdb driver.
- `short_open_tag` is not required and should remain disabled.
- Media and Explorer uploads require `fileinfo`; Explorer ZIP downloads require `zip`.

## Recent changes

### Content and comments

- Added an **Allow comments** option to content creation and editing, with English and Thai interface text.
- Added public comments for published content with comments enabled, including email validation, CAPTCHA, CSRF protection, and escaped comment output.
- Successful comments return visitors to the content's comments section.
- Corrected the CAPTCHA font path and disabled CAPTCHA image caching.
- Replaced Save links with native submit buttons on create and edit forms so TinyMCE can synchronize edits before submission.
- Changed Back to return directly to the content list instead of following a `#` link, and removed an extra closing script tag from the edit form.

### Media library

- Added a compact, paginated library with filename and metadata search and inclusive upload-date filters.
- Added multiple-file uploads, drag-and-drop selection, visible server upload limits, and results for individual files.
- Added editing for titles, captions, and alternative text. Metadata changes preserve existing file URLs.
- Integrated Explorer registrations into the library while retaining existing Media-managed uploads.

See the [Media guide](modules/media/README.md) for usage and database upgrade details.

### File explorer

- Added folder navigation, breadcrumbs, sorting, pagination, filename/category/date filters, and optional recursive search.
- Added individual upload progress with Keep both, Skip, and Replace conflict handling.
- Added rename, move, bulk ZIP download, and text, image, and PDF previews.
- Added Trash, restore, and confirmed permanent deletion. Replaced files also go to Trash.
- Added **Register in Media**, which gives supported files stable public URLs that follow Explorer moves and renames. Trash hides registrations; restore revives them.
- Restricted operations to configured storage roots with administrator, path, and CSRF checks. Legacy upload/delete routes no longer modify files.

See the [Explorer guide](modules/explorer/README.md) for storage configuration, limits, and recovery behavior.

### Settings and statistics

- Redesigned the Bootstrap settings interface with grouped navigation, a collapsible desktop sidebar, mobile navigation, and a user menu.
- Replaced common legacy action images with Bootstrap icons.
- Added **Settings > Statistics** with today's views and visitors, total recorded views, a seven-day chart, and popular pages and countries over 30 days.
- Replaced legacy logging with request analytics. Visitor estimates use a daily hash of IP address and user agent; country reporting uses supplied country headers and otherwise displays Unknown.

### Installation, API, and content discovery

- Corrected site URL detection for subdirectory installations and improved generated filesystem-path configuration.
- Updated fresh-install schemas for content comments, media metadata, Explorer registrations, and analytics; cleaned up obsolete installer entries and sample data.
- Kept API token authorization separate from the browser session identity and passed the authenticated user explicitly for content-type ownership and permission checks.
- Changed feeds to use the ten most recently modified published content items and made Content the default search category.
- Removed the legacy News API endpoint, News search integration, and News rewrite rule.

### Retired modules

Removed the legacy `news`, `rssthai`, `log`, and `ajaxtest` modules, plus the `bnews`, `brssthai`, and `bcounter` blocks. Content and the new Statistics dashboard provide the current publishing and reporting workflows. Existing News data is not automatically converted to Content.

## Existing installations

Back up the database and files before upgrading. Review menus and blocks that reference retired modules.

Media adds its metadata and Explorer registration columns on an administrator visit; the database account needs `ALTER` permission for these additions. The fresh-install schema also includes `content.conAllowComments` and the `analytics_event` table. Existing databases must have these schema changes before using the corresponding features; the installer is not a general upgrade runner.

Explorer Trash must be outside the public website and writable by PHP. Its location can be configured in `config.inc.php`; see the Explorer guide. Include that directory in backups if deleted and replaced versions need to be retained. Raw file URLs already embedded in content are not rewritten when files move.

## Development checks

Run the isolated checks from the repository root:

```text
php modules/explorer/tests/files_test.php
php modules/media/tests/library_test.php
php modules/content/tests/content_install_test.php
```

Filesystem and content checks require PDO SQLite; filesystem checks also use `fileinfo` and `zip`. See the Explorer guide for the isolated browser-test setup. These commands document the available checks, not a guarantee that they have passed in every environment.
