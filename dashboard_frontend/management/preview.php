<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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

session_start(); // Starting Session
$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);

$user_id = $_SESSION['login_user_id'];

$name_dashboard_selected = $_GET['nameDashboard'];

$selqDbtb2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard_selected' and user='$user_id'";
$result5 = mysqli_query($link, $selqDbtb2) or die(mysqli_error($link));

if ($result5) {
    if ($result5->num_rows > 0) {     
        while ($row2 = mysqli_fetch_array($result5)) {
            $_SESSION['id_dashboard'] = $row2['Id'];
        }
    }

    mysqli_close($link);
    header("location: dashboard_preview.php");
} else {
    mysqli_close($link);
}
?>