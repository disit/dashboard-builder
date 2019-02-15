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
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']))
{

    if(isset($_SESSION['refreshToken']))
    {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        $queryLimits = $ownershipApiBaseUrl."/v1/limits/?accessToken=".$accessToken;
        $queryLimitsResults = file_get_contents($queryLimits);
        $limitsResultArray = json_decode($queryLimitsResults, true);
        foreach ($limitsResultArray['limits'] as $limit) {
            if($limit['elementType'] == "DashboardID") {
                $dashIdLimit = $limit['limit'];
                $dashIdCurrent = $limit['current'];
                if ($limit['limit'] - $limit['current'] <= 0) {
                    $limitCheckResult = false;
                } else {
                    $limitCheckResult = true;
                }
            }
        }

        if($limitCheckResult === true)
        {
            $response['detail'] = 'DashboardLimitsOk';
            $response['dashIdLimit'] = $dashIdLimit;
            $response['dashIdCurrent'] = $dashIdCurrent;
        }
        else
        {
            $response['detail'] = 'DashboardLimitsKo';
            $response['dashIdLimit'] = $dashIdLimit;
            $response['dashIdCurrent'] = $dashIdCurrent;
        }
    } else {
        $response['detail'] = 'DashboardLimitsKo';
    }

    echo json_encode($response);
}

