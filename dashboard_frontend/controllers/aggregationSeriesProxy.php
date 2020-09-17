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

    function getMyKPIUpperTimeLimit($hoursOffset, $upperDateTime) {

        // Current date and time
        if (isset($upperDateTime)) {
            $datetime = $upperDateTime;
        } else {
            $datetime = date("Y-m-d H:i:s");
        }

        // Convert datetime to Unix timestamp
        $timestamp = strtotime($datetime);

        // Subtract time from datetime
        $time = $timestamp - ($hoursOffset * 60 * 60);

        // Date and time after subtraction
        $datetime = date("Y-m-d H:i:s", $time);

        return $datetime;
    }

    function getDynamicUpperTimeLimit($hoursOffset, $upperDateTime) {

        // Current date and time
        if (isset($upperDateTime)) {
            $datetime = $upperDateTime;
        } else {
            $datetime = date("Y-m-d H:i:s");
        }

        // Convert datetime to Unix timestamp
        $timestamp = strtotime($datetime);

        // Subtract time from datetime
        $time = ($timestamp - ($hoursOffset * 60 * 60 )) * 1000;

        // Date and time after subtraction
      //  $datetime = date("Y-m-d H:i:s", $time);

        return $time;
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
    $dataOrigin = json_decode($_REQUEST['dataOrigin']);
    $index = $_REQUEST['index'];
    if (strcmp($dataOrigin->metricHighLevelType, "Sensor" == 0) || strcmp($dataOrigin->metricHighLevelType, "MyKPI" == 0)) {
        if(isset($_SESSION['refreshToken'])) {
            $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
        }
    }
    session_write_close();
    
    switch($dataOrigin->metricHighLevelType)
    {
        case "KPI":
            $metricName = $dataOrigin->metricId;
            $q1 = "SELECT * FROM Dashboard.Descriptions WHERE IdMetric = '$metricName'";
            $r1 = mysqli_query($link, $q1);

            if($r1)
            {
                if(mysqli_num_rows($r1) > 0)
                {
                    $tempRow = mysqli_fetch_assoc($r1);
                    $metricType = $tempRow['metricType'];
                    $metricShortDesc = $tempRow['description_short'];
                    
                    if(isset($_REQUEST['timeRange']))
                    {
                        if($_REQUEST['timeRange'] != 'last')
                        {
                            switch($_REQUEST['timeRange'])
                            {
                                case "4 Ore":
                                    $timeRange = "4 HOUR";
                                    break;
                                
                                case "12 Ore":
                                    $timeRange = "12 HOUR";
                                    break;
                                
                                case "Giornaliera":
                                    $timeRange = "1 DAY";
                                    break;
                                
                                case "Settimanale":
                                    $timeRange = "7 DAY";
                                    break;
                                
                                case "Mensile":
                                    $timeRange = "30 DAY";
                                    break;

                                case "Semestrale":
                                    $timeRange = "180 DAY";
                                    break;
                                
                                case "Annuale":
                                    $timeRange = "365 DAY";
                                    break;
                            }
                            if(isset($_REQUEST['upperTime'])) {
                                $upperTime = mysqli_real_escape_string($link, $_REQUEST['upperTime']);
                                $q3 = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data LEFT JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' AND Data.computationDate >= DATE_SUB('" . $upperTime . "', INTERVAL " . $timeRange . ") AND Data.computationDate <= '" . $upperTime . "' ORDER BY computationDate ASC";
                            } else {
                                $q3 = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data LEFT JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' AND Data.computationDate >= DATE_SUB(now(), INTERVAL " . $timeRange . ") ORDER BY computationDate ASC";
                            }
                        }
                        else
                        {
                            $q3 = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
                        }
                    }
                    else
                    {
                        $q3 = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
                    }
                }
                else
                {
                    $q2 = "SELECT * FROM Dashboard.NodeRedMetrics WHERE name = '$metricName'";
                    $r2 = mysqli_query($link, $q2);

                    if($r2)
                    {
                        $tempRow = mysqli_fetch_assoc($r2);
                        $metricType = $tempRow['metricType'];
                        $metricShortDesc = $tempRow['shortDesc'];
                        if(mysqli_num_rows($r2) > 0)
                        {
                            $q3 = "SELECT Data.*, NodeRedMetrics.shortDesc as descrip, NodeRedMetrics.metricType, '', '', '', 1 from Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
                        }
                    }
                    else
                    {
                        $response['result'] = "q2Ko";
                    }
                }
            }
            else
            {
                $response['result'] = "q1Ko";
            }

            $r3 = mysqli_query($link, $q3);
            if($r3)
            {
                $response['result'] = "Ok";
                $response['metricType'] = $metricType;
                $response['metricName'] = $metricName;
                $response['metricShortDesc'] = $metricShortDesc;
                $response['metricHighLevelType'] = $dataOrigin->metricHighLevelType;
                if(isset($_REQUEST['timeRange']))
                {
                    if($_REQUEST['timeRange'] != 'last')
                    {
                        $response['data'] = [];
                        while($row = mysqli_fetch_assoc($r3))
                        {
                            array_push($response['data'], $row);
                        }
                    }
                    else
                    {
                        $response['data'] = json_encode(mysqli_fetch_assoc($r3));
                    }
                }
                else
                {
                    $response['data'] = json_encode(mysqli_fetch_assoc($r3));
                }
                
                $response['index'] = $index;
            }
            else
            {
                $response['result'] = "q3Ko";
            }
            break;
        
        
        case "Sensor":

        /*    if(isset($_SESSION['refreshToken'])) {
                $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
            }   */

            $smUrl = $kbUrlSuperServiceMap . "?serviceUri=" . $dataOrigin->serviceUri . "&format=json";

            $metricType = "Float";
            
            if(isset($_REQUEST['timeRange']))
            {
                if (!empty($_REQUEST['timeRange'])) {
                    if ($_REQUEST['timeRange'] != 'last') {
                        switch ($_REQUEST['timeRange']) {
                            case "4 Ore":
                                $timeRange = "fromTime=4-hour";
                                break;

                            case "12 Ore":
                                $timeRange = "fromTime=12-hour";
                                break;

                            case "Giornaliera":
                                $timeRange = "fromTime=1-day";
                                break;

                            case "Settimanale":
                                $timeRange = "fromTime=7-day";
                                break;

                            case "Mensile":
                                $timeRange = "fromTime=30-day";
                                break;

                            case "Semestrale":
                                $timeRange = "fromTime=180-day";
                                break;

                            case "Annuale":
                                $timeRange = "fromTime=365-day";
                                break;
                        }

                        $urlToCall = $smUrl . "&" . $timeRange;
                    } else {
                        $urlToCall = $smUrl;
                    }
                } else {
                    $urlToCall = $smUrl;
                }
            }
            else
            {
                $urlToCall = $smUrl;
            }

            if(isset($_REQUEST['upperTime'])) {
                $urlToCall = $urlToCall . "&toTime=" . $_REQUEST['upperTime'];
            }

            if (isset($dataOrigin->smField)) {
              //  if(strpos($dataOrigin->smField, 'Forecast') !== false) {
                    $urlToCall = $urlToCall . "&valueName=" . $dataOrigin->smField;
              //  }
            }

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
                $response['result'] = 'Ok';
                $response['data'] = $result;
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
                $response['index'] = $index;
            }
            else
            {
                $response['result'] = "Call to SM KO";
                $response['metricHighLevelType'] = $dataOrigin->metricHighLevelType;
            }
            
            break;

        case "Dynamic":
            $response['result'] = 'Ok';
            $response['data'] = [];
          //  $response['data'] = $dataOrigin->values;
            if(isset($_REQUEST['upperTime'])) {
                $upperTime = $_REQUEST['upperTime'];
            } else {
                $upperTime = null;
            }
            switch($_REQUEST['timeRange'])
            {
                case "4 Ore":
                    $t0 = getDynamicUpperTimeLimit(4, $upperTime);
                    break;

                case "12 Ore":
                    $t0 = getDynamicUpperTimeLimit(12, $upperTime);
                    break;

                case "Giornaliera":
                    $t0 = getDynamicUpperTimeLimit(24, $upperTime);
                    break;

                case "Settimanale":
                    $t0 = getDynamicUpperTimeLimit(168, $upperTime);
                    break;

                case "Mensile":
                    $t0 = getDynamicUpperTimeLimit(720, $upperTime);
                    break;

                case "Semestrale":
                    $t0 = getDynamicUpperTimeLimit(4320, $upperTime);
                    break;

                case "Annuale":
                    $t0 = getMyKPIUpperTimeLimit(8760, $upperTime);
                    break;
            }
            $t1 = strtotime($upperTime) * 1000;

            for ($n = 0; $n < sizeof($dataOrigin->values); $n++) {
                if ($dataOrigin->values[$n][0] >= $t0 && $dataOrigin->values[$n][0] <= $t1) {
                    array_push($response['data'], $dataOrigin->values[$n]);
                }
            }

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
            $response['index'] = $index;
            break;

        case "MyKPI":

            if(isset($_REQUEST['timeRange']))
            {
                if($_REQUEST['timeRange'] != 'last')
                {
                    if(isset($_REQUEST['upperTime'])) {
                        $upperTime = $_REQUEST['upperTime'];
                    } else {
                        $upperTime = null;
                    }
                    switch($_REQUEST['timeRange'])
                    {
                        case "4 Ore":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(4, $upperTime);
                            break;

                        case "12 Ore":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(12, $upperTime);
                            break;

                        case "Giornaliera":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(24, $upperTime);
                            break;

                        case "Settimanale":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(168, $upperTime);
                            break;

                        case "Mensile":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(720, $upperTime);
                            break;

                        case "Semestrale":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(4320, $upperTime);
                            break;

                        case "Annuale":
                            $timeRange = "from=" . getMyKPIUpperTimeLimit(8760, $upperTime);
                            break;
                    }
                    if(isset($_REQUEST['upperTime'])) {
                        $timeRange = $timeRange . "&to=" . $upperTime;
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