<?php
namespace S4C\TranslationAuto;
use RuntimeException;
require_once __DIR__.'/policy.php';
function readWorkerContext($input){
    stream_set_blocking($input,false);$data='';$deadline=microtime(true)+5;
    while(!feof($input) && strpos($data,"\n")===false){
        $left=$deadline-microtime(true);if($left<=0)throw new RuntimeException('invalid_worker_context');
        $read=[$input];$write=$except=[];$seconds=(int)$left;$micros=(int)(($left-$seconds)*1000000);
        if(stream_select($read,$write,$except,$seconds,$micros)===false)throw new RuntimeException('invalid_worker_context');
        if($read)$data.=fread($input,4096);if(strlen($data)>20000)throw new RuntimeException('invalid_worker_context');
    }
    $context=json_decode($data,true);
    if(!is_array($context) || count($context)!==5 || !is_int($context['run_id'] ?? null) || $context['run_id']<1 ||
        !is_string($context['launch_secret'] ?? null) || !preg_match('/^[a-f0-9]{64}$/D',$context['launch_secret']) ||
        !is_string($context['username'] ?? null) || $context['username']==='' || strlen($context['username'])>255 ||
        !validToken($context['access_token'] ?? null) ||
        !is_int($context['expires_at'] ?? null) || $context['expires_at']<=time()+5)throw new RuntimeException('invalid_worker_context');
    return $context;
}
