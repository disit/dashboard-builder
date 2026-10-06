<?php
// serveFlag.php - small proxy to serve flag images from the application tree.
// It maps ?code=xx or ?code=en_US to dashboard_frontend/img/flagicons/{code}.png.
// Falls back to a 2-letter code and then to a public CDN.

// Security: only allow alphanumeric, dash, underscore and dot in code
$code = isset($_GET['code']) ? $_GET['code'] : ''; // get and sanitize requested code
$code = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $code);
if ($code === '') {
    http_response_code(400);
    echo 'Bad Request';
    exit;
}

$candidates = [];
$flagDir = realpath(__DIR__ . '/../img/flagicons');

if ($flagDir !== false) {
    // full code (e.g. en_US.png)
    $candidates[] = $flagDir . DIRECTORY_SEPARATOR . $code . '.png';
    // 2-letter fallback
    $candidates[] = $flagDir . DIRECTORY_SEPARATOR . strtolower(substr($code, 0, 2)) . '.png';
}

// Then look in local project img/flagicons (relative to this script)
$localDir = __DIR__ . '/img/flagicons';
$candidates[] = $localDir . '/' . $code . '.png';
$candidates[] = $localDir . '/' . strtolower(substr($code, 0, 2)) . '.png';

$found = false;
$path = '';
foreach ($candidates as $p) {
    if (file_exists($p) && is_file($p)) {
        $found = true;
        $path = $p;
        break;
    }
}

if (!$found) {
    // fallback to a remote CDN (24x18 png)
    $cc = strtolower(substr($code, 0, 2));
    $cdnMap = [
        'en' => 'us',  // or 'gb' if you prefer UK flag
        'el' => 'gr',  // Greek language -> Greece flag
        'ja' => 'jp',  // Japanese language -> Japan flag
    ];
    if (isset($cdnMap[$cc])) {
        $cc = $cdnMap[$cc];
    }
    $remote = "https://flagcdn.com/24x18/{$cc}.png";
    header('Location: ' . $remote);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime = 'image/png';
switch ($ext) {
    case 'png': $mime = 'image/png'; break;
    case 'jpg': case 'jpeg': $mime = 'image/jpeg'; break;
    case 'gif': $mime = 'image/gif'; break;
}

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
readfile($path);
exit;
?>