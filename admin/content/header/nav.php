<?php

$navs = [
    "home" => [
        "title" => "Home",
        "links" => [
            "dashboard" => [
                "home" => [
                    "a" => "dashboard",
                    "title" => "Dashboard",
                ],
                // SVG icon for dashboard (gray)
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="11" width="4" height="6" rx="1" fill="#888"/><rect x="8" y="3" width="4" height="14" rx="1" fill="#888"/><rect x="13" y="7" width="4" height="10" rx="1" fill="#888"/></svg>'
            ],
        ]
    ],
   
    "Management" => [
        "title" => "Management", 
        "links" => [
            "users" => [
                // "new" => [
                //     "a" => "users/new",
                //     "title" => "Add New User",
                // ],
                "home" => [
                    "a" => "users", 
                    "title" => "Manage users", 
                ],
                // SVG icon for users (gray)
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="7" r="4" fill="#888"/><rect x="3" y="13" width="14" height="4" rx="2" fill="#888"/></svg>'
            ],

            

            "profile" => [
                "edit" => [
                    "a" => "profile/edit",
                    "title" => "Edit profile",
                ],
                "icon" => "custom-user"
            ],

            "products" => [
                // "new" => [
                //     "a" => "products/new",
                //     "title" => "Add New Product",
                // ],
                "home" => [
                    "a" => "products",
                    "title" => "Manage Products", 
                ],
                // SVG icon for products (gray)
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="14" height="10" rx="2" fill="#888"/><rect x="6" y="8" width="8" height="1.5" rx="0.75" fill="#fff"/><rect x="6" y="11" width="5" height="1.5" rx="0.75" fill="#fff"/></svg>'
            ],

            "shipping_methods" => [
                "new" => [
                    "a" => "shipping_methods/new",
                    "title" => "Add Shipping Method",
                ],
                "home" => [
                    "a" => "shipping_methods",
                    "title" => "Manage Shipping Methods",
                ],
                // SVG icon for shipping methods
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="7" width="16" height="6" rx="2" fill="#888"/><rect x="4" y="9" width="2" height="2" rx="1" fill="#fff"/><rect x="14" y="9" width="2" height="2" rx="1" fill="#fff"/></svg>'
            ],

            // "categories" => [
            //     "new" => [
            //         "a" => "categories/new",
            //         "title" => "Add New Category",
            //     ],
            //     "home" => [
            //         "a" => "categories",
            //         "title" => "Manage Categories",
            //     ],
            //     "icon" => "custom-category"
            // ],

            // "roles" => [
            //     "new" => [
            //         "a" => "roles/new",
            //         "title" => "Create",
            //     ],
            //     "home" => [
            //         "a" => "roles",
            //         "title" => "Manage",
            //     ],
            //     "icon" => "custom-data"
            // ],

            "orders" => [
                "home" => [
                    "a" => "orders",
                    "title" => "View Orders",
                ],
                // SVG icon for orders
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="14" height="10" rx="2" fill="#888"/><path d="M6 8h8v1H6zM6 11h5v1H6z" fill="#fff"/></svg>'
            ],

            "payments" => [
                "home" => [
                    "a" => "payments",
                    "title" => "Payment History",
                ],
                // SVG icon for payments
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="6" width="16" height="8" rx="2" fill="#888"/><circle cx="10" cy="10" r="3" fill="#fff"/><rect x="13" y="8" width="3" height="1.5" rx="0.75" fill="#2196F3"/></svg>'
            ],
            // "admins" => [
            //     "new" => [
            //         "a" => "admins/new",
            //         "title" => "Add New Admin",
            //     ],
            //     "home" => [
            //         "a" => "admins",
            //         "title" => "Manage Admins",
            //     ],
            //     "icon" => "custom-profile-2user-outline"
            // ],
        ]
    ],

    "Settings" => [
        "title" => "Settings",
        "links" => [
            "settings" => [
                "home" => [
                    "a" => "settings",
                    "title" => "General Settings",
                ],
                // SVG icon for settings
                "icon" => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="7" stroke="none" stroke-width="2" fill="#888"/><path d="M10 6v4l3 2" stroke="#ffff" stroke-width="2" stroke-linecap="round"/></svg>'
            ],
            // "roles" => [
            //     "home" => [
            //         "a" => "roles",
            //         "title" => "Manage Roles",
            //     ],
            //     "new" => [
            //         "a" => "roles/new",
            //         "title" => "Add New Role",
            //     ],
            // ]
        ]
    ],

    // "Content" => [
    //     "title" => "Content",
    //     "links" => [
    //         "content" => [
    //             "home" => [
    //                 "a" => "content/home",
    //                 "title" => "Homepage Content",
    //             ],
    //             "features" => [
    //                 "a" => "content/features",
    //                 "title" => "Features Content",
    //             ],
    //             "testimonials" => [
    //                 "a" => "content/testimonials",
    //                 "title" => "Testimonials",
    //             ],
    //             "icon" => "custom-edit"
    //         ],

    //         "notifications" => [
    //             "new" => [
    //                 "a" => "notifications/new",
    //                 "title" => "Send Notification",
    //             ],
    //             "home" => [
    //                 "a" => "notifications",
    //                 "title" => "View Notifications",
    //             ],
    //             "icon" => "custom-notification"
    //         ]
    //     ]
    // ]
];

$c->generateNavigation($navs, $r);