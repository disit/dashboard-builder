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
$translationAutoLogContext=[];
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
    $translationAutoLogContext['requested_by']=$identity['username'];
    if((int)($_SERVER['CONTENT_LENGTH'] ?? 0)>4096)throw new InvalidArgumentException('request_too_large');
    $raw=file_get_contents('php://input',false,null,0,4097);
    if(strlen($raw)>4096)throw new InvalidArgumentException('request_too_large');
    $body=json_decode($raw,true);
    if(!is_array($body))throw new InvalidArgumentException('invalid_request');
    $translationAutoLogContext['action']=in_array($body['action'] ?? null,['status','missing','start','reset'],true)?$body['action']:'unknown';
    $config=settings($base);
    $translationAutoLogContext['auth_mode']=$config['auth_mode'];
    // Disabled installations can open the manager before applying the optional migration.
    if(!$config['enabled'] && ($body['action'] ?? '')==='status'){session_abort();echo '{"enabled":false,"busy":false,"run":null}';exit;}
    if(in_array($body['action'] ?? '',['status','missing'],true))session_abort();
    $provider=function()use($base,$config){return \S4C\TranslationAuto\launchAccessToken($_SESSION,$base,$config);};
    echo json_encode(handleRequest(new Repository($base),$config,$identity,$body,null,$provider),JSON_UNESCAPED_UNICODE);
} catch(InvalidArgumentException $e) {
    \S4C\TranslationAuto\logFailure('request',$e,$translationAutoLogContext);
    http_response_code(400);echo json_encode(['error'=>$e->getMessage()]);
} catch(Throwable $e) {
    http_response_code(503);
    \S4C\TranslationAuto\logFailure('request',$e,$translationAutoLogContext);
    echo '{"error":"translation_service_unavailable"}';
} finally {if(session_status()===PHP_SESSION_ACTIVE)session_write_close();}
