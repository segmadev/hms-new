<?php
require_once '../consts/main.php';
require_once ROOT.'/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();
require_once 'consts/Regex.php';
require_once ROOT.'/functions/database.php';
$d = new database;
require_once "consts/general.php";
require_once 'content/content.php';
$c = new content;
require_once 'functions/autorize.php';
// require_once 'consts/user.php';
$a = new autorize;