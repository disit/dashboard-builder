<?php

include '../config.php';
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = "";

if (isset($_REQUEST['nature'])) {
    $nature = escapeForJS($_REQUEST['nature']);
    if (isset($_REQUEST['subNature'])) {
        $subNature = escapeForJS($_REQUEST['subNature']);
        if($_REQUEST['nature'] == "TransferServiceAndRenting" && $_REQUEST['subNature'] == "SensorSite") {
            $query = "SELECT * FROM Dashboard.DefaultNatureColors WHERE nature = '" . escapeForSQL($nature, $link) . "' AND sub_nature = '" . escapeForSQL($subNature, $link) . "';";
        } else {
            $query = "SELECT * FROM Dashboard.DefaultNatureColors WHERE nature = '" . escapeForSQL($nature, $link) . "';";
        }
    } else {
        $query = "SELECT * FROM Dashboard.DefaultNatureColors WHERE nature = '" . escapeForSQL($nature, $link) . "';";
    }
}

$result = mysqli_query($link, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            if ($row['defaultColor'] != null && $row['defaultColor'] !== "") {
                $newMapPinColor = $row['defaultColor'];
                $response['details'] = "No Default Pin Color Found!";
                $response['defColour'] = $newMapPinColor;
            }
        }
    } else {
        $response['details'] = "No Default Pin Color Found!";
    }
} else {
    $response['details'] = "Query Default Pin Color KO";
}
echo json_encode($response);