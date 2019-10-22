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

function udate($format = 'u', $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

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

error_reporting(E_ERROR);
mysqli_report(MYSQLI_REPORT_ALL) ;

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting Personal_Data_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$host_pd= $host_PD;
$token_endpoint= $token_endpoint_PD;
$client_id= $client_id_PD;
$username= $usernamePD;
$password= $passwordPD;


//$high_level_type_KPI = "MyKPI";
$high_level_type_KPI = "";
$nature_KPI = "";
$sub_nature_array_KPI = [];
$sub_nature_KPI = "";
$low_level_type_KPI = "";
$unique_name_to_split_KPI = "";
$unique_name_id_KPI = "";
$instance_uri_KPI = "MyKPI";
$get_instances_KPI = "";
$metric_KPI = "";
$saved_direct_KPI = "";
$kb_based_KPI = "";
$sm_based_KPI = "";
$unit_KPI = "";
$parameters_KPI = "";
$healthiness_KPI = "";
$ownership_KPI = "";
$lastCheck_KPI = "";
$organizations_KPI = "";
$parameters_KPI = "";

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);

$test = "yes";
if ($test === "yes") {
    $queryApiPersonalKPI = $host_pd.":8080/datamanager/api/v1/kpidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyKPI";
}
$queryPersonalKPIRresults = file_get_contents($queryApiPersonalKPI);
$resKPIArray = json_decode($queryPersonalKPIRresults, true);

foreach ($resKPIArray as $resKPIRecord) {

    $count++;
    if ($resKPIRecord['latitude'] == "" && $resKPIRecord['longitude'] == "") {

        $parameters_KPI = $resKPIRecord['id'];
        $querySearchGeolocatedKPI = $host_pd.":8080/datamanager/api/v1/poidata/" . $resKPIRecord['id'] . "/?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . "&last=0";
        $geolocatedKPIRresults = file_get_contents($querySearchGeolocatedKPI);
        $resGeolocatedKPIArray = json_decode($geolocatedKPIRresults, true);
        if (sizeof($resGeolocatedKPIArray['geometry']['coordinates']) != 0) {
            $latitude_KPI = $resKPIRecord['latitude'];
            $longitude_KPI = $resKPIRecord['longitude'];
        } else {
            $latitude_KPI = "";
            $longitude_KPI = "";
        }
    } else {
        $parameters_KPI = "datamanager/api/v1/poidata/" . $resKPIRecord['id'];
        $latitude_KPI = $resKPIRecord['latitude'];
        $longitude_KPI = $resKPIRecord['longitude'];
    }
    $high_level_type_KPI = $resKPIRecord['highLevelType'];
    $nature_KPI = $resKPIRecord['nature'];
    $low_level_type_KPI = $resKPIRecord['valueType'];
    $sub_nature_KPI = $resKPIRecord['subNature'];
    $unique_name_id_KPI = addslashes($resKPIRecord['valueName']);
    $last_value_KPI = $resKPIRecord['lastValue'];
    $get_instances_KPI = $resKPIRecord['getInstances'];
    if ($get_instances_KPI == "") {
        $get_instances_KPI = $parameters_KPI;
    }
    $unit_KPI = $resKPIRecord['dataType'] . "-mykpi";
    $kpiId = $get_instances_KPI;
    $last_date_KPI_millis = $resKPIRecord['lastDate'];
    $last_date_KPI = date("Y-m-d H:i:s",$last_date_KPI_millis/1000);

    $ownership_KPI = $resKPIRecord['ownership'];

    $metric_KPI = $resKPIRecord['metric'];
    $saved_direct_KPI = $resKPIRecord['savedDirect'];
    $kb_based_KPI = $resKPIRecord['kbBased'];
    if ($resKPIRecord['smBased'] == "") {
        $sm_based_KPI = "myKPI";
    } else {
        $sm_based_KPI = $resKPIRecord['smBased'];
    }
  //  $healthiness_KPI = $resKPIRecord['healthiness'];
    $healthiness_KPI = "true";
    $lastCheck_KPI = $lastCheck;
    $organizations_KPI = $resKPIRecord['organizations'];
    $organizations_KPI = explode( "ou=", $organizations_KPI)[1];
    $organizations_KPI = explode(",dc=ldap,dc=disit,dc=org]", $organizations_KPI)[0];

    if (!is_null($nature_KPI)) {
        if ($nature_KPI != '') {
            echo($count . " - MY KPI: " . $nature_KPI . ", PERSONAL DATA VARIABLE-NAME: " . $unique_name_id_KPI . ", MOTIVATION: " . $low_level_type_KPI . ", KPI-ID: " . $kpiId . "\n");
            $insertQuery_KPI = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude) VALUES ('$nature_KPI','$high_level_type_KPI','$sub_nature_KPI','$low_level_type_KPI', '$unique_name_id_KPI', '$instance_uri_KPI', '$get_instances_KPI', '$unit_KPI', '$metric_KPI', '$saved_direct_KPI', '$kb_based_KPI', '$sm_based_KPI', '$parameters_KPI', '$last_date_KPI', '$last_value_KPI', '$healthiness_KPI', '$lastCheck_KPI', '$ownership_KPI', '$organizations_KPI', '$latitude_KPI', '$longitude_KPI') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type_KPI . "', sub_nature = '" . $sub_nature_KPI . "', low_level_type = '" . $low_level_type_KPI . "', unique_name_id = '" . $unique_name_id_KPI . "', instance_uri = '" . $instance_uri_KPI . "', get_instances = '" . $get_instances_KPI . "', unit = '" . $unit_KPI . "', sm_based = '" . $sm_based_KPI . "', last_date = '" . $last_date_KPI . "', last_value = '" . $last_value_KPI . "', parameters = '" . $parameters_KPI . "', healthiness = '" . $healthiness_KPI . "', lastCheck = '" . $lastCheck_KPI . "', ownership = '" . $ownership_KPI . "', organizations = '" . $organizations_KPI . "', latitude = '" . $latitude_KPI . "', longitude = '" . $longitude_KPI . "';";
            try {
                //   mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "';");
                mysqli_query($link, $insertQuery_KPI);

                //   echo ("\nINSERT QUERY ON DUPLICATE KEY: ".$insertQuery."\n");
            } catch (Exception $e) {
                echo $e->getMessage();
                $updtQuery_KPI = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type_KPI . "', sub_nature = '" . $sub_nature_KPI . "', low_level_type = '" . $low_level_type_KPI . "', unique_name_id = '" . $unique_name_id_KPI . "', instance_uri = '" . $instance_uri_KPI . "', get_instances = '" . $get_instances_KPI . "', sm_based = '" . $sm_based_KPI . "', last_date = '" . $last_date_KPI . "', last_value = '" . $last_value_KPI . "', parameters = '" . $parameters_KPI . "', healthiness = '" . $healthiness_KPI . "', lastCheck = '" . $lastCheck_KPI . "', ownership = '" . $ownership_KPI . "', latitude = '" . $latitude_KPI . "', longitude = '" . $longitude_KPI . "' WHERE high_level_type = '" . $high_level_type_KPI . "' AND sub_nature = '" . $sub_nature_KPI . "' AND low_level_type = '" . $low_level_type_KPI . "' AND unique_name_id = '" . $unique_name_id_KPI . "' AND instance_uri = '" . $instance_uri_KPI . "' AND get_instances = '" . $get_instances_KPI . "';";
                mysqli_query($link, $updtQuery);
                echo ("\nUPDATE QUERY per KPI-ID: " . $kpiId . ": " . $updtQuery . "\n");
            }
        }
    }

}

//$high_level_type_POI = "MyPOI";
$high_level_type_POI = "";
$nature_POI = "";
$sub_nature_array_POI = [];
$sub_nature_POI = "";
$low_level_type_POI = "";
$unique_name_to_split_POI = "";
$unique_name_id_POI = "";
$instance_uri_POI = "MyPOI";
$get_instances_POI = "";
$unit_POI = "mypoi_map";
$metric_POI = "no";
$saved_direct_POI = "direct";
$kb_based_POI = "yes";
$sm_based_POI = "no";
$parameters_POI = "";
$healthiness_POI = "";
$ownership_POI = "";
$lastCheck_POI = "";
$organizations_POI = "";
$parameters_POI = "";
$widgets_POI = "map";

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);

$test = "yes";
if ($test === "yes") {
    $queryApiPersonalPOI = $host_pd.":8080/datamanager/api/v1/poidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyPOI";
}
$queryPersonalPOIRresults = file_get_contents($queryApiPersonalPOI);
$resPOIArray = json_decode($queryPersonalPOIRresults, true);

foreach ($resPOIArray as $resPOIRecord) {
    if (sizeOf($resPOIRecord) > 1) {
        $count++;

        if ($resPOIRecord['geometry']['coordinates'][0] != "" && $resPOIRecord['geometry']['coordinates'][1] != "") {
            $latitude_POI = $resPOIRecord['geometry']['coordinates'][1];
            $longitude_POI = $resPOIRecord['geometry']['coordinates'][0];
        }

        $high_level_type_POI = $resPOIRecord['properties']['kpidata']['highLevelType'];
        $nature_POI = $resPOIRecord['properties']['kpidata']['nature'];
        $low_level_type_POI = $resPOIRecord['properties']['kpidata']['valueType'];
        $sub_nature_POI = $resPOIRecord['properties']['kpidata']['subNature'];
        $unique_name_id_POI = addslashes($resPOIRecord['properties']['kpidata']['valueName']);
        $last_value_POI = $resPOIRecord['properties']['kpidata']['lastValue'];
        //   $unit_POI = $resPOIRecord['dataType'];

        $last_date_POI_millis = $resPOIRecord['properties']['kpidata']['lastDate'];
        $last_date_POI = date("Y-m-d H:i:s", $last_date_POI_millis / 1000);
     //   $parameters_POI = $resPOIRecord['properties']['kpidata']['id'] . "__" . $resPOIRecord['geometry']['coordinates'][1] . ";" . $resPOIRecord['geometry']['coordinates'][0];
        $parameters_POI = "datamanager/api/v1/poidata/" . $resPOIRecord['properties']['kpidata']['id'];
        $ownership_POI = $resPOIRecord['properties']['kpidata']['ownership'];
        $get_instances_POI = $resPOIRecord['properties']['kpidata']['getInstances'];
        if ($get_instances_POI == "") {
            $get_instances_POI = $parameters_POI;
        }

        //  $metric_POI = $resPOIRecord['metric'];
      //  $saved_direct_POI = $resPOIRecord['savedDirect'];
        $kb_based_POI = $resPOIRecord['properties']['kpidata']['kbBased'];
        if ($resPOIRecord['properties']['kpidata']['smBased'] == "") {
            $sm_based_POI = "myPOI";
        } else {
            $sm_based_POI = $resPOIRecord['properties']['kpidata']['smBased'];
        }
    //    $healthiness_POI = $resPOIRecord['properties']['kpidata']['healthiness'];
        $healthiness_POI = "true";
        $lastCheck_POI = $lastCheck;
        $organizations_POI = $resPOIRecord['properties']['kpidata']['organizations'];
        $organizations_POI = explode("ou=", $organizations_POI)[1];
        $organizations_POI = explode(",dc=ldap,dc=disit,dc=org]", $organizations_POI)[0];

        if (!is_null($nature_POI)) {
            if ($nature_POI != '') {
                echo($count . " - MY POI: " . $nature_POI . ", PERSONAL DATA VARIABLE-NAME: " . $unique_name_id_POI . ", MOTIVATION: " . $low_level_type_POI . "\n");
                $insertQuery_POI = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, widgets, parameters, last_date, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude) VALUES ('$nature_POI','$high_level_type_POI','$sub_nature_POI','$low_level_type_POI', '$unique_name_id_POI', '$instance_uri_POI', '$get_instances_POI', '$unit_POI', '$metric_POI', '$saved_direct_POI', '$kb_based_POI', '$sm_based_POI', '$widgets_POI', '$parameters_POI', '$last_date_POI', '$last_value_POI', '$healthiness_POI', '$lastCheck_POI', '$ownership_POI', '$organizations_POI', '$latitude_POI', '$longitude_POI') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type_POI . "', sub_nature = '" . $sub_nature_POI . "', low_level_type = '" . $low_level_type_POI . "', unique_name_id = '" . $unique_name_id_POI . "', instance_uri = '" . $instance_uri_POI . "', get_instances = '" . $get_instances_POI . "', sm_based = '" . $sm_based_POI . "', widgets = '" . $widgets_POI . "', last_date = '" . $last_date_POI . "', last_value = '" . $last_value_POI . "', parameters = '" . $parameters_POI . "', healthiness = '" . $healthiness_POI . "', lastCheck = '" . $lastCheck_POI . "', ownership = '" . $ownership_POI . "', organizations = '" . $organizations_POI . "', latitude = '" . $latitude_POI . "', longitude = '" . $longitude_POI . "';";
                try {
                    //   mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "';");
                    mysqli_query($link, $insertQuery_POI);

                    //   echo ("\nINSERT QUERY ON DUPLICATE KEY: ".$insertQuery."\n");
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $updtQuery_POI = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type_POI . "', sub_nature = '" . $sub_nature_POI . "', low_level_type = '" . $low_level_type_POI . "', unique_name_id = '" . $unique_name_id_POI . "', instance_uri = '" . $instance_uri_POI . "', get_instances = '" . $get_instances_POI . "', sm_based = '" . $sm_based_POI . "', last_date = '" . $last_date_POI . "', last_value = '" . $last_value_POI . "', parameters = '" . $parameters_POI . "', healthiness = '" . $healthiness_POI . "', lastCheck = '" . $lastCheck_POI . "', ownership = '" . $ownership_POI . "', latitude = '" . $latitude_POI . "', longitude = '" . $longitude_POI . "' WHERE high_level_type = '" . $high_level_type_POI . "' AND sub_nature = '" . $sub_nature_POI . "' AND low_level_type = '" . $low_level_type_POI . "' AND unique_name_id = '" . $unique_name_id_POI . "' AND instance_uri = '" . $instance_uri_POI . "' AND get_instances = '" . $get_instances_POI . "';";
                    mysqli_query($link, $updtQuery);
                    echo("\nUPDATE QUERY: " . $updtQuery . "\n");
                }
            }
        }
    }

}


//$high_level_type_MyData = "MyData";
$high_level_type_MyData = "";
$nature_MyData = "";
$sub_nature_array_MyData = [];
$sub_nature_MyData = "";
$low_level_type_MyData = "";
$unique_name_to_split_MyData = "";
$unique_name_id_MyData = "";
$instance_uri_MyData = "MyData";
$get_instances_MyData = "";
$metric_MyData = "";
$saved_direct_MyData = "";
$kb_based_MyData = "";
$sm_based_MyData = "";
$parameters_MyData = "";
$healthiness_MyData = "";
$ownership_MyData = "";
$lastCheck_MyData = "";
$organizations_MyData = "";
$parameters_MyData = "";

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);

$test = "yes";
if ($test === "yes") {
    $queryApiPersonalMyData = $host_pd.":8080/datamanager/api/v1/kpidata/?sourceRequest=dashboardmanager&accessToken=" . $accessToken . "&highLevelType=MyData";
}
$queryPersonalMyDataRresults = file_get_contents($queryApiPersonalMyData);
$resMyDataArray = json_decode($queryPersonalMyDataRresults, true);

foreach ($resMyDataArray as $resMyDataRecord) {

    $count++;
    //$unit_MyData = "mydata_";
    if ($resMyDataRecord['latitude'] == "" && $resMyDataRecord['longitude'] == "") {
        $parameters_MyData = $resMyDataRecord['id'];
        $querySearchGeolocatedMyData = $host_pd.":8080/datamanager/api/v1/poidata/" . $resMyDataRecord['id'] . "/?sourceRequest=dashboardmanager&highLevelType=MyPOI&accessToken=" . $accessToken . "&last=0";
        $geolocatedMyDataRresults = file_get_contents($querySearchGeolocatedMyData);
        $resGeolocatedMyDataArray = json_decode($geolocatedMyDataRresults, true);
        if (sizeof($resGeolocatedMyDataArray['geometry']['coordinates']) != 0) {
            $latitude_MyData = $resMyDataRecord['latitude'];
            $longitude_MyData = $resMyDataRecord['longitude'];
        } else {
            $latitude_MyData = "";
            $longitude_MyData = "";
        }
    } else {
        $parameters_MyData = "datamanager/api/v1/poidata/" . $resMyDataRecord['id'];
        $latitude_MyData = "";
        $longitude_MyData = "";
    }
    $high_level_type_MyData = $resMyDataRecord['highLevelType'];
    $nature_MyData = $resMyDataRecord['nature'];
    $low_level_type_MyData = $resMyDataRecord['valueType'];
    $sub_nature_MyData = $resMyDataRecord['subNature'];
    $unique_name_id_MyData = addslashes($resMyDataRecord['valueName']);
    $last_value_MyData = $resMyDataRecord['lastValue'];
  //  $unit_MyData = $resMyDataRecord['dataType'] . "-mykpi";
    $get_instances_MyData = $resMyDataRecord['getInstances'];
    if ($get_instances_MyData == "") {
        $get_instances_MyData = $parameters_MyData;
    }
    $unit_MyData = $resKPIRecord['dataType'] . "-mykpi";
    $kpiId = $get_instances_MyData;
    $last_date_MyData_millis = $resMyDataRecord['lastDate'];
    $last_date_MyData = date("Y-m-d H:i:s",$last_date_MyData_millis/1000);

    $ownership_MyData = $resMyDataRecord['ownership'];

    $metric_MyData = $resMyDataRecord['metric'];
    $saved_direct_MyData = $resMyDataRecord['savedDirect'];
    $kb_based_MyData = $resMyDataRecord['kbBased'];
    if ($resMyDataRecord['smBased'] == "") {
        $sm_based_MyData = "myData";
    } else {
        $sm_based_MyData = $resMyDataRecord['smBased'];
    }
  //  $healthiness_MyData = $resMyDataRecord['healthiness'];
    $healthiness_MyData = "true";
    $lastCheck_MyData = $lastCheck;
    $organizations_MyData = $resMyDataRecord['organizations'];
    $organizations_MyData = explode( "ou=", $organizations_MyData)[1];
    $organizations_MyData = explode(",dc=ldap,dc=disit,dc=org]", $organizations_MyData)[0];

    if (!is_null($nature_MyData)) {
        if ($nature_MyData != '') {
            echo($count . " - MY Data: " . $nature_MyData . ", PERSONAL DATA VARIABLE-NAME: " . $unique_name_id_MyData . ", MOTIVATION: " . $low_level_type_MyData . ", KPI-ID: " . $kpiId . "\n");
            $insertQuery_MyData = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership, organizations, latitude, longitude) VALUES ('$nature_MyData','$high_level_type_MyData','$sub_nature_MyData','$low_level_type_MyData', '$unique_name_id_MyData', '$instance_uri_MyData', '$get_instances_MyData', '$unit_MyData', '$metric_MyData', '$saved_direct_MyData', '$kb_based_MyData', '$sm_based_MyData', '$parameters_MyData', '$last_date_MyData', '$last_value_MyData', '$healthiness_MyData', '$lastCheck_MyData', '$ownership_MyData', '$organizations_MyData', '$latitude_MyData', '$longitude_MyData') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type_MyData . "', sub_nature = '" . $sub_nature_MyData . "', low_level_type = '" . $low_level_type_MyData . "', unique_name_id = '" . $unique_name_id_MyData . "', instance_uri = '" . $instance_uri_MyData . "', get_instances = '" . $get_instances_MyData . "', sm_based = '" . $sm_based_MyData . "', last_date = '" . $last_date_MyData . "', last_value = '" . $last_value_MyData . "', parameters = '" . $parameters_MyData . "', healthiness = '" . $healthiness_MyData . "',, lastCheck = '" . $lastCheck_MyData . "', ownership = '" . $ownership_MyData . "', organizations = '" . $organizations_MyData . "', latitude = '" . $latitude_MyData . "', longitude = '" . $longitude_MyData . "';";
            try {
                //   mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "';");
                mysqli_query($link, $insertQuery_MyData);

                //   echo ("\nINSERT QUERY ON DUPLICATE KEY: ".$insertQuery."\n");
            } catch (Exception $e) {
                echo $e->getMessage();
                $updtQuery_MyData = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type_MyData . "', sub_nature = '" . $sub_nature_MyData . "', low_level_type = '" . $low_level_type_MyData . "', unique_name_id = '" . $unique_name_id_MyData . "', instance_uri = '" . $instance_uri_MyData . "', get_instances = '" . $get_instances_MyData . "', sm_based = '" . $sm_based_MyData . "', last_date = '" . $last_date_MyData . "', last_value = '" . $last_value_MyData . "', parameters = '" . $parameters_MyData . "', healthiness = '" . $healthiness_MyData . "', lastCheck = '" . $lastCheck_MyData . "', ownership = '" . $ownership_MyData . "', latitude = '" . $latitude_MyData . "', longitude = '" . $longitude_MyData . "' WHERE high_level_type = '" . $high_level_type_MyData . "' AND sub_nature = '" . $sub_nature_MyData . "' AND low_level_type = '" . $low_level_type_MyData . "' AND unique_name_id = '" . $unique_name_id_MyData . "' AND instance_uri = '" . $instance_uri_MyData . "' AND get_instances = '" . $get_instances_MyData . "';";
                mysqli_query($link, $updtQuery);
                echo ("\nUPDATE QUERY: ".$updtQuery."\n");
            }
        }
    }

}

$high_level_type = "My Personal Data";
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

if (isset($accessToken)) {
    // QUERY ALLE API PER PERSONAL DATA
    $queryPersonalData = $host_pd.":8080/datamanager/api/v1/data?accessToken=".$accessToken."&sourceRequest=dashboardmanager&last=true";
} else {
    // QUERY ALLE API PER PERSONAL DATA
    $queryPersonalData = $host_pd.":8080/datamanager/api/v1/data?accessToken=fakeVal&sourceRequest=dashboardmanager&last=true";
}

$queryPersonalDataRresults = file_get_contents($queryPersonalData);
$resArray = json_decode($queryPersonalDataRresults, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);

$count = 0;

foreach ($resArray as $resRecord) {

    $count++;
    $nature = $resRecord['APPName'];
    $low_level_type = $resRecord['motivation'];
    $unique_name_id = addslashes($resRecord['variableName']);
    $last_value = $resRecord['variableValue'];
    $unit = $resRecord['variableUnit'];
    $get_instances = $resRecord['APPID'];
    $last_date_millis = $resRecord['dataTime'];
    $last_date = date("Y-m-d H:i:s",$last_date_millis/1000);

    $ownership = "private";

    $metric = "yes";
    $saved_direct = "saved";
    $kb_based = "no";
    $sm_based = "no";
    $healthiness = "true";

    if($low_level_type === 'ping-adv') {
        $stop_flag = 1;
    }

    if (!is_null($nature)) {
        if ($nature != '') {
            echo($count . " - PERSONAL APP-NAME: " . $nature . ", PERSONAL DATA VARIABLE-NAME: " . $unique_name_id . ", MOTIVATION: " . $low_level_type . "\n");
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', saved_direct = '" . $saved_direct_POI . "';";
            try {
             //   mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, last_date, last_value, healthiness, lastCheck, ownership) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$last_date', '$last_value', '$healthiness', '$lastCheck', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "';");
                mysqli_query($link, $insertQuery);

             //   echo ("\nINSERT QUERY ON DUPLICATE KEY: ".$insertQuery."\n");
            } catch (Exception $e) {
                echo $e->getMessage();
                $updtQuery = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = '" . $last_date . "', last_value = '" . $last_value . "', parameters = '" . $parameters . "', healthiness = healthiness, lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                mysqli_query($link, $updtQuery);
                echo ("\nUPDATE QUERY: ".$updtQuery."\n");
            }
        }
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
echo("End Personal_Data_FeedDashboardWizard SCRIPT at: ".$end_time_ok);