<?php
include_once '../config.php';
require_once '../sso/autoload.php';

function clearB64DashId($dashId){
    return str_replace("=", "", $dashId);
}

$role = $_SESSION['loggedRole'];
$showMenu = $role == "RootAdmin";
$visibleDashboardsIds = [];
if(!$showMenu){
    $refresh_token = $_SESSION["refreshToken"];
    $username = $_SESSION["loggedUsername"];
    $url = "$ssoEndpoint/dashboardSmartCity/management/getVisibleDashboards.php";
    if(isset($refresh_token) && isset($username)){
        $url .= "?refresh_token=$refresh_token&username=$username";
    }
    $options = array(
        'http' => array(
            'header' => "Content-type: application/json",
            'method' => 'GET',
            'timeout' => 30,
            'ignore_errors' => true
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseJson = json_decode($response, true);
    if($responseJson["status"] == "Ok"){
        $visibleDashboardsIds = array_map("clearB64DashId", $responseJson["dashIds"]);
        $showMenu = in_array(clearB64DashId($_GET['iddasboard']), $visibleDashboardsIds);
    }
}
function getDashboardIdFromUrl($url){
    parse_str(explode("?", explode("#", $url)[0])[1], $output);
    return clearB64DashId(trim($output["iddasboard"] ?? ""));
}

function isDashboardLink($url){
    global $ssoEndpoint;
    $baseHost = "$ssoEndpoint/dashboardSmartCity/view";
    return strpos($url, $baseHost) !== false  && strpos($url, "iddasboard=") !== false;
}

function checkLink($linkUrl){
    global $visibleDashboardsIds;
    return (!isDashboardLink($linkUrl) || in_array(getDashboardIdFromUrl($linkUrl), $visibleDashboardsIds));
}

if($showMenu){
    $sublinkObj = [];
    $customLinkVars = ['id', 'linkUrl', 'icon', 'text', 'openMode', 'iconColor', 'menuOrder', 'menuId', 'ignoreDelegation'];
    $customLinkSql = "SELECT " . join(",", $customLinkVars) . " FROM Dashboard.DashboardLinkMenuSubmenus WHERE dashboardId = $dashId ORDER BY menuOrder ASC";
    
    if (($customLinkStmt = mysqli_prepare($link, $customLinkSql)) && mysqli_stmt_execute($customLinkStmt)) {
        $result = mysqli_stmt_get_result($customLinkStmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $menuId = $row['menuId'];
            if (!key_exists($menuId, $sublinkObj)) {
                $sublinkObj[$menuId] = [];
            }
            $sublinkObj[$menuId][] = $row;
        }
    }
    $customLinkVars = ['id', 'linkUrl', 'icon', 'text', 'openMode', 'iconColor', 'menuOrder', 'ignoreDelegation'];
    $customLinkSql = "SELECT " . join(",", $customLinkVars) . " FROM Dashboard.DashboardLinkMenu WHERE dashboardId = $dashId ORDER BY menuOrder ASC";
    if (($customLinkStmt = mysqli_prepare($link, $customLinkSql)) && mysqli_stmt_execute($customLinkStmt)) {
        $customLinkRefs = [];
        foreach ($customLinkVars as $customLinkVar) {
            $$customLinkVar = null;
            $customLinkRefs[] = &$$customLinkVar;
        }
        mysqli_stmt_bind_result($customLinkStmt, ...$customLinkRefs);
    
        while ($customLinkStmt->fetch()) {
            $target = $openMode == "samePage" ? "" : "target='_blank'";
            $href = "href='$linkUrl'";
            $class = "";
            $subLinks = [];
            $submenuIndicator = "";
            $sublinksText = "";
            if (key_exists($id, $sublinkObj)) {
                $subLinks = $sublinkObj[$id];
                foreach ($subLinks as $subLink) {
                    $subLinkUrl = $subLink["linkUrl"];
                    $subLinkIgnoreDelegation = $subLink["ignoreDelegation"];
                    if ($subLinkUrl != "" && ($role == "RootAdmin" || $subLinkIgnoreDelegation || checkLink($subLinkUrl))){
                        $subId = $subLink["id"];
                        $subText = $subLink["text"];
                        $subOpenMode = $subLink["openMode"];
                        $subIcon = $subLink["icon"];
                        $subIconColor = $subLink["iconColor"];
                        $subTarget = $subOpenMode == "samePage" ? "" : "target='_blank'";
                        $sublinksText .= "<li class='customLinkRow sublink' data-linkid='$subId' data-fathermenuiddiv='$id' style='display: none;'>" .
                            "<a title='$subText' href='$subLinkUrl' $subTarget id='subCustomLink_$subId' class='customLink'>" .
                            "<i class='$subIcon' style='color: $subIconColor'></i><span class='menu-item'>$subText</span>" .
                            "</a>" .
                            "</li>";
                    }
                }
                if ($sublinksText != "") {
                    $href = "";
                    $class = "sublinkFather";
                    $submenuIndicator = '<i class="fa fa-caret-right submenuIndicator" style="color: rgb(51, 64, 69)"></i>';
                }
            }
            if ($sublinksText != "" || ($linkUrl != "" && ($role == "RootAdmin" || $ignoreDelegation || checkLink($linkUrl)))) {
                echo "<li class='customLinkRow $class' data-linkid='$id'>" .
                "<a title='$text' $href $target id='customLink_$id' class='customLink'>" .
                "<i class='$icon' style='color: $iconColor'></i><span class='menu-item'>$text</span>$submenuIndicator" .
                "</a>" .
                "</li>";
                echo $sublinksText;
            }
             
        }
    }
}