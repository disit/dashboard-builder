<?php
/**
 * Created by PhpStorm.
 * User: pantaleo
 * Date: 18/06/2018
 * Time: 11:14
 */

include '../config.php';

error_reporting(E_ERROR);

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$servername = "192.168.0.50";
$username = "root";
$password = "root";
$dbname = "twitter_content_extraction";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DISTINCT name FROM `canale` WHERE shareToUser = 6 AND name NOT REGEXP 'maturit' ORDER BY name ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $high_level_type = "External Service";
        $nature = "Social";
        $sub_nature = "Twitter Vigilance";
        $low_level_type = "";
        $unique_name_id = "ExternalContent";
        $instance_uri = $row["name"];
        $get_instances = "get channel/search";
        $unit = "webpagetv";
        $metric = "no";
        $saved_direct = "direct";
        $kb_based = "no";
        $sm_based = "no";
        $parameters = "https://www.disit.org/tv/index.php?p=chart_singlechannel&canale=".$row["name"];
        $microAppExtServIcon = "twitter-vigilance.png";
        $endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
        $end_scritp_time = $endTime->format('c');
        $end_scritp_time_string = explode("+", $end_scritp_time);
        $end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
        $last_value = $row["name"];
        $lastCheck = $end_time_ok;
        $healthiness = "true";
        $ownership = "public";

        $insertQueryTwitter = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, microAppExtServIcon, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$microAppExtServIcon', '$healthiness', '$lastCheck', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = sub_nature, low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', microAppExtServIcon = '" . $microAppExtServIcon . "', healthiness = healthiness, ownership = '" . $ownership . "';";
        mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, microAppExtServIcon, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$microAppExtServIcon','$healthiness', '$lastCheck', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = sub_nature, low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', microAppExtServIcon = '" . $microAppExtServIcon . "', healthiness = healthiness, ownership = '" . $ownership . "';");
    }
} else {
    echo "0 results";
}
$conn->close();


$servernameRT = "192.168.0.64";
$usernameRT = "root";
$passwordRT = "root";
$dbnameRT = "twitter_content_extraction";

// Create connection
$connRT = new mysqli($servernameRT, $usernameRT, $passwordRT, $dbnameRT);
// Check connection
if ($connRT->connect_error) {
    die("Connection failed: " . $connRT->connect_error);
}

$sqlRT = "SELECT DISTINCT name FROM `twitter_content_extraction`.`canale` WHERE shareToUser = 6 ORDER BY name ASC;";
$resultRT = $connRT->query($sqlRT);

if ($resultRT->num_rows > 0) {
    // output data of each row
    while($rowRT = $resultRT->fetch_assoc()) {

        $high_level_type = "External Service";
        $nature = "Social";
        $sub_nature = "Twitter Vigilance Real Time";
        $low_level_type = "";
        $unique_name_id = "ExternalContent";
        $instance_uri = $rowRT["name"];
        $get_instances = "get channel/search";
        $unit = "webpagerttv";
        $metric = "no";
        $saved_direct = "direct";
        $kb_based = "no";
        $sm_based = "no";
        $parameters = "https://www.disit.org/rttv/index.php?p=chart_singlechannel&canale=".$rowRT["name"];
        $microAppExtServIcon = "twitter-vigilance-RT.png";
        $endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
        $end_scritp_time = $endTime->format('c');
        $end_scritp_time_string = explode("+", $end_scritp_time);
        $end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
        $last_value = $rowRT["name"];
        $lastCheck = $end_time_ok;
        $healthiness = "true";
        $ownership = "public";

        $insertQueryTwitterRT = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, microAppExtServIcon, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$microAppExtServIcon', '$healthiness', '$lastCheck', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', microAppExtServIcon = '" . $microAppExtServIcon . "', healthiness = healthiness, ownership = '" . $ownership . "';";
        mysqli_query($link, "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, microAppExtServIcon, healthiness, lastCheck, last_value, ownership) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based',  '$sm_based', '$parameters', '$microAppExtServIcon','$healthiness', '$lastCheck', '$last_value', '$ownership') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = '" . $last_value . "', parameters = '" . $parameters . "', microAppExtServIcon = '" . $microAppExtServIcon . "', healthiness = healthiness, ownership = '" . $ownership . "';");

    }
} else {
    echo "0 results";
}
$connRT->close();