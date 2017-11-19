<?php

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

// Allow access with a download code, for mobile app and stuff
$date = date("Y-m-d H:i:s");
if (isset($VARS['code']) && LOADED) {
    if (!$database->has('report_access_codes', ["AND" => ['code' => $VARS['code'], 'expires[>]' => $date]])) {
        dieifnotloggedin();
    }
} else {
    dieifnotloggedin();
}

// Delete old DB entries
$database->delete('report_access_codes', ['expires[<=]' => $date]);

if (LOADED) {
    $user = null;
    require_once __DIR__ . "/userinfo.php";
    require_once __DIR__ . "/login.php";
    if ($VARS['users'] != "all" && !is_empty($VARS['user']) && user_exists($VARS['user'])) {
        $user = getUserByUsername($VARS['user']);
    }
    if (isset($VARS['type']) && isset($VARS['format'])) {
        generateReport($VARS['type'], $VARS['format'], $user);
        die();
    } else {
        lang("invalid parameters");
        die();
    }
}

function getShiftReport($user = null) {
    global $database;
    if ($user != null && array_key_exists('uid', $user)) {
        $shifts = $database->select(
                "shifts", [
            "[>]assigned_shifts" => ["shiftid" => "shiftid"]
                ], [
            "shifts.shiftid", "shiftname", "start", "end", "days"
                ], [
            "uid" => $user['uid']
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

function getReportData($type, $user = null) {
    switch ($type) {
        case "shifts":
            return getShiftReport($user);
            break;
        default:
            return [["error"]];
    }
}

function dataToCSV($data, $name = "report", $user = null) {
    $csv = Writer::createFromString('');
    $usernotice = "";
    $usertitle = "";
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = lang2("report filtered to", ["name" => $user['name'], "username" => $user['username']], false);
        $usertitle = "_" . $user['username'];
        $csv->insertOne([$usernotice]);
    }
    $csv->insertAll($data);
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . $usertitle . "_" . date("Y-m-d_Hi") . ".csv" . '"');
    echo $csv;
    die();
}

function dataToODS($data, $name = "report", $user = null) {
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
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = lang2("report filtered to", ["name" => $user['name'], "username" => $user['username']], false);
        $usertitle = "_" . $user['username'];
        $row = new odsTableRow();
        $row->addCell(new odsTableCellString($usernotice));
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
    $ods->downloadOdsFile($name . $usertitle . "_" . date("Y-m-d_Hi") . ".ods");
}

function dataToHTML($data, $name = "report", $user = null) {
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
    if ($user != null && array_key_exists('username', $user) && array_key_exists('name', $user)) {
        $usernotice = "<span>" . lang2("report filtered to", ["name" => $user['name'], "username" => $user['username']], false) . "</span><br />";
        $usertitle = "_" . $user['username'];
    }
    header('Content-type: text/html');
    $converter = new HTMLConverter();
    $out = "<!DOCTYPE html>\n"
            . "<meta charset=\"utf-8\">\n"
            . "<meta name=\"viewport\" content=\"width=device-width\">\n"
            . "<title>" . $name . $usertitle . "_" . date("Y-m-d_Hi") . "</title>\n"
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
            . $usernotice
            . $converter->convert($data);
    echo $out;
}

function generateReport($type, $format, $user = null) {
    $data = getReportData($type, $user);
    switch ($format) {
        case "ods":
            dataToODS($data, $type, $user);
            break;
        case "html":
            dataToHTML($data, $type, $user);
            break;
        case "csv":
        default:
            echo dataToCSV($data, $type, $user);
            break;
    }
}
