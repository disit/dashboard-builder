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
    
    $widgetName = $_GET['nomeWidget'];
    $link = new mysqli($host, $username, $password, $dbname);

    if($link->connect_error) 
    {
        die("Connection failed: " . $link->connect_error);
    }
    else
    {
        if (!$link->set_charset("utf8")) 
        {
            echo '<script type="text/javascript">';
            echo 'alert("KO");';
            echo '</script>';
            printf("Error loading character set utf8: %s\n", $link->error);
            exit();
        }
        
        $rows = array();
        foreach($widgetName as $widgetNameIteration)
        { 
            $widgetNameIteration = mysqli_real_escape_string($link, $widgetNameIteration);
            $sql = "SELECT * FROM Config_widget_dashboard WHERE name_w = '$widgetNameIteration'";
            $result = $link->query($sql);

            while($r = mysqli_fetch_assoc($result)) 
            {
                $parameters = array('param' => $r);
            }
        }    
        $par_json = json_encode($parameters);
        $link->close();
        echo($par_json);
    }

        



