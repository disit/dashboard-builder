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

include '../config.php';

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

$host_pd= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$accessToken=get_access_token($token_endpoint, $username, $password, $client_id);

$apiUrl = $host_PD . ":8080/datamanager/api/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=AppID";
// MOD-GP 2019 Query Per prendere TUTTI i DELEGATED ANONYMOUS
//$apiUrl = $host_PD . ":8080/datamanager/api/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
$apiResults = file_get_contents($apiUrl);

if(trim($apiResults) != "")
{
    $resApiArray = json_decode($apiResults, true);
    $publicApp = $resApiArray;
    foreach($resApiArray as $publicItem)
    {
        array_push($allowedElementIDs, $publicItem['elementId']);
    }
}

$apiAllDelegationsUrl = $host_PD . ":8080/datamanager/api/v1/delegation?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=AppID";
$apiAllDelegationsResults = file_get_contents($apiAllDelegationsUrl);

if(trim($apiAllDelegationsResults) != "")
{
    $resApiAllArray = json_decode($apiAllDelegationsResults, true);
    $delegatedApp = $resApiAllArray;
}

// NEW OWENRSHIP - REQUEST ALL OWNED AppID ELEMENTS
$apiOwnershipUrl = $ownershipApiBaseUrl . "/v1/list/?type=AppID&accessToken=" . $accessToken;

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'GET',
        'timeout' => 30,
        'ignore_errors' => true
    )
);

$context  = stream_context_create($options);
$ownedAppJson = file_get_contents($apiOwnershipUrl, false, $context);
$ownedApp = json_decode($ownedAppJson);

for($i = 0; $i < count($ownedApp); $i++) {
    array_push($ownedElements, $ownedApp[$i]->elementId);
}

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);
echo("*** Starting FeedT IO_App SCRIPT at: ".$lastCheck."\n");

$count2 = 0;

$high_level_type2 = "Dashboard-IOT App";
$nature2 = "";
$sub_nature_array2 = [];
$sub_nature2 = "";
$low_level_type2 = "";
$unique_name_to_split2 = "";
$unique_name_id2 = "";

$value_name2 = "";
$value_type2 = "";

$instance_uri2 = "";
$get_instances2 = "";
$unit2 = "";
$metric2 = "";
$saved_direct2 = "";
$kb_based2 = "";
$sm_based2 = "";
$parameters2 = "";
$healthiness2 = "true";
$ownership2 = "";
$organizations2 = "";

//$allowedElementIDs = [];
//$allowedElementCouples = [];
$ownedElements = [];
$encrCpls = [];                 // MOD OWN-DEL
$encrDelCpls = [];              // MOD OWN-DEL
$encrDelGroupCpls = [];         // MOD OWN-DEL

$s2 = "";
$a2 = "";
$dt2 = "";
$actSensArray2 = [];

$query2 = "SELECT NR.name, NR.metricType, NR.appId, NR.flowId, NR.flowName, NR.httpRoot, NR.organization, C.name_w, C.title_w, C.type_w, C.id_dashboard FROM Dashboard.NodeRedMetrics NR INNER JOIN Dashboard.Config_widget_dashboard C ON C.id_metric = NR.name;";
$rs2 = mysqli_query($link, $query2);
$result2 = [];
if($rs2) {

    $dashboardName2 = "";

    while($row2 = mysqli_fetch_assoc($rs2)) 
    {
        $owner = null;                  // MOD OWN-DEL
        $cryptedOwner = null;           // MOD OWN-DEL
        $cryptedDelegatedUsr = null;    // MOD OWN-DEL
        $decryptedOwner = null;         // MOD OWN-DEL
        $ownerCheck = null;             // MOD OWN-DEL
        $delegatedUsers = [];           // MOD OWN-DEL
        $delegatedGroups = [];          // MOD OWN-DEL
        $delegatedUsersStr = null;        // MOD OWN-DEL
        $delegatedGroupStr = null;        // MOD OWN-DEL

        $queryDashboardName2 = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = ".$row2['id_dashboard'];
        $rsDashName2 = mysqli_query($link, $queryDashboardName2);
        $resultDashName2 = [];
        if($rsDashName2) 
        {
            if($rowDashName2 = mysqli_fetch_assoc($rsDashName2)) 
            {
                $dashboardName2 = $rowDashName2['name_dashboard'];
            }
        }
        
        $appName2 = explode('/nodered/', $row2['httpRoot'])[1];
        
        if (!is_null($dashboardName2)) {
            if ($dashboardName2 != '') {
                $sub_nature2 = $dashboardName2;
                if (!is_null($appName2)) {
                    if ($appName2 != '') {
                        if (!is_null($row2['flowName']) && $row2['flowName'] != '') 
                        {
                            $unique_name_id2 = $appName2 . "_" . $row2['flowName'];
                        } else {
                            $unique_name_id2 = $appName2;
                        }
                        $value_name2 = $unique_name_id2;
                            array_push($actSensArray2, $unique_name_id2);
                            $nature2 = "From IOT App to Dashboard";
                            $low_level_type2 = $row2['title_w'];
                            $value_type2 = $low_level_type2;
                            $parameters2 = $row2['name'];
                            $instance_uri2 = '';                                                                                   
                            if ($row2['metricType'] == "Intero") {
                                $unit2 = "integer";
                            } else if ($row2['metricType'] == "Testuale") {
                                $unit2 = "string";
                            } else if ($row2['metricType'] == "webContent") {
                                $unit2 = "webpage";
                            } else if ($row2['metricType'] == "Float") {
                                $unit2 = "float";
                            } else if ($row2['metricType'] == "boolean") {
                                $unit2 = "string";
                            } else if ($row2['metricType'] == "String") {
                                $unit2 = "string";
                            } else {
                                $unit2 = $row2['metricType'];
                            }
                            $get_instances2 = $row2['appId'];
                            $metric2 = "yes";
                            $saved_direct2 = "saved";
                            $kb_based2 = "no";
                            $sm_based2 = "no";
                            
                            $ownership2 = "private";
                            $organizations2 = $row2['organization'];

                            foreach($ownedApp as $struct) {
                                if ($get_instances2 == $struct->elementId) {
                                    $owner = strtolower($struct->username);     // MOD OWN-DEL
                                    break;
                                }
                            }

                            //  if (!in_array($owner, $encrCpls)) {
                            if (!array_key_exists($owner, $encrCpls)) {
                                $cryptedOwner = encryptOSSL($owner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);           // MOD OWN-DEL
                                $decryptedOwner = decryptOSSL($cryptedOwner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);  // MOD OWN-DEL
                                $encrCpls[$owner] = $cryptedOwner;
                            } else {    // DO NOT ENCRYPT IF ALREADY CRYPTED USER
                                $cryptedOwner = $encrCpls[$owner];
                            }

                            /*    if ($availability != '') {
                                    $ownership = $availability;
                                } else if ($ownShip != '') {
                                    $ownership = $ownShip;
                                } else {
                                    $ownership = "public";
                                }*/

                            if (in_array($get_instances2, $allowedElementIDs)) {
                                $ownership2 = 'public';
                            } else {
                                $ownership2 = 'private';
                            }

                            if ($ownership2 == "private") {

                                // MOD OWN-DEL
                                foreach($delegatedApp as $delStruct) {
                                    $userDelegated = null;
                                    if ($get_instances2 == $delStruct['elementId']) {
                                        $userDelegated = strtolower($delStruct['usernameDelegated']);
                                        $cryptedDelegatedUsr = null;
                                        $ownerCheck = $delStruct['usernameDelegator'];
                                        if (!in_array($userDelegated, $delegatedUsers)) {
                                            if (strcmp($userDelegated, '') != 0 && $userDelegated != "anonymous") {
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
                                                     /*   if (!isset($delegatedGroupsItem) || trim($delegatedGroupsItem) === '') {
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

                            $insertQuery2 = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, lastCheck, ownership, organizations, ownerHash, delegatedHash, delegatedGroupHash, value_name, value_type) VALUES ('$nature2','$high_level_type2','$sub_nature2','$low_level_type2', '$unique_name_id2', '$instance_uri2', '$get_instances2', '$unit2', '$metric2', '$saved_direct2', '$kb_based2', '$sm_based2', '$parameters2', '$healthiness2', '$lastCheck', '$ownership2', '$organizations2', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$value_name2', '$value_type2') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type2 . "', sub_nature = '" . $sub_nature2 . "', low_level_type = '" . $low_level_type2 . "', unique_name_id = '" . $unique_name_id2 . "', instance_uri = '" . $instance_uri2 . "', get_instances = '" . $get_instances2 . "', unit = '" . $unit2 . "', sm_based = '" . $sm_based2 . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters2 . "', healthiness = '" . $healthiness2 . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership2 . "', organizations = '" . $organizations2 . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $value_name2 . "', value_type = '" . $value_type2 . "';";
                            mysqli_query($link, $insertQuery2);
                            $count2++;
                            echo($count2 . " - Dashboard METRIC for DataViewers (From IOT App to Dashboard) : " . $unique_name_id2 . ", MEASURE: " . $low_level_type2 . "\n");
                    
                    }
                }
            }
        }
    }
}

$count = 0;

$high_level_type = "Dashboard-IOT App";
$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";

$value_name = "";
$value_type = "";

$instance_uri = "";
$get_instances = "";
$unit = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "true";
$ownership = "";
$organizations = "";

//$allowedElementIDs = [];
//$allowedElementCouples = [];
$ownedElements = [];
$encrCpls = [];                 // MOD OWN-DEL
$encrDelCpls = [];              // MOD OWN-DEL
$encrDelGroupCpls = [];         // MOD OWN-DEL

$s = "";
$a = "";
$dt = "";
$actSensArray = [];

$query = "SELECT NR.name, NR.valueType, NR.appId, NR.flowId, NR.flowName, NR.httpRoot, NR.organization, C.name_w, C.title_w, C.type_w, C.id_dashboard FROM Dashboard.NodeRedInputs NR INNER JOIN Dashboard.Config_widget_dashboard C ON C.id_metric = NR.name;";
$rs = mysqli_query($link, $query);
$result = [];
if($rs) {

    $dashboardName = "";

    while ($row = mysqli_fetch_assoc($rs)) {

        $owner = null;                  // MOD OWN-DEL
        $cryptedOwner = null;           // MOD OWN-DEL
        $cryptedDelegatedUsr = null;    // MOD OWN-DEL
        $decryptedOwner = null;         // MOD OWN-DEL
        $ownerCheck = null;             // MOD OWN-DEL
        $delegatedUsers = [];           // MOD OWN-DEL
        $delegatedGroups = [];          // MOD OWN-DEL
        $delegatedUsersStr = null;        // MOD OWN-DEL
        $delegatedGroupStr = null;        // MOD OWN-DEL

        $queryDashboardName = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = ".$row['id_dashboard'];
        $rsDashName = mysqli_query($link, $queryDashboardName);
        $resultDashName = [];
        if($rsDashName) {

            if ($rowDashName = mysqli_fetch_assoc($rsDashName)) {
                $dashboardName = $rowDashName['name_dashboard'];
            }
        }
    //    $appName = strtoupper(explode($row['httpRoot'], '/nodered/')[1]);
        $appName = explode('/nodered/', $row['httpRoot'])[1];
        if (!is_null($dashboardName)) {
            if ($dashboardName != '') {
                $sub_nature = $dashboardName;
                if (!is_null($appName)) {
                    if ($appName != '') {
                        if (!is_null($row['flowName']) && $row['flowName'] != '') {
                            //    $unique_name_id = $row['name_w'];
                            $unique_name_id = $appName . "_" . $row['flowName'];
                        } else {
                            $unique_name_id = $appName;
                        }
                        $value_name = $unique_name_id;
                        array_push($actSensArray, $unique_name_id);
                        $nature = "From Dashboard to IOT App";
                        //    $low_level_type = $row['name'];
                        //   $low_level_type = $row['title_w'];      // e magari $parameters = $row['name']; ??
                    //    $low_level_type = $row['type_w'];
                        $low_level_type = $row['title_w'];
                        $value_type = $low_level_type;
                        $parameters = $row['name'];
                        $instance_uri = '';                                                                                    // EMPTY
                        if ($row['valueType'] == "geolocator") {
                            $unit = "json-act";
                        } else if ($row['valueType'] == "Intero") {
                            $unit = "integer-act";
                        } else if ($row['valueType'] == "Integer") {
                            $unit = "integer-act";
                        } else if ($row['valueType'] == "integer") {
                            $unit = "integer-act";
                        } else if ($row['valueType'] == "Testuale") {
                            $unit = "string-act";
                        } else if ($row['valueType'] == "webContent") {
                            $unit = "webpage-act";
                        } else if ($row['valueType'] == "Float") {
                            $unit = "float-act";
                        } else if ($row['valueType'] == "float") {
                            $unit = "float-act";
                        } else if ($row['valueType'] == "boolean") {
                            $unit = "boolean-act";
                        } else if ($row['valueType'] == "String") {
                            $unit = "string-act";
                        } else {
                            $unit = $row['valueType'];
                        }
                        $get_instances = $row['appId'];         // appID !!!!
                        $metric = "yes";
                        $saved_direct = "saved";
                        $kb_based = "no";
                        $sm_based = "no";
                        $ownership = "private";
                        $organizations = $row['organization'];
                            // METTERE ANCHE HEALTHINESS A VERDE

                        foreach($ownedApp as $struct) {
                            if ($get_instances == $struct->elementId) {
                                $owner = strtolower($struct->username);     // MOD OWN-DEL
                                break;
                            }
                        }

                        //  if (!in_array($owner, $encrCpls)) {
                        if (!array_key_exists($owner, $encrCpls)) {
                            $cryptedOwner = encryptOSSL($owner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);           // MOD OWN-DEL
                            $decryptedOwner = decryptOSSL($cryptedOwner, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);  // MOD OWN-DEL
                            $encrCpls[$owner] = $cryptedOwner;
                        } else {    // DO NOT ENCRYPT IF ALREADY CRYPTED USER
                            $cryptedOwner = $encrCpls[$owner];
                        }

                        /*    if ($availability != '') {
                                $ownership = $availability;
                            } else if ($ownShip != '') {
                                $ownership = $ownShip;
                            } else {
                                $ownership = "public";
                            }*/

                        if (in_array($get_instances, $allowedElementIDs)) {
                            $ownership = 'public';
                        } else {
                            $ownership = 'private';
                        }

                        if ($ownership == "private") {

                            // MOD OWN-DEL
                            foreach($delegatedApp as $delStruct) {
                                $userDelegated = null;
                                if ($get_instances == $delStruct['elementId']) {
                                    $userDelegated = strtolower($delStruct['usernameDelegated']);
                                    $cryptedDelegatedUsr = null;
                                    $ownerCheck = $delStruct['usernameDelegator'];
                                    if (!in_array($userDelegated, $delegatedUsers)) {
                                        if (!is_null($userDelegated) && $userDelegated != "anonymous") {
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
                                                $delegatedGroupsItem = explode(",ou", explode("cn=", $delegatedGroupsItem)[1])[0];
                                                if (!isset($delegatedGroupsItem) || trim($delegatedGroupsItem) === '') {
                                                    $delegatedGroupsItem = explode(",dc", explode("ou=", $delegatedGroupsItem)[1])[0];
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

                        $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, lastCheck, ownership, organizations, ownerHash, delegatedHash, delegatedGroupHash, value_name, value_type) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$lastCheck', '$ownership', '$organizations', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$value_name', '$value_type') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', unit = '" . $unit . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = '" . $healthiness . "', lastCheck = '" . $lastCheck . "', ownership = '" . $ownership . "', organizations = '" . $organizations . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "';";
                        mysqli_query($link, $insertQuery);
                            $count++;
                            echo($count . " - Dashboard ACTUATOR (From Dashboard to IOT App) : " . $unique_name_id . ", MEASURE: " . $low_level_type . "\n");
                    //    }
                    }
                }
            }
        }
    }
}

$queryMaxId = "SELECT * FROM Dashboard.DashboardWizard ORDER BY id DESC LIMIT 0, 1";
$rs = mysqli_query($link, $queryMaxId);
$result = [];
if($rs) {

    $dashboardName = "";

    if ($row = mysqli_fetch_assoc($rs)) {
        $maxWizardId = $row['id'];
        $queryUpdateMaxId = "ALTER TABLE Dashboard.DashboardWizard AUTO_INCREMENT " . (string) (intval($maxWizardId) + 1);
        $rs2 = mysqli_query($link, $queryUpdateMaxId);
    }
}

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End Feed IOT_App SCRIPT at: ".$end_time_ok);