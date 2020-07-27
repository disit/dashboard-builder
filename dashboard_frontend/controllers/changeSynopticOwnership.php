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

if(isset($_SESSION['loggedUsername']) && $_SESSION['loggedUsername']) 
{
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['id']);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in changeSynopticOwnership.php for id = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    };
    $dashboardTitle = mysqli_real_escape_string($link, $_REQUEST['title']);
    $newOwner = mysqli_real_escape_string($link, $_REQUEST['newOwner']);

    // Check if $newOwner exists as a valid LDAP user
    $ldapUsername = "cn=" . ldap_escape($newOwner) . "," . $ldapBaseDN;

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $bind = ldap_bind($ds);

    if ($ds && $bind) {
        if (checkLdapMembership($ds, $ldapUsername, $ldapToolName, $ldapBaseDN)) {
            if (checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN)) {
                $ldapRole = "RootAdmin";
                $ldapOk = true;
            }
            else
            {
                if (checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN)) {
                    $ldapRole = "ToolAdmin";
                    $ldapOk = true;
                } else {
                    if (checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN)) {
                        $ldapRole = "AreaManager";
                        $ldapOk = true;
                    } 
                }
            }
        }
    }

    if(isset($_SESSION['refreshToken']) && $ldapOk)
    {

        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        $callBody = ["elementId" => $_REQUEST['id'], "elementType" => $synOwnElmtType , "username" => $newOwner, "elementName" => $dashboardTitle];

        $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

        $options = array(
              'http' => array(
                      'header'  => "Content-type: application/json\r\n",
                      'method'  => 'POST',
                      'timeout' => 30,
                      'content' => json_encode($callBody),
                      'ignore_errors' => true
              )
        );

        try
        {
            $context  = stream_context_create($options);
            $callResult = @file_get_contents($apiUrl, false, $context);

            if(strpos($http_response_header[0], '200') !== false) 
            {
				
				// Delegate the template
				
				$qtid = "SELECT t.id templateId FROM Dashboard.DashboardWizard s JOIN SynopticTemplates t ON s.low_level_type = t.unique_name_id WHERE s.id = $dashboardId";
				$rtid = mysqli_query($link, $qtid);
				if($rtid)
				{
					while($row = mysqli_fetch_assoc($rtid))
					{
						$templateId = $row["templateId"];
					}
				}				
				$templateNewDelegated = $newOwner; 
				if(checkSynopticTplId($link, $templateId, $accessToken, $ownershipApiBaseUrl, $synTplOwnElmtType)) {					
					$ds = ldap_connect($ldapServer, $ldapPort);
					ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
					$bind = ldap_bind($ds);
					if($ds && $bind) {
						if(!checkLdapRole($ds, $templateNewDelegated, "RootAdmin", $ldapBaseDN)) {
							$templateAuthor = $_SESSION["loggedUsername"]; 
							if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin') {
								$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synTplOwnElmtType&accessToken=$accessToken";
								$options = array(
									  'http' => array(
											  'method'  => 'GET',
											  'timeout' => 30,
											  'ignore_errors' => true
									  )
								);
								$context  = stream_context_create($options);
								$callResult = file_get_contents($apiUrl, false, $context);	
								$ownership = json_decode($callResult,true);	
								foreach($ownership as $ownElmt) {
									if($templateId == $ownElmt["elementId"]) $templateAuthor = $ownElmt["username"];
								}
							}								
							$callBody = ["usernameDelegated" => $templateNewDelegated, "elementId" => $templateId, "elementType" => $synTplOwnElmtType];
							$apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($templateAuthor) . "/delegation?accessToken=" . $accessToken . "&sourceRequest=$synMgtSrcReq";
							$options = array(
								  'http' => array(
										  'header'  => "Content-type: application/json\r\n",
										  'method'  => 'POST',
										  'timeout' => 30,
										  'content' => json_encode($callBody),
										  'ignore_errors' => true
								  )
							);								
							$context  = stream_context_create($options);
							$callResult = file_get_contents($apiUrl, false, $context);							
						}						
					}						
				}
				
				// Return Ok
				
				$response['detail'] = 'Ok';
            }
            else
            {
                $response['detail'] = 'ApiCallKo1';
                $response['detail2'] = $http_response_header[0];
            }
        }
        catch (Exception $ex) 
        {
            $response['detail'] = 'ApiCallKo2';
            $response['detail2'] = $http_response_header[0];
        }
    } else {
        $response['detail'] = 'checkUserKo';
    }
    
    echo json_encode($response);
}
  
function checkSynopticTplId($link, $id, $accessToken, $ownershipApiBaseUrl, $synTplOwnElmtType) {
  if(isset($_SESSION['loggedRole']) && $_SESSION['loggedRole']=='RootAdmin') return true;
  $user = $_SESSION['loggedUsername'];
  $r = mysqli_query($link, "SELECT id FROM SynopticTemplates WHERE id = '$id'");
  if($r && mysqli_num_rows($r)>0) {	
	$apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=$synTplOwnElmtType&accessToken=$accessToken";
	$callResult = file_get_contents($apiUrl);	
	$ownership = json_decode($callResult,true);
	foreach($ownership as $ownElmt) {
		if($id == $ownElmt["elementId"] && $user&& $user == $ownElmt["username"]) {
			return true;
		}
	}
  }
  return false; 
}