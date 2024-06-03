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

class Organization
{
    private $organizationName;
    private $kbUrl;
    private $kbIP;

    public function __construct($orgName, $url, $ip)
    {
        $this->organizationName = $orgName;
        $this->kbUrl = $url;
        $this->kbIP = $ip;
    }

    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    public function getKbUrl()
    {
        return $this->kbUrl;
    }

    public function getKbIP()
    {
        return $this->kbIp;
    }
}

class Model {
    private $modelName;
    private $modelType;
    private $modelOrganizations = [];
    private $modelNature;
    private $modelSubNature;
    private $modelBroker;

    public function __construct($name, $type, $orgs, $nat, $sub_nat, $broker)
    {
        $this->modelName = $name;
        $this->modelType = $type;
        $this->modelOrganizations = [$orgs];
        $this->modelNature = $nat;
        $this->modelSubNature = $sub_nat;
        $this->modelBroker = $broker;
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function setModelName($name)
    {
        $this->modelName = $name;
    }

    public function getModelType()
    {
        return $this->modelType;
    }

    public function setModelType($type)
    {
        $this->modelType = $type;
    }

    public function getModelOrganizations()
    {
        return $this->modelOrganizations;
    }

    public function setModelOrganizations($orgs)
    {
        $this->modelOrganizations = $orgs;
    }

    public function getModelNature()
    {
        return $this->modelNature;
    }

    public function setModelNature($nat)
    {
        $this->modelNature = $nat;
    }

    public function getModelSubNature()
    {
        return $this->modelSubNature;
    }

    public function setModelSubNature($sub_nat)
    {
        $this->modelSubNature = $sub_nat;
    }

    public function getModelBroker()
    {
        return $this->modelBroker;
    }

    public function setModelBroker($broker)
    {
        $this->modelBroker = $broker;
    }
}

function  objExists($obj, $org) {
    global $modelArray;
    foreach ($modelArray as $elem) {
        if ($elem->getModelName() == $obj->getModelName()) {
            if (!in_array($org, $elem->getModelOrganizations())) {
                $newOrgArray = $elem->getModelOrganizations();
                array_push($newOrgArray, $org);
                $elem->setModelOrganizations($newOrgArray);
                return true;
            } else {
                return true;
            }
        }
    }
    return false;
}

function prepareElements($model, $impl, $is_mob, $serviceType, $org, $broker) {
    global $modelArray;
    $nat = explode("_", $serviceType)[0];
    $sub_nat = explode($nat . "_", $serviceType)[1];
    if ($impl == "http://www.disit.org/km4city/resource/iot/DataTable") {
        $hlt = "Data Table Device";
        $modelObj = New Model($model, "Data Table Model", $org, $nat, $sub_nat, $broker);
    } else if ($is_mob == "true") {
        $hlt = "Mobile Device";
        $modelObj = New Model($model, "Mobile Device Model", $org, $nat, $sub_nat, $broker);
    } else {
        $hlt = "IoT Device";
        $modelObj = New Model($model, "IoT Device Model", $org, $nat, $sub_nat, $broker);
    }

    if (!objExists($modelObj, $org)) {
        array_push($modelArray, $modelObj);
    }

    return array($hlt, $nat, $sub_nat);
}

function getHltFromGeneric($hlt_gen) {
    $hlt = "";
    if ($hlt_gen == "Data Table Device") {
        $hlt = "Data Table Variable";
    }
    if ($hlt_gen == "Mobile Device") {
        $hlt = "Mobile Device Variable";
    }
    if ($hlt_gen == "IoT Device") {
        $hlt = "IoT Device Variable";
    }
    return $hlt;
}

include '../config.php';
//ini_set('memory_limit', '2048M');
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

include '../opensearch/OpenSearchS4C.php';
$open_search = new OpenSearchS4C();
$open_search->initDashboardWizard();


error_reporting(E_ERROR);
session_start();

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
$client_secret= $personalDataFileContent["client_secret_PD"][$genFileContent['environment']['value']];
$username= $personalDataFileContent["usernamePD"][$genFileContent['environment']['value']];
$password= $personalDataFileContent["passwordPD"][$genFileContent['environment']['value']];

$accessToken=get_access_token($token_endpoint, $username, $password, $client_id, $client_secret);
if (empty($accessToken)) {
    exit("\nAccess Token Not Valid. Program Terminated.\n");
}
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
echo("*** Starting IOT_Device_Update_DashboardWizard SCRIPT at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON IOT SENSORS

$high_level_type = "";

$nature = "";
$sub_nature_array = [];
$sub_nature = "";
$low_level_type = "";
$unique_name_to_split = "";
$unique_name_id = "";

$high_level_type_new = "";
$nature_new = "";
$sub_nature_array_new = [];
$sub_nature_new = "";
//$low_level_type_new = "";
//$unique_name_id_new = "";

$value_name = "";
$value_type = "";
$device_model_name = "";
$is_mobile = "";

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
$organizationFromKb = "";
$organizations = "";
$kbUrl = "";
$orgArray = [];
$modelArray = [];
$imp = "";

$s = "";
$a = "";
$dt = "";
$sparqlErrorFlag = false;
$sparqlBatchCounter = 0;
$sparqlLimit = 50000;
$sparqlOffset = 0;

$queryOrg = "SELECT * FROM Dashboard.Organizations;";
$rsOrg = mysqli_query($link, $queryOrg);
if($rsOrg) {
    $totCount = 0;
    while ($rowOrg = mysqli_fetch_assoc($rsOrg)) {
        //  array_push($orgArray, new Organization($rowOrg['organizationName'], $rowOrg['kbUrl'], $rowOrg['kbIP']));
        $orgArray[$rowOrg['organizationName']] = new Organization($rowOrg['organizationName'], $rowOrg['kbUrl'], $rowOrg['kbIP']);
    }
}

$queryIP = "SELECT DISTINCT kbIP FROM Dashboard.Organizations WHERE kbIp IS NOT NULL;";
$rsIP = mysqli_query($link, $queryIP);
if($rsIP) {
    $totCount = 0;
    while ($rowIP = mysqli_fetch_assoc($rsIP)) {
        //   $organizations = $rowOrg['organizationName'];
        //   $kbUrl = $rowOrg['kbUrl'];
        $kbHostIp = $rowIP['kbIP'];
        //    if ($kbHostIp == "http://192.168.1.160:8890") {
        $sparqlErrorFlag = false;
        $sparqlBatchCounter = 0;
        $sparqlLimit = 50000;
        $sparqlOffset = 0;
        echo("\n--------- Ingestion IOT for kbIP: " . $kbHostIp . "\n");

        while ($sparqlErrorFlag === false) {
            $sparqlOffset = ($sparqlLimit * $sparqlBatchCounter) + 1;
            $queryIotSensorDecoded = "select distinct ?s ?n ?a ?avn ?avt ?dt ?u ?serviceType ?org ?imp ?brokerName ?model ?mobile ?lat ?lon { " .
                "?s a sosa:Sensor option (inference \"urn:ontology\"). " .
                "?s schema:name ?n. " .
                "?s km4c:hasAttribute ?a. " .
                "?s <http://purl.oclc.org/NET/UNIS/fiware/iot-lite#exposedBy> ?broker. " .
                "?broker <http://schema.org/name> ?brokerName. " .
                "?a km4c:data_type ?dt. " .
                "OPTIONAL {?a km4c:value_name ?avn.} " .
                "OPTIONAL {?a km4c:value_type ?avt.} " .
                "OPTIONAL {?a km4c:value_unit ?u.} " .
                "OPTIONAL {?s km4c:organization ?org.} " .
                "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
                "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
                "OPTIONAL {?s km4c:model ?model.} " .
                "OPTIONAL {?s km4c:isMobile ?mobile.} " .
                "OPTIONAL {?s <http://www.w3.org/ns/ssn/implements> ?imp.} " .
                "?s a ?sType. " .
                "?sType rdfs:subClassOf* ?sCategory. " .
                "?sCategory rdfs:subClassOf km4c:Service. " .
                "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType)} " .
                "OFFSET " . $sparqlOffset .
                " LIMIT " . $sparqlLimit;

            $queryIotSensor = $kbHostIp . "/sparql?default-graph-uri=&query=" . urlencode($queryIotSensorDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

        /*    try {
                $queryIotSensorRresults = file_get_contents($queryIotSensor);
            } catch (Exception $e) {
                echo ("ERRORE SPARQL QUERY: " . $e->getMessage() . "\n");
                $sparqlErrorFlag = true;
            }   */

            $queryIotSensorRresults = file_get_contents($queryIotSensor);
            if ($queryIotSensorRresults === false) {
                echo ("ERRORE SPARQL QUERY. Exit Script.\n");
                $sparqlErrorFlag = true;
                exit();
            }

            $resArray = json_decode($queryIotSensorRresults, true);

            if (sizeof($resArray['results']['bindings']) == 0) {
                $sparqlErrorFlag = true;
            }

            $serviceChangeBuffer = array(
                "last" => "",
                "current" => "",
            );

            $count = 0;

            foreach ($resArray['results']['bindings'] as $key => $val) {

                $count++;
                $totCount++;
                $owner = null;
                $cryptedOwner = null;
                $decryptedOwner = null;
                $ownerCheck = null;
                $delegatedUsers = [];
                $delegatedUsersStr = "";
                $delegatedGroups = [];
                $delegatedGroupStr = "";
                $s = $resArray['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
                $n = $resArray['results']['bindings'][$key]['n']['value'];   // $n --> service name
                $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute
                echo($totCount . " (" . $count . ") - IOT DEVICE: " . $s . ", MEASURE: " . $a . "\n");
                $value_name = $resArray['results']['bindings'][$key]['avn']['value'];
                $value_type = $resArray['results']['bindings'][$key]['avt']['value'];
                $value_type = explode("value_type/", $value_type)[1];
                $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
                $u = $resArray['results']['bindings'][$key]['u']['value'];
                $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
                $availability = $resArray['results']['bindings'][$key]['av']['value'];
                //  $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
                $brokerName = $resArray['results']['bindings'][$key]['brokerName']['value'];
                $latitude = $resArray['results']['bindings'][$key]['lat']['value'];
                $longitude = $resArray['results']['bindings'][$key]['lon']['value'];
                $imp = $resArray['results']['bindings'][$key]['imp']['value'];
                $is_mobile = $resArray['results']['bindings'][$key]['mobile']['value'];

                $low_level_type = explode($s . "/", $a)[1];

                $organizationFromKb = $resArray['results']['bindings'][$key]['org']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
                //  if (strcmp($organizationFromKb, $organizations) == 0) {
                if (isset($organizationFromKb) && $organizationFromKb != '') {
                    $organizations = $organizationFromKb;
                    if (isset($orgArray[$organizations])) {
                        $kbUrl = $orgArray[$organizations]->getKbUrl();
                    } else {
                        $stopFlag = 1;
                    }
                } else if ($kbHostIp == 'http://192.168.0.206:8890') {
                    $organizations = "DISIT";
                    $kbUrl = $orgArray[$organizations]->getKbUrl();
                    /*  } else if ($kbHostIp == 'http://192.168.0.205:8890') {
                          if ($brokerName == 'orionFinland')
                          $organizations = "Helsinki";
                          $kbUrl = $orgArray[$organizations]->getKbUrl();*/
                } else {
                    $organizations = "Other";
                    $kbUrl = $kbUrlSuperServiceMap;
                }

                $unique_name_id = $organizations . ":" . $brokerName . ":" . $n;
                $device_model_name = $n;

                foreach ($ownedIOT as $struct) {
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

                if (in_array($unique_name_id, $allowedElementIDs)) {
                    $ownership = "public";
                } else {
                    $ownership = "private";

                    // MOD OWN-DEL
                    foreach ($delegatedIOT as $delStruct) {
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

                if ($processId == 0) {      // UPDATE
                    $high_level_type = "Sensor";
                    $nature = "From IOT Device to KB";
                    $sub_nature = "IoTSensor";

                } else {                    // UPSERT
                    list($high_level_type, $nature, $sub_nature) = prepareElements($resArray['results']['bindings'][$key]['model']['value'], $imp, $is_mobile, $serviceType, $organizations, $resArray['results']['bindings'][$key]['brokerName']['value']);
                /*    $model = $resArray['results']['bindings'][$key]['model']['value'];
                    if ($imp == "http://www.disit.org/km4city/resource/iot/DataTable") {
                        $high_level_type = "Data Table Device";
                        $modelObj = New Model($model, "Data Table Model", $organizations, $nature, $sub_nature);
                    } else {
                        $high_level_type = "IoT Device";
                        $modelObj = New Model($model, "IoT Device Model", $organizations, $nature, $sub_nature);
                    }
                    $sub_nature_array = explode("_", $serviceType);
                    //  if (sizeof($sub_nature_array) > 2) {
                    $nature = explode("_", $serviceType)[0];
                    $sub_nature = explode($nature . "_", $serviceType)[1];

                    if (!isset($modelArray[$modelObj->getModelName()])) {
                        push_array($modelArray, $modelObj);
                    } else {
                        // check and eventually add organization to model
                        if (!in_array($organizations, $modelObj->getModelOrganizations())) {
                            array_push($modelObj->getModelOrganizations(), $organizations);
                        }
                    }*/
                }

                $instance_uri = 'single_marker';
                $unit = $dt;
                $get_instances = $s;
                $metric = "no";
                $saved_direct = "direct";
                $kb_based = "yes";
                $sm_based = "yes";
                $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";

                if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
                    if ($processId == 0) {
                        $generalServiceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND unit = 'sensor_map' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                        //$resGeneralServiceQuery = mysqli_query($link, $generalServiceQuery);
                        $result = [];
                        if($resGeneralServiceQuery) {
                            if ($row = mysqli_fetch_assoc($resGeneralServiceQuery)) {
                                list($high_level_type_new, $nature_new, $sub_nature_new) = prepareElements($resArray['results']['bindings'][$key]['model']['value'], $imp, $is_mobile, $serviceType, $organizations, $resArray['results']['bindings'][$key]['brokerName']['value']);
                                /*if ($imp == "http://www.disit.org/km4city/resource/iot/DataTable") {
                                    $high_level_type_new = "Data Table Device";
                                } else {
                                    $high_level_type_new = "IoT Device";
                                }
                                $sub_nature_array_new = explode("_", $serviceType);
                                //  if (sizeof($sub_nature_array) > 2) {
                                $nature_new = explode("_", $serviceType)[0];
                                $sub_nature_new = explode($nature_new . "_", $serviceType)[1];*/

                                $updateGeneralServiceQuery = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type_new . "', nature = '" . $nature_new . "', sub_nature = '" . $sub_nature_new . "', unique_name_id = '" . $unique_name_id . "', device_model_name = '" . $device_model_name . "', broker_name = '" . $brokerName . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "' AND unit = 'sensor_map';";
                                //mysqli_query($link, $updateGeneralServiceQuery);
                            }
                        }

                        

                        $id = $open_search->checkDuplicateDocument($high_level_type, $sub_nature, $instance_uri,
                         $get_instances, '', $unique_name_id, $open_search::default_index_name,'', '',true, ['term'=>['unit'=>'sensor_map']],false);

                        if($id !== false){
                            $array = $open_search->createUpdateDocumentDashboardWizard(
                                $nature_new,
                                $high_level_type_new,
                                $sub_nature_new,
                                '',
                                $unique_name_id,
                                $instance_uri,
                                $get_instances,
                                'sensor_map',
                                $metric,
                                $saved_direct,
                                $kb_based,
                                $sm_based,
                                '',
                                '',
                                $parameters,
                                '',
                                '',
                                'true',
                                '',
                                $ownership,
                                $organizations,
                                $latitude,
                                $longitude,
                                '',
                                '',
                                '',
                                $device_model_name,
                                $cryptedOwner,
                                $delegatedUsersStr,
                                $delegatedGroupStr,
                                $brokerName,
                                '',
                                $id,
                                false,
                                null,
                                OpenSearchS4C::default_index_name,
                                $device_model_name
    
    
                            );
                        }

                    } else {

                        $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash, delegatedGroupHash, device_model_name, broker_name) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$device_model_name', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', device_model_name = '" . $device_model_name . "', broker_name = '" . $brokerName . "';";
                        //mysqli_query($link, $insertGeneralServiceQuery);

                        $open_search->createUpdateDocumentDashboardWizard(
                            $nature,
                            $high_level_type,
                            $sub_nature,
                            '',
                            $unique_name_id,
                            $instance_uri,
                            $get_instances,
                            'sensor_map',
                            $metric,
                            $saved_direct,
                            $kb_based,
                            $sm_based,
                            '',
                            '',
                            $parameters,
                            '',
                            '',
                            'true',
                            '',
                            $ownership,
                            $organizations,
                            $latitude,
                            $longitude,
                            '',
                            '',
                            '',
                            $device_model_name,
                            $cryptedOwner,
                            $delegatedUsersStr,
                            $delegatedGroupStr,
                            $brokerName,
                            '',
                            false,
                            false,
                            null,
                            $open_search::default_index_name,
                            $device_model_name,
                            $resArray['results']['bindings'][$key]['model']['value'] 

                        );


                    }
                }

                if ($processId == 0) {
                    $serviceQuery = "SELECT * FROM DashboardWizard WHERE high_level_type = '". $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                    //$resServiceQuery = mysqli_query($link, $serviceQuery);
                    $result = [];
                    if($resServiceQuery) {
                        if ($row = mysqli_fetch_assoc($resServiceQuery)) {
                            list($high_level_type_new, $nature_new, $sub_nature_new) = prepareElements($resArray['results']['bindings'][$key]['model']['value'], $imp, $is_mobile, $serviceType, $organizations, $resArray['results']['bindings'][$key]['brokerName']['value']);
                            /*    if ($imp == "http://www.disit.org/km4city/resource/iot/DataTable") {
                                    $high_level_type_new = "Data Table Device";
                                } else {
                                    $high_level_type_new = "IoT Device";
                                }
                                $sub_nature_array_new = explode("_", $serviceType);
                                //  if (sizeof($sub_nature_array) > 2) {
                                $nature_new = explode("_", $serviceType)[0];
                                $sub_nature_new = explode($nature . "_", $serviceType)[1];*/
                            $high_level_type_new_v = getHltFromGeneric($high_level_type_new);
                            $updateServiceQuery = "UPDATE DashboardWizard SET high_level_type = '" . $high_level_type_new_v . "', nature = '" . $nature_new . "', sub_nature = '" . $sub_nature_new . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $device_model_name . "', broker_name = '" . $brokerName . "' WHERE high_level_type = '" . $high_level_type . "' AND sub_nature = '" . $sub_nature . "' AND low_level_type = '" . $low_level_type . "' AND unique_name_id = '" . $unique_name_id . "' AND instance_uri = '" . $instance_uri . "' AND get_instances = '" . $get_instances . "';";
                            //mysqli_query($link, $updateServiceQuery);
                        }
                    }
                    $id = $open_search->checkDuplicateDocument($high_level_type, $sub_nature, $instance_uri,
                         $get_instances, $low_level_type , $unique_name_id, $open_search::default_index_name);

                        if($id !== false){
                            $open_search->createUpdateDocumentDashboardWizard(
                                $nature_new,
                                $high_level_type_new_v,
                                $sub_nature_new,
                                $low_level_type,
                                $unique_name_id,
                                $instance_uri,
                                $get_instances,
                                $unit,
                                $metric,
                                $saved_direct,
                                $kb_based,
                                $sm_based,
                                '',
                                '',
                                $parameters,
                                '',
                                '',
                                $healthiness,
                                '',
                                $ownership,
                                $organizations,
                                $latitude,
                                $longitude,
                                $u,
                                $value_name,
                                $value_type,
                                $device_model_name,
                                $cryptedOwner,
                                $delegatedUsersStr,
                                $delegatedGroupStr,
                                $brokerName,
                                '',
                                $id

                            );
                        }
                } else {
                    $high_level_type_v = getHltFromGeneric($high_level_type);
                    $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, ownerHash, delegatedHash, delegatedGroupHash, value_name, value_type, device_model_name, broker_name) VALUES ('$nature','$high_level_type_v','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr', '$value_name', '$value_type', '$device_model_name', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type_v . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "',  unit = '" . $unit . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = '" . $ownership . "', organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $value_name . "', value_type = '" . $value_type . "', device_model_name = '" . $device_model_name . "', broker_name = '" . $brokerName . "';";
                    //mysqli_query($link, $insertQuery);

                    $open_search->createUpdateDocumentDashboardWizard(
                        $nature,
                        $high_level_type_v,
                        $sub_nature,
                        $low_level_type,
                        $unique_name_id,
                        $instance_uri,
                        $get_instances,
                        $unit,
                        $metric,
                        $saved_direct,
                        $kb_based,
                        $sm_based,
                        '',
                        '',
                        $parameters,
                        '',
                        '',
                        $healthiness,
                        '',
                        $ownership,
                        $organizations,
                        $latitude,
                        $longitude,
                        $u,
                        $value_name,
                        $value_type,
                        $device_model_name,
                        $cryptedOwner,
                        $delegatedUsersStr,
                        $delegatedGroupStr,
                        $brokerName,
                        '',
                        false,
                        false,
                        null,
                        $open_search::default_index_name,
                        $device_model_name,
                        $resArray['results']['bindings'][$key]['model']['value']

                    );
                }

                $serviceChangeBuffer["last"] = $unique_name_id;

            }
            $sparqlBatchCounter++;

        }
        //  }

    }
}

foreach ($modelArray as $mod) {
    $high_level_type_model = $mod->getModelType();
    $nature_model = $mod->getModelNature();
    $sub_nature_model = $mod->getModelSubNature();
    $low_level_type_model = "";
    $unique_name_id_model = $mod->getModelName();
    $orgs_model = $mod->getModelOrganizations();
    $brokerName = $mod->getModelBroker();
    if (sizeof($orgs_model) > 1) {
        $organizations_model = "[" . implode(", ",$orgs_model) . "]";
    } else {
        $organizations_model = implode(" ",$orgs_model);
    }
    $instance_uri_model = "any";
    $get_instances_model = "curr view (optional specific, shape, area, distance)";
    $unit_model = "map";
    $metric_model = "no";
    $saved_direct_model = "direct";
    $kb_based_model = "yes";
    $sm_based_model = "no";
    $widgets_model = "map";
    $parameters_model = $unique_name_id_model;
    $healthiness_model = "true";
    $ownership_model = "public";
    $device_model_name_model = $mod->getModelName();
    // $value_type_model

    // latitude = ?, longitude = ?, ownerHash = SI C'E', SENTIRE SE SERVE, delegatedHash = ?, delegatedGroupHash = ?, value_name, device_model_name, broker_name
    #$insertModelQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash, delegatedGroupHash, value_name, device_model_name, broker_name) VALUES ('$nature_model','$high_level_type_model','$sub_nature_model','', '$unique_name_id_model', '$instance_uri_model', '$get_instances_model', 'map', '$metric_model', '$saved_direct_model', '$kb_based_model', '$sm_based_model', '$parameters_model', 'true', '$ownership_model', '$organizations_model', '$latitude_model', '$longitude_model', '$cryptedOwner_model', '$delegatedUsersStr_model', '$delegatedGroupStr_model', '$device_model_name', '$device_model_name', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "', value_name = '" . $device_model_name . "', device_model_name = '" . $device_model_name . "', broker_name = '" . $brokerName . "';";
    $insertModelQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, user, widgets, parameters, healthiness, ownership, organizations, device_model_name, broker_name) VALUES ('$nature_model','$high_level_type_model','$sub_nature_model','', '$unique_name_id_model', '$instance_uri_model', '$get_instances_model', 'map', '$metric_model', '$saved_direct_model', '$kb_based_model', '$sm_based_model', '', '$widgets_model', '$parameters_model', 'true', '$ownership_model', '$organizations_model', '$device_model_name_model', '$brokerName') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', widgets = '" . $widgets_model . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', device_model_name = '" . $device_model_name_model . "', broker_name = '" . $brokerName . "';";
    //mysqli_query($link, $insertModelQuery);

    $open_search->createUpdateDocumentDashboardWizard(
        $nature_model,
        $high_level_type_model,
        $sub_nature_model,
        '',
        $unique_name_id_model,
        $instance_uri_model,
        $get_instances_model,
        'map',
        $metric_model,
        $saved_direct_model,
        $kb_based_model,
        $sm_based_model,
        '',
        $widgets_model,
        $parameters_model,
        '',
        '',
        'true',
        '',
        $ownership_model,
        $organizations_model,
        '',
        '',
        '',
        '',
        '',
        $device_model_name_model,
        '',
        '',
        '',
        $brokerName,
        '',
        false,
        false,
        null,
        $open_search::default_index_name,
        '',
        $device_model_name_model
        
    );

    /*var_dump("INSERT INTO DashboardWizard");*/

   
}

/*$queryMaxId = "SELECT * FROM Dashboard.DashboardWizard ORDER BY id DESC LIMIT 0, 1";
$rs = mysqli_query($link, $queryMaxId);
$result = [];
if($rs) {

    $dashboardName = "";

    if ($row = mysqli_fetch_assoc($rs)) {
        $maxWizardId = $row['id'];
        $queryUpdateMaxId = "ALTER TABLE Dashboard.DashboardWizard AUTO_INCREMENT " . (string) (intval($maxWizardId) + 1);
        $rs2 = mysqli_query($link, $queryUpdateMaxId);
    }
}*/

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End IOT_Device_Update_DashboardWizard SCRIPT at: ".$end_time_ok);
