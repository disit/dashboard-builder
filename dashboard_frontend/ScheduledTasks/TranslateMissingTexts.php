<?php
/* Internal finite worker. Only an authenticated web launch supplies a session token. */
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/../controllers/translationAuto/worker.php';
require_once __DIR__.'/../controllers/translationAuto/launchContext.php';
use S4C\TranslationAuto\Repository;
use S4C\TranslationAuto\Worker;
use function S4C\TranslationAuto\dashboardConfig;
use function S4C\TranslationAuto\settings;
$options=getopt('',['check','run-id:','session-input','retry-items:','help']);
if(isset($options['help'])){echo "Usage: php TranslateMissingTexts.php [--check | --retry-items=ID,ID]\nTranslation starts from the RootAdmin manager. No cron or stored credentials.\n";exit;}

try{
    if(!isset($options['check']) && !isset($options['retry-items']) &&
        (!isset($options['session-input']) || !ctype_digit($options['run-id'] ?? '') || (int)$options['run-id']<1))throw new RuntimeException('authenticated_manual_launch_required');
    $context=isset($options['session-input'])?\S4C\TranslationAuto\readWorkerContext(STDIN):null;
    $translationAutoCwd=getcwd();
    try{
        chdir(__DIR__.'/../management');@require_once __DIR__.'/../config.php';
    }
    finally{
        chdir($translationAutoCwd);
    }
    $base=dashboardConfig();$config=settings($base);
    if(isset($options['check'])){
        echo json_encode(['enabled'=>$config['enabled']]+($config['enabled']?(new Repository($base))->status():['busy'=>false]))."\n";
        exit;
    }
    $repository=new Repository($base);
    if(isset($options['retry-items'])){
        $ids=explode(',',$options['retry-items']);if(!$ids || count($ids)>200)throw new RuntimeException('invalid_retry_ids');
        foreach($ids as $id)if(!ctype_digit($id) || (int)$id<1)throw new RuntimeException('invalid_retry_ids');
        echo json_encode(['status'=>'retry_requested','items'=>$repository->retryItems(array_values(array_unique($ids)))])."\n";
        exit;
    }
    if(!$config['enabled']){
        echo "{\"status\":\"disabled\"}\n";
        exit;
    }
    if($context['run_id']!==(int)$options['run-id'])throw new RuntimeException('invalid_worker_context');
    $config['auth']=['access_token'=>$context['access_token'],'expires_at'=>$context['expires_at'],'source'=>'session'];
    $result=(new Worker($base,$config))->execute($context['run_id'],'manual','apply',[],$context);
    echo json_encode($result,JSON_UNESCAPED_UNICODE)."\n";
    exit(in_array($result['status'] ?? '',['completed_with_errors','waiting_auth'],true)?1:0);
} catch(Throwable $e) {
    fwrite(STDERR,"Manual translation unavailable. Check the session and server configuration; interrupted work can be resumed from the manager.\n");
    exit(1);
}
