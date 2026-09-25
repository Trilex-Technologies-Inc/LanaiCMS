# Compact media library

Open **Settings → Media** for the file list. Select multiple files or drop them
onto the upload area, then click **Upload selected files**. Search covers filenames,
titles, captions, and alternative text. Date filters use the stored upload date;
both selected dates are inclusive. The list shows 30 files per page.

Select a filename or **Edit details** to edit metadata or delete a file. Metadata
does not rename the physical file or rewrite existing content that copied its URL
or HTML. Explorer continues to browse server folders separately; its files are
not automatically imported into the media database. Explorer's **Register in
Media** action adds selected supported files with stable public URLs; these
registrations follow Explorer moves, renames, trash, and restore operations.

Existing installations add `title` (varchar 255) and `caption` (text) columns on
the first administrator visit. The database user needs ALTER permission for that
one-time, additive upgrade. New installations include these columns. No files are
moved. Uploads use the existing extension allowlist and storage, and require PHP's
fileinfo extension; image thumbnails use GD when available.

The upload form displays the server's per-file, total request, and file-count
limits. Uploads submit as one request and report successful and failed filenames
afterward. The native multi-file picker, search, and editing also work without
JavaScript; drag-and-drop requires JavaScript.

Run the isolated behavior checks from the repository root:

    php modules/media/tests/library_test.php

These checks use database and session doubles and do not modify site data.
