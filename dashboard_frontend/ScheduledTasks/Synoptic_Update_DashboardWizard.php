<?php

/* Dashboard Builder.
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

function getAccessToken($token_endpoint, $username, $password, $client_id){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$token_endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "username=".$username."&password=".$password."&scope=openid&grant_type=password&client_id=".$client_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($ch);
    curl_close($ch);
    return json_decode($curl_response)->access_token;

}

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
error_reporting(E_ERROR);

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$allowedElementIDs = [];
$allowedElementCouples = [];
$ownedElements = [];
$encrCpls = [];                 // MOD OWN-DEL
$encrDelCpls = [];              // MOD OWN-DEL
$encrDelGroupCpls = [];         // MOD OWN-DEL
$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$host_PD= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);
$apiUrl = $host_PD . ":8080/datamanager/api/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=SynopticID";

$apiResults = file_get_contents($apiUrl);

if(trim($apiResults) != "")
{
    $resApiArray = json_decode($apiResults, true);
    $publicSynoptic = $resApiArray;
    foreach($resApiArray as $publicSensor)
    {
        array_push($allowedElementIDs, $publicSensor['elementId']);
    }
}

$apiAllDelegationsUrl = $host_PD . ":8080/datamanager/api/v1/delegation?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=SynopticID";
$apiAllDelegationsResults = file_get_contents($apiAllDelegationsUrl);

if(trim($apiAllDelegationsResults) != "")
{
    $resApiAllArray = json_decode($apiAllDelegationsResults, true);
    $delegatedSynoptic = $resApiAllArray;
}

// NEW OWENRSHIP - REQUEST ALL OWNED SynopticID ELEMENTS
$apiOwnershipUrl = $ownershipApiBaseUrl . "/v1/list/?type=SynopticID&accessToken=" . $accessToken;

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'GET',
        'timeout' => 30,
        'ignore_errors' => true
    )
);

$context  = stream_context_create($options);
$ownedSynopticJson = file_get_contents($apiOwnershipUrl, false, $context);
$ownedSynoptic = json_decode($ownedSynopticJson);

for($i = 0; $i < count($ownedSynoptic); $i++) {
    array_push($ownedElements, $ownedSynoptic[$i]->elementId);
}

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("*** Starting Synoptic_Update_DashboardWizard SCRIPT at: ".$start_time_ok."\n");

// UPDATE TABELLA DASHBOARD_WIZARD CON SYNOPTICS

$high_level_type = "Synoptic";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";
$instance_uri = "";
$get_instances = "";
$unit = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "";
$ownership = "";
$elementId = null;
$count = 0;

$query = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = 'Synoptic';";
$rs = mysqli_query($link, $query);
$result = [];
if($rs) {

    $dashboardName = "";

    while ($row = mysqli_fetch_assoc($rs)) {

     //   echo(" Start \n");

        $owner = null;                  // MOD OWN-DEL
        $cryptedOwner = null;           // MOD OWN-DEL
        $cryptedDelegatedUsr = null;    // MOD OWN-DEL
        $decryptedOwner = null;         // MOD OWN-DEL
        $ownerCheck = null;             // MOD OWN-DEL
        $delegatedUsers = [];           // MOD OWN-DEL
        $delegatedGroups = [];          // MOD OWN-DEL
        $delegatedUsersStr = null;        // MOD OWN-DEL
        $delegatedGroupStr = null;        // MOD OWN-DEL

        $nature = $row['nature'];
        $sub_nature = $row['sub_nature'];
        $low_level_type = $row['low_level_type'];
        $unique_name_id = $row['unique_name_id'];
        $instance_uri = $row['isntance_uri'];
        $get_instances = $row['get_instances'];
        $elementId = $row['id'];
        $ownership = $row['ownership'];

        foreach($ownedSynoptic as $struct) {
            if ($elementId == $struct->elementId) {
                $owner = strtolower($struct->username);     // MOD OWN-DEL
            //    echo(" ------- Owner Found! \n");
                break;
            }
        }

        if (!is_null($owner)) {
            if (!array_key_exists($owner, $encrCpls)) {
                $cryptedOwner = encryptOSSL($owner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);           // MOD OWN-DEL
                $decryptedOwner = decryptOSSL($cryptedOwner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);  // MOD OWN-DEL
                $encrCpls[$owner] = $cryptedOwner;
            } else {    // DO NOT ENCRYPT IF ALREADY CRYPTED USER
                $cryptedOwner = $encrCpls[$owner];
            }

            if ($ownership === "private") {

                // MOD OWN-DEL
                foreach ($delegatedSynoptic as $delStruct) {
                    $userDelegated = null;
                    if ($unique_name_id == $delStruct['elementId']) {
                        $userDelegated = strtolower($delStruct['usernameDelegated']);
                        $cryptedDelegatedUsr = null;
                        $ownerCheck = $delStruct['usernameDelegator'];
                        if (!in_array($userDelegated, $delegatedUsers)) {
                            if (strcmp($userDelegated, '') != 0) {
                                array_push($delegatedUsers, $userDelegated);
                                if (!array_key_exists($userDelegated, $encrDelCpls)) {
                                    $cryptedDelegatedUsr = encryptOSSL($userDelegated, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
                                    $encrDelCpls[$userDelegated] = $cryptedDelegatedUsr;
                                } else {    // DO NOT ENCRYPT IF ALREADY CRYPTED USER
                                    $cryptedDelegatedUsr = $encrDelCpls[$userDelegated];
                                }
                                if ($delegatedUsersStr == null || $delegatedUsersStr === "") {
                                    $delegatedUsersStr = $cryptedDelegatedUsr;
                                } else {
                                    $delegatedUsersStr = $delegatedUsersStr . ", " . $cryptedDelegatedUsr;
                                }
                            }
                        } else {
                            $duplicateDelegationFlag = 1;
                        }

                        if (isset($delStruct['groupnameDelegated'])) {
                            //    $cryptedDelegatedGroup = null;
                            if (!in_array($delStruct['groupnameDelegated'], $delegatedGroups)) {
                                if (!is_null($delStruct['groupnameDelegated'])) {
                                    $delegatedGroupsItem = $delStruct['groupnameDelegated'];
                                    if (isset(explode(",ou", $delegatedGroupsItem)[1])) {
                                        $delegatedGroupsItem = explode(",ou", explode("cn=", $delegatedGroupsItem)[1])[0];
                                        /*  if (!isset($delegatedGroupsItem) || trim($delegatedGroupsItem) === '') {
                                              $delegatedGroupsItem = explode(",dc", explode("ou=", $delegatedGroupsItem)[1])[0];
                                          }*/
                                    } else {
                                        $delegatedGroupsItem = explode(",dc=", explode("ou=", $delegatedGroupsItem)[1])[0];
                                    }
                                    array_push($delegatedGroups, $delegatedGroupsItem);

                                    if ($delegatedGroupStr == null || $delegatedGroupStr === "") {
                                        $delegatedGroupStr = $delegatedGroupsItem;
                                    } else {
                                        $delegatedGroupStr = $delegatedGroupStr . ", " . $delegatedGroupsItem;
                                    }
                                }
                            }
                        }
                    }
                }

            }

            // $updtQuery = "UPDATE DashboardWizard SET ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
            $updtQuery = "UPDATE DashboardWizard SET ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "' WHERE id = " . $elementId . ";";
            mysqli_query($link, $updtQuery);
            $count++;
            echo($count . " - Updating Synoptic: " . $unique_name_id . " - ID = " . $elementId . "\n");
        }

    }
}
