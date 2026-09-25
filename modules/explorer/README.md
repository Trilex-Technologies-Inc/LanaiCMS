# Explorer

Open **Settings → Explorer**. The interface stays a compact, sortable file list.

- Click folders or breadcrumbs to navigate; **Up** opens the parent folder.
- Search by filename, file category, and inclusive modified-date range. Enable
  **Include subfolders** for recursive search. Large lists show 100 rows per page.
- Drop files or select multiple files. Each uploads separately with progress and
  its own result. Choose **Keep both**, **Skip**, or **Replace** before uploading.
- Check rows for moves, ZIP downloads, Media registration, or Trash. Rename uses
  one selected item. The move dialog browses destination folders and locations.
- Select a filename or **Preview** for the optional text, raster-image, or PDF
  panel. PDFs depend on browser support; a download link is always available.
- **Trash** lists deleted/replaced items from all configured locations. Restore
  supports Skip or Keep both. Restore a missing parent folder before its children.
  Permanent deletion requires confirmation and cannot be undone.

## Storage boundaries

Defaults are `datacenter` (Files) and `images` (Images). Dotfiles, symbolic links,
server scripts/active web files, and internal folders named `cache`, `package`,
`media`, `backup`, or `log` are excluded. Existing Media-managed files remain in
the Media module. Explorer's legacy upload/delete routes no longer perform file
mutations; downloads now require an administrator and validate the same roots.

To customize locations, add these optional values to `config.inc.php`:

```php
$cfg_explorer_roots = array(
    'data' => array('label' => 'Files', 'path' => $cfg_datadir),
    'images' => array('label' => 'Images', 'path' => $cfg_dir . '/images'),
);
$cfg_explorer_trash = '/absolute/private/path/lanai-trash';
```

Keep root IDs stable: Media registrations refer to these IDs. The trash directory
must be outside the website, writable by PHP, and on a filesystem that supports
renames from the content roots. By default it is a sibling of the server's public
document root (or the installation when the server does not expose a document
root), named `.lanai-explorer-trash-<installation-hash>`. If its parent is not writable,
configure an appropriate private path. The first mutation creates the directory.
Back it up alongside the website if you want deleted versions in backups.

Operations use a filesystem lock. Database-reference failures roll moves back;
failed items in a bulk operation are reported individually. Replacements move the
previous version into Trash. Interrupted/crashed operations should be reviewed by
an administrator; filesystem and database changes are not one atomic transaction.

## Media registration

**Register in Media** publishes supported selected files through stable URLs such
as `modules/explorer/media.php?id=123`. Files stay in their Explorer folders.
Registration is idempotent. Use the resulting Media link to edit title, caption,
and alternative text.

Moving a registered file or its parent folder updates its source reference while
keeping the Media URL unchanged. Replacing its bytes keeps the current Media URL
and refreshes size/type/dimensions; the previous bytes are recoverable from Trash.
Trashing hides registrations and their URLs return 404; restoring revives them.
Permanent deletion removes the corresponding registrations. Deleting an Explorer
registration from Media also sends its file to Explorer Trash.

Raw folder URLs already embedded in content are not rewritten. Existing uploads
managed directly by Media retain their original behavior. New nullable fields
`explorerRoot`, `explorerPath`, and `explorerTrashId` are added on the first admin
visit (the database user needs ALTER permission); new installs include them.

## Limits and checks

Uploads accept the content extensions listed in `ExplorerFiles::UPLOAD_TYPES`.
Executable and active web-file extensions cannot be uploaded or introduced by
renaming. PHP upload/request limits still apply to each file. ZIP downloads need
PHP's zip extension and are limited to 10,000 entries / 512 MB of source files.
Recursive searches and folder operations are capped at 10,000 items; recursive
search is capped at 30 levels. Text previews show at most 100 KB. The interface
requires JavaScript, while all authorization/path/CSRF checks run on the server.

Isolated tests (never use a production database):

```text
php modules/explorer/tests/files_test.php
php modules/media/tests/library_test.php
php modules/content/tests/content_install_test.php
```

The filesystem and content tests use PDO SQLite; filesystem tests also use
fileinfo and zip. On Windows, load disabled extensions with PHP's `-d extension=`
options. `tests/router.php` supports browser tests only when run with PHP's built-in
server and `LANAI_EXPLORER_TEST=1`, with `LANAI_EXPLORER_TEST_DIR` pointing to a new
disposable folder. Evaluate `tests/browser_test.js` on that isolated test page.
