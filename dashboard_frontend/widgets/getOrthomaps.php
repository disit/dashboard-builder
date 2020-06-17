<?php
/* Dashboard Builder.
Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
include '../config.php';//Escape

$orthomapsArray = [];
$data = [];
$org = "";

$link = new mysqli($host, $username, $password, $dbname);

if($link->connect_error)
{
    die("Connection failed: " . $link->connect_error);
}
else
{
    if(!$link->set_charset("utf8"))
    {
        exit();
    }

    $metricName = mysqli_real_escape_string($link, $_GET['widgetName']);

    $q1 = "SELECT * FROM Dashboard.Config_widget_dashboard widgetDash INNER JOIN Dashboard.Config_dashboard Dash ON Dash.id = widgetDash.id_dashboard WHERE widgetDash.name_w = '$metricName'";
    $r1 = mysqli_query($link, $q1);

    if($r1)
    {
        if(mysqli_num_rows($r1) > 0)
        {
            while($row1 = mysqli_fetch_assoc($r1)) {
                $org = mysqli_real_escape_string($link, $row1['organizations']);
                $sql = "SELECT orthomapJson FROM Dashboard.Organizations WHERE organizationName = '$org';";
            }
        }
        else
        {

        }
    }

    $rows = array();
    $result = $link->query($sql);

    while($r = mysqli_fetch_assoc($result))
    {
        $orthomapsArray =  json_decode($r['orthomapJson']);
    }

    for ($n = 0; $n < sizeof($orthomapsArray->dropdownMenu); $n++) {
        $entry = [];
        if ($orthomapsArray->dropdownMenu[$n]->id) {
            $entry[$orthomapsArray->dropdownMenu[$n]->id] = $orthomapsArray->dropdownMenu[$n]->label;
            array_push($data, $entry);
        }
    }
   // $data = array('data' =>  $rows);
    $data_json = json_encode($data);
    $link->close();
    echo($data_json);
}



