<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


/**
 * Simple JSON API to allow other apps to access data from this app.
 * 
 * Requests can be sent via either GET or POST requests.  POST is recommended
 * as it has a lower chance of being logged on the server, exposing unencrypted
 * user passwords.
 */
require __DIR__ . '/required.php';
require_once __DIR__ . '/lib/login.php';
require_once __DIR__ . '/lib/userinfo.php';
header("Content-Type: application/json");

$username = $VARS['username'];
$password = $VARS['password'];
if (user_exists($username) !== true || (authenticate_user($username, $password, $errmsg) !== true && checkAPIKey($password) !== true) || account_has_permission($username, "QWIKCLOCK") !== true) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}
$userinfo = getUserByUsername($username);

// query max results
$max = 20;
if (preg_match("/^[0-9]+$/", $VARS['max']) === 1 && $VARS['max'] <= 1000) {
    $max = (int) $VARS['max'];
}

switch ($VARS['action']) {
    case "ping":
        $out = ["status" => "OK", "maxresults" => $max, "pong" => true];
        exit(json_encode($out));
    case "punchin":
        if ($database->has('punches', ['AND' => ['uid' => $userinfo['uid'], 'out' => null]])) {
            die(json_encode(["status" => "ERROR", "msg" => lang("already punched in", false)]));
        }

        $shiftid = null;
        if ($database->has('assigned_shifts', ['uid' => $userinfo['uid']])) {
            $minclockintime = strtotime("now + 5 minutes");
            $shifts = $database->select('shifts', ["[>]assigned_shifts" => ['shiftid' => 'shiftid']], ["shifts.shiftid", "start", "end", "days"], ["AND" =>['uid' => $userinfo['uid'], 'start[<=]' => date("H:i:s", $minclockintime)]]);
            foreach ($shifts as $shift) {
                $curday = substr(date("D"), 0, 2);
                if (strpos($shift['days'], $curday) === FALSE) {
                    continue;
                }
                if (strtotime($shift['end']) >= strtotime($shift['start'])) {
                    if (strtotime("now") >= strtotime($shift['end'])) {
                        continue; // shift is already over
                    }
                }
                $shiftid = $shift['shiftid'];
            }
            if (is_null($shiftid)) {
                die(json_encode(["status" => "ERROR", "msg" => lang("not assigned to work now", false)]));
            }
        }

        $database->insert('punches', ['uid' => $userinfo['uid'], 'in' => date("Y-m-d H:i:s"), 'out' => null, 'notes' => '', 'shiftid' => $shiftid]);
        exit(json_encode(["status" => "OK", "msg" => lang("punched in", false)]));
    case "punchout":
        if (!$database->has('punches', ['AND' => ['uid' => $userinfo['uid'], 'out' => null]])) {
            die(json_encode(["status" => "ERROR", "msg" => lang("already punched out", false)]));
        }
        $database->update('punches', ['uid' => $userinfo['uid'], 'out' => date("Y-m-d H:i:s")], ['out' => null]);
        exit(json_encode(["status" => "OK", "msg" => lang("punched out", false)]));
    default:
        http_response_code(404);
        die("\"404 Action not found\"");
}