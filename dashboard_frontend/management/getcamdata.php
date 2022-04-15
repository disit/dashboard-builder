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

session_start();
include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

header('Access-Control-Allow-Origin: *');
error_reporting(E_ERROR);
checkSession('RootAdmin');
//
$message_output = "";

if (isset($_SESSION['refreshToken'])) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;
}
///////////////

$iot_directory_model = $iot_directory_api . '/model.php';
$iot_directory_device = $iot_directory_api . '/device.php';
$iot_directory_value = $iot_directory_api . '/value.php';

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$action = mysqli_real_escape_string($link, $_REQUEST['action']);
$action = filter_var($action, FILTER_SANITIZE_STRING);
if ($action == 'get_cam') {
    $model_array = explode(',', $iot_cam_model);
    $count_models = count($model_array);

    for ($y = 0; $y < $count_models; $y++) {
        $active_model = $model_array[$y];


    $dataArrayForGetModel = array("action" => 'get_model',
        "name" => $active_model,
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


    $serviceuri = '';
    if (isset($_REQUEST['serviceuri'])) {
        $serviceuri = mysqli_real_escape_string($link, $_REQUEST['serviceuri']);
        $serviceuri = filter_var($serviceuri, FILTER_SANITIZE_STRING);
    }

    $devices_list = array();
    //
    $service_uri = "";
    $query_cams = "SELECT * FROM camdata";
    $result_cams = mysqli_query($link, $query_cams) or die(mysqli_error($link));
    $i = 0;
    if ($result_cams) {
        while ($row_c = mysqli_fetch_assoc($result_cams)) {
            $name = $row_c['name'];
            $service_uri = $row_c['serviceuri'];
            //
            if (($service_uri !== "") && ($service_uri !== NULL)) {
                //
                //OLD SERVICE MAP
                //$link_to_uri =          $superServiceMapUrlPrefix."api/v1/?serviceUri=" . $service_uri . "&format=json&accessToken=".$accessToken;
                //superserivemap_curl
                //$res_sm = file_get_contents($link_to_uri, true);
               //$json_data = json_decode($res_sm);
                
                //NEW SERVICEMAP
                $ch_ssm = curl_init();
                $link_to_uri = $superServiceMapUrlPrefix."api/v1/?serviceUri=" . $service_uri . "&format=json";
                curl_setopt($ch_ssm, CURLOPT_URL, $link_to_uri);
                curl_setopt($ch_ssm, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ));
                curl_setopt($ch_ssm, CURLOPT_RETURNTRANSFER, 1);
                $ssmCall = curl_exec($ch_ssm);
                $json_data = json_decode($ssmCall);
                curl_close($ch_ssm);

                if (isset($json_data->failure)) {
                    //echo($name.'> failure');
                    // do something
                    $cam = array(
                        "id" => $i,
                        "name" => $name,
                        "description" => '',
                        "nature" => '',
                        "subnature" => $iot_subnature,
                        "latitude" => '',
                        "longitude" => '',
                        "videosource" => $videoSource,
                        "username" => $row_c['username'],
                        "password" => $row_c['password'],
                        "serviceURI" => $service_uri,
                        "organizations" => '',
                        "contextbroker" => '',
                        "model" => $active_model
                    );
                    //
                } else {
                    //print_r($json_data);
                    $service = $json_data->Service;
                    $feat = $service->features;
                    $properties = $feat[0]->properties;
                    $geometry = $feat[0]->geometry;
                    $coord = $geometry->coordinates;
                    //
                    //$properties1= json_decode($properties);
                    //print_r($properties1);
                    //
                    $rt = $json_data->realtime;
                    $rt_res = $rt->results;
                    $rt_bind = $rt_res->bindings;
                    //
                    $rt_cont= $rt_bind[0];
                    //
                    $dateObserved = $rt_cont;
                    $videoSource = $rt_cont->videoSource;
                    $description = $rt_cont->description;
                    /////////
                    $cam = array(
                        "id" => $i,
                        "name" => $name,
                        "description" => $description->value,
                        "nature" => '',
                        "subnature" => $properties->subnature,
                        "latitude" => $coord[1],
                        "longitude" => $coord[0],
                        "videosource" => $videoSource->value,
                        "username" => $row_c['username'],
                        "password" => $row_c['password'],
                        "serviceURI" => $service_uri,
                        "organizations" => $properties->organization,
                        "contextbroker" => $properties->brokerName,
                        "model" => $active_model
                    );
                    ///////////
                }
                //var_dump($properties);
                array_push($devices_list, $cam);
            } else {
                //

                $data_array = array(
                    "action" => 'get_device',
                    "id" => $name,
                    "model" => $active_model,
                    "contextbroker" => $iot_cam_broker,
                    "nodered" => 'access'
                );

                $curl = curl_init();
                $url = sprintf("%s?%s", $iot_directory_device, http_build_query($data_array));

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $deviceCall = curl_exec($curl);
                //
                $device = json_decode($deviceCall);
                //
                //
                $device0 = $device->status;
                $content = $device->content;
                if ($device0 == 'ok') {
                    ///////////////////ATTRIBUTES VALUE//////////////
                    $value_array = array(
                        "action" => 'get_device_data',
                        "id" => $name,
                        "type" => 'stream',
                        "contextbroker" => $iot_cam_broker,
                        "version" => 'v1',
                        "nodered" => 'access'
                    );
                    $ch1 = curl_init();

                    $url01 = sprintf("%s?%s", $iot_directory_device, http_build_query($value_array));

                    curl_setopt($ch1, CURLOPT_URL, $url01);
                    curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $accessToken
                    ));
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                    $deviceCall01 = curl_exec($ch1);
                    $response0 = json_decode($deviceCall01, true);
                    //$var_length = count($response0['attributes']);
                    //
                    //$dateObserved = "";
                    //$videoSource = "";
                    //$description = "";
                    //
                    /* for ($x = 0; $x < $var_length; $x++) {
                      $current_attr = $response0['attributes'][$x]['name'];
                      if ($current_attr == 'dateObserved') {
                      $dateObserved = $response0['attributes'][$x]['value'];
                      }
                      if ($current_attr == 'videoSource') {
                      $videoSource = $response0['attributes'][$x]['value'];
                      }
                      if ($current_attr == 'description') {
                      $description = $response0['attributes'][$x]['value'];
                      }
                      } */

                    $dateObserved = $response0['attributes'][0]['value'];
                    $videoSource = $response0['attributes'][5]['value'];
                    $description = $response0['attributes'][1]['value'];

                    curl_close($ch1);
                    //////////////////
                    $cam = array(
                        "id" => $i,
                        "name" => $name,
                        "description" => $description,
                        "nature" => '',
                        "subnature" => $iot_subnature,
                        "latitude" => $content->latitude,
                        "longitude" => $content->longitude,
                        "videosource" => $videoSource,
                        "username" => $row_c['username'],
                        "password" => $row_c['password'],
                        "serviceURI" => $content->uri,
                        "organizations" => $content->organization,
                        "contextbroker" => $iot_cam_broker,
                        //
                        //"devicetype" => $content->devicetype,
                        //"kind" => $content->kind,
                        //"status1" => $content->status1,
                        "model" => $active_model
                            //"protocol" => $content->protocol,
                            //"format" => $content->format,
                            //"frequency" => $content->frequency,
                            //"k1" => $content->k1,
                            //"k2" => $content->k2,
                            //"content" => $content
                    );
                    $current_su = $cam['serviceURI'];
                    curl_close($curl);
                    //$devices_list[$i]=$cam;
                    if ($serviceuri != "") {
                        if ($serviceuri == $current_su) {
                            array_push($devices_list, $cam);
                        }
                    } else {
                        array_push($devices_list, $cam);
                    }
                }
            }
            $i++;
            $element_in_array = count($devices_list);
            if ($element_in_array == 0) {
                $message_output['message'] = 'not founded devices';
            }
        }
    } else {
        $message_output['message'] = 'List of cams in DB is empty';
    }
    }
    $message_output['code'] = '200';
    $message_output['status'] = 'OK';
    $message_output['data'] = $devices_list;
    echo json_encode($message_output);
    //
} else if ($action == 'new_cam') {
    $latitude = mysqli_real_escape_string($link, $_POST['latitude']);
    $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);

    $longitude = mysqli_real_escape_string($link, $_POST['longitude']);
    $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);

    $name = mysqli_real_escape_string($link, $_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $name = str_replace(' ', '_', $name);

    $description = mysqli_real_escape_string($link, $_POST['description']);
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $description = str_replace(' ', '_', $description);

    $username = mysqli_real_escape_string($link, $_POST['username']);
    $username = filter_var($username, FILTER_SANITIZE_STRING);

    $password = mysqli_real_escape_string($link, $_POST['password']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $videosource = mysqli_real_escape_string($link, $_POST['videosource']);
    $videosource = filter_var($videosource, FILTER_SANITIZE_STRING);



    $org = mysqli_real_escape_string($link, $_POST['org']);
    $org = filter_var($org, FILTER_SANITIZE_STRING);
    //
    $model = mysqli_real_escape_string($link, $_POST['model']);
    $model = filter_var($model, FILTER_SANITIZE_STRING);

    
    ////////////
    $dataArrayForGetModel = array("action" => 'get_model',
        "name" => $model,
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
    $device_model = json_decode($modelCall);
    $content_model = $device_model->content;
    $iot_cam_broker = $content_model->contextbroker;
    $iot_org = $content_model->organization;
    $iot_attributes = $content_model->attributes;
    $k1 =  $content_model->k1;
    $k2 =  $content_model->k2;
    $kind = $content_model->kind;
    $devicetype =  $content_model->devicetype; 
    $format =   $content_model->format; 
    curl_close($ch_model);
    //
    $attributes = '[{ "value_name": "dateObserved", "data_type": "string", "value_type": "timestamp",  "editable": "0", "value_unit": "milliseconds", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "name", "data_type": "string", "value_type": "entity_desc",  "editable": "0", "value_unit": "text", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "description", "data_type": "string", "value_type": "entity_desc",  "editable": "0", "value_unit": "text", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "videoSource", "data_type": "string", "value_type": "URI",  "editable": "0", "value_unit": "SURI", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" }]';
    
        $data_array = array(
            "action" => 'insert',
            "id" => $name,
            "type" => $devicetype,
            "contextbroker" => $iot_cam_broker,
            "kind" => $kind,
            "format" => $format,
            //"subnature" => $subnature,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "frequency" => '600',
            "producer" => '',
            "model" => $model,
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
        $response = json_decode($deviceCall, true);
        //
        curl_close($curl);
        ////////////////////////////////////////
        if ($response) {
            /////////
            $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";

            $new_attributes = '{"description":{"value":"' . $description . '","type":"string"},"videoSource":{"value":"' . $videosource . '","type":"string"},"name":{"value":"' . $name . '","type":"string"},"dateObserved":{"value":"' . $date_observed . '","type":"string"}}';
            $value_array1 = array(
                "action" => 'Insert_Value',
                "id" => $name,
                "type" => 'stream',
                "contextbroker" => $iot_cam_broker,
                "service" => null,
                "servicePath" => null,
                "version" => 'v2',
                "nodered" => 'access',
                "token" => $accessToken,
                "payload" => $new_attributes
            );
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
            $response01 = json_decode($deviceCall01, true);
            $response_status = $deviceCall01->status;
            //
                $message_output['code'] = '200';
                $message_output['message'] = 'Device Successfully created';
                //
                //RETRIEVE $serviceuri
                $serviceuri = "";
                $data_array = array(
                    "action" => 'get_device',
                    "id" => $name,
                    "model" => $active_model,
                    "contextbroker" => $iot_cam_broker,
                    "nodered" => 'access'
                );

                $curl_su = curl_init();
                $url_su = sprintf("%s?%s", $iot_directory_device, http_build_query($data_array));

                curl_setopt($curl_su, CURLOPT_URL, $url_su);
                curl_setopt($curl_su, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ));
                curl_setopt($curl_su, CURLOPT_RETURNTRANSFER, 1);
                $deviceCall = curl_exec($curl_su);
                $device = json_decode($deviceCall);
                //
                //
                $device0 = $device->status;
                $content = $device->content;
		$serviceuri = $content->uri;		
                curl_close($curl_su);
                        //
                        $query = "INSERT INTO camdata (name, username, password, serviceuri) VALUES ('" . $name . "', '" . $username . "', '" . $password . "', '".$serviceuri."')";
                        $result = mysqli_query($link, $query) or die(mysqli_error($link));
                        if ($result) {
                            $message_output['code'] = '200';
                                    $message_output['message'] = 'Cam successfully inserted in Database';
                                   // echo json_encode($message_output);
                        } else {
                            $message_output['code'] = '404';
                            $message_output['message'] = 'error during cam insert in database';
                            //echo json_encode($message_output);
                        }
                //
                echo json_encode($message_output);
        } else {
            $message_output['code'] = '404';
            $message_output['message'] = 'error during device creation by api';
            echo json_encode($message_output);
        }

} else if ($action == 'delete_cam') {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $id = mysqli_real_escape_string($link, $_POST['id']);
    $id = filter_var($id, FILTER_SANITIZE_STRING);

    $broker = mysqli_real_escape_string($link, $_POST['broker']);
    $broker = filter_var($broker, FILTER_SANITIZE_STRING);

    $query = "DELETE FROM camdata WHERE name='" . $name . "'";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        //////////////////////
        $data_array = array(
            "action" => 'delete',
            "id" => $name,
            "contextbroker" => $broker,
            "nodered" => 'access'
        );
        $curl = curl_init();
        $url = sprintf("%s?%s", $iot_directory_device, http_build_query($data_array));
        // echo('$url');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $deviceCall = curl_exec($curl);
        $response = json_decode($deviceCall, true);
        //
        $message_output['code'] = '200';
        $message_output['message'] = 'Device successfully deleted';
        echo json_encode($message_output);
        //
    } else {
        $message_output['code'] = '404';
        $message_output['message'] = 'Error during deletion from database';
        echo json_encode($message_output);
    }
    /////////////////////////
} else if ($action == 'edit_cam') {
    $id = mysqli_real_escape_string($link, $_POST['id']);
    $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

    $latitude = mysqli_real_escape_string($link, $_POST['latitude']);
    $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);

    $longitude = mysqli_real_escape_string($link, $_POST['longitude']);
    $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);

    $name = mysqli_real_escape_string($link, $_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $name = str_replace(' ', '_', $name);

    $description = mysqli_real_escape_string($link, $_POST['description']);
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $description = str_replace(' ', '_', $description);

    $username = mysqli_real_escape_string($link, $_POST['username']);
    $username = filter_var($username, FILTER_SANITIZE_STRING);

    $password = mysqli_real_escape_string($link, $_POST['password']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $videosource = mysqli_real_escape_string($link, $_POST['videosource']);
    $videosource = filter_var($videosource, FILTER_SANITIZE_STRING);

    $broker = mysqli_real_escape_string($link, $_POST['broker']);
    $broker = filter_var($broker, FILTER_SANITIZE_STRING);
    /////////////////////
    $model = mysqli_real_escape_string($link, $_POST['model']);
    $model = filter_var($model, FILTER_SANITIZE_STRING);


    $dataArrayForGetModel = array("action" => 'get_model',
        "name" => $model,
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
    $device_model = json_decode($modelCall);
    $content_model = $device_model->content;
    $iot_cam_broker = $content_model->contextbroker;
    $iot_org = $content_model->organization;
    $iot_attributes = $content_model->attributes;
        $k1 =  $content_model->k1;
        $k2 =  $content_model->k2;
    $kind = $content_model->kind;
        $devicetype =  $content_model->devicetype; 
        $format =   $content_model->format; 
    curl_close($ch_model);

    $query = "UPDATE camdata SET username='" . $username . "', password='" . $password . "' WHERE name='" . $name . "'";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    //
    $attributes = '[{ "value_name": "dateObserved", "data_type": "string", "value_type": "timestamp",  "editable": "0", "value_unit": "milliseconds", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "name", "data_type": "string", "value_type": "entity_desc",  "editable": "0", "value_unit": "text", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "description", "data_type": "string", "value_type": "entity_desc",  "editable": "0", "value_unit": "text", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" },{ "value_name": "videoSource", "data_type": "string", "value_type": "URI",  "editable": "0", "value_unit": "SURI", "healthiness_criteria": "refresh_rate", "healthiness_value": "300" }]';
    //
    if ($result) {

        ////////////////
        $data_array = array(
            "action" => 'update',
            "id" => $name,
            "type" => $devicetype,
            "contextbroker" => $broker,
            "kind" => $kind,
            "format" => $format,
            "gb_old_cb" => $broker,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "frequency" => '600',
            "producer" => '',
            "model" => $model,
            "k1" => '',
            "k2" => '',
            "token" => $accessToken,
            "nodered" => 'access',
            "attributes" => $attributes,
            "newattributes" => '[]'
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
        $response = json_decode($deviceCall, true);
        //
        curl_close($curl);
        //////////////////
        /////////
        if ($deviceCall) {
            $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";
            $new_attributes = '{"description":{"value":"' . $description . '","type":"string"},"videoSource":{"value":"' . $videosource . '","type":"string"},"name":{"value":"' . $name . '","type":"string"},"dateObserved":{"value":"' . $date_observed . '","type":"string"}}';
            //echo($new_attributes);
            $value_array = array(
                "action" => 'Insert_Value',
                "id" => $name,
                "type" => 'stream',
                "contextbroker" => $broker,
                "service" => null,
                "servicePath" => null,
                "version" => 'v2',
                "nodered" => 'access',
                "token" => $accessToken,
                "payload" => $new_attributes
            );
            $curl_v = curl_init();
            $url_v = sprintf("%s?%s", $iot_directory_device, http_build_query($value_array));
            // echo('$url');
            curl_setopt($curl_v, CURLOPT_URL, $url_v);
            curl_setopt($curl_v, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ));
            curl_setopt($curl_v, CURLOPT_RETURNTRANSFER, 1);
            $valueCall = curl_exec($curl_v);
            $valueResponse = json_decode($valueCall, true);
            //
            $message_output['code'] = '200';
            $message_output['message'] = 'Device successfully modified';
            echo json_encode($message_output);
            //
        } else {
            $message_output['code'] = '404';
            $message_output['message'] = 'Error during editing device by API';
            echo json_encode($message_output);
        }
    } else {
        $message_output['code'] = '404';
        $message_output['message'] = 'Error during editing data in database';
        echo json_encode($message_output);
    }
    /////////////////EDIT VALUE.php/////
} else if ($action == 'get_models') {
    $model_array = explode(',', $iot_cam_model);
    echo json_encode($model_array);
} else if ($action == 'delegate_user') {
    $id = mysqli_real_escape_string($link, $_POST['id']);
    $id = filter_var($id, FILTER_SANITIZE_STRING);
    //
    $organization = mysqli_real_escape_string($link, $_POST['org']);
    $organization = filter_var($organization, FILTER_SANITIZE_STRING);
    //
    $contextbroker = mysqli_real_escape_string($link, $_POST['cb']);
    $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
    //
    $elementId = $organization . ':' . $contextbroker . ':' . $id;
    //
    $usernameDelegated = mysqli_real_escape_string($link, $_POST['usernameDelegated']);
    $usernameDelegated = filter_var($usernameDelegated, FILTER_SANITIZE_STRING);
    //
    if (isset($_SESSION['refreshToken'])) {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;
        $usernameDelegator = $_SESSION["loggedUsername"];
        //DELEGATE
        $url_api = $personalDataApiBaseUrl . '/v1/username/' . $usernameDelegator . '/delegation?accessToken=' . $accessToken . '&sourceRequest=IOTID';
        //
        $callBody = ["usernameDelegated" => $usernameDelegated, "usernameDelegator" => $usernameDelegator, "elementId" => $elementId, "elementType" => "IOTID"];
        $options = array(
            'http' => array(
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'timeout' => 30,
                'content' => json_encode($callBody),
                'ignore_errors' => true
            )
        );
        $context = stream_context_create($options);
       $callResult = file_get_contents($url_api, false, $context);
        //
        //print_r($callResult);
        //
        if (strpos($http_response_header[0], '200') !== false) {
            //echo('Ok');
            $message_output['code'] = '200';
            $message_output['message'] = 'Device successfully delegated';
            echo json_encode($message_output);
            //
        } else {
            //echo('ApiCallKo1');
            $message_output['code'] = '404';
            $message_output['message'] = 'Error during delegation';
            echo json_encode($message_output);
            //print_r($http_response_header[0]);
        }
    }
    ////////
} else {
    $message_output['code'] = '404';
    $message_output['message'] = 'Required action parameter';
    echo json_encode($message_output);
}
?>