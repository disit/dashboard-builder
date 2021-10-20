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

$processId = 1;     // 0 = SELECT & UPDATE; 1 = UPSERT
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
$genFileContent = parse_ini_file("../conf/environment.ini");
$personalDataFileContent = parse_ini_file("../conf/personalData.ini");
$env = $genFileContent['environment']['value'];

$host_pd= $personalDataFileContent["host_PD"][$env];
$token_endpoint= $personalDataFileContent["token_endpoint_PD"][$env];
$client_id= $personalDataFileContent["client_id_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$accessToken=get_access_token($token_endpoint, $username, $password, $client_id);

$apiUrl = $host_PD . ":8080/datamanager/api/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=IOTID";

$apiResults = file_get_contents($apiUrl);

if(trim($apiResults) != "")
{
    $resApiArray = json_decode($apiResults, true);
    $publicIOT = $resApiArray;
    foreach($resApiArray as $publicSensor)
    {
        array_push($allowedElementIDs, $publicSensor['elementId']);
    }
}

$apiAllDelegationsUrl = $host_PD . ":8080/datamanager/api/v1/delegation?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&elementType=IOTID";
$apiAllDelegationsResults = file_get_contents($apiAllDelegationsUrl);

if(trim($apiAllDelegationsResults) != "")
{
    $resApiAllArray = json_decode($apiAllDelegationsResults, true);
    $delegatedIOT = $resApiAllArray;
}

// NEW OWENRSHIP - REQUEST ALL OWNED IOTID ELEMENTS
$apiOwnershipUrl = $ownershipApiBaseUrl . "/v1/list/?type=IOTID&accessToken=" . $accessToken;

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'GET',
        'timeout' => 30,
        'ignore_errors' => true
    )
);

$context  = stream_context_create($options);
$ownedIOTJson = file_get_contents($apiOwnershipUrl, false, $context);
$ownedIOT = json_decode($ownedIOTJson);

for($i = 0; $i < count($ownedIOT); $i++) {
    array_push($ownedElements, $ownedIOT[$i]->elementId);
}

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("*** Starting IOT_Actuator_FeedDashboardWizard SCRIPT at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON IOT ACTUATORS

$high_level_type = "Sensor-Actuator";
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
$unitSens = "";
$metric = "";
$saved_direct = "";
$kb_based = "";
$sm_based = "";
$parameters = "";
$healthiness = "";
$ownership = "";
$organizations = "DISIT";

$s = "";
$a = "";
$dt = "";
$actSensArray = [];

$rs = mysqli_query($link, $query);
$result = [];

$actSensKbQueryDecoded = "select distinct ?s ?n ?a ?avn ?avt ?dt ?serviceType ?av ?ow (coalesce(?org,\"DISIT\") as ?organization) ?brokerName ?lat ?lon { " .
   "?s a km4c:IoTActuator. " .
   "?s schema:name ?n. " .
   "?s km4c:hasAttribute ?a. " .
   "?s <http://purl.oclc.org/NET/UNIS/fiware/iot-lite#exposedBy> ?broker. " .
   "?broker <http://schema.org/name> ?brokerName. " .
   "?a km4c:data_type ?dt. " .
   "OPTIONAL {?a km4c:value_name ?avn.} " .
   "OPTIONAL {?a km4c:value_type ?avt.} " .
   "OPTIONAL {?s km4c:availability ?av.} " .
   "OPTIONAL {?s km4c:ownership ?ow.} " .
   "OPTIONAL {?s km4c:organization ?org.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
   "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
   "?s a ?sType. " .
   "?sType rdfs:subClassOf ?sCategory. " .
   "?sCategory rdfs:subClassOf km4c:Service. " .
   "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType)}";

$actSensKbQuery = $kbHostUrl . ":8890/sparql?default-graph-uri=&query=" . urlencode($actSensKbQueryDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";


$queryActSensRresults = file_get_contents($actSensKbQuery);
$resArray = json_decode($queryActSensRresults, true);
$serviceChangeBuffer = array(
    "last" => "",
    "current" => "",
);


foreach ($resArray['results']['bindings'] as $key => $val) {


    $owner = null;                  // MOD OWN-DEL
    $cryptedOwner = null;           // MOD OWN-DEL
    $cryptedDelegatedUsr = null;    // MOD OWN-DEL
    $decryptedOwner = null;         // MOD OWN-DEL
    $ownerCheck = null;             // MOD OWN-DEL
    $delegatedUsers = [];           // MOD OWN-DEL
    $delegatedGroups = [];          // MOD OWN-DEL
    $delegatedUsersStr = null;        // MOD OWN-DEL
    $delegatedGroupStr = null;        // MOD OWN-DEL

    $s = $resArray['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
    $n = $resArray['results']['bindings'][$key]['n']['value'];   // $n --> service name
    $value_name = $resArray['results']['bindings'][$key]['avn']['value'];
    $value_type = $resArray['results']['bindings'][$key]['avt']['value'];
    $value_type = explode("value_type/", $value_type)[1];

  //  if (in_array($n, $actSensArray)) {
     //   echo($count . " - IOT ACTUATOR: " . $s . ", MEASURE: " . $a . "\n");
        $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute

        if (strpos($a, "actuatorcanceller") === false && strpos($a, "actuatorCanceller") === false && strpos($a, "actuatordeleted") === false && strpos($a, "actuatorDeleted") === false && strpos($a, "actuatordeletiondate") === false && strpos($a, "actuatorDeletionDate") === false && strpos($a, "creationdate") === false && strpos($a, "creationDate") === false && strpos($a, "entitycreator") === false && strpos($a, "entityCreator") === false && strpos($a, "entitydesc") === false && strpos($a, "entityDesc") === false) {
            $count++;
            echo($count . " - IOT ACTUATOR: " . $s . ", MEASURE: " . $a . "\n");
            $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
            $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
            $availability = $resArray['results']['bindings'][$key]['av']['value'];
            $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
            $brokerName = $resArray['results']['bindings'][$key]['brokerName']['value'];
            $latitude = $resArray['results']['bindings'][$key]['lat']['value'];
            $longitude = $resArray['results']['bindings'][$key]['lon']['value'];

            $organizationFromKb = $resArray['results']['bindings'][$key]['organization']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
            if (strcmp($organizationFromKb, "") !== 0) {
                $organizations = $organizationFromKb;
            } else {
                $organizations = "DISIT";
            }
            $unique_name_id = $organizations . ":" . $brokerName . ":" . $n;

            foreach($ownedIOT as $struct) {
                if ($unique_name_id == $struct->elementId) {
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

            if (in_array($unique_name_id, $allowedElementIDs)) {
                $ownership = 'public';
            } else {
                $ownership = 'private';
            }

            if ($ownership == "private") {

                // MOD OWN-DEL
                foreach($delegatedIOT as $delStruct) {
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
                                    /*    if (!isset($delegatedGroupsItem) || trim($delegatedGroupsItem) === '') {
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

            $serviceChangeBuffer["current"] = $unique_name_id;
        //    $nature = "From Dashboard to IOT Device";
        //    $sub_nature = "IoTSensor-Actuator";
            $nature = explode("_", $serviceType)[0];
            $sub_nature = explode($nature . "_", $serviceType)[1];
            $low_level_type = explode($s . "/", $a)[1];

            $instance_uri = 'single_marker';

            $unit = $dt;
            $unitSens = $unit;
            if ($unit == "geolocator") {
                $unit = "json-act";
            } else if ($unit == "integer") {
                $unit = "integer-act";
            } else if ($unit == "Testuale") {
                $unit = "string-act";
            } else if ($unit == "webContent") {
                $unit = "webpage-act";
            } else if ($unit == "float") {
                $unit = "float-act";
            } else if ($unit == "boolean") {
                $unit = "string-act";
            } else if ($unit == "binary") {
                $unit = "binary-act";
            } else if ($unit == "string") {
                $unit = "string-act";
            } else {

            }
            $get_instances = $s;

            $metric = "no";
            $saved_direct = "direct";
            $kb_based = "yes";
            $sm_based = "no";

            $parameters = $graphURI . "api/v1/?serviceUri=" . $s . "&format=json";     // CAMBIARE CON API NUOVE DI PIERO QUANDO E' PRONTA LA GET
            //  $healthiness = "na";
            if ($ownership != "private") {
                $ownership = "public";
            }

            if ($processId == 0) {
                $serviceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                $resServiceQuery = mysqli_query($link, $serviceQuery);
                $result = [];
                if($resServiceQuery) {
                    if ($row = mysqli_fetch_assoc($resServiceQuery)) {
                        $updateServiceQuery = "UPDATE DashboardWizard SET nature = '" . $nature . "', sub_nature = '" . $sub_nature . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $n . "', broker_name = '" . $brokerName . "' WHERE high_level_type = '" . $high_level_type . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $updateServiceQuery);
                    }
                }
                $serviceSensQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = 'Sensor' AND low_level_type = '" . $low_level_type . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                $resServiceSensQuery = mysqli_query($link, $serviceSensQuery);
                $resultSens = [];
                if($resServiceSensQuery) {
                    if ($rowSens = mysqli_fetch_assoc($resServiceSensQuery)) {
                        $updateServiceSensQuery = "UPDATE DashboardWizard SET nature = '" . $nature . "', sub_nature = '" . $sub_nature . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $n . "', broker_name = '" . $brokerName . "' WHERE high_level_type = 'Sensor' AND sub_nature = 'IoTSensor' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        mysqli_query($link, $updateServiceSensQuery);
                    }
                }
            } else {
                $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash, delegatedGroupHash, value_name, value_type, device_model_name, broker) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$value_name', '$value_type', '$n', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $n . "', broker_name = '" . $brokerName . "';";
                mysqli_query($link, $insertQuery);

                $insertQuerySens = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash, delegatedGroupHash, value_name, value_type, device_model_name, broker) VALUES ('$nature', 'Sensor Variable', $sub_nature, '$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unitSens', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$value_name', '$value_type', '$n', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = 'Sensor Variable', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', unit = '" . $unitSens . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $n . "', broker_name = '" . $brokerName . "';";
                mysqli_query($link, $insertQuerySens);
            }

            $serviceChangeBuffer["last"] = $unique_name_id;
        }
  //  }
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
echo("End IOT_Actuator_FeedDashboardWizard SCRIPT at: ".$end_time_ok);