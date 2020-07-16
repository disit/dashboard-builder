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

include('../config.php');
//include '../session.php';
require '../sso/autoload.php';
header('Access-Control-Allow-Origin: *');

use Jumbojett\OpenIDConnectClient;

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

$accessToken = null;
$loggedUsername = null;
$loggedRole = null;
if(isset($_GET['accessToken']) && (!empty($_GET['accessToken']))) {
    $oidc = new OpenIDConnectClient();
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
    $oidc->setAccessToken($_GET['accessToken']);
    $uinfo = $oidc->requestUserInfo();
    if(isset($uinfo->error)) {
        header("HTTP/1.1 401 Unauthorized");
        $response['result'] = 'KO';
        $response['errormsg'] = $uinfo->error . ". " . $uinfo->error_description;
        echo json_encode($response);
        die();
    }
    else {
        $accessToken = $_GET['accessToken'];
        if($uinfo->preferred_username)
            $loggedUsername = $uinfo->preferred_username;
        else
            $loggedUsername = $uinfo->username;
     //   $loggedUsername = $uinfo->preferred_username;
        $ROLES = array('RootAdmin','ToolAdmin','AreaManager','Manager','Observer','Public');
        if(isset($uinfo->roles)) {
            foreach($ROLES as $r) {
                if(in_array($r, $uinfo->roles)) {
                    $loggedRole = $r;
                    break;
                }
            }
        }
        $ldapUsername = "cn=" . $loggedUsername . "," . $ldapBaseDN;
        $ds = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if($ldapAdminDN)
            $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
        else
            $bind = ldap_bind($ds);
        $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
        $groups = [];
        $groups = checkLdapGroup($ds, $ldapUsername, $ldapBaseDN, $organization);
        if (is_null($organization)) {
            $organization = "Other";
        } else if ($organization == "") {
            $organization = "Other";
        }
    }
} else {
    // header("Location: ../management/ssoLogin.php?redirect=".urlencode("$appUrl/api/getEncryptedUserInfo.php"));
    header("HTTP/1.1 401 Unauthorized");
    $response['result'] = 'KO';
    $response['errormsg'] = "Unauthorized request: accessToken not present.";
    //  echo json_encode($response['responseState']);
    echo json_encode($response);
    die();
}

$dashboardTitle = "";
$dashboardSubtitle = "";
$username = "";
$dashboardId = "";

if($accessToken && $loggedUsername) {
    $queryLimits = $ownershipApiBaseUrl . "/v1/limits/?accessToken=" . $accessToken;
    $queryLimitsResults = file_get_contents($queryLimits);
    $limitsResultArray = json_decode($queryLimitsResults, true);
    if (sizeof($limitsResultArray) > 0) {
        foreach ($limitsResultArray['limits'] as $limit) {
            if ($limit['elementType'] == "DashboardID") {
                $dashIdLimit = $limit['limit'];
                $dashIdCurrent = $limit['current'];
                if ($limit['limit'] - $limit['current'] <= 0) {
                    $limitCheckResult = false;
                } else {
                    $limitCheckResult = true;
                }
            }
        }
    } else {
        $response['result'] = 'KO';
        $response['errormsg'] = 'Time-out Ownership API.';
    }
    $username = $loggedUsername;
    if ($limitCheckResult != false) {
        if ((isset($_GET['dashboardId'])) && (!empty($_GET['dashboardId']))) {
            $dashboardId = $_GET['dashboardId'];
            $query = "SELECT * FROM Dashboard.Config_dashboard WHERE id = '$dashboardId'";
            $result = mysqli_query($link, $query);
        } else if ((isset($_GET['dashboardTitle'])) && (!empty($_GET['dashboardTitle'])) && (isset($_GET['accessToken'])) && (!empty($_GET['accessToken']))) {
            $dashboardTitle = urldecode($_GET['dashboardTitle']);
            //    $username = $_GET['username'];              // CHECK FROM ACCESS TOKEN !
            $query = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$dashboardTitle' AND user = '$username' AND deleted != 'yes'";
            $result = mysqli_query($link, $query);

        } else {
            $response['result'] = 'KO';
            $response['errormsg'] = 'Not Valid Parameters in the Request.';
        }

        if ($result) {
            $dashboardParams = [];
            if (mysqli_num_rows($result) > 0) {
                //Dashboard già esistente
                $row = mysqli_fetch_assoc($result);
                if ($dashboardTitle == "") {
                    $dashboardTitle = $row['title_header'];
                }
                /*    if ($username == "") {
                        $username = $row['user'];
                    }*/
                $dashboardId = $row['Id'];
            /*    $dashboardAuthor = $row['user'];
                $dashboardEditor = $username;*/
                $response['result'] = 'KO';
                $response['errormsg'] = "A Dashboard You Own with title " . $dashboardTitle . " already exists!";
            } else {
                //Dashboard non esistente, viene creata
                $nCols = 15;
                $width = ($nCols * 78) + 10;
                $color = 'rgba(51, 204, 255, 1)';
                $background = '#FFFFFF';
                $externalColor = '#FFFFFF';
                $headerFontColor = 'white';
                $headerFontSize = 28;
                $viewMode = 'alwaysResponsive';
                $visibility = 'author';
                $headerVisible = 1;
                $embeddable = 'yes';
                $authorizedPagesJson = '[]';

                $lastUsedColors = [
                    "rgba(51, 204, 255, 1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                    "rgba(255,255,255,1)",
                ];

                $lastUsedColorsJson = json_encode($lastUsedColors);

                $query2 = "INSERT INTO Dashboard.Config_dashboard " .
                    "(name_dashboard, title_header, subtitle_header, color_header, width, height, num_rows, num_columns, user, status_dashboard, creation_date, color_background, external_frame_color, headerFontColor, headerFontSize, logoFilename, logoLink, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date, lastUsedColors, gridColor, organizations) " .
                    "VALUES ('$dashboardTitle', '$dashboardTitle', '$dashboardSubtitle', '$color', '$width', 0, 0, $nCols, '$username', 1, CURRENT_TIMESTAMP, '$background', '$externalColor', '$headerFontColor', $headerFontSize, NULL, '', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP, '$lastUsedColorsJson', 'rgba(89, 89, 89, 1)', '$organization')";

                $result2 = mysqli_query($link, $query2);

                if ($result2) {
                    $dashboardId = mysqli_insert_id($link);
                    $response['dashboardId'] = $dashboardId;
                    $callBody = ["elementId" => $dashboardId, "elementType" => "DashboardID", "username" => $username, "elementName" => $dashboardTitle];

                    $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/json\r\n",
                            'method' => 'POST',
                            'timeout' => 30,
                            'content' => json_encode($callBody),
                            'ignore_errors' => true
                        )
                    );

                    try
                    {
                        $context  = stream_context_create($options);
                        $callResult = @file_get_contents($apiUrl, false, $context);

                        if(strpos($http_response_header[0], '200') !== false) {
                            $response['result'] = 'OK';
                        }
                        else {
                            $response['result'] = 'KO';
                            $response['errormsg'] = 'Dashboard Succesfully Created, but Error in Ownership Registration.'; // . $http_response_header[0];
                        }
                    }
                    catch (Exception $ex)
                    {
                        $response['result'] = 'KO';
                        $response['errormsg'] = 'Dashboard Succesfully Created, but Error in Ownership Registration.'; // . $http_response_header[0];
                    }

                } else {
                    $response['result'] = 'KO';
                    $response['errormsg'] = 'Error in Creating Dashboard.'; // . $http_response_header[0];
                }
            }
        } else {
            $response['result'] = 'KO';
            $response['errormsg'] = 'DB Error.'; // . $http_response_header[0];
        }
    } else {
        $response['result'] = 'KO';
        $response['errormsg'] = 'You Have Reached the Maximum Limit of Dashboards You Can Create.'; // . $http_response_header[0];
    }
} else {
    if (!$loggedUsername) {
        $response['result'] = 'KO';
        $response['errormsg'] = 'No Username Found';
    }
}

mysqli_close($link);
echo json_encode($response);
