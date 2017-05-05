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

//file per colpiare una dashboard con un altro nome e duplicarne tutti i widget associati
include '../config.php';
session_start();
$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);
if (isset($_REQUEST['dashboardDuplication'])) 
{
    $copiaDash = $_REQUEST['dashboardDuplication'];

    $sourceDashName = mysqli_real_escape_string($link, $copiaDash['sourceDashboardName']); 
    $sourceDashAuthorName = mysqli_real_escape_string($link, $copiaDash['sourceDashboardAuthorName']); 
    $newDashName = mysqli_real_escape_string($link, $copiaDash['newDashboardName']);

    //Controllo su esistenza di una dashboard con il nome scelto per quella clonata  
    $sql0 = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard = '$newDashName'";
    $result0 = mysqli_query($link, $sql0) or die(mysqli_error($link));
    
    if ($result0->num_rows > 0) 
    {
        echo ("Errore: esiste già una dashboard con questo nome");
    } 
    else 
    {
        //Vengono selezionati tutti i parametri della dashboard sorgente
        //$sql = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard = '$sourceDashName'";
        $sql = "SELECT * FROM Dashboard.Config_dashboard INNER JOIN Dashboard.Users ON Dashboard.Config_dashboard.user = Dashboard.Users.IdUser WHERE name_dashboard = '$sourceDashName' AND Users.username = '$sourceDashAuthorName'";
        $result = mysqli_query($link, $sql) or die(mysqli_error($link));
        $resultList = [];
        
        if($result->num_rows > 0) 
        {
            while ($rows = mysqli_fetch_array($result)) 
            {
                $list = array(
                    $sourceDashId = $rows['Id'],
                    $sourceDashTitle = $rows['title_header'],
                    $sourceDashSubtitle = $rows['subtitle_header'],
                    $sourceDashHeaderColor = $rows['color_header'],
                    $sourceDashWidth = $rows['width'],
                    $sourceDashHeight = $rows['height'],
                    $sourceDashRows = $rows['num_rows'],
                    $sourceDashCols = $rows['num_columns'],
                    $sourceDashAuthor = $rows['user'],
                    $sourceDashStatus = $rows['status_dashboard'],
                    $sourceDashBckColor = $rows['color_background'],
                    $sourceDashExternalFrameColor = $rows['external_frame_color'],
                    $sourceDashHeaderFontColor = $rows['headerFontColor'],
                    $sourceDashFontSize = $rows['headerFontSize'],
                    $sourceDashLogoFilename = $rows['logoFilename'],
                    $sourceDashLogoLink = $rows['logoLink'],
                    $sourceDashWidgetsBorders = $rows['widgetsBorders'],
                    $sourceDashWidgetsBordersColor = $rows['widgetsBordersColor'],
                );
                array_push($resultList, $list);
            }
        }
        
        $time = date('Y-m-d');
        
        $statement = $link->prepare("INSERT INTO Dashboard.Config_dashboard (name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, color_background, external_frame_color, headerFontColor, headerFontSize, logoFilename, logoLink, widgetsBorders, widgetsBordersColor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->bind_param('ssssiiiiiisssdssss', $newDashName, $newDashName, $sourceDashSubtitle, $sourceDashHeaderColor, $sourceDashWidth, $sourceDashHeight, $sourceDashRows, $sourceDashCols, $sourceDashAuthor, $sourceDashStatus, $sourceDashBckColor, $sourceDashExternalFrameColor, $sourceDashHeaderFontColor, $sourceDashFontSize, $sourceDashLogoFilename, $sourceDashLogoLink, $sourceDashWidgetsBorders, $sourceDashWidgetsBordersColor);
        $result9 = $statement->execute();
        $idNuovaDash = mysqli_insert_id($link);
        
        $sql3 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$sourceDashId'";
        $result3 = mysqli_query($link, $sql3) or die(mysqli_error($link));
        $resultList3 = [];
        
        if ($result3->num_rows > 0) 
        {
            while ($rows3 = mysqli_fetch_array($result3)) 
            {
                $idOldWidget = $rows3['Id'];
                $name_w = $rows3['name_w'];
                $id_dashboard = $rows3['id_dashboard'];
                $id_metric = $rows3['id_metric'];
                $type_w = $rows3['type_w'];
                $n_row = $rows3['n_row'];
                $n_column = $rows3['n_column'];
                $size_rows = $rows3['size_rows'];
                $size_column = $rows3['size_columns'];
                $title_w = $rows3['title_w'];
                $color_w = $rows3['color_w'];
                $frequency_w = $rows3['frequency_w'];
                $temporal_range = $rows3['temporal_range_w'];
                $municipality_w = $rows3['municipality_w'];
                $infoMessage_w = $rows3['infoMessage_w'];
                $link_w = $rows3['link_w'];
                $parameters = $rows3['parameters'];
                $frame_w = $rows3['frame_color_w'];
                $udm = $rows3['udm'];
                $fontSize = $rows3['fontSize'];
                $fontColor = $rows3['fontColor'];
                $controlsPosition = $rows3['controlsPosition'];
                $showTitle = $rows3['showTitle'];
                $controlsVisibility = $rows3['controlsVisibility'];
                $zoomFactor = $rows3['zoomFactor'];
                $defaultTab = $rows3['defaultTab'];
                $zoomControlsColor= $rows3['zoomControlsColor'];
                $scaleX = $rows3['scaleX']; 
                $scaleY = $rows3['scaleY'];
                $sourceDashHeaderFontColor = $rows3['headerFontColor'];
                $styleParameters = $rows3['styleParameters'];
                $infoJson = $rows3['infoJson'];
                
                $num_wid = mysqli_insert_id($link) + 1;
                
                if($insqDbtb = $link->prepare("INSERT INTO Dashboard.Config_widget_dashboard (name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, infoJson) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) 
                {
                    $insqDbtb->bind_param('sissiiiissssssssssissssdisddsss', $nome_nuovo_wid, $idNuovaDash, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_column, $title_w, $color_w, $frequency_w, $temporal_range, $municipality_w, $infoMessage_w, $link_w, $parameters, $frame_w, $udm, $fontSize, $fontColor, $controlsPosition, $showTitle, $controlsVisibility, $zoomFactor, $defaultTab, $zoomControlsColor, $scaleX, $scaleY, $sourceDashHeaderFontColor, $styleParameters, $infoJson);
                    $result4 = $insqDbtb->execute();
                }
                else
                {
                    die("Error message: ". $mysqli->error);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error:' . $mysqli->error . '");';
                    echo 'window.location.href = "dashboard_configdash.php";';
                    echo '</script>';
                }
                
                //Workaround per scrivere il corretto id in coda al nome del widget anche per il primo record che viene scritto (mysqli_insert_id ritorna 0 in questo caso)
                $selId = "SELECT Max(Id) AS Id FROM Dashboard.Config_widget_dashboard where id_dashboard = $idNuovaDash";
                $resultId = mysqli_query($link, $selId) or die(mysqli_error($link));
                if($resultId) 
                {
                    while($row = mysqli_fetch_array($resultId)) 
                    {
                        if ((!is_null($row['Id'])) && (!empty($row['Id']))) 
                        {
                            $firstId = $row['Id'];
                        }
                    }
                }

                switch($type_w)
                {
                    case 'widgetSce':
                        //Sostituzione del vecchio Id widget col nuovo Id Widget
                        $nome_nuovo_wid = preg_replace('~widgetSce\d*~', 'widgetSce'.$firstId, $name_w);
                        //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                        $nome_nuovo_wid = preg_replace("/_\d+\_/", "_" . $idNuovaDash . "_", $nome_nuovo_wid);
                        break;

                    case 'widgetGenericContent':
                        //Sostituzione del vecchio Id widget col nuovo Id Widget
                        $nome_nuovo_wid = preg_replace('~widgetGenericContent\d*~', 'widgetGenericContent'.$firstId, $name_w);
                        //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                        $nome_nuovo_wid = preg_replace("/_\d+\_/", "_" . $idNuovaDash . "_", $nome_nuovo_wid);
                        break;
                    
                    case 'widgetTimeTrend':
                        //Sostituzione del vecchio Id widget col nuovo Id Widget
                        $nome_nuovo_wid = preg_replace('~widgetTimeTrend\d*~', 'widgetTimeTrend'.$firstId, $name_w);
                        //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                        $nome_nuovo_wid = preg_replace("/_\d+\_/", "_" . $idNuovaDash . "_", $nome_nuovo_wid);break;
                    
                    case 'widgetTimeTrendCompare':
                        //Sostituzione del vecchio Id widget col nuovo Id Widget
                        $nome_nuovo_wid = preg_replace('~widgetTimeTrendCompare\d*~', 'widgetTimeTrendCompare'.$firstId, $name_w);
                        //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                        $nome_nuovo_wid = preg_replace("/_\d+\_/", "_" . $idNuovaDash . "_", $nome_nuovo_wid);
                        break;

                    default:
                        $nome_nuovo_wid = $id_metric . '_' . $idNuovaDash . '_' . $type_w . $firstId;
                        break;
                }
                    
                $updFirstId = "UPDATE Dashboard.Config_widget_dashboard SET name_w = '$nome_nuovo_wid' WHERE id_dashboard = $idNuovaDash AND Id = $firstId";
                $result = mysqli_query($link, $updFirstId) or die(mysqli_error($link));
            }
        }
        
        /*Copia logo della dashboard*/
        if(($sourceDashLogoFilename != NULL) && ($sourceDashLogoFilename != ""))
        {
            $originalLogo = "../img/dashLogos/" . $sourceDashName . "/" . $sourceDashLogoFilename;
            $uploadFolder ="../img/dashLogos/". $newDashName ."/";
            
            if(file_exists("../img/dashLogos/") == false)
            {
                mkdir("../img/dashLogos/");
            }
            
            mkdir($uploadFolder);
            
            if(!is_dir($uploadFolder))  
            {  
                echo '<script type="text/javascript">';
                echo 'alert("Creation of directory dashLogos/"' . $name_dashboard . '"/ has not been possibile.");';
                echo '</script>';  
            }   
            else   
            {
                $clonedLogo = "../img/dashLogos/" . $newDashName . "/" . $sourceDashLogoFilename;
                if(copy($originalLogo, $clonedLogo) == false)
                {
                    echo '<script type="text/javascript">';
                    echo 'alert("Error while copying logo file from original dashboard directory to cloned dashboard directory");';
                    echo '</script>';  
                }
            }  
        }
        echo ("Dashboard has been cloned successfully");
    }
} 
else 
{
   echo ("errore nel passaggio dei parametri");
}

