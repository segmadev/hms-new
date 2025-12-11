<?php
handleAdminRouting();
if (!isset($page)) {
    $page = $_GET['page'] ?? "dashboard";
}
$page = str_replace(".php", "", $page);
$exept = ["login", "join", "passer", "auth", "callback", "modal", "cron-job"];
if (in_array("$page", $exept) && file_exists("$page.php")) {

    require_once "$page.php";
    exit();
}

function handleAdminRouting()
{
    // Get the current request URI
    $requestUri = $_SERVER['REQUEST_URI'];

    // Remove the `/admin/` prefix from the URI
    $baseUri = str_replace('/admin/', '', $requestUri);

    // Parse the URI into segments
    $segments = explode('/', trim($baseUri, '/'));

    // Default parameters
    $page = $segments[0] ?? null;
    $action = $segments[1] ?? null;
    $id = $segments[2] ?? null;

    // Build the query string for the index.php file
    $queryString = [];
    if ($page) {
        $_GET['page'] = $page;
    }
    if ($action) {
        $_GET['action'] = $action;
    }
    if ($id) {
        $_GET['ID'] = $id;
    }
}