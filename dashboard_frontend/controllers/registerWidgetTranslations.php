<?php
require_once __DIR__ . '/widgetTranslations.php';
require_once __DIR__ . '/translationRegistration.php';
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
$response = ['detail' => 'Ko'];
$link = null;
$stage = 'invalid_registration_input';
try {
    if (!function_exists('translationRegistrationFailureResponse')) {
        http_response_code(503);
        $response = ['detail' => 'Ko', 'reason' => 'registration_diagnostics_version_mismatch',
            'message' => 'The registration endpoint and helper versions do not match. Update both PHP files and check the PHP opcode cache.'];
    } elseif (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST'); http_response_code(405);
        $response = translationRegistrationFailureResponse('method_not_allowed');
    } elseif (!translationRegistrationRequestAllowed($_SERVER, $reason, $diagnostics)) {
        http_response_code(403);
        $response = translationRegistrationFailureResponse($reason, $diagnostics);
    } else {
        $widgetName = $_POST['widgetName'] ?? null;
        $dashboardMenuId = dashboardMenuTranslationId($_POST);
        $json = $_POST['keys'] ?? null;
        if (($dashboardMenuId === null && (!is_string($widgetName) || trim($widgetName) === '' || strlen($widgetName) > 512)) ||
            !is_string($json) || strlen($json) > 2097152) throw new InvalidArgumentException('Invalid registration request');
        $keys = normalizeTranslationRegistrationKeys(json_decode($json));
        // config.php installs the DB session handler on deployments using storeSessionOnDB.
        $stage = 'configuration_failed';
        require __DIR__ . '/../config.php';
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $session = $_SESSION ?? [];
        session_write_close();
        if (empty($session['translationRegistrationDashboardViews']) ||
            !is_array($session['translationRegistrationDashboardViews'])) {
            http_response_code(403);
            $response = translationRegistrationFailureResponse('dashboard_view_not_authorized');
        } else {
            $language = translationRegistrationLanguage(resolveWidgetTranslationLanguage($session, $_COOKIE ?? [], $curr_lang ?? ''), $localizations ?? null, $reason);
            if ($language === '') {
                http_response_code(403);
                $response = translationRegistrationFailureResponse($reason);
            } else {
                $stage = 'database_connection_failed';
                $link = mysqli_connect($host, $username, $password, $dbname);
                if (!$link) throw new RuntimeException('Registration database unavailable', mysqli_connect_errno());
                $stage = 'database_charset_failed';
                if (!mysqli_set_charset($link, 'utf8mb4')) throw new RuntimeException('Registration charset unavailable');
                $stage = 'registration_lock_failed';
                $response = withTranslationRegistrationLock($link, function () use ($link, $widgetName, $dashboardMenuId,
                    $session, $keys, $language, &$stage) {
                    $stage = 'dashboard_access_check_failed';
                    $context = translationRegistrationContext($link, $widgetName, $reason, $dashboardMenuId,
                        function ($dashboardId) use ($session) {
                            return translationRegistrationDashboardViewGranted($session, $dashboardId);
                        });
                    if (!$context) { http_response_code(403); return translationRegistrationFailureResponse($reason); }
                    $stage = 'dictionary_registration_failed';
                    registerMissingTranslationKeys($link, $keys, $language);
                    if ($dashboardMenuId === null && ($_POST['includeMetadata'] ?? '') === '1') {
                        $stage = 'metadata_registration_failed';
                        registerConfiguredDashboardTexts($link, $context, $language);
                    }
                    return ['detail' => 'Ok', 'dashboardId' => $context['dashboardId'], 'language' => $language];
                });
            }
        }
    }
} catch (InvalidArgumentException $error) {
    http_response_code(400);
    $response = translationRegistrationFailureResponse('invalid_registration_input');
} catch (Throwable $error) {
    http_response_code(503);
    $diagnostics = [];
    // Numeric DB error codes are useful in F12; raw SQL/server errors remain private.
    if ($error instanceof mysqli_sql_exception) $diagnostics['databaseErrorCode'] = (int) $error->getCode();
    elseif (in_array($stage, ['database_connection_failed', 'database_charset_failed', 'registration_lock_failed',
        'dashboard_access_check_failed', 'dictionary_registration_failed', 'metadata_registration_failed'], true) &&
        (int) $error->getCode() > 0) $diagnostics['databaseErrorCode'] = (int) $error->getCode();
    elseif ($link instanceof mysqli) {
        try { $code = mysqli_errno($link); if ($code) $diagnostics['databaseErrorCode'] = (int) $code; }
        catch (Throwable $ignored) {}
    }
    $response = translationRegistrationFailureResponse($stage, $diagnostics);
    error_log('Translation registration unavailable (code ' . (int) $error->getCode() . ')');
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    if ($link) mysqli_close($link);
}
echo json_encode($response, JSON_UNESCAPED_UNICODE);
