<?php

/* Dashboard Builder.
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

function getAccessToken($token_endpoint, $username, $password, $client_id){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$token_endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "username=".$username."&password=".$password."&grant_type=password&client_id=".$client_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($ch);
    curl_close($ch);
    return json_decode($curl_response)->access_token;

}

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
error_reporting(E_ERROR);
session_start();

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

$accessToken=getAccessToken($token_endpoint, $username, $password, $client_id);
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
echo("*** Starting IOT_Sensor_Feed_DashboardWizard SCRIPT at: ".$start_time_ok."\n");

// FEEDING TABELLA DASHBOARD_WIZARD CON IOT SENSORS

$high_level_type = "Sensor";
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
$organizationFromKb = "";
$organizations = "";
$kbUrl = "";
$orgArray = [];

$s = "";
$a = "";
$dt = "";

$queryOrg = "SELECT * FROM Dashboard.Organizations;";
$rsOrg = mysqli_query($link, $queryOrg);
if($rsOrg) {
    $totCount = 0;
    while ($rowOrg = mysqli_fetch_assoc($rsOrg)) {
      //  array_push($orgArray, new Organization($rowOrg['organizationName'], $rowOrg['kbUrl'], $rowOrg['kbIP']));
        $orgArray[$rowOrg['organizationName']] = new Organization($rowOrg['organizationName'], $rowOrg['kbUrl'], $rowOrg['kbIP']);
    }
}

$queryIP = "SELECT DISTINCT kbIP FROM Dashboard.Organizations;";
$rsIP = mysqli_query($link, $queryIP);
if($rsIP) {
    $totCount = 0;
    while ($rowIP = mysqli_fetch_assoc($rsIP)) {
     //   $organizations = $rowOrg['organizationName'];
     //   $kbUrl = $rowOrg['kbUrl'];
        $kbHostIp = $rowIP['kbIP'];

        echo("\n--------- Ingestion IOT for kbIP: " . $kbHostIp . "\n");

        $queryIotSensorDecoded = "select distinct ?s ?n ?a ?dt ?u ?serviceType ?org ?brokerName ?lat ?lon { " .
            "?s a sosa:Sensor option (inference \"urn:ontology\"). " .
            "?s schema:name ?n. " .
            "?s km4c:hasAttribute ?a. " .
            "?s <http://purl.oclc.org/NET/UNIS/fiware/iot-lite#exposedBy> ?broker. " .
            "?broker <http://schema.org/name> ?brokerName. " .
            "?a km4c:data_type ?dt. " .
            "OPTIONAL {?a km4c:value_unit ?u.}" .
            "OPTIONAL {?s km4c:organization ?org.} " .
            "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat.} " .
            "OPTIONAL {?s <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon.} " .
            "?s a ?sType. " .
            "?sType rdfs:subClassOf ?sCategory. " .
            "?sCategory rdfs:subClassOf km4c:Service. " .
            "bind(concat(replace(str(?sCategory),\"http://www.disit.org/km4city/schema#\",\"\"),\"_\",replace(str(?sType),\"http://www.disit.org/km4city/schema#\",\"\")) as ?serviceType)}";

        $queryIotSensor = $kbHostIp . "/sparql?default-graph-uri=&query=" . urlencode($queryIotSensorDecoded) . "&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";

        $queryIotSensorRresults = file_get_contents($queryIotSensor);
        $resArray = json_decode($queryIotSensorRresults, true);
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
            echo($totCount . " (" . $count . ") - IOT DEVICE: " . $s . ", MEASURE: " . $a . "\n");
            $s = $resArray['results']['bindings'][$key]['s']['value'];   // $s --> serviceUri
            $n = $resArray['results']['bindings'][$key]['n']['value'];   // $n --> service name
            $a = $resArray['results']['bindings'][$key]['a']['value'];   // $a --> attribute
            $dt = $resArray['results']['bindings'][$key]['dt']['value'];   // $dt --> data type
            $u = $resArray['results']['bindings'][$key]['u']['value'];
            $serviceType = $resArray['results']['bindings'][$key]['serviceType']['value'];
            $availability = $resArray['results']['bindings'][$key]['av']['value'];
            //  $ownShip = $resArray['results']['bindings'][$key]['ow']['value'];
            $brokerName = $resArray['results']['bindings'][$key]['brokerName']['value'];
            $latitude = $resArray['results']['bindings'][$key]['lat']['value'];
            $longitude = $resArray['results']['bindings'][$key]['lon']['value'];

            $organizationFromKb = $resArray['results']['bindings'][$key]['org']['value']; // $org --> organization NEW 10 GENNAIO 2019 !!
          //  if (strcmp($organizationFromKb, $organizations) == 0) {
            if (isset($organizationFromKb) && $organizationFromKb != '') {
                $organizations = $organizationFromKb;
                $kbUrl = $orgArray[$organizations]->getKbUrl();
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

            // $unique_name_id = explode($baseKm4CityUri, $s)[1];
            //  $unique_name_id = $n;
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

            if(in_array($unique_name_id, $allowedElementIDs)) {
                $ownership = "public";
            } else {
                $ownership = "private";

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

            $nature = "From IOT Device to KB";
            $sub_nature = "IoTSensor";
            $low_level_type = explode($s . "/", $a)[1];
            $instance_uri = 'single_marker';
            $unit = $dt;
            $get_instances = $s;
            $metric = "no";
            $saved_direct = "direct";
            $kb_based = "yes";
            $sm_based = "yes";
            $parameters = $kbUrl . "?serviceUri=" . $s . "&format=json";

            if ($serviceChangeBuffer["current"] != $serviceChangeBuffer["last"]) {
                //$insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude', '$cryptedOwner', '$delegatedUsersStr') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "';";
                $insertGeneralServiceQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, ownerHash, delegatedHash, delegatedGroupHash) VALUES ('$nature','$high_level_type','$sub_nature','', '$unique_name_id', '$instance_uri', '$get_instances', 'sensor_map', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', 'true', '$ownership', '$organizations', '$latitude', '$longitude', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "',  get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "';";
                mysqli_query($link, $insertGeneralServiceQuery);
            }

            //$insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, ownerHash, delegatedHash) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$cryptedOwner', '$delegatedUsersStr') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "';";
            $insertQuery = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, unit, metric, saved_direct, kb_based, sm_based, parameters, healthiness, ownership, organizations, latitude, longitude, value_unit, ownerHash, delegatedHash, delegatedGroupHash) VALUES ('$nature','$high_level_type','$sub_nature','$low_level_type', '$unique_name_id', '$instance_uri', '$get_instances', '$unit', '$metric', '$saved_direct', '$kb_based', '$sm_based', '$parameters', '$healthiness', '$ownership', '$organizations', '$latitude', '$longitude', '$u', '$cryptedOwner', '$delegatedUsersStr', '$delegatedGroupStr') ON DUPLICATE KEY UPDATE high_level_type = '" . $high_level_type . "', sub_nature = '" . $sub_nature . "', low_level_type = '" . $low_level_type . "', unique_name_id = '" . $unique_name_id . "', instance_uri = '" . $instance_uri . "', get_instances = '" . $get_instances . "', sm_based = '" . $sm_based . "', last_date = last_date, last_value = last_value, parameters = '" . $parameters . "', healthiness = healthiness, ownership = ownership, organizations = '" . $organizations . "', latitude = '" . $latitude . "', longitude = '" . $longitude . "', value_unit = '" . $u . "', ownerHash = '" . $cryptedOwner . "', delegatedHash = '" . $delegatedUsersStr . "', delegatedGroupHash = '" . $delegatedGroupStr . "';";
            mysqli_query($link, $insertQuery);

            $serviceChangeBuffer["last"] = $unique_name_id;

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
echo("End IOT_Sensor_Feed_DashboardWizard SCRIPT at: ".$end_time_ok);