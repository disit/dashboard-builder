<?php
include '../config.php';
require '../sso/autoload.php';
//
$link_dash = mysqli_connect($host, $username, $password) or die("failed to connect to server Processes !!");
            mysqli_set_charset($link_dash, 'utf8');
            mysqli_select_db($link_dash, $dbname);
//
$n_dash = $_GET['dashboard'];
$interval=$_GET['interval'];
//$start = "CURRENT_DATE";
$start = date('yy-m-d');
$end = date('yy-m-d', strtotime('-'.$interval));
//
if (isset($_GET['direction'])){
    if (isset($_GET['current_date'])){
        $start = $_GET['current_date'];
        
        //$start = date('Y-m-d',$start);
    }
    $dir= $_GET['direction'];
    if($dir == 'previous'){
        $end = date($start, strtotime('-'.$interval));
    }elseif($dir == 'next'){
        $end = date($start, strtotime('+'.$interval));
    }
}
if (isset($_GET['end_date'])){
    $min_data = $_GET['end_date'];
}else{
    if($dir == 'previous'){
        $end = date($start, strtotime('-'.$interval));
    }elseif($dir == 'next'){
        $end = date($start, strtotime('+'.$interval));
    }
}

if (isset($_GET['current_date'])){
    $max_data = $_GET['current_date'];
}else{
    $max_data = date('yy-m-d');
}

//Data_semplificata
$query_dash = "SELECT IdDashDailyAccess.*, Config_dashboard.name_dashboard FROM IdDashDailyAccess, Config_dashboard WHERE IdDashDailyAccess.IdDashboard=Config_dashboard.Id AND name_dashboard = '".$n_dash."' AND date >= '".$min_data."' AND date <= '".$max_data."' ORDER BY IdDashDailyAccess.Date ASC;";
//

            //
$result_dash = mysqli_query($link_dash, $query_dash) or die(mysqli_error($link_dash));
mysqli_close($link_dash);
$total1 = $result_dash->num_rows;
$list0 = array();
$list1 = array();
$list2 = array();
$list_final = array();
            while ($row = mysqli_fetch_assoc($result_dash)) {
                        $listFile0    = array(
                            intval($row['nAccessPerDay'])
                        );
                        $listFile1    = array(
                            intval($row['nMinutesPerDay'])
                        );
                        $listFile2    = array(
                            date($row['date'])
                        );
                        array_push($list0, $listFile0);
                        array_push($list1, $listFile1);
                        array_push($list2, $listFile2);
                  }
                  $list_final['AccessPerDay'] = $list0;
                  $list_final['MinutesPerDay'] = $list1;
                  $list_final['dates']= $list2;
                  $list_final['start']=$start;
                  $list_final['end']=$end;
//
echo json_encode($list_final);
//

?>