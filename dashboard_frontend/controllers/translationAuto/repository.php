<?php
namespace S4C\TranslationAuto;
require_once __DIR__.'/policy.php';
require_once dirname(__DIR__).'/translationRegistration.php';

class Repository
{
    private $link;
    private $base;
    private $config;
    public function __construct(array $base,array $config=null) {
        $this->base=$base;
        $this->config=$config ?? settings($base);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->link=mysqli_connect($base['host'],$base['username'],$base['password'],$base['dbname'],(int)($base['port'] ?? 3306));
        mysqli_set_charset($this->link,'utf8mb4');
    }
    public function languages(array $config){return languageMap($this->base['localizations'],$config['include_english']);}
    public function missingTranslations() {
        $configured=languageMap($this->base['localizations']);
        $automatic=$this->languages($this->config);$languages=[];$total=0;
        $rows=$this->query("SELECT LOWER(TRIM(COALESCE(language,''))) AS language,COUNT(*) AS n FROM multilanguage WHERE translatedText IS NOT NULL AND TRIM(translatedText)='' GROUP BY LOWER(TRIM(COALESCE(language,''))) ORDER BY language");
        foreach($rows as $row){
            $key=$row['language'];$count=(int)$row['n'];$total+=$count;
            $languages[]=['language'=>$configured[$key] ?? $key,'count'=>$count,'automatic'=>isset($automatic[$key])];
        }
        return ['total'=>$total,'languages'=>$languages,'updated_at'=>gmdate('Y-m-d H:i:s')];
    }
    public function connection(){return $this->link;}
    public function insertId(){return mysqli_insert_id($this->link);}
    public function query($sql,array $params=[]) {
        $stmt=\translationRegistrationStatement($this->link,$sql,$params);
        try {
            $meta=mysqli_stmt_result_metadata($stmt);
            if(!$meta) return mysqli_stmt_affected_rows($stmt);
            $row=[];$refs=[];
            while($field=mysqli_fetch_field($meta)){$row[$field->name]=null;$refs[]=&$row[$field->name];}
            mysqli_free_result($meta);unset($meta);
            mysqli_stmt_bind_result($stmt,...$refs);$out=[];
            while(($fetched=mysqli_stmt_fetch($stmt))===true) $out[]=array_map(function($v){return $v;},$row);
            if($fetched===false) throw new \RuntimeException('database_fetch_failed');
            return $out;
        } finally {mysqli_stmt_close($stmt);}
    }
    public function run($id){$rows=$this->query('SELECT * FROM translation_auto_runs WHERE id=?',[$id]);return $rows[0] ?? null;}
    public function active(){$rows=$this->query('SELECT * FROM translation_auto_runs WHERE active_slot=1');return $rows[0] ?? null;}
    public function reserve($origin,$user,$mode,array $languages,array $config,$key=null) {
        $key=$key ?? bin2hex(random_bytes(16));
        $old=$this->query('SELECT * FROM translation_auto_runs WHERE request_key=?',[$key]);
        if($old) return ['busy'=>true,'run'=>$old[0]];
        $active=$this->active();if($active)return ['busy'=>true,'run'=>$active];
        $max=$this->query('SELECT COALESCE(MAX(id),0) AS n FROM multilanguage')[0]['n'];
        try {
            $this->query('INSERT INTO translation_auto_runs(request_key,active_slot,origin,requested_by,mode,languages_json,max_items,snapshot_max_id,created_at,policy_version) VALUES(?,1,?,?,?,?,?,?,UTC_TIMESTAMP(),?)',
                [$key,$origin,$user,$mode,json_encode($languages),$config['max_items'],$max,POLICY_VERSION]);
            return ['busy'=>false,'run'=>$this->run($this->insertId())];
        } catch(\mysqli_sql_exception $e) {
            if((int)$e->getCode()!==1062)throw $e;
            $rows=$this->query('SELECT * FROM translation_auto_runs WHERE request_key=?',[$key]);
            return ['busy'=>true,'run'=>$rows[0] ?? $this->active()];
        }
    }
    private function startupPending(array $run){
        return $run['status']==='queued' && (int)$this->query('SELECT COALESCE(launch_at,created_at)>UTC_TIMESTAMP()-INTERVAL 30 SECOND AS pending FROM translation_auto_runs WHERE id=?',[$run['id']])[0]['pending']===1;
    }
    public function claimManualLaunch($user,$mode,array $languages,array $config,$key){
        $lock='Dashboard.multilanguage.autotranslate.worker.v1';
        if((int)$this->query('SELECT GET_LOCK(?,0) AS acquired',[$lock])[0]['acquired']!==1)return ['busy'=>true,'run'=>$this->active()];
        try{
            $old=$this->query('SELECT * FROM translation_auto_runs WHERE request_key=? OR launch_request_key=?',[$key,$key]);
            if($old)return ['busy'=>true,'run'=>$old[0]];
            $run=$this->active();$resumed=$run!==null;
            if($run && $this->startupPending($run))return ['busy'=>true,'run'=>$run];
            if(!$run)$run=$this->reserve('manual',$user,$mode,$languages,$config,$key)['run'];
            $secret=bin2hex(random_bytes(32));
            // Record the launch's authentication source in the existing origin field, including resumes.
            $this->query("UPDATE translation_auto_runs SET origin=?,status='queued',launch_hash=?,launch_at=UTC_TIMESTAMP(),started_by=?,launch_request_key=?,error_code=NULL WHERE id=? AND active_slot=1",[$config['auth_mode'] ?? 'session',hash('sha256',$secret),$user,$key,$run['id']]);
            return ['busy'=>false,'run'=>$this->run($run['id']),'launch_secret'=>$secret,'resumed'=>$resumed];
        }finally{$this->query('SELECT RELEASE_LOCK(?)',[$lock]);}
    }
    public function consumeLaunch($id,$secret,$user){
        if(!is_string($secret) || !preg_match('/^[a-f0-9]{64}$/D',$secret))throw new \RuntimeException('invalid_worker_launch');
        $changed=$this->query("UPDATE translation_auto_runs SET launch_hash=NULL,status='running',heartbeat_at=UTC_TIMESTAMP() WHERE id=? AND active_slot=1 AND launch_hash=? AND BINARY started_by=BINARY ? AND launch_at>UTC_TIMESTAMP()-INTERVAL 30 SECOND",[$id,hash('sha256',$secret),$user]);
        if($changed!==1)throw new \RuntimeException('invalid_worker_launch');
    }
    public function resetPendingRun($id){
        $lock='Dashboard.multilanguage.autotranslate.worker.v1';
        if((int)$this->query('SELECT GET_LOCK(?,0) AS acquired',[$lock])[0]['acquired']!==1)return false;
        try{
            $run=$this->active();
            // Bind reset to the displayed run and leave a newly launched worker time to start.
            if(!$run || (string)$run['id']!==(string)$id || $this->startupPending($run))return false;
            mysqli_begin_transaction($this->link);
            try{
                $this->query("UPDATE translation_auto_items SET status='cancelled',error_code='manual_reset',next_attempt_at=NULL,updated_at=UTC_TIMESTAMP() WHERE run_id=? AND status IN ('pending','generated')",[$id]);
                $this->query("UPDATE translation_auto_runs SET status='cancelled',active_slot=NULL,launch_hash=NULL,error_code=NULL,finished_at=UTC_TIMESTAMP(),heartbeat_at=UTC_TIMESTAMP() WHERE id=? AND active_slot=1",[$id]);
                mysqli_commit($this->link);
            }catch(\Throwable $e){mysqli_rollback($this->link);throw $e;}
            return true;
        }finally{$this->query('SELECT RELEASE_LOCK(?)',[$lock]);}
    }
    public function waitingForAuth($id,array $attempted=[],$error='session_token_required'){
        foreach($attempted as $item)$this->query("UPDATE translation_auto_items SET attempts=GREATEST(attempts-1,0),status='pending',error_code=NULL WHERE id=? AND status='pending'",[$item]);
        $this->query("UPDATE translation_auto_runs SET status='waiting_auth',error_code=?,launch_hash=NULL,heartbeat_at=UTC_TIMESTAMP() WHERE id=? AND active_slot=1",[$error,$id]);
    }
    public function status() {
        $run=$this->active();
        if(!$run){$rows=$this->query('SELECT * FROM translation_auto_runs ORDER BY id DESC LIMIT 1');$run=$rows[0] ?? null;}
        if(!$run)return ['run'=>null,'busy'=>false,'worker_active'=>false,'resumable'=>false];
        $counts=[];foreach($this->query('SELECT status,COUNT(*) AS n FROM translation_auto_items WHERE run_id=? GROUP BY status',[$run['id']]) as $row)$counts[$row['status']]=(int)$row['n'];
        $samples=$this->query("SELECT menu_text,target_language,proposed_text,status,error_code FROM translation_auto_items WHERE run_id=? AND status IN ('preview','review','failed') ORDER BY id LIMIT ".(int)$this->config['batch_items'],[$run['id']]);
        $active=$this->query('SELECT IS_USED_LOCK(?) AS owner',['Dashboard.multilanguage.autotranslate.worker.v1'])[0]['owner']!==null;
        $reserved=$run['active_slot']!==null;
        $starting=$reserved && $this->startupPending($run);
        $authMode=in_array($run['origin'],['session','dedicated'],true)?$run['origin']:null;
        return ['busy'=>$active || $starting,'worker_active'=>$active,'resumable'=>$reserved && !$active && !$starting,'run'=>[
            'id'=>(int)$run['id'],'origin'=>$authMode!==null?'manual':$run['origin'],'auth_mode'=>$authMode,'mode'=>$run['mode'],'status'=>$run['status'],
            'languages'=>json_decode($run['languages_json'],true) ?: [],'requested_by'=>$run['requested_by'],'started_by'=>$run['started_by'],'launch_at'=>$run['launch_at'],
            'created_at'=>$run['created_at'],'started_at'=>$run['started_at'],'heartbeat_at'=>$run['heartbeat_at'],
            'samples'=>$samples,'finished_at'=>$run['finished_at'],'error_code'=>$run['error_code'],'counts'=>$counts
        ]];
    }
    public function heartbeat($id) {
        $this->query("UPDATE translation_auto_runs SET status='running',started_at=COALESCE(started_at,UTC_TIMESTAMP()),heartbeat_at=UTC_TIMESTAMP() WHERE id=? AND active_slot=1",[$id]);
    }
    public function finish($id,$error=null) {
        $this->query("UPDATE translation_auto_runs SET status=?,error_code=?,active_slot=NULL,finished_at=UTC_TIMESTAMP(),heartbeat_at=UTC_TIMESTAMP() WHERE id=?",
            [$error?'completed_with_errors':'completed',$error,$id]);
    }
    public function launchFailed($id,$secret=null,$error='worker_launch_failed') {
        $params=[$error,$id];$guard='';
        if($secret!==null){$guard=' AND launch_hash=?';$params[]=hash('sha256',$secret);}
        $this->query("UPDATE translation_auto_runs SET status=IF(initialized=1,'waiting_auth','failed'),error_code=?,active_slot=IF(initialized=1,1,NULL),launch_hash=NULL,finished_at=IF(initialized=1,NULL,UTC_TIMESTAMP()) WHERE id=? AND status='queued'".$guard,$params);
    }
    public function materialize(array $run,array $config) {
        if((int)$run['initialized']) return;
        $map=$this->languages($config);
        $filter=json_decode($run['languages_json'],true);
        if($filter)$map=array_intersect_key($map,array_fill_keys($filter,true));
        // Fetch at most max_items full source rows across ALL languages, including large legacy labels.
        // A second pass fills quotas unused by languages with no work.
        if($map){$rotate=(int)$run['id']%count($map);$map=array_slice($map,$rotate,null,true)+array_slice($map,0,$rotate,true);}
        $active=$map;$cursor=array_fill_keys(array_keys($map),0);$chosen=[];
        while($active && count($chosen)<(int)$run['max_items']){
            $quota=max(1,(int)ceil(((int)$run['max_items']-count($chosen))/count($active)));
            $next=[];
            foreach($active as $normalized=>$canonical){
                $limit=min($quota,(int)$run['max_items']-count($chosen));
                if($limit<1)break;
                $rows=$this->query("SELECT m.* FROM multilanguage m WHERE m.menuText IS NOT NULL
                    AND m.id>? AND m.id<=? AND LOWER(TRIM(m.language))=?
                    AND m.translatedText IS NOT NULL AND TRIM(m.translatedText)='' ORDER BY m.id LIMIT ".$limit,
                    [$cursor[$normalized],$run['snapshot_max_id'],$normalized]);
                foreach($rows as $row){
                    $hash=hash('sha256',POLICY_VERSION."\0".$row['menuText']."\0".$row['language']);
                    $row['hash']=$hash;$row['canonical']=$canonical;
                    // A new explicit run gets a fresh attempt budget, regardless of old audit outcomes.
                    $row['attempts']=0;
                    $chosen[]=$row;$cursor[$normalized]=(int)$row['id'];
                }
                if(count($rows)===$limit)$next[$normalized]=$canonical;
            }
            $active=$next;
        }
        mysqli_begin_transaction($this->link);
        try {
            foreach($chosen as $row)$this->query("INSERT INTO translation_auto_items(run_id,translation_id,menu_text,source_language,target_language,source_hash,attempts,updated_at) VALUES(?,?,?,?,?,?,?,UTC_TIMESTAMP())",
                [$run['id'],$row['id'],$row['menuText'],$row['language'],$row['canonical'],$row['hash'],$row['attempts']]);
            $this->query('UPDATE translation_auto_runs SET initialized=1 WHERE id=?',[$run['id']]);
            mysqli_commit($this->link);
        } catch(\Throwable $e){mysqli_rollback($this->link);throw $e;}
    }
    public function retryItems(array $ids) {
        $acquired=$this->query('SELECT GET_LOCK(?,0) AS acquired',['Dashboard.multilanguage.autotranslate.worker.v1'])[0]['acquired'];
        if((int)$acquired!==1)throw new \RuntimeException('worker_running');
        try{
            if($this->active())throw new \RuntimeException('run_reserved');
            return $this->query("UPDATE translation_auto_items SET status='retry_requested',attempts=0,next_attempt_at=NULL,updated_at=UTC_TIMESTAMP() WHERE translation_id IN (".
                implode(',',array_fill(0,count($ids),'?')).") AND status IN ('review','failed')",$ids);
        } finally {$this->query('SELECT RELEASE_LOCK(?)',['Dashboard.multilanguage.autotranslate.worker.v1']);}
    }
    public function items($id) {
        // Also exclude NULL targets queued under an older policy or changed since selection.
        $this->query("UPDATE translation_auto_items i JOIN multilanguage m ON m.id=i.translation_id
            SET i.status='skipped_changed',i.error_code='null_translation_excluded',i.updated_at=UTC_TIMESTAMP()
            WHERE i.run_id=? AND i.status IN ('pending','generated') AND m.translatedText IS NULL",[$id]);
        return $this->query("SELECT * FROM translation_auto_items WHERE run_id=? AND status IN ('pending','generated') ORDER BY target_language,id",[$id]);
    }
    public function proposal($id,$text,$status,$error=null) {
        $this->query('UPDATE translation_auto_items SET proposed_text=?,status=?,error_code=?,updated_at=UTC_TIMESTAMP() WHERE id=?',[$text,$status,$error,$id]);
    }
    public function mark($id,$status,$error=null) {
        $this->query('UPDATE translation_auto_items SET status=?,error_code=?,updated_at=UTC_TIMESTAMP() WHERE id=?',[$status,$error,$id]);
    }
    public function attempt(array $ids) {
        foreach($ids as $id)$this->query('UPDATE translation_auto_items SET attempts=attempts+1,updated_at=UTC_TIMESTAMP() WHERE id=?',[$id]);
    }
    public function previousStatus(array $item) {
        $rows=$this->query('SELECT status FROM translation_auto_items WHERE translation_id=? AND source_hash=? AND id<? AND attempts>0 ORDER BY id DESC LIMIT 1',
            [$item['translation_id'],$item['source_hash'],$item['id']]);
        return $rows[0]['status'] ?? null;
    }
    public function failure(array $ids,$error,$retry) {
        foreach($ids as $id)$this->query('UPDATE translation_auto_items SET status=?,error_code=?,next_attempt_at=NULL,updated_at=UTC_TIMESTAMP() WHERE id=?',
            [$retry?'failed':'review',$error,$id]);
    }
    public function retryWithinRun($runId,array $ids,$maxAttempts) {
        $changed=0;
        foreach($ids as $id)$changed+=$this->query("UPDATE translation_auto_items SET status='pending',updated_at=UTC_TIMESTAMP() WHERE id=? AND run_id=? AND status IN ('failed','review') AND attempts<?",
            [$id,$runId,$maxAttempts]);
        return $changed;
    }
    public function peers(array $item) {
        $rows=$this->query("SELECT translatedText FROM multilanguage WHERE BINARY menuText=BINARY ? AND LOWER(TRIM(language))=LOWER(TRIM(?)) AND translatedText IS NOT NULL AND TRIM(translatedText)<>''",[$item['menu_text'],$item['source_language']]);
        $values=[];foreach($rows as $row)$values[$row['translatedText']]=true;
        return array_keys($values);
    }
    public function apply(array $item,$preview) {
        return \withTranslationRegistrationLock($this->link,function()use($item,$preview){
            $rows=$this->query('SELECT menuText,language,translatedText FROM multilanguage WHERE id=?',[$item['translation_id']]);
            $current=$rows[0] ?? null;
            if(!$current || $current['menuText']!==$item['menu_text'] || $current['language']!==$item['source_language'])
                return $this->mark($item['id'],'skipped_changed');
            if($current['translatedText']===null)return $this->mark($item['id'],'skipped_changed','null_translation_excluded');
            if(!emptyTranslation($current['translatedText']))return $this->mark($item['id'],$current['translatedText']===$item['proposed_text']?'applied':'preserved');
            $peers=$this->peers($item);
            if(count($peers)>1)return $this->mark($item['id'],'review','conflicting_dictionary');
            $proposal=$peers?$peers[0]:$item['proposed_text'];
            if(!validateTranslation($item['menu_text'],$proposal,$this->config))return $this->mark($item['id'],'review','invalid_translation');
            if($preview){$this->proposal($item['id'],$proposal,'preview');return;}
            $changed=$this->query("UPDATE multilanguage SET translatedText=? WHERE id=? AND BINARY menuText=BINARY ? AND BINARY language=BINARY ? AND translatedText IS NOT NULL AND TRIM(translatedText)=''",
                [$proposal,$item['translation_id'],$item['menu_text'],$item['source_language']]);
            $this->mark($item['id'],$changed?'applied':'skipped_changed');
        });
    }
}
