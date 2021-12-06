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

include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;
$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
$accessToken = $tkn->access_token;
$_SESSION['refreshToken'] = $tkn->refresh_token;

if (isset($_SESSION['loggedRole'])) {
    
    $link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
    mysqli_select_db($link, $dbname);
    //error_reporting(E_ERROR);
//
    if (isset($_GET['action']) && !empty($_GET['action'])) {
        //$action = $_REQUEST['action'];
        $action = mysqli_real_escape_string($link, $_REQUEST['action']);
        $action = filter_var($action, FILTER_SANITIZE_STRING);
        //
        if ($action == 'get_orgs') {
            $query = 'SELECT organizationName FROM Organizations ORDER BY organizationName ASC;';
            $result = mysqli_query($link, $query);

            if ($result) {
                $dashboardParams = [];
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        array_push($dashboardParams, $row['organizationName']);
                    }
                }
                echo json_encode($dashboardParams);
            }
        } else if ($action == 'edit_orgs') {
            //
            $dashboardId = mysqli_real_escape_string($link,  $_REQUEST['title']);
            if (checkVarType($dashboardId, "integer") === false) {
                eventLog("Returned the following ERROR in editDashboardOrganization.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
                exit();
            };
            //
            
            //
            //$title = $_REQUEST['title'];
            $title = mysqli_real_escape_string($link, $_REQUEST['title']);
            $title = filter_var($title, FILTER_SANITIZE_STRING);

            //$org = $_REQUEST['org'];
            $org = mysqli_real_escape_string($link, $_REQUEST['org']);
            $org = filter_var($org, FILTER_SANITIZE_STRING);
            //
            $query = 'UPDATE Config_dashboard SET organizations="' . $org . '" WHERE Id="' . $title . '"';
            $result = mysqli_query($link, $query);
            //
        } else {
            //nothing 
            exit();
        }
//
    } else {
        //
        exit();
    }
}else{
    echo('Not logged user');
}
?>