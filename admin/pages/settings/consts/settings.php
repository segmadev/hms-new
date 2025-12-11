<?php
// $logo_path = ROOTFILE."assets/images/logos/";
$logo_path = "../assets/images/logos/";
$logo_from = [
    "light_logo" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
    "dark_logo" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
    "favicon" => ["input_type" => "file", "is_required" => false, "path" => $logo_path],
];
$logo_from['input_data'] = $s->getdata($logo_from);
// about company
$about = [
    "about_company"=>["type"=>"textarea", "atb"=>"data-textarea=''", "global_class"=>"w-100"]
];
$about['input_data'] = $s->getdata($about);

$settings_form = [
    "company_name" => [],
    "website_url" => [],
    "support_email" => ["input_type" => "email"],
    "phone_number" => ["input_ype" => "tel"],
    "company_address" => ["type" => "textarea"],
    "default_currency" => [],
    "live_chat_widget" => ["type" => "textarea", "global_class" => "w-100", "is_required"=>false],
];
$settings_form['input_data'] = $s->getdata($settings_form);
$settings_form['input_data']['tiny_API'] = "--placeholder";
$settings_backup = [
    "dropbox_API" => ["input_type" => "password", "is_required" => false],
    "backup_interval" => ["input_type" => "number", "title" => "Backup Interval (in hour)", "description" => "Backup Interval in hour"],
];
$settings_backup['input_data'] = $s->getdata($settings_backup);
$settings_backup['input_data']['dropbox_API'] = "--placeholder";
$s->create_settings($settings_backup);

$settings_social_media = [
    "telegram_link" => ["is_required" => false],
    "facebook_link" => ["is_required" => false],
    "instagram_link" => ["is_required" => false],
    "x_link" => ["is_required" => false],
    "tiktok_link" => ["is_required" => false],
];

$settings_social_media['input_data'] = $s->getdata($settings_social_media);
$settings_seo = [
    "seo_title" => ["is_required" => false],
    "seo_description" => ["is_required" => false],
    "seo_tags" => ["is_required" => false],
];

$settings_seo['input_data'] = $s->getdata($settings_seo);

// var_dump($settings_form);
$settings_deposit_form = [
    "stripe_public" => ["input_type" => "password", "is_required" => false],
    "stripe_private" => ["input_type" => "password", "is_required" => false],
    "input_data" => array_merge(["stripe_private" => "--placeholder", "stripe_public" => "--placeholder"]),
];

$term_and_policy_condition = [
    "terms_and_conditions" => ["type" => "textarea", "id" => "richtext", "global_class" => "col-md-12"],
    "policy" => ["type" => "textarea", "id" => "richtext2", "global_class" => "col-md-12"],
];
$term_and_policy_condition['input_data'] = $s->getdata($term_and_policy_condition);