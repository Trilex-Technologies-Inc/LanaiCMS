<?

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

    function Pager(&$db, $sql, $offset)
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
        $this->maxrow = $arr->recordcount();
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
            <tr class="dataRow"><?
            for ($i = 0; $i < ($this->rs->FieldCount()); $i++) {
                ?>
                <td class="dataColumn"><?= $this->rs->fields[$i]; ?></td><?
            }
            ?></tr><?
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
            <?
            /* load table column name */
            if (empty($this->title)) {
                $fcnt = $this->rs->FieldCount();
                for ($i = 0; $i < $fcnt; $i++) {
                    $field = $this->rs->FetchField($i);
                    ?>
                    <td class="dataColumnHeader"><?= $field->name; ?></td><?
                }
            } else {
                /* load define title */
                foreach (($this->title) as $item) {
                    ?>
                    <td class="dataColumnHeader"><?= $item; ?></td><?
                }
            }
            ?>
        </tr>
        <?
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* render grid footer */
    function renderGridFooter()
    {
        ob_start();
        ?></table><?
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
        <?
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
                ?><?= $this->linkNext(); ?><?
                ?><?= $this->linkLast(); ?><?
            } else if ($this->abspage == $this->currpage) {
                ?><?= $this->linkFirst(); ?><?
                ?><?= $this->linkPrevious(); ?><?
            } else if (($this->currpage < $this->abspage) and ($this->currpage > 1)) {
                ?><?= $this->linkFirst(); ?><?
                ?><?= $this->linkPrevious(); ?><?
                ?><?= $this->linkNext(); ?><?
                ?><?= $this->linkLast(); ?><?
            }

        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* next link */
    function linkNext()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= ($this->currpage + 1); ?>" ><?= $this->nextStr; ?></a>&nbsp;<?
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* last link */
    function linkLast()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= $this->abspage; ?>"><?= $this->lastStr; ?></a><?
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* first link */
    function linkFirst()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=1"><?= $this->firstStr; ?></a>&nbsp;<?
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /* previous link */
    function linkPrevious()
    {
        ob_start();
        ?><a href="<?= $this->link; ?>page=<?= ($this->currpage - 1); ?>" ><?= $this->prevStr; ?></a>&nbsp;<?
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
        <?
        if (($this->currpage > 1) and ($this->abspag != 1)) {
            ?>
            <?= $this->pageStr; ?><?= $this->currpage; ?>/<?= $this->abspage; ?>
            <?
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
        if (($this->abspage == $this->currpage) and (($this->page + $this->offset) > $maxrow)) {
            ?><?= ($this->maxrow); ?><?
        } else {
            ?><?= ($this->page + $this->offset); ?><?
        }
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

} // class

?>
