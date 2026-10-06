<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version. */

include '../config.php';

header('Content-Type: application/json; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('result' => 'ko', 'error' => 'method_not_allowed'));
    exit();
}

$lang = isset($_POST['lang_dash']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['lang_dash']) : '';
$allowedLanguages = array();

if (isset($localizations)) {
    $localizationsConfig = json_decode($localizations, true);
    if (isset($localizationsConfig['languages']) && is_array($localizationsConfig['languages'])) {
        foreach ($localizationsConfig['languages'] as $languageConfig) {
            if (isset($languageConfig['code'])) {
                $allowedLanguages[] = $languageConfig['code'];
            }
        }
    }
}

if ($lang === '' || (!empty($allowedLanguages) && !in_array($lang, $allowedLanguages, true))) {
    http_response_code(400);
    echo json_encode(array('result' => 'ko', 'error' => 'invalid_language'));
    exit();
}

$_SESSION['lang_dash'] = $lang;
setcookie('lang_dash', $lang, time() + 30 * 24 * 60 * 60, "/");

echo json_encode(array('result' => 'ok', 'lang_dash' => $lang));
?>
