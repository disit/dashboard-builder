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
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
ini_set("max_execution_time", 0);
error_reporting(E_ERROR);
$output_message = [];

	
if(isset($_GET['accessToken'])) {
	//////
	$bearerToken = $_GET['accessToken'];
	$check_connection = $ssoUserinfoEndpoint;
	// Inizializza una sessione cURL
			$ch = curl_init();
			// Imposta l'URL dell'API
			curl_setopt($ch, CURLOPT_URL, $check_connection);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer " . $bearerToken
			));
			//
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Esegui la richiesta cURL
			$response = curl_exec($ch);
			// Controlla se ci sono errori
			if(curl_errno($ch)) {
				//echo 'Errore cURL: ' . curl_error($ch);
				$output_message['code'] = '400';
				$output_message['message'] = 'Errore cURL: ' . curl_error($ch);
				echo json_encode($output_message);
			} else {
				//echo ($response);
			}

// Chiudi la sessione cURL
$obj = json_decode($response, true);

if (array_key_exists('error', $obj)) {
	//header("HTTP/1.1 401 Unauthorized");
	$output_message['code'] = '401';
	$output_message['message'] = 'Error in accessTokan';
	echo json_encode($output_message);
	die();
}else if (array_key_exists('name', $obj)) {
	//echo($obj['name']);

	$name = $obj['name'];
	////////
	$link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $query= "SELECT * FROM Dashboard.TrustedUserGroups WHERE username='".$name."'";
    $result = mysqli_query($link, $query);
    // Controlla se la query è stata eseguita correttamente
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
            
	if(isset($_GET['action'])) {
		///
		//echo ($_GET['action']);
		if($_GET['action'] == 'addUser'){
			if (($_GET['username'])&&($_GET['group'])&&($_GET['organization'])){
				$username = $_GET['username'];
				$group = $_GET['group'];
				$organization = $_GET['organization'];
				////////**************************////////
				$connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
				ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3)or die("ERROR IN ldap_set_option");
				$bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);			
				//
				if ($group !=""){
						$groupDn =  "cn=$group,ou=$organization,dc=ldap,dc=organization,dc=com";
						$dn = "cn=" . strtolower($username) . "," . $ldapBaseDN;
						$dn_role = "cn=" . $username . "," . $ldapBaseDN;
						//
						$result = ldap_search($connection, $groupDn, "(objectClass=*)");
							if (ldap_count_entries($connection, $result) > 0) {
								$entry = ['member' => $dn];
								//
								//var_dump($entry);
												if (ldap_mod_add($connection, $groupDn, ['member' => $dn])) {

													$output_message['code'] = '200';
													$output_message['message'] = 'User successfully add to the group';
													echo json_encode($output_message);
													die();
											} else {
													$ldapErrorNo = ldap_errno($connection);
													$ldapErrorStr = ldap_err2str($ldapErrorNo);
												if (($ldapErrorNo === 20)||($ldapErrorNo === '20')){

													$output_message['code'] = '403';
													$output_message['message'] = 'User is yet in the group';
													echo json_encode($output_message);
													die();
												}else if (($ldapErrorNo === 32)||($ldapErrorNo === '32')){

													$output_message['code'] = '403';
													$output_message['message'] = 'wrong Group or Organization';
													echo json_encode($output_message);
													die();
												}else{

													$output_message['code'] = '403';
													$output_message['message'] = 'Error during adding operation';
													echo json_encode($output_message);
													die();
												}
											}
							//
						}else{
							//
							$output_message['code'] = '404';
							$output_message['message'] = 'Error not found group';
							echo json_encode($output_message);
							die();
						}
						//
				}
				ldap_close($connection);
				/////////*************/////////////
			}else{
				//
				$output_message['code'] = '401';
				$output_message['message'] = 'Unauthorized request. missing parameters';
				echo json_encode($output_message);
				die();
			}
		}
		if($_GET['action'] == 'deleteUser'){
			if (($_GET['username'])&&($_GET['group'])&&($_GET['organization'])){
				$username = $_GET['username'];
				$group = $_GET['group'];
				$organization = $_GET['organization'];
				//
				$connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
				ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3)or die("ERROR IN ldap_set_option");
				$bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
				////
						$groupDn =  "cn=$group,ou=$organization,dc=ldap,dc=organization,dc=com";
						$dn = "cn=" . strtolower($username) . "," . $ldapBaseDN;
						$dn_role = "cn=" . $username . "," . $ldapBaseDN;
						//
						/////////////
				if (ldap_mod_del($connection, $groupDn, ['member' => $dn])){
							//
							$output_message['code'] = '200';
							$output_message['message'] = 'User successfully deleted from group';
							echo json_encode($output_message);
							die();
				}else{
					$ldapErrorNo = ldap_errno($connection);
					$ldapErrorStr = ldap_err2str($ldapErrorNo);
					//
					$output_message['code'] = '401';
					$output_message['message'] = $ldapErrorNo;
					echo json_encode($output_message);
					die();
				}
				ldap_close($connection);
				////
			}else{
				//
				$output_message['code'] = '401';
				$output_message['message'] = 'Unauthorized request. missing paramters';
				echo json_encode($output_message);
		die();
			}
		}
	
	// Libera la memoria associata al risultato
	mysqli_free_result($result);
	$output_message['code'] = '401';
	$output_message['message'] = 'Unauthorized request. missing paramters';
	echo json_encode($output_message);
} else {
	// Stampa l'errore
	
	$output_message['code'] = '401';
	$output_message['message'] = 'User not authorizated';
	echo json_encode($output_message);
	die();
}
/////////
} else {
	$output_message['code'] = '401';
	$output_message['message'] = 'User not authorizated';
	echo json_encode($output_message);
	die();
}

curl_close($ch);

	}else{

		$output_message['code'] = '401';
		$output_message['message'] = 'Unauthorized request. missing paramters';
		echo json_encode($output_message);
		die();
	}

	
	}else{

		$output_message['code'] = '401';
		$output_message['message'] = 'Unauthorized request. missing paramters';
		echo json_encode($output_message);
		die();
	}


}else{

		$output_message['code'] = '401';
		$output_message['message'] = 'Unauthorized request. AccessToken required';
		echo json_encode($output_message);
		die();
}

?>