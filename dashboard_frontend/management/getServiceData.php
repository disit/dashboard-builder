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
$link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server !!");
            mysqli_set_charset($link, 'utf8');
            mysqli_select_db($link, $dbname_processes);
            $process = '';
            $process_list = array();

            $query0 ="SELECT * FROM graph_dataset, process_responsible WHERE graph_dataset.Process_RT='".$value."' AND process_responsible.process_name=graph_dataset.Process_RT";
            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
            $total0  = $result0->num_rows;
            $process_st0 = '';
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
                                    );
                                array_push($process_list, $listFile0);
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
        }
    //
    ////
    echo json_encode($list);
//

?>