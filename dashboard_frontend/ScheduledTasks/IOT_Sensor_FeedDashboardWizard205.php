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

function getAccessToken($token_endpoint, $username, $password, $client_id){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$token_endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "username=".$username."&password=".$password."&grant_type=password&client_id=".$client_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($ch);
    curl_close($ch);
    return json_decode($curl_response)->access_token;

}

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
error_reporting(E_ERROR);
session_start();

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$allowedElementIDs = [];
$allowedElementCouples = [];
$genFileContent = parse_ini_file("../conf/environment.ini");
$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$host_pd= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);
$apiUrl = $host_PD . ":8080/datamanager/api/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=IOTID";
$apiResults = file_get_contents($apiUrl);

if(trim($apiResults) != "")
{
    $resApiArray = json_decode($apiResults, true);
    foreach($resApiArray as $publicSensor)
    {
        array_push($allowedElementIDs, $publicSensor['elementId']);
    }
}

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting IOT_Sensor_FeedDashboardWizard205 SCRIPT on ANT/HEL KB at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON IOT SENSORS

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
$ownership = "";
$organizationFromKb = "";
$organizations = "";
$organizationHelsTemplate = "Helsinki";
$organizationAntwTemplate = "Antwerp";
$kbUrl = "";

$s = "";
$a = "";
$dt = "";

$queryIotSensorDecoded = "select distinct ?s ?n ?a ?dt ?serviceType ?org ?brokerName ?lat ?lon { " .
   "?s a sosa:Sensor option (inference \"urn:ontology\"). " .
   "?s schema:name ?n. " .
   "?s km4c:hasAttribute ?a. " .
   "?s <http://purl.oclc.org/NET/UNIS/fiware/iot-lite#exposedBy> ?broker. " .
   "?broker <http://schema.org/name> ?brokerName. " .
   "?a km4c:data_type ?dt. " .
   "OPTIONAL {?s km4c:organization ?org.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
   "?s a ?sType. " .
   "?sType rdfs:subClassOf ?sCategory. " .
   "?sCategory rdfs:subClassOf km4c:Service. " .
   "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType)}";

$queryIotSensor = $kbHostUrlAntHel . ":8890/sparql?default-graph-uri=&query=" . urlencode($queryIotSensorDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

$queryIotSensorRresults = file_get_contents($queryIotSensor);
$resArray = json_decode($queryIotSensorRresults, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);

$count = 0;

foreach ($resArray['results']['bindings'] as $key => $val) {

    $count++;
    echo($count . " - IOT DEVICE: " . $s . ", MEASURE: " . $a . "\n");
    $s = $resArray['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
    $n = $resArray['results']['bindings'][$key]['n']['value'];   // $n --> service name
    $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute
    $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
    $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
    $availability = $resArray['results']['bindings'][$key]['av']['value'];
  //  $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
    $brokerName = $resArray['results']['bindings'][$key]['brokerName']['value'];
    $latitude = $resArray['results']['bindings'][$key]['lat']['value'];
    $longitude = $resArray['results']['bindings'][$key]['lon']['value'];

    $organizationFromKb = $resArray['results']['bindings'][$key]['org']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
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

    // $unique_name_id = explode($baseKm4CityUri, $s)[1];
    //  $unique_name_id = $n;
    $unique_name_id = $organizations . ":" . $brokerName . ":" . $n;

    if(in_array($unique_name_id, $allowedElementIDs)) {
        $ownership = "public";
    } else {
        $ownership = "private";
    }

    $serviceChangeBuffer["current"] = $unique_name_id;

    $nature = "From IOT Device to KB";
    $sub_nature = "IoTSensor";
    $low_level_type = explode($s . "/", $a)[1];
    $instance_uri = 'single_marker';
    $unit = $dt;
    $get_instances = $s;
    $metric = "no";
    $saved_direct = "direct";
    $kb_based = "yes";
    $sm_based = "yes";
    $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";

    if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
        $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
        mysqli_query($link, $insertGeneralServiceQuery);
    }

    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
    mysqli_query($link, $insertQuery);

    $serviceChangeBuffer["last"] = $unique_name_id;

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
echo("End IOT_Sensor_FeedDashboardWizard205 SCRIPT on ANT/HEL KB at: ".$end_time_ok);
