<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$lanai_rss=new LRSSThai();
	?><span class="txtContentTitle"><?=_RSS_SETTING; ?></span><br/><br/>
	<?=_RSS_NEW_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
	<br><br>
	
	<table border="0">
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="rssedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="ac" value="new">
	<tr>
		<td width="100"><?=_RSS_TITLE; ?></td>
		<td><input name="rssTitle" type="text" size="40">*</td>
	</tr>
	<tr>
	  <td><?=_RSS_URL; ?></td>
	  <td><input name="rssURL" type="text" size="40">*</td>
	  </tr>
	<tr>
	  <td><?=_RSS_RELOAD; ?></td>
	  <td><input name="rssReload" type="text" id="rssReload" value="3600" size="10" maxlength="10"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_VIEWAS; ?></td>
	  <td><select name="rssView" id="rssView">
	    <option value="list" selected><?=_LIST; ?></option>
	    <option value="column"><?=_COLUMN; ?></option>
	    <option value="horz"><?=_HORIZONTAL; ?></option>
      </select></td>
	  </tr>
	<tr>
	  <td><?=_RSS_ITEMCOUNT; ?></td>
	  <td><input name="rssItemCount" type="text" id="rssItemCount" size="5" value="10"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_SHOWDES; ?></td>
	  <td><select name="rssShowDescription" id="rssShowDescription">
	    <option value="y" selected><?=_YES; ?></option>
	    <option value="n"><?=_NO; ?></option>
      </select></td>
	  </tr>
	<tr>
	  <td><?=_RSS_NUMCOL; ?></td>
	  <td><input name="rssNumColumn" type="text" id="rssNumColumn" size="5"  value="1"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_NUMIMAGE; ?></td>
	  <td><input name="rssNumImage" type="text" id="rssNumImage" size="5" value="10"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_FIXEDIMAGE; ?></td>
	  <td><input name="rssFixedImage" type="text" id="rssFixedImage" size="40"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_ALTERIMAGE; ?></td>
	  <td><input name="rssAlterImage" type="text" id="rssAlterImage" size="40"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEWIDTH ; ?></td>
	  <td><input name="rssImageWidth" type="text" id="rssImageWidth" value="75" size="5"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEHEIGHT; ?></td>
	  <td><input name="rssImageHeight" type="text" id="rssImageHeight" value="79" size="5"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEALIGN; ?></td>
	  <td><select name="rssImageAlign" id="rssImageAlign">
	    <option value="left" selected><?=_LEFT; ?></option>
	    <option value="center"><?=_CENTER; ?></option>
	    <option value="right"><?=_RIGHT; ?></option>
	    <option value="absmiddle"><?=_ABSMIDDLE; ?></option>
      </select></td>
	  </tr>
	<tr>
	  <td><?=_RSS_TARGET; ?></td>
	  <td><select name="rssTarget" id="rssTarget">
	    <option value="" selected><?=_NONE; ?></option>
	    <option value="_blank">_blank</option>
	    <option value="_parent">_parent</option>
	    <option value="_self">_self</option>
	    <option value="_top">_top</option>
      </select></td>
	</tr>
	</form>
	</table>
