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

//$json_conf_widgets = $_POST['configuration_widgets'];
if (isset($_POST['configuration_widgets'])) { 
    $json_conf_widgets = $_POST['configuration_widgets'];
$array_widgets = json_decode($json_conf_widgets, true);

foreach ($array_widgets as $item) {
    $name_widget = $item['id'];
    $col_widget = $item['col'];
    $row_widget = $item['row'];
    $size_x_widget = $item['size_y'];
    $size_y_widget = $item['size_x'];

    $updqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET n_row = '$row_widget', n_column = '$col_widget', size_rows = '$size_x_widget', size_columns = '$size_y_widget' WHERE name_w='$name_widget'";
    $result = mysqli_query($link, $updqDbtb);
    
    if (!$result) {      
      mysqli_close($link);
      echo 0;
      exit();
    }
  }
}
echo 1;
mysqli_close($link);
?>