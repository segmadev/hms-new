<?php 
    if(isset($_POST['permissions']) && isset($_POST['roleName'])) {
        $permissions = htmlspecialchars($_POST['permissions']);
        $roleName = htmlspecialchars($_POST['roleName']);
        $id = (isset($_POST['ID']) && $_POST['ID'] != "") ? htmlspecialchars($_POST['ID']) : null;
        $r->manage_role($roleName, $permissions, $id);
    }