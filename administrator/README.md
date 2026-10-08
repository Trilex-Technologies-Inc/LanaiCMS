# Administration interface

`setting.php` is the canonical administrator entry point. `/administrator/` redirects there so existing form actions, bookmarks, and relative asset URLs continue working.

The entry point checks that the current session belongs to an active administrator before loading module code or AJAX handlers. Invalid routes return 404. Guests, ordinary members, and inactive administrators receive 403. Responses are marked private/no-store and noindex.

The admin layout is `administrator/templates/layout.tpl`, with `assets/admin.css` and `assets/js/admin.js`. It preserves the existing grouped navigation, dashboard, mobile menu, and collapsible sidebar. My account opens the member area. Public theme templates, styles, headers, footers, and blocks are not loaded. Existing settings modules may still reference legacy images under the configured theme.

The public module entry point rejects nested paths, preventing it from loading settings files through a public module route. Existing action-level permission and CSRF checks remain in place.

Add administration pages beneath `modules/<module>/setting/`, using simple letters, numbers, and underscores for module/action names. Add navigation entries to the admin template. Shared bootstrap, user authentication, database, and legacy editor/calendar helpers remain in use.

Run `php administrator/tests/admin_test.php` for isolated access, routing, and real Smarty rendering checks. A configured installation is still needed to check live login, editor submissions, downloads, AJAX actions, and responsive behavior in a browser.
