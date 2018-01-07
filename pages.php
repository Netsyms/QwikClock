<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


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
        "title" => "punches",
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
    "jobs" => [
        "title" => "jobs",
        "navbar" => true,
        "icon" => "briefcase",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/jobs.js"
        ]
    ],
    "editjobs" => [
        "title" => "jobs",
        "navbar" => false,
        "icon" => "briefcase",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/editjobs.js"
        ]
    ],
    "editjob" => [
        "title" => "edit job",
        "navbar" => false,
        "scripts" => [
            "static/js/editjob.js"
        ]
    ],
    "editjobhistory" => [
        "title" => "edit job",
        "navbar" => false,
        "styles" => [
            "static/css/bootstrap-datetimepicker.min.css",
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/moment.min.js",
            "static/js/bootstrap-datetimepicker.min.js",
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/editjobhistory.js"
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
    "editpunch" => [
        "title" => "edit punch",
        "navbar" => false,
        "styles" => [
            "static/css/bootstrap-datetimepicker.min.css",
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/moment.min.js",
            "static/js/bootstrap-datetimepicker.min.js",
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/editpunch.js"
        ]
    ],
    "export" => [
        "title" => "reports",
        "navbar" => true,
        "icon" => "download",
        "styles" => [
            "static/css/bootstrap-datetimepicker.min.css",
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/moment.min.js",
            "static/js/bootstrap-datetimepicker.min.js",
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/export.js"
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
