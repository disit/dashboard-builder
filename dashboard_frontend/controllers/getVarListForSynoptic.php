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
	
	try {
		
		$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
		$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
		$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
		$accessToken = $tkn->access_token;
		$_SESSION['refreshToken'] = $tkn->refresh_token;
		
		// Private MyKPIs
		
		$apiUrl = "$personalDataApiBaseUrl/v1/kpidata/?sourceRequest=$synMgtSrcReq&highLevelType=MyKPI";
		$options = array(
			  'http' => array(
					  'method'  => 'GET',
					  'timeout' => 30,
					  'ignore_errors' => true,
					  "header" => "Authorization: Bearer $accessToken"
			  )
		);
		$context  = stream_context_create($options);
		$callResult = file_get_contents($apiUrl, false, $context);
		$privateMyKPI = json_decode($callResult);
		for($i = 0; $i < count($privateMyKPI); $i++) { $privateMyKPI[$i]->sk = "MyKPI ".$privateMyKPI[$i]->id; $privateMyKPI[$i]->isMyOwn = true; }
		
		$publicMyKPI = []; 
		$orgMyKPI = [];
		$delegatedMyKPI = [];
		
		if($_SESSION["loggedRole"] != "RootAdmin") 
		{

			// Public MyKPIs
			
			$apiUrl = "$personalDataApiBaseUrl/v1/public/kpidata/?sourceRequest=$synMgtSrcReq&highLevelType=MyKPI";
			$options = array(
				  'http' => array(
						  'method'  => 'GET',
						  'timeout' => 30,
						  'ignore_errors' => true,
						  "header" => "Authorization: Bearer $accessToken"
				  )
			);
			$context  = stream_context_create($options);
			$callResult = file_get_contents($apiUrl, false, $context);
			$publicMyKPI = json_decode($callResult);	
			for($i = 0; $i < count($publicMyKPI); $i++) {
				$publicMyKPI[$i]->isPublic = true;
				for($j = 0; $j < count($privateMyKPI); $j++) {
					if($privateMyKPI[$j]->id == $publicMyKPI[$i]->id) {
						$publicMyKPI[$i]->isDuplicated = true;
					}
				}
				$publicMyKPI[$i]->sk = "MyKPI ".$publicMyKPI[$i]->id;
			}
			
			$apiUrl = "$personalDataApiBaseUrl/v1/kpidata/organization/?sourceRequest=$synMgtSrcReq&highLevelType=MyKPI";
			$options = array(
				  'http' => array(
						  'method'  => 'GET',
						  'timeout' => 30,
						  'ignore_errors' => true,
						  "header" => "Authorization: Bearer $accessToken"
				  )
			);
			$context  = stream_context_create($options);
			$callResult = file_get_contents($apiUrl, false, $context);
			$orgMyKPI = json_decode($callResult);
			for($i = 0; $i < count($orgMyKPI); $i++) {
				$orgMyKPI[$i]->isPublic = true;
				for($j = 0; $j < count($privateMyKPI); $j++) {
					if($privateMyKPI[$j]->id == $orgMyKPI[$i]->id) {
						$orgMyKPI[$i]->isDuplicated = true;
					}
				}
				$orgMyKPI[$i]->sk = "MyKPI ".$orgMyKPI[$i]->id;
			}
			
			// Delegated MyKPIs
			
			$apiUrl = "$personalDataApiBaseUrl/v1/kpidata/delegated/?sourceRequest=$synMgtSrcReq&highLevelType=MyKPI";
			$options = array(
				  'http' => array(
						  'method'  => 'GET',
						  'timeout' => 30,
						  'ignore_errors' => true,
						  "header" => "Authorization: Bearer $accessToken"
				  )
			);
			$context  = stream_context_create($options);
			$callResult = file_get_contents($apiUrl, false, $context);
			$delegatedMyKPI = json_decode($callResult);
			for($i = 0; $i < count($delegatedMyKPI); $i++) {
				$delegatedMyKPI[$i]->isDelegated = true;
				for($j = 0; $j < count($privateMyKPI); $j++) {
					if($privateMyKPI[$j]->id == $delegatedMyKPI[$i]->id) {
						$delegatedMyKPI[$i]->isDuplicated = true;
					}
				}
				$delegatedMyKPI[$i]->sk = "MyKPI ".$delegatedMyKPI[$i]->id;
			}

		}
		
		
		// Temporary/test listing of sensors
		
		$sensors = [];
		
		if($synFakeSensors) for($i = 1; $i < 11; $i++) array_push($sensors,
			array( 
				"isSensor" => true, 
				"isPublic" => true,
				"id" => "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/Water_detector".str_pad("$i",2,"0",STR_PAD_LEFT)." water",
				"valueName" => "Water_detector".str_pad("$i",2,"0",STR_PAD_LEFT), 
				"valueType" => "water", 
				"deviceURI" => "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/Water_detector".str_pad("$i",2,"0",STR_PAD_LEFT),
				"ownership" => "public",
				"dataType" => "float",
				"organizations" => "DISIT",
				"sk" => "Sensor "."http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/Water_detector".str_pad("$i",2,"0",STR_PAD_LEFT)." water"
			)
		);
		
		// Shared variables
		
		$shared = [];
		$link = mysqli_connect($host, $username, $password);
		mysqli_select_db($link, $dbname);
		$q = "SELECT distinct usr_var_name FROM SynopticMappings WHERE usr_var_name LIKE 'shared_%' order by usr_var_name";
		$r = mysqli_query($link, $q);
		if($r)
		{
			while($row = mysqli_fetch_assoc($r)) {
				array_push($shared,
					array( 
						"isShared" => true, 
						"ownership" => "public",
						"varName" => $row["usr_var_name"],
						"organizations" => $_SESSION["loggedOrganization"],
						"sk" => "Shared ".$row["usr_var_name"]
					)
				);
			}				
		}
		mysqli_close($link);
		
		// In evidence
		
		$favourites = [];
		$link = mysqli_connect($host, $username, $password);
		mysqli_select_db($link, $dbname);
		$stmt = mysqli_prepare($link, "SELECT d.high_level_type, d.get_instances, d.low_level_type, d.unique_name_id, d.unit, d.organizations, d.ownership FROM Dashboard.SynopticVarPresel s, Dashboard.DashboardWizard d WHERE s.sel = d.id and s.usr = ?");
		$bindp = mysqli_stmt_bind_param($stmt, "s", encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod));
		$exec = mysqli_stmt_execute($stmt);		
		$bindr = mysqli_stmt_bind_result($stmt, $preselType, $preselId, $valueType, $deviceName, $dataType, $organizations, $ownership);
		while(mysqli_stmt_fetch($stmt)) {
			if("MyKPI" == $preselType) {
				foreach($privateMyKPI as $onePrivateMyKPI) {
					if($onePrivateMyKPI->id == str_replace("datamanager/api/v1/poidata/","",$preselId)) {
						$clonedOnePrivateMyKPI = clone $onePrivateMyKPI;
						$clonedOnePrivateMyKPI->isFavourite = true;
						$clonedOnePrivateMyKPI->sk = "Favourite MyKPI ".$clonedOnePrivateMyKPI->id;
						array_push($favourites,$clonedOnePrivateMyKPI);
					}
				}
				foreach($publicMyKPI as $onePublicMyKPI) {
					if($onePublicMyKPI->id == str_replace("datamanager/api/v1/poidata/","",$preselId)) {
						$clonedOnePublicMyKPI = clone $onePublicMyKPI;
						$clonedOnePublicMyKPI->isFavourite = true;
						$clonedOnePublicMyKPI->sk = "Favourite MyKPI ".$clonedOnePublicMyKPI->id;
						array_push($favourites,$clonedOnePublicMyKPI);
					}
				}
				foreach($delegatedMyKPI as $oneDelegatedMyKPI) {
					if($oneDelegatedMyKPI->id == str_replace("datamanager/api/v1/poidata/","",$preselId)) {
						$clonedOneDelegatedMyKPI = clone $oneDelegatedMyKPI;
						$clonedOneDelegatedMyKPI->isFavourite = true;
						$clonedOneDelegatedMyKPI->sk = "Favourite MyKPI ".$clonedOneDelegatedMyKPI->id;
						array_push($favourites,$clonedOneDelegatedMyKPI);
					}
				}
			}			
			else {
				array_push($favourites, array( 
					"isSensor" => true, 
					"isFavourite" => true,
					"isPublic" => ($ownership == "public"),
					"id" => "$preselId $valueType",
					"valueName" => $deviceName, 
					"valueType" => $valueType, 
					"deviceURI" => $preselId,
					"ownership" => $ownership,
					"dataType" => $dataType,
					"organizations" => $organizations,
					"sk" => "Favourite Sensor $preselId $valueType"
				));
			}
		}
		mysqli_close($link);
		
		$myKPI = array_merge($favourites, $privateMyKPI, $publicMyKPI, $delegatedMyKPI, $sensors, $shared);		
		
		$myKPI = array_filter($myKPI, function($item){ return !$item->isDuplicated; });
		
		asort($myKPI, function($a, $b) {
			return strcmp($b["sk"], $a["sk"]);
		});
		
		echo(json_encode($myKPI,JSON_PRETTY_PRINT));	
	}
	catch(Exception $e) {
		echo("[]");
	}