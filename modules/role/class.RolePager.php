<?php

/**
 * RolePager — admin listing of roles.
 */
class RolePager extends ADODB_Pager
{
    function __construct(&$db, $sql, $id = 'adodb', $showPageLinks = false)
    {
        parent::__construct($db, $sql, $id, $showPageLinks);
        $this->page = _PAGE;
    }

    function RenderLayout($header, $grid, $footer, $attributes = '')
    {
        echo "<table width=\"100%\"><tr><td>", "</td></tr><tr><td>",
            $grid, "</td></tr><tr><td>", $footer, " ", $header, "</td></tr></table>";
    }

    function RenderGrid()
    {
        ob_start();
        ?>
        <table cellpadding="3" cellspacing="1" width="100%">
            <tr>
                <th class="tblRowSolidTopDown" width="60%"><?=_ROLE_TITLE; ?></th>
                <th class="tblRowSolidTopDown"><?=_EDIT; ?></th>
                <th class="tblRowSolidTopDown"><?=_DELETE; ?></th>
            </tr>
            <?php
            while (!$this->rs->EOF) {
                ?>
                <tr>
                    <td class="tblRowDash"><?=htmlspecialchars($this->rs->fields['roleTitle']);?></td>
                    <td class="tblRowDash" align="center">
                        <a href="<?=$_SERVER['PHP_SELF']."?modname=role&mf=roleeditform&roleId=".$this->rs->fields['roleId'];?>">
                            <img src="theme/<?=$GLOBALS['cfg']['theme'];?>/images/edit.gif" border="0" align="absmiddle">
                        </a>
                    </td>
                    <td class="tblRowDash" align="center">
                        <?php if ($this->rs->fields['roleName'] !== 'administrator') { ?>
                        <form method="post" action="<?=$_SERVER['PHP_SELF'];?>" style="margin:0;" onsubmit="return confirm('<?=_DELETE_QUESTION;?>');">
                            <input type="hidden" name="modname" value="role">
                            <input type="hidden" name="mf" value="roleedit">
                            <input type="hidden" name="ac" value="delete">
                            <input type="hidden" name="roleId" value="<?=$this->rs->fields['roleId'];?>">
                            <?php global $sys_lanai; $sys_lanai->renderCsrfField('role'); ?>
                            <button type="submit" style="border:0;background:none;padding:0;cursor:pointer;">
                                <img src="theme/<?=$GLOBALS['cfg']['theme'];?>/images/delete.gif" border="0" align="absmiddle">
                            </button>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                $this->rs->movenext();
            }
            ?>
        </table>
        <?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }
}

?>
