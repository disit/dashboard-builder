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
set_time_limit (90);

session_start();

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getMyKPIUpperTimeLimit($hoursOffset) {

    // Current date and time
    $datetime = date("Y-m-d H:i:s");

    // Convert datetime to Unix timestamp
    $timestamp = strtotime($datetime);

    // Subtract time from datetime
    $time = $timestamp - ($hoursOffset * 60 * 60);

    // Date and time after subtraction
    $datetime = date("Y-m-d H:i:s", $time);

    return $datetime;
}

function getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;
    return $accessToken;
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];
$metricType = null;
//$dataOrigin = json_decode($_REQUEST['dataOrigin']);
$dataOrigin = null;
$dataQuery = $_REQUEST['query'];
$index = $_REQUEST['index'];
if (strcmp($dataOrigin->metricHighLevelType, "Sensor" == 0) || strcmp($dataOrigin->metricHighLevelType, "MyKPI" == 0)) {
    if(isset($_SESSION['refreshToken'])) {
        $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
    }
}
session_write_close();

if (strpos($dataQuery, 'selection=') !== false && strpos($dataQuery, 'categories=') !== false) {
    $dataOrigin = "POI";
} else if (strpos($dataQuery, 'serviceUri=') !== false) {
    $dataOrigin = "Sensor";
}

switch($dataOrigin)
{
    case "Sensor":
        $serviceUri = get_string_between($dataQuery, "serviceUri=", "&");

        $urlToCall = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" . $serviceUri . "&format=json";
      //  $urlToCall = $dataQuery;

        if(isset($urlToCall)) {
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'timeout' => 30,
                    'ignore_errors' => true
                )
            );
            if(isset($accessToken)) {
                $options['http']['header'] = "Authorization: Bearer $accessToken\r\n";
            }

            $context = stream_context_create($options);
            $result = file_get_contents($urlToCall, false, $context);
            header("Content-Type: application/json");
        }

        //    $smPayload = file_get_contents($urlToCall);
        if(strpos($http_response_header[0], '200') !== false)
            //    if ($result)
        {
            $data = json_decode($result);

            $response['result'] = "OK";
          //  $response['metrics'] = $data;
            $response['metrics'] = array();
            $deviceKey = array_keys(get_object_vars($data))[0];
            $realtimeAttributes = array_keys(get_object_vars($data->$deviceKey->features[0]->properties->realtimeAttributes));

            if (!empty($realtimeAttributes)) {
                for ($arrayCount = 0; $arrayCount < sizeof($realtimeAttributes); $arrayCount++) {
                    if (!in_array($realtimeAttributes[$arrayCount], $response['metrics'])) {
                        array_push($response['metrics'], $realtimeAttributes[$arrayCount]);
                        //  $response['result'] = "OK";
                        //  $response['metrics'] = $realtimeAttributes;
                        //  break;
                    }
                }
            }

        }
        else
        {
            $response['result'] = "Call to SM KO";
        }

        break;

    case "POI":

        $geoBb = get_string_between($dataQuery, "selection=", "&categories=");
        $serviceType = get_string_between($dataQuery, "categories=", "&");

    //    $urlToCall = "https://www.disit.org/superservicemap/api/v1/?selection=" . $geoBb . "&categories=" . $serviceType . "&maxResults=1";
        $urlToCall = "https://www.disit.org/superservicemap/api/v1/?selection=" . $geoBb . "&categories=" . $serviceType;
        //  $urlToCall = $dataQuery;

        if(isset($urlToCall)) {
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'timeout' => 30,
                    'ignore_errors' => true
                )
            );
            if(isset($accessToken)) {
                $options['http']['header'] = "Authorization: Bearer $accessToken\r\n";
            }

            $context = stream_context_create($options);
            $result = file_get_contents($urlToCall, false, $context);
            header("Content-Type: application/json");
        }

        //    $smPayload = file_get_contents($urlToCall);
        if(strpos($http_response_header[0], '200') !== false)
            //    if ($result)
        {
            $geoJsonData = json_decode($result);
            $key = array_keys(get_object_vars($geoJsonData))[0];
            $fatherGeoJsonNode = $geoJsonData->$key;
        /*    if ($geoJsonData->BusStop) {
                $fatherGeoJsonNode = $geoJsonData->BusStop;
            } else if ($geoJsonData->Sensor) {
                $fatherGeoJsonNode = $geoJsonData->Sensor;
            } else if ($geoJsonData->Service) {
                $fatherGeoJsonNode = $geoJsonData->Service;
            } else if ($geoJsonData->Services) {
                $fatherGeoJsonNode = $geoJsonData->Services;
            }*/

            $response['metrics'] = array();
            for ($count = 0; $count < sizeof($fatherGeoJsonNode->features); $count++) {
                $singleServieUri = $fatherGeoJsonNode->features[$count]->properties->serviceUri;

                $urlToCallSingleDevice = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" . rawurlencode($singleServieUri) . "&format=json";
                //  $urlToCall = $dataQuery;

                if (isset($urlToCallSingleDevice)) {
                    $options = array(
                        'http' => array(
                            'method' => 'GET',
                            'timeout' => 30,
                            'ignore_errors' => true
                        )
                    );
                    if (isset($accessToken)) {
                        $options['http']['header'] = "Authorization: Bearer $accessToken\r\n";
                    }

                    $context = stream_context_create($options);
                    $resultSingleDevice = file_get_contents($urlToCallSingleDevice, false, $context);
                    header("Content-Type: application/json");
                }

                //    $smPayload = file_get_contents($urlToCall);
                if (strpos($http_response_header[0], '200') !== false) //    if ($result)
                {
                    $singelDeviceData = json_decode($resultSingleDevice);
                    $deviceKey = array_keys(get_object_vars($singelDeviceData))[0];
                    $realtimeAttributes = array_keys(get_object_vars($singelDeviceData->$deviceKey->features[0]->properties->realtimeAttributes));

                    if (!empty($realtimeAttributes)) {
                        for ($arrayCount = 0; $arrayCount < sizeof($realtimeAttributes); $arrayCount++) {
                            if (!in_array($realtimeAttributes[$arrayCount], $response['metrics'])) {
                                array_push($response['metrics'], $realtimeAttributes[$arrayCount]);
                                //  $response['result'] = "OK";
                                //  $response['metrics'] = $realtimeAttributes;
                                //  break;
                            }
                        }
                    }

                } else {
                    $response['result'] = "Call to SM KO";
                }
            }
        }
        else
        {
            $response['result'] = "Call to SM KO";
        }

        $response['result'] = "OK";
        break;

    case "MyKPI":

        if(isset($_REQUEST['timeRange']))
        {
            if($_REQUEST['timeRange'] != 'last')
            {
                switch($_REQUEST['timeRange'])
                {
                    case "4 Ore":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(4);
                        break;

                    case "12 Ore":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(12);
                        break;

                    case "Giornaliera":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(24);
                        break;

                    case "Settimanale":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(168);
                        break;

                    case "Mensile":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(720);
                        break;

                    case "Annuale":
                        $timeRange = "from=" . getMyKPIUpperTimeLimit(8760);
                        break;
                }

            }
            else
            {
                $timeRange = "last=1";
            }
        }

        $timeRange = str_replace(" ","T", $timeRange);
        /*    if(isset($_SESSION['refreshToken'])) {
                $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
            }*/

        //    $myKPIId = $dataOrigin->metricId;
        $myKPIId = $dataOrigin->serviceUri;
        IF (strpos($myKPIId, 'datamanager/api/v1/poidata/') !== false) {
            $myKPIId = explode("datamanager/api/v1/poidata/", $myKPIId)[1];
        }

        $genFileContent = parse_ini_file("../conf/environment.ini");
        $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
        $env = $genFileContent['environment']['value'];

        $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

        $myKpiDataArray = [];
        if(isset($_SESSION['refreshToken'])) {
            $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKPIId . "/values?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&" . $timeRange;
        } else {
            $apiUrl = $personalDataApiBaseUrl . "/v1/public/kpidata/" . $myKPIId . "/values?sourceRequest=dashboardmanager" . $timeRange;
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
        $myKPIDataJson = file_get_contents($apiUrl, false, $context);

        if(isset($_SESSION['refreshToken'])) {
            $apiUrlUnit = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKPIId . "/?sourceRequest=dashboardmanager&accessToken=" . $accessToken;
        } else {
            $apiUrlUnit = $personalDataApiBaseUrl . "/v1/public/kpidata/" . $myKPIId . "/?sourceRequest=dashboardmanager&accessToken=";
        }
        $optionsUnit = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'GET',
                'timeout' => 30,
                'ignore_errors' => true
            )
        );

        $contextUnit = stream_context_create($optionsUnit);
        $myKpiUnitJson = file_get_contents($apiUrlUnit, false, $contextUnit);

        $myKpiUnit = json_decode($myKpiUnitJson);

        if(strpos($http_response_header[0], '200') !== false)
            //    if ($result)
        {
            $data = json_decode($myKPIDataJson);
            $response['result'] = 'Ok';
            $response['data'] = $myKPIDataJson;
            $response['metricHighLevelType'] = $dataOrigin->metricHighLevelType;
            if (!isset($dataOrigin->smField)) {
                if (isset($dataOrigin->metricType)) {
                    $response['metricType'] = $dataOrigin->metricType;
                    if ($dataOrigin->label != null) {
                        $response['label'] = $dataOrigin->label;
                    } else {
                        $response['label'] = $dataOrigin->metricName . " - " . $dataOrigin->metricType;
                    }
                }
            } else {
                $response['smField'] = $dataOrigin->smField;
                if ($dataOrigin->label != null) {
                    $response['label'] = $dataOrigin->label;
                } else {
                    $response['label'] = $dataOrigin->metricName . " - " . $dataOrigin->smField;
                }
            }
            $response['metricName'] = $dataOrigin->metricName;
            $response['metricValueUnit'] = $myKpiUnit->valueUnit;
            $response['index'] = $index;
        }
        else
        {
            $response['result'] = "Call to MyKPI API KO";
            $response['metricHighLevelType'] = $dataOrigin->metricHighLevelType;
        }

        break;

    //Poi si aggiungeranno le altre sorgenti dati
    default:
        $response['result'] = "NotImplemented";
        break;
}

echo json_encode($response);