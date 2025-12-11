<?php 
    $staff_from = [
        "name"=>[
            "value"=>"Segma",
            "description"=>"Make sure to enter your first name and last name."
        ],
        "email"=>[
            "input_type"=>"email"
        ],
        "role"=>[
            "type"=>"select",
            "options"=>["main"=>"role 1", "admin"=>"role 2"],
        ],
        "department"=>[],
        "contact"=>[
            "is_required"=>false, 
            "placeholder"=>"+23485755857",
            "input_type"=>"tel"
        ],
        "salary"=>[],
        "statuss"=>[],
        "input_data"=>[
            "email"=>"seriki@gmail.com"
        ]
    ];
?>