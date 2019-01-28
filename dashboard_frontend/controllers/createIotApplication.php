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

$response = [];

if(!isset($_REQUEST['name'])) {
  exit;
}

$type = 'basic';
if(isset($_REQUEST['type'])) {
  $type = $_REQUEST['type'];
}

if (isset($_SESSION['refreshToken'])) {
  $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
  $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

  $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

  $accessToken = $tkn->access_token;
  $_SESSION['refreshToken'] = $tkn->refresh_token;
  //echo $_SESSION['refreshToken'];

  $json = http_get($iotAppApiBaseUrl."/v1/?op=new_nodered&name=".urlencode($_REQUEST['name'])."&type=".urlencode($type)."&accessToken=" . $accessToken);
  if ($json['httpcode'] == 200 && !isset($json['result']["error"])) {
    $response['detail'] = 'Ok';
    $response['result'] = $json['result'];
  } else {
    $response['detail'] = 'Ko';
    if(isset($json['result']["error"]["error"]))
      $response['error'] = $json['result']["error"]["error"];
    else
      $response['error'] = $json['result']["error"];
  }
} else {
  $response['detail'] = 'Ko';
  $response['error'] = 'no refresh token';
}

echo json_encode($response);

function http_get($url) {
  $opts = array('http' =>
      array(
          'method' => 'GET',
          'ignore_errors' => true 
      )
  );

  # Create the context
  $context = stream_context_create($opts);
  # Get the response (you can use this for GET)
  $result = file_get_contents($url, false, $context);
  $json_result = json_decode($result, true);
  if($json_result===null || $json_result===false)
    $json_result = json_encode($result, true);
  //var_dump($http_response_header);
  return array("httpcode" => explode(" ", $http_response_header[0])[1], "result" => $json_result, "url"=>$url);
}
