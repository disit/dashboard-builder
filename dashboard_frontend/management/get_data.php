<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

if($_GET['action'] != 'get_dashboard_icon')
{
    header("Content-type: application/json");
}
else
{
    header('Content-type: image/png');
}
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start();
checkSession('Public');

$start_get_data = microtime(true);

$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);

//Altrimenti restituisce in output le warning
error_reporting(E_ERROR | E_NOTICE);

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
        else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "RootAdmin"))
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

if(isset($_REQUEST['notBySession'])&&($_REQUEST['notBySession'] == "true"))
{
    //API per editor NodeRED
    if(isset($_GET['action']) && !empty($_GET['action'])) 
    {
        $action = $_GET['action'];
        
        switch($action)
        {
            case "getDashboardParamsAndWidgetsNR":
              //  if((isset($_GET['dashboardTitle']))&&(!empty($_GET['dashboardTitle']))&&(isset($_GET['username']))&&(!empty($_GET['username'])))
                if((isset($_GET['dashboardId']))&&(!empty($_GET['dashboardId']))&&(isset($_GET['username']))&&(!empty($_GET['username'])))
                {
                    $response = [];
                    $dashboardTitle = escapeForSQL(urldecode(@$_GET['dashboardTitle']), $link);
                    $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
                    $dashboardSubtitle = "";
                    
                    $username = escapeForSQL($_GET['username'], $link);

                 //   $query = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$dashboardTitle' AND user = '$username' AND deleted = 'no'";
                    $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId' AND user = '$username' AND deleted = 'no'";
                    $result = mysqli_query($link, $query);
                    
                    if($result)
                    {
                        $dashboardParams = [];
                        if(mysqli_num_rows($result) > 0)
                        {
                            //OK - Dashboard già esistente
                            $dashboardWidgets = [];
                            $dashboardParams = mysqli_fetch_assoc($result);
                            $dashboardId = $dashboardParams['Id'];

                            $query2 = "SELECT * FROM Config_widget_dashboard AS dashboardWidgets " .
                                      "LEFT JOIN Widgets AS widgetTypes ON dashboardWidgets.type_w = widgetTypes.id_type_widget " .
                                      "LEFT JOIN Descriptions AS metrics ON dashboardWidgets.id_metric = metrics.IdMetric " .   
                                      "LEFT JOIN NodeRedMetrics AS nrMetrics ON dashboardWidgets.id_metric = nrMetrics.name " . 
                                      "LEFT JOIN NodeRedInputs AS nrInputs ON dashboardWidgets.id_metric = nrInputs.name " . 
                                      "WHERE dashboardWidgets.id_dashboard = '$dashboardId' " .
                                      "AND dashboardWidgets.canceller IS NULL " .
                                      "AND dashboardWidgets.cancelDate IS NULL " . 
                                      "ORDER BY dashboardWidgets.n_row, dashboardWidgets.n_column ASC";

                            $result2 = mysqli_query($link, $query2);

                            if(mysqli_num_rows($result2) > 0) 
                            {
                                while($row = mysqli_fetch_assoc($result2)) 
                                {
                                    array_push($dashboardWidgets, $row);
                                }
                            }
                        }
                        else
                        {
                            //OK - Dashboard non esistente, viene creata
                            $nCols = 10;
                            $width = ($nCols * 78) + 10;
                            $org = $_SESSION['loggedOrganization'];

                            $query2 = "INSERT INTO Dashboard.Config_dashboard " . 
                                      "(name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, logoFilename, logoLink, widgetsBorders, widgetsBordersColor, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, fromNodeRed, gridColor, organizations) " .
                                      "VALUES ('$dashboardTitle', '$dashboardTitle', '$dashboardSubtitle', 'rgba(0, 0, 0, 1)', $width, 0, 0, $nCols, '$username', 1, now(), 'rgba(255, 255, 255, 1)', 'rgba(255, 255, 255, 1)', 'rgba(0,240,255,1)', 28, NULL, '', 'yes', 'rgba(0, 0, 0, 1)', 'author', 1, 'no', '[]', 'mediumResponsive', 'yes', 'rgba(238, 238, 238, 1)', '$org')";
                            
                            $result2 = mysqli_query($link, $query2);
                    
                            if($result2)
                            {
                             //   $query3 = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$dashboardTitle' AND user = '$username' AND deleted = 'no'";
                                $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId' AND user = '$username' AND deleted = 'no'";
                                $result3 = mysqli_query($link, $query3);
                                
                                if($result3)
                                {
                                    if(mysqli_num_rows($result3) > 0)
                                    {
                                        $dashboardParams = mysqli_fetch_assoc($result3);
                                        $dashboardWidgets = [];
                                    }
                                    else
                                    {
                                        $dashboardParams = "Ko";
                                        $dashboardWidgets = "Ko";
                                    }
                                }
                                else
                                {
                                    $dashboardParams = "Ko";
                                    $dashboardWidgets = "Ko";
                                }
                            }
                        }
                    }
                    else
                    {
                        $dashboardParams = "Ko";
                        $dashboardWidgets = "Ko";
                    }
                    
                    $response["dashboardParams"] = $dashboardParams; 
                    @$response["dashboardWidgets"] = $dashboardWidgets;

                    echo json_encode($response);
                    mysqli_close($link);
                }
                break;
            
            case "getAppMetricsNR":
                $user = mysqli_real_escape_string($link, $_REQUEST['user']);
                
                $query = "SELECT * FROM Dashboard.NodeRedMetrics WHERE user = '$user' ORDER BY NodeRedMetrics.name ASC";
                $result = mysqli_query($link, $query);
                $metric_list = array();

                if($result->num_rows > 0) 
                {
                    while($row = mysqli_fetch_assoc($result)) 
                    {
                        array_push($metric_list, $row);
                    }
                }

                for($i = 0; $i < count($metric_list); $i++) 
                {
                    $id_metric_tmp = $metric_list[$i]['id'];
                    $type_M = $metric_list[$i]['metricType'];
					
                    //Controlla se esiste una metrica unica
                    $queryUnique = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '$id_metric_tmp' AND Widgets.widgetCategory = 'dataViewer'";
                    $resultUnique = mysqli_query($link, $queryUnique);
                    if($resultUnique->num_rows > 0) 
                    {
                        $result6 = $resultUnique;
                    } 
                    else 
                    {
                        if(($type_M == 'Map') || ($type_M == 'Button'))
                        {
                            $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType='$type_M' AND Widgets.widgetCategory = 'dataViewer'";
                        }
                        else 
                        {
							if($type_M == 'webContent')
							{
								$query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetCategory = 'dataViewer' AND Widgets.domainType LIKE '%webContent%'";
							}
							else
							{
								$query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '' && Widgets.widgetType REGEXP '$type_M' && Widgets.widgetType <> 'SCE' AND Widgets.widgetCategory = 'dataViewer'";	
							}
                            
                        }
						
                        $result6 = mysqli_query($link, $query6) or die(mysqli_error($link));
                    }
                    //fine controllo sulla metrica unica
                    
                    $widgets_tmp = array();
                    if($result6->num_rows > 0) 
                    {
                        while ($row6 = mysqli_fetch_array($result6)) 
                        {
                            $widget_tmp = array("id_type_widget" => utf8_encode($row6['id_type_widget']),
                                "source_php_widget" => utf8_encode($row6['source_php_widget']),
                                "size_rows_widget" => utf8_encode($row6['min_row']),
                                "max_rows_widget" => utf8_encode($row6['max_row']),
                                "size_columns_widget" => utf8_encode($row6['min_col']),
                                "max_columns_widget" => utf8_encode($row6['max_col']),
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
                break;
                
            case "getMetricsNR":
                $query2 = "SELECT * FROM Dashboard.Descriptions ORDER BY IdMetric ASC";
                $result2 = mysqli_query($link, $query2);
                $metric_list = array();

                if($result2->num_rows > 0) 
                {
                    while($row2 = mysqli_fetch_array($result2)) 
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
                            "typeMetric" => $row2['metricType'],
                            "dataSourceMetric" => $row2['dataSource'],
                            "queryMetric" => $row2['query'],
                            "query2Metric" => $row2['query2'],
                            "queryTypeMetric" => $row2['queryType'],
                            "processTypeMetric" => $row2['processType'],
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
                        $querySCE = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType= 'SCE' AND Widgets.widgetCategory = 'dataViewer'";
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
                        $queryUnique = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '$id_metric_tmp' AND Widgets.widgetCategory = 'dataViewer'";
                        $resultUnique = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                        if ($resultUnique->num_rows > 0) 
                        {
                            $result6 = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                        } 
                        else 
                        {
                            if(($type_M == 'Map') || ($type_M == 'Button'))
                            {
                                $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType='$type_M' AND Widgets.widgetCategory = 'dataViewer'";
                            }
                            else 
                            {
                                $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '' && Widgets.widgetType REGEXP '$type_M' && Widgets.widgetType <> 'SCE' AND Widgets.widgetCategory = 'dataViewer'";
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
                                "size_rows_widget" => utf8_encode($row6['min_row']),
                                "max_rows_widget" => utf8_encode($row6['max_row']),
                                "size_columns_widget" => utf8_encode($row6['min_col']),
                                "max_columns_widget" => utf8_encode($row6['max_col']),
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
                break;
                
            case "getWidgetParamsNR":
                $username = mysqli_real_escape_string($link, $_REQUEST['username']);
                $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
                $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);

                $query5 = "SELECT Config_widget_dashboard.*, Widgets.*, NodeRedInputs.*
                           FROM Config_widget_dashboard 
                           LEFT JOIN Widgets
                           ON Config_widget_dashboard.type_w = Widgets.id_type_widget 
                           LEFT JOIN NodeRedInputs
                           ON Config_widget_dashboard.id_metric = NodeRedInputs.name
                           WHERE id_dashboard = $dashboardId AND name_w = '$widgetName'";

                $result5 = mysqli_query($link, $query5);

                if($result5->num_rows > 0) 
                {
                    while ($row5 = mysqli_fetch_array($result5)) 
                    {
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
                            "info_mess" => $row5['infoMessage_w'],
                            "url" => $row5['link_w'],
                            "udm" => $row5['udm'],
                            "udmPos" => $row5['udmPos'],
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
                            "infoJson" => $row5['infoJson'],
                            "serviceUri" => $row5['serviceUri'],
                            "viewMode" => $row5['viewMode'],
                            "hospitalList" => $row5['hospitalList'],
                            "lastSeries" => $row5['lastSeries'],
                            "notificatorRegistered" => $row5['notificatorRegistered'],
                            "notificatorEnabled" => $row5['notificatorEnabled'],
                            "enableFullscreenTab" => $row5['enableFullscreenTab'],
                            "enableFullscreenModal" => $row5['enableFullscreenModal'],
                            "fontFamily" => $row5['fontFamily'],
                            "entityJson" => $row5['entityJson'],
                            "attributeName" => $row5['attributeName'],
                            "actuatorTarget" => $row5['actuatorTarget'],
                            "nodeRedInputId" => $row5['id'],
                            "nodeRedInputName" => $row5['name'],
                            "nodeRedInputValueType" => $row5['valueType'],
                            "nodeRedInputStartValue" => $row5['startValue'],
                            "nodeRedInputDomainType" => $row5['domainType'],
                            "nodeRedInputMinValue" => $row5['minValue'],
                            "nodeRedInputMaxValue" => $row5['maxValue'],
                            "nodeRedInputOffValue" => $row5['offValue'],
                            "nodeRedInputOnValue" => $row5['onValue'],
                            "nodeRedInputDataPrecision" => $row5['dataPrecision'],
                            "rowParams" => $row5['rowParameters']
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
				
                for($k = 0; $k < count($id_metric_tmp_w); ++$k) 
                {
                    $query8 = "SELECT metricType, description, area, source, status, municipalityOption, timeRangeOption FROM Dashboard.Descriptions where IdMetric = '$id_metric_tmp_w[$k]'";
                    $result8 = mysqli_query($link, $query8);

                    if($result8->num_rows > 0) 
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
                    else
                    {
                        $query9 = "SELECT * FROM Dashboard.NodeRedMetrics where NodeRedMetrics.name = '$id_metric_tmp_w[$k]' AND NodeRedMetrics.user = '$username'";
                        $result9 = mysqli_query($link, $query9);
                        if($result9->num_rows > 0) 
                        {
                            while($row9 = mysqli_fetch_array($result8)) 
                            {
                                $metric_tmp_w = array("id_metric" => $id_metric_tmp_w[$k],
                                    "descrip_metric_widget" => utf8_encode($row8['shortDesc']),
                                    "type_metric_widget" => utf8_encode($row8['metricType']),
                                    "source_metric_widget" => "NodeRED",
                                    "area_metric_widget" => "NodeRED",
                                    "municipalityOption_metric_widget" => 0,
                                    "timeRangeOption_metric_widget" => 1);

                                array_push($metrics_tmp_w, $metric_tmp_w);
                            }
                        }
                    }
                }
                $widget_param = array_merge($widget_param, array("metrics_prop" => $metrics_tmp_w));
                unset($metrics_tmp_w);

                mysqli_close($link);
                echo json_encode($widget_param);
                break;
                
            case "getPersonalAppsInputs":
                $username = mysqli_real_escape_string($link, $_REQUEST['username']);
                $response = [];
                //Si userà quando raffineremo gli input per dashboard come chiesto dal Prof.
                $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
                
                $q = "SELECT * " .
                     "FROM Dashboard.NodeRedInputs " .
                     "WHERE user = '$username'";

                $r = mysqli_query($link, $q);
                if($r)
                {
                    $response['personalAppsInputs'] = [];
                    while($row = mysqli_fetch_assoc($r)) 
                    {
                        array_push($response['personalAppsInputs'], $row);
                    }
                    
                    $response['result'] = 'Ok';
                }
                else 
                {
                    $response['result'] = 'Ko';
                }
                
                mysqli_close($link);
                echo json_encode($response);
                break;
                
            default:
                break;
        }
    }
}
else
{
    if(isset($_GET['action']) && !empty($_GET['action'])) 
    {
        $action = $_GET['action'];
    //    $action = escapeForHTML($action);

        if($action == "getConfigurationFilesList") 
        {
            $response = [];
            $filesList = scandir("../conf");
            $j = 0;

            for($i = 0; $i < count($filesList); $i++)
            {
                if(($filesList[$i] != ".")&&($filesList[$i] != ".."))
                {
                   $response[$j]["fileName"] = $filesList[$i];
                   $fileContent = parse_ini_file("../conf/" . $response[$j]["fileName"]);
                   $response[$j]["fileDesc"] = $fileContent["fileDesc"];
                   $response[$j]["customForm"] = $fileContent["customForm"];
                   $response[$j]["fileDeletable"] = $fileContent["fileDeletable"];
                   $j++;
                }
            }

            echo json_encode($response);
        }
        else if($action == "getSingleModuleData")
        {
            $fileName = mysqli_real_escape_string($link, $_REQUEST['fileName']);
            $fileContent = parse_ini_file("../conf/" . $fileName);
            echo json_encode($fileContent);
        }
        else if($action == "get_dashboards")
        {
            $time_elapsed_get_data_secs = microtime(true) - $start_get_data;
        //    eventLog("Init get_data: " . $time_elapsed_get_data_secs ." sec.");
            $start_get_dashboards = microtime(true);
            $start = microtime(true);
            $orgFlag = "all";
            if (isset($_GET['param']) && !empty($_GET['param'])) {
                $orgFlag = $_GET['param'];
            }
            $loggedUsername = $_SESSION['loggedUsername'];
            $dashIds = [];
            $delegations = [];
            $today = date('Y-m-d');

            switch(($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']))
            {
                //Nuova casistica GDPR Maggio 2018
                //RootAdmin: le vede e le edita tutte
                case "RootAdmin":
                case "Public":
                    $visibility = ($_SESSION['isPublic'] ? "AND visibility='public'" : "");
                    if ($_SESSION['loggedRole'] == "RootAdmin" && $orgFlag != 'all') {
                        if (isset($_SESSION["loggedOrganization"])) {
                            $orgName = $_SESSION["loggedOrganization"];
                        } else {
                            $orgName = "Other";
                        }
                        $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.deleted = 'no' AND organizations REGEXP '$orgName' $visibility ORDER BY dashboards.name_dashboard ASC";
                    } else {
                        $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.deleted = 'no' $visibility ORDER BY dashboards.name_dashboard ASC";
                    }

                    $result = mysqli_query($link, $query);
                    $dashboard_list = array();

                    $time_elapsed_get_data_1query_secs = microtime(true) - $start_get_dashboards;
                //    eventLog("Init get_dashboards 1st query: " . $time_elapsed_get_data_1query_secs ." sec.");
                    $start_get_dash_NR = microtime(true);

                    if($result)
                    {
                        while($row = mysqli_fetch_assoc($result))
                        {
                            $dashboardId = $row['Id'];
                            $row['managementLbl'] = 'show';

                            //Test
                            if($row['nAccessPerDay'] == null)
                            {
                                $row['nAccessPerDay'] = 0;
                            }

                            if($row['nMinutesPerDay'] == null)
                            {
                                $row['nMinutesPerDay'] = 0;
                            }

                            switch($row['visibility'])
                            {
                                case 'public':
                                    if($row['user'] == $loggedUsername)
                                    {
                                     //   if (strpos($_GET['param'], 'My org') !== false && ($_SESSION['loggedOrganization'] == $row['organizations'])) {
                                            $row['visibilityLbl'] = 'My own: Public';
                                            $row['authorLbl'] = 'hide';
                                            $row['rightsLbl'] = 'show';
                                     /*   } else if (strpos($_GET['param'], 'all') !== false) {
                                            $row['visibilityLbl'] = 'My own: Public';
                                            $row['authorLbl'] = 'hide';
                                            $row['rightsLbl'] = 'show';
                                        }*/
                                    }
                                    else
                                    {
                                        $row['visibilityLbl'] = 'Public';
                                        $row['authorLbl'] = ($_SESSION['isPublic'] ? 'hide' : $row['user']);
                                        $row['rightsLbl'] = 'hide';
                                    }
                                    break;

                                case 'author':
                                    if($row['user'] == $loggedUsername)
                                    {
                                      //  if (strpos($_GET['param'], 'My org') !== false && ($_SESSION['loggedOrganization'] == $row['organizations'])) {
                                            $row['visibilityLbl'] = 'My own';
                                            $row['authorLbl'] = 'hide';
                                            $row['rightsLbl'] = 'Delegations';
                                    /*    } else if (strpos($_GET['param'], 'all') !== false) {
                                            $row['visibilityLbl'] = 'My own: Public';
                                            $row['authorLbl'] = 'hide';
                                            $row['rightsLbl'] = 'show';
                                        }*/
                                    }
                                    else
                                    {
                                        $row['visibilityLbl'] = 'Private';
                                        $row['authorLbl'] = $row['user'];
                                        $row['rightsLbl'] = 'Delegations3rd';
                                    }

                                    break;
                            }

                            $row['deleteLbl'] = 'show';
                            $row['editLbl'] = 'show';
                            $row['cloneLbl'] = 'show';
                            $row['name_dashboard'] = htmlspecialchars($row['name_dashboard']);

                            $hasBroker = false;
                            $hasIotApp = false;

                            //Controlla se ha widget verso BROKER
                            $dashTypeQ1 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE actuatorTarget = 'broker' AND id_dashboard = $dashboardId";
                            $dashTypeR1 = mysqli_query($link, $dashTypeQ1);

                            if($dashTypeR1)
                            {
                                if(mysqli_num_rows($dashTypeR1) > 0)
                                {
                                    $hasBroker = true;
                                }
                            }

                            //Controlla se ha widget verso NodeRED
                        /*    $dashTypeQ2_b = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' AND NodeRedMetrics.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId) " .
                                          "UNION " .
                                          "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> '' AND NodeRedInputs.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId)";
                            */
                            $dashTypeQ2 = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics JOIN Dashboard.Config_widget_dashboard ON  NodeRedMetrics.name=id_metric AND id_dashboard = $dashboardId " .
                                "WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' " .
                                "UNION " .
                                "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs " .
                                "JOIN Dashboard.Config_widget_dashboard ON  NodeRedInputs.name=id_metric AND id_dashboard = $dashboardId " .
                                "WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> ''";

                            $dashTypeR2 = mysqli_query($link, $dashTypeQ2);

                            if($dashTypeR2)
                            {
                                if(mysqli_num_rows($dashTypeR2) > 0)
                                {
                                    $hasIotApp = true;
                                }
                            }

                            $row['brokerLbl'] = $hasBroker;
                            $row['iotLbl'] = $hasIotApp;

                            array_push($dashboard_list, $row);
                        }
                    }
                    $time_elapsed_get_dashboards_rootadmin_NR_secs = microtime(true) - $start_get_dash_NR;
                //    eventLog("Duration get_dashboards NR for RootAdmin: " . $time_elapsed_get_dashboards_rootadmin_NR_secs ." sec.");
                    break;
                
                //Altri utenti: vedono proprie, quelle con delega, public, editano e cancellano solo le proprie
                case "Manager": case "AreaManager": case "ToolAdmin": 
                    if(isset($_SESSION['refreshToken'])) 
                    {

                        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

                        $accessToken = $tkn->access_token;
                        $_SESSION['refreshToken'] = $tkn->refresh_token;

                        if ($orgFlag != "all") {
                            //1) Reperimento elenco sue dashboard tramite chiamata ad api di ownership


                            $apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=DashboardID&accessToken=" . $accessToken;

                            $options = array(
                                'http' => array(
                                    'header' => "Content-type: application/json\r\n",
                                    'method' => 'GET',
                                    'timeout' => 30,
                                    'ignore_errors' => true
                                )
                            );

                            $context = stream_context_create($options);
                            $myDashboardsJson = file_get_contents($apiUrl, false, $context);

                            $myDashboards = json_decode($myDashboardsJson);

                            for ($i = 0; $i < count($myDashboards); $i++) {
                                array_push($dashIds, $myDashboards[$i]->elementId);
                            }

                            //echo "Dashboards: " . count($dashIds);
                            //exit();
                        }


                    //    if ($orgFlag != "My orgMy?linkId") {
                            //2) Reperimento elenco dashboard pubbliche tramite chiamata ad api delegation ad anonymous

                            // See Public Dashboard filtered by ORGANIZATION
                            if (isset($_SESSION["loggedOrganization"])) {
                                $ldapBaseDnOrg = "ou=" . $_SESSION["loggedOrganization"] . ",dc=foo,dc=example,dc=org";
                            } else {
                                $ldapBaseDnOrg = "ou=Other,dc=foo,dc=example,dc=org";
                            }

                            if ($orgFlag == "all") {
                                $apiUrl = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
                            } else {
                                $ldapBaseDnOrgEncoded = urlencode($ldapBaseDnOrg);
                                $apiUrl = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&groupname=" . $ldapBaseDnOrgEncoded;
                            }

                            // PRODUZIONE
                            //      $apiUrlNewProd= $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&groupname=" .  urlencode($ldapBaseDnOrg);

                            $options = array(
                                'http' => array(
                                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                                    'method' => 'GET',
                                    'timeout' => 30,
                                    'ignore_errors' => true
                                )
                            );

                            $context = stream_context_create($options);
                            $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

                            $delegatedDashboards = json_decode($delegatedDashboardsJson);

                            // Patch per filtrare le public di roottooladmin1
                            $orgNameSql = mysqli_real_escape_string($link, $_SESSION['loggedOrganization']);
                            $dashOrgQuery = "SELECT * FROM Dashboard.Config_dashboard WHERE organizations = '" . $orgNameSql . "'";

                            $resultOrgDashIds = mysqli_query($link, $dashOrgQuery);
                            $dashboardOrgList = array();

                            if ($resultOrgDashIds) {
                                while ($rowOrgDash = mysqli_fetch_assoc($resultOrgDashIds)) {

                                    $dashboardId = $rowOrgDash['Id'];
                                    if ($rowOrgDash['organizations'] == $_SESSION['loggedOrganization']) {
                                        array_push($dashboardOrgList, $dashboardId);
                                    }

                                }
                            }

                            for ($i = 0; $i < count($delegatedDashboards); $i++) {
                                if (@$delegatedDashboards[$i]->elementType == 'DashboardID') {
                                    if ($orgFlag != "all") {
                                        if (in_array($delegatedDashboards[$i]->elementId, $dashboardOrgList, true)) {
                                            array_push($dashIds, $delegatedDashboards[$i]->elementId);
                                        }
                                    } else {
                                        array_push($dashIds, $delegatedDashboards[$i]->elementId);
                                    }
                                }
                            }
                    //    }


                        if ($orgFlag != "all" && $orgFlag != "My org") {
                            //3) Reperimento elenco dashboard per cui è delegato chiamata ad api delegation
                            $apiUrl = $personalDataApiBaseUrl . "/v2/username/" . rawurlencode($loggedUsername) . "/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

                            $options = array(
                                'http' => array(
                                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                                    'method' => 'GET',
                                    'timeout' => 30,
                                    'ignore_errors' => true
                                )
                            );

                            $context = stream_context_create($options);
                            $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

                            $delegatedDashboards = json_decode($delegatedDashboardsJson);

                            for ($i = 0; $i < count($delegatedDashboards); $i++) {
                                if ($delegatedDashboards[$i]->elementType == 'DashboardID') {
                                    array_push($dashIds, $delegatedDashboards[$i]->elementId);
                                    //   }
                                    // CHECK GROUPNAMEDELEGATED
                                    if (!is_null($delegatedDashboards[$i]->groupnameDelegated)) {
                                        $groupDelegationString = "";
                                        if ($delegatedDashboards[$i]->groupnameDelegated != "") {
                                            if (strpos($delegatedDashboards[$i]->groupnameDelegated, 'cn') !== false) {
                                                if (strpos($delegatedDashboards[$i]->groupnameDelegated, 'ou') !== false) {
                                                    $auxString = "";
                                                    $auxString2 = "";
                                                    if (explode("cn=", $delegatedDashboards[$i]->groupnameDelegated) != "") {
                                                        $auxString = explode("cn=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                                                        $auxString = explode(",", $auxString)[0];
                                                        $auxString2 = explode("ou=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                                                        $auxString2 = explode(",", $auxString2)[0];
                                                        $auxString = $auxString2 . " - " . $auxString;
                                                    } else if (explode("ou=", $delegatedDashboards[$i]->groupnameDelegated) != "") {
                                                        $auxString = explode("ou=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                                                        $auxString = explode(",", $auxString)[0];
                                                    }
                                                    $newDelegation = ["delegationId" => $delegatedDashboards[$i]->id, "delegatedGroup" => $auxString];
                                                    $newDelegationString = $auxString;
                                                }
                                            } else {
                                                if (strpos($delegatedDashboards[$i]->groupnameDelegated, 'ou') !== false) {
                                                    $auxString = "";
                                                    explode("ou=", $delegatedDashboards[$i]->groupnameDelegated) != "";
                                                    $auxString = explode("ou=", $delegatedDashboards[$i]->groupnameDelegated)[1];
                                                    $auxString = explode(",", $auxString)[0];
                                                    $newDelegation = ["delegationId" => $delegatedDashboards[$i]->id, "delegatedGroup" => $auxString . " - All Groups"];
                                                    $newDelegationString = $auxString . " - All Groups";
                                                }
                                            }
                                        }
                                        $delegations['dash' . $delegatedDashboards[$i]->elementId] = $delegatedDashboards[$i]->usernameDelegator . " to Group: " . $newDelegationString;
                                    } else {
                                        $delegations['dash' . $delegatedDashboards[$i]->elementId] = $delegatedDashboards[$i]->usernameDelegator;
                                    }
                                }
                            }
                        }

                        //4) Scrittura ed esecuzione query
                        $dashIdsForQuery = implode(",", $dashIds);
                        $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.Id IN(" . $dashIdsForQuery . ") AND dashboards.deleted = 'no' ORDER BY dashboards.name_dashboard ASC";
                        
                        $result = mysqli_query($link, $query);
                        $dashboard_list = array();

                        $time_elapsed_get_data_1query_secs = microtime(true) - $start_get_dashboards;
                    //    eventLog("Init get_data 1st query: " . $time_elapsed_get_data_1query_secs ." sec.");
                        $start_get_dash_NR_user = microtime(true);

                        if($result) 
                        {
                            while($row = mysqli_fetch_assoc($result)) 
                            {
                                $row['authorLbl'] = 'hide';
                                $dashboardId = $row['Id'];
                                
                                switch($row['visibility'])
                                {
                                    case 'public':
                                        
                                        if($row['user'] == $loggedUsername)
                                        {
                                                $row['visibilityLbl'] = 'My own: Public';
                                                $row['managementLbl'] = 'show';
                                                $row['rightsLbl'] = 'show';
                                                $row['deleteLbl'] = 'show';
                                                $row['editLbl'] = 'show';
                                                $row['cloneLbl'] = 'show';
                                        }
                                        else
                                        {
                                            $row['visibilityLbl'] = 'Public';
                                            $row['rightsLbl'] = 'hide';
                                            $row['deleteLbl'] = 'hide';
                                            $row['editLbl'] = 'hide';
                                            $row['cloneLbl'] = 'hide';
                                        }
                                        break;

                                    case 'author':
                                        if($row['user'] == $loggedUsername)
                                        {
                                                $row['visibilityLbl'] = 'My own';
                                                $row['managementLbl'] = 'show';
                                                $row['rightsLbl'] = 'show';
                                                $row['deleteLbl'] = 'show';
                                                $row['editLbl'] = 'show';
                                                $row['cloneLbl'] = 'show';
                                        }
                                        else
                                        {
                                                $row['visibilityLbl'] = 'Delegated by ' . @$delegations['dash' . $row['Id']];
                                                $row['managementLbl'] = 'hide';
                                                $row['rightsLbl'] = 'hide';
                                                $row['deleteLbl'] = 'hide';
                                                $row['editLbl'] = 'hide';
                                                $row['cloneLbl'] = 'hide';

                                        }
                                        break;
                                }
                                
                                $hasBroker = false;
                                $hasIotApp = false;

                                //Controlla se ha widget verso BROKER
                                $dashTypeQ1 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE actuatorTarget = 'broker' AND id_dashboard = $dashboardId";
                                $dashTypeR1 = mysqli_query($link, $dashTypeQ1);

                                if($dashTypeR1)
                                {
                                    if(mysqli_num_rows($dashTypeR1) > 0)
                                    {
                                        $hasBroker = true;
                                    }
                                }

                                //Controlla se ha widget verso NodeRED
                            /*    $dashTypeQ2_b = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' AND NodeRedMetrics.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId) " .
                                              "UNION " .
                                              "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> '' AND NodeRedInputs.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId)";
                            */
                                $dashTypeQ2 = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics JOIN Dashboard.Config_widget_dashboard ON  NodeRedMetrics.name=id_metric AND id_dashboard = $dashboardId " .
                                    "WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' " .
                                    "UNION " .
                                    "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs " .
                                    "JOIN Dashboard.Config_widget_dashboard ON  NodeRedInputs.name=id_metric AND id_dashboard = $dashboardId " .
                                    "WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> ''";

                                $dashTypeR2 = mysqli_query($link, $dashTypeQ2);

                                if($dashTypeR2)
                                {
                                    if(mysqli_num_rows($dashTypeR2) > 0)
                                    {
                                        $hasIotApp = true;
                                    }
                                }

                                $row['brokerLbl'] = $hasBroker;
                                $row['iotLbl'] = $hasIotApp;
                                //$row['query'] = $dashTypeQ2;
                                
                                array_push($dashboard_list, $row);
                            }
                        }
                    }
                    else
                    {
                        switch($_SESSION['loggedRole'])
                        {
                            case "RootAdmin":
                                $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.deleted = 'no' ORDER BY dashboards.name_dashboard ASC";
                                break;
                            
                            default:
                                $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.user = '$loggedUsername' AND dashboards.deleted = 'no' ORDER BY dashboards.name_dashboard ASC";
                                break;
                        }
                        
                        $result = mysqli_query($link, $query);
                        $dashboard_list = array();

                        if($result) 
                        {
                            while($row = mysqli_fetch_assoc($result)) 
                            {
                                array_push($dashboard_list, $row);
                            }
                        }
                        //Gestisce solo le proprie dashboard
                        /*case "Manager":
                            $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.user = '$loggedUsername' AND deleted = 'no' ORDER BY Config_dashboard.name_dashboard ASC";
                            break;

                        //Gestisce le proprie dashboard e di quelle dei manager dei pools di cui è admin 
                        case "AreaManager": case "ToolAdmin": 
                           $query = "SELECT * FROM Dashboard.Config_dashboard AS dashes " .
                                     "WHERE dashes.user = '$loggedUsername' AND deleted = 'no' " . //Proprie dashboard
                                     "OR (dashes.user IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$loggedUsername' AND isAdmin = 1))) " .
                                     "ORDER BY dashes.name_dashboard ASC";
                           break;*/
                    }
                    $time_elapsed_get_dashboards_user_NR_secs = microtime(true) - $start_get_dash_NR_user;
                //    eventLog("Duration get_dashboards NR for user: " . $time_elapsed_get_dashboards_user_NR_secs ." sec.");
                    break;
            }  
            
            /*$result = mysqli_query($link, $query);
            $dashboard_list = array();

            if($result) 
            {
                while($row = mysqli_fetch_assoc($result)) 
                {
                    array_push($dashboard_list, $row);
                }
            }*/

            mysqli_close($link);
            echo json_encode($dashboard_list);
        }
        else if($action == "get_all_dashboards")
        {
            $time_elapsed_get_data_secs = microtime(true) - $start_get_data;
        //    eventLog("Init get_data ALL: " . $time_elapsed_get_data_secs ." sec.");
            $start_get_dashboards = microtime(true);
            $orgFlag = "all";
            if (isset($_GET['param']) && !empty($_GET['param'])) {
                $orgFlag = $_GET['param'];
            }
            $loggedUsername = $_SESSION['loggedUsername'];
            $dashIds = [];
            $delegations = [];
            $today = date('Y-m-d');

          //  switch(($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']))
          //  {
                //Nuova casistica GDPR Maggio 2018
                //RootAdmin: le vede e le edita tutte
              //  case "RootAdmin":
              //  case "Public":
                    $visibility = ($_SESSION['isPublic'] ? "AND visibility='public'" : "");
                    if ($_SESSION['loggedRole'] == "RootAdmin" && $orgFlag != 'all') {
                        if (isset($_SESSION["loggedOrganization"])) {
                            $orgName = $_SESSION["loggedOrganization"];
                        } else {
                            $orgName = "Other";
                        }
                        $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.deleted = 'no' AND organizations REGEXP '$orgName' $visibility ORDER BY dashboards.name_dashboard ASC";
                    } else {
                        $query = "SELECT * FROM Dashboard.Config_dashboard AS dashboards LEFT JOIN (SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$today') AS accesses ON dashboards.Id = accesses.IdDashboard WHERE dashboards.deleted = 'no' $visibility ORDER BY dashboards.name_dashboard ASC";
                    }

                    $result = mysqli_query($link, $query);
                    $dashboard_list = array();

                    $time_elapsed_get_data_1query_secs = microtime(true) - $start_get_dashboards;
            //        eventLog("Init get_dashboards ALL 1st query: " . $time_elapsed_get_data_1query_secs ." sec.");
                    $start_get_dash_NR = microtime(true);

                    if($result)
                    {
                        while($row = mysqli_fetch_assoc($result))
                        {
                            $dashboardId = $row['Id'];
                            $row['managementLbl'] = 'show';

                            //Test
                            if($row['nAccessPerDay'] == null)
                            {
                                $row['nAccessPerDay'] = 0;
                            }

                            if($row['nMinutesPerDay'] == null)
                            {
                                $row['nMinutesPerDay'] = 0;
                            }

                            switch($row['visibility'])
                            {
                                case 'public':
                                    if($row['user'] == $loggedUsername)
                                    {
                                        $row['visibilityLbl'] = 'My own: Public';
                                        $row['authorLbl'] = 'hide';
                                        $row['rightsLbl'] = 'show';
                                    }
                                    else
                                    {
                                        $row['visibilityLbl'] = 'Public';
                                        $row['authorLbl'] = ($_SESSION['isPublic'] ? 'hide' : $row['user']);
                                        $row['rightsLbl'] = 'hide';
                                    }
                                    break;

                                case 'author':
                                    if($row['user'] == $loggedUsername)
                                    {
                                        $row['visibilityLbl'] = 'My own';
                                        $row['authorLbl'] = 'hide';
                                        $row['rightsLbl'] = 'Delegations';
                                    }
                                    else
                                    {
                                        $row['visibilityLbl'] = 'Private';
                                        $row['authorLbl'] = $row['user'];
                                        $row['rightsLbl'] = 'Delegations3rd';
                                    }

                                    break;
                            }

                            $row['deleteLbl'] = 'show';
                            $row['editLbl'] = 'show';
                            $row['cloneLbl'] = 'show';

                            $hasBroker = false;
                            $hasIotApp = false;

                            //Controlla se ha widget verso BROKER
                            $dashTypeQ1 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE actuatorTarget = 'broker' AND id_dashboard = $dashboardId";
                            $dashTypeR1 = mysqli_query($link, $dashTypeQ1);

                            if($dashTypeR1)
                            {
                                if(mysqli_num_rows($dashTypeR1) > 0)
                                {
                                    $hasBroker = true;
                                }
                            }

                            //Controlla se ha widget verso NodeRED
                        /*    $dashTypeQ2_b = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' AND NodeRedMetrics.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId) " .
                                "UNION " .
                                "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> '' AND NodeRedInputs.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $dashboardId)";
                        */
                            $dashTypeQ2 = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics JOIN Dashboard.Config_widget_dashboard ON  NodeRedMetrics.name=id_metric AND id_dashboard = $dashboardId " .
                                "WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' " .
                                "UNION " .
                                "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs " .
                                "JOIN Dashboard.Config_widget_dashboard ON  NodeRedInputs.name=id_metric AND id_dashboard = $dashboardId " .
                                "WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> ''";

                            $dashTypeR2 = mysqli_query($link, $dashTypeQ2);

                            if($dashTypeR2)
                            {
                                if(mysqli_num_rows($dashTypeR2) > 0)
                                {
                                    $hasIotApp = true;
                                }
                            }

                            $row['brokerLbl'] = $hasBroker;
                            $row['iotLbl'] = $hasIotApp;

                            array_push($dashboard_list, $row);
                        }
                    }
            $stopFlag = 1;
            $time_elapsed_get_dashboards_rootadmin_NR_secs = microtime(true) - $start_get_dash_NR;
        //    eventLog("Duration get_dashboards ALL NR for RootAdmin: " . $time_elapsed_get_dashboards_rootadmin_NR_secs ." sec.");
            mysqli_close($link);
            echo json_encode($dashboard_list);
        }
        else if($action == "get_dashboard_icon")
        {
            $dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
            $query = "SELECT imageData, imageExt FROM Dashboard.dashboardsScreenshots WHERE dashboardsScreenshots.dashboardId = $dashboardId";
            $result = mysqli_query($link, $query);
            
            if($result)
            {
                $row = mysqli_fetch_assoc($result);
                $response = $row['imageData'];
            }
            else
            {
                $response = 'Ko';
            }
            
            mysqli_close($link);
            echo $response;
        }
        else if($action == "get_metrics")//6/12/2017 - Non rimuovere, usato da più moduli, va ristrutturato tutto
        {
            $query2 = "SELECT * FROM Dashboard.Descriptions ORDER BY IdMetric ASC";
            $result2 = mysqli_query($link, $query2) or die(mysqli_error($link));
            $metric_list = array();

            if ($result2->num_rows > 0) 
            {
                while($row2 = mysqli_fetch_array($result2)) 
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
                        "typeMetric" => $row2['metricType'],
                        "dataSourceMetric" => $row2['dataSource'],
                        "queryMetric" => $row2['query'],
                        "query2Metric" => $row2['query2'],
                        "queryTypeMetric" => $row2['queryType'],
                        "processTypeMetric" => $row2['processType'],
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
                    $querySCE = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType= 'SCE' AND Widgets.widgetCategory = 'dataViewer'";
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
                    $queryUnique = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '$id_metric_tmp' AND Widgets.widgetCategory = 'dataViewer'";
                    $resultUnique = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                    if ($resultUnique->num_rows > 0) 
                    {
                        $result6 = mysqli_query($link, $queryUnique) or die(mysqli_error($link));
                    } 
                    else 
                    {
                        if(($type_M == 'Map') || ($type_M == 'Button'))
                        {
                            $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.widgetType='$type_M' AND Widgets.widgetCategory = 'dataViewer'";
                        }
                        else 
                        {
                            $query6 = "SELECT * FROM Dashboard.Widgets WHERE Widgets.unique_metric = '' && Widgets.widgetType REGEXP '$type_M' && Widgets.widgetType <> 'SCE' AND Widgets.widgetCategory = 'dataViewer'";
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
                            "size_rows_widget" => utf8_encode($row6['min_row']),
                            "max_rows_widget" => utf8_encode($row6['max_row']),
                            "size_columns_widget" => utf8_encode($row6['min_col']),
                            "max_columns_widget" => utf8_encode($row6['max_col']),
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
        else if($action == "getMetricList")//06-12-2017 - Nuovo: usato da metrics.php
        {
            $q = "SELECT * FROM Dashboard.Descriptions ORDER BY Descriptions.IdMetric ASC";
            $r = mysqli_query($link, $q);
            $metrics = [];

            if($r)
            {
                while($row = mysqli_fetch_assoc($r))
                {
                    array_push($metrics, $row);
                }
            }

            mysqli_close($link);
            echo json_encode($metrics);
        }//26/02/2018 - Nuova: fatta in attività di miglioramento prestazionale, NON CANCELLARE LE VECCHIE
        else if($action == "getDashboardParamsAndWidgets") 
        {
            $response = [];
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
            
            $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);
            if($result)
            {
                $response["dashboardParams"] = mysqli_fetch_assoc($result);
                
                $query = "SELECT * FROM Config_widget_dashboard AS dashboardWidgets " .
                        "LEFT JOIN Widgets AS widgetTypes ON dashboardWidgets.type_w = widgetTypes.id_type_widget " .
                        "LEFT JOIN Descriptions AS metrics ON dashboardWidgets.id_metric = metrics.IdMetric " .   
                        "WHERE dashboardWidgets.id_dashboard = $dashboardId " .
                        "AND dashboardWidgets.canceller IS NULL " .
                        "AND dashboardWidgets.cancelDate IS NULL " .
                        "ORDER BY dashboardWidgets.n_row, dashboardWidgets.n_column ASC";
        
                $result = mysqli_query($link, $query);
                $dashboardWidgets = [];

                if(mysqli_num_rows($result) > 0) 
                {
                    while($row = mysqli_fetch_assoc($result)) 
                    {
                        array_push($dashboardWidgets, $row);
                    }
                }
                $response["dashboardWidgets"] = $dashboardWidgets;
            }
            else
            {
                $response["dashboardParams"] = "Ko";
                $response["dashboardWidgets"] = "Ko";
            }
            
            echo json_encode($response);
            mysqli_close($link);
        }//NON CANCELLARE
        else if($action == "get_param_dashboard") 
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

            if($result3->num_rows > 0) 
            {
                while ($row3 = mysqli_fetch_array($result3)) 
                {
                    $dashboardParams[] = $row3;
                }
            } 
            echo json_encode($dashboardParams);
            unset($dashboardParams);
            mysqli_close($link);
        } //NON CANCELLARE
        else if($action == "get_widgets_dashboard")
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

            $query4 = "SELECT * FROM Config_widget_dashboard INNER JOIN Widgets ON Config_widget_dashboard.type_w=Widgets.id_type_widget WHERE id_dashboard = '$dashboardId' AND Config_widget_dashboard.canceller IS NULL AND Config_widget_dashboard.cancelDate IS NULL ORDER BY n_row, n_column ASC";
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
                        "udmPos" => $row4['udmPos'],
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
        else if($action == "get_param_widget")
        {
            $dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
            $name_widget = mysqli_real_escape_string($link, $_GET['widget_to_modify']);

            $query5 = "SELECT Config_widget_dashboard.*, Widgets.*
                       FROM Config_widget_dashboard 
                       INNER JOIN Widgets
                       ON Config_widget_dashboard.type_w = Widgets.id_type_widget 
                       WHERE id_dashboard='$dashboardId' AND name_w='$name_widget'";

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
                        "info_mess" => $row5['infoMessage_w'],
                        "url" => $row5['link_w'],
                        "udm" => $row5['udm'],
                        "udmPos" => $row5['udmPos'],
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
                        "infoJson" => $row5['infoJson'],
                        "serviceUri" => $row5['serviceUri'],
                        "viewMode" => $row5['viewMode'],
                        "hospitalList" => $row5['hospitalList'],
                        "lastSeries" => $row5['lastSeries'],
                        "notificatorRegistered" => $row5['notificatorRegistered'],
                        "notificatorEnabled" => $row5['notificatorEnabled'],
                        "enableFullscreenTab" => $row5['enableFullscreenTab'],
                        "enableFullscreenModal" => $row5['enableFullscreenModal'],
                        "fontFamily" => $row5['fontFamily'],
                        "entityJson" => $row5['entityJson'],
                        "attributeName" => $row5['attributeName']
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
        else if($action == "get_info_widget")
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
            $metricId = mysqli_real_escape_string($link, $_REQUEST['metricId']);
            $result = [];
            $q1 = "SELECT * FROM Descriptions WHERE Descriptions.id = $metricId";
            $r1 = mysqli_query($link, $q1);

            if($r1) 
            {
                $row = mysqli_fetch_assoc($r1);
                $result['result'] = 'Ok';
                $result['metricData'] = $row;
            } 
            else
            {
                $result['result'] = 'Ko';
            }

            echo json_encode($result);
            mysqli_close($link); 
        }
        else if($action == "getDataSources")
        {
            $q = "SELECT * FROM Dashboard.DataSource ORDER BY Id ASC";
            $r = mysqli_query($link, $q);
            $result = [];
            if($r) 
            {
                while($row = mysqli_fetch_assoc($r)) 
                {
                    array_push($result, $row);
                }
            }
            mysqli_close($link);
            echo json_encode($result);
        }
        else if($action == "getSingleDataSource")
        {
            $response = [];
            $id = mysqli_real_escape_string($link, $_GET['id']);
            if(isset($_SESSION['loggedRole']))
            {
                $q = "SELECT * FROM Dashboard.DataSource WHERE intId = $id";
                $r = mysqli_query($link, $q);

                if($r) 
                {
                    $row = mysqli_fetch_assoc($r); 
                    $response['result'] = "Ok";
                    $response['data'] = $row;
                }
                else
                {
                    $response['result'] = "Ko";
                }
            }
            mysqli_close($link);
            echo json_encode($response);
        }
        else if($action == "getLocalUsers")
        {
            $q = "SELECT * FROM Dashboard.Users ORDER BY username ASC";
            $r = mysqli_query($link, $q);
            $result = [];
            if($r) 
            {
                while($row = mysqli_fetch_assoc($r)) 
                {
                    array_push($result, $row);
                }
            }
            mysqli_close($link);
            echo json_encode($result);
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
        else if($action == "get_widget_types")
        {
            $result = array();
            if(isset($_SESSION['loggedRole']))
            {
                $q = "SELECT * FROM Dashboard.Widgets ORDER BY id_type_widget ASC";
                $r = mysqli_query($link, $q);

                if($r) 
                {
                    while($row = mysqli_fetch_assoc($r)) 
                    {
                        array_push($result, $row);
                    }
                }
            }
            mysqli_close($link);
            echo json_encode($result);
        } 
        else if($action == "get_single_widget_type")
        {
            $response = [];
            $id = mysqli_real_escape_string($link, $_GET['id']);
            if(isset($_SESSION['loggedRole']))
            {
                $q = "SELECT * FROM Dashboard.Widgets WHERE id = $id";
                $r = mysqli_query($link, $q);

                if($r) 
                {
                    $row = mysqli_fetch_assoc($r); 
                    $response['result'] = "Ok";
                    $response['data'] = $row;
                }
                else
                {
                    $response['result'] = "Ko";
                }
            }
            mysqli_close($link);
            echo json_encode($response);
        }
        else if (($action == "getSchedulers"))//Escape
        {
            $getSchedulers = "SELECT * FROM Dashboard.Schedulers";
            $elencoSchedulers = mysqli_query($link, $getSchedulers);
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
        else if($action == "getUsers")
        {

        }
        else if($action == "getHTTPMetrics")
        {
            $query2 = "SELECT * FROM Dashboard.Descriptions WHERE process='JavaProcess'";
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
                        "statusMetric" => $row2['status_HTTPRetr'],
                        "areaMetric" => $row2['area'],
                        "sourceMetric" => $row2['source'],
                        "frequencyMetric" => ($row2['frequency']),
                        "typeMetric" => $row2['metricType'],
                        "sourceURLMetric" => $row2['dataSource'],
                        "scriptMetric" => $row2['query'],
                        "rawDataTypeMetric" => $row2['queryType'],
                        "usernameMetric" => $row2['username_HTTPRetr'],
                        "passwordMetric" => $row2['password_HTTPRetr']
                    );
                    array_push($metric_list, $metric);
                }
            }

            mysqli_close($link);
            echo json_encode($metric_list);
        }
        else if ($action == "filterChangeMetricTable")
        {
            $widgetType = $_REQUEST['widgetType'];
            $getUnitQuery = "SELECT * FROM Dashboard.WidgetsIconsMap WHERE mainWidget='$widgetType'";
            $resultUnit = mysqli_query($link, $getUnitQuery) or die(mysqli_error($link));
            $retArray = array();

            if ($resultUnit->num_rows > 0)
            {
                while ($rowTab = mysqli_fetch_array($resultUnit))
                {
                    $rowMetrics = array(
                        "mainWidget" => $rowTab['mainWidget'],
                        "targetWidget" => $rowTab['targetWidget'],
                        "unit" => $rowTab['snap4CityType'],
                        "icon" => $rowTab['icon'],
                        "mono_multi" => $rowTab['mono_multi'],
                        "widgetCategory" => $rowTab['widgetCategory']
                    );
                    array_push($retArray, $rowMetrics);
                }
            }

            mysqli_close($link);
            echo json_encode($retArray);
        }
        else 
        {
            $action = escapeForHTML($action);
            echo 'invalid action ' . $action;
        }
    }
}




