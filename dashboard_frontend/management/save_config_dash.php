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
    session_start(); // Starting Session
    $link = mysqli_connect($host, $username, $password) or die("Error during database connection");
    mysqli_select_db($link, $dbname);

    if(isset($_REQUEST['ident']))
    {
        $response = array();
        $name_dash = $_REQUEST['ident'];
        $title_dash = $_REQUEST['inputTitleDashboard'];
        $subtitle_dash = $_REQUEST['inputSubTitleDashboard'];
        $color_dash = $_REQUEST['inputDashCol'];
        $nCols = $_POST['inputWidthDashboard'];
        $back_dash =  $_REQUEST['inputDashBckCol'];
        $external_dash = $_REQUEST['inputDashExtCol'];
        $headerFontSize = $_REQUEST['headerFontSize'];
        $headerFontColor = $_REQUEST['headerFontColor'];
        $filename = null;
        $logoLink = $_REQUEST['dashboardLogoLinkInput'];
        
        if($headerFontSize > 45)
        {
            $headerFontSize = 45;
        }
        
        //New version: lasciamo gli addendi espliciti per agevolare la lettura
        $width = ($nCols * 78) + 10;
        
        /*Logo della dashboard*/
        $uploadFolder = "../img/dashLogos/" . $name_dash . "/";
        
        if(($logoLink != null) && ($logoLink != ''))
        {
            if (strpos($logoLink, 'http://') === false) 
            {
                $logoLink = 'http://' . $logoLink;
            }
        }
        
        
        if($_FILES['dashboardLogoInput']['size'] > 0)
        {
            //Nuovo file caricato, si cancella il vecchio e si aggiorna il nome del file su DB.
            if(!file_exists("../img/dashLogos/"))
            {
                mkdir("../img/dashLogos/");
            }
            
            if(!file_exists($uploadFolder))
            {
                mkdir($uploadFolder);
            }
            else
            {
                $oldFiles = glob($uploadFolder . '*');
                foreach($oldFiles as $fileToDel)
                { 
                    if(is_file($fileToDel) && strpos(basename($fileToDel), 'old'))
                    {
                       unlink($fileToDel);
                    }
                }
            }
            
            $pointIndex = strrpos($_FILES['dashboardLogoInput']['name'], ".");
            $extension = substr($_FILES['dashboardLogoInput']['name'], $pointIndex);
            $filename = 'logo'.$extension;
            
            if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
            {  
                echo 'Something has gone wrong during logo upload: dashboard update has been cancelled';
                mysqli_close($link);
                exit();
            }
            else 
            {
                $updqDbtb = "UPDATE Dashboard.Config_dashboard SET title_header = '$title_dash', subtitle_header = '$subtitle_dash', color_header = '$color_dash', width = '$width', num_columns='$nCols', remains_width = NULL, color_background='$back_dash', external_frame_color='$external_dash', headerFontColor='$headerFontColor', headerFontSize=$headerFontSize, logoFilename='$filename', logoLink='$logoLink' WHERE name_dashboard='$name_dash'";
                $response["newLogo"] = "YES";
                $response["fileName"] = $uploadFolder . $filename;
                $response["logoLink"] = $logoLink;
                $response["width"] = $width;
                $response["num_cols"] = $nCols;
            }
        }
        else
        {
            //Nessun nuovo file caricato, si lascia quello attuale sia su filesystem che su DB.
            $updqDbtb = "UPDATE Dashboard.Config_dashboard SET title_header = '$title_dash', subtitle_header = '$subtitle_dash', color_header = '$color_dash', width = '$width', num_columns='$nCols', remains_width = NULL, color_background='$back_dash', external_frame_color='$external_dash', headerFontColor='$headerFontColor', headerFontSize=$headerFontSize, logoLink='$logoLink' WHERE name_dashboard='$name_dash'";
            $response["newLogo"] = "NO";
            $response["logoLink"] = $logoLink;
            $response["width"] = $width;
            $response["num_cols"] = $nCols;
        }

        $result = mysqli_query($link, $updqDbtb);

        if (!$result) 
        {
            echo 'Something has gone wrong: dashboard update has been cancelled';
        }
        else
        {
            echo json_encode($response);
        }
    }
    else 
    {
        echo 'Something has gone wrong: dashboard update has been cancelled';
    }
mysqli_close($link);




