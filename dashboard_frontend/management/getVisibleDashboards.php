<?php
include_once '../config.php';
require_once '../sso/autoload.php';

header('Content-Type: application/json');

// Since PHP 8.1 mysqli throws exceptions by default; this script expects false returns instead.
mysqli_report(MYSQLI_REPORT_OFF);
// @: a failed connect still emits a warning, which would corrupt the JSON output.
$link = @mysqli_connect($host, $username, $password);
if(!$link || !mysqli_select_db($link, $dbname)){
    echo json_encode(["status" => "Ko", "dashIds" => []]);
    exit;
}

function encodeB64($id){
    if($id === null || $id === ""){
        return null;
    }
    return base64_encode((string)$id);
}

function checkArray($arr){
    return is_array($arr) ? $arr : [];
}

function decodeJsonResponse($response){
    if(!is_string($response) || $response === ""){
        return null;
    }
    return json_decode($response, true);
}

function getAccessToken($refresh_token){
    global $ssoTokenEndpoint, $ssoClientId, $ssoClientSecret;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $ssoTokenEndpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $ssoClientId,
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token,
        'client_secret' => $ssoClientSecret
    ]),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
    ),
    ));
    $response = curl_exec($curl);
    $data = decodeJsonResponse($response);
    $token = is_array($data) ? ($data["access_token"] ?? "") : "";
    return is_string($token) ? $token : "";
}

function createACLDashboardsHandle($access_token){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/dashboardSmartCity/api/ACLAPI.php?action=get_dashboard_list',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$access_token,
    ),
    ));
    return $curl;
}

function createGetHandle($url, $contentType){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => array(
        'Content-type: ' . $contentType,
    ),
    ));
    return $curl;
}

function createOwnedDashboardsHandle($access_token){
    global $ownershipApiBaseUrl;
    $apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=DashboardID&accessToken=" . urlencode($access_token);
    return createGetHandle($apiUrl, "application/json");
}

function createDelegatedDashboardsHandle($access_token, $username){
    global $personalDataApiBaseUrl;
    $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($username) . "/delegated?elementType=dashboardID&accessToken=" . urlencode($access_token) . "&sourceRequest=dashboardmanager";
    return createGetHandle($apiUrl, "application/x-www-form-urlencoded");
}

// Runs all handles at the same time; total wait = slowest call, not the sum.
// Returns, with the same keys, ["body" => response body, "ok" => true only for HTTP 200].
function runParallel(array $handles){
    $multi = curl_multi_init();
    foreach ($handles as $handle) {
        curl_multi_add_handle($multi, $handle);
    }
    do {
        $status = curl_multi_exec($multi, $running);
        if ($running && curl_multi_select($multi, 1.0) === -1) {
            usleep(10000);
        }
    } while ($running && $status === CURLM_OK);

    $responses = [];
    foreach ($handles as $key => $handle) {
        $responses[$key] = [
            "body" => curl_multi_getcontent($handle),
            "ok" => curl_getinfo($handle, CURLINFO_HTTP_CODE) === 200
        ];
        curl_multi_remove_handle($multi, $handle);
    }
    return $responses;
}

function mapElementIds($x) {
    if(!is_array($x)){
        return null;
    }
    $elementId = $x["elementId"] ?? "";
    return is_scalar($elementId) ? encodeB64(trim((string)$elementId)) : null;
}

function getPublicDashboards(){
    $publicSql = "SELECT Id FROM Dashboard.Config_dashboard WHERE deleted = 'no' AND visibility='public' ORDER BY name_dashboard ASC";
    global $link;
    $out = [];
    if (($publicStmt = mysqli_prepare($link, $publicSql)) && mysqli_stmt_execute($publicStmt)) {
        $result = mysqli_stmt_get_result($publicStmt);
        while ($result && ($row = mysqli_fetch_assoc($result))) {
            if($row["Id"]){
                $out[] = encodeB64(trim((string)$row["Id"]));
            }
        }
    }
    return $out;
}

$acl_dash_ids = [];
$owned_dash_ids = [];
$delegate_dash_ids = [];
$public_dash_ids = getPublicDashboards();
// false when a source failed: the list may miss dashboards, so callers must not cache it.
$complete = true;
$reqRefreshToken = $_GET["refresh_token"] ?? null;
if(is_string($reqRefreshToken) && $reqRefreshToken !== ""){
    $access_token = getAccessToken($reqRefreshToken);
    if($access_token == ""){
        $complete = false;
    }else{
        $handles = [
            "acl" => createACLDashboardsHandle($access_token),
            "owned" => createOwnedDashboardsHandle($access_token)
        ];
        $reqUsername = $_GET["username"] ?? null;
        if(is_string($reqUsername) && $reqUsername !== ""){
            $handles["delegated"] = createDelegatedDashboardsHandle($access_token, $reqUsername);
        }
        $responses = runParallel($handles);

        $aclData = decodeJsonResponse($responses["acl"]["body"]);
        if($responses["acl"]["ok"] && is_array($aclData) && is_array($aclData["dashboardIDs"] ?? null)){
            $acl_dash_ids = $aclData["dashboardIDs"];
        }else{
            $complete = false;
        }
        $ownedData = decodeJsonResponse($responses["owned"]["body"]);
        if($responses["owned"]["ok"] && is_array($ownedData)){
            $owned_dash_ids = array_map("mapElementIds", $ownedData);
        }else{
            $complete = false;
        }
        if(isset($responses["delegated"])){
            $delegatedData = decodeJsonResponse($responses["delegated"]["body"]);
            if($responses["delegated"]["ok"] && is_array($delegatedData)){
                $delegate_dash_ids = array_map("mapElementIds", $delegatedData);
            }else{
                $complete = false;
            }
        }
    }
}
$all_ids = array_merge(checkArray($acl_dash_ids), checkArray($owned_dash_ids), checkArray($delegate_dash_ids), checkArray($public_dash_ids));
$all_ids = array_filter($all_ids, function($v){ return is_scalar($v) && (string)$v !== ""; });
$out_ids = array_values(array_unique(array_map("strval", $all_ids)));
$output = [
    "status" => "Ok",
    "dashIds" => $out_ids,
    "complete" => $complete
];
echo json_encode($output);