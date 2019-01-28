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
    include 'WidgetDbRow.php'; 
    include './widgetsFactoryClasses/aGenericWidgetFactory.php';
    include './comboFactoryClasses/aGenericComboFactory.php';
    
    foreach (glob("./widgetsFactoryClasses/*.php") as $filename)
    {
        if(strpos($filename, 'aGenericWidgetFactory.php') == false) 
        {
            include $filename;
        }
    }
    
    foreach (glob("./comboFactoryClasses/*.php") as $filename)
    {
        if(strpos($filename, 'aGenericComboFactory.php') == false) 
        {
            include $filename;
        }
    }
    
    require '../sso/autoload.php';
    use Jumbojett\OpenIDConnectClient;
    
    error_reporting(E_ALL);
    date_default_timezone_set('Europe/Rome');
    
    $iotDirPayloadGlobal = null;
    
    function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

          // 32 bits for "time_low"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),

          // 16 bits for "time_mid"
          mt_rand(0, 0xffff),

          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 4
          mt_rand(0, 0x0fff) | 0x4000,

          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          mt_rand(0, 0x3fff) | 0x8000,

          // 48 bits for "node"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
      }
    
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

    session_start(); 
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
    $defaultColors2 = ["#fff5cc", "#ffe0b3", "#ffcccc", "#99ffff", "#99e6ff", "#adebad", "#80ff80"];
    
    $response = [];
    $dashboardTitle = $_REQUEST['dashboardTitle'];
    if(isset($_REQUEST['dashboardAuthorName']))
    {
        $dashboardAuthorName = $_REQUEST['dashboardAuthorName'];
    }
    
    if(isset($_REQUEST['dashboardEditorName']))
    {
        $dashboardEditor = $_REQUEST['dashboardEditorName'];
        $creator = $_REQUEST['dashboardEditorName'];
    }
    
    $operation = $_REQUEST['operation'];
    $crashdedQuery = null;
    $crashdedQuery2 = null;
    
    function addWidgets($link, $serviceMapUrlPrefix, $addDashboard)
    {
        $genFileContent = parse_ini_file("../conf/environment.ini");
        $orionContent = parse_ini_file("../conf/orion.ini");
        $orionBaseUrlLocal = $orionContent["orionBaseUrl"][$genFileContent['environment']['value']];

        $ssoContent = parse_ini_file("../conf/sso.ini");
        $ssoEndpoint = $ssoContent["ssoEndpoint"][$genFileContent['environment']['value']];
        $ssoTokenEndpoint = $ssoContent["ssoTokenEndpoint"][$genFileContent['environment']['value']];
        $ssoClientId = $ssoContent["ssoClientId"][$genFileContent['environment']['value']];
        $ssoClientSecret = $ssoContent["ssoClientSecret"][$genFileContent['environment']['value']];

        
        $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        $defaultColors2 = ["#fff5cc", "#ffe0b3", "#ffcccc", "#99ffff", "#99e6ff", "#adebad", "#80ff80"];
        
        $id_dashboard = $_REQUEST['dashboardId'];
        $dashboardTitle = $_REQUEST['dashboardTitle'];
        $dashboardAuthorName = $_REQUEST['dashboardAuthorName'];
        $dashboardEditor = $_REQUEST['dashboardEditorName'];
        $creator = $_REQUEST['dashboardEditorName'];
        $selection = $_REQUEST['selection'];
        $mapCenterLat = $_REQUEST['mapCenterLat'];
        $mapCenterLng = $_REQUEST['mapCenterLng'];
        $mapZoom = $_REQUEST['mapZoom'];
        
        $widgetTypeDbRow = NULL;
        $id_metric = NULL; 
        $widgetWizardSelectedRows = $_REQUEST['widgetWizardSelectedRows'];
        $newWidgetType = $_REQUEST['widgetType']; 
        $actuatorTargetWizard = $_REQUEST['actuatorTargetWizard']; 
        $actuatorTargetInstance = $_REQUEST['actuatorTargetInstance'];
        $actuatorEntityName = $_REQUEST['actuatorEntityName'];
        $actuatorValueType = $_REQUEST['actuatorValueType']; 
        $actuatorMinBaseValue = $_REQUEST['actuatorMinBaseValue']; 
        $actuatorMaxImpulseValue = $_REQUEST['actuatorMaxImpulseValue']; 
        $title_w = NULL;
        $n_row = NULL;
        $n_column = NULL;
        $color_widget = NULL; 
        $freq_widget = NULL;
        $size_rows = NULL; 
        $size_columns = NULL; 
        $controlsPosition = NULL;
        $int_temp_widget = NULL;
        $comune_widget = NULL;
        $message_widget = NULL;
        $url_widget = "none";
        $showTitle = NULL;
        $controlsVisibility = NULL;
        $zoomFactor = NULL;
        $scaleX = NULL;
        $scaleY = NULL;
        $inputUdmWidget = NULL;
        $inputUdmPosition = NULL;
        $serviceUri = NULL;
        $viewMode = NULL;
        $hospitalList = NULL;
        $creationDate = NULL;
        $actuatorTarget = NULL;
        $defaultTab = NULL;
        $zoomControlsColor = NULL;
        $headerFontColor = NULL;
        $styleParameters = NULL;
        $showTableFirstCell = NULL;
        $tableFirstCellFontSize = NULL;
        $tableFirstCellFontColor = NULL;
        $rowsLabelsFontSize = NULL;
        $rowsLabelsFontColor = NULL;
        $colsLabelsFontSize = NULL;
        $colsLabelsFontColor = NULL;
        $rowsLabelsBckColor = NULL;
        $colsLabelsBckColor = NULL;
        $tableBorders = NULL;
        $tableBordersColor = NULL;
        $infoJsonObject = NULL;
        $infoJson = NULL;
        $legendFontSize = NULL;
        $legendFontColor = NULL;
        $dataLabelsFontSize = NULL;
        $dataLabelsFontColor = NULL;
        $barsColorsSelect = NULL;
        $barsColors = NULL;
        $chartType = NULL;
        $dataLabelsDistance = NULL;
        $dataLabelsDistance1 = NULL;
        $dataLabelsDistance2 = NULL;
        $dataLabels = NULL;
        $dataLabelsRotation = NULL;
        $xAxisDataset = NULL;
        $lineWidth = NULL;
        $alrLook = NULL;
        $colorsSelect = NULL;
        $colors = NULL;
        $colorsSelect1 = NULL;
        $colors1 = NULL;
        $innerRadius1 = NULL;
        $outerRadius1 = NULL;
        $innerRadius2 = NULL;
        $startAngle = NULL;
        $endAngle = NULL;
        $centerY = NULL;
        $gridLinesWidth = NULL;
        $gridLinesColor = NULL;
        $linesWidth = NULL;
        $alrThrLinesWidth = NULL;
        $clockData = NULL;
        $clockFont = NULL;
        $rectDim = NULL;
        $enableFullscreenTab = 'no';
        $enableFullscreenModal = 'no'; 
        $fontFamily = "";
        $newOrionEntityJson = NULL;
        $attributeName = NULL;
        $udm = NULL;
        $udmPosition = NULL;
        $nextId = 1;
        $firstFreeRow = NULL;
        $parameters = [];
        $newWidgetDbRowTarget = [];
        $sourceWidgetName = NULL;
        $sourceWidgetRow = NULL;
        $sourceEntityJson = NULL;
        $selectedRowIds = [];
        
            if($newWidgetType == NULL)
            {
                //Caso tipo di widget non selezionato: dashboards fully custom vuote, ritorniamo true se add dashboard ma false negli altri casi
                if($addDashboard)
                {
                    return true;
                }
                else
                {
                    echo "Ko";
                }
            }
            else
            {
                //Caso tipo di widget selezionato
                $q3 = "SELECT * " .
                      "FROM Dashboard.WidgetsIconsMap AS iconsMap " .
                      "LEFT JOIN Dashboard.Widgets AS widgets " .
                      "ON iconsMap.mainWidget = widgets.id_type_widget " . 
                      "WHERE iconsMap.icon = '$newWidgetType'";

                $r3 = mysqli_query($link, $q3);

                if($r3)
                {
                    $widgetTypeDbRow = mysqli_fetch_assoc($r3);
                    $widgetCategory = $widgetTypeDbRow['widgetCategory'];
                    
                    try
                    {
                        $defaultParameters = json_decode($widgetTypeDbRow['defaultParametersMainWidget'], true);
                        $defaultParametersTarget = json_decode($widgetTypeDbRow['defaultParametersTargetWidget'], true);
                    }
                    catch(Exception $e)
                    {

                    }
                    
                    //Ramo mono
                    if($widgetTypeDbRow['mono_multi'] == 'Mono')
                    {
                        if(($widgetCategory == 'actuator')&&($actuatorTargetInstance == 'new'))
                        {
                            if($actuatorTargetWizard == 'broker')
                            {
                                //Istanziamento nuovo widget con nuova entità su broker
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
                                    $id_metric = null;

                                    $type_w = $widgetTypeDbRow['mainWidget'];
                                    $name_w = preg_replace('/\+/', '', $actuatorEntityName) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                    $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                    //Costruzione titolo widget
                                    $title_w = $actuatorEntityName . " - " . $actuatorValueType;

                                    $creator = $_SESSION['loggedUsername'];
                                    $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], '{}', $newWidgetType);
                                    
                                    //Preparazione newOrionEntityJson
                                    $newOrionEntity = [];
                                    $newOrionEntity['id'] = $name_w;
                                    $newOrionEntity['type'] = $actuatorEntityName;
                                    $newOrionEntity['entityDesc'] = ["type" => "String", "value" => $actuatorEntityName];
                                    $newOrionEntity['entityCreator'] = ["value" => $creator, "type" => "String"];
                                    $newOrionEntity['creationDate'] = ["value" => $creationDate, "type" => "String"];
                                    $newOrionEntity['actuatorDeleted'] = ["value" => false, "type" => "Boolean"];
                                    $newOrionEntity['actuatorDeletionDate'] = ["value" => NULL, "type" => "String"];
                                    $newOrionEntity['actuatorCanceller'] = ["value" => NULL, "type" => "String"];
                                    
                                    //Per ora cablato, poi introdurremo una soluzione più elegante
                                    switch($type_w)
                                    {
                                        case "widgetImpulseButton": 
                                            $entityAttrType = "string";
                                            $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                            $widgetParameters->baseValue = $actuatorMinBaseValue;
                                            $widgetParameters->impulseValue = $actuatorMaxImpulseValue;
                                            $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                            break;
                                        
                                        case "widgetOnOffButton":
                                            $entityAttrType = "string";
                                            $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                            $widgetParameters->offValue = $actuatorMinBaseValue;
                                            $widgetParameters->onValue = $actuatorMaxImpulseValue;
                                            $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                            break;
                                            
                                        case "widgetKnob": 
                                            $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                            $widgetParameters->domainType = "continuous";
                                            $widgetParameters->minValue = $actuatorMinBaseValue;
                                            $widgetParameters->maxValue = $actuatorMaxImpulseValue;
                                            $widgetParameters->continuousRanges = null;
                                            $widgetParameters->dataPrecision = 2;
                                            $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                            $entityAttrType = "float";
                                            break;
                                        
                                        case "widgetNumericKeyboard":
                                            $entityAttrType = "float";
                                            break;
                                    }
                                    
                                    $newOrionEntity[$actuatorValueType] = ["type" => $entityAttrType, "value" => $actuatorMinBaseValue, "metadata" => ["attrDesc" => ["value" => $actuatorValueType, "type" => "String"]]];
                                    $newOrionEntityJson = json_encode($newOrionEntity);
                                    
                                    $newWidgetDbRow->actuatorTarget = $actuatorTargetWizard;
                                    $newWidgetDbRow->attributeName = $actuatorValueType;
                                    $newWidgetDbRow->sm_based = 'no';
                                    $newWidgetDbRow->entityJson = $newOrionEntityJson;
                                    
                                    if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                    {
                                        $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                        $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom, null);
                                        $newWidgetDbRow = $widgetFactory->completeWidget();
                                    }

                                    $newWidgetDbRow->title_w = htmlentities($newWidgetDbRow->title_w, ENT_QUOTES|ENT_HTML5);
                                    
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

                                    $newInsQuery = $newInsQuery . $newQueryFields;
                                    $newInsQuery = $newInsQuery . ") VALUES(";
                                    $newInsQuery = $newInsQuery . $newQueryValues;
                                    $newInsQuery = $newInsQuery . ")";

                                    //$file = fopen("C:\dashboardLog.txt", "w");
                                    //fwrite($file, "newInsQuery: " . $newInsQuery . "\n");

                                    $insR = mysqli_query($link, $newInsQuery);

                                    if($insR)
                                    {
                                        //Inserimento nuova entità su broker + inserimento nuovo valore su tabella DB nostro
                                        /*$orionAddEntityUrl = $orionBaseUrlLocal. "/v2/entities";

                                        $orionCallOptions = array(
                                                'http' => array(
                                                        'header'  => "Content-type: application/json\r\n",
                                                        'method'  => 'POST',
                                                        'content' => $newOrionEntityJson,
                                                        'timeout' => 30
                                                )
                                        );*/
                                        
                                        //Nuova versione che scrive su IOT directory e non più direttamente su broker
                                        
                                        $attributes = [
                                            ["value_name" => $actuatorValueType,  "data_type" => $entityAttrType, "value_type" => $actuatorValueType, "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "actuatorCanceller",  "data_type" => "string", "value_type" => "actuator_canceller", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "actuatorDeleted",  "data_type" => "boolean", "value_type" => "actuator_deleted", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "actuatorDeletionDate",  "data_type" => "boolean", "value_type" => "actuator_deletion_date", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "creationDate",  "data_type" => "string", "value_type" => "creation_date", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "entityCreator",  "data_type" => "string", "value_type" => "entity_creator", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                            ["value_name" => "entityDesc",  "data_type" => "string", "value_type" => "entity_desc", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000]
                                        ];

                                        $id = $name_w; //Id del widget
                                        $type = $actuatorEntityName; //$actuatorEntityName
                                        $kind = "actuator"; //Per ora fisso
                                        $contextBroker = "orionUNIFI"; //Per ora fisso
                                        $protocol = "ngsi"; //Per ora fisso
                                        $format = "json"; //Per ora fisso
                                        
                                        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                                        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                                        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                                        $accessToken = $tkn->access_token;
                                        $_SESSION['refreshToken'] = $tkn->refresh_token;
                                        $k1 = generateUUID();
                                        $k2 = generateUUID();

                                        $iotDirApiUrl = "https://iotdirectory.snap4city.org/api/device.php?action=insert&attributes=" . urlencode(json_encode($attributes)) . "&id=" . $id . "&type=" . $type . "&kind=" . $kind . "&contextbroker=" . $contextBroker . "&protocol=" . $protocol . "&format=" . $format . "&mac=&model=&producer=&latitude=43&longitude=11&visibility=private&frequency=1&nodered=yes&k1=" . $k1 . "&k2=" .$k2 . "&token=" . $accessToken . "&username=" . $_SESSION['loggedUsername'] . "&organization=" . $_SESSION['loggedOrganization'];
                                        
                                        try
                                        {
                                          $iotDirPayload = file_get_contents($iotDirApiUrl);

                                           //if(strpos($http_response_header[0], '201 Created') === false)
                                           if(strpos($http_response_header[0], '200') === false)
                                           {            
                                                $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                                $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                                mysqli_close($link);
                                                
                                                if($addDashboard)
                                                {
                                                    return false;
                                                }
                                                else
                                                {
                                                    echo "Ko";
                                                    exit();
                                                }
                                           }
                                           else
                                           {
                                                //Inserimento primo record di attuazione
                                                $entityId = $name_w;
                                                $actionTime = date('Y-m-d H:i:s');
                                                $value = $actuatorMinBaseValue;
                                                $username = $creator;
                                                $ipAddress = $_SERVER['REMOTE_ADDR'];

                                                $firstValueQuery = "INSERT INTO Dashboard.ActuatorsEntitiesValues(entityId, actionTime, value, username, ipAddress, actuationResult, actuationResultTime) " .
                                                                   "VALUES('$entityId', '$actionTime', '$value', '$username', '$ipAddress', 'Ok', '$actionTime')";

                                                $queryResult = mysqli_query($link, $firstValueQuery);
                                                mysqli_close($link);
                                                
                                                if($addDashboard)
                                                {
                                                    return true;
                                                }
                                                else
                                                {
                                                    echo "Ok";
                                                }
                                           }
                                        }
                                        catch (Exception $ex) 
                                        {
                                            $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
                                            mysqli_select_db($link, $dbname);            
                                            $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                            $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                            mysqli_close($link);
                                            
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }
                                }  
                                else
                                {
                                    if($addDashboard)
                                    {
                                        return false;
                                    }
                                    else
                                    {
                                        echo "Ko";
                                    }
                                }
                            }
                            else
                            {
                                //TBD - Istanziamento nuovo widget con nuovo blocchetto su NodeRED (non si farà)
                            }
                        }
                        else
                        {   // CASO NON ACTUATOR cioè DATA-VIEWER
                            //Caso widget selezionato di tipo mono con righe selezionate (differenza fra attuatori new e attuatori non new e viewer)
                            //Ramo per i mono SENZA target widget
                            if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                            {
                                //Ne creiamo uno per ogni riga selezionata
                                foreach($widgetWizardSelectedRows as $selectedRowKey => $selectedRow) 
                                { 
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
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator')&&($selectedRow['nature'] == 'From Dashboard to IOT Device'))
                                        {
                                            $id_metric = null;
                                            $sourceWidgetName = $selectedRow['unique_name_id'];
                                            $entityQ = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w = '$sourceWidgetName'";
                                            $entityR = mysqli_query($link, $entityQ);
                                            
                                            if($entityR)
                                            {
                                                if(mysqli_num_rows($entityR) > 0)
                                                {
                                                    $sourceWidgetRow = mysqli_fetch_assoc($entityR);
                                                    $sourceEntity = json_decode($sourceWidgetRow['entityJson']);
                                                    $sourceEntityJson = $sourceWidgetRow['entityJson'];
                                                    $actuatorEntityName = $sourceEntity->type;
                                                }
                                                else
                                                {
                                                    //Reperiamo l'entità dal broker
                                                    $orionGetEntityUrl = $orionBaseUrlLocal. "/v2/entities/" . $sourceWidgetName;
                                                    
                                                    try
                                                    {
                                                       $callResponse = file_get_contents($orionGetEntityUrl);
                                                       
                                                       if(strpos($http_response_header[0], '200') === false)
                                                       {      
                                                            if($addDashboard)
                                                            {
                                                                return false;
                                                            }
                                                            else
                                                            {
                                                                echo "Ko";
                                                            }
                                                       }
                                                       else
                                                       {
                                                            $sourceEntityJson = $callResponse;
                                                            $sourceEntity = json_decode($callResponse);
                                                            $actuatorEntityName = $sourceEntity->type;
                                                       }
                                                    }
                                                    catch(Exception $e)
                                                    {
                                                        if($addDashboard)
                                                        {
                                                            return false;
                                                        }
                                                        else
                                                        {
                                                            echo "Ko";
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                //Reperiamo l'entità dal broker
                                                $orionGetEntityUrl = $orionBaseUrlLocal. "/v2/entities/" . $sourceWidgetName;

                                                try
                                                {
                                                   $callResponse = file_get_contents($orionGetEntityUrl);

                                                   if(strpos($http_response_header[0], '200') === false)
                                                   {      
                                                        if($addDashboard)
                                                        {
                                                            return false;
                                                        }
                                                        else
                                                        {
                                                            echo "Ko";
                                                        }
                                                   }
                                                   else
                                                   {
                                                        $sourceEntityJson = $callResponse;
                                                        $sourceEntity = json_decode($callResponse);
                                                        $actuatorEntityName = $sourceEntity->type;
                                                   }
                                                }
                                                catch(Exception $e)
                                                {
                                                    if($addDashboard)
                                                    {
                                                        return false;
                                                    }
                                                    else
                                                    {
                                                        echo "Ko";
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Dashboard-IOT App'))
                                            {
                                                $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['parameters']));
                                            }
                                            else
                                            {
                                                if(isset($selectedRow['unique_name_id']))
                                                {
                                                    if($selectedRow['high_level_type'] == 'Dashboard-IOT App')
                                                    {
                                                        $id_metric = $selectedRow['parameters'];
                                                    }
                                                    else
                                                    {
                                                        $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                                    }
                                                }
                                                else
                                                {
                                                    $id_metric = "ToBeReplacedByFactory";
                                                }
                                            }
                                        }
                                        
                                        $type_w = $widgetTypeDbRow['mainWidget'];
                                        
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                        {
                                            $name_w = preg_replace('/\+/', '', $actuatorEntityName) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);
                                        }
                                        else
                                        {
                                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);
                                        }
                                        
                                        //Costruzione titolo widget
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                        {
                                            $title_w = $actuatorEntityName . " - " . $selectedRow['low_level_type'];
                                        }
                                        else
                                        {
                                            if($selectedRow['unique_name_id'] != null)
                                            {
                                                $title_w = $selectedRow['sub_nature'] . " - " . $selectedRow['unique_name_id'];
                                            }
                                            else
                                            {
                                                $title_w = $selectedRow['sub_nature'];
                                            }
                                        }
                                        
                                        $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);
                                        
                                        $creator = $_SESSION['loggedUsername'];
                                        $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], json_encode([$selectedRowKey => $selectedRow]), $newWidgetType);

                                        if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                        {
                                            $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                            $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom);
                                            $newWidgetDbRow = $widgetFactory->completeWidget();
                                        }
                                        
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                        {
                                            $newWidgetDbRow->entityJson = $sourceEntityJson;
                                            
                                            $sourceEntityDecoded = json_decode($sourceEntityJson);
                                                    
                                            foreach($sourceEntityDecoded as $key => $val) 
                                            {
                                                if(($key != 'actuatorCanceller')&&($key != 'actuatorDeleted')&&($key != 'actuatorDeletionDate')&&($key != 'creationDate')&&($key != 'entityCreator')&&($key != 'entityDesc')&&($key != 'id')&&($key != 'type'))
                                                {
                                                    $newWidgetDbRow->attributeName = $key;
                                                    break;
                                                }
                                            }
                                            
                                            $newWidgetDbRow->actuatorTarget = 'broker';
                                        }
                                        
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Dashboard-IOT App'))
                                        {
                                            $newWidgetDbRow->actuatorTarget = 'app';
                                        }
                                        
                                        if($widgetCategory == 'actuator')
                                        {
                                            if($newWidgetDbRow->type_w == $sourceWidgetRow['type_w'])
                                            {
                                                $newWidgetDbRow->parameters = $sourceWidgetRow['parameters'];
                                            }
                                            else
                                            {
                                                $sourceParams = json_decode($sourceWidgetRow['parameters']);
                                                $destParams = json_decode($newWidgetDbRow->parameters);
                                                switch($sourceWidgetRow['type_w'])
                                                {
                                                    case "widgetImpulseButton":
                                                        switch($newWidgetDbRow->type_w)
                                                        {
                                                            case "widgetOnOffButton":
                                                                $destParams->offValue = $sourceParams->baseValue;
                                                                $destParams->onValue = $sourceParams->impulseValue;
                                                                break;
                                                            
                                                            case "widgetKnob":
                                                                $destParams->minValue = $sourceParams->baseValue;
                                                                $destParams->maxValue = $sourceParams->impulseValue;
                                                                break;
                                                            
                                                            case "widgetNumericKeyboard":
                                                                break;
                                                        }
                                                        $newWidgetDbRow->parameters = json_encode($destParams);
                                                        break;
                                                    
                                                    case "widgetOnOffButton":
                                                        switch($newWidgetDbRow->type_w)
                                                        {
                                                            case "widgetImpulseButton":
                                                                $destParams->baseValue = $sourceParams->offValue;
                                                                $destParams->impulseValue = $sourceParams->onValue;
                                                                break;
                                                            
                                                            case "widgetKnob":
                                                                $destParams->minValue = $sourceParams->offValue;
                                                                $destParams->maxValue = $sourceParams->onValue;
                                                                break;
                                                            
                                                            case "widgetNumericKeyboard":
                                                                break;
                                                        }
                                                        $newWidgetDbRow->parameters = json_encode($destParams);
                                                        break;
                                                    
                                                    case "widgetKnob":
                                                        switch($newWidgetDbRow->type_w)
                                                        {
                                                            case "widgetImpulseButton":
                                                                $destParams->baseValue = $sourceParams->minValue;
                                                                $destParams->impulseValue = $sourceParams->maxValue;
                                                                break;
                                                            
                                                            case "widgetOnOffButton":
                                                                $destParams->offValue = $sourceParams->minValue;
                                                                $destParams->onValue = $sourceParams->maxValue;
                                                                break;
                                                            
                                                            case "widgetNumericKeyboard":
                                                                break;
                                                        }
                                                        $newWidgetDbRow->parameters = json_encode($destParams);
                                                        break;
                                                    
                                                    case "widgetNumericKeyboard":
                                                        break;
                                                }
                                            }
                                        }
                                        
                                        if($selectedRow['high_level_type'] == 'My Personal Data')
                                        {
                                            $newWidgetDbRow->sm_based = 'myPersonalData';
                                            $newWidgetDbRow->sm_field = $selectedRow['unique_name_id'];
                                            
                                            /*if($type_w == 'widgetTracker')
                                            {
                                                $newWidgetDbRow->rowParameters = $selectedRow['low_level_type'];
                                            }*/
                                        }
                                        
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
                                                if ($key == "name_w") {
                                                    $newQueryValues = preg_replace('/\s+/', '_', $newQueryValues);
                                                }
                                            }
                                            else
                                            {
                                                $newQueryFields = $newQueryFields . ", " . $key;
                                                if ($key == "title_w") {
                                                    $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(html_entity_decode($value, ENT_HTML5));
                                                } else {
                                                    $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                }
                                            }

                                            $count++;
                                        }

                                        $newInsQuery = $newInsQuery . $newQueryFields;
                                        $newInsQuery = $newInsQuery . ") VALUES(";
                                        $newInsQuery = $newInsQuery . $newQueryValues;
                                        $newInsQuery = $newInsQuery . ")";

                                        $insR = mysqli_query($link, $newInsQuery);

                                        if(!$insR)
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                                exit();
                                            }
                                        }
                                    }
                                }

                                //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                                if($addDashboard)
                                {
                                    return true;
                                }
                                else
                                {
                                    echo "Ok";
                                }
                            }
                            else
                            {
                                //Caso widget selezionato di tipo mono, però con target widgets (main + targets)

                                //Split dei target widgets
                                $targetWidgets = explode(",", $widgetTypeDbRow['targetWidget']); 
                                $hasTargetWidgetFactory = json_decode($widgetTypeDbRow['hasTargetWidgetFactory']);

                                foreach($widgetWizardSelectedRows as $selectedRowKey => $selectedRow) 
                                {
                                    //Costruzione del main widget
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
                                        if(isset($selectedRow['unique_name_id']))
                                        {
                                            $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                        }
                                        else
                                        {
                                            $id_metric = "ToBeReplacedByFactory";
                                        }

                                        $type_w = $widgetTypeDbRow['mainWidget'];
                                        $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                        $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                        //Costruzione titolo widget
                                        if($selectedRow['unique_name_id'] != null)
                                        {
                                            $title_w = $selectedRow['sub_nature'] . " - " . $selectedRow['unique_name_id'];
                                        }
                                        else
                                        {
                                            $title_w = $selectedRow['sub_nature'];
                                        }
                                        
                                        $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                        $creator = $_SESSION['loggedUsername'];
                                        $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], json_encode([$selectedRowKey => $selectedRow]), $newWidgetType);

                                        if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                        {
                                            $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                            $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom);
                                            $newWidgetDbRow = $widgetFactory->completeWidget();
                                        }

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

                                        $newInsQuery = $newInsQuery . $newQueryFields;
                                        $newInsQuery = $newInsQuery . ") VALUES(";
                                        $newInsQuery = $newInsQuery . $newQueryValues;
                                        $newInsQuery = $newInsQuery . ")";

                                        $insR = mysqli_query($link, $newInsQuery);

                                        if(!$insR)
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }

                                        //Costruzione dei target widgets
                                        for($i = 0; $i < count($targetWidgets); $i++)
                                        {
                                            $targetWidgets[$i] = trim($targetWidgets[$i]);

                                            //Reperimento dati del target widget i-esimo
                                            $qTw = "SELECT * FROM Dashboard.Widgets WHERE id_type_widget = '$targetWidgets[$i]'";

                                            $rTw = mysqli_query($link, $qTw);

                                             if($rTw)
                                             {
                                                $twDbRow = mysqli_fetch_assoc($rTw);

                                                //Istanziamento del target widget i-esimo
                                                $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                                $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                                //Calcolo del next id
                                                $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                                if((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT']))) 
                                                {
                                                    $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                                }

                                                //Costruzione n_row ed n_column: mettiamo il target widget accanto al main widget
                                                $n_row = $firstFreeRow;
                                                $n_column = $n_column + $size_columns;

                                                //Costruzione size_rows e size_columns
                                                $size_rows = $defaultParametersTarget[$i]['size_rows'];
                                                $size_columns = $defaultParametersTarget[$i]['size_columns'];

                                                //Costruzione nome del widget
                                                if($targetWidgets[$i] == 'widgetTimeTrend')
                                                {
                                                    $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                                }
                                                else
                                                {
                                                    $id_metric = "ToBeReplacedByFactory";
                                                }

                                                $type_w = $targetWidgets[$i];
                                                $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                                $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                                //Costruzione titolo widget
                                                $title_w = $selectedRow['sub_nature'] . " - Target";
                                                
                                                $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                                $creator = $_SESSION['loggedUsername'];
                                                $newWidgetDbRowTarget[$i] = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParametersTarget[$i]['color_w'], $defaultParametersTarget[$i]['frequency_w'], $defaultParametersTarget[$i]['temporal_range_w'], $defaultParametersTarget[$i]['municipality_w'], $defaultParametersTarget[$i]['infoMessage_w'], $defaultParametersTarget[$i]['link_w'], $defaultParametersTarget[$i]['parameters'], $defaultParametersTarget[$i]['frame_color_w'], $defaultParametersTarget[$i]['udm'], $defaultParametersTarget[$i]['udmPos'], $defaultParametersTarget[$i]['fontSize'], $defaultParametersTarget[$i]['fontColor'], $defaultParametersTarget[$i]['controlsPosition'], $defaultParametersTarget[$i]['showTitle'], $defaultParametersTarget[$i]['controlsVisibility'], $defaultParametersTarget[$i]['zoomFactor'], $defaultParametersTarget[$i]['defaultTab'], $defaultParametersTarget[$i]['zoomControlsColor'], $defaultParametersTarget[$i]['scaleX'], $defaultParametersTarget[$i]['scaleY'], $defaultParametersTarget[$i]['headerFontColor'], $defaultParametersTarget[$i]['styleParameters'], $defaultParametersTarget[$i]['infoJson'], $defaultParametersTarget[$i]['serviceUri'], $defaultParametersTarget[$i]['viewMode'], $defaultParametersTarget[$i]['hospitalList'], $defaultParametersTarget[$i]['notificatorRegistered'], $defaultParametersTarget[$i]['notificatorEnabled'], $defaultParametersTarget[$i]['enableFullscreenTab'], $defaultParametersTarget[$i]['enableFullscreenModal'], $defaultParametersTarget[$i]['fontFamily'], $defaultParametersTarget[$i]['entityJson'], $defaultParametersTarget[$i]['attributeName'], $creator, null, $defaultParametersTarget[$i]['canceller'], $defaultParametersTarget[$i]['lastEditDate'], $defaultParametersTarget[$i]['cancelDate'], $defaultParametersTarget[$i]['actuatorTarget'], $defaultParametersTarget[$i]['actuatorEntity'], $defaultParametersTarget[$i]['actuatorAttribute'], $defaultParametersTarget[$i]['chartColor'], $defaultParametersTarget[$i]['dataLabelsFontSize'], $defaultParametersTarget[$i]['dataLabelsFontColor'], $defaultParametersTarget[$i]['chartLabelsFontSize'], $defaultParametersTarget[$i]['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], '[]');

                                                if($hasTargetWidgetFactory[$i] == 'yes')
                                                {
                                                    $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                                    $widgetFactory = new $widgetFactoryClass($newWidgetDbRowTarget[$i], $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "target", null, $selection, $mapZoom);
                                                    $newWidgetDbRowTarget[$i] = $widgetFactory->completeWidget();
                                                }

                                                $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";

                                                //Query fields
                                                $newQueryFields = "";
                                                //Query values
                                                $newQueryValues = "";

                                                $count = 0;
                                                foreach($newWidgetDbRowTarget[$i] as $key => $value) 
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

                                                $newInsQuery = $newInsQuery . $newQueryFields;
                                                $newInsQuery = $newInsQuery . ") VALUES(";
                                                $newInsQuery = $newInsQuery . $newQueryValues;
                                                $newInsQuery = $newInsQuery . ")";

                                                $insR = mysqli_query($link, $newInsQuery);

                                                if(!$insR)
                                                {
                                                    if($addDashboard)
                                                    {
                                                        return false;
                                                    }
                                                    else
                                                    {
                                                        echo "Ko";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                if($addDashboard)
                                                {
                                                    return false;
                                                }
                                                else
                                                {
                                                    echo "Ko";
                                                }
                                            }
                                        }
                                    }

                                    //Costruzione dell'eventuale combo
                                    if($widgetTypeDbRow['comboName'] != null)
                                    {
                                        $comboFactoryClass = $widgetTypeDbRow['comboName'];
                                        $comboFactory = new $comboFactoryClass($newWidgetDbRow, $newWidgetDbRowTarget);
                                        if(!$comboFactory->finalizeCombo())
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }

                                }//Fine foreach sulla i-esima riga selezionata

                                //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                                if($addDashboard)
                                {
                                    return true;
                                }
                                else
                                {
                                    echo "Ok";
                                }
                            }
                        }
                    }
                    else
                    {
                        //Caso widget selezionato di tipo multi
                        if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                        {
                            //Caso widget multi ma senza target widget (pronto soccorso multi ospedale, series, service map stand alone)
                            //Costruzione del main widget
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
                                if(isset($selectedRow['unique_name_id']))
                                {
                                    $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                }
                                else
                                {
                                    $id_metric = "ToBeReplacedByFactory";
                                }

                                $type_w = $widgetTypeDbRow['mainWidget'];
                                $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                //Costruzione titolo widget
                                $title_w = "External content";
                                $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                $creator = $_SESSION['loggedUsername'];
                                
                                $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], no, null, null, json_encode($widgetWizardSelectedRows), $newWidgetType);

                                if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                {
                                    $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                    $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", $widgetWizardSelectedRows, $selection, $mapZoom);
                                    $newWidgetDbRow = $widgetFactory->completeWidget();
                                }

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

                                $newInsQuery = $newInsQuery . $newQueryFields;
                                $newInsQuery = $newInsQuery . ") VALUES(";
                                $newInsQuery = $newInsQuery . $newQueryValues;
                                $newInsQuery = $newInsQuery . ")";

                                $insR = mysqli_query($link, $newInsQuery);
                                
                                if(!$insR)
                                {
                                    if($addDashboard)
                                    {
                                        return false;
                                    }
                                    else
                                    {
                                        echo "Ko";
                                    }
                                }
                                else
                                {
                                    if($addDashboard)
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        echo "Ok";
                                    }
                                }
                            }
                        }
                        else
                        {
                            //Caso widget multi con target widgets (selector + map, selector + map + trend)
                            
                            //Split dei target widgets
                            $targetWidgets = explode(",", $widgetTypeDbRow['targetWidget']); 
                            $hasTargetWidgetFactory = json_decode($widgetTypeDbRow['hasTargetWidgetFactory']);
                            
                            //Costruzione del main widget
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
                                if(isset($selectedRow['unique_name_id']))
                                {
                                    $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                }
                                else
                                {
                                    //$id_metric = "ToBeReplacedByFactory";
                                    $id_metric = "Selector";
                                }

                                $type_w = $widgetTypeDbRow['mainWidget'];
                                $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                $creator = $_SESSION['loggedUsername'];
                                
                                $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], 'no', null, null, json_encode($widgetWizardSelectedRows), $newWidgetType);
                                
                                //Costruzione titolo widget
                                $title_w = "Selector";
                                $title_w = htmlentities($newWidgetDbRow->title_w, ENT_QUOTES|ENT_HTML5);
                                
                                if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                {
                                    $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                    $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", $widgetWizardSelectedRows, $selection, $mapZoom);
                                    $newWidgetDbRow = $widgetFactory->completeWidget();
                                }

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

                                $newInsQuery = $newInsQuery . $newQueryFields;
                                $newInsQuery = $newInsQuery . ") VALUES(";
                                $newInsQuery = $newInsQuery . $newQueryValues;
                                $newInsQuery = $newInsQuery . ")";

                                $insR = mysqli_query($link, $newInsQuery);

                                if(!$insR)
                                {
                                    if($addDashboard)
                                    {
                                        return false;
                                    }
                                    else
                                    {
                                        echo "Ko";
                                    }
                                }
                                else
                                {
                                    //Costruzione dei target widgets
                                    for($i = 0; $i < count($targetWidgets); $i++)
                                    {
                                        $targetWidgets[$i] = trim($targetWidgets[$i]);

                                        //Reperimento dati del target widget i-esimo
                                        $qTw = "SELECT * FROM Dashboard.Widgets WHERE id_type_widget = '$targetWidgets[$i]'";
                                        
                                        $rTw = mysqli_query($link, $qTw);

                                         if($rTw)
                                         {
                                            $twDbRow = mysqli_fetch_assoc($rTw);

                                            //Istanziamento del target widget i-esimo
                                            $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                            $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                            //Calcolo del next id
                                            $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                            if((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT']))) 
                                            {
                                                $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                            }

                                            //Costruzione n_row ed n_column: mettiamo il target widget accanto al main widget
                                            $n_row = $firstFreeRow;
                                            $n_column = $n_column + $size_columns;

                                            //Costruzione size_rows e size_columns
                                            $size_rows = $defaultParametersTarget[$i]['size_rows'];
                                            $size_columns = $defaultParametersTarget[$i]['size_columns'];

                                            //Costruzione nome del widget
                                            $id_metric = "ToBeReplacedByFactory";

                                            $type_w = $targetWidgets[$i];
                                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                            //Costruzione titolo widget
                                            $title_w = "Selector - Map";
                                            $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);
                                            
                                            $creator = $_SESSION['loggedUsername'];
                                            $newWidgetDbRowTarget[$i] = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParametersTarget[$i]['color_w'], $defaultParametersTarget[$i]['frequency_w'], $defaultParametersTarget[$i]['temporal_range_w'], $defaultParametersTarget[$i]['municipality_w'], $defaultParametersTarget[$i]['infoMessage_w'], $defaultParametersTarget[$i]['link_w'], $defaultParametersTarget[$i]['parameters'], $defaultParametersTarget[$i]['frame_color_w'], $defaultParametersTarget[$i]['udm'], $defaultParametersTarget[$i]['udmPos'], $defaultParametersTarget[$i]['fontSize'], $defaultParametersTarget[$i]['fontColor'], $defaultParametersTarget[$i]['controlsPosition'], $defaultParametersTarget[$i]['showTitle'], $defaultParametersTarget[$i]['controlsVisibility'], $defaultParametersTarget[$i]['zoomFactor'], $defaultParametersTarget[$i]['defaultTab'], $defaultParametersTarget[$i]['zoomControlsColor'], $defaultParametersTarget[$i]['scaleX'], $defaultParametersTarget[$i]['scaleY'], $defaultParametersTarget[$i]['headerFontColor'], $defaultParametersTarget[$i]['styleParameters'], $defaultParametersTarget[$i]['infoJson'], $defaultParametersTarget[$i]['serviceUri'], $defaultParametersTarget[$i]['viewMode'], $defaultParametersTarget[$i]['hospitalList'], $defaultParametersTarget[$i]['notificatorRegistered'], $defaultParametersTarget[$i]['notificatorEnabled'], $defaultParametersTarget[$i]['enableFullscreenTab'], $defaultParametersTarget[$i]['enableFullscreenModal'], $defaultParametersTarget[$i]['fontFamily'], $defaultParametersTarget[$i]['entityJson'], $defaultParametersTarget[$i]['attributeName'], $creator, null, $defaultParametersTarget[$i]['canceller'], $defaultParametersTarget[$i]['lastEditDate'], $defaultParametersTarget[$i]['cancelDate'], $defaultParametersTarget[$i]['actuatorTarget'], $defaultParametersTarget[$i]['actuatorEntity'], $defaultParametersTarget[$i]['actuatorAttribute'], $defaultParametersTarget[$i]['chartColor'], $defaultParametersTarget[$i]['dataLabelsFontSize'], $defaultParametersTarget[$i]['dataLabelsFontColor'], $defaultParametersTarget[$i]['chartLabelsFontSize'], $defaultParametersTarget[$i]['chartLabelsFontColor'], 'no', null, null, '{}', $newWidgetType);

                                            if($hasTargetWidgetFactory[$i] == 'yes')
                                            {
                                                $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                                $widgetFactory = new $widgetFactoryClass($newWidgetDbRowTarget[$i], null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "target", $widgetWizardSelectedRows, $selection, $mapZoom);
                                                $newWidgetDbRowTarget[$i] = $widgetFactory->completeWidget();
                                            }

                                            $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";

                                            //Query fields
                                            $newQueryFields = "";
                                            //Query values
                                            $newQueryValues = "";

                                            $count = 0;
                                            foreach($newWidgetDbRowTarget[$i] as $key => $value) 
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

                                            $newInsQuery = $newInsQuery . $newQueryFields;
                                            $newInsQuery = $newInsQuery . ") VALUES(";
                                            $newInsQuery = $newInsQuery . $newQueryValues;
                                            $newInsQuery = $newInsQuery . ")";

                                            $insR = mysqli_query($link, $newInsQuery);

                                            if(!$insR)
                                            {
                                                if($addDashboard)
                                                {
                                                    return false;
                                                }
                                                else
                                                {
                                                    echo "Ko";
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }
                                    
                                    //Costruzione dell'eventuale combo
                                    if($widgetTypeDbRow['comboName'] != null)
                                    {
                                        $comboFactoryClass = $widgetTypeDbRow['comboName'];
                                        $comboFactory = new $comboFactoryClass($newWidgetDbRow, $newWidgetDbRowTarget);
                                        if(!$comboFactory->finalizeCombo())
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }
                                    
                                    //Se si arriva qui si è sicuramente scritto correttamente su DB
                                    if($addDashboard)
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        echo "Ok";
                                    }
                                }
                            }
                            else
                            {
                                if($addDashboard)
                                {
                                    return false;
                                }
                                else
                                {
                                    echo "Ko";
                                }
                            }
                        }
                        
                    }
                }
                else
                {
                    if($addDashboard)
                    {
                        return false;
                    }
                    else
                    {
                        echo "Ko";
                    }
                }
            }
    }
    //Fine funzione addWidget()

    function updateWidgetsSame($link, $serviceMapUrlPrefix, $addDashboard)
    {
        $genFileContent = parse_ini_file("../conf/environment.ini");
        $orionContent = parse_ini_file("../conf/orion.ini");
        $orionBaseUrlLocal = $orionContent["orionBaseUrl"][$genFileContent['environment']['value']];

        $ssoContent = parse_ini_file("../conf/sso.ini");
        $ssoEndpoint = $ssoContent["ssoEndpoint"][$genFileContent['environment']['value']];
        $ssoTokenEndpoint = $ssoContent["ssoTokenEndpoint"][$genFileContent['environment']['value']];
        $ssoClientId = $ssoContent["ssoClientId"][$genFileContent['environment']['value']];
        $ssoClientSecret = $ssoContent["ssoClientSecret"][$genFileContent['environment']['value']];

        $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        $defaultColors2 = ["#fff5cc", "#ffe0b3", "#ffcccc", "#99ffff", "#99e6ff", "#adebad", "#80ff80"];

        $id_dashboard = $_REQUEST['dashboardId'];
        $dashboardTitle = $_REQUEST['dashboardTitle'];
        $dashboardAuthorName = $_REQUEST['dashboardAuthorName'];
        $dashboardEditor = $_REQUEST['dashboardEditorName'];
        $creator = $_REQUEST['dashboardEditorName'];
        $selection = $_REQUEST['selection'];
        $mapCenterLat = $_REQUEST['mapCenterLat'];
        $mapCenterLng = $_REQUEST['mapCenterLng'];
        $mapZoom = $_REQUEST['mapZoom'];

        $widgetTypeDbRow = NULL;
        $id_metric = NULL;
        $widgetWizardSelectedRows = $_REQUEST['widgetWizardSelectedRows'];
        $newWidgetType = $_REQUEST['widgetType'];
        $actuatorTargetWizard = $_REQUEST['actuatorTargetWizard'];
        $actuatorTargetInstance = $_REQUEST['actuatorTargetInstance'];
        $actuatorEntityName = $_REQUEST['actuatorEntityName'];
        $actuatorValueType = $_REQUEST['actuatorValueType'];
        $actuatorMinBaseValue = $_REQUEST['actuatorMinBaseValue'];
        $actuatorMaxImpulseValue = $_REQUEST['actuatorMaxImpulseValue'];
        $title_w = NULL;
        $n_row = NULL;
        $n_column = NULL;
        $color_widget = NULL;
        $freq_widget = NULL;
        $size_rows = NULL;
        $size_columns = NULL;
        $controlsPosition = NULL;
        $int_temp_widget = NULL;
        $comune_widget = NULL;
        $message_widget = NULL;
        $url_widget = "none";
        $showTitle = NULL;
        $controlsVisibility = NULL;
        $zoomFactor = NULL;
        $scaleX = NULL;
        $scaleY = NULL;
        $inputUdmWidget = NULL;
        $inputUdmPosition = NULL;
        $serviceUri = NULL;
        $viewMode = NULL;
        $hospitalList = NULL;
        $creationDate = NULL;
        $actuatorTarget = NULL;
        $defaultTab = NULL;
        $zoomControlsColor = NULL;
        $headerFontColor = NULL;
        $styleParameters = NULL;
        $showTableFirstCell = NULL;
        $tableFirstCellFontSize = NULL;
        $tableFirstCellFontColor = NULL;
        $rowsLabelsFontSize = NULL;
        $rowsLabelsFontColor = NULL;
        $colsLabelsFontSize = NULL;
        $colsLabelsFontColor = NULL;
        $rowsLabelsBckColor = NULL;
        $colsLabelsBckColor = NULL;
        $tableBorders = NULL;
        $tableBordersColor = NULL;
        $infoJsonObject = NULL;
        $infoJson = NULL;
        $legendFontSize = NULL;
        $legendFontColor = NULL;
        $dataLabelsFontSize = NULL;
        $dataLabelsFontColor = NULL;
        $barsColorsSelect = NULL;
        $barsColors = NULL;
        $chartType = NULL;
        $dataLabelsDistance = NULL;
        $dataLabelsDistance1 = NULL;
        $dataLabelsDistance2 = NULL;
        $dataLabels = NULL;
        $dataLabelsRotation = NULL;
        $xAxisDataset = NULL;
        $lineWidth = NULL;
        $alrLook = NULL;
        $colorsSelect = NULL;
        $colors = NULL;
        $colorsSelect1 = NULL;
        $colors1 = NULL;
        $innerRadius1 = NULL;
        $outerRadius1 = NULL;
        $innerRadius2 = NULL;
        $startAngle = NULL;
        $endAngle = NULL;
        $centerY = NULL;
        $gridLinesWidth = NULL;
        $gridLinesColor = NULL;
        $linesWidth = NULL;
        $alrThrLinesWidth = NULL;
        $clockData = NULL;
        $clockFont = NULL;
        $rectDim = NULL;
        $enableFullscreenTab = 'no';
        $enableFullscreenModal = 'no';
        $fontFamily = "";
        $newOrionEntityJson = NULL;
        $attributeName = NULL;
        $udm = NULL;
        $udmPosition = NULL;
        $nextId = 1;
        $firstFreeRow = NULL;
        $parameters = [];
        $newWidgetDbRowTarget = [];
        $sourceWidgetName = NULL;
        $sourceWidgetRow = NULL;
        $sourceEntityJson = NULL;
        $selectedRowIds = [];
        $oldWidgetIdToUpdate = $_REQUEST['widgetId'];  // PANTALEO

        if($newWidgetType == NULL)
        {
            //Caso tipo di widget non selezionato: dashboards fully custom vuote, ritorniamo true se add dashboard ma false negli altri casi
            if($addDashboard)
            {
                return true;
              //  return $oldWidgetIdToUpdate;
            }
            else
            {
                echo "Ko";
            }
        }
        else
        {
            //Caso tipo di widget selezionato
            $q3 = "SELECT * " .
                "FROM Dashboard.WidgetsIconsMap AS iconsMap " .
                "LEFT JOIN Dashboard.Widgets AS widgets " .
                "ON iconsMap.mainWidget = widgets.id_type_widget " .
                "WHERE iconsMap.icon = '$newWidgetType'";

            $r3 = mysqli_query($link, $q3);

            if($r3)
            {
                $widgetTypeDbRow = mysqli_fetch_assoc($r3);
                $widgetCategory = $widgetTypeDbRow['widgetCategory'];

                try
                {
                    $defaultParameters = json_decode($widgetTypeDbRow['defaultParametersMainWidget'], true);
                    $defaultParametersTarget = json_decode($widgetTypeDbRow['defaultParametersTargetWidget'], true);
                }
                catch(Exception $e)
                {

                }

                //Ramo mono
                if($widgetTypeDbRow['mono_multi'] == 'Mono')
                {
                    if(($widgetCategory == 'actuator')&&($actuatorTargetInstance == 'new'))
                    {
                        if($actuatorTargetWizard == 'broker')
                        {
                            //Istanziamento nuovo widget con nuova entità su broker
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
                                $id_metric = null;

                                $type_w = $widgetTypeDbRow['mainWidget'];
                                $name_w = preg_replace('/\+/', '', $actuatorEntityName) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                //Costruzione titolo widget
                                $title_w = $actuatorEntityName . " - " . $actuatorValueType;

                                $creator = $_SESSION['loggedUsername'];
                                $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], '{}', $newWidgetType);

                                //Preparazione newOrionEntityJson
                                $newOrionEntity = [];
                                $newOrionEntity['id'] = $name_w;
                                $newOrionEntity['type'] = $actuatorEntityName;
                                $newOrionEntity['entityDesc'] = ["type" => "String", "value" => $actuatorEntityName];
                                $newOrionEntity['entityCreator'] = ["value" => $creator, "type" => "String"];
                                $newOrionEntity['creationDate'] = ["value" => $creationDate, "type" => "String"];
                                $newOrionEntity['actuatorDeleted'] = ["value" => false, "type" => "Boolean"];
                                $newOrionEntity['actuatorDeletionDate'] = ["value" => NULL, "type" => "String"];
                                $newOrionEntity['actuatorCanceller'] = ["value" => NULL, "type" => "String"];

                                //Per ora cablato, poi introdurremo una soluzione più elegante
                                switch($type_w)
                                {
                                    case "widgetImpulseButton":
                                        $entityAttrType = "string";
                                        $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                        $widgetParameters->baseValue = $actuatorMinBaseValue;
                                        $widgetParameters->impulseValue = $actuatorMaxImpulseValue;
                                        $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                        break;

                                    case "widgetOnOffButton":
                                        $entityAttrType = "string";
                                        $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                        $widgetParameters->offValue = $actuatorMinBaseValue;
                                        $widgetParameters->onValue = $actuatorMaxImpulseValue;
                                        $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                        break;

                                    case "widgetKnob":
                                        $widgetParameters = json_decode($newWidgetDbRow->parameters);
                                        $widgetParameters->domainType = "continuous";
                                        $widgetParameters->minValue = $actuatorMinBaseValue;
                                        $widgetParameters->maxValue = $actuatorMaxImpulseValue;
                                        $widgetParameters->continuousRanges = null;
                                        $widgetParameters->dataPrecision = 2;
                                        $newWidgetDbRow->parameters = json_encode($widgetParameters);
                                        $entityAttrType = "float";
                                        break;

                                    case "widgetNumericKeyboard":
                                        $entityAttrType = "float";
                                        break;
                                }

                                $newOrionEntity[$actuatorValueType] = ["type" => $entityAttrType, "value" => $actuatorMinBaseValue, "metadata" => ["attrDesc" => ["value" => $actuatorValueType, "type" => "String"]]];
                                $newOrionEntityJson = json_encode($newOrionEntity);

                                $newWidgetDbRow->actuatorTarget = $actuatorTargetWizard;
                                $newWidgetDbRow->attributeName = $actuatorValueType;
                                $newWidgetDbRow->sm_based = 'no';
                                $newWidgetDbRow->entityJson = $newOrionEntityJson;

                                if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                {
                                    $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                    $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom, null);
                                    $newWidgetDbRow = $widgetFactory->completeWidget();
                                }

                                $newWidgetDbRow->title_w = htmlentities($newWidgetDbRow->title_w, ENT_QUOTES|ENT_HTML5);

                                //    $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                                $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                                //Query fields
                                $newQueryFields = "";
                                //Query values
                                $newQueryValues = "";
                                $fieldsAndValuesObj = [];

                                $count = 0;
                                foreach($newWidgetDbRow as $key => $value)
                                {
                                    if($count == 0)
                                    {
                                        $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                        $newQueryValues = returnManagedStringForDb($value);
                                        $fieldsAndValuesObj[$key] = $value;
                                    }
                                    else
                                    {
                                     //   if ($key != 'n_row') {
                                    //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                        if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                            $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                            $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                            $fieldsAndValuesObj[$key] = $value;
                                        } else {
                                          //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                        }
                                    }

                                    $count++;
                                }

                                $newInsQuery = $newInsQuery . $newQueryFields;              // PANTALEO Qr END
                            /*    $newInsQuery = $newInsQuery . ") VALUES(";
                                $newInsQuery = $newInsQuery . $newQueryValues;
                                $newInsQuery = $newInsQuery . ")";  */
                                $newInsQuery = substr($newInsQuery, 0, -1);
                                $newInsQuery = $newInsQuery . " WHERE name_w = '" . $oldWidgetIdToUpdate ."';";

                                //$file = fopen("C:\dashboardLog.txt", "w");
                                //fwrite($file, "newInsQuery: " . $newInsQuery . "\n");

                                $insR = mysqli_query($link, $newInsQuery);

                                if($insR)
                                {
                                    //Inserimento nuova entità su broker + inserimento nuovo valore su tabella DB nostro
                                    /*$orionAddEntityUrl = $orionBaseUrlLocal. "/v2/entities";

                                    $orionCallOptions = array(
                                            'http' => array(
                                                    'header'  => "Content-type: application/json\r\n",
                                                    'method'  => 'POST',
                                                    'content' => $newOrionEntityJson,
                                                    'timeout' => 30
                                            )
                                    );*/

                                    //Nuova versione che scrive su IOT directory e non più direttamente su broker

                                    $attributes = [
                                        ["value_name" => $actuatorValueType,  "data_type" => $entityAttrType, "value_type" => $actuatorValueType, "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "actuatorCanceller",  "data_type" => "string", "value_type" => "actuator_canceller", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "actuatorDeleted",  "data_type" => "boolean", "value_type" => "actuator_deleted", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "actuatorDeletionDate",  "data_type" => "boolean", "value_type" => "actuator_deletion_date", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "creationDate",  "data_type" => "string", "value_type" => "creation_date", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "entityCreator",  "data_type" => "string", "value_type" => "entity_creator", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000],
                                        ["value_name" => "entityDesc",  "data_type" => "string", "value_type" => "entity_desc", "editable" => 0, "value_unit" => "#", "healthiness_criteria" => "refresh_rate", "healthiness_value" => 1000]
                                    ];

                                    $id = $name_w; //Id del widget
                                    $type = $actuatorEntityName; //$actuatorEntityName
                                    $kind = "actuator"; //Per ora fisso
                                    $contextBroker = "orionUNIFI"; //Per ora fisso
                                    $protocol = "ngsi"; //Per ora fisso
                                    $format = "json"; //Per ora fisso

                                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                                    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                                    $accessToken = $tkn->access_token;
                                    $_SESSION['refreshToken'] = $tkn->refresh_token;
                                    $k1 = generateUUID();
                                    $k2 = generateUUID();

                                    $iotDirApiUrl = "https://iotdirectory.snap4city.org/api/device.php?action=insert&attributes=" . urlencode(json_encode($attributes)) . "&id=" . $id . "&type=" . $type . "&kind=" . $kind . "&contextbroker=" . $contextBroker . "&protocol=" . $protocol . "&format=" . $format . "&mac=&model=&producer=&latitude=43&longitude=11&visibility=private&frequency=1&nodered=yes&k1=" . $k1 . "&k2=" .$k2 . "&token=" . $accessToken . "&username=" . $_SESSION['loggedUsername'] . "&organization=" . $_SESSION['loggedOrganization'];

                                    try
                                    {
                                        $iotDirPayload = file_get_contents($iotDirApiUrl);

                                        //if(strpos($http_response_header[0], '201 Created') === false)
                                        if(strpos($http_response_header[0], '200') === false)
                                        {
                                            $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                            $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                            mysqli_close($link);

                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                                exit();
                                            }
                                        }
                                        else
                                        {
                                            //Inserimento primo record di attuazione
                                            $entityId = $name_w;
                                            $actionTime = date('Y-m-d H:i:s');
                                            $value = $actuatorMinBaseValue;
                                            $username = $creator;
                                            $ipAddress = $_SERVER['REMOTE_ADDR'];

                                            $firstValueQuery = "INSERT INTO Dashboard.ActuatorsEntitiesValues(entityId, actionTime, value, username, ipAddress, actuationResult, actuationResultTime) " .
                                                "VALUES('$entityId', '$actionTime', '$value', '$username', '$ipAddress', 'Ok', '$actionTime')";

                                            $queryResult = mysqli_query($link, $firstValueQuery);
                                            mysqli_close($link);

                                            if($addDashboard)
                                            {
                                                return true;
                                            }
                                            else
                                            {
                                                echo "Ok";
                                            }
                                        }
                                    }
                                    catch (Exception $ex)
                                    {
                                        $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
                                        mysqli_select_db($link, $dbname);
                                        $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                        $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                        mysqli_close($link);

                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }
                                }
                                else
                                {
                                    if($addDashboard)
                                    {
                                        return false;
                                    }
                                    else
                                    {
                                        echo "Ko";
                                    }
                                }
                            }
                            else
                            {
                                if($addDashboard)
                                {
                                    return false;
                                }
                                else
                                {
                                    echo "Ko";
                                }
                            }
                        }
                        else
                        {
                            //TBD - Istanziamento nuovo widget con nuovo blocchetto su NodeRED (non si farà)
                        }
                    }
                    else
                    {   // CASO NON ACTUATOR cioè DATA-VIEWER
                        //Caso widget selezionato di tipo mono con righe selezionate (differenza fra attuatori new e attuatori non new e viewer)
                        //Ramo per i mono SENZA target widget
                        if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                        {
                            //Ne creiamo uno per ogni riga selezionata
                            foreach($widgetWizardSelectedRows as $selectedRowKey => $selectedRow)
                            {
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
                                    if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator')&&($selectedRow['nature'] == 'From Dashboard to IOT Device'))
                                    {
                                        $id_metric = null;
                                        $sourceWidgetName = $selectedRow['unique_name_id'];
                                        $entityQ = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w = '$sourceWidgetName'";
                                        $entityR = mysqli_query($link, $entityQ);

                                        if($entityR)
                                        {
                                            if(mysqli_num_rows($entityR) > 0)
                                            {
                                                $sourceWidgetRow = mysqli_fetch_assoc($entityR);
                                                $sourceEntity = json_decode($sourceWidgetRow['entityJson']);
                                                $sourceEntityJson = $sourceWidgetRow['entityJson'];
                                                $actuatorEntityName = $sourceEntity->type;
                                            }
                                            else
                                            {
                                                //Reperiamo l'entità dal broker
                                                $orionGetEntityUrl = $orionBaseUrlLocal. "/v2/entities/" . $sourceWidgetName;

                                                try
                                                {
                                                    $callResponse = file_get_contents($orionGetEntityUrl);

                                                    if(strpos($http_response_header[0], '200') === false)
                                                    {
                                                        if($addDashboard)
                                                        {
                                                            return false;
                                                        }
                                                        else
                                                        {
                                                            echo "Ko";
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $sourceEntityJson = $callResponse;
                                                        $sourceEntity = json_decode($callResponse);
                                                        $actuatorEntityName = $sourceEntity->type;
                                                    }
                                                }
                                                catch(Exception $e)
                                                {
                                                    if($addDashboard)
                                                    {
                                                        return false;
                                                    }
                                                    else
                                                    {
                                                        echo "Ko";
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            //Reperiamo l'entità dal broker
                                            $orionGetEntityUrl = $orionBaseUrlLocal. "/v2/entities/" . $sourceWidgetName;

                                            try
                                            {
                                                $callResponse = file_get_contents($orionGetEntityUrl);

                                                if(strpos($http_response_header[0], '200') === false)
                                                {
                                                    if($addDashboard)
                                                    {
                                                        return false;
                                                    }
                                                    else
                                                    {
                                                        echo "Ko";
                                                    }
                                                }
                                                else
                                                {
                                                    $sourceEntityJson = $callResponse;
                                                    $sourceEntity = json_decode($callResponse);
                                                    $actuatorEntityName = $sourceEntity->type;
                                                }
                                            }
                                            catch(Exception $e)
                                            {
                                                if($addDashboard)
                                                {
                                                    return false;
                                                }
                                                else
                                                {
                                                    echo "Ko";
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Dashboard-IOT App'))
                                        {
                                            $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['parameters']));
                                        }
                                        else
                                        {
                                            if(isset($selectedRow['unique_name_id']))
                                            {
                                                if($selectedRow['high_level_type'] == 'Dashboard-IOT App')
                                                {
                                                    $id_metric = $selectedRow['parameters'];
                                                }
                                                else
                                                {
                                                    $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                                }
                                            }
                                            else
                                            {
                                                $id_metric = "ToBeReplacedByFactory";
                                            }
                                        }
                                    }

                                    $type_w = $widgetTypeDbRow['mainWidget'];

                                    if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                    {
                                        $name_w = preg_replace('/\+/', '', $actuatorEntityName) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                        $name_w = preg_replace('/%20/', 'NBSP', $name_w);
                                    }
                                    else
                                    {
                                        $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                        $name_w = preg_replace('/%20/', 'NBSP', $name_w);
                                    }

                                    //Costruzione titolo widget
                                    if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                    {
                                        $title_w = $actuatorEntityName . " - " . $selectedRow['low_level_type'];
                                    }
                                    else
                                    {
                                        if($selectedRow['unique_name_id'] != null)
                                        {
                                            $title_w = $selectedRow['sub_nature'] . " - " . $selectedRow['unique_name_id'];
                                        }
                                        else
                                        {
                                            $title_w = $selectedRow['sub_nature'];
                                        }
                                    }

                                    $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                    $creator = $_SESSION['loggedUsername'];
                                    $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], json_encode([$selectedRowKey => $selectedRow]), $newWidgetType);

                                    if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                    {
                                        $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                        $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom);
                                        $newWidgetDbRow = $widgetFactory->completeWidget();
                                    }

                                    if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Sensor-Actuator'))
                                    {
                                        $newWidgetDbRow->entityJson = $sourceEntityJson;

                                        $sourceEntityDecoded = json_decode($sourceEntityJson);

                                        foreach($sourceEntityDecoded as $key => $val)
                                        {
                                            if(($key != 'actuatorCanceller')&&($key != 'actuatorDeleted')&&($key != 'actuatorDeletionDate')&&($key != 'creationDate')&&($key != 'entityCreator')&&($key != 'entityDesc')&&($key != 'id')&&($key != 'type'))
                                            {
                                                $newWidgetDbRow->attributeName = $key;
                                                break;
                                            }
                                        }

                                        $newWidgetDbRow->actuatorTarget = 'broker';
                                    }

                                    if(($widgetCategory == 'actuator')&&($selectedRow['high_level_type'] == 'Dashboard-IOT App'))
                                    {
                                        $newWidgetDbRow->actuatorTarget = 'app';
                                    }

                                    if($widgetCategory == 'actuator')
                                    {
                                        if($newWidgetDbRow->type_w == $sourceWidgetRow['type_w'])
                                        {
                                            $newWidgetDbRow->parameters = $sourceWidgetRow['parameters'];
                                        }
                                        else
                                        {
                                            $sourceParams = json_decode($sourceWidgetRow['parameters']);
                                            $destParams = json_decode($newWidgetDbRow->parameters);
                                            switch($sourceWidgetRow['type_w'])
                                            {
                                                case "widgetImpulseButton":
                                                    switch($newWidgetDbRow->type_w)
                                                    {
                                                        case "widgetOnOffButton":
                                                            $destParams->offValue = $sourceParams->baseValue;
                                                            $destParams->onValue = $sourceParams->impulseValue;
                                                            break;

                                                        case "widgetKnob":
                                                            $destParams->minValue = $sourceParams->baseValue;
                                                            $destParams->maxValue = $sourceParams->impulseValue;
                                                            break;

                                                        case "widgetNumericKeyboard":
                                                            break;
                                                    }
                                                    $newWidgetDbRow->parameters = json_encode($destParams);
                                                    break;

                                                case "widgetOnOffButton":
                                                    switch($newWidgetDbRow->type_w)
                                                    {
                                                        case "widgetImpulseButton":
                                                            $destParams->baseValue = $sourceParams->offValue;
                                                            $destParams->impulseValue = $sourceParams->onValue;
                                                            break;

                                                        case "widgetKnob":
                                                            $destParams->minValue = $sourceParams->offValue;
                                                            $destParams->maxValue = $sourceParams->onValue;
                                                            break;

                                                        case "widgetNumericKeyboard":
                                                            break;
                                                    }
                                                    $newWidgetDbRow->parameters = json_encode($destParams);
                                                    break;

                                                case "widgetKnob":
                                                    switch($newWidgetDbRow->type_w)
                                                    {
                                                        case "widgetImpulseButton":
                                                            $destParams->baseValue = $sourceParams->minValue;
                                                            $destParams->impulseValue = $sourceParams->maxValue;
                                                            break;

                                                        case "widgetOnOffButton":
                                                            $destParams->offValue = $sourceParams->minValue;
                                                            $destParams->onValue = $sourceParams->maxValue;
                                                            break;

                                                        case "widgetNumericKeyboard":
                                                            break;
                                                    }
                                                    $newWidgetDbRow->parameters = json_encode($destParams);
                                                    break;

                                                case "widgetNumericKeyboard":
                                                    break;
                                            }
                                        }
                                    }

                                    if($selectedRow['high_level_type'] == 'My Personal Data')
                                    {
                                        $newWidgetDbRow->sm_based = 'myPersonalData';
                                        $newWidgetDbRow->sm_field = $selectedRow['unique_name_id'];

                                        /*if($type_w == 'widgetTracker')
                                        {
                                            $newWidgetDbRow->rowParameters = $selectedRow['low_level_type'];
                                        }*/
                                    }

                                    //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                                    $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                                    //Query fields
                                    $newQueryFields = "";
                                    //Query values
                                    $newQueryValues = "";
                                    $fieldsAndValuesObj = [];

                                    $count = 0;
                                    foreach($newWidgetDbRow as $key => $value)
                                    {
                                        if($count == 0)
                                        {
                                          //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                          //  $newQueryValues = returnManagedStringForDb($value);
                                          //  $fieldsAndValuesObj[$key] = $value;
                                        }
                                        else
                                        {
                                            //if ($key != 'n_row') {
                                        //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                            if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                                $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                $fieldsAndValuesObj[$key] = $value;
                                            } else {
                                              //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                            }
                                        }

                                        $count++;
                                    }

                                    $newInsQuery = $newInsQuery . $newQueryFields;              // PANTALEO Qr END
                                    /*    $newInsQuery = $newInsQuery . ") VALUES(";
                                        $newInsQuery = $newInsQuery . $newQueryValues;
                                        $newInsQuery = $newInsQuery . ")";  */
                                    $newInsQuery = substr($newInsQuery, 0, -1);
                                    $newInsQuery = $newInsQuery . " WHERE name_w = '" . $oldWidgetIdToUpdate ."';";

                                    $insR = mysqli_query($link, $newInsQuery);

                                    if(!$insR)
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                            exit();
                                        }
                                    }
                                }
                            }

                            //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                            if($addDashboard)
                            {
                                return true;
                            }
                            else
                            {
                                //echo "Ok";
                                echo json_encode($fieldsAndValuesObj);
                                // mettere in echo $oldWidgetIdToUpdate ?
                            }
                        }
                        else
                        {
                            //Caso widget selezionato di tipo mono, però con target widgets (main + targets)

                            //Split dei target widgets
                            $targetWidgets = explode(",", $widgetTypeDbRow['targetWidget']);
                            $hasTargetWidgetFactory = json_decode($widgetTypeDbRow['hasTargetWidgetFactory']);

                            foreach($widgetWizardSelectedRows as $selectedRowKey => $selectedRow)
                            {
                                //Costruzione del main widget
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
                                    if(isset($selectedRow['unique_name_id']))
                                    {
                                        $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                    }
                                    else
                                    {
                                        $id_metric = "ToBeReplacedByFactory";
                                    }

                                    $type_w = $widgetTypeDbRow['mainWidget'];
                                    $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                    $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                    //Costruzione titolo widget
                                    if($selectedRow['unique_name_id'] != null)
                                    {
                                        $title_w = $selectedRow['sub_nature'] . " - " . $selectedRow['unique_name_id'];
                                    }
                                    else
                                    {
                                        $title_w = $selectedRow['sub_nature'];
                                    }

                                    $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                    $creator = $_SESSION['loggedUsername'];
                                    $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], json_encode([$selectedRowKey => $selectedRow]), $newWidgetType);

                                    if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                                    {
                                        $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                        $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", null, $selection, $mapZoom);
                                        $newWidgetDbRow = $widgetFactory->completeWidget();
                                    }

                                    //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                                    $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                                    //Query fields
                                    $newQueryFields = "";
                                    //Query values
                                    $newQueryValues = "";

                                    $count = 0;
                                    foreach($newWidgetDbRow as $key => $value)
                                    {
                                        if($count == 0)
                                        {
                                            //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                            //  $newQueryValues = returnManagedStringForDb($value);
                                            //  $fieldsAndValuesObj[$key] = $value;
                                        }
                                        else
                                        {
                                            //if ($key != 'n_row') {
                                            //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                            if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                                $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                $fieldsAndValuesObj[$key] = $value;
                                            } else {
                                                //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                            }
                                        }

                                        $count++;
                                    }

                                    $newInsQuery = $newInsQuery . $newQueryFields;
                                    $newInsQuery = $newInsQuery . ") VALUES(";
                                    $newInsQuery = $newInsQuery . $newQueryValues;
                                    $newInsQuery = $newInsQuery . ")";

                                    $insR = mysqli_query($link, $newInsQuery);

                                    if(!$insR)
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }

                                    //Costruzione dei target widgets
                                    for($i = 0; $i < count($targetWidgets); $i++)
                                    {
                                        $targetWidgets[$i] = trim($targetWidgets[$i]);

                                        //Reperimento dati del target widget i-esimo
                                        $qTw = "SELECT * FROM Dashboard.Widgets WHERE id_type_widget = '$targetWidgets[$i]'";

                                        $rTw = mysqli_query($link, $qTw);

                                        if($rTw)
                                        {
                                            $twDbRow = mysqli_fetch_assoc($rTw);

                                            //Istanziamento del target widget i-esimo
                                            $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                            $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                            //Calcolo del next id
                                            $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                            if((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT'])))
                                            {
                                                $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                            }

                                            //Costruzione n_row ed n_column: mettiamo il target widget accanto al main widget
                                            $n_row = $firstFreeRow;
                                            $n_column = $n_column + $size_columns;

                                            //Costruzione size_rows e size_columns
                                            $size_rows = $defaultParametersTarget[$i]['size_rows'];
                                            $size_columns = $defaultParametersTarget[$i]['size_columns'];

                                            //Costruzione nome del widget
                                            if($targetWidgets[$i] == 'widgetTimeTrend')
                                            {
                                                $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                                            }
                                            else
                                            {
                                                $id_metric = "ToBeReplacedByFactory";
                                            }

                                            $type_w = $targetWidgets[$i];
                                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                            //Costruzione titolo widget
                                            $title_w = $selectedRow['sub_nature'] . " - Target";

                                            $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                            $creator = $_SESSION['loggedUsername'];
                                            $newWidgetDbRowTarget[$i] = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParametersTarget[$i]['color_w'], $defaultParametersTarget[$i]['frequency_w'], $defaultParametersTarget[$i]['temporal_range_w'], $defaultParametersTarget[$i]['municipality_w'], $defaultParametersTarget[$i]['infoMessage_w'], $defaultParametersTarget[$i]['link_w'], $defaultParametersTarget[$i]['parameters'], $defaultParametersTarget[$i]['frame_color_w'], $defaultParametersTarget[$i]['udm'], $defaultParametersTarget[$i]['udmPos'], $defaultParametersTarget[$i]['fontSize'], $defaultParametersTarget[$i]['fontColor'], $defaultParametersTarget[$i]['controlsPosition'], $defaultParametersTarget[$i]['showTitle'], $defaultParametersTarget[$i]['controlsVisibility'], $defaultParametersTarget[$i]['zoomFactor'], $defaultParametersTarget[$i]['defaultTab'], $defaultParametersTarget[$i]['zoomControlsColor'], $defaultParametersTarget[$i]['scaleX'], $defaultParametersTarget[$i]['scaleY'], $defaultParametersTarget[$i]['headerFontColor'], $defaultParametersTarget[$i]['styleParameters'], $defaultParametersTarget[$i]['infoJson'], $defaultParametersTarget[$i]['serviceUri'], $defaultParametersTarget[$i]['viewMode'], $defaultParametersTarget[$i]['hospitalList'], $defaultParametersTarget[$i]['notificatorRegistered'], $defaultParametersTarget[$i]['notificatorEnabled'], $defaultParametersTarget[$i]['enableFullscreenTab'], $defaultParametersTarget[$i]['enableFullscreenModal'], $defaultParametersTarget[$i]['fontFamily'], $defaultParametersTarget[$i]['entityJson'], $defaultParametersTarget[$i]['attributeName'], $creator, null, $defaultParametersTarget[$i]['canceller'], $defaultParametersTarget[$i]['lastEditDate'], $defaultParametersTarget[$i]['cancelDate'], $defaultParametersTarget[$i]['actuatorTarget'], $defaultParametersTarget[$i]['actuatorEntity'], $defaultParametersTarget[$i]['actuatorAttribute'], $defaultParametersTarget[$i]['chartColor'], $defaultParametersTarget[$i]['dataLabelsFontSize'], $defaultParametersTarget[$i]['dataLabelsFontColor'], $defaultParametersTarget[$i]['chartLabelsFontSize'], $defaultParametersTarget[$i]['chartLabelsFontColor'], $selectedRow['sm_based'], $selectedRow['parameters'], $selectedRow['low_level_type'], '[]');

                                            if($hasTargetWidgetFactory[$i] == 'yes')
                                            {
                                                $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                                $widgetFactory = new $widgetFactoryClass($newWidgetDbRowTarget[$i], $selectedRowKey, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "target", null, $selection, $mapZoom);
                                                $newWidgetDbRowTarget[$i] = $widgetFactory->completeWidget();
                                            }

                                            //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                                            $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                                            //Query fields
                                            $newQueryFields = "";
                                            //Query values
                                            $newQueryValues = "";

                                            $count = 0;
                                            foreach($newWidgetDbRowTarget[$i] as $key => $value)
                                            {
                                                if($count == 0)
                                                {
                                                    //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                                    //  $newQueryValues = returnManagedStringForDb($value);
                                                    //  $fieldsAndValuesObj[$key] = $value;
                                                }
                                                else
                                                {
                                                    //if ($key != 'n_row') {
                                                    //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                    if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                        $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                                        $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                        $fieldsAndValuesObj[$key] = $value;
                                                    } else {
                                                        //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                                    }
                                                }

                                                $count++;
                                            }

                                            $newInsQuery = $newInsQuery . $newQueryFields;
                                            $newInsQuery = $newInsQuery . ") VALUES(";
                                            $newInsQuery = $newInsQuery . $newQueryValues;
                                            $newInsQuery = $newInsQuery . ")";

                                            $insR = mysqli_query($link, $newInsQuery);

                                            if(!$insR)
                                            {
                                                if($addDashboard)
                                                {
                                                    return false;
                                                }
                                                else
                                                {
                                                    echo "Ko";
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }
                                }

                                //Costruzione dell'eventuale combo
                                if($widgetTypeDbRow['comboName'] != null)
                                {
                                    $comboFactoryClass = $widgetTypeDbRow['comboName'];
                                    $comboFactory = new $comboFactoryClass($newWidgetDbRow, $newWidgetDbRowTarget);
                                    if(!$comboFactory->finalizeCombo())
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }
                                }

                            }//Fine foreach sulla i-esima riga selezionata

                            //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                            if($addDashboard)
                            {
                                return true;
                            }
                            else
                            {
                                echo "Ok";
                            }
                        }
                    }
                }
                else
                {
                    //Caso widget selezionato di tipo multi
                    if(($widgetTypeDbRow['targetWidget'] == '')||($widgetTypeDbRow['targetWidget'] == null))
                    {
                        //Caso widget multi ma senza target widget (pronto soccorso multi ospedale, series, service map stand alone)
                        //Costruzione del main widget
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
                            if(isset($selectedRow['unique_name_id']))
                            {
                                $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                            }
                            else
                            {
                                $id_metric = "ToBeReplacedByFactory";
                            }

                            $type_w = $widgetTypeDbRow['mainWidget'];
                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                            //Costruzione titolo widget
                            $title_w = "External content";
                            $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                            $creator = $_SESSION['loggedUsername'];

                            $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], no, null, null, json_encode($widgetWizardSelectedRows), $newWidgetType);

                            if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                            {
                                $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", $widgetWizardSelectedRows, $selection, $mapZoom);
                                $newWidgetDbRow = $widgetFactory->completeWidget();
                            }

                            //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                            $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                            //Query fields
                            $newQueryFields = "";
                            //Query values
                            $newQueryValues = "";
                            $fieldsAndValuesObj = [];

                            $count = 0;
                            foreach($newWidgetDbRow as $key => $value)
                            {
                                if($count == 0)
                                {
                                    //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                    //  $newQueryValues = returnManagedStringForDb($value);
                                    //  $fieldsAndValuesObj[$key] = $value;
                                }
                                else
                                {
                                    //if ($key != 'n_row') {
                                    //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                    if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                        $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                        $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                        $fieldsAndValuesObj[$key] = $value;
                                    } else {
                                        //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                    }
                                }

                                $count++;
                            }

                            $newInsQuery = $newInsQuery . $newQueryFields;              // PANTALEO Qr END
                            /*    $newInsQuery = $newInsQuery . ") VALUES(";
                            $newInsQuery = $newInsQuery . $newQueryValues;
                            $newInsQuery = $newInsQuery . ")";  */
                            $newInsQuery = substr($newInsQuery, 0, -1);
                            $newInsQuery = $newInsQuery . " WHERE name_w = '" . $oldWidgetIdToUpdate . "';";

                            $insR = mysqli_query($link, $newInsQuery);

                            if(!$insR)
                            {
                                if($addDashboard)
                                {
                                    return false;
                                }
                                else
                                {
                                    echo "Ko";
                                }
                            }
                            else
                            {
                                if($addDashboard)
                                {
                                    return true;
                                }
                                else
                                {
                                    echo "Ok";
                                }
                            }
                        }
                    }
                    else
                    {
                        //Caso widget multi con target widgets (selector + map, selector + map + trend)

                        //Split dei target widgets
                        $targetWidgets = explode(",", $widgetTypeDbRow['targetWidget']);
                        $hasTargetWidgetFactory = json_decode($widgetTypeDbRow['hasTargetWidgetFactory']);

                        //Costruzione del main widget
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
                            if(isset($selectedRow['unique_name_id']))
                            {
                                $id_metric = str_replace('.', '_', str_replace('-', '_', $selectedRow['unique_name_id']));
                            }
                            else
                            {
                                //$id_metric = "ToBeReplacedByFactory";
                                $id_metric = "Selector";
                            }

                            $type_w = $widgetTypeDbRow['mainWidget'];
                            $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                            $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                            $creator = $_SESSION['loggedUsername'];

                            $newWidgetDbRow = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParameters['color_w'], $defaultParameters['frequency_w'], $defaultParameters['temporal_range_w'], $defaultParameters['municipality_w'], $defaultParameters['infoMessage_w'], $defaultParameters['link_w'], $defaultParameters['parameters'], $defaultParameters['frame_color_w'], htmlentities($defaultParameters['udm'], ENT_QUOTES|ENT_HTML5), $defaultParameters['udmPos'], $defaultParameters['fontSize'], $defaultParameters['fontColor'], $defaultParameters['controlsPosition'], $defaultParameters['showTitle'], $defaultParameters['controlsVisibility'], $defaultParameters['zoomFactor'], $defaultParameters['defaultTab'], $defaultParameters['zoomControlsColor'], $defaultParameters['scaleX'], $defaultParameters['scaleY'], $defaultParameters['headerFontColor'], $defaultParameters['styleParameters'], $defaultParameters['infoJson'], $defaultParameters['serviceUri'], $defaultParameters['viewMode'], $defaultParameters['hospitalList'], $defaultParameters['notificatorRegistered'], $defaultParameters['notificatorEnabled'], $defaultParameters['enableFullscreenTab'], $defaultParameters['enableFullscreenModal'], $defaultParameters['fontFamily'], $defaultParameters['entityJson'], $defaultParameters['attributeName'], $creator, null, $defaultParameters['canceller'], $defaultParameters['lastEditDate'], $defaultParameters['cancelDate'], $defaultParameters['actuatorTarget'], $defaultParameters['actuatorEntity'], $defaultParameters['actuatorAttribute'], $defaultParameters['chartColor'], $defaultParameters['dataLabelsFontSize'], $defaultParameters['dataLabelsFontColor'], $defaultParameters['chartLabelsFontSize'], $defaultParameters['chartLabelsFontColor'], 'no', null, null, json_encode($widgetWizardSelectedRows), $newWidgetType);

                            //Costruzione titolo widget
                            $title_w = "Selector";
                            $title_w = htmlentities($newWidgetDbRow->title_w, ENT_QUOTES|ENT_HTML5);

                            if($widgetTypeDbRow['hasMainWidgetFactory'] == 'yes')
                            {
                                $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                $widgetFactory = new $widgetFactoryClass($newWidgetDbRow, null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "main", $widgetWizardSelectedRows, $selection, $mapZoom);
                                $newWidgetDbRow = $widgetFactory->completeWidget();
                            }

                            //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                            $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                            //Query fields
                            $newQueryFields = "";
                            //Query values
                            $newQueryValues = "";

                            $count = 0;
                            foreach($newWidgetDbRow as $key => $value)
                            {
                                if($count == 0)
                                {
                                    //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                    //  $newQueryValues = returnManagedStringForDb($value);
                                    //  $fieldsAndValuesObj[$key] = $value;
                                }
                                else
                                {
                                    //if ($key != 'n_row') {
                                    //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                    if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                        $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                        $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                        $fieldsAndValuesObj[$key] = $value;
                                    } else {
                                        //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                    }
                                }

                                $count++;
                            }

                            $newInsQuery = $newInsQuery . $newQueryFields;
                            $newInsQuery = $newInsQuery . ") VALUES(";
                            $newInsQuery = $newInsQuery . $newQueryValues;
                            $newInsQuery = $newInsQuery . ")";

                            $insR = mysqli_query($link, $newInsQuery);

                            if(!$insR)
                            {
                                if($addDashboard)
                                {
                                    return false;
                                }
                                else
                                {
                                    echo "Ko";
                                }
                            }
                            else
                            {
                                //Costruzione dei target widgets
                                for($i = 0; $i < count($targetWidgets); $i++)
                                {
                                    $targetWidgets[$i] = trim($targetWidgets[$i]);

                                    //Reperimento dati del target widget i-esimo
                                    $qTw = "SELECT * FROM Dashboard.Widgets WHERE id_type_widget = '$targetWidgets[$i]'";

                                    $rTw = mysqli_query($link, $qTw);

                                    if($rTw)
                                    {
                                        $twDbRow = mysqli_fetch_assoc($rTw);

                                        //Istanziamento del target widget i-esimo
                                        $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                                        $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

                                        //Calcolo del next id
                                        $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
                                        if((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT'])))
                                        {
                                            $nextId = $rowMaxSel2['AUTO_INCREMENT'];
                                        }

                                        //Costruzione n_row ed n_column: mettiamo il target widget accanto al main widget
                                        $n_row = $firstFreeRow;
                                        $n_column = $n_column + $size_columns;

                                        //Costruzione size_rows e size_columns
                                        $size_rows = $defaultParametersTarget[$i]['size_rows'];
                                        $size_columns = $defaultParametersTarget[$i]['size_columns'];

                                        //Costruzione nome del widget
                                        $id_metric = "ToBeReplacedByFactory";

                                        $type_w = $targetWidgets[$i];
                                        $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_w . $nextId;
                                        $name_w = preg_replace('/%20/', 'NBSP', $name_w);

                                        //Costruzione titolo widget
                                        $title_w = "Selector - Map";
                                        $title_w = htmlentities($title_w, ENT_QUOTES|ENT_HTML5);

                                        $creator = $_SESSION['loggedUsername'];
                                        $newWidgetDbRowTarget[$i] = new WidgetDbRow($name_w, $id_dashboard, $id_metric, $type_w, $n_row, $n_column, $size_rows, $size_columns, $title_w, $defaultParametersTarget[$i]['color_w'], $defaultParametersTarget[$i]['frequency_w'], $defaultParametersTarget[$i]['temporal_range_w'], $defaultParametersTarget[$i]['municipality_w'], $defaultParametersTarget[$i]['infoMessage_w'], $defaultParametersTarget[$i]['link_w'], $defaultParametersTarget[$i]['parameters'], $defaultParametersTarget[$i]['frame_color_w'], $defaultParametersTarget[$i]['udm'], $defaultParametersTarget[$i]['udmPos'], $defaultParametersTarget[$i]['fontSize'], $defaultParametersTarget[$i]['fontColor'], $defaultParametersTarget[$i]['controlsPosition'], $defaultParametersTarget[$i]['showTitle'], $defaultParametersTarget[$i]['controlsVisibility'], $defaultParametersTarget[$i]['zoomFactor'], $defaultParametersTarget[$i]['defaultTab'], $defaultParametersTarget[$i]['zoomControlsColor'], $defaultParametersTarget[$i]['scaleX'], $defaultParametersTarget[$i]['scaleY'], $defaultParametersTarget[$i]['headerFontColor'], $defaultParametersTarget[$i]['styleParameters'], $defaultParametersTarget[$i]['infoJson'], $defaultParametersTarget[$i]['serviceUri'], $defaultParametersTarget[$i]['viewMode'], $defaultParametersTarget[$i]['hospitalList'], $defaultParametersTarget[$i]['notificatorRegistered'], $defaultParametersTarget[$i]['notificatorEnabled'], $defaultParametersTarget[$i]['enableFullscreenTab'], $defaultParametersTarget[$i]['enableFullscreenModal'], $defaultParametersTarget[$i]['fontFamily'], $defaultParametersTarget[$i]['entityJson'], $defaultParametersTarget[$i]['attributeName'], $creator, null, $defaultParametersTarget[$i]['canceller'], $defaultParametersTarget[$i]['lastEditDate'], $defaultParametersTarget[$i]['cancelDate'], $defaultParametersTarget[$i]['actuatorTarget'], $defaultParametersTarget[$i]['actuatorEntity'], $defaultParametersTarget[$i]['actuatorAttribute'], $defaultParametersTarget[$i]['chartColor'], $defaultParametersTarget[$i]['dataLabelsFontSize'], $defaultParametersTarget[$i]['dataLabelsFontColor'], $defaultParametersTarget[$i]['chartLabelsFontSize'], $defaultParametersTarget[$i]['chartLabelsFontColor'], 'no', null, null, '{}', $newWidgetType);

                                        if($hasTargetWidgetFactory[$i] == 'yes')
                                        {
                                            $widgetFactoryClass = ucfirst($type_w) . 'Factory';
                                            $widgetFactory = new $widgetFactoryClass($newWidgetDbRowTarget[$i], null, $widgetTypeDbRow, $mapCenterLat, $mapCenterLng, "target", $widgetWizardSelectedRows, $selection, $mapZoom);
                                            $newWidgetDbRowTarget[$i] = $widgetFactory->completeWidget();
                                        }

                                        //$newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(";
                                        $newInsQuery = "UPDATE Dashboard.Config_widget_dashboard SET";      // PANTALEO

                                        //Query fields
                                        $newQueryFields = "";
                                        //Query values
                                        $newQueryValues = "";

                                        $count = 0;
                                        foreach($newWidgetDbRowTarget[$i] as $key => $value)
                                        {
                                            if($count == 0)
                                            {
                                                //  $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";	//PANTALEO Qr
                                                //  $newQueryValues = returnManagedStringForDb($value);
                                                //  $fieldsAndValuesObj[$key] = $value;
                                            }
                                            else
                                            {
                                                //if ($key != 'n_row') {
                                                //    if ($key == 'name_w' || $key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                if ($key == 'title_w' || $key == 'id_metric' || $key == 'rowParameters' || $key == 'sm_field' || $key == 'wizardRowIds') {
                                                    $newQueryFields = $newQueryFields . " " . $key . " = " . returnManagedStringForDb($value) . ",";    //PANTALEO Qr
                                                    $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                                    $fieldsAndValuesObj[$key] = $value;
                                                } else {
                                                    //  $newQueryFields = $newQueryFields . " n_row = n_row,";
                                                }
                                            }

                                            $count++;
                                        }

                                        $newInsQuery = $newInsQuery . $newQueryFields;
                                        $newInsQuery = $newInsQuery . ") VALUES(";
                                        $newInsQuery = $newInsQuery . $newQueryValues;
                                        $newInsQuery = $newInsQuery . ")";

                                        $insR = mysqli_query($link, $newInsQuery);

                                        if(!$insR)
                                        {
                                            if($addDashboard)
                                            {
                                                return false;
                                            }
                                            else
                                            {
                                                echo "Ko";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }
                                }

                                //Costruzione dell'eventuale combo
                                if($widgetTypeDbRow['comboName'] != null)
                                {
                                    $comboFactoryClass = $widgetTypeDbRow['comboName'];
                                    $comboFactory = new $comboFactoryClass($newWidgetDbRow, $newWidgetDbRowTarget);
                                    if(!$comboFactory->finalizeCombo())
                                    {
                                        if($addDashboard)
                                        {
                                            return false;
                                        }
                                        else
                                        {
                                            echo "Ko";
                                        }
                                    }
                                }

                                //Se si arriva qui si è sicuramente scritto correttamente su DB
                                if($addDashboard)
                                {
                                    return true;
                                }
                                else
                                {
                                    echo "Ok";
                                }
                            }
                        }
                        else
                        {
                            if($addDashboard)
                            {
                                return false;
                            }
                            else
                            {
                                echo "Ko";
                            }
                        }
                    }

                }
            }
            else
            {
                if($addDashboard)
                {
                    return false;
                }
                else
                {
                    echo "Ko";
                }
            }
        }
    }
    //Fine funzione updateWidgetSame()

    function updateWidgets($link, $serviceMapUrlPrefix, $addDashboard)
    {
        $genFileContent = parse_ini_file("../conf/environment.ini");
        $orionContent = parse_ini_file("../conf/orion.ini");
        $orionBaseUrlLocal = $orionContent["orionBaseUrl"][$genFileContent['environment']['value']];

        $defaultColors1 = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        $defaultColors2 = ["#fff5cc", "#ffe0b3", "#ffcccc", "#99ffff", "#99e6ff", "#adebad", "#80ff80"];

        $id_dashboard = $_REQUEST['dashboardId'];
        $dashboardTitle = $_REQUEST['dashboardTitle'];
        $dashboardAuthorName = $_REQUEST['dashboardAuthorName'];
        $dashboardEditor = $_REQUEST['dashboardEditorName'];
        $creator = $_REQUEST['dashboardEditorName'];
        $selection = $_REQUEST['selection'];
        $mapCenterLat = $_REQUEST['mapCenterLat'];
        $mapCenterLng = $_REQUEST['mapCenterLng'];
        $mapZoom = $_REQUEST['mapZoom'];

        $widgetTypeDbRow = NULL;
        $id_metric = NULL;
        $widgetWizardSelectedRows = $_REQUEST['widgetWizardSelectedRows'];
        $newWidgetType = $_REQUEST['widgetType'];
        $actuatorTargetWizard = $_REQUEST['actuatorTargetWizard'];
        $actuatorTargetInstance = $_REQUEST['actuatorTargetInstance'];
        $actuatorEntityName = $_REQUEST['actuatorEntityName'];
        $actuatorValueType = $_REQUEST['actuatorValueType'];
        $actuatorMinBaseValue = $_REQUEST['actuatorMinBaseValue'];
        $actuatorMaxImpulseValue = $_REQUEST['actuatorMaxImpulseValue'];
        $title_w = NULL;
        $n_row = NULL;
        $n_column = NULL;
        $color_widget = NULL;
        $freq_widget = NULL;
        $size_rows = NULL;
        $size_columns = NULL;
        $controlsPosition = NULL;
        $int_temp_widget = NULL;
        $comune_widget = NULL;
        $message_widget = NULL;
        $url_widget = "none";
        $showTitle = NULL;
        $controlsVisibility = NULL;
        $zoomFactor = NULL;
        $scaleX = NULL;
        $scaleY = NULL;
        $inputUdmWidget = NULL;
        $inputUdmPosition = NULL;
        $serviceUri = NULL;
        $viewMode = NULL;
        $hospitalList = NULL;
        $creationDate = NULL;
        $actuatorTarget = NULL;
        $defaultTab = NULL;
        $zoomControlsColor = NULL;
        $headerFontColor = NULL;
        $styleParameters = NULL;
        $showTableFirstCell = NULL;
        $tableFirstCellFontSize = NULL;
        $tableFirstCellFontColor = NULL;
        $rowsLabelsFontSize = NULL;
        $rowsLabelsFontColor = NULL;
        $colsLabelsFontSize = NULL;
        $colsLabelsFontColor = NULL;
        $rowsLabelsBckColor = NULL;
        $colsLabelsBckColor = NULL;
        $tableBorders = NULL;
        $tableBordersColor = NULL;
        $infoJsonObject = NULL;
        $infoJson = NULL;
        $legendFontSize = NULL;
        $legendFontColor = NULL;
        $dataLabelsFontSize = NULL;
        $dataLabelsFontColor = NULL;
        $barsColorsSelect = NULL;
        $barsColors = NULL;
        $chartType = NULL;
        $dataLabelsDistance = NULL;
        $dataLabelsDistance1 = NULL;
        $dataLabelsDistance2 = NULL;
        $dataLabels = NULL;
        $dataLabelsRotation = NULL;
        $xAxisDataset = NULL;
        $lineWidth = NULL;
        $alrLook = NULL;
        $colorsSelect = NULL;
        $colors = NULL;
        $colorsSelect1 = NULL;
        $colors1 = NULL;
        $innerRadius1 = NULL;
        $outerRadius1 = NULL;
        $innerRadius2 = NULL;
        $startAngle = NULL;
        $endAngle = NULL;
        $centerY = NULL;
        $gridLinesWidth = NULL;
        $gridLinesColor = NULL;
        $linesWidth = NULL;
        $alrThrLinesWidth = NULL;
        $clockData = NULL;
        $clockFont = NULL;
        $rectDim = NULL;
        $enableFullscreenTab = 'no';
        $enableFullscreenModal = 'no';
        $fontFamily = "";
        $newOrionEntityJson = NULL;
        $attributeName = NULL;
        $udm = NULL;
        $udmPosition = NULL;
        $nextId = 1;
        $firstFreeRow = NULL;
        $parameters = [];
        $newWidgetDbRowTarget = [];
        $sourceWidgetName = NULL;
        $sourceWidgetRow = NULL;
        $sourceEntityJson = NULL;
        $selectedRowIds = [];

        if ($newWidgetType == NULL) {
            //Caso tipo di widget non selezionato: dashboards fully custom vuote, ritorniamo true se add dashboard ma false negli altri casi
            if ($addDashboard) {
                return true;
            } else {
                echo "Ko";
            }
        } else {
            //Caso tipo di widget selezionato
            $q3 = "SELECT * " .
                "FROM Dashboard.WidgetsIconsMap AS iconsMap " .
                "LEFT JOIN Dashboard.Widgets AS widgets " .
                "ON iconsMap.mainWidget = widgets.id_type_widget " .
                "WHERE iconsMap.icon = '$newWidgetType'";

            $r3 = mysqli_query($link, $q3);

            if ($r3) {
                $widgetTypeDbRow = mysqli_fetch_assoc($r3);
                $widgetCategory = $widgetTypeDbRow['widgetCategory'];

                try
                {
                    $defaultParameters = json_decode($widgetTypeDbRow['defaultParametersMainWidget'], true);
                    $defaultParametersTarget = json_decode($widgetTypeDbRow['defaultParametersTargetWidget'], true);
                }
                catch(Exception $e)
                {

                }

                if($widgetTypeDbRow['mono_multi'] == 'Mono') {
                    if (($widgetCategory == 'actuator') && ($actuatorTargetInstance == 'new')) {

                    } else {   // CASO NON ACTUATOR cioè DATA-VIEWER
                        //Caso widget selezionato di tipo mono con righe selezionate (differenza fra attuatori new e attuatori non new e viewer)
                        //Ramo per i mono SENZA target widget
                        if (($widgetTypeDbRow['targetWidget'] == '') || ($widgetTypeDbRow['targetWidget'] == null)) {
                            //Ne creiamo uno per ogni riga selezionata
                            foreach ($widgetWizardSelectedRows as $selectedRowKey => $selectedRow) {

                            }
                        }
                    }
                }
            }
        }
    }
    //Fine funzione updateWidgets()

    //Inizio del "main"
    switch($operation)
    {
        case "addWidget":
            addWidgets($link, $serviceMapUrlPrefix, false, null);
            break;

        case "updateWidget":
            updateWidgetsSame($link, $serviceMapUrlPrefix, false, null);
            break;

        case "addDashboard":
            $dashboardAuthorName = $_SESSION['loggedUsername'];
            $dashboardTemplate = mysqli_real_escape_string($link, $_POST['dashboardTemplate']);  
            $title = mysqli_real_escape_string($link, $_POST['dashboardTitle']);  
            $widgetType = mysqli_real_escape_string($link, $_REQUEST['widgetType']);  
            $subtitle = "";  
            $color = "rgba(51, 204, 255, 1)";  //E' header color
            $background = "#FFFFFF";  
            $externalColor = "#FFFFFF";  
            $nCols = 24;  
            $headerFontColor = "white";  
            $headerFontSize = 28;
            $viewMode = "fixed";
            $addLogo = false;
            $logoLink = null;
            $filename = null;
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
            
            $q = "INSERT INTO Dashboard.Config_dashboard
                (name_dashboard, title_header, subtitle_header, color_header,
                width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date, lastUsedColors, organizations) 
                VALUES ('$title', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP, '$lastUsedColorsJson', '$org')";
                
            $r = mysqli_query($link, $q);
                
            if($r) 
            {
                $_REQUEST['dashboardId'] = mysqli_insert_id($link);
                
                $resultAddWidget = addWidgets($link, $serviceMapUrlPrefix, true);
                
                if($resultAddWidget)
                {
                    $result['detail'] = "Ok";
                    $result['newDashId'] = $_REQUEST['dashboardId'];
                }
                else
                {
                    $result['detail'] = "CreateWidgetKo";       
                }
                
                //Salvataggio su API ownership
                if(isset($_SESSION['refreshToken'])) 
                {
                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                    $accessToken = $tkn->access_token;
                    $_SESSION['refreshToken'] = $tkn->refresh_token;
                    
                    $callBody = ["elementId" => $_REQUEST['dashboardId'], "elementType" => "DashboardID", "elementName" => $title];
                    
                    $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

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
                        $callResult = @file_get_contents($apiUrl, false, $context);
                    }
                    catch (Exception $ex) 
                    {
                        //Non facciamo niente di specifico in caso di mancata risposta dell'host
                    }
                }
            }
            else
            {
                $result['detail'] = "CreateDashboardKo";
            }
            echo json_encode($result);
            break;
    }
    