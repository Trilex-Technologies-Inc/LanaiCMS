# Interface languages

LanaiCMS includes English, Thai, Spanish, German, French, Brazilian Portuguese
and Japanese. Choose a language in **Administration > Site > Language**, then
save. The installer also offers these languages. The configured language applies
to the site interface and administration together; there is no per-user override.

The five new packs cover every constant in the existing English core, module and
installer language files. Shared translations additionally cover administration
navigation, the content editor, member account labels and cookie banner controls.
Screens with remaining hardcoded English, including parts of the dashboard,
Explorer, Media and privacy administration, still need to be migrated. The bundled
TinyMCE toolbar remains English. Missing shared translations fall back to English.
Native-speaker review is recommended before describing these packs as fully reviewed.

Interface translation does not translate authored pages, user data, theme content
or stored policy text. Linked translations of pages, a visitor language switcher,
localized date/number formatting and separate admin/site language preferences are
additional features, not supplied by these packs.

## Maintaining translations

`translations.tsv` is a UTF-8 tab-separated catalog with an English source column
and one column for each new language. Keep HTML links, file paths, protocol names
and `{section}` placeholders intact. Represent newlines as literal `\n` in this
file. Generated `lang-*.php` packs should not be edited directly.

After changing the catalog or English constant files, run:

```text
php language/build-packs.php
php language/tests/localization_test.php
php administrator/tests/admin_test.php
```

For new PHP interface labels, require `include/lanai/localization.php`, use
`lanai_translate($english)` and escape its result for the output context. Existing
English/Thai pairs can use `lanai_translate($english, null, $thai)` to retain Thai.
The helper returns plain strings; callers must escape HTML, attributes and JSON
appropriately. `lanai_language_locale()` supplies HTML language tags, including
`pt-BR` for Brazilian Portuguese.
