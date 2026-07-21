<?php
    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	}

    $bup=new DBBackup();
    if ((empty($_REQUEST['schema'])) AND (empty($_REQUEST['value']))) {
        $sys_lanai->getErrorBox(_SELECT_OPTION);
    } else {
      if ($_REQUEST['schema']=="s") $schema=true; else $schema=false;
      if ($_REQUEST['value']=="v") $value=true; else $value=false;
      if ((count($_REQUEST['table'])) > 0) {
          for ($i=0;$i<(count($_REQUEST['table']));$i++) {
            $sqlStr.=$bup->BackUpTable($_REQUEST['table'][$i],$schema,$value);
          }
      }
      if (!empty($sqlStr)) {
        $filename=date("YmdHis");
        $bup->SaveFile($sqlStr,$filename);
        ?><?=_BACKUP_COMPLETE." : ".$filename; ?><br/><?php
        $sys_lanai->go2Page($_SERVER['PHP_SELF']."?modname=backup");
      }
    }

?>
