<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * Get user info for the given username.
 * @param int $u username
 * @return [string] Array of [uid, username, name]
 */
function getUserByUsername($u) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "userinfo",
            'username' => $u
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['data'];
    } else {
        // this shouldn't happen, but in case it does just fake it.
        return ["name" => $u, "username" => $u, "uid" => $u];
    }
}

/**
 * Get user info for the given UID.
 * @param int $u user ID
 * @return [string] Array of [uid, username, name]
 */
function getUserByID($u) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "userinfo",
            'uid' => $u
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['data'];
    } else {
        // this shouldn't happen, but in case it does just fake it.
        return ["name" => $u, "username" => $u, "uid" => $u];
    }
}

/**
 * Check if the first UID is a manager of the second UID.
 * @param int $m Manager UID
 * @param int $e Employee UID
 * @return boolean
 */
function isManagerOf($m, $e) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "ismanagerof",
            'manager' => $m,
            'employee' => $e,
            'uid' => 1
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['managerof'] === true;
    } else {
        // this shouldn't happen, but in case it does just fake it.
        return false;
    }
}

/**
 * Get an array of UIDs the given UID is a manager of.
 * @param int $manageruid The UID of the manager to find employees for.
 * @return [int]
 */
function getManagedUIDs($manageruid) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "getmanaged",
            'uid' => $manageruid
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['employees'];
    } else {
        return [];
    }
}

/**
 * Get an array of username the given UID is a manager of.
 * @param int $manageruid The UID of the manager to find employees for.
 * @return [int]
 */
function getManagedUsernames($manageruid) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "getmanaged",
            'uid' => $manageruid,
            'get' => "username"
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['employees'];
    } else {
        return [];
    }
}

/**
 * Get a list of the groups the user is a member of, as {['id':1,'name':"abc"],...}
 * @param int $uid
 */
function getGroupsByUID($uid) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "getgroupsbyuser",
            'uid' => $uid
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['groups'];
    } else {
        return [];
    }
}

/**
 * Get a list of the groups the user is a member of, as {['id':1,'name':"abc"],...}
 * @param int $username
 */
function getGroupsByUsername($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "getgroupsbyuser",
            'username' => $username
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['groups'];
    } else {
        return [];
    }
}
