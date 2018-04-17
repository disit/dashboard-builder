<?php
    include('../config.php');
    
    date_default_timezone_set('Europe/Rome');
    
    $envFileContent = parse_ini_file("../conf/environment.ini");
    $activeEnv = $envFileContent["environment"]["value"];
    $nrInstanceContent = parse_ini_file("../conf/nodeEmittersApi.ini");
    $nrInstanceAddress = $nrInstanceContent["nrInstanceAddress"][$activeEnv];
    $nrInstancePort = $nrInstanceContent["nrInstancePort"][$activeEnv];
    
    $response = [];
    
    $httpRelativeUrl = $_REQUEST['httpRelativeUrl'];
    
    $requestBody = [
        //"username" => $_REQUEST['username'],
        "dashboardTitle" => $_REQUEST['dashboardTitle'],
        "httpRelativeUrl" => $httpRelativeUrl,
        "gpsData" => $_REQUEST['gpsData']
    ];
    
    //$httpCompleteUrl = 'http://' . $nrInstanceAddress . ":" . $nrInstancePort . "/nodered/nr20/" . $httpRelativeUrl;
    $httpCompleteUrl = 'http://' . $nrInstanceAddress . "/nodered/nr20/" . $httpRelativeUrl;
    
    $callOptions = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($requestBody),
                'timeout' => 60
            )
        );
    
    try
    {
        $context = stream_context_create($callOptions);
        $callResult = file_get_contents($httpCompleteUrl, false, $context);

        if(strpos($http_response_header[0], '200') === false)
        {
            $response['result'] = "sendGpsKo";
            
        }
        else
        {
            $response['result'] = "sendGpsOk";
        }
    } 
    catch (Exception $ex) 
    {
        $response['result'] = "sendGpsKoException";
    }
    
    $response['url'] = $httpCompleteUrl;
    
    echo json_encode($response);
    

