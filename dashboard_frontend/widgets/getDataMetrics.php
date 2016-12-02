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

$rows = array();
foreach($id as $id_value)
{   
    $sql = "SELECT Data.IdMetric_data, Data.computationDate, Data.value_num, Data.value_perc1, Data.value_perc2, Data.value_perc3, Data.value_text, Data.quant_perc1, Data.quant_perc2, Data.quant_perc3, Data.tot_perc1, Data.tot_perc2, Data.tot_perc3, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.threshold, Descriptions.thresholdEval from Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='".$id_value."' ORDER BY computationDate desc LIMIT 1"; 
    $result = $conn->query($sql);

    while($r = mysqli_fetch_assoc($result)) {
        $rows[] =  array('commit' => array ('author' => $r));
    }
    $data = array('data' =>  $rows);

}    
$data_json = json_encode($data);   

$conn->close();
echo($data_json);
?>
