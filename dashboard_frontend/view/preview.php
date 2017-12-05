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
       Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
    */

    include '../config.php';

    session_start();
    $link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
    mysqli_select_db($link, $dbname);

    $dashboardName = mysqli_real_escape_string($link, $_GET['dashboardName']); //Escape
    $dashboardAuthor = mysqli_real_escape_string($link, $_GET['dashboardAuthor']); //Escape

    $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard = '$dashboardName' AND Config_dashboard.user = '$dashboardAuthor'";
    $queryResult = mysqli_query($link, $query) or die(mysqli_error($link));

    if($queryResult) 
    {
        if($queryResult->num_rows > 0) 
        {     
            while($row2 = mysqli_fetch_array($queryResult)) 
            {
                $dashboardId = $row2['Id'];
            }
        }

        mysqli_close($link);
        //Lasciare il vecchio refuso "iddasboard" per non cambiare i link già esistenti.
        $url=  urldecode("index.php?iddasboard=".base64_encode($dashboardId));   
        header("location: ".$url);
    } 
    else 
    {
        mysqli_close($link);
    }