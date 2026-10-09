<?php
/* Reads only the requested entries of the global dictionary. */
require_once __DIR__ . '/widgetTranslations.php';

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
$response = ['detail' => 'Ko', 'dashboardId' => null, 'enabled' => false,
    'language' => '', 'translations' => []];
$link = null;
try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
    } else {
        $widgetName = $_POST['widgetName'] ?? null;
        $keyJson = $_POST['keys'] ?? null;
        if (!is_string($widgetName) || trim($widgetName) === '' || strlen($widgetName) > 512 ||
            !is_string($keyJson) || strlen($keyJson) > 2097152) {
            throw new InvalidArgumentException('Invalid translation request');
        }
        $keys = normalizeWidgetTranslationKeys(json_decode($keyJson));
        require __DIR__ . '/../config.php';
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $language = resolveWidgetTranslationLanguage($_SESSION ?? [], $_COOKIE ?? [], $curr_lang ?? '');
        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
        $link = mysqli_connect($host, $username, $password, $dbname);
        if (!$link || !mysqli_set_charset($link, 'utf8mb4')) throw new RuntimeException('Translation database unavailable');
        $response = loadWidgetTextTranslations($link, $widgetName, $keys, $language);
        if ($response['detail'] !== 'Ok') http_response_code(503);
    }
} catch (InvalidArgumentException $exception) {
    http_response_code(400);
} catch (Throwable $exception) {
    http_response_code(503);
    error_log('Widget translation endpoint unavailable (code ' . (int) $exception->getCode() . ')');
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    if ($link) mysqli_close($link);
}
echo encodeWidgetTextTranslations($response);
