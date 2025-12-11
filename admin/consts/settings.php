<?php
// $logo_path = ROOTFILE."assets/images/logos/";
$logo_path = "../assets/images/logos/";
$logo_from = [
    "light_logo" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
    "dark_logo" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
    "favicon" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
];
$logo_from['input_data'] = $s->getdata($logo_from);
$settings_form = [
    "company_name" => [],
    "website_url" => [],
    "tiny_API" => ["is_required" => false, "input_type" => "password"],
    "account_preview" => ["type" => "select", "Title" => "Allow account preview", "options" => ["1" => "Yes", "0" => "No"]],
    "support_email" => ["input_type" => "email"],
    "phone_number" => ["input_ype" => "tel"],
    "company_address" => ["type" => "textarea"],
    "default_currency" => [],
    "welcome_note" => ["type" => "textarea", "description" => "Welcome note will display to new users who login to dashboard for the first time.", "global_class" => "w-100"],
    "live_chat_widget" => ["type" => "textarea", "global_class" => "w-100"],
];

$admin = $d->getall("admins", "ID = ?", [$adminID]);
if (!is_array($admin)) {
    exit();
}
$admin_account = [
    "email" => ["input_type" => "email"],
    "current_password" => ["input_type" => "password"],
    "password" => ["title" => "Change Password", "input_type" => "password", "is_required" => false],
    "confirm_password" => ["title" => "Re-type Change Password", "input_type" => "password", "is_required" => false],
    "input_data" => ["email" => $admin['email']],
];
