<?
$configFile = "../config.inc.php";

if (file_exists($configFile)) {
    include_once($configFile);
} else {
    die("Config file not found!");
}

include_once("../include/adodb/adodb.inc.php");

$ADODB_CACHE_DIR = "./datacenter/cache/";
$db = &ADONewConnection($dbtype);

function dbexecute($title, $sql) {
    global $db;
    $rs = $db->execute($sql);
    if ($rs) {
        ?><?=$title."&nbsp;&nbsp;["; ?><span style="color:green;">OK</span>]<?
    } else {
        ?><?=$title."&nbsp;&nbsp;["; ?><span style="color:red;">FAILED</span>]<?
    }
}

function upgradeBannerTableAlt() {
    global $db, $tablepre;
    
    $columnExists = false;
    
    $describeSQL = "DESCRIBE `".$tablepre."banner`";
    $columns = $db->execute($describeSQL);
    
    if ($columns) {
        while (!$columns->EOF) {
            if ($columns->fields['Field'] == 'banPosition') {
                $columnExists = true;
                break;
            }
            $columns->moveNext();
        }
        
        if (!$columnExists) {
            $alterSQL = "ALTER TABLE `".$tablepre."banner` 
                         ADD COLUMN `banPosition` ENUM('l','r','c','t','b') NOT NULL DEFAULT 'l' 
                         AFTER `banURL`";
            
            $result = $db->execute($alterSQL);
            
            if ($result) {
                ?>Upgrade Banner Table - Adding banPosition column&nbsp;&nbsp;[<span style="color:green;">OK</span>]<?
            } else {
                ?>Upgrade Banner Table - Adding banPosition column&nbsp;&nbsp;[<span style="color:red;">FAILED</span>]<?
            }
        } else {
            ?>Upgrade Banner Table - banPosition column already exists&nbsp;&nbsp;[<span style="color:green;">OK</span>]<?
        }
    }
}

if ($db->NConnect($dbhost, $dbuser, $dbpw, $dbname)) {
?>
<b>Upgrading Database Structure:</b>
<ul>
    <li>
<?
    upgradeBannerTableAlt();
?>
    </li>
</ul>
<?
}
?>