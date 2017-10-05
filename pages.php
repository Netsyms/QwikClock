<?php

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "home",
        "scripts" => [
            "static/js/home.js"
        ]
    ],
    "404" => [
        "title" => "404 error"
    ],
    "punches" => [
        "title" => "punch card",
        "navbar" => true,
        "icon" => "clock-o",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/punches.js"
        ]
    ],
    "shifts" => [
        "title" => "shifts",
        "navbar" => true,
        "icon" => "calendar",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/shifts.js"
        ]
    ],
    "editshift" => [
        "title" => "new shift",
        "navbar" => false,
        "styles" => [
            "static/css/bootstrap-datetimepicker.min.css"
        ],
        "scripts" => [
            "static/js/moment.min.js",
            "static/js/bootstrap-datetimepicker.min.js",
            "static/js/addshift.js"
        ]
    ],
    "assignshift" => [
        "title" => "assign shift",
        "navbar" => false,
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/jquery.color-2.1.2.min.js",
            "static/js/assignshift.js"
        ]
    ],
]);
