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

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername']) 
{
    $dashboardId = $_REQUEST['dashboardId'];
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in delDashboardDelegation.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
    if(!checkDashboardId($link, $dashboardId)) {
        eventLog("invalid request for delDashboardDelegation.php for dashboardId = $dashboardId user: ".$_SESSION['loggedUsername']);
        exit;
    }
    
    $delegationId = $_REQUEST['delegationId'];
    if (checkVarType($delegationId, "integer") === false) {
        eventLog("Returned the following ERROR in delDashboardDelegation.php for delegationId = ".$delegationId.": ".$delegationId." is not an integer as expected. Exit from script.");
        exit();
    }
    
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

            // ENCODIZZARE username per username con SPAZI !!!
            $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($dashboardAuthor) . "/delegation/" .$delegationId . "?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

            $options = array(
                'http' => array(
                        'header'  => "Content-type: application/json\r\n",
                        'method'  => 'DELETE',
                        'timeout' => 30,
                        'ignore_errors' => true
                )
            );

            $context  = stream_context_create($options);
            $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

            if(strpos($http_response_header[0], '200') !== false) 
            {
                $response['detail'] = 'Ok';
            }
            else
            {
                $response['detail'] = 'Ko';
            }

            echo json_encode($response);
            
        }
    }
}
  
