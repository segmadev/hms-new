<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
// Parse and clean the URL path
$cleanPath = rtrim(parse_url($_SERVER['REQUEST_URI'])['path'], '/'); // Remove trailing slash
define("ISAPI", true);
// Define PATH without GET parameters
define("PATH", str_replace("//", "/", str_replace("/api", "", $cleanPath)));
// Parse the query string into $_GET if present
if (!empty($parsedUrl['query'])) {
    parse_str($parsedUrl['query'], $_GET);
}
// Load required files
require_once "../consts/main.php";
require_once ROOT."functions/database.php";
require_once ROOT."api/functions/router.php";
require_once ROOT."functions/helper.php";