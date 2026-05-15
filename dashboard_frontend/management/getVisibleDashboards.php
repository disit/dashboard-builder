<?php
include_once '../config.php';
require_once '../sso/autoload.php';

$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
mysqli_select_db($link, $dbname);

function encodeB64($id){
    if($id != null){
        $decoded = base64_decode($id, true);
        /*if($decoded !== false && base64_encode($decoded) === $id){
            return $id;
        }*/
        return base64_encode($id);
    }else return null;
}

function checkArray($arr){
    return ($arr != null && is_array($arr)) ? $arr : [];
}

function getAccessToken($refresh_token){
    global $ssoTokenEndpoint, $ssoClientId, $ssoClientSecret;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $ssoTokenEndpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => "client_id=$ssoClientId&grant_type=refresh_token&refresh_token=$refresh_token&client_secret=$ssoClientSecret",
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true)["access_token"] ?? "";
}

function getACLDashboards($access_token){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'localhost/dashboardSmartCity/api/ACLAPI.php?action=get_dashboard_list',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$access_token,
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true)["dashboardIDs"] ?? [];
}

function mapElementIds($x) {
    return encodeB64(trim($x["elementId"] ?? ""));
};

function getOwnedDashboards($access_token){
    global $ownershipApiBaseUrl;
    $apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=DashboardID&accessToken=" . $access_token;
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    $context = stream_context_create($options);
    $myDashboardsJson = file_get_contents($apiUrl, false, $context);
    $myDashboards = json_decode($myDashboardsJson, true);
    return checkArray(array_map("mapElementIds", $myDashboards));
}

function getDelegatedDashboards($access_token, $username = "ANONYMOUS"){
    global $personalDataApiBaseUrl;
    $apiUrl = $personalDataApiBaseUrl . "/v1/username/$username/delegated?elementType=dashboardID&accessToken=" . $access_token . "&sourceRequest=dashboardmanager";
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    $context = stream_context_create($options);
    $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);
    $delegatedDashboards = json_decode($delegatedDashboardsJson, true);
    return checkArray(array_map("mapElementIds", $delegatedDashboards));
}

function getPublicDashboards(){
    $publicSql = "SELECT Id FROM Dashboard.Config_dashboard WHERE deleted = 'no' AND visibility='public' ORDER BY name_dashboard ASC";
    global $link;
    $out = [];
    if (($publicStmt = mysqli_prepare($link, $publicSql)) && mysqli_stmt_execute($publicStmt)) {
        $result = mysqli_stmt_get_result($publicStmt);
        while ($row = mysqli_fetch_assoc($result)) {
            if($row["Id"]){
                $out[] = encodeB64(trim($row["Id"] ?? ""));
            }
        }
    }
    return $out;
}

$acl_dash_ids = [];
$owned_dash_ids = [];
$delegate_dash_ids = [];
$public_dash_ids = getPublicDashboards();
if(isset($_GET["refresh_token"])){
    $refresh_token = $_GET["refresh_token"];
    $access_token = getAccessToken($refresh_token);
    if($access_token != ""){
        $acl_dash_ids = getACLDashboards($access_token);
        $owned_dash_ids = getOwnedDashboards($access_token);
        if(isset($_GET["username"])){
            $username = $_GET["username"];
            $delegate_dash_ids = getDelegatedDashboards($access_token, $username);
        }
    }
}
$out_ids = array_values(array_unique(array_merge(checkArray($acl_dash_ids), checkArray($owned_dash_ids), checkArray($delegate_dash_ids), checkArray($public_dash_ids)))); 
$output = [
    "status" => "Ok",
    "dashIds" => $out_ids
];
echo json_encode($output);