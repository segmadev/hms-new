<?php 
require_once ROOT."functions/helper.php";
    class users  {
       public $user_details;
    function __construct() {
        $user_details = $this->get_user();
        $user_form = [
            "first_name"=>[],
            "last_name"=>[],
            "email"=>[],
            "phone_number"=>[],
            "password"=>[],
            "confirm_password"=>[]
        ];

        $shipping_form = [
            "address"=>[],
            "city"=>[],
            "zipcode"=>[],
            
        ];
    }

    function get_user() {
        
    }
    }