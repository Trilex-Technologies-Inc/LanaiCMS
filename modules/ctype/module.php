<?php

include_once("class.CTypePager.php");
include_once("class.CItemPager.php");

/**
 * ContentType — generic content-type / flexible-field engine.
 * Lets an admin define new "types" (e.g. Product, Testimonial) with custom
 * fields, without writing a new module per vertical.
 */
class ContentType
{
    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    static $fieldTypes = array('text', 'textarea', 'richtext', 'number', 'date', 'select', 'checkbox', 'image', 'file');
    static $uploadFieldTypes = array('image', 'file');
    static $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    static $fileExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt');

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
    }

    // ---------------------------------------------------------------
    // Content types
    // ---------------------------------------------------------------

    function getTypes()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "ctype ORDER BY ctpOrder ASC, ctpTitle ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getTypeList($rows = 30)
    {
        $this->getTypes();
        $pager = new CTypePager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getTypeById($ctpId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "ctype WHERE ctpId=" . intval($ctpId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getTypeBySlug($slug)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "ctype WHERE ctpSlug=" . $this->db->qstr($slug) . " AND ctpActive='y'";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function slugify($text)
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    function setNewType($title)
    {
        $title = trim((string) $title);
        $name = $this->slugify($title);
        $slug = $name;

        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "ctype (ctpName, ctpTitle, ctpSlug, ctpActive, ctpOrder)
                VALUES (" . $this->db->qstr($name) . ", " . $this->db->qstr($title) . ", " . $this->db->qstr($slug) . ", 'y', 0)";
        $rs = $this->db->Execute($sql);
        if (!$rs) {
            return false;
        }
        return $this->db->Insert_ID();
    }

    function setEditType($ctpId, $title, $slug)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "ctype
                SET ctpTitle=" . $this->db->qstr($title) . ",
                    ctpSlug=" . $this->db->qstr($this->slugify($slug)) . "
                WHERE ctpId=" . intval($ctpId);
        return $this->db->Execute($sql);
    }

    function setTypeActive($ctpId, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "ctype SET ctpActive=" . $this->db->qstr($value) . "
                WHERE ctpId=" . intval($ctpId);
        return $this->db->Execute($sql);
    }

    function setDeleteType($ctpId)
    {
        $ctpId = intval($ctpId);
        $tablepre = $this->cfg['tablepre'];
        // remove field values -> items -> fields -> type, in FK-safe order
        $this->db->Execute("DELETE cv FROM {$tablepre}cvalue cv
                             INNER JOIN {$tablepre}citem ci ON ci.citId = cv.citId
                             WHERE ci.ctpId = $ctpId");
        $this->db->Execute("DELETE FROM {$tablepre}citem WHERE ctpId = $ctpId");
        $this->db->Execute("DELETE FROM {$tablepre}cfield WHERE ctpId = $ctpId");
        return $this->db->Execute("DELETE FROM {$tablepre}ctype WHERE ctpId = $ctpId");
    }

    // ---------------------------------------------------------------
    // Fields (the "flexible fields" per type)
    // ---------------------------------------------------------------

    function getFields($ctpId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "cfield
                WHERE ctpId=" . intval($ctpId) . " ORDER BY cfdOrder ASC, cfdId ASC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getFieldById($cfdId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "cfield WHERE cfdId=" . intval($cfdId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getFieldByName($ctpId, $name)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "cfield
                WHERE ctpId=" . intval($ctpId) . " AND cfdName=" . $this->db->qstr($name);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function setNewField($ctpId, $label, $type, $required, $options = '')
    {
        if (!in_array($type, self::$fieldTypes, true)) {
            $type = 'text';
        }
        $name = $this->slugify($label);
        $name = $name !== '' ? $name : ('field_' . time());

        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "cfield
                (ctpId, cfdName, cfdLabel, cfdType, cfdOptions, cfdRequired, cfdOrder)
                VALUES (" . intval($ctpId) . ", " . $this->db->qstr($name) . ", " . $this->db->qstr($label) . ",
                        " . $this->db->qstr($type) . ", " . $this->db->qstr($options) . ",
                        " . $this->db->qstr($required === 'y' ? 'y' : 'n') . ", 0)";
        return $this->db->Execute($sql);
    }

    function setDeleteField($cfdId)
    {
        $cfdId = intval($cfdId);
        $tablepre = $this->cfg['tablepre'];
        $this->db->Execute("DELETE FROM {$tablepre}cvalue WHERE cfdId = $cfdId");
        return $this->db->Execute("DELETE FROM {$tablepre}cfield WHERE cfdId = $cfdId");
    }

    /**
     * Validate and move an uploaded image/file into datacenter/uimage/ctype,
     * returning the web-relative path to store in cvalue, or false on failure.
     */
    function saveUploadedFile($fieldType, $fileArr)
    {
        if (empty($fileArr['tmp_name']) || !is_uploaded_file($fileArr['tmp_name'])) {
            return false;
        }

        $allowed = $fieldType === 'image' ? self::$imageExtensions : self::$fileExtensions;
        $ext = strtolower(pathinfo(basename($fileArr['name']), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return false;
        }
        if ($fieldType === 'image' && @getimagesize($fileArr['tmp_name']) === false) {
            return false;
        }

        $destDir = $this->cfg['datadir'] . DIRECTORY_SEPARATOR . 'uimage' . DIRECTORY_SEPARATOR . 'ctype';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(12)) . '.' . $ext;
        $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($fileArr['tmp_name'], $destPath)) {
            return false;
        }

        return 'datacenter/uimage/ctype/' . $filename;
    }

    // ---------------------------------------------------------------
    // Items (the actual content rows for a type)
    // ---------------------------------------------------------------

    function getItems($ctpId, $activeOnly = false)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "citem WHERE ctpId=" . intval($ctpId);
        if ($activeOnly) {
            $sql .= " AND citActive='y'";
        }
        $sql .= " ORDER BY citCreated DESC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getItemList($ctpId, $rows = 30)
    {
        $this->getItems($ctpId);
        $pager = new CItemPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getItemById($citId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "citem WHERE citId=" . intval($citId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getItemBySlug($ctpId, $slug)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "citem
                WHERE ctpId=" . intval($ctpId) . " AND citSlug=" . $this->db->qstr($slug) . " AND citActive='y'";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getItemValues($citId)
    {
        // one row per field the item has a value for, joined to field metadata
        $sql = "SELECT f.cfdId, f.cfdName, f.cfdLabel, f.cfdType, f.cfdOptions, v.cvalText, v.cvalNumber
                FROM " . $this->cfg['tablepre'] . "cfield f
                LEFT JOIN " . $this->cfg['tablepre'] . "cvalue v ON v.cfdId = f.cfdId AND v.citId = " . intval($citId) . "
                WHERE f.ctpId = (SELECT ctpId FROM " . $this->cfg['tablepre'] . "citem WHERE citId = " . intval($citId) . ")
                ORDER BY f.cfdOrder ASC, f.cfdId ASC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    /**
     * Save an item (insert when $citId is empty, update otherwise) plus its
     * flexible field values. $values is [cfdId => rawValue] keyed by field id.
     */
    function setSaveItem($citId, $ctpId, $title, $values)
    {
        $uid = (int) $this->uid;
        $tablepre = $this->cfg['tablepre'];

        if (empty($citId)) {
            $slug = $this->slugify($title) . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
            $sql = "INSERT INTO {$tablepre}citem (ctpId, citTitle, citSlug, citActive, citCreated, citUpdated, userId)
                    VALUES (" . intval($ctpId) . ", " . $this->db->qstr($title) . ", " . $this->db->qstr($slug) . ",
                            'y', NOW(), NOW(), $uid)";
            if (!$this->db->Execute($sql)) {
                return false;
            }
            $citId = $this->db->Insert_ID();
        } else {
            $citId = intval($citId);
            $sql = "UPDATE {$tablepre}citem SET citTitle=" . $this->db->qstr($title) . ", citUpdated=NOW()
                    WHERE citId=" . $citId;
            if (!$this->db->Execute($sql)) {
                return false;
            }
        }

        foreach ((array) $values as $cfdId => $rawValue) {
            $cfdId = intval($cfdId);
            $numeric = is_numeric($rawValue) ? (float) $rawValue : null;
            $this->db->Execute(
                "DELETE FROM {$tablepre}cvalue WHERE citId=$citId AND cfdId=$cfdId"
            );
            $this->db->Execute(
                "INSERT INTO {$tablepre}cvalue (citId, cfdId, cvalText, cvalNumber)
                 VALUES ($citId, $cfdId, " . $this->db->qstr((string) $rawValue) . ", " .
                    ($numeric === null ? "NULL" : $this->db->qstr($numeric)) . ")"
            );
        }

        return $citId;
    }

    function setItemActive($citId, $value)
    {
        $value = $value === 'n' ? 'n' : 'y';
        $sql = "UPDATE " . $this->cfg['tablepre'] . "citem SET citActive=" . $this->db->qstr($value) . "
                WHERE citId=" . intval($citId);
        return $this->db->Execute($sql);
    }

    function setDeleteItem($citId)
    {
        $citId = intval($citId);
        $tablepre = $this->cfg['tablepre'];
        $this->db->Execute("DELETE FROM {$tablepre}cvalue WHERE citId = $citId");
        return $this->db->Execute("DELETE FROM {$tablepre}citem WHERE citId = $citId");
    }
}

?>
