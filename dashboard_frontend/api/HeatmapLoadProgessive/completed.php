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

   include "../../config.php";
   require "../../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

session_start();
ini_set("max_execution_time", 0);
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$info_heatmap = []; // Inizializza l'array per evitare avvisi

if (isset($_POST["accessToken"])) {

            $heatmap_name = $_POST["heatmap_name"];
            $metric_name = $_POST["metric_name"];
            $to_date_time = $_POST["time"];
            $completed = 1;

            // Definisce i parametri per la seconda chiamata API
           /* $url_completed= "http://heatmap-api:8080/completed?mapName=".$heatmap_name."&metricName=".$metric_name."&date=".$to_date_time."&completed=1";
            $heatmapCompleted = "http://heatmap-api:8080/completed";
            $heatmapCompleted = "http://dashboard/heatmap/setMap.php";*/
            $url_completed =
                $heatmapSetMap .
                "?mapName=" .
                $heatmap_name .
                "&metricName=" .
                $metric_name .
                "&date=" .
                $to_date_time .
                "&completed=1";

            // Inizializza una nuova sessione cURL per la richiesta GET
            $ch1 = curl_init($url_completed);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            //  curl_setopt($ch1, CURLOPT_POST, true);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
            ]);

            // Esegue la seconda richiesta GET
            $response_get = curl_exec($ch1);
            //$api_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //

            if ($response_get === false) {
                $error_get = curl_error($ch1);
                error_log("Errore cURL: " . $error_get);
                die(json_encode(["status" => "error", "message" => "Errore durante la richiesta. Contattare il supporto."]));

            } else {
                $api_status_code = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                curl_close($ch1);
                if ($api_status_code !== 200) {
                    error_log("Errore cURL: " . $error_get);
                    die(json_encode(["status" => "error", "message" => "Errore durante la richiesta. Contattare il supporto."]));
                    
                }
            }


            if ($api_status_code === 200) {
                $info_heatmap["interpolation"] = [
                    "POSTstatus" => "Interpolated data saved correctly"
                ];
                // echo "Interpolated data saved correctly";
                createDevice();      
            } else {
                $info_heatmap["interpolation"] = [
                    "POSTstatus" => "Problems on saving interpolated data",
                    "error" => $response_get
                ];
            }

        // Restituisce $info_heatmap come JSON solo se contiene dati
}else {
    header("HTTP/1.1 401 Unauthorized");
    $info_heatmap["responseState"] = "Unauthorized request.";
     json_encode($info_heatmap);
   // die();
}
//createDevice();

function createDevice(){
   // $iot_directory_api = 'http://dashboard/iot-directory/api/';
    $iot_directory_api = $GLOBALS['iot_directory_api'];
    $iot_directory_model = $iot_directory_api . "model.php";
    $iot_directory_device = $iot_directory_api . "device.php";
    $iot_directory_value = $iot_directory_api . "value.php";
    //
    
    // Itera sull'array per calcolare i valori minimi e massimi
// Trova i valori minimo e massimo
$minLat = $_POST['minLatitude'];
$maxLat = $_POST['maxLatitude'];
$minLong = $_POST['minLongitude'];
$maxLong = $_POST['maxLongitude'];
    //

    $accessToken = $_POST["accessToken"];
    $minDate = $_POST["minDate"];
    $maxDate = $_POST["maxDate"];
    //
    $name =  $_POST["heatmap_name"];

    $dataArrayForGetModel = array("action" => 'get_model',
        "name" => 'heatMapModel_vectorFlow',
        "token" => $accessToken,
        "nodered" => 'access'
    );

    $ch_model = curl_init();
    $url_model = sprintf("%s?%s", $iot_directory_model, http_build_query($dataArrayForGetModel));
    curl_setopt($ch_model, CURLOPT_URL, $url_model);
    curl_setopt($ch_model, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ));
    curl_setopt($ch_model, CURLOPT_RETURNTRANSFER, 1);
    $modelCall = curl_exec($ch_model);
    /*if (curl_errno($ch_model)) {
        die('CURL error: ' . curl_error($ch_model));
    }*/
    $device_model = json_decode($modelCall);
    $content_model = $device_model->content;
    $iot_broker = $content_model->contextbroker;
    $iot_org = $content_model->organization;
    $iot_attributes = $content_model->attributes;
    $k1 =  $content_model->k1;
    $k2 =  $content_model->k2;
    $kind = $content_model->kind;
    $devicetype =  $content_model->devicetype; 
    $format =   $content_model->format; 
    curl_close($ch_model);
    
////
//'[{"name":"heatMapModel_vectorFlow","description":"Model to store a heatmap","device_type":"Heatmap","frequency":"600","kind":"sensor","contextbroker":"orion-1","protocol":"ngsi","format":"json","healthiness_criteria":"refresh_rate","healthiness_value":"300","key_generator":"normal","producer":"DISIT","subnature":"","static_attributes":"[]","service":"null","service_path":"","d_attributes":[{"value_name":"mapName","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"colorMap","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"minimumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"maximumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"instances","data_type":"integer","value_type":"Count","editable":"0","value_unit":"#","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"description","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"boundingBox","data_type":"string","value_type":"geolocation","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"size","data_type":"integer","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"}]}]';
$attributes ='[{"value_name":"mapName","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"colorMap","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"minimumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"maximumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"instances","data_type":"integer","value_type":"Count","editable":"0","value_unit":"#","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"description","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"boundingBox","data_type":"string","value_type":"geolocation","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"size","data_type":"integer","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"}]';
///
$data_array = array(
    "action" => 'insert',
    "id" => $name,
    "type" => $devicetype,
    "contextbroker" => $iot_broker,
    "kind" => $kind,
    "format" => $format,
    "latitude" => '43.769',
    "longitude" => '11.251',
    "frequency" => '600',
    "producer" => '',
    "model" => 'heatMapModel_vectorFlow',
    "k1" => '',
    "k2" => '',
    "token" => $accessToken,
    "nodered" => 'access',
    "attributes" => $attributes
);
$data_array0 = json_encode($data_array);
$curl = curl_init();
$url = sprintf("%s?%s", $iot_directory_device, http_build_query($data_array));
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$deviceCall = curl_exec($curl);
if (curl_errno($curl)) {
    $error_code = curl_errno($curl);
    $error_message = curl_error($curl);
    $info = curl_getinfo($curl);


}
$response = json_decode($deviceCall, true);
//
//
curl_close($curl);

$metric_name = $_POST["metric_name"];
$size = $_POST['size'];
$vectorDescription = $_POST['description'];
///////////
        ////////////////////////////////////////
        if ($response) {
            /////////
            $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";
            $bbox = '{"min_lat":"'.$minLat.'", "min_lon":"'.$minLong.'", "max_lat":"'.$maxLat.'", "max_lon":"'.$maxLong.'"}';
           $boundingBox='{
                "type": "json",
                "value": {
                "type": "Polygon",
                "coordinates": [
                [['.$minLat.','.$minLong.'],
                ['.$minLat.','.$maxLong.'],
                ['.$maxLat.','.$minLong.'],
                ['.$maxLat.','.$maxLong.']]
                ]
                }
                }';
            $new_attributes =
                '{"mapName":{"value":"' .
                $name .
                '","type":"string"},
                                       "colorMap":{"value":"'.$metric_name.'","type":"string"},
                                       "minimumDate":{"value":"' .
                $minDate .
                '","type":"string"},
                                       "maximumDate":{"value":"' .
                $maxDate .
                '","type":"string"},
                                       "instances":{"value":1,"type":"integer"},
                                       "description":{"value":"'.$vectorDescription.'","type":"string"},
                                       "boundingBox":'.$boundingBox.',
                                       "size":{"value":"'.$size.'","type":"integer"}
                                       }';
            $value_array1 = [
                "action" => "Insert_Value",
                "id" => $name,
                "type" => "Heatmap",
                "contextbroker" => $iot_broker,
                "service" => null,
                "servicePath" => null,
                "version" => "v2",
                "nodered" => "access",
                "token" => $accessToken,
                "payload" => $new_attributes,
            ];
            

            $curl01 = curl_init();
            $url01 = sprintf("%s?%s", $iot_directory_device, http_build_query($value_array1));
            // echo('$url');
            curl_setopt($curl01, CURLOPT_URL, $url01);
            curl_setopt($curl01, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ));
            curl_setopt($curl01, CURLOPT_RETURNTRANSFER, 1);
                $deviceCall01 = curl_exec($curl01);

                if ($deviceCall01 === false) {
                    die(json_encode([
                        "status" => "error",
                        "message" => "Errore cURL: " . curl_error($curl01),
                    ]));
                }

                $response01 = json_decode($deviceCall01, true);

                if (!is_array($response01) || !isset($response01["status"])) {
                    die(json_encode([
                        "status" => "error",
                        "message" => "Risposta non valida dal server: " . $deviceCall01,
                    ]));
                }

                $response_status = $response01["status"];
                //echo('response_status: '.$response_status);
                $http_status = curl_getinfo($curl01, CURLINFO_HTTP_CODE);
            //
            if($http_status == 'error'){
                $message_output['code'] = $http_status;
                $message_output['message'] = 'Error during device creation';
                $message_output['callback']=$response01;
            }else{
                $message_output['code'] = $http_status;
                $message_output['message'] = 'Device Successfully created';
                $message_output['callback']=$deviceCall01;
            }
                
                echo json_encode($message_output);
        } else {
            //
            //echo 'HTTP Status Code: ' . $http_status . "\n";
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
           // echo 'Raw response: ' . $deviceCall . "\n";
            //
            $message_output['code'] = $http_status;
            $message_output['message'] = $deviceCall;
            echo json_encode($message_output);
        }
/////////////////
}


?>
