<?php
//include('process-form.php');
include '../config.php';
require '../sso/autoload.php';
error_reporting(0);
if (isset($_REQUEST['parameters'])){
    $base_url = $_REQUEST['parameters'];
}else{
     $base_url = '';
}
//
if (isset($_REQUEST['unique_id'])){
    $unique_id = $_REQUEST['unique_id'];
}else{
    // $unique_id = '373773207E330101';
}
if (isset($_REQUEST['valueName'])){
    $valueName = '&valueName='.$_REQUEST['valueName'];
}else{
     $valueName = '';
}
//
//fromTime
if (isset($_REQUEST['fromTime'])){
    $fromTime = '&fromTime='.$_REQUEST['fromTime'];
}else{
    // $fromTime = '&fromTime=2020-02-01T00:00:00';
       $fromTime =  '';
}
//toTime
if (isset($_REQUEST['toTime'])){
    $toTime = '&toTime='.$_REQUEST['toTime'];
}else{
     //$toTime = '&toTime=2021-03-01T00:00:00';
     $toTime = '';
}
 $result_data['data'] = [];
 //
//$data_api ='https://helsinki.snap4city.org/ServiceMap/api/v1/?serviceUri=http://www.disit.org/km4city/resource/iot/orionFinland/Helsinki/373773207E330101&format=json&fromTime=2020-02-01T00:00:00&toTime=2021-03-01T00:00:00&valueName=PM10';
//$data_api ='https://helsinki.snap4city.org/ServiceMap/api/v1/?serviceUri=http://www.disit.org/km4city/resource/iot/orionFinland/Helsinki/'.$unique_id.'&format=json'.$fromTime.''.$toTime.''.$valueName;
//
$data_api = $base_url.$fromTime.''.$toTime.''.$valueName;
//echo($data_api);
$result=file_get_contents($data_api);
$data = json_decode($result, true);
//print_r($data);
// $result_data['data']['results']=$data['Sensor']['features'][0]['properties'];
//
$list_header = $data['realtime']['head']['vars'];
//print_r($list_header);
//

$data_json = $data['realtime']['results']['bindings'];

$data_js2 = json_encode($data_json);

//$result_data = array();
$array_times = array();
$pm10_array = array();
//
//
//$result_data['data']['measuredtime']=$array_times;
//$result_data['data']['pm10']=$pm10_array;

//
$n = count($data_json);
//
//$list_header
/*
        foreach ($list_header as $value) {
                $array_data = array();
            for ($i = 0; $i < $n; $i++) {
                    //echo $i;
                     $array_data[$i] = $data['realtime']['results']['bindings'][$i][$value]['value'];
                    //print_r($data['realtime']['results']['bindings'][$i][$value]['value']);
            }
            $result_data['data'][$value]=$array_data;
}*/
///////////////////************************//////////////////
$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//error_reporting(-1);
$result_data['input'] = "";
mysqli_select_db($link, $dbname);
$result_data['user'] = null;
/*
$query_own ="SELECT DISTINCT unique_name_id, ownerHash FROM DashboardWizard WHERE parameters LIKE '%".$base_url."%';";
                $result_own = mysqli_query($link, $query_own) or die(mysqli_error($link));
               // print_r($result_own);
                while ($row_own = mysqli_fetch_assoc($result_own)) {
                    $OwnerHarsh_01 = $row_own['ownerHash'];
                    if (($OwnerHarsh_01 != null)&&($OwnerHarsh_01 !=="")){
                          $decryptedOwn = decryptOSSL($OwnerHarsh_01, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
                          $OwnerHarsh = $decryptedOwn;
                          $result_data['user'] = $OwnerHarsh;
                    }
                    
                    //print_r($decryptedOwn);
                }*/
            
///////////////////***************************///////////////////////
for ($i = 0; $i < $n; $i++) {
    foreach ($list_header as $value) {
       
        if ($value == 'measuredTime'){
            $data01 = $data['realtime']['results']['bindings'][$i][$value]['value'];
            $data02 = explode("+",$data01);
            $result_data['data'][$i][$value]= $data02[0];
        }else{
            $data01 = $data['realtime']['results']['bindings'][$i][$value]['value'];
            if (($data01 == "")||($data01 == null)){
              // $result_data['data'][$i]['value']= 0; 
            }else{
               if( is_numeric($data01)){
                  // $result_data['data'][$i]['value']= str_replace('.',',',$data01);
                   $result_data['data'][$i]['value']=$data01;
               }else{
                   //$result_data['data'][$i]['value']=0;
               }
             
            }
         
        }
        //$result_data['data'][$i][$value]=$data['realtime']['results']['bindings'][$i][$value]['value'];
    }
}
$arr01= array_reverse($result_data['data']);
$result_data['data'] = $arr01;
$count = count($arr01);
//$result_data['count'] = $count;
$intdiv= floor($count /15);
//$result_data['interval'] = $intdiv;
if ($count > 15){
    //$result_data['data'] = null;
    $insert=0;
    $index = 0;
    for ($i=0; $i<$count; $i++){
        $insert++;
        if ($insert == $intdiv){
            $array_agg[$index]=$arr01[$i];
            //array_push($array_agg,$arr01[$i]);
            $insert = 0;
            $index++;
        }
       
    }
    //var_dump($array_agg);
    $result_data['data']=$array_agg;
}
///
$output = json_encode($result_data);
//
//echo($data_js2);
echo($output);

?>