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
include '../opensearch/OpenSearchS4C.php';
$open_search = new OpenSearchS4C();
$open_search->initDashboardWizard();

error_reporting(E_ERROR);

$link = mysqli_connect($host, $username, $password);

$high_level_type = "";
$nature = "";
$sub_nature = "";
$low_level_type = "";
$unique_name_id = "";
$instance_uri = "";
$unit = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$parameters = "";

$host_pd= $host_PD;
$token_endpoint= $token_endpoint_PD;
$client_id= $client_id_PD;
$username= $usernamePD;
$password= $passwordPD;

//$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$current_dateTimeZone = new DateTimeZone('Europe/Rome');
$startTime = new DateTime("now", $current_dateTimeZone);
$offset = $current_dateTimeZone->getOffset($startTime);
//$startTime = new DateTime(null, new DateTimeZone('GMT'));
$start_scritp_time = $startTime->format('c');
//$start_scritp_time_string = explode("+", $start_scritp_time);
//$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
$start_time_ok = str_replace("T", " ", $start_scritp_time);
echo("Starting MyKPICheck SCRIPT at: ".$start_time_ok."\n");

//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

// MyKPI Check if updated entries in wizard
$queryMyKPI = "SELECT * FROM Dashboard.DashboardWizard WHERE (DashboardWizard.high_level_type = 'MyKPI' OR DashboardWizard.high_level_type = 'MyPOI') GROUP BY unique_name_id ORDER BY id DESC;";
// $rsMyKPI = mysqli_query($link, $queryMyKPI);
$resultMyKPI = [];
$serviceChangeBufferMyKPI = array(
    "last" => "",
    "current" => "",
);

$rsMyKPI = $open_search->getMyKPI();

$accessToken=get_access_token($token_endpoint, $username, $password, $client_id, $client_secret);
if (empty($accessToken)) {
    exit("\nAccess Token Not Valid. Program Terminated.\n");
}

//if ($rsMyKPI) {
if (sizeof($rsMyKPI) > 0) {
    $result = [];
    $count = 0;
    $oldEntries = [];
    $noFeatures = [];
    try {
        //while ($rowMyKPI = mysqli_fetch_assoc($rsMyKPI)) {
        foreach ( $rsMyKPI as $rowMyKPI ) {
            $myKpiId = "";
            $high_level_type = $rowMyKPI['high_level_type'];
            if ($high_level_type == 'MyPOI') {
                $stopFlag = 1;
            }
            $nature = $rowMyKPI['nature'];
            $sub_nature = $rowMyKPI['sub_nature'];
            $low_level_type = $rowMyKPI['low_level_type'];
            $unique_name_id = $rowMyKPI['unique_name_id'];
            $get_instances = $rowMyKPI['get_instances'];
            $myKpiId = $get_instances;

            $instance_uri = $rowMyKPI['instance_uri'];
            //   $unit = $row[unit];
            $metric = $rowMyKPI['metric'];
            $saved_direct = $rowMyKPI['saved_direct'];
            $kb_based = $rowMyKPI['kb_based'];
            $parameters = $rowMyKPI['parameters'];

            $genFileContent = parse_ini_file("../conf/environment.ini");
            $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
            $env = $genFileContent['environment']['value'];

            $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $date_now = $now->format('c');
            $date_now_ok = explode("+", $date_now);
            $check_time = str_replace("T", " ", $date_now_ok[0]);

            $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];

            if (strpos($myKpiId, "datamanager/api/v1") !== false) {
                $myKpiId = explode("datamanager/api/v1/poidata/", $get_instances)[1];
            } else {
                $myKpiId = $get_instances;
            }

            $myKpiDataArray = [];
            //    $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/values/dates?sourceRequest=dashboardmanager&accessToken=" . $accessToken;
            $apiUrl = $personalDataApiBaseUrl . "/v1/kpidata/" . $myKpiId . "/?sourceRequest=dashboardmanager&accessToken=" . $accessToken;

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'GET',
                    'timeout' => 30,
                    'ignore_errors' => true
                )
            );

            $context = stream_context_create($options);
            $myKpiDataJson = file_get_contents($apiUrl, false, $context);

            $myKpiData = json_decode($myKpiDataJson);

            if (isset($myKpiData->result)) {
                if ($myKpiData->result === false) {   // $myKpiData['result'] != false
                    echo($check_time . " - MyKPI ID " . $get_instances . ": " . $unique_name_id . " Marked as OLD.");
                    $query_updateOld = "UPDATE DashboardWizard SET oldEntry = 'old', healthiness = 'false', lastCheck = '" . $check_time . "' WHERE get_instances = '" . $get_instances . "';";
                    //mysqli_query($link, $query_updateOld);
                    array_push($oldEntries, $myKpiId);

                    $open_search->healthinessUpdate($get_instances,$check_time,'old','false');
                    
                    //continue;
                }
            }

        }
    } catch (Exception $err) {
        echo 'Exception in MyKPI Check: ',  $err->getMessage(), "\n";
    }
}

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End MyKPICheck SCRIPT at: ".$end_time_ok);