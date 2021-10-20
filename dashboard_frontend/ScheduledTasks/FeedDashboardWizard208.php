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
ini_set('memory_limit', '1024M');
error_reporting(E_ERROR);

$processId = 1;     // 0 = SELECT & UPDATE; 1 = UPSERT
$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting FeedDashboardWizard208 SCRIPT on LonatoDelGarda KB at: ".$start_time_ok."\n");

$high_level_type = "Sensor";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";

$value_name = "";
$value_type = "";

$instance_uri = "";
$get_instances = "";
$unit = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "";
$ownership = "public";
$organizationFromKb = "";
$organizations = "LonatoDelGarda";

$kbUrl = $kbUrlLonatoDelGarda;

$s = "";
$a = "";
$dt = "";

$queryAscapiEtlDecoded = "select distinct ?s ?a ?n ?avn ?avt ?dt ?u ?serviceType ?ow ?org ?lat ?lon { " .
    "?s km4c:hasAttribute ?a. " .
    "?a km4c:data_type ?dt. " .
    "?a km4c:value_unit ?u. " .
    "OPTIONAL {?a km4c:value_name ?avn.} " .
    "OPTIONAL {?a km4c:value_type ?avt.} " .
    "OPTIONAL {?s schema:name|foaf:name ?n}." .
    "OPTIONAL {?s km4c:ownership ?ow.} " .
    "OPTIONAL {?s km4c:organization ?org.} " .
    "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
    "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
    "?s a ?sType. " .
    "?sType rdfs:subClassOf ?sCategory. " .
    "?sCategory rdfs:subClassOf km4c:Service. " .
    "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType) " .
   // "MINUS{?s a sosa:Sensor} MINUS {?s a sosa:Actuator}}}";
    "}";

$queryAscapiEtl = $kbHostUrlLonatoDelGarda . ":8890/sparql?default-graph-uri=&query=" . urlencode($queryAscapiEtlDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

$queryAscapiEtlRresults = file_get_contents($queryAscapiEtl);
$resArrayEtl = json_decode($queryAscapiEtlRresults, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);

$count = 0;

foreach ($resArrayEtl['results']['bindings'] as $key => $val) {

    $count++;
    echo($count." - ETL DEVICE: ".$s.", MEASURE: ".$a."\n");
    $s = $resArrayEtl['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
    $a = $resArrayEtl['results']['bindings'][$key]['a']['value'];   // $a --> attribute
    $n = $resArrayEtl['results']['bindings'][$key]['n']['value'];   // $a --> name
    $value_name = $resArrayEtl['results']['bindings'][$key]['avn']['value'];
    $value_type = $resArrayEtl['results']['bindings'][$key]['avt']['value'];
    $value_type = explode("value_type/", $value_type)[1];

    $dt = $resArrayEtl['results']['bindings'][$key]['dt']['value'];
    $u = $resArrayEtl['results']['bindings'][$key]['u']['value'];
    $serviceType = $resArrayEtl['results']['bindings'][$key]['serviceType']['value'];   // $n --> serviceName

    if ($n && $n != '') {
        $unique_name_id = $n;
    } else {
        $unique_name_id = explode($baseKm4CityUri, $s)[1];
    }

    $organizationFromKb = $resArrayEtl['results']['bindings'][$key]['org']['value'];

    if(!isset($organizationFromKb) || $organizationFromKb === '') {
        $organizationFromKb = "LonatoDelGarda";
    } else {
        $organizations = $organizationFromKb;
    }

    $latitude = $resArrayEtl['results']['bindings'][$key]['lat']['value'];
    $longitude = $resArrayEtl['results']['bindings'][$key]['lon']['value'];

    echo($count." - DEVICE: ".$s.", MEASURE: ".$a."\n");

    if ($serviceType != "Emergency_First_aid" && $serviceType != "Pollen_monitoring_station") {

        $serviceChangeBuffer["current"] = $unique_name_id;

        $sub_nature_array = explode("_", $serviceType);
        //  if (sizeof($sub_nature_array) > 2) {
        $nature = explode("_", $serviceType)[0];
        $sub_nature = explode($nature . "_", $serviceType)[1];

        $low_level_type = explode($s . "/", $a)[1];

        $instance_uri = "any + status";

        $unit = $dt;
        $get_instances = $s;

        $metric = "no";
        $saved_direct = "direct";
        $kb_based = "yes";
        $sm_based = "yes";

        $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";

        if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
            $sm_based = "yes";
            if ($processId == 0) {
                $generalServiceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND unit = 'sensor_map' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                $resGeneralServiceQuery = mysqli_query($link, $generalServiceQuery);
                $result = [];
                if($resGeneralServiceQuery) {
                    if ($row = mysqli_fetch_assoc($resGeneralServiceQuery)) {
                        $updateGeneralServiceQuery = "UPDATE DashboardWizard SET high_level_type = 'Sensor Device', device_model_name = '$unique_name_id' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "' AND unit = 'sensor_map';";
                        mysqli_query($link, $updateGeneralServiceQuery);
                    }
                }
            } else {
                $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, device_model_name) VALUES ('$nature', 'Sensor Device', '$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = 'Sensor Device', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', device_model_name = '" . $unique_name_id . "';";
                mysqli_query($link, $insertGeneralServiceQuery);
            }
        }

        if ($processId == 0) {
            $serviceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
            $resServiceQuery = mysqli_query($link, $serviceQuery);
            $result = [];
            if($resServiceQuery) {
                if ($row = mysqli_fetch_assoc($resServiceQuery)) {
                    $updateServiceQuery = "UPDATE DashboardWizard SET value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                    mysqli_query($link, $updateServiceQuery);
                }
            }
        } else {
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, value_name, value_type, device_model_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$value_name', '$value_type', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "';";
            mysqli_query($link, $insertQuery);
        }

        $serviceChangeBuffer["last"] = $unique_name_id;


    } else {
        $stop_flag = 4;
    }


}

$queryMaxId = "SELECT * FROM Dashboard.DashboardWizard ORDER BY id DESC LIMIT 0, 1";
$rs = mysqli_query($link, $queryMaxId);
$result = [];
if($rs) {

    $dashboardName = "";

    if ($row = mysqli_fetch_assoc($rs)) {
        $maxWizardId = $row['id'];
        $queryUpdateMaxId = "ALTER TABLE Dashboard.DashboardWizard AUTO_INCREMENT " . (string) (intval($maxWizardId) + 1);
        $rs2 = mysqli_query($link, $queryUpdateMaxId);
    }
}

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End FeedDashboardWizard208 SCRIPT on LonatoDelGarda KB at: ".$end_time_ok);
