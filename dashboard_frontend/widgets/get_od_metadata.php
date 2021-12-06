<?php
/* Resource Manager - Process Loader
Copyright (C) 2018 DISIT Lab http://www.disit.org - University of Florence

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

$link = new PDO("pgsql:host=" . $od_ip . ";dbname=" . $od_pgsql_dbname, $od_pgsql_user, $od_pgsql_passw) or die("failed to connect to server !!");
$precision = $_REQUEST['precision'];
$organization = $_REQUEST['organization'];
$action = $_REQUEST['action'];
if($action === "dates"){
    $query_date = "";
    if ($precision == 'communes'){
        $query_date = "SELECT DISTINCT from_date FROM od_data FULL OUTER JOIN od_metadata ON od_data.od_id=od_metadata.od_id WHERE organization = '".$organization."' AND from_date IS NOT NULL ORDER BY from_date";
    }
    else{
        $od_id = "od_".$organization."_".$precision;
        $query_date = "SELECT DISTINCT from_date FROM od_data_mgrs FULL OUTER JOIN od_metadata ON od_data_mgrs.od_id=od_metadata.od_id WHERE od_data_mgrs.od_id='".$od_id."' AND organization='".$organization."' AND from_date IS NOT NULL ORDER BY from_date";
    }
    if($query_date != ""){
        $result = $link->query($query_date) or die($link->errorInfo());
        $process_list = array();
        foreach ($result as $row) {
            $process_list[] = $row['from_date'];
        }
        echo json_encode($process_list);
    }else{
        echo('ERROR');
    }
}elseif ($action === "shape_type"){
    $query_types = "SELECT od_id FROM od_metadata WHERE organization='".$organization."'";
    $result = $link->query($query_types) or die($link->errorInfo());
    $process_list = array();
    foreach ($result as $row) {
        $process_list[] = $row['od_id'];
    }
    echo json_encode($process_list);
}else{
    echo('ERROR');
}
?>
