<?php
include '../config.php'; 
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start();
error_reporting(E_ERROR);
date_default_timezone_set('Europe/Rome');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$q = "SELECT * FROM Dashboard.Config_dashboard ORDER BY Id ASC";
$r = mysqli_query($link, $q);

if($r)
{
    while($row = mysqli_fetch_assoc($r))
    {
        if(isset($_SESSION['refreshToken'])) 
        {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

            $accessToken = $tkn->access_token;

            //$file = fopen("dashboardLog.txt", "w");
            //fwrite($file, "Access token: " . $accessToken . "\n");

            //$result['accessToken'] = $accessToken;

            $_SESSION['refreshToken'] = $tkn->refresh_token;

            $callBody = ["elementId" => $row['Id'], "elementType" => "DashboardID", "elementName" => $row['title_header']];

            $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

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
                $callResult = @file_get_contents($apiUrl, false, $context);
            }
            catch (Exception $ex) 
            {
                //Non facciamo niente di specifico in caso di mancata risposta dell'host
            }
        }
    }
    
    
}
