<?php

/**
 * CTypePager — admin listing of content types.
 */
class CTypePager extends ADODB_Pager
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
        <script language="javascript" type="text/javascript">
            function selectall(obj) {
                var checkBoxes = document.getElementsByTagName('input');
                for (i = 0; i < checkBoxes.length; i++) {
                    if (obj.checked == true) { checkBoxes[i].checked = true; }
                    else { checkBoxes[i].checked = false; }
                }
            }
            function prepareActiveToggle(id, value) {
                document.form.ac.value = 'active';
                document.getElementById('single-ctpId').value = id;
                document.getElementById('single-v').value = value;
                return true;
            }
        </script>
        <table cellpadding="3" cellspacing="1" width="100%">
            <form name="form" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                <input type="hidden" name="modname" value="ctype">
                <input type="hidden" name="mf" value="typeedit">
                <input type="hidden" name="ac" value="">
                <input type="hidden" name="ctpId" id="single-ctpId" value="">
                <input type="hidden" name="v" id="single-v" value="">
                <?php global $sys_lanai; $sys_lanai->renderCsrfField('ctype'); ?>
                <tr>
                    <th class="tblRowSolidTopDown" align="center"><input type="checkbox" onclick="selectall(this);" class="radioButton" /></th>
                    <th class="tblRowSolidTopDown" width="60%"><?=_CTYPE_TITLE; ?></th>
                    <th class="tblRowSolidTopDown"><?=_CTYPE_FIELDS; ?></th>
                    <th class="tblRowSolidTopDown"><?=_ACTIVE; ?></th>
                    <th class="tblRowSolidTopDown"><?=_EDIT; ?></th>
                </tr>
                <?php
                while (!$this->rs->EOF) {
                    ?>
                    <tr>
                        <td class="tblRowDash" align="center">
                            <input type="checkbox" name="ctpId[]" value="<?=$this->rs->fields['ctpId'];?>" class="radioButton" />
                        </td>
                        <td class="tblRowDash">
                            <?=htmlspecialchars($this->rs->fields['ctpTitle']);?>
                            <div style="font-size:11px;color:#888;">/<?=htmlspecialchars($this->rs->fields['ctpSlug']);?></div>
                        </td>
                        <td class="tblRowDash" align="center">
                            <a href="<?=$_SERVER['PHP_SELF']."?modname=ctype&mf=fields&ctpId=".$this->rs->fields['ctpId'];?>"><?=_CTYPE_MANAGE_FIELDS;?></a>
                        </td>
                        <td class="tblRowDash" align="center">
                            <?php if ($this->rs->fields['ctpActive'] == 'y') { ?>
                                <button type="submit" onclick="return prepareActiveToggle('<?=$this->rs->fields['ctpId'];?>','n');" style="border:0;background:none;padding:0;cursor:pointer;">
                                    <img src="theme/<?=$GLOBALS['cfg']['theme'];?>/images/ok.gif" border="0" align="absmiddle">
                                </button>
                            <?php } else { ?>
                                <button type="submit" onclick="return prepareActiveToggle('<?=$this->rs->fields['ctpId'];?>','y');" style="border:0;background:none;padding:0;cursor:pointer;">
                                    <img src="theme/<?=$GLOBALS['cfg']['theme'];?>/images/cancel.gif" border="0" align="absmiddle">
                                </button>
                            <?php } ?>
                        </td>
                        <td class="tblRowDash" align="center">
                            <a href="<?=$_SERVER['PHP_SELF']."?modname=ctype&mf=typeeditform&ctpId=".$this->rs->fields['ctpId'];?>">
                                <img src="theme/<?=$GLOBALS['cfg']['theme'];?>/images/edit.gif" border="0" align="absmiddle">
                            </a>
                        </td>
                    </tr>
                    <?php
                    $this->rs->movenext();
                }
                ?>
            </form>
        </table>
        <?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }
}

?>
