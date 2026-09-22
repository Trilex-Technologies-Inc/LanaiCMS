<?php

/**
 * MediaPager — admin grid of uploaded media with thumbnails.
 */
class MediaPager extends ADODB_Pager
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
        <div class="row g-3">
            <?php
            while (!$this->rs->EOF) {
                $preview = !empty($this->rs->fields['thumbPath']) ? $this->rs->fields['thumbPath'] : $this->rs->fields['filePath'];
                ?>
                <div class="col-md-2 col-sm-3 col-4">
                    <div class="card h-100">
                        <?php if ($this->rs->fields['mediaType'] === 'image') { ?>
                            <img src="<?=htmlspecialchars($preview);?>" class="card-img-top" style="height:100px;object-fit:cover;">
                        <?php } else { ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height:100px;background:#f1f1f1;">
                                <i class="bi bi-file-earmark"></i>
                            </div>
                        <?php } ?>
                        <div class="card-body p-2">
                            <small class="d-block text-truncate" title="<?=htmlspecialchars($this->rs->fields['origName']);?>"><?=htmlspecialchars($this->rs->fields['origName']);?></small>
                            <input type="text" readonly class="form-control form-control-sm mb-1" value="<?=htmlspecialchars($this->rs->fields['filePath']);?>" onclick="this.select();">
                            <form method="post" action="<?=$_SERVER['PHP_SELF'];?>" onsubmit="return confirm('<?=_DELETE_QUESTION;?>');">
                                <input type="hidden" name="modname" value="media">
                                <input type="hidden" name="mf" value="mediaedit">
                                <input type="hidden" name="ac" value="delete">
                                <input type="hidden" name="mediaId" value="<?=$this->rs->fields['mediaId'];?>">
                                <?php global $sys_lanai; $sys_lanai->renderCsrfField('media'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100"><?=_DELETE;?></button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                $this->rs->movenext();
            }
            ?>
        </div>
        <?php
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }
}

?>
