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

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting SCRIPT HealthinessCheck for CAR PARK PREDICTIONS at: ".$start_time_ok."\n");

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

if (defined('STDIN')) {
    if ($argv[1]) {
        $id_arg = $argv[1];
        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE (sub_nature = 'Car_park' AND low_level_type REGEXP 'freeParkingLotsPrediction') AND id > ".$id_arg ." GROUP BY unique_name_id ORDER BY id DESC;";
    } else {
        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE sub_nature = 'Car_park' AND low_level_type REGEXP 'freeParkingLotsPrediction' GROUP BY unique_name_id ORDER BY id DESC;";
    }
} else {
    $query = "SELECT * FROM Dashboard.DashboardWizard WHERE sub_nature = 'Car_park' AND low_level_type REGEXP 'freeParkingLotsPrediction' GROUP BY unique_name_id ORDER BY id DESC;";
}


$rs = mysqli_query($link, $query);
$result = [];


if ($rs) {
    $result = [];
    $count = 0;
    try {
        while ($row = mysqli_fetch_assoc($rs)) {
            $high_level_type = $row['high_level_type'];
            $nature = $row['nature'];
            $sub_nature = $row['sub_nature'];
            $low_level_type = $row['low_level_type'];
            $unique_name_id = $row['unique_name_id'];

            $instance_uri = $row['instance_uri'];
            //   $unit = $row[unit];
            $metric = $row['metric'];
            $saved_direct = $row['saved_direct'];
            $kb_based = $row['kb_based'];
            $parameters = $row['parameters'];

            array_push($result, $row);
            $instance_uri = "any";
            $url = $row['parameters'];

            $last_value_as_name = $row['last_value'];

            $url_for_sparql_query = str_replace("https", "http", $url)."&healthiness=true";
            $response = file_get_contents($url_for_sparql_query);
            $responseArray = json_decode($response, true);

            $realtime_data = $responseArray['vars']['results']['bindings'][0];
            $healthiness = $responseArray['healthiness'];
            $predictions = $responseArray['predictions'];

            $now = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $date_now = $now->format('c');
            $date_now_ok = explode("+", $date_now);
            $check_time = str_replace("T", " ", $date_now_ok[0]);
            //     print_r($check_time);

            if ($sub_nature === 'Car_park') {
                $stop_flag = 1;
            }

        //    if (!empty($realtime_data)) {
            if (!empty($predictions)) {
                if (strpos($unique_name_id, 'METRO') !== false) {
                    $last_date = $realtime_data['instantTime']['value'];
                } else {
                    $last_date = $realtime_data['measuredTime']['value'];
                }

                $last_date_ok = explode("+", $last_date);
                $last_date_wonderful = str_replace("T", " ", $last_date_ok[0]);

                $true_flag = 0;
                $num = 0;
                foreach ($predictions as $key => $item) {

                    $num++;
                    if ($num == 1) {
                        $low_level_type = "freeParkingLotsPrediction_15min";
                    } else if ($num == 2) {
                        $low_level_type = "freeParkingLotsPrediction_30min";
                    } else if ($num == 3) {
                        $low_level_type = "freeParkingLotsPrediction_45min";
                    } else if ($num == 4) {
                        $low_level_type = "freeParkingLotsPrediction_1h";
                    }

                    $last_value  = $item['freePrediction'];
                    $last_date_raw = $item['datePrediction'];
                    $last_date = str_replace(".000", "", $last_date_raw);

                    echo($count . " " . $unique_name_id . " - ". $low_level_type . "\n");

                    // CAMBIARE HEALTHINESS QUANDO ARRIVA DA PIERO !!!
                    $query_update = "UPDATE DashboardWizard SET last_date= '" . $last_date . "', last_value = '" . $last_value . "', healthiness = 'true', lastCheck = '" . $start_time_ok . "' WHERE sub_nature = 'Car_park' AND unique_name_id = '".$unique_name_id."' AND low_level_type = '" . $low_level_type . "';";
                    //    $rs = mysqli_query($link, $query_update);
                    mysqli_query($link, "UPDATE DashboardWizard SET last_date= '" . $last_date . "', last_value = '" . $last_value . "', healthiness = 'true', lastCheck = '" . $start_time_ok . "' WHERE sub_nature = 'Car_park' AND unique_name_id = '".$unique_name_id."' AND low_level_type = '" . $low_level_type . "';");

                }

                //**********************************************************************************

            }

            $count++;

        }
    } catch (Exception $e) {
        echo 'Exception: ',  $e->getMessage(), "\n";
    }

} else {
    mysqli_close($link);
    $result['detail'] = 'Ko';
}

//mysqli_query($link, "UPDATE DashboardWizard SET healthiness = 'false' WHERE healthiness IS NULL OR healthiness = '';");

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End HealthinessCheck SCRIPT at: ".$end_time_ok);
//echo json_encode($result);