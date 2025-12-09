<?
include_once("include/lanai/class.system.php");
$sys_lanai = new Systems();
if (isset($_REQUEST['vertexlogin']) && $_REQUEST['vertexlogin'] == "1") {
    // do login script
    $uxid = $sys_lanai->getUserAuthentication($_REQUEST['txtLogin'], $_REQUEST['txtPassword']);
    if ($uxid > 0) {
        $_SESSION['uid'] = $uxid;
        $sys_lanai->goBack(1);
    } else {
        $sys_lanai->getErrorAlertBox("Cannot login, please verify your login and password!");
        $sys_lanai->goBack(1);
    }
} else {
    if (empty($_SESSION['uid']) || $_SESSION['uid'] <= 0) {
        // show login form
        ?>
        <div class="login-form">
            <form id="form" name="form1" method="post" action="">
                <div class="d-flex align-items-center justify-content-end flex-wrap text-white">
                   <span class="me-3"> Login to the system. Not member yet ? <a
                               class="text-white" href="module.php?modname=member&amp;mf=memsignup">Signup</a> today.</span>
                    <div class="d-flex">
                        <input name="txtLogin" type="text" class="form-control me-2 bg-secondary text-light border-0" size="15" placeholder="Login" />
                        <input name="txtPassword" type="password" class="form-control me-2 bg-secondary text-light border-0" size="15" placeholder="Password" />
                        <input type="hidden" name="vertexlogin" value="1" />
                        <button type="submit" class="btn btn-light">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
            </form>

        </div>

        <?
    } else {
        // show user info
        include_once("modules/member/module.php");
        $mem_lanai = new User();
        $mem = $mem_lanai->getUser($_SESSION['uid']);
        ?>
        <div class="user-info d-flex justify-content-between align-items-center text-white">
        <div>
            <i class="fas fa-user-circle me-2"></i>
            <span> <?
                ?>Welcome, <?= $mem->fields['userFname'] . " " . $mem->fields['userLname']; ?>.</span>
        </div>
        <div>
        <?
        if ($mem_lanai->getUserPrivilege($_SESSION['uid']) == "a") {
            ?>&nbsp;&nbsp;Now you can <a  class="btn btn-light btn-sm me-2"
                                          href="<?= "setting.php?modname=setting"; ?>">  <i class="fas fa-cog me-1"></i>setting</a> your site or <a class="btn btn-outline-light btn-sm"
                                                                                                                                                    href="<?= "module.php?modname=member&mf=memlogout"; ?>"><i class="fas fa-sign-out-alt me-1"></i>Signout</a>.<?
        }
        ?>

        <?
    }
}
?>