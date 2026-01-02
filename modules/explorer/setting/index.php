<?
	if ( !eregi( "setting.php", $_SERVER['PHP_SELF'] ) ) {
	    die ( "You can't access this file directly..." );
	}

	$module_name = basename( dirname( substr( __FILE__, 0, strlen( dirname( __FILE__ ) ) ) ) );
	$modfunction = "modules/$module_name/module.php";
	include_once( $modfunction );

    $objExpl=new Explorer();

    /* load current dir */

    if (!empty($_REQUEST['dir'])){
        if (strstr($_REQUEST['dir'],".")){
            $vardir=$cfg['dir'].$sys_lanai->getPath();
            $path="";
        } else {
            $vardir=$cfg['dir'].$sys_lanai->getPath().$_REQUEST['dir'].$sys_lanai->getPath();
            $path=$_REQUEST['dir']."/";
        }
    } else {
        $vardir=$cfg['dir'].$sys_lanai->getPath();
        $path="";
    }
$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
?>
<span class="txtContentTitle"><?=_EXPLORER_SETTING; ?></span><br/><br/>
<?=_EXPLORER_INSTRUCTION; ?><br/><br/>
<img src="modules/explorer/images/db_comit.png" border="0" align="absmiddle"/>
<a href="setting.php?modname=explorer&mf=upload&dir=<?=  $dir ?>"><?=_UPLOAD; ?></a>&nbsp;&nbsp;
<img src="theme/<?=$cfg['theme']; ?>/images/back.gif" border="0" align="absmiddle"/>
<a href="module.php?modname=setting" >
<?=_CANCEL; ?></a>&nbsp;&nbsp;
<br/><br/>
<table width="90%" cellpadding="3" cellspacing="1">
<tr bgcolor="#CCCCCC">
<th width="22">&nbsp;</td>
<th width="80%"><?=_FILENAME; ?></th>
<th><?=_FILESIZE; ?></th>
<th><?=_FILETYPE; ?></th>
<th><?=_FILEPERMS; ?></th>
<th colspan="2">&nbsp;</th>
</tr>


    <?
    $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
    /* load dir */
    $dirarr=$objExpl->loadDir($vardir);
    for ($i=0;$i<count($dirarr);$i++){
    ?>
    <tr bgcolor="#EEEEEE">
     <?
        if ($dirarr[$i]['name']==".") {
            $linkpath="";
        } else if ($dirarr[$i]['name']=="..") {
            $spath=split("/",$dir);
                for ($j=0;$j<(count($spath)-1);$j++){
                   $linkpath.=$spath[$j]."/";
                }
                $linkpath=substr($linkpath,0,(strlen($linkpath)-1));
        }
        else {
            $linkpath=$path.$dirarr[$i]['name'];
        }
     ?>
     <td><a href="setting.php?modname=explorer&dir=<?=$linkpath; ?>"><?=$objExpl->getMimeIcon($vardir.$dirarr[$i]['name'],true); ?></a></td>
     <td><?=$dirarr[$i]['name']; ?></td>
     <td><?=$dirarr[$i]['size']; ?></td>
     <td align="center">dir</td>
     <td align="center"><?=$dirarr[$i]['perms']; ?></td>
     <td>&nbsp;</td>
     <td>&nbsp;</td>
    </tr>
    <?
    }

    /* load file */
    $filearr=$objExpl->loadFile($vardir);
    for ($i=0;$i<count($filearr);$i++){
    ?>
    <tr bgcolor="#DDDDDD">
     <td><?=$objExpl->getMimeIcon($vardir.$filearr[$i]['name'],0); ?></td>
     <td><?=$filearr[$i]['name']; ?></td>
     <td><?=$filearr[$i]['size']; ?></td>
     <td align="center"><?=isset($filearr[$i]['mime']) ? $filearr[$i]['mime'] : ''; ?></td>
     <td align="center"><?=$filearr[$i]['perms']; ?></td>
     <td><a href="modules/explorer/download.php?f=<?=$path.$filearr[$i]['name'];?>"><img src="modules/explorer/images/db_update.png" border="0" alt="<?=_SAVE; ?>"/></td>
     <td><a href="javascript:deletex('<?=$filearr[$i]['name'];?>')"><img src="modules/explorer/images/cnrdelete-all.png" border="0" alt="<?=_DELETE; ?>"/></a></td>
    </tr>
    <?
   }
?>
</table>
<script language="JavaScript" type="text/javascript">
    function deletex(f){
        if (confirm("<?=_DELETE_QUESTION; ?>")) {
            location.href="setting.php?modname=explorer&mf=delete&dir=<?=$dir; ?>&f="+f
        }
    }
</script>
<br/>
<div><? include_once("modules/explorer/version.txt"); ?></div>
