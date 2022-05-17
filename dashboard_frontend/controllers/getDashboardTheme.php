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
error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
//checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];
$dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
if (checkVarType($dashboardId, "integer") === false) {
    eventLog("Returned the following ERROR in getDashboardTemplate.php for id = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
    $response['details'] = 'dashboardId is not an integer as expected';
    $response['result'] = 'Ko';
    echo json_encode($response);
    exit();
}

$q = "SELECT theme FROM Dashboard.Config_dashboard WHERE id = '$dashboardId'";
$r = mysqli_query($link, $q);

if($r)
{
    $response['details'] = mysqli_fetch_assoc($r);
    $response['result'] = 'Ok';
}
else
{
    $response['result'] = 'Ko';
}

echo json_encode($response);
