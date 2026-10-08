<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$content=new Content();
	try { $contentSchemaReady = $content->ensureEditorSchema(); } catch (Throwable $error) { $contentSchemaReady = false; }
	$contentFilter = (($_GET['filter'] ?? '') === 'pending' && $contentSchemaReady) ? 'pending' : '';
	$contentCanPublish = $sys_lanai->userHasCapability('publish_content');
	$contentPendingCount = ($contentCanPublish && $contentSchemaReady) ? $content->countPending() : 0;
	?><span class="txtContentTitle"><?=_CONTENT_SETTING; ?></span><br/><br/>
	<?=_CONTENT_SETTING_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/new.gif" border="0" align="absmiddle"/>
	<a href="<?=$_SERVER['PHP_SELF']?>?modname=<?=$module_name?>&mf=connew" ><?=_NEW; ?></a>&nbsp;&nbsp;
	
	<?php if ($contentCanPublish) { ?>
	<img src="theme/<?=$cfg['theme']; ?>/images/ok.gif" border="0" align="absmiddle"/>
	<button type="button" onclick="chk_mactive();"><?=_ACTIVE; ?></button>&nbsp;&nbsp;
	<?php } ?>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/delete.gif" border="0" align="absmiddle"/>
	<button type="button" onclick="chk_mdelete();"><?=_DELETE; ?></button>&nbsp;&nbsp;
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="module.php?modname=setting" ><?=_BACK; ?></a>
	<br><br>
	<?php if ($contentPendingCount > 0 || $contentFilter === 'pending') { ?>
	<p class="alert alert-warning">
		<?=(int)$contentPendingCount; ?> page(s) waiting for review.
		<?php if ($contentFilter === 'pending') { ?><a href="setting.php?modname=content">Show all content</a><?php } else { ?><a href="setting.php?modname=content&amp;filter=pending">Show pages to review</a><?php } ?>
	</p>
	<?php } ?>
	<script language="javascript">
	<!--
		function chk_mdelete() {
			if (confirm("<?=_DELETE_QUESTION; ?>")){
				document.getElementById("content-list-form").ac.value="mdelete";
				document.getElementById("content-list-form").requestSubmit();
			}
		}
		function chk_mactive() {
			document.getElementById("content-list-form").ac.value="mactive";
			document.getElementById("content-list-form").requestSubmit();
		}
	//-->
	</script>
<?php
	$content->getContentList(30, $contentFilter);
	
?>