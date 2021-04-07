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
    checkSession('AreaManager','../management/ssoLogin.php');
	
	$link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
	
	if (sanitizePostString('id') == null) {       
        $synopticId = mysqli_real_escape_string($link, sanitizeGetString('id'));
    } else {
        $synopticId = mysqli_real_escape_string($link, sanitizePostString('id'));
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
	$authorized = false;
	foreach($ownership as $ownElmt) {
		if($synopticId == $ownElmt["elementId"]) {
			$authorized = true;
		}
    }
	
	if(!$authorized) {
		echo(json_encode(array("detail" => "Ko")));
		die();
	}

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
	for($i = 0; $i < count($privateMyKPI); $i++) $privateMyKPI[$i]->isMyOwn = true;
	
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
	for($i = 0; $i < count($delegatedMyKPI); $i++) $delegatedMyKPI[$i]->isDelegated = true;
	
	$myKPIs = array_merge($privateMyKPI, $delegatedMyKPI);
	
	$response = [];
	$response["variables"] = [];
	$mq = "SELECT tpl_var_name, usr_var_name, GROUP_CONCAT(tpl_var_role SEPARATOR '/') tpl_var_role FROM SynopticMappings WHERE synoptic_id = $synopticId GROUP BY tpl_var_name, usr_var_name";
	$m = mysqli_query($link, $mq);
	if($m) {
		while($rowm = mysqli_fetch_assoc($m)) {
			$thename = "";
			$synVarOwn = "public";
			foreach($myKPIs as $myKPI) {
				if($myKPI->id == $rowm["usr_var_name"]) {					
					$thename = $myKPI->valueName;
					$synVarOwn = ucfirst($myKPI->ownership);
				}
			}
			$synVarSrc = "MyKPI";
			if(!is_numeric($rowm["usr_var_name"])) $synVarSrc = "Sensor";
			if($rowm["usr_var_name"] == $rowm["tpl_var_name"]) $synVarSrc = "IOTApp";
			$synVarName = "--";
			if($synVarSrc == "Sensor") $synVarName = substr(explode(" ",$rowm["usr_var_name"])[0],1+strrpos(explode(" ",$rowm["usr_var_name"])[0],"/"))." ".explode(" ",$rowm["usr_var_name"])[1];
			if($synVarSrc == "MyKPI") $synVarName = $thename; if($_SESSION["loggedRole"] == "RootAdmin" && is_numeric($rowm["usr_var_name"])) $synVarName.= " (".$rowm["usr_var_name"].")";
			if($synVarSrc == "IOTApp") $synVarOwn = "--";
			$response["variables"][] = array(
				"tplVarName" => $rowm["tpl_var_name"],
				"tplVarAction" => str_replace(array("input","output"),array("R","W"),$rowm["tpl_var_role"]),
				"synVarSrc" => $synVarSrc,
				"synVarName" => $synVarName,
				"synVarOwn" => ucfirst($synVarOwn)
			);
		}
	}
	$response["detail"] = "Ok";
	
	echo(json_encode($response,JSON_PRETTY_PRINT));
	
	mysqli_close($link);