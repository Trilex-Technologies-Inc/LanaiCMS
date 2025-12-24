<?php

include_once("class.BlockPager.php");

/**
 * Block
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2006
 * @version $Id: module.php,v 1.1 2007/03/23 12:37:33 redlinesoft Exp $
 * @access public
 */
class Block
{
    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    function Block()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
        //$this->db->debug = true;
    }

    function getBlock()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "block ORDER BY blcOrder ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getBlockById($blcId)
    {
        $blcId = (int)$blcId;
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "block WHERE blcId = $blcId";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getBlockList($rows = 30)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "block ORDER BY blcPosition";
        $this->_sql = $sql;
        $pager = new BlockPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function setBlockActive($mid, $v)
    {
        $mid = (int)$mid;
        $v = addslashes($v);
        $sql = "UPDATE " . $this->cfg['tablepre'] . "block SET blcActive = '$v' WHERE blcId = $mid";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setBlockOrder($mid, $order)
    {
        $mid = (int)$mid;
        $order = (int)$order;
        $sql = "UPDATE " . $this->cfg['tablepre'] . "block SET blcOrder = $order WHERE blcId = $mid";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setDeleteBlock($mid)
    {
        $mid = (int)$mid;
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "block WHERE blcId = $mid";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getMaxOrderValue()
    {
        $sql = "SELECT MAX(blcOrder) FROM " . $this->cfg['tablepre'] . "block";
        $rs = $this->db->execute($sql);
        return $rs->fields[0];
    }

    function setNewBlock($blcTitle, $blcName, $blcContent, $blcRssUrl, $blcRssRefesh = 600, $blcPosition, $m)
    {
        // Ensure numeric values
        $blcRssRefesh = (int)$blcRssRefesh;
        $blcRssTime = time();
        $blcOrder = (int)($this->getMaxOrderValue() + 1);

        // Convert name to lowercase
        $blcName = strtolower($blcName);

        // Escape string values
        $blcTitle = addslashes($blcTitle);
        $blcName = addslashes($blcName);
        $blcContent = addslashes($blcContent);
        $blcRssUrl = addslashes($blcRssUrl);
        $blcPosition = addslashes($blcPosition);
        $m = addslashes($m);

        // Build SQL
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "block 
                (blcTitle, blcName, blcType, blcRssUrl, blcRssRefesh, blcRssTime, blcContent, blcPosition, blcOrder, blcActive) 
                VALUES 
                ('$blcTitle', '$blcName', '$m', '$blcRssUrl', $blcRssRefesh, $blcRssTime, '$blcContent', '$blcPosition', $blcOrder, 'y')";

        // Execute query
        $rs = $this->db->execute($sql);

        // Log error if it fails
        if (!$rs) {
            $errorMsg = "DB Error: " . $this->db->error . " | SQL: " . $sql . "\n";
            file_put_contents(__DIR__ . '/db_errors.log', $errorMsg, FILE_APPEND);
        }

        return $rs;
    }

    function setEditBlock($blcId, $blcTitle, $blcName, $blcContent, $blcRssUrl, $blcRssRefesh = 600, $blcPosition, $m)
    {
        // Cast numeric values
        $blcId = (int)$blcId;
        $blcRssRefesh = (int)$blcRssRefesh;
        $blcRssTime = time();

        // Convert name to lowercase
        $blcName = strtolower($blcName);

        // Escape all string values
        $blcTitle = addslashes($blcTitle);
        $blcName = addslashes($blcName);
        $blcContent = addslashes($blcContent);
        $blcRssUrl = addslashes($blcRssUrl);
        $blcPosition = addslashes($blcPosition);
        $m = addslashes($m);

        // Build SQL query
        $sql = "UPDATE " . $this->cfg['tablepre'] . "block 
                SET blcTitle = '$blcTitle',
                    blcName = '$blcName',
                    blcType = '$m',
                    blcRssUrl = '$blcRssUrl',
                    blcRssRefesh = $blcRssRefesh,
                    blcRssTime = $blcRssTime,
                    blcContent = '$blcContent',
                    blcPosition = '$blcPosition'
                WHERE blcId = $blcId";

        // Execute query
        $rs = $this->db->execute($sql);

        // Log error if it fails
        if (!$rs) {
            $errorMsg = "DB Error: " . $this->db->error . " | SQL: " . $sql . "\n";
            file_put_contents(__DIR__ . '/db_errors.log', $errorMsg, FILE_APPEND);
        }

        return $rs;
    }

    function setBlockUpload($userfile)
    {
        global $sys_lanai;
        $modpath = $this->cfg['dir'] . $sys_lanai->getPath() . "blocks/";
        $cfgPackagePath = $this->cfg['packdir'] . $sys_lanai->getPath();

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $cfgPackagePath . $prefix . $_FILES['userfile']['name'])) {
            include('include/pclzip/pclzip.lib.php');

            $moddir = substr($_FILES['userfile']['name'], 0, (strlen($_FILES['userfile']['name']) - 4));
            $archive = new PclZip($cfgPackagePath . $prefix . $_FILES['userfile']['name']);

            if ($archive->extract(PCLZIP_OPT_PATH, $modpath)) {
                return true;
            } else {
                return false;
            }

            unlink($cfgPackagePath . $prefix . $_FILES['userfile']['name']);
        } else {
            return false;
        }
    }
}

?>
