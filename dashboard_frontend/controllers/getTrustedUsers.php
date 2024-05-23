<?php
/* Dashboard Builder.
Copyright (C) 2022 DISIT Lab https://www.disit.org - University of Florence

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
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if (isset($_REQUEST['user'])) {

    $user = mysqli_real_escape_string($link, $_REQUEST['user']);
    $q = "SELECT * FROM Dashboard.TrustedUsers WHERE userName = '$user';";
    $r = mysqli_query($link, $q);

    if ($r) {
        if ($row = mysqli_fetch_assoc($r)) {
            $response['detail'] = 'Ok';
        }
    } else {
        $response['detail'] = 'Ko';
    }

} else {

    $q = "SELECT * FROM Dashboard.TrustedUsers;";
    $r = mysqli_query($link, $q);

    if ($r) {
        $response['trustedUsers'] = [];
        while ($row = mysqli_fetch_assoc($r)) {
            array_push($response['trustedUsers'], $row['userName']);
        }
        $response['detail'] = 'Ok';
    } else {
        $response['detail'] = 'Ko';
    }

}

echo json_encode($response);