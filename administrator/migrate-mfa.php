<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__));
ob_start();
require 'setconfig.inc.php';
ob_end_clean();
lanai_mfa_service()->ensureSchema();
echo "MFA table is ready. Configure LANAI_MFA_KEY before enrollment.\n";
