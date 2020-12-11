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
if (!isset($_SESSION)) {
    session_start();
    session_write_close();
}

function udate($format = 'u', $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

//if(isset($_SESSION['loggedUsername']))
//{

if (isset($_GET['myPOIId'])) {
    $myPOIId = $_GET['myPOIId'];
    if (checkVarType($myPOIId, "integer") === false) {
        if ($myPOIId !== "All") {
            eventLog("Returned the following ERROR in myPOIProxy.php for myPOIId = " . $myPOIId . ": " . $myPOIId . " is not an integer as expected. Exit from script.");
            exit();
        }
    };
} else {
    $myPOIId = "";
}

if (isset($_GET['timeRange'])) {
    $myPOITimeRange = $_GET['timeRange'];
} else {
    $myPOITimeRange = "";
}

if (isset($_REQUEST['last'])) {
    if ($_REQUEST['last'] == "1") {
        $lastValueString = "&last=" . $_REQUEST['last'];
    }
} else {
 //   $lastValueString = "&last=0";
}

if(isset($_SESSION['refreshToken'])) {
//  if(isset($_SESSION['refreshToken'])) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;

    $myPOIDataJsonDelegated = [];

    $genFileContent = parse_ini_file("../conf/environment.ini");
    $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
    $env = $genFileContent['environment']['value'];

    $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

    $myKpiDataArray = [];
    if ($myPOIId != "All") {
      //  $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/" . $myPOIId . "/?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . urlencode($myPOITimeRange) . urlencode($lastValueString);
        $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/" . $myPOIId . "/?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . $myPOITimeRange . $lastValueString;
    } else {
    //    $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyPOI" . urlencode($myPOITimeRange) . urlencode($lastValueString);
        $apiUrl = $personalDataApiBaseUrl . "/v1/poidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyPOI" . $myPOITimeRange . $lastValueString;
   //     $apiUrlDelegated = $personalDataApiBaseUrl . "/v1/poidata/delegated?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . urlencode($myPOITimeRange) . urlencode($lastValueString);
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

}