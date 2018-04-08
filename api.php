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
            $shifts = $database->select('shifts', ["[>]assigned_shifts" => ['shiftid' => 'shiftid']], ["shifts.shiftid", "start", "end", "days"], ["AND" => ['uid' => $userinfo['uid'], 'start[<=]' => date("H:i:s", $minclockintime)]]);
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
        // Stop active job
        $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $userinfo['uid'], 'end' => null]]);
        $database->update('punches', ['uid' => $userinfo['uid'], 'out' => date("Y-m-d H:i:s")], ['out' => null]);
        exit(json_encode(["status" => "OK", "msg" => lang("punched out", false)]));
    case "getassignedshifts":
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
                ], [
            "uid" => $userinfo['uid']
                ]
        );
        for ($i = 0; $i < count($shifts); $i++) {
            $shifts[$i]['start_f'] = date(TIME_FORMAT, strtotime($shifts[$i]['start']));
            $shifts[$i]['end_f'] = date(TIME_FORMAT, strtotime($shifts[$i]['end']));
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
            $shifts[$i]['day_list'] = $days;
        }
        exit(json_encode(["status" => "OK", "shifts" => $shifts]));
    case "getjobs":
        $jobs = [];
        if ($database->count("job_groups") > 0) {
            require_once __DIR__ . "/lib/userinfo.php";
            $groups = getGroupsByUID($userinfo['uid']);
            $gids = [];
            foreach ($groups as $g) {
                $gids[] = $g['id'];
            }
            $jobs = $database->select('jobs', ['[>]job_groups' => ['jobid']], ['jobs.jobid (id)', 'jobname (name)', 'jobcode (code)', 'color'], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0]]);
        } else {
            $jobs = $database->select('jobs', ['jobid (id)', 'jobname (name)', 'jobcode (code)', 'color'], ['deleted' => 0]);
        }
        $jobids = [];
        $out = [];
        foreach ($jobs as $job) {
            if (in_array($job['id'], $jobids)) {
                continue;
            }
            $jobids[] = $job['id'];
            $out[] = $job;
        }
        exit(json_encode(["status" => "OK", "jobs" => $out]));
    case "getactivejob":
        $jobs = [];
        $job = $database->get('job_tracking', ['[>]jobs' => ['jobid']], ['jobs.jobid (id)', 'jobname (name)', 'color', 'start'], ["AND" => ["uid" => $userinfo['uid'], 'end' => null]]);
        exit(json_encode(["status" => "OK", "job" => $job]));
    case "setjob":
        if (is_empty($VARS['job'])) {
            exit(json_encode(["status" => "ERROR", "msg" => lang("invalid job", false)]));
        }
        if ($database->count("job_groups") > 0) {
            require_once __DIR__ . "/lib/userinfo.php";
            $groups = getGroupsByUID($userinfo['uid']);
            $gids = [];
            foreach ($groups as $g) {
                $gids[] = $g['id'];
            }
            $job = $database->has('jobs', ['[>]job_groups' => ['jobid']], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0, 'jobs.jobid' => $VARS['job']]]);
        } else {
            $job = $database->has('jobs', 'jobid', ['jobid' => $VARS['job']]);
        }
        if ($job === true) {
            // Stop other jobs
            $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $userinfo['uid'], 'end' => null]]);
            $database->insert('job_tracking', ['uid' => $userinfo['uid'], 'jobid' => $VARS['job'], 'start' => date("Y-m-d H:i:s")]);
            exit(json_encode(["status" => "OK", "msg" => lang("job changed", false)]));
        } else if ($VARS['job'] == "-1") {
            $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $userinfo['uid'], 'end' => null]]);
            exit(json_encode(["status" => "OK", "msg" => lang("job changed", false)]));
        } else {
            exit(json_encode(["status" => "ERROR", "msg" => lang("invalid job", false)]));
        }
        break;
    default:
        http_response_code(404);
        die("\"404 Action not found\"");
}
