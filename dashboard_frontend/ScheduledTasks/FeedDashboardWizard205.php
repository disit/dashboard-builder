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

include '../config.php';
ini_set('memory_limit', '1024M');
error_reporting(E_ERROR);

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting FeedDashboardWizard205 SCRIPT on HEL/ANTW KB at: ".$start_time_ok."\n");

$high_level_type = "Sensor";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
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
$organizations = "";
$organizationHelsTemplate = "Helsinki";
$organizationAntwTemplate = "Antwerp";

$kbUrl = "";

$s = "";
$a = "";
$dt = "";

$queryAscapiEtlDecoded = "select distinct ?s ?a ?n ?dt ?serviceType ?ow ?org ?lat ?lon {{ " .
    "?s km4c:hasAttribute ?a. " .
    "?a km4c:data_type ?dt. " .
    "OPTIONAL {?s schema:name|foaf:name ?n}." .
    "OPTIONAL {?s km4c:ownership ?ow.} " .
    "OPTIONAL {?s km4c:organization ?org.} " .
    "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
    "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
    "?s a ?sType. " .
    "?sType rdfs:subClassOf ?sCategory. " .
    "?sCategory rdfs:subClassOf km4c:Service. " .
    "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType) " .
    "MINUS{?s a sosa:Sensor} MINUS {?s a sosa:Actuator}}}";

$queryAscapiEtl = $kbHostUrlAntHel . ":8890/sparql?default-graph-uri=&query=" . urlencode($queryAscapiEtlDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

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

    $dt = $resArrayEtl['results']['bindings'][$key]['dt']['value'];   // $n --> serviceName
    $serviceType = $resArrayEtl['results']['bindings'][$key]['serviceType']['value'];   // $n --> serviceName

    if ($n && $n != '') {
        $unique_name_id = $n;
    } else {
        $unique_name_id = explode($baseKm4CityUri, $s)[1];
    }

    $organizationFromKb = $resArrayEtl['results']['bindings'][$key]['org']['value'];
    if (strcmp($organizationFromKb, "Helsinki") == 0) {
        $organizations = $organizationHelsTemplate;
        $kbUrl = $kbUrlHelsinki;
    } else if (strcmp($organizationFromKb, "Antwerp") == 0) {
        $organizations = $organizationAntwTemplate;
        $kbUrl = $kbUrlAntwerp;
    } else {
        $organizations = "Other";
        $kbUrl = $kbUrlSuperServiceMap;
    }

    $latitude = $resArrayEtl['results']['bindings'][$key]['lat']['value'];
    $longitude = $resArrayEtl['results']['bindings'][$key]['lon']['value'];

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
            $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

        }

        $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
        mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

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
echo("End FeedDashboardWizard205 SCRIPT on on HEL/ANTW KB at: ".$end_time_ok);