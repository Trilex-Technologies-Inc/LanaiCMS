<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$role = new Role();
$roleId = intval($_REQUEST['roleId']);
$rs = $role->getRoleById($roleId);

if ($rs->recordcount() < 1) {
    $sys_lanai->getErrorBox(_ROLE_NOT_FOUND);
    return;
}

$assignedCapIds = $role->getRoleCapabilityIds($roleId);
?>
<span class="txtContentTitle"><?=_ROLE_EDIT; ?></span><br/><br/>
<?=_ROLE_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();"><?=_SAVE; ?></a>&nbsp;&nbsp;

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
<br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="mf" value="roleedit">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="roleId" value="<?=$roleId;?>">
    <input type="hidden" name="ac" value="edit">
    <?php $sys_lanai->renderCsrfField('role'); ?>
    <table cellpadding="3" cellspacing="1" border="0">
        <tr>
            <td><?=_ROLE_TITLE; ?></td>
            <td><input type="text" name="roleTitle" size="40" value="<?=htmlspecialchars($rs->fields['roleTitle']);?>">*</td>
        </tr>
        <tr>
            <td valign="top"><?=_ROLE_CAPABILITIES; ?></td>
            <td>
                <?php
                $rsc = $role->getCapabilities();
                while (!$rsc->EOF) {
                    $checked = in_array((int) $rsc->fields['capId'], $assignedCapIds, true) ? ' checked' : '';
                    ?>
                    <label style="display:block;">
                        <input type="checkbox" name="capId[]" value="<?=$rsc->fields['capId'];?>"<?=$checked;?>>
                        <?=htmlspecialchars($rsc->fields['capTitle']);?>
                    </label>
                    <?php
                    $rsc->movenext();
                }
                ?>
            </td>
        </tr>
    </table>
</form>
