<?php
include '../config.php';
/* @var $value type */
$value               = $_REQUEST['service'];
$type                = $_REQUEST['type'];
$list                = array();
$role_session_active = $_REQUEST['role_session_active'];
if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin")) {
    if ($type == 'Sensor') {
        $payload = file_get_contents($value);
        $result  = json_decode($payload);
        //$list = array();
        
        $healthiness = $result->healthiness;
        if (property_exists($result, 'Service')) {
            $serviceUri = $result->Service->features[0]->properties->serviceUri;
        } else {
            $serviceUri = $result->Sensor->features[0]->properties->serviceUri;
        }
        $serviceUri_arr = explode("/", $serviceUri);
        $n              = count($serviceUri_arr);
        $value          = $serviceUri_arr[$n - 1];
        $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbname_processes);
        $process      = '';
        $process_list = array();
        
        $query0 = "SELECT * FROM process_manager_graph, process_manager_responsible WHERE process_manager_graph.Process_RT='" . $value . "' AND process_manager_responsible.process_name=process_manager_graph.Process_RT";
        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
        $total0       = $result0->num_rows;
        $process_st0  = '';
        $process_path = '';
        if ($total0 > 0) {
            while ($row0 = mysqli_fetch_assoc($result0)) {
                $listFile0    = array(
                    "process_name_ST" => $row0['Process_ST'],
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
        $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
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
        
        $ip_disc  = '';
        $query_ip = "SELECT distinct IP_ADDRESS FROM quartz.QRTZ_NODES LIMIT 1;";
        $result_ip = mysqli_query($link_disces, $query_ip) or die(mysqli_error($link_disces));
        $total_ip = $result_ip->num_rows;
        if ($total_ip > 0) {
            while ($row_ip = mysqli_fetch_assoc($result_ip)) {
                $ip_disc = $row_ip['IP_ADDRESS'];
            }
        }
        //
        $list['healthiness'] = $healthiness;
        $list['realtime']    = $result->realtime;
        if (property_exists($result, 'Service')) {
            $list['Service'] = $result->Service;
        } else {
            $list['Service'] = $result->Sensor;
        }
        if (count($process_list) > 0) {
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
            $list['disces_data']     = $total1;
            $list['jobName']         = $jobn;
            $list['jobGroup']        = $jobg;
            $list['ip_disc']         = $ip_disc;
            $list['dataSource']      = '';
        }
        //
        ////
        echo json_encode($list);
        //
    } elseif ($type == 'KPI') {
        $value = $_REQUEST['value'];
        $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbname_processes);
        $process      = '';
        $process_list = array();
        $query0       = "SELECT dataSource, query FROM dashboard.descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value) . "'";
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
        $query_sparql = 'http://192.168.0.206:8890/sparql?query=select+distinct+%3Fgraph+%7B+graph+%3Fgraph+%7B%3C' . $pieces[1] . '%3E+a+%3Fc%7D+%3Fgraph+%3Fx+%3Fy.%7D&format=application%2Fsparql-results%2Bjson';
        $payload      = file_get_contents($query_sparql);
        $result       = json_decode($payload);
        if ($result) {
            $json_sparql  = json_encode($result);
            $json_sparql2 = json_decode($json_sparql);
            $result_query = $json_sparql2->results;
            $av           = $result_query->bindings;
            $graph_uri0   = $av[0]->graph;
            $graph_uri    = $graph_uri0->value;
            
            //
            //
            //
            $query0 = "SELECT * FROM process_manager_graph WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "' LIMIT 1";
            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
            $total0       = $result0->num_rows;
            $process_st0  = '';
            $process_path = '';
            if ($total0 > 0) {
                while ($row0 = mysqli_fetch_assoc($result0)) {
                    $listFile0    = array(
                        "process_name_ST" => $row0['Process_ST'],
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
            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
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
            
            $ip_disc  = '';
            $query_ip = "SELECT distinct IP_ADDRESS FROM quartz.QRTZ_NODES LIMIT 1;";
            $result_ip = mysqli_query($link_disces, $query_ip) or die(mysqli_error($link_disces));
            $total_ip = $result_ip->num_rows;
            if ($total_ip > 0) {
                while ($row_ip = mysqli_fetch_assoc($result_ip)) {
                    $ip_disc = $row_ip['IP_ADDRESS'];
                }
            }
            //
            //
            //$list['process_name_ST'] = $graph_uri;
            if (count($process_list) > 0) {
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
                $list['disces_data']     = $total1;
                $list['jobName']         = $jobn;
                $list['jobGroup']        = $jobg;
                $list['ip_disc']         = $ip_disc;
                $list['dataSource']      = '';
            }
            //
            ////
            echo json_encode($list);
        }
    } elseif ($type == 'Special Widget') {
        $value = $_REQUEST['value'];
        $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbname_processes);
        $process      = '';
        $process_list = array();
        $query0       = "SELECT dataSource, query FROM dashboard.descriptions WHERE IdMetric='" . mysqli_real_escape_string($link, $value) . "'";
        $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
        $total0     = $result0->num_rows;
        $dataSource = "";
        //
        //echo ("RESULT:  ".$query0);
        if ($total0 > 0) {
            while ($row = $result0->fetch_assoc()) {
                $dataSource = $row['dataSource'];
                $query_des  = $row['query'];
                //print_r($row);
                if (($query_des !== null) || ($query_des !== "")) {
                    exit;
                }
                $pieces = explode("'", $query_des);
                //print_r($query_des);
            }
            //
            echo ($query_des);
            $query_sparql = 'http://192.168.0.206:8890/sparql?query=select+distinct+%3Fgraph+%7B+graph+%3Fgraph+%7B%3C' . $pieces[1] . '%3E+a+%3Fc%7D+%3Fgraph+%3Fx+%3Fy.%7D&format=application%2Fsparql-results%2Bjson';
            $payload      = file_get_contents($query_sparql);
            $result       = json_decode($payload);
            if ($result) {
                $json_sparql  = json_encode($result);
                $json_sparql2 = json_decode($json_sparql);
                $result_query = $json_sparql2->results;
                $av           = $result_query->bindings;
                $graph_uri0   = $av[0]->graph;
                $graph_uri    = $graph_uri0->value;
                print_r($json_sparql);
                //
                //
                //
                $query0 = "SELECT * FROM process_manager_graph WHERE process_manager_graph.Graph_Uri='" . $graph_uri . "' LIMIT 1";
                $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                $total0       = $result0->num_rows;
                $process_st0  = '';
                $process_path = '';
                if ($total0 > 0) {
                    while ($row0 = mysqli_fetch_assoc($result0)) {
                        $listFile0    = array(
                            "process_name_ST" => $row0['Process_ST'],
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
                $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%" . mysqli_real_escape_string($link, $process_path) . "%' escape '|'";
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
                
                $ip_disc  = '';
                $query_ip = "SELECT distinct IP_ADDRESS FROM quartz.QRTZ_NODES LIMIT 1;";
                $result_ip = mysqli_query($link_disces, $query_ip) or die(mysqli_error($link_disces));
                $total_ip = $result_ip->num_rows;
                if ($total_ip > 0) {
                    while ($row_ip = mysqli_fetch_assoc($result_ip)) {
                        $ip_disc = $row_ip['IP_ADDRESS'];
                    }
                }
                //
                //
                //$list['process_name_ST'] = $graph_uri;
                if (count($process_list) > 0) {
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
                    $list['disces_data']     = $total1;
                    $list['jobName']         = $jobn;
                    $list['jobGroup']        = $jobg;
                    $list['ip_disc']         = $ip_disc;
                    $list['dataSource']      = '';
                }
                //
                ////
                echo json_encode($list);
            }
        } else {
            $list['process_name_ST'] = '';
            $list['KB_Ip']           = '';
            $list['Graph_Uri']       = '';
            $list['process_path']    = '';
            $list['process_type']    = '';
            $list['licence']         = '';
            $list['webpage']         = '';
            $list['telephone']       = '';
            $list['phoenix_table']   = '';
            $list['mail']            = '';
            $list['owner']           = '';
            $list['disces_ip']       = '';
            $list['disces_data']     = '';
            $list['jobName']         = '';
            $list['jobGroup']        = '';
            $list['ip_disc']         = '';
            $list['dataSource']      = '';
            echo json_encode($list);
        }
    } else {
        //nothing
    }
}
?>