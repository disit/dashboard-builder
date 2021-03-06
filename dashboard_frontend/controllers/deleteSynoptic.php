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

$response = NULL;

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername'])
{
    $dashboardId = mysqli_real_escape_string($link, $_GET['id']);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in deleteSynoptic.php for id = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
    $dashboardTitle = mysqli_real_escape_string($link, $_GET['synTitle']);
    $username = mysqli_real_escape_string($link, $_SESSION['loggedUsername']);
    
	$authorized = (isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin');
	$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
	$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
	$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
	$accessToken = $tkn->access_token;
	$_SESSION['refreshToken'] = $tkn->refresh_token;
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synOwnElmtType&accessToken=$accessToken";
	$callResult = file_get_contents($apiUrl);
	$ownership = json_decode($callResult,true);
	foreach($ownership as $ownElmt) {
		if($dashboardId == $ownElmt["elementId"] && $username && $username == $ownElmt["username"]) {
			$authorized = true;
		}
	}
	
	if(!$authorized) {
		$response = "Ko";
		exit();
	}
	
	$q = "DELETE FROM DashboardWizard WHERE high_level_type = 'Synoptic' and id = $dashboardId";
    
	$r = mysqli_query($link, $q);

    if($r && mysqli_affected_rows($link)==1)
    {
        $response = "Ok";

        //Salvataggio su API ownership
        if(isset($_SESSION['refreshToken']))
        {

        //    $dashboardIdValidated = checkVarType($dashboardId, "integer");
            $apiUrl = $ownershipApiBaseUrl . "/v1/delete/?type=$synOwnElmtType&elementId=". $dashboardId ."&accessToken=" . $accessToken;

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

}

mysqli_close($link);

?>