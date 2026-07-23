<?php
if (stripos($_SERVER['PHP_SELF'], "module.php") === false) {
    die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
$modfunction = "modules/$module_name/module.php";
include_once($modfunction);

$news = new News();

// Get grouped news
$rsc = $news->getGroupList();
?>


<div class="container my-4">
<?php if (($rsc->RecordCount()) > 1) { 
    $rs = $news->getShowGroup(); ?>
    
    <h4 class="text-primary mb-3"><?=_NWS_GROUP; ?></h4>
    
    <ul class="list-group">
    <?php while(!$rs->EOF){ 
        $link = $sys_lanai->getSEOLink($_SERVER['PHP_SELF']."?modname=".$module_name."&mf=nwsviewgroup&mid=".$rs->fields['chnId']); ?>
        
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <img src="theme/<?=$news->cfg['theme'];?>/images/file.gif" alt="" class="me-2" style="vertical-align:middle;">
                <a href="<?=$link; ?>" class="text-decoration-none"><?=$rs->fields['chnTitle']; ?></a>
            </div>
            <span class="badge bg-primary rounded-pill"><?=$news->getNumGroup($rs->fields['chnId']); ?></span>
        </li>
        
    <?php $rs->MoveNext(); } ?>
    </ul>

<?php } else { ?>

    <h4 class="text-primary mb-3"><?=_NWS_LIST; ?></h4>
    <div>
        <?php $news->getNewsListPager(); ?>
    </div>

<?php } ?>
</div>
