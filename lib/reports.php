<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


// Detect if loaded by the user or by PHP
if (count(get_included_files()) == 1) {
    define("LOADED", true);
} else {
    define("LOADED", false);
}

require_once __DIR__ . "/../required.php";

use League\Csv\Writer;
use League\Csv\HTMLConverter;
use odsPhpGenerator\ods;
use odsPhpGenerator\odsTable;
use odsPhpGenerator\odsTableRow;
use odsPhpGenerator\odsTableColumn;
use odsPhpGenerator\odsTableCellString;
use odsPhpGenerator\odsStyleTableColumn;
use odsPhpGenerator\odsStyleTableCell;

require_once __DIR__ . "/userinfo.php";
require_once __DIR__ . "/login.php";

// Allow access with a download code, for mobile app and stuff
$date = date("Y-m-d H:i:s");
$allowed_users = [];
$requester = -1;
if (isset($VARS['code']) && LOADED) {
    if (!$database->has('report_access_codes', ["AND" => ['code' => $VARS['code'], 'expires[>]' => $date]])) {
        dieifnotloggedin();
        $requester = $_SESSION['uid'];
    } else {
        $requester = $database->get('report_access_codes', 'uid', ['code' => $VARS['code']]);
    }
} else {
    dieifnotloggedin();
    $requester = $_SESSION['uid'];
}

if (account_has_permission($_SESSION['username'], "ADMIN")) {
    $allowed_users = true;
} else {
    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
        $allowed_users = getManagedUIDs($requester);
    }

    if (account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF")) {
        $allowed_users[] = $_SESSION['uid'];
    }
}

// Delete old DB entries
$database->delete('report_access_codes', ['expires[<=]' => $date]);

if (LOADED) {
    $user = null;
    if ($VARS['users'] != "all" && !is_empty($VARS['user']) && user_exists($VARS['user'])) {
        $user = getUserByUsername($VARS['user']);
    }
    if (isset($VARS['type']) && isset($VARS['format'])) {
        generateReport($VARS['type'], $VARS['format'], $user, $VARS['startdate'], $VARS['enddate']);
        die();
    } else {
        lang("invalid parameters");
        die();
    }
}

function getShiftReport($user = null) {
    global $database;
    global $allowed_users;
    if ($user != null && array_key_exists('uid', $user)) {
        $uid = -1;
        if ($allowed_users === true || in_array($user['uid'], $allowed_users)) {
            $uid = $user['uid'];
        }
        $shifts = $database->select(
                "shifts", [
            "[>]assigned_shifts" => ["shiftid" => "shiftid"]
                ], [
            "shifts.shiftid", "shiftname", "start", "end", "days"
                ], [
            "uid" => $uid
                ]
        );
    } else {
        $shifts = $database->select(
                "shifts", [
            "shiftid", "shiftname", "start", "end", "days"
                ]
        );
    }
    $header = [lang("shiftid", false), lang("shift name", false), lang("start", false), lang("end", false), lang("workers", false), lang("sunday", false), lang("monday", false), lang("tuesday", false), lang("wednesday", false), lang("thursday", false), lang("friday", false), lang("saturday", false)];
    $out = [$header];
    for ($i = 0; $i < count($shifts); $i++) {
        $daycodes = str_split($shifts[$i]['days'], 2);
        $assigned = $database->count("assigned_shifts", ['shiftid' => $shifts[$i]["shiftid"]]);
        $out[] = [
            $shifts[$i]["shiftid"],
            $shifts[$i]["shiftname"],
            date(TIME_FORMAT, strtotime($shifts[$i]['start'])),
            date(TIME_FORMAT, strtotime($shifts[$i]['end'])),
            $assigned . "",
            (in_array("Su", $daycodes) == true ? "Y" : "N"),
            (in_array("Mo", $daycodes) == true ? "Y" : "N"),
            (in_array("Tu", $daycodes) == true ? "Y" : "N"),
            (in_array("We", $daycodes) == true ? "Y" : "N"),
            (in_array("Th", $daycodes) == true ? "Y" : "N"),
            (in_array("Fr", $daycodes) == true ? "Y" : "N"),
            (in_array("Sa", $daycodes) == true ? "Y" : "N")
        ];
    }
    return $out;
}

function getPunchReport($user = null, $start = null, $end = null) {
    global $database;
    global $allowed_users;
    $where = [];
    if ((bool) strtotime($start) == TRUE) {
        $where["OR #start"] = [
            "in[>=]" => date("Y-m-d", strtotime($start)),
            "out[>=]" => date("Y-m-d", strtotime($start))
        ];
    }
    if ((bool) strtotime($end) == TRUE) {
        // Make the date be the end of the day, not the start
        $where["in[<=]"] = date("Y-m-d", strtotime($end)) . " 23:59:59";
    }
    if ($user != null && array_key_exists('uid', $user) && ($allowed_users === true || in_array($user['uid'], $allowed_users))) {
        $where["uid"] = $user['uid'];
    } else if ($user != null && array_key_exists('uid', $user) && $allowed_users !== true && !in_array($user['uid'], $allowed_users)) {
        $where["uid"] = -1;
    } else {
        if ($allowed_users !== true) {
            $where["uid"] = $allowed_users;
        }
    }
    if (count($where) > 1) {
        $where = ["AND" => $where];
    }
    $punches = $database->select(
            "punches", [
        "[>]shifts" => ["shiftid" => "shiftid"]
            ], [
        "punchid", "uid", "in", "out", "notes", "punches.shiftid", "shiftname"
            ], $where
    );
    $header = [lang("punchid", false), lang("name", false), lang("in", false), lang("out", false), lang("shiftid", false), lang("shiftname", false), lang("notes", false)];
    $out = [$header];
    $usercache = [];
    for ($i = 0; $i < count($punches); $i++) {
        if (!array_key_exists($punches[$i]["uid"], $usercache)) {
            $usercache[$punches[$i]["uid"]] = getUserByID($punches[$i]["uid"]);
        }
        $out[] = [
            $punches[$i]["punchid"],
            $usercache[$punches[$i]["uid"]]["name"] . " (" . $usercache[$punches[$i]["uid"]]["username"] . ")",
            date(DATETIME_FORMAT, strtotime($punches[$i]['in'])),
            (is_null($punches[$i]['out']) ? "" : date(DATETIME_FORMAT, strtotime($punches[$i]['out']))),
            $punches[$i]['shiftid'],
            $punches[$i]['shiftname'],
            $punches[$i]['notes']
        ];
    }
    return $out;
}

function getTotalsReport($user = null, $start = null, $end = null) {
    global $database;
    global $allowed_users;
    $where = [];
    if ((bool) strtotime($start) == TRUE) {
        $where["OR #start"] = [
            "in[>=]" => date("Y-m-d", strtotime($start)),
            "out[>=]" => date("Y-m-d", strtotime($start))
        ];
    }
    if ((bool) strtotime($end) == TRUE) {
        // Make the date be the end of the day, not the start
        $where["in[<=]"] = date("Y-m-d", strtotime($end)) . " 23:59:59";
    }
    if ($user != null && array_key_exists('uid', $user) && ($allowed_users === true || in_array($user['uid'], $allowed_users))) {
        $where["uid"] = $user['uid'];
    } else if ($user != null && array_key_exists('uid', $user) && $allowed_users !== true && !in_array($user['uid'], $allowed_users)) {
        $where["uid"] = -1;
    } else {
        if ($allowed_users !== true) {
            $where["uid"] = $allowed_users;
        }
    }
    if (count($where) > 1) {
        $where = ["AND" => $where];
    }
    $punches = $database->select(
            "punches", [
        "punchid", "uid", "in", "out"
            ], $where
    );
    $header = [lang("name", false), lang("punches", false), lang("hours:minutes", false), lang("hours", false)];
    $out = [$header];
    $usercache = [];
    $totalseconds = [];
    $totalpunches = [];
    for ($i = 0; $i < count($punches); $i++) {
        if (!array_key_exists($punches[$i]["uid"], $usercache)) {
            $usercache[$punches[$i]["uid"]] = getUserByID($punches[$i]["uid"]);
        }
        if (!array_key_exists($punches[$i]["uid"], $totalseconds)) {
            $totalseconds[$punches[$i]["uid"]] = 0.0;
            $totalpunches[$punches[$i]["uid"]] = 0;
        }
        $insec = strtotime($punches[$i]["in"]);
        if (is_null($punches[$i]["out"])) {
            $outsec = time();
        } else {
            $outsec = strtotime($punches[$i]["out"]);
        }
        $totalseconds[$punches[$i]["uid"]] += $outsec - $insec;
        $totalpunches[$punches[$i]["uid"]] += 1;
    }

    foreach ($totalseconds as $uid => $sec) {
        if (!array_key_exists($uid, $usercache)) {
            $usercache[$uid] = getUserByID($uid);
        }
        $hhmm = floor($sec / 3600) . ":" . str_pad(floor(($sec / 60) % 60), 2, "0", STR_PAD_LEFT);
        $out[] = [
            $usercache[$uid]["name"] . " (" . $usercache[$uid]["username"] . ")",
            $totalpunches[$uid] . "",
            $hhmm,
            round($sec / 60.0 / 60.0, 4) . ""
        ];
    }
    return $out;
}

function getReportData($type, $user = null, $start = null, $end = null) {
    switch ($type) {
        case "shifts":
            return getShiftReport($user);
            break;
        case "punches":
            return getPunchReport($user, $start, $end);
            break;
        case "totals":
            return getTotalsReport($user, $start, $end);
            break;
        default:
            return [["error"]];
    }
}

function dataToCSV($data, $name = "report", $user = null, $start = null, $end = null) {
    $csv = Writer::createFromString('');
    $usernotice = "";
    $usertitle = "";
    $datetitle = "";
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = lang2("report filtered to user", ["name" => $user['name'], "username" => $user['username']], false);
        $usertitle = "_" . $user['username'];
        $csv->insertOne([$usernotice]);
    }
    if ($start != null && (bool) strtotime($start)) {
        $datenotice = lang2("report filtered to start date", ["date" => date(DATE_FORMAT, strtotime($start))], false);
        $datetitle = "_" . date(DATE_FORMAT, strtotime($start));
        $csv->insertOne([$datenotice]);
    }
    if ($end != null && (bool) strtotime($end)) {
        $datenotice = lang2("report filtered to end date", ["date" => date(DATE_FORMAT, strtotime($end))], false);
        $datetitle .= ($datetitle == "" ? "_" : "-") . date(DATE_FORMAT, strtotime($end));
        $csv->insertOne([$datenotice]);
    }
    $csv->insertAll($data);
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . $usertitle . $datetitle . "_" . date("Y-m-d_Hi") . ".csv" . '"');
    echo $csv;
    die();
}

function dataToODS($data, $name = "report", $user = null, $start = null, $end = null) {
    $ods = new ods();
    $styleColumn = new odsStyleTableColumn();
    $styleColumn->setUseOptimalColumnWidth(true);
    $headerstyle = new odsStyleTableCell();
    $headerstyle->setFontWeight("bold");
    $table = new odsTable($name);

    for ($i = 0; $i < count($data[0]); $i++) {
        $table->addTableColumn(new odsTableColumn($styleColumn));
    }

    $usernotice = "";
    $usertitle = "";
    $datetitle = "";
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = lang2("report filtered to user", ["name" => $user['name'], "username" => $user['username']], false);
        $usertitle = "_" . $user['username'];
        $row = new odsTableRow();
        $row->addCell(new odsTableCellString($usernotice));
        $table->addRow($row);
    }
    if ($start != null && (bool) strtotime($start)) {
        $datenotice = lang2("report filtered to start date", ["date" => date(DATE_FORMAT, strtotime($start))], false);
        $datetitle = "_" . date(DATE_FORMAT, strtotime($start));
        $row = new odsTableRow();
        $row->addCell(new odsTableCellString($datenotice));
        $table->addRow($row);
    }
    if ($end != null && (bool) strtotime($end)) {
        $datenotice = lang2("report filtered to end date", ["date" => date(DATE_FORMAT, strtotime($end))], false);
        $datetitle .= ($datetitle == "" ? "_" : "-") . date(DATE_FORMAT, strtotime($end));
        $row = new odsTableRow();
        $row->addCell(new odsTableCellString($datenotice));
        $table->addRow($row);
    }

    $rowid = 0;
    foreach ($data as $datarow) {
        $row = new odsTableRow();
        foreach ($datarow as $cell) {
            if ($rowid == 0) {
                $row->addCell(new odsTableCellString($cell, $headerstyle));
            } else {
                $row->addCell(new odsTableCellString($cell));
            }
        }
        $table->addRow($row);
        $rowid++;
    }
    $ods->addTable($table);
    $ods->downloadOdsFile($name . $usertitle . $datetitle . "_" . date("Y-m-d_Hi") . ".ods");
}

function dataToHTML($data, $name = "report", $user = null, $start = null, $end = null) {
    global $SECURE_NONCE;
    // HTML exporter doesn't like null values
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]); $j++) {
            if (is_null($data[$i][$j])) {
                $data[$i][$j] = '';
            }
        }
    }
    $usernotice = "";
    $usertitle = "";
    $datenotice = "";
    $datetitle = "";
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = "<span>" . lang2("report filtered to user", ["name" => $user['name'], "username" => $user['username']], false) . "</span><br />";
        $usertitle = "_" . $user['username'];
    }
    if ($start != null && (bool) strtotime($start)) {
        $datenotice = "<span>" . lang2("report filtered to start date", ["date" => date(DATE_FORMAT, strtotime($start))], false) . "</span><br />";
        $datetitle = "_" . date(DATE_FORMAT, strtotime($start));
    }
    if ($end != null && (bool) strtotime($end)) {
        $datenotice .= "<span>" . lang2("report filtered to end date", ["date" => date(DATE_FORMAT, strtotime($end))], false) . "</span><br />";
        $datetitle .= ($datetitle == "" ? "_" : "-") . date(DATE_FORMAT, strtotime($end));
    }
    header('Content-type: text/html');
    $converter = new HTMLConverter();
    $out = "<!DOCTYPE html>\n"
            . "<meta charset=\"utf-8\">\n"
            . "<meta name=\"viewport\" content=\"width=device-width\">\n"
            . "<title>" . $name . $usertitle . $datetitle . "_" . date("Y-m-d_Hi") . "</title>\n"
            . <<<STYLE
<style nonce="$SECURE_NONCE">
    .table-csv-data {
        border-collapse: collapse;
    }
    .table-csv-data tr:first-child {
        font-weight: bold;
    }
    .table-csv-data tr td {
        border: 1px solid black;
    }
</style>
STYLE
            . $usernotice . $datenotice
            . $converter->convert($data);
    echo $out;
}

function generateReport($type, $format, $user = null, $start = null, $end = null) {
    $data = getReportData($type, $user, $start, $end);
    switch ($format) {
        case "ods":
            dataToODS($data, $type, $user, $start, $end);
            break;
        case "html":
            dataToHTML($data, $type, $user, $start, $end);
            break;
        case "csv":
        default:
            echo dataToCSV($data, $type, $user, $start, $end);
            break;
    }
}
