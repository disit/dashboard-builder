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
        $filterOrgQuery = " AND organizations REGEXP '$org' ";
    }

    if (isset($_GET['param'])) {
        if ($_GET['param'] == "AllOrgs") {
            $filterOrgQuery = "";
        }
    }

    if (isset($_GET['role'])) {
        if ($_GET['role'] == "Public") {
            //$filterOrgQuery = "AND organizations REGEXP 'Other'";
			$filterOrgQuery = "";
        }
    }
	
	if (isset($_SESSION['loggedUsername'])){
        $user = $_SESSION['loggedUsername'];
    } else {
        $user = "";
    } 
	
	if($_SESSION['refreshToken']) {
		$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
		$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
		$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
		$accessToken = $tkn->access_token;
		$_SESSION['refreshToken'] = $tkn->refresh_token;
	}
	
	$ownElmtIdUsr = [];
	if($accessToken) {
		$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synTplOwnElmtType&accessToken=$accessToken";
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
			$ownElmtIdUsr[$ownElmt["elementId"]] = $ownElmt["username"];
		}
	}
	
	$natSnats = json_decode(file_get_contents($synTplNatSnatSrc),true)["content"];	
		
    if ($_GET['role'] != "RootAdmin") {
        $q = "SELECT * FROM SynopticTemplates WHERE high_level_type = 'SynopticTemplate' $filterOrgQuery ORDER BY unique_name_id ASC";
    } else {
        $q = "SELECT * FROM SynopticTemplates WHERE high_level_type = 'SynopticTemplate' ORDER BY unique_name_id ASC";
    }
		
    $r = mysqli_query($link, $q);

    if($r)
    {
		$response['applications'] = [];
		$delegations = [];
		if($accessToken) {
			$delegationsResponse = file_get_contents($personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegated?accessToken=$accessToken&sourceRequest=$synMgtSrcReq");
			$delegations = json_decode($delegationsResponse,true);
		}
        while($row = mysqli_fetch_assoc($r))
        {			
			$shallBeReturned = false;
			if($_SESSION['loggedRole'] == "RootAdmin") $shallBeReturned = true;
			if($row["ownership"] == "public") $shallBeReturned = true;
			if($user && $ownElmtIdUsr[$row["id"]] == $user) $shallBeReturned = true;
			$usernameDelegator = null;
			foreach($delegations as $delegation) {
				if($delegation["elementType"] == $synTplOwnElmtType && $delegation["elementId"] == $row["id"]) {
					$shallBeReturned = true;	
					$usernameDelegator = $delegation["usernameDelegator"];
				}
			}
			if($shallBeReturned) {
				$clone = $row;
				$clone["user"] = null;
				if($ownElmtIdUsr[$row["id"]]) {
					$clone["user"] = $ownElmtIdUsr[$row["id"]];
					if($clone["user"] == $_SESSION["loggedUsername"]) $clone["userLabel"] = "My own";
					else if($_SESSION['loggedRole'] != "RootAdmin") $clone["user"] = null;
				}
				if($usernameDelegator) {
					if($_SESSION['loggedRole'] == "RootAdmin" ) {
						$clone["user"] = $usernameDelegator;
						$clone["userLabel"] = "Delegated by";
					}
				}
				if($row["ownership"] == "public") {
					$clone["userLabel"] = "Public";
				}
				foreach($natSnats as $natSnat) {
					if($natSnat["type"] == "nature" && $natSnat["value"] == $clone["nature"]) $clone["nature_label"] = $natSnat["label"];
					if($natSnat["type"] == "subnature" && $natSnat["value"] == $clone["sub_nature"]) $clone["sub_nature_label"] = $natSnat["label"];
				}
				array_push($response['applications'], $clone);
			}
        }        
        $response['detail'] = 'Ok';		
    }
    else
    {
        $response['detail'] = 'Ko';
    }

	mysqli_close($link);
		
    echo(json_encode($response));
	
?>	