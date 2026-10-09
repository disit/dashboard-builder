<?php
/* Shared read-only lookup for widget display texts. No registration or configuration writes. */

function resolveWidgetTranslationLanguage(array $session, array $cookies, $fallback)
{
    foreach ([$session['lang_dash'] ?? null, $cookies['lang_dash'] ?? null,
        $session['lang'] ?? null, $cookies['lang'] ?? null, $fallback] as $language) {
        if (is_string($language) && preg_match('/^[a-zA-Z0-9_.@-]{1,45}$/D', trim($language))) {
            return trim($language);
        }
    }
    return '';
}

function normalizeWidgetTranslationKeys($keys)
{
    if (!is_array($keys) || $keys !== array_values($keys) || count($keys) > 200) {
        throw new \InvalidArgumentException('Invalid translation keys');
    }
    $unique = [];
    $bytes = 0;
    foreach ($keys as $key) {
        if (!is_string($key) || !preg_match('//u', $key)) {
            throw new \InvalidArgumentException('Invalid translation text');
        }
        if (trim($key) === '' || isset($unique[$key])) continue;
        $bytes += strlen($key);
        if ($bytes > 262144) throw new \InvalidArgumentException('Translation batch too large');
        $unique[$key] = $key;
    }
    return array_values($unique);
}

function loadWidgetTextTranslations($link, $widgetName, array $keys, $language)
{
    $response = ['detail' => 'Ko', 'dashboardId' => null, 'enabled' => false,
        'language' => $language, 'translations' => []];
    $statement = null;
    try {
        $statement = mysqli_prepare($link, 'SELECT w.id_dashboard, d.langSelectorVisible ' .
            'FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ' .
            'ON w.id_dashboard = d.Id WHERE w.name_w = ? LIMIT 1');
        if (!$statement) {
            if (mysqli_errno($link) === 1054 && stripos(mysqli_error($link), 'langSelectorVisible') !== false) {
                $response['detail'] = 'Ok';
            }
            return $response;
        }
        if (!mysqli_stmt_bind_param($statement, 's', $widgetName) || !mysqli_stmt_execute($statement)) return $response;
        mysqli_stmt_bind_result($statement, $dashboardId, $flag);
        if (!mysqli_stmt_fetch($statement)) return $response;
        mysqli_stmt_close($statement);
        $statement = null;
        $response['detail'] = 'Ok';
        $response['dashboardId'] = (int) $dashboardId;
        $response['enabled'] = $flag === 'yes' && $language !== '';
        if (!$response['enabled'] || !$keys) return $response;

        $statement = mysqli_prepare($link, 'SELECT menuText, translatedText FROM Dashboard.multilanguage ' .
            'WHERE LOWER(TRIM(language)) = LOWER(TRIM(?)) AND TRIM(translatedText) <> \'\' ' .
            'AND menuText IN (' . implode(',', array_fill(0, count($keys), '?')) . ') ORDER BY id ASC');
        if (!$statement) {
            $response['detail'] = 'Ko';
            return $response;
        }
        $parameters = array_merge([$language], $keys);
        if (!mysqli_stmt_bind_param($statement, str_repeat('s', count($parameters)), ...$parameters) ||
            !mysqli_stmt_execute($statement)) {
            $response['detail'] = 'Ko';
            return $response;
        }
        mysqli_stmt_bind_result($statement, $reference, $translated);
        $requested = array_fill_keys($keys, true);
        while (($fetched = mysqli_stmt_fetch($statement)) === true) {
            // Recheck exactly: utf8mb4_unicode_ci can match case/accent variants in SQL.
            if (isset($requested[$reference]) && is_string($translated) && trim($translated) !== '' && preg_match('//u', $translated)) {
                $response['translations'][$reference] = $translated;
            }
        }
        if ($fetched === false) {
            $response['detail'] = 'Ko';
            $response['translations'] = [];
        }
    } catch (\Throwable $exception) {
        $response['translations'] = [];
        $response['detail'] = 'Ko';
        if ((int) $exception->getCode() === 1054 && stripos($exception->getMessage(), 'langSelectorVisible') !== false) {
            $response['detail'] = 'Ok';
            $response['enabled'] = false;
        }
        error_log('Widget display translation lookup failed (code ' . (int) $exception->getCode() . ')');
    } finally {
        if ($statement) mysqli_stmt_close($statement);
    }
    return $response;
}

function encodeWidgetTextTranslations(array $response)
{
    $response['translations'] = (object) $response['translations'];
    // Inputs and DB translations are validated as UTF-8 above; no PHP 7.2-only flag is needed.
    return json_encode($response, JSON_UNESCAPED_UNICODE);
}
