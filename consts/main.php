<?php
define("Regex", "");
define("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/");
require_once ROOT . "functions/utilities.php";
if($_ENV['APP_DEBUG'] && $_ENV['APP_ENV'] == "local"){
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}