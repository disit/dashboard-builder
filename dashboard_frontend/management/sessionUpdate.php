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
   
    //Altrimenti restituisce in output le warning
    error_reporting(E_ERROR | E_NOTICE);
    
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    
    if(!$link->set_charset("utf8")) 
    {
        exit();
    }
    
    if(isset($_REQUEST['sessionAction']))
    {
        $response = [];
        
        switch($_REQUEST['sessionAction'])
        {
            case 'closeViewSession':
                if(isset($_REQUEST['dashboardId']))
                {
                  $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']); 
                
                  if(isset($_SESSION["dashViewUsername" . $dashboardId]))
                  {

                    $response["detail"] = "Ok";
                    unset($_SESSION["dashViewUsername" . $dashboardId]);
                    unset($_SESSION["dashViewSessionEndTime" . $dashboardId]);
                  }
                  else
                  {
                     $response["detail"] = "Ko";	
                  }
                }
                else
                {
                   $response["detail"] = "Ko";
                }
                
                break;
                
            default:
               $response["detail"] = "Ko";
               break;
            //Lasciamo lo switch per eventuali nuovi case futuri
        }
        mysqli_close($link);
        echo json_encode($response);
    }
