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
    session_start(); 
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    //Escape
    
    function canEditDashboard()
    {
        $result = false;
        if(isset($_SESSION['isAdmin']))
        {
            if($_SESSION['isAdmin'] == 0)
            {
                //Utente non amministratore, edita una dashboard solo se ne é l'autore
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName']))&&(isset($_SESSION['dashboardAuthorId']))&&($_SESSION['loggedUsername'] == $_SESSION['dashboardAuthorName']))
                {
                    $result = true;
                }
            }
            else if(($_SESSION['isAdmin'] == 1) || ($_SESSION['isAdmin'] == 2))
            {
                //Utente amministratore, edita qualsiasi dashboard
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName']))&&(isset($_SESSION['dashboardAuthorId'])))
                {
                    $result = true;
                }
            }
        }
        return $result;
    }
    
    if(!$link->set_charset("utf8")) 
    {
        echo '<script type="text/javascript">';
        echo 'alert("KO");';
        echo '</script>';
        printf("Error loading character set utf8: %s\n", $link->error);
        exit();
    }
    
    if(isset($_REQUEST['ident'])&&canEditDashboard())
    {
        $response = array();
        $dashboardId = $_SESSION['dashboardId'];
        $dashboardName = mysqli_real_escape_string($link, $_REQUEST['ident']); 
        $newDashboardTitle = mysqli_real_escape_string($link, $_REQUEST['inputTitleDashboard']); 
        $newDashboardSubtitle = mysqli_real_escape_string($link, $_REQUEST['inputSubTitleDashboard']); 
        $newDashboardColor = mysqli_real_escape_string($link, $_REQUEST['inputDashCol']);
        $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']); 
        $newDashboardBckColor =  mysqli_real_escape_string($link, $_REQUEST['inputDashBckCol']); 
        $newDashboardExtColor = mysqli_real_escape_string($link, $_REQUEST['inputDashExtCol']); 
        $headerFontSize = mysqli_real_escape_string($link, $_REQUEST['headerFontSize']); 
        $widgetsBorders = mysqli_real_escape_string($link, $_REQUEST['widgetsBorders']); 
        $widgetsBordersColor = mysqli_real_escape_string($link, $_REQUEST['inputWidgetsBordersColor']); 
        $headerFontColor = mysqli_real_escape_string($link, $_REQUEST['headerFontColor']); 
        $filename = NULL;
        //$logoLink = $_REQUEST['dashboardLogoLinkInput'];
        $logoLink = NULL;
        
        if($headerFontSize > 45)
        {
            $headerFontSize = 45;
        }
        
        //New version: lasciamo gli addendi espliciti per agevolare la lettura
        $width = ($nCols * 78) + 10;
        
        //Logo della dashboard
        $uploadFolder = "../img/dashLogos/" . $dashboardName . "/";
        
        if(($_REQUEST['dashboardLogoLinkInput'] != NULL) && ($_REQUEST['dashboardLogoLinkInput'] != ''))
        {
            $logoLink = mysqli_real_escape_string($link, $_REQUEST['dashboardLogoLinkInput']); 
            if (strpos($logoLink, 'http://') === false) 
            {
                $logoLink = 'http://' . $logoLink;
            }
        }
        
        //Nuovo file caricato, si cancella il vecchio e si aggiorna il nome del file su DB.
        if($_FILES['dashboardLogoInput']['size'] > 0)
        {
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
                $query = $link->prepare("UPDATE Dashboard.Config_dashboard SET title_header = ?, subtitle_header = ?, color_header = ?, width = ?, num_columns = ?, color_background = ?, external_frame_color = ?, headerFontColor = ?, headerFontSize = ?, logoFilename = ?, logoLink = ?, widgetsBorders = ?, widgetsBordersColor = ? WHERE Id = ?");
                $query->bind_param('sssiisssissssi', $newDashboardTitle, $newDashboardSubtitle, $newDashboardColor, $width, $nCols, $newDashboardBckColor, $newDashboardExtColor, $headerFontColor, $headerFontSize, $filename, $logoLink, $widgetsBorders, $widgetsBordersColor, $dashboardId);
                $result = $query->execute();
                
                $response["newLogo"] = "YES";
                $response["fileName"] = $uploadFolder . $filename;
                $response["logoLink"] = $logoLink;
                $response["width"] = $width;
                $response["num_cols"] = $nCols;
            }
        }//Nessun nuovo file caricato
        else
        {
            $query = $link->prepare("UPDATE Dashboard.Config_dashboard SET title_header = ?, subtitle_header = ?, color_header = ?, width = ?, num_columns = ?, color_background = ?, external_frame_color = ?, headerFontColor = ?, headerFontSize = ?, logoLink = ?, widgetsBorders = ?, widgetsBordersColor = ? WHERE Id = ?");
            $query->bind_param('sssiisssisssi', $newDashboardTitle, $newDashboardSubtitle, $newDashboardColor, $width, $nCols, $newDashboardBckColor, $newDashboardExtColor, $headerFontColor, $headerFontSize, $logoLink, $widgetsBorders, $widgetsBordersColor, $dashboardId);
            $result = $query->execute();
            $response["newLogo"] = "NO";
            $response["logoLink"] = $logoLink;
            $response["width"] = $width;
            $response["num_cols"] = $nCols;
        }

        if(!$result7) 
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




