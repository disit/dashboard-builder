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

$id = $_GET['IdMisura'];

$conn = new mysqli($host, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

$rangedays= array();
$hourMin='';
if (isset($_GET['time'])) {   
    list($v,$unit) = explode("/", $_GET['time']);
    if(($v==1 && $unit=="DAY") || $unit=="HOUR") {
      $hourMin=",hour(computationDate),minute(computationDate) ";
    }
    if($unit=='DAY')
      $having1 = "HAVING date(computationDate)>=date(now())-interval " . ($v-1) . " $unit";
    else
      $having1 = "HAVING computationDate>=now()-interval " . $v . " $unit";
    array_push($rangedays,  $having1);
    if (isset($_GET['compare']) && $_GET['compare']==1) {
        if($unit=='DAY')
          $having2 = "HAVING date(computationDate)>=date(now())-interval " . ((2*$v)-1) . " $unit AND date(computationDate)<date(now())-interval " . ($v-1) . " $unit" ;
        else
          $having2 = "HAVING computationDate>=now()-interval " . $v . " HOUR - INTERVAL 1 DAY AND computationDate<now()-interval 1 DAY" ;
        array_push($rangedays,  $having2);
    }       
} else{
    $having1 = '';
    array_push($rangedays,  $having1);
}    

$rows = array();
$i= -1;
foreach ($id as $id_value) {
    foreach ($rangedays as $rangedays_value) {
        $i++;
        $sql = "SELECT IdMetric_data, computationDate, value_perc1, MAX(value_num)as value, description_short as descrip, Descriptions.threshold, Descriptions.thresholdEval from Dashboard.Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='" . $id_value . "' GROUP BY date(computationDate)$hourMin  $rangedays_value";
        $result = $conn->query($sql);
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = array('commit' => array('author' => $r, 'range_dates' => $i));
        }
        $data = array('data' => $rows);
    }
}
$data_json = json_encode($data);

$conn->close();
 
echo($data_json);
?>