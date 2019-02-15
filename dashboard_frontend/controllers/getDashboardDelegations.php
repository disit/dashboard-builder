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

$delegated = [];

if(isset($_SESSION['loggedUsername'])) 
{
    $dashboardId = $_REQUEST['dashboardId'];
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

            // ENCODIZZARE username per username con SPAZI !!!
            $apiUrl = $personalDataApiBaseUrl . "/v2/username/" . rawurlencode($dashboardAuthor) . "/delegator?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
          //  $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegated?accessToken=" . $accessToken. "&sourceRequest=dashboardmanager";
                        
            $options = array(
                'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'GET',
                        'timeout' => 30,
                        'ignore_errors' => true
                )
            );

            $context  = stream_context_create($options);
            $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

            $delegatedDashboards = json_decode($delegatedDashboardsJson);

            for($i = 0; $i < count($delegatedDashboards); $i++) 
            {
                if($delegatedDashboards[$i]->elementId == $dashboardId)
                {
                    if (!is_null($delegatedDashboards[$i]->usernameDelegated)) {
                        $newDelegation = ["delegationId" => $delegatedDashboards[$i]->id, "delegatedUser" => $delegatedDashboards[$i]->usernameDelegated];
                    } else if (!is_null($delegatedDashboards[$i]->groupnameDelegated)) {
                        $auxString = "";
                        $auxString2 = "";
                        if (explode("cn=", $delegatedDashboards[$i]->groupnameDelegated)!= "") {
                            $auxString = explode("cn=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                            $auxString = explode(",", $auxString)[0];
                            $auxString2 = explode("ou=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                            $auxString2 = explode(",", $auxString2)[0];
                            if ($auxString != "") {
                                $auxString = $auxString2 . " - " . $auxString;
                            } else {
                                $auxString = $auxString2 . " - All Groups";
                            }
                        } else if (explode("ou=", $delegatedDashboards[$i]->groupnameDelegated) != "") {
                            $auxString = explode("ou=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                            $auxString = explode(",", $auxString)[0];
                        }
                        $newDelegation = ["delegationId" => $delegatedDashboards[$i]->id, "delegatedGroup" => $auxString];
                    }
                    array_push($delegated, $newDelegation);
                }
            }

            echo json_encode($delegated);
        }
    }
}
  
