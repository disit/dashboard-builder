<?php
$sublinkObj = [];
$customLinkVars = ['id', 'linkUrl', 'icon', 'text', 'openMode', 'iconColor', 'menuOrder', 'menuId'];
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
$customLinkVars = ['id', 'linkUrl', 'icon', 'text', 'openMode', 'iconColor', 'menuOrder'];
$customLinkSql = "SELECT " . join(",", $customLinkVars) . " FROM Dashboard.DashboardLinkMenu WHERE dashboardId = $dashId ORDER BY menuOrder ASC";
// echo $customLinkSql;
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
                if ($subLinkUrl != "") {
                    $subId = $subLink["id"];
                    $subText = $subLink["text"];
                    $subOpenMode = $subLink["openMode"];
                    $subIcon = $subLink["icon"];
                    $subIconColor = $subLink["iconColor"];
                    $subTarget = $subOpenMode == "samePage" ? "" : "target='_blank'";
                    $sublinksText .= "<div class='row customLinkRow sublink' data-linkid='$subId'>" .
                        "<div class='col-md-12 orgMenuItemCnt' data-fathermenuiddiv='$id' style='display: none;'>" .
                        "<a title='$subText' href='$subLinkUrl' $subTarget id='subCustomLink_$subId' class='customLink'>" .
                        "<i class='$subIcon' style='color: $subIconColor'></i><span class='customLinkText'>$subText</span>" .
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
        if ($linkUrl != "" || $sublinksText != "") {
            echo "<div class='row customLinkRow $class' data-linkid='$id'>" .
                "<div class='col-md-12 orgMenuItemCnt'>" .
                "<a title='$text' $href $target id='customLink_$id' class='customLink'>" .
                "<i class='$icon' style='color: $iconColor'></i><span class='customLinkText'>$text</span>" .
                $submenuIndicator .
                "</a>" .
                "</div></div>";
            echo $sublinksText;
        }
    }
}
$role = $_SESSION['loggedRole'];
echo "<script>if('$role' != 'RootAdmin'){fetch('../management/getDashboardData.php?dashboardId=".escapeForJS(base64_decode($_GET['iddasboard']))."&loggedUserFirstAttempt=true').then(response => response.json()).then(data => {if(data.visibility != 'public' && data.detail.toLowerCase() != 'ok') document.querySelectorAll('.customLinkRow').forEach((item) => item.style.display = 'none')})}</script>";
