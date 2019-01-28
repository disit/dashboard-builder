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

    $envFileContent = parse_ini_file("conf/environment.ini"); 
    $activeEnv = $envFileContent["environment"]["value"];
   
    $filesList = scandir("../conf/");
    $j = 0;
    
    for($i = 0; $i < count($filesList); $i++)
    {
        if(($filesList[$i] != ".")&&($filesList[$i] != "..")&&($filesList[$i] != "environment.ini"))
        {
            $fileContent = parse_ini_file("../conf/" . $filesList[$i]);
           
            foreach($fileContent as $key => $value) 
            {
                if(($key != "fileDesc")&&($key != "customForm"))
                {
                    if(is_array($value))
                    {
                        $varName = $key;
                        $$varName = $fileContent[$key][$activeEnv];
                    }
                }
            }
           $j++;
        }
    }

    if($ssoEndpointsFromUrl=='yes') {
      //infer SSO settings from URL
      $http_host='main.snap4city.org';
      if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $http_host=$_SERVER['HTTP_X_FORWARDED_HOST'];
      } else if(isset($_SERVER['HTTP_HOST'])) {
        $http_host=$_SERVER['HTTP_HOST'];
      }
      if(strpos($http_host, '192.168.') === false && strpos($http_host, 'localhost') === false) {
        $h=explode('.',$http_host);
        unset($h[0]);
        $http_domain = implode('.', $h);
      } else {
        $http_domain='snap4city.org';
      }

      if($http_host=='main.snap4city.org')
        $appUrl = "https://$http_host"; //'https://main.snap4city.org';
      else if(strpos($http_host, '192.168.') === 0 || strpos($http_host, 'localhost') === 0)
        $appUrl = "http://$http_host/dashboardSmartCity"; //'https://main.snap4city.org';
      else
        $appUrl = "https://$http_host/dashboardSmartCity"; //'https://main.snap4city.org';

      $ssoEndpoint = "https://www.$http_domain";
      $ssoTokenEndpoint = "https://www.$http_domain/auth/realms/master/protocol/openid-connect/token";
      $ssoAuthorizationEndpoint = "https://www.$http_domain/auth/realms/master/protocol/openid-connect/auth";
      $ssoUserinfoEndpoint = "https://www.$http_domain/auth/realms/master/protocol/openid-connect/userinfo";
      $ssoJwksUri = "https://www.$http_domain/auth/realms/master/protocol/openid-connect/certs";
      $ssoIssuer = "https://www.$http_domain/auth/realms/master";
      $ssoEndSessionEndpoint = "https://www.$http_domain/auth/realms/master/protocol/openid-connect/logout";
    }
    require_once 'common.php';