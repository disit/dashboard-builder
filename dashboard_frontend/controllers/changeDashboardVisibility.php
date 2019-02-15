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
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
    $dashboardTitle = mysqli_real_escape_string($link, $_REQUEST['dashboardTitle']);
    $newVisibility = mysqli_real_escape_string($link, $_REQUEST['newVisibility']);
    $dashboardAuthor = $_SESSION['loggedUsername'];
    
    if($newVisibility == 'private')
    {
        $dbVisibility = 'author';
    }
    else
    {
        $dbVisibility = 'public';
    }
     
    if(isset($_SESSION['refreshToken'])) 
    {
        $q = "UPDATE Dashboard.Config_dashboard SET visibility='$dbVisibility' WHERE Id = $dashboardId";
        $r = mysqli_query($link, $q);

        if($r) 
        {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;

            if($newVisibility == 'public')
            {
                //Da privata a pubblica: 1) Cancelliamo deleghe pregresse; 
                $apiUrl = $personalDataApiBaseUrl . "/v1/apps/" . $dashboardId . "/delegations?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

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
                    //2) Aggiungiamo delega anonima; 
                    $callBody = ["usernameDelegated" => "ANONYMOUS", "elementId" => $dashboardId, "elementType" => "DashboardID"];
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
                        }
                        else
                        {
                            $response['detail'] = 'AddPublicDelegationApiCallKo';
                            $response['detail2'] = $http_response_header[0];
                        }
                    }
                    catch(Exception $ex) 
                    {
                        $response['detail'] = 'DelOldDelegationsApiCallKo';
                    }
                }
                else
                {
                    $response['detail'] = 'DeleteOldDelegationsKo';
                }
            }
            else
            {
                //Da pubblica a privata: 1) Cancelliamo deleghe (anche quella pubblica);
                $apiUrl = $personalDataApiBaseUrl . "/v1/apps/" . $dashboardId . "/delegations?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

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
                    $response['detail'] = 'DeleteOldDelegationsKo';
                }
            }
        }
        else
        {
            $response['detail'] = 'UpdateDbKo';
        }
    }
    
    echo json_encode($response);
}
  
