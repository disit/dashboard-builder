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
    include '../config.php';//Escape
require_once __DIR__.'/widgetDatabase.php';
$widgetDatabaseGuard = dashboardWidgetDatabaseBegin();
try {


    $needWebSocket = false;
    
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
        
        $sql = null; // Unknown/modern metrics have no legacy Data query.
        $q1 = "SELECT * FROM Dashboard.Descriptions WHERE IdMetric = '$metricName'";
        $r1 = mysqli_query($link, $q1);
        
        if($r1)
        {
            if(mysqli_num_rows($r1) > 0)
            {
                $sql = "SELECT Data.*, Descriptions.description_short as descrip, Descriptions.metricType, Descriptions.field1Desc, Descriptions.field2Desc, Descriptions.field3Desc, Descriptions.hasNegativeValues from Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data = '$metricName' ORDER BY computationDate desc LIMIT 1"; 
                $needWebSocket = false;
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
                        $needWebSocket = true;
                    }
                }
            }
        }
        
        $rows = array();
        if ($sql !== null) {
            $result = $link->query($sql);
            if ($result !== false) {
                while ($r = mysqli_fetch_assoc($result)) {
                    $rows[] = array('commit' => array('author' => $r), 'needWebSocket' => $needWebSocket);
                }
            }
        }
        
        $data = array('data' =>  $rows);
        $data_json = json_encode($data);   
        $link->close();
        echo($data_json);
    }

    

} catch (\mysqli_sql_exception $widgetDatabaseError) {
    dashboardWidgetDatabaseFailure($widgetDatabaseError,isset($link)?$link:(isset($esbLink)?$esbLink:null),$widgetDatabaseGuard,'json');
} catch (\DashboardWidgetIconDirectoryException $widgetDirectoryError) {
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,503,'icon_directory_unavailable','Widget icon directory is unavailable.');
} catch (\PDOException $widgetPdoError) {
    error_log('Widget PostgreSQL operation failed at '.basename($widgetPdoError->getFile()).':'.$widgetPdoError->getLine());
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,500,'database_error','PostgreSQL database operation failed.');
} finally {
    dashboardWidgetDatabaseEnd($widgetDatabaseGuard);
}
