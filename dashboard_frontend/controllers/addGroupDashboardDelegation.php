<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']))
{
    $dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in addGroupDashboardDelegation.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
    if(!checkDashboardId($link, $dashboardId)) {
        eventLog("invalid request for updateDashboard.php for dashboardId = $dashboardId user: ".$_SESSION['loggedUsername']);
        exit;
    }
    
    $newDelegated = $_REQUEST['newDelegated'];

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if($ldapAdminDN)
        $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
    else
        $bind = ldap_bind($ds);
    if($ds && $bind)
    {
        if(checkLdapRole($ds, $newDelegated, "RootAdmin", $ldapBaseDN))
        {
            $response['detail'] = 'RootAdmin';
        }
        else
        {
            $q = "SELECT user FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
            $r = mysqli_query($link, $q);

            if($r)
            {
                $dashboardAuthor = mysqli_fetch_assoc($r)['user'];

                if(isset($_SESSION['refreshToken']))
                {
                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

                    $accessToken = $tkn->access_token;
                    $_SESSION['refreshToken'] = $tkn->refresh_token;
                    // URL ENCODE $newDelegated
                    $newDelegatedEncoded = urlencode($newDelegated);
                    $callBody = ["groupnameDelegated" => $newDelegated, "elementId" => $dashboardId, "elementType" => "DashboardID"];
                    // ENCODIZZARE username per username con SPAZI !!!
                    $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($dashboardAuthor) . "/delegation?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

                    $options = array(
                        'http' => array(
                            'header'  => "Content-type: application/json\r\n",
                            'method'  => 'POST',
                            'timeout' => 30,
                            'content' => json_encode($callBody),
                            'ignore_errors' => true
                        )
                    );

                    try
                    {
                        $context  = stream_context_create($options);
                        $callResult = file_get_contents($apiUrl, false, $context);

                        if(strpos($http_response_header[0], '200') !== false)
                        {
                            $response['detail'] = 'Ok';
                            $response['delegationId'] = json_decode($callResult)->id;
                            $response['ldapCheck'] = checkLdapRole($ds, $newDelegated, "RootAdmin", $ldapBaseDN);
                        }
                        else
                        {
                            $response['detail'] = 'ApiCallKo';
                            $response['detail2'] = $http_response_header[0];
                        }
                    }
                    catch(Exception $ex)
                    {
                        $response['detail'] = 'ApiCallKo';
                    }

                }
            }
            else
            {
                $response['detail'] = 'QueryKo';
            }
        }
    }
    else
    {
        $response['detail'] = 'LdapKo';
    }
    echo json_encode($response);
}