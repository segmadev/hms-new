<?php
// error_reporting(0);
// ini_set('display_errors', 0);
// if($_SERVER[‘HTTPS’] != "on") {
// $redirect= "https://".$_SERVER[‘HTTP_HOST’].$_SERVER[‘REQUEST_URI’];
// header("Location:$redirect");
// }
// var_dump($_SESSION);
// die("Welcome to admin session");
$redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Build GET params, but ignore 'page' and 'action'
$getParams = '';
if (!empty($_GET)) {
    $filteredGet = $_GET;
    unset($filteredGet['page'], $filteredGet['action']);
    if (!empty($filteredGet)) {
        $getParams = '?' . http_build_query($filteredGet);
    }
}

if (!isset($_SESSION['logTk'])) {
    $_SESSION['urlgoto'] = $redirect;
    echo '<script>window.location.href = "/admin/login' . $getParams . '";</script>';
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['logTk']);
    echo '<script>window.location.href = "/admin/login' . $getParams . '";</script>';
    exit();
}

if (isset($_SESSION['logTk'])) {
    $adminToken = $_SESSION['logTk'];
} else {
    session_destroy();
    echo '<script>window.location.href = "/admin/login' . $getParams . '";</script>';
    exit();
}