<?
 
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
			die ("You can't access this file directly...");
	}
	
	$module_name = basename(dirname(__FILE__));
	$modfunction="modules/$module_name/module.php";
	include_once($modfunction);
	
	$content = new Content();
	/// settype
	settype($_REQUEST['cid'],"integer");
	$rs=$content->getContentById($_REQUEST['cid']);
	if (($rs->recordcount())>0) {
			
?>
<?=$sys_lanai->setPageTitle($rs->fields['conTitle']); ?>


<?
	} else {
		$sys_lanai->getErrorBox(_CONTENT_NOT_FOUND);
	}
?>
<section class="article-hero">
    <div class="container">
        <h1 class="display-5 fw-bold"><?=$rs->fields['conTitle'] ?></h1>
        <div class="article-meta mt-2">
            <i class="fas fa-calendar-alt"></i> October 25, 2025 &nbsp;|&nbsp;
            <i class="fas fa-user"></i> By Admin &nbsp;|&nbsp;
            <i class="fas fa-folder"></i> Tech
        </div>
    </div>
</section>

<!-- Main Article Content -->
<div class="container my-5">
    <div class="row g-5">

        <!-- Article -->
        <div class="col-md-12">
            <div class="article-content bg-white p-4 rounded shadow-sm">
                <?=$rs->fields['conBody1']?>
                <?=$rs->fields['conBody2']?>

            </div>
        </div>

       
    </div>
</div>
