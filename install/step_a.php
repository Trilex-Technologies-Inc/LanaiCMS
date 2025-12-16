<?php
if (session_id() == '') {
    session_start();
}

if (!empty($_REQUEST['set'])) {
    $_SESSION['lang'] = $_REQUEST['set'];
    ?>
    <script>
        location.href = "index.php";
    </script>
    <?php
}

function getLanguage() {
    $arTheme = [];
    if ($handle = opendir('language/')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && !is_file($file)) {
                $arTheme[] = $file;
            }
        }
        closedir($handle);
    }
    return $arTheme;
}

$langar = getLanguage();
?>

<!-- Bootstrap 5 Language Selector -->
<div class="container mt-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <h5 class="card-title mb-3 text-primary fw-bold">
                <?= _SETUP_SELECT_LANGUAGE; ?>
            </h5>

            <form name="form1" class="mb-4">
                <div class="mb-3">
                    <select name="menu1" class="form-select" onChange="MM_jumpMenu('parent',this,0)">
                        <?php
                        foreach ($langar as $value) {
                            $xvalue = substr($value, 5, strlen($value));
                            $xvalue = substr($xvalue, 0, strlen($xvalue) - 4);
                            $selected = ($_SESSION['lang'] == $xvalue) ? "selected" : "";
                            ?>
                            <option value="index.php?set=<?= $xvalue; ?>" <?= $selected; ?>>
                                <?= ucwords($xvalue); ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </form>

            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="step" value="b">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <?= _SETUP_LICENSE_AGREEMENT; ?> &raquo;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function MM_jumpMenu(targ, selObj, restore) {
    eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
    if (restore) selObj.selectedIndex = 0;
}
</script>
