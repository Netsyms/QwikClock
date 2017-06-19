<?php

require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('punches', ['uid' => $_SESSION['uid']]);
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 1:
        $order = ["in" => $sortby];
        break;
    case 2:
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
            "uid" => $_SESSION['uid']
        ]
    ];
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["uid" => $_SESSION['uid'], "LIMIT" => [$VARS['start'], $VARS['length']]];
}

if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$punches = $database->select('punches', [
    'in',
    'out',
    'notes'
        ], $where);

for ($i = 0; $i < count($punches); $i++) {
    $punches[$i][0] = "";
    $punches[$i][1] = date(DATETIME_FORMAT, strtotime($punches[$i]['in']));
    if (is_null($punches[$i]['out'])) {
        $punches[$i][2] = lang("na", false);
    } else {
        $punches[$i][2] = date(DATETIME_FORMAT, strtotime($punches[$i]['out']));
    }
    $punches[$i][3] = $punches[$i]['notes'];
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
