<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
	
session_start();
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
header('Access-Control-Allow-Origin: *');

// cfg

// $kburls = [ $kbHostUrl, $kbHostUrlAntHel, $baseKm4CityUri, $kbUrlHelsinki, $kbUrlAntwerp, $kbUrlSuperServiceMap ];
$kburls = [];

// get inputs

$pagesize = 10; if(isset($_GET["pageSize"])) $pagesize = intval($_GET["pageSize"]);
$pagenum = 1; if(isset($_GET["pageNum"])) $pagenum = intval($_GET["pageNum"]);
$search = ""; if(isset($_GET["search"])) $search = trim($_GET["search"]); 
$id = null; if(isset($_GET["id"]) && !empty($_GET["id"])) $id = $_GET["id"]; 

// authenticate
	
$accessToken = null;
$loggedUsername = null;
$loggedRole = null;
if(isset($_GET['accessToken'])) {
	$oidc = new OpenIDConnectClient();
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
	$oidc->setAccessToken($_GET['accessToken']);
    $uinfo = $oidc->requestUserInfo();
	if(isset($uinfo->error)) {
		header("HTTP/1.1 401 Unauthorized");
        echo json_encode($uinfo);
        die();
	}
	else {
		$accessToken = $_GET['accessToken'];
		$loggedUsername = $uinfo->preferred_username;
		$ROLES = array('RootAdmin','ToolAdmin','AreaManager','Manager','Observer','Public');
		if(isset($uinfo->roles)) {
			foreach($ROLES as $r) {
				if(in_array($r, $uinfo->roles)) {
					$loggedRole = $r;
					break;
				}
			}
		 }
	}
}
else if($_SESSION['accessToken'] && $_SESSION["loggedUsername"] && $_SESSION['loggedRole']) {
	$accessToken = $_SESSION['accessToken'];
	$loggedUsername = $_SESSION["loggedUsername"];
	$loggedRole = $_SESSION['loggedRole'];
}
else {
	header("Location: ../management/ssoLogin.php?redirect=".urlencode("$appUrl/api/sensors.php?pagesize=$pagesize&pagenum=$pagenum&search=$search&id=$id"));
	die();
}
  
// do the job

if($accessToken && $loggedUsername && $loggedRole) {
	
	if($loggedRole == "RootAdmin") {
		$ownersMap = getOwnersOfServiceURIs($ownershipApiBaseUrl,$accessToken,$kburls);		
		if($ownersMap === false) { header("HTTP/1.1 500 Internal Server Error"); die();	}
		if(empty($ownersMap)) { echo("[]"); die(); }		
		if($id == null) {
			$link = mysqli_connect($host, $username, $password);
			$query = "SELECT id, sub_nature deviceType, low_level_type valueName, unique_name_id deviceName, get_instances deviceId FROM DashboardWizard WHERE high_level_type = 'Sensor' and unique_name_id <> '' and ( low_level_type like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or sub_nature like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or unique_name_id like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' ) order by sub_nature, unique_name_id, low_level_type limit $pagesize offset ".($pagesize*($pagenum-1));	   		
			if(!$link->set_charset("utf8")) {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_select_db($link, $dbname);
			$result = mysqli_query($link, $query);
			$response = [];
			if($result) {
				while($row=mysqli_fetch_assoc($result)) {
				  $row["id"] = intval($row["id"]);
				  $row["deviceOwner"] = $ownersMap[$row["deviceId"]];
				  $response[]=$row;
				}
			}
			else {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_close($link);
			$eresponse = array( "heading" => array ( "pageNum" => $pagenum, "pageSize" => $pagesize ), "payload" => $response );
			$json = json_encode($eresponse,JSON_PRETTY_PRINT);		
			echo($json);
		}
		else {
			$link = mysqli_connect($host, $username, $password);
			$query = "SELECT id, sub_nature deviceType, low_level_type valueName, unique_name_id deviceName, get_instances deviceId FROM DashboardWizard WHERE high_level_type = 'Sensor' and unique_name_id <> '' and id in ( ".mysqli_real_escape_string($link, $id)." ) and ( low_level_type like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or sub_nature like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or unique_name_id like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' )"; 
			if(!$link->set_charset("utf8")) {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_select_db($link, $dbname);
			$result = mysqli_query($link, $query);
			if($result && !mysqli_num_rows($result)) {
				//header("HTTP/1.1 404 Not Found");    
				echo("[]");
				die();
				
			}
			$response = [];
			if($result) {				
				while($row=mysqli_fetch_assoc($result)) { 
					$row["id"] = intval($row["id"]);
					$row["deviceOwner"] = $ownersMap[$row["deviceId"]];
					$response[] = $row;
				}
			}
			else {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_close($link);
			$json = json_encode($response,JSON_PRETTY_PRINT);		
			echo($json);
		}
		die();
	}
	else {
		$ownedServiceURIs = getOwnedServiceURIs($ownershipApiBaseUrl,$accessToken,$kburls);
		if($ownedServiceURIs === false) { header("HTTP/1.1 500 Internal Server Error"); die(); }
		if($id == null) {
			$response = [];
			if(!empty($ownedServiceURIs)) {
				$link = mysqli_connect($host, $username, $password);
				$query = "SELECT id, sub_nature deviceType, low_level_type valueName, unique_name_id deviceName, get_instances deviceId FROM DashboardWizard WHERE high_level_type = 'Sensor' and get_instances in (".implode(",",$ownedServiceURIs).") and ( low_level_type like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or sub_nature like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or unique_name_id like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' ) order by sub_nature, unique_name_id, low_level_type limit $pagesize offset ".($pagesize*($pagenum-1));			
				if(!$link->set_charset("utf8")) {
					header("HTTP/1.1 500 Internal Server Error");    
					die();
				}
				mysqli_select_db($link, $dbname);
				$result = mysqli_query($link, $query);
				
				if($result) {
					while($row=mysqli_fetch_assoc($result)) {
					  $row["id"] = intval($row["id"]);
					  $row["deviceOwner"] = $loggedUsername;
					  $response[]=$row;
					}
				}
				else {
					header("HTTP/1.1 500 Internal Server Error");    
					die();
				}
				mysqli_close($link);
			}
			$eresponse = array( "heading" => array ( "pageNum" => $pagenum, "pageSize" => $pagesize ), "payload" => $response );
			$json = json_encode($eresponse,JSON_PRETTY_PRINT);
			echo($json);
		}
		else {
			$link = mysqli_connect($host, $username, $password);
			$query = "SELECT id, sub_nature deviceType, low_level_type valueName, unique_name_id deviceName, get_instances deviceId FROM DashboardWizard WHERE high_level_type = 'Sensor' and id in ( ".mysqli_real_escape_string($link, $id)." ) and ( low_level_type like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or sub_nature like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' or unique_name_id like '%".str_replace(" ","%",mysqli_real_escape_string($link, $search))."%' )";					
			if(!$link->set_charset("utf8")) {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_select_db($link, $dbname);
			$result = mysqli_query($link, $query);
			if($result && !mysqli_num_rows($result)) {
				//header("HTTP/1.1 404 Not Found");    
				echo("[]");
				die();
			}
			$response = [];			
			if($result) {
				while($row=mysqli_fetch_assoc($result)) {
					$row["id"] = intval($row["id"]);
					if(in_array("'".$row["deviceId"]."'",$ownedServiceURIs)) {
						$row["deviceOwner"] = $loggedUsername; 
					}
					else {
						$row["deviceOwner"] = null;
					}
					$response[] = $row;
				}
			}
			else {
				header("HTTP/1.1 500 Internal Server Error");    
				die();
			}
			mysqli_close($link);
			$json = json_encode($response,JSON_PRETTY_PRINT);
			echo($json);
		}
		
		die();
	}
  
} 
else {
	header("HTTP/1.1 500 Internal Server Error");    
    die();
}

function getOwnedServiceURIs($ownershipApiBaseUrl, $accessToken, $kburls) {
	
	$uris = [];	
		
	// Get ownership set directly on IOTID	
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=IOTID&accessToken=" . $accessToken;
	$options = array(
		  'http' => array(
				  'method'  => 'GET',
				  'timeout' => 30,
				  'ignore_errors' => true
		  )
	);
	$context  = stream_context_create($options);
	$callResult = file_get_contents($apiUrl, false, $context);
	if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
	foreach(json_decode($callResult,true) as $serviceURI) {
		$uris[] = "'".$serviceURI["elementUrl"]."'";
	}
	
	// Get ownership set directly on ServiceURI
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=ServiceURI&accessToken=" . $accessToken;
	$options = array(
		  'http' => array(
				  'method'  => 'GET',
				  'timeout' => 30,
				  'ignore_errors' => true
		  )
	);
	$context  = stream_context_create($options);
	$callResult = file_get_contents($apiUrl, false, $context);
	if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
	foreach(json_decode($callResult,true) as $serviceURI) {
		$uris[] = "'".$serviceURI["elementId"]."'";
	}
	// Get ownership set on graphs and explode
	if(!empty($kburls)) {
		$graphs = [];
		$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=ServiceGraphID&accessToken=" . $accessToken;
		$options = array(
			  'http' => array(
					  'method'  => 'GET',
					  'timeout' => 30,
					  'ignore_errors' => true
			  )
		);
		$context  = stream_context_create($options);
		$callResult = file_get_contents($apiUrl, false, $context);
		if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
		foreach(json_decode($callResult,true) as $graphURI) {
			$graphs[] = "'".$graphURI["elementId"]."'";
		}
		foreach($kburls as $kburl) {
			foreach($graphs as $graphURI) {
				$qry = $kburl."?default-graph-uri=&query=select+distinct+%3Fs+%7B+graph+%3C".$graphURI."%3E+%7B+%3Fs+a+%3Fc+%7D+%7D&format=text%2Fcsv&timeout=0&debug=on";
				$res = file_get_contents($qry);
				$graphuris = [];
				foreach(explode("\n",$res) as $line){
					if($line == "\"s\"") continue;
					$graphuris[] = trim($line,"\"");
				} 
				$uris = array_merge($uris,$graphuris);		
			}	
		}
	}
	// Return
	return $uris;
}

function getOwnersOfServiceURIs($ownershipApiBaseUrl, $accessToken, $kburls) {
	
	$uris = [];
	
	// Get ownership set directly on ServiceURI
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=IOTID&accessToken=" . $accessToken;
	$options = array(
		  'http' => array(
				  'method'  => 'GET',
				  'timeout' => 30,
				  'ignore_errors' => true
		  )
	);
	$context  = stream_context_create($options);
	$callResult = file_get_contents($apiUrl, false, $context);
	if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
	foreach(json_decode($callResult,true) as $serviceURI) {
		$uris[$serviceURI["elementUrl"]] = $serviceURI["username"];
	}
	
	// Get ownership set directly on ServiceURI
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=ServiceURI&accessToken=" . $accessToken;
	$options = array(
		  'http' => array(
				  'method'  => 'GET',
				  'timeout' => 30,
				  'ignore_errors' => true
		  )
	);
	$context  = stream_context_create($options);
	$callResult = file_get_contents($apiUrl, false, $context);
	if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
	foreach(json_decode($callResult,true) as $serviceURI) {
		$uris[$serviceURI["elementId"]] = $serviceURI["username"];
	}
	
	// Get ownership set on graphs and explode
	if(!empty($kburls)) {
		$graphs = [];
		$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=ServiceGraphID&accessToken=" . $accessToken;
		$options = array(
			  'http' => array(
					  'method'  => 'GET',
					  'timeout' => 30,
					  'ignore_errors' => true
			  )
		);
		$context  = stream_context_create($options);
		$callResult = file_get_contents($apiUrl, false, $context);
		if(array_key_exists("error",json_decode($callResult,true))) { echo($callResult); return false; }
		foreach(json_decode($callResult,true) as $graphURI) {
			$graphs[$graphURI["elementId"]] = $graphURI["username"];
		}
		foreach($kburls as $kburl) {
			foreach(array_keys($graphs) as $graphURI) {
				$qry = $kburl."?default-graph-uri=&query=select+distinct+%3Fs+%7B+graph+%3C".$graphURI."%3E+%7B+%3Fs+a+%3Fc+%7D+%7D&format=text%2Fcsv&timeout=0&debug=on";				
				$res = file_get_contents($qry);
				foreach(explode("\n",$res) as $line){
					if($line == "\"s\"") continue;
					$uris[trim($line,"\"")] = $graphs[$graphURI];
				} 		
			}	
		}
	}
	// Return
	return $uris;
}

?>
  