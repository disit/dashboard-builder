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

//include 'process-form.php';
include '../config.php';
include '../session.php';
require '../sso/autoload.php';
header('Access-Control-Allow-Origin: *');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

use Jumbojett\OpenIDConnectClient;

function checkAuth($dashboardId, $loggedUsername, $link) {
    $resultCheck = false;
    $query = "SELECT Dashboard.Config_dashboard.visibility AS visibility, Dashboard.Users.admin AS role, Dashboard.Config_dashboard.user AS username FROM Dashboard.Config_dashboard " .
        "LEFT JOIN Dashboard.Users " .
        "ON Dashboard.Config_dashboard.user = Dashboard.Users.username " .
        "WHERE Dashboard.Config_dashboard.Id = '$dashboardId'";

    $result = mysqli_query($link, $query);
    $response = [];
    $ds = null;
    $authorOrigin = null;

    if($result)
    {
        $row = mysqli_fetch_array($result);
        $authorUsername = $row["username"];
        $proceed = false;
        if((isset($_SESSION['loggedUsername'])))
        {
            if($_SESSION['loggedUsername'] == $authorUsername)
            {
                $proceed = true;
            }
            else
            {
                if($_SESSION['loggedRole'] == 'RootAdmin')
                {
                    $proceed = true;
                }
                else
                {
                    $proceed = false;
                }
            }
        }

        if($proceed)
        {
            $resultCheck = true;
        }
        else
        {
            $resultCheck = false;
        }
    }
    else
    {
        $resultCheck = false;
    }

    return $resultCheck;

}


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
    } else {
        $accessToken = $_GET['accessToken'];
        $loggedUsername = $uinfo->preferred_username;
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
} else if($_SESSION['accessToken'] && $_SESSION["loggedUsername"] && $_SESSION['loggedRole']) {
    $accessToken = $_SESSION['accessToken'];
    $loggedUsername = $_SESSION["loggedUsername"];
    $loggedRole = $_SESSION['loggedRole'];
} else {
    // header("Location: ../management/ssoLogin.php?redirect=".urlencode("$appUrl/api/getEncryptedUserInfo.php"));
    header("HTTP/1.1 401 Unauthorized");
    $response['responseState'] = "Unauthorized request: accessToken not present.";
    //  echo json_encode($response['responseState']);
    echo ($response['responseState']);
    die();
}

if($accessToken && $loggedUsername) {
    if (isset($_GET['dashboardId'])) {
        if (isset($_GET['value'])) {
            $idDash = mysqli_real_escape_string($link, $_GET['dashboardId']);
            if (checkVarType($idDash, "integer") === false) {
                eventLog("Returned the following ERROR in setInfoMsgPopupFlag.php for dashboardId = ".$idDash.": ".$idDash." is not an integer as expected. Exit from script.");
                exit();
            }
            $querySel = "SELECT * FROM Dashboard.Config_dashboard WHERE id = '$idDash';";
            $resultSel = mysqli_query($link, $querySel);
            if($resultSel) {
                if (mysqli_num_rows($resultSel) > 0) {
                    if ($loggedRole == "RootAdmin" || checkAuth($idDash, $loggedUsername, $link)) {
                        $flag = mysqli_real_escape_string($link, $_GET['value']);
                        $text = mysqli_real_escape_string($link, urldecode($_GET['infoMsgText']));
                        $query = "UPDATE Dashboard.Config_dashboard SET infoMsgPopup = '$flag', infoMsgText = '$text' WHERE id = '$idDash';";
                        $result = mysqli_query($link, $query);
                        if ($result) {
                            $response['responseState'] = "Successful response";
                        } else {
                            $response['responseState'] = "Error in db writing";
                        }
                    } else {
                        $response['responseState'] = "User not Authorized to Write Dashboard Info Msg for This Dashboard.";
                    }
                } else {
                    $response['responseState'] = "Provided Dashboard ID not existing.";
                }
            }
        } else {
            $response['responseState'] = "Missing Flag Value.";
        }
    } else {
        $response['responseState'] = "Missing Dashboard ID.";
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $response['responseState'] = "Unauthorized request.";
    echo ($response['responseState']);
    die();
}

echo json_encode($response);
