<?php

class Role extends Model
{
    private function initalizeRoles()
    {
        if (!$this->tableExists()) {
            self::getDatabaseInstance()->query('CREATE TABLE roles(id INT(11) AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            value VARCHAR(255) NOT NULL
            );');
        }
    }

    public function giveRoleToAccount($account, $role)
    {
        $this->initalizeRoles();
        $data = $this->select()->where('type="give_role" AND name=? AND value=?')->bind($account)->bind($role)->execute();
        if (count($data) === 0) {
            return $this->insert()->set('type', 'give_role')->set('name', $account)->set('value', $role)->execute();
        }
        return false;
        //return $this->query('INSERT INTO roles(type,name,value)VALUES("give_role",?,?)', array($account, $role)) !== false;
    }

    public function revokeRoleFromAccount($account, $role)
    {
        $this->initalizeRoles();
        return $this->delete()->where('type="give_role" AND name=? AND value=?')->bind($account)->bind($role)->execute();
        //return $this->query('DELETE FROM roles WHERE type="give_role" AND name=? AND value=?', array($account, $role)) !== false;
    }

    public function addRole($role)
    {
        $this->initalizeRoles();
        return $this->insert()->set('type', 'add_role')->set('name', $role)->execute();
        //return $this->query('INSERT INTO roles(type,name)VALUES("add_role",?)', array($role)) !== false;
    }

    public function givePermissionToRole($permission, $role)
    {
        $this->initalizeRoles();
        return $this->insert()->set('type', 'add_permission')->set('name', $role)->set('value', $permission)->execute();
        //return $this->query('INSERT INTO roles(type,name,value)VALUES("add_permission",?,?)', array($role, $permission)) !== false;
    }

    public function addInherit($role, $inheritFrom)
    {
        $this->initalizeRoles();
        return $this->insert()->set('type', 'add_inherit')->set('name', $role)->set('value', $inheritFrom)->execute();
        //return $this->query('INSERT INTO roles(type,name,value)VALUES("add_inherit",?,?)', array($role, $inheritFrom)) !== false;
    }

    public function getInheritsOfRole($role)
    {
        $this->initalizeRoles();
        $data = $this->select()->where('type="add_inherit" AND name=?')->bind($role)->execute();
        //$data = $this->query('SELECT value FROM roles WHERE type="add_inherit" AND name=?', array($role));
        $ret = array();
        foreach ($data as $row) {
            $ret[] = $row['value'];
        }

        return $ret;
    }

    public function getAllRoles()
    {
        $this->initalizeRoles();
        $data = $this->select()->where('type="add_role"')->execute();
        //$data = $this->query('SELECT name FROM roles WHERE type="add_role"');
        $ret = array();
        foreach ($data as $row) {
            $ret[] = $row['name'];
        }

        return $ret;
    }

    public function getRolesOfAccount($username)
    {
        $this->initalizeRoles();
        $data = $this->select()->where('type="give_role" AND name=?')->bind($username)->execute();
        //$data = $this->query('SELECT value FROM roles WHERE type="give_role" AND name=?', array($username));
        $ret = array();
        foreach ($data as $row) {
            $ret[] = $row['value'];
        }

        return $ret;
    }

    public function doesRoleHoldPermission($role, $permission)
    {
        $this->initalizeRoles();
        $data = $this->select()->where('type="add_permission" AND name=?')->bind($role)->execute();
        //$data = $this->query('SELECT value FROM roles WHERE type="add_permission" AND name=?', array($role));
        if ($data === false) {
            return false;
        }
        foreach ($data as $row) {
            if ($row['value'] === $permission || $row['value'] === '*') {
                return true;
            }
        }

        return false;
    }

    public function isRoleAllowed($role, $permission)
    {
        if ($this->doesRoleHoldPermission($role, $permission)) {
            return true;
        }
        $inherits = $this->getInheritsOfRole($role);
        foreach ($inherits as $inherit) {
            if ($this->isRoleAllowed($inherit, $permission)) {
                return true;
            }
        }
    }

    public function isAllowed($username, $permission)
    {
        $roles = $this->getRolesOfAccount($username);
        foreach ($roles as $role) {
            if ($this->isRoleAllowed($role, $permission)) {
                return true;
            }
        }

        return false;
    }

}
