<?php
    require_once('../phpWebsockets/websockets.php');
    include '../controllers/WidgetDbRow.php'; 
    require '../sso/autoload.php';
    use Jumbojett\OpenIDConnectClient;
    
    date_default_timezone_set("Europe/Rome");
    //error_reporting(E_ERROR | E_NOTICE);
    error_reporting(E_ALL);
    
    class wsServer extends WebSocketServer 
    {
        protected $envFileContent = null;
        protected $genFileContent = null;  
        protected $dbFileContent = null;
        protected $wsServerContent = null;
        protected $activeEnv = null;
        protected $host = null; 
        protected $username = null; 
        protected $password = null; 
        protected $dbname = null; 
        protected $serverAddress = null;
        protected $serverPort = null;
        protected $clientWidgets = [];
        
        function __construct($bufferLength = 2048) 
        {
            $this->envFileContent = parse_ini_file("../conf/environment.ini");
            $this->activeEnv = $this->envFileContent["environment"]["value"];
            $this->genFileContent = parse_ini_file("../conf/general.ini");
            $this->dbFileContent = parse_ini_file("../conf/database.ini");
            
            $this->host = $this->genFileContent["host"][$this->activeEnv];
            $this->username = $this->dbFileContent["username"][$this->activeEnv];
            $this->password = $this->dbFileContent["password"][$this->activeEnv];
            $this->dbname = $this->dbFileContent["dbname"][$this->activeEnv];
            
            $this->wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
            $this->serverAddress = $this->wsServerContent["wsServerAddress"][$this->activeEnv];
            $this->serverPort = $this->wsServerContent["wsServerPort"][$this->activeEnv];
            
            parent::__construct($this->serverAddress, $this->serverPort, $bufferLength);
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
        
        function addWidget($link, $dashboardTitle, $username, $widgetType, $metricName, $metricType, $appId, $flowId, $nodeId, $widgetTitle)
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
                            return ["result" => true, "widgetUniqueName" => null];
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
                                                        $newQueryValues = $this->returnManagedStringForDb($value);
                                                    }
                                                    else
                                                    {
                                                        $newQueryFields = $newQueryFields . ", " . $key;
                                                        $newQueryValues = $newQueryValues . ", " . $this->returnManagedStringForDb($value);
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
                                                    return ["result" => false, "widgetUniqueName" => null];
                                                }
                                                else
                                                {
                                                    //Cancellazione widget da dashboard vecchia
                                                    $qDel = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w = '$currentWidgetUniqueId'";
                                                    $rDel = mysqli_query($link, $qDel);
                                                    
                                                    if(!$rDel)
                                                    {
                                                        return ["result" => false, "widgetUniqueName" => null];
                                                    }
                                                }
                                            }

                                            //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                                            return ["result" => true, "widgetUniqueName" => $name_w];
                                        }
                                        else
                                        {
                                            //CASO WIDGET COMBO PER ORA NON SI USA
                                        }
                                    }
                                }
                                else
                                {
                                    return ["result" => false, "widgetUniqueName" => null];
                                }
                            }
                            else
                            {
                                return ["result" => false, "widgetUniqueName" => null];
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
                                                $newQueryValues = $this->returnManagedStringForDb($value);
                                            }
                                            else
                                            {
                                                $newQueryFields = $newQueryFields . ", " . $key;
                                                $newQueryValues = $newQueryValues . ", " . $this->returnManagedStringForDb($value);
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
                                            return ["result" => false, "widgetUniqueName" => null];
                                        }
                                    }

                                    //Se si esce dal ciclo e si arriva qui si è sicuramente scritto correttamente su DB
                                    return ["result" => true, "widgetUniqueName" => $name_w];
                                }
                                else
                                {
                                    //CASO WIDGET COMBO PER ORA NON SI USA
                                }
                            }
                        }
                        else
                        {
                            return ["result" => false, "widgetUniqueName" => null];
                        }
                    }
                    else
                    {
                        return ["result" => false, "widgetUniqueName" => null];
                    }
                }
            }
            else
            {
                return ["result" => false, "widgetUniqueName" => null];
            }
        }
        
        protected function process($user, $message) 
        {
            try
            {
                $msgObj = json_decode($message);
                $msgType = $msgObj->msgType;
                $response = [];
                $response['msgType'] = $msgObj->msgType;      
                
                switch($msgType)
                {
                    case "AddEditMetric":
                        try
                        {
                            $link = mysqli_connect($this->host, $this->username, $this->password);
                            mysqli_select_db($link, $this->dbname);
                            $link->set_charset("utf8");
                            
                            $msgObj->dashboardTitle = urldecode($msgObj->dashboardTitle);
                            
                            $q0 = "DELETE FROM Dashboard.NodeRedMetrics WHERE NodeRedMetrics.name = '$msgObj->metricName' AND NodeRedMetrics.metricType = '$msgObj->metricType' AND NodeRedMetrics.user = '$msgObj->user' AND appId = '$msgObj->appId' AND flowId = '$msgObj->flowId'";
                            $r0 = mysqli_query($link, $q0);
                            
                            if($r0)
                            {
                                $q = "INSERT INTO Dashboard.NodeRedMetrics(name, metricType, user, shortDesc, fullDesc, appId, flowId, flowName, nodeId, httpRoot) " .
                                     "VALUES ('$msgObj->metricName', '$msgObj->metricType', '$msgObj->user', '$msgObj->metricName', '$msgObj->metricName', '$msgObj->appId', '$msgObj->flowId', '$msgObj->flowName', '$msgObj->nodeId', '$msgObj->httpRoot') ";
                                    
                                $r = mysqli_query($link, $q);
                                
                                if($r) 
                                {
                                    $computationDate = date('Y-m-d H:i:s');
                                    $newMetricName = $msgObj->metricName;

                                    switch($msgObj->metricType)
                                    {
                                        case "Intero": case "Float":
                                            $dataField = 'value_num';
                                            $metricStartValue = 0;
                                            break;

                                        case "Percentuale":
                                            $dataField = 'value_perc1';
                                            $metricStartValue = 0;
                                            break;

                                        case "Testuale": case "webContent":
                                            $dataField = 'value_text';
                                            $metricStartValue = '0';
                                            break;

                                        case "Series":
                                            $dataField = 'series';
                                            $metricStartValue = '0';
                                            break;
                                    }
                                    $qDataEx = "SELECT * FROM Dashboard.Data WHERE IdMetric_data = '$msgObj->metricName'";
                                    $rDataEx = mysqli_query($link, $qDataEx);
                                    
                                    if(mysqli_num_rows($rDataEx) == 0)
                                    {
                                        $q3 = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, " . $dataField . ", appId, flowId, nrMetricType, nrUsername) VALUES('$newMetricName', '$computationDate', '$metricStartValue', '$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                        $r3 = mysqli_query($link, $q3);
                                    }
                                    
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

                                                $insDashQ = "INSERT INTO Dashboard.Config_dashboard(Id, name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date, lastUsedColors, organizations) " .
                                                            "VALUES (NULL, '$title', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP, '$lastUsedColorsJson', '$org')";
                                                
                                                $insDashR = mysqli_query($link, $insDashQ);

                                                if($insDashR)
                                                {
                                                    $newDashId = mysqli_insert_id($link);
                                                    
                                                    //Salvataggio su API ownership
                                                    $oidc = new OpenIDConnectClient('https://www.snap4city.org', 'php-dashboard-builder', '0afa15e8-87b9-4830-a60c-5fd4da78a9c4');
                                                    $oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));

                                                    $callBody = ["elementId" => $newDashId, "elementType" => "DashboardID", "elementName" => $title];

                                                    $apiUrl = "http://192.168.0.207/ownership-api/v1/register/?accessToken=" . $msgObj->accessToken;

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
                                                        
                                                        $addWidgetRes = $this->addWidget($link, $msgObj->dashboardTitle, $msgObj->user, $msgObj->widgetType, $msgObj->metricName, $msgObj->metricType, $msgObj->appId, $msgObj->flowId, $msgObj->nodeId, $msgObj->widgetTitle);
                                                        if($addWidgetRes["result"] == true)
                                                        {
                                                            $response['result'] = 'Ok';
                                                        }
                                                        else
                                                        {
                                                            $response['result'] = 'Ko';
                                                        }
                                                    }
                                                    catch (Exception $ex) 
                                                    {
                                                        $this->stderr("Ownership call crashed");
                                                        $response['result'] = 'Ko';
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $addWidgetRes = $this->addWidget($link, $msgObj->dashboardTitle, $msgObj->user, $msgObj->widgetType, $msgObj->metricName, $msgObj->metricType, $msgObj->appId, $msgObj->flowId, $msgObj->nodeId, $msgObj->widgetTitle);
                                                if($addWidgetRes["result"] == true)
                                                {
                                                    $response['result'] = 'Ok';
                                                }
                                                else
                                                {
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
                                }
                                else
                                {
                                    $response['result'] = 'Ko';
                                }
                            }
                            else
                            {
                                $response['result'] = 'Ko';
                            }
                        } 
                        catch(Exception $ex) 
                        {
                            $response['result'] = 'Ko';
                        }
                        mysqli_close($link);
                        break;
                    
                    case "AddMetricData":
                        //Nuova versione
                        /*if(array_key_exists($msgObj->metricName, $this->clientWidgets))
                        {
                            foreach($this->clientWidgets[$msgObj->metricName] as $key => $singleUser) 
                            {
                                $this->stdout("Metric: " . $msgObj->metricName . " - Recipient widget: " . $singleUser->widgetUniqueName);
                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                $this->send($singleUser, json_encode($newMessage));
                            }
                        }*/
                        
                        //Vecchia versione
                        foreach($this->users as $key => $singleUser) 
                        {
                            if($singleUser->userType == "widgetInstance")
                            {
                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                $this->send($singleUser, json_encode($newMessage));
                            }
                        }
                        
                        try
                        {
                            /*$link = mysqli_connect($this->host, $this->username, $this->password);
                            mysqli_select_db($link, $this->dbname);
                            $link->set_charset("utf8");*/
                            
                            $computationDate = date("Y-m-d H:i:s");
                            
                            switch($msgObj->metricType)
                            {
                                case "Float":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_num, appId, flowId, nrMetricType, nrUsername) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue', '$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                    /*$r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    break;
                                
                                case "Intero":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_num, appId, flowId, nrMetricType, nrUsername) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue', '$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                    //$r = mysqli_query($link, $q);
                                    /*if($r)
                                    {
                                        $response['result'] = 'Ok';
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    break;
                                
                                case "Percentuale":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_perc1, appId, flowId, nrMetricType, nrUsername) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue','$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                    //$r = mysqli_query($link, $q);
                                    /*if($r)
                                    {
                                        $response['result'] = 'Ok';
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    break;
                                
                                case "Series":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, series, appId, flowId, nrMetricType, nrUsername) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue', '$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                    /*$r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    break;
                                
                                case "Testuale": case "webContent":
                                    if(strpos($msgObj->newValue, 'OperatorEvent') !== false) 
                                    {
                                        $parsedNewValue = $msgObj->newValue;

                                        $personNumber = $parsedNewValue->personNumber;
                                        $lat = $parsedNewValue->lat;
                                        $lng = $parsedNewValue->lng;
                                        $codeColor = $parsedNewValue->codeColor;
                                        $user = $parsedNewValue->user;

                                        $q = "INSERT INTO Dashboard.OperatorEvents(time, personNumber, lat, lng, codeColor, user) VALUES('$computationDate', '$personNumber', '$lat', '$lng', '$codeColor', '$user')";
                                        
                                        $link = mysqli_connect($this->host, $this->username, $this->password);
                                        mysqli_select_db($link, $this->dbname);
                                        $link->set_charset("utf8");
                                        
                                        $r = mysqli_query($link, $q);
                                        if($r)
                                        {
                                            $response['result'] = 'Ok';
                                        }
                                        else
                                        {
                                            $response['result'] = 'Ko';
                                        }
                                    }
                                    else
                                    {
                                        $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_text, appId, flowId, nrMetricType, nrUsername) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue', '$msgObj->appId', '$msgObj->flowId', '$msgObj->metricType', '$msgObj->user')";
                                    }
								
                                    /*$r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    break;
                                    
                                case "geoJson":
                                    $response['result'] = 'Ok';
                                    break;
                            }
                        }
                        catch(Exception $ex) 
                        {
                            $response['result'] = 'Ko';
                        }
                        //mysqli_close($link);
                        break;
                    
                    case "ClientWidgetRegistration":
                        $user->userType = $msgObj->userType;
                        $user->metricName = $msgObj->metricName;
                        $user->widgetUniqueName = $msgObj->widgetUniqueName;
                        
                        if($msgObj->widgetUniqueName != null)
                        {
                            if(array_key_exists($user->metricName, $this->clientWidgets))
                            {
                                array_push($this->clientWidgets[$user->metricName], $user);
                                //$this->clientWidgets[$user->metricName] = array_unique($this->clientWidgets[$user->metricName]);
                            }
                            else
                            {
                                $this->clientWidgets[$user->metricName] = [];
                                array_push($this->clientWidgets[$user->metricName], $user);
                            }
                        }
                        
                        $response['result'] = 'Ok';
                        break;
                        
                    case "DelMetric":
                        $link = mysqli_connect($this->host, $this->username, $this->password);
                        mysqli_select_db($link, $this->dbname);
                        $link->set_charset("utf8");
                        
                        $qMulti = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE nodeId = '$msgObj->nodeId'";
                        
                        $rMulti = mysqli_query($link, $qMulti);
                        
                        if($rMulti)
                        {
                            if(mysqli_num_rows($rMulti) > 1)
                            {
                                //Se ci son più widget cancella solo il widget ma lascia metrica e dati
                                $q2 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE nodeId = '$msgObj->nodeId'"; 
                                $r2 = mysqli_query($link, $q2);

                                if($r2)
                                {
                                    $response['result'] = 'Ok';
                                }
                                else 
                                {
                                    $response['result'] = 'Ko';
                                }
                            }
                            else
                            {
                                //Se rimane un solo widget cancella widget, metrica e dati
                                mysqli_autocommit($link, FALSE);
                                mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);

                                $q0 = "DELETE FROM Dashboard.NodeRedMetrics WHERE NodeRedMetrics.name = '$msgObj->metricName' AND NodeRedMetrics.metricType = '$msgObj->metricType' AND NodeRedMetrics.user = '$msgObj->user' AND NodeRedMetrics.appId = '$msgObj->appId' AND NodeRedMetrics.flowId = '$msgObj->flowId'";
                                $r0 = mysqli_query($link, $q0);

                                if($r0)
                                {
                                    $q1 = "DELETE FROM Dashboard.Data WHERE Data.IdMetric_data = '$msgObj->metricName' AND appId = '$msgObj->appId' AND flowId = '$msgObj->flowId' AND nrMetricType = '$msgObj->metricType' AND nrUsername = '$msgObj->user'"; 
                                    $r1 = mysqli_query($link, $q1);

                                    if($r1)
                                    {
                                        $q2 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE nodeId = '$msgObj->nodeId'"; 
                                        $r2 = mysqli_query($link, $q2);

                                        if($r2)
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
                                    mysqli_autocommit($link, TRUE);
                                }
                            }
                        }
                        else
                        {
                            $response['result'] = 'Ko';
                        }
                        break;
                    
                    default:
                        
                        break;
                }
                
            } 
            catch(Exception $ex) 
            {
                $response['result'] = 'Ko';
            }
            
            $this->send($user, json_encode($response));
        }

        protected function connected($user) 
        {
            
        }

        protected function closed($user) 
        {
            if(property_exists($user, "widgetUniqueName"))
            {
                if($user->widgetUniqueName != null)
                {
                    //$this->clientWidgets[$user->metricName] = array_diff($this->clientWidgets[$user->metricName], [$user]);
                    
                    $unsetKey = null;
                    
                    foreach($this->clientWidgets[$user->metricName] as $key => $singleUser) 
                    {
                        if($singleUser->widgetUniqueName == $user->widgetUniqueName)
                        {
                            $unsetKey = $key;
                        }
                    }
                    
                    unset($this->clientWidgets[$user->metricName][$unsetKey]);
                }
            }
        }
    }

  $server = new wsServer();

  try 
  {
    $server->run();
  }
  catch(Exception $e) 
  {
    $server->stdout("Eccezione: " . $e->getMessage());
  }
    

