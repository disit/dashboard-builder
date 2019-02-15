<?php
$url = $_SERVER['REQUEST_URI'];
$data = explode("?url=",$url);
$pos1= stripos($data[1], 'wfs');
if ($pos1 === false) {
$data_url = file_get_contents($data[1]);
$manage = json_decode($data_url, true);
if (isset($manage['Service'])){
$row =$manage['Service'];    
}else{
$row =$manage['Services'];
}
echo json_encode($row);
}else{
$data_url = file_get_contents($data[1]);
echo($data_url);
}
?>