<?php
include_once '../config.php';
require_once '../sso/autoload.php';

// Shared helpers, identical in customLinks.php and customLinks-side.php.
// Prefixed names avoid clashes with other functions in the application.
// The guard allows both menu files on the same page without "Cannot redeclare" fatals.
if (!function_exists('customLinksBuildMenu')) {
    function customLinksClearB64DashId($dashId): string
    {
        return str_replace("=", "", is_scalar($dashId) ? (string)$dashId : "");
    }

    function customLinksDashboardIdFromUrl(string $url): string
    {
        parse_str((string)parse_url($url, PHP_URL_QUERY), $output);
        $dashId = $output["iddasboard"] ?? "";
        return customLinksClearB64DashId(is_string($dashId) ? trim($dashId) : "");
    }

    function customLinksEscapeHtml($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    function customLinksIsValidUrl(string $url, array $schemes = ['http', 'https']): bool
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

    function customLinksIsDashboardLink(string $url, string $ssoEndpoint): bool
    {
        $baseHost = "$ssoEndpoint/dashboardSmartCity/view";
        return strpos($url, $baseHost) !== false && strpos($url, "iddasboard=") !== false;
    }

    function customLinksCanShow(string $url, $ignoreDelegation, bool $isRootAdmin, array $visibleDashboardsIds, string $ssoEndpoint): bool
    {
        if (!customLinksIsValidUrl($url)) {
            return false;
        }
        if ($isRootAdmin || $ignoreDelegation) {
            return true;
        }
        return !customLinksIsDashboardLink($url, $ssoEndpoint)
            || in_array(customLinksDashboardIdFromUrl($url), $visibleDashboardsIds, true);
    }

    // Returns " style='color: ...'" only for plain CSS colors; anything else (e.g. "red; background: url(...)") is dropped.
    function customLinksColorStyle($color): string
    {
        $color = trim((string)$color);
        $isHex = preg_match('/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color) === 1;
        $isName = preg_match('/^[a-z]{3,30}$/i', $color) === 1;
        $isFunction = preg_match('/^(?:rgba?|hsla?)\([0-9a-z.,%\s\/+-]+\)$/i', $color) === 1;
        if (!$isHex && !$isName && !$isFunction) {
            return "";
        }
        return " style='color: " . customLinksEscapeHtml($color) . "'";
    }

    function customLinksIconHtml($icon, $color): string
    {
        return "<i class='" . customLinksEscapeHtml($icon) . "'" . customLinksColorStyle($color) . "></i>";
    }

    function customLinksTarget(bool $newTab): string
    {
        return $newTab ? "target='_blank'" : "";
    }

    // Returns the dashboard ids visible to the session user, or null when the service fails.
    // Successful results are cached in the session for 5 minutes, so most page loads make no HTTP call.
    function customLinksGetVisibleDashboardIds(string $ssoEndpoint): ?array
    {
        $cacheTtl = 300;
        $refreshToken = $_SESSION["refreshToken"] ?? null;
        $username = $_SESSION["loggedUsername"] ?? null;
        $cacheUser = is_string($username) ? $username : "";
        $cache = $_SESSION["customLinksVisibleDashboards"] ?? null;
        if (is_array($cache) && ($cache["user"] ?? null) === $cacheUser && is_array($cache["ids"] ?? null)
            && is_int($cache["time"] ?? null) && time() - $cache["time"] < $cacheTtl) {
            return $cache["ids"];
        }

        $url = "$ssoEndpoint/dashboardSmartCity/management/getVisibleDashboards.php";
        if (isset($refreshToken, $username)) {
            $url .= "?" . http_build_query(["refresh_token" => $refreshToken, "username" => $username]);
        }
        $context = stream_context_create([
            'http' => [
                'header' => "Content-type: application/json",
                'method' => 'GET',
                'timeout' => 60,
                'ignore_errors' => true
            ]
        ]);
        // @: a network failure would print a warning inside the menu HTML; false is handled below.
        $response = @file_get_contents($url, false, $context);
        $responseJson = is_string($response) ? json_decode($response, true) : null;
        if (!is_array($responseJson) || ($responseJson["status"] ?? "") !== "Ok" || !is_array($responseJson["dashIds"] ?? null)) {
            return null;
        }
        $ids = array_map("customLinksClearB64DashId", $responseJson["dashIds"]);
        // An incomplete list (a source failed) is used for this page only, never cached.
        if (($responseJson["complete"] ?? true) !== false) {
            $_SESSION["customLinksVisibleDashboards"] = ["user" => $cacheUser, "ids" => $ids, "time" => time()];
        }
        return $ids;
    }

    function customLinksFetchRows(mysqli $link, string $sql, int $dashId): array
    {
        $rows = [];
        $stmt = mysqli_prepare($link, $sql);
        if ($stmt && mysqli_stmt_bind_param($stmt, "i", $dashId) && mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($result && ($row = mysqli_fetch_assoc($result))) {
                $rows[] = $row;
            }
        }
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
        return $rows;
    }

    // Returns the menu items the session user may see. Each item: id, text, icon, iconColor, url, newTab, sublinks.
    function customLinksBuildMenu($link, $dashId, $ssoEndpoint): array
    {
        // dashboardId is an integer column; a missing or non-integer $dashId shows no menu.
        // Checked first, so no HTTP call to getVisibleDashboards.php happens when the menu cannot be built anyway.
        $dashIdStr = is_int($dashId) || is_string($dashId) ? trim((string)$dashId) : "";
        $dashIdInt = preg_match('/^\d+$/', $dashIdStr) === 1 ? (int)$dashIdStr : false;
        if ($dashIdInt === false || !($link instanceof mysqli)) {
            return [];
        }

        $ssoEndpoint = is_string($ssoEndpoint) ? $ssoEndpoint : "";
        $isRootAdmin = ($_SESSION['loggedRole'] ?? null) === "RootAdmin";
        $visibleDashboardsIds = [];
        if (!$isRootAdmin) {
            $visibleDashboardsIds = customLinksGetVisibleDashboardIds($ssoEndpoint);
            if ($visibleDashboardsIds === null) {
                return [];
            }
            // Uses the dashboard being rendered, not $_GET: the page may receive iddasboard via POST ($_REQUEST).
            $currentDashId = customLinksClearB64DashId(base64_encode((string)$dashIdInt));
            if (!in_array($currentDashId, $visibleDashboardsIds, true)) {
                return [];
            }
        }

        // Since PHP 8.1 mysqli throws exceptions by default; these queries expect false returns instead.
        $prevReportMode = (new mysqli_driver())->report_mode;
        mysqli_report(MYSQLI_REPORT_OFF);
        try {
            $subRows = customLinksFetchRows($link, "SELECT id, linkUrl, icon, text, openMode, iconColor, menuOrder, menuId, ignoreDelegation FROM Dashboard.DashboardLinkMenuSubmenus WHERE dashboardId = ? ORDER BY menuOrder ASC", $dashIdInt);
            $menuRows = customLinksFetchRows($link, "SELECT id, linkUrl, icon, text, openMode, iconColor, menuOrder, ignoreDelegation FROM Dashboard.DashboardLinkMenu WHERE dashboardId = ? ORDER BY menuOrder ASC", $dashIdInt);
        } finally {
            mysqli_report($prevReportMode);
        }

        $subRowsByMenu = [];
        foreach ($subRows as $subRow) {
            if ($subRow['menuId'] !== null) {
                $subRowsByMenu[$subRow['menuId']][] = $subRow;
            }
        }

        $items = [];
        foreach ($menuRows as $row) {
            $sublinks = [];
            $rowSubRows = $row['id'] !== null ? ($subRowsByMenu[$row['id']] ?? []) : [];
            foreach ($rowSubRows as $subRow) {
                $subUrl = (string)$subRow['linkUrl'];
                if (customLinksCanShow($subUrl, $subRow['ignoreDelegation'], $isRootAdmin, $visibleDashboardsIds, $ssoEndpoint)) {
                    $sublinks[] = [
                        'id' => $subRow['id'],
                        'text' => $subRow['text'],
                        'icon' => $subRow['icon'],
                        'iconColor' => $subRow['iconColor'],
                        'url' => $subUrl,
                        'newTab' => $subRow['openMode'] != "samePage"
                    ];
                }
            }
            $url = (string)$row['linkUrl'];
            if ($sublinks !== [] || customLinksCanShow($url, $row['ignoreDelegation'], $isRootAdmin, $visibleDashboardsIds, $ssoEndpoint)) {
                $items[] = [
                    'id' => $row['id'],
                    'text' => $row['text'],
                    'icon' => $row['icon'],
                    'iconColor' => $row['iconColor'],
                    'url' => $url,
                    'newTab' => $row['openMode'] != "samePage",
                    'sublinks' => $sublinks
                ];
            }
        }
        return $items;
    }
}

if (!function_exists('customLinksRenderSide')) {
    function customLinksRenderSide(array $items): void
    {
        foreach ($items as $item) {
            $isFather = $item['sublinks'] !== [];
            $href = $isFather ? "" : "href='" . customLinksEscapeHtml($item['url']) . "'";
            $class = $isFather ? "sublinkFather" : "";
            $submenuIndicator = $isFather ? '<i class="fa fa-caret-right submenuIndicator" style="color: rgb(51, 64, 69)"></i>' : "";
            $target = customLinksTarget($item['newTab']);
            $id = customLinksEscapeHtml($item['id']);
            $text = customLinksEscapeHtml($item['text']);
            echo "<li class='customLinkRow $class' data-linkid='$id'>" .
                "<a title='$text' $href $target id='customLink_$id' class='customLink'>" .
                customLinksIconHtml($item['icon'], $item['iconColor']) . "<span class='menu-item'>$text</span>$submenuIndicator" .
                "</a>" .
                "</li>";
            foreach ($item['sublinks'] as $sub) {
                $subId = customLinksEscapeHtml($sub['id']);
                $subText = customLinksEscapeHtml($sub['text']);
                $subTarget = customLinksTarget($sub['newTab']);
                echo "<li class='customLinkRow sublink' data-linkid='$subId' data-fathermenuiddiv='$id' style='display: none;'>" .
                    "<a title='$subText' href='" . customLinksEscapeHtml($sub['url']) . "' $subTarget id='subCustomLink_$subId' class='customLink'>" .
                    customLinksIconHtml($sub['icon'], $sub['iconColor']) . "<span class='menu-item'>$subText</span>" .
                    "</a>" .
                    "</li>";
            }
        }
    }
}

// All work happens inside functions, so no variable leaks into the including page.
customLinksRenderSide(customLinksBuildMenu($link ?? null, $dashId ?? null, $ssoEndpoint ?? ""));
