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
   
    error_reporting(E_ERROR | E_NOTICE);
    session_start(); 
    date_default_timezone_set('Europe/Rome');
   
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    $link->set_charset("utf8");
   
    $response = [];
    $dashboardId = mysqli_real_escape_string($link, $_POST['dashboardId']);
    $entityId = mysqli_real_escape_string($link, $_POST['entityId']);
    $value = mysqli_real_escape_string($link, $_POST['value']);
    $actionTime = date('Y-m-d H:i:s');
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $entityJson = mysqli_real_escape_string($link, $_POST['entityJson']);//Non usata, ma può essere utile in futuro
    $entityJsonObj = json_decode($entityJson);//Non usata, ma può essere utile in futuro
    $attributeName = mysqli_real_escape_string($link, $_POST['attributeName']);
    $attributeType = mysqli_real_escape_string($link, $_POST['attributeType']);
    
    if(isset($_SESSION['loggedUsername']))
    {
       $username = $_SESSION['loggedUsername'];
    }
    else
    {
       if(isset($_REQUEST["dashboardUsername"]))
       {
           if($_REQUEST["dashboardUsername"] != "")
           {
               $username = $_REQUEST["dashboardUsername"];
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
    }
   
    //1)Inserimento nuovo record su DB
    $q = "INSERT INTO Dashboard.ActuatorsEntitiesValues(entityId, actionTime, value, username, ipAddress) " .
         "VALUES('$entityId', '$actionTime', '$value', '$username', '$ipAddress')";
                    
    $r = mysqli_query($link, $q);
                    
    if($r) 
    {
        //2) Se insert su DB OK, aggiorniamo il valore sull'entità Orion
        $lastActionId = mysqli_insert_id($link);
        $entityUpdatedAttribute = [$attributeName => ["value" => $value, "type" => $attributeType]];
        $entityUpdatedAttributeJson = json_encode($entityUpdatedAttribute);

        $orionUpdateEntityUrl = $orionBaseUrl. "/v2/entities/" . $entityId . "/attrs";

        $orionCallOptions = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'PATCH',
                'content' => $entityUpdatedAttributeJson,
                'timeout' => 2
            )
        );

        try
        {
           $context  = stream_context_create($orionCallOptions);
           $callResult = file_get_contents($orionUpdateEntityUrl, false, $context);
           
           if(strpos($http_response_header[0], '204 No Content') === false)
           {
              //3 negativo) Se update entità su Orion KO, aggiorniamo record con risultato positivo e timestamp
              $actuationResultTime = date('Y-m-d H:i:s');
              $updateQ = "UPDATE Dashboard.ActuatorsEntitiesValues SET actuationResult = 'Ko', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
              $updateR = mysqli_query($link, $updateQ);

              if($updateR) 
              {
                  $response['result'] = "updateEntityKo";
              }
              else
              {
                  $response['result'] = "updateEntityAndUpdateQueryKo";
              }
           }
           else
           {
                //3 positivo) Se update entità su Orion OK, aggiorniamo record con risultato positivo e timestamp
                $actuationResultTime = date('Y-m-d H:i:s');
                $updateQ = "UPDATE Dashboard.ActuatorsEntitiesValues SET actuationResult = 'Ok', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
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
            //3 negativo) Se update entità su Orion KO, aggiorniamo record con risultato positivo e timestamp
            $actuationResultTime = date('Y-m-d H:i:s');
            $updateQ = "UPDATE Dashboard.ActuatorsEntitiesValues SET actuationResult = 'Ko', actuationResultTime = '$actuationResultTime' WHERE id = $lastActionId";
            $updateR = mysqli_query($link, $updateQ);

            if($updateR) 
            {
                $response['result'] = "updateEntityKo";
            }
            else
            {
                $response['result'] = "updateEntityAndUpdateQueryKo";
            }
        }
    }
    else
    {
        $response['result'] = "insertQueryKo";
    }
    
   mysqli_close($link);
   echo json_encode($response);