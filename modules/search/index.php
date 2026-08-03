<?php
$obsearch = new LanaiSeach();
$schemaarr = $obsearch->loadSchema();
$ssm = array();
$ssi = array();
foreach ($schemaarr as $val) {
    $valitm = explode("#", $val);
    array_push($ssi, $valitm[0]);
    array_push($ssm, $valitm[1]);
}
?>

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h3 class="mb-3"><?php echo _SEARCH_LANAI; ?></h3>
            <p class="text-muted mb-4"><?php echo _SEARCH_LANAI_INSTRUCTION; ?></p>

            <form id="searchForm" class="row g-3" method="get" onsubmit="return goSearch();">
                <input type="hidden" name="modname" value="search">

                <div class="col-md-12">
                    <label for="search_keyword" class="form-label"><?php echo _SEARCH_KEYWORD; ?></label>
                    <input type="text" class="form-control" id="search_keyword" name="keyword"
                           value="<?php echo isset($_REQUEST['keyword']) ? htmlspecialchars($_REQUEST['keyword']) : ''; ?>"
                           placeholder="<?php echo _SEARCH_KEYWORD; ?>...">
                </div>

                <div class="col-md-12">
                    <label for="item" class="form-label"><?php echo _SEARCH_ITEM; ?></label>
                    <select name="item" id="item" class="form-select mb-2">
                        <?php
                        for ($i = 0; $i < count($ssi); $i++) {
                            $selected = (isset($_REQUEST['item']) && $ssi[$i] == $_REQUEST['item']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($ssi[$i]) . '" ' . $selected . '>' . htmlspecialchars($SEARCH_LANG[$ssi[$i]]) . '</option>';
                        }
                        ?>
                    </select>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="method" id="methodWord" value="word"
                            <?php if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'word') echo 'checked'; ?>>
                        <label class="form-check-label" for="methodWord"><?php echo _SEARCH_WORD; ?></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="method" id="methodPhase" value="phase"
                            <?php if (!isset($_REQUEST['method']) || $_REQUEST['method'] == 'phase') echo 'checked'; ?>>
                        <label class="form-check-label" for="methodPhase"><?php echo _SEARCH_PHASE; ?></label>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary px-4"><?php echo _SEARCH; ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (!empty($_REQUEST['keyword'])) {
        $searchItem = isset($_REQUEST['item']) && in_array($_REQUEST['item'], $ssi, true)
            ? $_REQUEST['item']
            : (isset($ssi[0]) ? $ssi[0] : '');
        $searchMethod = isset($_REQUEST['method']) && $_REQUEST['method'] === 'word' ? 'word' : 'phase';
        $sql = $obsearch->getSchema($searchItem);

        if (empty($sql)) {
            return;
        }

        $sql = preg_replace("/%tablepre%/", $cfg['tablepre'], $sql);
        if ($searchMethod == "phase") {
            $sql = preg_replace("/%keyword%/", "%" . trim($_REQUEST['keyword']) . "%", $sql);
        } else {
            $sql = preg_replace("/%keyword%/", trim($_REQUEST['keyword']), $sql);
        }

        $pager = new SearchPage($db, $sql, 30);
        $pager->item = $searchItem;

        if ($cfg['seo'] == "yes") {
            $pager->link = "/search/" . urlencode($searchItem) . "/" . urlencode($searchMethod) . "/" . urlencode($_REQUEST['keyword']) . "/?";
        } else {
            $pager->link = "module.php?modname=search&keyword=" . urlencode($_REQUEST['keyword']) . "&item=" . urlencode($searchItem) . "&method=" . urlencode($searchMethod) . "&";
        }

        $pager->renderPage();
    }
    ?>
</div>

<script type="text/javascript">
function goSearch() {
   
    var keyword = document.getElementById('search_keyword').value.replace(/^\s+|\s+$/g, '');
    console.log(keyword)
    var item = document.getElementById('item').value;
    var methodRadios = document.getElementsByName('method');
    var method = 'phase';
    for (var i = 0; i < methodRadios.length; i++) {
        if (methodRadios[i].checked) {
            method = methodRadios[i].value;
            break;
        }
    }

    if (keyword === '') return false;

    <?php if ($cfg['seo'] == "yes") { ?>
        var url = '/search/' + encodeURIComponent(item) + '/' + encodeURIComponent(method) + '/' + encodeURIComponent(keyword);
        window.location.href = url;
    <?php } else { ?>
        var url = 'module.php?modname=search&item=' + encodeURIComponent(item) + '&method=' + encodeURIComponent(method) + '&keyword=' + encodeURIComponent(keyword);
        console.log(url)
                  window.location.href = url;
    <?php } ?>

    return false;
}
</script>
