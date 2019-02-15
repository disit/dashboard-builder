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
echo("Starting FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON ETL REALTIME

$high_level_type = "Special Widget";
$nature = "Environment";
$sub_nature_array = [];
$sub_nature = "Weather Forecast";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "Previ_Meteo";
$instance_uri = "";
$get_instances = "";
$unit = "special weather";
$metric = "";
$saved_direct = "saved";
$kb_based = "yes";
$sm_based = "no";
$parameters = "";
$healthiness = "true";
$last_value = "";
$ownership = "public";

$baseKm4CityUri = "http://www.disit.org/km4city/resource/";

$s = "";
$a = "";
$dt = "";

$queryPreviMeteo = "http://192.168.0.206:8890/sparql?default-graph-uri=&query=select+distinct+%3Floc+%3FlocName+where+%7B%3Fs+a+km4c%3AWeatherReport.+%3Fs+km4c%3ArefersToMunicipality+%3Floc.+%3Floc+foaf%3Aname+%3FlocName.%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

$queryPreviMeteo = file_get_contents($queryPreviMeteo);
$resArrayPreviMeteo = json_decode($queryPreviMeteo, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);

$count = 0;

foreach ($resArrayPreviMeteo['results']['bindings'] as $key => $val) {

    $count++;
    $parameters = $val['locName']['value'];
    $instance_uri = $parameters;
    $last_value = strtolower($parameters);
    $last_value = ucfirst($last_value);

    echo($count." - WEATHER FORECAST OF: ".$instance_uri."\n");

    $insertQueryPreviMeteo = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$start_time_ok', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $start_time_ok . "', ownership = '" . $ownership . "';";
    mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$start_time_ok', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $start_time_ok . "', ownership = '" . $ownership . "';");

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