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

include "../config.php";
require "../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;
header('Access-Control-Allow-Origin: *');

session_start();
ini_set("max_execution_time", 0);
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$url = $heatmapInsert; // http://heatmap-api:8080/insertArray
$info_heatmap = []; // Inizializza l'array per evitare avvisi
$DEBUG = false;
$DEBUG_FILE = 'heatmapLoad_debug.log';

if (isset($_POST["accessToken"])) {
    $bearerToken = $_POST['accessToken'];
	$check_connection = $ssoUserinfoEndpoint;
	// Inizializza una sessione cURL
    $ch0 = curl_init();
    // Imposta l'URL dell'API
    curl_setopt($ch0, CURLOPT_URL, $check_connection);
    curl_setopt($ch0, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $bearerToken
    ));
    curl_setopt($ch0, CURLOPT_RETURNTRANSFER, true);
    // Esegui la richiesta cURL
    $response = curl_exec($ch0);
    // Controlla se ci sono errori
    if(curl_errno($ch0)) {
        //echo 'Errore cURL: ' . curl_error($ch);
        $output_message['code'] = '400';
        $output_message['message'] = 'Errore cURL: ' . curl_error($ch0);
        $output_message['responseState'] = "ko";
        echo json_encode($output_message);
        die();
    }

    if (isset($_POST["data"])) {
        logDebug(">>>>>>>>>>>> Ingesting Heatmap data in DB... \n");
        $interpolated_heatmap   = $_POST["data"]; // Ricevi i dati dall'input POST

        // Decodifica i dati in un array PHP se non lo sono già
        if (is_string($interpolated_heatmap)) {
            $interpolated_heatmap = json_decode($interpolated_heatmap, true); // true per ottenere un array
        }

        // Verifica che la decodifica sia andata a buon fine
        if (!is_array($interpolated_heatmap)) {
            die("Errore: dati non validi.");
        }
        // Itera sull'array e converte i valori di 'id' in interi
        foreach ($interpolated_heatmap as &$item) {
            if (isset($item["id"])) {
                $item["id"] = (int) $item["id"]; // Converte 'id' in un intero
            }
            if (isset($item["value"])) {
                $item["value"] = (float) $item["value"];
            }
            if (isset($item["latitude"])) {
                $item["latitude"] = (float) $item["latitude"];
            }
            if (isset($item["longitude"])) {
                $item["longitude"] = (float) $item["longitude"];
            }
            if (isset($item["clustered"])) {
                $item["clustered"] = (int) $item["clustered"];
            }
            if (isset($item["projection"])) {
                $item["projection"] = (int) $item["projection"];
            }
            if (isset($item["file"])) {
                $item["file"] = (int) $item["file"];
            }
            if (isset($item["xLength"])) {
                $item["xLength"] = (float) $item["xLength"];
            }
            if (isset($item["yLength"])) {
                $item["yLength"] = (float) $item["yLength"];
            }
        }

        // Codifica l'array aggiornato in JSON
        $request_body_json_string = json_encode($interpolated_heatmap);

        // Invia la richiesta con cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_json_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);

        $response = curl_exec($ch);

        // Controlla eventuali errori
        if ($response === false) {
            $error = curl_error($ch);
            die(json_encode([
                "responseState" => "ko",
                "status" => "error",
                "message" => "Errore cURL durante la prima chiamata POST: $error",
                "inputData" =>$interpolated_heatmap
            ]));
        } else {
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode !== 200) {
                die(json_encode([
                    "responseState" => "ko",
                    "status" => "error",
                    "message" => "Errore HTTP nella prima chiamata POST: Codice $httpcode",
                    "response" => $response,
                    "inputData" =>$interpolated_heatmap
                ]));
            }
        }
        // Se la risposta ha avuto successo (status code 200), prosegue con la seconda chiamata GET
        if ($httpcode === 200) {
            $heatmap_name = $_POST["heatmap_name"];
            $metric_name = $_POST["metric_name"];
            $to_date_time = $_POST["time"];
            if (isset($_POST["completed_val"])) {
                $completed = $_POST["completed_val"];
            } else {
                $completed = 1;
            }

            // Definisce i parametri per la seconda chiamata API
           // $url_completed= "http://heatmap-api:8080/completed?mapName=".$heatmap_name."&metricName=".$metric_name."&date=".$to_date_time."&completed=1";
           // $heatmapCompleted = "http://heatmap-api:8080/completed";
           // $heatmapCompleted = "http://dashboard/heatmap/setMap.php";
            $url_completed =
                $heatmapSetMap .
                "?mapName=" .
                $heatmap_name .
                "&metricName=" .
                $metric_name .
                "&date=" .
                $to_date_time .
                "&completed=" .
                $completed;

            // Inizializza una nuova sessione cURL per la richiesta GET
            $ch1 = curl_init($url_completed);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
            ]);

            // Esegue la seconda richiesta GET
            $response_get = curl_exec($ch1);

            if ($response_get === false) {
                $error_get = curl_error($ch1);
                die(json_encode([
                    "responseState" => "ko",
                    "status" => "error",
                    "message" => "Errore cURL durante la seconda chiamata GET: " . $error_get,
                    "inputData" =>$interpolated_heatmap
                ]));
            } else {
                $api_status_code = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                curl_close($ch1);
                if ($api_status_code !== 200) {
                    die(json_encode([
                        "responseState" => "ko",
                        "status" => "error",
                        "message" => "Errore HTTP nella seconda chiamata GET: Codice " . $api_status_code,
                        "response" => $response_get,
                        "inputData" =>$interpolated_heatmap
                    ]));
                }
            }

            logDebug(">>>>>>>>>>>> Heatmap data inserted in DB \n");

            // Controlla il codice di stato della seconda richiesta
            if ($api_status_code === 200) {
                $info_heatmap["responseState"] = "ok";
                $info_heatmap["interpolation"] = [
                    "POSTstatus" => "Interpolated data saved correctly",
                    "inputData" =>$interpolated_heatmap
                ];
                
                logDebug(">>>>>>>>>>>> Interpolated data saved correctly \n");
                // createDevice($request_body_json_string);      
            } else {
                $info_heatmap["responseState"] = "ko";
                $info_heatmap["interpolation"] = [
                    "POSTstatus" => "Problems on saving interpolated data",
                    "error" => $response_get,
                    "inputData" =>$interpolated_heatmap
                ];
            }
        } else {
            $info_heatmap[
                "responseState"
            ] = "Errore nella prima chiamata POST: codice di stato $httpcode\n, response: $response_get";
            echo json_encode($info_heatmap);
        }
       
        // Restituisce $info_heatmap come JSON solo se contiene dati
        if (!empty($info_heatmap)) {
            echo json_encode($info_heatmap);
        }
    } else {
        header("HTTP/1.1 403 Forbidden");
        $info_heatmap["responseState"] = "Required parameter 'data' missing";
        echo json_encode($info_heatmap);
        die();
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $info_heatmap["responseState"] = "Unauthorized request.";
    echo json_encode($info_heatmap);
    die();
}

function logDebug($string){
    global $DEBUG;
    global $DEBUG_FILE;
    if($DEBUG){
        file_put_contents($DEBUG_FILE, $string, FILE_APPEND);
    }
}

// THINGS TO REMOVE ///

//createDevice();

// function createDevice($listArray){
//     file_put_contents('TMP_debug.log', ">>>>>>>>>>>> Creating HEATMAP device... \n", FILE_APPEND);

//     //$iot_directory_api = 'http://dashboard/iot-directory/api/';
//     $iot_directory_api = $GLOBALS['iot_directory_api'];
//     $iot_directory_model = $iot_directory_api . "model.php";
//     $iot_directory_device = $iot_directory_api . "device.php";
//     $iot_directory_value = $iot_directory_api . "value.php";

//     $dataArr = json_decode($listArray);

//     $minLatitude = $maxLatitude = $dataArr[0]->latitude;
//     $minLongitude = $maxLongitude = $dataArr[0]->longitude;
    
//     // Itera sull'array per calcolare i valori minimi e massimi
//     foreach ($dataArr as $item) {
//         if ($item->latitude < $minLatitude) $minLatitude = $item->latitude;
//         if ($item->latitude > $maxLatitude) $maxLatitude = $item->latitude;
//         if ($item->longitude < $minLongitude) $minLongitude = $item->longitude;
//         if ($item->longitude > $maxLongitude) $maxLongitude = $item->longitude;
//     }
//     // Trova i valori minimo e massimo
//     $minLat = ($minLatitude);
//     $maxLat = ($maxLatitude);
//     $minLong = ($minLongitude);
//     $maxLong = ($maxLongitude);

//     $centroid = getGeodeticCentroid($minLat, $maxLat, $minLong, $maxLong);

//     $accessToken = $_POST["accessToken"];
//     $minDate = $_POST["minDate"];
//     $maxDate = $_POST["maxDate"];
//     $date_observed = $_POST["dateObserved"];
//     $name =  $_POST["heatmap_name"];

//     $device_model_name = 'heatMapModel_vectorFlow';

//     // GET DEVICE MODEL ///////////////////////////////////////////////////////////////////////////////
//     $dataArrayForGetModel = array("action" => 'get_model',
//         "name" => $device_model_name,
//         "token" => $accessToken,
//         "nodered" => 'access'
//     );       
//     $ch_model = curl_init();
//     $url_model = sprintf("%s?%s", $iot_directory_model, http_build_query($dataArrayForGetModel));
//     curl_setopt($ch_model, CURLOPT_URL, $url_model);
//     curl_setopt($ch_model, CURLOPT_HTTPHEADER, array(
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $accessToken
//     ));
//     curl_setopt($ch_model, CURLOPT_RETURNTRANSFER, 1);

//     file_put_contents('TMP_debug.log', "URL to retrieve model \n", FILE_APPEND);
//     file_put_contents('TMP_debug.log', $url_model . "\n", FILE_APPEND);

//     $modelCall = curl_exec($ch_model);
//     /*if (curl_errno($ch_model)) {
//         die('CURL error: ' . curl_error($ch_model));
//     }*/

//     $device_model = json_decode($modelCall);
//     $content_model = $device_model->content;
//     $iot_broker = $content_model->contextbroker;
//     $iot_org = $content_model->organization;
//     $producer = $content_model->producer;
//     $frequency = $content_model->frequency;
//     $iot_attributes = $content_model->attributes;
//     $k1 =  $content_model->k1;
//     $k2 =  $content_model->k2;
//     $kind = $content_model->kind;
//     $devicetype =  $content_model->devicetype; 
//     $format =   $content_model->format; 
//     $hlt = $content_model->hlt;
//     curl_close($ch_model);

//     file_put_contents('TMP_debug.log', "Retrieved model \n", FILE_APPEND);
//     file_put_contents('TMP_debug.log', $modelCall . "\n", FILE_APPEND);
    
//     // CREATE DEVICE ACCORDING TO THE RETRIEVED MODEL /////////////////////////////////////////////////////
//     //'[{"name":"heatMapModel_vectorFlow","description":"Model to store a heatmap","device_type":"Heatmap","frequency":"600","kind":"sensor","contextbroker":"orion-1","protocol":"ngsi","format":"json","healthiness_criteria":"refresh_rate","healthiness_value":"300","key_generator":"normal","producer":"DISIT","subnature":"","static_attributes":"[]","service":"null","service_path":"","d_attributes":[{"value_name":"mapName","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"colorMap","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"minimumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"maximumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"instances","data_type":"integer","value_type":"Count","editable":"0","value_unit":"#","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"description","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"boundingBox","data_type":"string","value_type":"geolocation","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"size","data_type":"integer","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"}]}]';
//     // $attributes ='[{"value_name":"mapName","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"colorMap","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"minimumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"maximumDate","data_type":"string","value_type":"time","editable":"0","value_unit":"s","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"instances","data_type":"integer","value_type":"Count","editable":"0","value_unit":"#","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"description","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"boundingBox","data_type":"string","value_type":"geolocation","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"},{"value_name":"size","data_type":"integer","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300","real_time_flag":"false"}]';
    
//     $wkt_bbox = 'POLYGON((' . 
//         $minLong . ' ' . $minLat . ',' .
//         $minLong . ' ' . $maxLat . ',' .
//         $maxLong . ' ' . $maxLat . ',' .
//         $maxLong . ' ' . $minLat .         
//     '))'; //'{"min_lat":"'.$minLat.'", "min_lon":"'.$minLong.'", "max_lat":"'.$maxLat.'", "max_lon":"'.$maxLong.'"}';

//     $data_array = array(
//         "action" => 'insert',
//         "id" => $name,
//         "type" => $devicetype,
//         "contextbroker" => $iot_broker,
//         "kind" => $kind,
//         "format" => $format,
//         "latitude" => (string)$centroid['latitude'],
//         "longitude" => (string)$centroid['longitude'],
//         "frequency" => $frequency,
//         "producer" => $producer,
//         "model" => $device_model_name,
//         "k1" => '',
//         "k2" => '',
//         "token" => $accessToken,
//         "nodered" => 'access',
//         "attributes" => $iot_attributes, //$attributes,
//         "hlt" => $hlt,
//         "wktGeometry" => $wkt_bbox
//     );
    
//     file_put_contents('TMP_debug.log', "Data for device creation \n", FILE_APPEND);
//     file_put_contents('TMP_debug.log', json_encode($data_array) . "\n", FILE_APPEND);

//     $curl = curl_init();
//     $url = sprintf("%s?%s", $iot_directory_device, http_build_query($data_array));
//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//         'Content-Type: application/json',
//         'Authorization: Bearer ' . $accessToken
//     ));
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//     file_put_contents('TMP_debug.log', "URL for device creation \n", FILE_APPEND);
//     file_put_contents('TMP_debug.log', $url . "\n", FILE_APPEND);

//     $deviceCall = curl_exec($curl);

//     file_put_contents('TMP_debug.log', "Response of device creation \n", FILE_APPEND);
//     file_put_contents('TMP_debug.log', $deviceCall . "\n", FILE_APPEND);

//     if (curl_errno($curl)) {
//         $error_code = curl_errno($curl);
//         $error_message = curl_error($curl);
//         //echo "CURL error code: $error_code\n";
//         // echo "CURL error message: $error_message\n";
//         $info = curl_getinfo($curl);
//         //echo "CURL error info: $info\n";
//         //var_dump($url);
//         // echo "url: ".$url;
//     }
//     $response = json_decode($deviceCall, true);

//     curl_close($curl);

//     // SET DATA INTO THE DEVICE ///////////////////////////////////////////////////////////////////////////////
//     $metric_name = $_POST["metric_name"];
//     $arrayString = json_decode($_POST['data'], true);
//     $size =count($arrayString);
//     $vectorDescription = $_POST['description'];

//     if ($response->status != "ko") {
//         // IF DEVICE WAS SUCCESSFULLY CREATED /////////////////////////////////////////////////////////////////

//         // CONTINUA DA QUA... dataObserved manca, verifica il modello e controlla che tutto abbia senso!!!!!!!!!!!!!!!!!!!!!

//         // $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";

//         // TODO MAYBE TO BE ADDED >>> $date_observed
        
//         $boundingBox='{
//             "type": "Feature",
//             "geometry": {
//                 "type": "Polygon",
//                 "coordinates": [
//                     [
//                         ['.$minLat.','.$minLong.'],
//                         ['.$minLat.','.$maxLong.'],
//                         ['.$maxLat.','.$minLong.'],
//                         ['.$maxLat.','.$maxLong.']
//                     ]
//                 ]
//             }
//         }';
//         $new_attributes = '{
//             "mapName":{"value":"' . $name . '","type":"string"},
//             "colorMap":{"value":"'.$metric_name.'","type":"string"},
//             "minimumDate":{"value":"' . $minDate . '","type":"string"},
//             "maximumDate":{"value":"' . $maxDate . '","type":"string"},
//             "instances":{"value":1,"type":"integer"},
//             "description":{"value":"'.$vectorDescription.'","type":"string"},
//             "boundingBox":'.$boundingBox.',
//             "size":{"value":"'.$size.'","type":"integer"}
//         }';
//         // echo($new_attributes);
        
//         $value_array1 = [
//             "action" => "Insert_Value",
//             "id" => $name,
//             "type" => "Heatmap",
//             "contextbroker" => $iot_broker,
//             "service" => null,
//             "servicePath" => null,
//             "version" => "v2",
//             "nodered" => "access",
//             "token" => $accessToken,
//             "payload" => $new_attributes,
//         ];

//         file_put_contents('TMP_debug.log', "Data to send into device \n", FILE_APPEND);
//         file_put_contents('TMP_debug.log', json_encode($value_array1) . "\n", FILE_APPEND);

//         $curl01 = curl_init();
//         $url01 = sprintf("%s?%s", $iot_directory_device, http_build_query($value_array1));
//         // echo('$url');
//         curl_setopt($curl01, CURLOPT_URL, $url01);
//         curl_setopt($curl01, CURLOPT_HTTPHEADER, array(
//             'Content-Type: application/json',
//             'Authorization: Bearer ' . $accessToken
//         ));
//         curl_setopt($curl01, CURLOPT_RETURNTRANSFER, 1);

//         file_put_contents('TMP_debug.log', "URL for data insertion \n", FILE_APPEND);
//         file_put_contents('TMP_debug.log', $url01 . "\n", FILE_APPEND);

//         $deviceCall01 = curl_exec($curl01);

//         file_put_contents('TMP_debug.log', "Response of data insertion \n", FILE_APPEND);
//         file_put_contents('TMP_debug.log', $deviceCall01 . "\n", FILE_APPEND);

//         if ($deviceCall01 === false) {
//             die(json_encode([
//                 "status" => "error",
//                 "message" => "Errore cURL: " . curl_error($curl01),
//             ]));
//         }

//         $response01 = json_decode($deviceCall01, true);

//         if (!is_array($response01) || !isset($response01["status"])) {
//             die(json_encode([
//                 "status" => "error",
//                 "message" => "Risposta non valida dal server: " . $deviceCall01,
//             ]));
//         }

//         $response_status = $response01["status"];
//         //echo('response_status: '.$response_status);
//         $http_status = curl_getinfo($curl01, CURLINFO_HTTP_CODE);
        
//         if($http_status == 'error'){
//             $message_output['code'] = $http_status;
//             $message_output['message'] = 'Error during device creation';
//             $message_output['callback']=$response01;
//         }else{
//             $message_output['code'] = $http_status;
//             $message_output['message'] = 'Device Successfully created';
//             $message_output['callback']=$deviceCall01;
//         }
            
//         echo json_encode($message_output);

//         file_put_contents('TMP_debug.log', 'ALL DONE!!! :: ' . json_encode($message_output) . '\n', FILE_APPEND);

//     } else {

//         //echo 'HTTP Status Code: ' . $http_status . "\n";
//         $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         // echo 'Raw response: ' . $deviceCall . "\n";

//         $message_output['code'] = $http_status;
//         $message_output['message'] = $deviceCall;
//         echo json_encode($message_output);
//     }
// }

function getGeodeticCentroid($minLat, $maxLat, $minLon, $maxLon) {
    // Convert degrees to radians
    $minLatRad = deg2rad($minLat);
    $maxLatRad = deg2rad($maxLat);
    $minLonRad = deg2rad($minLon);
    $maxLonRad = deg2rad($maxLon);

    // Calculate the centroid in radians
    $x = (cos($minLatRad) * cos($minLonRad) + cos($maxLatRad) * cos($maxLonRad)) / 2;
    $y = (cos($minLatRad) * sin($minLonRad) + cos($maxLatRad) * sin($maxLonRad)) / 2;
    $z = (sin($minLatRad) + sin($maxLatRad)) / 2;

    // Normalize the vector
    $hyp = sqrt($x * $x + $y * $y);
    $centroidLatRad = atan2($z, $hyp);
    $centroidLonRad = atan2($y, $x);

    // Convert back to degrees
    $centroidLat = rad2deg($centroidLatRad);
    $centroidLon = rad2deg($centroidLonRad);

    return ['latitude' => $centroidLat, 'longitude' => $centroidLon];
}


function latLonToUTM($lat, $lon) {
    // Costanti del sistema WGS84
    $a = 6378137.0; // Semi-asse maggiore (raggio equatoriale in metri)
    $f = 1 / 298.257223563; // Schiacciamento
    $k0 = 0.9996; // Fattore di scala

    // Calcolo della zona UTM
    $zone = (int) floor(($lon + 180) / 6) + 1;

    // Longitudine centrale della zona UTM
    $lon0 = ($zone - 1) * 6 - 180 + 3; // Gradi

    // Converto lat e lon in radianti
    $latRad = deg2rad($lat);
    $lonRad = deg2rad($lon);
    $lon0Rad = deg2rad($lon0);

    // Parametri derivati
    $e = sqrt(2 * $f - $f * $f); // Eccentricità
    $n = $a / sqrt(1 - $e * $e * sin($latRad) * sin($latRad));
    $t = tan($latRad) * tan($latRad);
    $c = ($e * cos($latRad)) ** 2 / (1 - $e * $e);
    $A = cos($latRad) * ($lonRad - $lon0Rad);

    // Calcolo delle coordinate UTM
    $M = $a * (
        (1 - $e * $e / 4 - 3 * $e ** 4 / 64 - 5 * $e ** 6 / 256) * $latRad
        - (3 * $e ** 2 / 8 + 3 * $e ** 4 / 32 + 45 * $e ** 6 / 1024) * sin(2 * $latRad)
        + (15 * $e ** 4 / 256 + 45 * $e ** 6 / 1024) * sin(4 * $latRad)
        - (35 * $e ** 6 / 3072) * sin(6 * $latRad)
    );

    $x = $k0 * $n * ($A + (1 - $t + $c) * $A ** 3 / 6 + (5 - 18 * $t + $t ** 2 + 72 * $c - 58 * $e ** 2) * $A ** 5 / 120) + 500000;
    $y = $k0 * ($M + $n * tan($latRad) * ($A ** 2 / 2 + (5 - $t + 9 * $c + 4 * $c ** 2) * $A ** 4 / 24 + (61 - 58 * $t + $t ** 2 + 600 * $c - 330 * $e ** 2) * $A ** 6 / 720));

    // Aggiusta la coordinata Y per l'emisfero Sud
    if ($lat < 0) {
        $y += 10000000; // Offset per l'emisfero sud
    }

    return [
        'easting' => round($x), // Arrotonda senza decimali
        'northing' => round($y), // Arrotonda senza decimali
        'zone' => $zone,
        'hemisphere' => $lat >= 0 ? 'N' : 'S'
    ];
}
?>
