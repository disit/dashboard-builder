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
            // $filterOrgQuery = "AND organizations REGEXP 'Other'";
			$filterOrgQuery = "";
        }
    }
	
	if (isset($_SESSION['loggedUsername'])){
        $user = $_SESSION['loggedUsername'];
    } else {
        $user = "";
    } 
	
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
	
	$natSnats = json_decode(file_get_contents($synTplNatSnatSrc),true)["content"];	
	
    if ($_GET['role'] != "RootAdmin") {
        $q = "SELECT * FROM DashboardWizard WHERE high_level_type = 'Synoptic' $filterOrgQuery ORDER BY sub_nature ASC";
    } else {
        $q = "SELECT * FROM DashboardWizard WHERE high_level_type = 'Synoptic' ORDER BY sub_nature ASC";
    }
	
    $r = mysqli_query($link, $q);

    if($r)
    {
        $response['applications'] = [];
		$delegations = [];
		$delegationsResponse = file_get_contents($personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegated?accessToken=$accessToken&sourceRequest=$synMgtSrcReq");
		$delegations = json_decode($delegationsResponse,true);
        while($row = mysqli_fetch_assoc($r))
        {
            $shallBeReturned = false;
			if($_SESSION['loggedRole'] == "RootAdmin") $shallBeReturned = true;
			if($row["ownership"] == "public") $shallBeReturned = true;
			if($user && $ownElmtIdUsr[$row["id"]] == $user) $shallBeReturned = true;
			$usernameDelegator = null;
			foreach($delegations as $delegation) {
				if($delegation["elementType"] == $synOwnElmtType && $delegation["elementId"] == $row["id"]) {
					$shallBeReturned = true;			
					$usernameDelegator = $delegation["usernameDelegator"];			
				}
			}			
			if($shallBeReturned) {
				$clone = $row;
				if($ownElmtIdUsr[$row["id"]]) {
					$clone["user"] = $ownElmtIdUsr[$row["id"]];
					if($clone["user"] == $_SESSION["loggedUsername"]) $clone["userLabel"] = "My own";
					if($clone["user"] == $_SESSION["loggedUsername"] && $row["ownership"] == "public") $clone["userLabel"] = "My own: Public";
				}
				if($usernameDelegator) {
					if($_SESSION['loggedRole'] == "RootAdmin" || $_SESSION['loggedRole'] == "ToolAdmin") {
						$clone["user"] = $usernameDelegator;			
						$clone["userLabel"] = "Delegated by";
					}
					else {
						$clone["user"] = "";
						$clone["userLabel"] = "Delegated";
					}
				}
				foreach($natSnats as $natSnat) {
					if($natSnat["type"] == "nature" && $natSnat["value"] == $clone["nature"]) $clone["nature_label"] = $natSnat["label"];
					if($natSnat["type"] == "subnature" && $natSnat["value"] == $clone["sub_nature"]) $clone["sub_nature_label"] = $natSnat["label"];
				}
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