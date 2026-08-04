<?php

include '../config.php';

function sendExportAllOrgDashboardsError($statusCode, $message)
{
    http_response_code($statusCode);
    header('Content-Type: text/plain; charset=UTF-8');

    if (function_exists('eventLog')) {
        eventLog('Export all org dashboards error: ' . $message);
    }

    echo $message;
    exit();
}

function sanitizeArchiveNamePart($value, $fallback)
{
    $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
    $value = preg_replace('/[^A-Za-z0-9._-]+/', '_', $value);
    $value = trim($value, "._- \t\n\r\0\x0B");

    if ($value === '' || $value === null) {
        $value = $fallback;
    }

    return substr($value, 0, 80);
}

function getAllowedOrganizationsFromSession()
{
    if (!isset($_SESSION['loggedOrganizations'])) {
        return array();
    }

    if (is_array($_SESSION['loggedOrganizations'])) {
        return array_map('strval', $_SESSION['loggedOrganizations']);
    }

    if (is_string($_SESSION['loggedOrganizations'])) {
        return array_map('trim', explode(',', $_SESSION['loggedOrganizations']));
    }

    return array();
}

function isOrganizationAllowedForSession($organization)
{
    $allowedOrganizations = getAllowedOrganizationsFromSession();

    if (count($allowedOrganizations) === 0) {
        return true;
    }

    return in_array($organization, $allowedOrganizations, true);
}

function fetchDashboardExportPayload($link, $dashboardId)
{
    $dashboardId = (int)$dashboardId;
    $dashboardQuery = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = " . $dashboardId . " AND deleted = 'no' LIMIT 1";
    $dashboardResult = mysqli_query($link, $dashboardQuery);

    if (!$dashboardResult) {
        throw new Exception('Dashboard query failed: ' . mysqli_error($link));
    }

    $dashboardRow = mysqli_fetch_assoc($dashboardResult);
    mysqli_free_result($dashboardResult);

    if (!$dashboardRow) {
        throw new Exception('Dashboard not found: ' . $dashboardId);
    }

    unset($dashboardRow['Id']);

    $widgets = array();
    $widgetsQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = " . $dashboardId;
    $widgetsResult = mysqli_query($link, $widgetsQuery);

    if (!$widgetsResult) {
        throw new Exception('Widget query failed: ' . mysqli_error($link));
    }

    while ($widgetRow = mysqli_fetch_assoc($widgetsResult)) {
        unset($widgetRow['Id']);
        $widgets[] = $widgetRow;
    }

    mysqli_free_result($widgetsResult);

    return array(
        'Dashboard' => $dashboardRow,
        'Widget' => $widgets
    );
}

session_start();
checkSession('Manager');

if (!isset($_SESSION['loggedRole']) || $_SESSION['loggedRole'] !== 'RootAdmin') {
    sendExportAllOrgDashboardsError(403, 'Only RootAdmin users can export all organization dashboards.');
}

if (isset($_SESSION['isPublic']) && $_SESSION['isPublic']) {
    sendExportAllOrgDashboardsError(403, 'Public sessions cannot export organization dashboards.');
}

if (!isset($_SESSION['loggedOrganization']) || trim($_SESSION['loggedOrganization']) === '') {
    sendExportAllOrgDashboardsError(400, 'No organization selected for the current session.');
}

$organization = trim($_SESSION['loggedOrganization']);

if (!isOrganizationAllowedForSession($organization)) {
    sendExportAllOrgDashboardsError(403, 'The selected organization is not available for the current user.');
}

if (!class_exists('ZipArchive')) {
    sendExportAllOrgDashboardsError(500, 'PHP ZipArchive extension is not available on this server.');
}

$link = mysqli_connect($host, $username, $password, $dbname);

if (!$link) {
    sendExportAllOrgDashboardsError(500, 'Database connection failed.');
}

mysqli_set_charset($link, 'utf8');

$dashboardListQuery = "SELECT Id, name_dashboard FROM Dashboard.Config_dashboard WHERE deleted = 'no' AND organizations = ? ORDER BY name_dashboard ASC, Id ASC";
$dashboardListStmt = mysqli_prepare($link, $dashboardListQuery);

if (!$dashboardListStmt) {
    sendExportAllOrgDashboardsError(500, 'Unable to prepare dashboard list query.');
}

mysqli_stmt_bind_param($dashboardListStmt, 's', $organization);
mysqli_stmt_execute($dashboardListStmt);
$dashboardListResult = mysqli_stmt_get_result($dashboardListStmt);

if (!$dashboardListResult) {
    sendExportAllOrgDashboardsError(500, 'Unable to read organization dashboards.');
}

$dashboards = array();

while ($dashboardRow = mysqli_fetch_assoc($dashboardListResult)) {
    $dashboards[] = $dashboardRow;
}

mysqli_free_result($dashboardListResult);
mysqli_stmt_close($dashboardListStmt);

if (count($dashboards) === 0) {
    sendExportAllOrgDashboardsError(404, 'No dashboards found for the selected organization.');
}

$tmpZipPath = tempnam(sys_get_temp_dir(), 'dash_export_');

if ($tmpZipPath === false) {
    sendExportAllOrgDashboardsError(500, 'Unable to create temporary export file.');
}

$zip = new ZipArchive();

if ($zip->open($tmpZipPath, ZipArchive::OVERWRITE) !== true) {
    @unlink($tmpZipPath);
    sendExportAllOrgDashboardsError(500, 'Unable to create dashboard export archive.');
}

$usedEntryNames = array();

try {
    foreach ($dashboards as $dashboard) {
        $dashboardId = (int)$dashboard['Id'];
        $payload = fetchDashboardExportPayload($link, $dashboardId);
        $json = json_encode($payload);

        if ($json === false) {
            throw new Exception('JSON encoding failed for dashboard ' . $dashboardId . ': ' . json_last_error_msg());
        }

        $dashboardName = isset($payload['Dashboard']['name_dashboard']) ? $payload['Dashboard']['name_dashboard'] : $dashboard['name_dashboard'];
        $entryBaseName = 'export_' . sanitizeArchiveNamePart($dashboardName, 'dashboard_' . $dashboardId) . '_' . $dashboardId;
        $entryName = $entryBaseName . '.json';
        $suffix = 2;

        while (isset($usedEntryNames[$entryName])) {
            $entryName = $entryBaseName . '_' . $suffix . '.json';
            $suffix++;
        }

        $usedEntryNames[$entryName] = true;

        if ($zip->addFromString($entryName, $json) === false) {
            throw new Exception('Unable to add dashboard ' . $dashboardId . ' to archive.');
        }
    }
} catch (Exception $exception) {
    $zip->close();
    @unlink($tmpZipPath);
    sendExportAllOrgDashboardsError(500, $exception->getMessage());
}

$zip->close();
mysqli_close($link);

$archiveName = 'dashboards_' . sanitizeArchiveNamePart($organization, 'organization') . '_' . date('Ymd_His') . '.zip';

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $archiveName . '"');
header('Content-Length: ' . filesize($tmpZipPath));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if (isset($_GET['downloadToken']) && preg_match('/^[A-Za-z0-9._-]{1,120}$/', $_GET['downloadToken'])) {
    header('Set-Cookie: dashboardExportAllOrgDashboardsReady=' . rawurlencode($_GET['downloadToken']) . '; Path=/; SameSite=Lax');
}

readfile($tmpZipPath);
@unlink($tmpZipPath);
exit();
