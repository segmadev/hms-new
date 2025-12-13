<?php 
    $staff_from = [
        "ID"=>["type"=>"hidden"],
        "name"=>[
            "value"=>"Segma",
            "description"=>"Make sure to enter your first name and last name.",
            "class"=>"bg-sucess",
            "placeholder"=>"John Doe",
            "title"=>"Full Name"
        ],
        "email"=>[
            "input_type"=>"email"
        ],
        "role"=>[
            "type"=>"select",
            "options"=>$d->options_list("roles", "ID", "name"),
        ],
        "department"=>[
            "type"=>"select",
            "options"=>$d->options_list("departments", "department_id", "department_name"),
        ],
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

    // $d->create_table("staff2", $staff_from, true);
?>