<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

//include 'process-form.php';
include '../config.php';
include '../session.php';
require '../sso/autoload.php';
header('Access-Control-Allow-Origin: *');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

use Jumbojett\OpenIDConnectClient;

$response = [];

$accessToken = null;
$loggedUsername = null;
$loggedRole = null;
if(isset($_GET['accessToken'])) {
    $oidc = new OpenIDConnectClient();
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
    $oidc->setAccessToken($_GET['accessToken']);
    $uinfo = $oidc->requestUserInfo();
    if(isset($uinfo->error)) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode($uinfo);
        die();
    }
    else {
        $accessToken = $_GET['accessToken'];
        $loggedUsername = $uinfo->preferred_username;
        if ($loggedUsername == null) {
            $loggedUsername = $uinfo->username;
        }
        $ROLES = array('RootAdmin','ToolAdmin','AreaManager','Manager','Observer','Public');
        if(isset($uinfo->roles)) {
            foreach($ROLES as $r) {
                if(in_array($r, $uinfo->roles)) {
                    $loggedRole = $r;
                    break;
                }
            }
        }
    }
}
else if($_SESSION['accessToken'] && $_SESSION["loggedUsername"] && $_SESSION['loggedRole']) {
    $accessToken = $_SESSION['accessToken'];
    $loggedUsername = $_SESSION["loggedUsername"];
    $loggedRole = $_SESSION['loggedRole'];
}
else {
    // header("Location: ../management/ssoLogin.php?redirect=".urlencode("$appUrl/api/getEncryptedUserInfo.php"));
    header("HTTP/1.1 401 Unauthorized");
    $response['responseState'] = "Unauthorized request: accessToken not present.";
    //  echo json_encode($response['responseState']);
    echo ($response['responseState']);
    die();
}


if($accessToken && $loggedUsername) {
//if (isset($_GET['username'])) {
    //    $encryptionInitKey = "EncryptionIniKey";
    //    $encryptionIvKey = "IVKeyivKey123456";
    if(isset($_GET['organizationName'])) {
        $orgName = mysqli_real_escape_string($link, $_GET['organizationName']);
        $query = "SELECT * FROM Organizations WHERE organizationName = '$orgName';";
        $result = mysqli_query($link, $query);
        $response = [];
        $responseParam = [];
        $response['organizationParams'] = [];
        if ($result) {
            if ($row = mysqli_fetch_assoc($result)) {
                $responseParam['organizationName'] = $row['organizationName'];
            //    array_push($response['kbUrl'], $row['kbUrl']);
                $responseParam['gpsCentreLat'] = trim(explode(",", $row['gpsCentreLatLng'])[0]);
                $responseParam['gpsCentreLng'] = trim(explode(",", $row['gpsCentreLatLng'])[1]);
                $responseParam['zoomLevel'] = $row['zoomLevel'];
            //    $responseParam['lang'] = $row['lang'];
            //    $responseParam['broker'] = $row['broker'];
            //    $responseParam['orionIP'] = $row['orionIP'];
            //    $responseParam['orthomapJson'] = $row['orthomapJson'];
            //    $responseParam['kbIP'] = $row['kbIP'];
            }
            array_push($response['organizationParams'], $responseParam);
            $response['responseState'] = "Successful response";
        } else {
            $response['responseState'] = "No results found.";
        }
    } else {
        $response['responseState'] = "Invalid or Null Organization name. Please specify a valid Organization name.";
    }
//}
} else {
    header("HTTP/1.1 401 Unauthorized");
    $response['responseState'] = "Unauthorized request.";
    //  echo json_encode($response['responseState']);
    echo ($response['responseState']);
    die();
}

echo json_encode($response);
