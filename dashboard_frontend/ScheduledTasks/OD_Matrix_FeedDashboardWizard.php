<?php

/* Dashboard Builder.
   Copyright (C) 2022 DISIT Lab https://www.disit.org - University of Florence
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

function get_from_date ($od_hostname, $dateArray, $precision, $organization_od, $longitude, $latitude, $unique_name_id) {
    for ($n=0; $n < sizeof($dateArray); $n++) {
        $od_data_API = $od_hostname . "api/get?precision=" . $precision . "&from_date=" . str_replace(" ", "%20", $dateArray[$n]) . "&organization=" . $organization_od . "&inflow=True&longitude=" . $longitude . "&latitude=" . $latitude . "&od_id=" . $unique_name_id . "&perc=True";
        $od_data_Results = file_get_contents($od_data_API);
        $od_data_Results_Array = json_decode($od_data_Results, true);
        if (sizeof($od_data_Results_Array) > 0) {
            return $dateArray[$n];
        }
    }
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
echo("Starting OD_Matrix_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$host_pd= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$high_level_type = "OD Matrix";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
$instance_uri = "";

$value_name = "";
$value_type = "";

$get_instances = "";
$metric = "no";
$saved_direct = "direct";
$kb_based = "";
$sm_based = "";
$unit = "heatmap";
$latitude = "";
$longitude = "";
$parameters = "";
$healthiness = "";
$ownership = "";
//$lastCheck = "";
$organizations = "";
$host_int_api = "http://" . $host . "/dashboardSmartCity";

// require("../widgets/get_od_metadata.php");
$queryOD = $host_int_api . "/widgets/get_od_metadata.php?action=od_list";

$queryODResults = file_get_contents($queryOD);
$ODResultsArray = json_decode($queryODResults, true);

foreach ($ODResultsArray as $od) {

    $count++;

    $healthiness = "true";
    $od_id = $od['od_id'];
    $unique_name_id = $od_id;
    $value_name = $unique_name_id;
    $low_level_type = $od['value_type'];
    $value_type = $low_level_type;
    $precision = $od['precision'];
    if ($precision == null) {
        $precision = "communes";
    }
    $organization_od = $od['organization'];
    if ($organization_od == 'Tuscany') {
        $organizations = "[\'DISIT\', \'Firenze\', \'Toscana\']";
        $organization_sql = "Toscana";
    } else {
        $organizations = $organization_od;
    }
    $dateAPI = $host_int_api . "/widgets/get_od_metadata.php?action=dates&organization=" . $organization_od . "&od_id=" . $od_id . "&precision=" . $precision;
    $dateRsults = file_get_contents($dateAPI);
    $dateArray = json_decode($dateRsults, true);
    $last_date = end($dateArray);

    $orgParamsQuery = "SELECT * FROM Dashboard.Organizations WHERE organizationName = '$organization_sql'";
    $r = mysqli_query($link, $orgParamsQuery);

    if($r) {
        if($row = mysqli_fetch_assoc($r))
        {
            $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
            $latitude = explode (", ", $orgGpsCentreLatLng)[0];
            $longitude = explode (", ", $orgGpsCentreLatLng)[1];
        }
    }

    $from_date = $dateArray[0];
    // $from_date = get_from_date ($od_hostname, $dateArray, $precision, $organization_od, $longitude, $latitude, $unique_name_id);

    $get_instances = $od_hostname . "api/get?precision=" . $precision . "&from_date=" . str_replace(" ", "%20", $from_date) . "&organization=" . $organization_od . "&inflow=True&longitude=" . $longitude . "&latitude=" . $latitude . "&od_id=" . $unique_name_id . "&perc=True";
    // https://odmm.snap4city.org/api/get?precision=communes&from_date=2017-10-19%2000:00:00&organization=Tuscany&inflow=True&longitude=11.257123947143556&latitude=43.771837562821375&od_id=od_Tuscany_communes&perc=True
    $parameters = $get_instances;
    $ownership = "public";

    $nature = "Environment";
    $sub_nature = "People_counter";

    if (!is_null($low_level_type)) {
        echo($count . " - OD MATRIX: " . $unique_name_id . ", METRIC NAME: " . $low_level_type . "\n");
        if ($last_date) {
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$latitude', '$longitude', '$value_name', '$value_type') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "';";
        } else {
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_value', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$latitude', '$longitude', '$value_name', '$value_type') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "';";
        }
        try {
            mysqli_query($link, $insertQuery);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}


$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End OD_Matrix_FeedDashboardWizard SCRIPT at: ".$end_time_ok);
