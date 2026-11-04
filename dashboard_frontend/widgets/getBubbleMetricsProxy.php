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
set_time_limit (90);

session_start();

/*function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    if (strpos($string, $end, $ini)) {
        $len = strpos($string, $end, $ini) - $ini;
    } else {
        return false;
    }
    return substr($string, $ini, $len);
}*/

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $endPosition = strpos($string, $end, $ini);
    if ($endPosition === false) return substr($string, $ini);
    $len = $endPosition - $ini;
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


// A valid empty feature list is distinct from a failed or malformed response.
function bubbleMetricsServiceResponse($body, $headers, $origin) {
    $status = null;
    if (is_array($headers)) {
        foreach ($headers as $header) {
            if (is_string($header) && preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $header, $match)) {
                $status = (int)$match[1];
            }
        }
    }
    if ($status !== 200 || !is_string($body)) {
        http_response_code(502);
        return ['result' => 'Call to SM KO', 'error' => 'upstream_request_failed'];
    }

    $data = json_decode($body);
    try {
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($data)) {
            throw new UnexpectedValueException('invalid_payload');
        }
        foreach (['error', 'Error', 'errors'] as $errorKey) {
            if (property_exists($data, $errorKey)) throw new UnexpectedValueException('invalid_payload');
        }
        if ($origin === 'Sensor') {
            $roots = get_object_vars($data);
            if (!$roots) throw new UnexpectedValueException('invalid_payload');
            $node = reset($roots);
        } else {
            $node = $data;
            foreach (['BusStop', 'Sensor', 'Service', 'Services', 'SensorSites'] as $key) {
                if (property_exists($data, $key)) {
                    $node = $data->$key;
                    break;
                }
            }
        }
        if (!is_object($node) || !isset($node->features) || !is_array($node->features)) {
            throw new UnexpectedValueException('invalid_payload');
        }
        $metrics = [];
        if ($origin === 'Sensor') {
            if ($node->features !== []) {
                $feature = $node->features[0];
                if (!is_object($feature) || !isset($feature->properties) || !is_object($feature->properties)
                    || !isset($feature->properties->realtimeAttributes) || !is_object($feature->properties->realtimeAttributes)) {
                    throw new UnexpectedValueException('invalid_payload');
                }
                $metrics = array_keys(get_object_vars($feature->properties->realtimeAttributes));
            }
        } else {
            foreach ($node->features as $feature) {
                if (!is_object($feature) || !isset($feature->properties) || !is_object($feature->properties)) {
                    throw new UnexpectedValueException('invalid_payload');
                }
                // POIs without realtime values are valid and contribute no metrics.
                if (!isset($feature->properties->values)) continue;
                if (!is_object($feature->properties->values)) {
                    throw new UnexpectedValueException('invalid_payload');
                }
                foreach ($feature->properties->values as $key => $value) {
                    if (!in_array($key, $metrics, true)) $metrics[] = $key;
                }
            }
        }
        return ['result' => 'OK', 'metrics' => $metrics];
    } catch (UnexpectedValueException $e) {
        http_response_code(502);
        return ['result' => 'Call to SM KO', 'error' => 'invalid_upstream_payload'];
    }
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];
$metricType = null;
//$dataOrigin = json_decode($_REQUEST['dataOrigin']);
$dataOrigin = null;
$dataQuery = $_REQUEST['query'];
$index = $_REQUEST['index'];
if (isset($_SESSION['refreshToken'])) {
    try {
        $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
        if (!is_string($accessToken) || $accessToken === '') {
            throw new UnexpectedValueException('invalid_access_token');
        }
    } catch (Throwable $e) {
        session_write_close();
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['result' => 'KO', 'error' => 'authentication_failed']);
        return;
    }
}
session_write_close();

if (strpos($dataQuery, 'selection=') !== false && (strpos($dataQuery, 'categories=') !== false || strpos($dataQuery, 'model=') !== false)) {
    $dataOrigin = "POI";
} else if (strpos($dataQuery, 'serviceUri=') !== false) {
    $dataOrigin = "Sensor";
}

switch($dataOrigin)
{
    case "Sensor":
        if (strpos($dataQuery, "&") === false) {
            $serviceUri = substr($dataQuery, strpos($dataQuery, "serviceUri=")+11);
        } else {
            $serviceUri = get_string_between($dataQuery, "serviceUri=", "&");
        }

        $urlToCall = $superServiceMapUrlPrefix . "api/v1?serviceUri=" . $serviceUri . "&format=json";
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
            $result = @file_get_contents($urlToCall, false, $context);
            header("Content-Type: application/json");
        }

        $response = bubbleMetricsServiceResponse($result, $http_response_header ?? [], 'Sensor');

        break;

    case "POI":

        $geoBb = get_string_between($dataQuery, "selection=", "&");
        // $urlToCall = $superServiceMapUrlPrefix . "api/v1?selection=" . $geoBb;
        $urlToCall = $superServiceMapUrlPrefix . "api/v1/iot-search/?selection=" . $geoBb;
        if (strpos($dataQuery, 'categories=') !== false) {
            $serviceType = get_string_between($dataQuery, "categories=", "&");
            if ($serviceType === false) {
                $serviceType = explode('&categories=', $dataQuery)[1];
            }
            $urlToCall = $urlToCall . "&categories=" . $serviceType;
        }
        if (strpos($dataQuery, 'model=') !== false) {
            if (strpos($dataQuery, 'iot-search') === false) {
                $model = get_string_between($dataQuery, "model=", "&");
                if ($model === false) {
                    $model = explode('&model=', $dataQuery)[1];
                }
                $urlToCall = $urlToCall . "&model=" . $model;
            } else {
                $urlToCall = $dataQuery;
            }
        } /* else {

            //    $urlToCall = "https://www.disit.org/superservicemap/api/v1/?selection=" . $geoBb . "&categories=" . $serviceType . "&maxResults=1";
            $urlToCall = $superServiceMapUrlPrefix . "api/v1?selection=" . $geoBb . "&categories=" . $serviceType;
            //  $urlToCall = $dataQuery;
        }   */

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
            $result = @file_get_contents($urlToCall, false, $context);
            header("Content-Type: application/json");
        }

        $response = bubbleMetricsServiceResponse($result, $http_response_header ?? [], 'POI');

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