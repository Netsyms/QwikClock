<?php

require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

require_once __DIR__ . "/login.php";
require_once __DIR__ . "/userinfo.php";

$showall = ($VARS['show_all'] == 1); // && account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE"));

$showmanaged = false;
$managed_uids = [];
if ($showmanaged) {
    $managed_uids = getManagedUIDs($_SESSION['uid']);
}
$managed_uids[] = $_SESSION['uid'];

$out = [];

$out['draw'] = intval($VARS['draw']);

if ($showall) {
    $out['recordsTotal'] = $database->count('shifts');
} else {
    $out['recordsTotal'] = $database->count('shifts', ["[>]assigned_shifts" => ["shiftid" => "shiftid"]], 'shiftname', ["uid" => $managed_uids]);
}
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 1:
        $order = ["shiftname" => $sortby];
        break;
    case 2:
        $order = ["start" => $sortby];
        break;
    case 3:
        $order = ["end" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "AND" => [
            "OR" => [
                "shiftname[~]" => $VARS['search']['value'],
                "start[~]" => $VARS['search']['value'],
                "end[~]" => $VARS['search']['value'],
            ]
        ]
    ];
    if (!$showall) {
        $wherenolimit["AND"]["uid"] = $managed_uids;
    }
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["LIMIT" => [$VARS['start'], $VARS['length']]];
    if (!$showall) {
        $where["uid"] = $managed_uids;
    }
}

if (!is_null($order)) {
    $where["ORDER"] = $order;
}


if ($showall) {
    $shifts = $database->select('shifts', [
        'shiftid',
        'shiftname',
        'start',
        'end',
        'days'
            ], $where);
} else {
    $shifts = $database->select('shifts', [
        "[>]assigned_shifts" => [
            "shiftid" => "shiftid"
        ]
            ], [
        'shifts.shiftid',
        'shiftname',
        'start',
        'end',
        'days'
            ], $where);
}

$showeditbtn = account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE");

for ($i = 0; $i < count($shifts); $i++) {
    $shifts[$i][0] = "";
    if ($showeditbtn) {
        $shifts[$i][1] = '<a class="btn btn-blue btn-xs" href="app.php?page=editshift&id=' . $shifts[$i]['shiftid'] . '"><i class="fa fa-pencil-square-o"></i> ' . lang("edit", false) . '</a>';
    } else {
        $shifts[$i][1] = "";
    }
    $shifts[$i][2] = $shifts[$i]['shiftname'];
    $shifts[$i][3] = date(TIME_FORMAT, strtotime($shifts[$i]['start']));
    $shifts[$i][4] = date(TIME_FORMAT, strtotime($shifts[$i]['end']));
    $days = [];
    $daycodes = str_split($shifts[$i]['days'], 2);
    foreach ($daycodes as $day) {
        switch ($day) {
            case "Su":
                $days[] = lang("sunday", false);
                break;
            case "Mo":
                $days[] = lang("monday", false);
                break;
            case "Tu":
                $days[] = lang("tuesday", false);
                break;
            case "We":
                $days[] = lang("wednesday", false);
                break;
            case "Th":
                $days[] = lang("thursday", false);
                break;
            case "Fr":
                $days[] = lang("friday", false);
                break;
            case "Sa":
                $days[] = lang("saturday", false);
                break;
        }
    }
    $shifts[$i][5] = "<span style=\"word-wrap: break-word;\">" . implode(", ", $days) . "</span>";
}

$out['status'] = "OK";
if ($filter) {
    if ($showall) {
        $recordsFiltered = $database->count('shifts', $wherenolimit);
    } else {
        $recordsFiltered = count($shifts);
    }
} else {
    $recordsFiltered = $out['recordsTotal'];
}

$out['recordsFiltered'] = $recordsFiltered;
$out['data'] = $shifts;

echo json_encode($out);
