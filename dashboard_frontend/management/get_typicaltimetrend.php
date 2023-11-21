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

//$name = $_POST['name'];
include "../config.php";
session_start();
require "../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
//checkSession("RootAdmin");

$name = $_POST["name"];
$name = filter_var($name, FILTER_SANITIZE_STRING);
$name = str_replace(" ", "_", $name);

$action = $_POST["action"];
$action = filter_var($action, FILTER_SANITIZE_STRING);
$action = str_replace(" ", "_", $action);


$message_output = "";

if (isset($_SESSION["refreshToken"])) {
    $oidc = new OpenIDConnectClient(
        $ssoEndpoint,
        $ssoClientId,
        $ssoClientSecret
    );
    $oidc->providerConfigParam(["token_endpoint" => $ssoTokenEndpoint]);
    $tkn = $oidc->refreshToken($_SESSION["refreshToken"]);
    $accessToken = $tkn->access_token;
    $_SESSION["refreshToken"] = $tkn->refresh_token;
}
///////////////
$iot_directory_api = $iotDirectoryBaseApiUrl;
$iot_directory_model = $iot_directory_api . "/model.php";
$iot_directory_device = $iot_directory_api . "/device.php";
$iot_directory_value = $iot_directory_api . "/value.php";
////////////
$dataArrayForGetModel = [
    "action" => "get_model",
    "name" => "TTT-Model",
    "token" => $accessToken,
    "nodered" => "access",
];
$ch_model = curl_init();
$url_model = sprintf(
    "%s?%s",
    $iot_directory_model,
    http_build_query($dataArrayForGetModel)
);
curl_setopt($ch_model, CURLOPT_URL, $url_model);
curl_setopt($ch_model, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $accessToken,
]);
curl_setopt($ch_model, CURLOPT_RETURNTRANSFER, 1);
$modelCall = curl_exec($ch_model);
$device_model = json_decode($modelCall);
$content_model = $device_model->content;
$iot_cam_broker = $content_model->contextbroker;
$iot_org = $content_model->organization;
$iot_attributes = $content_model->attributes;
$k1 = $content_model->k1;
$k2 = $content_model->k2;
$kind = $content_model->kind;
$devicetype = $content_model->devicetype;
$format = $content_model->format;
curl_close($ch_model);
//


if ($action == "insert") {

//$value_type = $_POST['value_type'];
$value_type = $_POST["value_type"];
$value_type = filter_var($value_type, FILTER_SANITIZE_STRING);

$data_type = $_POST["data_type"];
//$data_type = mysqli_real_escape_string($link, $_POST["data_type"]);
$data_type = filter_var($data_type, FILTER_SANITIZE_STRING);

$value_unit = $_POST["value_unit"];
//$value_unit = mysqli_real_escape_string($link, $_POST["value_unit"]);
$value_unit = filter_var($value_unit, FILTER_SANITIZE_STRING);

$max_value = $_POST["max_value"];
//$max_value = mysqli_real_escape_string($link, $_POST["max_value"]);
$max_value = filter_var($max_value, FILTER_SANITIZE_STRING);

$from_date = $_POST["from_date"];
//$from_date = mysqli_real_escape_string($link, $_POST["from_date"]);
$from_date = filter_var($from_date, FILTER_SANITIZE_STRING);

$to_date = $_POST["to_date"];
//$to_date = mysqli_real_escape_string($link, $_POST["to_date"]);
$to_date = filter_var($to_date, FILTER_SANITIZE_STRING);

$values = $_POST["values"];
//$values = mysqli_real_escape_string($link, $_POST["values"]);
$values = filter_var($values, FILTER_SANITIZE_STRING);

//$referenceTo = mysqli_real_escape_string($link, $_POST["referenceTo"]);
$referenceTo = $_POST["referenceTo"];
$referenceTo = filter_var($referenceTo, FILTER_SANITIZE_STRING);

$kind = $_POST["kind"];
$kind = filter_var($kind, FILTER_SANITIZE_STRING);

$kindDetails = $_POST["kindDetails"];
$kindDetails = filter_var($kindDetails, FILTER_SANITIZE_STRING);
//

    
        //'[{"value_name":"dateObserved","data_type":"string","value_type":"timestamp","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"name","dataType":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueName","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueType","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"dataType","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueUnit","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"maxValue","data_type":"float","value_type":"max_temperature","editable":"0","value_unit":"°C","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"fromDate","data_type":"string","value_type":"datetime","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"toDate","data_type":"string","value_type":"datetime","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"kind","data_type":"string","value_type":"description","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"kindDetails","data_type":"string","value_type":"description","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"values","data_type":"json","value_type":"datastructure","editable":"0","value_unit":"complex","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"referenceTo","data_type":"string","value_type":"URL","editable":"0","value_unit":"SURI","healthiness_criteria":"refresh_rate","healthiness_value":"300"}]';
    $attributes =   '[{"value_name":"kindDetails","data_type":"string","value_type":"description","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"dateObserved","data_type":"string","value_type":"timestamp","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"name","data_type":"string","value_type":"Identifier","editable":"0","value_unit":"ID","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueName","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueType","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"dataType","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"valueUnit","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"maxValue","data_type":"float","value_type":"max_temperature","editable":"0","value_unit":"°C","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"fromDate","data_type":"string","value_type":"datetime","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"toDate","data_type":"string","value_type":"datetime","editable":"0","value_unit":"timestamp","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"kind","data_type":"string","value_type":"description","editable":"0","value_unit":"text","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"values","data_type":"json","value_type":"datastructure","editable":"0","value_unit":"complex","healthiness_criteria":"refresh_rate","healthiness_value":"300"},{"value_name":"referenceTo","data_type":"string","value_type":"URL","editable":"0","value_unit":"SURI","healthiness_criteria":"refresh_rate","healthiness_value":"300"}]';

    $data_array = [
        "action" => "insert",
        "id" => $name,
        "type" => $devicetype,
        "contextbroker" => $iot_cam_broker,
        "kind" => "sensor",
        "format" => "json",
        "latitude" => "43.76276",
        "longitude" => "11.26923",
        "frequency" => "600",
        "producer" => "",
        "model" => "TTT-Model",
        "k1" => "",
        "k2" => "",
        "token" => $accessToken,
        "nodered" => "access",
        "attributes" => $attributes,
    ];
    $data_array0 = json_encode($data_array);

    $curl = curl_init();
    $url = sprintf(
        "%s?%s",
        $iot_directory_device,
        http_build_query($data_array)
    );
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken,
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $deviceCall = curl_exec($curl);

    if ($deviceCall === false) {
        echo "Errore cURL: " . curl_error($curl);
    }

    $response = json_decode($deviceCall, true);
    //
    if (curl_errno($curl)) {
        echo 'Errore cURL: ' . curl_error($curl);
    }
    $info = curl_getinfo($curl);
        if ($info['http_code'] >= 400) {
            echo 'Errore HTTP: ' . $info['http_code'];
            var_dump($info);
        }

        if ($deviceCall === false) {
            echo "Errore cURL: " . curl_error($curl);
        }
    //
    curl_close($curl);
    ////////////////////////////////////////
    if ($response) {
        //
        $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";
//{"value_name":"valueName","data_type":"string","value_type":"status","editable":"0","value_unit":"status","healthiness_criteria":"refresh_rate","healthiness_value":"300"}
        $new_attributes =  '{"name":{"value":"'.$name.'", "type":"string"}, "dateObserved":{"value":"'.$date_observed.'", "type":"string"}, "dataType":{"value":"'.$data_type.'","type":"string"},"kind":{"value":"'.$kind.'","type":"string"},"toDate":{"value":"' .   $to_date . '","type":"string"},"fromDate":{"value":"' .   $from_date . '","type":"string"}, "valueName":{"value":"'.$name.'","type":"string"}, "valueUnit":{"value":"'.$value_unit.'","type":"string"}, "valueType":{"value":"'.$value_type.'","type":"string"}, "values":{"value": '.$values.',"type":"json"}, "maxValue":{"value":"'.$max_value.'","type":"float"}, "longitude":{"type": "float", "value": "11.26923"}, "latitude":{"type": "float", "value": "43.76276"}, "kindDetails":{"value":"'.$kindDetails.'", "type":"string"}}';

        echo($new_attributes);
        ///
        $value_array1 = [
            "action" => "Insert_Value",
            "id" => $name,
            "type" => $devicetype,
            "contextbroker"=> $iot_cam_broker,
            "service" => null,
            "servicePath" => null,
            "version" => "v2",
            "nodered" => "access",
            "token" => $accessToken,
            "payload" => $new_attributes
        ];
        $curl01 = curl_init();
        $url01 = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($value_array1)
        );
        // echo('$url');
        curl_setopt($curl01, CURLOPT_URL, $url01);
        curl_setopt($curl01, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl01, CURLOPT_RETURNTRANSFER, 1);
        $deviceCall01 = curl_exec($curl01);

        if (curl_errno($curl01)) {
            echo 'Errore cURL: ' . curl_error($curl01);
        }

        $info = curl_getinfo($curl01);
        if ($info['http_code'] >= 400) {
            echo 'Errore HTTP: ' . $info['http_code'];
            var_dump($info);
        }

        if ($deviceCall01 === false) {
            echo "Errore cURL: " . curl_error($curl01);
        }

        // Se non ci sono errori o avvertimenti, elabora la risposta
            if (empty(curl_error($curl01)) && $info['http_code'] < 400) {
                // Elabora la risposta qui
                echo $deviceCall01;
            }

        $response01 = json_decode($deviceCall01, true);
        $response_status = $deviceCall01->status;
        //
        $message_output["code"] = "200";
        $message_output["message"] = "Device Successfully created";
        //
        //echo json_encode($message_output);
        //
    }
} else if ($action == "delete") {
    $data_array = [
        "action" => "delete",
        "id" => $name,
        "type" => $devicetype,
        "contextbroker" => $iot_cam_broker,
        "kind" => "sensor",
        "format" => "json",
        //"latitude" => "43.76276",
        //"longitude" => "11.26923",
        "frequency" => "600",
        "producer" => "",
        "model" => "TTT-Model",
        "k1" => "",
        "k2" => "",
        "token" => $accessToken,
        "nodered" => "access",
        //"attributes" => $attributes
    ];
    $data_array0 = json_encode($data_array);

    $curl = curl_init();
    $url = sprintf(
        "%s?%s",
        $iot_directory_device,
        http_build_query($data_array)
    );
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken,
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $deviceCall = curl_exec($curl);

    if ($deviceCall === false) {
        echo "Errore cURL: " . curl_error($curl);
    }

    $response = json_decode($deviceCall, true);

    $status = $response["status"];
    $message_output = $status;
    //
    //echo($status);
    //
   // $message_output["code"] = "200";
    curl_close($curl);
    echo json_encode($message_output);

}else {
    echo "NO REQUEST";
}
//////////////////////////
?>
