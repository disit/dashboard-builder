<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence
   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
requireAdmin();

$link = getDbLink();       
$ldap = getLdapConn();   

$action = $_REQUEST['action'];
switch ($action) {
    case 'get_list':
        header('Content-Type: application/json');
        //pull server-side pagination params from GET
        $limit  = isset($_GET['limit'])  ? intval($_GET['limit'])  : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $search = trim($_GET['search']  ?? '');
        $sort   = $_GET['sort']   ?? 'username';
        $order  = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc')
                   ? SORT_DESC
                   : SORT_ASC;
        $roleFilter = trim($_GET['role'] ?? '');
        //get the full user list
        $allUsers = buildUserList($link, $ldap, $roleFilter);
        //filter by search term if present
        if ($search !== '') {
            $searchLower = mb_strtolower($search, 'UTF-8');
            $allUsers = array_filter($allUsers, function($u) use ($searchLower) {
                return
                   mb_stripos($u['username'],     $searchLower, 0, 'UTF-8') !== false
                || mb_stripos($u['mail'],         $searchLower, 0, 'UTF-8') !== false
                || mb_stripos(mb_strtolower($u['admin'], 'UTF-8'), $searchLower, 0, 'UTF-8') !== false
                || mb_stripos($u['organization'], $searchLower, 0, 'UTF-8') !== false;
            });
        }
        //sort by the requested field
        usort($allUsers, function($a, $b) use ($sort, $order) {
            $va = $a[$sort] ?? '';
            $vb = $b[$sort] ?? '';
            if ($va == $vb) return 0;
            // numeric vs string compare
            if (is_numeric($va) && is_numeric($vb)) {
                return $order === SORT_ASC ? ($va - $vb) : ($vb - $va);
            }
            return $order === SORT_ASC
                ? strcasecmp($va, $vb)
                : strcasecmp($vb, $va);
        });
        //compute total and slice the page
        $total = count($allUsers);
        $rows  = array_slice($allUsers, $offset, $limit);
        //emit { total, rows }
        echo json_encode([
            'total' => $total,
            'rows'  => array_values($rows),
        ]);
        break;

    case 'add_user':
        AddUser($link, $ldap);
        break;

    case 'edit_user':
        EditUser($link, $ldap);
        break;

    case 'list_org':
        ListOrg($ldap);
        break;

    case 'delete_user':
        DeleteUser($ldap);
        break;

    case 'get_groups':
        GetGroups($link, $ldap);
        break;

    default:
        break;
}
exit;

function requireAdmin() {
    if (
        empty($_SESSION['loggedUsername'])
        || empty($_SESSION['refreshToken'])
        || empty($_SESSION['loggedRole'])
        || $_SESSION['loggedRole'] !== 'RootAdmin'
    ) {
        echo "You are not authorized to access ot this data!";
        exit;
    }
    global $ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint;
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(['token_endpoint' => $ssoTokenEndpoint]);
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $_SESSION['refreshToken'] = $tkn->refresh_token;
}

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

function hash_password($password) { // SSHA with random 4-character salt
    $salt = substr(str_shuffle(str_repeat(
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4
    )), 0, 4);
    return '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
}

function control_mail($ldapServer, $ldapPort, $ldapBaseDN, $mail) {
    $connection = ldap_connect($ldapServer, $ldapPort)
        or die("That LDAP-URI was not parseable");
    $attr   = ['mail'];
    $filter = "(mail=" . $mail . ")";
    $result = ldap_search($connection, $ldapBaseDN, $filter, $attr);
    $entries = ldap_get_entries($connection, $result);
    return $entries['count'];
}

function check_csbl($link, $user) {
    $sql = "SELECT 1 FROM TrustedUsers WHERE userName = ? LIMIT 1";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("check_csbl(): prepare failed: " . mysqli_error($link));
        return mysqli_error($link);
    }
    mysqli_stmt_bind_param($stmt, 's', $user);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("check_csbl(): execute failed: " . mysqli_error($link));
        return mysqli_error($link);
    }
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

function add_csbl($link, $user, $add) {
    if ($add === "true") {
        if (check_csbl($link, $user) === false) {
            $sql = "INSERT INTO TrustedUsers (userName) VALUES (?)";
        } else {
            return true; // already present
        }
    } else {
        $sql = "DELETE FROM TrustedUsers WHERE userName = ?";
    }
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("add_csbl(): prepare failed: " . mysqli_error($link));
        return mysqli_error($link);
    }
    mysqli_stmt_bind_param($stmt, 's', $user);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("add_csbl(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
    return true;
}

function check_data_table_user($link, $user) {
    $sql = "
        SELECT organizationName
        FROM Organizations
        WHERE FIND_IN_SET(?, users)
    ";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("check_data_table_user(): prepare failed: " . mysqli_error($link));
        return mysqli_error($link);
    }
    mysqli_stmt_bind_param($stmt, 's', $user);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("check_data_table_user(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return mysqli_error($link);
    }
    mysqli_stmt_bind_result($stmt, $orgName);
    $orgs = [];
    while (mysqli_stmt_fetch($stmt)) {
        $orgs[] = $orgName;
    }
    mysqli_stmt_close($stmt);
    return empty($orgs) ? false : $orgs;
}

function add_data_table_user($link, $user, $add, $orgs) {
    if (! is_array($orgs) || empty($orgs)) {
        return "No organizations";
    }
    if ($add === "true") {
        $current = check_data_table_user($link, $user);
        if (is_string($current)) {
            return $current;
        }
        $currentOrgs = $current === false ? [] : $current;
        $toAdd    = array_values(array_diff($orgs,       $currentOrgs));
        $toRemove = array_values(array_diff($currentOrgs, $orgs));

        if (! empty($toRemove)) {
            $ph = implode(',', array_fill(0, count($toRemove), '?'));
            $sql = "
                UPDATE Organizations
                SET users = NULLIF(
                    TRIM(BOTH ',' FROM REPLACE(
                        CONCAT(',', COALESCE(users, ''), ','), 
                        CONCAT(',', ?, ','), 
                        ','
                    )),
                    ''
                )
                WHERE organizationName IN ($ph)
            ";
            $stmt = mysqli_prepare($link, $sql)
                or error_log("add_data_table_user(): remove prepare failed: " . mysqli_error($link));
            $types  = 's' . str_repeat('s', count($toRemove));
            $values = array_merge([$user], $toRemove);
            $bind   = [$types];
            foreach ($values as $i => &$v) {
                $bind[] = &$values[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind);
            if (! mysqli_stmt_execute($stmt)) {
                error_log("add_data_table_user(): remove execute failed: " . mysqli_error($link));
                mysqli_stmt_close($stmt);
                return mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }

        if (! empty($toAdd)) {
            $ph = implode(',', array_fill(0, count($toAdd), '?'));
            $sql = "
                UPDATE Organizations
                SET users = CASE
                    WHEN users IS NULL OR users = '' THEN ?
                    ELSE CONCAT(users, ',', ?)
                END
                WHERE organizationName IN ($ph)
                  AND (users IS NULL OR users = '' OR NOT FIND_IN_SET(?, users))
            ";
            $stmt = mysqli_prepare($link, $sql)
                or error_log("add_data_table_user(): add prepare failed: " . mysqli_error($link));
            $types  = str_repeat('s', 2 + count($toAdd) + 1);
            $values = array_merge([$user, $user], $toAdd, [$user]);
            $bind   = [$types];
            foreach ($values as $i => &$v) {
                $bind[] = &$values[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind);
            if (! mysqli_stmt_execute($stmt)) {
                error_log("add_data_table_user(): add execute failed: " . mysqli_error($link));
                mysqli_stmt_close($stmt);
                return mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }

        return true;
    } else {
        // remove from all
        $sql = "
            UPDATE Organizations
            SET users = NULLIF(
                TRIM(BOTH ',' FROM REPLACE(
                    CONCAT(',', COALESCE(users, ''), ','), 
                    CONCAT(',', ?, ','), 
                    ','
                )),
                ''
            )
        ";
        $stmt = mysqli_prepare($link, $sql)
            or error_log("add_data_table_user(): prepare failed: " . mysqli_error($link));
        mysqli_stmt_bind_param($stmt, 's', $user);
        if (! mysqli_stmt_execute($stmt)) {
            error_log("add_data_table_user(): execute failed: " . mysqli_error($link));
            mysqli_stmt_close($stmt);
            return mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
        return true;
    }
}
function get_user_ldap_groups($ldapConn, $baseDn, $username) {
    // escape for use in a filter
    $uFilter   = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
    $uFilterlower   = ldap_escape(strtolower($username), '', LDAP_ESCAPE_FILTER);
    // build the full DN and escape that too
    $userDn    = 'cn=' . ldap_escape($username, '', LDAP_ESCAPE_DN) . ',' . $baseDn;
    $userDnlower    = 'cn=' . ldap_escape(strtolower($username), '', LDAP_ESCAPE_DN) . ',' . $baseDn;
    $dnFilter  = ldap_escape($userDn, '', LDAP_ESCAPE_FILTER);
    $dnFilterlower  = ldap_escape($userDnlower, '', LDAP_ESCAPE_FILTER);
    // Look for any of the three common group types,
    // matching either memberUid=username, memberUid=fullDN, member=fullDN, or uniqueMember=fullDN
    $filter = '(&(|(objectClass=groupOfNames)'
                       . '(objectClass=posixGroup)'
                       . '(objectClass=groupOfUniqueNames))'
                  . '(|(memberUid='   . $uFilter  . ')'
                  . '(memberUid='   . $uFilterlower  . ')'
                  . '(memberUid='   . $dnFilter . ')'
                  . '(memberUid='   . $dnFilterlower . ')'
                  . '(member='      . $dnFilter . ')'
                  . '(uniqueMember='. $dnFilter . ')))';
    $attrs = ['cn'];
    $sr = @ldap_search($ldapConn, $baseDn, $filter, $attrs);
    if ($sr === false) {
        return [];
    }
    $entries = ldap_get_entries($ldapConn, $sr);
    $groups = [];
    for ($i = 0; $i < $entries['count']; $i++) {
        if (empty($entries[$i]['cn'][0])) continue;
        $groups[] = [
            'cn' => $entries[$i]['cn'][0],
            'dn' => $entries[$i]['dn'],
        ];
    }
    return $groups;
}
function set_delegated_userstats_orgs(string $owner, array $orgs){
    global $resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort;
    $link2 = mysqli_connect($resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort);
    $csv = implode(',', $orgs);
    $sql = "UPDATE `users` 
            SET `delegated_orgs` = ? 
            WHERE `owner` = ?";
    if (! $stmt = mysqli_prepare($link2, $sql)) {
        error_log("setDelegatedOrgs(): prepare failed: " . mysqli_error($link2));
        return mysqli_error($link2);
    }
    mysqli_stmt_bind_param($stmt, 'ss', $csv, $owner);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("setDelegatedOrgs(): execute failed: " . mysqli_error($link2));
        mysqli_stmt_close($stmt);
        return mysqli_error($link2);
    }
    mysqli_stmt_close($stmt);
    return true;
}
function get_delegated_userstats_orgs(string $owner, bool $encrypted){
    global $resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort,
           $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    if(!$encrypted){
        $owner = encryptOSSL($owner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    }
    $link2 = mysqli_connect($resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort);
    $sql = "SELECT delegated_orgs
            FROM `users`
            WHERE `owner` = ?";
    if (! $stmt = mysqli_prepare($link2, $sql)) {
        error_log("getDelegatedOrgs(): prepare failed: " . mysqli_error($link2));
        return [];
    }
    mysqli_stmt_bind_param($stmt, 's', $owner);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("getDelegatedOrgs(): execute failed: " . mysqli_error($link2));
        mysqli_stmt_close($stmt);
        return [];
    }
    mysqli_stmt_bind_result($stmt, $csv);
    $orgs = [];
    if (mysqli_stmt_fetch($stmt) && $csv !== null && $csv !== '') {
        $orgs = array_map('trim', explode(',', $csv));
    }
    mysqli_stmt_close($stmt);
    return $orgs;
}
function findGroupDn($ldap, $baseDn, $cn) {
    $filter = '(&(cn='
            . ldap_escape($cn, '', LDAP_ESCAPE_FILTER)
            . ')(|(objectClass=groupOfNames)'
            .     '(objectClass=posixGroup)'
            .     '(objectClass=groupOfUniqueNames)))';
    $sr = @ldap_search($ldap, $baseDn, $filter, ['dn']);
    if ($sr === false) {
        return false;
    }
    $ents = ldap_get_entries($ldap, $sr);
    return ($ents['count'] > 0)
         ? $ents[0]['dn']
         : false;
}

//Action handlers

function buildUserList($link, $ldap, string $roleFilter = '') {
    global $ldapBaseDN, $ldapToolGroups, $dbname, $userMonitoring;
    $usersOut = [];
    // If a roleFilter is specified, load only that group's members
    if ($roleFilter !== '' && $roleFilter !== 'Observer') {
        // fetch the group entry for the given role
        $groupSearch = @ldap_search(
            $ldap,
            $ldapBaseDN,
            "(cn={$roleFilter})",
            ['roleOccupant']
        );
        $groupEnt = ldap_get_entries($ldap, $groupSearch);
        if (! empty($groupEnt[0]['roleoccupant'])) {
            $idx = 0;
            // loop each DN listed as a roleOccupant
            for ($i = 0; $i < $groupEnt[0]['roleoccupant']['count']; $i++) {
                $userDn = $groupEnt[0]['roleoccupant'][$i];
                // read just that one user entry
                $userSearch = @ldap_read(
                    $ldap,
                    $userDn,
                    '(objectClass=inetOrgPerson)',
                    ['cn','mail','ou']
                );
                $userEnts = ldap_get_entries($ldap, $userSearch);
                if ($userEnts['count'] === 0) {
                    continue;
                }
                $e = $userEnts[0];
                // extract username from DN
                if (! preg_match('/^cn=([^,]+)/i', $e['dn'], $m)) {
                    continue;
                }
                $username = $m[1];
                $email    = $e['mail'][0] ?? '';
                // collect OU-based organizations
                $orgs = [];
                if (! empty($e['ou']['count'])) {
                    for ($j = 0; $j < $e['ou']['count']; $j++) {
                        $orgs[] = $e['ou'][$j];
                    }
                }
                $orgString = implode(', ', $orgs);
                // assemble output row
                $usersOut[] = [
                    "IdUser"       => (string)$idx++,
                    "username"     => $username,
                    "organization" => $orgString,
                    "status"       => null,
                    "reg_data"     => null,
                    "password"     => null,
                    "mail"         => $email,
                    "admin"        => $roleFilter,
                    "cn"           => $e['dn'],
                    "csbl"         => (check_csbl($link, $username) === true),
                    "data_table"   => is_array(check_data_table_user($link, $username)),
                    "groups"       => get_user_ldap_groups($ldap, $ldapBaseDN, $username),
                    "delegated_userstats_orgs" =>
                         ($userMonitoring === 'true')
                         ? get_delegated_userstats_orgs($username, false)
                         : [],
                ];
            }
        }

        return $usersOut;
    }
    // NO ROLE FILTER
    //role to dn map
    $roles = ['ToolAdmin','RootAdmin','Manager','AreaManager','Observer'];
    $roleMembers = [];
    foreach ($roles as $r) {
        $rRes = ldap_search($ldap, $ldapBaseDN, "(cn=$r)", ['roleOccupant']);
        $rEnt = ldap_get_entries($ldap, $rRes);
        $roleMembers[$r] = array_map('strtolower', $rEnt[0]['roleoccupant'] ?? []);
    }
    mysqli_select_db($link, $dbname);
    //get all users
    $srUsers = ldap_search(
        $ldap,
        $ldapBaseDN,
        '(objectClass=inetOrgPerson)',
        ['cn','mail']
    );
    if ($srUsers === false) {
        error_log("buildUserList(): could not fetch users: " . ldap_error($ldap));
        return [];
    }
    $entriesUsers = ldap_get_entries($ldap, $srUsers);
    //iterate and build each user
    for ($i = 0; $i < $entriesUsers['count']; $i++) {
        $e  = $entriesUsers[$i];
        $dn = $e['dn'] ?? '';
        // extract username
        if (! preg_match('/^cn=([^,]+)/i', $dn, $m)) {
            continue;
        }
        $username = $m[1];
        $email    = $e['mail'][0] ?? '';
        //fetch orgs via 'l' from OUs
        $dnFilter  = ldap_escape($dn, '', LDAP_ESCAPE_FILTER);
        $orgFilter = '(&(objectClass=organizationalUnit)(l=' . $dnFilter . '))';
        $srOrgs    = @ldap_search($ldap, $ldapBaseDN, $orgFilter, ['ou']);
        $orgs      = [];
        if ($srOrgs !== false) {
            $entriesOrgs = ldap_get_entries($ldap, $srOrgs);
            for ($j = 0; $j < $entriesOrgs['count']; $j++) {
                if (! empty($entriesOrgs[$j]['ou'][0])) {
                    $orgs[] = $entriesOrgs[$j]['ou'][0];
                }
            }
        }
        $orgString = implode(', ', $orgs);
        // find this user’s role by seeing which roleOccupant list contains their DN
        $dnLower = strtolower($dn);
        $role    = null;
        foreach ($roleMembers as $rName => $dns) {
            if (in_array($dnLower, $dns, true)) {
                $role = $rName;
                break;
            }
        }
        // check CSBL & data-table flags
        $has_csbl        = (check_csbl($link, $username) === true);
        $data_table_user = is_array(check_data_table_user($link, $username));
        // fetch LDAP groups
        $groups = get_user_ldap_groups($ldap, $ldapBaseDN, $username);
        // fetch delegated orgs if enabled
        if ($userMonitoring == 'true') {
            $delegated_userstats_orgs = get_delegated_userstats_orgs($username, false);
        } else {
            $delegated_userstats_orgs = [];
        }
        // push into the output
        $usersOut[] = [
            "IdUser"       => (string)$i,
            "username"     => $username,
            "organization" => $orgString,
            "status"       => null,
            "reg_data"     => null,
            "password"     => null,
            "mail"         => $email,
            "admin"        => $role ?? 'Observer',
            "cn"           => $dn,
            "csbl"         => $has_csbl,
            "data_table"   => $data_table_user,
            "groups"       => $groups,
            "delegated_userstats_orgs" => $delegated_userstats_orgs,
        ];
    }
    if ($roleFilter === 'Observer') {
        $usersOut = array_filter($usersOut, function($u){
            return isset($u['admin']) && $u['admin'] === 'Observer';
        });
        // re-index if you want contiguous numeric IdUser
        $usersOut = array_values($usersOut);
    }
    return $usersOut;
}

function AddUser($link, $ldap) {
    global $ldapServer, $ldapPort, $ldapBaseDN, $ldapAdminDN, $ldapAdminPwd,
           $encryptionInitKey, $encryptionIvKey, $encryptionMethod, $ldapToolGroups, $dbname, $userMonitoring;

    $results = [];
    // basic input checks
    if (empty($_POST['new_username']) || trim($_POST['new_username']) === '') {
        $results['result'] = 'nousername';
        echo json_encode($results);
        return;
    }
    if (empty($_POST['new_password']) || trim($_POST['new_password']) === '') {
        $results['result'] = 'nopassword';
        echo json_encode($results);
        return;
    }
    if (empty($_POST['new_email']) || trim($_POST['new_email']) === '') {
        $results['result'] = 'noemail';
        echo json_encode($results);
        return;
    }

    // sanitize
    $new_username = htmlspecialchars($_POST['new_username']);
    $newPassw     = htmlspecialchars($_POST['new_password']);
    $mail         = htmlspecialchars($_POST['new_email']);
    $new_userType = ! empty($_POST['new_userType'])
                 ? htmlspecialchars($_POST['new_userType'])
                 : 'Manager';
    $group = (isset($_POST['group']) && is_array($_POST['group']))
           ? $_POST['group'] : [];

    $orgs = isset($_POST['org']) ? $_POST['org'] : [];
    if (! is_array($orgs)) {
        $orgs = [$orgs];
    }
    if (empty($orgs)) {
        $results['result'] = 'noorg';
        echo json_encode($results);
        return;
    }
    $primaryOrg = $orgs[0];

    if (control_mail($ldapServer, $ldapPort, $ldapBaseDN, $mail) !== 0) {
        $results['result'] = 'emailnotunique';
        echo json_encode($results);
        return;
    }

    // build LDAP entry
    $uid = encryptOSSL($new_username, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    $data = [
        'objectClass'   => ['inetOrgPerson'],
        'sn'            => strtolower($new_username),
        'uid'           => $uid,
        'userPassword'  => hash_password($newPassw),
        'ou'            => $orgs,
    ];
    $dn = "cn=" . strtolower($new_username) . ",{$ldapBaseDN}";

    if (! @ldap_add($ldap, $dn, $data)) {
        $results['result']    = 'error';
        $results['ldapErrNo'] = ldap_errno($ldap);
        $results['ldapMsg']   = ldap_error($ldap);
        echo json_encode($results);
        return;
    }

    // link in OUs and role/groups
    foreach ($orgs as $o) {
        @ldap_mod_add($ldap, "ou={$o},{$ldapBaseDN}", ['l' => $dn]);
    }
    if ($new_userType !== 'Observer') {
        @ldap_mod_add($ldap, "cn={$new_userType},{$ldapBaseDN}", ['roleOccupant' => $dn]);
    }
    foreach (explode(',', $ldapToolGroups) as $tg) {
        @ldap_mod_add($ldap, "cn={$tg},{$ldapBaseDN}", ['memberUid' => $dn]);
    }
    if (! empty($group)) {
        foreach ($group as $g) {
            $groupDn = $g;
            $okUid    = @ldap_mod_add($ldap, $groupDn, ['memberUid'    => $dn]);
            $okMember = @ldap_mod_add($ldap, $groupDn, ['member'       => $dn]);
            $okUniq   = @ldap_mod_add($ldap, $groupDn, ['uniqueMember' => $dn]);
            $results['debug']["add_group_{$g}"] =
                ($okUid || $okMember || $okUniq)
                ? "added with at least one attribute"
                : ldap_error($ldap);
        }
    }
    @ldap_mod_replace($ldap, $dn, ['mail' => $mail]);

    $results['result'] = 'success';
    $csbl          = $_POST['csbl'] ?? '';
    $data_ingestion= $_POST['data_ingestion'] ?? '';

    if ($csbl === "true") {
        $results['add_csbl'] = add_csbl($link, $new_username, $csbl);
    }
    if ($data_ingestion === "true") {
        $results['data_ingestion'] = add_data_table_user(
            $link, $new_username, $data_ingestion, $orgs
        );
    }
    if($userMonitoring == 'true'){
        $delegated_userstats_orgs = $_POST['delegated_userstats_orgs'];
        if($delegated_userstats_orgs && $delegated_userstats_orgs != ''){
            $res['userstats'] = set_delegated_userstats_orgs($uid,$delegated_userstats_orgs);
        }
    }
    echo json_encode($results);
}

function EditUser($link, $ldap) {
    global $ldapBaseDN, $encryptionInitKey, $encryptionIvKey,
           $encryptionMethod, $dbname, $userMonitoring;

    $res = [
        'index'    => 1,
        'uid'      => 'not modified',
        'role'     => 'not modified',
        'password' => 'not modified',
        'mail'     => 'not modified',
        'org'      => 'not modified',
        'group'    => 'not modified',
        'debug'    => []
    ];
    $res['debug']['raw_post'] = $_POST;

    $user = strtolower(htmlspecialchars($_POST['user'] ?? ''));
    $dn   = "cn={$user},{$ldapBaseDN}";

    $search = ldap_search($ldap, $ldapBaseDN, "(cn=$user)", ['cn','ou']);
    $entries = ldap_get_entries($ldap, $search);
    $currentOrgs = [];
    if ($entries['count'] > 0 && isset($entries[0]['ou'])) {
        for ($i = 0; $i < $entries[0]['ou']['count']; $i++) {
            $currentOrgs[] = $entries[0]['ou'][$i];
        }
    }
    $res['debug']['currentOrgs'] = $currentOrgs;

    // update uid
    $newUid = encryptOSSL($user, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    if (@ldap_mod_replace($ldap, $dn, ['uid' => $newUid])) {
        $res['uid'] = 'OK';
    } else {
        $res['uid'] = 'error updating uid';
        $res['ldapError'] = ldap_error($ldap);
        $res['index'] = 0;
    }
    // parse new & old orgs from POST
    $newOrgs = [];
    if (isset($_POST['org'])) {
        if (is_string($_POST['org']) && substr($_POST['org'],0,1)==='[') {
            $newOrgs = json_decode($_POST['org'], true) ?: [];
        } elseif (is_array($_POST['org'])) {
            $newOrgs = $_POST['org'];
        } else {
            $newOrgs = [$_POST['org']];
        }
    }
    $oldOrgs = ! empty($currentOrgs)
             ? $currentOrgs
             : (isset($_POST['old_org']) 
                ? (is_string($_POST['old_org']) && substr($_POST['old_org'],0,1)==='['
                    ? json_decode($_POST['old_org'], true) ?: []
                    : (is_array($_POST['old_org']) ? $_POST['old_org'] : [$_POST['old_org']])
                  )
                : []
               );

    $res['debug']['oldOrgs'] = $oldOrgs;
    $res['debug']['newOrgs'] = $newOrgs;

    $orgsDiffer = (count(array_diff($oldOrgs, $newOrgs)) > 0)
               || (count(array_diff($newOrgs, $oldOrgs)) > 0);
    $res['debug']['orgsDiffer'] = $orgsDiffer;

    if ($orgsDiffer && ! empty($newOrgs)) {
        if (@ldap_mod_replace($ldap, $dn, ['ou' => $newOrgs])) {
            $res['org'] = 'OK';
            foreach ($oldOrgs as $o) {
                @ldap_mod_del($ldap, "ou={$o},{$ldapBaseDN}", ['l' => $dn]);
            }
            foreach ($newOrgs as $o) {
                $r = @ldap_mod_add($ldap, "ou={$o},{$ldapBaseDN}", ['l' => $dn]);
                $res['debug']["add_l_{$o}"] = $r ? "success" : ldap_error($ldap);
            }
        } else {
            $res['org'] = 'error modifying OU';
            $res['ldapOrgError'] = ldap_error($ldap);
            $res['index'] = 0;
        }
    }

    //role swap
    $oldRole = $_POST['old_role'] ?? '';
    $newRole = $_POST['role']     ?? '';

    //If Observer or blank → remove roles
    if ($newRole === 'Observer' || $newRole === '') {
        if ($oldRole && $oldRole !== '-') {
            @ldap_mod_del(
                $ldap,
                "cn={$oldRole},{$ldapBaseDN}",
                ['roleOccupant' => $dn]
            );
            $res['role'] = 'removed';
        } else {
            $res['role'] = 'none';
        }
    //else change
    } elseif ($newRole !== $oldRole) {
        if ($oldRole !== '-' && $oldRole !== '') {
            @ldap_mod_del(
                $ldap,
                "cn={$oldRole},{$ldapBaseDN}",
                ['roleOccupant' => $dn]
            );
        }
        if (@ldap_mod_add(
                $ldap,
                "cn={$newRole},{$ldapBaseDN}",
                ['roleOccupant' => $dn]
            )) {
            $res['role'] = 'OK';
        } else {
            $res['role']      = 'error modifying role';
            $res['ldapRoleError'] = ldap_error($ldap);
            $res['index']     = 0;
        }
    }

    // password change
    $confirmPass = $_POST['conf_password'] ?? '';
    $newPass     = $_POST['password']      ?? '';
    if ($newPass !== '' && $confirmPass !== '' && $newPass === $confirmPass) {
        $hash = hash_password($newPass);
        if (@ldap_mod_replace($ldap, $dn, ['userPassword' => $hash])) {
            $res['password'] = 'OK';
        } else {
            $res['password'] = 'error updating password';
            $res['ldapPassError'] = ldap_error($ldap);
            $res['index'] = 0;
        }
    }

    // mail change
    $oldMail = htmlspecialchars($_POST['old_mail'] ?? '');
    $newMail = htmlspecialchars($_POST['mail']     ?? '');
    if ($newMail && $newMail !== $oldMail) {
        if (control_mail($GLOBALS['ldapServer'], $GLOBALS['ldapPort'], $GLOBALS['ldapBaseDN'], $newMail) === 0
         && @ldap_mod_replace($ldap, $dn, ['mail' => $newMail])
        ) {
            $res['mail'] = 'OK';
        } else {
            $res['mail'] = 'error updating mail';
            $res['ldapMailError'] = ldap_error($ldap);
            $res['index'] = 0;
        }
    }
    //group changes
    $newGroups = [];
    if (isset($_POST['group'])) {
        if (is_string($_POST['group']) && substr($_POST['group'],0,1)==='[') {
            $newGroups = json_decode($_POST['group'], true) ?: [];
        } elseif (is_array($_POST['group'])) {
            $newGroups = $_POST['group'];
        } else {
            $newGroups = [$_POST['group']];
        }
    }
    $oldGroups = isset($_POST['old_groups'])
        ? (is_string($_POST['old_groups']) && substr($_POST['old_groups'],0,1)==='[')
            ? (json_decode($_POST['old_groups'], true) ?: [])
            : (is_array($_POST['old_groups'])
                ? $_POST['old_groups']
                : [$_POST['old_groups']])
        : [];
    $res['debug']['oldGroups'] = $oldGroups;
    $res['debug']['newGroups'] = $newGroups;

    $toAdd    = array_diff($newGroups, $oldGroups);
    $toRemove = array_diff($oldGroups, $newGroups);

    foreach ($toRemove as $grp) {
        $groupDn = $grp;
        foreach (['memberUid','member','uniqueMember'] as $attr) {
            @ldap_mod_del($ldap, $grp, [$attr => $dn]);
        }
        $res['debug']["remove_group_{$grp}"] = $r ? "removed" : ldap_error($ldap);
    }

    foreach ($toAdd as $grp) {
        $groupDn = $grp;
        $okUid    = @ldap_mod_add($ldap, $grp, ['memberUid'   => $dn]);
        $okMember = @ldap_mod_add($ldap, $grp, ['member'      => $dn]);
        $okUniq   = @ldap_mod_add($ldap, $grp, ['uniqueMember'=> $dn]);
        $res['debug']["add_group_{$grp}"] =
            ($okUid || $okMember || $okUniq)
            ? "added with at least one attr"
            : ldap_error($ldap);
    }
    $res['group'] = 'OK';

    // verify final OU set
    $vsearch = ldap_search($ldap, $ldapBaseDN, "(cn=$user)", ['ou']);
    $vent    = ldap_get_entries($ldap, $vsearch);
    $verifiedOrgs = [];
    if ($vent['count'] > 0 && isset($vent[0]['ou'])) {
        for ($i = 0; $i < $vent[0]['ou']['count']; $i++) {
            $verifiedOrgs[] = $vent[0]['ou'][$i];
        }
    }
    $res['debug']['verifiedOrgs'] = $verifiedOrgs;

    $csbl          = $_POST['csbl']           ?? '';
    $data_ingestion= $_POST['data_ingestion'] ?? '';

    if ($csbl) {
        $res['add_csbl'] = add_csbl($link, $user, $csbl);
    }
    if ($data_ingestion) {
        $res['data_ingestion'] = add_data_table_user(
            $link, $user, $data_ingestion, $verifiedOrgs
        );
    }
    if($userMonitoring == 'true'){
        $delegated_userstats_orgs = $_POST['delegated_userstats_orgs'];
        if($delegated_userstats_orgs && $delegated_userstats_orgs != ''){
            $res['userstats'] = set_delegated_userstats_orgs($newUid,$delegated_userstats_orgs);
        }
    }

    echo json_encode($res);
}

function ListOrg($ldap) {
    global $ldapBaseDN;
    error_reporting(E_ERROR);
    $search = ldap_search($ldap, $ldapBaseDN, '(objectClass=organizationalUnit)');
    $entriesOrg = ldap_get_entries($ldap, $search);
    $array_org = [];
    for ($i = 0; $i < $entriesOrg['count']; $i++) {
        $org = strval($entriesOrg[$i]['ou'][0]);
        $array_org[] = $org;
    }
    echo json_encode($array_org);
}

function DeleteUser($ldap) {
    global $ldapBaseDN, $ldapToolGroups;
    $results = [];
    //username → DN
    if (empty($_POST['username'])) {
        echo json_encode(['error'=>'nousername']);
        return;
    }
    $username = htmlspecialchars($_POST['username']);
    $dn       = "cn=" . strtolower($username) . ",{$ldapBaseDN}";
    //remove roles
    $roles = ['ToolAdmin','RootAdmin','Manager','AreaManager','Observer'];
    $results['roles'] = [];
    foreach ($roles as $role) {
        $roleDn = "cn={$role},{$ldapBaseDN}";
        $ok = @ldap_mod_del($ldap, $roleDn, ['roleOccupant' => $dn]);
        $results['roles'][$role] = $ok ? 'success' : 'failure';
    }
    //remove from orgs
    $dnFilter = ldap_escape($dn, '', LDAP_ESCAPE_FILTER);
    $ouFilter = "(&(objectClass=organizationalUnit)(l={$dnFilter}))";
    $srOus = @ldap_search($ldap, $ldapBaseDN, $ouFilter, ['ou']);
    $results['orgs'] = [];
    if ($srOus !== false) {
        $entries = ldap_get_entries($ldap, $srOus);
        for ($i = 0; $i < $entries['count']; $i++) {
            if (!empty($entries[$i]['ou'][0])) {
                $ou   = $entries[$i]['ou'][0];
                $ouDn = "ou={$ou},{$ldapBaseDN}";
                $ok   = @ldap_mod_del($ldap, $ouDn, ['l' => $dn]);
                $results['orgs'][$ou] = $ok ? 'success' : 'failure';
            }
        }
    } else {
        $results['orgs']['error'] = ldap_error($ldap);
    }
    //remove tool groups
    $results['toolGroups'] = [];
    foreach (explode(',', $ldapToolGroups) as $tg) {
        $tg = trim($tg);
        if ($tg === '') continue;
        $tgDn = "cn={$tg},{$ldapBaseDN}";
        $ok   = @ldap_mod_del($ldap, $tgDn, ['memberUid' => $dn]);
        $results['toolGroups'][$tg] = $ok ? 'success' : 'failure';
    }
    //Remove from all other LDAP groups (posixGroup, groupOfNames, groupOfUniqueNames)
    $groups = get_user_ldap_groups($ldap, $ldapBaseDN, $username);
    $results['groups'] = [];
    if (is_array($groups)) {
        foreach ($groups as $g) {
            $gDn   = "cn={$g},{$ldapBaseDN}";
            $ok1   = @ldap_mod_del($ldap, $gDn, ['memberUid'   => $dn]);
            $ok2   = @ldap_mod_del($ldap, $gDn, ['member'      => $dn]);
            $ok3   = @ldap_mod_del($ldap, $gDn, ['uniqueMember'=> $dn]);
            $results['groups'][$g] = ($ok1 || $ok2 || $ok3) ? 'success' : 'failure';
        }
    } else {
        // get_user_ldap_groups returned an error string
        $results['groups']['error'] = $groups;
    }
    //delete the user entry itself
    $okDel = @ldap_delete($ldap, $dn);
    $results['result'] = $okDel ? 'success' : 'error';
    echo json_encode($results);
}

function GetGroups($link, $ldap) {
    global $ldapBaseDN;
    //filter for groups
    $filter = '(|(objectClass=groupOfNames)'
            . '(objectClass=posixGroup)'
            . '(objectClass=groupOfUniqueNames))';
    $attrs = ['cn'];                
    $sr = @ldap_search($ldap, $ldapBaseDN, $filter, $attrs);
    $entries = ldap_get_entries($ldap, $sr);
    $groups  = [];
    for ($i = 0; $i < $entries['count']; $i++) {
        if (empty($entries[$i]['cn'][0])) continue;
        $groups[] = [
            'cn' => $entries[$i]['cn'][0],
            'dn' => $entries[$i]['dn'],
        ];
    }
    echo json_encode($groups);
}

