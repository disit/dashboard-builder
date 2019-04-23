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
echo("Starting FeedDashboardWizard205 SCRIPT on 192.168.0.205 at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON ETL REALTIME HELSINKI / ANTWERP 205

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

$baseKm4CityUri = "http://www.disit.org/km4city/resource/";
$kbUrlHelsinki = "https://helsinki.snap4city.org/ServiceMap/api/v1/";
$kbUrlAntwerp = "https://antwerp.snap4city.org/ServiceMap/api/v1/";
$kbUrlSuperServiceMap = "https://www.disit.org/superservicemap/api/v1/";
$kbUrl = "";

$s = "";
$a = "";
$dt = "";

//$queryAscapiEtl = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fa+%3Fdt+%3FserviceType+%3Fow+%7B%7B%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29+MINUS%7B%3Fs+a+km4c%3AIoTSensor%7D+MINUS+%7B%3Fs+a+km4c%3AIoTActuator%7D%7D%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";


// LAST OK
// $queryAscapiEtl = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fs+%3Fa+%3Fdt+%3FserviceType+%3Fow+%3Forg+%7B%7B%3Fs+km4c%3AhasAttribute+%3Fa.+%3Fa+km4c%3Adata_type+%3Fdt.+OPTIONAL+%7B%3Fs+km4c%3Aownership+%3Fow.%7D+OPTIONAL+%7B%3Fs+km4c%3Aorganization+%3Forg.%7D+%3Fs+a+%3FsType.+%3FsType+rdfs%3AsubClassOf+%3FsCategory.+%3FsCategory+rdfs%3AsubClassOf+km4c%3AService.+bind%28concat%28replace%28str%28%3FsCategory%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%2C%22_%22%2Creplace%28str%28%3FsType%29%2C%22http%3A%2F%2Fwww.disit.org%2Fkm4city%2Fschema%23%22%2C%22%22%29%29+as+%3FserviceType%29+MINUS%7B%3Fs+a+km4c%3AIoTSensor%7D+MINUS+%7B%3Fs+a+km4c%3AIoTActuator%7D%7D%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

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
    "MINUS{?s a km4c:IoTSensor} MINUS {?s a km4c:IoTActuator}}}";

$queryAscapiEtl = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=" . urlencode($queryAscapiEtlDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

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
  //  $unique_name_id = explode($baseKm4CityUri, $s)[1];
    if ($n && $n != '') {
        $unique_name_id = $n;
    } else {
        $unique_name_id = explode($baseKm4CityUri, $s)[1];
    }
 //   $ownership = $resArrayEtl['results']['bindings'][$key]['ow']['value']; // $ow --> ownership
    $organizationFromKb = $resArrayEtl['results']['bindings'][$key]['org']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
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
 /*
    if ($serviceType === "Emergency_First_aid") {

        $queryName = "http://192.168.0.205:8890/sparql?default-graph-uri=&query=select+distinct+%3Fn+%7B+%3Chttp%3A%2F%2Fwww.disit.org%2Fkm4city%2Fresource%2F" . $unique_name_id ."%3E+%3Chttp%3A%2F%2Fschema.org%2Fname%3E+%3Fn.%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
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
        //if (strpos($n, 'careggi') !== false) {
        if ($n === "PRONTO SOCCORSO OSPEDALE SANTA MARIA ANNUNZIATA") {
            $last_valueFA = "S. Maria Annunziata";
            $instance_uriFA = "ERSMA";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO AZIENDA OSPEDALIERA CAREGGI") {
            $last_valueFA = "Careggi";
            $instance_uriFA = "ERCA";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE SERRISTORI") {
            $last_valueFA = "Serristori";
            $instance_uriFA = "ERSER";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE SAN GIOVANNI DI DIO TORREGALLI") {
            $last_valueFA = "Torregalli";
            $instance_uriFA = "ERTOR";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO AZIENDA OSPEDALIERA PISANA") {
            $last_valueFA = "Azienda Ospedaliera Pisana";
            $instance_uriFA = "ERPI";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE DI BORGO SAN LORENZO") {
            $last_valueFA = "Borgo S. Lorenzo";
            $instance_uriFA = "ERBSL";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE DELLA VALDINIEVOLE") {
            $last_valueFA = "Valdinievole";
            $instance_uriFA = "ERVAL";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE SANTA MARIA NUOVA") {
            $last_valueFA = "S. Maria Nuova";
            $instance_uriFA = "ERSMN";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . ", organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . ", organizations = '" . $organizations . "';");
        }
        if ($n === "PRONTO SOCCORSO OSPEDALE SAN JACOPO") {
            $last_valueFA = "S. Jacopo";
            $instance_uriFA = "ERSJ";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
        if ($n === "PUNTO DI PRIMO SOCCORSO DI SAN MARCELLO") {
            $last_valueFA = "S. Marcello";
            $instance_uriFA = "ERSM";
            $insertGeneralServiceQueryFirstAid = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null, '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "',  get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';";
            mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_value, healthiness, ownership, organizations) VALUES ('$natureFA','$high_level_typeFA','$sub_natureFA', '', '$unique_name_idFA', '$instance_uriFA', '$get_instancesFA', '$unitFA', null, null, null,  '$sm_basedFA', '$parametersFA', '$last_valueFA', '$healthinessFA', '$ownershipFA', '$organizations') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_typeFA . "', sub_nature = '" . $sub_natureFA . "', low_level_type = '', unique_name_id = '" . $unique_name_idFA . "', instance_uri = '" . $instance_uriFA . "', get_instances = '" . $get_instancesFA . "', sm_based = '" . $sm_basedFA . "', last_date = last_date, last_value = last_value, parameters = '" . $parametersFA . "', healthiness = healthiness, ownership = '" . $ownershipFA . "', organizations = '" . $organizations . "';");
        }
    }
        */

    if ($serviceType != "Emergency_First_aid" && $serviceType != "Pollen_monitoring_station") {

        $serviceChangeBuffer["current"] = $unique_name_id;

        $sub_nature_array = explode("_", $serviceType);
        //  if (sizeof($sub_nature_array) > 2) {
        $nature = explode("_", $serviceType)[0];
        $sub_nature = explode($nature . "_", $serviceType)[1];
        /*  } else {
              $nature = explode("_", $serviceType)[0];
              $sub_nature = explode($nature."_", $serviceType)[1];
          }   */

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


        //    $parameters = $sub_nature;
    //   $parameters = $kbUrl ."?serviceUri=http://www.disit.org/km4city/resource/" . $unique_name_id . "&format=json";      // CAMBIARE CON API NUOVE DI PIERO QUANDO E' PRONTA LA GET
        $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";
   /*     if ($ownership != "private") {
            $ownership = "public";
        }   */
        //  $healthiness = "na";

        if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
            //    $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', 'map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;";
            //    mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', 'map', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$healthiness') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', last_date = last_date, last_value = last_value, parameters = parameters, healthiness = healthiness;");
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
echo("End FeedDashboardWizard205 SCRIPT on 192.168.0.205 at: ".$end_time_ok);