<?php
    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}
    
    if (file_exists($cfg['datadir']."/backup/".$_REQUEST['f'])){
        unlink($cfg['datadir']."/backup/".$_REQUEST['f']);
        $sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=backup");
    }
?>
