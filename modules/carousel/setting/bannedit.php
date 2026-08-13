<?php
switch ($_REQUEST['ac']) {
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
        $selected = isset($_REQUEST['midId']) && is_array($_REQUEST['midId'])
            ? $_REQUEST['midId']
            : array();
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
        $selarr=$_REQUEST['midId'] ;
        $itmarr=$_REQUEST['banId'] ;
        for ($i=0;$i<count($itmarr);$i++) {
            //if ($selarr[$i]=="on") {
            $objbanner=new banner();
            $rs=$objbanner->Load("banId=".$selarr[$i]);
            if (!$rs) {
                /* no data to delete - show error message*/
                $sys_lanai->getErrorBox("Data not found!");
            }  else {
                /* perform delete */
                $objbanner->deleteBanner($selarr[$i]);

                $sys_lanai->go2Page("setting.php?modname=carousel");
            }
            //}
        }
        break;
}
?>
