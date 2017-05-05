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

header("Content-type: application/json");
include '../config.php';
session_start();
$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);

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

if (!$link->set_charset("utf8")) 
{
    echo '<script type="text/javascript">';
    echo 'alert("KO");';
    echo '</script>';
    printf("Error loading character set utf8: %s\n", $link->error);
    exit();
}


if(isset($_GET['action']) && !empty($_GET['action'])) 
{
    $action = $_GET['action'];
    
    if($action == "get_dashboards")//Escape 
    {
        $loggedUsername = $_SESSION['loggedUsername'];
        
        if(($_SESSION['isAdmin'] == 1) || ($_SESSION['isAdmin'] == 2))
        {
            $query = "SELECT * FROM Dashboard.Config_dashboard INNER JOIN Users ON Config_dashboard.user = Users.IdUser ORDER BY Config_dashboard.name_dashboard ASC";
        }
        else if($_SESSION['isAdmin'] == 0)
        {
            $query = "SELECT * FROM Dashboard.Config_dashboard INNER JOIN Users ON Config_dashboard.user = Users.IdUser WHERE Users.username = '$loggedUsername' ORDER BY Config_dashboard.name_dashboard ASC";
        }
        $result = mysqli_query($link, $query) or die(mysqli_error($link));
        
        if($result->num_rows > 0) 
        {
            $dashboard_list = array();
            while ($row = mysqli_fetch_array($result)) {
                $dashboard = array("dashboard" => array(
                        "id" => $row['Id'],
                        "name" => $row['name_dashboard'],
                        "title_header" => $row['title_header'],
                        "creation_date" => $row['creation_date'],
                        "status" => $row['status_dashboard'],
                        "id_user" => $row['IdUser'],
                        "name_user" => $row['name'],
                        "surname_user" => $row['surname'],
                        "username" => $row['username'],
                        "reference" => $row['reference']
                ));
                $dashboard_list[] = $dashboard;
            }
            mysqli_close($link);
            echo json_encode($dashboard_list);
        }
        else 
        {
            mysqli_close($link);
        }
    } 
    else if($action == "get_metrics")//Escape 
    {
        $query2 = "SELECT * FROM Dashboard.Descriptions";
        $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));
        $metric_list = array();

        if ($result2->num_rows > 0) 
        {
            while ($row2 = mysqli_fetch_array($result2)) 
            {
                $metric = array(
                    "idMetric" => $row2['IdMetric'],
                    "descMetric" => $row2['description'],
                    "descShortMetric" => $row2['description_short'],
                    "statusMetric" => $row2['status'],
                    "areaMetric" => $row2['area'],
                    "sourceMetric" => $row2['source'],
                    "freqMetric" => ($row2['frequency'] / 1000),
                    "municipalityOptionMetric" => $row2['municipalityOption'],
                    "timeRangeOptionMetric" => $row2['timeRangeOption'],
                    "colorDefaultMetric" => $row2['colorDefault'],
                    "typeMetric" => $row2['metricType'],
                    "dataSourceMetric" => $row2['dataSource'],
                    "queryMetric" => $row2['query'],
                    "query2Metric" => $row2['query2'],
                    "queryTypeMetric" => $row2['queryType'],
                    "processTypeMetric" => $row2['processType'],
                    "thresholdMetric" => $row2['threshold'],
                    "thresholdEvalMetric" => $row2['thresholdEval'],
                    "thresholdEvalCountMetric" => $row2['thresholdEvalCount'],
                    "thresholdTimeMetric" => $row2['thresholdTime'],
                    "storingDataMetric" => $row2['storingData']
                );
                array_push($metric_list, $metric);
            }
        }

        for($i = 0; $i < count($metric_list); ++$i) 
        {
            $id_metric_tmp = $metric_list[$i]['idMetric'];
            $type_M = $metric_list[$i]['typeMetric'];

            $pattern = 'Percentuale';
            if (preg_match("/Percentuale/", $type_M)) 
            {
                $type_M = 'Percentuale';
            }
            //controlla se la metrica è uno SCE
            $metrcSCE = $metric_list[$i]['idMetric'];
            if (preg_match("/^Sce_/", $metrcSCE)) 
            {
                $querySCE = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType= 'SCE'";
                $resultSCE = mysqli_query($link, $querySCE) or die(mysqli_error($link));
                if ($resultSCE->num_rows > 0) 
                {
                    $result6 = mysqli_query($link, $querySCE) or die(mysqli_error($link));
                }
            } 
            else 
            {
                //fine controlli su SCE    
                //controlla se esiste una metrica unica
                $queryUnique = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '$id_metric_tmp'";
                $resultUnique = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                if ($resultUnique->num_rows > 0) 
                {
                    $result6 = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                } 
                else 
                {
                    if(($type_M == 'Map') || ($type_M == 'Button'))
                    {
                        $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType='$type_M'";
                    }
                    else 
                    {
                        $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '' && Widgets.widgetType REGEXP '$type_M' && Widgets.widgetType <> 'SCE'";
                    }
                    $result6 = mysqli_query($link, $query6) or die(mysqli_error($link));
                }
                //fine controllo sulla metrica unica
            }

            $widgets_tmp = array();
            if ($result6->num_rows > 0) 
            {
                while ($row6 = mysqli_fetch_array($result6)) 
                {
                    $widget_tmp = array("id_type_widget" => utf8_encode($row6['id_type_widget']),
                        "source_php_widget" => utf8_encode($row6['source_php_widget']),
                        //dati modificati
                        "size_rows_widget" => utf8_encode($row6['min_row']),
                        "max_rows_widget" => utf8_encode($row6['max_row']),
                        "size_columns_widget" => utf8_encode($row6['min_col']),
                        "max_columns_widget" => utf8_encode($row6['max_col']),
                        //fine dati modificati
                        "numeric_range" => $row6['numeric_rangeOption'],
                        "color_widgetOption" => utf8_encode($row6['color_widgetOption']),
                        "number_metrics_widget" => $row6['number_metrics_widget'],
                        "dimMap" => utf8_encode($row6['dimMap']),    
                    );

                    array_push($widgets_tmp, $widget_tmp);
                }
            }
            $metric_list[$i] = array_merge($metric_list[$i], array("widgets" => $widgets_tmp));
            unset($widgets_tmp);
        }
        mysqli_close($link);
        echo json_encode($metric_list);
    } 
    else if($action == "get_param_dashboard")//Escape 
    {
        //Questo controllo viene fatto per gestire le chiamate da dashboard_configdash.php e da index.php, che mandano l'ID della dashboard rispettivamente in sessione o in GET
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
            $dashboardId = $_SESSION['dashboardId'];
        }
        
        $query3 = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
        $result3 = mysqli_query($link, $query3) or die(mysqli_error($link));
        $dashboardParams = array();

        if ($result3->num_rows > 0) 
        {
            while ($row3 = mysqli_fetch_array($result3)) 
            {
                $dashboardParams[] = $row3;
            }
        } 
        echo json_encode($dashboardParams);
        unset($dashboardParams);
        mysqli_close($link);
    } 
    else if($action == "get_widgets_dashboard")//Escape 
    {
        //Questo controllo viene fatto per gestire le chiamate da dashboard_configdash.php e da index.php, che mandano l'ID della dashboard rispettivamente in sessione o in GET
        if (isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
        }
        else 
        {
            $dashboardId = $_SESSION['dashboardId'];
        }
        
        $query4 = "SELECT * FROM Config_widget_dashboard INNER JOIN Widgets ON Config_widget_dashboard.type_w=Widgets.id_type_widget WHERE id_dashboard = '$dashboardId'";
        $result4 = mysqli_query($link, $query4) or die(mysqli_error($link));
        $dashboardWidgets = array();

        if($result4->num_rows > 0) 
        {
            while ($row4 = mysqli_fetch_array($result4)) 
            {
                $widget = array(
                    "id_widget" => $row4['Id'],
                    "name_widget" => $row4['name_w'],
                    "id_metric_widget" => $row4['id_metric'],
                    "type_widget" => $row4['type_w'],
                    "n_row_widget" => $row4['n_row'],
                    "n_column_widget" => $row4['n_column'],
                    "size_rows_widget" => $row4['size_rows'],
                    "size_columns_widget" => $row4['size_columns'],
                    "title_widget" => preg_replace('/\s+/', '_', $row4['title_w']),
                    "color_widget" => $row4['color_w'],
                    "frequency_widget" => $row4['frequency_w'],
                    "temporal_range_widget" => $row4['temporal_range_w'],
                    "source_file_widget" => $row4['source_php_widget'],
                    "municipality_widget" => $row4['municipality_w'],
                    "message_widget" => $row4['infoMessage_w'],
                    "link_w" => $row4['link_w'],
                    "param_w" => $row4['parameters'],
                    "frame_color" => $row4['frame_color_w'],
                    "udm" => $row4['udm'],
                    "fontSize" => $row4['fontSize'],
                    "fontColor" => $row4['fontColor'],
                    "controlsPosition" => $row4['controlsPosition'],
                    "showTitle" => $row4['showTitle'],
                    "controlsVisibility" => $row4['controlsVisibility'],
                    "zoomFactor" => $row4['zoomFactor'],
                    "zoomControlsColor" => $row4['zoomControlsColor'],
                    "defaultTab" => $row4['defaultTab'],
                    "scaleX" => $row4['scaleX'],
                    "scaleY" => $row4['scaleY'],
                    "headerFontColor" => $row4['headerFontColor']
                );
                array_push($dashboardWidgets, $widget);
            }
        }
        for ($j = 0; $j < count($dashboardWidgets); ++$j) 
        {
            $id_metric_tmp = array();
            if(strpos($dashboardWidgets[$j]['id_metric_widget'], '+') !== false) 
            {
                $id_metric_tmp = explode('+', $dashboardWidgets[$j]['id_metric_widget']);
            } 
            else 
            {
                $id_metric_tmp[] = $dashboardWidgets[$j]['id_metric_widget'];
            }

            $metrics_tmp = array();
            for ($k = 0; $k < count($id_metric_tmp); ++$k) 
            {
                $query7 = "SELECT metricType, description_short, source, municipalityOption, timeRangeOption FROM Dashboard.Descriptions where IdMetric='$id_metric_tmp[$k]'";
                $result7 = mysqli_query($link, $query7) or die(mysqli_error($link));

                if ($result7->num_rows > 0) 
                {
                    while ($row7 = mysqli_fetch_array($result7)) 
                    {
                        $metric_tmp = array("id_metric" => $id_metric_tmp[$k],
                            "descripshort_metric" => $row7['description_short'],
                            "type_metric" => $row7['metricType'],
                            "range" => $row7['timeRangeOption'],
                            "source_metric" => utf8_encode(preg_replace('/\s+/', '_', $row7['source'])));

                        array_push($metrics_tmp, $metric_tmp);
                    }
                }
            }
            $dashboardWidgets[$j] = array_merge($dashboardWidgets[$j], array("metrics_prop" => $metrics_tmp));
            unset($metrics_tmp);
        }
        mysqli_close($link);
        echo json_encode($dashboardWidgets);
    } 
    else if($action == "get_param_widget")//Escape 
    {
        if(canEditDashboard())
        {
            $id_dash3 = $_SESSION['dashboardId'];
            $name_widget = mysqli_real_escape_string($link, $_GET['widget_to_modify']);

            $query5 = "SELECT Config_widget_dashboard.*, Widgets.*
                       FROM Config_widget_dashboard 
                       INNER JOIN Widgets
                       ON Config_widget_dashboard.type_w = Widgets.id_type_widget 
                       WHERE id_dashboard='$id_dash3' AND name_w='$name_widget'";

            $result5 = mysqli_query($link, $query5) or die(mysqli_error($link));

            if ($result5->num_rows > 0) {
                while ($row5 = mysqli_fetch_array($result5)) {
                    $widget_param = array(
                        "id_widget" => $row5['Id'],
                        "name_widget" => $row5['name_w'],
                        "id_metric_widget" => $row5['id_metric'],
                        "type_widget" => $row5['type_w'],
                        "n_row_widget" => $row5['n_row'],
                        "n_column_widget" => $row5['n_column'],
                        "size_rows_widget" => $row5['size_rows'],
                        "size_columns_widget" => $row5['size_columns'],
                        "title_widget" => $row5['title_w'],
                        "color_widget" => $row5['color_w'],
                        "frequency_widget" => $row5['frequency_w'],
                        "temporal_range_widget" => $row5['temporal_range_w'],
                        "widgets_metric" => $row5['type_w'],
                        "municipality_metric_widget" => $row5['municipality_w'],
                        "number_metrics_widget" => $row5['number_metrics_widget'],
                        "color_widgetOption_widget" => $row5['color_widgetOption'],
                        "info_mess" => $row5['infoMessage_w'],
                        "url" => $row5['link_w'],
                        "udm" => $row5['udm'],
                        "param_w" => $row5['parameters'],
                        "frame_color" => $row5['frame_color_w'],
                        "fontSize" => $row5['fontSize'],
                        "fontColor" => $row5['fontColor'],
                        "controlsPosition" => $row5['controlsPosition'],
                        "showTitle" => $row5['showTitle'],
                        "controlsVisibility" => $row5['controlsVisibility'],
                        "zoomFactor" => $row5['zoomFactor'],
                        "headerFontColor" => $row5['headerFontColor'],
                        "defaultTab" => $row5['defaultTab'],
                        "zoomControlsColor" => $row5['zoomControlsColor'],
                        "min_col" => $row5['min_col'],
                        "max_col" => $row5['max_col'],
                        "min_row" => $row5['min_row'],
                        "max_row" => $row5['max_row'],
                        "dimMap" => $row5['dimMap'],
                        "styleParameters" => $row5['styleParameters'],
                        "infoJson" => $row5['infoJson']
                    );
                }
            }

            $id_metric_tmp_w = array();
            if (strpos($widget_param['id_metric_widget'], '+') !== false) 
            {
                $id_metric_tmp_w = explode('+', $widget_param['id_metric_widget']);
            } 
            else 
            {
                $id_metric_tmp_w[] = $widget_param['id_metric_widget'];
            }

            $metrics_tmp_w = array();
            for ($k = 0; $k < count($id_metric_tmp_w); ++$k) 
            {
                $query8 = "SELECT metricType, description, area, source, status, municipalityOption, timeRangeOption FROM Dashboard.Descriptions where IdMetric='$id_metric_tmp_w[$k]'";
                $result8 = mysqli_query($link, $query8) or die(mysqli_error($link));

                if ($result8->num_rows > 0) 
                {
                    while ($row8 = mysqli_fetch_array($result8)) 
                    {
                        $metric_tmp_w = array("id_metric" => $id_metric_tmp_w[$k],
                            "descrip_metric_widget" => utf8_encode($row8['description']),
                            "type_metric_widget" => utf8_encode($row8['metricType']),
                            "source_metric_widget" => utf8_encode($row8['source']),
                            "area_metric_widget" => utf8_encode($row8['area']),
                            "status_metric_widget" => utf8_encode($row8['status']),
                            "municipalityOption_metric_widget" => utf8_encode($row8['municipalityOption']),
                            "timeRangeOption_metric_widget" => utf8_encode($row8['timeRangeOption']));

                        array_push($metrics_tmp_w, $metric_tmp_w);
                    }
                }
            }
            $widget_param = array_merge($widget_param, array("metrics_prop" => $metrics_tmp_w));
            unset($metrics_tmp_w);

            mysqli_close($link);
            echo json_encode($widget_param);
        }
    } 
    else if($action == "get_info_widget")//Escape 
    {
        $name_widget = mysqli_real_escape_string($link, $_GET['widget_info']);
        $query9 = "SELECT Config_widget_dashboard.Id AS Id_widget_dash, title_w, infoMessage_w
                   FROM Config_widget_dashboard 
                   INNER JOIN Widgets
                   ON Config_widget_dashboard.type_w=Widgets.id_type_widget 
                   WHERE name_w='$name_widget'";

        $result9 = mysqli_query($link, $query9) or die(mysqli_error($link));
        $widget_information = array();

        if ($result9->num_rows > 0) 
        {
            while ($row9 = mysqli_fetch_array($result9)) 
            {
                $widget_information = array(
                    "id_widget" => utf8_encode($row9['Id_widget_dash']),
                    "title_widget" => utf8_encode($row9['title_w']),
                    "info_mess" => utf8_encode($row9['infoMessage_w']),
                );
            }
        }
        mysqli_close($link);
        echo json_encode($widget_information);
    }
    else if($action == 'get_param_metrics')//Escape 
    {
        $name_metrics = mysqli_real_escape_string($link, $_REQUEST['metric_to_modify']);
        $queryX = "SELECT * FROM Dashboard.Descriptions WHERE Descriptions.IdMetric = '$name_metrics'";
        $resultX = mysqli_query($link, $queryX) or die(mysqli_error($link));
        $selected_metric = array();
        if($rowX = mysqli_fetch_array($resultX)) 
        {
            $selected_metric = array(
                "id_metric" => $rowX['IdMetric'],
                "descritpion_metric" => $rowX['description'],
                "status_metric" => $rowX['status'],
                "query_metric" => $rowX['query'],
                "query2_metric" => $rowX['query2'],
                "queryType_metric" => $rowX['queryType'],
                "metricType_metric" => $rowX['metricType'],
                "frequency_metric" => $rowX['frequency'],
                "processType_metric" => $rowX['processType'],
                "area_metric" => $rowX['area'],
                "source_metric" => $rowX['source'],
                "description_short_metric" => $rowX['description_short'],
                "dataSource_metric" => $rowX['dataSource'],
                "threshold_metric" => $rowX['threshold'],
                "thresholdEval_metric" => $rowX['thresholdEval'],
                "thresholdEvalCount_metric" => $rowX['thresholdEvalCount'],
                "thresholdTime_metric" => $rowX['thresholdTime'],
                "storingData_metric" => $rowX['storingData'],
                "municipalityOption_metric" => $rowX['municipalityOption'],
                "timeRangeOption_metric" => $rowX['timeRangeOption'],
                "colorDefault_metric" => $rowX['colorDefault']
            );
        } 
        else
        {
            echo 'not found ' . $name_metrics;
        }
            
        $r = json_encode($selected_metric);
        if ($r === FALSE)
        {
            echo "failed json_encode";
        }
        else
        {
             echo $r;
        }
        mysqli_close($link); 
    }
    else if ($action == "get_dataSource")//Escape 
    {
        $queryDS = "SELECT * FROM Dashboard.DataSource";
        $resultDS = mysqli_query($link, $queryDS) or die(mysqli_error($link));
        $DS_list = array();
        if ($resultDS->num_rows > 0) 
        {
            while($rowDS = mysqli_fetch_array($resultDS)) 
            {
                $DS = array(
                    "idDataS" => $rowDS['Id'],
                    "urlDataS" => $rowDS['url'],
                    "usernameDS" => $rowDS['username'],
                    "passwordDS" => $rowDS['password'],
                    "databaseDS" => $rowDS['database'],
                    "databaseTypeDS" => $rowDS['databaseType']
                );
                array_push($DS_list, $DS);
            }
            mysqli_close($link);
            echo json_encode($DS_list);
        }
    }
    else if($action == "query_test")//Escape 
    {
        $queryDaTestare = mysqli_real_escape_string($link, $_GET['valore_query']);
        $modality = mysqli_real_escape_string($link, $_GET['tipo_acquisizione']);
        if($modality == 'SQL') 
        {
            if (!mysqli_query($link, $queryDaTestare)) 
            {
                $risposta = "Error description: " . mysqli_error($link);
            } 
            else 
            {
                $risposta = "No errors found";
            }
        } 
        else if($modality == 'SPARQL') 
        {
            $risposta = "Al momento lo script non funziona su SPARQL";
        } 
        else if($modality == 'null') 
        {
            $risposta = "The modality of acquisition is not specified";
        }
        mysqli_close($link);
        echo json_encode($risposta);
    } 
    else if ($action == "get_widget")//Escape 
    {
        $queryWidgets = "SELECT * FROM Dashboard.Widgets";
        $resultWidgets = mysqli_query($link, $queryWidgets) or die(mysqli_error($link));
        $widgets_list = array();
        if($resultWidgets->num_rows > 0) 
        {
            while ($rowsWidgets = mysqli_fetch_array($resultWidgets)) 
            {
                $wids = array(
                    "type_widget" => $rowsWidgets['id_type_widget'],
                    "source_widget" => $rowsWidgets['source_php_widget']
                );
                array_push($widgets_list, $wids);
            }
            mysqli_close($link);
            echo json_encode($widgets_list);
        }
    } 
    else if ($action == "get_widget_types")//Escape 
    {
        $array_widget = array();
        if(isset($_SESSION['isAdmin']))
        {
            if(($_SESSION['isAdmin'] == 1) || ($_SESSION['isAdmin'] == 2))
            {
                $getWidget = "SELECT * FROM Dashboard.Widgets";
                $elencoWidgets = mysqli_query($link, $getWidget) or die(mysqli_error($link));
                if ($elencoWidgets->num_rows > 0) 
                {
                    while ($rowsW = mysqli_fetch_array($elencoWidgets)) {
                        $typeWid = array(
                            "type_widget" => $rowsW['id_type_widget'],
                            "source_widget" => $rowsW['source_php_widget'],
                            "min_row" => $rowsW['min_row'],
                            "max_row" => $rowsW['max_row'],
                            "min_col" => $rowsW['min_col'],
                            "max_col" => $rowsW['max_col'],
                            "type" => $rowsW['widgetType'],
                            "n_met" => $rowsW['number_metrics_widget'],
                            "color" => $rowsW['color_widgetOption'],
                            "unique" => $rowsW['unique_metric'],
                            "range" =>  $rowsW['numeric_rangeOption'],
                        );
                        array_push($array_widget, $typeWid);
                    }
                }
            }   
        }
        mysqli_close($link);
        echo json_encode($array_widget);
    } 
    else if (($action == "getSchedulers"))//Escape
    {
        $getSchedulers = "SELECT * FROM Dashboard.Schedulers";
        $elencoSchedulers = mysqli_query($link, $getSchedulers) or die(mysqli_error($link));
        $arraySchedulers = array();
        if ($elencoSchedulers->num_rows > 0) {
            while ($rowsS = mysqli_fetch_array($elencoSchedulers)) {
                $record = array(
                    "id" => $rowsS['id'],
                    "name" => $rowsS['name'],
                    "ip" => $rowsS['ip'],
                    "user" => $rowsS['user'],
                    "pass" => $rowsS['pass'],
                    "hasJobAreas" => $rowsS['hasJobAreas']
                );
                array_push($arraySchedulers, $record);
            }
            mysqli_close($link);
            echo json_encode($arraySchedulers);
        }
    }
    else if($action == "getJobAreas")//Escape
    {
        $schedulerId = mysqli_real_escape_string($link, $_REQUEST['schedulerId']);
        $getJobAreas = "SELECT * FROM Dashboard.JobAreas where schedulerId = $schedulerId";
        $elencoJobAreas = mysqli_query($link, $getJobAreas) or die(mysqli_error($link));
        $arrayJobAreas = array();
        if ($elencoJobAreas->num_rows > 0) {
            while ($rowsJ = mysqli_fetch_array($elencoJobAreas)) 
            {
                $record = array(
                    "id" => $rowsJ['id'],
                    "schedulerId" => $rowsJ['schedulerId'],
                    "name" => $rowsJ['name']
                );
                array_push($arrayJobAreas, $record);
            }
            mysqli_close($link);
            echo json_encode($arrayJobAreas);
        }
        else 
        {
            $record = array(
                    "id" => "none",
                    "schedulerId" => "none",
                    "name" => "none"
            );
            array_push($arrayJobAreas, $record);
            mysqli_close($link);
            echo json_encode($arrayJobAreas);
        }
    }
    else 
    {
        echo 'invalid action ' . $action;
    }
}


