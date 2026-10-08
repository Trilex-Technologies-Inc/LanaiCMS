<?php
// Schedule using the OS task scheduler. HTTP access never boots the application.
if (PHP_SAPI!=='cli') { http_response_code(404); exit; }
if (count($argv)!==2 || !in_array($argv[1],array('--dry-run','--apply'),true)) {
    fwrite(STDERR,"Usage: php modules/privacy/cleanup.php --dry-run|--apply\n"); exit(2);
}
chdir(dirname(__DIR__,2));
try {
    // A minimal CLI bootstrap avoids sessions, themes and browser output.
    require 'config.inc.php';
    if (!isset($dbtype,$dbhost,$dbuser,$dbpw,$dbname,$tablepre,$cfg_url,$cfg_datadir) || !$dbtype) throw new RuntimeException('Database is not configured.');
    require 'include/lanai/php_compat.php';
    require 'include/adodb/adodb.inc.php';
    require __DIR__.'/module.php';
    $db=ADONewConnection(lanai_normalize_dbtype($dbtype));
    if (!$db->NConnect($dbhost,$dbuser,$dbpw,$dbname)) throw new RuntimeException('Database unavailable.');
    $privacy=new LanaiPrivacy($db,$tablepre,$cfg_url);
    $service=new LanaiPrivacyData($db,$tablepre,$cfg_datadir);
    $result=$service->retention($privacy->config(),$argv[1]==='--apply');
    echo json_encode(array('mode'=>$argv[1],'records'=>$result),JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR)."\n";
} catch (Throwable $error) { fwrite(STDERR,"Privacy cleanup failed. Check configuration, migrations and database access.\n"); exit(1); }
