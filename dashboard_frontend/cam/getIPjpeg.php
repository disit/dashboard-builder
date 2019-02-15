<?php

include '../config.php';

$ip_from_source = $_REQUEST['ip'];

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);
$conn = new mysqli($host, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*
$get_IP = "SELECT IP_address FROM IP_selectorWeb WHERE ID=1;";  // GP MOD
$result = $conn->query($get_IP);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $ip = $row["IP_address"];
    }
}
*/

// $get_credentials = "SELECT username,password FROM IP_cam_credentials WHERE IP = '$ip';";             // GP MOD
$get_credentials = "SELECT username,password FROM IP_cam_credentials WHERE IP = '$ip_from_source';";
$result = $conn->query($get_credentials);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $username = $row["username"];
        $password = $row["password"];
    }
}
$conn->close();

// $url = "http://" . $ip;
$url = "http://" . $ip_from_source;     // GP MOD

$ch = curl_init();
header("Content-type: image/jpeg");

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

$store = curl_exec($ch);

curl_close($ch);

echo $store;