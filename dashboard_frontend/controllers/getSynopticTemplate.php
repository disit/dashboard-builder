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
	 
	 $name = mysqli_real_escape_string($link, sanitizeGetString('name'));
	 
	 $accessToken = $_SESSION["accessToken"];
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
	$ownElmtIdUsr = [];
	foreach($ownership as $ownElmt) {
		$ownElmtIdUsr[$ownElmt["elementId"]] = $ownElmt["username"];
	}

    if ($_GET['role'] != "RootAdmin" && !$_GET["force"]) {
        $q = "SELECT * FROM SynopticTemplates WHERE high_level_type = 'SynopticTemplate' and unique_name_id = '$name' $filterOrgQuery ";
    } else {
        $q = "SELECT * FROM SynopticTemplates WHERE high_level_type = 'SynopticTemplate' and unique_name_id = '$name' ";
    }

    $r = mysqli_query($link, $q);

    if($r)
    {
        $response['applications'] = [];
		$delegations = [];
		$delegationsResponse = file_get_contents($personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegated?accessToken=" . $_SESSION["accessToken"]. "&sourceRequest=$synMgtSrcReq");		
		$delegations = json_decode($delegationsResponse,true);
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
				if($ownElmtIdUsr[$row["id"]]) $clone["user"] = $ownElmtIdUsr[$row["id"]];
				if($usernameDelegator) $clone["user"] = $usernameDelegator;
				array_push($response['applications'], $clone);
			}
			else if($_GET["force"]) {
				array_push($response['applications'], array("unique_name_id" => $row["unique_name_id"]));
			}
        }        
        $response['detail'] = 'Ok';
    }
    else
    {
        $response['detail'] = 'Ko';
    }

    echo json_encode($response);