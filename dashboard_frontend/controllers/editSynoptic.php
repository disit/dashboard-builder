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
    checkSession('Manager','../management/ssoLogin.php');

	$organizationArray = $_SESSION['loggedOrganization'];
	
    if (isset($_SESSION['loggedUsername'])){
        $user = $_SESSION['loggedUsername'];
    } else {
        $user = "";
    }   

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $response = NULL;
    
    if (sanitizePostString('low_level_type') == null) {      
        $lowLevelType = mysqli_real_escape_string($link, sanitizeGetString('low_level_type'));
    } else {
        $lowLevelType = mysqli_real_escape_string($link, sanitizePostString('low_level_type'));
    }
	$lowLevelType = str_replace("/", "_", $lowLevelType);
    $lowLevelType = str_replace(".", "_", $lowLevelType);
    $lowLevelType = str_replace("\\", '_', $lowLevelType);
    $lowLevelType = str_replace(":", "_", $lowLevelType);
	$lowLevelType = str_replace("'", "_", $lowLevelType);
	if(empty($lowLevelType)) {
		$response['result'] = "Ko";
		$response['detail'] = "The template to be used has not been specified.";
		echo json_encode($response);
		die();
	}
	
    if (sanitizePostString('unique_name_id') == null) {       
        $uniqueNameId = mysqli_real_escape_string($link, sanitizeGetString('unique_name_id'));
    } else {
        $uniqueNameId = mysqli_real_escape_string($link, sanitizePostString('unique_name_id'));
    }
    $uniqueNameId = str_replace("/", "_", $uniqueNameId);
    $uniqueNameId = str_replace(".", "_", $uniqueNameId);
    $uniqueNameId = str_replace("\\", '_', $uniqueNameId);
    $uniqueNameId = str_replace(":", "_", $uniqueNameId);
	$uniqueNameId = str_replace("'", "_", $uniqueNameId);
	if(empty($uniqueNameId)) {
		$response['result'] = "Ko";
		$response['detail'] = "The name of the synoptic has not been specified.";
		echo json_encode($response);
		die();
	}

    if (sanitizePostString('ownership') == null) {      
        $ownership = mysqli_real_escape_string($link, sanitizeGetString('ownership'));
    } else {
        $ownership = mysqli_real_escape_string($link, sanitizePostString('ownership'));
    }
	if($ownership != "private") {
		mysqli_close($link);
		$response['result'] = "Ko";
		$response['detail'] = "The ownership indication is not valid.";
		echo json_encode($response);
		die();
	}
	
	$microAppExtServIcon = null;
	if($_FILES['getIcon']['size'] > 0)
    {
        $filename = $_FILES['getIcon']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $lowLevelType."_".$uniqueNameId . "." . $ext;
        $filename = preg_replace('/\s+/', '_', $filename);
        $microAppExtServIcon = $filename;
    }
	
    $lastCheck = date("Y-m-d H:i:s");
	
	$synopticId = preg_replace("/[^0-9]/", "", $_POST["id"] );
	
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
	if($ownElmtIdUsr[$synopticId] != $user && !(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin')) {
		mysqli_close($link);
		$response['result'] = "Ko";
		$response['detail'] = "Unauthorized.";
		echo json_encode($response);
		die();
	}
		
	$d = mysqli_query($link, "DELETE FROM SynopticMappings WHERE synoptic_id = $synopticId");
	if($d) {
		$isMapOk = true;
		foreach(array_keys($_POST) as $variable) {
			if((!in_array($variable,["nature","subnature","ownership","getIcon","id","unique_name_id","low_level_type"]))) {
				$tplVarName = mysqli_real_escape_string($link, substr($variable,1+strpos($variable,"_")));
				$tplVarRole = mysqli_real_escape_string($link, substr($variable,0,strpos($variable,"_")));
				$usrVarName = mysqli_real_escape_string($link, sanitizePostString($variable));
				if($usrVarName == "do_create_new_shared_variable" || !$usrVarName) $usrVarName = $tplVarName;
				if(!mysqli_query($link,"INSERT INTO SynopticMappings(synoptic_id,tpl_var_name,tpl_var_role,usr_var_name) values ($synopticId,'$tplVarName','$tplVarRole','$usrVarName')")) $isMapOk = false;
			}
		}
		if($isMapOk) {
			
			$uploadFolder = "../img/synoptics/";
			if($microAppExtServIcon)
			{
				if(!move_uploaded_file($_FILES['getIcon']['tmp_name'], $uploadFolder.$microAppExtServIcon))
				{  
					$queryFail = true;
				}
				else 
				{
				   chmod($uploadFolder.$microAppExtServIcon, 0777); 
				   $i = mysqli_query($link, "UPDATE DashboardWizard SET microAppExtServIcon = '$microAppExtServIcon' WHERE id = $synopticId");	
				}				
			}
			$response['result'] = "Ok";
			$response['url'] = escapeForHTML("$synBaseUrl$synopticId");
		}
		else {
			$response['result'] = "Ko";
			$response['detail'] = "Variable mappings could not be saved.";
		}
	}
	else {
		$response['result'] = "Ko";
		$response['detail'] = "Variable mappings could not be saved.";
	}
	mysqli_close($link);
    
    echo json_encode($response);
?>