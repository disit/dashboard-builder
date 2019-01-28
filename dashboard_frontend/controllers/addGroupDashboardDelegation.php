<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']))
{
    $dashboardId = $_REQUEST['dashboardId'];
    $newDelegated = $_REQUEST['newDelegated'];

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $bind = ldap_bind($ds);
    if($ds && $bind)
    {
        if(checkLdapRole($ds, $newDelegated, "RootAdmin", $ldapBaseDN))
        {
            $response['detail'] = 'RootAdmin';
        }
        else
        {
            $q = "SELECT user FROM Dashboard.Config_dashboard WHERE Id = $dashboardId";
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
                    $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . $dashboardAuthor . "/delegation?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

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