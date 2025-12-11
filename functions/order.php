<?php 
  class order extends users {
    private $order_form;
    function __construct(){
        parent::__construct();
        $this->order_form = [
            "size"=>["input_type"=>"number"],
            "quantity"=>[],
        ];

    }
    function make_order() {
        
    }
  }