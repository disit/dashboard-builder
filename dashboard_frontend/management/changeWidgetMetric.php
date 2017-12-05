<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    
    //Definizioni di funzione
    function updateWidgetMetric($host, $username, $password, $dbName, $row1, $widgetName, $newMetricType)
    {
      $link = mysqli_connect($host, $username, $password);
      mysqli_select_db($link, $dbName);
      error_reporting(E_ERROR | E_NOTICE);
      /*$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "a");
      fwrite($file, "Host: " . $host . "\n");
      fwrite($file, "Username: " . $username . "\n");
      fwrite($file, "Password: " . $password . "\n");
      fwrite($file, "Link: " . $dbName . "\n");*/
       
      $id = 2681;
      $dashboardId = "328";
      $dashboardIdIndex = strrpos($widgetName, "_" . $dashboardId . "_");
      $newWidgetName = $newMetricType . substr($widgetName, $dashboardIdIndex);

      $query2 = "UPDATE Dashboard.Config_widget_dashboard SET name_w='$newWidgetName', id_metric='$newMetricType' WHERE Id='$id'";
      $result2 = mysqli_query($link, $query2);
      
      if($result2)
      {
         //TBD - SOSTITUIRLO CON UN JSON DEI DATI WIDGET AGGIORNATI, PER POTER POI CONSENTIRE IL RELOAD
         mysqli_close($link);
         return "Ok";
      }
      else
      {
         mysqli_close($link);
         return "Ko";
      }
    }

    
    //Inizio codice dell'API
    session_start(); 
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    
    if(isset($_SESSION['loggedUsername'])&&isset($_REQUEST['widgetName']))
    {
      $widgetName = $_REQUEST['widgetName'];
      $newMetricType = $_REQUEST['newMetricType'];
      
      /*$id = 2681;
      $dashboardId = "328";
      $dashboardIdIndex = strrpos($widgetName, "_" . $dashboardId . "_");
      $newWidgetName = $newMetricType . substr($widgetName, $dashboardIdIndex);

      $query2 = "UPDATE Dashboard.Config_widget_dashboard SET name_w='$newWidgetName', id_metric='$newMetricType' WHERE Id='$id'";
      $result2 = mysqli_query($link, $query2);

      if($result2)
      {
         //TBD - SOSTITUIRLO CON UN JSON DEI DATI WIDGET AGGIORNATI, PER POTER POI CONSENTIRE IL RELOAD
         return "Ok";
      }
      else
      {
         return "Ko";
      }*/
       
      $query1 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w='$widgetName'";
      $result1 = mysqli_query($link, $query1);
      
      $row = mysqli_fetch_assoc($result1);
      mysqli_close($link);
      updateWidgetMetric($host, $username, $password, $dbname, $row, $widgetName, $newMetricType);
         //mysqli_free_result($result1);
       
         
         
         /*$id = $row1['Id'];
         //$widgetName = $row1['name_w'];
         $dashboardId = $row1['id_dashboard'];
         $metricType = $row1['id_metric'];
         $widgetType = $row1['type_w'];
         $nRow = $row1['n_row'];
         $nCol = $row1['n_column'];
         $sizeRows = $row1['size_rows'];
         $sizeCols = $row1['size_columns'];
         $widgetTitle = $row1['title_w'];
         $color_w = $row1['color_w'];
         $freq = $row1['frequency_w'];
         $tempRange = $row1['temporal_range_w'];
         $municipality = $row1['municipality_w'];
         $infoMsg = $row1['infoMessage_w'];
         $link = $row1['link_w'];
         $parameters = $row1['parameters'];
         $frameColor = $row1['frame_color_w'];
         $udm = $row1['udm'];
         $udmPos = $row1['udmPos'];
         $fontSize = $row1['fontSize'];
         $fontColor = $row1['fontColor'];
         $controlsPosition = $row1['controlsPosition'];
         $showTitle = $row1['showTitle'];
         $controlsVisibility = $row1['controlsVisibility'];
         $zoomFactor = $row1['zoomFactor'];
         $defaultTab = $row1['defaultTab'];
         $zoomControlsColor = $row1['zoomControlsColor'];
         $scaleX = $row1['scaleX'];
         $scaleY = $row1['scaleY'];
         $headerFontColor = $row1['headerFontColor'];
         $styleParameters = $row1['styleParameters'];
         $infoJson = $row1['infoJson'];
         $serviceUri = $row1['serviceUri'];
         $viewMode = $row1['viewMode'];
         $hospitalList = $row1['hospitalList'];
         $lastSeries = $row1['lastSeries'];*/
         
         //mysqli_free_result($result1);

         //$query2 = "UPDATE Dashboard.Config_widget_dashboard SET name_w='$newWidgetName', id_metric='$newMetricType' WHERE Id='$id'";
         //$result2 = mysqli_query($link, $query2);

         //$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "w");
         //fwrite($file, "Query2: " . $query2 . "\n");
         //fwrite($file, "Result2: " . $result2 . "\n");

         /*if($result2)
         {
            //TBD - SOSTITUIRLO CON UN JSON DEI DATI WIDGET AGGIORNATI, PER POTER POI CONSENTIRE IL RELOAD
            return "Ok";
         }
         else
         {
            return "Ko";
         }*/
    }

    