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
    parse_str((string)parse_url($url, PHP_URL_QUERY), $output);
    $dashboardId = $output["iddasboard"] ?? "";
    return is_string($dashboardId) ? clearB64DashId(trim($dashboardId)) : "";
}

function escapeHtml($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isValidUrl(string $url, array $schemes = ['http', 'https']): bool
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    $parts = parse_url($url);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return false;
    }

    return in_array(strtolower($parts['scheme']), $schemes, true);
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
            $href = "href='" . escapeHtml($linkUrl) . "'";
            $class = "";
            $subLinks = [];
            $submenuIndicator = "";
            $sublinksText = "";
            if (key_exists($id, $sublinkObj)) {
                $subLinks = $sublinkObj[$id];
                foreach ($subLinks as $subLink) {
                    $subLinkUrl = $subLink["linkUrl"];
                    $subLinkIgnoreDelegation = $subLink["ignoreDelegation"];
                    if (isValidUrl((string)$subLinkUrl) && ($role == "RootAdmin" || $subLinkIgnoreDelegation || checkLink($subLinkUrl))){
                        $subId = $subLink["id"];
                        $subText = $subLink["text"];
                        $subOpenMode = $subLink["openMode"];
                        $subIcon = $subLink["icon"];
                        $subIconColor = $subLink["iconColor"];
                        $subTarget = $subOpenMode == "samePage" ? "" : "target='_blank'";
                        $sublinksText .= "<div class='row customLinkRow sublink' data-linkid='" . escapeHtml($subId) . "'>" .
                            "<div class='col-md-12 orgMenuItemCnt' data-fathermenuiddiv='" . escapeHtml($id) . "' style='display: none;'>" .
                            "<a title='" . escapeHtml($subText) . "' href='" . escapeHtml($subLinkUrl) . "' $subTarget id='subCustomLink_" . escapeHtml($subId) . "' class='customLink'>" .
                            "<i class='" . escapeHtml($subIcon) . "' style='color: " . escapeHtml($subIconColor) . "'></i><span class='customLinkText'>" . escapeHtml($subText) . "</span>" .
                            "</a>" .
                            "</div></div>";
                    }
                }
                if ($sublinksText != "") {
                    $href = "";
                    $class = "sublinkFather";
                    $submenuIndicator = '<i class="fa fa-caret-right submenuIndicator" style="color: rgb(51, 64, 69)"></i>';
                }
            }
            if ($sublinksText != "" || (isValidUrl((string)$linkUrl) && ($role == "RootAdmin" || $ignoreDelegation || checkLink($linkUrl)))) {
                echo "<div class='row customLinkRow $class' data-linkid='" . escapeHtml($id) . "'>" .
                    "<div class='col-md-12 orgMenuItemCnt'>" .
                    "<a title='" . escapeHtml($text) . "' $href $target id='customLink_" . escapeHtml($id) . "' class='customLink'>" .
                    "<i class='" . escapeHtml($icon) . "' style='color: " . escapeHtml($iconColor) . "'></i><span class='customLinkText'>" . escapeHtml($text) . "</span>" .
                    $submenuIndicator .
                    "</a>" .
                    "</div></div>";
                echo $sublinksText;
            }
        }
        
    }
}