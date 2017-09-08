<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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

include '../config.php'; //Escape
//Altrimenti restituisce in output le warning
error_reporting(E_ERROR | E_NOTICE);

session_start(); // Starting Session
$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);
mysqli_autocommit($link, false);

$dashboardId = $_SESSION['dashboardId'];
$widgetName = mysqli_real_escape_string($link, $_GET['nameWidget']);

if (isset($_GET['operation']) && !empty($_GET['operation'])) 
{
    $operation = mysqli_real_escape_string($link, $_GET['operation']);
    if($operation == "remove") 
    {
       mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE); 
       
       $notificatorQuery = "SELECT id_metric, title_w, type_w FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName' AND id_dashboard = '$dashboardId'";
       $result0 = mysqli_query($link, $notificatorQuery);
       
       if($result0)
       {
         $row0 = mysqli_fetch_assoc($result0);
         $generatorOriginalName = $row0['title_w'];
         $generatorOriginalType = $row0['id_metric'];
         $widgetType = $row0['type_w'];
       }
       else
       {
         $generatorOriginalName = null;
         $generatorOriginalType = null;
       }
       
       $query1 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName' AND id_dashboard = '$dashboardId'";
       $result1 = mysqli_query($link, $query1);
        
        if($result1) 
        {
            //Cancellazione del widget eliminato fra gli widget target degli widget che, eventualmente, lo puntano per l'interazione cross widget
            if(strpos($widgetName, 'widgetExternalContent') !== false)
            {
               //Cancellazione dai target dei buttons della stessa dashboard
               $query2 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%Button%' AND id_dashboard = '$dashboardId'";
               $result2 = mysqli_query($link, $query2);
               
               if($result2)
               {
                  while($row2 = mysqli_fetch_array($result2)) 
                  {
                     $targetList = json_decode($row2['parameters'], false);
                     $widgetId = $row2['Id'];
                     
                     if(count($targetList) > 0)
                     {
                        $index = array_search($widgetName, $targetList);
                        
                        if($index !== false)
                        {
                           array_splice($targetList, $index, 1);
                           $updatedTargetList = json_encode($targetList);
                           $query3 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                           $result3 = mysqli_query($link, $query3);
                           
                           if(!$result3)
                           {
                              $rollbackResult = mysqli_rollback($link);
                              mysqli_close($link);
                              echo '<script type="text/javascript">';
                              echo 'alert("Error while updating widget properties");';
                              echo 'window.location.href = "dashboard_configdash.php";';
                              echo '</script>';
                              exit();
                           }
                        }
                     }
                  }
               }
               else
               {
                  $rollbackResult = mysqli_rollback($link);
                  mysqli_close($link);
                  echo '<script type="text/javascript">';
                  echo 'alert("Error while deleting widget event producers from database");';
                  echo 'window.location.href = "dashboard_configdash.php";';
                  echo '</script>';
               }
               
               //Cancellazione dai target dei widget events della stessa dashboard
               $query4 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvents%' AND id_dashboard = '$dashboardId'";
               $result4 = mysqli_query($link, $query4);
               
               if($result4)
               {
                  while($row4 = mysqli_fetch_array($result4)) 
                  {
                     $targetList = json_decode($row4['parameters'], true);
                     $widgetId = $row4['Id'];
                     
                     if(count($targetList) > 0)
                     {
                        unset($targetList[$widgetName]);
                        $updatedTargetList = json_encode($targetList);
                        $query5 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                        $result5 = mysqli_query($link, $query5);

                        if(!$result5)
                        {
                           $rollbackResult = mysqli_rollback($link);
                           mysqli_close($link);
                           echo '<script type="text/javascript">';
                           echo 'alert("Error while updating widget properties");';
                           echo 'window.location.href = "dashboard_configdash.php";';
                           echo '</script>';
                           exit();
                        }
                     }
                  }
               }
               else
               {
                  $rollbackResult = mysqli_rollback($link);
                  mysqli_close($link);
                  echo '<script type="text/javascript">';
                  echo 'alert("Error while deleting widget event producers from database");';
                  echo 'window.location.href = "dashboard_configdash.php";';
                  echo '</script>';
               }
               
               //Cancellazione dai target dei widget events della stessa dashboard
               $query6 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetTrafficEvents%' AND id_dashboard = '$dashboardId'";
               $result6 = mysqli_query($link, $query6);
               
               if($result6)
               {
                  while($row6 = mysqli_fetch_array($result6)) 
                  {
                     $targetList = json_decode($row6['parameters'], true);
                     $widgetId = $row6['Id'];
                     
                     if(count($targetList) > 0)
                     {
                        unset($targetList[$widgetName]);
                        $updatedTargetList = json_encode($targetList);
                        
                        //Workaround per riportarlo nella forma giusta quando la lista dei parametri viene svuotata completamente
                        if($updatedTargetList == "[]")
                        {
                           $updatedTargetList = "{}";
                        }
                        
                        $query7 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                        $result7 = mysqli_query($link, $query7);

                        if(!$result7)
                        {
                           $rollbackResult = mysqli_rollback($link);
                           mysqli_close($link);
                           echo '<script type="text/javascript">';
                           echo 'alert("Error while updating widget properties");';
                           echo 'window.location.href = "dashboard_configdash.php";';
                           echo '</script>';
                           exit();
                        }
                     }
                  }
                  
                  $commit = mysqli_commit($link);
                  mysqli_close($link);
                  header("location: dashboard_configdash.php");
                  
                  //"Cancellazione" (validità settata a 0) del generatore dal notificatore
                  if(($generatorOriginalName != null)&&($generatorOriginalType != null))
                  {
                     $url = $notificatorUrl;
                     $generatorOriginalName = preg_replace('/\s+/', '+', $generatorOriginalName);
                     $generatorOriginalType = preg_replace('/\s+/', '+', $generatorOriginalType);
                     $containerName = preg_replace('/\s+/', '+', $_SESSION['dashboardTitle']);
                     $appUsr = preg_replace('/\s+/', '+', $_SESSION['loggedUsername']); 

                     $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . $containerName;
                     $url = $url.$data;

                     $options = array(
                         'http' => array(
                             'header'  => "Content-type: application/json\r\n",
                             'method'  => 'POST'
                             //'timeout' => 2
                         )
                     );

                     try
                     {
                        $context  = stream_context_create($options);
                        $callResult = @file_get_contents($url, false, $context);
                     }
                     catch (Exception $ex) 
                     {
                        //Non facciamo niente di specifico in caso di mancata risposta dell'host
                     }
                  }
               }
               else
               {
                  $rollbackResult = mysqli_rollback($link);
                  mysqli_close($link);
                  echo '<script type="text/javascript">';
                  echo 'alert("Error while deleting widget event producers from database");';
                  echo 'window.location.href = "dashboard_configdash.php";';
                  echo '</script>';
               }
            }
            else
            {
               $commit = mysqli_commit($link);
               
               if(($widgetType == "widgetButton")&&(file_exists("../img/widgetButtonImages/" . $widgetName)))
               {
                  array_map('unlink', glob("../img/widgetButtonImages/" . $widgetName . "/*.*"));
                  rmdir("../img/widgetButtonImages/" . $widgetName);
               }
               
               mysqli_close($link);
               header("location: dashboard_configdash.php");
               
               //"Cancellazione" (validità settata a 0) del generatore dal notificatore
               if(($generatorOriginalName != null)&&($generatorOriginalType != null))
               {
                  $url = $notificatorUrl;
                  $generatorOriginalName = preg_replace('/\s+/', '+', $generatorOriginalName);
                  $generatorOriginalType = preg_replace('/\s+/', '+', $generatorOriginalType);
                  $containerName = preg_replace('/\s+/', '+', $_SESSION['dashboardTitle']);
                  $appUsr = preg_replace('/\s+/', '+', $_SESSION['loggedUsername']); 

                  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . $containerName;
                  $url = $url.$data;

                  $options = array(
                      'http' => array(
                          'header'  => "Content-type: application/json\r\n",
                          'method'  => 'POST'
                          //'timeout' => 2
                      )
                  );

                  try
                  {
                     $context  = stream_context_create($options);
                     $callResult = @file_get_contents($url, false, $context);
                  }
                  catch (Exception $ex) 
                  {
                     //Non facciamo niente di specifico in caso di mancata risposta dell'host
                  }
               }
            }
        } 
        else 
        {
            $rollbackResult = mysqli_rollback($link);
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error while deleting widget record from database");';
            echo 'window.location.href = "dashboard_configdash.php";';
            echo '</script>';
        }
    }
}
