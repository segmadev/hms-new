<?php
$user_form = [
    "ID" => ["input_type" => "hidden", "is_required" => false],
    "first_name" => ["global_class"=>"col-sm-6"],
    "last_name" => ["global_class"=>"col-sm-6"],
    "email" => ["input_type" => "email", "global_class"=>"col-12 col-md-12 col-sm-12"],
    "phone_number" => ["input_type" => "number", "global_class"=>"col-12 col-md-12 col-sm-12"],
    "gender" => ["global_class"=>"col-12 col-md-12 col-sm-12", "placeholder" => "Select your gender", "is_required" => true, "options" => ["Male" => "Male", "Female" => "Female"], "type" => "select"],
    "country" => ["global_class"=>"col-12 col-md-12 col-sm-12", "type" => "select", "options" => $d->options_list("countries")],
    "password" => ["global_class"=>"col-12 col-md-12 col-sm-12", "input_type" => "password", "validate"=>true],
    "confirm_password" => ["global_class"=>"col-12 col-md-12 col-sm-12", "input_type" => "password"],
    "Referral_code" => ["global_class"=>"col-12 col-md-12 col-sm-12", "placeholder" => "UX920", "is_required" => false,],
    // "input_data" => ["full_name" => "", "gender"=>"Male"],
];