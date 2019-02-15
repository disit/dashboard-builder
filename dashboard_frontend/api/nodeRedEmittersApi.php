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

   include '../config.php';
   include '../controllers/WidgetDbRow.php';
   require '../sso/autoload.php';
   use Jumbojett\OpenIDConnectClient;
   
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
   
   function returnManagedStringForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return "'" . $original . "'";
        }
    }
    
    function returnManagedNumberForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return $original;
        }
    }
   
    function addWidget($link, $dashboardTitle, $username, $widgetType, $metricName, $metricType, $appId, $flowId, $nodeId, $widgetTitle, $startValue, $nrInputId)
    {
        $dashboardAuthorName = $username;
        $dashboardEditor = $username;
        $creator = $username;

        $widgetTypeDbRow = NULL;
        $id_metric = $metricName; 
        $newWidgetType = $widgetType; 
        $title_w = $widgetTitle;
        $n_row = NULL;
        $n_column = NULL;
        $size_rows = NULL; 
        $size_columns = NULL;
        $nextId = 1;
        $firstFreeRow = NULL;
        $parameters = [];

        $qExists = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE nodeId = '$nodeId'";
        $rExists = mysqli_query($link, $qExists);

        if($rExists)
        {
            if(mysqli_num_rows($rExists) > 0)
            {
                $rowExistentWidget = mysqli_fetch_assoc($rExists);
                $currentWidgetDashId = $rowExistentWidget['id_dashboard'];
                $currentWidgetUniqueId = $rowExistentWidget['name_w'];

                $qDashCfr = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = $currentWidgetDashId";
                $rDashCfr = mysqli_query($link, $qDashCfr);

                if($rDashCfr)
                {
                    $rowExWidgetDash = mysqli_fetch_assoc($rDashCfr);

                    if(($rowExWidgetDash['user'] == $username)&&($rowExWidgetDash['title_header'] == $dashboardTitle))
                    {
                        //Non ha cambiato dashboard
                        return true;
                    }
                    else
                    {
                        //Dashboard cambiata, dobbiamo cancellare il widget dalla vecchia e metterlo nella nuova
                        $q0 = "SELECT * FROM Dashboard.Config_dashboard WHERE user = '$username' AND title_header = '$dashboardTitle'";
                        $r0 = mysqli_query($link, $q0);

                        if($r0)
                        {
                            $row = mysqli_fetch_assoc($r0);
                            $id_dashboard = $row['Id'];

                            $q3 = "SELECT * " .
                                  "FROM Dashboard.WidgetsIconsMap AS iconsMap " .
                                  "LEFT JOIN Dashboard.Widgets AS widgets " .
                                  "ON iconsMap.mainWidget = widgets.id_type_widget " . 
                                  "WHERE iconsMap.mainWidget = '$newWidgetType' AND iconsMap.targetWidget = ''";

                            $r3 = mysqli_query($link, $q3);

                            if($r3)
                            {
                                $widgetTypeDbRow = mysqli_fetch_assoc($r3);
                                try
                                {
                                    $defaultParameters = json_decode($widgetTypeDbRow['defaultParametersMainWidget'], true);
                                    $defaultParametersTarget = json_decode($widgetTypeDbRow['defaultParametersTargetWidget'], true);
                                }
                                catch(Exception $e)
                                {

                                }

                                if($widgetTypeDbRow['mono_multi'] == 'Mono')
                                {
                                    //Caso widget selezionato di tipo mono
                                    if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                                    {
                                        //Caso widget selezionato di tipo singolo (mancano i series e qualcun altro)
                                        $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                        $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                        //Calcolo del next id
                                        $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                        if ((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT']))) 
                                        {
                                            $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                        }

                                        //Calcolo del first free row
                                        $q2 = "SELECT MAX(n_row + size_rows) AS maxRow FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $id_dashboard";
                                        $r2 = mysqli_query($link, $q2);

                                        if($r2)
                                        {
                                            $row2 = mysqli_fetch_assoc($r2);
                                            if($row2['maxRow'] == null)
                                            {
                                                $firstFreeRow = 1;
                                            }
                                            else
                                            {
                                                $firstFreeRow = $row2['maxRow'];
                                            }

                                            //Costruzione n_row ed n_column
                                            $n_row = $firstFreeRow;
                                            $n_column = 1;

                                            //Costruzione size_rows e size_columns
                                            $size_rows = $defaultParameters['size_rows'];
                                            $size_columns = $defaultParameters['size_columns'];

                                            //Costruzione nome del widget
                                            $type_w = $widgetType;
                                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                            //Costruzione titolo widget
                                            $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], $defaultParameters['udm'], $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], 'no', null, null);
                                            $newWidgetDbRow->actuatorTarget = "app";
                                            
                                            if($type_w == 'widgetExternalContent')
                                            {
                                                $newWidgetDbRow->link_w = "http://www.disit.org";
                                            }

                                            //LASCIARLO COMMENTATO NON CANCELLARE - Da NodeRED non dovremmo quasi mai avere a che fare con le factory, ma in futuro può cambiare    
                                            /*if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                            {
                                                $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                                $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom);
                                                $newWidgetDbRow = $widgetFactory->completeWidget();
                                            }*/

                                            $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";

                                            //Query fields
                                            $newQueryFields = "";
                                            //Query values
                                            $newQueryValues = "";

                                            $count = 0;
                                            foreach($newWidgetDbRow as $key => $value) 
                                            {
                                                if($count == 0)
                                                {
                                                    $newQueryFields = $key;
                                                    $newQueryValues = returnManagedStringForDb($value);
                                                }
                                                else
                                                {
                                                    $newQueryFields = $newQueryFields . ", " . $key;
                                                    $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                }

                                                $count++;
                                            }

                                            $newQueryFields = $newQueryFields . ", appId, flowId, nrMetricType, nodeId";
                                            $newQueryValues = $newQueryValues . ", '$appId', '$flowId', '$metricType', '$nodeId'";

                                            $newInsQuery = $newInsQuery . $newQueryFields;
                                            $newInsQuery = $newInsQuery . ") VALUES(";
                                            $newInsQuery = $newInsQuery . $newQueryValues;
                                            $newInsQuery = $newInsQuery . ")";

                                            $insR = mysqli_query($link, $newInsQuery);

                                            if(!$insR)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                //Cancellazione widget da dashboard vecchia
                                                $qDel = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w = '$currentWidgetUniqueId'";
                                                $rDel = mysqli_query($link, $qDel);

                                                if(!$rDel)
                                                {
                                                    return false;
                                                }
                                            }
                                        }

                                        //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                                        /*$q2b = "INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress, actuationResult, actuationResultTime, nrInputId) " .
                                               "VALUES ('$name_w', CURRENT_TIMESTAMP, '$startValue', '$creator', '127.0.0.1', 'Ok', CURRENT_TIMESTAMP, $nrInputId) ";
                                        $r2b = mysqli_query($link, $q2b);*/
                                        return true;
                                    }
                                    else
                                    {
                                        //CASO WIDGET COMBO PER ORA NON SI USA
                                    }
                                }
                            }
                            else
                            {
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                }
            }
            else
            {
                $q0 = "SELECT * FROM Dashboard.Config_dashboard WHERE user = '$username' AND title_header = '$dashboardTitle'";
                $r0 = mysqli_query($link, $q0);

                if($r0)
                {
                    $row = mysqli_fetch_assoc($r0);
                    $id_dashboard = $row['Id'];
                    
                    $q3 = "SELECT * " .
                          "FROM Dashboard.WidgetsIconsMap AS iconsMap " .
                          "LEFT JOIN Dashboard.Widgets AS widgets " .
                          "ON iconsMap.mainWidget = widgets.id_type_widget " . 
                          "WHERE iconsMap.mainWidget = '$newWidgetType' AND iconsMap.targetWidget = ''";
                    
                    $r3 = mysqli_query($link, $q3);

                    if($r3)
                    {
                        $widgetTypeDbRow = mysqli_fetch_assoc($r3);
                        try
                        {
                            $defaultParameters = json_decode($widgetTypeDbRow['defaultParametersMainWidget'], true);
                            $defaultParametersTarget = json_decode($widgetTypeDbRow['defaultParametersTargetWidget'], true);
                        }
                        catch(Exception $e)
                        {

                        }

                        if($widgetTypeDbRow['mono_multi'] == 'Mono')
                        {
                            //Caso widget selezionato di tipo mono
                            if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                            {
                                //Caso widget selezionato di tipo singolo (mancano i series e qualcun altro)

                                $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                //Calcolo del next id
                                $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                if ((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT']))) 
                                {
                                    $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                }

                                //Calcolo del first free row
                                $q2 = "SELECT MAX(n_row + size_rows) AS maxRow FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = $id_dashboard";
                                $r2 = mysqli_query($link, $q2);

                                if($r2)
                                {
                                    $row2 = mysqli_fetch_assoc($r2);
                                    if($row2['maxRow'] == null)
                                    {
                                        $firstFreeRow = 1;
                                    }
                                    else
                                    {
                                        $firstFreeRow = $row2['maxRow'];
                                    }

                                    //Costruzione n_row ed n_column
                                    $n_row = $firstFreeRow;
                                    $n_column = 1;

                                    //Costruzione size_rows e size_columns
                                    $size_rows = $defaultParameters['size_rows'];
                                    $size_columns = $defaultParameters['size_columns'];

                                    //Costruzione nome del widget
                                    $type_w = $widgetType;
                                    $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                    $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                    //Costruzione titolo widget
                                    $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], $defaultParameters['udm'], $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], 'no', null, null);
                                    
                                    $newWidgetDbRow->actuatorTarget = "app";
                                    
                                    $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";

                                    //Query fields
                                    $newQueryFields = "";
                                    //Query values
                                    $newQueryValues = "";
                                    
                                    $count = 0;
                                    foreach($newWidgetDbRow as $key => $value) 
                                    {
                                        if($count == 0)
                                        {
                                            $newQueryFields = $key;
                                            $newQueryValues = returnManagedStringForDb($value);
                                        }
                                        else
                                        {
                                            $newQueryFields = $newQueryFields . ", " . $key;
                                            $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                        }

                                        $count++;
                                    }

                                    $newQueryFields = $newQueryFields . ", appId, flowId, nrMetricType, nodeId";
                                    $newQueryValues = $newQueryValues . ", '$appId', '$flowId', '$metricType', '$nodeId'";

                                    $newInsQuery = $newInsQuery . $newQueryFields;
                                    $newInsQuery = $newInsQuery . ") VALUES(";
                                    $newInsQuery = $newInsQuery . $newQueryValues;
                                    $newInsQuery = $newInsQuery . ")";

                                    $insR = mysqli_query($link, $newInsQuery);

                                    if(!$insR)
                                    {
                                        return false;
                                    }
                                }
                                
                                return true;
                            }
                            else
                            {
                                //CASO WIDGET COMBO PER ORA NON SI USA
                            }
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }
   
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   error_reporting(E_ERROR | E_NOTICE);
    
   if(!$link->set_charset("utf8")) 
   {
      exit();
   } 
   
   $reqBody = json_decode(file_get_contents('php://input'));
   $msgObj = $reqBody->message;
   
   $envFileContent = parse_ini_file("../conf/environment.ini");
    $activeEnv = $envFileContent["environment"]["value"];
   
    $msgType = $msgObj->msgType;
    $response = [];
    $response['msgType'] = $msgObj->msgType;

    switch($msgType)
    {
        case "AddEmitter":
            $name = mysqli_real_escape_string($link, $msgObj->name);
            $valueType = mysqli_real_escape_string($link, $msgObj->valueType);
            $user = mysqli_real_escape_string($link, $msgObj->user);
            $startValue = mysqli_real_escape_string($link, $msgObj->startValue);
            $domainType = mysqli_real_escape_string($link, $msgObj->domainType);
            $minValue = mysqli_real_escape_string($link, $msgObj->minValue);
            $maxValue = mysqli_real_escape_string($link, $msgObj->maxValue);
            $offValue = mysqli_real_escape_string($link, $msgObj->offValue);
            $onValue = mysqli_real_escape_string($link, $msgObj->onValue);
            $endPointPort = mysqli_real_escape_string($link, $msgObj->endPointPort);
            $endPointHost = mysqli_real_escape_string($link, $msgObj->endPointHost);
            $appId = mysqli_real_escape_string($link, $msgObj->appId);
            $flowId = mysqli_real_escape_string($link, $msgObj->flowId);
            $flowName = mysqli_real_escape_string($link, $msgObj->flowName);
            $nodeId = mysqli_real_escape_string($link, $msgObj->nodeId);
            $httpRoot = $msgObj->httpRoot;
            $msgObj->dashboardTitle = urldecode($msgObj->dashboardTitle);
            
            mysqli_autocommit($link, FALSE);
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            
            $q0 = "SELECT * FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.nodeId = '$nodeId' AND NodeRedInputs.user = '$user'";
            $r0 = mysqli_query($link, $q0);
            
            if(mysqli_num_rows($r0) > 0)
            {
                $row0 = mysqli_fetch_assoc($r0);
                $oldNrInputId = $row0['id'];
            }
            else
            {
                $oldNrInputId = null;
            }
            
            $q1 = "DELETE FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.nodeId = '$nodeId' AND NodeRedInputs.user = '$user'";
            $r1 = mysqli_query($link, $q1);

            if($r1)
            {
                $q2 = "INSERT INTO Dashboard.NodeRedInputs(name, valueType, user, startValue, domainType, minValue, NodeRedInputs.maxValue, offValue, onValue, endPointPort, endPointHost, httpRoot, appId, flowId, flowName, nodeId) " .
                      "VALUES ('$name', '$valueType', '$user', '$startValue', '$domainType', " . returnManagedNumberForDb($minValue) . ", " . returnManagedNumberForDb($maxValue) . ", " . returnManagedStringForDb($offValue) . ", " . returnManagedStringForDb($onValue) . ", $endPointPort, '$endPointHost', " . returnManagedStringForDb($httpRoot) .", '$appId', '$flowId', '$flowName', '$nodeId') ";
                
                $r2 = mysqli_query($link, $q2);
                if($r2)
                {
                    $nrInputId = mysqli_insert_id($link);
                    if(property_exists($msgObj, "widgetType"))
                    {
                        $qDash = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$msgObj->dashboardTitle' AND user = '$msgObj->user'";
                        $rDash = mysqli_query($link, $qDash);
                        
                        if($rDash)
                        {
                            if(mysqli_num_rows($rDash) == 0)
                            {
                                $title = $msgObj->dashboardTitle;   
                                $dashboardAuthorName = $msgObj->user;
                                $subtitle = "";  
                                $color = "rgba(51, 204, 255, 1)";  //E' header color
                                $background = "#FFFFFF";  
                                $externalColor = "#FFFFFF";  
                                $nCols = 15;  
                                $headerFontColor = "white";  
                                $headerFontSize = 28;
                                $viewMode = "alwaysResponsive";
                                $addLogo = false;
                                $logoLink = null;
                                $filename = null;
                                $widgetsBorders = "yes";
                                $widgetsBordersColor = "rgba(51, 204, 255, 1)";
                                $visibility = "author";
                                $headerVisible = 1;
                                $embeddable = "yes";
                                $authorizedPagesJson = "[]";
                                $width = ($nCols * 78) + 10;

                                $lastUsedColors = [
                                    "rgba(51, 204, 255, 1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                    "rgba(255,255,255,1)",
                                ];

                                $lastUsedColorsJson = json_encode($lastUsedColors);
                                $org = $_SESSION['loggedOrganization'];

                                $insDashQ = "INSERT INTO Dashboard.Config_dashboard
                                            (Id, name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date, lastUsedColors, organizations) 
                                            VALUES(NULL, '$title', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP, '$lastUsedColorsJson', '$org')";
                                
                                $insDashR = mysqli_query($link, $insDashQ);

                                if($insDashR)
                                {
                                    $newDashId = mysqli_insert_id($link);
                                    
                                    //Salvataggio su API ownership
                                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                                    $callBody = ["elementId" => $newDashId, "elementType" => "DashboardID", "elementName" => $title];

                                    $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $msgObj->accessToken;

                                    $options = array(
                                          'http' => array(
                                                  'header'  => "Content-type: application/json\r\n",
                                                  'method'  => 'POST',
                                                  'timeout' => 30,
                                                  'content' => json_encode($callBody),
                                                  'ignore_errors' => true
                                          )
                                    );

                                    try
                                    {
                                        $context  = stream_context_create($options);
                                        $callResult = file_get_contents($apiUrl, false, $context);

                                        $addWidgetRes = addWidget($link, $msgObj->dashboardTitle, $msgObj->user, $msgObj->widgetType, $name, null, $msgObj->appId, $msgObj->flowId, $msgObj->nodeId, $msgObj->widgetTitle, $startValue, $nrInputId);
                                        if($addWidgetRes)
                                        {
                                            $response['result'] = 'Ok';
                                        }
                                        else
                                        {
                                            mysqli_rollback($link);
                                            $response['result'] = 'Ko';
                                        }
                                    }
                                    catch (Exception $ex) 
                                    {
                                        mysqli_rollback($link);
                                        $response['result'] = 'Ko';
                                    }
                                }
                            }
                            else
                            {
                                $addWidgetRes = addWidget($link, $msgObj->dashboardTitle, $msgObj->user, $msgObj->widgetType, $name, null, $msgObj->appId, $msgObj->flowId, $msgObj->nodeId, $msgObj->widgetTitle, $startValue, $nrInputId);
                                if($addWidgetRes)
                                {
                                    $response['result'] = 'Ok';
                                }
                                else
                                {
                                    mysqli_rollback($link);
                                    $response['result'] = 'Ko';
                                }
                            }
                        }
                        else
                        {
                            $response['result'] = 'Ko';
                        }
                    }
                    else
                    {
                        $response['result'] = 'Ok';
                    }
                    
                    if($oldNrInputId == null)
                    {
                        $q2b = "INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress, actuationResult, actuationResultTime, nrInputId) " .
                               "VALUES ('none', CURRENT_TIMESTAMP, '$startValue', '$user', '127.0.0.1', 'Ok', CURRENT_TIMESTAMP, $nrInputId) ";
                    }
                    else
                    {
                        $q2b = "UPDATE Dashboard.ActuatorsAppsValues SET nrInputId = $nrInputId WHERE nrInputId = $oldNrInputId";
                    }

                    $r2b = mysqli_query($link, $q2b);

                    if($r2b)
                    {
                        $response['result'] = 'Ok';
                        $commitResult = mysqli_commit($link);
                    }
                    else
                    {
                        $rollbackResult = mysqli_rollback($link);
                        $response['result'] = $q2b;
                    }
                }
                else
                {
                    mysqli_rollback($link);
                    $response['result'] = $q2;
                }
            }
            else
            {
                mysqli_rollback($link);
                $response['result'] = $q1;
            }
            mysqli_autocommit($link, TRUE);
            break;
            
        case "DelEmitter":
            $name = mysqli_real_escape_string($link, $msgObj->name);
            $user = mysqli_real_escape_string($link, $msgObj->user);
            
            
            mysqli_autocommit($link, FALSE);
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            $q1 = "DELETE FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.name = '$name' AND NodeRedInputs.user = '$user'";
            $r1 = mysqli_query($link, $q1);
            if($r1)
            {
                $q2 = "DELETE FROM Dashboard.ActuatorsAppsValues WHERE ActuatorsAppsValues.widgetName LIKE '" . $name . "_%" . "' AND ActuatorsAppsValues.username = '$user'";
                $r2 = mysqli_query($link, $q2);
                
                if($r2)
                {
                    $q3 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Config_widget_dashboard.id_metric = '$name' AND Config_widget_dashboard.creator = '$user'"; 
                    $r3 = mysqli_query($link, $q3);

                    if($r3)
                    {
                        mysqli_commit($link);
                        $response['result'] = 'Ok';
                    }
                    else 
                    {
                        mysqli_rollback($link);
                        $response['result'] = 'Ko';
                    }
                }
                else
                {
                    mysqli_rollback($link);
                    $response['result'] = 'Ko';
                }
            }
            else
            {
                mysqli_rollback($link);
                $response['result'] = 'Ko';
            }
            mysqli_autocommit($link, TRUE);
            break;
            
        default:
            break;

    }

    mysqli_close($link);
    echo json_encode($response);
   
   
   
   
   