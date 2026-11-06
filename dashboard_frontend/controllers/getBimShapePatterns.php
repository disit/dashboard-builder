<?php

include '../config.php';

header('Content-Type: application/json');

function getDefaultBimShapePatternConfig($patternCode) {
    $defaults = array(
        1 => array('type' => 'lines', 'spacing' => 8, 'angle' => 45, 'strokeColor' => 'rgba(0,0,0,0.85)', 'strokeWidth' => 2),
        2 => array('type' => 'dots', 'spacing' => 10, 'fillColor' => 'rgba(0,0,0,0.85)', 'radius' => 2),
        3 => array('type' => 'trees', 'spacing' => 16, 'strokeColor' => 'rgba(0,0,0,0.85)', 'strokeWidth' => 2.3, 'size' => 10),
        4 => array('type' => 'lines', 'spacing' => 8, 'angle' => 90, 'strokeColor' => 'rgba(0,0,0,0.85)', 'strokeWidth' => 2),
        5 => array('type' => 'triangles', 'spacing' => 12, 'strokeColor' => 'rgba(0,0,0,0.85)', 'strokeWidth' => 2.3, 'size' => 8),
        6 => array('type' => 'lines', 'spacing' => 8, 'angle' => 0, 'strokeColor' => 'rgba(0,0,0,0.85)', 'strokeWidth' => 2.4)
    );
    return isset($defaults[$patternCode]) ? $defaults[$patternCode] : null;
}

function clampBimShapePatternNumber($value, $min, $max, $fallback) {
    if (!is_numeric($value)) {
        return $fallback;
    }
    $value = floatval($value);
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

function isValidBimShapePatternColor($color) {
    if (!is_string($color)) {
        return false;
    }
    $color = trim($color);
    if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color)) {
        return true;
    }
    if (!preg_match('/^rgba?\((.+)\)$/i', $color, $matches)) {
        return false;
    }
    $parts = array_map('trim', explode(',', $matches[1]));
    if (count($parts) !== 3 && count($parts) !== 4) {
        return false;
    }
    for ($i = 0; $i < 3; $i++) {
        if (!is_numeric($parts[$i])) {
            return false;
        }
        $channel = intval($parts[$i]);
        if ($channel < 0 || $channel > 255) {
            return false;
        }
    }
    if (count($parts) === 4) {
        if (!is_numeric($parts[3])) {
            return false;
        }
        $alpha = floatval($parts[3]);
        if ($alpha < 0 || $alpha > 1) {
            return false;
        }
    }
    return true;
}

function normalizeBimShapePatternConfig($patternCode, $configJson, &$warning) {
    $fallback = getDefaultBimShapePatternConfig($patternCode);
    if ($fallback === null) {
        $warning = 'unsupported patternCode';
        return null;
    }
    if ($configJson === null || (is_string($configJson) && trim($configJson) === '')) {
        return $fallback;
    }

    if (!is_string($configJson)) {
        $warning = 'invalid JSON, default config used';
        return $fallback;
    }

    $decoded = json_decode($configJson, true);
    if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
        $warning = 'invalid JSON, default config used';
        return $fallback;
    }

    if (isset($decoded['type']) && !is_string($decoded['type'])) {
        $warning = 'unsupported pattern type, default config used';
        return $fallback;
    }
    $type = isset($decoded['type']) ? strtolower(trim($decoded['type'])) : $fallback['type'];
    if (!in_array($type, array('lines', 'dots', 'triangles', 'trees'))) {
        $warning = 'unsupported pattern type, default config used';
        return $fallback;
    }

    // A configured type may differ from the default type associated with this code.
    // Use defaults for that type so missing fields never become undefined/null.
    if ($type !== $fallback['type']) {
        $typeCodes = array('lines' => 1, 'dots' => 2, 'triangles' => 5, 'trees' => 3);
        $fallback = getDefaultBimShapePatternConfig($typeCodes[$type]);
    }

    $normalized = array(
        'type' => $type,
        'spacing' => clampBimShapePatternNumber(isset($decoded['spacing']) ? $decoded['spacing'] : null, 4, 64, $fallback['spacing'])
    );

    if ($type === 'lines') {
        $strokeColor = isset($decoded['strokeColor']) && isValidBimShapePatternColor($decoded['strokeColor']) ? trim($decoded['strokeColor']) : $fallback['strokeColor'];
        $normalized['angle'] = clampBimShapePatternNumber(isset($decoded['angle']) ? $decoded['angle'] : null, -180, 180, $fallback['angle']);
        $normalized['strokeColor'] = $strokeColor;
        $normalized['strokeWidth'] = clampBimShapePatternNumber(isset($decoded['strokeWidth']) ? $decoded['strokeWidth'] : null, 0.5, 8, $fallback['strokeWidth']);
    } else if ($type === 'dots') {
        $fillColor = isset($decoded['fillColor']) && isValidBimShapePatternColor($decoded['fillColor']) ? trim($decoded['fillColor']) : $fallback['fillColor'];
        $normalized['fillColor'] = $fillColor;
        $normalized['radius'] = clampBimShapePatternNumber(isset($decoded['radius']) ? $decoded['radius'] : null, 0.5, 12, $fallback['radius']);
    } else {
        $strokeColor = isset($decoded['strokeColor']) && isValidBimShapePatternColor($decoded['strokeColor']) ? trim($decoded['strokeColor']) : $fallback['strokeColor'];
        $normalized['strokeColor'] = $strokeColor;
        $normalized['strokeWidth'] = clampBimShapePatternNumber(isset($decoded['strokeWidth']) ? $decoded['strokeWidth'] : null, 0.5, 8, $fallback['strokeWidth']);
        $normalized['size'] = clampBimShapePatternNumber(isset($decoded['size']) ? $decoded['size'] : null, 4, 32, $fallback['size']);
    }

    return $normalized;
}

$response = array('status' => 'error', 'patterns' => array(), 'warnings' => array());
$link = null;
$response['details'] = 'Database connection failed';

try {
    $link = @mysqli_connect($host, $username, $password);
    if ($link && @mysqli_select_db($link, $dbname) && @mysqli_set_charset($link, 'utf8')) {
        $response['details'] = 'Query BIM shape patterns KO';
        $query = "SELECT patternCode, patternKey, patternLabel, description, patternConfig FROM Dashboard.BimShapePatterns WHERE enabled = 1 ORDER BY sortOrder ASC, patternCode ASC";
        $result = @mysqli_query($link, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $patternCode = intval($row['patternCode']);
                $warning = null;
                $config = normalizeBimShapePatternConfig($patternCode, $row['patternConfig'], $warning);
                if ($config === null) {
                    continue;
                }
                if ($warning !== null) {
                    $response['warnings'][] = array('patternCode' => $patternCode, 'warning' => $warning);
                }
                $response['patterns'][] = array(
                    'patternCode' => $patternCode,
                    'patternKey' => $row['patternKey'],
                    'patternLabel' => $row['patternLabel'],
                    'description' => $row['description'],
                    'config' => $config
                );
            }
            $response['status'] = 'ok';
            unset($response['details']);
        }
    }
} catch (mysqli_sql_exception $exception) {
    // PHP 8 mysqli can throw instead of returning false. Preserve the JSON contract
    // so the map can fall back to its built-in patterns, including during migration.
} finally {
    if ($link) {
        mysqli_close($link);
    }
}

echo json_encode($response);
