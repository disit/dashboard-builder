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

    if (isset($_GET['orgFilter'])) {
        $org = mysqli_real_escape_string($link, $_GET['orgFilter']);
        $filterOrgQuery = "AND organizations REGEXP '$org'";
    }

    if (isset($_GET['param'])) {
        if ($_GET['param'] == "AllOrgs") {
            $filterOrgQuery = "";
        }
    }

    if (isset($_GET['role'])) {
        if ($_GET['role'] == "Public") {
            $filterOrgQuery = "AND organizations REGEXP 'Other'";
        }
    }
	
	if (isset($_SESSION['loggedUsername'])){
        $user = $_SESSION['loggedUsername'];
    } else {
        $user = "";
    } 
	 
	$uniqueNameId = mysqli_real_escape_string($link, sanitizeGetString('uniqueNameId'));
	 
	$lowLevelType = mysqli_real_escape_string($link, sanitizeGetString('lowLevelType'));
	 
	$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
	$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
	$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
	$accessToken = $tkn->access_token;
	$_SESSION['refreshToken'] = $tkn->refresh_token;
	
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
	$ownElmtIdUsr = [];
	foreach($ownership as $ownElmt) {
		$ownElmtIdUsr[$ownElmt["elementId"]] = $ownElmt["username"];
    }

    if ($_GET['role'] != "RootAdmin") {
        $q = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = 'Synoptic' and low_level_type = '$lowLevelType' and unique_name_id = '$uniqueNameId' $filterOrgQuery";
    } else {
        $q = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = 'Synoptic' and low_level_type = '$lowLevelType' and unique_name_id = '$uniqueNameId'";
    }

    $r = mysqli_query($link, $q);

    if($r)
    {
        $response['applications'] = [];
        while($row = mysqli_fetch_assoc($r))
        {
            
			$shallBeReturned = false;
			if($_SESSION['loggedRole'] == "RootAdmin") $shallBeReturned = true;
			if($row["ownership"] == "public") $shallBeReturned = true;			
			if($user && $ownElmtIdUsr[$row["id"]] == $user) $shallBeReturned = true;
			$delegations = json_decode(file_get_contents($personalDataApiBaseUrl . "/v1/username/$user/delegated?accessToken=$accessToken&sourceRequest=$synMgtSrcReq"),true);
			$usernameDelegator = null;
			foreach($delegations as $delegation) {
				if($delegation["elementType"] == $synOwnElmtType && $delegation["elementId"] == $row["id"]) {					
					$shallBeReturned = true;			
					$usernameDelegator = $delegation["usernameDelegator"];					
				}
			}
			if($shallBeReturned) {
				
				$clone = $row;
				if($ownElmtIdUsr[$row["id"]]) $clone["user"] = $ownElmtIdUsr[$row["id"]];
				if($usernameDelegator) $clone["user"] = $usernameDelegator;
				array_push($response['applications'], $clone);

				$mq = "SELECT * FROM Dashboard.SynopticMappings WHERE synoptic_id = ".$row["id"];
				$m = mysqli_query($link, $mq);
				if($m) {
					while($rowm = mysqli_fetch_assoc($m)) {
						$response["applications"][count($response["applications"])-1][$rowm["tpl_var_role"]][$rowm["tpl_var_name"]] = $rowm["usr_var_name"];
					}
				}
				
			}
			
        }        
        $response['detail'] = 'Ok';
    }
    else
    {
        $response['detail'] = 'Ko';
    }

	mysqli_close($link);
	
    echo json_encode($response);