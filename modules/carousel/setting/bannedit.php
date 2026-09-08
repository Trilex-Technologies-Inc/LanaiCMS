<?php
$action = isset($_POST['ac']) && !is_array($_POST['ac'])
    ? (string) $_POST['ac']
    : '';

$submittedToken = isset($_POST['carousel_form_token']) && !is_array($_POST['carousel_form_token'])
    ? (string) $_POST['carousel_form_token']
    : '';
$expectedToken = isset($_SESSION['carousel_form_token']) ? (string) $_SESSION['carousel_form_token'] : '';
$isValidCarouselPost = $_SERVER['REQUEST_METHOD'] === 'POST'
    && $submittedToken !== ''
    && $expectedToken !== ''
    && (function_exists('hash_equals') ? hash_equals($expectedToken, $submittedToken) : $expectedToken === $submittedToken);

switch ($action) {
    case "active" :
        if (!$isValidCarouselPost) {
            $sys_lanai->getErrorBox("Invalid request.");
            break;
        }
        $objbanner = new banner();
        $objbanner->setBannerActive(
            isset($_POST['mid']) ? $_POST['mid'] : 0,
            isset($_POST['v']) ? $_POST['v'] : 'y'
        );
        $sys_lanai->go2Page("setting.php?modname=carousel");
        break;
    case "mactive" :
        if (!$isValidCarouselPost) {
            $sys_lanai->getErrorBox("Invalid request.");
            break;
        }
        global $db;
        $objbanner = new banner();
        $selected = array();
        if (isset($_POST['midId']) && is_array($_POST['midId'])) {
            $selected = $_POST['midId'];
        } elseif (isset($_POST['mid']) && is_array($_POST['mid'])) {
            $selected = $_POST['mid'];
        }

        foreach ($selected as $selectedId) {
            $selectedId = (int) $selectedId;
            if ($selectedId < 1) {
                continue;
            }
            $bannerState = $db->GetOne(
                "SELECT banActive FROM " . $objbanner->_table . " WHERE banId = ?",
                array($selectedId)
            );
            if ($bannerState === false) {
                continue;
            }
            $objbanner->setBannerActive(
                $selectedId,
                $bannerState === 'y' ? 'n' : 'y'
            );
        }
        $sys_lanai->go2Page("setting.php?modname=carousel");
        break;
    case "mdelete" :
        if (!$isValidCarouselPost) {
            $sys_lanai->getErrorBox("Invalid request.");
            break;
        }
        $selected = array();
        if (isset($_POST['midId']) && is_array($_POST['midId'])) {
            $selected = $_POST['midId'];
        } elseif (isset($_POST['mid']) && is_array($_POST['mid'])) {
            $selected = $_POST['mid'];
        }

        if (empty($selected)) {
            $sys_lanai->getErrorBox("Data not found!");
            break;
        }

        foreach ($selected as $selectedId) {
            $selectedId = (int) $selectedId;
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
