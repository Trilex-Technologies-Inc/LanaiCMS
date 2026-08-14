<?php
$action = isset($_REQUEST['ac']) && !is_array($_REQUEST['ac'])
    ? (string) $_REQUEST['ac']
    : '';

switch ($action) {
    case "active" :
        $objbanner = new banner();
        $objbanner->setBannerActive(
            isset($_REQUEST['mid']) ? $_REQUEST['mid'] : 0,
            isset($_REQUEST['v']) ? $_REQUEST['v'] : 'y'
        );
        $sys_lanai->go2Page("setting.php?modname=carousel");
        break;
    case "mactive" :
        $objbanner = new banner();
        $selected = array();
        if (isset($_REQUEST['midId']) && is_array($_REQUEST['midId'])) {
            $selected = $_REQUEST['midId'];
        } elseif (isset($_REQUEST['mid']) && is_array($_REQUEST['mid'])) {
            $selected = $_REQUEST['mid'];
        }

        foreach ($selected as $selectedId) {
            $selectedId = intval($selectedId);
            if ($selectedId < 1 || !$objbanner->Load("banId=" . $selectedId)) {
                continue;
            }
            $objbanner->setBannerActive(
                $selectedId,
                $objbanner->banactive === 'y' ? 'n' : 'y'
            );
        }
        $sys_lanai->go2Page("setting.php?modname=carousel");
        break;
    case "mdelete" :
        $selected = array();
        if (isset($_REQUEST['midId']) && is_array($_REQUEST['midId'])) {
            $selected = $_REQUEST['midId'];
        } elseif (isset($_REQUEST['mid']) && is_array($_REQUEST['mid'])) {
            $selected = $_REQUEST['mid'];
        }

        if (empty($selected)) {
            $sys_lanai->getErrorBox("Data not found!");
            break;
        }

        foreach ($selected as $selectedId) {
            $selectedId = intval($selectedId);
            if ($selectedId < 1) {
                continue;
            }

            $objbanner = new banner();
            if (!$objbanner->deleteBanner($selectedId)) {
                $sys_lanai->getErrorBox($objbanner->ErrorMsg());
            }
        }

        $sys_lanai->go2Page("setting.php?modname=carousel");
        break;
}
?>
