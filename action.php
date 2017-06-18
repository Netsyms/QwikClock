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
    case "time":
        $out = ["status" => "OK", "time" => date(TIME_FORMAT), "date" => date(LONG_DATE_FORMAT), "seconds" => date("s")];
        header('Content-Type: application/json');
        exit(json_encode($out));
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}