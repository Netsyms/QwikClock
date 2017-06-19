<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";

dieifnotloggedin();

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
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}