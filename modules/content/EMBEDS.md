# Poll and contact embeds

Create or edit a content page. The visible **Insert into your page** controls above the editor let you select a poll, media item or contact. Choose Page content or Additional content as the destination and click **Insert poll**, **Insert media** or **Insert contact**. The same actions are available in either editor toolbar. Poll/contact references are stored as non-editable placeholders and rendered from current module data on the public page.

Upload images/documents using the **Upload / manage media** link. Inserted images use their library URL and alternative text; documents become links. Trashed files are excluded. After creating new library items, save and reopen the editor to refresh the available choices. The visible controls also insert HTML into the text areas if TinyMCE cannot load.

Saving synchronizes both editor bodies, validates that the page has a title and content, and returns to the saved page with a **View page** link. **Also add a menu link** is optional: the content is created first, and the menu uses its exact database ID. Failed saves keep entered text and never create a menu. A menu failure is reported separately without losing the saved content. Existing installations add the comments column if missing; this requires one-time ALTER permission.

- Manage questions/options/results in Polls and reusable contact information in Contacts.
- Changes to a referenced item appear wherever it is embedded.
- Inactive, missing, and invalid references render nothing.
- Contact names and details are escaped. Email links require a valid address and website links require HTTP or HTTPS.
- Embedded polls include results, POST voting, CSRF protection, option ownership checks, active-poll checks, and the existing IP-based voting cooldown. Votes return to the article. Anonymous polls are not a unique-person voting system: users behind the same IP share the cooldown.
- The older poll block now includes the same CSRF token used by the vote endpoint. Stale open pages must be reloaded before voting.

The modules remain separate. The stored reference looks like:

```html
<div class="mceNonEditable" data-lanai-embed="poll" data-lanai-id="12">Poll: Example</div>
```

Rendering is limited to public content pages; API/feed consumers receive stored content, not interactive forms. No database migration is needed for embeds.

Checks:

```text
php -d extension=pdo_sqlite modules/content/tests/embeds_test.php
php -d extension=pdo_sqlite modules/content/tests/content_install_test.php
php -d extension=pdo_sqlite modules/content/tests/editor_test.php
```

The isolated voting test substitutes MySQL advisory locks. For the browser editor fixture, set `LANAI_CONTENT_BROWSER_TEST=1`, run `php -d extension=pdo_sqlite -d extension=fileinfo -S 127.0.0.1:8881 -t .`, and open `/modules/content/tests/editor_browser.php`. Omit extension flags when already enabled. It exercises the bundled TinyMCE, visible controls, poll/image insertion, form synchronization, page/menu saving, public rendering, fallback editing and a real multipart media upload using disposable storage. Production voting locks and installed-site integration still require deployment verification.
