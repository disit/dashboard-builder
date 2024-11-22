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

$link = new PDO("pgsql:host=" . $od_ip . ";dbname=" . $od_pgsql_dbname, $od_pgsql_user, $od_pgsql_passw);
$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$precision = $_REQUEST['precision'] ?? '';
$organization = $_REQUEST['organization'] ?? '';
$od_id = $_REQUEST['od_id'] ?? '';
$action = $_REQUEST['action'] ?? '';

if ($action === "dates") {
    $query_date = "";
    if (in_array($precision, ['communes', 'poi', 'region', 'province', 'municipality', 'ace', 'section'], true)) {
        $query_date = "SELECT DISTINCT from_date FROM od_data FULL OUTER JOIN od_metadata ON od_data.od_id = od_metadata.od_id WHERE organization = :organization AND od_data.od_id = :od_id AND from_date IS NOT NULL ORDER BY from_date";
    } else {
        $query_date = "SELECT DISTINCT from_date FROM od_data_mgrs FULL OUTER JOIN od_metadata ON od_data_mgrs.od_id = od_metadata.od_id WHERE od_data_mgrs.od_id = :od_id AND organization = :organization AND from_date IS NOT NULL ORDER BY from_date";
    }

    if ($query_date !== "") {
        $stmt = $link->prepare($query_date);
        $stmt->bindParam(':organization', $organization, PDO::PARAM_STR);
        $stmt->bindParam(':od_id', $od_id, PDO::PARAM_STR);
        $stmt->execute();
        $process_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($process_list);
    } else {
        echo('ERROR');
    }
} elseif ($action === "shape_type") {
    $lastDelimiterPos = strrpos($od_id, "_");
    $od_id_base = substr($od_id, 0, $lastDelimiterPos + 1);

    $query_types = "SELECT od_id FROM od_metadata WHERE organization = :organization AND od_id LIKE :od_id_base";

    $stmt = $link->prepare($query_types);
    $stmt->bindParam(':organization', $organization, PDO::PARAM_STR);
    $od_id_base = $od_id_base . '%';
    $stmt->bindParam(':od_id_base', $od_id_base, PDO::PARAM_STR);
    $stmt->execute();
    $process_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($process_list);
} elseif ($action === "od_list") {
    $query_n = "SELECT table_id, od_metadata.od_id, value_type, value_unit, description, organization, kind, mode, transport, purpose, precision FROM od_metadata
                FULL OUTER JOIN (
                    SELECT 'od_data' as table_id, od_id, precision
                    FROM od_data 
                    GROUP BY od_id, precision
                    UNION ALL
                    SELECT 'od_data_mgrs' as table_id, od_id, precision
                    FROM od_data_mgrs
                    GROUP BY od_id, precision
                ) AS table_union 
                ON od_metadata.od_id = table_union.od_id";

    $query_n_count = "SELECT COUNT(od_id) FROM od_metadata";

    $stmt = $link->query($query_n);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $num_rows = $link->query($query_n_count)->fetchColumn();
    $process_list = [];

    if ($num_rows > 0) {
        foreach ($result as $row) {
            $shape = ($row['table_id'] === "od_data") ? "communes" : "square";
            $listFile = [
                "od_id" => $row['od_id'],
                "value_type" => $row['value_type'],
                "value_unit" => $row['value_unit'],
                "description" => $row['description'],
                "organization" => $row['organization'],
                "shape" => $shape,
                "precision" => $row['precision'],
                "kind" => $row['kind'],
                "mode" => $row['mode'],
                "transport" => $row['transport'],
                "purpose" => $row['purpose'],
                "metric_name" => "ODcolormap1"
            ];
            $process_list[] = $listFile;
        }
    }
    echo json_encode($process_list);
} else {
    echo('ERROR');
}
?>
