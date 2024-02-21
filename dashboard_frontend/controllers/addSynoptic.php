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
	include '../opensearch/OpenSearchS4C.php';
	$open_search = new OpenSearchS4C();

	use Jumbojett\OpenIDConnectClient;
	
    error_reporting(E_ERROR);
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
	
	 $nature = null;
	 $subnature = null;
	 $tplStmt = mysqli_prepare($link, "SELECT nature, sub_nature FROM SynopticTemplates WHERE low_level_type = ?");
	 mysqli_stmt_bind_param($tplStmt, "s", $lowLevelType);
	 if(mysqli_stmt_execute($tplStmt)) {
		 mysqli_stmt_bind_result($tplStmt,$tplNature,$tplSubNature);
		 while(mysqli_stmt_fetch($tplStmt)) {
			$nature = $tplNature;
			$subnature = $tplSubNature;
		 }
	 }
	 else {
		 mysqli_close($link);
		 $response['result'] = 'Ko';
		 $response['detail'] = "Template's metadata could not be retrieved.";
		 echo json_encode($response);
		 die();		 
	 }
	 if($nature == null || $subnature == null) {
		 mysqli_close($link);
		 $response['result'] = 'Ko';
		 $response['detail'] = "Template's metadata are not valid.";
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
		$response['detail'] = "The name of the new synoptic has not been specified.";
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
	
	/*
	$microAppExtServIcon = null;
	if($_FILES['getIcon']['size'] > 0)
    {
        $filename = $_FILES['getIcon']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $lowLevelType."_".$uniqueNameId . "." . $ext;
        $filename = preg_replace('/\s+/', '_', $filename);
        $microAppExtServIcon = $filename;
    }
    else
    {
		$tplStmt = mysqli_prepare($link, "SELECT microAppExtServIcon FROM SynopticTemplates WHERE low_level_type = ?");
		mysqli_stmt_bind_param($tplStmt, "s", $lowLevelType);
		if(mysqli_stmt_execute($tplStmt)) {
			mysqli_stmt_bind_result($tplStmt,$resMicroAppExtServIcon);
			while(mysqli_stmt_fetch($tplStmt)) {
				copy("../img/synopticTemplates/$resMicroAppExtServIcon","../img/synoptics/$uniqueNameId.svg");
				$microAppExtServIcon = "$uniqueNameId.svg";
			}
		}
    }
	*/
	
    $lastCheck = date("Y-m-d H:i:s");
    
    $q = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, last_date, last_value, unit, metric, saved_direct, kb_based, sm_based, user, widgets, parameters, healthiness, microAppExtServIcon, lastCheck, ownership, organizations, value_name, device_model_name) " .
         "VALUES ('$nature','Synoptic', '$subnature', '$lowLevelType', '$uniqueNameId', NULL, NULL, NULL, NULL, 'webpage', 'no', 'direct', NULL, 'no', NULL, NULL,'$synBaseUrl', 'true', '', '$lastCheck', '$ownership', '$organizationArray', '$uniqueNameId', '$lowLevelType')";
    $r = mysqli_query($link, $q);

    if(isset($useOpenSearch) && $useOpenSearch == "yes") {
        $return_data = $open_search->createUpdateDocumentDashboardWizard(
            $nature,
            'Synoptic',
            $subnature,
            $lowLevelType,
            $uniqueNameId,
            '',
            '',
            'webpage',
            'no',
            'direct',
            '',
            'no',
            '',
            '',
            $synBaseUrl,
            '',
            '',
            'true',
            $lastCheck,
            $ownership,
            $organizationArray,
            '',
            '',
            '',
            $uniqueNameId,
            '',
            $lowLevelType,
            '',
            '',
            '',
            '',
            '',
            false,
            false,
            null,
            OpenSearchS4C::default_index_name,
            $lowLevelType

        );
    }
	

 //   {
        if(isset($useOpenSearch) && $useOpenSearch == "yes" && isset($return_data['_id'])) {
            $synopticId = $return_data['_id'];//mysqli_insert_id($link);
        } else {
            $synopticId = mysqli_insert_id($link);
        }
		
		$tplStmt = mysqli_prepare($link, "SELECT microAppExtServIcon FROM SynopticTemplates WHERE low_level_type = ?");
		mysqli_stmt_bind_param($tplStmt, "s", $lowLevelType);
		if(mysqli_stmt_execute($tplStmt)) {
			mysqli_stmt_bind_result($tplStmt,$resMicroAppExtServIcon);
			while(mysqli_stmt_fetch($tplStmt)) {
				$ext = pathinfo("../img/synopticTemplates/$resMicroAppExtServIcon")["extension"];
				copy("../img/synopticTemplates/$resMicroAppExtServIcon","../img/synoptics/$synopticId.$ext");
				if(file_exists("../img/synoptics/$synopticId.$ext")) chmod("../img/synoptics/$synopticId.$ext", 0777); 
				$microAppExtServIcon = "$synopticId.$ext";
			}
		}
		
		mysqli_query($link,"update DashboardWizard set microAppExtServIcon = '$microAppExtServIcon' where id = $synopticId");
	
		$u = mysqli_query($link, "UPDATE Dashboard.DashboardWizard SET parameters = '$synBaseUrl$synopticId' WHERE id = $synopticId");

        if(isset($useOpenSearch) && $useOpenSearch == "yes" && isset($return_data['_id'])) {
            $open_search->createUpdateDocumentDashboardWizard(
                $nature,
                'Synoptic',
                $subnature,
                $lowLevelType,
                $uniqueNameId,
                '',
                '',
                'webpage',
                'no',
                'direct',
                '',
                'no',
                '',
                '',
                $synBaseUrl . $synopticId,
                '',
                '',
                'true',
                $lastCheck,
                $ownership,
                $organizationArray,
                '',
                '',
                '',
                $uniqueNameId,
                '',
                $lowLevelType,
                '',
                '',
                '',
                '',
                $microAppExtServIcon,
                $synopticId

            );
        }
		
		if($u) {

			$uploadFolder = "../img/synoptics/";
			
			if(!file_exists($uploadFolder))
			{
				$oldMask = umask(0);
				mkdir($uploadFolder, 0777);
				umask($oldMask);
			}
			
			if($_FILES['getIcon']['size'] > 0)
			{
				if(!move_uploaded_file($_FILES['getIcon']['tmp_name'], $uploadFolder.$microAppExtServIcon))
				{  
					$queryFail = true;
				}
				else 
				{
				   chmod($uploadFolder.$microAppExtServIcon, 0777); 
				}
			}
			
			$isMapOk = true;
			foreach(array_keys($_POST) as $variable) {
				if((!in_array($variable,["nature","subnature","ownership","getIcon","low_level_type","unique_name_id"]))) {
					$tplVarName = mysqli_real_escape_string($link, substr($variable,1+strpos($variable,"_")));
					$tplVarRole = mysqli_real_escape_string($link, substr($variable,0,strpos($variable,"_")));
					$usrVarName = mysqli_real_escape_string($link, sanitizePostString($variable));
					if($usrVarName == "do_create_new_shared_variable" || !$usrVarName) $usrVarName = $tplVarName;
					if(!mysqli_query($link,"INSERT INTO SynopticMappings(synoptic_id,tpl_var_name,tpl_var_role,usr_var_name) values ($synopticId,'$tplVarName','$tplVarRole','$usrVarName')")) $isMapOk = false;
				}
			}
			if($isMapOk) {							
				$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
				$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
				$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
				$accessToken = $tkn->access_token;
				$_SESSION['refreshToken'] = $tkn->refresh_token;
	
				$apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=$accessToken";
				$ownData = array(
					"elementId" => $synopticId,
					"elementType" => $synOwnElmtType,
					"elementName" => $uniqueNameId,
					"elementUrl" => "$synBaseUrl$synopticId",
					"elementDetails" => array(),
					"accessToken" => $accessToken
				);
				
				$options = array(
					  'http' => array(
							  'method'  => 'POST',
							  'header' => "Content-Type: application/json",
							  'content' => json_encode($ownData),
							  'timeout' => 30,
							  'ignore_errors' => true
					  )
				);
				$context  = stream_context_create($options);
				$callResult = file_get_contents($apiUrl, false, $context);	
				
				if(!json_decode($callResult,true)["error"]) {
					$response['result'] = "Ok";
				}
				else {
					$response['result'] = "Ko"; 
					$response['detail'] = json_decode($callResult,true)["error"];
				}				
				
				$response['id'] = $synopticId;
				$response['url'] = escapeForHTML("$synBaseUrl$synopticId"); 
			}
			else {
				$response['result'] = "Ko";
				$response['detail'] = "Variable mappings could not be saved for the new synoptic.";
			}
		}
		else {
			$response['result'] = "Ko";
			$response['detail'] = "The new synoptic's URL could not be set.";
		}
 /*   }
    else
    {
        $response['result'] = "Ko";
		$response['detail'] = "The new synoptic could not be saved. ".mysqli_error($link);
    }   */
    			
	mysqli_close($link);
	
    echo json_encode($response);
	
