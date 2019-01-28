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
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "";
$ownership = "";

// $baseKm4CityUri = "http://www.disit.org/km4city/resource/";

$s = "";
$a = "";
$dt = "";
$actSensArray = [];

//$query = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE actuatorTarget = 'broker';";
$rs = mysqli_query($link, $query);
$result = [];
//if($rs) {

//    while ($row = mysqli_fetch_assoc($rs)) {
 //       array_push($actSensArray, $row['name_w']);
 //   }
//}

// $actSensKbQuery = "http://192.168.0.206:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fn+%3Fa+%3Fdt+%3FserviceType+%3Fav+%7B%7B%3Fs+a+km4c%3AIoTActuator.%7D+UNION+%7B%3Fs+a+km4c%3AIoTSensor.%7D+%3Fs+schema%3Aname+%3Fn.+%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aavailability+%3Fav.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

// QUERY CON OWNERSHIP NEW !!
$actSensKbQuery = "http://192.168.0.206:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fn+%3Fa+%3Fdt+%3FserviceType+%3Fav+%3Fow+%7B%7B%3Fs+a+km4c%3AIoTActuator.%7D+UNION+%7B%3Fs+a+km4c%3AIoTSensor.%7D+%3Fs+schema%3Aname+%3Fn.+%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aavailability+%3Fav.%7D+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

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
        echo($count . " - IOT ACTUATOR: " . $s . ", MEASURE: " . $a . "\n");
        $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute
        if (strpos($a, "actuatorcanceller") === false && strpos($a, "actuatorCanceller") === false && strpos($a, "actuatordeleted") === false && strpos($a, "actuatorDeleted") === false && strpos($a, "actuatordeletiondate") === false && strpos($a, "actuatorDeletionDate") === false && strpos($a, "creationdate") === false && strpos($a, "creationDate") === false && strpos($a, "entitycreator") === false && strpos($a, "entityCreator") === false && strpos($a, "entitydesc") === false && strpos($a, "entityDesc") === false) {
            $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
            $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
            $availability = $resArray['results']['bindings'][$key]['av']['value'];
            $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
            if ($availability != '') {
                $ownership = $availability;
            } else if ($ownShip != '') {
                $ownership = $ownShip;
            } else {
                $ownership = "public";
            }
            // $unique_name_id = explode($baseKm4CityUri, $s)[1];
            $unique_name_id = $n;

            $serviceChangeBuffer["current"] = $unique_name_id;

            //  $sub_nature_array = explode("_", $serviceType);
            //  if (sizeof($sub_nature_array) > 2) {
            //  $nature = explode("_", $serviceType)[0];
            $nature = "From Dashboard to IOT Device";
            //  $sub_nature = explode($nature . "_", $serviceType)[1];
            /*  } else {
                  $nature = explode("_", $serviceType)[0];
                  $sub_nature = explode($nature."_", $serviceType)[1];
              }   */
            $sub_nature = "IoTSensor-Actuator";

            $low_level_type = explode($s . "/", $a)[1];
            /*
                    if ($low_level_type = 'actuatordeleted') {
                        $queryDeleted = urlencode("select distinct * where {<" . $a . "> <http://www.disit.org/km4city/schema#editable> ?canc}");
                    }
            */
            $instance_uri = 'single_marker';

            //  $unique_name_id = explode($baseKm4CityUri, $s)[1];
            $unit = $dt;
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

            $parameters = "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=" . $s . "&format=json";
            //  $healthiness = "na";
            if ($ownership != "private") {
                $ownership = "public";
            }

            /*
              if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
                  // $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;";
                  // mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;");
                  $sm_based = "yes";
                  $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';";
                  mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';");
              }
              $sm_based = "no";
            */

            // $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$unit', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;";
            // mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$unit', '$metric', '$saved_direct', '$kb_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;");
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "';");

            $serviceChangeBuffer["last"] = $unique_name_id;
        }
  //  }
}

// Eventualmente eseguire da qui HealthinessCheck.php ?
// include 'HealthinessCheck.php';

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End IOT_Actuator_FeedDashboardWizard SCRIPT at: ".$end_time_ok);


