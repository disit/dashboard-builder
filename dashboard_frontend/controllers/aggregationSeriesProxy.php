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
                                
                                case "Annuale":
                                    $timeRange = "365 DAY";
                                    break;
                            }
                            $q3 = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data LEFT JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' AND Data.computationDate >= DATE_SUB(now(), INTERVAL " . $timeRange . ") ORDER BY computationDate ASC"; 
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
         /*   $smUrl = "http://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=" . $dataOrigin->serviceUri . "&format=json";
            if (isset($_SESSION['orgKbUrl'])) {
                $smUrl = $orgKbUrl . $dataOrigin->serviceUri . "&format=json";
            }*/

            if(isset($_SESSION['refreshToken'])) {
                $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
            }

            $smUrl = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" . $dataOrigin->serviceUri . "&format=json";

            $metricType = "Float";
            
            if(isset($_REQUEST['timeRange']))
            {
                if($_REQUEST['timeRange'] != 'last')
                {
                    switch($_REQUEST['timeRange'])
                    {
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

                        case "Annuale":
                            $timeRange = "fromTime=365-day";
                            break;
                    }
                    
                    $urlToCall = $smUrl . "&" . $timeRange;
                }
                else
                {
                    $urlToCall = $smUrl;
                }
            }
            else
            {
                $urlToCall = $smUrl;
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
                $response['smField'] = $dataOrigin->smField;
                $response['metricName'] = $dataOrigin->metricName . " - " . $dataOrigin->smField;
                $response['index'] = $index;
            }
            else
            {
                $response['result'] = "Call to SM KO";
                $response['metricHighLevelType'] = $dataOrigin->metricHighLevelType;
            }
            
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

            if(isset($_SESSION['refreshToken'])) {
                $accessToken = getAccessToken($ssoEndpoint, $ssoClientId, $ssoClientSecret, $ssoTokenEndpoint);
            }

            $myKPIId = $dataOrigin->metricId;

            $genFileContent = parse_ini_file("../conf/environment.ini");
            $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
            $env = $genFileContent['environment']['value'];

            $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

            $myKpiDataArray = [];
            $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKPIId . "/values?sourceRequest=dashboardmanager&accessToken=" . $accessToken . $timeRange;

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

            $myKPIDataOwnedArray = json_decode($myKPIDataJson);

            $myKPIDataArray = [];

            $myKPIDataJson = json_encode($myKPIDataOwnedArray);

            break;

        //Poi si aggiungeranno le altre sorgenti dati
        default:
            $response['result'] = "NotImplemented";
            break;
    }

    echo json_encode($response);