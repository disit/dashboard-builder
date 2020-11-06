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
    checkSession('AreaManager','../management/ssoLogin.php');

	$organizationArray = $_SESSION['loggedOrganization'];
		
    if (isset($_SESSION['loggedUsername'])){
        $user = $_SESSION['loggedUsername'];
    } else {
        $user = "";
    }   

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $response = [];

    if (sanitizePostString('edit') == null) {      
        $edit = mysqli_real_escape_string($link, sanitizeGetString('edit'));
    } else {
        $edit = mysqli_real_escape_string($link, sanitizePostString('edit'));
    }
	
	
    if (sanitizePostString('name') == null) {       
        $name = mysqli_real_escape_string($link, sanitizeGetString('name'));
    } else {
        $name = mysqli_real_escape_string($link, sanitizePostString('name'));
    }
	$name = str_replace("/", "_", $name);
    $name = str_replace(".", "_", $name);
    $name = str_replace("\\", '_', $name);
    $name = str_replace(":", "_", $name);
	$name = str_replace("'", "_", $name);
	if(empty($name)) {
		mysqli_close($link);
		$response['result'] = "Ko";
		$response['detail'] = "The name of the template has not been specified.";
		echo json_encode($response);
		die();
	}
	
	// Find out ID to be appended to filename 
	if($edit) {
		$q4id = "SELECT id FROM SynopticTemplates WHERE unique_name_id = '$name'";
		$r4id = mysqli_query($link, $q4id);
		while($idr = mysqli_fetch_assoc($r4id)) {
			$synopticTplId = $idr["id"];
		}
	}
	else {
		$q4id = "SELECT 1+max(id) as id FROM SynopticTemplates";
		$r4id = mysqli_query($link, $q4id);
		while($idr = mysqli_fetch_assoc($r4id)) {
			$synopticTplId = $idr["id"];
		}
	}
	//
    
    if (sanitizePostString('nature') == null) {       
        $nature = mysqli_real_escape_string($link, sanitizeGetString('nature'));
    } else {
        $nature = mysqli_real_escape_string($link, sanitizePostString('nature'));
    }
	$nature = str_replace("/", "_", $nature);
    $nature = str_replace(".", "_", $nature);
    $nature = str_replace("\\", '_', $nature);
    $nature = str_replace(":", "_", $nature);
	$nature = str_replace("'", "_", $nature);
	if(empty($nature)) {
		mysqli_close($link);
		$response['result'] = "Ko";
		echo json_encode($response);
		die();
	}
	
	if (sanitizePostString('nature_old') == null) {      
        $nature_old = mysqli_real_escape_string($link, sanitizeGetString('nature_old'));
    } else {
        $nature_old = mysqli_real_escape_string($link, sanitizePostString('nature_old'));
    }
	$nature_old = str_replace("/", "_", $nature_old);
    $nature_old = str_replace(".", "_", $nature_old);
    $nature_old = str_replace("\\", '_', $nature_old);
    $nature_old = str_replace(":", "_", $nature_old);
	$nature_old = str_replace("'", "_", $nature_old);
	
    if (sanitizePostString('subnature') == null) {       
        $sub_nature = mysqli_real_escape_string($link, sanitizeGetString('subnature'));
    } else {
        $sub_nature = mysqli_real_escape_string($link, sanitizePostString('subnature'));
    }
    $sub_nature = str_replace("/", "_", $sub_nature);
    $sub_nature = str_replace(".", "_", $sub_nature);
    $sub_nature = str_replace("\\", '_', $sub_nature);
    $sub_nature = str_replace(":", "_", $sub_nature);
	$sub_nature = str_replace("'", "_", $sub_nature);
	if(empty($sub_nature)) {
		mysqli_close($link);
		$response['result'] = "Ko";
		$response['detail'] = "The nature of the template has not been specified.";
		echo json_encode($response);
		die();
	}
	
	if (sanitizePostString('subnature_old') == null) {       
        $subnature_old = mysqli_real_escape_string($link, sanitizeGetString('subnature_old'));
    } else {
        $subnature_old = mysqli_real_escape_string($link, sanitizePostString('subnature_old'));
    }
	$subnature_old = str_replace("/", "_", $subnature_old);
    $subnature_old = str_replace(".", "_", $subnature_old);
    $subnature_old = str_replace("\\", '_', $subnature_old);
    $subnature_old = str_replace(":", "_", $subnature_old);
	$subnature_old = str_replace("'", "_", $subnature_old);
	
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
	
    $lastCheck = date("Y-m-d H:i:s");

	if($_FILES['getTemplate']['size'] > 0)
    {
        $filename = $_FILES['getTemplate']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $synopticTplId."_".$name . "." . $ext;
        $filename = preg_replace('/\s+/', '_', $filename);
        $synopticTemplateFile = $filename;
		$synopticTemplateFileURL = "$synTplBaseUrl$synopticTemplateFile";
    }
	else if(!$edit) {
		$response['result'] = "Ko";
		$response['detail'] = "The SVG file has not been sent.";
		echo json_encode($response);
		die();
	}
	else {
		$q4p = "SELECT parameters FROM SynopticTemplates WHERE unique_name_id = '$name'";
		$r4p = mysqli_query($link, $q4p);
		while($pr = mysqli_fetch_assoc($r4p)) {
			$synopticTemplateFileURL = $pr["parameters"];
		}
	}
  
	$insDefaultIcon = false;
    if($_FILES['getIcon']['size'] > 0)
    {
        $filename = $_FILES['getIcon']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $synopticTplId."_".$name . "." . $ext;
        $filename = preg_replace('/\s+/', '_', $filename);
        $microAppExtServIcon = $filename;
    }
    else
    {        
		if($edit) {
			$q4i = "SELECT microAppExtServIcon FROM SynopticTemplates WHERE unique_name_id = '$name'";
			$r4i = mysqli_query($link, $q4i);
			while($ir = mysqli_fetch_assoc($r4i)) {
				$microAppExtServIcon = $ir["microAppExtServIcon"];
			}
		}
		else {
			$microAppExtServIcon = "$synopticTemplateFile";
			$insDefaultIcon = true;
		}
    }
	
	$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
	$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
	$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
	$accessToken = $tkn->access_token;
	$_SESSION['refreshToken'] = $tkn->refresh_token;
	
	$singleReadVariable = "null";
	try {
		$inputs = [];
		$doc = new DOMDocument();	
		if(!$_FILES['getTemplate']['tmp_name']) $doc->load($synopticTemplateFileURL); else $doc->load($_FILES['getTemplate']['tmp_name']);
		$xpath = new DOMXPath($doc);
		$elements = $xpath->query("//*[@data-siow]");
		foreach($elements as $element) {			
			 $datasiows = json_decode($element->getAttribute("data-siow"));
			 foreach($datasiows as $datasiow) {
				if($datasiow->originator == "server") {
					if(!in_array($datasiow->event,$inputs)) {
						$inputs[] = $datasiow->event;
					}
				} 
			 }
		}			 
		if(count($inputs) == 1) $singleReadVariable = "'".$inputs[0]."'";
	}
	catch(Exception $sve) {}

	if($edit) {
		
		/*$q4id = "SELECT id FROM SynopticTemplates WHERE unique_name_id = '$name'";
		$r4id = mysqli_query($link, $q4id);
		while($idr = mysqli_fetch_assoc($r4id)) {
			$synopticTplId = $idr["id"];
		} */
	
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
		if($ownElmtIdUsr[$synopticTplId] != $user && !(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin')) {
			mysqli_close($link);
			$response['url'] = $ownershipApiBaseUrl . "/v1/list/?type=$synTplOwnElmtType&accessToken=$accessToken";
			$response['result'] = "Ko";
			$response['detail'] = "Unauthorized.";
			echo json_encode($response);
			die();
		}
		
		$q = "UPDATE SynopticTemplates SET nature='$nature', sub_nature='$sub_nature', parameters='$synopticTemplateFileURL', microAppExtServIcon='$microAppExtServIcon', lastCheck='$lastCheck', singleReadVariable=$singleReadVariable WHERE unique_name_id = '$name'";
		$r = mysqli_query($link, $q);
		if($r) {				
			$us = mysqli_query($link, "UPDATE DashboardWizard SET nature = '$nature', sub_nature = '$sub_nature' WHERE high_level_type = 'Synoptic' and low_level_type = '$name' and nature = '$nature_old' and sub_nature = '$subnature_old'"); 
			
			if($_FILES['getIcon']['size'] > 0) {
				$uploadFolder = "../img/synopticTemplates/";
				if(!move_uploaded_file($_FILES['getIcon']['tmp_name'], $uploadFolder.$microAppExtServIcon))
				{  
					$queryFail = true;
				}
				else 
				{
				    chmod($uploadFolder.$microAppExtServIcon, 0777); 
				}
			}
			
			if($_FILES['getTemplate']['size'] > 0) {
				$uploadFolder = "../img/synopticTemplates/svg/";		
				if(!move_uploaded_file($_FILES['getTemplate']['tmp_name'], $uploadFolder.$synopticTemplateFile))
				{  
					$queryFail = true;
				}
				else 
				{
				   chmod($uploadFolder.$synopticTemplateFile, 0777); 
				}
				if(! $_FILES['getIcon']['size'] > 0) {					
					$iconUploadFolder = "../img/synopticTemplates/";
					if(!copy($uploadFolder.$synopticTemplateFile, $iconUploadFolder.$microAppExtServIcon))
					{  
						$queryFail = true;
					}
					else 
					{
						chmod($iconUploadFolder.$microAppExtServIcon, 0777); 
					}
				}
			}
			
			$response["id"] = mysqli_insert_id($link);
			$response["url"] = $synopticTemplateFileURL;
			$response["inSync"] = $us;
			$response['result'] = "Ok";
		}
			
	}
	else {
		
		$q = "INSERT INTO SynopticTemplates (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, last_date, last_value, unit, metric, saved_direct, kb_based, sm_based, user, widgets, parameters, healthiness, microAppExtServIcon, lastCheck, ownership, organizations, singleReadVariable) " .
			 "VALUES ('$nature','SynopticTemplate', '$sub_nature', '$name', '$name', NULL, NULL, NULL, NULL, 'SVG', 'no', 'direct', NULL, 'no', NULL, NULL,'$synopticTemplateFileURL', 'true', '$microAppExtServIcon', '$lastCheck', '$ownership', '$organizationArray', $singleReadVariable)";
		$r = mysqli_query($link, $q);
		
		if($r)
		{
			
			$response["id"] = mysqli_insert_id($link);
			$response["url"] = $synopticTemplateFileURL;		
			
			// Template
			
			$uploadFolder = "../img/synopticTemplates/svg/";
			
			if(!file_exists($uploadFolder))
			{
				$oldMask = umask(0);
				mkdir($uploadFolder, 0777);
				umask($oldMask);
			}
			
			if(!move_uploaded_file($_FILES['getTemplate']['tmp_name'], $uploadFolder.$synopticTemplateFile))
			{  
				$queryFail = true;
			}
			else 
			{
			   chmod($uploadFolder.$synopticTemplateFile, 0777); 
			}
			
			// Icona
			
			$uploadFolder = "../img/synopticTemplates/";
			
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
			
			// Use the template itself as the default icon
			
			if($insDefaultIcon) {
				copy("../img/synopticTemplates/svg/$synopticTemplateFile", "../img/synopticTemplates/$synopticTemplateFile");
				if(file_exists("../img/synopticTemplates/$synopticTemplateFile")) chmod("../img/synopticTemplates/$synopticTemplateFile", 0777);
			}
			
			// Ownership
			
			$apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=$accessToken";
			$ownData = array(
				"elementId" => $response["id"],
				"elementType" => $synTplOwnElmtType,
				"elementName" => $name,
				"elementUrl" => $response["url"],
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
			
			mysqli_close($link);
					
			$response['url'] = escapeForHTML($synopticTemplateFileURL);
			$response["inSync"] = true;
			

		}
		else
		{
			$response['result'] = "Ko";
			$response['detail'] = "The template could not be saved to the database.";
		}
	
	}
    
    echo json_encode($response);
	
?>