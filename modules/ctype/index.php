<?php

if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$ctype = new ContentType();

$ctpSlug = isset($_REQUEST['ctp']) && !is_array($_REQUEST['ctp']) ? trim($_REQUEST['ctp']) : '';
$itemSlug = isset($_REQUEST['item']) && !is_array($_REQUEST['item']) ? trim($_REQUEST['item']) : '';

$typeRs = $ctype->getTypeBySlug($ctpSlug);
if ($typeRs->recordcount() < 1) {
    $sys_lanai->getErrorBox(_CTYPE_NOT_FOUND);
    return;
}
$ctpId = $typeRs->fields['ctpId'];

if ($itemSlug !== '') {
    // detail view
    $itemRs = $ctype->getItemBySlug($ctpId, $itemSlug);
    if ($itemRs->recordcount() < 1) {
        $sys_lanai->getErrorBox(_CTYPE_ITEM_NOT_FOUND);
        return;
    }
    ?>
    <?=$sys_lanai->setPageTitle($itemRs->fields['citTitle']);?>
    <section class="article-hero">
        <div class="container">
            <h1 class="display-5 fw-bold"><?=htmlspecialchars($itemRs->fields['citTitle']);?></h1>
        </div>
    </section>
    <div class="container my-5">
        <div class="article-content bg-white p-4 rounded shadow-sm">
            <table class="table">
                <?php
                $rsv = $ctype->getItemValues($itemRs->fields['citId']);
                while (!$rsv->EOF) {
                    ?>
                    <tr>
                        <th><?=htmlspecialchars($rsv->fields['cfdLabel']);?></th>
                        <td>
                            <?php if ($rsv->fields['cfdType'] === 'richtext') { ?>
                                <?=$rsv->fields['cvalText'];?>
                            <?php } elseif ($rsv->fields['cfdType'] === 'image') { ?>
                                <?php if ((string) $rsv->fields['cvalText'] !== '') { ?>
                                    <img src="<?=htmlspecialchars($rsv->fields['cvalText']);?>" class="img-fluid rounded" style="max-height:300px;">
                                <?php } ?>
                            <?php } elseif ($rsv->fields['cfdType'] === 'file') { ?>
                                <?php if ((string) $rsv->fields['cvalText'] !== '') { ?>
                                    <a href="<?=htmlspecialchars($rsv->fields['cvalText']);?>" target="_blank"><?=htmlspecialchars(basename($rsv->fields['cvalText']));?></a>
                                <?php } ?>
                            <?php } else { ?>
                                <?=nl2br(htmlspecialchars((string) $rsv->fields['cvalText']));?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $rsv->movenext();
                }
                ?>
            </table>
        </div>
    </div>
    <?php
} else {
    // listing view
    ?>
    <?=$sys_lanai->setPageTitle($typeRs->fields['ctpTitle']);?>
    <section class="article-hero">
        <div class="container">
            <h1 class="display-5 fw-bold"><?=htmlspecialchars($typeRs->fields['ctpTitle']);?></h1>
        </div>
    </section>
    <div class="container my-5">
        <div class="row g-4">
            <?php
            $rs = $ctype->getItems($ctpId, true);
            if ($rs->recordcount() < 1) {
                ?>
                <p class="text-muted"><?=_CTYPE_NO_ITEMS;?></p>
                <?php
            }
            while (!$rs->EOF) {
                $itemLink = $sys_lanai->getSEOLink("module.php?modname=" . $module_name . "&ctp=" . urlencode($ctpSlug) . "&item=" . urlencode($rs->fields['citSlug']));
                ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?=$itemLink;?>">
                                    <?=htmlspecialchars($rs->fields['citTitle']);?>
                                </a>
                            </h5>
                        </div>
                    </div>
                </div>
                <?php
                $rs->movenext();
            }
            ?>
        </div>
    </div>
    <?php
}
?>
