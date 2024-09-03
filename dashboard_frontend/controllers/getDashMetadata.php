<?php
/* Dashboard Builder.
Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
// error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);

$response = [];

$q = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$dashboardId'";
$r = mysqli_query($link, $q);
$ssbl = false;
$csbl = false;
if($r && mysqli_num_rows($r) > 0) {
    while($res = mysqli_fetch_array($r)) {
        if(!is_null($res['appId'])) {
            $ssbl = true;
        }
        if(!is_null($res['code'])) {
            $csbl = true;
        }
    }
    $response['ssbl'] = $ssbl ? "ssbl" : "No";
    $response['csbl'] = $csbl ? "csbl" : "No";

    $q2 = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
    $r2 = mysqli_query($link, $q2);
    if($r2 && mysqli_num_rows($r2) > 0) {
        while($res2 = mysqli_fetch_array($r2)) {
            $response['metadata'] = json_decode($res2['metaData'], true);
            if ($res2['organizations'] === "DISIT" || $res2['organizations'] === "Firenze2") {
                $response['area'] = "Firenze";
            } else {
                $response['area'] = $res2['organizations'];
            }
        }
    }
    $response['status'] = 'Ok';
} else {
    $response['status'] = 'Ko';
}

/*if($r) {
    if($r->num_rows > 0) {
        while($row = mysqli_fetch_array($r)) {
            $oldContainerName = $row['name_dashboard'];
        }
    }
}*/

echo json_encode($response);