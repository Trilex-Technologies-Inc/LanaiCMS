<?php

if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(substr(__FILE__, 0, strlen(dirname(__FILE__)))));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);
include_once("modules/member/module.php");

$apiToken = new ApiToken();
$member = new User();

$newPlainToken = isset($_SESSION['apitoken_new_plain']) ? $_SESSION['apitoken_new_plain'] : '';
unset($_SESSION['apitoken_new_plain']);
?>
<span class="txtContentTitle"><?=_APITOKEN_SETTING; ?></span><br/><br/>
<?=_APITOKEN_SETTING_INSTRUCTION; ?><br/><br/>

<?php if ($newPlainToken !== '') { ?>
    <div class="alert alert-success">
        <?=_APITOKEN_NEW_TOKEN; ?><br>
        <code><?=htmlspecialchars($newPlainToken);?></code><br>
        <small><?=_APITOKEN_COPY_NOW; ?></small>
    </div>
<?php } ?>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>" class="mb-4">
    <input type="hidden" name="modname" value="<?=$module_name; ?>">
    <input type="hidden" name="mf" value="apitokenedit">
    <input type="hidden" name="ac" value="generate">
    <?php $sys_lanai->renderCsrfField('apitoken'); ?>
    <div class="d-flex gap-2 align-items-center">
        <select name="userId" class="form-select" style="max-width:250px;">
            <?php
            $rsu = $member->getUser(0);
            while (!$rsu->EOF) {
                ?>
                <option value="<?=$rsu->fields['userId'];?>"><?=htmlspecialchars($rsu->fields['userLogin']);?></option>
                <?php
                $rsu->movenext();
            }
            ?>
        </select>
        <input type="text" name="label" class="form-control" placeholder="<?=_APITOKEN_LABEL;?>" style="max-width:250px;">
        <button type="submit" class="btn btn-primary"><?=_APITOKEN_GENERATE;?></button>
    </div>
</form>

<table class="table">
    <tr>
        <th><?=_APITOKEN_USER;?></th>
        <th><?=_APITOKEN_LABEL;?></th>
        <th><?=_APITOKEN_CREATED;?></th>
        <th><?=_APITOKEN_LAST_USED;?></th>
        <th></th>
    </tr>
    <?php
    $rst = $apiToken->getTokens();
    while (!$rst->EOF) {
        ?>
        <tr>
            <td><?=htmlspecialchars($rst->fields['userLogin']);?></td>
            <td><?=htmlspecialchars($rst->fields['label']);?></td>
            <td><?=htmlspecialchars($rst->fields['createdAt']);?></td>
            <td><?=htmlspecialchars($rst->fields['lastUsedAt']);?></td>
            <td>
                <form method="post" action="<?=$_SERVER['PHP_SELF'];?>" onsubmit="return confirm('<?=_DELETE_QUESTION;?>');">
                    <input type="hidden" name="modname" value="<?=$module_name;?>">
                    <input type="hidden" name="mf" value="apitokenedit">
                    <input type="hidden" name="ac" value="revoke">
                    <input type="hidden" name="tokenId" value="<?=$rst->fields['tokenId'];?>">
                    <?php $sys_lanai->renderCsrfField('apitoken'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger"><?=_APITOKEN_REVOKE;?></button>
                </form>
            </td>
        </tr>
        <?php
        $rst->movenext();
    }
    ?>
</table>
