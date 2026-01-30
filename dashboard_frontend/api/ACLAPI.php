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
    require '../sso/autoload.php';
    use Jumbojett\OpenIDConnectClient;
    session_start();
    include_once('../management/ACLInternal.php');
    ACLAPI_action_router();

    function ACLAPI_action_router() {
            $action = $_REQUEST['action'] ?? '';
            //possible variables
            $requested_name = trim($_REQUEST['auth_name'] ?? '');
            $requested_org = trim($_REQUEST['organization'] ?? '');
            $dashboardParam = trim($_REQUEST['dashboard_id'] ?? '');
            $collectionParam = trim($_REQUEST['collection_id'] ?? '');
            $substrParam = trim($_REQUEST['ACL_substring'] ?? '');
            $claims = requireUser(); //vars from token/session
            $preferred_username = $claims['preferred_username'];
            $ou = $claims['ou'] ?? '';
            $data = [
                    'auth_name'=> $requested_name ?? '',
                    'organization'=> $requested_org ?? '',
                    'preferred_username'=> $preferred_username ?? '',
                    'ou'=> $ou ?? '',
                    'collection_id'=> $collectionParam ?? '',
                    'dashboard_id'=> $dashboardParam ?? '',
                    'ACL_substring'=> $substrParam ?? '',
                    ];
            switch ($action) {
                case 'check_auth': //EXPECTED $data: ["auth_name":"required", "organization": "optional", "preferred_username":"required", "ou"='unused for now' ]
                    header('Content-Type: application/json');
                    $res = ACLAPI_check_auth($data);
                    if (!empty($res['error'])) {http_response_code(400);}
                    echo json_encode($res);
                    exit;
                case 'check_dashboard':
                    header('Content-Type: application/json');
                    $res = ACLAPI_check_dashboard($data);
                    if (!empty($res['error'])) {http_response_code(400);}
                    echo json_encode($res);
                    exit;
                case 'check_collection':
                    header('Content-Type: application/json');
                    $res = ACLAPI_check_collection($data);
                    if (!empty($res['error'])) {http_response_code(400);}
                    echo json_encode($res);
                    exit;
                case 'get_user_menuIDs':
                    header('Content-Type: application/json');
                    $res = ACLAPI_check_menuIDs($data);
                    if (!empty($res['error'])) {http_response_code(400);}
                    echo json_encode($res);
                    exit;
                case 'get_user_ACLs':
                    header('Content-Type: application/json');
                    $res = ACLAPI_get_user_ACLs($data);
                    if (!empty($res['error'])) {http_response_code(400);}
                    echo json_encode($res);
                    exit;
                default:
                    http_response_code(400);
                    echo json_encode(['error'=>'Unknown action:' . $action]);
                    exit;
            }
        }
    /**
    * Ensure the incoming request has a valid user (Bearer token or session).
    * On success, returns ['preferred_username'=>..., 'ou'=>[]]
    * On failure, sends 401 and exits.
    */
    function requireUser(): array {
        // grab Authorization header
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

        // If Bearer token, call userinfo endpoint
        if (preg_match('/^Bearer\s+(\S+)$/i', $auth, $m)) {
            $token = $m[1];
            global $ssoUserinfoEndpoint;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,            $ssoUserinfoEndpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Authorization: Bearer {$token}"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FAILONERROR,    false);

            $body     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err      = curl_error($ch);
            curl_close($ch);

            if ($body === false || $httpCode !== 200) {
                error_log("userinfo fetch failed (HTTP {$httpCode}): {$err}");
                header('HTTP/1.1 401 Unauthorized');
                echo json_encode(   ['authorized' => 'false',
                                    'reason' => 'Token invalid or expired / userinfo fetch failed']);
                exit;
            }

            $userinfo = json_decode($body, true);
            if (!is_array($userinfo)) {
                header('HTTP/1.1 401 Unauthorized');
                echo json_encode(   ['authorized' => 'false',
                                    'reason' => 'Invalid userinfo response']);
                exit;
            }

            return [
                'preferred_username' => $userinfo['preferred_username'] ?? null,
                'ou'                 => $userinfo['ou'] ?? [],
            ];
        }

        // fallback to session
        if (empty($_SESSION['loggedUsername']) || empty($_SESSION['refreshToken'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(   ['authorized' => 'false',
                                'reason' => 'not authenticated']);
            exit;
        }

        // refresh token via OIDC
        try {
            global $ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint;
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(['token_endpoint' => $ssoTokenEndpoint]);
            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
            $_SESSION['refreshToken'] = $tkn->refresh_token;

            return [
                'preferred_username' => $_SESSION['loggedUsername'],
                'ou'                 => [],
            ];
        } catch (\Exception $e) {
            error_log("OIDC refresh error: " . $e->getMessage());
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(   ['authorized' => 'false',
                                    'reason' => 'Session refresh failed with error' . $e]);
            exit;
        }
    }

?>