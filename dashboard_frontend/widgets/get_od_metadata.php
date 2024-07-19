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
$od_id = $_REQUEST['od_id'];
$action = $_REQUEST['action'];
if($action === "dates"){
    $query_date = "";
    if ($precision == 'communes' or 
        $precision == 'poi' or 
        $precision == 'region' or
        $precision == 'province' or 
        $precision == 'municipality' or 
        $precision == 'ace' or 
        $precision == 'section'){
            
        $query_date = "SELECT DISTINCT from_date FROM od_data FULL OUTER JOIN od_metadata ON od_data.od_id=od_metadata.od_id WHERE organization = '".$organization."' AND od_data.od_id = '". $od_id ."' AND from_date IS NOT NULL ORDER BY from_date";
    }
    else{
        // $od_id = "od_".$organization."_".$precision;
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
    $lastDelimiterPos = strrpos($od_id, "_");
    $od_id_base = substr($od_id, 0, $lastDelimiterPos + 1);
    $query_types = "SELECT od_id FROM od_metadata WHERE organization='".$organization."' AND od_id LIKE '".$od_id_base."%' ";
    $result = $link->query($query_types) or die($link->errorInfo());
    $process_list = array();
    foreach ($result as $row) {
        $process_list[] = $row['od_id'];
    }
    echo json_encode($process_list);
}elseif ($action === "od_list"){
    $query_n =	"SELECT table_id, od_metadata.od_id,value_type, value_unit, description, organization, kind, mode, transport, purpose, precision " .
    "FROM od_metadata
	FULL OUTER JOIN (SELECT 'od_data' as table_id, od_id, precision
              FROM od_data 
	          GROUP BY od_id, precision
	          UNION ALL
          SELECT 'od_data_mgrs' as table_id, od_id, precision
              FROM od_data_mgrs
	          GROUP BY od_id, precision) AS table_union 
			  ON od_metadata.od_id = table_union.od_id";
    $query_n_count = "SELECT COUNT(od_id) FROM od_metadata";
    $result = $link->query($query_n) or die($link->errorInfo());
    $process_list = array();
    $num_rows     = $link->query($query_n_count)->fetchColumn();
    $num_r = 0;
    if ($num_rows > 0) {
        foreach ($result as $row) {
            $od_id = $row['od_id'];
            $value_type = $row['value_type'];
            $value_unit = $row['value_unit'];
            $description = $row['description'];
            $organization = $row['organization'];
            $shape = "";
            $precision = $row['precision'];
            $kind = $row['kind'];
            $mode = $row['mode'];
            $transport = $row['transport'];
            $purpose = $row['purpose'];
            $metric_name = "ODcolormap1";
            if ($row['table_id'] == "od_data") {
                $shape = "communes";
            } else if ($row['table_id'] == "od_data_mgrs") {
                $shape = "square";
            }

            $listFile = array("od_id" => $od_id,
                "value_type" => $value_type,
                "value_unit" => $value_unit,
                "description" => $description,
                "organization" => $organization,
                "shape" => $shape,
                "precision" => $precision,
                "kind" => $kind,
                "mode" => $mode,
                "transport" => $transport,
                "purpose" => $purpose,
                "metric_name" => $metric_name
            );
            array_push($process_list, $listFile);
        }
    }
    echo json_encode($process_list);
}else{
    echo('ERROR');
}
?>
