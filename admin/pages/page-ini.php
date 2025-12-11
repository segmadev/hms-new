<?php
// require_once "consts/Regex.php";
if (!isset($page)) {
    $page = $_GET['page'] ?? "dashboard";
}
$accepted_actions = ["home", "list", "view", "add", "edit", "new", "create"];
$action = "home";
if (isset($_GET['action'])) {
    $action = htmlspecialchars($_GET['action']);
}
if (!isset($pageexclude)) {
    $pageexclude = "no";
}
define("PAGEROOT", "pages/$page/");
if (file_exists(PAGEROOT . "css/style.css") && $pageexclude != "yes") {
    echo "<link rel='stylesheet' href='" . PAGEROOT . "css/style.css?n=90'>";
}
// var_dump($_GET);
// echo $page;
try {
    if (file_exists("pages/$page/functions/" . $page . ".php")) {
        require_once "pages/$page/functions/" . $page . ".php";
        ${substr($page, 0, 1)."_"} = new $page;
        if (!isset(${substr($page, 0, 1)})) {
            ${substr($page, 0, 1)} = new $page;
        } else {
            ${substr($page, 0, 2)} = new $page;
        }
    }
    if (file_exists("pages/" . $page . "/ini.php")) {
        require_once "pages/" . $page . "/ini.php";
    }
    if (file_exists("pages/$page/consts/$page" . ".php")) {
        require_once "pages/$page/consts/$page" . ".php";
    }
    
    if (in_array($action, $accepted_actions) && file_exists("pages/" . $page . "/" . $action . ".php") && $pageexclude != "yes") {
        require_once "pages/" . $page . "/" . $action . ".php";
    }
} catch (\Throwable $th) {
    echo $th;
}


if (file_exists(PAGEROOT . "js/main.js") && $pageexclude != "yes") {
    echo "<script src='" . PAGEROOT . "js/main.js?n=90'></script>";
}