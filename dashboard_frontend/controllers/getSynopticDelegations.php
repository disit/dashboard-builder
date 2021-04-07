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

$delegated = [];

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername']) 
{
    $dashboardId = escapeForSQL($_REQUEST['id'], $link);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in getSynopticDelegations.php for id = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
	
	$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
	$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
	$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
	$_SESSION['refreshToken'] = $tkn->refresh_token;
	$accessToken = $tkn->access_token;
		
    if(!checkSynopticId($link, $dashboardId, $accessToken, $ownershipApiBaseUrl, $synOwnElmtType)) {
        eventLog("invalid request for getSynopticDelegations.php for id = $dashboardId user: ".$_SESSION['loggedUsername']);
        exit;
    }
    
	$dashboardAuthor = $_SESSION["loggedUsername"]; 
	if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin') {
		$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synOwnElmtType&accessToken=$accessToken";
		$options = array(
			  'http' => array(
					  'method'  => 'GET',
					  'timeout' => 30,
					  'ignore_errors' => true
			  )
		);
		$context  = stream_context_create($options);
		$callResult = file_get_contents($apiUrl, false, $context);	
		$ownership = json_decode($callResult,true);	
		foreach($ownership as $ownElmt) {
			if($dashboardId == $ownElmt["elementId"]) $dashboardAuthor = $ownElmt["username"];
		}
	}
	
	if(isset($_SESSION['refreshToken'])) 
	{
		
		// ENCODIZZARE username per username con SPAZI !!!
		$apiUrl = $personalDataApiBaseUrl . "/v2/username/" . rawurlencode($dashboardAuthor) . "/delegator?accessToken=" . $accessToken . "&sourceRequest=$synMgtSrcReq";
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
  
function checkSynopticId($link, $id, $accessToken, $ownershipApiBaseUrl, $synOwnElmtType) {
  	if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin') return true;
	$user = $_SESSION['loggedUsername'];  
	$r = mysqli_query($link, "SELECT id FROM DashboardWizard WHERE high_level_type = 'Synoptic' and id = '$id'");
  	if($r && mysqli_num_rows($r)>0) {
		$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synOwnElmtType&accessToken=$accessToken";
		$callResult = file_get_contents($apiUrl);
		$ownership = json_decode($callResult,true);
		foreach($ownership as $ownElmt) {
			if($id == $ownElmt["elementId"] && $user && $user == $ownElmt["username"]) {
				return true;
			}
		}
  	}
  	return false;
}