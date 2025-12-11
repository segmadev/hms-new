<?php
$defaultRoles = $r->defaultRoles();
if (isset($_GET['ID'])) {
    $role = $r->getall("roles", "ID = ?", [htmlspecialchars($_GET['ID'])]);
    $rolePermissions = $r->get_role(htmlspecialchars($_GET['ID']));
}
if ($action == "home") {
    $roles = $r->getall("roles", "ID != ? order by date DESC", [""], fetch: "all");
}
