<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
session_write_close();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$delegated = [];
if(isset($_SESSION['loggedUsername'])) 
{
    $username = $_SESSION['loggedUsername'];
    $vname = $_REQUEST['variableName'];
/*    switch($vname) {
      case 'Num_Utenti_globali_connessi':
      case 'Num_Utenti_distinti_globali':
      case 'Timestamp_estrazione_dati':
      case 'Num_Connessioni_globali_giornaliere':
      case 'percentual_charging_active':
      case 'total_number':
      case 'percentual_station_active':

        $link = mysqli_connect($sql_host_pd, $usrDb, $pwdDb);
        mysqli_select_db($link, "profiledb");
        $r = mysqli_query($link, "SELECT unix_timestamp(data_time) as data_time_uts,app_name,app_id,variable_value,variable_unit FROM data WHERE variable_name ='$vname' AND username='$username' ORDER BY data_time DESC LIMIT 1");
        if($r && $row=  mysqli_fetch_assoc($r)) {
          $result='[{"username":"'.$username.'","dataTime":'.$row['data_time_uts'].
                  ',"APPName":"'.$row['app_name'].'","APPID":"'.$row['app_id]'].
                  '","motivation":"","variableName":"'.$vname.'","variableValue":"'.$row['variable_value'].'","variableUnit":"'.$row['variable_unit'].'"}]';
          echo $result;
          exit;
        } 
    }*/
    if(isset($_SESSION['refreshToken'])) 
    {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        
        if(isset($_REQUEST['fromTime']))
        {
            switch($_REQUEST['fromTime'])
            {
                case "4-hour":
                    $startTimeEpoch = time() - 14400;
                    break;
                
                case "12-hour":
                    $startTimeEpoch = time() - 43200;
                    break;
                
                case "1-day":
                    $startTimeEpoch = time() - 86400;
                    break;
                
                case "7-day":
                    $startTimeEpoch = time() - 604800;
                    break;
                
                case "30-day":
                    $startTimeEpoch = time() - 2592000;
                    break;
                
                case "365-day":
                    $startTimeEpoch = time() - 31536000;
                    break;
            }
            
            $startDate = date("Y-m-d", $startTimeEpoch);
            $startTime = date("H:i:s", $startTimeEpoch);
            
            $startTimeFormatted = $startDate . "T" . $startTime;
            
            //$todayDate = date("Y-m-d", time()) . "T00:00:00";
            $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($_SESSION['loggedUsername']) . "/data?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&variableName=" . $_REQUEST['variableName'] . "&from=" . $startTimeFormatted;
        }
        else
        {
            if(isset($_REQUEST['']))
            {
                $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($_SESSION['loggedUsername']) . "/data?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&variableName=" . $_REQUEST['variableName'] . "&last=" . $_REQUEST['last'] . "&motivation=" . urlencode($_REQUEST['motivation']);
            }
            else
            {
                $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($_SESSION['loggedUsername']) . "/data?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&variableName=" . $_REQUEST['variableName'] . "&last=" . $_REQUEST['last'];
            }
        }
                    
        $options = array(
            'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'GET',
                    'timeout' => 30,
                    'ignore_errors' => true
            )
        );

        $context  = stream_context_create($options);
        $j1=$j2=array();
        $myPersonalDataJson = file_get_contents($apiUrl, false, $context);
        if($myPersonalDataJson)
          $j1=json_decode($myPersonalDataJson, true);
        $myPersonalDataJson = file_get_contents($apiUrl."&delegated=true", false, $context);
        if($myPersonalDataJson)
          $j2=json_decode($myPersonalDataJson, true);
        $result=array_merge($j1,$j2);
        if(count($j1)>0 && count($j2)>0) {
          usort($result,"cmpDataTime");
        }
        echo json_encode($result);
    }
}

function cmpDataTime($a, $b) {
    if ($a['dataTime'] == $b['dataTime']) {
        return 0;
    }
    return ($a['dataTime'] < $b['dataTime']) ? -1 : 1;
}
