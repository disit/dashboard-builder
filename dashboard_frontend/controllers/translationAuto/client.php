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
class Client
{
    private $config;
    private $transport;
    public function __construct(array $config, callable $transport = null) {
        $this->config=$config+settings([]);$config=$this->config;
        if (parse_url($config['api_url'], PHP_URL_SCHEME)!=='https') throw new \InvalidArgumentException('https_required');
        $this->transport=$transport ?: function($url,$headers,$body,$timeout)use($config){return self::http($url,$headers,$body,$timeout,$config);};
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
        if(!is_string($token) || $token==='' || preg_match('/[\s\x00-\x1F\x7F]/',$token))throw new ApiError('missing_session_access_token');
        if(isset($auth['expires_at']) && (int)$auth['expires_at']<=time()+5)throw new ApiError('session_token_expired',true);
        return $token;
    }
    private function checkHttp(array $reply,$prefix) {
        $status=$reply['status'];
        if($status<200 || $status>=300) throw new ApiError($prefix.'_http_'.$status,
            $status===429 || $status>=500,$reply['retry_after'] ?? 0);
    }
    public function complete($language,array $items,$timeout=null) {
        $deadline=microtime(true)+($timeout ?? $this->config['timeout_seconds']);
        {
            $remaining=$deadline-microtime(true);
            if($remaining<=0)throw new ApiError('time_budget',true);
            $token=$this->token();
            if(requestTextTokens($language,$items,$this->config)>inputContextBudget($this->config))
                throw new ApiError('context_token_budget_exceeded');
            $body=json_encode(['access_token'=>$token,'endpoint'=>'llama4-agentic-inference','params'=>[
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
            $this->checkHttp($reply,'api');
            if(strlen($reply['body'])>responseByteLimit($this->config))throw new ApiError('response_size_exceeded',true);
            if(!preg_match('//u',$reply['body']))throw new ApiError('invalid_gateway_response',true);
            $json=json_decode($reply['body'],true);
            if(!is_array($json) || isset($json['error']) || !isset($json['choices'])) throw new ApiError('invalid_gateway_response',true);
            return $json;
        }
    }
}
