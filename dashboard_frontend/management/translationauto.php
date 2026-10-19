<?php
require_once __DIR__.'/../controllers/translationAuto/http.php';
use function S4C\TranslationAuto\dashboardConfig;
use function S4C\TranslationAuto\settings;
use function S4C\TranslationAuto\authorize;
use function S4C\TranslationAuto\handleRequest;
use S4C\TranslationAuto\Repository;
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
try {
    // Root config.php expects a management-directory working directory.
    // INI parser diagnostics may contain source values; keep them out of responses.
    $translationAutoCwd=getcwd();
    try{chdir(__DIR__);@require_once __DIR__.'/../config.php';}
    finally{chdir($translationAutoCwd);}
    $base=dashboardConfig();
    session_start();
    $identity=authorize($_SESSION,$_SERVER);

    if(!$identity){session_write_close();http_response_code(403);echo '{"error":"authorization_required"}';exit;}
    if((int)($_SERVER['CONTENT_LENGTH'] ?? 0)>4096)throw new InvalidArgumentException('request_too_large');
    $raw=file_get_contents('php://input',false,null,0,4097);
    if(strlen($raw)>4096)throw new InvalidArgumentException('request_too_large');
    $body=json_decode($raw,true);
    if(!is_array($body))throw new InvalidArgumentException('invalid_request');
    $config=settings($base);
    // Disabled installations can open the manager before applying the optional migration.
    if(!$config['enabled'] && ($body['action'] ?? '')==='status'){session_abort();echo '{"enabled":false,"busy":false,"run":null}';exit;}
    if(in_array($body['action'] ?? '',['status','missing'],true))session_abort();
    $provider=function()use($base){$token=\S4C\TranslationAuto\sessionAccessToken($_SESSION,$base);if(!session_write_close())throw new RuntimeException('session_save_failed');return $token;};
    echo json_encode(handleRequest(new Repository($base),$config,$identity,$body,null,$provider),JSON_UNESCAPED_UNICODE);
} catch(InvalidArgumentException $e) {
    http_response_code(400);echo json_encode(['error'=>$e->getMessage()]);
} catch(Throwable $e) {
    http_response_code(503);
    // Do not expose SQL, credentials, paths, or gateway bodies in HTTP responses/logs.
    error_log('Automatic translation request failed');
    echo '{"error":"translation_service_unavailable"}';
} finally {if(session_status()===PHP_SESSION_ACTIVE)session_write_close();}
