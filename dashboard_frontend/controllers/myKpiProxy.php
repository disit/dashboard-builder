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

    if (isset($_GET['myKpiId'])) {
        $myKpiId = $_GET['myKpiId'];
        if (checkVarType($myKpiId, "integer") === false) {
            eventLog("Returned the following ERROR in myKpiProxy.php for myKpiId = ".$myKpiId.": ".$myKpiId." is not an integer as expected. Exit from script.");
            exit();
        };
    } else {
        $myKpiId = "";
    }

    if (isset($_GET['timeRange'])) {
        $myKpiTimeRange = $_GET['timeRange'];
    } else {
        $myKpiTimeRange = "";
    }

    if (isset($_REQUEST['last'])) {
        $lastValueString = "&last=" . $_REQUEST['last'];
    } else if (isset($_GET['lastValue'])) {
        $lastValueString = "&last=" . $_GET['lastValue'];
    } else {
        $lastValueString = "&last=0";
    }

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = "";
    }

if(isset($_SESSION['refreshToken'])) {
    //  if(isset($_SESSION['refreshToken'])) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;

    $genFileContent = parse_ini_file("../conf/environment.ini");
    $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
    $env = $genFileContent['environment']['value'];

    $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

    $myKpiDataArray = [];
    if ($action == "getDistinctDays") {
        $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/values/dates?sourceRequest=dashboardmanager&accessToken=" . $accessToken;
    } else {
    //    $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/values?sourceRequest=dashboardmanager&accessToken=" . $accessToken . urlencode($myKpiTimeRange) . urlencode($lastValueString);
        $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/values?sourceRequest=dashboardmanager&accessToken=" . $accessToken . $myKpiTimeRange . $lastValueString;
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
    $myKpiDataJson = file_get_contents($apiUrl, false, $context);

    $myKpiData = json_decode($myKpiDataJson);

    echo $myKpiDataJson;

} else {

    $genFileContent = parse_ini_file("../conf/environment.ini");
    $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
    $env = $genFileContent['environment']['value'];

    $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

    $myKpiDataArray = [];
    if ($action == "getDistinctDays") {
        $apiUrl = $personalDataApiBaseUrl . "/v1/public/kpidata/" . $myKpiId . "/values/dates?sourceRequest=dashboardmanager";
    } else {
        //    $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/values?sourceRequest=dashboardmanager&accessToken=" . $accessToken . urlencode($myKpiTimeRange) . urlencode($lastValueString);
        $apiUrl = $personalDataApiBaseUrl . "/v1/public/kpidata/" . $myKpiId . "/values?sourceRequest=dashboardmanager" . $myKpiTimeRange . $lastValueString;
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
    $myKpiDataJson = file_get_contents($apiUrl, false, $context);

    $myKpiData = json_decode($myKpiDataJson);

    echo $myKpiDataJson;


}