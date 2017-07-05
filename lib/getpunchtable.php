<?php

require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

require_once __DIR__ . "/login.php";
require_once __DIR__ . "/userinfo.php";

$showmanaged = ($VARS['show_all'] == 1 && account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE"));
$managed_uids = [];
if ($showmanaged) {
    $managed_uids = getManagedUIDs($_SESSION['uid']);
}
$managed_uids[] = $_SESSION['uid'];

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('punches', ['uid' => $managed_uids]);
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 1:
        $order = ["uid" => $sortby];
        break;
    case 2:
        $order = ["in" => $sortby];
        break;
    case 3:
        $order = ["out" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "AND" => [
            "OR" => [
                "in[~]" => $VARS['search']['value'],
                "out[~]" => $VARS['search']['value']
            ],
            "uid" => $managed_uids
        ]
    ];
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["uid" => $managed_uids, "LIMIT" => [$VARS['start'], $VARS['length']]];
}

if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$punches = $database->select('punches', [
    'uid',
    'in',
    'out',
    'notes'
        ], $where);

$usercache = [];

for ($i = 0; $i < count($punches); $i++) {
    // Get user info
    if (!isset($usercache[$punches[$i]['uid']])) {
        $usercache[$punches[$i]['uid']] = getUserByID($punches[$i]['uid']);
    }
    
    $punches[$i][0] = "";
    $punches[$i][1] = $usercache[$punches[$i]['uid']]['name'];
    $punches[$i][2] = date(DATETIME_FORMAT, strtotime($punches[$i]['in']));
    if (is_null($punches[$i]['out'])) {
        $punches[$i][3] = lang("na", false);
    } else {
        $punches[$i][3] = date(DATETIME_FORMAT, strtotime($punches[$i]['out']));
    }
    $punches[$i][4] = $punches[$i]['notes'];
}

$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('punches', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
$out['data'] = $punches;

echo json_encode($out);
