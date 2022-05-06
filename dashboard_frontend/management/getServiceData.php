<?php
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
session_start();
ini_set("max_execution_time", 0);
error_reporting(E_ERROR);
//if (isset($_SESSION['loggedUsername'])) {
                    /*****************/
                    //if(isset($_SESSION['refreshToken'])) {
                        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
                        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                        $accessToken = $tkn->access_token;
                        $_SESSION['refreshToken'] = $tkn->refresh_token;
                    
    //error_reporting(E_ERROR);
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    //error_reporting(-1);
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
            
            if (isset($_REQUEST['rb_row'])) {
                $row_db = $_REQUEST['rb_row'];
            } else {
                $row_db = "";
            }
            if (isset($_REQUEST['subnature'])){
                $subnature = $_REQUEST['subnature'];
            }else{
                $subnature = '';
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
            //OwnerHarsh
            $OwnerHarsh = "";
            
            if ($role_session_active == "RootAdmin"){
                //row_db
                //$query_own ="SELECT DISTINCT unique_name_id, ownerHash FROM DashboardWizard WHERE unique_name_id = '".$value_name."';";
                $query_own ="SELECT DISTINCT unique_name_id, ownerHash, value_name,value_type, value_unit FROM DashboardWizard WHERE id = '".$row_db."';";
                $result_own = mysqli_query($link, $query_own) or die(mysqli_error($link));
               // print_r($result_own);
                $value_name = "";
                $value_type="";
                $value_unit="";
                while ($row_own = mysqli_fetch_assoc($result_own)) {
                    $OwnerHarsh_01 = $row_own['ownerHash'];
                    $value_name = $row_own['value_name'];
                    $value_type = $row_own['value_type'];
                    $value_unit = $row_own['$value_unit'];
                    if (($OwnerHarsh_01 != null)&&($OwnerHarsh_01 !=="")){
                          $decryptedOwn = decryptOSSL($OwnerHarsh_01, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
                          $OwnerHarsh = $decryptedOwn;
                    }
                    //print_r($decryptedOwn);
                }
            }
            //value
            //
            $list                        = array();
            $list['device_set_name'] = '';
            $list['ch1']= date("Y-m-d H:i:s");
            $list['process_name_ST']     = '';
            $list['KB_Ip']               = $kbHostUrl;
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
            $list['creator'] = $OwnerHarsh;
            $list['process_name']='';
            $list['address'] =             '';
            $list['reference_person'] =    '';
            $list['value_name'] =$value_name;
            $list['value_type'] =$value_type;
            $list['value_unit']=$value_unit;
            
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
            ///////
            $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                        mysqli_set_charset($link, 'utf8');
                        mysqli_select_db($link, $dbname_processes);
                        $process_list = array();
                        $p_name = "";
                        if (($type == 'POI')||($type =='External Service')||($value_name=='')||($value_name =='ExternalContent')){
                        //if (($type == 'POI')||($value_name=='')||($value_name =='ExternalContent')){
                         $p_name = $subnature;
                         $query0 = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='".$subnature."';";
                        }else{
                         $p_name = $value_name;
                        $query0 = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='".$value_name."';";
                        }
                        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        $total0       = $result0->num_rows;
                        $process_st0  = '';
                        $process_path = '';
                        if ($total0 > 0) {
                            while ($row0 = mysqli_fetch_assoc($result0)) {
                                $listFile0    = array(
                                    "licence" => $row0['licence'],
                                    "webpage" => $row0['webpage'],
                                    "telephone" => $row0['telephone'],
                                    "mail" => $row0['mail'],
                                    "owner" => $row0['responsible'],
                                    "address" =>$row0['address'],
                                    "reference_person"=>$row0['reference_person'],
                                    "process_name"=>$row0['process_name']
                                );
                                array_push($process_list, $listFile0);
                            }
                        }
                        if (count($process_list) > 0) {
                            $list['licence']           = $process_list[0]['licence'];
                            $list['webpage']           = $process_list[0]['webpage'];
                            $list['telephone']         = $process_list[0]['telephone'];
                            $list['mail']              = $process_list[0]['mail'];
                            $list['owner']             = $process_list[0]['owner'];
                            $list['address'] =              $process_list[0]['address'];
                            $list['reference_person'] =     $process_list[0]['reference_person'];
                            $list['process_name']=   $process_list[0]['process_name'];
                        }else{
                            $list['process_name']=   $p_name;
                        }
             ///////////////////////
            //
            /*** Types ****/
            switch ($type) {
                case "Sensor":
                case "Sensor-Actuator":
                case 'Data Table Device':
                case 'Data Table Model':
                case 'Data Table Variable':
                case 'IoT Device':
                case 'IoT Device Model':
                case 'IoT Device Variable':
                case 'IoT Device':
                case 'Mobile Device':
                case 'Mobile Device Model':
                case 'Mobile Device Variable':
                case 'Sensor Device':
                    //
                    $currentDir      = getcwd();
                    $newDir          = explode('management', $currentDir);
                    $uploadDirectory = "img/sensorImages/";
                    $currentDir2     = $newDir[0];
                    $id_img          = $id_row;
                    $serviceUri = "";
                    $healthiness = "";
                   $result = "";

                    
                    
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
                    
                    //$date = new DateTime();
                    $list['ch2']= date("Y-m-d H:i:s");
                    $context = stream_context_create($options);
                    
                    if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
                            echo json_encode($list);
                            die();
                        }else{
                                $payload = file_get_contents($value, false, $context);
                                $result = json_decode($payload);
                                $error = json_last_error();
                                //print_r($result);
                                
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
                    ///$query0       = "SELECT devices.device_name AS 'Process_ST', devices.process AS 'process', devices.HealthinessCriteria AS 'HealthinessCriteria', devices.Period AS 'Period', process_manager_graph.KB_Ip AS 'KB_Ip', process_manager_graph.Graph_Uri AS 'Graph_Uri', process_manager_graph.phoenix_table AS 'phoenix_table', process_manager_graph.process_path AS 'process_path', process_manager_graph.DISCES_Ip AS 'DISCES_Ip', process_manager_responsible.responsible AS 'responsible', process_manager_responsible.process_type AS 'process_type', process_manager_responsible.licence AS 'licence', process_manager_responsible.webpage AS 'webpage', process_manager_responsible.telephone AS 'telephone', process_manager_responsible.mail AS 'mail', process_manager_graph.HealthinessCriteria AS 'HealthinessCriteria_graph', process_manager_graph.Period AS 'Period_graph' FROM devices, process_manager_graph, process_manager_responsible WHERE process_manager_graph.Process_name=devices.process AND process_manager_responsible.process_name=devices.process AND devices.device_name ='" . $value_name . "' group by Process_ST";
                    $query0       = "SELECT devices.device_name AS 'Process_ST', devices.process AS 'process', devices.HealthinessCriteria AS 'HealthinessCriteria', devices.Period AS 'Period', process_manager_graph.KB_Ip AS 'KB_Ip', process_manager_graph.Graph_Uri AS 'Graph_Uri', process_manager_graph.phoenix_table AS 'phoenix_table', process_manager_graph.process_path AS 'process_path', process_manager_graph.DISCES_Ip AS 'DISCES_Ip', process_manager_responsible.responsible AS 'responsible', process_manager_responsible.process_type AS 'process_type', process_manager_responsible.licence AS 'licence', process_manager_responsible.webpage AS 'webpage', process_manager_responsible.telephone AS 'telephone', process_manager_responsible.mail AS 'mail', process_manager_graph.HealthinessCriteria AS 'HealthinessCriteria_graph', process_manager_graph.Period AS 'Period_graph', process_manager_responsible.address AS 'address', process_manager_responsible.reference_person AS 'reference_person' FROM devices, process_manager_graph, process_manager_responsible WHERE process_manager_graph.Process_name=devices.process AND process_manager_responsible.process_name=devices.process AND devices.device_name ='" . $value_name . "' group by Process_ST";
                    $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                    //echo($query0);
                    //
                    $total0       = $result0->num_rows;
                    $process_st0  = '';
                    $process_path = '';
                    $graph_uri    = "";
                    $health_c     = "";
                    $period       = "";
                    $p_name = '';
                    if ($total0 > 0){
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
                                if (($row0['Process_ST'] == null)||($row0['Process_ST'] =='')){
                                                $p_name = $row0['Process_ST'];
                                }else{
                                                $p_name = $value_name;
                                }
                                /***/
                                $listFile0    = array(
                                    "process_name_ST" => $row0['Process_ST'],
                                    //"KB_Ip" => $row0['KB_Ip'],
                                    "KP_Ip" => $kbHostUrl,
                                    "Graph_Uri" => $graph_uri,
                                    "phoenix_table" => $row0['phoenix_table'],
                                    "process_path" => $row0['process_path'],
                                    "process_type" => $row0['process_type'],
                                    "licence" => $row0['licence'],
                                    "webpage" => $row0['webpage'],
                                    "telephone" => $row0['telephone'],
                                    "mail" => $row0['mail'],
                                    "owner" => $row0['responsible'],
                                    "Disces_Ip" => $row0['DISCES_Ip'],
                                    "address" => $row0['address'],
                                    "reference_person" => $row0['reference_person'],
                                    "process_name" => $p_name,
                                    "ETL_process" => $row0['process']
                                );
                                $process_path = $row0['process_path'];
                                array_push($process_list, $listFile0);
                            }
                    }else{
                         $listFile0    = array(
                                    "process_name_ST" => "",
                                    "KB_Ip" => $kbHostUrl,
                                    "Graph_Uri" => "",
                                    "phoenix_table" => "",
                                    "process_path" => "",
                                    "process_type" => "",
                                    "licence" => "",
                                    "webpage" => "",
                                    "telephone" => "",
                                    "mail" => "",
                                    "owner" => "",
                                    "Disces_Ip" => "",
                                    "address" =>"",
                                    "reference_person" => "",
                                    "process_name" => $value_name,
                                    "ETL_process" => ""
                                ); 
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
                                        $pr_ST = "";
                    if (count($process_list) > 0) {
                        $list['process_name_ST']     = $process_list[0]['process_name_ST'];
                        $pr_ST = $process_list[0]['ETL_process'];
                        $list['KB_Ip']               = $kbHostUrl;
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
                        $list['creator'] = $OwnerHarsh;
                        $list['address'] =              $process_list[0]['address'];
                        $list['reference_person'] =     $process_list[0]['reference_person'];
                        $list['process_name'] = $process_list[0]['process_name'];
                        $list['device_set_name']=$pr_ST;
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
                        $list['process_name'] = $value_name;
                        $list['device_set_name'] = "";
                    }
                    
                    if (($pr_ST != null)&&($pr_ST !="")){
                                //listETL
                        $query_ETL = "SELECT DISTINCT device_name FROM devices WHERE process='".$pr_ST."' AND device_name !='".$value_name."';";
                        $result_ETL = mysqli_query($link, $query_ETL) or die(mysqli_error($link));
                        $total_ETL = $result_ETL->num_rows;
                        if ($total_ETL > 0){
                            $listETL = array();
                            while ($row_ETL = mysqli_fetch_assoc($result_ETL)) {
                                    $ix = $row_ETL['device_name'];
                                   array_push($listETL,$ix);
                            }
                            
                            $list['list_ETL']=$listETL;
                        }
                        $list['total_ETL']=$total_ETL;
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
                    $link_dash = mysqli_connect($host, $username, $password) or die("failed to connect to server Processes !!");
                        mysqli_set_charset($link_dash, 'utf8');
                        mysqli_select_db($link_dash, $dbname);
                    $process      = '';
                    $process_list = array();
                    $query0       = "SELECT dataSource, query FROM Descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value) . "'";
                    $result0 = mysqli_query($link_dash, $query0) or die(mysqli_error($link));
                    $total0     = $result0->num_rows;
                    $dataSource = "";
                    //
                    while ($row = $result0->fetch_assoc()) {
                        $dataSource = $row['dataSource'];
                        $query_des  = $row['query'];
                        $pieces     = explode("'", $query_des);
                    }
                    //
                    $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
                    $query_sparql = 'http://' . $graph_uri_sparql_uri . ':8890/sparql?query=select+distinct+%3Fgraph+%7B+graph+%3Fgraph+%7B%3C' . $pieces[1] . '%3E+a+%3Fc%7D+%3Fgraph+%3Fx+%3Fy.%7D&format=application%2Fsparql-results%2Bjson';
                    $payload      = file_get_contents($query_sparql,$context,false);
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
                        //$query0 = "SELECT process_manager_graph.*, process_manager_responsible.licence, process_manager_responsible.webpage, process_manager_responsible.process_type, process_manager_responsible.telephone, process_manager_responsible.responsible, process_manager_responsible.mail FROM process_manager_graph, process_manager_responsible WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "' AND process_manager_graph.Process_name=process_manager_responsible.process_name LIMIT 1";
                        $query0 = "SELECT process_manager_graph.*, process_manager_responsible.licence, process_manager_responsible.webpage, process_manager_responsible.process_type, process_manager_responsible.telephone, process_manager_responsible.responsible, process_manager_responsible.mail, process_manager_responsible.reference_person, process_manager_responsible.address FROM process_manager_graph, process_manager_responsible WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "' AND process_manager_graph.Process_name=process_manager_responsible.process_name LIMIT 1";
                        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        $total0       = $result0->num_rows;
                        $process_st0  = '';
                        $process_path = '';
                        if ($total0 > 0) {
                            while ($row0 = mysqli_fetch_assoc($result0)) {
                                $listFile0    = array(
                                    "process_name_ST" => $row0['Process_name'],
                                    "KB_Ip" => $kbHostUrl,
                                    "Graph_Uri" => $row0['Graph_Uri'],
                                    "phoenix_table" => $row0['phoenix_table'],
                                    "process_path" => $row0['process_path'],
                                    "process_type" => $row0['process_type'],
                                    "licence" => $row0['licence'],
                                    "webpage" => $row0['webpage'],
                                    "telephone" => $row0['telephone'],
                                    "mail" => $row0['mail'],
                                    "owner" => $row0['responsible'],
                                    "Disces_Ip" => $row0['DISCES_Ip'],
                                    "address" =>$row0['address'],
                                    "reference_person"=>$row0['reference_person'],
                                    "process_name" => $row0['Process_name']
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
                            $list['KB_Ip']             = $kbHostUrl;
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
                            $list['creator'] = $OwnerHarsh;
                            $list['address'] =              $process_list[0]['address'];
                            $list['reference_person'] =     $process_list[0]['reference_person'];
                            $list['process_name']   = $process_list[0]['process_name'];
                        }
                    }
                    echo json_encode($list);
                    break;
                    case "Dashboard list":
                        $arr = array();
                        //$app_id = 'nr6dsjh';
                        $app_id =  $value;
                        $link_dash = mysqli_connect($host, $username, $password) or die("failed to connect to server Processes !!");
                        mysqli_set_charset($link_dash, 'utf8');
                        mysqli_select_db($link_dash, $dbname);
                   //$q = "SELECT DISTINCT id_dashboard as dashboardId,title_header as dashboardName, user as dashboardAuthor FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ON d.Id=w.id_dashboard WHERE appId='$app_id' AND d.deleted='no'";
                    $q = "SELECT DISTINCT id_dashboard as dashboardId,title_header as dashboardName FROM Config_widget_dashboard w JOIN Config_dashboard d ON d.Id=w.id_dashboard WHERE appId='$app_id' AND d.deleted='no'";
                        //$r = mysqli_query($link, $q);
                        $r = mysqli_query($link_dash, $q) or die(mysqli_error($link_dash));
                    //print_r($r);
                    if($r)
                    {
                        $i = 0;
                    while($row = mysqli_fetch_assoc($r))
                    {
                         $row_dash = htmlspecialchars($row['dashboardName']);
                       $arr[$i] = $row;
                       $i++;
                    }
                    }else{
                        //echo('nothing');
                        $arr[0] = 'null';
                    }
                    $list_01['dashboards'] =$arr;
                        echo json_encode($list_01);
                    break;
                    case "DashKpi":
                        $id_kpi = $_GET['service'];
                        $arr = array();
                        $list_01['dashboards'];
                        //
                        $kpi_request = "";
                        $kpi_request = 'https://www.snap4city.org/mypersonaldata/api/v1/public/kpidata/'.$id_kpi.'/activities';
                        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
                        $kpi_payload      = file_get_contents($kpi_request,$context, false);
                        //$test='[{"id":268555277,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-01T21:55:01Z"},{"id":269600227,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-02T21:55:01Z"},{"id":270664235,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-03T21:55:01Z"},{"id":271745332,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-04T21:55:01Z"},{"id":272802258,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-05T21:55:01Z"},{"id":273691677,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:10:08Z"},{"id":273691749,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:10:11Z"},{"id":273691839,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:10:14Z"},{"id":273692027,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:10:19Z"},{"id":273696285,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:16:10Z"},{"id":273696311,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:16:14Z"},{"id":273696337,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:16:18Z"},{"id":273700423,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:20:20Z"},{"id":273700452,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:20:26Z"},{"id":273700498,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:20:33Z"},{"id":273701454,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:22:59Z"},{"id":273701477,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:23:02Z"},{"id":273701515,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:23:05Z"},{"id":273724465,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-06T18:50:48Z"},{"id":273863281,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-06T21:55:01Z"},{"id":274658789,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T15:58:58Z"},{"id":274658795,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T15:58:59Z"},{"id":274658821,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T15:59:02Z"},{"id":274687165,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:35:54Z"},{"id":274687230,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:35:58Z"},{"id":274687266,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:36:01Z"},{"id":274687414,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:36:09Z"},{"id":274687651,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:36:17Z"},{"id":274687874,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:36:23Z"},{"id":274704883,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T16:58:12Z"},{"id":274714520,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:10:41Z"},{"id":274714839,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:11:06Z"},{"id":274716099,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:12:10Z"},{"id":274716219,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:12:14Z"},{"id":274716771,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:12:38Z"},{"id":274716847,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:12:43Z"},{"id":274718720,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:16:36Z"},{"id":274719722,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:17:23Z"},{"id":274723366,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:22:00Z"},{"id":274723422,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:22:11Z"},{"id":274723514,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:22:26Z"},{"id":274723550,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:22:32Z"},{"id":274724926,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:23:58Z"},{"id":274724970,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:24:06Z"},{"id":274725016,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:24:12Z"},{"id":274727293,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:07Z"},{"id":274727358,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:14Z"},{"id":274727688,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:28Z"},{"id":274727795,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:32Z"},{"id":274727980,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:45Z"},{"id":274728092,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:26:59Z"},{"id":274733289,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:33:14Z"},{"id":274733400,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:33:18Z"},{"id":274733465,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:33:20Z"},{"id":274740204,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:41:23Z"},{"id":274740391,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:41:52Z"},{"id":274744404,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:46:47Z"},{"id":274744676,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:47:01Z"},{"id":274745153,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:47:24Z"},{"id":274745616,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:48:01Z"},{"id":274745757,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:48:24Z"},{"id":274746235,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:49:33Z"},{"id":274746577,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:50:22Z"},{"id":274750845,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:54:19Z"},{"id":274750855,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:54:21Z"},{"id":274750903,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:54:24Z"},{"id":274750939,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:54:29Z"},{"id":274750961,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T17:54:32Z"},{"id":274756321,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:02:05Z"},{"id":274776528,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:29:17Z"},{"id":274776620,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:29:26Z"},{"id":274776657,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:29:34Z"},{"id":274776694,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:29:42Z"},{"id":274798083,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:57:15Z"},{"id":274798201,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T18:57:29Z"},{"id":274821989,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T19:27:59Z"},{"id":274822035,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T19:28:08Z"},{"id":274822087,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T19:28:16Z"},{"id":274822151,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-07T19:28:23Z"},{"id":274931925,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-07T21:55:01Z"},{"id":275753190,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-08T21:11:49Z"},{"id":275753254,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge3f352de21b550efe88d8b81b83a03f6cbb8c8cdf4d2f2d05eb4fddaa1ab3","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-08T21:12:08Z"},{"id":275778251,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-08T21:55:01Z"},{"id":276557888,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-09T21:55:01Z"},{"id":277361983,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T19:18:17Z"},{"id":277402880,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T20:10:35Z"},{"id":277420968,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T20:54:07Z"},{"id":277433236,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:29:26Z"},{"id":277436094,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:35:15Z"},{"id":277438190,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:41:19Z"},{"id":277439627,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:45:24Z"},{"id":277439665,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:45:29Z"},{"id":277439734,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T21:45:37Z"},{"id":277443002,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-10T21:55:00Z"},{"id":277450721,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T22:17:17Z"},{"id":277450728,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-10T22:17:18Z"},{"id":277701287,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T09:04:01Z"},{"id":277701335,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T09:04:07Z"},{"id":277759565,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:15:13Z"},{"id":277761960,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:20:41Z"},{"id":277761978,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:20:43Z"},{"id":277762050,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:20:50Z"},{"id":277772422,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:41:50Z"},{"id":277772973,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:43:12Z"},{"id":277772993,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:43:14Z"},{"id":277773017,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:43:19Z"},{"id":277778976,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:54:08Z"},{"id":277779071,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:54:12Z"},{"id":277781259,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T11:56:05Z"},{"id":277787197,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"edge376bf69bc0d850ecfe9e6fde2b236f30143cdebbd737ceda947c7e719ac7","accessType":"WRITE","domain":"VALUE","insertTime":"2020-06-11T12:03:10Z"},{"id":278023839,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-11T21:55:00Z"},{"id":278549017,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-12T21:55:00Z"},{"id":279072399,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-13T21:55:00Z"},{"id":279595164,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-14T21:55:00Z"},{"id":280116695,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-15T21:55:00Z"},{"id":280797711,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-16T21:55:00Z"},{"id":281799641,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-17T21:55:00Z"},{"id":282811440,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-18T21:55:00Z"},{"id":283981954,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-19T21:55:00Z"},{"id":285124326,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-20T21:55:00Z"},{"id":286265075,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-21T21:55:00Z"},{"id":287388193,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-22T21:55:40Z"},{"id":288056947,"username":"nicolaroot","kpiId":17057470,"sourceRequest":"synoptic","sourceId":"126806747","accessType":"READ","domain":"VALUE","insertTime":"2020-06-23T14:10:32Z"},{"id":288090204,"username":"nicolaroot","kpiId":17057470,"sourceRequest":"synoptic","sourceId":"126806747","accessType":"READ","domain":"VALUE","insertTime":"2020-06-23T14:57:34Z"},{"id":288383623,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-23T21:55:00Z"},{"id":289378883,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-24T21:55:00Z"},{"id":290380701,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-25T21:55:09Z"},{"id":290912690,"username":"nicolaroot","kpiId":17057470,"sourceRequest":"synoptic","sourceId":"126806747","accessType":"READ","domain":"VALUE","insertTime":"2020-06-26T10:30:13Z"},{"id":290914775,"username":"nicolaroot","kpiId":17057470,"sourceRequest":"synoptic","sourceId":"126806747","accessType":"READ","domain":"VALUE","insertTime":"2020-06-26T10:33:27Z"},{"id":291437524,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-26T21:55:00Z"},{"id":292592474,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-27T21:55:00Z"},{"id":293754800,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-28T21:55:00Z"},{"id":295030601,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-29T21:55:00Z"},{"id":296376442,"username":"demo_domotica","kpiId":17057470,"sourceRequest":"iotapp","sourceId":"nr35igt","accessType":"READ","domain":"VALUE","insertTime":"2020-06-30T21:55:00Z"}]';
                        $dec_1 = json_decode($test);
                        $list_01['dashboards'][0]['dashboardName'] = $dec_1[0];
                        echo json_encode($list_01);
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
                    $link_dash = mysqli_connect($host, $username, $password) or die("failed to connect to server Processes !!");
                    mysqli_set_charset($link_dash, 'utf8');
                    mysqli_select_db($link_dash, $dbname);
                    $process      = '';
                    $process_list = array();
                    $query0       = "SELECT dataSource, query FROM Descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value_name) . "'";
                    $result0 = mysqli_query($link_dash, $query0) or die(mysqli_error($link));
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
                                        "process_name" => $proc_n,
                                        "KB_Ip" => $kbHostUrl,
                                        "Graph_Uri" => $row0['Graph_Uri'],
                                        "phoenix_table" => $row0['phoenix_table'],
                                        "process_path" => $row0['process_path'],
                                        "process_type" => '',
                                        "licence" => '',
                                        "webpage" => '',
                                        "telephone" => '',
                                        "mail" => '',
                                        "owner" => '',
                                        "Disces_Ip" => $row0['DISCES_Ip'],
                                        "address" =>'',
                                        "reference_person"=>''
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
                        $list['process_name'] =    $process_list[0]['process_name'];
                        $list['process_name_ST'] = $process_list[0]['process_name_ST'];
                        $list['KB_Ip']           = $kbHostUrl;
                        $list['Graph_Uri']       = $process_list[0]['Graph_Uri'];
                        $list['process_path']    = $process_list[0]['process_path'];
                        $list['process_type']    = $process_list[0]['process_type'];
                        //$list['licence']         = $process_list[0]['licence'];
                        //$list['webpage']         = $process_list[0]['webpage'];
                        //$list['telephone']       = $process_list[0]['telephone'];
                        $list['phoenix_table']   = $process_list[0]['phoenix_table'];
                        //$list['mail']            = $process_list[0]['mail'];
                        //$list['owner']           = $process_list[0]['owner'];
                        $list['disces_ip']       = $process_list[0]['Disces_Ip'];
                        $list['creator'] = $OwnerHarsh;
                        //$list['address'] =              $process_list[0]['address'];
                        //$list['reference_person'] =     $process_list[0]['reference_person'];
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
                    $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
                    $url_api       = "https://www.snap4city.org/mypersonaldata/api/v1/public/kpidata/?sourceRequest=";
                    $payload       = file_get_contents($url_api, $context, false);
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
                    case "check_iot": 
                        $url = $_REQUEST['link'];
                        //////
                        $result = array();
                            $array = get_headers($url);
                            $string = $array[0];
                            if(strpos($string,"200"))
                              {
                                //echo 'url exists';
                                 $result['result'] = true;
                              }
                              else
                              {
                                  $result['result'] = false;
                                //echo 'url does not exist';
                              }
                        ///
                        echo json_encode($result);
                    break;
                    //case 'External Service':
                    //case 'Complex Event':
                    default:
                        //echo('Ciao');
                        $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                        mysqli_set_charset($link, 'utf8');
                        mysqli_select_db($link, $dbname_processes);
                        $process_list = array();
                        //
                        $p_name='';
                         if (($type == 'POI')||($type =='External Service')||($value_name=='')||($value_name =='ExternalContent')){
                        //if (($type == 'POI')||($value_name=='')||($value_name =='ExternalContent')){
                             $p_name = $subnature;
                         $query0 = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='".$subnature."';";
                        }else{
                            $p_name = $value_name;
                        $query0 = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='".$value_name."';";
                        }
                        //$query0 = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='".$value_name."';";
                        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        $total0       = $result0->num_rows;
                        $process_st0  = '';
                        $process_path = '';
                        if ($total0 > 0) {
                            while ($row0 = mysqli_fetch_assoc($result0)) {
                                $listFile0    = array(
                                    "licence" => $row0['licence'],
                                    "webpage" => $row0['webpage'],
                                    "telephone" => $row0['telephone'],
                                    "mail" => $row0['mail'],
                                    "owner" => $row0['responsible'],
                                    "address" =>$row0['address'],
                                    "reference_person"=>$row0['reference_person']
                                );
                                array_push($process_list, $listFile0);
                            }
                        }
                        if (count($process_list) > 0) {
                            $list['licence']           = $process_list[0]['licence'];
                            $list['webpage']           = $process_list[0]['webpage'];
                            $list['telephone']         = $process_list[0]['telephone'];
                            $list['mail']              = $process_list[0]['mail'];
                            $list['owner']             = $process_list[0]['owner'];
                            $list['address'] =              $process_list[0]['address'];
                            $list['reference_person'] =     $process_list[0]['reference_person'];
                            $list['process_name']                 = $p_name;
                        }
                         //echo json_encode($list);
                
                    //
                    echo json_encode($list);
                    break;
            }
            /*** Fine Types ***/
            
            
        } else {
            //echo ("You are not authorized to access ot this data!");
            //exit;
        }
        
    } else {
        //echo ("You are not authorized to access ot this data!");
        //exit;
    }
    
  //}else{
        //echo ("You are not authorized to access ot this data!");
       // exit;
  //}
//}else{
        //echo ("You are not authorized to access ot this data!");
        //exit;
 //   }
?>