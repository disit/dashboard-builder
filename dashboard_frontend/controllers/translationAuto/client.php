<?php
namespace S4C\TranslationAuto;
require_once __DIR__ . '/policy.php';

class ApiError extends \RuntimeException
{
    public $retryable;
    public $retryAfter;
    public function __construct($code, $retryable = false, $retryAfter = 0) {
        parent::__construct($code); $this->retryable=$retryable; $this->retryAfter=max(0,(int)$retryAfter);
    }
}

/* Server-to-server only: no SSO bootstrap, browser cookies or refresh-token storage. */
function dedicatedAccessToken(array $config,callable $transport=null,$timeout=null) {
    $auth=$config['dedicated_auth'] ?? [];
    if(($config['auth_mode'] ?? 'session')!=='dedicated' || !is_array($auth) || !validDedicatedAuth($auth))
        throw new \RuntimeException('invalid_dedicated_auth_configuration');
    $timeout=min(10,$config['timeout_seconds'],$timeout ?? 10);
    if($timeout<=0)throw new ApiError('time_budget',true);
    $form=['grant_type'=>'password','client_id'=>$auth['client_id'],'username'=>$auth['username'],'password'=>$auth['password']];
    if(($auth['client_secret'] ?? '')!=='')$form['client_secret']=$auth['client_secret'];
    // Token-response protection is independent of a small inference output budget.
    $transport=$transport ?: function($url,$headers,$body,$seconds){return Client::http($url,$headers,$body,$seconds);};
    try{$reply=$transport($auth['token_url'],['Accept: application/json','Content-Type: application/x-www-form-urlencoded'],
        http_build_query($form,'','&',PHP_QUERY_RFC3986),$timeout);}
    catch(\Throwable $e){throw new ApiError('dedicated_auth_unavailable',true);}
    $status=$reply['status'] ?? 0;
    if(!is_int($status) || $status<200 || $status>=300)
        throw new ApiError('dedicated_auth_http_'.(int)$status,$status===429 || $status>=500);
    $body=$reply['body'] ?? null;
    if(!is_string($body) || strlen($body)>responseByteLimit(settings([])))throw new ApiError('dedicated_auth_invalid_response');
    $data=json_decode($body,true);
    $duration=is_array($data)?($data['expires_in'] ?? null):null;
    if(!is_array($data) || isset($data['error']) || !validToken($data['access_token'] ?? null) ||
        (!is_int($duration) && (!is_string($duration) || !ctype_digit($duration))) ||
        filter_var($duration,FILTER_VALIDATE_INT)===false || $duration<=0)throw new ApiError('dedicated_auth_invalid_response');
    $expires=time()+min(86400,(int)$duration);
    $parts=explode('.',$data['access_token']);
    if(count($parts)===3){
        $claims=json_decode(base64_decode(strtr($parts[1],'-_','+/'),true),true);
        if(is_array($claims) && isset($claims['exp']) && is_numeric($claims['exp']))$expires=min($expires,(int)$claims['exp']);
    }
    if($expires<=time()+5)throw new ApiError('dedicated_auth_invalid_response');
    return ['access_token'=>$data['access_token'],'expires_at'=>$expires];
}

class Client
{
    private $config;
    private $transport;
    public function __construct(array $config, callable $transport = null) {
        $this->config=$config+settings([]);$config=$this->config;
        if (parse_url($config['api_url'], PHP_URL_SCHEME)!=='https') throw new \InvalidArgumentException('https_required');
        $this->transport=$transport ?: function($url,$headers,$body,$timeout)use($config){
            return self::http($url,$headers,$body,$timeout,
                in_array('Content-Type: application/x-www-form-urlencoded',$headers,true)?null:$config);
        };
    }
    public static function http($url,array $headers,$body,$timeout,array $config=null){
        $config=$config ?? settings([]);
        if(parse_url($url,PHP_URL_SCHEME)!=='https')throw new ApiError('https_required');
        $curl=curl_init($url);$data='';$retryAfter=0;
        $bodyLimit=responseByteLimit($config);
        // One timeout covers connection and response; milliseconds preserve fractional run remainders.
        $timeoutMs=$timeout>PHP_INT_MAX/1000?PHP_INT_MAX:max(1,(int)ceil($timeout*1000));
        curl_setopt_array($curl,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_TIMEOUT_MS=>$timeoutMs,CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,
            CURLOPT_WRITEFUNCTION=>function($h,$chunk)use(&$data,$bodyLimit){if(strlen($data)>$bodyLimit-strlen($chunk))return 0;$data.=$chunk;return strlen($chunk);},
            CURLOPT_HEADERFUNCTION=>function($h,$header)use(&$retryAfter){if(stripos($header,'Retry-After:')===0){$v=trim(substr($header,12));$retryAfter=ctype_digit($v)?(int)$v:max(0,(int)strtotime($v)-time());}return strlen($header);}]);
        try{if(curl_exec($curl)===false)throw new ApiError('api_transport',true);return ['status'=>(int)curl_getinfo($curl,CURLINFO_HTTP_CODE),'body'=>$data,'retry_after'=>$retryAfter];}
        finally{curl_close($curl);}
    }
    private function token() {
        $auth=$this->config['auth'] ?? [];
        $token=$auth['access_token'] ?? null;
        $source=$this->config['auth_mode']==='dedicated'?'dedicated':'session';
        if(!validToken($token))throw new ApiError('missing_'.$source.'_access_token');
        if(isset($auth['expires_at']) && (int)$auth['expires_at']<=time()+5)throw new ApiError($source.'_token_expired',true);
        return $token;
    }
    private function renewDedicatedToken($deadline) {
        $remaining=$deadline-microtime(true);
        if($remaining<=0)throw new ApiError('time_budget',true);
        $this->config['auth']=dedicatedAccessToken($this->config,$this->transport,$remaining)+['source'=>'dedicated'];
    }
    private function checkHttp(array $reply,$prefix) {
        $status=$reply['status'];
        if($status<200 || $status>=300) throw new ApiError($prefix.'_http_'.$status,
            $status===429 || $status>=500,$reply['retry_after'] ?? 0);
    }
    public function complete($language,array $items,$timeout=null) {
        $deadline=microtime(true)+($timeout ?? $this->config['timeout_seconds']);
        $renewed=false;
        while(true){
            $remaining=$deadline-microtime(true);
            if($remaining<=0)throw new ApiError('time_budget',true);
            try{$token=$this->token();}
            catch(ApiError $e){
                if($this->config['auth_mode']!=='dedicated' || $renewed ||
                    !in_array($e->getMessage(),['missing_dedicated_access_token','dedicated_token_expired'],true))throw $e;
                $renewed=true;$this->renewDedicatedToken($deadline);continue;
            }
            if(requestTextTokens($language,$items,$this->config)>inputContextBudget($this->config))
                throw new ApiError('context_token_budget_exceeded');
            $body=json_encode(['access_token'=>$token,'endpoint'=>$this->config['api_endpoint'],'params'=>[
                'messages'=>[
                    ['role'=>'system','content'=>[['type'=>'text','text'=>systemPrompt($this->config['glossary'] ?? [])]]],
                    ['role'=>'user','content'=>[['type'=>'text','text'=>json_encode(['target_language'=>$language,'items'=>$items],JSON_UNESCAPED_UNICODE)]]]
                ],'tools'=>[],'tool_choice'=>'none','temperature'=>0,'max_tokens'=>$this->config['output_tokens']
            ]],JSON_UNESCAPED_UNICODE);
            $remaining=$deadline-microtime(true);
            if($remaining<=0)throw new ApiError('time_budget',true);
            $reply=call_user_func($this->transport,$this->config['api_url'],[
                'Accept: application/json','Content-Type: application/json','Authorization: Bearer '.$token
            ],$body,$remaining);
            if($reply['status']===401 && $this->config['auth_mode']==='dedicated' && !$renewed){
                $renewed=true;$this->renewDedicatedToken($deadline);continue;
            }
            $this->checkHttp($reply,'api');
            if(strlen($reply['body'])>responseByteLimit($this->config))throw new ApiError('response_size_exceeded',true);
            if(!preg_match('//u',$reply['body']))throw new ApiError('invalid_gateway_response',true);
            $json=json_decode($reply['body'],true);
            if(!is_array($json) || isset($json['error']) || !isset($json['choices'])) throw new ApiError('invalid_gateway_response',true);
            return $json;
        }
    }
}
