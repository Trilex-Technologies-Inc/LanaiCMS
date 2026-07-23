<?php
if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$member = new User();
$userLogin = isset($_REQUEST['u']) ? $_REQUEST['u'] : '';
$passwordHash = isset($_REQUEST['p']) ? $_REQUEST['p'] : '';

$rs = false;
if ($userLogin != '' && $passwordHash != '') {
    $rs = $member->getUserActivate($userLogin, $passwordHash);
}

if ($rs && $rs->recordcount() > 0) {
    if ($rs->fields['userActive'] == 'y') {
        ?>
        <div class="alert alert-info d-flex align-items-center gap-2">
            <img src="theme/<?=$cfg['theme'];?>/images/ok.gif" alt="">
            <?=_MEMBER_ALREADY_ACTIVATED;?>
        </div>
        <?php
    } else {
        $member->setUserActive($rs->fields['userId'], 'y');
        ?>
        <div class="alert alert-success d-flex align-items-center gap-2">
            <img src="theme/<?=$cfg['theme'];?>/images/ok.gif" alt="">
            <?=_MEMBER_ACTIVATE_COMPLETE;?>
        </div>
        <?php
    }
} else {
    $sys_lanai->getErrorBox(_MEMBER_CANNOT_ACTIVATE);
}
?>
