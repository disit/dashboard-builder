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

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']) || @$_SESSION['isPublic'] === true)
{

    if(isset($_SESSION['refreshToken']))
    {
        $ldapUsername = "cn=" . $_SESSION['loggedUsername'] . "," . $ldapBaseDN;
        $ds = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if($ldapAdminDN)
            $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
        else
            $bind = ldap_bind($ds);
        $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
        if (is_null($organization)) {
            $organization = "None";
            $organizationSql = "Other";
        } else if ($organization == "") {
            $organization = "None";
            $organizationSql = "Other";
        } else {
            $organizationSql = $organization;
        }
        
        $action = $_GET['action'];
        
        switch($action)
        {
            case "getAllParameters":
                $orgParamsQuery = "SELECT * FROM Dashboard.Organizations WHERE organizationName = '$organizationSql'";
                $r = mysqli_query($link, $orgParamsQuery);

                if($r)
                {
                    if($row = mysqli_fetch_assoc($r))
                    {
                        $orgId = $row['id'];
                        $orgName = $row['organizationName'];
                        $orgKbUrl = $row['kbUrl'];
                        $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
                        $orgZoomLevel = $row['zoomLevel'];
                        $orgBroker = $row['broker'];
                        $orionIP = $row['orionIP'];
                        $ghRouting = $row['ghRouting'];
                        $response['orgId'] = $orgId;
                        $response['orgName'] = $orgName;
                        $response['orgKbUrl'] = $orgKbUrl;
                        $response['orgGpsCentreLatLng'] = $orgGpsCentreLatLng;
                        $response['orgZoomLevel'] = $orgZoomLevel;
                        $response['orgBroker'] = $orgBroker;
                        $response['orionIP'] = $orionIP;
                        $response['ghRouting'] = $ghRouting;
                        $response['detail'] = 'GetOrganizationParameterOK';
                    } else {
                        $response['detail'] = 'GetOrganizationParameterEMPTY';
                    }
                } else {
                    $response['detail'] = 'GetOrganizationParameterEMPTY';
                }
                break;

            case "getSpecificOrgParameters":
                if (isset($_GET['param'])) {
                    $orgSql = escapeForSQL($_GET['param'], $link);
                    $orgSqlAdd = " WHERE organizationName = '$orgSql';";
                } else {
                    $orgSql = "";
                    $orgSqlAdd = ";";
                }
                $orgParamsQuery = "SELECT * FROM Dashboard.Organizations" . $orgSqlAdd;
                $r = mysqli_query($link, $orgParamsQuery);
                //$response['sql'] = $orgParamsQuery;

                if($r)
                {
                    if($row = mysqli_fetch_assoc($r))
                    {
                        $orgId = $row['id'];
                        $orgName = $row['organizationName'];
                        $orgKbUrl = $row['kbUrl'];
                        $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
                        $orgZoomLevel = $row['zoomLevel'];
                        $orgBroker = $row['broker'];
                        $orionIP = $row['orionIP'];
                        $response['orgId'] = $orgId;
                        $response['orgName'] = $orgName;
                        $response['orgKbUrl'] = $orgKbUrl;
                        $response['orgGpsCentreLatLng'] = $orgGpsCentreLatLng;
                        $response['orgZoomLevel'] = $orgZoomLevel;
                        $response['orgBroker'] = $orgBroker;
                        $response['orionIP'] = $orionIP;
                        $response['detail'] = 'GetOrganizationParameterOK';
                    } else {
                        $response['detail'] = 'GetOrganizationParameterEMPTY';
                    }
                } else {
                    $response['detail'] = 'GetOrganizationParameterEMPTY';
                }
                break;
        }
        
    } else {
      //  $response['detail'] = 'GetOrganizationParameter_Not_Auth';
        $action = $_GET['action'];
        switch($action)
        {
            case "getSpecificOrgParameters":
                if (isset($_GET['param'])) {
                    $orgSql = mysqli_real_escape_string($link, sanitizeGetString('param'));
                    $orgSqlAdd = " WHERE organizationName = '$orgSql';";
                } else {
                    $orgSql = "";
                    $orgSqlAdd = ";";
                }
                $orgParamsQuery = "SELECT * FROM Dashboard.Organizations" . $orgSqlAdd;
                $r = mysqli_query($link, $orgParamsQuery);
                //$response['sql'] = $orgParamsQuery;

                if($r)
                {
                    if($row = mysqli_fetch_assoc($r))
                    {
                        $orgId = $row['id'];
                        $orgName = $row['organizationName'];
                        $orgKbUrl = $row['kbUrl'];
                        $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
                        $orgZoomLevel = $row['zoomLevel'];
                        $orgBroker = $row['broker'];
                        $orionIP = $row['orionIP'];
                        $response['orgId'] = $orgId;
                        $response['orgName'] = $orgName;
                        $response['orgKbUrl'] = $orgKbUrl;
                        $response['orgGpsCentreLatLng'] = $orgGpsCentreLatLng;
                        $response['orgZoomLevel'] = $orgZoomLevel;
                        $response['orgBroker'] = $orgBroker;
                        $response['orionIP'] = $orionIP;
                        $response['detail'] = 'GetOrganizationParameterOK';
                    } else {
                        $response['detail'] = 'GetOrganizationParameterEMPTY';
                    }
                } else {
                    $response['detail'] = 'GetOrganizationParameterEMPTY';
                }
                break;
        }
    }

    echo json_encode($response);
}

