<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

use OpenSearchS4C as GlobalOpenSearchS4C;

require_once __DIR__.'/vendor/autoload.php';

class OpenSearchS4C
{

    protected $client;
    const default_index_name = 'dashboardwizard';
    const columns = [0=>"high_level_type",1=>"nature",2=>"sub_nature",3=>
    "low_level_type",4=>'unique_name_id',5=>'instance_uri',6=>'device_model_name',7=>'model_name',8=>"broker_name",
    9=>"value_name",10=>'value_type',11=>'unit',12=>'value_unit',13=>'last_date',14=>'last_value',15=>'healthiness',
    16=>'instance_uri',17=>'parameters',18=>'id',19=>'lastCheck',20=>'get_instances',21=>'ownership',22=>'organizations',
    23=>'latitude',24=>'longitude',25=>'sm_based',26=>'ownerHash',27=>'delegatedHash',28=>'delegatedGroupHash'];
    //const globalSqlFilterFieldName = ['high_level_type'=>"high_level_type", 'nature'=>'nature','sub_nature'=>'sub_nature',
    //'low_level_type'=>'low_level_type','unique_name_id'=>'unique_name_id','instance_uri'=>'instance_uri','unit'=>]
    protected $debug;

	

    // Initialize connection with opensearch node
    public function __construct($debug = false,$ip=NULL, $port=NULL, $user=NULL, $password=NULL)
    {
        if (!isset($ip)) {
            $ip = $GLOBALS["openSearchHostIP"];
        }
        if (!isset($port)) {
            $port = $GLOBALS["openSearchPort"];
        }
        if (!isset($user)) {
            $user = $GLOBALS["openSearchUser"];
        }
        if (!isset($password)) {
            $password = $GLOBALS["openSearchPwd"];
        }
        $this->client = (new \OpenSearch\ClientBuilder())
        ->setHosts(["https://$ip:$port"])
        ->setBasicAuthentication($user, $password)
        ->setSSLVerification(false)
        ->build();

        $this->debug = $debug;


    }


    // Ingest from the sql server to opensearch server, remove the cache.txt to start at beggining
    public function ingestionSqlDataToOpenSearch($host, $username, $password, $table = 'Dashboard.DashboardWizard', $index = 'dashboardwizard'){
        
        $cache_id = 0;
        if(file_exists('./cache.txt')){
            $cache_id = file_get_contents('./cache.txt');
        }

 
        $link = mysqli_connect($host, $username, $password);

        $query = "SELECT * FROM $table WHERE id > $cache_id ORDER BY id ASC;";

        $rs = mysqli_query($link, $query);
        
        if($rs)
        {
            /*id, nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, last_date, 
            last_value, unit, metric, saved_direct, kb_based, sm_based, `user`, widgets, parameters, healthiness, `microAppExtServIcon`, 
            `lastCheck`, ownership, organizations, latitude, longitude, value_unit, `ownerHash`, `delegatedHash`, `delegatedGroupHash`, 
            `oldEntry`, value_name, value_type, device_model_name, broker_name*/
            while($row = mysqli_fetch_assoc($rs))
            {
                $row = array_map('utf8_encode', $row);

                $device_name = '';
                $model_name  = '';

                switch($row['high_level_type']){
                    case "Data Table Model":
                    case "IoT Device Model":
                    case "Mobile Device Model":

                        $model_name = $row['device_model_name'];

                        break;

                    case "IoT Device":
                    case "Mobile Device":
                    case "Data Table Device":

                        $device_name = $row['device_model_name'];

                        break;

                    case "Data Table Variable":
                    case "IoT Device Variable":
                    case "Mobile Device Variable":
                        $device_name = $row['device_model_name'];
                        break;
                    
                    case "Sensor Device":
                    case "Sensor":
                    case "Sensor-Actuator":
                        $device_name = $row['device_model_name'];
                        break;

                    case "BIM Device":
                        $device_name = $row['device_model_name'];
                        break;
                    case "My Personal Data":
                        $device_name = $row['get_instances'];
                        break;

                    case "MyData":
                    case "MyKPI":
                    case "MyPOI":
                        $device_name = $row['device_model_name'];
                        break;

                    default:

                        break;
                }



                $this->createUpdateDocumentDashboardWizard($row['nature'],
                    $row['high_level_type'], $row['sub_nature'], $row['low_level_type'],
                    $row['unique_name_id'], $row['instance_uri'], $row['get_instances'],
                    $row['unit'],$row['metric'], $row['saved_direct'], $row['kb_based'],
                    $row['sm_based'], $row['user'], $row['widgets'], $row['parameters'],
                    $row['last_date'], $row['last_value'], $row['healthiness'], $row['lastCheck'],
                    $row['ownership'], $row['organizations'], $row['latitude'], $row['longitude'],
                    $row['value_unit'], $row['value_name'], $row['value_type'], $device_name,
                    $row['ownerHash'], $row['delegatedHash'], $row['delegatedGroupHash'], $row['broker_name'],
                    $row['microAppExtServIcon'],false,false,null,self::default_index_name, $device_name, $model_name);

                    /*

                        var_dump($this->createUpdateDocumentDashboardWizard($row['nature'],
                            $row['high_level_type'], $row['sub_nature'], $row['low_level_type'],
                            $row['unique_name_id'], $row['instance_uri'], $row['get_instances'],
                            $row['unit'],$row['metric'], $row['saved_direct'], $row['kb_based'], 
                            $row['sm_based'], $row['user'], $row['widgets'], $row['parameters'],
                            $row['last_date'], $row['last_value'], $row['healthiness'], $row['lastCheck'],
                            $row['ownership'], $row['organizations'], $row['latitude'], $row['longitude'],
                            $row['value_unit'], $row['value_name'], $row['value_type'], $row['device_model_name'],
                            $row['ownerHash'], $row['delegatedHash'], $row['delegatedGroupHash'], $row['broker_name'],
                            $row['microAppExtServIcon'],false,false,null,self::default_index_name, $device_name, $model_name));

                            exit(1);

                    */
                
                file_put_contents('./cache.txt',$row['id']);

                
                echo date('Y/m/d h:i:s') . ": Row id " . $row['id'] . ' exported to opensearch successfully ' . PHP_EOL;
            }
            

            mysqli_close($link);

            
        }
        else
        {
            mysqli_close($link);
            echo 'Mysqli query KO';
        }

    }

    public function returnColumnKeyByName($name){
        return array_flip(OpenSearchS4C::columns)[$name] ?? '';
    }

    // freeze results, new data ignored when processing, it will be processed next execution
    // it only works with newest version of opensearch
    public function createPointInTime(){
        $result = $this->client->createPointInTime([
            'index' => self::default_index_name,
            'keep_alive' => '10m'
        ]);

        var_dump("aaaa",$result);
        exit(1);
        $pitId = $result['pit_id'];

        return $pitId;
    }

    public function deletePointInTime($pitId){
        // Close Point-in-Time
        $result = $this->client->deletePointInTime([
            'body' => [
              'pit_id' => $pitId,
            ]
        ]);

        return $result;
    }
    ///////////////////////////////////////////////////////////////////////////////////////

    public function setAddItemToSearchById($id){
        $_REQUEST['ids_search_per_column'][] = $id;
    }

    public function searchByHighLevelTypeAndSearchAfter($pitId, $hlt ,$last_sort = null)
    {


        if($last_sort === null){
            // Get first page of results in Point-in-Time
            $results = $this->client->search([
                'body' => [
                    /*'pit' => [
                        'id' => $pitId,
                        'keep_alive' => '10m',
                    ],*/
                    'size' => 10000, // normally you would do 10000
                    'query' => [
                        'bool' => [
                            'must' => ['term'=>['high_level_type'=>$hlt]]
                        ]
                    ],
                    'sort' => ['_id'=>'asc','high_level_type'=>'asc'],
                ]
            ]);

            return $results;
        }else{
            $results = $this->client->search([
                'body' => [
                    /*'pit' => [
                        'id' => $pitId,
                        'keep_alive' => '10m',
                    ],*/
                    'search_after' => $last_sort,
                    'size' => 10000, // normally you would do 10000
                    'query' => [
                        'bool' => [
                            'must' => ['term'=>['high_level_type'=>$hlt]]
                        ]
                    ],
                    'sort' => ['_id'=>'asc','high_level_type'=>'asc'],
                ]
            ]);

            return $results;
        }
    

    
    }

    //set custom column search
    public function setCustomColumnSearch($column_name, $value){
        $column_key = $this->returnColumnKeyByName($column_name);
        $custom_search_by_column = [$column_key=>$value];

        if(isset($_REQUEST['custom_search_per_column']) && !empty($_REQUEST['custom_search_per_column'])
           && isset($_REQUEST['columns']) && !empty($_REQUEST['columns'])){
            array_push($_REQUEST['custom_search_per_column'], $custom_search_by_column);
            array_push($_REQUEST['columns'], [$column_key=>$column_name]);
        }else{
            $_REQUEST['custom_search_per_column'][] = $custom_search_by_column;
            $_REQUEST['columns'][] = [$column_key=>$column_name];
        }


    }

    //set or search by column name (so like OR in mysql)
    public function setOrColumnSearch($column_name, $value){
        $custom_search_by_column = [$column_name=>$value];

        if(isset($_REQUEST['or_search_per_column']) && !empty($_REQUEST['or_search_per_column'])
        ){
         array_push($_REQUEST['or_search_per_column'], $custom_search_by_column);

        }else{
            $_REQUEST['or_search_per_column'][] = $custom_search_by_column;

        }
    }


    public function getAllDistinctElementOfColumn($column_name,$organizations = null, $ownership = null, $ownerHash = null,
    $delegatedHash = null, $is_root_admin = false, $north_east_point_lat = null, $north_east_point_long = null,
         $south_west_point_lat = null, $south_west_point_long = null, $globalSqlFilter = null){
            
        $elements =  $this->getAllDocuments(0,0,$organizations,$ownership, $ownerHash,
        $delegatedHash, $is_root_admin, $column_name, $north_east_point_lat, $north_east_point_long,
        $south_west_point_lat, $south_west_point_long, $globalSqlFilter);


        $contents = [];
        if(isset($elements['aggregations']['bound_box_map']['distinct_element']['buckets'])){
            $contents = $elements['aggregations']['bound_box_map']['distinct_element']['buckets'];
        }else if(isset($elements['aggregations']['distinct_element']['buckets'])){
            $contents = $elements['aggregations']['distinct_element']['buckets'];
        }

        
        $el = [];
        foreach($contents as $buck){
            if($buck['key'] == 'NONE'){
                continue;
            }
            $el[] = $buck['key'];
        }


        return $el;


    }

    // get all document by index with paginate results
    // from = size * (page_number - 1)
    public function getAllDocuments($from = null, $length = null, $organizations = null, $ownership = null, $ownerHash = null,
                                    $delegatedHash = null, $is_root_admin = false, $aggColumnName = null, 
                                    $north_east_point_lat = null, $north_east_point_long = null,
                                    $south_west_point_lat = null, $south_west_point_long = null, $globalSqlFilter2 = null){

        $columns = $_REQUEST['columns'] ?? [];
        $aggs = null;
        
        $query = [
            
        ];

        $addition_query = [

        ];

        $sort = [

        ];

        if(isset($_REQUEST['order'])){
            $order = $_REQUEST['order'];
            if(isset($order[0]['column']) && isset($order[0]['dir'])){
                $column = $order[0]['column'];
                $dir = $order[0]['dir'];
                if(isset($columns[$column]) && isset($columns[$column]['orderable']) && $columns[$column]['orderable'] == true){
                    $sort[] = [self::columns[$column] => ["order"=>$dir]];
                }

            }
        }

        


        // or search per column
        if(isset($_REQUEST['or_search_per_column']) && !empty($_REQUEST['or_search_per_column'])){
            $should = [];
            foreach($_REQUEST['or_search_per_column'] as $item){
                foreach($item as $column_name => $value){
                    $should[] = ['wildcard'=>[
                        $column_name=>["value"=>$value,"case_insensitive"=>true]
                        ]
                    ];
                }

            }
            $addition_query[] = ['bool'=>['should'=>$should]];
            
        }

        // search by global search
        if(isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])){
            $should = [];
            foreach(self::columns as $key_column => $column_name){


                
                $should[] = ['wildcard'=>[
                    $column_name=>["value"=>'*'.$_REQUEST['search']['value'].'*',"case_insensitive"=>true]
                    ]
                ];

                
            }
            $addition_query[] = ['bool'=>['should'=>$should]];
        }

        //$asterisk = (isset($_REQUEST['search_bar']) && $_REQUEST['search_bar'] == "true") ? '*' : '';

        // add filter by text or by selection (table head selection)
        foreach($columns as $column_key => $column){

            if($column['search']['value'] != ''){

                $splitted = explode('|',$column['search']['value']);

                if(count($splitted) > 1){

                    $should = [];
                    foreach($splitted as $sp){
                        if(!empty($sp)){
                            $should[] = ['wildcard'=>[
                                self::columns[$column_key]=>["value"=>$sp,"case_insensitive"=>true]
                                ]
                            ];
                        }

                    }

                    $addition_query[] = ['bool'=>['should'=>$should]];

                }else{
                    $addition_query[] = ['bool'=>['filter'=>['wildcard'=>[
                        self::columns[$column_key]=>["value"=>$column['search']['value'],"case_insensitive"=>true]
                        ]
                    ]]];
                }
            }
                
            if(
                isset($_REQUEST['custom_search_per_column']) && isset($_REQUEST['custom_search_per_column'][$column_key])
                && !empty($_REQUEST['custom_search_per_column'][$column_key])
              ){
                
                $addition_query[] = ['bool'=>['filter'=>['wildcard'=>[
                    self::columns[$column_key]=>["value"=>'*'.$_REQUEST['custom_search_per_column'][$column_key].'*',"case_insensitive"=>true]
                    ]
                ]]];
            }


            
            

            
        }


        
        if($globalSqlFilter2 !== null && is_array($globalSqlFilter2)){
                
            foreach($globalSqlFilter2 as $gsf){

                //v/ar_dump($gsf);
                //exit(1);
                //if($gsf['field'] != self::columns[$column_key]){
                //    continue;
                //}

                
                

                if(count($gsf['selectedVals']) > 0){

                    $should = [];
                    foreach($gsf['selectedVals'] as $sp){
                        if(!empty($sp)){
                            $should[] = ['wildcard'=>[
                                $gsf['field']=>["value"=>$sp,"case_insensitive"=>true]
                                ]
                            ];
                        }

                    }

                    $addition_query[] = ['bool'=>['should'=>$should]];
                }


            }
        }



        if($north_east_point_lat !== null && $north_east_point_long !== null &&
        $south_west_point_lat !== null && $south_west_point_long !== null){
            $addition_query[] = ['bool' => [
                'filter' => [
                    'geo_bounding_box'=> [
                        'location' => [
                            'top_right' => [
                                'lat'=> (float)$north_east_point_lat,
                                'lon'=> (float)$north_east_point_long
                            ],
                            'bottom_left' => [
                                'lat'=> (float)$south_west_point_lat,
                                'lon'=> (float)$south_west_point_long
                            ]
                        ]
                    ]
                ]
            ]];
        }
        

        if(intval($_REQUEST["synMode"])) {

            $addition_query[] = ['bool'=>['must'=>['terms'=>[
                 "high_level_type"=>["MyKPI", "Sensor", "IoT Device Variable", "Mobile Device Variable",
                 "Data Table Variable", "Sensor-Actuator"]
                ]
            ]]];
        }

        if(!$is_root_admin){

            $query['bool'] = [
                'must' => [
                    
                    [
                        'bool' => [
                            'filter' => [
                                'term' => [
                                    'oldEntry' => 'NONE'
                                ]
                            ]
                        ]
                    ],
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'bool' => [
                                        'must' => [
                                            [
                                                'wildcard' => [
                                                    'organizations' => '*' . $organizations . '*'
                                                ]
                                            ],
                                            [
                                                'term' => [
                                                    'ownership' => $ownership
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'ownerHash' => '*' . $ownerHash . '*'
                                    ]
                                ],
                                [
                                    'wildcard' => [
                                        'delegatedHash' => '*' . $delegatedHash . '*'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            foreach($addition_query as $aq){
                $query['bool']['must'][] = $aq;
            }
        }else{
            $query['bool'] = [
                'must' => 
                    $addition_query
                
            ];
        }



  
 
        if($aggColumnName !== null){
            //$sort = [];
            //$query = [];

            $max_size = self::getIndexDocsSize(self::default_index_name) + 1;

            if($north_east_point_lat !== null && $north_east_point_long !== null &&
            $south_west_point_lat !== null && $south_west_point_long !== null){
                $aggs = [
                    'bound_box_map'=>[
                        'filter'=>[
                            'geo_bounding_box'=> [
                                'location' => [
                                    'top_right' => [
                                        'lat'=> (float)$north_east_point_lat,
                                        'lon'=> (float)$north_east_point_long
                                    ],
                                    'bottom_left' => [
                                        'lat'=> (float)$south_west_point_lat,
                                        'lon'=> (float)$south_west_point_long
                                    ]
                                ]
                            ]
                        ],
                        'aggs'=>[
                            'distinct_element' =>[
                                'terms' => [
                                    'field' => $aggColumnName,
                                    "size" => $max_size
                                ]
                            ]
                        ]
                        
                    ]
                ];
            }else{
                $aggs = [
                    'distinct_element' =>[
                        'terms' => [
                            'field' => $aggColumnName,
                            "size" => $max_size
                        ]
                    ]
                ];
            }


        }

        // search by ids of opensearch document
        if(isset($_REQUEST['ids_search_per_column']) && !empty($_REQUEST['ids_search_per_column'])){
            unset($query['bool']);
            $aggs = null;
            $query = [
            
            ];
            $query['ids']['values'] = $_REQUEST['ids_search_per_column'];

            if($_REQUEST['ids_search_per_column'] == "empty"){
                return [];
            }
        }

        //var_dump(self::default_index_name,$query, $length, $from, $sort, $aggs);

        return $this->search(self::default_index_name,$query, $length, $from, $sort, $aggs);
    }


    // Create an index with non-default settings.
    public function createIndex($index_name)
    {
        if(!$this->client->indices()->exists(['index'=>$index_name])){
            $this->client->indices()->create([
                'index' => $index_name,
                'body' =>[ 'mappings'=>[
                "properties" => [
                    "nature" =>    [ "type" => "keyword", "null_value" => "NONE"  ],
                    "high_level_type" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "sub_nature" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "low_level_type" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "unique_name_id" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "instance_uri" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "get_instances" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "last_date" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "last_value" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "unit" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "metric" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "saved_direct" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "kb_based" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "sm_based" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "user" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "widgets" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "parameters" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "healthiness" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "microAppExtServIcon" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "lastCheck" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "ownership" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "organizations" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "latitude" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "longitude" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "location" => ["type"=>"geo_point", "null_value"=> [0,0]],
                    "value_unit" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "ownerHash" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "delegatedHash" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "delegatedGroupHash" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "oldEntry" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "value_name" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "value_type" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "device_model_name" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "broker_name" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "device_name" =>    [ "type" => "keyword", "null_value" => "NONE" ],
                    "model_name" =>    [ "type" => "keyword", "null_value" => "NONE" ]
                ]
            //    ],'settings'=>['max_result_window'=>20368580700]]]);
                ],'settings'=>['max_result_window'=>1000000000]]]);
            //    ]]]);
            return true;
        }
        return false;
    }

    // Get client info
    public function info()
    {
        // Print OpenSearch version information on console.
        var_dump($this->client->info());
    }

    public function getIndexDocsSize($index_name){
        return $this->client->count(['index'=>$index_name])['count'] ?? 0;
    }

    // Create a document 
    public function createDocument($index_name, $id, $body)
    {


        // Create a document passing the id
        return $this->client->create([
            'id' => $id,
            'index' => $index_name,
            'body' => $body
        ]);

        



    }

    // Update document given the document
    public function updateDocument($index_name, $id, $doc)
    {
        return $this->client->update([
            'id' => $id,
            'index' => $index_name,
            'body' => [
                //data must be wrapped in 'doc' object
                'doc' => $doc
            ]
        ]);
    }

    // Delete a single document
    public function deleteByID($index_name, $id)
    {
        $this->client->delete([
            'id' => $id,
            'index' => $index_name,
        ]);
    }

    // Search by custom query
    public function search($index_name, $query, $size = null, $from = null, $sort = null, $aggs = null)
    {
        $param = ['index' => $index_name];
        

        if(!empty($query)){
            $param['body'] = [
                'query' => $query
            ];
        }

        if($aggs !== null){
            $param['body']['aggs'] = $aggs;
        }

        if($sort !== null){
            $param['body']['sort'] = $sort;
        }

        if($size !== null){
            $param['body']['size'] = (int)$size;
        }

        if($from !== null){
            $param['body']['from'] = (int)$from;
        }

        $param['body']['track_total_hits'] = true;

        if($this->debug){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            var_dump($param);

            echo '<br><br>Error in JSON Format:<br>';
            echo json_encode($param);
        }

        $docs = $this->client->search($param);

        return $docs;
    }

    // Delete index
    public function deleteByIndex($index_name)
    {
        $this->client->indices()->delete([
            'index' => $index_name
        ]);
    }

    public function updateByQuery($params){
        return $this->client->updateByQuery($params);
    }

    public function initDashboardWizard(){
        return $this->createIndex('dashboardwizard');
    }

    private function createGeoPoint($lan, $lon){
        if($lan === "" || $lon === ""){
            return [0,0];
        }else{
            //!!! OpenSearch convention long, lat
            return [(float)$lon, (float)$lan];
        }
    }

    private function returnNullIfEmpty($x){

        if($x === ""){
            return null;
        }else{
            return $x;
        }

    }

    private function returnNoneIfEmpty($x){
        if($x == false){
            return "NONE";
        }else{
            return $x;
        }
    }

    // Create the document in the index, if duplication exists just update it
    public function createUpdateDocumentDashboardWizard(
        $nature,
        $high_level_type,
        $sub_nature,
        $low_level_type,
        $unique_name_id,
        $instance_uri,
        $get_instances,
        $unit,
        $metric,
        $saved_direct,
        $kb_based,
        $sm_based,
        $user,
        $widgets,
        $parameters,
        $last_date,
        $last_value,
        $healthiness,
        $lastCheck,
        $ownership,
        $organizations,
        $latitude,
        $longitude,
        $value_unit,
        $value_name,
        $value_type,
        $device_model_name = '',
        $ownerHash = '',
        $delegatedHash = '',
        $delegatedGroupHash = '',
        $broker_name = '',
        $microAppExtServIcon = '',
        $id = false,
        $remove_low_level_type = false,
        $extra_param = null,
        $index_name = 'dashboardwizard',
        $device_name = '',
        $model_name = ''
    ){

        $doc = [
                 
                 'nature'            => self::returnNullIfEmpty($nature)       ,'high_level_type' => self::returnNullIfEmpty($high_level_type),
                 'sub_nature'        => self::returnNullIfEmpty($sub_nature)   ,'low_level_type'  => self::returnNullIfEmpty($low_level_type),
                 'instance_uri'      => self::returnNullIfEmpty($instance_uri) ,'get_instances'   => self::returnNullIfEmpty($get_instances),
                 'unit'              => self::returnNullIfEmpty($unit)         ,'metric'          => self::returnNullIfEmpty($metric),
                 'saved_direct'      => self::returnNullIfEmpty($saved_direct) ,'kb_based'        => self::returnNullIfEmpty($kb_based),
                 'sm_based'          => self::returnNullIfEmpty($sm_based)     ,'parameters'      => self::returnNullIfEmpty($parameters),
                 'last_value'        => self::returnNullIfEmpty($last_value)   ,'healthiness'     => self::returnNullIfEmpty($healthiness),
                 'ownership'         => self::returnNullIfEmpty($ownership)    ,'organizations'   => self::returnNullIfEmpty($organizations),
                 'latitude'          => self::returnNullIfEmpty($latitude)     ,'longitude'       => self::returnNullIfEmpty($longitude), 
                 'value_name'        => self::returnNullIfEmpty($value_name)   ,'value_type'      => self::returnNullIfEmpty($value_type),
                 'device_model_name' => self::returnNullIfEmpty($device_model_name),  'value_unit' => self::returnNullIfEmpty($value_unit),
                 'last_date'         => self::returnNullIfEmpty($last_date)        ,  'lastCheck'  => self::returnNullIfEmpty($lastCheck),
                 'unique_name_id'    => self::returnNullIfEmpty($unique_name_id)   ,  'ownerHash'  => self::returnNullIfEmpty($ownerHash),
                 'delegatedHash'     => self::returnNullIfEmpty($delegatedHash)    ,  'delegatedGroupHash' => self::returnNullIfEmpty($delegatedGroupHash),
                 'broker_name'       => self::returnNullIfEmpty($broker_name)      ,  'user' => self::returnNullIfEmpty($user),
                 'widgets'           => self::returnNullIfEmpty($widgets)          ,  'microAppExtServIcon' => self::returnNoneIfEmpty($microAppExtServIcon),
                 'oldEntry'          => self::returnNullIfEmpty('')                ,  'location' => self::createGeoPoint($latitude, $longitude),
                 'device_name'       => self::returnNullIfEmpty($device_name)      ,  'model_name' => self::returnNullIfEmpty($model_name)
                
                ];

            

        if($id === false){
            //return id of the doc if exists, else it return false
            $id = $this->checkDuplicateDocument($high_level_type,
            $sub_nature, $instance_uri, $get_instances, $low_level_type, 
            $unique_name_id, $index_name, $broker_name, $parameters, false, $extra_param);
        }


        if($this->debug){
            var_dump("ID",$id);
        }

        if($id !== false){
            return $this->updateDocument($index_name,$id,$doc);
        }else{
            return $this->createDocument($index_name,uniqid(),$doc);
        }
    }

    // Chheck duplicate document of dashboardwizard based on (high_level_type, sub_nature, low_level_type, unique_name_id,
    // instance_uri, get_instances and other custom column)
    public function checkDuplicateDocument($high_level_type,
     $sub_nature, $instance_uri, $get_instances, $low_level_type, $unique_name_id, 
      $index_name, 
     $broker_name = '',$parameters = '',
     $called_directly = true, $extra_param = null, $merge = true){

        $must = [

        ];

        if($high_level_type !== '' || !$called_directly){
            array_push($must,[
                'term' => ['high_level_type' => self::returnNoneIfEmpty($high_level_type)]
            ]);
        }

        if($sub_nature !== '' || !$called_directly){
            array_push($must,[
                'term' => ['sub_nature' => self::returnNoneIfEmpty($sub_nature)]
            ]);
        }

        if($instance_uri !== '' || !$called_directly){
            array_push($must, [
                'term' => ['instance_uri' => self::returnNoneIfEmpty($instance_uri)]
            ]);
        }

        if($get_instances !== '' || !$called_directly){
            array_push($must,             [
                'term' => ['get_instances' => self::returnNoneIfEmpty($get_instances)]
            ]);
        }

        if($unique_name_id !== '' || !$called_directly){
            array_push($must,            [
                'term' => ['unique_name_id' => self::returnNoneIfEmpty($unique_name_id)]
            ]);
        }

        if($low_level_type !== '' || !$called_directly){
            array_push($must,[
                'term' => ['low_level_type' => self::returnNoneIfEmpty($low_level_type)]
            ]);
        }

        if($broker_name !== '' && $called_directly){
            array_push($must,[
                'term' => ['broker_name' => self::returnNoneIfEmpty($broker_name)]
            ]);
        }

        if($parameters !== '' && $called_directly){
            array_push($must,[
                'term' => ['parameters' => self::returnNoneIfEmpty($parameters)]
            ]);
        }

        if($extra_param !== null){
            if(is_array($extra_param) && $merge){
                $must = array_merge($must, $extra_param);
            }else{
                array_push($must, $extra_param);
            }

        }

        if($this->debug){
            var_dump($must);

            echo '<br><br>Error in JSON Format:<br>';
            echo json_encode($must);
        }


        $params = [
            'index' => $index_name,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must
                    ]
                ]
            ]
        ];

        $val_returned = $this->client->search($params);

        if($this->debug){
            var_dump($val_returned);
        }

        //Check if the server opensearch has returned any data
        if(isset($val_returned['hits']['hits']) && count($val_returned['hits']['hits']) > 0){

            return $val_returned['hits']['hits'][0]['_id'];
        }else{
            return false;
        }
    }

    public function isNotEmptyResult($x){
        if(isset($x['hits']['hits'][0]) && is_array($x['hits']['hits'])){
            return true;
        }else{
            return false;
        }
    }

    public function getGetInstancesGeneralQuery($get_instances){
        $query = [
            'bool'=> [
                "must"=>[
                    ["term"=>["get_instances"=>$get_instances]]

                ]
                
            ]
        
        ];

        return $this->search(self::default_index_name,$query);
    }

    public function getHealthinessSensorGeneralQuery($get_instances){
        $query = [
            'bool'=> [
                "must"=>[
                    ["term"=>["get_instances"=>$get_instances]],
                    ["term"=>["healthiness"=>"true"]]

                ],
                "must_not" => [
                    ["term"=>["low_level_type"=>"NONE"]]
                ]
                
            ]
        
        ];

        return $this->search(self::default_index_name,$query);
    }

    public function getDevices(){
        $query = [
            'bool'=> [
                "should"=>[
                    ["term"=>["high_level_type"=>"Sensor"]],
                    ["term"=>["high_level_type"=>"IoT Device"]],
                    ["term"=>["high_level_type"=>"IoT Device Variable"]],
                    ["term"=>["high_level_type"=>"Mobile Device"]],
                    ["term"=>["high_level_type"=>"Mobile Device Variable"]],
                    ["term"=>["high_level_type"=>"Data Table Device"]],
                    ["term"=>["high_level_type"=>"Data Table Variable"]],
                    ["term"=>["high_level_type"=>"Sensor-Actuator"]],
                    ["bool"=>[
                      "must"=> [
                        ["term"=>["high_level_type"=>"Sensor"]],
                        ["term"=>["sub_nature"=>"First Aid Data"]]
                      ]  
                    ]]

                ]
                
            ]
        
        ];

        /*$query = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['oldEntry' => 'null']],
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['high_level_type' => 'Sensor']],
                                    ['term' => ['high_level_type' => 'IoT Device']],
                                    ['term' => ['high_level_type' => 'IoT Device Variable']],
                                    ['term' => ['high_level_type' => 'Mobile Device']],
                                    ['term' => ['high_level_type' => 'Mobile Device Variable']],
                                    ['term' => ['high_level_type' => 'Data Table Device']],
                                    ['term' => ['high_level_type' => 'Data Table Variable']],
                                    ['term' => ['high_level_type' => 'Sensor-Actuator']],
                                    [
                                        'bool' => [
                                            'must' => [
                                                ['term' => ['high_level_type' => 'Special Widget']],
                                                ['term' => ['sub_nature' => 'First Aid Data']]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'aggs' => [
                'group_by_unique_name_id' => [
                    'terms' => [
                        'field' => 'unique_name_id',
                        'order' => [
                            '_key' => 'desc'
                        ]
                    ]
                ]
            ]
        ];*/

        //return $this->search(self::default_index_name,$query);

    /*    $params = [
            'index' => 'dashboardwizard',
            'size' => 1000,
            'scroll' => '30s', // durata dello scroll
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['term' => ['high_level_type' => 'Sensor']],
                            ['term' => ['high_level_type' => 'IoT Device']],
                            ['term' => ['high_level_type' => 'Mobile Device']],
                            ['term' => ['high_level_type' => 'Data Table Device']],
                            ['term' => ['high_level_type' => 'Sensor-Actuator']]
                        ]
                    ]
                ]
            ]
        ];  */

        $params = [
            'index' => 'dashboardwizard',
            'size' => 1000,
            'scroll' => '30s', // durata dello scroll
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['term' => ['unit' => 'sensor_map']],
                            //['term' => ['high_level_type' => 'Sensor-Actuator']]
                        ]
                    ]
                ],
                'sort' => [
                    ['_id' => ['order' => 'desc']]
                ]
            ]
        ];

        $response = $this->client->search($params);

        $allDocuments = []; // Array per salvare tutti i documenti

        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            foreach ($response['hits']['hits'] as $document) {
                $source = $document['_source'];
                $allDocuments[] = $source; // Aggiungi il documento all'array
            }

            // Quando hai finito di elaborare i documenti della risposta corrente, chiedi i successivi
            $scroll_id = $response['_scroll_id'];
            $response = $this->client->scroll([
                'scroll_id' => $scroll_id,  //...passa il vecchio scroll_id
                'scroll' => '30s'           //...e la stessa durata dello scroll
            ]);
        }

        return $allDocuments;

    }

    public function setBoolEmptyHealthinessUpdate(){
        $match = [
            'term' => [
                'healthiness' => 'NONE'
            ]
        ];


        $ctx = "ctx._source.healthiness = 'false';";


        $params = [
            'index' => self::default_index_name,
            'body' => [
                'query' => ['bool'=>[
                    'must'=>$match
                ]],
                'script' => [
                    'source' =>
                    $ctx,
                    'lang' => 'painless'
                ]
            ]
        ];

        $this->client->updateByQuery($params);
    }

    public function buildParams($extraUpdate) {
        $params = [];
        $assignments = explode(';', $extraUpdate);

        foreach ($assignments as $assignment) {
            $parts = explode('=', $assignment);
            if (count($parts) == 2) {
                $key = trim(str_replace('ctx._source.', '', $parts[0]));
                $value = trim($parts[1], " '");
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function storeScript($scriptId, $scriptSource) {
        try {
            $this->client->putScript([
                'id' => $scriptId,
                'body' => [
                    'script' => [
                        'lang' => 'painless',
                        'source' => $scriptSource
                    ]
                ]
            ]);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
    }

/*    public function healthinessUpdate($get_instances, $lastCheck, $oldEntry = 'old', $healthiness = 'false', $extra = null, $extraUpdate = null) {
        $match = [
            ['term' => [
                'get_instances' => $get_instances
            ]]
        ];

        if($extra !== null){
            if(is_array($extra) && count($extra) > 1 ){
                $match = array_merge($match, $extra);
            }else{
                array_push($match, $extra);
            }
        }

        $params = [
            'index' => self::default_index_name,
            'body' => [
                'query' => ['bool'=>[
                    'must'=>$match
                ]],
                'script' => [
                    'source' =>
                        "ctx._source.oldEntry = params.oldEntry;
                ctx._source.healthiness = params.healthiness;
                ctx._source.lastCheck = params.lastCheck;" . $extraUpdate,
                    'lang' => 'painless',
                    'params' => [
                        'oldEntry' => $oldEntry,
                        'healthiness' => $healthiness,
                        'lastCheck' => $lastCheck
                    ]
                ]
            ]
        ];

        $this->client->cluster()->putSettings([
            'body' => [
                'transient' => [
                    'script.max_compilations_rate' => '500/1m'
                ]
            ]
        ]);

        try {
            $this->client->updateByQuery($params);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
    }*/

    public function healthinessUpdate($get_instances, $lastCheck, $oldEntry = 'old', $healthiness = 'false', $extra = null, $extraUpdate = null) {
        $match = [
            ['term' => [
                'get_instances' => $get_instances
            ]]
        ];

        if($extra !== null){
            if(is_array($extra) && count($extra) > 1 ){
                $match = array_merge($match, $extra);
            }else{
                array_push($match, $extra);
            }
        }

        if ($extraUpdate != NULL) {
            $extraParams = $this->buildParams($extraUpdate);
            $extraUpdateKeys = array_keys($extraParams);
            $extraUpdateValues = array_values($extraParams);
            $params = [
                'index' => self::default_index_name,
                'body' => [
                    'query' => ['bool'=>[
                        'must'=>$match
                    ]],
                    'script' => [
                        'id' => 'healthinessUpd',
                        'params' => [
                            'oldEntry' => $oldEntry,
                            'healthiness' => $healthiness,
                            'lastCheck' => $lastCheck,
                            'extraUpdateKeys' => $extraUpdateKeys,
                            'extraUpdateValues' => $extraUpdateValues
                        ]
                    ]
                ]
            ];
        } else {
            //$extraUpdateKeys = NULL;
            //$extraUpdateValues = NULL;
            $params = [
                'index' => self::default_index_name,
                'body' => [
                    'query' => ['bool'=>[
                        'must'=>$match
                    ]],
                    'script' => [
                        'id' => 'healthinessUpd',
                        'params' => [
                            'oldEntry' => $oldEntry,
                            'healthiness' => $healthiness,
                            'lastCheck' => $lastCheck
                        //    'extraUpdateKeys' => $extraUpdateKeys,
                        //    'extraUpdateValues' => $extraUpdateValues
                        ]
                    ]
                ]
            ];
        }

        //$this->client->cluster()->putSettings([
        //    'body' => [
        //        'transient' => [
        //            'script.max_compilations_rate' => '500/1m'
        //        ]
        //    ]
        //]);

        try {
            $this->client->updateByQuery($params);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
    }

    public function updateTypicalDashboardWizard($high_level_type, $sub_nature, $instance_uri,
     $get_instances, $low_level_type_add, $value_type, $unique_name_id, $value_name){
        $params = [
            'index' => 'dashboardwizard',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => ['high_level_type' => $high_level_type]
                            ],
                            [
                                'term' => ['sub_nature' => $sub_nature]
                            ],
                            [
                                'term' => ['instance_uri' => $instance_uri]
                            ],
                            [
                                'term' => ['get_instances' => $get_instances]
                            ],
                            [
                                'term' => ['low_level_type' => $low_level_type_add]
                            ]
                        ]
                    ]
                ],
                'script' => [
                    'source' =>
                     "ctx._source.low_level_type = '$low_level_type_add';
                      ctx._source.value_name = '$value_name';
                      ctx._source.value_type = '$value_type';
                      ctx._source.device_model_name = '$unique_name_id';",
                    'lang' => 'painless'
                ]
            ]
        ];

        $this->client->updateByQuery($params);
    }

    public function exportToJson() {
        ini_set('memory_limit', '4096M');
        require 'vendor/autoload.php';

        $params = ['index' => 'dashboardwizard'];

        // Esporta le impostazioni dell'indice
        $settings = $this->client->indices()->getSettings($params);
        file_put_contents('settings.json', json_encode($settings));

        // Esporta gli alias dell'indice
        $aliases = $this->client->indices()->getAliases($params);
        file_put_contents('aliases.json', json_encode($aliases));

        // Esporta i documenti dell'indice
        $docs = [];
        $from = 0;
        $size = 10000;

        do {
            $params = [
                'index' => 'dashboardwizard',
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'query' => [
                        'match_all' => new \stdClass()
                    ]
                ]
            ];

            $response = $this->client->search($params);
            $docs = array_merge($docs, $response['hits']['hits']);
            $from += $size;
        } while (count($response['hits']['hits']) > 0);

        file_put_contents('documents.json', json_encode($docs));
    }

    public function exportModelDocsToJson($maxDocs) {
        ini_set('memory_limit', '4096M');
        require 'vendor/autoload.php';

        $params = ['index' => 'dashboardwizard'];

// Esporta le impostazioni dell'indice
        $settings = $this->client->indices()->getSettings($params);
        file_put_contents('settings.json', json_encode($settings));

// Esporta gli alias dell'indice
        $aliases = $this->client->indices()->getAliases($params);
        file_put_contents('aliases.json', json_encode($aliases));

// Esporta i documenti dell'indice
        $docs = [];
        $from = 0;
        $size = 10000;
        //$maxDocs = 30000;

        do {
            $params = [
                'index' => 'dashboardwizard',
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['term' => ['model_name' => 'metrotrafficsensor']],
                                ['term' => ['model_name' => 'bikeSharingPark']],
                                ['term' => ['model_name' => 'carSharingPark']]
                            ]
                       /*     'must' => [
                                'term' => ['model_name' => 'bikeSharingPark']
                            ]
                            'filter' => [
                                'exists' => ['field' => 'model_name']
                            ],
                            "must" => [
                                "exists" => [
                                    "field" => "model_name"
                                ]
                            ],
                            "must_not" => [
                                "term" => [
                                    "model_name" => "NULL"
                                ]
                            ]*/
                        ]
                    ]
                ]
            ];

            $response = $this->client->search($params);
            $docs = array_merge($docs, $response['hits']['hits']);
            $from += $size;
        } while (count($response['hits']['hits']) > 0 && count($docs) < $maxDocs);

        file_put_contents('documents.json', json_encode($docs));
    }

    public function findAndDeleteDuplicates($deleteFlag, $filterFlag) {

        $params = [
            'scroll' => '30s',
            'size' => 10000,
            'index' => 'dashboardwizard',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        $response = $this->client->search($params);

        $documents = [];
        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            $scroll_id = $response['_scroll_id'];

            foreach ($response['hits']['hits'] as $document) {
                $id = $document['_id'];
                $source = $document['_source'];

                if ($filterFlag == "all") {
                    $hash = md5(json_encode($source));
                } else {
                    $hash = md5(json_encode([
                    //    'high_level_type' => $source['high_level_type'],
                    //    'sub_nature' => $source['sub_nature'],
                        'low_level_type' => $source['low_level_type'],
                        'unique_name_id' => $source['unique_name_id'],
                    //    'instance_uri' => $source['instance_uri'],
                        'get_instances' => $source['get_instances'],
                        'organizations' => $source['organizations'],
                        'value_name' => $source['value_name'],
                        'value_type' => $source['value_type']

                    ]));
                }
                if (isset($documents[$hash])) {
                    $documents[$hash][] = $id;
                } else {
                    $documents[$hash] = [$id];
                }
            }

            $response = $this->client->scroll([
                'scroll_id' => $scroll_id,
                'scroll' => '30s'
            ]);
        }

        foreach ($documents as $hash => $ids) {
            // Skip the first document (it's the original)
            array_shift($ids);

            // Print and delete the duplicates
            foreach ($ids as $id) {
                $params = ['index' => 'dashboardwizard', 'id' => $id];
                $doc = $this->client->get($params);
                echo ("\nFound duplicate: " . $id . "\n");
                echo ("device_name: " . $doc['_source']['device_name'] . "\n");
                echo ("unique_name_id: " . $doc['_source']['unique_name_id'] . "\n");
                echo ("value_name: " . $doc['_source']['value_name'] . "\n");
                echo ("value_type: " . $doc['_source']['value_type'] . "\n");
                echo ("low_level_type: " . $doc['_source']['low_level_type'] . "\n");
                if ($deleteFlag) {
                    $this->client->delete($params);
                    echo("Deleted duplicate: " . $id . "\n");
                }
            }
        }

    }

    public function getMyKPI(){

        $params = [
            'index' => 'dashboardwizard',
            'size' => 1000,
            'scroll' => '30s', // durata dello scroll
            'body' => [
                'query' => [
                    'bool'=> [
                        "should"=>[
                            ["term"=>["high_level_type"=>"MyKPI"]],
                            ["term"=>["high_level_type"=>"MyPOI"]],
                            ["term"=>["high_level_type"=>"MyData"]]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->client->search($params);

        $allDocuments = []; // Array per salvare tutti i documenti

        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            foreach ($response['hits']['hits'] as $document) {
                $source = $document['_source'];
                $allDocuments[] = $source; // Aggiungi il documento all'array
            }

            // Quando hai finito di elaborare i documenti della risposta corrente, chiedi i successivi
            $scroll_id = $response['_scroll_id'];
            $response = $this->client->scroll([
                'scroll_id' => $scroll_id,  //...passa il vecchio scroll_id
                'scroll' => '30s'           //...e la stessa durata dello scroll
            ]);
        }

        return $allDocuments;

    }

}
