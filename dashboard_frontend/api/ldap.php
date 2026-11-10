<?php
   /* Snap4City: IoT-Directory
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
require_once '../common.php';
// session_start();

//Altrimenti restituisce in output le warning
error_reporting(E_ERROR | E_NOTICE);

function compare_values($obj_a, $obj_b) {
  return  strcasecmp($obj_a->value_name,$obj_b->value_name);
}

// Keep failed directory lookups separate from genuinely empty LDAP results.
function ldapApiReadEntries($server, $port, $baseDn, $filter, $list = false) {
    global $ldapAdminDN, $ldapAdminPwd;
    $connection = false;
    $entries = false;
    $stage = 'connect';
    $ldapCode = null;
    try {
        $connection = @ldap_connect($server, $port);
        if ($connection === false) throw new \RuntimeException('directory_unavailable');
        $stage = 'protocol';
        if (!@ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3)) throw new \RuntimeException('directory_unavailable');
        // Match updateLdapUsers.php: use the configured reader, otherwise anonymous bind.
        // Never retry anonymously after a configured authenticated bind fails.
        $stage = 'bind';
        if (!empty($ldapAdminDN)) {
            if (!is_string($ldapAdminDN) || trim($ldapAdminDN) === '' || !is_string($ldapAdminPwd) || $ldapAdminPwd === '') {
                $stage = 'bind_configuration';
                throw new \RuntimeException('directory_unavailable');
            }
            $bound = @ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
        } else {
            $bound = @ldap_bind($connection);
        }
        if (!$bound) throw new \RuntimeException('directory_unavailable');
        $stage = 'search';
        $search = $list ? @ldap_list($connection, $baseDn, $filter) : @ldap_search($connection, $baseDn, $filter);
        if ($search === false) throw new \RuntimeException('directory_unavailable');
        $stage = 'decode';
        $entries = @ldap_get_entries($connection, $search);
        if (!is_array($entries) || !isset($entries['count']) || !is_int($entries['count']) || $entries['count'] < 0) {
            throw new \RuntimeException('directory_unavailable');
        }
    } catch (\Throwable $ignored) {
        $entries = false;
        // Capture only the numeric LDAP status, before unbinding; never log DN, filters or passwords.
        if ($connection !== false && function_exists('ldap_errno')) {
            try { $ldapCode = @ldap_errno($connection); } catch (\Throwable $ignoredCode) {}
        }
    } finally {
        if ($connection !== false) {
            try { @ldap_unbind($connection); } catch (\Throwable $ignored) {}
        }
    }
    if ($entries === false) {
        $response = ['status'=>'ko', 'msg'=>'directory_unavailable', 'content'=>'', 'log'=>'', 'reason'=>$stage.'_failed'];
        if (is_int($ldapCode) && $ldapCode > 0) $response['ldap_code'] = $ldapCode;
        error_log('Dashboard LDAP lookup failed: '.json_encode(['reason'=>$response['reason'], 'ldap_code'=>$response['ldap_code'] ?? null]));
        http_response_code(502);
        echo json_encode($response);
        exit;
    }
    return $entries;
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
	$entries = ldapApiReadEntries($ldapServer, $ldapPort, $ldapBaseDN, '(objectClass=organizationalUnit)');

	if ($entries['count']==0){
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
	$userDN="cn=". $currentUser .",".$ldapBaseDN;
	$entries = ldapApiReadEntries($ldapServer, $ldapPort, $ldapBaseDN, '(&(objectClass=organizationalUnit)(l=' . $userDN . '))');

        if ($entries['count']==0){
                error_log("No LDAP organization Unit found for user".$userDN);
                $result['status'] = 'ko';
                $result['msg'] = 'Error: No LDAP organization Unit found for user '.$userDN.' <br/>';
                $result['log'] .= '\n\r action:get_logged_ou. Error: No LDAP organization Unit found for user '.$userDN;
        }
        else{
                $allOu = array();
                for ($i = 0; $i < $entries["count"]; $i++) {
                        if (isset($entries[$i]["ou"][0]) && !in_array($entries[$i]["ou"][0], $allOu)) {
                                $allOu[] = $entries[$i]["ou"][0];
                        }
                }
                $result["status"] = 'ok';
                $result["content"] =  $allOu;
                $result["log"] .= "\n\r action:get_logged_ou. Ok, got n-entries: ".count($allOu);
        }
        //my_log($result);
        echo json_encode($result);
}

else if($action == 'get_group_for_ou')
{
	if (checkAlphaNumAndSpaces($_REQUEST['ou']) === true || preg_match('/^[\w\-\s\p{L}]+$/', $_REQUEST['ou'])) {
	    if($_REQUEST['ou']=='No Organization') {
                $entries = ldapApiReadEntries($ldapServer, $ldapPort, $ldapBaseDN, '(objectClass=groupOfNames)', true);
            } else {
                $entries = ldapApiReadEntries($ldapServer, $ldapPort, $ldapBaseDN, '(&(objectClass=groupOfNames)(ou=' . $_REQUEST['ou'] . '))');
	    }
        } else {
            eventLog("Returned the following ERROR in ldap.php: organization '" . escapeForHTML($_REQUEST['ou']) ."' is not an alpha-numeric string.");
            $entries["count"] = 0;
            exit();
        }

	$allGroupsUserOu=array();

	for ($i = 0; $i<$entries["count"]; $i++) {
		$group = $entries[$i]["cn"][0];
		if(!in_array($group, ["Dashboard","IoTDirectory","ProcessLoader"]))
			$allGroupsUserOu[$i]=$group;
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
