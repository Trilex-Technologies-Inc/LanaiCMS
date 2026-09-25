<?php
require_once __DIR__ . '/class.ExplorerFiles.php';
class Explorer extends ExplorerFiles
{
    function __construct() { global $cfg; parent::__construct($cfg); }
}
