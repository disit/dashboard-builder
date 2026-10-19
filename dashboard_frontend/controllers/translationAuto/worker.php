<?php
namespace S4C\TranslationAuto;
require_once __DIR__.'/repository.php';
require_once __DIR__.'/client.php';

class Worker
{
    private $repository;
    private $lock;
    private $config;
    private $client;
    const LOCK_NAME='Dashboard.multilanguage.autotranslate.worker.v1';
    public function __construct(array $base,array $config,Client $client=null) {
        $config=$config+settings([]);
        $this->repository=new Repository($base,$config);
        // MySQL 5.6 releases a connection's previous named lock on GET_LOCK.
        // Exclusivity therefore has a dedicated connection, separate from dictionary writes.
        $this->lock=new Repository($base,$config);
        $this->config=$config; $this->client=$client ?: new Client($config);
    }
    private function assertLock() {
        $row=$this->lock->query('SELECT IS_USED_LOCK(?) AS owner,CONNECTION_ID() AS connection',[self::LOCK_NAME])[0];
        if($row['owner']===null || (string)$row['owner']!==(string)$row['connection'])
            throw new \RuntimeException('worker_lock_lost');
    }
    public function execute($requestedId=null,$origin='manual',$mode='apply',array $languages=[],array $launch=null) {
        if(!$this->config['enabled']) return ['status'=>'disabled','busy'=>false];
        $acquired=$this->lock->query('SELECT GET_LOCK(?,0) AS acquired',[self::LOCK_NAME])[0]['acquired'];
        if((int)$acquired!==1)return ['busy'=>true]+$this->repository->status();
        try {
            $run=$this->repository->active();
            if($requestedId && (!$run || (string)$run['id']!==(string)$requestedId))
                return ['busy'=>$run!==null,'status'=>'request_already_finished']+$this->repository->status();
            if(!$run)$run=$this->repository->reserve($origin,null,$mode,$languages,$this->config)['run'];
            if($launch!==null)$this->repository->consumeLaunch($run['id'],$launch['launch_secret'] ?? null,$launch['username'] ?? '');
            $id=$run['id'];$preview=$run['mode']==='preview';$start=microtime(true);
            $this->assertLock();$this->repository->heartbeat($id);$this->repository->materialize($run,$this->config);
            $items=$this->repository->items($id);$groups=[];$error=null;
            foreach($items as $item) {
                $this->assertLock();
                if($item['status']==='generated'){$this->repository->apply($item,$preview);continue;}
                if(!eligibleText($item['menu_text'],$this->config['max_text_tokens'],$this->config)){
                    $this->repository->mark($item['id'],'review','ineligible_text');continue;
                }
                if((int)$item['attempts']>=$this->config['max_attempts']){
                    $this->repository->mark($item['id'],'review','attempts_exhausted');continue;
                }
                $peers=$this->repository->peers($item);
                if(count($peers)>1){$this->repository->mark($item['id'],'review','conflicting_dictionary');continue;}
                if($peers){
                    $this->repository->proposal($item['id'],$peers[0],'generated');
                    $item['proposed_text']=$peers[0];$this->repository->apply($item,$preview);continue;
                }
                if(!batchFits($item['target_language'],[['id'=>(string)$item['id'],'text'=>$item['menu_text']]],$this->config)){
                    $this->repository->mark($item['id'],'review','item_token_budget_exceeded');continue;
                }
                $key=$item['target_language']."\0".$item['menu_text'];
                if(!isset($groups[$key]))$groups[$key]=['id'=>(string)$item['id'],'text'=>$item['menu_text'],'language'=>$item['target_language'],'tasks'=>[]];
                $groups[$key]['tasks'][]=$item;
            }
            $lookup=[];foreach($groups as $group)$lookup[$group['id']]=$group;
            $fresh=[];$retry=[];
            foreach($groups as $group){
                $retried=false;
                foreach($group['tasks'] as $task)if((int)$task['attempts']>0 || in_array($this->repository->previousStatus($task),['failed','review'],true)){$retried=true;break;}
                if($retried)$retry[]=['language'=>$group['language'],'items'=>[['id'=>$group['id'],'text'=>$group['text']]]];
                else $fresh[]=$group;
            }
            $batches=array_merge(buildBatches($fresh,$this->config['batch_items'],$this->config['batch_tokens'],$this->config),$retry);
            for($batchIndex=0;$batchIndex<count($batches);$batchIndex++) {
                $batch=$batches[$batchIndex];
                $remaining=$this->config['max_seconds']-(microtime(true)-$start);
                if($remaining<=0){$error='time_budget';break;}
                $this->assertLock();$this->repository->heartbeat($id);
                $active=[];foreach($this->repository->items($id) as $item)$active[$item['id']]=true;
                $inputs=[];
                foreach($batch['items'] as $input){
                    $lookup[$input['id']]['tasks']=array_values(array_filter($lookup[$input['id']]['tasks'],function($task)use($active){return isset($active[$task['id']]);}));
                    if($lookup[$input['id']]['tasks'])$inputs[]=$input;
                }
                if(!$inputs)continue;
                $batch['items']=$inputs;
                $ids=[];foreach($batch['items'] as $input)foreach($lookup[$input['id']]['tasks'] as $task)$ids[]=$task['id'];
                $this->repository->attempt($ids);
                try {
                    $response=$this->client->complete($batch['language'],$batch['items'],min($remaining,$this->config['timeout_seconds']));
                    $results=parseCompletion($response,$batch['items'],$this->config);
                } catch(ApiError $e) {
                    $error=$e->getMessage();$this->assertLock();
                    if(($this->config['auth']['source'] ?? '')==='session' && in_array($error,['session_token_expired','api_http_401','api_http_403'],true)){
                        $this->repository->waitingForAuth($id,$ids,$error);
                        return ['status'=>'waiting_auth']+$this->repository->status();
                    }
                    $this->repository->failure($ids,$error,$e->retryable);
                    break;
                } catch(\InvalidArgumentException $e) {
                    $this->assertLock();
                    $this->repository->failure($ids,$e->getMessage(),true);
                    // Retry individually within this finite run; never apply malformed responses.
                    if($this->repository->retryWithinRun($id,$ids,$this->config['max_attempts']))
                        foreach($batch['items'] as $input)$batches[]=['language'=>$batch['language'],'items'=>[$input]];
                    continue;
                }
                $this->assertLock();
                // Persist the entire response before writing the nontransactional dictionary.
                mysqli_begin_transaction($this->repository->connection());
                $reviewRetries=[];
                try {
                    foreach($results as $key=>$result){
                        $reviewIds=[];
                        foreach($lookup[$key]['tasks'] as $task){
                            $this->repository->proposal($task['id'],$result['text'],$result['status']==='review'?'review':'generated',$result['error_code'] ?? null);
                            if($result['status']==='review')$reviewIds[]=$task['id'];
                        }
                        // A review is not terminal until this run's configured attempt budget is spent.
                        if($reviewIds && $this->repository->retryWithinRun($id,$reviewIds,$this->config['max_attempts']))
                            $reviewRetries[]=['language'=>$batch['language'],'items'=>[['id'=>(string)$key,'text'=>$lookup[$key]['text']]]];
                    }
                    mysqli_commit($this->repository->connection());
                } catch(\Throwable $e){mysqli_rollback($this->repository->connection());throw $e;}
                $batches=array_merge($batches,$reviewRetries);
                foreach($results as $key=>$result)if($result['status']!=='review')foreach($lookup[$key]['tasks'] as $task){
                    $this->assertLock();$task['proposed_text']=$result['text'];$this->repository->apply($task,$preview);
                }
                $this->repository->heartbeat($id);
            }
            $this->assertLock();
            // Items left by a budget limit or a service failure become eligible in a later finite run.
            foreach($this->repository->items($id) as $item)if($item['status']==='pending')
                $this->repository->failure([$item['id']],$error ?: 'deferred',true);
            if($error===null){
                $failed=$this->repository->query("SELECT error_code FROM translation_auto_items WHERE run_id=? AND status='failed' ORDER BY id LIMIT 1",[$id]);
                $error=$failed[0]['error_code'] ?? null;
            }
            $this->repository->finish($id,$error);
            return ['status'=>$error?'completed_with_errors':'completed','busy'=>false]+$this->repository->status();
        } catch(\Throwable $e) {
            try {
                $this->assertLock();
                if(isset($id))$this->repository->query("UPDATE translation_auto_runs SET error_code='worker_interrupted' WHERE id=?",[$id]);
            } catch(\Throwable $ignored){}
            throw $e;
        } finally {
            try{$this->lock->query('SELECT RELEASE_LOCK(?)',[self::LOCK_NAME]);}catch(\Throwable $e){}
        }
    }
}
