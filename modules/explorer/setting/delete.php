<?php
// Legacy route: all operations now use the authenticated, CSRF-protected API.
if (stripos($_SERVER['PHP_SELF'], 'setting.php') === false) die('Direct access denied.');
include __DIR__ . '/index.php';
