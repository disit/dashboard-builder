<?php
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
session_start();
if (isset($_SESSION['loggedUsername'])) {
                    /*****************/
                    if(isset($_SESSION['refreshToken'])) {
                        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
                        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                        $accessToken = $tkn->access_token;
                        $_SESSION['refreshToken'] = $tkn->refresh_token;
                    
    //error_reporting(E_ERROR);
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ALL);
    ini_set('display_errors', 1);
    error_reporting(-1);
    mysqli_select_db($link, $dbname);
    if (isset($_SESSION['loggedRole'])) {
        $role_session_active = $_SESSION['loggedRole'];
        
        
        if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin") || ($role_session_active == "AreaManager") || ($role_session_active == "Manager")) {
        //if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin") || ($role_session_active == "Manager")) {
            //
            $ip_disc = 'disces.snap4city.org';
            $vars                 = "";
            $url_ownership        = "";
            $payload_own          = "";
            $broker               = "";
            $organization         = "";
            $device_id            = "";
            $username_own         = $_SESSION['loggedUsername'];
            $graph_uri_sparql_uri = "192.168.0.206";
            $files1               = "";
            $accessToken          = "";
            $owner_iot            = "";
            /* @var $value type */
            if (isset($_REQUEST['service'])) {
                $value = $_REQUEST['service'];
            } else {
                $value = "";
            }
            if (isset($_REQUEST['value'])) {
                $value_name = $_REQUEST['value'];
            } else {
                $value_name = "";
            }
            if (isset($_REQUEST['type'])) {
                $type = $_REQUEST['type'];
            }else{
                $type = "";
            }
            if (isset($_REQUEST['data_get_instances'])) {
                $data_get_instances = $_REQUEST['data_get_instances'];
            } else {
                $data_get_instances = '';
            }
            if (isset($_REQUEST['id_row'])) {
                $id_row = $_REQUEST['id_row'];
            } else {
                $id_row = "";
            }
            if (isset($_REQUEST['data_source'])) {
                $data_source = $_REQUEST['data_source'];
            } else {
                $data_source = "";
            }
            
            if ($data_source == 'IoT') {
                if (strpos($value_name, ':') !== false) {
                    $pieces = explode(":", $value_name);
                    if (count($pieces) > 1) {
                        $organization = $pieces[0];
                        $broker       = $pieces[1];
                        $device_id    = $pieces[2];
                    }
                } else if (strpos($value_name, '/') !== false) {
                    $pieces = explode("/", $value_name);
                    if (count($pieces) > 1) {
                        //$organization = $pieces[0];
                        $broker    = $pieces[1];
                        $device_id = $pieces[2];
                    }
                } else {
                    $pieces0 = explode("iot/", $data_get_instances);
                    if (count($pieces0) > 1) {
                    $r1      = $pieces0[1];
                    $pieces  = explode("/", $r1);
                    if (count($pieces) > 1) {
                        $organization = $pieces[1];
                        $broker       = $pieces[0];
                        $device_id    = $pieces[2];
                    }
                  }
                }
            }
            //
            //
            $list                        = array();
            $list['process_name_ST']     = '';
            $list['KB_Ip']               = '';
            $list['Graph_Uri']           = '';
            $list['process_path']        = '';
            $list['process_type']        = '';
            $list['licence']             = '';
            $list['webpage']             = '';
            $list['telephone']           = '';
            $list['phoenix_table']       = '';
            $list['mail']                = '';
            $list['owner']               = '';
            $list['disces_ip']           = '';
            $list['disces_data']         = '';
            $list['jobName']             = '';
            $list['jobGroup']            = '';
            $list['ip_disc']             = '';
            $list['dataSource']          = '';
            $list['url_ownership']     = $url_ownership;
            $list['ownership_content']   = $vars;
            $list['organization']        = $organization;
            $list['broker']              = $broker;
            $list['device_id']           = $device_id;
            $list['HealthinessCriteria'] = '';
            $list['period']              = '';
            
            $currentDir      = getcwd();
            $newDir          = explode('management', $currentDir);
            $uploadDirectory = "img/sensorImages/";
            $currentDir2     = $newDir[0];
            $id_img          = $id_row;
            
            
            
            if ($id_img !== "") {
                
                if (file_exists($currentDir2 . $uploadDirectory . $id_img)) {
                    $files  = scandir($currentDir2 . $uploadDirectory . $id_img);
                    $files1 = $files[2];
                }
            } else {
                $files1 = "";
            }
            $list['icon'] = $files1;
            
            //
            /*** Types ****/
            switch ($type) {
                case "Sensor";
                case "Sensor-Actuator":
                    //
                    $currentDir      = getcwd();
                    $newDir          = explode('management', $currentDir);
                    $uploadDirectory = "img/sensorImages/";
                    $currentDir2     = $newDir[0];
                    $id_img          = $id_row;
                    
                    
                    
                    if ($id_img !== "") {
                        
                        if (file_exists($currentDir2 . $uploadDirectory . $id_img)) {
                            $files  = scandir($currentDir2 . $uploadDirectory . $id_img);
                            $files1 = $files[2];
                        }
                    } else {
                        $files1 = "";
                    }
                    

                    //NUOVO PEZZO FILES//
                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
                    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                    $accessToken = $tkn->access_token;
                    $_SESSION['refreshToken'] = $tkn->refresh_token;

                    $options = array(
                        'http' => array(
                            'method' => 'GET',
                            'timeout' => 30,
                            'ignore_errors' => true,
                            'header' => "Authorization: Bearer $accessToken\r\n"
                        )
                    );

                    $context = stream_context_create($options);
                    
                    if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
                            echo json_encode($list);
                            die();
                        }else{
                                $payload = file_get_contents($value, false, $context);
                                $result = json_decode($payload);
                                $error = json_last_error();
                                    
                                
                                 switch (json_last_error()) {
                                            case JSON_ERROR_NONE:
                                                if (property_exists($result, 'healthiness')) {
                                                                    $healthiness = $result->healthiness;
                                                                    if (property_exists($result, 'Service')) {
                                                                        $serviceUri = $result->Service->features[0]->properties->serviceUri;
                                                                    } else {
                                                                        $serviceUri = $result->Sensor->features[0]->properties->serviceUri;
                                                                    }
                                                        } else{
                                                            $serviceUri = "";
                                                            $healthiness = "";
                                                        }
                                                //
                                            break;
                                            case JSON_ERROR_DEPTH:
                                                $serviceUri = "";
                                                $healthiness = "";
                                            break;
                                            case JSON_ERROR_STATE_MISMATCH:
                                                $serviceUri = "";
                                                $healthiness = "";
                                            break;
                                            case JSON_ERROR_CTRL_CHAR:
                                                 $serviceUri = "";
                                                 $healthiness = "";
                                            break;
                                            case JSON_ERROR_SYNTAX:
                                                 $serviceUri = "";
                                                 $healthiness = "";
                                            break;
                                            case JSON_ERROR_UTF8:
                                                 $serviceUri = "";
                                                 $healthiness = "";
                                            break;
                                            default:
                                                 $serviceUri = "";
                                                 $healthiness = "";
                                            break;
                                        }
                                //var_dump($result, $error === JSON_ERROR_UTF8);
                                /*
                                    if (property_exists($result, 'healthiness')) {
                                            $healthiness = $result->healthiness;
                                            if (property_exists($result, 'Service')) {
                                                $serviceUri = $result->Service->features[0]->properties->serviceUri;
                                            } else {
                                                $serviceUri = $result->Sensor->features[0]->properties->serviceUri;
                                            }
                                } else{
                                    $serviceUri = "";
                                    $healthiness = "";
                                }
                                */

                        }
                    $serviceUri_arr = explode("/", $serviceUri);
                    $n              = count($serviceUri_arr);
                    $value          = $serviceUri_arr[$n - 1];
                    $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                    mysqli_set_charset($link, 'utf8');
                    mysqli_select_db($link, $dbname_processes);
                    $process      = '';
                    $process_list = array();
                    $query0       = "SELECT devices.device_name AS 'Process_ST', devices.process AS 'process', devices.HealthinessCriteria AS 'HealthinessCriteria', devices.Period AS 'Period', process_manager_graph.KB_Ip AS 'KB_Ip', process_manager_graph.Graph_Uri AS 'Graph_Uri', process_manager_graph.phoenix_table AS 'phoenix_table', process_manager_graph.process_path AS 'process_path', process_manager_graph.DISCES_Ip AS 'DISCES_Ip', process_manager_responsible.responsible AS 'responsible', process_manager_responsible.process_type AS 'process_type', process_manager_responsible.licence AS 'licence', process_manager_responsible.webpage AS 'webpage', process_manager_responsible.telephone AS 'telephone', process_manager_responsible.mail AS 'mail', process_manager_graph.HealthinessCriteria AS 'HealthinessCriteria_graph', process_manager_graph.Period AS 'Period_graph' FROM devices, process_manager_graph, process_manager_responsible WHERE process_manager_graph.Process_name=devices.process AND process_manager_responsible.process_name=devices.process AND devices.device_name ='" . $value_name . "' group by Process_ST";
                    $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                    //echo($query0);
                    //
                    $total0       = $result0->num_rows;
                    $process_st0  = '';
                    $process_path = '';
                    $graph_uri    = "";
                    $health_c     = "";
                    $period       = "";
                    
                    while ($row0 = mysqli_fetch_assoc($result0)) {
                        /***/
                        if (($row0['Graph_Uri'] !== null) || ($row0['Graph_Uri'] !== 'undefined')) {
                            $graph_uri = $row0['Graph_Uri'];
                        } else {
                            $graph_uri = "";
                        }
                        if (($row0['HealthinessCriteria'] == null) || ($row0['HealthinessCriteria'] == '')) {
                            $health_c = $row0['HealthinessCriteria_graph'];
                        } else {
                            $health_c = $row0['HealthinessCriteria'];
                        }
                        if (($row0['Period'] == null) || ($row0['Period'] == '')) {
                            $period = $row0['Period_graph'];
                        } else {
                            $period = $row0['Period'];
                        }
                        /***/
                        $listFile0    = array(
                            "process_name_ST" => $row0['Process_ST'],
                            "KB_Ip" => $row0['KB_Ip'],
                            "Graph_Uri" => $graph_uri,
                            "phoenix_table" => $row0['phoenix_table'],
                            "process_path" => $row0['process_path'],
                            "process_type" => $row0['process_type'],
                            "licence" => $row0['licence'],
                            "webpage" => $row0['webpage'],
                            "telephone" => $row0['telephone'],
                            "mail" => $row0['mail'],
                            "owner" => $row0['responsible'],
                            "Disces_Ip" => $row0['DISCES_Ip']
                        );
                        $process_path = $row0['process_path'];
                        array_push($process_list, $listFile0);
                    }
                    
                    $link_disces = mysqli_connect($disces_host, $disces_username, $disces_password) or die("failed to connect to server Disces!!");
                    mysqli_set_charset($link_disces, 'utf8');
                    mysqli_select_db($link_disces, $disces_dbname);
                    if ($data_source !== 'IoT') {
                        if ($process_path == '') {
                            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA=''";
                        } else {
                            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
                        }
                        $result1 = mysqli_query($link_disces, $query1) or die(mysqli_error($link_disces));
                        $total1 = $result1->num_rows;
                        $jobn   = '';
                        $jobg   = '';
                        if ($total1 > 0) {
                            while ($row1 = mysqli_fetch_assoc($result1)) {
                                $jobn = $row1['JOB_NAME'];
                                $jobg = $row1['JOB_GROUP'];
                            }
                        }
                    } else {
                        $total1 = 0;
                        $jobn   = '';
                        $jobg   = '';
                    }
                    //
                    $list['healthiness'] = $healthiness;
                    /*****
                    if (property_exists($result, 'realtime')) {
                        $list['realtime'] = $result->realtime;
                    }else{
                        $list['realtime'] = "";
                    }
                    if (property_exists($result, 'Service')) {
                        $list['Service'] = $result->Service;
                    } else if(property_exists($result, 'Sensor')) {
                        $list['Service'] = $result->Sensor;
                    }else{
                        $list['Service'] = "";
                    }
                     ****/
                    switch (json_last_error()) {
                                            case JSON_ERROR_NONE:
                                                if (property_exists($result, 'realtime')) {
                                                                $list['realtime'] = $result->realtime;
                                                            }else{
                                                                $list['realtime'] = "";
                                                            }
                                                            if (property_exists($result, 'Service')) {
                                                                $list['Service'] = $result->Service;
                                                            } else if(property_exists($result, 'Sensor')) {
                                                                $list['Service'] = $result->Sensor;
                                                            }else{
                                                                $list['Service'] = "";
                                                            }
                                                //
                                            break;
                                            case JSON_ERROR_DEPTH:
                                                $list['realtime'] = "";
                                                $list['Service'] = "";
                                            break;
                                            case JSON_ERROR_STATE_MISMATCH:
                                                $list['realtime'] = "";
                                                $list['Service'] = "";
                                            break;
                                            case JSON_ERROR_CTRL_CHAR:
                                                 $list['realtime'] = "";
                                                 $list['Service'] = "";
                                            break;
                                            case JSON_ERROR_SYNTAX:
                                                 $list['realtime'] = "";
                                                 $list['Service'] = "";
                                            break;
                                            case JSON_ERROR_UTF8:
                                                 $list['realtime'] = "";
                                                 $list['Service'] = "";
                                            break;
                                            default:
                                                 $list['realtime'] = "";
                                                 $list['Service'] = "";
                                            break;
                                        }
                    /****/
                    if (count($process_list) > 0) {
                        $list['process_name_ST']     = $process_list[0]['process_name_ST'];
                        $list['KB_Ip']               = $process_list[0]['KB_Ip'];
                        $list['Graph_Uri']           = $graph_uri;
                        $list['process_path']        = $process_list[0]['process_path'];
                        $list['process_type']        = $process_list[0]['process_type'];
                        $list['licence']             = $process_list[0]['licence'];
                        $list['webpage']             = $process_list[0]['webpage'];
                        $list['telephone']           = $process_list[0]['telephone'];
                        $list['phoenix_table']       = $process_list[0]['phoenix_table'];
                        $list['mail']                = $process_list[0]['mail'];
                        $list['owner']               = $process_list[0]['owner'];
                        $list['disces_ip']           = $process_list[0]['Disces_Ip'];
                        $list['disces_data']         = $total1;
                        $list['jobName']             = $jobn;
                        $list['jobGroup']            = $jobg;
                        $list['ip_disc']             = $ip_disc;
                        //$list['url_ownership']     = $url_ownership;
                        $list['ownership_content']   = $vars;
                        $list['organization']        = $organization;
                        $list['broker']              = $broker;
                        $list['device_id']           = $device_id;
                        $list['icon']                = $files1;
                        $list['HealthinessCriteria'] = $health_c;
                        $list['Period']              = $period;
                    } else {
                        $list['Graph_Uri'] = $graph_uri;
                        if ($owner_iot == '') {
                            $list['owner'] = $owner_iot;
                        } else {
                            $list['owner'] = $process_list[0]['owner'];
                        }
                        $list['disces_data']       = $total1;
                        $list['jobName']           = $jobn;
                        $list['jobGroup']          = $jobg;
                        $list['ip_disc']           = $ip_disc;
                        $list['dataSource']        = '';
                        //$list['url_ownership']     = $url_ownership;
                        $list['ownership_content'] = $vars;
                        $list['organization']      = $organization;
                        $list['broker']            = $broker;
                        $list['device_id']         = $device_id;
                        $list['icon']              = $files1;
                    }
                    echo json_encode($list);
                    break;
                case "KPI":
                    $currentDir      = getcwd();
                    $newDir          = explode('management', $currentDir);
                    $uploadDirectory = "img/sensorImages/";
                    $currentDir2     = $newDir[0];
                    $id_img          = $id_row;
                    
                    
                    
                    if ($id_img !== "") {
                        
                        if (file_exists($currentDir2 . $uploadDirectory . $id_img)) {
                            $files  = scandir($currentDir2 . $uploadDirectory . $id_img);
                            $files1 = $files[2];
                        }
                    } else {
                        $files1 = "";
                    }
                    $value = $value_name;
                    $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                    mysqli_set_charset($link, 'utf8');
                    mysqli_select_db($link, $dbname_processes);
                    $process      = '';
                    $process_list = array();
                    $query0       = "SELECT dataSource, query FROM Descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value) . "'";
                    $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                    $total0     = $result0->num_rows;
                    $dataSource = "";
                    //
                    while ($row = $result0->fetch_assoc()) {
                        $dataSource = $row['dataSource'];
                        $query_des  = $row['query'];
                        $pieces     = explode("'", $query_des);
                    }
                    //
                    $query_sparql = 'http://' . $graph_uri_sparql_uri . ':8890/sparql?query=select+distinct+%3Fgraph+%7B+graph+%3Fgraph+%7B%3C' . $pieces[1] . '%3E+a+%3Fc%7D+%3Fgraph+%3Fx+%3Fy.%7D&format=application%2Fsparql-results%2Bjson';
                    $payload      = file_get_contents($query_sparql);
                    $result       = json_decode($payload);
                    if ($result) {
                        $json_sparql  = json_encode($result);
                        $json_sparql2 = json_decode($json_sparql);
                        if (isset($json_sparql2->results)) {
                            $result_query = $json_sparql2->results;
                        } else {
                            $result_query = "";
                        }
                        if (isset($result_query->bindings)) {
                            $av = $result_query->bindings;
                        } else {
                            $av = "";
                        }
                        if (isset($av[0]->graph)) {
                            $graph_uri0 = $av[0]->graph;
                        } else {
                            $graph_uri0 = "";
                        }
                        if (isset($graph_uri0->value)) {
                            $graph_uri = $graph_uri0->value;
                        } else {
                            $graph_uri = "";
                        }
                        $query0 = "SELECT process_manager_graph.*, process_manager_responsible.licence, process_manager_responsible.webpage, process_manager_responsible.process_type, process_manager_responsible.telephone, process_manager_responsible.responsible, process_manager_responsible.mail FROM process_manager_graph, process_manager_responsible WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "' AND process_manager_graph.Process_name=process_manager_responsible.process_name LIMIT 1";
                        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        $total0       = $result0->num_rows;
                        $process_st0  = '';
                        $process_path = '';
                        if ($total0 > 0) {
                            while ($row0 = mysqli_fetch_assoc($result0)) {
                                $listFile0    = array(
                                    "process_name_ST" => $row0['Process_name'],
                                    "KB_Ip" => $row0['KB_Ip'],
                                    "Graph_Uri" => $row0['Graph_Uri'],
                                    "phoenix_table" => $row0['phoenix_table'],
                                    "process_path" => $row0['process_path'],
                                    "process_type" => $row0['process_type'],
                                    "licence" => $row0['licence'],
                                    "webpage" => $row0['webpage'],
                                    "telephone" => $row0['telephone'],
                                    "mail" => $row0['mail'],
                                    "owner" => $row0['responsible'],
                                    "Disces_Ip" => $row0['DISCES_Ip']
                                );
                                $process_path = $row0['process_path'];
                                array_push($process_list, $listFile0);
                            }
                        }
                        $link_disces = mysqli_connect($disces_host, $disces_username, $disces_password) or die("failed to connect to server Disces!!");
                        mysqli_set_charset($link_disces, 'utf8');
                        mysqli_select_db($link_disces, $disces_dbname);
                        if ($process_path == '') {
                            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA=''";
                        } else {
                            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
                        }
                        $result1 = mysqli_query($link_disces, $query1) or die(mysqli_error($link_disces));
                        $total1 = $result1->num_rows;
                        $jobn   = '';
                        $jobg   = '';
                        if ($total1 > 0) {
                            while ($row1 = mysqli_fetch_assoc($result1)) {
                                $jobn = $row1['JOB_NAME'];
                                $jobg = $row1['JOB_GROUP'];
                            }
                        }
                        if (count($process_list) > 0) {
                            $list['process_name_ST']   = $process_list[0]['process_name_ST'];
                            $list['KB_Ip']             = $process_list[0]['KB_Ip'];
                            $list['Graph_Uri']         = $process_list[0]['Graph_Uri'];
                            $list['process_path']      = $process_list[0]['process_path'];
                            $list['process_type']      = $process_list[0]['process_type'];
                            $list['licence']           = $process_list[0]['licence'];
                            $list['webpage']           = $process_list[0]['webpage'];
                            $list['telephone']         = $process_list[0]['telephone'];
                            $list['phoenix_table']     = $process_list[0]['phoenix_table'];
                            $list['mail']              = $process_list[0]['mail'];
                            $list['owner']             = $process_list[0]['owner'];
                            $list['disces_ip']         = $process_list[0]['Disces_Ip'];
                            $list['disces_data']       = $total1;
                            $list['jobName']           = $jobn;
                            $list['jobGroup']          = $jobg;
                            $list['ip_disc']           = $ip_disc;
                            $list['dataSource']        = '';
                            //$list['url_ownership']     = $url_ownership;
                            $list['ownership_content'] = $vars;
                            $list['organization']      = $organization;
                            $list['broker']            = $broker;
                            $list['device_id']         = $device_id;
                            $list['icon']              = $files1;
                        }
                    }
                    echo json_encode($list);
                    break;
                case "From Dashboard to IOT Device":
                    $link_dash = mysqli_connect($host, $username, $password) or die("failed to connect to server Processes !!");
                    mysqli_set_charset($link_dash, 'utf8');
                    mysqli_select_db($link_dash, $dbname);
                    //echo($value);
                    $query_dash = "SELECT id_dashboard FROM Config_widget_dashboard WHERE name_w = '" . $value . "'";
                    $result_dash = mysqli_query($link_dash, $query_dash) or die(mysqli_error($link_dash));
                    
                    $total1         = $result_dash->num_rows;
                    $name_dashboard = array();
                    if ($total1 > 0) {
                        while ($row1 = mysqli_fetch_assoc($result_dash)) {
                            $name_dashboard['name'] = $row1['id_dashboard'];
                        }
                    } else {
                        $name_dashboard['name'] = 'no';
                    }
                    //
                    echo json_encode($name_dashboard);
                    //echo json_encode($list);
                    break;
                case "Special Widget":
                    $value_name = $_REQUEST['value'];
                    $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                    mysqli_set_charset($link, 'utf8');
                    mysqli_select_db($link, $dbname_processes);
                    $process      = '';
                    $process_list = array();
                    $query0       = "SELECT dataSource, query FROM Descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value_name) . "'";
                    $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                    $total0     = $result0->num_rows;
                    $dataSource = "";
                    //
                    if ($total0 > 0) {
                        while ($row = $result0->fetch_assoc()) {
                            $dataSource = $row['dataSource'];
                            $query_des  = $row['query'];
                            if (($query_des == null) || ($query_des == "")) {
                                $pieces    = '';
                                $graph_uri = '';
                            } else {
                                $pieces       = explode("'", $query_des);
                                $query_sparql = 'http://' . $graph_uri_sparql_uri . ':8890/sparql?query=select+distinct+%3Fgraph+%7B+graph+%3Fgraph+%7B%3C' . $pieces[1] . '%3E+a+%3Fc%7D+%3Fgraph+%3Fx+%3Fy.%7D&format=application%2Fsparql-results%2Bjson';
                                $payload      = file_get_contents($query_sparql);
                                $result       = json_decode($payload);
                                if ($result) {
                                    $json_sparql  = json_encode($result);
                                    $json_sparql2 = json_decode($json_sparql);
                                    if (isset($json_sparql2->results)) {
                                        $result_query = $json_sparql2->results;
                                    } else {
                                        $result_query = "";
                                    }
                                    if (isset($result_query->bindings)) {
                                        $av = $result_query->bindings;
                                    } else {
                                        $av = "";
                                    }
                                    if (isset($av[0]->graph)) {
                                        $graph_uri0 = $av[0]->graph;
                                    } else {
                                        $graph_uri0 = "";
                                    }
                                    if (isset($graph_uri0->value)) {
                                        $graph_uri = $graph_uri0->value;
                                    } else {
                                        $graph_uri = "";
                                    }
                                }
                            }
                            $query0 = "SELECT * FROM process_manager_graph WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "'";
                            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                            $total0       = $result0->num_rows;
                            $process_st0  = '';
                            $process_path = '';
                            if ($total0 > 0) {
                                while ($row0 = mysqli_fetch_assoc($result0)) {
                                    $proc_n = "";
                                    if ($graph_uri == "") {
                                        $proc_n = "";
                                    } else {
                                        $proc_n = $row0['Process_name'];
                                    }
                                    $listFile0    = array(
                                        "process_name_ST" => $proc_n,
                                        "KB_Ip" => $row0['KB_Ip'],
                                        "Graph_Uri" => $row0['Graph_Uri'],
                                        "phoenix_table" => $row0['phoenix_table'],
                                        "process_path" => $row0['process_path'],
                                        "process_type" => '',
                                        "licence" => '',
                                        "webpage" => '',
                                        "telephone" => '',
                                        "mail" => '',
                                        "owner" => '',
                                        "Disces_Ip" => $row0['DISCES_Ip']
                                    );
                                    $process_path = $row0['process_path'];
                                    array_push($process_list, $listFile0);
                                }
                            }
                            $link_disces = mysqli_connect($disces_host, $disces_username, $disces_password) or die("failed to connect to server Disces!!");
                            mysqli_set_charset($link_disces, 'utf8');
                            mysqli_select_db($link_disces, $disces_dbname);
                            if ($process_path == '') {
                                $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA=''";
                            } else {
                                $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
                            }
                            $result1 = mysqli_query($link_disces, $query1) or die(mysqli_error($link_disces));
                            $total1 = $result1->num_rows;
                            $jobn   = '';
                            $jobg   = '';
                            if ($total1 > 0) {
                                while ($row1 = mysqli_fetch_assoc($result1)) {
                                    $jobn = $row1['JOB_NAME'];
                                    $jobg = $row1['JOB_GROUP'];
                                }
                            }
                        }
                        $list['process_name_ST'] = $process_list[0]['process_name_ST'];
                        $list['KB_Ip']           = $process_list[0]['KB_Ip'];
                        $list['Graph_Uri']       = $process_list[0]['Graph_Uri'];
                        $list['process_path']    = $process_list[0]['process_path'];
                        $list['process_type']    = $process_list[0]['process_type'];
                        $list['licence']         = $process_list[0]['licence'];
                        $list['webpage']         = $process_list[0]['webpage'];
                        $list['telephone']       = $process_list[0]['telephone'];
                        $list['phoenix_table']   = $process_list[0]['phoenix_table'];
                        $list['mail']            = $process_list[0]['mail'];
                        $list['owner']           = $process_list[0]['owner'];
                        $list['disces_ip']       = $process_list[0]['Disces_Ip'];
                    }
                    
                    $currentDir      = getcwd();
                    $newDir          = explode('management', $currentDir);
                    $uploadDirectory = "img/sensorImages/";
                    $currentDir2     = $newDir[0];
                    $id_img          = $id_row;
                    
                    
                    
                    if ($id_img !== "") {
                        
                        if (file_exists($currentDir2 . $uploadDirectory . $id_img)) {
                            $files  = scandir($currentDir2 . $uploadDirectory . $id_img);
                            $files1 = $files[2];
                        }
                    } else {
                        $files1 = "";
                    }
                    $list['icon'] = $files1;
                    
                    echo json_encode($list);
                    break;
                case "MyKPI":
                case "MyPOI":
                case "POI":
                    //
                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
                        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                        $accessToken = $tkn->access_token;
                        $_SESSION['refreshToken'] = $tkn->refresh_token;
                        $url_ownership = 'http://'.$iot_device_ownership.'/ownership-api/v1/list/?elementId='.urlencode($value_name).'&type=IOTID&accessToken='.$accessToken;
                        $owner_iot = '';
                        $payload_own = file_get_contents($url_ownership);
                        $var_own = json_decode($payload_own, true);
                        //
                    //
                    $url_api       = "https://www.snap4city.org/mypersonaldata/api/v1/public/kpidata/?sourceRequest=";
                    $payload       = file_get_contents($url_api);
                    $result        = json_decode($payload);
                    $id_mypoi      = '';
                    $name_owner    = '';
                    $organizations = '';
                    $arr_id        = explode("/", $data_get_instances);
                    $c             = count($arr_id);
                    if ($c > 0) {
                        $new_id = $arr_id[$c - 1];
                    } else {
                        $new_id = $arr_id[0];
                    }
                    
                    $currentDir      = getcwd();
                    $newDir          = explode('management', $currentDir);
                    $uploadDirectory = "img/sensorImages/";
                    $currentDir2     = $newDir[0];
                    $id_img          = $id_row;
                    
                    
                    
                    if ($id_img !== "") {
                        
                        if (file_exists($currentDir2 . $uploadDirectory . $id_img)) {
                            $files  = scandir($currentDir2 . $uploadDirectory . $id_img);
                            $files1 = $files[2];
                        }
                    } else {
                        $files1 = "";
                    }
                    $list['icon'] = $files1;
                    $value_org = "";
                    if ($result) {
                        $json_sparql  = json_encode($result);
                        $json_sparql2 = json_decode($json_sparql);
                        $tot          = count($json_sparql2);
                        for ($i = 0; $i < $tot; $i++) {
                            $id_mypoi = $json_sparql2[$i]->id;
                            if ($new_id == $id_mypoi) {
                                $name_owner           = $json_sparql2[$i]->valueName;
                                $organizations        = $json_sparql2[$i]->organizations;
                                $list['owner']        = $name_owner;
                                //$list['organization'] = $organizations;
                                //
                                $value_org = $organizations;
                                //
                                $Firstpos=strpos($value_org, "ou=");
                                $Secondpos=strpos($value_org, ",dc=");
                                $value_org2 = substr($value_org , $Firstpos +3, $Secondpos-4);
                                //
                                $list['organization'] = $value_org2;
                                //
                                $list['dataSource']   = $new_id;
                            }
                            
                        }
                    }
                    echo json_encode($list);
                    break;
                default:
                    //echo json_encode($list);
                    break;
            }
            /*** Fine Types ***/
            
            
        } else {
            echo ("You are not authorized to access ot this data!");
            exit;
        }
        
    } else {
        echo ("You are not authorized to access ot this data!");
        exit;
    }
    
  }else{
        echo ("You are not authorized to access ot this data!");
        exit;
  }
}else{
        echo ("You are not authorized to access ot this data!");
        exit;
    }
?>