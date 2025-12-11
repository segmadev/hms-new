<?php
// session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
if (isset($_POST['page'])) $pageexclude = "yes";
require_once "include/ini.php";
if (isset($_POST['page'])) {
    $page = htmlspecialchars($_POST['page']);
    require_once "pages/page-ini.php";
}

$_POST['adminID'] = $adminID;
if (file_exists("pages/$page/passer.php")) {
    require_once "pages/$page/passer.php";
}
// change profile pic
if (isset($_POST['change_profile_pic'])) {
    echo $u->change_profile_pic($adminID);
}

// change password
if (isset($_POST['change_password'])) {
    echo $u->change_password($change_password_from, $adminID);
}
// update profile
// if (isset($_POST['update_profile'])) {
//     echo $u->update_profile($profile_form, $adminID);
// }