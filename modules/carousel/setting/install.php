<?

    if ( !eregi( "setting.php", $_SERVER['PHP_SELF'] ) ) {
       die ( "You can't access this file directly..." );
    }

    $module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );

    /* load class package */
    include_once("include/lanai/class.package.php");

    /* initial object */
    $objPackage=new Package();
    $cus_modname="carousel";
    $cus_title="carousel";

    /* case steps */
    switch ($_REQUEST['step']) {
      case "1":
        /* create necessary tables */
        ?>
        <span class="txtContentTitle">Create Necessary Tables </span>
        <br /><br />
        This step adds the carousel position field if it does not already exist.
        <br /><br />
        <?php
        global $db;
        $columns = $db->MetaColumns("tbl_ln_banner");
        if (!isset($columns['BANPOSITION'])) {
            $sql="ALTER TABLE tbl_ln_banner ADD banPosition ENUM('l','r','c','t','b') NOT NULL DEFAULT 'l' AFTER banURL";
            $objPackage->execQuery($sql);
        } else {
            $sql="ALTER TABLE tbl_ln_banner MODIFY banPosition ENUM('l','r','c','t','b') NOT NULL DEFAULT 'l'";
            $objPackage->execQuery($sql);
        }
        ?>
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=2';">
        <?
      break;
      case "2":
        ?>
        <span class="txtContentTitle">Insert Module & Menu </span>
        <br /><br />
        Please edit this message for insert module and menu information script.
        <br /><br />
        <?
        /* insert module */
        $objPackage->setupModule($cus_modname);
        /* insert menu */
        //$objPackage->setupMenu($cus_modname,$cus_title);
        /* insert privilege */
        $objPackage->setupPrivilege("a");
        ?>
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>';">
        <?
      break;
      default:
        ?>
        <span class="txtContentTitle">Install banner Module</span>
        <br /><br />
        Install the carousel module and add support for banner position ordering.
        <br /><br />
        <!-- form button -->
        <input type="button" class="inputButton" value="Next ->" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name; ?>&mf=install&step=1';">
        <?
    }

?>
