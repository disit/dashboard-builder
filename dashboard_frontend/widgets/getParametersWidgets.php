<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
    
    $widgetName = $_GET['nomeWidget'];
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
        
        $rows = array();
        foreach($widgetName as $widgetNameIteration)
        { 
            $widgetNameIteration = mysqli_real_escape_string($link, $widgetNameIteration);
            $sql = "SELECT widgets.*, descriptions.metricType, nodeRedInputs.id AS nrInputId, nodeRedInputs.name, nodeRedInputs.valueType, nodeRedInputs.user, nodeRedInputs.startValue, nodeRedInputs.domainType, nodeRedInputs.minValue, nodeRedInputs.maxValue, nodeRedInputs.offValue, nodeRedInputs.onValue, nodeRedInputs.dataPrecision FROM Config_widget_dashboard AS widgets " .
                   "LEFT JOIN Descriptions AS descriptions " .
                   "ON widgets.id_metric = descriptions.IdMetric " .
                   "LEFT JOIN NodeRedInputs AS nodeRedInputs " .
                   "ON widgets.id_metric = nodeRedInputs.name " .
                   "WHERE widgets.name_w = '$widgetNameIteration'";
            
            //$file = fopen("C:\dashboardLog.txt", "w");
            //fwrite($file, "sql: " . $sql . "\n");
            
            $result = $link->query($sql);

            while($r = mysqli_fetch_assoc($result)) 
            {
               $parameters = array('param' => $r);
               
               if($r['entityJson'] != null)
               {
                   $sourceEntity = json_decode($r['entityJson']);
                   $entityId = $sourceEntity->id;
                   
                   //Se il widget è un attuatore su broker, recuperiamo anche il suo valore più recente impostato
                   $lastValueQuery = "SELECT value FROM ActuatorsEntitiesValues WHERE entityId = '$entityId' AND actuationResult = 'Ok' ORDER BY STR_TO_DATE(actionTime, '%Y-%m-%d %T') DESC LIMIT 1";
                   $lastValueResult = $link->query($lastValueQuery);
                   
                   if($lastValueResult)
                   {
                       $row = mysqli_fetch_assoc($lastValueResult);
                       $parameters['param']['currentValue'] = $row['value'];
                   }
                   else
                   {
                       $parameters['param']['currentValue'] = null;
                   }
               }
               else
               {
                   if($r['actuatorTarget'] == 'app')
                   {
                       $nrInputId = $r['nrInputId'];
                       //Se il widget è un attuatore su personal app, recuperiamo anche il suo valore più recente impostato
                        //$lastValueQuery = "SELECT value FROM ActuatorsAppsValues WHERE nrInputId = $nrInputId AND actuationResult = 'Ok' ORDER BY STR_TO_DATE(actionTime, '%Y-%m-%d %T') DESC LIMIT 1";
                        $lastValueQuery = "SELECT value FROM ActuatorsAppsValues WHERE widgetName='$widgetNameIteration' AND actuationResult = 'Ok' ORDER BY STR_TO_DATE(actionTime, '%Y-%m-%d %T') DESC LIMIT 1";
                        //fwrite($file, "lastValueQuery: " . $lastValueQuery . "\n");
                        $lastValueResult = $link->query($lastValueQuery);

                        if($lastValueResult)
                        {
                            $row = mysqli_fetch_assoc($lastValueResult);
                            $parameters['param']['currentValue'] = $row['value'];
                        }
                        else
                        {
                            $parameters['param']['currentValue'] = null;
                        }
                   }
               }
            }
        }

        $parameters['geoServerUrl'] = $geoServerUrl;
        $parameters['heatmapUrl'] = $heatmapUrl;

        // MS> Use defaults for geo server and heatmap URLs if necessary
        if($parameters['geoServerUrl'] == null) $parameters['geoServerUrl'] = "https://wmsserver.snap4city.org/";
        if($parameters['heatmapUrl'] == null) $parameters['heatmapUrl'] = "https://heatmap.snap4city.org/";
        // <MS

        $par_json = json_encode($parameters);
        $link->close();
        echo($par_json);
    }

        



