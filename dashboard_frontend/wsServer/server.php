<?php
    require_once('../phpWebsockets/websockets.php');
    
    date_default_timezone_set("Europe/Rome");
    error_reporting(E_ERROR | E_NOTICE);
    
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
        
        protected function process($user, $message) 
        {
            //$this->send($user, $message);
            /*foreach($this->users as $receiver)
            {
                $this->send($receiver, $message);
            }*/
            
            try
            {
                $msgObj = json_decode($message);
                $msgType = $msgObj->msgType;
                $response = [];
                $response['msgType'] = $msgObj->msgType;
                
                //$file = fopen("dashboardLog.txt", "a");
                //fwrite($file, "Message: \n" . $msgType . "\n");        
                
                switch($msgType)
                {
                    case "AddEditMetric":
                        try
                        {
                            $link = mysqli_connect($this->host, $this->username, $this->password);
                            mysqli_select_db($link, $this->dbname);
                            $link->set_charset("utf8");
                            
                            $q0 = "DELETE FROM Dashboard.NodeRedMetrics WHERE NodeRedMetrics.name = '$msgObj->metricName' AND NodeRedMetrics.user = '$msgObj->user'";
                            
                            //$file = fopen("dashboardLog.txt", "w");
                            //fwrite($file, "Query: " . $q0 . "\n");
                            $r0 = mysqli_query($link, $q0);
                            
                            if($r0)
                            {
                                $response['result'] = 'Ok';
                                if($msgObj->metricId != null)
                                {
                                    $q = "UPDATE Dashboard.NodeRedMetrics(name, metricType, user, shortDesc, fullDesc) " .
                                         "SET name = '$msgObj->metricName', metricType = '$msgObj->metricType', user = '$msgObj->user', shortDesc = '$msgObj->metricShortDesc', fullDesc = '$msgObj->metricFullDesc'" .
                                         "WHERE NodeRedMetrics.id = $msgObj->metricId";

                                    $r = mysqli_query($link, $q);
                                    $metricId = $msgObj->metricId; 
                                }
                                else
                                {
                                    $q = "INSERT INTO Dashboard.NodeRedMetrics(name, metricType, user, shortDesc, fullDesc) " .
                                         "VALUES ('$msgObj->metricName', '$msgObj->metricType', '$msgObj->user', '$msgObj->metricShortDesc', '$msgObj->metricFullDesc') " .
                                         "ON DUPLICATE KEY UPDATE metricType='$msgObj->metricType', shortDesc='$msgObj->metricShortDesc', fullDesc='$msgObj->metricFullDesc'";  

                                    $r = mysqli_query($link, $q);
                                    $metricId = mysqli_insert_id($link); 
                                }
                                
                                if($r) 
                                {
                                    $q2 = "SELECT count(*) AS dataCount FROM Dashboard.Data WHERE IdMetric_data = '$msgObj->metricName'";
                                    $r2 = mysqli_query($link, $q2);
                                    
                                    if($r2)
                                    {
                                        if(mysqli_fetch_assoc($r2)['dataCount'] > 0)
                                        {
                                            $response['result'] = 'Ok';
                                            $response['metricId'] = $metricId;
                                        }
                                        else 
                                        {
                                            $computationDate = date('Y-m-d H:i:s');
                                            $metricStartValue = $msgObj->startValue;
                                            $newMetricName = $msgObj->metricName;
                                            
                                            switch($msgObj->metricType)
                                            {
                                                case "Intero": case "Float":
                                                    $dataField = 'value_num';
                                                    break;
                                                
                                                case "Percentuale":
                                                    $dataField = 'value_perc1';
                                                    break;
                                                
                                                case "Testuale": case "webContent":
                                                    $dataField = 'value_text';
                                                    break;
                                                
                                                case "Series":
                                                    $dataField = 'series';
                                                    break;
                                            }
                                            
                                            $q3 = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, " . $dataField . ") VALUES('$newMetricName', '$computationDate', '$metricStartValue')";
                                            
                                            //$file = fopen("C:\dashDebug.txt", "w");
                                            //fwrite($file, "Q3: " . $q3 . "\n");
                                            
                                            $r3 = mysqli_query($link, $q3);
                                            
                                            $response['result'] = 'Ok';
                                            $response['metricId'] = $metricId;
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ok';
                                        $response['metricId'] = $metricId;
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
                        try
                        {
                            $link = mysqli_connect($this->host, $this->username, $this->password);
                            mysqli_select_db($link, $this->dbname);
                            $link->set_charset("utf8");
                            
                            $computationDate = date("Y-m-d H:i:s");
                            
                            //$file = fopen("dashboardLog.txt", "w");
                            
                            switch($msgObj->metricType)
                            {
                                case "Float":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_num) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }
                                    break;
                                
                                case "Intero":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_num) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }
                                    break;
                                
                                case "Percentuale":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_perc1) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }
                                    break;
                                
                                case "Series":
                                    $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, series) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }
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
                                    }
                                    else
                                    {
                                            $q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_text) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    }
								
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }
                                    break;
                                    
                                case "geoJson":
                                    $response['result'] = 'Ok';
                                    foreach($this->users as $key => $singleUser) 
                                    {
                                        if($singleUser->userType == "widgetInstance")
                                        {
                                            $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                            $this->send($singleUser, json_encode($newMessage));
                                        }
                                    }
                                    
                                    
                                    /*$q = "INSERT INTO Dashboard.Data(IdMetric_data, computationDate, value_text) VALUES('$msgObj->metricName', '$computationDate', '$msgObj->newValue')";
                                    $r = mysqli_query($link, $q);
                                    if($r)
                                    {
                                        $response['result'] = 'Ok';
                                        foreach($this->users as $key => $singleUser) 
                                        {
                                            if($singleUser->userType == "widgetInstance")
                                            {
                                                $newMessage = ['msgType' => 'newNRMetricData', 'metricName' => $msgObj->metricName, 'newValue' => $msgObj->newValue];
                                                $this->send($singleUser, json_encode($newMessage));
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $response['result'] = 'Ko';
                                    }*/
                                    
                                    break;
                            }
                        }
                        catch(Exception $ex) 
                        {
                            $response['result'] = 'Ko';
                        }
                        mysqli_close($link);
                        break;
                    
                    case "ClientWidgetRegistration":
                        $user->userType = $msgObj->userType;
                        $user->metricName = $msgObj->metricName;
                        $response['result'] = 'Ok';
                        break;
                        
                    case "DelMetric":
                        $link = mysqli_connect($this->host, $this->username, $this->password);
                        mysqli_select_db($link, $this->dbname);
                        $link->set_charset("utf8");
                        mysqli_autocommit($link, FALSE);
                        mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                        
                        $q0 = "DELETE FROM Dashboard.NodeRedMetrics WHERE NodeRedMetrics.name = '$msgObj->metricName' AND NodeRedMetrics.user = '$msgObj->user'";

                        //$file = fopen("dashboardLog.txt", "w");
                        //fwrite($file, "Query: " . $q0 . "\n");
                        $r0 = mysqli_query($link, $q0);

                        if($r0)
                        {
                            //BUG FOND - APP POSSIBILE AGGIUNGI A DATA ANCHE LO USERNAME, SENNO' A METRICHE OMONIME CANCELLI TUTTI I DATI DI TUTTE LE METRICHE OMONIME!!! AND NodeRedMetrics.user = '$msgObj->user'";
                            $q1 = "DELETE FROM Dashboard.Data WHERE Data.IdMetric_data = '$msgObj->metricName'"; 
                            $r1 = mysqli_query($link, $q1);
                            
                            if($r1)
                            {
                                $q2 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Config_widget_dashboard.id_metric = '$msgObj->metricName'"; 
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
            //$file = fopen("dashboardLog.txt", "a");
            //fwrite($file, "User connected: " . $user->id . "\n");
        }

        protected function closed($user) 
        {
          
        }
    }

  $server = new wsServer();

  try 
  {
    $server->run();
  }
  catch(Exception $e) 
  {
    $server->stdout($e->getMessage());
  }
    

