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
echo("Starting IOT_Sensor_FeedDashboardWizard205 SCRIPT on 192.168.0.205 at: ".$start_time_ok."\n");

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

// $baseKm4CityUri = "http://www.disit.org/km4city/resource/";
$kbUrlHelsinki = "https://helsinki.snap4city.org/ServiceMap/api/v1/";
$kbUrlAntwerp = "https://antwerp.snap4city.org/ServiceMap/api/v1/";
$kbUrlSuperServiceMap = "https://www.disit.org/superservicemap/api/v1/";
$kbUrl = "";

$s = "";
$a = "";
$dt = "";

// $queryIotSensor = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fn+%3Fa+%3Fdt+%3FserviceType+%3Fav+%3Fow+%7B%3Fs+a+km4c%3AIoTSensor.+%3Fs+schema%3Aname+%3Fn.+%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aavailability+%3Fav.%7D+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
// $queryIotSensor = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fn+%3Fa+%3Fdt+%3FserviceType+%3Fav+%3Fow+%3Forg+%7B%3Fs+a+km4c%3AIoTSensor.+%3Fs+schema%3Aname+%3Fn.+%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aavailability+%3Fav.%7D+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+OPTIONAL+%7B%3Fs+km4c%3Aorganization+%3Forg.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

// LAST OK
// $queryIotSensor = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fn+%3Fa+%3Fdt+%3FserviceType+%3Fav+%3Fow+%3Forg+%3FbrokerName+%7B%3Fs+a+sosa%3ASensor+option+%28inference+%22urn%3Aontology%22%29.+%3Fs+schema%3Aname+%3Fn.+%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fs+%3Chttp%3A%2F%2Fpurl.oclc.org%2FNET%2FUNIS%2Ffiware%2Fiot-lite%23exposedBy%3E+%3Fbroker.+%3Fbroker+%3Chttp%3A%2F%2Fschema.org%2Fname%3E+%3FbrokerName.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aavailability+%3Fav.%7D+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+OPTIONAL+%7B%3Fs+km4c%3Aorganization+%3Forg.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

$queryIotSensorDecoded = "select distinct ?s ?n ?a ?dt ?serviceType ?av ?ow ?org ?brokerName ?lat ?lon { " .
   "?s a sosa:Sensor option (inference \"urn:ontology\"). " .
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

$queryIotSensor = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=" . urlencode($queryIotSensorDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

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
    $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
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

    if ($availability != '') {
        $ownership = $availability;
    } else if ($ownShip != '') {
        $ownership = $ownShip;
    } else {
        $ownership = "public";
    }
    // $unique_name_id = explode($baseKm4CityUri, $s)[1];
  //  $unique_name_id = $n;
    $unique_name_id = $organizations . ":" . $brokerName . ":" . $n;

    $serviceChangeBuffer["current"] = $unique_name_id;

    //  $sub_nature_array = explode("_", $serviceType);
    //  if (sizeof($sub_nature_array) > 2) {
    //  $nature = explode("_", $serviceType)[0];
    $nature = "From IOT Device to KB";
    //  $sub_nature = explode($nature . "_", $serviceType)[1];
    /*  } else {
          $nature = explode("_", $serviceType)[0];
          $sub_nature = explode($nature."_", $serviceType)[1];
      }   */
    $sub_nature = "IoTSensor";

    $low_level_type = explode($s . "/", $a)[1];

    $instance_uri = 'single_marker';
    //$instance_uri = 'any + status';

    //  $unique_name_id = explode($baseKm4CityUri, $s)[1];
    $unit = $dt;
    $get_instances = $s;

    $metric = "no";
    $saved_direct = "direct";
    $kb_based = "yes";
    $sm_based = "yes";

  //  $parameters = "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=".$s."&format=json";     // CAMBIARE CON API NUOVE DI PIERO QUANDO E' PRONTA LA GET tipo:
    $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";      // CAMBIARE CON API NUOVE DI PIERO QUANDO E' PRONTA LA GET

    //  $healthiness = "na";
    if ($ownership != "private") {
        $ownership = "public";
    }

    if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
        //    $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', 'map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;";
        //    mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', 'map', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;");
        //    $sm_based = "yes";
        $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
        mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

        // ANY + STATUS
        /*   $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', 'any + status', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = 'any + status',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';";
           mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', 'any + status', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = 'any + status', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';");
       */

    }

    // $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$unit', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;";
    // mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$unit', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;");
    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';";
    mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "';");

    $serviceChangeBuffer["last"] = $unique_name_id;

}

// Eventualmente eseguire da qui HealthinessCheck.php ?
// include 'HealthinessCheck.php';

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
echo("End IOT_Sensor_FeedDashboardWizard205 SCRIPT on 192.168.0.205 at: ".$end_time_ok);
