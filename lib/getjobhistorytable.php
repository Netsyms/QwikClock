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
$showmanaged = (!empty($VARS['show_all']) && $VARS['show_all'] == 1 && (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") || $account_is_admin));
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
    $out['recordsTotal'] = $database->count('job_tracking');
} else {
    $out['recordsTotal'] = $database->count('job_tracking', ['uid' => $managed_uids]);
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
        $order = ["jobname" => $sortby];
        break;
    case 3:
        $order = ["start" => $sortby];
        break;
    case 4:
        $order = ["end" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "AND" => [
            "OR" => [
                "jobname[~]" => $VARS['search']['value'],
                "jobcode[~]" => $VARS['search']['value'],
                "start[~]" => $VARS['search']['value'],
                "end[~]" => $VARS['search']['value'],
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


$jobs = $database->select('job_tracking', ['[>]jobs' => ['jobid']], [
    'id',
    'job_tracking.jobid',
    'uid',
    'start',
    'end',
    'jobname',
    'jobcode',
    'color',
    'deleted'
        ], $where);

$usercache = [];

$editself = account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF");

for ($i = 0; $i < count($jobs); $i++) {
    // Get user info
    if (!isset($usercache[$jobs[$i]['uid']])) {
        $usercache[$jobs[$i]['uid']] = getUserByID($jobs[$i]['uid']);
    }

    $jobs[$i][0] = "";
    if ($_SESSION['uid'] == $jobs[$i]['uid']) {
        if ($editself) {
            $jobs[$i][1] = '<a class="btn btn-blue btn-sm" href="app.php?page=editjobhistory&job=' . $jobs[$i]['id'] . '"><i class="fas fa-edit"></i> ' . lang("edit", false) . '</a>';
        } else {
            $jobs[$i][1] = "";
        }
    } else if ($showmanaged) {
        $jobs[$i][1] = '<a class="btn btn-blue btn-sm" href="app.php?page=editjobhistory&job=' . $jobs[$i]['id'] . '"><i class="fas fa-edit"></i> ' . lang("edit", false) . '</a>';
    } else {
        $jobs[$i][1] = "";
    }
    $jobs[$i][2] = '<span class="badge mr-1 px-2 py-1 badge-' . $jobs[$i]['color'] . '">&nbsp;</span> ' . ($jobs[$i]['deleted'] == 1 ? "<s>" : "") . $jobs[$i]['jobname'] . ($jobs[$i]['deleted'] == 1 ? "</s>" : "");
    $jobs[$i][3] = date(DATETIME_FORMAT, strtotime($jobs[$i]['start']));
    if (is_null($jobs[$i]['end'])) {
        $jobs[$i][4] = lang("na", false);
    } else {
        $jobs[$i][4] = date(DATETIME_FORMAT, strtotime($jobs[$i]['end']));
    }
    $jobs[$i][5] = $usercache[$jobs[$i]['uid']]['name'];
}

$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('job_tracking', ['[>]jobs' => ['jobid']], 'job_tracking.id', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
$out['data'] = $jobs;

echo json_encode($out);
