<?


class SiteMap
{

    var $uid;
    var $db;
    var $cfg;
    var $_sql;
    var $version = "0.1";

    function SiteMap()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
        //$this->db->debug=true;
    }

    function render()
    {
        include_once("include/lanai/class.navigator.php");
        $sys_nav = new Navigator();
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "menu WHERE mnuActive='y'  AND mnuParentId=0 ORDER BY mnuOrder ASC ";
        $rs = $this->db->execute($sql);
        $num = $rs->recordcount();
        ?>
        <ul><?
        while (!$rs->EOF) {
            $prelink = $sys_nav->getMenuRealLink($rs);
            ?>
            <li><a href="<?= $prelink; ?>"
                   target="<?= $rs->fields['mnuTarget']; ?>" ><?= $rs->fields['mnuTitle']; ?></a><?
            // get sub menu
            $sqlsub = "SELECT * FROM " . $this->cfg['tablepre'] . "menu WHERE mnuParentId=" . $rs->fields['mnuId'] . " AND mnuActive='y' ORDER BY mnuOrder ASC";
            $rssub = $this->db->execute($sqlsub);
            if ($rssub->recordcount() > 0) {
                ?>
                <ul><?
                while (!$rssub->EOF) {
                    // find real link
                    $prelink = $sys_nav->getMenuRealLink($rssub);
                    ?>
                    <li><a href="<?= $prelink; ?>"
                           target="<?= $rssub->fields['mnuTarget']; ?>"><?= $rssub->fields['mnuTitle']; ?></a></li><?
                    $rssub->movenext();
                } // while
                ?></ul></li><?
            } else {
                ?></li><?
            }
            $rs->movenext();
        } // while
        ?></ul><?
    }

    function feed_render()
    {
        $menuitem = array();
        $menulink = array();
        $sys_nav = new Navigator();
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "menu WHERE mnuActive='y'  AND mnuParentId=0 ORDER BY mnuOrder ASC ";
        $rs = $this->db->execute($sql);
        $num = $rs->recordcount();
        while (!$rs->EOF) {
            $prelink = $sys_nav->getMenuRealLink($rs);
            if ($rs->fields['mnuType'] == "l") {
                array_push($menulink, $prelink);
            } else {
                array_push($menulink, $this->cfg['url'] . "/" . $prelink);
            }
            array_push($menuitem, $rs->fields['mnuTitle']);
            // get sub menu
            $sqlsub = "SELECT * FROM " . $this->cfg['tablepre'] . "menu WHERE mnuParentId=" . $rs->fields['mnuId'] . " AND mnuActive='y' ORDER BY mnuOrder ASC";
            $rssub = $this->db->execute($sqlsub);
            if ($rssub->recordcount() > 0) {

                while (!$rssub->EOF) {
                    // find real link
                    $prelink = $sys_nav->getMenuRealLink($rssub);
                    if ($rssub->fields['mnuType'] == "l") {
                        array_push($menulink, $prelink);
                    } else {
                        array_push($menulink, $this->cfg['url'] . "/" . $prelink);
                    }
                    array_push($menuitem, $rssub->fields['mnuTitle']);
                    $rssub->movenext();
                } // while
            }
            $rs->movenext();
        } // while
        return (array($menuitem, $menulink));
    }

}

?>
