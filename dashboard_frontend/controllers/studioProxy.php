<?php

// save data into db for the username (profiledb.data table)
function postMyPersonalData($baseUrl, $username, $sourceRequest, $accessToken, $motivation, $variableValue, $studioName, $variableName) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/data?sourceRequest=".$sourceRequest
            ."&accessToken=".$accessToken;

    $bodyData = [
        "username" => $username,
        "dataTime" => round(microtime(true) * 1000),
        "motivation" => $motivation,
        "variableName" => $variableName,
        "elementId" => $studioName,
        "APPID" => $studioName,
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

// Creates an anonymous delegations associated to the specified studio
function postAnonymousDelegation($baseUrl, $username, $sourceRequest, $accessToken, $variableName, $studioName, $motivation) {
    $apiUrl = $baseUrl . "/v1/username/" . rawurlencode($username) . "/delegation?sourceRequest="
            .$sourceRequest."&accessToken=".$accessToken;

    $bodyData = [
        "usernameDelegated" => "ANONYMOUS", 
        "elementId" => $studioName, 
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
$dmSourceRequest = "widgetstudio";
$dmMotivation = "studio";
$variableName = "studio_id";

if(!isset($_SESSION['refreshToken'])) {
    // Call used to save a new studio
    if($_GET['method'] === "POST") {
        echo "-2";
    }
    // Call used to populate studios' select widget
    else if($_GET['method'] === "GET" && $_GET['opt'] === "name") {
        // Get anonymous delegations for scenarios type (elementType)
        $query = "SELECT element_id as elementId from profiledb.delegation where variable_name='".mysqli_escape_string($link, $variableName)."'";
        $result = mysqli_query($link, $query);
        /*$delegations = getAnonymousDelegation($personalDataApiBaseUrl, $dmSourceRequest, $elementType , $accessToken);
        $delegations = json_decode($delegations);*/

        $outArray = array();
        $tmpArray = array();
        if($result) {
            while($d = mysqli_fetch_assoc($result)) {
                $curStudioName = $d['elementId'];
                $tmpArray['name'] = $curStudioName;
                $outArray[] = $tmpArray;
            }
        }
        echo json_encode($outArray);
    }
    // when the user chooses a studio, call this method to get studio's features and draw them on the map
    else if($_GET['method'] === "GET" && isset($_GET['sel'])) {
        $selectedStudio = $_GET['sel'];
       
        $query = "SELECT variable_value as variableValue from profiledb.data where variable_name='".mysqli_escape_string($link, $variableName)."' and app_id='".mysqli_escape_string($link, $selectedStudio)."'";
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
    
    // Call used to save a new studio
    if($_GET['method'] === "POST") {
        $studioName = $_GET['studioName'];
        $scenarioName = $_GET['scenarioName'];
        $studioDescription = $_GET['studioDescription']; //add scenario description 
        $waypoints = $_GET['waypoints'];
        $weighting = $_GET['weighting'];
        $startDatetime = $_GET['startDatetime'];
        $vehicle = $_GET['vehicle'];
        $isPublic = $_GET['public'];
        
        // encapsulate studio's data in a json to store it in db
        $dmVariableValue = json_encode(
            array('studioName' => $studioName, 'scenarioName' => $scenarioName, 'studioDescription' => $studioDescription,
                'waypoints' => $waypoints, 'vehicle' => $vehicle, 'weighting' => $weighting, 'startDatetime' => $startDatetime,
                'isPublic' => $isPublic)
        );
        
        // before insert data, we get if the studioName already exists (among studios -> use of variable_name)
        $query = "SELECT count(*) as count from profiledb.data where variable_name='".mysqli_escape_string($link, $variableName)."' and app_id='".mysqli_escape_string($link, $studioName)."'";
        $result = mysqli_query($link, $query);
        $feature = mysqli_fetch_assoc($result);
        if($feature['count'] == 0 ) {
            // 1: save studio's data
            $postResp = postMyPersonalData($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $dmMotivation, $dmVariableValue, $studioName, $variableName);
            // if the created studio is public, we also need to create an anonymous delegation for this studio
            if($isPublic == "true") {
                // 2: if the studio is public, save delegation to anonymous
                postAnonymousDelegation($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $variableName, $studioName, $dmMotivation);
            }
            echo "1";
        }
        else        // name already exists
            echo "-1";
    }
    
    // Call used to populate studios' select widget
    else if($_GET['method'] === "GET" && $_GET['opt'] === "name") {
        // root admin can view all studios
        if($_SESSION['loggedRole'] === "RootAdmin") {
            $getData = getMyPersonalData($personalDataApiBaseUrl, "ANY", $dmSourceRequest, $accessToken, $dmMotivation);
            
            $getDataJson = json_decode($getData, true);

            $outArray = array();
            $tmpArray = array();
            foreach ($getDataJson as $feature) {
                $tmpArray['name'] = $feature['APPID'] . ":" . json_decode($feature['variableValue'], true)['scenarioName'];
                if( json_decode($feature['variableValue'], true)['isPublic'] == "true" ){
                    $tmpArray['name'] .= " (Public)";
                } else if($_SESSION['loggedRole'] === "RootAdmin") {
                    if($feature['username'] === $_SESSION['loggedUsername']) {
                        $tmpArray['name'] .= " (My Own)";
                    } else {
                        $tmpArray['name'] .= " (" . $feature['username'] .")";
                    }
                } else {
                    $tmpArray['name'] .= " (My Own)";
                }
                
                $outArray[] = $tmpArray;
            }
            echo json_encode($outArray);
        }
        // normal user can view both created and public studios
        else {
            // get scenarios created by the user
            $getData = getMyPersonalData($personalDataApiBaseUrl, $dmUsername, $dmSourceRequest, $accessToken, $dmMotivation);
            $getDataJson = json_decode($getData, true);
            
            $outArray = array();
            $tmpArray = array();
            foreach ($getDataJson as $feature) {
                $tmpArray['name'] = $feature['APPID'] . ":" . json_decode($feature['variableValue'], true)['scenarioName'];
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
                    $curName = json_decode($d->variableValue, true)['studioName'] . ":" . 
                            json_decode($d->variableValue, true)['scenarioName'];
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
    
    // when the user chooses a studio, call this method to get studio's features and draw them on the map
    else if($_GET['method'] === "GET" && isset($_GET['sel'])) {
        $selectedStudio = $_GET['sel'];
       
        $query = "SELECT variable_value as variableValue from profiledb.data where variable_name='".mysqli_escape_string($link, $variableName)."' and app_id='".mysqli_escape_string($link, $selectedStudio)."'";
        $result = mysqli_query($link, $query);
        
        if($result) {
            $feature = mysqli_fetch_assoc($result);
            echo $feature['variableValue'];
        }   
    }
}