<?php
    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}

    if (file_exists($cfg['datadir']."/backup/".$_REQUEST['f'])){
        $res=new DBRestore($_REQUEST['f']);
        ?>
        <img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
        <a href="setting.php?modname=backup"><?=_BACK; ?></a>
        <?php
    }
?>
