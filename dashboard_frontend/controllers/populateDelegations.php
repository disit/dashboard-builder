<?php
include '../config.php'; 
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start();
error_reporting(E_ERROR);
date_default_timezone_set('Europe/Rome');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$q = "SELECT * FROM Dashboard.Config_dashboard WHERE visibility='public' ORDER BY Id ASC";
$r = mysqli_query($link, $q);

if($r)
{
    while($row = mysqli_fetch_assoc($r))
    {
        $dashboardAuthor = $row['user'];
        $callBody = ["usernameDelegated" => "ANONYMOUS", "elementId" => $row['Id'], "elementType" => "DashboardID", "sourceRequest" => "dashboardmanager"];            
        $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($dashboardAuthor) . "/delegation";

        $options = array(
              'http' => array(
                      'header'  => "Content-type: application/json\r\n",
                      'method'  => 'POST',
                      'timeout' => 30,
                      'content' => json_encode($callBody),
                      'ignore_errors' => true
              )
        );

        try
        {
            $context  = stream_context_create($options);
            $callResult = file_get_contents($apiUrl, false, $context);
        }
        catch(Exception $ex) 
        {
            //$response['detail'] = 'ApiCallKo';
        }
    }
    
    
}