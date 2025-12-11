<?php
class roles extends database
{
    public function manage_role($name, $permissions, $id = null)
    {
        if ($name == "") return $this->message("Enter role name", "error");
        if ($permissions == "") return $this->message("No permission selected or passed", "error");
        $check = $this->getall("roles", "ID != ? and name = ?", [$id ?? "", $name]);
        if (is_array($check)) return $this->message("$name already exists", "error");
        $data = ["name" => $name, "permissions" => base64_encode($permissions)];
        if ($id != null && $id != "") {
            if (!$this->validate_action(["roles" => "edit"], true)) return;
            return $this->update("roles", $data, "ID = '$id'", "$name Updated");
        }
        if (!$this->validate_action(["roles" => "new"], true)) return;
        return $this->quick_insert("roles", $data, "$name Created.");
    }

    public function get_role($roleID)
    {
        $role = $this->getall("roles", "ID = ?", [$roleID]);
        $rolePermissions = htmlspecialchars_decode(base64_decode($role['permissions']));
        $roles = json_decode($rolePermissions, true) ?? [];
        $roles['name'] = (isset($role['name'])) ? $role['name'] : "Unknow";
        return $roles;
    }

    public function validate_action($permission, $message = false)
    {
        return true;
        if (!is_array($permission)) {
            return (isset(ADMINROLE[$permission]) && is_array(ADMINROLE[$permission]) && count(ADMINROLE[$permission]) > 0) ? true : false;
        }
        $key = array_key_first($permission);
        $is_permission = (isset(ADMINROLE[$key]) && in_array($permission[$key], ADMINROLE[$key])) ?  true : false;
        if (!$is_permission && $message != false) $this->message(is_string($message) ? $message : "You can not perfrom this action", "error");
        return $is_permission;
    }

    function defaultRoles(): array
    {
        return json_decode(base64_decode($this->get_settings("roles")) ?? [], true);
    }
}