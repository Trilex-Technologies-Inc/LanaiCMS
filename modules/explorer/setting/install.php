<?php

    if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
       die ( "You can't access this file directly..." );
    }

    $module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );

    /* load class package */
    include_once("include/lanai/class.package.php");

    /* initial object */
    $objPackage=new Package();
    $cus_modname="explorer";
    $cus_title="explorer";

    /* case steps */
    switch ($_REQUEST['step']) {
      case "1":
        /* create necessary tables */
        ?>
        <span class="txtContentTitle">Create Necessary Tables </span>
        <br /><br />
        Please edit this message for create necessary tables script.
        <br /><br />
        <?php
        $sql="";
        $objPackage->execQuery($sql);
        ?>
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=2';">
        <?php
      break;
      case "2":
        ?>
        <span class="txtContentTitle">Insert Module & Menu </span>
        <br /><br />
        Please edit this message for insert module and menu information script.
        <br /><br />
        <?php
        /* insert module */
        $objPackage->setupModule($cus_modname);
        /* insert menu */
        //$objPackage->setupMenu($cus_modname,$cus_title);
        /* insert privilege */
        $objPackage->setupPrivilege("a");
        ?>
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>';">
        <?php
      break;
      default:
        ?>
        <span class="txtContentTitle">Install explorer Module</span>
        <br /><br />
        Please edit this message for custom module installation script.
        <br /><br />
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=2';">
        <?php
    }

?>
