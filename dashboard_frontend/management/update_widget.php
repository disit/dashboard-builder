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

$dashboard_id = $_SESSION['id_dashboard'];

$name_widget_selected = $_GET['nameWidget'];

if (isset($_GET['operation']) && !empty($_GET['operation'])) {
    if ($_GET['operation'] == "update") {
        
    } else {
        $selqDbtb2 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w='$name_widget_selected' AND id_dashboard='$dashboard_id'";
        $result5 = mysqli_query($link, $selqDbtb2) or die(mysqli_error($link));

        if ($result5) {

            mysqli_close($link);
            header("location: dashboard_configdash.php");
        } else {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error");';
            echo 'window.location.href = "dashboard_configdash.php";';
            echo '</script>';
        }
    }
}
?>