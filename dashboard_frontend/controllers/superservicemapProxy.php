<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence
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
if (!isset($_SESSION)) {
    session_start();
}

if(isset($_SESSION['refreshToken'])) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;
}
session_write_close();

if(isset($_GET['url'])) {
  //TBD validate url
  $apiUrl=$_GET['url'];
} else {
  $uri = $_SERVER['REQUEST_URI'];
  $p = strpos($uri,'superservicemapProxy.php/');
  $x =  substr($uri, $p+strlen('superservicemapProxy.php/'));
  if(substr($x,0,6)=='api/v1') {
    $apiUrl = $superServiceMapUrlPrefix . $x;
  } else   if(substr($x,0,7)=='/api/v1') {
    $apiUrl = $superServiceMapUrlPrefix . $x;
  } else if(substr($x,0,7)=='http://' || substr($x,0,8)=='https://') {
    //TBD validate url
    $apiUrl=$x;
  }  else if(substr($x,0,6)=='http:/') {
    //TBD validate url
    $apiUrl="http://".substr($x,6);
  } else if(substr($x,0,7)=='https:/') {
    //TBD validate url
    $apiUrl="https://".substr($x,7);
  }
}

if(isset($apiUrl)) {
  $options = array(
      'http' => array(
          'method' => 'GET',
          'timeout' => 30,
          'ignore_errors' => true
      )
  );
  if(isset($accessToken)) {
    $options['http']['header'] = "Authorization: Bearer $accessToken\r\n";
  }
  
  $context = stream_context_create($options);
  $result = file_get_contents($apiUrl, false, $context);
  header("Content-Type: application/json");
  echo $result;
}
