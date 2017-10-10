<?php

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
        $database->insert('punches', ['uid' => $_SESSION['uid'], 'in' => date("Y-m-d H:i:s"), 'out' => null, 'notes' => '']);
        returnToSender("punched_in");
    case "punchout":
        if (!$database->has('punches', ['AND' => ['uid' => $_SESSION['uid'], 'out' => null]])) {
            returnToSender("already_out");
        }
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
    case "editshift":
        if (account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
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
    case "assignshift":
        if (!account_has_permission($_SESSION['username'], "QWIKCLOCK_MANAGE")) {
            returnToSender("no_permission");
        }
        if (!$database->has('shifts', ['shiftid' => $VARS['shift']])) {
            returnToSender("invalid_shiftid");
        }
        $already_assigned = $database->select('assigned_shifts', 'uid', ['shiftid' => $VARS['shift']]);
        require_once __DIR__ . "/lib/userinfo.php";
        $managedusers = getManagedUsernames($_SESSION['uid']);
        foreach ($VARS['users'] as $u) {
            if (!account_has_permission($_SESSION['username'], "ADMIN")) {
                if (!in_array($u, $managedusers)) {
                    returnToSender("you_arent_my_supervisor", htmlentities($u));
                }
            }
            if (!user_exists($u)) {
                returnToSender("user_not_exists", htmlentities($u));
            }
            $uid = getUserByUsername($u)['uid'];
            $database->insert('assigned_shifts', ['uid' => $uid, 'shiftid' => $VARS['shift']]);
            $already_assigned = array_diff($already_assigned, [$uid]); // Remove user from old list
        }
        foreach ($already_assigned as $uid) {
            $database->delete('assigned_shifts', ["AND" => ['uid' => $uid, 'shiftid' => $VARS['shift']]]);
        }
        returnToSender("shift_assigned");
        break;
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
            exit(json_encode($resp['result']));
        } else {
            exit("[]");
        }
        break;
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}