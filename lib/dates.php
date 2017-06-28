<?php

/**
 * Some handy date calculations.
 * 
 * License exception: this code file can be used under the MIT License.
 */

/**
 * Convert some number of seconds into hours:minutes:seconds.
 * @param Integer $seconds Some integer number.
 * @param Boolean $showseconds True if the returned string should show a 
 * seconds value.
 * @return String
 */
function seconds2string($seconds, $showseconds = true) {
    // Thanks to https://stackoverflow.com/a/34681477
    return floor($seconds / 3600) . gmdate(($showseconds ? ":i:s" : ":i"), $seconds % 3600);
}

/**
 * Gets the timestamp of the first instant of the current week.
 * 
 * If $firstday = "Monday", this will return a value equal to midnight on the 
 * previous Monday, unless it is Monday, in which case this will return the 
 * timestamp of 00:00 (12:00am) last night.
 * 
 * <b>Examples:</b>
 * 
 * Current date is Tuesday June 3rd, $firstday is Monday, this returns
 * a value equal to 12:00am Monday June 2nd.
 * 
 * Current date is Monday June 2nd, $firstday is Monday, this returns 12:00am 
 * Monday June 2nd.
 * 
 * @param String $firstday "Sunday", "Monday", etc.  Default "Sunday".
 * @return Integer UNIX timestamp of when the week started.
 */
function getstartofweek($firstday = "Sunday") {
    if (date("z") - date("z", strtotime("last " . $firstday)) >= 7) {
        // Today is the start of the week
        $weekstart = strtotime($firstday);
    } else {
        // Today is not the start of the week
        $weekstart = strtotime("last " . $firstday);
    }
    return $weekstart;
}

/**
 * Formats the date string or timestamp for MySQL.
 * @param Integer/String $time A UNIX timestamp or date/time string.
 * @return String Y-m-d H:i:s
 */
function sqldatetime($time) {
    if (is_numeric($time)) {
        return date("Y-m-d H:i:s", $time);
    }
    return date("Y-m-d H:i:s", strtotime($time));
}

/**
 * Given an array of time ranges, calculates the sum of all those ranges.
 * @param Array $times [["2017-06-20 12:23:34","2017-06-20 15:56:00"],["Monday", "Tuesday"],...,[12345678,12345890]]
 * @return Integer A number of seconds.
 */
function sumelapsedtimearray($times) {
    $totalseconds = 0;
    foreach ($times as $t) {
        $curtime = time();
        if (is_null($t[0])) {
            $t[0] = $curtime;
        }
        if (is_null($t[1])) {
            $t[1] = $curtime;
        }
        
        $t0 = (is_numeric($t[0]) ? $t[0] * 1 : strtotime($t[0]));
        $t1 = (is_numeric($t[1]) ? $t[1] * 1 : strtotime($t[1]));
        
        if ($t1 == $t0) {
            // The times are equal, so we don't need to add anything
            continue;
        }

        $diff = abs($t1 - $t0);
        $totalseconds += $diff;
    }
    return $totalseconds;
}
