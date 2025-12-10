<?php
if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$mem_lanai = new User();
?>


<div class="card shadow-sm border-0 mt-3  m-auto" style="width: 330px">
  <div class="card-body">
    <?php if (!empty($_SESSION['uid']) && $_SESSION['uid'] > 0): ?>
      <div class="d-flex align-items-center mb-3">
        <?php if ($mem_lanai->isUserImageExist($_SESSION['uid'])): ?>
          <img src="<?= "datacenter/uimage/u" . $_SESSION['uid'] . ".gif"; ?>" class="rounded-circle me-3" width="96" height="96" alt="User">
        <?php else: ?>
          <img src="datacenter/uimage/u0.gif" class="rounded-circle me-3" width="96" height="96" alt="User">
        <?php endif; ?>

        <?php $mem = $mem_lanai->getUser($_SESSION['uid']); ?>
        <div>
          <h5 class="mb-1"><?= $mem->fields['userFname'] . " " . $mem->fields['userLname']; ?></h5>
          <div class="d-flex flex-wrap gap-3 mt-2">
            <a href="module.php?modname=member&mf=meminfo" class="text-decoration-none">
              <img src="theme/<?= $cfg['theme']; ?>/images/user.gif" class="me-1" alt=""> <?= _USER_INFO; ?>
            </a>

            <?php if ($mem_lanai->getUserPrivilege($_SESSION['uid']) == "a"): ?>
              <a href="setting.php?modname=setting" class="text-decoration-none">
                <img src="theme/<?= $cfg['theme']; ?>/images/setting.gif" class="me-1" alt=""> <?= _SITE_SETTING; ?>
              </a>
            <?php endif; ?>

            <a href="module.php?modname=member&mf=memlogout" class="text-decoration-none text-danger">
              <img src="theme/<?= $cfg['theme']; ?>/images/logout.gif" class="me-1" alt=""> <?= _USER_LOGOUT; ?>
            </a>
          </div>
        </div>
      </div>

    <?php else: ?>
      <h5 class="text-primary mb-4"><?= _MEMBER_LOGIN; ?></h5>

      <div class="mb-3">
        <a href="module.php?modname=member&mf=memsignup" class="me-3 text-decoration-none">
          <img src="theme/<?= $cfg['theme']; ?>/images/user2.gif" class="me-1" alt=""> <?= _USER_SIGNUP; ?>
        </a>
        <a href="module.php?modname=member&mf=memlostpass" class="text-decoration-none">
          <img src="theme/<?= $cfg['theme']; ?>/images/config.gif" class="me-1" alt=""> <?= _USER_LOST; ?>
        </a>
      </div>

      <form method="post" action="module.php" class="p-3 border rounded bg-light">
        <input type="hidden" name="modname" value="member">
        <input type="hidden" name="mf" value="memlogin">

        <div class="mb-3">
          <label class="form-label"><?= _USERNAME; ?></label>
          <input type="text" name="username" class="form-control" placeholder="<?= _USERNAME; ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= _PASSWORD; ?></label>
          <input type="password" name="password" class="form-control" placeholder="<?= _PASSWORD; ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= _MEMBER_CAPTEXT; ?></label>
          <div class="d-flex align-items-center gap-3">
            <input type="text" name="captext" class="form-control w-auto" size="12" maxlength="5">
           
          </div>
          <div class="d-flex align-items-center gap-3">
           
            <img src="images/captcha.php?hash=<?= md5(time()); ?>" alt="Captcha" class="border rounded">
          </div>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-primary"><?= _SIGNIN; ?></button>
          <button type="reset" class="btn btn-secondary"><?= _RESET; ?></button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
