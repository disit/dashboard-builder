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

    include '../config.php';//Escape

    session_start(); 
    $link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
    mysqli_select_db($link, $dbname);
    
    if(isset($_SESSION['loggedRole']))
    {
        if(isset($_POST['configuration_widgets'])) 
        { 
            $widgetsConfigJson = $_POST['configuration_widgets'];
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
        }
        echo 1;
    }
    
    mysqli_close($link);
    
    
