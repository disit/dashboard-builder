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
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

//Patch: inspiegabilmente in ssoEndpoint ci viene scritto https://www.
$genFileContent = parse_ini_file("../conf/environment.ini");
$ssoContent = parse_ini_file("../conf/sso.ini");
$ssoEndpoint = $ssoContent["ssoEndpoint"][$genFileContent['environment']['value']];

//$init_flag = 1;
session_start();

//Te la gestisci in base al grado di gestione d'errore che ti serve
//error_reporting(E_ALL);
//set_error_handler("exception_error_handler");
if (isset($REQUEST["getIcons"])) {

    $stop_flag = 1;
    
}

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ERROR | E_NOTICE);
error_reporting(E_ERROR);

// if (isset($_REQUEST["filterGlobal"])) {
if (isset($_REQUEST["globalSqlFilter"])) {
//if (!empty($_REQUEST["filterGlobal"]) && !empty($_REQUEST["value"])) {

    if (isset($_REQUEST["filterGlobal"])) {
        $sql_where = $_REQUEST['filterGlobal'];
    } else {
        $sql_where = "";
    }
    $freezeMap = $_REQUEST['freezeMap'];
 //   $orgFilter = $_REQUEST['orgFilter'];
  //  $noneSelectedFlagWithGeoFilter = $_REQUEST['noneSelectedFlagWithGeoFilter'];

    $whereString = "";
    $nActive = $_REQUEST['nActive'];
    if (checkVarType($nActive, "integer") === false) {
        //     eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. USER = " . $_SESSION['loggedUsername'] . ". Exit from script.");
        eventLog("Returned the following ERROR in dashboardWizardController.php for nActive = ".$nActive.": ".$nActive." is not an integer as expected. Exit from script.");
        exit();
    };
    $globalSqlFilter = $_REQUEST['globalSqlFilter'];
    $n = $_REQUEST['n'];
    if (checkVarType($n, "integer") === false) {
        //     eventLog("Returned the following ERROR in index.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. USER = " . $_SESSION['loggedUsername'] . ". Exit from script.");
        eventLog("Returned the following ERROR in dashboardWizardController.php for n = ".$n.": ".$n." is not an integer as expected. Exit from script.");
        exit();
    };
    if (!is_array($globalSqlFilter)) {
        eventLog("Returned the following ERROR in dashboardWizardController.php: globalSqlFilter is not an array as expected. Exit from script.");
        exit();
    }
    // CHECK IF $globalSqlFilter IS ARRAY ? OR OBJECT ?
    $orgFilter = $_REQUEST['orgFilter'];

    // FARE QUI COMPOSIZIONE FILTRO GLOBALE STRINGA  GUARDANDO QUALE NON E' FIELD !
    for ($k = 0; $k < sizeof($_REQUEST['globalSqlFilter']); $k++) {
        if ($k !== 4 && $k != 5) {
            if (($k != $n || $nActive > 1)) {
                $str = $globalSqlFilter[$k]['value'];
                if(is_array($str) && sizeof($str) == 0) {
                    $str = "";
                }
                $auxArray = explode("|", $str);
                $auxFilterString = "";
            //    for ($j in auxArray) {
                foreach($auxArray as $j => $valAuxArray) {
                    if ($auxArray[$j] != '') {
                        if ($j != 0 && $auxFilterString != '') {
                            $auxFilterString = $auxFilterString . " OR " . $globalSqlFilter[$k]['field'] . " = '" . $valAuxArray . "'";
                        } else {
                            $auxFilterString = $globalSqlFilter[$k]['field'] . " = '" . $valAuxArray . "'";
                        }
                    }
                }

                if ($auxFilterString != '') {
                    if ($whereString != '') {
                        if ($k != 0) {
                            $whereString = $whereString . " AND (" . $auxFilterString . ")";
                        } else {
                            $whereString = $whereString . "(" . $auxFilterString . ")";
                        }
                    } else {
                        $whereString =  "(" . $auxFilterString . ")";
                    }
                }
            }
        }
    }

    if ($_SESSION['loggedRole'] !== "RootAdmin") {
        if ($whereString === "") {
            //   $whereString = " WHERE organizations REGEXP '" + $orgFilter + "' OR $ownership = 'private'";
            $whereString = " organizations REGEXP '" . $orgFilter . "'";
        } else {
            //   $whereString = " AND organizations REGEXP '" + $orgFilter + "' OR $ownership = 'private'";
            $whereString = $whereString . " AND organizations REGEXP '" . $orgFilter . "'";
        }
    }
  
    $sql_distinct_field = $_REQUEST['distinctField'];
    if ($sql_distinct_field != "high_level_type" && $sql_distinct_field != "nature" && $sql_distinct_field != "sub_nature" && $sql_distinct_field != "low_level_type" && $sql_distinct_field != "unit" && $sql_distinct_field != "unique_name_id" && $sql_distinct_field != "healthiness" && $sql_distinct_field != "ownership" && $sql_distinct_field != "value_unit" && $sql_distinct_field != "broker_name" && $sql_distinct_field != "value_name" && $sql_distinct_field != "value_type") {
    //    eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_distinct_field '".$sql_distinct_field."' is not an allowed value. Force sql_distinct_field = 'high_level_type'");
        eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_distinct_field '".$sql_distinct_field."' is not an allowed value. Exit from script.");
    //    $sql_distinct_field = "high_level_type";
        exit();
    }
    
  /*  if (strpos($sql_where, "AND") == 1) {
        $sql_where_ok = explode("AND ", $sql_where.trim())[1];
    } else {
        $sql_where_ok = $sql_where;
    }
    
    if (empty($_REQUEST["filterGlobal"])) {
        $sql_where_ok = 1;
    }*/

  //  $sql_where_escaped = escapeForSQL($sql_where_ok, $link);
    $sql_where_ok = $whereString;

    $wizardColumns = array('high_level_type', 'nature', 'sub_nature', 'low_level_type', 'broker_name', 'value_name', 'value_type', 'unit', 'healthiness', 'ownership', 'value_unit');

    $dashLoggedUsername = $_SESSION['loggedUsername'];
    if ($_SESSION['loggedRole'] !== "RootAdmin") {
        $cryptedUsr = encryptOSSL($dashLoggedUsername, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);

        $whereAllHash = " AND (ownership = 'public' OR (ownerHash LIKE '%" . $cryptedUsr . "%' OR delegatedHash LIKE '%" . $cryptedUsr . "%'))";
    } else {
        $whereAllHash = "";
        if ($sql_where_ok === "") {
            $sql_where_ok = "1";
        }
    }
	
	$synMode = ""; if(intval($_REQUEST["synMode"])) $synMode .= " AND high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";

    $geoQuery = "";
    if(isset($_REQUEST['northEastPointLat']) && isset($_REQUEST['FreezeMap'])){
        if ($_REQUEST['FreezeMap'] === "true") {
            $northEastPointLat = escapeForSQL($_REQUEST['northEastPointLat'], $link);
            $northEastPointLng = escapeForSQL($_REQUEST['northEastPointLng'], $link);
            $southWestPointLat = escapeForSQL($_REQUEST['southWestPointLat'], $link);
            $southWestPointLng = escapeForSQL($_REQUEST['southWestPointLng'], $link);
            $geoQuery = " AND (latitude is not null and longitude is not null and latitude<>'' and longitude<>'' and latitude <='" . $northEastPointLat . "' and latitude >='" . $southWestPointLat . "' and longitude <='" . $northEastPointLng . "' and longitude >='" . $southWestPointLng . "') OR (high_level_type = 'POI' OR high_level_type = 'IoT Device Model' OR high_level_type = 'Mobile Device Model' OR high_level_type = 'Data Table Model') ";
        }
    }

    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE oldEntry IS NULL AND ".$sql_where_ok . $whereAllHash . $synMode . $geoQuery . " ORDER BY ".$sql_distinct_field." ASC;";

    if ($freezeMap == "true") {
        $wizardColId = array_search($sql_distinct_field, $wizardColumns);
      //  eventLog("Wizard Cols ID for field = " . $sql_distinct_field . ": " . $wizardColId);
        $newQueryFirst = explode(' AND ('. $wizardColumns[$wizardColId + 1], $query)[0];
     //   eventLog("New QUERY First: " . $newQueryFirst);
        $newQuerySecondAux = explode(" AND organizations REGEXP ", $query)[1];
    //    eventLog("New QUERY Second AUX: " . $newQuerySecondAux);
        $newQuerySecond = " AND organizations REGEXP " . $newQuerySecondAux;
    //    eventLog("New QUERY Second: " . $newQuerySecond);
     //   $queryAlt = $newQueryFirst . $newQuerySecond;
     //   if ($noneSelectedFlagWithGeoFilter == "false") {
      //      $query = $queryAlt;
      //  }
     //   eventLog("Adapted QUERY WIZARD: " . $query);
    }

    //  $query = "SELECT * FROM Dashboard.DashboardWizard";
    
    //   echo ($query);
    
    $rs = mysqli_query($link, $query);
    
    $result = [];
    
    if($rs)
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs))
        {
            array_push($result['table'], $row);
        }
        
        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';

    /*    if ($sql_distinct_field == "high_level_type" && $freezeMap == "true") {
            if (!in_array("POI", $result['table'])) {
                array_push($result['table'], "POI");
              //  sort($result['table']);
            }
        }*/

        echo json_encode($result);
        
    }
    else
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
}


if (!empty($_REQUEST["filterField"]) && !empty($_REQUEST["value"])) {
    
    $stopFlag = 1;
    $sql_filter_field = $_REQUEST['filterField'];
    if ($sql_filter_field != "high_level_type" && $sql_filter_field != "nature" && $sql_filter_field != "sub_nature" && $sql_filter_field != "low_level_type" && $sql_filter_field != "unit" && $sql_filter_field != "unique_name_id" && $sql_filter_field != "healthiness" && $sql_filter_field != "ownership" && $sql_filter_field != "value_unit" && $sql_filter_field != "broker_name" && $sql_filter_field != "value_name" && $sql_filter_field != "value_type") {
      //  eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_filter_field '".$sql_filter_field."' is not an allowed value. Force sql_filter_field = 'high_level_type'");
        eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_filter_field '".$sql_filter_field."' is not an allowed value. Exit from script.");
      //  $sql_filter_field = "high_level_type";
        exit();
    }
    $sql_filter_value = escapeForSQL($_REQUEST['value'], $link);
    $sql_distinct_field = $_GET['filter'];
    if ($sql_distinct_field != "high_level_type" && $sql_distinct_field != "nature" && $sql_distinct_field != "sub_nature" && $sql_distinct_field != "low_level_type" && $sql_distinct_field != "unit" && $sql_distinct_field != "unique_name_id" && $sql_distinct_field != "healthiness" && $sql_distinct_field != "ownership" && $sql_distinct_field != "value_unit" && $sql_distinct_field != "broker_name" && $sql_distinct_field != "value_name" && $sql_distinct_field != "value_type") {
    //    eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_distinct_field '".$sql_distinct_field."' is not an allowed value. Force sql_distinct_field = 'high_level_type'");
        eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_distinct_field '".$sql_distinct_field."' is not an allowed value. Exit from script.");
    //    $sql_distinct_field = "high_level_type";
        exit();
    }
    
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ERROR | E_NOTICE);
    error_reporting(E_ERROR);
    
	$synMode = ""; if(intval($_REQUEST["synMode"])) $synMode .= " AND high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";
	
    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE ".$sql_filter_field." LIKE '".$sql_filter_value."' " .$synMode. " ORDER BY ".$sql_distinct_field." ASC";
    //  $query = "SELECT * FROM Dashboard.DashboardWizard";
    
    //   echo ($query);
    
    
    $rs = mysqli_query($link, $query);
    
    $result = [];
    
    if($rs)
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs))
        {
            array_push($result['table'], $row);
        }
        
        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';
        
        echo json_encode($result);
        
    }
    else
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
}

if (!empty($_REQUEST["filterDistinct"])) {

        $time_start2 = microtime(true);
        $sql_filter = $_GET['filter'];
        if ($sql_filter != "high_level_type" && $sql_filter != "nature" && $sql_filter != "sub_nature" && $sql_filter != "low_level_type" && $sql_filter != "unit" && $sql_filter != "unique_name_id" && $sql_filter != "healthiness" && $sql_filter != "ownership" && $sql_filter != "value_unit" && $sql_filter != "broker_name" && $sql_filter != "value_name" && $sql_filter != "value_type") {
        //    eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_filter '".$sql_filter."' is not an allowed value. Force sql_filter = 'high_level_type'");
            eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_filter '".$sql_filter."' is not an allowed value. Exit from script.");
        //    $sql_filter = "high_level_type";
            exit();
        }
        $org = $_GET['filterOrg'];
        $link = mysqli_connect($host, $username, $password);
      //  $sql_filter = escapeForSQL($sql_filter, $link);     // $link OK
        $org = escapeForSQL($org, $link);
        
        if (strcmp($sql_filter, "High-Level Type") == 0) {
            
            $sql_filter = "high_level_type";
            
        } else if (strcmp($sql_filter, "Nature") == 0) {
            
            $sql_filter = "nature";
            
        } else if (strcmp($sql_filter, "Subnature") == 0) {
            
            $sql_filter = "sub_nature";
            
        } else if (strcmp($sql_filter, "Value Type") == 0) {
            
            $sql_filter = "low_level_type";
            
        } else if (strcmp($sql_filter, "Value Name") == 0) {
            
            $sql_filter = "unique_name_id";
            
        } else if (strcmp($sql_filter, "Instance URI") == 0) {
            
            $sql_filter = "instance_uri";
            
        } else if (strcmp($sql_filter, "Data Type") == 0) {
            
            $sql_filter = "unit";
            
        } else if (strcmp($sql_filter, "Last Date") == 0) {
            
            $sql_filter = "last_date";
            
        } else if (strcmp($sql_filter, "Last Value") == 0) {
            
            $sql_filter = "last_value";
            
        } else if (strcmp($sql_filter, "Healthiness") == 0) {
            
            $sql_filter = "healthiness";
            
        } else if (strcmp($sql_filter, "ownership") == 0) {

            $sql_filter = "ownership";

        } else if (strcmp($sql_filter, "value_unit") == 0) {

            $sql_filter = "value_unit";

        }
        /* else if (strcmp($sql_filter, "Widgets") == 0) {
            
            $sql_filter = "icon1";
            
        } else if (strcmp($sql_filter, "Widget2") == 0) {
            
            $sql_filter = "icon2";
            
        } else if (strcmp($sql_filter, "Widget3") == 0) {
            
            $sql_filter = "icon3";
            
        } else if (strcmp($sql_filter, "Widget4") == 0) {
            
            $sql_filter = "icon4";
            
        } else if (strcmp($sql_filter, "Widget5") == 0) {
            
            $sql_filter = "icon5";
            
        }   */
        
     //   echo ($sql_filter);

        // GESTIONE COMPLETA DELEGHE DA ALTRE ORGANIZATIONS ********************************************

        $dashLoggedUsername = $_SESSION['loggedUsername'];
        $cryptedUsr = encryptOSSL($dashLoggedUsername, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
        $dashUserRole = $_SESSION['loggedRole'];
        $organizationName = $_SESSION['loggedOrganization'];

    /*    $myPOIQueryString = "";

        // RECUPERA IL REFRESH TOKEN PER CHIAMATA API OWNERSHIP E DELEGATION
        if (isset($_SESSION['refreshToken'])) {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;
        }

        $whereAllUsers = "";

        // COSTRUZIONE FILTRI AGGIUNTIVI QUERY PER IOT-APP
        $queryApp = "(SELECT name, appId FROM Dashboard.NodeRedInputs WHERE user = '".$dashLoggedUsername."') UNION (SELECT name, appId FROM Dashboard.NodeRedMetrics WHERE user = '".$dashLoggedUsername."');";
        $rsApp = mysqli_query($link, $queryApp);
        $resultApp = [];
        if($rsApp) {
            while ($rowApp = mysqli_fetch_assoc($rsApp)) {
                $recordSignRows = [];
                if (!is_null($rowApp['appId'])) {
                    if ($rowApp['appId'] != '') {
                        //    $whereAllUsers = $whereAllUsers . " OR parameters = '" . $rowApp['name'] . "'";
                        $whereAllUsers = $whereAllUsers . " OR get_instances = '" . $rowApp['appId'] . "'";
                        array_push($recordSignRows, $rowApp['name']);
                        array_push($recordSignRows, $rowApp['appId']);
                        //   array_push($recordSignRows, null);    // $rowApp['parameters'] ?? Se è utile...
                        array_push($recordSignRows, "Dashboard-IOT App");
                        array_push($iotAppRows, $recordSignRows);
                    }
                }
            }
        }

        // Call Delegation API
        //    $queryDelegated = $personalDataApiBaseUrl . "/v1/username/".$dashLoggedUsername."/delegated?sourceRequest=dashboardwizard&accessToken=valoreFake";
        // ENCODIZZARE username per username con SPAZI !!!
        $queryDelegated = $personalDataApiBaseUrl . "/v1/username/".rawurlencode($dashLoggedUsername)."/delegated?sourceRequest=dashboardwizard&accessToken=" . $accessToken;
        $queryDelegatedResults = file_get_contents($queryDelegated);

        //$file = fopen("C:\dashboardLog.txt", "w");
        //fwrite($file, "queryDelegatedResults: " . $queryDelegatedResults . "\n");

        if(trim($queryDelegatedResults) != "")
        {
            $resDelegatedArray = json_decode($queryDelegatedResults, true);
            foreach($resDelegatedArray as $delegatedRecord)
            {
                if(!in_array($delegatedRecord['elementId'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    if ($delegatedRecord['elementType'] != "DashboardID" && $delegatedRecord['elementType'] != "MyKPI" && $delegatedRecord['elementType'] != "MyPOI" && $delegatedRecord['elementType'] != "MyData") {
                        array_push($delegatedElements, $delegatedRecord);
                        $allowedElementCouples[] = array($delegatedRecord['elementId'], $delegatedRecord['elementType'], $delegatedRecord['variableName'], $delegatedRecord['motivation'], "Delegated");
                        array_push($allowedElementIDs, $delegatedRecord['elementId']);
                    }
                }
            }
        }


        $whereAll = "(organizations LIKE '%" . $organizationName . "%' AND ownership = 'public'";
        //  $whereAll = "(sub_nature = 'sdfsdfsdfsdf'";      // PER DEBUG PRIVATE DATA

        if(trim($queryDelegatedResults) != "")
        {
            // COSTRUZIONE FILTRI AGGIUNTIVI QUERY PER ETL E PERSONAL_DATA
            foreach($allowedElementCouples as $allowedRecord) {
                if ($allowedRecord[1] === 'ServiceGraphID') {
                    $privatePOIrecord = [];
                    $queryServiceTypeFromGraphId = $kbHostUrl . ":8890/sparql?default-graph-uri=&query=SELECT+%3Fa+%3Ftype+WHERE+%7Bgraph+%3C".$allowedRecord[0]."%3E+%7B%3Fs+a+%3Ftype+FILTER%28%3Ftype%21%3Dkm4c%3ARegularService+%26%26+%3Ftype%21%3Dkm4c%3AService+%26%26+%3Ftype%21%3Dkm4c%3ADigitalLocation+%26%26+%3Ftype%21%3Dkm4c%3ATransverseService+%26%26+%3Ftype%21%3Dgtfs%3AStop+%26%26+%3Ftype%21%3Dkm4c%3ARoad%29%7D+optional%7B%3C".$allowedRecord[0]."%3E+km4c%3Aavailability+%3Fa.%7D%7D+group+by+%3Ftype+%3Fa&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
                    $queryServiceTypeResults = file_get_contents($queryServiceTypeFromGraphId);
                    $resServiceTypeArray = json_decode($queryServiceTypeResults, true);
                    if (empty($resServiceTypeArray['results']['bindings'])) {
                        $queryServiceTypeFromGraphId2 = $kbHostUrlAntHel . ":8890/sparql?default-graph-uri=&query=SELECT+%3Fa+%3Ftype+WHERE+%7Bgraph+%3C".$allowedRecord[0]."%3E+%7B%3Fs+a+%3Ftype+FILTER%28%3Ftype%21%3Dkm4c%3ARegularService+%26%26+%3Ftype%21%3Dkm4c%3AService+%26%26+%3Ftype%21%3Dkm4c%3ADigitalLocation+%26%26+%3Ftype%21%3Dkm4c%3ATransverseService+%26%26+%3Ftype%21%3Dgtfs%3AStop+%26%26+%3Ftype%21%3Dkm4c%3ARoad%29%7D+optional%7B%3C".$allowedRecord[0]."%3E+km4c%3Aavailability+%3Fa.%7D%7D+group+by+%3Ftype+%3Fa&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
                        $queryServiceTypeResults2 = file_get_contents($queryServiceTypeFromGraphId2);
                        $resServiceTypeArray = json_decode($queryServiceTypeResults2, true);
                    }
                    foreach($resServiceTypeArray['results']['bindings'] as $serviceTypeRecord) {
                        //    if ($constraint != 'oiunqauhalknsufhvnoqwpnvfv') {
                        $whereAll = $whereAll . " OR sub_nature = '" . explode('http://www.disit.org/km4city/schema#', $serviceTypeRecord['type']['value'])[1] . "'";
                        //    }
                        array_push($privatePOIrecord, $allowedRecord[0]);
                        array_push($privatePOIrecord, $serviceTypeRecord['type']['value']);

                        array_push($privatePOIsGraphId, $privatePOIrecord);
                    }
                }  else if ($allowedRecord[1] === 'ServiceURI') {
                    if (strpos($allowedRecord[0], $allowedRecord[2]) !== false) {
                        // $whereAll = $whereAll . " OR (get_instances = '" . str_replace("/".$allowedRecord[2],'',$allowedRecord[0]) . "' AND low_level_type = '". $allowedRecord[2] ."')";
                        $whereAll = $whereAll . " OR (get_instances = '" . substr($allowedRecord[0], 0, strrpos( $allowedRecord[0], '/')) . "' AND low_level_type = '". $allowedRecord[2] ."')";
                    } else {
                        $whereAll = $whereAll . " OR get_instances = '" . $allowedRecord[0] . "'";
                    }
                } else if ($allowedRecord[1] === 'AppID' OR $allowedRecord[1] === 'AppId') {
                    if (sizeof($allowedRecord) > 3) {   // DELEGATED !!
                        $whereAll = $whereAll . " OR (get_instances = '" . $allowedRecord[0]. "' AND low_level_type = '". $allowedRecord[3] ."')";
                    } else {
                        $whereAll = $whereAll . " OR get_instances = '" . $allowedRecord[0]. "'";
                    }
                } else if ($allowedRecord[1] === 'IOTID') {
                    if (sizeof($allowedRecord) > 3) {   // DELEGATED !!
                        $whereAll = $whereAll . " OR (unique_name_id = '" . $allowedRecord[0]. "' AND low_level_type = '". $allowedRecord[3] ."')";
                    } else {
                        $whereAll = $whereAll . " OR unique_name_id = '" . $allowedRecord[0]. "'";
                    }
                } else if ($allowedRecord[1] === 'MyKPI') {
                    $uniqueNameIdEsc = mysqli_real_escape_string($link, $allowedRecord[2]);
                    if (sizeof($allowedRecord) > 3) {
                        $lowLevelTypeEsc = mysqli_real_escape_string($link, $allowedRecord[3]);
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                } else if ($allowedRecord[1] === 'MyPOI') {
                    $uniqueNameIdEsc = mysqli_real_escape_string($link, $allowedRecord[2]);
                    if (sizeof($allowedRecord) > 3) {
                        $lowLevelTypeEsc = mysqli_real_escape_string($link, $allowedRecord[3]);
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                } else if ($allowedRecord[1] === 'MyData') {
                    $uniqueNameIdEsc = mysqli_real_escape_string($link, $allowedRecord[2]);
                    if (sizeof($allowedRecord) > 3) {
                        $lowLevelTypeEsc = mysqli_real_escape_string($link, $allowedRecord[3]);
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                }
            }
        }

        if ($myPOIQueryString === "") {
            $myPOIQueryString = " AND (high_level_type != 'MyPOI' AND nature != 'Any' AND sub_nature != 'Any')";
        }

        $whereAll = $whereAll.$whereAllUsers . $myPOIQueryString;
        $whereAll = $whereAll.")";
*/
        if ($dashUserRole != "RootAdmin") {
            $whereAllHash = "oldEntry IS NULL AND (organizations LIKE '%" . $organizationName . "%' AND ownership = 'public' OR (ownerHash LIKE '%" . $cryptedUsr . "%' OR delegatedHash LIKE '%" . $cryptedUsr . "%'))";
        } else {
            $whereAllHash = "oldEntry IS NULL";
        }
        $whereAll = $whereAllHash;
		if(intval($_REQUEST["synMode"])) $whereAll .= " AND high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";

        $link = mysqli_connect($host, $username, $password);
        //error_reporting(E_ERROR | E_NOTICE);
        error_reporting(E_ERROR);

    //    $query = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard WHERE organizations REGEXP '". $org ."' ORDER BY ".$sql_filter." ASC";
        $query = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard WHERE " . $whereAll . " ORDER BY ".$sql_filter." ASC";

     //   $queryNEW_KO = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard WHERE " . $whereAll . " ORDER BY ".$sql_filter." ASC";
      //  $query = "SELECT * FROM Dashboard.DashboardWizard";

        // echo ($query);

        
        $rs = mysqli_query($link, $query);

        $result = [];

        if($rs) 
        {
            $result['table'] = [];
            while($row = mysqli_fetch_assoc($rs)) 
            {
                array_push($result['table'], $row); 
            }

            //Eliminiamo i duplicati
			$result = array_unique($result);
            mysqli_close($link);
            $result['detail'] = 'Ok';

            $time_end2 = microtime(true);
            $time2 = $time_end2 - $time_start2;
        //    eventLog("Wizard/Inspector Filtering time for user " . $dashLoggedUsername . ": " . $time2 . " seconds.");

            echo json_encode($result); // echo json_encode($result,JSON_INVALID_UTF8_IGNORE);

        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }
    }
    


if(isset($_REQUEST['getDashboardWizardData'])) 
{
    $stop_flag = 1;
    
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ERROR | E_NOTICE);
    error_reporting(E_ERROR);
    
	$synMode = ""; if(intval($_REQUEST["synMode"])) $synMode .= " WHERE high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";
	$query = "SELECT * FROM Dashboard.DashboardWizard".$synMode;
	
    $rs = mysqli_query($link, $query);

    $result = [];

    if($rs) 
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs)) 
        {
            array_push($result['table'], $row);
        }

        //Eliminiamo i duplicati
        $result = array_unique($result);
        mysqli_close($link);
        $result['detail'] = 'Ok';

    } 
    else 
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    
    echo json_encode($result);
}

if(isset($_REQUEST['getDashboardWizardIcons'])) 
{
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ERROR | E_NOTICE);
    error_reporting(E_ERROR);

    $query = "SELECT * FROM Dashboard.WidgetsIconsMap";
	$rs = mysqli_query($link, $query);

    $result = [];

    if($rs) 
    {
        $result['table'] = [];
        while($row = mysqli_fetch_assoc($rs)) 
        {
            array_push($result['table'], $row);
        }

        mysqli_close($link);
        $result['detail'] = 'Ok';
    } 
    else 
    {
        mysqli_close($link);
        $result['detail'] = 'Ko';
    }
    echo json_encode($result);
}

if(isset($_REQUEST['filterUnitByIcon']))
{
    $sql_unit = $_GET["unit"];
    $stop_flag = 1;
}

if(isset($_REQUEST['updateWizardIcons']))
{
    $link = mysqli_connect($host, $username, $password);
    //error_reporting(E_ERROR | E_NOTICE);
    error_reporting(E_ERROR);
    $sql_field = $_GET["filterField"];
    if ($sql_field != "high_level_type" && $sql_field != "nature" && $sql_field != "sub_nature" && $sql_field != "low_level_type" && $sql_field != "unit" && $sql_field != "unique_name_id" && $sql_field != "healthiness" && $sql_field != "ownership") {
     //   eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_field '".$sql_field."' is not an allowed value. Force sql_field = 'high_level_type'");
        eventLog("Returned the following ERROR in dashboardWizardController.php fo: sql_field '".$sql_field."' is not an allowed value. Exit from script.");
     //   $sql_field = "high_level_type";
        exit();
    }

    $sql_value = escapeForSQL($_GET["filterValue"], $link);
    
	$synMode = ""; if(intval($_REQUEST["synMode"])) $synMode .= " AND high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";
    $query_out = "SELECT DISTINCT unit FROM Dashboard.DashboardWizard WHERE ".$sql_field ." LIKE '".$sql_value."'".$synMode.";";

    $rs_out = mysqli_query($link, $query_out);

    $result_out = [];

    if($rs_out) 
    {
        $result_out['table'] = [];
        $unit_filter = "";
            while($row1 = mysqli_fetch_assoc($rs_out)) 
            {
                array_push($result_out['table'], $row1);
                $unit_filter = "'".$unit_filter."' OR ";
                
            }

         //   mysqli_close($link);
         //   $result['detail'] = 'Ok';
        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }

        $unit_filter = substr($unit_filter,-4);
        $query = "SELECT * FROM Dashboard.WidgetsIconsMap WHERE snap4CityType LIKE '".$unit_filter."';";
		$rs = mysqli_query($link, $query);

        $result = [];

        if($rs) 
        {
            $result['table'] = [];
            while($row = mysqli_fetch_assoc($rs)) 
            {
                array_push($result['table'], $row);
            }

            mysqli_close($link);
            $result['detail'] = 'Ok';
        } 
        else 
        {
            mysqli_close($link);
            $result['detail'] = 'Ko';
        }
        echo json_encode($result);
}

if(isset($_REQUEST["initWidgetWizard"]) || isset($_REQUEST["initSynVarPresel"]) ) {
    
    if( $_REQUEST["initWidgetWizard"] == 'true' || $_REQUEST["initSynVarPresel"] == "true" ) {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * Easy set variables
         */

        $time_start = microtime(true);
        // DB table to use
        $table = 'DashboardWizard';

        // Table's primary key
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array( 'db' => 'high_level_type', 'dt' => 0 ),
            array( 'db' => 'nature',  'dt' => 1 ),
            array( 'db' => 'sub_nature',   'dt' => 2 ),
            array( 'db' => 'low_level_type',     'dt' => 3 ),
            array( 'db' => 'unique_name_id',     'dt' => 4 ),
            array( 'db' => 'instance_uri',     'dt' => 5 ),
            array( 'db' => 'device_model_name',     'dt' => 6 ),
            array( 'db' => 'broker_name',     'dt' => 7 ),
            array( 'db' => 'value_name',     'dt' => 8 ),
            array( 'db' => 'value_type',     'dt' => 9 ),
            array( 'db' => 'unit',     'dt' => 10 ),                 // EX 6
            array( 'db' => 'value_unit',     'dt' => 11 ),           // EX 7
            array(
                'db'        => 'last_date',
                'dt'        => 12,                                   // EX 8
                'formatter' => function( $d, $row ) {
                    if ($d != null) {
                        return date( 'Y-m-d H:i:s', strtotime($d));
                    } else {
                        return null;
                    }
                }
            ),
            array( 'db' => 'last_value',     'dt' => 13 ),           // EX 9
            array( 'db' => 'healthiness',     'dt' => 14 ),         // EX 10
            array( 'db' => 'instance_uri',     'dt' => 15 ),        // EX 11
            array( 'db' => 'parameters',     'dt' => 16 ),          // EX 12
            array( 'db' => 'id',     'dt' => 17 ),                  // EX 13
            array( 'db' => 'lastCheck',     'dt' => 18 ),           // EX 14
            array( 'db' => 'get_instances',     'dt' => 19 ),       // EX 15
            array( 'db' => 'ownership',     'dt' => 20 ),           // EX 16
            array( 'db' => 'organizations',     'dt' => 21 ),       // EX 17
            array( 'db' => 'latitude',     'dt' => 22 ),            // EX 18
            array( 'db' => 'longitude',     'dt' => 23 ),           // EX 19
            array( 'db' => 'sm_based',     'dt' => 24 ),            // EX 20
            array( 'db' => 'ownerHash',     'dt' => 25 ),           // EX 21
            array( 'db' => 'delegatedHash',     'dt' => 26 ),       // EX 22
            array( 'db' => 'oldEntry',     'dt' => 27 )             // EX 23

        );

        // SQL server connection information
        $sql_details = array(
            'user' => $username,
            'pass' => $password,
            'db'   => 'Dashboard',
            'host' => $host
        );
        
        if(isset($_REQUEST['northEastPointLat'])){
            $northEastPointLat=$_REQUEST['northEastPointLat'];
            $northEastPointLng=$_REQUEST['northEastPointLng'];
            $southWestPointLat=$_REQUEST['southWestPointLat'];
            $southWestPointLng=$_REQUEST['southWestPointLng'];
        }


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        require('dashboardWizardControllerSSP.class.php');

        $link = mysqli_connect($host, $username, $password);

        $allowedElements = [];
        $allowedElementCouples = [];
        $allowedElementIDs = [];
        $ownedElements = [];
        $delegatedElements = [];
        $publicElements = [];
        $ownedKpiElements = [];
        $delegatedKpiElements = [];
        $ownedPOIElements = [];
        $delegatedPOIElements = [];
        $ownedMyDataElements = [];
        $delegatedMyDataElements = [];
        $iotAppRows = [];
        $privateDashWizRows = [];
        $privateString = "";
        $privatePOIsGraphId = [];

        $dashLoggedUsername = $_SESSION['loggedUsername'];
        $dashUserRole = $_SESSION['loggedRole'];
        
        $ldapUsername = "cn=" . $_SESSION['loggedUsername'] . "," . $ldapBaseDN;
        $ds = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if($ldapAdminDN)
            $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
        else
            $bind = ldap_bind($ds);
        $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
        if (is_null($organization)) {
            $organization = "Other";
            $organizationName = "Other";
        } else if ($organization == "") {
            $organization = "Other";
            $organizationName = "Other";
        } else {
            $organizationName = $organization;
        }

        $myPOIQueryString = "";

        $cryptedUsr = encryptOSSL($dashLoggedUsername, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
      //  $whereAllHash = "(organizations LIKE '%" . $organizationName . "%' AND ownership = 'public' OR (ownerHash LIKE '%" . $cryptedUsr . "%' OR delegatedHash LIKE '%" . $cryptedUsr . "%'))";
        // NEW QUERY FOR oldEntries
        $whereAllHash = "oldEntry IS NULL AND (organizations LIKE '%" . $organizationName . "%' AND ownership = 'public' OR (ownerHash LIKE '%" . $cryptedUsr . "%' OR delegatedHash LIKE '%" . $cryptedUsr . "%'))";
        $whereAll = $whereAllHash;
		if(intval($_REQUEST["synMode"])) $whereAll .= " AND high_level_type IN ( 'MyKPI', 'Sensor', 'IoT Device Variable', 'Mobile Device Variable', 'Data Table Variable', 'Sensor-Actuator' ) ";
		if($_REQUEST["initSynVarPresel"] == "true") $whereAll .= " AND id IN ( select sel from Dashboard.SynopticVarPresel where usr = '".encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod)."') "; 

        $pageBuffer = [];

        if ($dashUserRole != "RootAdmin") {
            $out = dashboardWizardControllerSSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll);
        } else {
            $out = dashboardWizardControllerSSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
        }
		if($_REQUEST["initSynVarPresel"] == "true") $out["draw"] = 1;

        $time_start1 = microtime(true);
        for($n=0; $n < sizeof($out['data']); $n++) {
          /*  if ($out['data'][$n][0] == "Sensor" && $out['data'][$n][6] != 'sensor_map') {
                // CALL ASCAPI
                $sUri = $out['data'][$n][15];
                $sUriEnc = str_replace('%3A', '%253A', $sUri);
                $url = $kbUrlSuperServiceMap . "?serviceUri=" . $sUriEnc . "&valueName=" . $out['data'][$n][3] . "&format=application%2Fsparql-results%2Bjson&apikey=" . $ssMapAPIKey;
            //    $url = $kbUrlSuperServiceMap . "?serviceUri=" . $sUriEnc . "&format=application%2Fsparql-results%2Bjson&apikey=" . $ssMapAPIKey;
                $context = stream_context_create([
                    "http" => [
                        // http://docs.php.net/manual/en/context.http.php
                        "method"        => "GET",
                        "ignore_errors" => true,
                    ],
                ]);

                $response = file_get_contents($url);
                $status_line = $http_response_header[0];
                preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
                $status = $match[1];
                $responseArray = json_decode($response, true);

                if ($status == "200") {
                    $realtime_data = $responseArray['realtime']['results']['bindings'][0];
                    foreach ($realtime_data as $key => $item) {
                        if ($key != 'measuredTime' && $key != 'updating' && $key != 'instantTime') {
                            // LAST VALUE
                            if ($key == $out['data'][$n][3]) {
                                $out['data'][$n][9] = $realtime_data[$key]['value'];
                            }
                        } else {
                            // LAST DATE
                        //    $out['data'][$n][8] = $realtime_data[$key]['value'];
                            $out['data'][$n][8] = str_replace("T", " ", $realtime_data[$key]['value']);
                            $out['data'][$n][8] = str_replace(".000", " ", $out['data'][$n][8]);
                        }
                    }
                }

            } */
            $privateString = "private";
            if ($out['data'][$n][20] == "private") {
                if (strpos($out['data'][$n][25], $cryptedUsr) !== false) {
                    $privateString = "private (My Own)";
                } else if (strpos($out['data'][$n][26], $cryptedUsr) !== false) {
                    $privateString = "private (Delegated)";
                }
                $out['data'][$n][20] = $privateString;
            }
        }
        $time_end1 = microtime(true);
        $time1 = $time_end1 - $time_start1;
    //    eventLog("Wizard/Inspector AFTER API " . $dashLoggedUsername . ": " . $time1 . " seconds.");

        $out_json = json_encode($out);
        $time_end = microtime(true);
        $time = $time_end - $time_start;
    //    eventLog("Wizard/Inspector loading time for user " . $dashLoggedUsername . ": " . $time . " seconds.");
        echo $out_json;
    }
}
if(isset($_REQUEST["doSynVarPresel"])) {
	try {
		$noautocommit = mysqli_autocommit($link, false);		
		if($noautocommit) $transaction = mysqli_begin_transaction($link);
		if($transaction) $stmt = mysqli_prepare($link, "delete from Dashboard.SynopticVarPresel where usr = ?");
		if($stmt) $bind = mysqli_stmt_bind_param($stmt, "s", encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod));
		if($bind) $exec = mysqli_stmt_execute($stmt);
		if($exec) mysqli_stmt_close($stmt);
		else { mysqli_rollback($link); mysqli_close($link); header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500); die(); }
		foreach( explode(",",$_REQUEST["sel"]) as $sel ) {
			$stmt = mysqli_prepare($link, "insert into Dashboard.SynopticVarPresel(usr,sel) values (?,?)");
			if($stmt) $bind = mysqli_stmt_bind_param($stmt, "si", encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod),$sel);
			if($bind) $exec = mysqli_stmt_execute($stmt);
			if($exec) mysqli_stmt_close($stmt);
			else { mysqli_rollback($link); mysqli_close($link); header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500); die(); }
		}
		mysqli_commit($link); 
		mysqli_close($link);
	}
	catch(Exception $e) {
		mysqli_rollback($link); mysqli_close($link); header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500); die($e);
	}	
}