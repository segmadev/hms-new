<?php
if (isset($_GET['action'])) {
    $route = htmlspecialchars($_GET['action']);
}

$accepted_actions[] = "about";
$accepted_actions[] = "api";
$accepted_actions[] = "backup";

if(isset($_GET['id'])) {
    $id = htmlspecialchars($_GET['id']);
    $data = $d->getall("settings", "ID = ?", [$id], fetch: "details");
}

if($action == "backup") {
    require_once "functions/backup.php";
    $bk = new backup;
}