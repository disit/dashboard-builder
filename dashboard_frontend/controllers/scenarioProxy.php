<?php
/*
 * For documentation see:
 * MyPersonalData API: https://www.km4city.org/swagger/internal/index.html?urls.primaryName=Data%20Manager%20API
 * Section 'From Username'
 */

// save data into db for the username (profiledb.data table)
function postMyPersonalData($baseUrl, $username, $sourceRequest, $accessToken, $motivation, $variableValue, $scenarioName, $variableName) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/data?sourceRequest=".$sourceRequest
            ."&accessToken=".$accessToken;

	$dataTime = round(microtime(true)*1000);
	$dataTimeEnd = null;
	try { $dataTime = strtotime((json_decode($variableValue,true)["scenarioDatetimeStart"]).":00 ".date_default_timezone_get())*1000; } catch(Exception $e) { }
	try { $dataTimeEnd = strtotime((json_decode($variableValue,true)["scenarioDatetimeEnd"]).":00 ".date_default_timezone_get())*1000; } catch(Exception $e) {  }
	
    $bodyData = [
        "username" => $username,
        "dataTime" => $dataTime,
		"dataTimeEnd" => $dataTimeEnd,
        "motivation" => $motivation,
        "variableName" => $variableName,
        "elementId" => $scenarioName,
        "APPID" => $scenarioName,
        "variableValue" => $variableValue,
        "variableUnit" => "json"
    ];

    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'timeout' => 30,
            'ignore_errors' => true,
            'content' => json_encode($bodyData)
        )
    );
    
    $context = stream_context_create($options);
    
    $respJson = file_get_contents($apiUrl, false, $context);

    return $respJson;
}

// get data from db for the username (profiledb.data table)
function getMyPersonalData($baseUrl, $username, $sourceRequest, $accessToken, $motivation) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/data?sourceRequest=".$sourceRequest
                ."&accessToken=".$accessToken."&motivation=".$motivation;
        
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    
    $context = stream_context_create($options);
    
    $respJson = file_get_contents($apiUrl, false, $context);

    return $respJson;
}

// Creates an anonymous delegations associated to the specified (scenario) id (
function postAnonymousDelegation($baseUrl, $username, $sourceRequest, $accessToken, $variableName, $scenarioName, $motivation) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/delegation?sourceRequest="
            .$sourceRequest."&accessToken=".$accessToken;

    $bodyData = [
        "usernameDelegated" => "ANONYMOUS", 
        "elementId" => $scenarioName, 
        "elementType" => "AppID",
        "variableName" => $variableName,
        "motivation" => $motivation
    ];

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'timeout' => 30,
            'ignore_errors' => true,
            'content' => json_encode($bodyData)
        )
    );
        
    $context = stream_context_create($options);
    
    $respJson = file_get_contents($apiUrl, false, $context);
    
    return $respJson;
}

// Get anonymous delegation's full data
function getAnonymousData($baseUrl, $username, $sourceRequest, $accessToken, $variableName) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/data?anonymous=true&delegated=true"."&accessToken=".$accessToken
            ."&sourceRequest=".$sourceRequest."&variableName=".$variableName;
        
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    
    $context = stream_context_create($options);
    
    $respJson = file_get_contents($apiUrl, false, $context);

    return $respJson;
}

// PHP header
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start();
checkSession('Public');
// $link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
$link = mysqli_connect($sql_host_pd, $usrDb, $pwdDb) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);

// Personal Data used params
$dmUsername = $_SESSION['loggedUsername'];
$dmSourceRequest = "widgetscenario";
$dmMotivation = "scenario";
$variableName = "scenario_id";

// user not logged in
if(!isset($_SESSION['refreshToken'])) {
    // Call used to save a new scenario
    if($_GET['method'] === "POST") {
        echo "-2";
    }
    // Call used to populate (public) scenarios' select widget
    else if($_GET['method'] === "GET" && $_GET['opt'] === "name") {
        // Get anonymous delegations for scenarios type (elementType)
        $query = "SELECT element_id as elementId from profiledb.delegation where variable_name='$variableName'";
        $result = mysqli_query($link, $query);
        /*$delegations = getAnonymousDelegation($personalDataApiBaseUrl, $dmSourceRequest, $elementType , $accessToken);
        $delegations = json_decode($delegations);*/
        
        $outArray = array();
        $tmpArray = array();
        if($result) {
            while($d = mysqli_fetch_assoc($result)) {
                $curScenarioName = $d['elementId'];
                $tmpArray['name'] = $curScenarioName;
                $outArray[] = $tmpArray;
            }
        }
        echo json_encode($outArray);
    }
    
    // when the user chooses a scenario, call this method to get scenario's features and draw them on the map
    else if($_GET['method'] === "GET" && isset($_GET['sel'])) {
        $selectedScenario = $_GET['sel'];
       
        $query = "SELECT variable_value as variableValue from profiledb.data where variable_name='$variableName' and app_id='$selectedScenario'";
        $result = mysqli_query($link, $query);
        
        if($result) {
            $feature = mysqli_fetch_assoc($result);
            echo $feature['variableValue'];
        }   
    }
}

else if(isset($_SESSION['refreshToken'])) {
    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
    $accessToken = $tkn->access_token;
    $_SESSION['refreshToken'] = $tkn->refresh_token;

    $genFileContent = parse_ini_file("../conf/environment.ini");
    $ownershipFileContent = parse_ini_file("../conf/ownership.ini");
    $env = $genFileContent['environment']['value'];

    $personalDataApiBaseUrl = $ownershipFileContent["personalDataApiBaseUrl"][$env];
    
    // Call used to save a new scenario
    if($_GET['method'] === "POST") {
        $dmVariableValue = $_GET['geojson'];
        $newName = json_decode($dmVariableValue, true)['scenarioName'];
        $isPublic = json_decode($dmVariableValue, true)['isPublic'];
        
        // before insert data, we get if the scenarioName already exists (among scenarios -> use of variable_name)
        $query = "SELECT count(*) as count from profiledb.data where variable_name='$variableName' and app_id='$newName'";
        $result = mysqli_query($link, $query);
        $feature = mysqli_fetch_assoc($result);
        if($feature['count'] == 0 ) {
            // 1: save scenario's data
            $postResp = postMyPersonalData($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $dmMotivation, $dmVariableValue, $newName, $variableName);
            // if the created scenario is public, we also need to create an anonymous delegation for this scenarioId
            if($isPublic == "true") {
                // 2: if the scenario is public, save delegation to anonymous
                //$createdScenarioId = json_decode($postResp)->id;
                postAnonymousDelegation($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $variableName, $newName, $dmMotivation);
            }
            echo "1";
        }
        else        // name already exists
            echo "-1";
    }
    
    // Call used to populate scenarios' select widget
    else if($_GET['method'] === "GET" && $_GET['opt'] === "name") {
        // root admin can view all scenarios
        if($_SESSION['loggedRole'] === "RootAdmin") {
            $getData = getMyPersonalData($personalDataApiBaseUrl, "ANY", $dmSourceRequest, $accessToken, $dmMotivation);
        
            $getDataJson = json_decode($getData, true);

            $outArray = array();
            $tmpArray = array();
            foreach ($getDataJson as $feature) {
                $tmpArray['name'] = $feature['APPID'];
                $tmpArray['variable_value'] = json_decode($feature['variableValue'], true); //added to sort scenario list
                if( json_decode($feature['variableValue'], true)['isPublic'] == "true" ) {
                    $tmpArray['name'] .= " (Public)";
                } else if($feature['username'] === $_SESSION['loggedUsername']) {
                    $tmpArray['name'] .= " (My Own)";
                } else {
                    $tmpArray['name'] .= " (" . $feature['username'] .")";
                }
                $outArray[] = $tmpArray;
            }
            echo json_encode($outArray);
        }
        // normal user can view both created and public scenarios
        else {
            // get scenarios created by the user
            $getData = getMyPersonalData($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $dmMotivation);
            $getDataJson = json_decode($getData, true);
            
            $outArray = array();
            $tmpArray = array();
            foreach ($getDataJson as $feature) {
                $tmpArray['name'] = $feature['APPID'];
//                $tmpArray['variable_value'] = json_decode($feature['variableValue'], true); //added to sort scenario list

                if( json_decode($feature['variableValue'], true)['isPublic'] == "true" )
                        $tmpArray['name'] .= " (Public)";
                else
                    $tmpArray['name'] .= " (My Own)";
                
                $outArray[] = $tmpArray;
            }

            // Get anonymous delegations for scenarios
            $delegations = getAnonymousData($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $variableName);
            $delegations = json_decode($delegations);

            if($delegations) {
                $tmpArray = array();
                // foreach delegation, get related scenario
                foreach($delegations as $d) {
                    $curName = json_decode($d->variableValue, true)['scenarioName'];
                    if( json_decode($d->variableValue, true)['isPublic'] == "true" )
                        $curName .= " (Public)";
                    else
                        $curName .= " (Private)";
                    
                    if(!in_array(["name"=>$curName], $outArray)) {
                        $tmpArray['name'] = $curName;
                        $outArray[] = $tmpArray;
                    }               
                }
            }
            echo json_encode($outArray);
        }
    }
    
    // when the user chooses a scenario, call this method to get scenario's features and draw them on the map
    else if($_GET['method'] === "GET" && isset($_GET['sel'])) {
        $selectedScenario = $_GET['sel'];
       
        $query = "SELECT variable_value as variableValue from profiledb.data where variable_name='$variableName' and app_id='$selectedScenario'";
        $result = mysqli_query($link, $query);
        
        if($result) {
            $feature = mysqli_fetch_assoc($result);
            echo $feature['variableValue'];
        }   
    }
}

