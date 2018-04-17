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
        
        $metricName = mysqli_real_escape_string($link, $_GET['IdMisura'][0]); 
        
        $q1 = "SELECT * FROM Dashboard.Descriptions WHERE IdMetric = '$metricName'";
        $r1 = mysqli_query($link, $q1);
        
        if($r1)
        {
            if(mysqli_num_rows($r1) > 0)
            {
                $sql = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
            }
            else
            {
                $q2 = "SELECT * FROM Dashboard.NodeRedMetrics WHERE name = '$metricName'";
                $r2 = mysqli_query($link, $q2);
                
                if($r2)
                {
                    if(mysqli_num_rows($r2) > 0)
                    {
                        $sql = "SELECT Data.*, NodeRedMetrics.shortDesc as descrip, NodeRedMetrics.metricType, '', '', '', 1 from Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
                    }
                }
            }
        }
        
        $rows = array();
        $result = $link->query($sql);

        while($r = mysqli_fetch_assoc($result)) 
        {
            $rows[] =  array('commit' => array ('author' => $r));
        }
        $data = array('data' =>  $rows);
          

        $data_json = json_encode($data);   
        $link->close();
        echo($data_json);
    }

    
