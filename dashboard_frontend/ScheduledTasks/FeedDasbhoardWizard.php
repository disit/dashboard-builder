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
ini_set('memory_limit', '2048M');
error_reporting(E_ERROR);

$processId = 1;     // 0 = SELECT & UPDATE; 1 = UPSERT
$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting FeedDashboardWizard on FLO/TUSC KB SCRIPT at: ".$start_time_ok."\n");

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

$organizations = "[\'DISIT\', \'Firenze\', \'Toscana\', \'Other\']";

$s = "";
$a = "";
$dt = "";
$sparqlErrorFlag = false;
$sparqlBatchCounter = 0;
$sparqlLimit = 1000;
$sparqlOffset = 0;

while ($sparqlErrorFlag === false) {
    $sparqlOffset = ($sparqlLimit * $sparqlBatchCounter) + 1;
    $queryAscapiEtlDecoded = "select distinct ?s ?a ?n ?avn ?avt ?dt ?u ?serviceType ?ow ?org ?lat ?lon {{ " .
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
        "MINUS{?s a km4c:IoTSensor} MINUS {?s a km4c:IoTActuator} " .
        "MINUS{?s a sosa:Sensor} MINUS {?s a sosa:Actuator}}} " .
        "OFFSET " . $sparqlOffset .
        " LIMIT " . $sparqlLimit;

    $queryAscapiEtl = $kbHostUrl . ":8890/sparql?default-graph-uri=&query=" . urlencode($queryAscapiEtlDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
    try {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'timeout' => 180,
                'ignore_errors' => true
            )
        );
        $context  = stream_context_create($options);
        $queryAscapiEtlRresults = file_get_contents($queryAscapiEtl, false, $context);
    } catch (Exception $e) {
        echo ("ERRORE SPARQL QUERY: " . $e->getMessage() . "\n");
        $sparqlErrorFlag = true;
    }
//    echo("SPARQL file_get_contents response: " . $queryAscapiEtlRresults. "\n");
    $resArrayEtl = json_decode($queryAscapiEtlRresults, true);
    echo("SPARQL Query (Not Encoded): " . $queryAscapiEtlDecoded. "\n");
    echo("SPARQL Query (URL-Encoded): " . $queryAscapiEtl. "\n");
    echo("SPARQL Response records length: " . sizeof($resArrayEtl['results']['bindings']) . "\n");
    if (sizeof($resArrayEtl['results']['bindings']) == 0) {
        $sparqlErrorFlag = true;
    }
    $serviceChangeBuffer = array(
        "last" => "",
        "current" => "",
    );

    $count = 0;

    foreach ($resArrayEtl['results']['bindings'] as $key => $val) {

            $count++;
            $batchCounter = $count + $sparqlOffset - 1;
            $s = $resArrayEtl['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
            $a = $resArrayEtl['results']['bindings'][$key]['a']['value'];   // $a --> attribute
            $n = $resArrayEtl['results']['bindings'][$key]['n']['value'];   // $n --> name
            $value_name = $resArrayEtl['results']['bindings'][$key]['avn']['value'];
            $value_type = $resArrayEtl['results']['bindings'][$key]['avt']['value'];
            $value_type = explode("value_type/", $value_type)[1];
            $dt = $resArrayEtl['results']['bindings'][$key]['dt']['value'];
            $u = $resArrayEtl['results']['bindings'][$key]['u']['value'];
            $serviceType = $resArrayEtl['results']['bindings'][$key]['serviceType']['value'];
         //   $unique_name_id = explode($baseKm4CityUri, $s)[1];
            if ($n && $n != '') {
                $unique_name_id = $n;
            } else {
                $unique_name_id = explode($baseKm4CityUri, $s)[1];
            }
         //   $unique_name_id = $n;
          //  $ownership = $resArrayEtl['results']['bindings'][$key]['ow']['value']; // $ow --> ownership

            $org = $resArrayEtl['results']['bindings'][$key]['org']['value'];
            if ($org && $org != '') {
                $organizations = $org;
            }

        $latitude = $resArrayEtl['results']['bindings'][$key]['lat']['value'];
        $longitude = $resArrayEtl['results']['bindings'][$key]['lon']['value'];

        echo($batchCounter . " - ETL/PHOENIX DEVICE: ".$s.", MEASURE: ".$a."\n");

        if ($serviceType === "Emergency_First_aid") {

            $queryName = $kbHostUrl . ":8890/sparql?default-graph-uri=&query=select+distinct+%3Fn+%7B+%3Chttp%3A%2F%2Fwww.disit.org%2Fkm4city%2Fresource%2F" . $unique_name_id_for_query ."%3E+%3Chttp%3A%2F%2Fschema.org%2Fname%3E+%3Fn.%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
            $queryNameRresults = file_get_contents($queryName);
            $ArrayName = json_decode($queryNameRresults, true);
            $n = $ArrayName['results']['bindings'][0]['n']['value'];

            $high_level_typeFA = "Special Widget";
            $natureFA = "HealthCare";
            $sub_natureFA = "First Aid Data";
        //    $unique_name_idFA = "FirstAid";
            $unique_name_idFA = "FirstAid";
            $unitFA = "special-first-aid";
         //   $instance_uriFA = "any";
            $get_instancesFA = "curr view (optional specific, shape, area, distance)";
            $sm_basedFA= "no";
            $ownershipFA = "public";
            $parametersFA = $s;
            $value_typeFA = "FirstAid";
            if ($n === "PRONTO SOCCORSO OSPEDALE SANTA MARIA ANNUNZIATA") {
                $last_valueFA = "S. Maria Annunziata";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERSMA";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO AZIENDA OSPEDALIERA CAREGGI") {
                $last_valueFA = "Careggi";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERCA";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE SERRISTORI") {
                $last_valueFA = "Serristori";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERSER";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE SAN GIOVANNI DI DIO TORREGALLI") {
                $last_valueFA = "Torregalli";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERTOR";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO AZIENDA OSPEDALIERA PISANA") {
                $last_valueFA = "Azienda Ospedaliera Pisana";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERPI";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE DI BORGO SAN LORENZO") {
                $last_valueFA = "Borgo S. Lorenzo";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERBSL";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE DELLA VALDINIEVOLE") {
                $last_valueFA = "Valdinievole";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERVAL";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE SANTA MARIA NUOVA") {
                $last_valueFA = "S. Maria Nuova";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERSMN";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PRONTO SOCCORSO OSPEDALE SAN JACOPO") {
                $last_valueFA = "S. Jacopo";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERSJ";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
            if ($n === "PUNTO DI PRIMO SOCCORSO DI SAN MARCELLO") {
                $last_valueFA = "S. Marcello";
                $value_nameFA = $last_valueFA;
                $instance_uriFA = "ERSM";
                $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations, latitude, longitude, value_name, value_type) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations', '$latitude', '$longitude', '$value_nameFA', '$value_typeFA') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_name = value_name, value_type = value_type;";
                mysqli_query($link, $insertGeneralServiceQueryFirstAid);
            }
        }

        if ($serviceType != "Emergency_First_aid" && $serviceType != "Pollen_monitoring_station") {

            $serviceChangeBuffer["current"] = $unique_name_id;

            $sub_nature_array = explode("_", $serviceType);
            //  if (sizeof($sub_nature_array) > 2) {
            $nature = explode("_", $serviceType)[0];
            $sub_nature = explode($nature . "_", $serviceType)[1];

            $low_level_type = explode($s . "/", $a)[1];

            //  $instance_uri = $s;
            $instance_uri = "any + status";

            //  $unique_name_id = explode($baseKm4CityUri, $s)[1];
            $unit = $dt;
            $get_instances = $s;

            $metric = "no";
            $saved_direct = "direct";
            $kb_based = "yes";
            $sm_based = "yes";

            $parameters = $graphURI . "api/v1/?serviceUri=" . $s . "&format=json";

            if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
                $sm_based = "yes";
                if ($processId == 0) {
                    $generalServiceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND unit = 'sensor_map' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                    $resGeneralServiceQuery = mysqli_query($link, $generalServiceQuery);
                    $result = [];
                    if($resGeneralServiceQuery) {
                        if ($row = mysqli_fetch_assoc($resGeneralServiceQuery)) {
                            $updateGeneralServiceQuery = "UPDATE DashboardWizard SET high_level_type = 'Sensor Device', device_model_name = '$unique_name_id' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "' AND unit = 'sensor_map';";
                            mysqli_query($link, $updateGeneralServiceQuery);
                        }
                    }
                } else {
                 //   if (stripos(explode($baseKm4CityUri, $s)[1], 'SIRSensor') !== false || stripos(explode($baseKm4CityUri, $s)[1], 'tusc_weather_sensor_ow_') !== false) {
                        $sir_unique_name_id = explode($baseKm4CityUri, $s)[1];
                        $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, device_model_name) VALUES ('$nature', 'Sensor Device', '$sub_nature','', '$sir_unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = 'Sensor Device', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $sir_unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', device_model_name = '" . $unique_name_id . "';";
                        mysqli_query($link, $insertGeneralServiceQuery);
                 /*   } else {
                        $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, device_model_name) VALUES ('$nature', 'Sensor Device', '$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = 'Sensor Device', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', device_model_name = '" . $unique_name_id . "';";
                        mysqli_query($link, $insertGeneralServiceQuery);
                    }   */
                }

            }

                if ($sub_nature === "Car_park") {

                    if ($s == "http://www.disit.org/km4city/resource/CarParkS.Lorenzo" || $s == "http://www.disit.org/km4city/resource/CarParkCareggi" || $s == "http://www.disit.org/km4city/resource/CarParkOltrarno" || $s == "http://www.disit.org/km4city/resource/CarParkPieracciniMeyer" || $s == "http://www.disit.org/km4city/resource/CarParkS.Ambrogio" || $s == "http://www.disit.org/km4city/resource/CarParkParterre" || $s == "http://www.disit.org/km4city/resource/CarParkAlberti" || $s == "http://www.disit.org/km4city/resource/CarParkBeccaria" || $s == "http://www.disit.org/km4city/resource/CarParkStazioneFortezzaFiera" || $s == "http://www.disit.org/km4city/resource/CarParkPal.Giustizia" || $s == "http://www.disit.org/km4city/resource/CarParkStazioneBinario16" || $s == "http://www.disit.org/km4city/resource/CarParkStazioneFirenzeS.M.N." || $s == "http://www.disit.org/km4city/resource/CarParkPortaalPrato") {

                        $unit_add = "integer";
                        $value_type = "car_park_free_places";

                        $low_level_type_add = "freeParkingLotsPrediction_15min";
                    //    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, device_model_name, value_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type_add', '$unique_name_id', '$instance_uri', '$get_instances', '$unit_add', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$unique_name_id', '$low_level_type_add') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type_add . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_type = NULL, value_name = '" . $low_level_type_add . "', device_model_name = '" . $unique_name_id . "';";
                        $insertQuery = "UPDATE DashboardWizard SET low_level_type = '" . $low_level_type_add . "', value_name = '" . $low_level_type_add . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type_add . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $insertQuery);

                        $low_level_type_add1 = "freeParkingLotsPrediction_30min";
                    //    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, device_model_name, value_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type_add1', '$unique_name_id', '$instance_uri', '$get_instances', '$unit_add', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$unique_name_id', '$low_level_type_add1') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type_add1 . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_type = NULL, value_name = '" . $low_level_type_add1 . "', device_model_name = '" . $unique_name_id . "';";
                        $insertQuery = "UPDATE DashboardWizard SET low_level_type = '" . $low_level_type_add1 . "', value_name = '" . $low_level_type_add1 . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type_add1 . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $insertQuery);

                        $low_level_type_add2 = "freeParkingLotsPrediction_45min";
                    //    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, device_model_name, value_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type_add2', '$unique_name_id', '$instance_uri', '$get_instances', '$unit_add', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$unique_name_id', '$low_level_type_add2') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type_add2 . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_type = NULL, value_name = '" . $low_level_type_add2 . "', device_model_name = '" . $unique_name_id . "';";
                        $insertQuery = "UPDATE DashboardWizard SET low_level_type = '" . $low_level_type_add2 . "', value_name = '" . $low_level_type_add2 . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type_add2 . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $insertQuery);

                        $low_level_type_add3 = "freeParkingLotsPrediction_1h";
                    //    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, device_model_name, value_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type_add3', '$unique_name_id', '$instance_uri', '$get_instances', '$unit_add', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$unique_name_id', '$low_level_type_add3') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type_add3 . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_type = NULL, value_name = '" . $low_level_type_add3 . "', device_model_name = '" . $unique_name_id . "';";
                        $insertQuery = "UPDATE DashboardWizard SET low_level_type = '" . $low_level_type_add3 . "', value_name = '" . $low_level_type_add3 . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type_add3 . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $insertQuery);

                    }

                    if ($processId == 0) {
                        $serviceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        $resServiceQuery = mysqli_query($link, $serviceQuery);
                        $result = [];
                        if($resServiceQuery) {
                            if ($row = mysqli_fetch_assoc($resServiceQuery)) {
                                $updateServiceQuery = "UPDATE DashboardWizard SET value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                                mysqli_query($link, $updateServiceQuery);
                            }
                        }
                    } else {
                        $sir_unique_name_id = explode($baseKm4CityUri, $s)[1];
                        $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, value_name, value_type, device_model_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$sir_unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$value_name', '$value_type', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $sir_unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "';";
                        mysqli_query($link, $insertQuery);

                    //    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, value_name, value_type, device_model_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$value_name', '$value_type', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "';";
                    //    mysqli_query($link, $insertQuery);
                    }

                    $serviceChangeBuffer["last"] = $unique_name_id;

                } else {

                    if ($processId == 0) {
                        $serviceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        $resServiceQuery = mysqli_query($link, $serviceQuery);
                        $result = [];
                        if($resServiceQuery) {
                            if ($row = mysqli_fetch_assoc($resServiceQuery)) {
                                $updateServiceQuery = "UPDATE DashboardWizard SET value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                                mysqli_query($link, $updateServiceQuery);
                            }
                        }
                    } else {
                     //   if (stripos(explode($baseKm4CityUri, $s)[1], 'SIRSensor') !== false || stripos(explode($baseKm4CityUri, $s)[1], 'tusc_weather_sensor_ow_') !== false) {
                            $sir_unique_name_id = explode($baseKm4CityUri, $s)[1];
                            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, value_name, value_type, device_model_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$sir_unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$value_name', '$value_type', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $sir_unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "';";
                            mysqli_query($link, $insertQuery);
                   /*     } else {
                            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, value_name, value_type, device_model_name) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$value_name', '$value_type', '$unique_name_id') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $unique_name_id . "';";
                            mysqli_query($link, $insertQuery);
                        }   */
                    }

                    $serviceChangeBuffer["last"] = $unique_name_id;
                }
        //    }

        } else {
            $stop_flag = 4;
        }

    }

    $sparqlBatchCounter++;

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
echo("End FeedDashboardWizard SCRIPT at: ".$end_time_ok);
