<?php
// Endpoints bootstrap independently of the HTML settings renderer.
chdir(dirname(__DIR__, 2));
ob_start();
require_once 'setconfig.inc.php';
ob_end_clean();
require_once 'modules/member/module.php';
require_once 'modules/media/module.php';
require_once __DIR__ . '/class.ExplorerFiles.php';
require_once __DIR__ . '/class.ExplorerMedia.php';
require_once __DIR__ . '/class.ExplorerResponse.php';

require_once __DIR__ . '/request.php';
define('LANAI_EXPLORER_READY', true);
