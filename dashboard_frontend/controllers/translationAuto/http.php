<?php
/* Shared HTTP controller and worker launcher for Linux and Windows. */
namespace S4C\TranslationAuto;
require_once __DIR__.'/repository.php';
require_once __DIR__.'/client.php';

function authorize(array $session,array $server) {
    $identity=\translationRegistrationIdentity($session);
    if(!$identity || $identity['role']!=='RootAdmin' || !\translationRegistrationRequestAllowed($server) ||
        !is_string($session['translationAutoCsrf'] ?? null) ||
        !is_string($server['HTTP_X_S4C_TRANSLATION_CSRF'] ?? null) ||
        !hash_equals($session['translationAutoCsrf'],$server['HTTP_X_S4C_TRANSLATION_CSRF']))return null;
    return $identity;
}
function launchWorker($id,array $config,array $context) {
    if(PHP_OS_FAMILY==='Windows')return launchWindowsWorker($id,$config,$context);
    if( !is_file($config['php_binary']) || !is_executable($config['php_binary']) ||
        !is_callable('proc_open') || in_array('proc_open',array_map('trim',explode(',',ini_get('disable_functions'))),true))
        throw new \RuntimeException('worker_launcher_unavailable');
    $script=dirname(__DIR__,2).'/ScheduledTasks/TranslateMissingTexts.php';
    // FD 3 is a private anonymous pipe, inherited as stdin by the detached worker.
    // No token in command arguments, environment variables, files or database.
    $command='nohup '.escapeshellarg($config['php_binary']).' '.escapeshellarg($script).
        ' --run-id='.(int)$id.' --session-input <&3 3<&- >/dev/null 2>&1 & echo $!';
    $packet=json_encode($context)."\n";
    if(strlen($packet)>20000)throw new \RuntimeException('invalid_worker_context');
    $process=proc_open($command,[0=>['file','/dev/null','r'],1=>['pipe','w'],2=>['file','/dev/null','a'],3=>['pipe','r']],$pipes);
    if(!is_resource($process))throw new \RuntimeException('worker_launch_failed');
    $offset=0;$deadline=microtime(true)+5;stream_set_blocking($pipes[3],false);stream_set_blocking($pipes[1],false);
    try{
        while($offset<strlen($packet)){
            $left=$deadline-microtime(true);if($left<=0)throw new \RuntimeException('worker_launch_failed');
            $read=$except=[];$write=[$pipes[3]];
            if(stream_select($read,$write,$except,(int)$left,(int)(($left-(int)$left)*1000000))===false)throw new \RuntimeException('worker_launch_failed');
            if($write){$n=@fwrite($pipes[3],substr($packet,$offset,4096));if(!$n)throw new \RuntimeException('worker_launch_failed');$offset+=$n;}
        }
        fclose($pipes[3]);unset($pipes[3]);$pid='';
        while(!feof($pipes[1])){
            $left=$deadline-microtime(true);if($left<=0)throw new \RuntimeException('worker_launch_failed');
            $read=[$pipes[1]];$write=$except=[];
            if(stream_select($read,$write,$except,(int)$left,(int)(($left-(int)$left)*1000000))===false)throw new \RuntimeException('worker_launch_failed');
            if($read)$pid.=fread($pipes[1],128);if(strlen($pid)>128)throw new \RuntimeException('worker_launch_failed');
        }
        fclose($pipes[1]);unset($pipes[1]);$pid=trim($pid);
        do{$state=proc_get_status($process);if(!$state['running'])break;if(microtime(true)>$deadline)throw new \RuntimeException('worker_launch_failed');usleep(1000);}while(true);
        $code=$state['exitcode'];$closed=proc_close($process);$process=null;if($code===-1)$code=$closed;
        if($code!==0 || !preg_match('/^[0-9]+$/D',$pid))throw new \RuntimeException('worker_launch_failed');
    }finally{foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);if(is_resource($process)){proc_terminate($process);proc_close($process);}}
}

/* On Windows a short PowerShell relay starts PHP with .NET, then exits.
 * The context travels only through anonymous stdin pipes. Output goes to NUL. */
function launchWindowsWorker($id,array $config,array $context) {
    $script=dirname(__DIR__,2).'/ScheduledTasks/TranslateMissingTexts.php';
    $powershell=rtrim(getenv('SystemRoot') ?: 'C:/Windows','/\\').'/System32/WindowsPowerShell/v1.0/powershell.exe';
    if(!is_file($config['php_binary']) || !is_executable($config['php_binary']) || !is_file($script) ||
        !is_file($powershell) || !is_callable('proc_open') || !is_callable('iconv') ||
        in_array('proc_open',array_map('trim',explode(',',ini_get('disable_functions'))),true))
        throw new \RuntimeException('worker_launcher_unavailable');
    $packet=json_encode($context)."\n";
    if(json_last_error()!==JSON_ERROR_NONE || strlen($packet)>20000)throw new \RuntimeException('invalid_worker_context');
    $literal=function($text){return "'".str_replace("'","''",$text)."'";};
    $relay=<<<'POWERSHELL'
$ErrorActionPreference='Stop'
$worker=$null
try {
    [Console]::InputEncoding=New-Object System.Text.UTF8Encoding($false)
    $packet=[Console]::In.ReadToEnd()
    $info=New-Object System.Diagnostics.ProcessStartInfo
POWERSHELL;
    $relay.="\n    \$info.FileName=".$literal($config['php_binary']).
        "\n    \$info.Arguments=".$literal('"'.$script.'" --run-id='.(int)$id.' --session-input').
        "\n    \$info.WorkingDirectory=".$literal(dirname(__DIR__,2));
    $relay.=<<<'POWERSHELL'

    $info.UseShellExecute=$false
    $info.CreateNoWindow=$true
    $info.RedirectStandardInput=$true
    $worker=[System.Diagnostics.Process]::Start($info)
    # Write exact UTF-8 bytes: the default .NET writer prepends a BOM.
    $bytes=[System.Text.Encoding]::UTF8.GetBytes($packet)
    $inputPipe=$worker.StandardInput.BaseStream
    $inputPipe.Write($bytes,0,$bytes.Length)
    $inputPipe.Close()
    exit 0
} catch { exit 1 }
finally { if($null -ne $worker){$worker.Dispose()} }
POWERSHELL;
    // EncodedCommand contains paths and run ID only, never the token or launch proof.
    $encoded=iconv('UTF-8','UTF-16LE',$relay);
    if($encoded===false)throw new \RuntimeException('worker_launcher_unavailable');
    $command='"'.$powershell.'" -NoLogo -NoProfile -NonInteractive -EncodedCommand '.base64_encode($encoded);
    $process=@proc_open($command,[0=>['pipe','r'],1=>['file','NUL','w'],2=>['file','NUL','w']],$pipes,
        dirname(__DIR__,2),null,['bypass_shell'=>true,'suppress_errors'=>true,'blocking_pipes'=>false]);
    if(!is_resource($process))throw new \RuntimeException('worker_launch_failed');
    $offset=0;$deadline=microtime(true)+5;stream_set_blocking($pipes[0],false);
    try {
        while($offset<strlen($packet)) {
            if(microtime(true)>$deadline)throw new \RuntimeException('worker_launch_failed');
            $state=proc_get_status($process);
            if(!$state['running'])throw new \RuntimeException('worker_launch_failed');
            $written=@fwrite($pipes[0],substr($packet,$offset,1024));
            if($written===false)throw new \RuntimeException('worker_launch_failed');
            if($written===0){usleep(1000);continue;}
            $offset+=$written;
        }
        fclose($pipes[0]);unset($pipes[0]);
        do {
            $state=proc_get_status($process);if(!$state['running'])break;
            if(microtime(true)>$deadline)throw new \RuntimeException('worker_launch_failed');
            usleep(1000);
        } while(true);
        $code=$state['exitcode'];$closed=proc_close($process);$process=null;if($code===-1)$code=$closed;
        if($code!==0)throw new \RuntimeException('worker_launch_failed');
    } finally {
        foreach($pipes as $pipe)if(is_resource($pipe))fclose($pipe);
        if(is_resource($process)){proc_terminate($process);proc_close($process);}
    }
}

function handleRequest(Repository $repository,array $config,array $identity,array $body,callable $launcher=null,callable $tokenProvider=null) {
    $action=$body['action'] ?? '';
    if($action==='missing')return ['missing'=>$repository->missingTranslations()];
    if($action==='status')return ['enabled'=>$config['enabled']]+$repository->status();
    if(!in_array($action,['start','reset'],true) || !$config['enabled'])throw new \InvalidArgumentException('automatic_translation_disabled_or_invalid_action');
    if($action==='reset'){
        if(!is_int($body['run_id'] ?? null) || $body['run_id']<1)throw new \InvalidArgumentException('invalid_request');
        return ['enabled'=>true,'reset'=>$repository->resetPendingRun($body['run_id'])]+$repository->status();
    }
    $mode=$body['mode'] ?? 'apply';
    if($mode!=='apply' || !is_string($body['request_key'] ?? null) || !preg_match('/^[a-f0-9]{32}$/D',$body['request_key']))throw new \InvalidArgumentException('invalid_request');
    $languages=$body['languages'] ?? [];$map=$repository->languages($config);
    if(!is_array($languages) || count($languages)>count($map) || array_values($languages)!==$languages)throw new \InvalidArgumentException('invalid_languages');
    foreach($languages as $language)if(!is_string($language) || !isset($map[$language]))throw new \InvalidArgumentException('invalid_languages');
    $reserved=$repository->claimManualLaunch($identity['username'],$mode,$languages,$config,$body['request_key']);
    if(!$reserved['busy']){
        try{
            if(!$tokenProvider)throw new \RuntimeException('session_authentication_required');
            $token=$tokenProvider();
            if(!validToken($token['access_token'] ?? null) || !is_int($token['expires_at'] ?? null) || $token['expires_at']<=time()+30)throw new \RuntimeException('session_token_unavailable');
            $context=['run_id'=>(int)$reserved['run']['id'],'launch_secret'=>$reserved['launch_secret'],'username'=>$identity['username'],'access_token'=>$token['access_token'],'expires_at'=>$token['expires_at']];
            ($launcher ?: __NAMESPACE__.'\\launchWorker')($context['run_id'],$config,$context);
        }catch(\Throwable $e){
            $error=$e instanceof ApiError && preg_match('/^dedicated_auth_(?:unavailable|invalid_response|http_[0-9]+)$/D',$e->getMessage())
                ?$e->getMessage():'worker_launch_failed';
            $repository->launchFailed($reserved['run']['id'],$reserved['launch_secret'],$error);
            throw new \RuntimeException('manual_translation_start_failed');
        }
    }
    return ['enabled'=>true,'already_running'=>$reserved['busy'],'resumed'=>$reserved['resumed'] ?? false]+$repository->status();
}

function launchAccessToken(array &$session,array $base,array $config,callable $transport=null) {
    if($config['auth_mode']==='dedicated'){
        // Release the local session before remote I/O; never replace its user or refresh token.
        if(!session_write_close())throw new \RuntimeException('session_save_failed');
        return dedicatedAccessToken($config,$transport);
    }
    $token=sessionAccessToken($session,$base);
    if(!session_write_close())throw new \RuntimeException('session_save_failed');
    return $token;
}

/* Refresh with the same SSO library/client as controllers/getAccessToken.php.
 * The caller holds the session lock and persists any rotation before launching. */
function sessionAccessToken(array &$session,array $base){
    if(session_status()!==PHP_SESSION_ACTIVE || !is_string($session['loggedUsername'] ?? null) || $session['loggedUsername']==='' ||
        !validToken($session['refreshToken'] ?? null))throw new \RuntimeException('session_authentication_required');
    if(!is_string($base['ssoTokenEndpoint'] ?? null) || parse_url($base['ssoTokenEndpoint'],PHP_URL_SCHEME)!=='https' ||
        !is_string($base['ssoClientId'] ?? null) || $base['ssoClientId']==='')throw new \RuntimeException('invalid_sso_configuration');
    require_once dirname(__DIR__,2).'/sso/autoload.php';
    $oidc=new \Jumbojett\OpenIDConnectClient($base['ssoEndpoint'] ?? null,$base['ssoClientId'],$base['ssoClientSecret'] ?? '');
    $oidc->providerConfigParam(['token_endpoint'=>$base['ssoTokenEndpoint']]);
    $oidc->setTimeout(10);
    // The installed client accesses refresh_token before validating the JSON.
    // Keep malformed provider responses from leaking warning text to the browser.
    set_error_handler(function(){return true;},E_WARNING|E_NOTICE);
    try{$reply=$oidc->refreshToken($session['refreshToken']);}
    catch(\Throwable $e){throw new \RuntimeException('session_token_unavailable');}
    finally{restore_error_handler();}
    $status=$oidc->getResponseCode();
    if($status<200 || $status>=300)throw new \RuntimeException('token_http_'.(int)$status);
    if(!is_object($reply) || isset($reply->error))throw new \RuntimeException('invalid_token_response');
    if(isset($reply->refresh_token)){
        if(!validToken($reply->refresh_token))throw new \RuntimeException('invalid_token_response');
        $session['refreshToken']=$reply->refresh_token;
    }
    if(!validToken($reply->access_token ?? null) ||
        (!is_int($reply->expires_in ?? null) && (!is_string($reply->expires_in ?? null) || !ctype_digit($reply->expires_in))) ||
        $reply->expires_in<=0)throw new \RuntimeException('invalid_token_response');
    $expires=time()+min(86400,(int)$reply->expires_in);
    $parts=explode('.',$reply->access_token);
    if(count($parts)===3){$claims=json_decode(base64_decode(strtr($parts[1],'-_','+/'),true),true);if(is_array($claims) && isset($claims['exp']) && is_numeric($claims['exp']))$expires=min($expires,(int)$claims['exp']);}
    if($expires<=time())throw new \RuntimeException('invalid_token_response');
    return ['access_token'=>$reply->access_token,'expires_at'=>$expires];
}
