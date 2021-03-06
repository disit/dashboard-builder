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

    session_start();
    checkSession('Manager');
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    if(isset($_POST['configuration_widgets'])) 
    { 
        $widgetsConfigJson = $_POST['configuration_widgets'];
        $dashboardId = $_POST['dashboardId'];
        if (checkVarType($dashboardId, "integer") === false) {
            eventLog("Returned the following ERROR in saveWidgetsPositions.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
            exit();
        };
        if(!checkDashboardId($link, $dashboardId)) {
            eventLog("invalid request for saveWidgetsPositions.php for dashboardId = $dashboardId user: ".$_SESSION['loggedUsername']);
            exit;
        }
        
        $widgetsConfigArray = json_decode($widgetsConfigJson, true);

        foreach ($widgetsConfigArray as $item) 
        {
            $widgetName = mysqli_real_escape_string($link, $item['id']);
            $widgetCol = mysqli_real_escape_string($link, $item['col']); 
            $widgetRow = mysqli_real_escape_string($link, $item['row']); 

            $updqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET n_row = '$widgetRow', n_column = '$widgetCol' WHERE name_w = '$widgetName'";
            $result = mysqli_query($link, $updqDbtb);

            if(!$result) 
            {      
                mysqli_close($link);
                echo 0;
                exit();
            }
        }
        
        $q2 = "UPDATE Dashboard.Config_dashboard SET last_edit_date = CURRENT_TIMESTAMP WHERE Config_dashboard.Id = '$dashboardId'";
        $r2 = mysqli_query($link, $q2);

        if(!$r2) 
        {      
            mysqli_close($link);
            echo 0;
        }
        else
        {
            mysqli_close($link);
            echo 1;
        }
    }
    
    
    
