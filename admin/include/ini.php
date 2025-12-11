<?php
session_start();
require_once "../consts/main.php";
require_once ROOT."/admin/include/session.php";
// require_once "consts/Regex.php";
require_once ROOT."/functions/database.php";
$d = new database;
require_once "consts/general.php";
require_once "content/content.php";
$c = new content;
require_once "pages/roles/functions/roles.php";
$r = new roles;
$user_log = $d->getall("user_logs", "token = ? and expiry_date >= ? and status = ?", [$adminToken, time(), 1]);
if (!is_array($user_log)) {
    $d->message("Unable to identify admin or login session expired", "error");
    utilities::loadpage("/admin/login");
    exit();
}

$admin = $d->selectdata("user", "ID = ?", [htmlspecialchars($user_log['userID'])]);
if (!is_array($admin)) {
    $d->message("Unable to identify admin", "error");
    exit();
}
define("ADMINROLE", $r->get_role($admin['roleID']));
// var_dump(ADMINROLE);
define("adminID", $admin['ID']);
if (!is_array($admin)) {
    $d->message("Unable to identify admin", "error");
    $d->loadpage("login");
    exit();
}
$adminID = $admin['ID'];
$script = [];

define("currency", $d->get_settings("default_currency"));