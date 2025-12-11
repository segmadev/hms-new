<?php 
session_start();
    require_once "include/auth-ini.php";
    if(isset($_POST['register'])) {
        echo $a->signup($user_form);
    }

    if(isset($_POST['signin'])) {
        echo $a->signin();
    }

    if(isset($_POST['forget_password'])) {
        echo $a->sendotp();
    }
    if(isset($_POST['reset_password'])) {
        echo $a->resetpassword($reset_form);
    }