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

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

function udate($format = 'u', $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

//if(isset($_SESSION['loggedUsername']))
//{

if (isset($_GET['myPOIId'])) {
    $myPOIId = $_GET['myPOIId'];
} else {
    $myPOIId = "";
}

if (isset($_GET['timeRange'])) {
    $myPOITimeRange = $_GET['timeRange'];
} else {
    $myPOITimeRange = "";
}

if (isset($_REQUEST['last'])) {
    $lastValueString = "&last=" . $_REQUEST['last'];
} else {
    $lastValueString = "&last=0";
}


//  if(isset($_SESSION['refreshToken'])) {
$oidc = new OpenIDConnectClient('https://www.snap4city.org', 'php-dashboard-builder', '0afa15e8-87b9-4830-a60c-5fd4da78a9c4');
$oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
$accessToken = $tkn->access_token;
$_SESSION['refreshToken'] = $tkn->refresh_token;

/*   $genFileContent = parse_ini_file("../conf/environment.ini");
   $personalDataFileContent = parse_ini_file("../conf/personalData.ini");
   $env = $genFileContent['environment']['value'];

   $host_pd= $personalDataFileContent["host_PD"][$env];
   $token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
   $client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
   $username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
   $password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL,$token_endpoint);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,
       "username=".$username."&password=".$password."&grant_type=password&client_id=".$client_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

   $curl_response = curl_exec($ch);
   curl_close($ch);
   $access_token_output = json_decode($curl_response)->access_token;   */

//    echo json_encode($accessToken);

$myPOIDataJsonDelegated = [];

$genFileContent = parse_ini_file("../conf/environment.ini");
$ownershipFileContent = parse_ini_file("../conf/ownership.ini");
$env = $genFileContent['environment']['value'];

$personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

$myKpiDataArray = [];
if ($myPOIId != "All") {
    $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/" . $myPOIId . "/?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . $myPOITimeRange . $lastValueString;
} else {
    $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyPOI" . $myPOITimeRange . $lastValueString;
    $apiUrlDelegated = $personalDataApiBaseUrl . "/v1/poidata/delegated?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . $myPOITimeRange . $lastValueString;
    $optionsDelegated = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    $contextDelegated = stream_context_create($optionsDelegated);
    $myPOIDataJsonDelegated = file_get_contents($apiUrlDelegated, false, $contextDelegated);
}

$options = array(
    'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'GET',
        'timeout' => 30,
        'ignore_errors' => true
    )
);

$context = stream_context_create($options);
$myPOIDataJsonOwned = file_get_contents($apiUrl, false, $context);

$myPOIDataOwnedArray = json_decode($myPOIDataJsonOwned);

/*  for($i = 0; $i < count($myKpiData); $i++) {
    //  if ($myKpiData[$i]->elementType == 'DashboardID') {
          //   if(!in_array($delegatedDashboards[$i]->elementId, $dashIds, true)) {
          array_push($myKpiDataArray, $myKpiData[$i]->elementId);
    //  }
  }   */
$myPOIDataArray = [];

if ($myPOIDataJsonDelegated) {
    $myPOIDataJsonArray = json_decode($myPOIDataJsonDelegated);
    foreach ($myPOIDataJsonArray as $myPOIDataJsonElement) {
        array_push($myPOIDataArray, $myPOIDataJsonElement);
    }
    foreach ($myPOIDataOwnedArray as $myPOIDataJsonOwnedElement) {
        array_push($myPOIDataArray, $myPOIDataJsonOwnedElement);
    }
    $myPOIDataJson = json_encode($myPOIDataArray);
} else {
    $myPOIDataJson = json_encode($myPOIDataOwnedArray);
}

echo $myPOIDataJson;
//   }

//}