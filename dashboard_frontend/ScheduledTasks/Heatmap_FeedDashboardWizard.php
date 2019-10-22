<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence
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
echo("Starting Heatmap_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$host_pd= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$high_level_type = "Heatmap";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
$instance_uri = "WMS";

$get_instances = "";
$metric = "no";
$saved_direct = "direct";
$kb_based = "";
$sm_based = "";
$unit = "heatmap";
$parameters = "";
$healthiness = "";
$ownership = "";
//$lastCheck = "";
$organizations = "";

$queryHeatmapAPI = $heatmapUrl . "maps-completed.php";

$queryHeatmapResults = file_get_contents($queryHeatmapAPI);
$heatmapResultsArray = json_decode($queryHeatmapResults, true);

foreach ($heatmapResultsArray as $heatmapName) {

    $count++;

    $queryMetadataAPI = $heatmapUrl . "heatmap-metadata.php?dataset=" . $heatmapName;
    $queryMetadataResults = file_get_contents($queryMetadataAPI);
    $metadataResultsArray = json_decode($queryMetadataResults, true);

    foreach ($metadataResultsArray as $metadata) {
        if ($metadata['metadata']['org'] !== '') {
            $organizations = $metadata['metadata']['org'];
            $queryOrg = "SELECT * FROM Dashboard.Organizations WHERE organizationName = '" . $organizations . "';";
            $rs = mysqli_query($link, $queryOrg);
            $result = [];
            if($rs) {
                if ($row = mysqli_fetch_assoc($rs)) {
                    $coords = explode(",", $row['gpsCentreLatLng']);
                    $latitude = trim($coords[0]);
                    $longitude = trim($coords[1]);
                }
            }
            break;
        } else {
            $stopFlag = 1;
        }
    }

    $healthiness = "true";
    $lastCheck = $lastCheck;
    $unique_name_id = $metadata['metadata']['mapName'];
    $low_level_type = $metadata['metadata']['metricName'];
    $last_date = $metadata['metadata']['date'];
    $get_instances = $geoServerUrl . "geoserver/Snap4City/wms?service=WMS&layers=" . $unique_name_id;
    $parameters = $get_instances;
    $ownership = "public";

    if (strpos($low_level_type, 'NO') !== false || strpos($low_level_type, 'NO2') !== false || strpos($low_level_type, 'SO') !== false || strpos($low_level_type, 'CO') !== false || stripos($low_level_type, 'airquality') !== false || stripos($low_level_type, 'aqi') !== false || stripos($low_level_type, 'pm10') !== false || stripos($low_level_type, 'pm2_5') !== false || stripos($low_level_type, 'benzene') !== false || stripos($low_level_type, 'nox') !== false) {
        $nature = "Environment";
        $sub_nature = "Air_quality_monitoring_station";
    } else if (strpos($low_level_type, 'LAeq') !== false) {
        $nature = "Environment";
        $sub_nature = "Noise_Level_Sensor";
    } else if (stripos($low_level_type, 'humidity') !== false || stripos($low_level_type, 'temperature') !== false || stripos($low_level_type, 'wind') !== false) {
        $nature = "Environment";
        $sub_nature = "Weather_sensor";
    } else if (stripos($low_level_type, 'bike') !== false) {
        $nature = "Mobility and Transport";
        $sub_nature = "Safety_on_Bike";
    } else if (stripos($low_level_type, 'accident') !== false) {
        $nature = "Mobility and Transport";
        $sub_nature = "Accident_Density";
    } else if (stripos($low_level_type, 'accident') !== false) {
        $nature = "Environment";
        $sub_nature = "People_counter";
    }

    if (!is_null($low_level_type)) {
    //    if ($nature != '') {
            echo($count . " - HEATMAP: " . $unique_name_id . ", METRIC NAME: " . $low_level_type . "\n");
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
            try {
                //   mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "';");
                mysqli_query($link, $insertQuery);

                //   echo ("\nINSERT QUERY ON DUPLICATE KEY: ".$insertQuery."\n");
            } catch (Exception $e) {
                echo $e->getMessage();
                $updtQuery = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                mysqli_query($link, $updtQuery);
                echo("\nUPDATE QUERY per KPI-ID: " . $kpiId . ": " . $updtQuery . "\n");
            }
   //     }
    }
}


$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End Heatmap_FeedDashboardWizard SCRIPT at: ".$end_time_ok);