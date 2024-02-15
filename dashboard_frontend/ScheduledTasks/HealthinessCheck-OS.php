<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence
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
include '../opensearch/OpenSearchS4C.php';
$open_search = new OpenSearchS4C();
$open_search->initDashboardWizard();
error_reporting(E_ERROR);

$high_level_type = "";
$nature = "";
$sub_nature = "";
$low_level_type = "";
$unique_name_id = "";
$instance_uri = "";
$unit = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$parameters = "";

//$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$current_dateTimeZone = new DateTimeZone('Europe/Rome');
$startTime = new DateTime("now", $current_dateTimeZone);
$offset = $current_dateTimeZone->getOffset($startTime);
//$startTime = new DateTime(null, new DateTimeZone('GMT'));
$start_scritp_time = $startTime->format('c');
//$start_scritp_time_string = explode("+", $start_scritp_time);
//$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
$start_time_ok = str_replace("T", " ", $start_scritp_time);
echo("Starting HealthinessCheck SCRIPT at: ".$start_time_ok."\n");

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

/*if (defined('STDIN')) {
    if ($argv[1]) {
        $id_arg = $argv[1];
        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE oldEntry IS NULL AND (DashboardWizard.high_level_type = 'Sensor' OR DashboardWizard.high_level_type = 'IoT Device' OR DashboardWizard.high_level_type = 'IoT Device Variable' OR DashboardWizard.high_level_type = 'Mobile Device' OR DashboardWizard.high_level_type = 'Mobile Device Variable' OR DashboardWizard.high_level_type = 'Data Table Device' OR DashboardWizard.high_level_type = 'Data Table Variable' OR DashboardWizard.high_level_type = 'Sensor-Actuator' OR (DashboardWizard.high_level_type = 'Special Widget' AND sub_nature = 'First Aid Data')) AND id > ".$id_arg ." GROUP BY unique_name_id ORDER BY id DESC;";
    } else {
      //  $query = "SELECT * FROM Dashboard.DashboardWizard WHERE (DashboardWizard.high_level_type = 'Sensor' OR DashboardWizard.high_level_type = 'Sensor-Actuator' OR (DashboardWizard.high_level_type = 'Special Widget' AND sub_nature = 'First Aid Data')) GROUP BY unique_name_id ORDER BY id DESC;";
        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE oldEntry IS NULL AND (DashboardWizard.high_level_type = 'Sensor' OR DashboardWizard.high_level_type = 'IoT Device' OR DashboardWizard.high_level_type = 'IoT Device Variable' OR DashboardWizard.high_level_type = 'Mobile Device' OR DashboardWizard.high_level_type = 'Mobile Device Variable' OR DashboardWizard.high_level_type = 'Data Table Device' OR DashboardWizard.high_level_type = 'Data Table Variable' OR DashboardWizard.high_level_type = 'Sensor-Actuator' OR (DashboardWizard.high_level_type = 'Special Widget' AND sub_nature = 'First Aid Data')) GROUP BY unique_name_id ORDER BY id DESC;";
    }
} else {
    $query = "SELECT * FROM Dashboard.DashboardWizard WHERE oldEntry IS NULL AND (DashboardWizard.high_level_type = 'Sensor' OR DashboardWizard.high_level_type = 'IoT Device' OR DashboardWizard.high_level_type = 'IoT Device Variable' OR DashboardWizard.high_level_type = 'Mobile Device' OR DashboardWizard.high_level_type = 'Mobile Device Variable' OR DashboardWizard.high_level_type = 'Data Table Device' OR DashboardWizard.high_level_type = 'Data Table Variable' OR DashboardWizard.high_level_type = 'Sensor-Actuator' OR (DashboardWizard.high_level_type = 'Special Widget' AND sub_nature = 'First Aid Data')) GROUP BY unique_name_id ORDER BY id DESC;";
}*/

$rs = $open_search->getDevices();


//$rs = mysqli_query($link, $query);
$result = [];

$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);

// if ($open_search->isNotEmptyResult($rs)) {
if (sizeof($rs) > 0) {
    $result = [];
    $count = 0;
    $oldEntries = [];
    $noFeatures = [];
    try {
        //foreach ( $rs['hits']['hits'] as $hit  /*$row = mysqli_fetch_assoc($rs)*/ ) {
        foreach ( $rs as $row  /*$row = mysqli_fetch_assoc($rs)*/ ) {
            //$row = $hit['_source'];

            $high_level_type = $row['high_level_type'];
            $nature = $row['nature'];
            $sub_nature = $row['sub_nature'];
            $low_level_type = $row['low_level_type'];
            $unique_name_id = $row['unique_name_id'];
            $get_instances = $row['get_instances'];

            $instance_uri = $row['instance_uri'];
            //   $unit = $row[unit];
            $metric = $row['metric'];
            $saved_direct = $row['saved_direct'];
            $kb_based = $row['kb_based'];
            $parameters = $row['parameters'];

        /*    if (stripos($unique_name_id, 'tusc_weather_sensor') !== false ||
                stripos($unique_name_id, '-Rest') !== false ||
                stripos($unique_name_id, '-Papier') !== false ||
                stripos($unique_name_id, '-Glas') !== false ||
                stripos($unique_name_id, '-Plastic') !== false ||
                stripos($unique_name_id, 'SIRSensor_') !== false ||
                stripos($unique_name_id, 'ARPAT_') !== false) {
                    $stop_flag = 1;
            }*/

          //  if (strpos($row['get_instances'] , 'weather_sensor_ow') == false) {
            array_push($result, $row);
            if ($row['high_level_type'] == 'From Dashboad to IOT Device' || $row['high_level_type'] == 'From IOT Device to Dashboard') {
                if (strpos($row['get_instances'], "%2525") != false && strpos($row['get_instances'], "%252525") == false) {
                    $sUri = urlencode($row['get_instances']);
                } else {
                    $sUri = $row['get_instances'];
                }
                $url = $kbUrlSuperServiceMap . "?serviceUri=" . $sUri . "&healthiness=true&format=json";
                $instance_uri = "single_marker";
            } else if ($row['nature'] != 'IoTDevice' && $sub_nature != "First Aid Data") {
                if (strpos($row['get_instances'], "%2525") != false && strpos($row['get_instances'], "%252525") == false) {
                    $sUri = urlencode($row['get_instances']);
                } else {
                    $sUri = $row['get_instances'];
                }
                if ($row['sub_nature'] != 'IoTSensor' && $row['sub_nature'] != 'IoTSensor-Actuator') {
                    $sUriEnc = str_replace('%3A', '%253A', $sUri);
                    $url = $kbUrlSuperServiceMap . "?serviceUri=" . $sUriEnc . "&healthiness=true&format=json&apikey=" . $ssMapAPIKey;
                } else {
                    $sUriEnc = str_replace('%3A', '%253A', $sUri);
                    $url = $kbUrlSuperServiceMap . "?serviceUri=" . $sUriEnc . "&healthiness=true&format=json&apikey=" . $ssMapAPIKey;
                }
                $instance_uri = "any + status";
            } else if ($sub_nature === "First Aid Data") {
                $url =  $kbUrlSuperServiceMap . "?serviceUri=" . $row['parameters'] . "&healthiness=true&format=json";
            }

            $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $date_now = $now->format('c');
            $date_now_ok = explode("+", $date_now);
            $check_time = str_replace("T", " ", $date_now_ok[0]);
            //     print_r($check_time);

            $serviceChangeBuffer["current"] = $sUri;
            if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {

                $context = stream_context_create([
                    "http" => [
                        // http://docs.php.net/manual/en/context.http.php
                        "method"        => "GET",
                        "ignore_errors" => true,
                    ],
                ]);

                $response = file_get_contents($url);

                $status_line = $http_response_header[0];

                preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);

                $status = $match[1];

              /*  if ($status !== "200") {
                    throw new RuntimeException("unexpected response status: {$status_line}\n" . $response);
                }*/

                $responseArray = json_decode($response, true);
           //     if ($response != false) {
                if ($status == "400") {
                  /*  if ($responseArray["Service"]) {
                        if (sizeof($responseArray["Service"]["features"]) > 0) {

                        } else {
                            // mark as NO-FEATURES
                            array_push($noFeatures, $sUri);
                        }
                    } else if ($responseArray["Sensor"]) {
                        if (sizeof($responseArray["Sensor"]["features"]) > 0) {

                        } else {
                            // mark as NO-FEATURES
                            array_push($noFeatures, $sUri);
                        }
                    }
                } else {*/
                    // mark as OLD
                    $query_updateOld = "UPDATE DashboardWizard SET oldEntry = 'old', healthiness = 'false', lastCheck = '" . $check_time . "' WHERE high_level_type = '" . $high_level_type . "' AND nature = '" . $nature . "' AND unique_name_id = '" . $unique_name_id . "' AND get_instances = '" . $get_instances . "';";
                    mysqli_query($link, $query_updateOld);
                    array_push($oldEntries, $sUri);

                    
                    $open_search->healthinessUpdate($get_instances,$check_time,'old','false',
                    [['term'=>[
                              'high_level_type'=>$high_level_type,
                              
                        ]],
                     ['term'=>['nature'=>$nature,
                    ]],
                     ['term'=>['unique_name_id'=>$unique_name_id]
                    ]]);


                    continue;
                } else if ($status == "200") {

                    $realtime_data = $responseArray['realtime']['results']['bindings'][0];
                    $healthiness = $responseArray['healthiness'];

                    /*     $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
                         $date_now = $now->format('c');
                         $date_now_ok = explode("+", $date_now);
                         $check_time = str_replace("T", " ", $date_now_ok[0]);
                         //     print_r($check_time);*/

                    if ($sub_nature === 'Car_park') {
                        $stop_flag = 1;
                    }

                    if ($realtime_data['measuredTime']) {
                        $last_date = str_replace("T", " ", $realtime_data['measuredTime']['value']);
                    } else if ($realtime_data['instantTime']) {
                        $last_date = str_replace("T", " ", $realtime_data['instantTime']['value']);
                    } else if ($realtime_data['updating']) {
                        $last_date = str_replace("T", " ", $realtime_data['updating']['value']);
                    }

                    if (!empty($realtime_data)) {

                        foreach ($realtime_data as $key => $item) {

                            if ($key != 'measuredTime' && $key != 'updating' && $key != 'instantTime') {
                                if ($key != 'capacity' || $sub_nature != 'Car_park') {

                                /*    if ($realtime_data[$key]['valueDate']) {
                                        $last_date = str_replace("T", " ", $realtime_data[$key]['valueDate']);
                                        $last_date = str_replace(".000", " ", $last_date);
                                    }*/
                                    $measure = $realtime_data[$key]['value'];
                                    // if ($realtime_data[$key]['unit'] != '') {
                                    if (!empty($realtime_data[$key]['unit'])) {
                                        $unit = $realtime_data[$key]['unit'];
                                    }

                                    if (array_key_exists($key, $healthiness)) {

                                        $healthiness_value = $healthiness[$key]['healthy'];

                                    } else {

                                        $healthiness_value = "false";
                                    }

                                    if ($healthiness_value = $healthiness[$key]['healthy'] === false) {
                                        $healthy = "false";
                                    } else if ($healthiness_value = $healthiness[$key]['healthy'] === true) {
                                        $healthy = "true";
                                    } else {
                                        $healthy = "false";
                                    }

                                    $updateTimeU = new DateTime("now", $current_dateTimeZone);
                                    $offset = $current_dateTimeZone->getOffset($updateTimeU);
                                    $update_scritp_timeU = $updateTimeU->format('c');
                                    $update_time_okU = str_replace("T", " ", $update_scritp_timeU);
                                    echo("             Udpating : " . $key . " at: " . $update_time_okU . " --> healthiness = " . $healthy . "\n");
                                    $query_update = "UPDATE DashboardWizard SET oldEntry = NULL, last_date= '" . substr($last_date, 0, strlen($last_date) - 6) . "', last_value = '" . $measure . "', healthiness = '" . $healthy . "', lastCheck = '" . substr($update_time_okU, 0, strlen($update_time_okU) - 6) . "' WHERE get_instances= '" . $get_instances . "' AND low_level_type = '" . $key . "';";
                                    mysqli_query($link, $query_update);

                                    $ldopen = substr($last_date, 0, strlen($last_date) - 6);
                                    $lscopen = substr($update_time_okU, 0, strlen($update_time_okU) - 6);

                                    $open_search->healthinessUpdate($get_instances,
                                    $lscopen,'',$healthy,
                                    ['term'=>[
                                            'low_level_type'=>$key,
                                            
                                        ]
                                    ],"ctx._source.last_date = '$ldopen';ctx._source.last_value = '$measure'");

                                }
                            } else {
                                $stop_flag = 1;
                            }

                        }
                        //**********************************************************************************
                        $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
                        $date_now = $now->format('c');
                        $date_now_ok = explode("+", $date_now);
                        $check_time = str_replace("T", " ", $date_now_ok[0]);

                        // Per i Sensori a livello generale (senza misure) si mette healthiness = 'true' se almeno una delle sue misure ha heathiness = 'true', altrimenti si mette healthiness = 'false';

                        $checkHealthinessSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE
                         get_instances = '" . $get_instances . "' AND low_level_type != '' AND healthiness = 'true'";

                        $rs2 = $open_search->getHealthinessSensorGeneralQuery($get_instances);

                        
                        //$rs2 = mysqli_query($link, $checkHealthinessSensorGeneralQuery);

                        $result2 = [];

                        //if ($rs2) {
                            $result2['table'] = [];
                            if ($open_search->isNotEmptyResult($rs2) /*$row2 = mysqli_fetch_assoc($rs2)*/) {
                                $row2 = $rs2['hits']['hits'][0]['_source'];
                                $healthiness_sql = 'true';
                                $last_date_sql = $row2['last_date'];
                            } else {
                                $healthiness_sql = 'false';
                                //$lastDateSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE unique_name_id = '" . $unique_name_id . "'";
                                $lastDateSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE get_instances = '" . $get_instances . "'";
                                //$rs3 = mysqli_query($link, $lastDateSensorGeneralQuery);

                                $rs3 = $open_search->getGetInstancesGeneralQuery($get_instances);
                                //if ($rs3) {
                                    $result3['table'] = [];
                                    if ( $open_search->isNotEmptyResult($rs3)/*$row3 = mysqli_fetch_assoc($rs3)*/) {
                                        $row3 = $rs3['hits']['hits'][0]['_source'];
                                        $last_date_sql = $row3['last_date'];
                                    }
                                //}
                            }
                        //}

                        if ($last_date_sql === null && $last_date != null) {

                            $query_updateGeneral = "UPDATE DashboardWizard SET oldEntry = NULL, last_date = '" . $last_date . "', healthiness = '" . $healthiness_sql . "', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '';";
                            mysqli_query($link, $query_updateGeneral);

                            $open_search->healthinessUpdate($get_instances,$check_time, '', $healthiness_sql,["term"=>["low_level_type"=>"NONE"]],
                            "ctx._source.last_date = '$last_date'");

                        } else if ($last_date_sql === null) {

                            $query_updateGeneral = "UPDATE DashboardWizard SET oldEntry = NULL, last_date = last_date, healthiness = '" . $healthiness_sql . "', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '';";
                            mysqli_query($link, $query_updateGeneral);

                            $open_search->healthinessUpdate($get_instances,$check_time, '', $healthiness_sql,["term"=>["low_level_type"=>"NONE"]]);

                        } else if ($last_date_sql != null) {

                            $query_updateGeneral = "UPDATE DashboardWizard SET oldEntry = NULL, last_date= '" . $last_date_sql . "', healthiness = '" . $healthiness_sql . "', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '';";
                            mysqli_query($link, $query_updateGeneral);

                            $open_search->healthinessUpdate($get_instances,$check_time, '', $healthiness_sql,["term"=>["low_level_type"=>"NONE"]],
                            "ctx._source.last_date = '$last_date'");

                        }
                        //**********************************************************************************

                    } else {
                        if ($unique_name_id != '') {
                            if (!empty($healthiness)) {
                                foreach ($healthiness as $key => $item) {

                                    if ($key != 'measuredTime' && $key != 'updating' && $key != 'instantTime') {
                                        if ($key != 'capacity' || $sub_nature != 'Car_park') {

                                            $measure = $realtime_data[$key]['value'];
                                            // if ($realtime_data[$key]['unit'] != '') {
                                            if (!empty($realtime_data[$key]['unit'])) {
                                                $unit = $realtime_data[$key]['unit'];
                                            }

                                            if (array_key_exists($key, $healthiness)) {

                                                $healthiness_value = $healthiness[$key]['healthy'];

                                            } else {

                                                $healthiness_value = "false";
                                            }

                                            if ($healthiness_value = $healthiness[$key]['healthy'] === false) {
                                                $healthy = "false";
                                            } else if ($healthiness_value = $healthiness[$key]['healthy'] === true) {
                                                $healthy = "true";
                                            } else {
                                                $healthy = "false";
                                            }

                                            $updateTimeU = new DateTime("now", $current_dateTimeZone);
                                            $offset = $current_dateTimeZone->getOffset($updateTimeU);
                                            $update_scritp_timeU = $updateTimeU->format('c');
                                            $update_time_okU = str_replace("T", " ", $update_scritp_timeU);
                                            echo("             Udpating : " . $key . " at: " . $update_time_okU . " --> healthiness = " . $healthy . "\n");

                                            $query_update = "UPDATE DashboardWizard SET oldEntry = NULL, healthiness = '" . $healthy . "', lastCheck = '" . substr($update_time_okU, 0, strlen($update_time_okU) - 6) . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '" . $key . "';";
                                            mysqli_query($link, $query_update);

                                            $open_search->healthinessUpdate($get_instances,substr($update_time_okU, 0, strlen($update_time_okU) - 6),
                                             '', $healthy,["term"=>["low_level_type"=>$key]]);

                                        }
                                    } else {
                                        $stop_flag = 1;
                                    }

                                }
                            }
                            //**********************************************************************************
                            $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
                            $date_now = $now->format('c');
                            $date_now_ok = explode("+", $date_now);
                            $check_time = str_replace("T", " ", $date_now_ok[0]);

                            $checkHealthinessSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE get_instances = '" . $get_instances . "' AND low_level_type != '' AND healthiness = 'true'";
                            $rs2_old = mysqli_query($link, $checkHealthinessSensorGeneralQuery);

                            $rs2 = $open_search->getHealthinessSensorGeneralQuery($get_instances);
     
                            $result2 = [];

                            //if ($rs2) {
                                $result2['table'] = [];
                                if ($open_search->isNotEmptyResult($rs2) /*$row2 = mysqli_fetch_assoc($rs2)*/) {
                                    $row2 = $rs2['hits']['hits'][0]['_source'];
                                    $healthiness_sql = 'true';
                                    $last_date_sql = $row2['last_date'];
                                } else {
                                    $healthiness_sql = 'false';
                                    //$lastDateSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE unique_name_id = '" . $unique_name_id . "'";
                                    $lastDateSensorGeneralQuery = "SELECT * FROM DashboardWizard WHERE get_instances = '" . $get_instances . "'";
                                    $rs3_old = mysqli_query($link, $lastDateSensorGeneralQuery);

                                    $rs3 = $open_search->getGetInstancesGeneralQuery($get_instances);


                                    //if ($rs3) {
                                        $result3['table'] = [];
                                        if ( $open_search->isNotEmptyResult($rs3) /*$row3 = mysqli_fetch_assoc($rs3)*/) {
                                            $row3 = $rs3['hits']['hits'][0]['_source'];
                                            $last_date_sql = $row3['last_date'];

                                        }
                                    //}
                                }
                            //}
                            if ($last_date_sql === null) {

                                $query_updateGeneral = "UPDATE DashboardWizard SET oldEntry = NULL, healthiness = '" . $healthiness_sql . "', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '';";
                                mysqli_query($link, $query_updateGeneral);

                                $open_search->healthinessUpdate($get_instances,$check_time,
                                 '', $healthiness_sql,["term"=>["low_level_type"=>"NONE"]]);



                            } else {

                                $query_updateGeneral = "UPDATE DashboardWizard SET oldEntry = NULL, last_date= '" . $last_date_sql . "', healthiness = '" . $healthiness_sql . "', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "' AND low_level_type = '';";
                                mysqli_query($link, $query_updateGeneral);

                                $open_search->healthinessUpdate($get_instances,$check_time,
                                 '', $healthiness_sql,["term"=>["low_level_type"=>"NONE"]],"ctx._source.last_date = '$last_date_sql'");

                            }
                            //**********************************************************************************

                        }
                    }
                }


                $serviceChangeBuffer["last"] = $sUri;
                $stopFlag = 1;
                $count++;
                echo($count . " FINISHED HEALTHINESS CHECK FOR DEVICE: " . $unique_name_id . "\n");
            }

        }
    } catch (Exception $e) {
        echo 'Exception: ',  $e->getMessage(), "\n";
    }


} else {
    mysqli_close($link);
    $result['detail'] = 'Ko';
}


$open_search->setBoolEmptyHealthinessUpdate();
mysqli_query($link, "UPDATE DashboardWizard SET healthiness = 'false' WHERE healthiness IS NULL OR healthiness = '';");

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End HealthinessCheck SCRIPT at: ".$end_time_ok);
//echo json_encode($result);