<?php
include '../config.php';
/* @var $value type */
$value = $_GET['service'];
$payload = file_get_contents($value);
$result = json_decode($payload);
$list = array();

$healthiness = $result->healthiness;
$serviceUri = $result->Service->features[0]->properties->serviceUri;
$serviceUri_arr =  explode("/", $serviceUri);
$n = count($serviceUri_arr);
$value = $serviceUri_arr[$n-1];
$link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
            mysqli_set_charset($link, 'utf8');
            mysqli_select_db($link, $dbname_processes);
            $process = '';
            $process_list = array();

            $query0 ="SELECT * FROM process_manager_graph, process_manager_responsible WHERE process_manager_graph.Process_RT='".$value."' AND process_manager_responsible.process_name=process_manager_graph.Process_RT";
            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
            $total0  = $result0->num_rows;
            $process_st0 = '';
            $process_path = '';
            if ($total0 > 0) {
                    while ($row0 = mysqli_fetch_assoc($result0)){
                                    $listFile0 = array(
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
                                        "owner"=>$row0['responsible'],
                                        "Disces_Ip"=>$row0['DISCES_Ip'],
                                    );
                                $process_path = $row0['process_path'];
                                array_push($process_list, $listFile0);
                         }
            }
            
$link_disces = mysqli_connect($disces_host, $disces_username, $disces_password) or die("failed to connect to server Disces!!");            
mysqli_set_charset($link_disces, 'utf8');
mysqli_select_db($link_disces, $disces_dbname);            
           // \\/media\\/Trasformazioni\\/Phoenix_ETL\\/Sensors\\/SmartBench\\/Smartbench_RT\\/Ingestion\\/Main.kjb
            $query1 = "SELECT JOB_NAME, JOB_GROUP FROM quartz.QRTZ_JOB_DETAILS WHERE JOB_DATA LIKE '%".mysqli_real_escape_string($link, $process_path)."%' escape '|'";
            $result1 = mysqli_query($link_disces, $query1) or die(mysqli_error($link_disces));
            $total1  = $result1->num_rows;
            $jobn = '';
            $jobg = '';
            if($total1 > 0){
                    while ($row1 = mysqli_fetch_assoc($result1)){
                        $jobn = $row1['JOB_NAME'];
                        $jobg = $row1['JOB_GROUP'];
                    }
            }
            
            $ip_disc = '';
            $query_ip = "SELECT distinct IP_ADDRESS FROM quartz.QRTZ_NODES LIMIT 1;";
            $result_ip = mysqli_query($link_disces, $query_ip) or die(mysqli_error($link_disces));
             $total_ip  = $result_ip->num_rows;
            if($total_ip > 0){
                    while ($row_ip = mysqli_fetch_assoc($result_ip)){
                        $ip_disc = $row_ip['IP_ADDRESS'];
                    }
            }
    //
    $list['healthiness'] = $healthiness;
    $list['realtime'] = $result->realtime;
    $list['Service'] = $result->Service;
        if(count($process_list)>0){
            $list['process_name_ST'] = $process_list[0]['process_name_ST'];
            $list['KB_Ip'] = $process_list[0]['KB_Ip'];
            $list['Graph_Uri'] = $process_list[0]['Graph_Uri'];
            $list['process_path'] = $process_list[0]['process_path'];
            $list['process_type'] = $process_list[0]['process_type'];
            $list['licence'] = $process_list[0]['licence'];
            $list['webpage'] = $process_list[0]['webpage'];
            $list['telephone'] = $process_list[0]['telephone'];
            $list['phoenix_table'] = $process_list[0]['phoenix_table'];
            $list['mail'] = $process_list[0]['mail'];
            $list['owner']=$process_list[0]['owner'];
            $list['disces_ip']=$process_list[0]['Disces_Ip'];
            $list['disces_data']= $total1;
            $list['jobName']= $jobn;
            $list['jobGroup']=$jobg;
            $list['ip_disc']=$ip_disc;
        }
    //
    ////
    echo json_encode($list);
//

?>