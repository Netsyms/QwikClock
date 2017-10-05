<?php
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
        return $resp['managerof'];
    } else {
        // this shouldn't happen, but in case it does just fake it.
        return ["name" => $u, "username" => $u, "uid" => $u];
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