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
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername']) 
{
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['id']);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in changeSynopticTplOwnership.php for id = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    };
    $dashboardTitle = mysqli_real_escape_string($link, $_REQUEST['tplTitle']);
    $newOwner = mysqli_real_escape_string($link, $_REQUEST['newOwner']);

    // Check if $newOwner exists as a valid LDAP user
    $ldapUsername = "cn=" . ldap_escape($newOwner) . "," . $ldapBaseDN;

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if($ldapAdminDN)
        $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
    else
        $bind = ldap_bind($ds);

    if ($ds && $bind) {
        if (checkLdapMembership($ds, $ldapUsername, $ldapToolName, $ldapBaseDN)) {
            if (checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN)) {
                $ldapRole = "RootAdmin";
                $ldapOk = true;
            }
            else
            {
                if (checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN)) {
                    $ldapRole = "ToolAdmin";
                    $ldapOk = true;
                } else {
                    if (checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN)) {
                        $ldapRole = "AreaManager";
                        $ldapOk = true;
                    } 
                }
            }
        }
    }

    if(isset($_SESSION['refreshToken']) && $ldapOk)
    {

        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        $callBody = ["elementId" => $_REQUEST['id'], "elementType" => $synTplOwnElmtType , "username" => $newOwner, "elementName" => $dashboardTitle];

        $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

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
            $callResult = @file_get_contents($apiUrl, false, $context);

            if(strpos($http_response_header[0], '200') !== false) 
            {
				$response['detail'] = 'Ok';
            }
            else
            {
                $response['detail'] = 'ApiCallKo1';
                $response['detail2'] = $http_response_header[0];
            }
        }
        catch (Exception $ex) 
        {
            $response['detail'] = 'ApiCallKo2';
            $response['detail2'] = $http_response_header[0];
        }
    } else {
        $response['detail'] = 'checkUserKo';
    }
    
    echo json_encode($response);
}
  
