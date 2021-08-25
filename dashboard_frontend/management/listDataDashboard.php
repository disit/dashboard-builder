<?php
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start();
checkSession('Public');
$link = mysqli_connect($host, $username, $password);
        mysqli_select_db($link, $dbname);

$service0  = mysqli_real_escape_string($link,$_REQUEST['service']);
$service = filter_var($service0 , FILTER_SANITIZE_STRING);
//$homepage = file_get_contents($service);
if(get_http_response_code($service) != "200"){
   $homepage = "error";
   $final_rep['message']=$homepage;
   echo json_encode($final_rep);
   //echo $homepage;
}else{
   $homepage = file_get_contents($service);
   //ATTIVITA DI ESTRAZINE INFORMAZIONI WIDGET//
   $data = json_decode($homepage, true);
   $array = $data['widget-data'];
   $n = count($array);
   $array_result= array();
   for ($i =0; $i <$n; $i++){
       $widget = strval($array[$i]['widget']); 
        $widget_1 = explode("Widget_", $widget);
       $widget_code = $widget_1[1];
       $array_result[$i]['type_w'] = $array[$i]['type'];
       $array_result[$i]['title_w'] = $array[$i]['title'];
       $name = $array[$i]['data'][0]['name'];
//
       if (( $array[$i]['type'] == 'widgetExternalContent')||( $array[$i]['type'] == 'widgetSelector')||( $array[$i]['type'] == 'widgetButton')) {
           $array_result[$i]['unique_name_id'] = "";
       }else{
       $query = "SELECT DISTINCT unique_name_id FROM dashboardwizard WHERE get_instances='".$name."' LIMIT 1;";
       $result = mysqli_query($link, $query);
      // echo ($query.'<br />');
      if ($result){
               while ($row = $result->fetch_assoc()) {
                        $unique_name_id = $row['unique_name_id'];
                        $array_result[$i]['unique_name_id'] = strval($unique_name_id);
              }
       }
       //
     }
       //widgetButton
       $resource1="http://www.disit.org/km4city/resource/";
       if(strpos($name, $resource1) !== false){
                    //echo "Word Found!";
                     $array_result[$i]['unique_name_id'] = str_replace($resource1,"",$name);
          } 
       //       
   } 
       $final_rep['message']='ok';
       $final_rep['data']=$data;
       $final_rep['desc']=$array_result;
       echo json_encode($final_rep);
   //
}

       
function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}
?>