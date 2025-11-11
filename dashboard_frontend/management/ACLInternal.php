<?php

/* Dashboard Builder.
   Copyright (C) 2025 DISIT Lab https://www.disit.org - University of Florence
   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

    ini_set('display_errors', 1);
    error_reporting(E_ERROR | E_PARSE);

    include '../config.php';

    function getDbLink() {
        global $host, $username, $password, $dbname;
        $link = mysqli_connect($host, $username, $password, $dbname)
            or die("MySQL connect error: " . mysqli_error($link));
        return $link;
    }
    function getLdapConn() {
    global $ldapServer, $ldapPort, $ldapBaseDN, $ldapAdminDN, $ldapAdminPwd;
    $conn = ldap_connect($ldapServer, $ldapPort)
        or die("That LDAP-URI was not parseable");
    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_bind($conn, $ldapAdminDN, $ldapAdminPwd)
        or die("ERROR IN BIND");
    return $conn;
    }
    /**
    * Handle authorization for the 'check_auth' endpoint by first
    * ensuring the user is authenticated, then delegating ACL checks.
    * FOR INTERNAL: EXPECTED $data: ["auth_name":"required", "organization": "optional", "preferred_username":"required", "ou"='unused for now' ]
    */
    function ACLAPI_check_auth($data = []) {
    global $ldapBaseDN;
    $link = getDbLink();
    $ldap = getLdapConn();
    $requested_name = trim($data['auth_name'] ?? '');
    $requested_org = trim($data['organization'] ?? '');
    $user_orgs = checkLdapOrganizations($ldap, 'cn='.$data["preferred_username"].','.$ldapBaseDN, $ldapBaseDN);
    if(!in_array($requested_org, $user_orgs)){
        return ['error' => 'requested org is not in user orgs',
                'baseDn' => $ldapBaseDN,
                'userDn' => 'cn='.$data["preferred_username"].','.$ldapBaseDN,
                'user_orgs' => $user_orgs,
                'requested_org' => $requested_org];
    }
    $claims = $data;
    /*$enc_username = encryptOSSL(
        strtolower($claims['preferred_username']),
        $GLOBALS['encryptionInitKey'],
        $GLOBALS['encryptionIvKey'],
        $GLOBALS['encryptionMethod']
    );*/
    if ($requested_name === '') {
        http_response_code(400);
        return ['error' => 'auth_name parameter is required'];
    }
    $sql = "SELECT ID FROM AccessDefinitions WHERE authname = ? LIMIT 1";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("ACLAPI_check_auth(): prepare failed: " . mysqli_error($link));
        return ['error' => 'Server error'];
    }
    mysqli_stmt_bind_param($stmt, "s", $requested_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $requested_auth_id);

    if (! mysqli_stmt_fetch($stmt)) {
        // no such authname
        mysqli_stmt_close($stmt);
        return ['authorized' => false, 'reason' => 'Unknown authname', 'requested_auth' => $requested_name];
    }
    mysqli_stmt_close($stmt);

    return check_db($link, [
        'preferred_username' => $claims['preferred_username'],
        'ou'                 => $claims['ou'],
        'requested_auth'     => (int)$requested_auth_id,
        'requested_org'      => $requested_org,
    ]);
}

function allowed_orgs($requestedOrg, $possibleOrgs): bool
{
    // wildcards are * and null
    if ($possibleOrgs === '*' || $possibleOrgs === null) {
        return true;
    }
    else if($requestedOrg === null){
        return false;
    }
    $orgs = array_map('trim', explode(',', $possibleOrgs));
    return in_array($requestedOrg, $orgs, true);
}
function check_profile_access(mysqli $link, array $req, $enc_username): ?array {
    //find all profiles assigned to this user
    $sql = "SELECT profileID FROM ACLProfilesAssignment WHERE `user` = ?";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("check_profile_access(): prepare failed: " . mysqli_error($link));
        return null;
    }
    mysqli_stmt_bind_param($stmt, 's', $enc_username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $profileId);

    $profileIds = [];
    while (mysqli_stmt_fetch($stmt)) {
        $profileIds[] = $profileId;
    }
    mysqli_stmt_close($stmt);

    //no profiles
    if (empty($profileIds)) {
        return null;
    }

    //for each profile, see if it grants the requested_auth
    foreach ($profileIds as $pid) {
        // load the comma‐separated list of AccessDefinition IDs
        $sql = "SELECT authIDs, profilename FROM ACLProfiles WHERE ID = ?";
        if (! $stmt = mysqli_prepare($link, $sql)) {
            error_log("check_profile_access(): prepare failed: " . mysqli_error($link));
            continue;
        }
        mysqli_stmt_bind_param($stmt, 'i', $pid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $rawAuthIDs, $profileName);
        if (! mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            continue;
        }
        mysqli_stmt_close($stmt);

        // parse into an array of ints
        $authIDs = array_filter(array_map('trim', explode(',', $rawAuthIDs)), function($v){ return $v !== ''; });
        $authIDs = array_map('intval', $authIDs);

        if (! in_array($req['requested_auth'], $authIDs, true)) {
            // this profile doesn't mention that auth
            continue;
        }
        $sql = "
          SELECT maxbyday, maxbymonth, maxtotalaccesses,
                 dashboardID, collectionID, menuID, org
            FROM AccessDefinitions
           WHERE ID = ?
           LIMIT 1
        ";
        if (! $stmt = mysqli_prepare($link, $sql)) {
            error_log("check_profile_access(): prepare failed: " . mysqli_error($link));
            return null;
        }
        mysqli_stmt_bind_param($stmt, 'i', $req['requested_auth']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result(
            $stmt,
            $maxByDay, $maxByMonth, $maxTotal,
            $rawDashID, $collID, $menuID, $possibleOrgs
        );
        if (! mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            continue;
        }
        mysqli_stmt_close($stmt);

        //org check (return if org is not allowed)
        if (! allowed_orgs($req['requested_org'], $possibleOrgs)) {
            return ['authorized' => false , 'reason' => 'Organization not allowed'];
        }
        //convert dash b64 ID to int IF it's not an int already
        $dashID = null;
        if (filter_var($rawDashID, FILTER_VALIDATE_INT) !== false) {
            $dashID = (int)$rawDashID;
        } elseif (($dec = base64_decode($rawDashID, true)) !== false
                  && filter_var($dec, FILTER_VALIDATE_INT) !== false) {
            $dashID = (int)$dec;
        }

        //if there are no counts to enforce, allow immediately
        if ($dashID === null || ($maxByDay === null && $maxByMonth === null && $maxTotal === null)) {
            return array_filter([
                'authorized'     => true,
                'authorized_by'  => $profileName,
                'dash_authorized'=> $dashID,
                'maxbyday'       => $maxByDay    !== null ? (int)$maxByDay    : null,
                'maxbymonth'     => $maxByMonth  !== null ? (int)$maxByMonth  : null,
                'maxtotal'       => $maxTotal     !== null ? (int)$maxTotal     : null,
                'collectionID'   => $collID,
                'menuID'         => $menuID,
                'org'            => $req['requested_org'],
            ], function($v){ return $v !== null; });
        }
        //else check userstats
        if (check_userstats_accesses_dashboards(
            $enc_username, $dashID,
            $maxByDay, $maxByMonth, $maxTotal
        )) {
            return array_filter([
                'authorized'     => true,
                'authorized_by'  => $profileName,
                'dash_authorized'=> $dashID,
                'maxbyday'       => $maxByDay    !== null ? (int)$maxByDay    : null,
                'maxbymonth'     => $maxByMonth  !== null ? (int)$maxByMonth  : null,
                'maxtotal'       => $maxTotal     !== null ? (int)$maxTotal     : null,
                'collectionID'   => $collID,
                'menuID'         => $menuID,
                'org'            => $req['requested_org'],
            ], function($v){ return $v !== null; });
        } else {
            return ['authorized'=>false, 'reason'=>'Limit reached by profile'];
        }
    }
    return null;
}
function check_db($link, $req) {
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;

    $enc_username = encryptOSSL(strtolower($req['preferred_username']),$encryptionInitKey,$encryptionIvKey,$encryptionMethod);
    $profileResult = check_profile_access($link, $req, $enc_username);
    //check profiles then single auths
    if ($profileResult !== null) {
        return $profileResult;
    }
    $requested_auth = intval($req['requested_auth']);
    $requested_org = trim($req['requested_org']);
    $sql = "
      SELECT
        AD.maxbyday,
        AD.maxbymonth,
        AD.maxtotalaccesses,
        AD.dashboardID,
        AD.collectionID,
        AD.menuID,
        AD.org
      FROM ACL AS A
      JOIN AccessDefinitions AS AD
        ON A.defID = AD.ID
      WHERE A.`user` = ?
        AND A.defID   = ?
      LIMIT 1
    ";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("check_db(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_param($stmt, 'si', $enc_username, $requested_auth);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("check_db(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_result($stmt, $maxbyday, $maxbymonth, $maxtotalaccesses, $dashID, $collID, $menuID, $possible_orgs);

    if (mysqli_stmt_fetch($stmt)) {
        //row exists
        mysqli_stmt_close($stmt);
        //org check
        if (! allowed_orgs($requested_org, $possible_orgs)){
            return ['authorized' => false , 'reason' => 'Organization not allowed'];
        }
        //check se dash id è già int o un base64
        $raw = $dashID;
        $dashID = null;
        if (filter_var($raw, FILTER_VALIDATE_INT) !== false) {
            $dashID = (int)$raw;
        }
        elseif (($dec = base64_decode($raw, true)) !== false && filter_var($dec, FILTER_VALIDATE_INT) !== false) {
                $dashID = (int)$dec;
        }
        if ($dashID === null || ($maxbyday   === null && $maxbymonth === null && $maxtotalaccesses   === null)) {
            return array_filter([
                'authorized'     => true,
                'authorized_by'  => 'Direct_ACL',
                'dash_authorized'=> $dashID,
                'maxbyday'       => $maxbyday    !== null ? (int)$maxbyday    : null,
                'maxbymonth'     => $maxbymonth  !== null ? (int)$maxbymonth  : null,
                'maxtotal'       => $maxtotalaccesses !== null ? (int)$maxtotalaccesses : null,
                'collectionID'   => $collID,
                'menuID'         => $menuID,
                'org'            => $requested_org,
            ], function($v){
                return $v !== null;
            });
        } else{
            $out = check_userstats_accesses_dashboards($enc_username, $dashID, $maxbyday, $maxbymonth, $maxtotalaccesses);
            if($out === true){
                return array_filter([
                    'authorized'     => true,
                    'authorized_by'  => 'Direct_ACL',
                    'dash_authorized'=> $dashID,
                    'maxbyday'       => $maxbyday    !== null ? (int)$maxbyday    : null,
                    'maxbymonth'     => $maxbymonth  !== null ? (int)$maxbymonth  : null,
                    'maxtotal'       => $maxtotalaccesses !== null ? (int)$maxtotalaccesses : null,
                    'collectionID'   => $collID,
                    'menuID'         => $menuID,
                    'org'            => $requested_org,
                ], function($v){
                    return $v !== null;
                });
            }
            else{
                mysqli_stmt_close($stmt);
                return ['authorized' => false , 'reason' => 'Limit reached'];
            }
        }
    }
    mysqli_stmt_close($stmt);
    return ['authorized' => false ,
     'reason' => 'User does not have ACL or does not exist',
     'requested_auth'=> $requested_auth,
     'requested_by'=> $req['preferred_username']
    ];
}

function check_userstats_accesses_dashboards(string $owner, int $dashID, ?int $maxByDay, ?int $maxByMonth, ?int $maxTotal): bool {
    // if there's no dashboard to check, always allow
    if ($dashID === 0) {
        return true;
    }
    // connect to userstats
    global $resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort;
    $link2 = mysqli_connect($resourcesconsumptionHost,$resourcesconsumptionUser,$resourcesconsumptionPassword,$resourcesconsumptionDb,$resourcesconsumptionPort);
    if (! $link2) {
        error_log("check_userstats_accesses: Cannot connect to resources DB: ".mysqli_connect_error());
        // conservatively deny if you can't check
        return false;
    }
    // helper to run a single‐row SUM() query
    $run_sum = function(string $sql, array $params) use ($link2): int {
        $stmt = mysqli_prepare($link2, $sql);
        if (! $stmt) {
            error_log("check_userstats: prepare failed: ".mysqli_error($link2));
            return PHP_INT_MAX; // force fail
        }
        // build types string for bind_param
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : 's';
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $sum);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return (int)$sum;
    };
    $today      = date('Y-m-d');
    $monthStart = date('Y-m-01');     // first day of this month
    $monthEnd   = date('Y-m-t');      // last day of this month
    // check TOTAL so far
    if ($maxTotal !== null) {
        if($maxTotal <= 0) {
            mysqli_close($link2);
            return false;
        }
        $sql = "
          SELECT COALESCE(SUM(nAccessPerDay),0)
            FROM daily_dashboard_accesses
           WHERE IdDashboard = ?
             AND UserID      = ?
        ";
        $totalSoFar = $run_sum($sql, [$dashID, $owner]);
        if ($totalSoFar >= $maxTotal) {
            mysqli_close($link2);
            return false;
        }
    }
    // check month
    if ($maxByMonth !== null) {
        if($maxByMonth <= 0) {
            mysqli_close($link2);
            return false;
        }
        $sql = "
          SELECT COALESCE(SUM(nAccessPerDay),0)
            FROM daily_dashboard_accesses
           WHERE IdDashboard = ?
             AND UserID      = ?
             AND `date` BETWEEN ? AND ?
        ";
        $monthSoFar = $run_sum($sql, [
            $dashID,
            $owner,
            $monthStart,
            $monthEnd,
        ]);
        if ($monthSoFar >= $maxByMonth) {
            mysqli_close($link2);
            return false;
        }
    }
    // check day
    if ($maxByDay !== null) {
        if($maxByDay <= 0) {
            mysqli_close($link2);
            return false;
        }
        $sql = "
          SELECT nAccessPerDay
            FROM daily_dashboard_accesses
           WHERE IdDashboard = ?
             AND UserID      = ?
             AND `date`      = ?
        ";
        $stmt = mysqli_prepare($link2, $sql);
        if (! $stmt) {
            error_log("check_userstats: prepare failed: ".mysqli_error($link2));
            return PHP_INT_MAX;
        }
        mysqli_stmt_bind_param($stmt, 'iss', $dashID, $owner, $today);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $todayCount);
        $found = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // if there's already an entry for today, compare it
        if ($found && (int)$todayCount >= $maxByDay) {
            mysqli_close($link2);
            return false;
        }
    }
    mysqli_close($link2);
    return true;
}

//Check if requested user has access to that dash ID
//FOR INTERNAL: expected $data= ["preferred_username":"required", "dashboard_idx":"required"]
function ACLAPI_check_dashboard($data = []): array {
    $link = getDbLink();
    $debug = [];
    $username = $data['preferred_username'] ?? '';
    $dashboardParam = $data['dashboard_id'] ?? '';
    $debug[] = "Requested internal user={$username}";
    $debug[] = "Raw dashboard_id param='{$dashboardParam}'";
    if ($dashboardParam === '') {
        http_response_code(400);
        return [
            'authorized' => false,
            'debug'      => $debug,
            'error'      => 'dashboard_id parameter is required'
        ];
    }
    if ($username === '') {
        http_response_code(400);
        return [
            'authorized' => false,
            'debug'      => $debug,
            'error'      => 'preferred_username parameter is required'
        ];
    }
    //Normalize into int / b64 / raw
    $intParam = $b64Param = null;
    $dashID   = null;
    if (filter_var($dashboardParam, FILTER_VALIDATE_INT) !== false) {
        $intParam = $dashboardParam;
        $dashID   = (int)$dashboardParam;
        $b64Param = base64_encode($dashboardParam);
        $debug[]  = "INT input ⇒ intParam={$intParam}, dashID={$dashID}, b64Param={$b64Param}";
    }
    elseif (($dec = base64_decode($dashboardParam, true)) !== false
           && filter_var($dec, FILTER_VALIDATE_INT) !== false) {
        $dashID   = (int)$dec;
        $b64Param = $dashboardParam;
        $intParam = (string)$dashID;
        $debug[]  = "B64 input ⇒ intParam={$intParam}, dashID={$dashID}, b64Param={$b64Param}";
    }
    else {
        $debug[] = "Invalid format: neither INT nor valid INT-B64";
        return ['authorized'=>false, 'debug'=>$debug];
    }
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    $enc_username = encryptOSSL(
        strtolower($username),
        $encryptionInitKey,
        $encryptionIvKey,
        $encryptionMethod
    );
    $debug[] = "Encrypted username={$enc_username}";
    //Fetch matching AccessDefinitions
    $adSql = "
      SELECT ID, maxbyday, maxbymonth, maxtotalaccesses
        FROM AccessDefinitions
       WHERE dashboardID = ?
          OR dashboardID = ?
          OR CAST(dashboardID AS UNSIGNED) = ?
    ";
    if (! $adStmt = mysqli_prepare($link, $adSql)) {
        $debug[] = "AD prepare failed: " . mysqli_error($link);
        return ['authorized'=>false, 'debug'=>$debug];
    }
    mysqli_stmt_bind_param($adStmt, 'ssi', $intParam, $b64Param, $dashID);
    if (! mysqli_stmt_execute($adStmt)) {
        $debug[] = "AD execute failed: " . mysqli_stmt_error($adStmt);
        mysqli_stmt_close($adStmt);
        return ['authorized'=>false, 'debug'=>$debug];
    }
    //BUFFER all rows so we can open new statements inside the loop
    mysqli_stmt_store_result($adStmt);
    mysqli_stmt_bind_result($adStmt, $defID, $maxByDay, $maxByMonth, $maxTotal);
    $hitAny = false;
    while (mysqli_stmt_fetch($adStmt)) {
        $hitAny = true;
        $debug[] = "Found AD row: defID={$defID}, maxByDay={$maxByDay}, maxByMonth={$maxByMonth}, maxTotal={$maxTotal}";
        //Direct ACL?
        $debug[] = "Checking direct ACL for defID={$defID}";
        $aclSql = "SELECT 1 FROM ACL WHERE `user` = ? AND defID = ? LIMIT 1";
        if (! $aclStmt = mysqli_prepare($link, $aclSql)) {
            $debug[] = "  ACL prepare failed: " . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($aclStmt, 'si', $enc_username, $defID);
            mysqli_stmt_execute($aclStmt);
            mysqli_stmt_store_result($aclStmt);
            $n = mysqli_stmt_num_rows($aclStmt);
            $debug[] = "  ACL num_rows={$n}";
            if ($n > 0) {
                $debug[] = "  → Direct ACL HIT";
                if($maxByDay   !== null || $maxByMonth !== null || $maxTotal  !== null){
                    if (check_userstats_accesses_dashboards(
                            $enc_username, $dashID, $maxByDay, $maxByMonth, $maxTotal
                        )) {
                        mysqli_stmt_close($aclStmt);
                        mysqli_stmt_close($adStmt);
                        return [
                            'authorized'    => true,
                            'authorized_by' => 'Direct_ACL'
                        ];
                    }
                    $debug[] = "Usage-limit FAILED (direct)";
                } else {
                    $debug[] = "No usage limits detected, authorizing.";
                    return [
                        'authorized'    => true,
                        'authorized_by' => 'Direct_ACL',
                        //'debug'      => $debug
                    ];
                    
            }
            }
            mysqli_stmt_close($aclStmt);
        }
        //Profile-based ACL?
        $debug[] = "Checking profile ACLs";
        $paSql = "SELECT profileID FROM ACLProfilesAssignment WHERE `user` = ?";
        if (! $paStmt = mysqli_prepare($link, $paSql)) {
            $debug[] = "  PA prepare failed: " . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($paStmt, 's', $enc_username);
            mysqli_stmt_execute($paStmt);
            mysqli_stmt_store_result($paStmt);
            mysqli_stmt_bind_result($paStmt, $profileID);
            $foundProfile = false;
            while (mysqli_stmt_fetch($paStmt)) {
                $foundProfile = true;
                $debug[] = "Profile assignment: profileID={$profileID}";
                $pSql = "SELECT authIDs, profilename FROM ACLProfiles WHERE ID = ?";
                if (! $pStmt = mysqli_prepare($link, $pSql)) {
                    $debug[] = "Profile prepare failed: " . mysqli_error($link);
                    continue;
                }
                mysqli_stmt_bind_param($pStmt, 'i', $profileID);
                mysqli_stmt_execute($pStmt);
                mysqli_stmt_bind_result($pStmt, $rawAuthIDs, $profileName);
                if (mysqli_stmt_fetch($pStmt)) {
                    $debug[] = "Loaded profile='{$profileName}', authIDs='{$rawAuthIDs}'";
                    $ids = array_map('intval',
                        array_filter(array_map('trim', explode(',', $rawAuthIDs)))
                    );
                    if (in_array($defID, $ids, true)) {
                        $debug[] = "    → Profile '{$profileName}' includes defID={$defID}";
                        if($maxByDay   !== null || $maxByMonth !== null || $maxTotal  !== null){
                        if (check_userstats_accesses_dashboards(
                                $enc_username, $dashID, $maxByDay, $maxByMonth, $maxTotal
                            )) {
                            mysqli_stmt_close($pStmt);
                            mysqli_stmt_close($paStmt);
                            mysqli_stmt_close($adStmt);
                            return [
                                'authorized'    => true,
                                'authorized_by' => $profileName
                            ];
                        }
                        $debug[] = "Usage-limit FAILED (profile)";
                    } else{
                        $debug[] = "No usage limits detected, authorizing.";
                        return [
                                'authorized'    => true,
                                'authorized_by' => $profileName,
                                'debug'      => $debug
                            ];
                    }
                    } else {
                        $debug[] = "Profile does NOT include defID={$defID}";
                    }
                } else {
                    $debug[] = "No ACLProfiles row for ID={$profileID}";
                }
                mysqli_stmt_close($pStmt);
            }
            if (! $foundProfile) {
                $debug[] = "No profiles assigned to user";
            }
            mysqli_stmt_close($paStmt);
        }
    }
    mysqli_stmt_close($adStmt);
    if (! $hitAny) {
        $debug[] = "No AccessDefinition matched dashboardID";
        return [
        'authorized' => true,
        'debug'      => $debug
    ];
    }
    return [
        'authorized' => false,
        'debug'      => $debug
    ];
}

//Check if requested user has access to that collection ID
//FOR INTERNAL: expected $data= ["preferred_username":"required", "collection_id":"required"]
function ACLAPI_check_collection($data = []): array {
    $link = getDbLink();
    $debug = [];
    $username = $data['preferred_username'] ?? '';
    $collectionParam = $data['collection_id'] ?? '';
    $debug[] = "Requested internal user={$username}";
    $debug[] = "Raw collection_id param='{$collectionParam}'";
    if ($collectionParam === '') {
        http_response_code(400);
        return [
            'authorized' => false,
            'debug'      => $debug,
            'error'      => 'collection_id parameter is required'
        ];
    }
    if ($username === '') {
        http_response_code(400);
        return [
            'authorized' => false,
            'debug'      => $debug,
            'error'      => 'preferred_username parameter is required'
        ];
    }
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    $enc_username = encryptOSSL(
        strtolower($username),
        $encryptionInitKey,
        $encryptionIvKey,
        $encryptionMethod
    );
    $debug[] = "Encrypted username={$enc_username}";
    //Fetch matching AccessDefinitions
    $adSql = "
      SELECT ID
        FROM AccessDefinitions
       WHERE collectionID = ?
    ";
    if (! $adStmt = mysqli_prepare($link, $adSql)) {
        $debug[] = "AD prepare failed: " . mysqli_error($link);
        return ['authorized'=>false, 'debug'=>$debug];
    }
    mysqli_stmt_bind_param($adStmt, 's', $collectionParam);
    if (! mysqli_stmt_execute($adStmt)) {
        $debug[] = "AD execute failed: " . mysqli_stmt_error($adStmt);
        mysqli_stmt_close($adStmt);
        return ['authorized'=>false, 'debug'=>$debug];
    }
    // buffer so we can open new stmts inside loop
    mysqli_stmt_store_result($adStmt);
    mysqli_stmt_bind_result($adStmt, $defID);
    $hitAny = false;
    while (mysqli_stmt_fetch($adStmt)) {
        $hitAny = true;
        $debug[] = "Found AD row: defID={$defID}";
        //Direct ACL?
        $debug[] = "→ Checking direct ACL for defID={$defID}";
        $aclSql = "SELECT 1 FROM ACL WHERE `user` = ? AND defID = ? LIMIT 1";
        if (! $aclStmt = mysqli_prepare($link, $aclSql)) {
            $debug[] = "  ACL prepare failed: " . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($aclStmt, 'si', $enc_username, $defID);
            mysqli_stmt_execute($aclStmt);
            mysqli_stmt_store_result($aclStmt);
            $n = mysqli_stmt_num_rows($aclStmt);
            $debug[] = "  ACL num_rows={$n}";
            if ($n > 0) {
                $debug[] = "  → Direct ACL HIT";
                // TODO: re-enable usage limits when collections are supported in userstats:
                // if (! check_userstats_accesses_collections($enc_username, $defID, ...)) {
                //     $debug[] = "  → Usage-limit FAILED (direct)";
                //     mysqli_stmt_close($aclStmt);
                //     continue;
                // }
                mysqli_stmt_close($aclStmt);
                mysqli_stmt_close($adStmt);
                return [
                    'authorized'    => true,
                    'authorized_by' => 'Direct_ACL'
                ];
            }
            $debug[] = "  → Direct ACL MISS";
            mysqli_stmt_close($aclStmt);
        }
        //Profile-based ACL?
        $debug[] = "→ Checking profile ACLs";
        $paSql = "SELECT profileID FROM ACLProfilesAssignment WHERE `user` = ?";
        if (! $paStmt = mysqli_prepare($link, $paSql)) {
            $debug[] = "  PA prepare failed: " . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($paStmt, 's', $enc_username);
            mysqli_stmt_execute($paStmt);
            mysqli_stmt_bind_result($paStmt, $profileID);

            $foundProfile = false;
            while (mysqli_stmt_fetch($paStmt)) {
                $foundProfile = true;
                $debug[] = "  Profile assignment: profileID={$profileID}";

                $pSql = "SELECT authIDs, profilename FROM ACLProfiles WHERE ID = ?";
                if (! $pStmt = mysqli_prepare($link, $pSql)) {
                    $debug[] = "    Profile prepare failed: " . mysqli_error($link);
                    continue;
                }
                mysqli_stmt_bind_param($pStmt, 'i', $profileID);
                mysqli_stmt_execute($pStmt);
                mysqli_stmt_bind_result($pStmt, $rawAuthIDs, $profileName);

                if (mysqli_stmt_fetch($pStmt)) {
                    $debug[] = "    Loaded profile='{$profileName}', authIDs='{$rawAuthIDs}'";
                    $ids = array_map('intval',
                        array_filter(array_map('trim', explode(',', $rawAuthIDs)))
                    );
                    if (in_array($defID, $ids, true)) {
                        $debug[] = "    → Profile '{$profileName}' includes defID={$defID}";

                        // TODO: re-enable usage limits when collections are supported in userstats:
                        // if (! check_userstats_accesses_collections($enc_username, $defID, ...)) {
                        //     $debug[] = "    → Usage-limit FAILED (profile)";
                        //     mysqli_stmt_close($pStmt);
                        //     continue;
                        // }

                        mysqli_stmt_close($pStmt);
                        mysqli_stmt_close($paStmt);
                        mysqli_stmt_close($adStmt);
                        return [
                            'authorized'    => true,
                            'authorized_by' => $profileName
                        ];
                    }
                    $debug[] = "    → Profile does NOT include defID={$defID}";
                } else {
                    $debug[] = "    No ACLProfiles row for ID={$profileID}";
                }
                mysqli_stmt_close($pStmt);
            }

            if (! $foundProfile) {
                $debug[] = "  No profiles assigned to user";
            }
            mysqli_stmt_close($paStmt);
        }
    }
    mysqli_stmt_close($adStmt);
    if (! $hitAny) {
        $debug[] = "→ No AccessDefinition matched collectionID";
        return [
        'authorized' => true,
        'debug'      => $debug
    ];
    }
    return [
        'authorized' => false,
        'debug'      => $debug
    ];
}

//Return user's allowed menuIDs from his list of acl+profiles
//FOR INTERNAL: expected $data= ["preferred_username":"required"]
function ACLAPI_check_menuIDs($data = []): array {
    $link = getDbLink();
    $username = $data['preferred_username'] ?? '';
    if ($username === '') {
        http_response_code(400);
        return [
            'authorized' => false,
            'error'      => 'preferred_username parameter is required'
        ];
    }
    //Encrypt username
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    $enc_username = encryptOSSL(
        strtolower($username),
        $encryptionInitKey,
        $encryptionIvKey,
        $encryptionMethod
    );
    $menuIDs = [];
    //Direct ACL
    $sql = "
      SELECT AD.menuID
        FROM ACL AS A
        JOIN AccessDefinitions AS AD
          ON A.defID = AD.ID
       WHERE A.`user` = ?
    ";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $enc_username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $mID);
        while (mysqli_stmt_fetch($stmt)) {
            if ($mID !== null) {
                $menuIDs[] = (int)$mID;
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("ACLAPI_check_menuIDs(): direct ACL prepare failed: " . mysqli_error($link));
    }
    //Profile-based ACL to authIDs to menuID
    //Get all profile IDs for this user
    $profileIDs = [];
    $paSql = "SELECT profileID FROM ACLProfilesAssignment WHERE `user` = ?";
    if ($paStmt = mysqli_prepare($link, $paSql)) {
        mysqli_stmt_bind_param($paStmt, 's', $enc_username);
        mysqli_stmt_execute($paStmt);
        mysqli_stmt_bind_result($paStmt, $pid);
        while (mysqli_stmt_fetch($paStmt)) {
            $profileIDs[] = $pid;
        }
        mysqli_stmt_close($paStmt);
    } else {
        error_log("ACLAPI_check_menuIDs(): profile assignment prepare failed: " . mysqli_error($link));
    }

    // For each profile, parse its authIDs and fetch menuIDs
    foreach ($profileIDs as $profileID) {
        $pSql = "SELECT authIDs FROM ACLProfiles WHERE ID = ?";
        if ($pStmt = mysqli_prepare($link, $pSql)) {
            mysqli_stmt_bind_param($pStmt, 'i', $profileID);
            mysqli_stmt_execute($pStmt);
            mysqli_stmt_bind_result($pStmt, $rawAuthIDs);
            if (mysqli_stmt_fetch($pStmt) && $rawAuthIDs !== null) {
                // parse comma-separated IDs
                $auths = array_filter(
                    array_map('trim', explode(',', $rawAuthIDs)),
                    function($v) {
                        return $v !== '';
                    });
                $auths = array_map('intval', $auths);
                //for each authID, grab its menuID
                foreach ($auths as $defID) {
                    $mdSql = "SELECT menuID FROM AccessDefinitions WHERE ID = ? LIMIT 1";
                    if ($mdStmt = mysqli_prepare($link, $mdSql)) {
                        mysqli_stmt_bind_param($mdStmt, 'i', $defID);
                        mysqli_stmt_execute($mdStmt);
                        mysqli_stmt_bind_result($mdStmt, $mID2);
                        if (mysqli_stmt_fetch($mdStmt) && $mID2 !== null) {
                            $menuIDs[] = (int)$mID2;
                        }
                        mysqli_stmt_close($mdStmt);
                    }
                }
            }
            mysqli_stmt_close($pStmt);
        }
    }
    //Dedupe and reindex
    $menuIDs = array_values(array_unique($menuIDs, SORT_NUMERIC));
    return $menuIDs;
}

?>