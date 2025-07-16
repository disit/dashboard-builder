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


/*TLDR:
get action from request and switch case
case get_list: get list of all AD (access definitions) from Dashboard.AccessDefinitions
case add_AD: add definition to Dashboard.AccessDefinitions
case update_ACL: add user's selected ACL to Dashboard.ACL and remove the ones deselected
case get_user_ACL : get list of ACL for a specific user from Dashboard.ACL
case get_list_ACL : select * from Dashboard.ACL
*/
include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;
session_start();
requireAdmin();

$link = getDbLink();       

$action = $_REQUEST['action'];
switch ($action) {
    case 'get_list_AD':
        header('Content-Type: application/json');
        echo json_encode(get_AD_list($link));
        break;

    case 'get_list_menus':
        header('Content-Type: application/json');
        echo json_encode(get_menu_list($link));
        break;

    case 'add_AD':
        add_access_definition($link);
        break;

    case 'edit_AD':
        edit_access_definition($link);
        break;

    case 'delete_AD':
        delete_access_definition($link);
        break;

    case 'update_ACL':
        update_ACL_list($link);
        break;

    case 'get_list_ACL':
        echo json_encode(get_list_ACL($link));
        break;

    case 'get_user_ACL':
        echo json_encode(get_user_ACL($link));
        break;

    case 'get_list_profiles':
        header('Content-Type: application/json');
        echo json_encode(get_profiles_list($link)); 
        break;

    case 'edit_profile':
        header('Content-Type: application/json');
        echo json_encode(edit_profile($link));
        break;

    case 'add_profile':
        header('Content-Type: application/json');
        echo json_encode(add_profile($link));
        break;

    case 'get_user_profiles':
        header('Content-Type: application/json');
        echo json_encode(get_user_profiles($link));
        break;

    case 'update_user_profiles':
        header('Content-Type: application/json');
        echo json_encode(update_user_profiles($link));
        break;

    default:
        break;
}
exit;

function requireAdmin() {
    //grab any Authorization header
    $auth = $_SERVER['HTTP_AUTHORIZATION']
         ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
         ?? '';
    if (!$auth && function_exists('apache_request_headers')) {
        foreach (apache_request_headers() as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $auth = $v;
                break;
            }
        }
    }
    //if it’s Bearer, call userinfo endpoint directly
    if (preg_match('/^Bearer\s+(\S+)$/i', $auth, $m)) {
        $token = $m[1];
        global $ssoUserinfoEndpoint;
        // init curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $ssoUserinfoEndpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Authorization: Bearer {$token}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR,    false);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);
        if ($body === false || $httpCode !== 200) {
            error_log("userinfo fetch failed (HTTP {$httpCode}): {$curlErr}");
            header('HTTP/1.1 401 Unauthorized');
            echo $body;
            exit;
        }
        // parse JSON
        $userinfo = json_decode($body, true);
        if (! is_array($userinfo)) {
            header('HTTP/1.1 401 Unauthorized');
            echo 'Invalid userinfo response';
            exit;
        }
        //pull roles from the JSON
        $roles = [];
        if (! empty($userinfo['roles']) && is_array($userinfo['roles'])) {
            $roles = $userinfo['roles'];
        } elseif (! empty($userinfo['role'])) {
            $roles = [ $userinfo['role'] ];
        } elseif (
            ! empty($userinfo['realm_access']['roles'])
            && is_array($userinfo['realm_access']['roles'])
        ) {
            $roles = $userinfo['realm_access']['roles'];
        }
        //must include RootAdmin
        if (in_array('RootAdmin', $roles, true)) {
            return;  //ok
        }
        header('HTTP/1.1 403 Forbidden');
        echo 'You are not authorized to access this data!';
        exit;
    }
    //session
    if (
        empty($_SESSION['loggedUsername'])
        || empty($_SESSION['refreshToken'])
        || empty($_SESSION['loggedRole'])
        || $_SESSION['loggedRole'] !== 'RootAdmin'
    ) {
        echo "You are not authorized to access to this data!";
        exit;
    }
    try {
        global $ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint;
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(['token_endpoint' => $ssoTokenEndpoint]);
        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $_SESSION['refreshToken'] = $tkn->refresh_token;
    } catch (\Exception $e) {
        error_log("OIDC refresh error: " . $e->getMessage());
        echo "Session refresh failed, please log in again.";
        exit;
    }
}

function getDbLink() {
    global $host, $username, $password, $dbname;
    $link = mysqli_connect($host, $username, $password, $dbname)
        or die("MySQL connect error: " . mysqli_error($link));
    return $link;
}

function reserveAcName(mysqli $link, string $name): void {
    $sql = "INSERT INTO ACNames (name) VALUES (?)";
    $stmt = mysqli_prepare($link, $sql);
    if (! $stmt) {
        throw new \Exception("reserveAcName(): " . mysqli_error($link));
    }
    mysqli_stmt_bind_param($stmt, "s", $name);
    if (! mysqli_stmt_execute($stmt)) {
        $errno = mysqli_errno($link);
        mysqli_stmt_close($stmt);
        if ($errno === 1062) {
            // duplicate key on ACNames.name
            throw new \Exception("That name is already in use");
        }
        throw new \Exception("reserveAcName(): " . mysqli_error($link));
    }
    mysqli_stmt_close($stmt);
}

function get_AD_list($link){
    $sql = "
        SELECT
            ID,
            authname,
            org,
            menuID,
            dashboardID,
            collectionID,
            maxbyday,
            maxbymonth,
            maxtotalaccesses
        FROM AccessDefinitions
    ";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("get_AD_list(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    if (! mysqli_stmt_execute($stmt)) {
        error_log("get_AD_list(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $authname,
            $org,
            $menuID,
            $dashboardID,
            $collectionID,
            $maxbyday,
            $maxbymonth,
            $maxtotalaccesses
        );
    $rows = [];
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'ID'       => $id,
            'authname' => $authname,
            'org'      => $org,
            'menuID'   => $menuID,
            'dashboardID'  => $dashboardID,
            'collectionID' => $collectionID,
            'maxbyday'     => $maxbyday,
            'maxbymonth'   => $maxbymonth,
            'maxtotal'   => $maxtotalaccesses,
        ];
    }
    mysqli_stmt_close($stmt);
    return $rows;
}
function get_menu_list($link) {
    $sql = "SELECT id, pageTitle FROM MainMenuSubmenus";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("get_MainMenuSubmenus_list(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    if (! mysqli_stmt_execute($stmt)) {
        error_log("get_MainMenuSubmenus_list(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_result($stmt, $id, $pageTitle);
    $rows = [];
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'ID'        => $id,
            'pageTitle' => $pageTitle,
        ];
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function add_access_definition($link){
    if (empty($_POST['name']) || trim($_POST['name']) === '') {
        echo json_encode(['error' => 'no access name']);
        return;
    }
    $name    = trim($_POST['name']);
    //uniqueness for profiles and ACL
    try {
        reserveAcName($link, $name);
    } catch (\Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
    $orgs    = isset($_POST['orgs']) && is_array($_POST['orgs'])
                ? $_POST['orgs']
                : null;
    $menu_id = (isset($_POST['menu_id']) && $_POST['menu_id'] != '') 
                ? (int)$_POST['menu_id'] 
                : null;
    if (isset($_POST['dashboard_id']) && trim($_POST['dashboard_id']) !== '') {
        $dashboardID = trim($_POST['dashboard_id']);
    } else {
        $dashboardID = null;
    }
    if (isset($_POST['collection_id']) && trim($_POST['collection_id']) !== '') {
        $collectionID = trim($_POST['collection_id']);
    } else {
        $collectionID = null;
    }
    if (isset($_POST['maxbyday']) && trim($_POST['maxbyday']) !== '') {
        $maxByDay = (int) $_POST['maxbyday'];
    } else {
        $maxByDay = null;
    }
    if (isset($_POST['maxbymonth']) && trim($_POST['maxbymonth']) !== '') {
        $maxByMonth = (int) $_POST['maxbymonth'];
    } else {
        $maxByMonth = null;
    }
    if (isset($_POST['maxtotal']) && trim($_POST['maxtotal']) !== '') {
        $maxtotalaccesses = (int) $_POST['maxtotal'];
    } else {
        $maxtotalaccesses = null;
    }
    $org_list = implode(',', $orgs);
    //Check for an existing entry
    $check_sql = "
        SELECT ID
          FROM AccessDefinitions
         WHERE authname = ?
         LIMIT 1
    ";
    if (! $stmt = mysqli_prepare($link, $check_sql)) {
        error_log("add_access_definition(): prepare failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }
    mysqli_stmt_bind_param($stmt, "s", $name);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("add_access_definition(): execute failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        mysqli_stmt_close($stmt);
        return;
    }
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        //already exists
        mysqli_stmt_close($stmt);
        echo json_encode(['error' => 'already exists']);
        return;
    }
    mysqli_stmt_close($stmt);
    $insert_sql = "
        INSERT INTO AccessDefinitions
            (authname, org, menuID, dashboardID, collectionID, maxbyday, maxbymonth, maxtotalaccesses)
        VALUES
            (?,        ?,   ?,      ?,           ?,            ?,        ?,         ?)
    ";
    if (! $stmt = mysqli_prepare($link, $insert_sql)) {
        error_log("add_access_definition(): prepare failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }
    // param types: s = authname, s = org, i = menuID, s = dashboardID, s = collectionID, i = maxday, i = maxmonth
    mysqli_stmt_bind_param(
        $stmt,
        "ssissiii",
        $name,
        $org_list,
        $menu_id,
        $dashboardID,
        $collectionID,
        $maxByDay,
        $maxByMonth,
        $maxtotalaccesses
    );
    if (! mysqli_stmt_execute($stmt)) {
        error_log("add_access_definition(): execute failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        mysqli_stmt_close($stmt);
        return;
    }
    $new_id = mysqli_insert_id($link);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'result'   => 'added',
        'id'       => $new_id,
        'name'     => $name,
        'orgs'     => $orgs,
        'menu_id'  => $menu_id,
        'dashboard_id'  => $dashboardID,
        'collection_id' => $collectionID,
        'maxbyday'      => $maxByDay,
        'maxbymonth'    => $maxByMonth,
        'maxtotal'    => $maxtotalaccesses
    ]);
}
function edit_access_definition($link){
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;

    if (empty($_POST['id']) || empty($_POST['name'])|| trim($_POST['name']) === '') {
        echo json_encode(['error'=>'invalid parameters']);
        return;
    }
    $id         = (int) $_POST['id'];
    $newName    = trim($_POST['name']);
    $newOrgs    = isset($_POST['orgs']) && is_array($_POST['orgs'])
                  ? $_POST['orgs']
                  : [];
    $newMenu    = isset($_POST['menu_id']) && $_POST['menu_id'] !== ''
                  ? (int) $_POST['menu_id']
                  : null;
    if (isset($_POST['dashboard_id']) && trim($_POST['dashboard_id']) !== '') {
        $newDashboard = trim($_POST['dashboard_id']);
    } else {
        $newDashboard = null;
    }
    if (isset($_POST['collection_id']) && trim($_POST['collection_id']) !== '') {
        $newCollection = trim($_POST['collection_id']);
    } else {
        $newCollection = null;
    }
    if (isset($_POST['maxbyday']) && trim($_POST['maxbyday']) !== '') {
        $newMaxByDay = (int) $_POST['maxbyday'];
    } else {
        $newMaxByDay = null;
    }
    if (isset($_POST['maxbymonth']) && trim($_POST['maxbymonth']) !== '') {
        $newMaxByMonth = (int) $_POST['maxbymonth'];
    } else {
        $newMaxByMonth = null;
    }
    if (isset($_POST['maxtotal']) && trim($_POST['maxtotal']) !== '') {
        $newmaxtotalaccesses = (int) $_POST['maxtotal'];
    } else {
        $newmaxtotalaccesses = null;
    }
    $newOrgList = implode(',', $newOrgs);

    $debug = [];

    //Duplicate check
    $dup_sql = "
      SELECT ID
        FROM AccessDefinitions
       WHERE authname = ?
         AND ID       != ?
       LIMIT 1
    ";
    $dup = mysqli_prepare($link, $dup_sql);
    mysqli_stmt_bind_param($dup, "si", $newName, $id);
    mysqli_stmt_execute($dup);
    mysqli_stmt_store_result($dup);
    if (mysqli_stmt_num_rows($dup) > 0) {
        mysqli_stmt_close($dup);
        echo json_encode(['error'=>'already exists']);
        return;
    }
    mysqli_stmt_close($dup);

    //Fetch the old menuID
    $old_sql = "SELECT menuID FROM AccessDefinitions WHERE ID = ?";
    $old     = mysqli_prepare($link, $old_sql);
    mysqli_stmt_bind_param($old, "i", $id);
    mysqli_stmt_execute($old);
    mysqli_stmt_bind_result($old, $oldMenu);
    if (! mysqli_stmt_fetch($old)) {
        mysqli_stmt_close($old);
        echo json_encode(['error'=>'not found']);
        return;
    }
    mysqli_stmt_close($old);

    $debug['oldMenu'] = $oldMenu;
    $debug['newMenu'] = $newMenu;

    //Update the AccessDefinitions row
    $upd_sql = "
        UPDATE AccessDefinitions
            SET authname     = ?,
                org          = ?,
                menuID       = ?,
                dashboardID  = ?,
                collectionID = ?,
                maxbyday     = ?,
                maxbymonth   = ?,
                maxtotalaccesses   = ?
        WHERE ID = ?
    ";
    $upd = mysqli_prepare($link, $upd_sql);
    // types: s, s, i, s, s, i, i, i , i
    mysqli_stmt_bind_param(
        $upd,
        "ssissiiii",
        $newName,
        $newOrgList,
        $newMenu,
        $newDashboard,
        $newCollection,
        $newMaxByDay,
        $newMaxByMonth,
        $newmaxtotalaccesses,
        $id
    );
    if (! mysqli_stmt_execute($upd)) {
        mysqli_stmt_close($upd);
        echo json_encode(['error'=>mysqli_error($link)]);
        return;
    }
    mysqli_stmt_close($upd);

    //If menuID changed, patch MainMenuSubmenusUser
    if ($oldMenu !== $newMenu) {
        $debug['menu_changed'] = true;
        
        // Get all users for this access definition
        $acl_sql = "SELECT user FROM ACL WHERE defID = ?";
        $stmtAcl = mysqli_prepare($link, $acl_sql);
        mysqli_stmt_bind_param($stmtAcl, "i", $id);
        mysqli_stmt_execute($stmtAcl);
        mysqli_stmt_bind_result($stmtAcl, $encUser);
        
        // Collect all users first
        $users = [];
        while (mysqli_stmt_fetch($stmtAcl)) {
            $user = decryptOSSL($encUser, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
            $users[] = ['encrypted' => $encUser, 'decrypted' => $user];
        }
        mysqli_stmt_close($stmtAcl);
        
        $debug['users_found'] = count($users);
        $users_processed = [];

        foreach ($users as $userData) {
            $encUser = $userData['encrypted'];
            $user = $userData['decrypted'];
            $users_processed[] = $user;
            
            $debug['processing_user'] = $user;
            
            //Check if user has other ACLs for the old menu
            if ($oldMenu !== null) {
                $count_old_sql = "SELECT COUNT(*) FROM ACL a JOIN AccessDefinitions ad ON a.defID = ad.ID WHERE a.user = '" . mysqli_real_escape_string($link, $encUser) . "' AND ad.menuID = " . (int)$oldMenu . " AND a.defID != " . (int)$id;
                $result = mysqli_query($link, $count_old_sql);
                if ($result) {
                    $cntOld = (int)mysqli_fetch_row($result)[0];
                    mysqli_free_result($result);
                    $debug['old_menu_count_for_user_' . $user] = $cntOld;
                    
                    if ($cntOld === 0) {
                        $delResult = mysqli_query($link, "DELETE FROM MainMenuSubmenusUser WHERE submenu = " . (int)$oldMenu . " AND user = '" . mysqli_real_escape_string($link, $user) . "'");
                        $debug['deleted_old_menu_for_user_' . $user] = $delResult;
                        if (!$delResult) $debug['delete_error_for_user_' . $user] = mysqli_error($link);
                    }
                } else {
                    $debug['count_old_error'] = mysqli_error($link);
                }
            }
            
            // Check if user already has access to the new menu
            if ($newMenu !== null) {
                $exists_new_sql = "SELECT COUNT(*) FROM MainMenuSubmenusUser WHERE submenu = " . (int)$newMenu . " AND user = '" . mysqli_real_escape_string($link, $user) . "'";
                $result = mysqli_query($link, $exists_new_sql);
                if ($result) {
                    $cntNew = (int)mysqli_fetch_row($result)[0];
                    mysqli_free_result($result);
                    $debug['new_menu_count_for_user_' . $user] = $cntNew;
                    
                    if ($cntNew === 0) {
                        $insResult = mysqli_query($link, "INSERT INTO MainMenuSubmenusUser (submenu, user) VALUES (" . (int)$newMenu . ", '" . mysqli_real_escape_string($link, $user) . "')");
                        $debug['inserted_new_menu_for_user_' . $user] = $insResult;
                        if (!$insResult) $debug['insert_error_for_user_' . $user] = mysqli_error($link);
                    } else {
                        $debug['new_menu_already_exists_for_user_' . $user] = true;
                    }
                } else {
                    $debug['exists_new_error'] = mysqli_error($link);
                }
            }
        }
        
        $debug['users_processed'] = $users_processed;
    } else {
        $debug['menu_changed'] = false;
    }

    echo json_encode([
        'result'  => 'edited',
        'id'      => $id,
        'name'    => $newName,
        'orgs'    => $newOrgs,
        'menu_id' => $newMenu,
        'dashboard_id'   => $newDashboard,
        'collection_id'  => $newCollection,
        'maxbyday'       => $newMaxByDay,
        'maxbymonth'     => $newMaxByMonth,
        'maxtotal'       => $newmaxtotalaccesses,
        'debug'   => $debug
    ]);
}

function delete_access_definition($link){
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    
    if (empty($_POST['id'])) {
        echo json_encode(['error' => 'no id specified']);
        return;
    }
    
    $id = (int) $_POST['id'];
    
    // Fetch the menuID of the definition to delete
    $m_sql = "SELECT menuID FROM AccessDefinitions WHERE ID = ?";
    $m = mysqli_prepare($link, $m_sql);
    if (!$m) {
        error_log("delete_access_definition(): fetch-menu prepare failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }
    
    mysqli_stmt_bind_param($m, "i", $id);
    mysqli_stmt_execute($m);
    mysqli_stmt_bind_result($m, $menuID);
    if (!mysqli_stmt_fetch($m)) {
        mysqli_stmt_close($m);
        echo json_encode(['error' => 'not found']);
        return;
    }
    mysqli_stmt_close($m);
    
    // Only process menu cleanup if menuID is not null
    if ($menuID !== null) {
        // Gather all users who had this defID
        $u_sql = "SELECT user FROM ACL WHERE defID = ?";
        $stmtU = mysqli_prepare($link, $u_sql);
        mysqli_stmt_bind_param($stmtU, "i", $id);
        mysqli_stmt_execute($stmtU);
        mysqli_stmt_bind_result($stmtU, $encUser);
        
        // Collect all users first (same pattern as edit function)
        $users = [];
        while (mysqli_stmt_fetch($stmtU)) {
            $user = decryptOSSL($encUser, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
            $users[] = ['encrypted' => $encUser, 'decrypted' => $user];
        }
        mysqli_stmt_close($stmtU);
        
        // Process each user
        foreach ($users as $userData) {
            $encUser = $userData['encrypted'];
            $user = $userData['decrypted'];
            
            // Check if user has other ACLs for this menu (excluding the one being deleted)
            $count_sql = "SELECT COUNT(*) FROM ACL a JOIN AccessDefinitions ad ON a.defID = ad.ID WHERE a.user = '" . mysqli_real_escape_string($link, $encUser) . "' AND ad.menuID = " . (int)$menuID . " AND a.defID != " . (int)$id;
            $result = mysqli_query($link, $count_sql);
            
            if ($result) {
                $cnt = (int)mysqli_fetch_row($result)[0];
                mysqli_free_result($result);
                
                // If no other definition grants this menu, remove from MainMenuSubmenusUser
                if ($cnt === 0) {
                    $delMenu_sql = "DELETE FROM MainMenuSubmenusUser WHERE submenu = " . (int)$menuID . " AND user = '" . mysqli_real_escape_string($link, $user) . "'";
                    mysqli_query($link, $delMenu_sql);
                }
            }
        }
    }
    // Delete the AccessDefinitions row (cascades to ACL due to foreign key)
    $del_sql = "DELETE FROM AccessDefinitions WHERE ID = ?";
    $delDef = mysqli_prepare($link, $del_sql);
    if (!$delDef) {
        error_log("delete_access_definition(): delete-AD prepare failed: " . mysqli_error($link));
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }
    
    mysqli_stmt_bind_param($delDef, "i", $id);
    mysqli_stmt_execute($delDef);
    $affected = mysqli_stmt_affected_rows($delDef);
    mysqli_stmt_close($delDef);
    
    if ($affected > 0) {
        echo json_encode([
            'result' => 'deleted',
            'id'     => $id
        ]);
    } else {
        echo json_encode(['error' => 'not found']);
    }
}
function get_list_ACL($link){
    $sql = "SELECT * FROM ACL";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("get_list_ACL(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    if (! mysqli_stmt_execute($stmt)) {
        error_log("get_list_ACL(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_result($stmt, $id, $defID, $user);
    $rows = [];
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'ID'    => $id,
            'defID' => $defID,
            'user'  => $user,
        ];
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function get_user_ACL($link){
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    if (empty($_POST['username']) || trim($_POST['username']) === '') {
        error_log("get_list_ACL_for_user(): no username specified");
        return ['error' => 'no username'];
    }
    $username = trim($_POST['username']);
    $enc_username = encryptOSSL($username, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    $sql = "SELECT ID, defID
                FROM ACL
                WHERE user = ?";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("get_list_ACL_for_user(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_param($stmt, "s", $enc_username);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("get_list_ACL_for_user(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_result($stmt, $id, $defID);
    $rows = [];
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'ID'    => $id,
            'defID' => $defID,
            'user'  => $username,
        ];
    }
    mysqli_stmt_close($stmt);
    return $rows;
}
function update_ACL_list($link){
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;

    $user = trim($_POST['username'] ?? '');
    if ($user === '') {
        echo json_encode(['error'=>'no username']); return;
    }
    $original = isset($_POST['original_defs']) && is_array($_POST['original_defs'])
              ? array_map('intval', $_POST['original_defs'])
              : [];
    $newDefs  = isset($_POST['new_defs'])      && is_array($_POST['new_defs'])
              ? array_map('intval', $_POST['new_defs'])
              : [];
    $enc_user = encryptOSSL($user,$encryptionInitKey,$encryptionIvKey,$encryptionMethod);
    $toAdd    = array_diff($newDefs, $original);
    $toRemove = array_diff($original, $newDefs);

    //AccessDefinitions to menuID lookup
    $allDefs = array_unique(array_merge($toAdd, $toRemove));
    $menuMap = [];
    if (count($allDefs)) {
        $placeholders = implode(',', array_fill(0, count($allDefs), '?'));
        $sqlAll = "SELECT ID, menuID FROM AccessDefinitions WHERE ID IN ($placeholders)";
        if ($stmtAll = mysqli_prepare($link, $sqlAll)) {
            mysqli_stmt_bind_param($stmtAll, str_repeat('i', count($allDefs)), ...$allDefs);
            mysqli_stmt_execute($stmtAll);
            mysqli_stmt_bind_result($stmtAll, $defFetch, $menuFetch);
            while (mysqli_stmt_fetch($stmtAll)) {
                $menuMap[$defFetch] = $menuFetch;
            }
            mysqli_stmt_close($stmtAll);
        }
    }
    $stmtInsertACL = mysqli_prepare(
        $link,
        "INSERT INTO ACL (defID, user) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmtInsertACL, "is", $defID, $enc_user);

    $sql  = "INSERT INTO `MainMenuSubmenusUser` (`submenu`,`user`) VALUES (?,?)";
    $stmtInsertMenuUser = mysqli_prepare($link, $sql);
    if (! $stmtInsertMenuUser) {
        error_log("update_ACL_list(): prepare INSERT submenu failed: " . mysqli_error($link));
        echo json_encode(['error'=>'could not prepare submenu‐insert']); 
        exit;
    }
    mysqli_stmt_bind_param($stmtInsertMenuUser, "is", $menuID, $user);
    $stmtDeleteACL = mysqli_prepare(
        $link,
        "DELETE FROM ACL WHERE defID = ? AND user = ?"
    );
    mysqli_stmt_bind_param($stmtDeleteACL, "is", $defID, $enc_user);

    $sql  = "DELETE FROM `MainMenuSubmenusUser` WHERE `submenu` = ? AND `user` = ?";
    $stmtDeleteMenuUser = mysqli_prepare($link, $sql);
    if (! $stmtDeleteMenuUser) {
        error_log("update_ACL_list(): prepare DELETE submenu failed: " . mysqli_error($link));
        echo json_encode(['error'=>'could not prepare submenu‐delete']); 
        exit;
    }
    mysqli_stmt_bind_param($stmtDeleteMenuUser, "is", $menuID, $user);

    $added   = [];
    $removed = [];
    $errors  = [];

    //to be added
    foreach ($toAdd as $defID) {
        if ($defID <= 0) continue;
        if (! mysqli_stmt_execute($stmtInsertACL)) {
            $errors[] = "ACL insert failed for defID={$defID}";
            continue;
        }
        $added[] = $defID;

        //lookup menuID from pre‐fetched map
        if (isset($menuMap[$defID]) && $menuMap[$defID] !== null) {
            $menuID = $menuMap[$defID];
            //insert submenu link
            if (! mysqli_stmt_execute($stmtInsertMenuUser)) {
                $errno = mysqli_errno($link);
                $errors[] = "submenu insert failed for defID={$defID}, menuID={$menuID}, errno={$errno} " . mysqli_error($link);
            }
        }
    }
    //removals
    foreach ($toRemove as $defID) {
        if ($defID <= 0) continue;
        if (! mysqli_stmt_execute($stmtDeleteACL)) {
            $errors[] = "ACL delete failed for defID={$defID}";
        } else {
            $removed[] = $defID;
        }
        //lookup menuID from pre‐fetched map
        if (isset($menuMap[$defID]) && $menuMap[$defID] !== null) {
            $menuID = $menuMap[$defID];
            //delete submenu link
            if (! mysqli_stmt_execute($stmtDeleteMenuUser)) {
                $errors[] = "submenu delete failed (defID={$defID},menuID={$menuID}): " . mysqli_error($link);
            }
        }
    }
    mysqli_stmt_close($stmtInsertACL);
    mysqli_stmt_close($stmtInsertMenuUser);
    mysqli_stmt_close($stmtDeleteACL);
    mysqli_stmt_close($stmtDeleteMenuUser);
    echo json_encode([
        'added'   => array_values($added),
        'removed' => array_values($removed),
        'errors'  => $errors
    ]);
}

function get_profiles_list($link) {
    $sql = "SELECT ID, profilename, authIDs FROM ACLProfiles";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("get_profiles_list(): prepare failed: " . mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    if (! mysqli_stmt_execute($stmt)) {
        error_log("get_profiles_list(): execute failed: " . mysqli_error($link));
        mysqli_stmt_close($stmt);
        return ['error' => mysqli_error($link)];
    }
        mysqli_stmt_bind_result(
            $stmt,
            $id,
            $profilename,
            $authIDs
        );
    $rows = [];
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = [
            'ID'       => $id,
            'profilename' => $profilename,
            'authIDs'      => $authIDs ?? '',
        ];
    }
    mysqli_stmt_close($stmt);
    return $rows;
  }
  function edit_profile($link) {
    if (empty($_POST['id'])) {
        return ['error'=>'missing profile ID'];
    }
    $id      = (int) $_POST['id'];
    $authIDs = isset($_POST['authIDs']) && is_array($_POST['authIDs'])
             ? $_POST['authIDs']
             : [];
    // build csv list
    $authList = implode(',', array_map('intval', $authIDs));

    $sql = "UPDATE ACLProfiles
               SET authIDs = ?
             WHERE ID = ?";
    if (! $stmt = mysqli_prepare($link, $sql)) {
        error_log("edit_profile(): prepare failed: ".mysqli_error($link));
        return ['error'=>mysqli_error($link)];
    }
    mysqli_stmt_bind_param($stmt, "si", $authList, $id);
    if (! mysqli_stmt_execute($stmt)) {
        error_log("edit_profile(): execute failed: ".mysqli_error($link));
        return ['error'=>mysqli_error($link)];
    }
    mysqli_stmt_close($stmt);

    return [
      'result'  => 'updated',
      'id'      => $id,
      'authIDs' => $authIDs
    ];
}
function add_profile($link) {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        return ['error' => 'Profile name cannot be empty'];
        exit;
    }
    try {
        reserveAcName($link, $name);
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
        exit;
    }
    // ACL IDs array
    $authIDs = [];
    if (!empty($_POST['authIDs']) && is_array($_POST['authIDs'])) {
        // sanitize as integers
        $authIDs = array_map('intval', $_POST['authIDs']);
    }
    $authList = implode(',', $authIDs);
    $sql = "INSERT INTO ACLProfiles (profilename, authIDs) VALUES (?, ?)";
    if (!$stmt = mysqli_prepare($link, $sql)) {
        error_log("add_profile(): prepare failed: ".mysqli_error($link));
        return ['error' => mysqli_error($link)];
    }
    mysqli_stmt_bind_param($stmt, "ss", $name, $authList);
    if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_errno($link) === 1062
             ? 'Profile name already exists'
             : mysqli_error($link);
        mysqli_stmt_close($stmt);
        return ['error' => $err];
    }
    $newId = mysqli_insert_id($link);
    mysqli_stmt_close($stmt);
    return [
      'result'     => 'added',
      'ID'         => $newId,
      'profilename'=> $name,
      'authIDs'    => $authIDs
    ];
}
function get_user_profiles($link) {
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    $user = trim($_POST['username'] ?? '');
    if ($user === '') return [];
    $enc_user = encryptOSSL($user, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    $sql = "SELECT profileID FROM ACLProfilesAssignment WHERE user = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $enc_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pid);
    $out = [];
    while (mysqli_stmt_fetch($stmt)) {
        $out[] = (int)$pid;
    }
    mysqli_stmt_close($stmt);
    return $out;
}

function update_user_profiles($link) {
    global $encryptionInitKey, $encryptionIvKey, $encryptionMethod;
    $user = trim($_POST['username'] ?? '');
    if ($user === '') return ['error'=> 'no username'];
    $enc_user = encryptOSSL($user, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
    $original = isset($_POST['original_profiles']) && is_array($_POST['original_profiles'])
              ? array_map('intval', $_POST['original_profiles'])
              : [];
    $new      = isset($_POST['new_profiles'])      && is_array($_POST['new_profiles'])
              ? array_map('intval', $_POST['new_profiles'])
              : [];
    $toAdd    = array_diff($new, $original);
    $toRem    = array_diff($original, $new);

    $ins = mysqli_prepare($link, "INSERT IGNORE INTO ACLProfilesAssignment(profileID,user) VALUES(?,?)");
    mysqli_stmt_bind_param($ins, "is", $pid, $enc_user);
    $del = mysqli_prepare($link, "DELETE FROM ACLProfilesAssignment WHERE profileID=? AND user=?");
    mysqli_stmt_bind_param($del, "is", $pid, $enc_user);

    $added = $removed = [];
    foreach ($toAdd as $pid) {
      if (mysqli_stmt_execute($ins)) $added[] = $pid;
    }
    foreach ($toRem as $pid) {
      if (mysqli_stmt_execute($del)) $removed[] = $pid;
    }
    mysqli_stmt_close($ins);
    mysqli_stmt_close($del);
    return ['added'=>$added,'removed'=>$removed];
}


