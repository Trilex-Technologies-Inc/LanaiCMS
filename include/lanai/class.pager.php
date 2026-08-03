<?php

class Pager
{

    /* define value */
    var $offset = 20;
    var $rs;
    var $page;
    var $maxrow;
    var $abspage;
    var $currpage;
    var $lastStr = "Last &gt;|";
    var $firstStr = "|&lt; Frist";
    var $prevStr = "&lt; Previouse";
    var $nextStr = "Next &gt;";
    var $recStr = "Record : ";
    var $pageStr = "Page : ";
    var $title = array();
    var $link;

    function __construct(&$db, $sql, $offset)
    {

        $this->offset = $offset;
        /* check page */
        if ((empty($_GET['page']) or ($_GET['page'] == 0))) {
            $this->page = 0;
        } else {
            $this->page = (($_GET['page'] * $this->offset) - $this->offset);
        }
        /* execute sql */
        $this->rs = $db->execute($sql . " LIMIT $this->page,$this->offset");
        /* calculate max row */
        $arr = $db->execute($sql);
        if ($this->rs === false || $arr === false) {
            error_log('Pager query failed: ' . $db->ErrorMsg());
            $this->rs = $db->execute("SELECT NULL AS empty_result WHERE 1=0");
            $this->maxrow = 0;
        } else {
            $this->maxrow = $arr->recordcount();
        }
        /* calculate absulut page */
        $this->abspage = (ceil($this->maxrow / $this->offset));
        /* get current page */
        if (isset($_GET['page']))
            $this->currpage = $_GET['page'];
    }

    /* reder page  */
    function renderGrid()
    {
        ob_start();
        while (!$this->rs->EOF) {
            ?>
            <tr class="dataRow"><?php
            for ($i = 0; $i < ($this->rs->FieldCount()); $i++) {
                ?>
                <td class="dataColumn"><?= $this->rs->fields[$i]; ?></td><?php
            }
            ?></tr><?php
            $this->rs->movenext();
        }
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* render grid header */
    function renderGridHeader()
    {
        ob_start();
        ?>
        <table class="dataTable" width="100%">
        <tr class="dataRowHeader">
            <?php
            /* load table column name */
            if (empty($this->title)) {
                $fcnt = $this->rs->FieldCount();
                for ($i = 0; $i < $fcnt; $i++) {
                    $field = $this->rs->FetchField($i);
                    ?>
                    <td class="dataColumnHeader"><?= $field->name; ?></td><?php
                }
            } else {
                /* load define title */
                foreach (($this->title) as $item) {
                    ?>
                    <td class="dataColumnHeader"><?= $item; ?></td><?php
                }
            }
            ?>
        </tr>
        <?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* render grid footer */
    function renderGridFooter()
    {
        ob_start();
        ?></table><?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* render */
    function renderPage()
    {
        ?>
        <table cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td>
                    <?= $this->renderGridHeader(); ?>
                    <?= $this->renderGrid(); ?>
                    <?= $this->renderGridFooter(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="dataNavTable">
                        <tr class="dataNavRow">
                            <td class="dataNavColumn"><?= $this->showPageNumber(); ?></td>
                            <td class="dataNavColumn"><?= $this->renderNavigator(); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php
    }

    /* reder navigator link */
    function renderNavigator()
    {
        if (empty($this->link)) $this->link = $_SERVER["PHP_SELF"] . "?";
        ob_start();
        if ($this->currpage == 0) $this->currpage = 1;
        if (($this->currpage == 1) and ($this->abspage == 1)) {
        } else
            if (($this->currpage == 1) and ($this->abspage > 0)) {
                ?><?= $this->linkNext(); ?><?php
                ?><?= $this->linkLast(); ?><?php
            } else if ($this->abspage == $this->currpage) {
                ?><?= $this->linkFirst(); ?><?php
                ?><?= $this->linkPrevious(); ?><?php
            } else if (($this->currpage < $this->abspage) and ($this->currpage > 1)) {
                ?><?= $this->linkFirst(); ?><?php
                ?><?= $this->linkPrevious(); ?><?php
                ?><?= $this->linkNext(); ?><?php
                ?><?= $this->linkLast(); ?><?php
            }

        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* next link */
    function linkNext()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= ($this->currpage + 1); ?>" ><?= $this->nextStr; ?></a>&nbsp;<?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* last link */
    function linkLast()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= $this->abspage; ?>"><?= $this->lastStr; ?></a><?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* first link */
    function linkFirst()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=1"><?= $this->firstStr; ?></a>&nbsp;<?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* previous link */
    function linkPrevious()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= ($this->currpage - 1); ?>" ><?= $this->prevStr; ?></a>&nbsp;<?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* page number */
    function showPageNumber()
    {
        ob_start();
        if (empty($this->currpage)) $this->currpage = 1;
        if (empty($this->abspage)) $this->abspage = 1;
        ?>
        <?php
        if (($this->currpage > 1) and ($this->abspage != 1)) {
            ?>
            <?= $this->pageStr; ?><?= $this->currpage; ?>/<?= $this->abspage; ?>
            <?php
        }
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* record number */
    function showRecordNumber()
    {
        ob_start();
        ?><?= $this->recStr; ?><?= ($this->page + 1); ?> -
        <?php
        if (($this->abspage == $this->currpage) and (($this->page + $this->offset) > $this->maxrow)) {
            ?><?= ($this->maxrow); ?><?php
        } else {
            ?><?= ($this->page + $this->offset); ?><?php
        }
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

} // class

?>
