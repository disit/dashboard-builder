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

error_reporting(E_ERROR);

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting IOT_Actuator_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON IOT ACTUATORS

$high_level_type = "Sensor-Actuator";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
$instance_uri = "";
$get_instances = "";
$unit = "";
$unitSens = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "";
$ownership = "";
$organizations = "DISIT";

$s = "";
$a = "";
$dt = "";
$actSensArray = [];

$rs = mysqli_query($link, $query);
$result = [];

$actSensKbQueryDecoded = "select distinct ?s ?n ?a ?dt ?serviceType ?av ?ow (coalesce(?org,\"DISIT\") as ?organization) ?brokerName ?lat ?lon { " .
   "?s a km4c:IoTActuator. " .
   "?s schema:name ?n. " .
   "?s km4c:hasAttribute ?a. " .
   "?s <http://purl.oclc.org/NET/UNIS/fiware/iot-lite#exposedBy> ?broker. " .
   "?broker <http://schema.org/name> ?brokerName. " .
   "?a km4c:data_type ?dt. " .
   "OPTIONAL {?s km4c:availability ?av.} " .
   "OPTIONAL {?s km4c:ownership ?ow.} " .
   "OPTIONAL {?s km4c:organization ?org.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
   "?s a ?sType. " .
   "?sType rdfs:subClassOf ?sCategory. " .
   "?sCategory rdfs:subClassOf km4c:Service. " .
   "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType)}";

$actSensKbQuery = $kbHostUrl . ":8890/sparql?default-graph-uri=&query=" . urlencode($actSensKbQueryDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";


$queryActSensRresults = file_get_contents($actSensKbQuery);
$resArray = json_decode($queryActSensRresults, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);


foreach ($resArray['results']['bindings'] as $key => $val) {

    $count++;

    $s = $resArray['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
    $n = $resArray['results']['bindings'][$key]['n']['value'];   // $n --> service name

  //  if (in_array($n, $actSensArray)) {
     //   echo($count . " - IOT ACTUATOR: " . $s . ", MEASURE: " . $a . "\n");
        $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute
        echo($count . " - IOT ACTUATOR: " . $s . ", MEASURE: " . $a . "\n");
        if (strpos($a, "actuatorcanceller") === false && strpos($a, "actuatorCanceller") === false && strpos($a, "actuatordeleted") === false && strpos($a, "actuatorDeleted") === false && strpos($a, "actuatordeletiondate") === false && strpos($a, "actuatorDeletionDate") === false && strpos($a, "creationdate") === false && strpos($a, "creationDate") === false && strpos($a, "entitycreator") === false && strpos($a, "entityCreator") === false && strpos($a, "entitydesc") === false && strpos($a, "entityDesc") === false) {
            $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
            $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
            $availability = $resArray['results']['bindings'][$key]['av']['value'];
            $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
            $brokerName = $resArray['results']['bindings'][$key]['brokerName']['value'];
            $latitude = $resArray['results']['bindings'][$key]['lat']['value'];
            $longitude = $resArray['results']['bindings'][$key]['lon']['value'];

            if ($availability != '') {
                $ownership = $availability;
            } else if ($ownShip != '') {
                $ownership = $ownShip;
            } else {
                $ownership = "public";
            }

            $organizationFromKb = $resArray['results']['bindings'][$key]['organization']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
            if (strcmp($organizationFromKb, "") !== 0) {
                $organizations = $organizationFromKb;
            } else {
                $organizations = "DISIT";
            }

            $unique_name_id = $organizations . ":" . $brokerName . ":" . $n;
            $serviceChangeBuffer["current"] = $unique_name_id;
            $nature = "From Dashboard to IOT Device";
            $sub_nature = "IoTSensor-Actuator";
            $low_level_type = explode($s . "/", $a)[1];

            $instance_uri = 'single_marker';

            $unit = $dt;
            $unitSens = $unit;
            if ($unit == "geolocator") {
                $unit = "json-act";
            } else if ($unit == "integer") {
                $unit = "integer-act";
            } else if ($unit == "Testuale") {
                $unit = "string-act";
            } else if ($unit == "webContent") {
                $unit = "webpage-act";
            } else if ($unit == "float") {
                $unit = "float-act";
            } else if ($unit == "boolean") {
                $unit = "string-act";
            } else if ($unit == "binary") {
                $unit = "binary-act";
            } else if ($unit == "string") {
                $unit = "string-act";
            } else {

            }
            $get_instances = $s;

            $metric = "no";
            $saved_direct = "direct";
            $kb_based = "yes";
            $sm_based = "no";

            $parameters = $graphURI . "api/v1/?serviceUri=" . $s . "&format=json";     // CAMBIARE CON API NUOVE DI PIERO QUANDO E' PRONTA LA GET
            //  $healthiness = "na";
            if ($ownership != "private") {
                $ownership = "public";
            }

            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

            $insertQuerySens = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unitSens', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unitSens . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','Sensor','IoTSensor','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unitSens', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = 'Sensor', sub_nature = 'IoTSensor', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', unit = '" . $unitSens . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

            $serviceChangeBuffer["last"] = $unique_name_id;
        }
  //  }
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
echo("End IOT_Actuator_FeedDashboardWizard SCRIPT at: ".$end_time_ok);


