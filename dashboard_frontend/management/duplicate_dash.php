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
if (isset($_REQUEST['duplication_dashboard'])) 
{
    $copiaDash = [];
    $copiaDash = $_REQUEST['duplication_dashboard'];

    $vecchiaDash = $copiaDash['nomeDashAttuale'];
    $nomeDash = $copiaDash['nomeDashDuplicata'];

    //Controllo che verifica se esiste già una dashboard con il nome che si vuole dare a quella duplicata  
    $sql0 = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard = '$nomeDash'";
    $result0 = mysqli_query($link, $sql0) or die(mysqli_error($link));
    if ($result0->num_rows > 0) 
    {
        echo ("Errore: esiste già una dashboard con questo nome");
    } 
    else 
    {
        //seleziona tutti i parametri della vecchia dashboard
        $sql = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.name_dashboard = '$vecchiaDash'";
        $result = mysqli_query($link, $sql) or die(mysqli_error($link));
        $resultList = [];
        //echo ($nomeDash);
        if ($result->num_rows > 0) 
        {
            while ($rows = mysqli_fetch_array($result)) 
            {
                $list = array(
                    $dash_id = $rows['Id'],
                    $dash_title_header = $rows['title_header'],
                    $dash_subtitle_header = $rows['subtitle_header'],
                    $dash_color_header = $rows['color_header'],
                    $dash_width = $rows['width'],
                    $dash_height = $rows['height'],
                    $dash_num_rows = $rows['num_rows'],
                    $dash_num_columns = $rows['num_columns'],
                    $dash_user = $rows['user'],
                    $dash_status_dashboard = $rows['status_dashboard'],
                    $dash_remains_width = $rows['remains_width'],
                    $dash_remains_height = $rows['remains_height'],
                    $dash_color_background = $rows['color_background'],
                    $dash_external_frame_color = $rows['external_frame_color']
                );
                array_push($resultList, $list);
            }
        }
        $idvecchiaDash = $dash_id;
        //inserisci i dati della vecchia dashboard in una nuova riga del database ad eccezione del nome e della data di creazione
        $sql2 = "INSERT INTO Dashboard.Config_dashboard(name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, remains_width, remains_height,color_background,external_frame_color)
                 VALUES ('$nomeDash','$nomeDash','$dash_subtitle_header','$dash_color_header','$dash_width','$dash_height','$dash_num_rows','$dash_num_columns','$dash_user','$dash_status_dashboard',current_timestamp,'$dash_remains_width','$dash_remains_height','$dash_color_background','$dash_external_frame_color')";

        $result2 = mysqli_query($link, $sql2) or die(mysqli_error($link));
        $idNuovaDash = mysqli_insert_id($link);
        
        //seleziona i dati dei widget associati alla dashboard vecchia
        $sql3 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard='$idvecchiaDash'";
        $result3 = mysqli_query($link, $sql3) or die(mysqli_error($link));
        $resultList3 = [];
        if ($result3->num_rows > 0) {

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
                $parameters = $rows3['parameters'];
                $link_w = $rows3['link_w'];
                $color_wh = $rows3['color_wh'];
                $frame_w = $rows3['frame_color_w'];
                $num_wid = mysqli_insert_id($link) + 1;
                switch($type_w)
                {
                    case 'widgetSce':
                        $nome_nuovo_wid = str_replace($id_dashboard, $idNuovaDash, $name_w);
                        break;
                    
                    case 'widgetGenericContent':
                        //$name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $comune_widget . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                        
                        $nome_nuovo_wid = preg_replace('~widgetGenericContent\d*~', 'widgetGenericContent'.$num_wid, $name_w);
                        break;
                    
                    default:
                        $nome_nuovo_wid = $id_metric . '_' . $idNuovaDash . '_' . $type_w . $num_wid;
                }
                
                $sql4 = "INSERT INTO Dashboard.Config_widget_dashboard (name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, color_wh)
                VALUES ('$nome_nuovo_wid','$idNuovaDash','$id_metric','$type_w','$n_row','$n_column','$size_rows','$size_column','$title_w','$color_w','$frequency_w','$temporal_range','$municipality_w','$infoMessage_w','$link_w','$parameters','$frame_w','$color_wh')";
                $result4 = mysqli_query($link, $sql4) or die(mysqli_error($link));
            }
        }
        echo ("Creata con successo copia della dashboard in uso");
    }
} 
else 
{
   echo ("errore nel passaggio dei parametri");
}
?>

