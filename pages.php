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
        "title" => "history",
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
]);
