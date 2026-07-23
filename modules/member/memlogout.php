<?php

if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$_SESSION['uid'] = 0;
unset($_SESSION['uid']);

$sys_lanai->go2Page("index.php");
?>
