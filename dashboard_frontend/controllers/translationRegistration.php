<?php
/* Write-only registration primitives. Readers deliberately remain independent. */
require_once __DIR__ . '/dashboardFooterTranslations.php';

function translationRegistrationIdentity(array $session, &$reason = null)
{
    $reason = null;
    $role = $session['loggedRole'] ?? null;
    $user = $session['loggedUsername'] ?? null;
    $token = $session['refreshToken'] ?? null;
    if ($role === null) { $reason = 'session_role_missing'; return null; }
    if (!in_array($role, ['Manager', 'AreaManager', 'ToolAdmin', 'RootAdmin'], true)) { $reason = 'session_role_not_allowed'; return null; }
    if (!is_string($user) || $user === '') { $reason = 'session_username_missing'; return null; }
    if (!is_string($token) || $token === '') { $reason = 'session_refresh_token_missing'; return null; }
    $parts = explode('.', $token);
    if (count($parts) !== 3) { $reason = 'session_refresh_token_invalid'; return null; }
    $claims = json_decode(base64_decode(strtr($parts[1], '-_', '+/'), true), true);
    // The JWT is taken from the server-side SSO session, never accepted from the request.
    if (!is_array($claims) || !isset($claims['exp']) || !is_numeric($claims['exp'])) { $reason = 'session_refresh_token_invalid'; return null; }
    if ((float) $claims['exp'] <= time()) { $reason = 'session_refresh_token_expired'; return null; }
    return ['role' => $role, 'username' => $user];
}

function translationRegistrationLanguage($language, $localizations, &$reason = null)
{
    $reason = null;
    if (!is_string($language) || !preg_match('/^[a-zA-Z0-9_.@-]{1,45}$/D', $language)) { $reason = 'language_invalid'; return ''; }
    $config = is_string($localizations) ? json_decode($localizations, true) : null;
    if (!is_array($config) || !isset($config['languages']) || !is_array($config['languages'])) { $reason = 'language_configuration_invalid'; return ''; }
    foreach ($config['languages'] as $entry) {
        if (is_array($entry) && ($entry['code'] ?? null) === $language) return $language;
    }
    $reason = 'language_not_configured';
    return '';
}

function translationRegistrationParseOrigin($value)
{
    // An Origin is scheme + host + optional port, never a URL with credentials/path.
    if (!is_string($value) || !preg_match('/\Ahttps?:\/\/(?:\[[0-9a-f:.]+\]|[a-z0-9_.-]+)(?::[0-9]{1,5})?\z/i', $value)) return null;
    $origin = parse_url($value);
    if (!$origin || !isset($origin['scheme'], $origin['host'])) return null;
    if ($origin['host'][0] === '[' && !filter_var(substr($origin['host'], 1, -1), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return null;
    $scheme = strtolower($origin['scheme']);
    $port = $origin['port'] ?? ($scheme === 'https' ? 443 : 80);
    if ($port < 1 || $port > 65535) return null;
    return ['scheme' => $scheme, 'host' => strtolower($origin['host']), 'port' => $port];
}

function translationRegistrationRequestAllowed(array $server, &$reason = null, &$diagnostics = null, $publicOrigins = null)
{
    $reason = null; $diagnostics = [];
    // A cross-origin HTML form cannot set this header. No CORS permission is granted.
    if (($server['REQUEST_METHOD'] ?? '') !== 'POST') { $reason = 'method_not_allowed'; return false; }
    if (($server['HTTP_X_S4C_TRANSLATION_REGISTRATION'] ?? '') !== '1') { $reason = 'registration_header_missing'; return false; }
    if (in_array($server['HTTP_SEC_FETCH_SITE'] ?? '', ['cross-site', 'same-site'], true)) { $reason = 'cross_origin_request'; return false; }
    $origin = isset($server['HTTP_ORIGIN']) ? translationRegistrationParseOrigin($server['HTTP_ORIGIN']) : null;
    if (isset($server['HTTP_ORIGIN']) && $origin === null) { $reason = 'invalid_origin'; return false; }
    // Browser-controlled Fetch Metadata describes the PUBLIC request, independently
    // of TLS termination, rewritten Host/ports and any number of reverse proxies.
    // This is a CSRF guard, not authorization: the session dashboard-view proof still follows.
    if (($server['HTTP_SEC_FETCH_SITE'] ?? '') === 'same-origin') return true;
    if ($origin !== null) {
        // Older clients (or unknown Fetch Metadata values) use a strict Origin check.
        // Only operator-configured origins are trusted; never infer trust from
        // client-supplied Forwarded/X-Forwarded-* headers or derived app URLs.
        if ($publicOrigins === null) {
            $configured = getenv('S4C_TRANSLATION_PUBLIC_ORIGINS');
            $publicOrigins = ($configured === false || $configured === '') ? [] : json_decode($configured);
        }
        if (!is_array($publicOrigins) || $publicOrigins !== array_values($publicOrigins)) { $reason = 'public_origin_configuration_invalid'; return false; }
        $trusted = [];
        foreach ($publicOrigins as $value) {
            $parsed = translationRegistrationParseOrigin($value);
            if ($parsed === null) { $reason = 'public_origin_configuration_invalid'; return false; }
            $trusted[] = $parsed;
        }
        foreach ($trusted as $allowed) if ($origin === $allowed) return true;
        $https = !empty($server['HTTPS']) && !in_array(strtolower((string) $server['HTTPS']), ['off', '0'], true);
        $host = translationRegistrationParseOrigin(($https ? 'https://' : 'http://') . ($server['HTTP_HOST'] ?? ''));
        if ($host === null) { $reason = 'invalid_origin'; return false; }
        if ($origin['host'] !== $host['host']) { $reason = 'origin_host_mismatch'; return false; }
        if ($origin['port'] !== $host['port']) {
            $reason = 'origin_port_mismatch';
            $diagnostics = ['originPort' => $origin['port'], 'serverPort' => $host['port'], 'phpHttpsDetected' => $https];
            return false;
        }
        if ($origin['scheme'] !== $host['scheme']) { $reason = 'origin_scheme_mismatch'; return false; }
    }
    // Legacy browsers without either header retain the custom-header/no-CORS guard.
    return true;
}

function translationRegistrationFailureResponse($reason, array $diagnostics = [])
{
    // Fixed public diagnostics only. Never echo requests, credentials, SQL or exception messages.
    $messages = [
        'method_not_allowed' => 'Use a POST request for translation registration.',
        'registration_header_missing' => 'The required registration header is missing or invalid.',
        'cross_origin_request' => 'Cross-origin registration requests are not allowed.',
        'invalid_origin' => 'The request origin or host is invalid.',
        'origin_host_mismatch' => 'The request origin host does not match the server host.',
        'origin_port_mismatch' => 'The request origin port does not match the port detected by PHP. Check HTTPS/proxy configuration.',
        'origin_scheme_mismatch' => 'The request origin scheme does not match the scheme detected by PHP. Check HTTPS/proxy configuration.',
        'public_origin_configuration_invalid' => 'S4C_TRANSLATION_PUBLIC_ORIGINS must be a JSON array of valid HTTP/HTTPS origins without paths or wildcards.',
        'session_role_missing' => 'The PHP session has no authenticated dashboard role.',
        'session_role_not_allowed' => 'The session role is not allowed to register translations.',
        'session_username_missing' => 'The PHP session has no valid authenticated username.',
        'session_refresh_token_missing' => 'The PHP session has no SSO refresh token. Sign in again.',
        'session_refresh_token_invalid' => 'The session SSO refresh token has an invalid format or expiry claim. Sign in again.',
        'session_refresh_token_expired' => 'The session SSO refresh token has expired. Sign in again.',
        'language_invalid' => 'The selected language is missing or invalid.',
        'language_configuration_invalid' => 'The configured localization language list is missing or invalid.',
        'language_not_configured' => 'The selected language is not in the configured language list.',
        'dashboard_not_found' => 'No dashboard was found for this request.',
        'dashboard_not_found_or_not_owned' => 'The dashboard was not found or is not owned by the authenticated user.',
        'dashboard_view_not_authorized' => 'This session has not been authorized to view the requested dashboard.',
        'language_selector_disabled' => 'The dashboard language selector is not enabled.',
        'dashboard_menu_disabled' => 'The dashboard links menu is not enabled.',
        'dashboard_deleted' => 'The dashboard is marked as deleted.',
        'invalid_registration_input' => 'The widget name or translation key batch is invalid.',
        'configuration_failed' => 'The registration configuration or session bootstrap failed.',
        'database_connection_failed' => 'The registration database connection failed.',
        'database_charset_failed' => 'The registration database UTF-8 charset setup failed.',
        'registration_lock_failed' => 'The translation registration lock could not be acquired or queried.',
        'dashboard_access_check_failed' => 'The dashboard authorization database lookup failed.',
        'dictionary_registration_failed' => 'The dictionary lookup or insertion failed.',
        'metadata_registration_failed' => 'The dashboard metadata lookup or registration failed.',
        'registration_failed' => 'Translation registration failed.',
    ];
    if (!isset($messages[$reason])) $reason = 'registration_failed';
    $response = ['detail' => 'Ko', 'reason' => $reason, 'message' => $messages[$reason]];
    $safe = [];
    foreach (['originPort', 'serverPort', 'databaseErrorCode'] as $key)
        if (isset($diagnostics[$key]) && is_int($diagnostics[$key])) $safe[$key] = $diagnostics[$key];
    if (isset($diagnostics['phpHttpsDetected']) && is_bool($diagnostics['phpHttpsDetected'])) $safe['phpHttpsDetected'] = $diagnostics['phpHttpsDetected'];
    if ($safe) $response['diagnostics'] = $safe;
    return $response;
}

function normalizeTranslationRegistrationKeys($keys)
{
    if (!is_array($keys) || $keys !== array_values($keys) || count($keys) > 200)
        throw new \InvalidArgumentException('Invalid registration batch');
    $unique = []; $bytes = 0;
    foreach ($keys as $key) {
        if (!is_string($key) || !preg_match('//u', $key) || strlen($key) > 65535)
            throw new \InvalidArgumentException('Invalid registration text');
        if (trim($key) === '' || is_numeric(trim($key)) ||
            preg_match('/^(?:https?:\/\/|urn:|\d{4}-\d{2}-\d{2}(?:[T ].*)?$|\d{1,2}:\d{2}(?::\d{2})?$)/i', trim($key))) continue;
        if (isset($unique[$key])) continue;
        $bytes += strlen($key);
        if ($bytes > 262144) throw new \InvalidArgumentException('Registration batch too large');
        $unique[$key] = $key;
    }
    return array_values($unique);
}

function translationRegistrationStatement($link, $sql, array $parameters)
{
    $statement = mysqli_prepare($link, $sql);
    if (!$statement) throw new \RuntimeException('Registration prepare failed', mysqli_errno($link));
    try {
        if (($parameters && !mysqli_stmt_bind_param($statement, str_repeat('s', count($parameters)), ...$parameters)) ||
            !mysqli_stmt_execute($statement)) throw new \RuntimeException('Registration query failed', mysqli_stmt_errno($statement));
        return $statement;
    } catch (\Throwable $error) {
        mysqli_stmt_close($statement);
        throw $error;
    }
}

function acquireTranslationRegistrationLock($link)
{
    // One lock only: acquiring a second named lock would release the first on MySQL 5.6.
    $statement = translationRegistrationStatement($link, 'SELECT GET_LOCK(?, 2)', ['Dashboard.multilanguage.registration.v1']);
    try {
        mysqli_stmt_bind_result($statement, $acquired);
        if (!mysqli_stmt_fetch($statement) || (int) $acquired !== 1)
            throw new \RuntimeException('Translation registration lock unavailable');
    } finally { mysqli_stmt_close($statement); }
}

function releaseTranslationRegistrationLock($link)
{
    try {
        $statement = translationRegistrationStatement($link, 'SELECT RELEASE_LOCK(?)', ['Dashboard.multilanguage.registration.v1']);
        try {
            mysqli_stmt_bind_result($statement, $released);
            if (!mysqli_stmt_fetch($statement) || (int) $released !== 1)
                error_log('Translation registration lock release failed');
        } finally { mysqli_stmt_close($statement); }
    } catch (\Throwable $error) {
        // Connection close also releases a named lock; never mask the original error.
        error_log('Translation registration lock release unavailable');
    }
}

function withTranslationRegistrationLock($link, callable $operation)
{
    acquireTranslationRegistrationLock($link);
    try { return $operation(); }
    finally { releaseTranslationRegistrationLock($link); }
}

function translationRegistrationRememberDashboardView(array &$session, $dashboardId)
{
    if ((!is_int($dashboardId) && !is_string($dashboardId)) ||
        !preg_match('/^[1-9][0-9]*$/D', (string) $dashboardId)) return false;
    if (!isset($session['translationRegistrationDashboardViews']) ||
        !is_array($session['translationRegistrationDashboardViews']))
        $session['translationRegistrationDashboardViews'] = [];
    $session['translationRegistrationDashboardViews'][(string) (int) $dashboardId] = true;
    return true;
}

function translationRegistrationForgetDashboardView(array &$session, $dashboardId)
{
    if ((!is_int($dashboardId) && !is_string($dashboardId)) ||
        !preg_match('/^[1-9][0-9]*$/D', (string) $dashboardId)) return;
    unset($session['translationRegistrationDashboardViews'][(string) (int) $dashboardId]);
}

function translationRegistrationDashboardViewGranted(array $session, $dashboardId)
{
    return (is_int($dashboardId) || is_string($dashboardId)) &&
        preg_match('/^[1-9][0-9]*$/D', (string) $dashboardId) &&
        (($session['translationRegistrationDashboardViews'][(string) (int) $dashboardId] ?? null) === true);
}

function translationRegistrationContext($link, $widgetName, &$reason = null, $dashboardMenuId = null, callable $viewCheck = null)
{
    $reason = null;
    $query = $dashboardMenuId === null
        ? 'SELECT d.Id, d.langSelectorVisible, d.deleted, d.title_header ' .
          'FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ON w.id_dashboard = d.Id ' .
          'WHERE w.name_w = ? LIMIT 1'
        : 'SELECT d.Id, d.langSelectorVisible, d.deleted, d.title_header, d.orgMenuVisible ' .
          'FROM Dashboard.Config_dashboard d WHERE d.Id = ? LIMIT 1';
    $statement = translationRegistrationStatement($link, $query,
        [$dashboardMenuId === null ? $widgetName : (string) $dashboardMenuId]);
    try {
        if ($dashboardMenuId === null) mysqli_stmt_bind_result($statement, $id, $flag, $deleted, $title);
        else mysqli_stmt_bind_result($statement, $id, $flag, $deleted, $title, $menuFlag);
        if (!mysqli_stmt_fetch($statement)) { $reason = 'dashboard_not_found'; return null; }
    } finally { mysqli_stmt_close($statement); }
    if ($flag !== 'yes') { $reason = 'language_selector_disabled'; return null; }
    if ($deleted === 'yes') { $reason = 'dashboard_deleted'; return null; }
    if ($dashboardMenuId !== null && $menuFlag !== 'yes') { $reason = 'dashboard_menu_disabled'; return null; }
    if (!$viewCheck || $viewCheck((int) $id) !== true) { $reason = 'dashboard_view_not_authorized'; return null; }
    return ['dashboardId' => (int) $id, 'title' => $title];
}

function registerMissingTranslationKeys($link, array $keys, $language)
{
    // Caller must hold the shared lock throughout this check-and-insert operation.
    $keys = normalizeTranslationRegistrationKeys($keys);
    if (!$keys) return [];
    $statement = translationRegistrationStatement($link,
        'SELECT menuText FROM Dashboard.multilanguage WHERE LOWER(TRIM(language)) = LOWER(TRIM(?)) ' .
        'AND menuText IN (' . implode(',', array_fill(0, count($keys), '?')) . ')', array_merge([$language], $keys));
    $missing = array_fill_keys($keys, true);
    try {
        mysqli_stmt_bind_result($statement, $reference);
        while (($fetched = mysqli_stmt_fetch($statement)) === true) {
            // Keep the same exact key semantics as current readers, despite SQL collation.
            unset($missing[$reference]);
        }
        if ($fetched === false) throw new \RuntimeException('Registration lookup failed');
    } finally { mysqli_stmt_close($statement); }
    if (!$missing) return [];
    $parameters = [];
    foreach ($missing as $key => $unused) { $parameters[] = (string) $key; $parameters[] = $language; }
    $statement = translationRegistrationStatement($link,
        'INSERT INTO Dashboard.multilanguage (menuText, language, translatedText) VALUES ' .
        implode(',', array_fill(0, count($missing), '(?, ?, \'\')')), $parameters);
    mysqli_stmt_close($statement);
    return array_map('strval', array_keys($missing));
}

function collectConfiguredTranslationKeys(array $widget)
{
    $keys = [];
    if (isset($widget['title_w']) && is_string($widget['title_w'])) $keys[] = $widget['title_w'];
    $rows = isset($widget['rowParameters']) && is_string($widget['rowParameters']) ? json_decode($widget['rowParameters'], true) : null;
    if (is_array($rows)) foreach ($rows as $row) {
        if (!is_array($row)) continue;
        // These fields identify metrics. Generic axis/device labels and composed series names do not.
        foreach (['metricType', 'smField'] as $field) if (isset($row[$field]) && is_string($row[$field])) $keys[] = $row[$field];
    }
    return $keys;
}

function registerConfiguredDashboardTexts($link, array $context, $language)
{
    $statement = translationRegistrationStatement($link,
        'SELECT title_w, rowParameters FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = ?', [(string) $context['dashboardId']]);
    try {
        // Buffer rows before issuing nested queries on the same connection (no mysqlnd dependency).
        if (!mysqli_stmt_store_result($statement)) throw new \RuntimeException('Metadata buffering failed');
        mysqli_stmt_bind_result($statement, $title, $rows);
        $buffer = []; $bytes = 0;
        $append = function ($key) use ($link, $language, &$buffer, &$bytes) {
            if (!is_string($key) || strlen($key) > 65535 || !preg_match('//u', $key)) return;
            if (count($buffer) >= 200 || $bytes + strlen($key) > 262144) {
                registerMissingTranslationKeys($link, $buffer, $language); $buffer = []; $bytes = 0;
            }
            $buffer[] = $key; $bytes += strlen($key);
        };
        $append($context['title']);
        foreach (dashboardFooterTranslationKeys() as $footerKey) $append($footerKey);
        while (($fetched = mysqli_stmt_fetch($statement)) === true)
            foreach (collectConfiguredTranslationKeys(['title_w' => $title, 'rowParameters' => $rows]) as $key) $append($key);
        if ($fetched === false) throw new \RuntimeException('Metadata lookup failed');
        registerMissingTranslationKeys($link, $buffer, $language);
    } finally { mysqli_stmt_close($statement); }
}
