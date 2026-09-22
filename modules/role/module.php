<?php

include_once("class.RolePager.php");

/**
 * Role — manages roles and their assigned capabilities, used by
 * Systems::userHasCapability() to gate finer-grained actions than the
 * legacy userPrivilege 'a'/'m'/'u' flag.
 */
class Role
{
    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
    }

    function getRoles()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "role ORDER BY roleOrder ASC, roleTitle ASC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getRoleList($rows = 30)
    {
        $this->getRoles();
        $pager = new RolePager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getRoleById($roleId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "role WHERE roleId=" . intval($roleId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getCapabilities()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "capability ORDER BY capId ASC";
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function getRoleCapabilityIds($roleId)
    {
        $sql = "SELECT capId FROM " . $this->cfg['tablepre'] . "role_capability WHERE roleId=" . intval($roleId);
        $rs = $this->db->execute($sql);
        $ids = array();
        while ($rs && !$rs->EOF) {
            $ids[] = (int) $rs->fields['capId'];
            $rs->movenext();
        }
        return $ids;
    }

    function setRoleCapabilities($roleId, $capIds)
    {
        $roleId = intval($roleId);
        $tablepre = $this->cfg['tablepre'];
        $this->db->Execute("DELETE FROM {$tablepre}role_capability WHERE roleId = $roleId");
        foreach ((array) $capIds as $capId) {
            $this->db->Execute("INSERT INTO {$tablepre}role_capability (roleId, capId) VALUES ($roleId, " . intval($capId) . ")");
        }
        return true;
    }

    function setNewRole($title, $capIds)
    {
        $name = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim($title)));
        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "role (roleName, roleTitle, roleOrder)
                VALUES (" . $this->db->qstr($name) . ", " . $this->db->qstr($title) . ", 0)";
        if (!$this->db->Execute($sql)) {
            return false;
        }
        $roleId = $this->db->Insert_ID();
        $this->setRoleCapabilities($roleId, $capIds);
        return $roleId;
    }

    function setEditRole($roleId, $title, $capIds)
    {
        $sql = "UPDATE " . $this->cfg['tablepre'] . "role SET roleTitle=" . $this->db->qstr($title) . "
                WHERE roleId=" . intval($roleId);
        $this->db->Execute($sql);
        $this->setRoleCapabilities($roleId, $capIds);
        return true;
    }

    function setDeleteRole($roleId)
    {
        $roleId = intval($roleId);
        $tablepre = $this->cfg['tablepre'];
        $this->db->Execute("DELETE FROM {$tablepre}role_capability WHERE roleId = $roleId");
        $this->db->Execute("UPDATE {$tablepre}user SET userRoleId=NULL WHERE userRoleId = $roleId");
        return $this->db->Execute("DELETE FROM {$tablepre}role WHERE roleId = $roleId");
    }
}

?>
