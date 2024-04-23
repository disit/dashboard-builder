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

//This file has been edited to also allow the operations by simply providing the accesstoken instead of only using a session.
//It is the responsability of those who call these APIs to provide the tokens should the session not be used.
//If no session is set and no token is provided, the result is an empty webpage
//If an accesstoken is provided, it will override the operations related to the session.

include "../config.php";
require "../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;

session_start();
header("Access-Control-Allow-Origin: *");
error_reporting(E_ERROR);
//commented to enable all users to use the service
//checkSession("AreaManager");
$iot_contextbroker = $contextbroker_filemanager;
$model = $model_filemanager;
$processloader_uri = $processloader_uri_filemanager;
define("CHUNK_SIZE", 1024 * 1024);

if (isset($_SESSION["refreshToken"]) || isset($_POST["accessToken"])) {
    $oidc = new OpenIDConnectClient(
        $ssoEndpoint,
        $ssoClientId,
        $ssoClientSecret
    );
    $oidc->providerConfigParam([
        "token_endpoint" => $ssoTokenEndpoint,
    ]);
    if (isset($_POST["accessToken"])) {
        $accessToken = $_POST["accessToken"];
    }
    else {
        $tkn = $oidc->refreshToken($_SESSION["refreshToken"]);
        $accessToken = $tkn->access_token;
        $_SESSION["refreshToken"] = $tkn->refresh_token;
    }
}
if (isset($_SESSION["loggedRole"]) || isset($_POST["accessToken"])) {
    $iot_directory_model = $iot_directory_api . "/model.php";
    $iot_directory_device = $iot_directory_api . "/device.php";
    $iot_directory_ldap = $iot_directory_api . "/ldap.php";

    $message_output["message"] = "";
    $message_output["code"] = "";
    $message_output["status"] = "";
    $message_output["content"] = "";

    //open new connection to mysql server
    $link = mysqli_connect($host, $username, $password);

    if (isset($_REQUEST["action"]) && !empty($_REQUEST["action"])) {
        $action = mysqli_real_escape_string($link, $_REQUEST["action"]);
        $action = filter_var($action, FILTER_SANITIZE_STRING);
    } else {
        $message_output["code"] = "404";
        $message_output["message"] = "Required action parameter";
        echo json_encode($message_output);
        mysqli_close($link);
        exit();
    }

    if ($action == "list_files") {
        $data_array = [
            "action" => "get_all_device",
            "model" => $model,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $ch = curl_init();
        $debug= '';
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($data_array)
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $apiCall = curl_exec($ch);
    	if ($apiCall) {
		    $apiArray = json_decode($apiCall, true);
	        if(!apiArray) {
		        $message_output["code"] = "404";
			    $message_output["message"] = "failed get_all_device";
			    $message_output["extra"] = $apiCall;
                echo json_encode($message_output);
                exit();
	        }
            $data = $apiArray["data"];
            $devices_list = [];
            $length = count($data);
            for ($i = 0; $i < $length; $i++) {
                $current_device = $data[$i];
                if ($current_device["devicetype"] == "File") {
                    $deviceid = $current_device["id"];
                    $value_array = [
                        "action" => "get_device_data",
                        "id" => $deviceid,
                        "type" => "File",
                        "contextbroker" => $iot_contextbroker,
                        "version" => "v1",
                        "nodered" => "access",
                    ];
                    $ch1 = curl_init();

                    $url01 = sprintf(
                        "%s?%s",
                        $iot_directory_device,
                        http_build_query($value_array)
                    );
                    curl_setopt($ch1, CURLOPT_URL, $url01);
                    curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $accessToken,
                    ]);
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
		            $deviceCall01 = curl_exec($ch1);
                    if ($deviceCall01){
                        $response0 = json_decode($deviceCall01, true);
                        $var_length = count($response0["attributes"]);
                        $dateObserved = "";
                        $newfileid = "";
                        $description = "";
                        $originalfilename = "";
                        $filesize = "";
                        $language = "";
                        $filetype = "";
                        for ($x = 0; $x < $var_length; $x++) {
                            $current_attr = $response0["attributes"][$x]["name"];
                            if ($current_attr == "dateObserved") {
                                $dateObserved = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "description") {
                                $description = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "originalfilename") {
                                $originalfilename = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "filesize") {
                                $filesize = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "language") {
                                $language = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "newfileid") {
                                $newfileid = $response0["attributes"][$x]["value"];
                            }
                            if ($current_attr == "filetype") {
                                $filetype = $response0["attributes"][$x]["value"];
                            }
                        }
                    } else {
                        $message_output["code"] = "404";
                        $message_output["status"] = "KO";
                        $message_output["message"] = "Error loading devices attributes";
                        echo json_encode($message_output);
                        die();
                    }
                    curl_close($ch1);
                    $file = [
                        "deviceid" => $current_device["id"],
                        "filename" => $originalfilename,
                        "description" => $description,
                        "subnature" => $current_device["subnature"],
                        "language" => $language,
                        "filesize" => $filesize,
                        "latitude" => $current_device["latitude"],
                        "longitude" => $current_device["longitude"],
                        "newfileid" => $newfileid,
                        "date" => $dateObserved,
                        "filetype" => $filetype,
                        "visibility" => $current_device["visibility"],
                        "organization" => $current_device["organization"],
                        "contextbroker" => $current_device["contextBroker"],
                    ];
        			array_push($devices_list, $file);
                }
            }
		    $message_output["code"] = "200";
            $message_output["status"] = "OK";
            $message_output["message"] = "get devices successfully";
            $message_output["data"] = $devices_list;
            //$message_output["debug"] = $debug;
            echo json_encode($message_output);
            curl_close($ch);
        } else {
            $message_output["code"] = "404";
            $message_output["status"] = "KO";
            $message_output["message"] = "Devices not found";
            echo json_encode($message_output);
        }
    } elseif ($action == "get_my_files") {
        $devices_list = [];
        $dataArray = [
            "action" => "get_all_device",
            "model" => $model,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $apiCall = "";
        $ch = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $apiCall = curl_exec($ch);
        $apiArray = json_decode($apiCall, true);
        $data = $apiArray["data"];
        $length = count($data);
        $debug = $length.'-';
        for ($i = 0; $i < $length; $i++) {
            $current_device = $data[$i];
            $debug = $debug . $current_device["devicetype"] . '-';
            if ($current_device["devicetype"] == "File") {
                $deviceid = $current_device["id"];
                $value_array = [
                    "action" => "get_device_data",
                    "id" => $deviceid,
                    "type" => "file",
                    "contextbroker" => $iot_contextbroker,
                    "version" => "v1",
                    "nodered" => "access",
                ];
                $ch1 = curl_init();
                $url01 = sprintf(
                    "%s?%s",
                    $iot_directory_device,
                    http_build_query($value_array)
                );
                curl_setopt($ch1, CURLOPT_URL, $url01);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $accessToken,
                ]);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                $deviceCall01 = curl_exec($ch1);
                $response0 = json_decode($deviceCall01, true);
                $var_length = count($response0["attributes"]);
                $dateObserved = "";
                $newfileid = "";
                $description = "";
                $originalfilename = "";
                $filesize = "";
                $language = "";
                $filetype = "";
                for ($x = 0; $x < $var_length; $x++) {
                    $current_attr = $response0["attributes"][$x]["name"];
                    if ($current_attr == "dateObserved") {
                        $dateObserved = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "description") {
                        $description = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "originalfilename") {
                        $originalfilename = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "filesize") {
                        $filesize = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "language") {
                        $language = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "newfileid") {
                        $newfileid = $response0["attributes"][$x]["value"];
                    }
                    if ($current_attr == "filetype") {
                        $filetype = $response0["attributes"][$x]["value"];
                    }
                }
                curl_close($ch1);
                $file = [
                    "deviceid" => $current_device["id"],
                    "filename" => $originalfilename,
                    "description" => $description,
                    "subnature" => $current_device["subnature"],
                    "language" => $language,
                    "filesize" => $filesize,
                    "latitude" => $current_device["latitude"],
                    "longitude" => $current_device["longitude"],
                    "newfileid" => $newfileid,
                    "date" => $dateObserved,
                    "filetype" => $filetype,
                    "visibility" => $current_device["visibility"],
                    "organization" => $current_device["organization"],
                    "contextbroker" => $current_device["contextBroker"],
                ];
                array_push($devices_list, $file);
            }
            $element_in_array = count($devices_list);
            if ($element_in_array == 0) {
                $message_output["code"] = "404";
                $message_output["message"] = "devices not found";
                //$message_output["debug"] = $debug;
                echo json_encode($message_output);
                exit();
            }
        }
        $message_output['message'] = $devices_list;
        if ($apiCall) {
            $message_output["status"] = "ok";
            $message_output["code"] = "200";
            echo json_encode($message_output);
        } else {
            $message_output["status"] = "ko";
            $message_output["code"] = "501";
            $mesagge_output["message"] = $apiCall;
            echo json_encode($message_output);
        }
        curl_close($ch);
    } elseif ($action == "upload_file") {
        $latitude = mysqli_real_escape_string($link, $_POST["latitude"]);
        $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);
        $longitude = mysqli_real_escape_string($link, $_POST["longitude"]);
        $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);
        $description = mysqli_real_escape_string($link, $_POST["description"]);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        $description = str_replace(" ", "_", $description);
        $language = mysqli_real_escape_string($link, $_POST["language"]);
        $language = filter_var($language, FILTER_SANITIZE_STRING);
        $subnature = mysqli_real_escape_string($link, $_POST["subnature"]);
        $subnature = filter_var($subnature, FILTER_SANITIZE_STRING);
        $filetype = mysqli_real_escape_string($link, $_POST["filetype"]);
        $filetype = filter_var($filetype, FILTER_SANITIZE_STRING);
        $file_tmp_path = $_FILES["new_file"]["tmp_name"];
        $originalfilename = $_FILES["new_file"]["name"];
        $filesize = $_FILES["new_file"]["size"];
        $filesize = human_filesize($filesize);
        $ext = pathinfo($originalfilename, PATHINFO_EXTENSION);
        mysqli_select_db($link, $dbname);
        $query_extensions = "SELECT extension FROM fileextensions";
        ($result_extensions = mysqli_query($link, $query_extensions)) or
            die(mysqli_error($link));
        $allowed_extensions = [];
        while ($row = mysqli_fetch_assoc($result_extensions)) {
            array_push($allowed_extensions, $row["extension"]);
        }
        if (!in_array($ext, $allowed_extensions)) {
            $message_output["code"] = "404";
            $message_output["message"] = "not allowed file extension";
        } else {
            if (!file_exists($protecteduploads_directory)) {
                mkdir($protecteduploads_directory, 0333);
                chmod($protecteduploads_directory, 0333);
            }
            $new_fileid = uniqid() . "-" . time();
            $upload_dir = $protecteduploads_directory . $new_fileid;
            while (file_exists($upload_dir)) {
                $new_fileid = uniqid() . "-" . time();
                $upload_dir = $protecteduploads_directory . $new_fileid;
            }
            $filename = $new_fileid . "." . $ext;
            mkdir($upload_dir);
            chmod($upload_dir, 0333); // no read permissions
            $filepath = $protecteduploads_directory . "/" . $filename;
            if (move_uploaded_file($file_tmp_path, $filepath)) {
                chmod($filepath, 0700);
                $dataArrayForGetModel = [
                    "action" => "get_model",
                    "name" => $model,
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
                $info = curl_getinfo($ch_model);
                curl_setopt($ch_model, CURLOPT_RETURNTRANSFER, 1);
                $modelCall = curl_exec($ch_model);
                $device_model = json_decode($modelCall);
                $message_output['extra']=$device_model;
                $content_model = $device_model->content;
                if (!$content_model){
                    $message_output["code"] = "404";
                    $message_output["message"] = "Error: model not found";
                    echo json_encode($message_output);
                    exit();
                }
                $iot_contextbroker = $content_model->contextbroker;
                $iot_attributes = $content_model->attributes;
                $kind = $content_model->kind;
                $devicetype = $content_model->devicetype;
                $format = $content_model->format;
                $attributes = $content_model->attributes;
                curl_close($ch_model);
                $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";
                $device_id = md5($originalfilename) . $date_observed;
                $data_array = [
                    "action" => "insert",
                    "id" => $device_id,
                    "type" => $devicetype,
                    "contextbroker" => $iot_contextbroker,
                    "kind" => $kind,
                    "format" => $format,
                    "subnature" => $subnature,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "frequency" => "600",
                    "producer" => "",
                    "model" => $model,
                    "k1" => "",
                    "k2" => "",
                    "token" => $accessToken,
                    "nodered" => "access",
                    "attributes" => $attributes,
                ];
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
                $response = json_decode($deviceCall, true);
                curl_close($curl);
                if ($response) {
                    $message_output["code"] = "200";
                    $message_output["message"] = "Device successfully created";
                    $new_attributes =
                        '{"description":{"value":"' .
                        $description .
                        '","type":"string"},"originalfilename":{"value":"' .
                        $originalfilename .
                        '","type":"string"},"newfileid":{"value":"' .
                        $new_fileid .
                        '","type":"string"},"language":{"value":"' .
                        $language .
                        '","type":"string"},"filesize":{"value":"' .
                        $filesize .
                        '","type":"string"},"filetype":{"value":"' .
                        $filetype .
                        '","type":"string"},"dateObserved":{"value":"' .
                        $date_observed .
                        '","type":"string"}}';
                    $value_array1 = [
                        "action" => "Insert_Value",
                        "id" => $device_id,
                        "type" => "stream",
                        "contextbroker" => $iot_contextbroker,
                        "service" => null,
                        "servicePath" => null,
                        "version" => "v2",
                        "nodered" => "access",
                        "token" => $accessToken,
                        "payload" => $new_attributes,
                    ];
                    $curl01 = curl_init();
                    $url01 = sprintf(
                        "%s?%s",
                        $iot_directory_device,
                        http_build_query($value_array1)
                    );
                    curl_setopt($curl01, CURLOPT_URL, $url01);
                    curl_setopt($curl01, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $accessToken,
                    ]);
                    curl_setopt($curl01, CURLOPT_RETURNTRANSFER, 1);
                    $deviceCall01 = curl_exec($curl01);
                    $response01 = json_decode($deviceCall01, true);
                    if ($response01) {
                        $message_output["code"] = "200";
			$message_output["message"] = "Device attributes successfully inserted";
			$message_output["result"] = [ "fileid" => $new_fileid, "filetype" => $filetype, "device_id" => $device_id ];
                    } else {
                        $message_output["code"] = "500";
			$message_output["message"] = "Error during device attributes creation by api";
			$message_output["extra"] = $deviceCall01;
                        echo json_encode($message_output);
                        exit();
                    }
                } else {
                    $message_output["code"] = "500";
                    $message_output["message"] = "Error during device creation by api";
                    echo json_encode($message_output);
                    exit();
                }
                $message_output["code"] = "200";
                $message_output["message"] = "File is successfully uploaded.";
            } else {
                $message_output["code"] = "500";
                $message_output["message"] = "There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.";
            }
        }
        echo json_encode($message_output);
    } elseif ($action == "delete_file") {
        $id = mysqli_real_escape_string($link, $_POST["id"]);
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        $broker = mysqli_real_escape_string($link, $_POST["broker"]);
        $broker = filter_var($broker, FILTER_SANITIZE_STRING);
        $fileid = mysqli_real_escape_string($link, $_POST["fileid"]);
        $fileid = filter_var($fileid, FILTER_SANITIZE_STRING);
        $filetype = mysqli_real_escape_string($link, $_POST["filetype"]);
        $filetype = filter_var($filetype, FILTER_SANITIZE_STRING);
        $filename = $fileid . "." . $filetype;
        $filesubdirectory = $protecteduploads_directory . "/" . $fileid;
        $filepath = $filesubdirectory . "/" . $filename;
        unlink($filepath);
        rmdir($filesubdirectory);
        $data_array = [
            "action" => "delete",
            "id" => $id,
            "contextbroker" => $broker,
            "nodered" => "access",
        ];
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
        $response = json_decode($deviceCall, true);
        if ($response) {
            $message_output["code"] = "200";
            $message_output["message"] .= "File successfully deleted";
        } else {
            $message_output["code"] = "500";
            $message_output["message"] .= "Error during device deletion from API";
            echo json_encode($message_output);
        }
        echo json_encode($message_output);
    } elseif ($action == "view_file") {
        $fileid = mysqli_real_escape_string($link, $_GET["fileid"]);
        $fileid = filter_var($fileid, FILTER_SANITIZE_STRING);
        $filetype = mysqli_real_escape_string($link, $_GET["filetype"]);
        $filetype = filter_var($filetype, FILTER_SANITIZE_STRING);
        $filename = $fileid . "." . $filetype;
        $filepath = $protecteduploads_directory . "/" . $filename;
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $filepath);
            header("Content-Description: File Transfer");
            //do not assume the file type, try to get it from the extension
            header("Content-Type: ".mime_content_type($filename));
            header("Content-Disposition: inline; filename=" . basename($filepath));
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
            header("Content-Length: " . filesize($filepath));
            readfile($filepath);
            exit();
        } else {
            $mesagge_output["code"] = "404";
            $message_output["message"] = "The file you were looking for does not exist.";
        }
        echo json_encode($message_output);
    } elseif ($action == "edit_file") {
        $id = mysqli_real_escape_string($link, $_POST["id"]);
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        $subnature = mysqli_real_escape_string($link, $_POST["subnature"]);
        $subnature = filter_var($subnature, FILTER_SANITIZE_STRING);
        $latitude = mysqli_real_escape_string($link, $_POST["latitude"]);
        $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);
        $longitude = mysqli_real_escape_string($link, $_POST["longitude"]);
        $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);
        $description = mysqli_real_escape_string($link, $_POST["description"]);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        $description = str_replace(" ", "_", $description);
        $filename = mysqli_real_escape_string($link, $_POST["filename"]);
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        $language = mysqli_real_escape_string($link, $_POST["language"]);
        $language = filter_var($language, FILTER_SANITIZE_STRING);
        $filetype = mysqli_real_escape_string($link, $_POST["filetype"]);
        $filetype = filter_var($filetype, FILTER_SANITIZE_STRING);
        $filesize = mysqli_real_escape_string($link, $_POST["filesize"]);
        $filesize = filter_var($filesize, FILTER_SANITIZE_STRING);
        $date = mysqli_real_escape_string($link, $_POST["date"]);
        $date = filter_var($date, FILTER_SANITIZE_STRING);
        $newfileid = mysqli_real_escape_string($link, $_POST["newfileid"]);
        $newfileid = filter_var($newfileid, FILTER_SANITIZE_STRING);
        $dataArrayForGetModel = [
            "action" => "get_model",
            "name" => $model,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $ch_model = curl_init();
        mysqli_select_db($link, $dbname);
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
        $iot_contextbroker = $content_model->contextbroker;
        $iot_org = $content_model->organization;
        $iot_attributes = $content_model->attributes;
        $k1 = $content_model->k1;
        $k2 = $content_model->k2;
        $kind = $content_model->kind;
        $devicetype = $content_model->devicetype;
        $format = $content_model->format;
        $attributes = $content_model->attributes;
        curl_close($ch_model);
        $data_array = [
            "action" => "update",
            "id" => $id,
            "type" => $devicetype,
            "contextbroker" => $iot_contextbroker,
            "kind" => $kind,
            "format" => $format,
            "gb_old_cb" => $iot_contextbroker,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "frequency" => "600",
            "producer" => "",
            "model" => $model,
            "subnature" => $subnature,
            "k1" => "",
            "k2" => "",
            "token" => $accessToken,
            "nodered" => "access",
            "attributes" => $attributes,
            "newattributes" => "[]",
            "newfileid" => $newfileid,
        ];
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
        $response = json_decode($deviceCall, true);
        curl_close($curl);
        if ($response) {
            $date_observed = date("Y-m-d") . "T" . date("H:i:s") . ".000Z";
            $new_attributes =
                '{"description":{"value":"' .
                $description .
                '","type":"string"},"originalfilename":{"value":"' .
                $filename .
                '","type":"string"},"language":{"value":"' .
                $language .
                '","type":"string"},"filesize":{"value":"' .
                $filesize .
                '","type":"float"},"filetype":{"value":"' .
                $filetype .
                '","type":"string"},"dateObserved":{"value":"' .
                $date_observed .
                '","type":"string"},"newfileid":{"value":"' .
                $newfileid .
                '","type":"string"}}';
            $value_array = [
                "action" => "Insert_Value",
                "id" => $id,
                "type" => "stream",
                "contextbroker" => $iot_contextbroker,
                "service" => null,
                "servicePath" => null,
                "version" => "v2",
                "nodered" => "access",
                "token" => $accessToken,
                "payload" => $new_attributes,
            ];
            $curl_v = curl_init();
            $url_v = sprintf(
                "%s?%s",
                $iot_directory_device,
                http_build_query($value_array)
            );
            curl_setopt($curl_v, CURLOPT_URL, $url_v);
            curl_setopt($curl_v, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $accessToken,
            ]);
            curl_setopt($curl_v, CURLOPT_RETURNTRANSFER, 1);
            $valueCall = curl_exec($curl_v);
            $valueResponse = json_decode($valueCall, true);
            $message_output["code"] = "200";
            $message_output["message"] = "Device successfully modified";
        } else {
            $message_output["code"] = "500";
            $message_output["message"] = "Error during editing device by API";
        }
        echo json_encode($message_output);
    }
    elseif ($action == "get_extensions") {
        mysqli_select_db($link, $dbname);
        $query_extensions = "SELECT extension FROM fileextensions";
        ($result_extensions = mysqli_query($link, $query_extensions)) or
            die(mysqli_error($link));
        $array_extensions = [];
        while ($row = mysqli_fetch_assoc($result_extensions)) {
            array_push($array_extensions, $row["extension"]);
        }
        echo json_encode($array_extensions);
    } elseif ($action == "get_languages") {
        $array_languages = json_decode($filemanager_languages);
        echo json_encode($array_languages);
    }
    elseif ($action == "get_subnature") {
        $url_api = $processloader_uri . "/?type=subnature";
        $options = [
            "http" => [
                "header" => "Content-type: application/json\r\n",
                "method" => "GET",
                "timeout" => 30,
                "ignore_errors" => true,
            ],
        ];
        $context = stream_context_create($options);
        $callResult = file_get_contents($url_api, true, $context);
        $call_json = json_decode($callResult, true);
        $message_output["content"] = $call_json["content"];
        if (strpos($http_response_header[0], "200") !== false) {
            $message_output["code"] = "200";
            $message_output["message"] = "Subnature successfully retrieved";
            echo json_encode($message_output);
        } else {
            $message_output["code"] = "500";
            $message_output["message"] = "Error during retrieving subnature";
            echo json_encode($message_output);
        }
    } elseif ($action == "change_visibility") {
        $contextbroker = mysqli_real_escape_string($link, $_POST["contextbroker"]);
        $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
        $visibility = mysqli_real_escape_string($link, $_POST["visibility"]);
        $visibility = filter_var($visibility, FILTER_SANITIZE_STRING);
        $deviceid = mysqli_real_escape_string($link, $_POST["id"]);
        $deviceid = filter_var($deviceid, FILTER_SANITIZE_STRING);
        $dataArray = [
            "action" => "change_visibility",
            "id" => $deviceid,
            "contextbroker" => $contextbroker,
            "visibility" => $visibility,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $ch = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $apiCall = curl_exec($ch);
        if ($apiCall) {
            $message_output["status"] = "ok";
        } else {
            $message_output["status"] = "ko";
            $mesagge_output["message"] = $apiCall;
        }
        curl_close($ch);
        echo json_encode($message_output);
    } elseif ($action == "change_owner") {
        $contextbroker = mysqli_real_escape_string(
            $link,
            $_POST["contextbroker"]
        );
        $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
        $deviceid = mysqli_real_escape_string($link, $_POST["id"]);
        $deviceid = filter_var($deviceid, FILTER_SANITIZE_STRING);
        $newOwner = mysqli_real_escape_string($link, $_POST["newOwner"]);
        $newOwner = filter_var($newOwner, FILTER_SANITIZE_STRING);
        $k1 = mysqli_real_escape_string($link, $_POST["k1"]);
        $k1 = filter_var($k1, FILTER_SANITIZE_STRING);
        $k2 = mysqli_real_escape_string($link, $_POST["k2"]);
        $k2 = filter_var($k2, FILTER_SANITIZE_STRING);
        $dataArray = [
            "action" => "change_owner",
            "id" => $deviceid,
            "contextbroker" => $contextbroker,
            "newOwner" => $newOwner,
            "k1" => $k1,
            "k2" => $k2,
            "model" => $model,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $ch = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $apiCall = curl_exec($ch);
        if ($apiCall) {
            $message_output["status"] = "ok";
        } else {
            $message_output["status"] = "ko";
            $mesagge_output["message"] = $apiCall;
        }
        curl_close($ch);
        echo json_encode($message_output);
    } elseif ($action == "get_delegations") {
        $contextbroker = mysqli_real_escape_string(
            $link,
            $_POST["contextbroker"]
        );
        $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
        $deviceid = mysqli_real_escape_string($link, $_POST["id"]);
        $deviceid = filter_var($deviceid, FILTER_SANITIZE_STRING);
        $dataArray = [
            "action" => "get_delegations",
            "id" => $deviceid,
            "contextbroker" => $contextbroker,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $deviceCall = curl_exec($curl);
        $response = json_decode($deviceCall, true);
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["message"] = $response;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response;
        }
        echo json_encode($message_output);
    } elseif ($action == "remove_delegation") {
        $contextbroker = mysqli_real_escape_string($link, $_POST["contextbroker"]);
        $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
        $deviceid = mysqli_real_escape_string($link, $_POST["id"]);
        $deviceid = filter_var($deviceid, FILTER_SANITIZE_STRING);
        $delegationId = mysqli_real_escape_string($link, $_POST["delegationId"]);
        $delegationId = filter_var($delegationId, FILTER_SANITIZE_STRING);
        if (isset($_POST["userDelegated"])) {
            $userDelegated = mysqli_real_escape_string($link, $_POST["userDelegated"]);
            $userDelegated = filter_var($userDelegated, FILTER_SANITIZE_STRING);
        } else {
            $userDelegated = "";
        }
        if (isset($_POST["groupDelegated"])) {
            $groupDelegated = mysqli_real_escape_string($link, $_POST["groupDelegated"]);
            $groupDelegated = filter_var($groupDelegated, FILTER_SANITIZE_STRING);
        } else {
            $groupDelegated = "";
        }
        $dataArray = [
            "action" => "remove_delegation",
            "id" => $deviceid,
            "contextbroker" => $contextbroker,
            "delegationId" => $delegationId,
            "userDelegated" => $userDelegated,
            "groupDelegated" => $groupDelegated,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $deviceCall = curl_exec($curl);
        $response = json_decode($deviceCall, true);
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["message"] = $response;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response;
        }
        echo json_encode($message_output);
    } elseif ($action == "add_delegation") {
        $contextbroker = mysqli_real_escape_string(
            $link,
            $_POST["contextbroker"]
        );
        $contextbroker = filter_var($contextbroker, FILTER_SANITIZE_STRING);
        $deviceid = mysqli_real_escape_string($link, $_POST["id"]);
        $deviceid = filter_var($deviceid, FILTER_SANITIZE_STRING);
        if (isset($_POST["delegated_user"])) {
            $delegated_user = mysqli_real_escape_string($link, $_POST["delegated_user"]);
            $delegated_user = filter_var($delegated_user, FILTER_SANITIZE_STRING);
        } else {
            $delegated_user = "";
        }
        if (isset($_POST["delegated_group"])) {
            $delegated_group = mysqli_real_escape_string($link, $_POST["delegated_group"]);
            $delegated_group = filter_var($delegated_group, FILTER_SANITIZE_STRING);
        } else {
            $delegated_group = "";
        }
        $k1 = mysqli_real_escape_string($link, $_POST["k1"]);
        $k1 = filter_var($k1, FILTER_SANITIZE_STRING);
        $k2 = mysqli_real_escape_string($link, $_POST["k2"]);
        $k2 = filter_var($k2, FILTER_SANITIZE_STRING);
        $dataArray = [
            "action" => "add_delegation",
            "id" => $deviceid,
            "contextbroker" => $contextbroker,
            "delegated_user" => $delegated_user,
            "delegated_group" => $delegated_group,
            "k1" => $k1,
            "k2" => $k2,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_device,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $deviceCall = curl_exec($curl);
        $response = json_decode($deviceCall, true);
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["message"] = $response;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response;
        }

        echo json_encode($message_output);
    } elseif ($action == "get_all_ou") {
        $dataArray = [
            "action" => "get_all_ou",
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_ldap,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $ldapCall = curl_exec($curl);
        $response = json_decode($ldapCall, true);
        $message_output["message"] = $response;
        $content = $response["content"];
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["content"] = $content;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response["error_msg"];
        }
        echo json_encode($message_output);
    } elseif ($action == "get_group_for_ou") {
        $ou = mysqli_real_escape_string($link, $_POST["ou"]);
        $ou = filter_var($k2, FILTER_SANITIZE_STRING);
        $dataArray = [
            "action" => "get_group_for_ou",
            "ou" => $ou,
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_ldap,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $ldapCall = curl_exec($curl);
        $response = json_decode($ldapCall, true);
        $message_output["message"] = $response;
        $content = $response["content"];
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["content"] = $content;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response["error_msg"];
        }
        echo json_encode($message_output);
    } elseif ($action == "get_logged_ou") {
        $dataArray = [
            "action" => "get_logged_ou",
            "token" => $accessToken,
            "nodered" => "access",
        ];
        $curl = curl_init();
        $url = sprintf(
            "%s?%s",
            $iot_directory_ldap,
            http_build_query($dataArray)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $ldapCall = curl_exec($curl);
        $response = json_decode($ldapCall, true);
        $message_output["message"] = $response;
        $content = $response["content"];
        curl_close($curl);
        if ($response) {
            $message_output["status"] = "ok";
            $message_output["content"] = $content;
        } else {
            $message_output["status"] = "ko";
            $message_output["message"] = $response["error_msg"];
        }
        echo json_encode($message_output);
    }
}
function human_filesize($bytes, $dec = 2)
{
    $bytes = number_format($bytes, 0, ".", "");
    $size = ["B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) {
        $dec = 0;
    }
    return sprintf(
        "%.{$dec}f %s",
        $bytes / 1024 ** $factor,
        $size[$factor]
    );
}
