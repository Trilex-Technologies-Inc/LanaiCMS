<?php

if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$_SESSION['uid'] = 0;
unset($_SESSION['uid'], $_SESSION['mfa_pending'], $_SESSION['mfa_verified'], $_SESSION['mfa_setup']);
session_regenerate_id(true);

$sys_lanai->go2Page("index.php");
?>
