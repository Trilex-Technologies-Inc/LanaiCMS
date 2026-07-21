<?php
	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
		
	$blc_lanai = new Block();
?>
<span class="txtContentTitle"><?=_BLOCK_SETTING; ?></span><br/><br/>
<?=_BLOCK_NEW_INSTRUCTION; ?><br/><br/>

<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 

<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a><br><br>

<table>
<form name="form" method="post" action="<?=$_SERVER['PHP_SELF']; ?>" ENCTYPE="multipart/form-data">	
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mf" value="blcedit">
	<input type="hidden" name="ac" value="new">	
	<input type="hidden" name="m" value="<?=$_REQUEST['m']?>">

<?php
	switch($_REQUEST['m']){
		case 'c': 
			// content type
?>
	<tr>
		<td><?=_BLOCK_TITLE; ?></td>
		<td><input type="text" name="blcTitle" size="50">*</td>
	</tr>
	<tr>
		<td><?=_BLOCK_POSITION; ?></td>
		<td>
		<select name="blcPosition">
			<option value="l"><?=_LEFT; ?></option>
			<option value="r"><?=_RIGHT; ?></option>
			<option value="c"><?=_CENTER; ?></option>
			<option value="t"><?=_TOP; ?></option>
			<option value="b"><?=_BOTTOM; ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td valign="top"><?=_BLOCK_CONTENT; ?></td>
		<td>
			<!-- TinyMCE textarea -->
			<textarea id="blcContent" name="blcContent" rows="20" cols="80">This is some <strong>sample text</strong>.</textarea>

            <script src="include/tinymce/js/tinymce/tinymce.min.js"></script>

            <script>
			tinymce.init({
				selector: '#blcContent',
                license_key: 'gpl',
				height: 500,
				menubar: true,
				plugins: "print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
				toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
				paste_as_text: false, 
				valid_elements: '*[*]', 
				extended_valid_elements: '*[*]',
				verify_html: false,
				cleanup: false,
				toolbar_mode: 'sliding',
				content_css: false
			});
			</script>
		</td>
	</tr>

<?php
			break;
		case 'b': 
			// upload file
?>
	<tr>
		<td><?=_BLOCK_TITLE; ?></td>
		<td><input type="text" name="blcTitle" size="30">*</td>
	</tr>
	<tr>
		<td><?=_BLOCK_POSITION; ?></td>
		<td>
		<select name="blcPosition">
			<option value="l"><?=_LEFT; ?></option>
			<option value="r"><?=_RIGHT; ?></option>
			<option value="c"><?=_CENTER; ?></option>
			<option value="t"><?=_TOP; ?></option>
			<option value="b"><?=_BOTTOM; ?></option>
		</select>
		</td>
	</tr>	
	<tr>
		<td><?=_BLOCK_FILE; ?></td>
		<td><input type="file" name="userfile" size="50"></td>
	</tr>

<?php
			break;
		case 'r':
			// rss/url
?>
	<tr>
		<td><?=_BLOCK_TITLE; ?></td>
		<td><input type="text" name="blcTitle" size="30">*</td>
	</tr>
	<tr>
		<td><?=_BLOCK_POSITION; ?></td>
		<td>
		<select name="blcPosition">
			<option value="l"><?=_LEFT; ?></option>
			<option value="r"><?=_RIGHT; ?></option>
			<option value="c"><?=_CENTER; ?></option>
			<option value="t"><?=_TOP; ?></option>
			<option value="b"><?=_BOTTOM; ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td><?=_BLOCK_URL; ?></td>
		<td><input type="text" name="blcRssUrl" size="50">*</td>
	</tr>
	<tr>
		<td><?=_BLOCK_REFRESH; ?></td>
		<td><input type="text" name="blcRssRefesh" size="5" value="600"></td>
	</tr>

<?php
			break;
		case 'p': 
			// block path
?>
	<tr>
		<td><?=_BLOCK_TITLE; ?></td>
		<td><input type="text" name="blcTitle" size="30">*</td>
	</tr>
	<tr>
		<td><?=_BLOCK_POSITION; ?></td>
		<td>
		<select name="blcPosition">
			<option value="l"><?=_LEFT; ?></option>
			<option value="r"><?=_RIGHT; ?></option>
			<option value="c"><?=_CENTER; ?></option>
			<option value="t"><?=_TOP; ?></option>
			<option value="b"><?=_BOTTOM; ?></option>
		</select>
		</td>
	</tr>	
	<tr>
		<td><?=_BLOCK_EXTRACTED_PATH; ?></td>
		<td><input type="text" name="blcPath" size="20"></td>
	</tr>

<?php
			break;
	} // switch
?>	
</table>
</form>
