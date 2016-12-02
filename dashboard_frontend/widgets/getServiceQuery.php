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
    $valore = $_GET['nomeMetrica'];

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $rows = array();
foreach($valore as $id_value)
{ 
    $sql = "SELECT query FROM Descriptions WHERE IdMetric = '" . $id_value . "'";
    $result = $conn->query($sql);

    while ($r = mysqli_fetch_assoc($result)) {
        $parameters = array('param' => $r);
    }
}    
    $par_json = json_encode($parameters);
    $conn->close();
    echo($par_json);

?>

