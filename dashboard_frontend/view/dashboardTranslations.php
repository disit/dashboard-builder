<?php
// Shared by the EC/Gea views; reads translations only, never registers missing keys.
require_once __DIR__ . '/../controllers/dashboardFooterTranslations.php';
function loadDashboardViewTranslations($link, $dashboardId, $dashboardParams, $language, $selectorVisible)
{
    if (empty($language) || $selectorVisible !== 'yes' || !is_array($dashboardParams)) {
        return array();
    }

    try {
        $keys = array();
        $addKey = function ($text) use (&$keys) {
            if (is_scalar($text) && (string) $text !== '') {
                $keys[(string) $text] = true;
            }
        };
        foreach (dashboardFooterTranslationKeys() as $footerKey) $addKey($footerKey);
        if (isset($dashboardParams['title_header'])) {
            $addKey($dashboardParams['title_header']);
        }

        $dashIdEsc = mysqli_real_escape_string($link, (string) $dashboardId);
        $widgetsResult = mysqli_query($link, "SELECT title_w, serviceUri FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '" . $dashIdEsc . "'");
        if (!$widgetsResult) {
            return array();
        }
        while ($widget = mysqli_fetch_assoc($widgetsResult)) {
            if (isset($widget['title_w'])) {
                $addKey($widget['title_w']);
            }
            $serviceUri = isset($widget['serviceUri']) && is_string($widget['serviceUri'])
                ? json_decode($widget['serviceUri'], true) : null;
            foreach (array('firstAxis', 'secondAxis') as $axis) {
                if (isset($serviceUri[$axis]['labels']) && is_array($serviceUri[$axis]['labels'])) {
                    foreach ($serviceUri[$axis]['labels'] as $label) {
                        $addKey($label);
                    }
                }
            }
        }
        if (empty($keys)) {
            return array();
        }

        // Legacy views remove CR/LF anywhere and PHP trim characters at the edges.
        // SQL removes the remaining control characters everywhere to select a safe
        // superset; the exact legacy PHP key is checked below (internal tabs survive).
        $sqlKeys = array();
        foreach ($keys as $key => $unused) {
            $sqlKey = trim(str_replace(array("\r", "\n", "\t", "\x0B", "\0"), '', (string) $key), ' ');
            $sqlKeys[$sqlKey] = "'" . mysqli_real_escape_string($link, $sqlKey) . "'";
        }
        $sqlKeyExpression = "TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(menuText, CHAR(13), ''), CHAR(10), ''), CHAR(9), ''), CHAR(11), ''), CHAR(0), ''))";
        $langEsc = mysqli_real_escape_string($link, $language);
        $query = "SELECT menuText, translatedText FROM multilanguage WHERE LOWER(TRIM(language)) = LOWER(TRIM('" . $langEsc . "')) AND " . $sqlKeyExpression . " IN (" . implode(',', $sqlKeys) . ")";
        $result = mysqli_query($link, $query);
        $translations = array();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $key = trim(preg_replace('/\r\n|\r|\n/', '', (string) $row['menuText']));
                if (isset($keys[$key])) {
                    $translations[$key] = trim(preg_replace('/\r\n|\r|\n/', '', (string) $row['translatedText']));
                }
            }
        }
        return $translations;
    } catch (\Throwable $e) {
        // Translation lookup failures must not prevent the dashboard from loading.
        return array();
    }
}
