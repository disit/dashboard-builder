<?php
include '../config.php'; 

error_reporting(E_ERROR);
date_default_timezone_set('Europe/Rome');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$q = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE appId IS NOT NULL";
echo $q . "<br>";
$r = mysqli_query($link, $q);

if($r)
{
    while($row = mysqli_fetch_assoc($r))
    {
         $metricId = $row['id_metric'];
         $appId = $row['appId'];
         $flowId = $row['flowId'];
         
         $q2 = "UPDATE Dashboard.NodeRedInputs SET appId = '$appId', flowId = '$flowId' WHERE NodeRedInputs.name = '$metricId'";
         echo $q2 . "<br>";
         $r2 = mysqli_query($link, $q2);
    }
    
    
}
