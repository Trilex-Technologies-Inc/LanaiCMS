<?php
function getLanguage() {
    if ($handle = opendir("../language/")) {
        $i=0;
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && !is_file($file)) {
                $arTheme[$i]=$file;
                $i++;
            }
        }
        closedir($handle);
    }
    return ($arTheme);
}
?>

<form method="POST" action="<?=$_SERVER['PHP_SELF']; ?>">
    <!-- Site Info Section -->
    <div class="mb-4">
        <h5><?=_SETUP_SITEINFO; ?>:</h5>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_SITENAME; ?></label>
            <div class="col-sm-9">
                <input type="text" name="cfg_title" class="form-control" value="Lanai Core!">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_URL; ?></label>
            <div class="col-sm-9">
                <input type="text" name="cfg_url" class="form-control" value="http://<?=$_SERVER["SERVER_NAME"]; ?>">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DIR; ?></label>
            <div class="col-sm-9">
                <input type="text" name="cfg_dir" class="form-control" value="<?=substr(getcwd(),0,(strlen(getcwd())-8)); ?>">
                <input type="hidden" name="cfg_off" value="no">
                <input type="hidden" name="cfg_log" value="yes">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_LANG; ?></label>
            <div class="col-sm-9">
                <select name="cfg_lang" class="form-select">
                <?php
                    $langar = getLanguage();
                    foreach ($langar as $value) {
                        $xvalue = substr($value,5,strlen($value)-9);
                        echo '<option value="'.$xvalue.'" selected>'.ucwords($xvalue).'</option>';
                    }
                ?>
                </select>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_THEME; ?></label>
            <div class="col-sm-9">
                <select name="cfg_theme" class="form-select">
                    <option value="bootstrap" selected>Default</option>
                </select>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_TIMEZONE; ?></label>
            <div class="col-sm-9">
                <select name="cfg_offsettime" class="form-select">
                    <?php
                        for ($i=0; $i<26; $i++) {
                            $isx = ($i-12);
                            $select = ($isx==7) ? "selected" : "";
                            $isx = ($isx > 0) ? "+".$isx : $isx;
                            echo "<option value='".($i-12)."' $select>$isx</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Admin Info Section -->
    <div class="mb-4">
        <h5><?=_SETUP_ADMININFO; ?>:</h5>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_USERNAME; ?></label>
            <div class="col-sm-9">
                <input type="text" name="username" class="form-control" value="admin">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_PASSWORD; ?></label>
            <div class="col-sm-9">
                <?php
                    $pwdarr = array_merge(range('a','z'), range('A','Z'), range('0','9'));
                    $pwsstr = '';
                    for ($i=0; $i<16; $i++) { $pwsstr .= $pwdarr[rand(0,61)]; }
                ?>
                <input type="text" name="password" class="form-control" value="<?=$pwsstr; ?>">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_EMAIL; ?></label>
            <div class="col-sm-9">
                <input type="text" name="cfg_email" class="form-control" value="admin@<?=$_SERVER["SERVER_NAME"]; ?>">
            </div>
        </div>
    </div>

    <!-- Database Info Section -->
    <div class="mb-4">
        <h5><?=_SETUP_DATABASEINFO; ?>:</h5>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DB; ?></label>
            <div class="col-sm-9">
                MySQL <input type="hidden" name="dbtype" value="mysqli">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DBHOST; ?></label>
            <div class="col-sm-9">
                <input type="text" name="dbhost" class="form-control" value="localhost">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DBUSER; ?></label>
            <div class="col-sm-9">
                <input type="text" name="dbuser" class="form-control" value="root">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DBPWD; ?></label>
            <div class="col-sm-9">
                <input type="text" name="dbpw" class="form-control" value="">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_DBNAME; ?></label>
            <div class="col-sm-9">
                <input type="text" name="dbname" class="form-control" value="lanaicore">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_TABLEPRE; ?></label>
            <div class="col-sm-9">
                <input type="text" name="tablepre" class="form-control" value="tbl_ln_">
            </div>
        </div>
    </div>

    <!-- Mail Info Section -->
    <div class="mb-4">
        <h5><?=_SETUP_MAILINFO; ?>:</h5>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_SMHOST; ?></label>
            <div class="col-sm-9">
                <input type="text" name="smtp_host" class="form-control" value="localhost">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_SMPORT; ?></label>
            <div class="col-sm-9">
                <input type="text" name="smtp_port" class="form-control" value="25">
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label"><?=_SETUP_SNT2ADMIN; ?></label>
            <div class="col-sm-9">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cfg_sendmail" value="yes" checked>
                    <label class="form-check-label"><?=_SETUP_YES; ?></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cfg_sendmail" value="no">
                    <label class="form-check-label"><?=_SETUP_NO; ?></label>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between">
        <input type="hidden" name="step" value="<?=($_REQUEST['step']-1)?>">
        <button type="button" class="btn btn-secondary" onclick="history.back();">&lt; <?=_SETUP_BACK; ?></button>

        <input type="hidden" name="step" value="<?=($_REQUEST['step']+1)?>">
        <button type="submit" class="btn btn-primary"><?=_SETUP_CREATE_TABLE; ?> &gt;</button>
    </div>
</form>
