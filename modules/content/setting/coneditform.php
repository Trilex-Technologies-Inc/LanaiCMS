<?

	if ( !eregi( "setting.php", $_SERVER['PHP_SELF'] ) ) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	$content = new Content();
	$rs = $content->getContentById($_REQUEST['mid']);
?>

<script src="include/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
	selector: 'textarea.tinymce',
	height: 500,
                    menubar: true,
  license_key: 'gpl',
                     plugins:  " preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",

    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                    paste_as_text: false, // allow full HTML
                    valid_elements: '*[*]', // allow any tag and attribute
                    extended_valid_elements: '*[*]',
                    verify_html: false,
                    cleanup: false,
                    height: 400,
                    code_dialog_height: 500,
                    code_dialog_width: 800,
                    toolbar_mode: 'sliding',
                    setup: function(editor) {
                        editor.on('PastePreProcess', function(e) {
                            // allow raw HTML paste
                            e.content = e.content;
                        });
                    },
                    content_css: false // keep user CSS classes intact
                });
</script>
</script>

<span class="txtContentTitle"><?=_CONTENT_SETTING; ?></span><br/><br/>
<?=_CONTENT_EDIT_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
<br><br>

<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="conedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mid" value="<?=$_REQUEST['mid']; ?>">
	<input type="hidden" name="ac" value="edit">

	<table cellpadding="3" cellspacing="1">
		<tr>
			<td><?=_CONTENT_TITLE; ?></td>
			<td width="100%">
				<input type="text" name="conTitle" size="40" value="<?=$rs->fields['conTitle']; ?>">*
			</td>	
		</tr>

		<tr>
			<td></td>
			<td>
				<textarea name="conBody1" class="tinymce"><?=htmlspecialchars($rs->fields['conBody1']); ?></textarea> *
			</td>	
		</tr>

		<tr>
			<td></td>
			<td>
				<textarea name="conBody2" class="tinymce"><?=htmlspecialchars($rs->fields['conBody2']); ?></textarea>
			</td>	
		</tr>
	</table>
</form>
