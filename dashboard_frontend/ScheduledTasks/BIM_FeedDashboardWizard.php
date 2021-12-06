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

function udate($format = 'u', $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

include '../config.php';

error_reporting(E_ERROR);
//mysqli_report(MYSQLI_REPORT_ALL) ;

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting BIM_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$high_level_type = "";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
$instance_uri = "";

$value_name = "";
$value_type = "";
$device_model_name = "";

$get_instances = "";
$metric = "yes";
$saved_direct = "saved";
$kb_based = "no";
$sm_based = "no";
$unit = "webpage";
$parameters = "";
$healthiness = "true";
$ownership = "public";
//$lastCheck = "";
$organizations = "";
$count = 0;

$apiUrl = $BIMServerApi;

/*
$data = [
    'request' => [
        'interface' => 'AuthInterface',
        'method' => 'login',
        'parameters' => [
            'username' => $BIMServerUser,
            'password' => $BIMServerPassw
        ],
    ]
];
$postdata = json_encode($data);

$options = array(
    'http' => array(
        'method' => 'POST',
        'header'  => "Content-type: application/json\r\n",
        'content' => $postdata
    )
);

$context = stream_context_create($options);
$result = file_get_contents($apiUrl, false, $context);
$resultArray = json_decode($result, true);
if ($result !== false && !$resultArray['response']['exception']) {
    $token = $resultArray['response']['result'];
}
*/

$query = "SELECT * FROM Dashboard.BIMProjects;";
$rs = mysqli_query($link, $query);
if($rs) {
    while ($row = mysqli_fetch_assoc($rs)) {
        $count++;
        $BIMRequestAPI = $BIMDeviceApi . $row['poid'];
        $organizations = $row['organizations'];
        $unique_name_id = $row['project_name'];
        $broker_name = $row['project_name'];
        // $BIMRequestAPI = BIMDeviceApi . "983041";
        $BIMRequestResults = file_get_contents($BIMRequestAPI);
        if ($BIMRequestResults !== false) {
            $high_level_type = 'BIM Device';
            $BIMRequestResultsArray = json_decode($BIMRequestResults, true);
            $cnt = 0;
            foreach ($BIMRequestResultsArray as $BIMDevice) {
                echo($count . " - BIM Device - poid: " . $row['poid'] . ", BIM Project: " . $BIMRequestResultsArray[$cnt]['project_name'] . ", Pin: " . $BIMRequestResultsArray[$cnt]['service_uri'] . "\n");
                $unique_name_id = $row['project_name'];
                $nature = $BIMDevice['nature'];
                $sub_nature = $BIMDevice['subnature'];
                $value_name = $BIMDevice['pin_title'];
            //    $unique_name_id = $BIMDevice['project_name'];
            //    $broker_name = $BIMDevice['project_name'];
                $last_date = $BIMDevice['date'];
                $last_date = str_replace("T"," ","$last_date");
                $last_date = str_replace(".000Z","","$last_date");
                $parameters = "https://bim.snap4city.org/public/view.html?poid=" . $row['poid'] . "&sidebar=off&pins=" . $BIMDevice['service_uri'];
                $instance_uri = $BIMDevice['service_uri'];
                $pos = strrpos($instance_uri, '/');
                $device_model_name = $pos === false ? $instance_uri : substr($instance_uri, $pos + 1);
                $unique_name_id = $unique_name_id . '-' . $device_model_name . '-' . $value_name;
                // $insertQuery1 = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, healthiness, lastCheck, ownership, organizations, broker_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$broker_name') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', broker_name = '" . $broker_name . "';";
                $queryDev = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = 'BIM Device' AND nature = '$nature' AND sub_nature = '$sub_nature' AND broker_name = '$broker_name' AND parameters = '$parameters' AND device_model_name = '$device_model_name' AND value_name = '$value_name';";
                $rsDev = mysqli_query($link, $queryDev);
                if($rsDev) {
                    if ($rowDev = mysqli_fetch_assoc($rsDev)) {
                        $upserQuery1 = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type . "', nature = '" . $nature . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', broker_name = '" . $broker_name . "', device_model_name = '" . $device_model_name . "', value_name = '" . $value_name . "' WHERE high_level_type = 'BIM Device' AND nature = '$nature' AND sub_nature = '$sub_nature' AND broker_name = '$broker_name' AND parameters = '$parameters' AND device_model_name = '$device_model_name' AND value_name = '$value_name';";
                    } else {
                        $upserQuery1 = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, healthiness, lastCheck, ownership, organizations, broker_name, device_model_name, value_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$broker_name', '$device_model_name', '$value_name');";
                    }
                    try {
                        mysqli_query($link, $upserQuery1);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
                $cnt++;
                $count++;
            }
        }
        $high_level_type = 'BIM View';
        $unique_name_id = $row['project_name'];
        $instance_uri = '';
        echo($count . " - BIM View - poid: " . $row['poid'] . ", BIM Project: " . $row['project_name'] . "\n");
        $nature = $row['nature'];
        $sub_nature = $row['sub_nature'];
        $parameters = "https://bim.snap4city.org/public/view.html?poid=" . $row['poid'] . "&sidebar=off&pins=null";
        $query2 = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = 'BIM View' AND broker_name = '$broker_name' AND parameters = '$parameters';";
        $rs2 = mysqli_query($link, $query2);
        if($rs2) {
            if ($row2 = mysqli_fetch_assoc($rs2)) {
                $upserQuery2 = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type . "', nature = '" . $nature . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', broker_name = '" . $broker_name . "' WHERE high_level_type = 'BIM View' AND broker_name = '$broker_name' AND parameters = '$parameters';";
            } else {
                $upserQuery2 = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, healthiness, lastCheck, ownership, organizations, broker_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$broker_name');";
            }
        }
        try {
            mysqli_query($link, $upserQuery2);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

/*
// Logout BIM Server
$logout_data = [
    'token' => $token,
    'request' => [
        'interface' => 'AuthInterface',
        'method' => 'logout'
    ]
];
$postdata_logout = json_encode($logout_data);

$options_logout = array(
    'http' => array(
        'method' => 'POST',
        'header'  => "Content-type: application/json\r\n",
        'content' => $postdata_logout
    )
);

$context_logout = stream_context_create($options_logout);
$result_logout = file_get_contents($apiUrl, false, $context_logout);
*/

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End BIM_FeedDashboardWizard SCRIPT at: ".$end_time_ok);
