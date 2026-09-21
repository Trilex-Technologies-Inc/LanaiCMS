<?php
    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}
    
    if (!$sys_lanai->validateCsrfToken('backup', isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '')) {
        $sys_lanai->getErrorBox("Invalid request, please try again.");
        return;
    }

    if (file_exists($cfg['datadir']."/backup/".$_REQUEST['f'])){
        unlink($cfg['datadir']."/backup/".$_REQUEST['f']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=backup");
    }
?>
