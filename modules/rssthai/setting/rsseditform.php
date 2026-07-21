<?php

	if (stripos($_SERVER['PHP_SELF'], "setting.php") === false) {
	    die ( "You can't access this file directly..." );
	} 
	
	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction ); 
	
	
	$lanai_rss=new LRSSThai();
	
	$rs=$lanai_rss->getRSSById($_REQUEST['mid']);
	
	?><span class="txtContentTitle"><?=_RSS_SETTING; ?></span><br/><br/>
	<?=_RSS_EDIT_INSTRUCTION; ?><br/><br/>
	
	<img src="theme/<?=$cfg['theme']; ?>/images/save.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:document.form.submit();" ><?=_SAVE; ?></a>&nbsp;&nbsp; 
	
	<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
	<a href="#" onClick="javascript:history.back();"><?=_BACK; ?></a>
	<br><br>
	
	<table border="0">
	<form name="form" method="post"  action="<?=$_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="mf" value="rssedit">
	<input type="hidden" name="modname" value="<?=$module_name; ?>">
	<input type="hidden" name="mid" value="<?=$_REQUEST['mid']; ?>">
	<input type="hidden" name="ac" value="edit">
	<tr>
		<td width="100"><?=_RSS_TITLE; ?></td>
		<td><input name="rssTitle" type="text" size="40" value="<?=$rs->fields['rssTitle'];?>">*</td>
	</tr>
	<tr>
	  <td><?=_RSS_URL; ?></td>
	  <td><input name="rssURL" type="text" size="40" value="<?=$rs->fields['rssURL'];?>">*</td>
	  </tr>
	<tr>
	  <td><?=_RSS_RELOAD; ?></td>
	  <td><input name="rssReload" type="text" id="rssReload" value="<?=$rs->fields['rssReload'];?>" size="10" maxlength="10"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_VIEWAS; ?></td>
	  <td>
	  <?php
	  		if ($rs->fields['rssView']=="list") {
	  		    $sellist="selected";
	  		} else if ($rs->fields['rssView']=="column") {
	  		    $selcol="selected";
	  		} else if ($rs->fields['rssView']=="horz") {
	  		    $selhorz="selected";
	  		} 
	  ?>
	  <select name="rssView" id="rssView">
	    <option value="list" <?=$sellist; ?>><?=_LIST; ?></option>
	    <option value="column" <?=$selcol; ?>><?=_COLUMN; ?></option>
	    <option value="horz" <?=$selhorz; ?>><?=_HORIZONTAL; ?></option>
      </select>
	  </td>
	 </tr>
	<tr>
	  <td><?=_RSS_ITEMCOUNT; ?></td>
	  <td><input name="rssItemCount" type="text" id="rssItemCount" size="5"  value="<?=$rs->fields['rssItemCount'];?>"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_SHOWDES; ?></td>
	  <td>
	  <?php
	  		if ($rs->fields['rssShowDescription']=="y") {
	  		    $selyes="selected";
	  		} else {
	  		    $selno="selected";
	  		} 
	  ?>
	  <select name="rssShowDescription" id="rssShowDescription">
	    <option value="y"  <?=$selyes; ?>><?=_YES; ?></option>
	    <option value="n" <?=$selno; ?>><?=_NO; ?></option>
      </select>
	  </td>
	 </tr>
	<tr>
	  <td><?=_RSS_NUMCOL; ?></td>
	  <td><input name="rssNumColumn" type="text" id="rssNumColumn" size="5" value="<?=$rs->fields['rssNumColumn'];?>"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_NUMIMAGE; ?></td>
	  <td><input name="rssNumImage" type="text" id="rssNumImage" size="5" value="<?=$rs->fields['rssNumImage'];?>"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_FIXEDIMAGE; ?></td>
	  <td><input name="rssFixedImage" type="text" id="rssFixedImage" size="40"  value="<?=$rs->fields['rssFixedImage'];?>"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_ALTERIMAGE; ?></td>
	  <td><input name="rssAlterImage" type="text" id="rssAlterImage" size="40"  value="<?=$rs->fields['rssAlterImage'];?>"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEWIDTH ; ?></td>
	  <td><input name="rssImageWidth" type="text" id="rssImageWidth" value="<?=$rs->fields['rssImageWidth'];?>" size="5"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEHEIGHT; ?></td>
	  <td><input name="rssImageHeight" type="text" id="rssImageHeight" value="<?=$rs->fields['rssImageHeight'];?>" size="5"></td>
	  </tr>
	<tr>
	  <td><?=_RSS_IMAGEALIGN; ?></td>
	  <td>
	   <?php
	  		if ($rs->fields['rssImageAlign']=="left") {
	  		    $selleft="selected";
	  		} else if ($rs->fields['rssImageAlign']=="center") {
	  		    $selcenter="selected";
	  		} else if ($rs->fields['rssImageAlign']=="right") {
	  		    $selright="selected";
	  		} else {
				$selabsmiddle="selected";
			}
	  ?>
	  <select name="rssImageAlign" id="rssImageAlign">
	    <option value="left" <?=$selleft; ?>><?=_LEFT; ?></option>
	    <option value="center" <?=$selcenter; ?>><?=_CENTER; ?></option>
	    <option value="right" <?=$selright; ?>><?=_RIGHT; ?></option>
	    <option value="absmiddle" <?=$selabsmiddle; ?>><?=_ABSMIDDLE; ?></option>
      </select></td>
	  </tr>
	<tr>
	  <td><?=_RSS_TARGET; ?></td>
	  <td>
	   <?php
	  		if ($rs->fields['rssTarget']=="") {
	  		    $selnone="selected";
	  		} else if ($rs->fields['rssTarget']=="_blank") {
	  		    $selblank="selected";
	  		} else if ($rs->fields['rssTarget']=="_parent") {
	  		    $selparent="selected";
	  		} else if ($rs->fields['rssTarget']=="_self") {
	  		    $selself="selected";
	  		} else {
				$seltop="selected";
			}
	  ?>
	  <select name="rssTarget" id="rssTarget">
	    <option value="" <?=$selnone; ?>><?=_NONE; ?></option>
	    <option value="_blank" <?=$selblank; ?> >_blank</option>
	    <option value="_parent" <?=$selparent; ?>>_parent</option>
	    <option value="_self" <?=$selself; ?>>_self</option>
	    <option value="_top" <?=$seltop; ?>>_top</option>
      </select></td>
	</tr>
	</form>
	</table>
