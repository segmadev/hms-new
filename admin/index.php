<?php
// Define a base path for easier reference
$pageexclude = "no";
require_once "pages/exemptions.php";
require_once "include/ini.php";
require_once "content/header/header.php";
require_once "pages/page-ini.php";
require_once "content/footer/footer.php";
if (file_exists(  "js/main.js")) {
    echo "<script src='js/main.js'></script>";
}