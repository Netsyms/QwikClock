<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

require_once __DIR__ . "/login.php";
require_once __DIR__ . "/userinfo.php";

$account_is_admin = account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN");
$showmanaged = ($VARS['show_all'] == 1 && (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") || $account_is_admin));
$managed_uids = [];
$managed_uids[] = $_SESSION['uid'];
if ($showmanaged) {
    if ($account_is_admin) {
        $managed_uids = false;
    } else {
        $managed_uids = getManagedUIDs($_SESSION['uid']);
        $managed_uids[] = $_SESSION['uid'];
    }
}

$out = [];

$out['draw'] = intval($VARS['draw']);

if ($managed_uids === false) {
    $out['recordsTotal'] = $database->count('punches');
} else {
    $out['recordsTotal'] = $database->count('punches', ['uid' => $managed_uids]);
}

$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 2:
        $order = ["uid" => $sortby];
        break;
    case 3:
        $order = ["in" => $sortby];
        break;
    case 4:
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
    if ($managed_uids !== false) {
        $where["AND"]["uid"] = $managed_uids;
    }
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["LIMIT" => [$VARS['start'], $VARS['length']]];
    if ($managed_uids !== false) {
        $where["uid"] = $managed_uids;
    }
}

if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$punches = $database->select('punches', [
    'punchid',
    'uid',
    'in',
    'out',
    'notes'
        ], $where);

$usercache = [];

$editself = account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF");

for ($i = 0; $i < count($punches); $i++) {
    // Get user info
    if (!isset($usercache[$punches[$i]['uid']])) {
        $usercache[$punches[$i]['uid']] = getUserByID($punches[$i]['uid']);
    }

    $punches[$i][0] = "";
    if ($_SESSION['uid'] == $punches[$i]['uid']) {
        if ($editself) {
            $punches[$i][1] = '<a class="btn btn-blue btn-sm" href="app.php?page=editpunch&pid=' . $punches[$i]['punchid'] . '"><i class="fas fa-edit"></i> ' . lang("edit", false) . '</a>';
        } else {
            $punches[$i][1] = "";
        }
    } else if ($showmanaged) {
        $punches[$i][1] = '<a class="btn btn-blue btn-sm" href="app.php?page=editpunch&pid=' . $punches[$i]['punchid'] . '"><i class="fas fa-edit"></i> ' . lang("edit", false) . '</a>';
    } else {
        $punches[$i][1] = "";
    }
    $punches[$i][2] = $usercache[$punches[$i]['uid']]['name'];
    $punches[$i][3] = date(DATETIME_FORMAT, strtotime($punches[$i]['in']));
    if (is_null($punches[$i]['out'])) {
        $punches[$i][4] = lang("na", false);
    } else {
        $punches[$i][4] = date(DATETIME_FORMAT, strtotime($punches[$i]['out']));
    }
    $punches[$i][5] = $punches[$i]['notes'];
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
