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
    error_reporting(E_ERROR | E_NOTICE);
    date_default_timezone_set('Europe/Rome');

    session_start(); 
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
    
    $response = [];

    $q = "SELECT * FROM Dashboard.Config_widget_dashboard " . 
         "LEFT JOIN Dashboard.Widgets " .    
         "ON Config_widget_dashboard.type_w = Widgets.id_type_widget " .   
         "WHERE name_w = '$widgetName'";
    $r = mysqli_query($link, $q);

    if($r && mysqli_num_rows($r)>0) {
        $response['params'] = mysqli_fetch_assoc($r);       
        $response['detail'] = 'Ok';
    } else { //check if it is from datainspector
        $q = "SELECT * FROM Dashboard.DataInspector " . 
             "LEFT JOIN Dashboard.Widgets " .    
             "ON DataInspector.type_w = Widgets.id_type_widget " .   
             "WHERE name_w = '$widgetName'";
        $r = mysqli_query($link, $q);

        if($r && mysqli_num_rows($r)>0) {
            $response['params'] = mysqli_fetch_assoc($r);       
            $response['detail'] = 'Ok';
        }
        else {
            $response['detail'] = 'Ko';
        }
    }

    $response['geoServerUrl'] = $geoServerUrl;
    $response['heatmapUrl'] = $heatmapUrl;

	// MS> Use defaults for geo server and heatmap URLs if necessary
	if($response['geoServerUrl'] == null) $response['geoServerUrl'] = "https://wmsserver.snap4city.org/";
  if($response['heatmapUrl'] == null) $response['heatmapUrl'] = "https://heatmap.snap4city.org/";
	// <MS

    echo json_encode($response);