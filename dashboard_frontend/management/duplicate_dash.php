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

//file per colpiare una dashboard con un altro nome e duplicarne tutti i widget associati
include '../config.php';
session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

if(isset($_SESSION['loggedRole']))
{
    if(isset($_REQUEST['dashboardDuplication'])) 
    {
        $copiaDash = $_REQUEST['dashboardDuplication'];

        $sourceDashId = mysqli_real_escape_string($link, $copiaDash['sourceDashboardId']); 
        $sourceDashTitle = mysqli_real_escape_string($link, $copiaDash['sourceDashboardTitle']); 
        $sourceDashAuthorName = mysqli_real_escape_string($link, $copiaDash['sourceDashboardAuthorName']); 
        $newDashboardTitle = mysqli_real_escape_string($link, $copiaDash['newDashboardTitle']);

        $query0 = "SELECT Config_dashboard.logoFilename FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = $sourceDashId";
        $result0 = mysqli_query($link, $query0);

        if($result0)
        {
           $row0 = mysqli_fetch_array($result0);
           $sourceDashLogoFilename = $row0['logoFilename'];
        }
        else
        {
           echo "originalDashRecordQueryKo";
           exit();
        }

        //Controllo su esistenza di una dashboard con il nome scelto per quella clonata  
        $query1 = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.title_header = '$newDashboardTitle'";
        $result1 = mysqli_query($link, $query1) or die(mysqli_error($link));

        if($result1->num_rows > 0) 
        {
            echo "titleAlreadyUsed";
        } 
        else 
        {
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            //Vengono selezionati tutti i parametri della dashboard sorgente

            //$file = fopen("C:\Users\marazzini\Desktop\dashboardLog.txt", "w");

            $query2 = "INSERT INTO Dashboard.Config_dashboard (name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, color_background, external_frame_color, headerFontColor, headerFontSize, logoFilename, logoLink, widgetsBorders, widgetsBordersColor, reference, visibility, headerVisible) " .
                      "VALUES ('$newDashboardTitle', '$newDashboardTitle', " .
                      "(SELECT src.subtitle_header FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.color_header FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.width FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.height FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.num_rows FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.num_columns FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.user FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.status_dashboard FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.color_background FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.external_frame_color FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.headerFontColor FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.headerFontSize FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.logoFilename FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.logoLink FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.widgetsBorders FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.widgetsBordersColor FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "(SELECT src.reference FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId), " .
                      "'public', " .
                      "(SELECT src.headerVisible FROM Dashboard.Config_dashboard AS src WHERE src.Id= $sourceDashId)" .
                      ")";

             //fwrite($file, "Query2: " . $query2 . "\n");                   

             $result2 = mysqli_query($link, $query2);

             if($result2)
             {
                $clonedDashId = mysqli_insert_id($link);

                $query3 = "SELECT AUTO_INCREMENT AS MaxId FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                //fwrite($file, "Query3: " . $query3 . "\n");
                $result3 = mysqli_query($link, $query3);
                if($result3) 
                {
                   $row3 = mysqli_fetch_array($result3);
                   $maxId = $row3['MaxId'];

                   $query4 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$sourceDashId'";
                   //fwrite($file, "Query4: " . $query4 . "\n");
                   $result4 = mysqli_query($link, $query4);

                   if($result4)
                   {
                      if(mysqli_num_rows($result4) > 0) 
                      {
                         while($row4 = mysqli_fetch_array($result4)) 
                         {
                            $clonedWidgetId = $maxId;

                            //Costruzione del nome del widget clonato
                            switch($row4['type_w'])
                            {
                                case 'widgetSce':
                                    //Sostituzione del vecchio Id widget col nuovo Id Widget
                                    $clonedWidgetName = preg_replace('~widgetSce\d*~', 'widgetSce'.$clonedWidgetId, $row4['name_w']);
                                    //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                    $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                    break;

                                case 'widgetGenericContent':
                                    //Sostituzione del vecchio Id widget col nuovo Id Widget
                                    $clonedWidgetName = preg_replace('~widgetGenericContent\d*~', 'widgetGenericContent'.$clonedWidgetId, $row4['name_w']);
                                    //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                    $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                    break;

                                case 'widgetTimeTrend':
                                    //Sostituzione del vecchio Id widget col nuovo Id Widget
                                    $clonedWidgetName = preg_replace('~widgetTimeTrend\d*~', 'widgetTimeTrend'.$clonedWidgetId, $row4['name_w']);
                                    //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                    $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);break;

                                case 'widgetTimeTrendCompare':
                                    //Sostituzione del vecchio Id widget col nuovo Id Widget
                                    $clonedWidgetName = preg_replace('~widgetTimeTrendCompare\d*~', 'widgetTimeTrendCompare'.$clonedWidgetId, $row4['name_w']);
                                    //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                    $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                    break;

                                default:
                                    $clonedWidgetName = $row4['id_metric'] . '_' . $clonedDashId . '_' . $row4['type_w'] . $clonedWidgetId;
                                    break;
                            }

                            if(($row4['frequency_w'] == null)||($row4['frequency_w'] == '')||($row4['frequency_w'] == 'NULL'))
                            {
                               $frequency = "NULL";
                            }
                            else
                            {
                               $frequency = $row4['frequency_w'];
                            }

                            if(($row4['temporal_range_w'] == null)||($row4['temporal_range_w'] == '')||($row4['temporal_range_w'] == 'NULL'))
                            {
                               $temporal_range_w = "NULL";
                            }
                            else
                            {
                               $temporal_range_w = "'" . $row4['temporal_range_w'] . "'";
                            }

                            if(($row4['municipality_w'] == null)||($row4['municipality_w'] == '')||($row4['municipality_w'] == 'NULL'))
                            {
                               $municipality_w = "NULL";
                            }
                            else
                            {
                               $municipality_w = "'" . $row4['municipality_w'] . "'";
                            }

                            if(($row4['infoMessage_w'] == null)||($row4['infoMessage_w'] == '')||($row4['infoMessage_w'] == 'NULL'))
                            {
                               $infoMessage_w = "NULL";
                            }
                            else
                            {
                               if($row4['infoMessage_w'] == '')
                               {
                                  $infoMessage_w = "''";
                               }
                               else
                               {
                                  $infoMessage_w = "'" . mysqli_real_escape_string($link, $row4['infoMessage_w']) . "'";
                               }
                            }

                            if(($row4['link_w'] == null)||($row4['link_w'] == '')||($row4['link_w'] == 'NULL'))
                            {
                               $link_w = "NULL";
                            }
                            else
                            {
                               $link_w = "'" . $row4['link_w'] . "'";
                            }

                            if(($row4['parameters'] == null)||($row4['parameters'] == '')||($row4['parameters'] == 'NULL'))
                            {
                               $parameters = "NULL";
                            }
                            else
                            {
                               $parameters = "'" . $row4['parameters'] . "'";
                            }

                            if(($row4['frame_color_w'] == null)||($row4['frame_color_w'] == '')||($row4['frame_color_w'] == 'NULL'))
                            {
                               $frame_color_w = "NULL";
                            }
                            else
                            {
                               $frame_color_w = "'" . $row4['frame_color_w'] . "'";
                            }

                            if(($row4['udm'] == null)||($row4['udm'] == '')||($row4['udm'] == 'NULL'))
                            {
                               $udm = "NULL";
                            }
                            else
                            {
                               $udm = "'" . $row4['udm'] . "'";
                            }

                            if(($row4['udmPos'] == null)||($row4['udmPos'] == '')||($row4['udmPos'] == 'NULL'))
                            {
                               $udmPos = "NULL";
                            }
                            else
                            {
                               $udmPos = "'" . $row4['udmPos'] . "'";
                            }

                            if(($row4['fontSize'] == null)||($row4['fontSize'] == '')||($row4['fontSize'] == 'NULL'))
                            {
                               $fontSize = "NULL";
                            }
                            else
                            {
                               $fontSize = $row4['fontSize'];
                            }

                            if(($row4['fontColor'] == null)||($row4['fontColor'] == '')||($row4['fontColor'] == 'NULL'))
                            {
                               $fontColor= "NULL";
                            }
                            else
                            {
                               $fontColor = "'" . $row4['fontColor'] . "'";
                            }

                            if(($row4['controlsPosition'] == null)||($row4['controlsPosition'] == '')||($row4['controlsPosition'] == 'NULL'))
                            {
                               $controlsPosition = "NULL";
                            }
                            else
                            {
                               $controlsPosition = "'" . $row4['controlsPosition'] . "'";
                            }

                            if(($row4['showTitle'] == null)||($row4['showTitle'] == '')||($row4['showTitle'] == 'NULL'))
                            {
                               $showTitle = "NULL";
                            }
                            else
                            {
                               $showTitle = "'" . $row4['showTitle'] . "'";
                            }

                            if(($row4['controlsVisibility'] == null)||($row4['controlsVisibility'] == '')||($row4['controlsVisibility'] == 'NULL'))
                            {
                               $controlsVisibility = "NULL";
                            }
                            else
                            {
                               $controlsVisibility = "'" . $row4['controlsVisibility'] . "'";
                            }

                            if(($row4['zoomFactor'] == null)||($row4['zoomFactor'] == '')||($row4['zoomFactor'] == 'NULL'))
                            {
                               $zoomFactor = "NULL";
                            }
                            else
                            {
                               $zoomFactor = $row4['zoomFactor'];
                            }

                            if(($row4['defaultTab'] == null)||($row4['defaultTab'] == '')||($row4['defaultTab'] == 'NULL'))
                            {
                               $defaultTab = "NULL";
                            }
                            else
                            {
                               $defaultTab = $row4['defaultTab'];
                            }

                            if(($row4['zoomControlsColor'] == null)||($row4['zoomControlsColor'] == '')||($row4['zoomControlsColor'] == 'NULL'))
                            {
                               $zoomControlsColor = "NULL";
                            }
                            else
                            {
                               $zoomControlsColor = "'" . $row4['zoomControlsColor'] . "'";
                            }

                            if(($row4['scaleX'] == null)||($row4['scaleX'] == '')||($row4['scaleX'] == 'NULL'))
                            {
                               $scaleX = "NULL";
                            }
                            else
                            {
                               $scaleX = $row4['scaleX'];
                            }

                            if(($row4['scaleY'] == null)||($row4['scaleY'] == '')||($row4['scaleY'] == 'NULL'))
                            {
                               $scaleY = "NULL";
                            }
                            else
                            {
                               $scaleY = $row4['scaleY'];
                            }

                            if(($row4['headerFontColor'] == null)||($row4['headerFontColor'] == '')||($row4['headerFontColor'] == 'NULL'))
                            {
                               $headerFontColor = "NULL";
                            }
                            else
                            {
                               $headerFontColor = "'" . $row4['headerFontColor'] . "'";
                            }

                            if(($row4['styleParameters'] == null)||($row4['styleParameters'] == '')||($row4['styleParameters'] == 'NULL'))
                            {
                               $styleParameters = "NULL";
                            }
                            else
                            {
                               $styleParameters = "'" . $row4['styleParameters'] . "'";
                            }

                            if(($row4['infoJson'] == null)||($row4['infoJson'] == '')||($row4['infoJson'] == 'NULL'))
                            {
                               $infoJson = "NULL";
                            }
                            else
                            {
                               $infoJson = "'" . mysqli_real_escape_string($link, $row4['infoJson']) . "'";
                            }

                            if(($row4['serviceUri'] == null)||($row4['serviceUri'] == '')||($row4['serviceUri'] == 'NULL'))
                            {
                               $serviceUri = "NULL";
                            }
                            else
                            {
                               $serviceUri = "'" . $row4['serviceUri'] . "'";
                            }

                            if(($row4['viewMode'] == null)||($row4['viewMode'] == '')||($row4['viewMode'] == 'NULL'))
                            {
                               $viewMode = "NULL";
                            }
                            else
                            {
                               $viewMode = "'" . $row4['viewMode'] . "'";
                            }

                            if(($row4['hospitalList'] == null)||($row4['hospitalList'] == '')||($row4['hospitalList'] == 'NULL'))
                            {
                               $hospitalList = "NULL";
                            }
                            else
                            {
                               $hospitalList = "'" . $row4['hospitalList'] . "'";
                            }

                            if(($row4['lastSeries'] == null)||($row4['lastSeries'] == '')||($row4['lastSeries'] == 'NULL'))
                            {
                               $lastSeries = "NULL";
                            }
                            else
                            {
                               $lastSeries = "'" . $row4['lastSeries'] . "'";
                            }

                            $query5 = "INSERT INTO Dashboard.Config_widget_dashboard " .
                                      "(name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, " .
                                      "link_w, parameters, frame_color_w, udm, udmPos, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, " .
                                      "scaleX, scaleY, headerFontColor, styleParameters, infoJson, serviceUri, viewMode, hospitalList, lastSeries, notificatorRegistered, notificatorEnabled) " .
                                      "VALUES ('$clonedWidgetName', $clonedDashId, '" . $row4['id_metric'] . "', '" . $row4['type_w'] . "', " . $row4['n_row'] . ", " . $row4['n_column'] . ", " .
                                      "" . $row4['size_rows'] . ", " . $row4['size_columns'] . ", '" . $row4['title_w'] . "', '" . $row4['color_w'] . "', " . $frequency . ", " . $temporal_range_w . ", " .
                                      "" . $municipality_w . ", " . $infoMessage_w . ", " . $link_w . ", " . $parameters . ", " . $frame_color_w . ", " . $udm . ", " .
                                      "" . $udmPos . ", " . $fontSize . ", " . $fontColor . ", " . $controlsPosition . ", " . $showTitle . ", " . $controlsVisibility . ", " .
                                      "" . $zoomFactor . ", " . $defaultTab . ", " . $zoomControlsColor . ", " . $scaleX . ", " . $scaleY . ", " . $headerFontColor . ", " .
                                      "" . $styleParameters . ", " . $infoJson . ", " . $serviceUri . ", " . $viewMode . ", " . $hospitalList . ", " . $lastSeries . ", " .
                                      "'" . 'no' . "', '" . 'no' . "'" .
                                      ")";

                            //fwrite($file, "Query5: " . $query5 . "\n");

                            $result5 = mysqli_query($link, $query5);
                            if(!$result5)
                            {
                               mysqli_rollback($link);
                               echo "Ko";
                               exit();
                            }
                            else
                            {
                               $maxId++;
                            }
                         }

                         mysqli_commit($link);

                         //Copia logo della dashboard
                         if(($sourceDashLogoFilename != NULL) && ($sourceDashLogoFilename != ""))
                         {
                             $originalLogo = "../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename;
                             $uploadFolder ="../img/dashLogos/dashboard". $clonedDashId ."/";

                             if(file_exists("../img/dashLogos/") == false)
                             {
                                 mkdir("../img/dashLogos/");
                             }

                             mkdir($uploadFolder);

                             if(!is_dir($uploadFolder))  
                             {  
                                 echo "logoDirCreationKo";
                                 exit();  
                             }   
                             else   
                             {
                                 $clonedLogo = "../img/dashLogos/dashboard" . $clonedDashId . "/" . $sourceDashLogoFilename;
                                 if(copy($originalLogo, $clonedLogo) == false)
                                 {
                                     echo "logoFileCopyKo";
                                     exit();   
                                 }
                                 else
                                 {
                                    //mysqli_commit($link);
                                    echo "Ok";
                                    exit();
                                 }
                             }  
                         }
                         else
                         {
                            //mysqli_commit($link);
                            echo "Ok";
                            exit();
                         }
                      }
                      else
                      {
                          mysqli_commit($link);

                         //Copia logo della dashboard
                         if(($sourceDashLogoFilename != NULL) && ($sourceDashLogoFilename != ""))
                         {
                             $originalLogo = "../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename;
                             $uploadFolder ="../img/dashLogos/dashboard". $clonedDashId ."/";

                             if(file_exists("../img/dashLogos/") == false)
                             {
                                 mkdir("../img/dashLogos/");
                             }

                             mkdir($uploadFolder);

                             if(!is_dir($uploadFolder))  
                             {  
                                 echo "logoDirCreationKo";
                                 exit();  
                             }   
                             else   
                             {
                                 $clonedLogo = "../img/dashLogos/dashboard" . $clonedDashId . "/" . $sourceDashLogoFilename;
                                 if(copy($originalLogo, $clonedLogo) == false)
                                 {
                                     echo "logoFileCopyKo";
                                     exit();   
                                 }
                                 else
                                 {
                                    //mysqli_commit($link);
                                    echo "Ok";
                                    exit();
                                 }
                             }  
                         }
                         else
                         {
                            //mysqli_commit($link);
                            echo "Ok";
                            exit();
                         }
                      }
                   }
                   else
                   {
                      mysqli_rollback($link);
                      echo "Ko";
                      exit();
                   }
                }
                else
                {
                   mysqli_rollback($link);
                   echo "Ko";
                   exit();
                }
             }
             else
             {
                mysqli_rollback($link);
                echo "Ko";
                exit();
             }
        }
    } 
    else 
    {
       echo "Ko";
    }
}



