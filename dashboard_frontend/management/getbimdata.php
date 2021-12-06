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
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
checkSession('RootAdmin');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$action = mysqli_real_escape_string($link, $_REQUEST['action']);
$action = filter_var($action, FILTER_SANITIZE_STRING);


if ($action == 'get_bim') {

    $process_list = array();
    $query = "SELECT * FROM BIMProjects";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $listFile = array(
                "id" => $row['id'],
                "poid" => $row['poid'],
                "project_name" => $row['project_name'],
                "nature" => $row['nature'],
                "sub_nature" => $row['sub_nature'],
                "organizations" => $row['organizations']
            );
            array_push($process_list, $listFile);
        }
    }
    echo json_encode($process_list);
} else if ($action == 'new_bim') {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $nature = mysqli_real_escape_string($link, $_POST['nature']);
    $nature = filter_var($nature, FILTER_SANITIZE_STRING);

    $subnature = mysqli_real_escape_string($link, $_POST['subnature']);
    $subnature = filter_var($subnature, FILTER_SANITIZE_STRING);

    $iod = mysqli_real_escape_string($link, $_POST['iod']);
    $iod = filter_var($iod, FILTER_SANITIZE_STRING);

    $org = mysqli_real_escape_string($link, $_POST['org']);
    $org = filter_var($org, FILTER_SANITIZE_STRING);
    //
    $query = "INSERT INTO BIMProjects (project_name, poid, nature, sub_nature, organizations)
                    VALUES ('" . $name . "', '" . $iod . "', '" . $nature . "','" . $subnature . "', '" . $org . "')";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        $message['message'] = 'ok';
    } else {
        $message['message'] = 'ko';
    }
    echo json_encode($message);
    //
} else if ($action == 'delete_bim') {
    $id = mysqli_real_escape_string($link, $_POST['id']);
    $id = filter_var($id, FILTER_SANITIZE_STRING);

    $query = "DELETE FROM BIMProjects WHERE id=" . $id;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    echo($result);
} else if ($action == 'edit_bim') {
    $id = mysqli_real_escape_string($link, $_POST['id']);
    $id = filter_var($id, FILTER_SANITIZE_STRING);

    $name = mysqli_real_escape_string($link, $_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $nature = mysqli_real_escape_string($link, $_POST['nature']);
    $nature = filter_var($nature, FILTER_SANITIZE_STRING);

    $subnature = mysqli_real_escape_string($link, $_POST['subnature']);
    $subnature = filter_var($subnature, FILTER_SANITIZE_STRING);

    $iod = mysqli_real_escape_string($link, $_POST['iod']);
    $iod = filter_var($iod, FILTER_SANITIZE_STRING);

    $org = mysqli_real_escape_string($link, $_POST['org']);
    $org = filter_var($org, FILTER_SANITIZE_STRING);
    //

    $query = "UPDATE BIMProjects SET project_name='" . $name . "', nature='" . $nature . "', sub_nature='" . $subnature . "', poid='" . $iod . "', organizations='" . $org . "' WHERE id='" . $id . "'";

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    if ($result) {
        $message['message'] = 'ok';
    } else {
        $message['message'] = 'ko';
    }
    echo json_encode($message);
    //
} else if ($action == 'get_parameters') {

    //
    $api_nat = $api_dictionary . '?type=nature';
    $content_nat = file_get_contents($api_nat);
    $content_nat = json_decode($content_nat);
    $arr_nat = ($content_nat->content);

    $api_subn = $api_dictionary . '?type=subnature';
    $content_subn = file_get_contents($api_subn);
    $content_subn = json_decode($content_subn);
    $arr_subn = ($content_subn->content);
    //
    $process_list = array();

    $nat_list = array();

    $process_list['nature'] = $arr_nat;

    $subnat_list = array();
    //$query_subnat = "";

    $process_list['subnature'] = $arr_subn;

    $org_list = array();
    $query_org = "SELECT organizationName FROM Organizations;";
    $result_org = mysqli_query($link, $query_org) or die(mysqli_error($link));
    if ($result_org) {
        while ($row_org = mysqli_fetch_assoc($result_org)) {
            array_push($org_list, $row_org['organizationName']);
        }
    }

    $process_list['organizations'] = $org_list;
    echo json_encode($process_list);
} else {
    
}
?>