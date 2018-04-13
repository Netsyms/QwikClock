<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";
require_once __DIR__ . "/lib/login.php";

if ($VARS['action'] !== "signout") {
    dieifnotloggedin();
}

if (account_has_permission($_SESSION['username'], "QWIKCLOCK") == FALSE) {
    die("You don't have permission to be here.");
}

/**
 * Redirects back to the page ID in $_POST/$_GET['source'] with the given message ID.
 * The message will be displayed by the app.
 * @param string $msg message ID (see lang/messages.php)
 * @param string $arg If set, replaces "{arg}" in the message string when displayed to the user.
 */
function returnToSender($msg, $arg = "") {
    global $VARS;
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=$msg&arg=$arg");
    }
    die();
}

switch ($VARS['action']) {
    case "punchin":
        if ($database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]])) {
            returnToSender("already_in");
        }

        $shiftid = null;
        if ($database->has('assigned_shifts', ['uid' => $_SESSION['uid']])) {
            $minclockintime = strtotime("now + 5 minutes");
            $shifts = $database->select('shifts', ["[>]assigned_shifts" => ['shiftid' => 'shiftid']], ["shifts.shiftid", "start", "end", "days"], ["AND" => ['uid' => $_SESSION['uid'], 'start[<=]' => date("H:i:s", $minclockintime)]]);
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
                returnToSender("not_assigned_to_work");
            }
        }

        $database->insert('punches', ['uid' => $_SESSION['uid'], 'in' => date("Y-m-d H:i:s"), 'out' => null, 'notes' => '', 'shiftid' => $shiftid]);
        returnToSender("punched_in");
    case "punchout":
        if (!$database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]])) {
            returnToSender("already_out");
        }
        // Stop active job
        $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $_SESSION['uid'], 'end' => null]]);
        $database->update('punches', ['uid' => $_SESSION['uid'], 'out' => date("Y-m-d H:i:s")], ['out' => null]);
        returnToSender("punched_out");
    case "gettime":
        $out = ["status" => "OK", "time" => date(TIME_FORMAT), "date" => date(LONG_DATE_FORMAT), "seconds" => date("s")];
        header('Content-Type: application/json');
        exit(json_encode($out));
    case "getinoutstatus":
        $in = $database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]]) === TRUE;
        $out = ["status" => "OK", "in" => $in];
        header('Content-Type: application/json');
        exit(json_encode($out));
    case "editpunch":
        require_once __DIR__ . "/lib/userinfo.php";
        if (user_exists($VARS['user'])) {
            $uid = getUserByUsername($VARS['user'])['uid'];
        } else {
            returnToSender("invalid_user");
        }

        $in = strtotime($VARS['in']);
        $out = strtotime($VARS['out']);
        if ($in === false) {
            returnToSender("invalid_datetime");
        }
        if ($out === false) {
            returnToSender("invalid_datetime");
        }
        if ($out < $in) {
            returnToSender("in_before_out");
        }

        if (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN") || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && isManagerOf($_SESSION['uid'], $uid)
                ) || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF") && $_SESSION['uid'] == $uid
                )
        ) {
            $data = [
                "uid" => $uid,
                "in" => date('Y-m-d H:i:s', $in),
                "out" => date('Y-m-d H:i:s', $out),
                "notes" => $VARS['notes']
            ];
            if ($database->has("punches", ["punchid" => $VARS['punchid']])) {
                $database->update("punches", $data, ["punchid" => $VARS['punchid']]);
            } else {
                $database->insert("punches", $data);
            }
            returnToSender("punch_saved");
        } else {
            returnToSender("no_permission");
        }
    case "deletepunch":
        require_once __DIR__ . "/lib/userinfo.php";

        if (!$database->has("punches", ["punchid" => $VARS['punchid']])) {
            returnToSender("invalid_parameters");
        }

        $pid = $VARS['punchid'];
        $uid = $database->get("punches", "uid", ["punchid" => $pid]);

        if (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN") || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && isManagerOf($_SESSION['uid'], $uid)
                ) || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF") && $_SESSION['uid'] == $uid
                )
        ) {

            $database->delete("punches", ["punchid" => $VARS['punchid']]);
            returnToSender("punch_deleted");
        } else {
            returnToSender("no_permission");
        }
    case "editshift":
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            $valid_daycodes = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];

            $name = htmlentities($VARS['shiftname']);
            $start = $VARS['start'];
            $end = $VARS['end'];
            $days = $VARS['days'];

            $startepoch = strtotime($start);
            if ($startepoch === false) {
                returnToSender("invalid_time");
            }
            $startformatted = date("H:i:s", $startepoch);

            $endepoch = strtotime($end);
            if ($endepoch === false) {
                returnToSender("invalid_time");
            }
            $endformatted = date("H:i:s", $endepoch);

            // Parse days into string, validating along the way
            $daystring = "";
            foreach ($days as $d) {
                if (in_array($d, $valid_daycodes)) {
                    if (strpos($daystring, $d) === FALSE) {
                        $daystring .= $d;
                    }
                }
            }

            if (is_empty($VARS['shiftid'])) {
                if ($database->has('shifts', ['shiftname' => $name])) {
                    returnToSender("shift_name_used");
                }
                $database->insert('shifts', ["shiftname" => $name, "start" => $startformatted, "end" => $endformatted, "days" => $daystring]);
                returnToSender("shift_added");
            } else if ($database->has('shifts', ['shiftid' => $VARS['shiftid']])) {
                $database->update('shifts', ["shiftname" => $name, "start" => $startformatted, "end" => $endformatted, "days" => $daystring], ["shiftid" => $VARS['shiftid']]);
                returnToSender("shift_saved");
            } else {
                returnToSender("invalid_shiftid");
            }
        } else {
            returnToSender("no_permission");
        }
    case "deleteshift":
        if (!$database->has('shifts', ['shiftid' => $VARS['shiftid']])) {
            returnToSender("invalid_shiftid");
        }
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            if ($database->has('assigned_shifts', ['shiftid' => $VARS['shiftid']])) {
                returnToSender('shift_has_users');
            }
            $database->delete('shifts', ['shiftid' => $VARS['shiftid']]);
            returnToSender("shift_deleted");
        } else {
            returnToSender("no_permission");
        }
    case "assignshift":
        if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && !account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            returnToSender("no_permission");
        }
        if (!$database->has('shifts', ['shiftid' => $VARS['shift']])) {
            returnToSender("invalid_shiftid");
        }
        $already_assigned = $database->select('assigned_shifts', 'uid', ['shiftid' => $VARS['shift']]);
        require_once __DIR__ . "/lib/userinfo.php";
        $managedusers = getManagedUsernames($_SESSION['uid']);
        $manageduids = getManagedUIDs($_SESSION['uid']);
        foreach ($VARS['users'] as $u) {
            if (!user_exists($u)) {
                returnToSender("user_not_exists", htmlentities($u));
            }
            $uid = getUserByUsername($u)['uid'];
            if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                if (!in_array($u, $managedusers) && !in_array($uid, $already_assigned)) {
                    returnToSender("you_arent_my_supervisor", htmlentities($u));
                }
            }
            if (!in_array($uid, $already_assigned)) {
                $database->insert('assigned_shifts', ['uid' => $uid, 'shiftid' => $VARS['shift']]);
            }
            $already_assigned = array_diff($already_assigned, [$uid]); // Remove user from old list
        }
        // $already_assigned now only has removed users
        $removefailed = false;
        foreach ($already_assigned as $uid) {
            if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                if (!in_array($uid, $manageduids)) {
                    $removefailed = true;
                    continue;
                }
            }
            $database->delete('assigned_shifts', ["AND" => ['uid' => $uid, 'shiftid' => $VARS['shift']]]);
        }
        returnToSender($removefailed ? "shift_assigned_removefailed" : "shift_assigned");
        break;
    case "setjob":
        if ($database->count("job_groups") > 0) {
            require_once __DIR__ . "/lib/userinfo.php";
            $groups = getGroupsByUID($_SESSION['uid']);
            $gids = [];
            foreach ($groups as $g) {
                $gids[] = $g['id'];
            }
            $job = $database->has('jobs', ['[>]job_groups' => ['jobid']], ["AND" => ["OR" => ['groupid' => $gids, 'groupid #-1' => -1], 'deleted' => 0, 'jobs.jobid' => $VARS['job']]]);
        } else {
            $job = $database->has('jobs', 'jobid', ['jobid' => $VARS['job']]);
        }
        if ($job) {
            // Stop other jobs
            $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $_SESSION['uid'], 'end' => null]]);
            $database->insert('job_tracking', ['uid' => $_SESSION['uid'], 'jobid' => $VARS['job'], 'start' => date("Y-m-d H:i:s")]);
            returnToSender("job_changed");
        } else if ($VARS['job'] == "-1") {
            $database->update('job_tracking', ['end' => date("Y-m-d H:i:s")], ['AND' => ['uid' => $_SESSION['uid'], 'end' => null]]);
            returnToSender("job_changed");
        } else {
            returnToSender("job_invalid");
        }
        break;
    case "editjob":
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            $name = htmlentities($VARS['jobname']);
            $code = $VARS['jobcode'];
            $color = $VARS['color'];
            $groups = $VARS['groups'];

            if (is_empty($VARS['jobid'])) {
                if ($database->has('jobs', ['jobname' => $name])) {
                    returnToSender("job_name_used");
                }
                $database->insert('jobs', ["jobname" => $name, "jobcode" => $code, "color" => $color]);
                $jobid = $database->id();
                $database->delete('job_groups', ['jobid' => $jobid]);
                foreach ($groups as $g) {
                    $database->insert('job_groups', ['jobid' => $jobid, 'groupid' => $g]);
                }
                returnToSender("job_added");
            } else if ($database->has('jobs', ['jobid' => $VARS['jobid']])) {
                $database->update('jobs', ["jobname" => $name, "jobcode" => $code, "color" => $color], ["jobid" => $VARS['jobid']]);
                $database->delete('job_groups', ['jobid' => $VARS['jobid']]);
                foreach ($groups as $g) {
                    $database->insert('job_groups', ['jobid' => $VARS['jobid'], 'groupid' => $g]);
                }
                returnToSender("job_saved");
            } else {
                returnToSender("invalid_jobid");
            }
        } else {
            returnToSender("no_permission");
        }
        break;
    case "deletejob":
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
            if (is_empty($VARS['jobid'])) {
                returnToSender("invalid_jobid");
            } else if ($database->has('jobs', ['jobid' => $VARS['jobid']])) {
                $database->update('jobs', ["deleted" => 1], ["jobid" => $VARS['jobid']]);
                returnToSender("job_deleted");
            } else {
                returnToSender("invalid_jobid");
            }
        } else {
            returnToSender("no_permission");
        }
        break;
    case "editjobhistory":
        require_once __DIR__ . "/lib/userinfo.php";

        if ($database->has('job_tracking', ['id' => $VARS['jobid']])) {
            $uid = $database->get('job_tracking', 'uid', ['id' => $VARS['jobid']]);
        } else {
            returnToSender("invalid_parameters");
        }

        if (!$database->has("jobs", ['jobid' => $VARS['job']])) {
            returnToSender("invalid_jobid");
        }

        $start = strtotime($VARS['start']);
        $end = strtotime($VARS['end']);
        if ($start === false) {
            returnToSender("invalid_datetime");
        }
        if ($end === false) {
            returnToSender("invalid_datetime");
        }
        if ($end < $start) {
            returnToSender("in_before_out");
        }

        if (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN") || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && isManagerOf($_SESSION['uid'], $uid)
                ) || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF") && $_SESSION['uid'] == $uid
                )
        ) {
            $data = [
                "jobid" => $VARS['job'],
                "start" => date('Y-m-d H:i:s', $start),
                "end" => date('Y-m-d H:i:s', $end)
            ];
            $database->update("job_tracking", $data, ["id" => $VARS['jobid']]);
            returnToSender("job_saved");
        } else {
            returnToSender("no_permission");
        }
    case "deletejobhistory":
        require_once __DIR__ . "/lib/userinfo.php";

        if ($database->has('job_tracking', ['id' => $VARS['jobid']])) {
            $uid = $database->get('job_tracking', 'uid', ['id' => $VARS['jobid']]);
        } else {
            returnToSender("invalid_parameters");
        }

        if (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN") || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE") && isManagerOf($_SESSION['uid'], $uid)
                ) || (
                account_has_permission($_SESSION['username'], "QWIKCLOCK_EDITSELF") && $_SESSION['uid'] == $uid
                )
        ) {

            $database->delete("job_tracking", ["id" => $VARS['jobid']]);
            returnToSender("job_deleted");
        } else {
            returnToSender("no_permission");
        }
    case "autocomplete_user":
        header("Content-Type: application/json");
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => PORTAL_KEY,
                'action' => "usersearch",
                'search' => $VARS['q']
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            exit("[]");
        }

        $resp = json_decode($response->getBody(), TRUE);
        if ($resp['status'] == "OK") {
            if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_ADMIN")) {
                require_once __DIR__ . "/lib/userinfo.php";
                $managed = getManagedUIDs($_SESSION['uid']);
                $result = $resp['result'];
                for ($i = 0; $i < count($result); $i++) {
                    if (!in_array($result[$i]['uid'], $managed)) {
                        $result[$i]['managed'] = 0;
                    }
                }
                exit(json_encode($result));
            } else {
                exit(json_encode($resp['result']));
            }
        } else {
            exit("[]");
        }
        break;
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}
