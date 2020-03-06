<?php
   /* Snap4City: IoT-Directory
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


$result=array("status"=>"","msg"=>"","content"=>"","log"=>"");	
/* all the primitives return an array "result" with the following structure

result["status"] = ok/ko; reports the status of the operation (mandatory)
result["msg"] a message related to the execution of the operation (optional)
result["content"] in case of positive execution of the operation the content extracted from the db (optional)
result["log"] keep trace of the operations executed on the db

This array should be encoded in json
*/	



header("Content-type: application/json");
header("Access-Control-Allow-Origin: *\r\n");
include ('../config.php');
include ('../common.php');
// session_start();

//Altrimenti restituisce in output le warning
error_reporting(E_ERROR | E_NOTICE);

function compare_values($obj_a, $obj_b) {
  return  strcasecmp($obj_a->value_name,$obj_b->value_name);
}

if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])) 
    {
        $action = $_REQUEST['action'];
    }
else
{
    exit();
}

require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;


if (isset($_REQUEST['nodered']))
{
   if ($_REQUEST['token']!='undefined')
      $accessToken = $_REQUEST['token'];
   else $accessToken = "";
} 
else
{
if (isset($_REQUEST['token'])) {
 /* $oidc = new OpenIDConnectClient($keycloakHostUri, $clientId, $clientSecret);
  $oidc->providerConfigParam(array('token_endpoint' => $keycloakHostUri.'/auth/realms/master/protocol/openid-connect/token'));

  $tkn = $oidc->refreshToken($_REQUEST['token']);
  $accessToken = $tkn->access_token;    */
  $accessToken = $_REQUEST['token'];
}
else $accessToken ="";
}

if (isset($_REQUEST['username'])) {
	$currentUser = $_REQUEST['username'];
}


if($action == 'get_all_ou')
{
	$connection = ldap_connect($ldapServer, $ldapPort);
	$resultldap = ldap_search($connection, $ldapBaseDN, '(objectClass=organizationalUnit)');
	$entries = ldap_get_entries($connection, $resultldap);

	if (ldap_count_entries($connection,$resultldap)==0){
        	error_log("No LDAP organization Unit found at all");
		$result['status'] = 'ko'; 
        	$result['msg'] = 'Error: No LDAP organization Unit found at all <br/>';
	        $result['log'] .= '\n\r action:get_all_ou. Error: No LDAP organization Unit found at all ';
	}
	else{
        	for ($i = 0; $i<$entries["count"]; $i++) {
	                $allOu[$i]=$entries[$i]["ou"][0];
        	}
		$result['status'] = 'ok';
        	$result['content'] =  $allOu;
	        $result['log'] .= "\n\r action:get_all_ou. Ok, got n-entries:".count($allOu);
	}	
	//my_log($result);
    echo json_encode($result);
}

else if($action == 'get_logged_ou')
{
	$connection = ldap_connect($ldapServer, $ldapPort);
	$userDN="cn=". $currentUser .",".$ldapBaseDN;
	$resultldap = ldap_search($connection, $ldapBaseDN, '(&(objectClass=organizationalUnit)(l=' . $userDN . '))');
        $entries = ldap_get_entries($connection, $resultldap);

        if (ldap_count_entries($connection,$resultldap)==0){
                error_log("No LDAP organization Unit found for user".$userDN);
                $result['status'] = 'ko';
                $result['msg'] = 'Error: No LDAP organization Unit found for user '.$userDN.' <br/>';
                $result['log'] .= '\n\r action:get_logged_ou. Error: No LDAP organization Unit found for user '.$userDN;
        }
        else{
                $result["status"] = 'ok';
                $result["content"] =  $entries["0"]["ou"][0];
                $result["log"] .= "\n\r action:get_logged_ou. Ok, got ".$entries["0"]["ou"][0];
        }
        //my_log($result);
        echo json_encode($result);
}

else if($action == 'get_group_for_ou')
{
        $connection = ldap_connect($ldapServer, $ldapPort);
        if (checkAlphaNumAndSpaces($_REQUEST['ou']) === true) {
            $resultldap = ldap_search($connection, $ldapBaseDN, '(&(objectClass=groupOfNames)(ou=' . $_REQUEST['ou'] . '))');
            $entries = ldap_get_entries($connection, $resultldap);
        } else {
            eventLog("Returned the following ERROR in ldap.php: organization '" . escapeForHTML($_REQUEST['ou']) ."' is not an alpha-numeric string.");
            $entries["count"] = 0;
            exit();
        }

	$allGroupsUserOu=array();

	for ($i = 0; $i<$entries["count"]; $i++) {
		$allGroupsUserOu[$i]=$entries[$i]["cn"][0];
        }
        
        $result['status'] = 'ok';
        $result['content'] =  $allGroupsUserOu;
        $result['log'] .= "\n\r action:get_group_for_ou. Ok, got n-entries: ". count($allGroupsUserOu);

        //my_log($result);
        echo json_encode($result);
}

else 
{
	$result['status'] = 'ko';
	$result['msg'] = 'invalid action ' . escapeForHTML($action);
	$result['log'] = 'invalid action ' . escapeForHTML($action);
	//my_log($result);
    echo json_encode($result);
}
