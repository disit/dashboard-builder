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

require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

include '../config.php';
error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if (isset($_SESSION['refreshToken'])) {
  $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
  $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

  $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

  $accessToken = $tkn->access_token;
  $_SESSION['refreshToken'] = $tkn->refresh_token;
  $response['access_token'] = $accessToken;
  
  $json = http_get($iotAppApiBaseUrl."/v1/?op=list&accessToken=" . $accessToken);
  if ($json['httpcode'] == 200) {
    $response['applications'] = array();
    foreach ($json['result'] as $app) {
      $a = $app;
      $a['name'] = htmlspecialchars($a['name']);
      if($a['type']!='edge') {
        if($a['type']=='plumber') {
          $a['icon'] = 'dataAnalyticIcon.png';
          @$a['iotapps'] = join(',', $a['iotapps']);
        }
        else if($a['type']=='portia') {
          $a['icon'] = 'portiaIcon.png';
        }
        else
          $a['icon'] = $a['type']=='basic' ? 'iotAppBasicIcon.png' : 'iotAppAdvIcon.png';
      } else {
        $os = explode('_', $a['edgetype']);
        $os = $os[0];
        if($os==='win32') 
          $a['icon'] = 'iotAppBasicPcIcon.png';
        else if($os=='android')
          $a['icon'] = 'iotAppBasicMobileIcon.png';
        else
          $a['icon'] = 'iotAppBasicRaspberryIcon.png';
      }
      $a['dashboards'] = array();
      //search for connected dashboards
      $q = "SELECT DISTINCT id_dashboard as dashboardId,title_header as dashboardName, user as dashboardAuthor FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ON d.Id=w.id_dashboard WHERE appId='$app[id]' AND d.deleted='no'";
      $r = mysqli_query($link, $q);
      if($r)
      {
          while($row = mysqli_fetch_assoc($r))
          {
              $row['dashboardName'] = htmlspecialchars($row['dashboardName']);
              array_push($a['dashboards'], $row);
          }         
      }
      $response['applications'][] = $a;
    }
    $response['detail'] = 'Ok';
  } else {
    $response['detail'] = 'Ko';
    $response['error'] = $json['result'];
  }
} else {
  $response['detail'] = 'Ko';
  $response['error'] = 'no refresh token';
}
$response['refresh_token'] = $_SESSION['refreshToken'];
echo json_encode($response);

function http_get($url) {
  $opts = array('http' =>
      array(
          'method' => 'GET',
      )
  );

  # Create the context
  $context = stream_context_create($opts);
  # Get the response (you can use this for GET)
  $result = file_get_contents($url, false, $context);
  //echo "result:$result\n";
  //var_dump($http_response_header);
  return array("httpcode" => explode(" ", $http_response_header[0])[1], "result" => json_decode($result,true));
}
