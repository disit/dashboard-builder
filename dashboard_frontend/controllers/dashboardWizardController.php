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
error_reporting(E_ALL);
//set_error_handler("exception_error_handler");
if (isset($REQUEST["getIcons"])) {
    
    $sql_array = $_REQUEST['sqlData'];
    $stop_flag = 1;
    
}

if (isset($_REQUEST["filterGlobal"])) {
//if (!empty($_REQUEST["filterGlobal"]) && !empty($_REQUEST["value"])) {

    $sql_where = $_REQUEST['filterGlobal'];
  
    $sql_distinct_field = $_REQUEST['distinctField'];
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    if (strpos($sql_where, "AND") == 1) {
        $sql_where_ok = explode("AND ", $sql_where.trim())[1];
    } else {
        $sql_where_ok = $sql_where;
    }
    
    if (empty($_REQUEST["filterGlobal"])) {
        $sql_where_ok = 1;
    }
        
    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE ".$sql_where_ok." ORDER BY ".$sql_distinct_field." ASC;";
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


if (!empty($_REQUEST["filterField"]) && !empty($_REQUEST["value"])) {
    
    $stopFlag = 1;
    $sql_filter_field = $_REQUEST['filterField'];
    $sql_filter_value = $_REQUEST['value'];
    $sql_distinct_field = $_GET['filter'];
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    $query = "SELECT DISTINCT ".$sql_distinct_field." FROM Dashboard.DashboardWizard WHERE ".$sql_filter_field." LIKE '".$sql_filter_value."' ORDER BY ".$sql_distinct_field." ASC";
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

        $sql_filter = $_GET['filter'];
        $org = $_GET['filterOrg'];
        
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
            
        }   /* else if (strcmp($sql_filter, "Widgets") == 0) {
            
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
/*
        // GESTIONE DELEGHE DA ALTRE ORGANIZATIONS  ***************************************************
        $dashLoggedUsername = $_SESSION['loggedUsername'];
        $dashUserRole = $_SESSION['loggedRole'];
        $organizationName = $_SESSION['loggedOrganization'];


        // RECUPERA IL REFRESH TOKEN PER CHIAMATA API OWNERSHIP E DELEGATION
        if (isset($_SESSION['refreshToken'])) {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;
        }

        $queryDelegated = $personalDataApiBaseUrl . "/v1/username/".$dashLoggedUsername."/delegated?sourceRequest=dashboardwizard&accessToken=" . $accessToken;
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
                        $whereAll = $whereAll . " OR (get_instances = '" . str_replace("/".$allowedRecord[2],'',$allowedRecord[0]) . "' AND low_level_type = '". $allowedRecord[2] ."')";
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
                    if (sizeof($allowedRecord) > 3) {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND unique_name_id = '" . $allowedRecord[2] . "' AND low_level_type = '" . $allowedRecord[3] . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND unique_name_id = '" . $allowedRecord[2] . "'";
                    }
                } else if ($allowedRecord[1] === 'MyPOI') {
                    if (sizeof($allowedRecord) > 3) {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND unique_name_id = '" . $allowedRecord[2] . "' AND low_level_type = '" . $allowedRecord[3] . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND unique_name_id = '" . $allowedRecord[2] . "'";
                    }
                } else if ($allowedRecord[1] === 'MyData') {
                    if (sizeof($allowedRecord) > 3) {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND unique_name_id = '" . $allowedRecord[2] . "' AND low_level_type = '" . $allowedRecord[3] . "')";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND unique_name_id = '" . $allowedRecord[2] . "'";
                    }
                }
            }
        }

        if ($myPOIQueryString === "") {
            $myPOIQueryString = " AND (high_level_type != 'MyPOI' AND nature != 'Any' AND sub_nature != 'Any')";
        }

        $whereAll = $whereAll.$whereAllUsers . $myPOIQueryString;
        $whereAll = $whereAll.")";

        // FINE GESTIONE DELEGHE DA ALTRE ORGANIZATIONS  ***********************************************
        */



        // GESTIONE COMPLETA DELEGHE DA ALTRE ORGANIZATIONS ********************************************

        $dashLoggedUsername = $_SESSION['loggedUsername'];
        $dashUserRole = $_SESSION['loggedRole'];
        $organizationName = $_SESSION['loggedOrganization'];

        $myPOIQueryString = "";

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
                        $whereAll = $whereAll . " OR (get_instances = '" . str_replace("/".$allowedRecord[2],'',$allowedRecord[0]) . "' AND low_level_type = '". $allowedRecord[2] ."')";
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



        $link = mysqli_connect($host, $username, $password);
        error_reporting(E_ERROR | E_NOTICE);

        $query = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard WHERE organizations REGEXP '". $org ."' ORDER BY ".$sql_filter." ASC";
     //   $queryNEW_KO = "SELECT DISTINCT ".$sql_filter." FROM Dashboard.DashboardWizard WHERE " . $whereAll . " ORDER BY ".$sql_filter." ASC";
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
    


if(isset($_REQUEST['getDashboardWizardData'])) 
{
    $stop_flag = 1;
    
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);
    
    $query = "SELECT * FROM Dashboard.DashboardWizard";
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

if(isset($_REQUEST['getDashboardWizardDataFiltered']))  {
    
    if (!empty($_REQUEST["filter"])) {
        session_start();
        $sql_filter = $_REQUEST["filter"];
        
     //   echo ($sql_filter);
        
        $link = mysqli_connect($host, $username, $password);
        error_reporting(E_ERROR | E_NOTICE);

        $query = "SELECT * FROM Dashboard.DashboardWizard WHERE ".$sql_filter;
        
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
    } else {
        
        
    }
    
    echo json_encode($result);
    $flag = 1;
    
}

if(isset($_REQUEST['getDashboardWizardIcons'])) 
{
    $link = mysqli_connect($host, $username, $password);
    error_reporting(E_ERROR | E_NOTICE);

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
    error_reporting(E_ERROR | E_NOTICE);
    $sql_field = $_GET["filterField"];
    $sql_value = $_GET["filterValue"];
    
    $query_out = "SELECT DISTINCT unit FROM Dashboard.DashboardWizard WHERE ".$sql_field ." LIKE '".$sql_value."';";

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

if(isset($_REQUEST["initWidgetWizard"])) {
    
    if(($_REQUEST["initWidgetWizard"]) == 'true') {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * Easy set variables
         */

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
            array( 'db' => 'unit',     'dt' => 6 ),
            array(
                'db'        => 'last_date',
                'dt'        => 7,
                'formatter' => function( $d, $row ) {
                    if ($d != null) {
                        return date( 'Y-m-d H:i:s', strtotime($d));
                    } else {
                        return null;
                    }
                }
            ),
            array( 'db' => 'last_value',     'dt' => 8 ),
            array( 'db' => 'healthiness',     'dt' => 9 ),
            array( 'db' => 'instance_uri',     'dt' => 10 ),
            array( 'db' => 'parameters',     'dt' => 11 ),
            array( 'db' => 'id',     'dt' => 12 ),
            array( 'db' => 'lastCheck',     'dt' => 13 ),
            array( 'db' => 'get_instances',     'dt' => 14 ),
            array( 'db' => 'ownership',     'dt' => 15 ),
            array( 'db' => 'sm_based',     'dt' => 16 ),
            array( 'db' => 'organizations',     'dt' => 17 ),
            array( 'db' => 'latitude',     'dt' => 18 ),
            array( 'db' => 'longitude',     'dt' => 19 )

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

        $dashLoggedUsername = $_GET['dashUsername'];
        $dashUserRole = $_GET['dashUserRole'];
        
        $ldapUsername = "cn=" . $_SESSION['loggedUsername'] . "," . $ldapBaseDN;
        $ds = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
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
    /*    $orgParamsQuery = "SELECT * FROM Dashboard.Organizations WHERE organizationName = '$organizationName'";
        $r = mysqli_query($link, $orgParamsQuery);

        if($r)
        {
            if($row = mysqli_fetch_assoc($r))
            {
                $orgId = $row['id'];
                $organizationName = $row['organizationName'];
                $orgKbUrl = $row['kbUrl'];
                $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
                $orgZoomLevel = $row['zoomLevel'];
                $response['orgId'] = $orgId;
                $response['orgName'] = $orgName;
                $response['orgKbUrl'] = $orgKbUrl;
                $response['orgGpsCentreLatLng'] = $orgGpsCentreLatLng;
                $response['orgZoomLevel'] = $orgZoomLevel;
                $response['detail'] = 'GetOrganizationParameterOK';
            } else {
                $organizationName = null;
            }
        } else {
            $organizationName = null;
        }*/

        // RECUPERA IL REFRESH TOKEN PER CHIAMATA API OWNERSHIP E DELEGATION
        if (isset($_SESSION['refreshToken'])) {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;
        }
        session_write_close();

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

        // $privateDashWizRows NON USATO, SOLO PER REFERENCE !!
    /*    $queryPriv = "(SELECT * FROM Dashboard.DashboardWizard WHERE ownership = 'private');";
        $rsPriv = mysqli_query($link, $queryPriv);
        $resultPriv = [];
        if($rsPriv) {
            while ($rowPriv = mysqli_fetch_assoc($rsPriv)) {
                $recordPriv = [];
                array_push($recordPriv, $rowPriv['parameters']);
                array_push($recordPriv, null);
                    // $rowApp['parameters'] ?? Se è utile...
                array_push($recordPriv, $rowPriv['high_level_type']);
                array_push($privateDashWizRows, $recordPriv);
            }
        }   */

        // Call Onwership API
    //    $queryOwnership = $ownershipApiBaseUrl . "/v1/list?username=".$dashLoggedUsername;
        $queryOwnership = $ownershipApiBaseUrl . "/v1/list/?accessToken=".$accessToken;
        $queryOwnershipResults = file_get_contents($queryOwnership);
        $resOwnershipArray = json_decode($queryOwnershipResults, true);
        foreach($resOwnershipArray as $ownershipRecord) {
          //  $stopFlag = 1;
         //   $allowedElements[] = $ownershipRecord;
            if ($ownershipRecord['elementType'] != "DashboardID" && $ownershipRecord['elementType'] != "MyKPI" && $ownershipRecord['elementType'] != "MyPOI" && $ownershipRecord['elementType'] != "MyData") {
                array_push($ownedElements, $ownershipRecord);
                if ($ownershipRecord['username'] == $dashLoggedUsername) {
                    $allowedElementCouples[] = array($ownershipRecord['elementId'], $ownershipRecord['elementType'], "MyOwn");
                } else {
                    $allowedElementCouples[] = array($ownershipRecord['elementId'], $ownershipRecord['elementType'], "");
                }
                array_push($allowedElementIDs, $ownershipRecord['elementId']);
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


        // NEW DELEGATED ANONYMOUS PER ORG !
        if (isset($_SESSION["loggedOrganization"])) {
            $ldapBaseDnOrg = "ou=". $_SESSION["loggedOrganization"] .",dc=foo,dc=example,dc=org";
        } else {
            $ldapBaseDnOrg = "ou=Other,dc=foo,dc=example,dc=org";
        }

        if (isset($_SESSION['loggedOrganization'])) {
          //  $apiUrl = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
      //  } else {
            $ldapBaseDnOrgEncoded = urlencode($ldapBaseDnOrg);
            $apiUrl = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&groupname=" . $ldapBaseDnOrgEncoded;
        } else {
            $apiUrl = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
        }

        // PRODUZIONE
        //      $apiUrlNewProd= $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegated?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager&groupname=" .  urlencode($ldapBaseDnOrg);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'GET',
                'timeout' => 30,
                'ignore_errors' => true
            )
        );

        $context  = stream_context_create($options);
        $queryPublicResults = file_get_contents($apiUrl, false, $context);
        $queryPublicResultsArray = json_decode($queryPublicResults);
        if(trim($queryPublicResults) != "")
        {
            $resPublicArray = json_decode($queryPublicResults, true);
            foreach($resPublicArray as $publicRecord)
            {
                if(!in_array($publicRecord['elementId'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($publicElements, $publicRecord);
                    $allowedElementCouples[] = array($publicRecord['elementId'], $publicRecord['elementType'], $publicRecord['variableName'], $publicRecord['motivation'], "Delegated");
                    array_push($allowedElementIDs, $publicRecord['elementId']);
                }
            }
        }


        // Call MyKPI API
        $queryMyKPI = $personalDataApiBaseUrl . "/v1/kpidata/?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyKPI";
        $queryMyKPIResults = file_get_contents($queryMyKPI);
        if(trim($queryMyKPIResults) != "")
        {
            $myKpiArray = json_decode($queryMyKPIResults, true);
            foreach($myKpiArray as $myKpiRecord)
            {
                if(!in_array((string)$myKpiRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($ownedKpiElements, $myKpiRecord);
                    $allowedElementCouples[] = array($myKpiRecord['id'], $myKpiRecord['highLevelType'], $myKpiRecord['valueName'], $myKpiRecord['valueType'], "My Own");
                    array_push($allowedElementIDs, $myKpiRecord['id']);
                }
            }
        }

        // Call MyKPI Delegated API
        $queryMyKPIDelegated = $personalDataApiBaseUrl . "/v1/kpidata/delegated?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyKPI";
        $queryMyKPIDelegatedResults = file_get_contents($queryMyKPIDelegated);
        if(trim($queryMyKPIDelegatedResults) != "")
        {
            $myKpiDelegatedArray = json_decode($queryMyKPIDelegatedResults, true);
            foreach($myKpiDelegatedArray as $myKpiDelegatedRecord)
            {
                if(!in_array($myKpiDelegatedRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($delegatedKpiElements, $myKpiDelegatedRecord);
                    $allowedElementCouples[] = array($myKpiDelegatedRecord['id'], $myKpiDelegatedRecord['highLevelType'], $myKpiDelegatedRecord['valueName'], $myKpiDelegatedRecord['valueType'], "Delegated");
                    array_push($allowedElementIDs, $myKpiDelegatedRecord['id']);
                }
            }
        }


        // Call MyPOI API
        $queryMyPOI = $personalDataApiBaseUrl . "/v1/poidata/?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyPOI";
        $queryMyPOIResults = file_get_contents($queryMyPOI);
        if(trim($queryMyPOIResults) != "")
        {
            $myPOIArray = json_decode($queryMyPOIResults, true);
            foreach($myPOIArray as $myPOIRecord)
            {
                if ($myPOIQueryString === "") {
                    $myPOIQueryString = " OR (high_level_type = 'MyPOI' AND nature = 'Any' AND sub_nature = 'Any')";
                }

                if(!in_array((string)$myPOIRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($ownedPOIElements, $myPOIRecord);
                    $allowedElementCouples[] = array($myPOIRecord['properties']['kpidata']['id'], $myPOIRecord['properties']['kpidata']['highLevelType'], $myPOIRecord['properties']['kpidata']['valueName'], $myPOIRecord['properties']['kpidata']['valueType'], "My Own");
                    array_push($allowedElementIDs, $myPOIRecord['id']);
                }
            }
        }

        // Call MyPOI Delegated API
        $queryMyPOIDelegated = $personalDataApiBaseUrl . "/v1/poidata/delegated?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyPOI";
        $queryMyPOIDelegatedResults = file_get_contents($queryMyPOIDelegated);
        if(trim($queryMyPOIDelegatedResults) != "")
        {
            $myPOIDelegatedArray = json_decode($queryMyPOIDelegatedResults, true);
            foreach($myPOIDelegatedArray as $myPOIDelegatedRecord)
            {
                if ($myPOIQueryString === "") {
                    $myPOIQueryString = " OR (high_level_type = 'MyPOI' AND nature = 'Any' AND sub_nature = 'Any')";
                }
                if(!in_array((string)$myPOIDelegatedRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($delegatedPOIElements, $myPOIDelegatedRecord);
                    $allowedElementCouples[] = array($myPOIDelegatedRecord['properties']['kpidata']['id'], $myPOIDelegatedRecord['properties']['kpidata']['highLevelType'], $myPOIDelegatedRecord['properties']['kpidata']['valueName'], $myPOIDelegatedRecord['properties']['kpidata']['valueType'], "Delegated");
                    array_push($allowedElementIDs, $myPOIDelegatedRecord['id']);
                }
            }
        }


        // Call MyData API
        $queryMyData = $personalDataApiBaseUrl . "/v1/kpidata/?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyData";
        $queryMyDataResults = file_get_contents($queryMyData);
        if(trim($queryMyDataResults) != "")
        {
            $myDataArray = json_decode($queryMyDataResults, true);
            foreach($myDataArray as $myDataRecord)
            {
                if(!in_array((string)$myDataRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($ownedMyDataElements, $myDataRecord);
                    $allowedElementCouples[] = array($myDataRecord['id'], $myDataRecord['highLevelType'], $myDataRecord['valueName'], $myDataRecord['valueType'], "My Own");
                    array_push($allowedElementIDs, $myDataRecord['id']);
                }
            }
        }

        // Call MyData Delegated API
        $queryMyDataDelegated = $personalDataApiBaseUrl . "/v1/kpidata/delegated?sourceRequest=dashboardwizard&accessToken=" . $accessToken . "&highLevelType=MyData";
        $queryMyDataDelegatedResults = file_get_contents($queryMyDataDelegated);
        if(trim($queryMyDataDelegatedResults) != "")
        {
            $myDataDelegatedArray = json_decode($queryMyDataDelegatedResults, true);
            foreach($myDataDelegatedArray as $myDataDelegatedRecord)
            {
                if(!in_array($myDataDelegatedRecord['id'], $allowedElementIDs, true)) {
                    //   $allowedElements[] = $delegatedRecord;
                    array_push($delegatedMyDataElements, $myDataDelegatedRecord);
                    $allowedElementCouples[] = array($myDataDelegatedRecord['id'], $myDataDelegatedRecord['highLevelType'], $myDataDelegatedRecord['valueName'], $myDataDelegatedRecord['valueType'], "Delegated");
                    array_push($allowedElementIDs, $myDataDelegatedRecord['id']);
                }
            }
        }


        $whereAll = "(organizations LIKE '%" . $organizationName . "%' AND ownership = 'public'";
      //  $whereAll = "(sub_nature = 'sdfsdfsdfsdf'";      // PER DEBUG PRIVATE DATA

     //   if(trim($queryDelegatedResults) != "")
     //   {
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
                        $whereAll = $whereAll . " OR (get_instances = '" . str_replace("/".$allowedRecord[2],'',$allowedRecord[0]) . "' AND low_level_type = '". $allowedRecord[2] ."')";
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
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "' AND (get_instances = '" . $allowedRecord[0] . "' OR get_instances = 'datamanager/api/v1/poidata/" . $allowedRecord[0] . "'))";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyKPI' AND BINARY  unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                } else if ($allowedRecord[1] === 'MyPOI') {
                    $uniqueNameIdEsc = mysqli_real_escape_string($link, $allowedRecord[2]);
                    if (sizeof($allowedRecord) > 3) {
                        $lowLevelTypeEsc = mysqli_real_escape_string($link, $allowedRecord[3]);
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "' AND (get_instances = '" . $allowedRecord[0] . "' OR get_instances = 'datamanager/api/v1/poidata/" . $allowedRecord[0] . "'))";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyPOI' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                } else if ($allowedRecord[1] === 'MyData') {
                    $uniqueNameIdEsc = mysqli_real_escape_string($link, $allowedRecord[2]);
                    if (sizeof($allowedRecord) > 3) {
                        $lowLevelTypeEsc = mysqli_real_escape_string($link, $allowedRecord[3]);
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "' AND BINARY low_level_type = '" . $lowLevelTypeEsc . "' AND (get_instances = '" . $allowedRecord[0] . "' OR get_instances = 'datamanager/api/v1/poidata/" . $allowedRecord[0] . "'))";
                    } else {
                        $whereAll = $whereAll . " OR (high_level_type = 'MyData' AND BINARY unique_name_id = '" . $uniqueNameIdEsc . "'";
                    }
                }
            }
    //    }
        /*else
        {
             fwrite($file, "Risposta vuota 2\n");
        }*/

        if ($myPOIQueryString === "") {
            $myPOIQueryString = " AND (high_level_type != 'MyPOI' AND nature != 'Any' AND sub_nature != 'Any')";
        }

        $whereAll = $whereAll.$whereAllUsers . $myPOIQueryString;
        $whereAll = $whereAll.")";

        $pageBuffer = [];

        if ($dashUserRole != "RootAdmin") {
            $out = dashboardWizardControllerSSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $whereAll);
        } else {
            $out = dashboardWizardControllerSSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
        }



        for($n=0; $n < sizeof($out['data']); $n++) {
            if ($out['data'][$n][15] == "private") {
                $privateString = "";
                if ($out['data'][$n][0] == "Dashboard-IOT App") {      // IOT-App *****************
                    if ($dashUserRole != "RootAdmin") {
                        $privateString = "private (My Own)";
                        $out['data'][$n][15] = $privateString;
                    } else {
                        foreach ($ownedElements as $ownedElement) {
                            if ($out['data'][$n][14] == $ownedElement['elementId']) {
                                if ($ownedElement['username'] == $dashLoggedUsername) {
                                    $privateString = "private (My Own)";
                                } else {
                                    $privateString = "private";
                                }
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }
                    /*    foreach ($delegatedElements as $delegatedElement) {
                            if ($out['data'][$n][14] == $delegatedElement['elementId']) {
                                $privateString = "private (Delegated)";
                                    $out['data'][$n][15] = $privateString;
                            }
                        }       */
                } else if ($out['data'][$n][0] == "POI") {                                  // POI ********************
                    foreach ($ownedElements as $ownedElement) {
                        if ($ownedElement['elementType'] == "ServiceGraphID") {
                            foreach ($privatePOIsGraphId as $privatePOIel) {
                                if ($ownedElement['elementId'] == $privatePOIel[0]) {
                                    if ($ownedElement['username'] == $dashLoggedUsername) {
                                        $privateString = "private (My Own)";
                                    } else {
                                        $privateString = "private";
                                    }
                                    $out['data'][$n][15] = $privateString;
                                }
                            }
                        }
                    }
                    //    if ($privateString == '') {
                    foreach ($delegatedElements as $delegatedElement) {
                        /* if ($out['data'][$n][11] == $delegatedElement['elementId']) {
                             $privateString = "private (Delegated)";
                             $out['data'][$n][15] = $privateString;
                         }   */
                        foreach ($privatePOIsGraphId as $privatePOIel) {
                            if ($delegatedElement['elementId'] == $privatePOIel[0]) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }
                    //    }
                } else if ($out['data'][$n][0] == "My Personal Data") {                         // Personal Data ********************
                    $personalDataPrivateMatch = false;
                    foreach ($ownedElements as $ownedElement) {
                        if ($out['data'][$n][14] == $ownedElement['elementId']) {
                            if ($ownedElement['username'] == $dashLoggedUsername) {
                                $privateString = "private (My Own)";
                                $personalDataPrivateMatch = true;
                            } else {
                                $privateString = "private";
                            }
                            $out['data'][$n][15] = $privateString;
                        }
                    }
                    //    if ($private != '') {
                    if ($personalDataPrivateMatch != true) {
                        foreach ($delegatedElements as $delegatedElement) {
                            if ($out['data'][$n][14] == $delegatedElement['elementId'] && $out['data'][$n][3] == $delegatedElement['motivation'] && $out['data'][$n][4] == $delegatedElement['variableName']) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                                $personalDataPrivateMatch = true;
                            }
                        }
                    }

                    if ($personalDataPrivateMatch != true) {
                        $privateString = "private (Delegated)";
                        $out['data'][$n][15] = $privateString;
                        $personalDataPrivateMatch = true;
                    }

                 /*   if ($personalDataPrivateMatch != true) {
                        foreach ($publicElements as $publicElement) {
                            if ($out['data'][$n][14] == $publicElement['elementId'] && $out['data'][$n][3] == $publicElement['motivation'] && $out['data'][$n][4] == $publicElement['variableName']) {
                                $privateString = "public";
                                $out['data'][$n][15] = $privateString;
                                $personalDataPrivateMatch = true;
                            }
                        }
                    }*/

                } else if ($out['data'][$n][0] == "Sensor" || $out['data'][$n][0] == "Sensor-Actuator") {                                  // Sensor ********************
                    if ($dashUserRole != "RootAdmin") {
                        $privateString = "private (My Own)";
                        $out['data'][$n][15] = $privateString;
                    } else {
                        foreach ($ownedElements as $ownedElement) {
                            //  if ($ownedElement['elementType'] == "ServiceUri") {
                            if ($out['data'][$n][14] == $ownedElement['elementId'] || $out['data'][$n][4] == $ownedElement['elementId']) {
                                if ($ownedElement['username'] == $dashLoggedUsername) {
                                    $privateString = "private (My Own)";
                                } else {
                                    $privateString = "private";
                                };
                                $out['data'][$n][15] = $privateString;
                            }
                            //  }
                        }
                    }
                    //    if ($privateString == '') {
                    foreach ($delegatedElements as $delegatedElement) {
                        if ($delegatedElement['elementType'] == "ServiceURI") {
                            if ($out['data'][$n][14] == str_replace("/" . $allowedRecord[2], '', $allowedRecord[0])) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        } else {
                            if ($out['data'][$n][14] == $delegatedElement['elementId'] || $out['data'][$n][4] == $delegatedElement['elementId']) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }
                    //    }
                } else if ($out['data'][$n][0] == "MyKPI") {            // MyKPI ***********************************************************

                    $kpiPrivateMatch = false;
                    foreach ($ownedKpiElements as $ownedKpiElement) {
                        if ($out['data'][$n][4] == $ownedKpiElement['valueName']) {
                            if ($ownedKpiElement['username'] == $dashLoggedUsername) {
                                $privateString = "private (My Own)";
                                $kpiPrivateMatch = true;
                            } else {
                                $privateString = "private";
                            }
                            $out['data'][$n][15] = $privateString;
                        }
                    }

                    if ($kpiPrivateMatch != true) {
                        foreach ($delegatedKpiElements as $delegatedKpiElement) {
                            if ($out['data'][$n][4] == $delegatedKpiElement['valueName']) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }

                }
                else if ($out['data'][$n][0] == "MyPOI") {            // MyPOI ***********************************************************

                    $POIPrivateMatch = false;
                    foreach ($ownedPOIElements as $ownedPOIElement) {
                        if ($out['data'][$n][4] == $ownedPOIElement['properties']['kpidata']['valueName']) {
                            if ($ownedPOIElement['properties']['kpidata']['username'] == $dashLoggedUsername) {
                                $privateString = "private (My Own)";
                                $POIPrivateMatch = true;
                            } else {
                                $privateString = "private";
                            }
                            $out['data'][$n][15] = $privateString;
                        }
                    }

                    if ($POIPrivateMatch != true) {
                        foreach ($delegatedPOIElements as $delegatedPOIElement) {
                            if ($out['data'][$n][4] == $delegatedPOIElement['properties']['kpidata']['valueName']) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }

                }
                else if ($out['data'][$n][0] == "MyData") {            // MyData ***********************************************************

                    $myDataPrivateMatch = false;
                    foreach ($ownedMyDataElements as $ownedMyDataElement) {
                        if ($out['data'][$n][4] == $ownedMyDataElement['valueName']) {
                            if ($ownedMyDataElement['username'] == $dashLoggedUsername) {
                                $privateString = "private (My Own)";
                                $myDataPrivateMatch = true;
                            } else {
                                $privateString = "private";
                            }
                            $out['data'][$n][15] = $privateString;
                        }
                    }

                    if ($myDataPrivateMatch != true) {
                        foreach ($delegatedMyDataElements as $delegatedMyDataElement) {
                            if ($out['data'][$n][4] == $delegatedMyDataElement['valueName']) {
                                $privateString = "private (Delegated)";
                                $out['data'][$n][15] = $privateString;
                            }
                        }
                    }

                }
                else {
                 //   $stop_flag = 1;
                }

            }
        }
        $out['allowed_elements'] = $allowedElementCouples;
        $out_json = json_encode($out);
        echo $out_json;
    }
}