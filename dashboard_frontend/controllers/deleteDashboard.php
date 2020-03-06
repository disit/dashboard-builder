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
define('REST_API_ROOT', '/api/v1/');
define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
include "../rocket-chat-rest-client/RocketChatClient.php";
include "../rocket-chat-rest-client/RocketChatUser.php";
include "../rocket-chat-rest-client/RocketChatChannel.php";

require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = NULL;

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername'])
{
    $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in deleteDashboard.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
    $dashboardTitle = mysqli_real_escape_string($link, $_GET['dashboardTitle']);
    $username = mysqli_real_escape_string($link, $_SESSION['loggedUsername']);
    
    if($_SESSION['loggedRole']=='RootAdmin')
      $q = "UPDATE Dashboard.Config_dashboard SET deleted = 'yes' WHERE Id = '$dashboardId'";
    else
      $q = "UPDATE Dashboard.Config_dashboard SET deleted = 'yes' WHERE Id = '$dashboardId' AND user='$username'";
    $r = mysqli_query($link, $q);

    if($r && mysqli_affected_rows($link)==1)
    {
        $response = "Ok";

        //Salvataggio su API ownership
        if(isset($_SESSION['refreshToken']))
        {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;

        //    $dashboardIdValidated = checkVarType($dashboardId, "integer");
            $apiUrl = $ownershipApiBaseUrl . "/v1/delete/?type=DashboardID&elementId=". $dashboardId ."&accessToken=" . $accessToken;

            try
            {
                //    $context  = stream_context_create($options);
                $callResult = file_get_contents($apiUrl);
            }
            catch (Exception $ex)
            {
                //Non facciamo niente di specifico in caso di mancata risposta dell'host
            }

        }

    }
    else
    {
        $response = "Ko";
    }
    
    echo $response;
    
    if($response == "Ok" && $chatBaseUrl)
    {        
        $nameGroup=strtolower(str_replace(" ", "", str_replace('%2520','',str_replace('%20', '', $dashboardTitle."-".$dashboardId))));
        $nameGroup=urldecode ($nameGroup);
        $nameGroup = str_replace('à', 'a', $nameGroup);
        $nameGroup = str_replace('è', 'e', $nameGroup);
        $nameGroup = str_replace('é', 'e', $nameGroup);
        $nameGroup = str_replace('ì', 'i', $nameGroup);
        $nameGroup = str_replace('ò', 'o', $nameGroup);
        $nameGroup = str_replace('ù', 'u', $nameGroup);
        $nameGroup = str_replace('å', 'a', $nameGroup);
        $nameGroup = str_replace('ë', 'e', $nameGroup);
        $nameGroup = str_replace('ô', 'o', $nameGroup);
        $nameGroup = str_replace('á', 'a', $nameGroup);
        $nameGroup = str_replace('ç', 'c', $nameGroup);
        $nameGroup = str_replace('ÿ', 'y', $nameGroup);
        $nameGroup=preg_replace("/[^a-zA-Z0-9_-]/", "", $nameGroup);
        $admin = new \RocketChat\User();
        $admin->login();
        $channelArc = new \RocketChat\Channel('N');
        $infoChannel=$channelArc->infoByName($nameGroup);
        if(isset($infoChannel->channel->_id)){
        $channelArc->archive($infoChannel->channel->_id);
        }
       $admin->logout();
       
        
    }
    //Se cancellazione dashboard fallisce, non cancelliamo la chat
}


