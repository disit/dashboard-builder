<?php
    /* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

    include '../config.php';
   
    error_reporting(E_ALL);
    session_start(); 
    date_default_timezone_set('Europe/Rome');
   
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    $link->set_charset("utf8");
    
    $envFileContent = parse_ini_file("../conf/environment.ini");
    $activeEnv = $envFileContent["environment"]["value"];
    $nrInstanceContent = parse_ini_file("../conf/nodeEmittersApi.ini");
    $nrInstanceAddress = $nrInstanceContent["nrInstanceAddress"][$activeEnv];
    //$nrInstancePort = json_decode($nrInstanceContent["nrInstancePort"][$activeEnv])[$nrInstanceIndex - 1];
   
    $response = [];
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
    $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
    $value = mysqli_real_escape_string($link, $_REQUEST['value']);
    $actionTime = date('Y-m-d H:i:s');
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    //$endPointHost = mysqli_real_escape_string($link, $_REQUEST['endPointHost']);
    $endPointPort = mysqli_real_escape_string($link, $_REQUEST['endPointPort']);
    $httpRoot = mysqli_real_escape_string($link, $_REQUEST['httpRoot']);
    $nodeRedInputName = mysqli_real_escape_string($link, $_REQUEST['inputName']);
    
    if(($httpRoot == "null")||($httpRoot == "NULL"))
    {
        $httpRoot = null;
    }
    
    
    if(isset($_REQUEST["username"]))
    {
        if($_REQUEST["username"] != "")
        {
            $username = mysqli_real_escape_string($link, $_REQUEST['username']);
        }
        else
        {
            $username = "publicDashboard";
        }
    } 
    else
    {
        $username = "publicDashboard";
    }
    
   
    //1)Inserimento nuovo record su DB
    $q = "INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress) " .
         "VALUES('$widgetName', '$actionTime', '$value', '$username', '$ipAddress')";
                    
    $r = mysqli_query($link, $q);
    
    $response['result'] = "Ok";
                    
    if($r) 
    {
        //2) Se insert su DB OK, aggiorniamo il valore sul blocchetto NodeRED
        $lastActionId = mysqli_insert_id($link);
        $blockUpdatedValue = ["newValue" => $value];
        $blockUpdatedValueJson = json_encode($blockUpdatedValue);

        if($httpRoot != null)
        {
            $nodeRedUpdateBlockUrl = 'http://' . $nrInstanceAddress . $httpRoot . "/" . $nodeRedInputName;
        }
        else
        {
            $nodeRedUpdateBlockUrl = 'http://' . $nrInstanceAddress . ":" . $endPointPort . "/" . $nodeRedInputName;
        }
        
        $callOptions = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => $blockUpdatedValueJson,
                'timeout' => 60
            )
        );

        try
        {
           $context = stream_context_create($callOptions);
           $callResult = file_get_contents($nodeRedUpdateBlockUrl, false, $context);
           
           if(strpos($http_response_header[0], '200') === false)
           {
              //3 negativo) Se update su NodeRED KO, aggiorniamo record con risultato positivo e timestamp
              $actuationResultTime = date('Y-m-d H:i:s');
              $updateQ = "UPDATE Dashboard.ActuatorsAppsValues SET actuationResult = 'Ko', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
              $updateR = mysqli_query($link, $updateQ);

              if($updateR) 
              {
                  $response['result'] = "updateBlockKo ". $nodeRedUpdateBlockUrl ;
              }
              else
              {
                  $response['result'] = "updateBlockAndUpdateQueryKo";
              }
           }
           else
           {
                //3 positivo) Se update entità su Orion OK, aggiorniamo record con risultato positivo e timestamp
                $actuationResultTime = date('Y-m-d H:i:s');
                $updateQ = "UPDATE Dashboard.ActuatorsAppsValues SET actuationResult = 'Ok', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
                $updateR = mysqli_query($link, $updateQ);

                if($updateR) 
                {
                    $response['result'] = "Ok";
                }
                else
                {
                    $response['result'] = "updateQueryKo";
                }
           }
        }
        catch (Exception $ex) 
        {
            //3 negativo) Se update su NodeRED KO, aggiorniamo record con risultato negativo e timestamp
            $actuationResultTime = date('Y-m-d H:i:s');
            $updateQ = "UPDATE Dashboard.ActuatorsAppsValues SET actuationResult = 'Ko', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
            $updateR = mysqli_query($link, $updateQ);

            if($updateR) 
            {
                $response['result'] = "updateBlockKo";
            }
            else
            {
                $response['result'] = "updateBlockAndUpdateQueryKo";
            }
        }
    }
    else
    {
        $response['result'] = "insertQueryKo";
    }
    
   mysqli_close($link);
   echo json_encode($response);
