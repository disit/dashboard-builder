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
    session_start(); 
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    //Escape
    
    function canEditDashboard()
    {
        $result = false;
        if(isset($_SESSION['loggedRole']))
        {
            if($_SESSION['loggedRole'] == "Manager")
            {
                //Utente non amministratore, edita una dashboard solo se ne é l'autore
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName']))&&($_SESSION['loggedUsername'] == $_SESSION['dashboardAuthorName']))
                {
                    $result = true;
                }
            }
            else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "ToolAdmin"))
            {
                //Utente amministratore, edita qualsiasi dashboard
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName'])))
                {
                    $result = true;
                }
            }
        }
        return $result;
    }
    
    if(!$link->set_charset("utf8")) 
    {
        exit();
    }
    
    mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
    $queryFail = false;
    
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
        $visibility = mysqli_real_escape_string($link, $_POST['inputDashboardVisibility']);
        $filename = NULL;
        $logoLink = NULL;
        $embeddable = mysqli_real_escape_string($link, $_POST['embeddable']);
        $headerVisible = $_POST['headerVisible'];
        if(isset($_POST['authorizedPagesJson']))
        {
            if(($_POST['authorizedPagesJson'] != "[]")&&($_POST['authorizedPagesJson'] != ""))
            {
               $authorizedPagesJson = mysqli_real_escape_string($link, $_POST['authorizedPagesJson']); 
            }
            else
            {
                $authorizedPagesJson = NULL;
            }
        }
        else
        {
            $authorizedPagesJson = NULL;
        }
        
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
        }
        
        //Nuovo file caricato, si cancella il vecchio e si aggiorna il nome del file su DB.
        if($_FILES['dashboardLogoInput']['size'] > 0)
        {
            if(!file_exists("../img/dashLogos/"))
            {
                $oldMask = umask(0);
                mkdir("../img/dashLogos/", 0777);
                umask($oldMask);
            }
            
            if(!file_exists($uploadFolder))
            {
                $oldMask = umask(0);
                mkdir($uploadFolder, 0777);
                umask($oldMask);
            }
            else
            {
                $oldFiles = glob($uploadFolder . '*');
                foreach($oldFiles as $fileToDel)
                { 
                    if(is_file($fileToDel))
                    {
                       unlink($fileToDel);
                    }
                }
            }
            
            /*$pointIndex = strrpos($_FILES['dashboardLogoInput']['name'], ".");
            $extension = substr($_FILES['dashboardLogoInput']['name'], $pointIndex);
            $filename = 'logo'.$extension;*/
            $filename = $_FILES['dashboardLogoInput']['name'];
            
            if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
            {  
                echo 'Something has gone wrong during logo upload: dashboard update has been cancelled';
                mysqli_close($link);
                exit();
            }
            else 
            {
               chmod($uploadFolder.$filename, 0666); 
               $query = "UPDATE Dashboard.Config_dashboard SET title_header = '$newDashboardTitle', subtitle_header = '$newDashboardSubtitle', color_header = '$newDashboardColor', width = $width, num_columns = $nCols, color_background = '$newDashboardBckColor', external_frame_color = '$newDashboardExtColor', headerFontColor = '$headerFontColor', headerFontSize = $headerFontSize, logoFilename = '$filename', logoLink = '$logoLink', widgetsBorders = '$widgetsBorders', widgetsBordersColor = '$widgetsBordersColor', visibility = '$visibility', headerVisible = $headerVisible, embeddable = '$embeddable', authorizedPagesJson = '$authorizedPagesJson' WHERE Id = $dashboardId";
               $result = mysqli_query($link, $query);  
                
                if(!$result)
                {
                    $rollbackResult = mysqli_rollback($link);
                    mysqli_close($link);
                    $queryFail = true;
                    echo '<script type="text/javascript">';
                    echo 'alert("Error during dashboard update: please repeat the procedure.");';
                    echo 'window.location.href = "dashboard_configdash.php";';
                    echo '</script>';
                    die();
                }
                else
                {
                  $response["newLogo"] = "YES";
                  $response["fileName"] = $uploadFolder . $filename;
                  $response["logoLink"] = $logoLink;
                  $response["width"] = $width;
                  $response["num_cols"] = $nCols;
                }
            }
        }//Nessun nuovo file caricato
        else
        {
            $query = "UPDATE Dashboard.Config_dashboard SET title_header = '$newDashboardTitle', subtitle_header = '$newDashboardSubtitle', color_header = '$newDashboardColor', width = $width, num_columns = $nCols, color_background = '$newDashboardBckColor', external_frame_color = '$newDashboardExtColor', headerFontColor = '$headerFontColor', headerFontSize = $headerFontSize, logoLink = '$logoLink', widgetsBorders = '$widgetsBorders', widgetsBordersColor = '$widgetsBordersColor', visibility = '$visibility', headerVisible = $headerVisible, embeddable = '$embeddable', authorizedPagesJson = '$authorizedPagesJson' WHERE Id = $dashboardId";
            $result = mysqli_query($link, $query);
            
            if(!$result)
            {
               $rollbackResult = mysqli_rollback($link);
               mysqli_close($link);
               $queryFail = true;
               echo '<script type="text/javascript">';
               echo 'alert("Error during dashboard update: please repeat the procedure.");';
               echo 'window.location.href = "dashboard_configdash.php";';
               echo '</script>';
               die();
            }
            else
            {
               $response["newLogo"] = "NO";
               $response["logoLink"] = $logoLink;
               $response["width"] = $width;
               $response["num_cols"] = $nCols;
            }
        }
        
        //Cancellazione vecchi permessi di visualizzazione
         $delOldPermissionsQuery = "DELETE FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId";
         $delOldPermissionsResult = mysqli_query($link, $delOldPermissionsQuery);

         if(!$delOldPermissionsResult)
         {
             $rollbackResult = mysqli_rollback($link);
             mysqli_close($link);
             $queryFail = true;
             echo '<script type="text/javascript">';
             echo 'alert("Error during dashboard update: please repeat the procedure.");';
             echo 'window.location.href = "dashboard_configdash.php";';
             echo '</script>';
             die();
         }
         else
         {
            if($visibility == "restrict")
            {
               foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
               {
                  $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($dashboardId, '$selectedUser')";
                  $result4 = mysqli_query($link, $insertQuery);
               }
            }
         }
        
        if(!$queryFail) 
        {
            $commit = mysqli_commit($link);
            mysqli_close($link);
            echo json_encode($response);
        }
    }
    else 
    {
       mysqli_close($link);
       echo '<script type="text/javascript">';
       echo 'alert("Error during dashboard update: please repeat the procedure.");';
       echo 'window.location.href = "dashboard_configdash.php";';
       echo '</script>';
    }
    




