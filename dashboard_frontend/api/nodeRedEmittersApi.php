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
   
   function returnManagedStringForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return "'" . $original . "'";
        }
    }
    
    function returnManagedNumberForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return $original;
        }
    }
    
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   error_reporting(E_ERROR | E_NOTICE);
    
   if(!$link->set_charset("utf8")) 
   {
      exit();
   } 
   
   //$file = fopen("C:\dashboardLog.txt", "w");
   //fwrite($file, "MSG: \n" . file_get_contents('php://input'));
   
   $reqBody = json_decode(file_get_contents('php://input'));
   $msgObj = $reqBody->message;
   
   $envFileContent = parse_ini_file("../conf/environment.ini");
    $activeEnv = $envFileContent["environment"]["value"];
   
    $msgType = $msgObj->msgType;
    $response = [];
    $response['msgType'] = $msgObj->msgType;

    switch($msgType)
    {
        case "AddEmitter":
            $name = mysqli_real_escape_string($link, $msgObj->name);
            $valueType = mysqli_real_escape_string($link, $msgObj->valueType);
            $user = mysqli_real_escape_string($link, $msgObj->user);
            $startValue = mysqli_real_escape_string($link, $msgObj->startValue);
            $domainType = mysqli_real_escape_string($link, $msgObj->domainType);
            $minValue = mysqli_real_escape_string($link, $msgObj->minValue);
            $maxValue = mysqli_real_escape_string($link, $msgObj->maxValue);
            $offValue = mysqli_real_escape_string($link, $msgObj->offValue);
            $onValue = mysqli_real_escape_string($link, $msgObj->onValue);
            $endPointPort = mysqli_real_escape_string($link, $msgObj->endPointPort);
            $endPointHost = mysqli_real_escape_string($link, $msgObj->endPointHost);
            
            $httpRoot = $msgObj->httpRoot;
            
            mysqli_autocommit($link, FALSE);
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            $q1 = "DELETE FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.name = '$name' AND NodeRedInputs.user = '$user'";
            $r1 = mysqli_query($link, $q1);

            if($r1)
            {
                $q2 = "INSERT INTO Dashboard.NodeRedInputs(name, valueType, user, startValue, domainType, minValue, NodeRedInputs.maxValue, offValue, onValue, endPointPort, endPointHost, httpRoot) " .
                      "VALUES ('$name', '$valueType', '$user', '$startValue', '$domainType', " . returnManagedNumberForDb($minValue) . ", " . returnManagedNumberForDb($maxValue) . ", " . returnManagedStringForDb($offValue) . ", " . returnManagedStringForDb($onValue) . ", $endPointPort, '$endPointHost', " . returnManagedStringForDb($httpRoot) . ") " .
                      "ON DUPLICATE KEY UPDATE valueType='$valueType', user='$user', startValue='$startValue', domainType='$domainType', minValue = " . returnManagedNumberForDb($minValue) . ", NodeRedInputs.maxValue = " . returnManagedNumberForDb($maxValue) . ", offValue = " . returnManagedStringForDb($offValue) . ", onValue = " . returnManagedStringForDb($onValue) . ", endPointPort = $endPointPort, endPointHost = '$endPointHost', httpRoot = " . returnManagedStringForDb($httpRoot);  
                
                $r2 = mysqli_query($link, $q2);
                if($r2)
                {
                    mysqli_commit($link);
                    $response['result'] = 'Ok';
                }
                else
                {
                    mysqli_rollback($link);
                    $response['result'] = $q2;
                }
            }
            else
            {
                mysqli_rollback($link);
                $response['result'] = $q1;
            }
            mysqli_autocommit($link, TRUE);
            break;
            
        case "DelEmitter":
            $name = mysqli_real_escape_string($link, $msgObj->name);
            $user = mysqli_real_escape_string($link, $msgObj->user);
            
            
            mysqli_autocommit($link, FALSE);
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            $q1 = "DELETE FROM Dashboard.NodeRedInputs WHERE NodeRedInputs.name = '$name' AND NodeRedInputs.user = '$user'";
            $r1 = mysqli_query($link, $q1);
            if($r1)
            {
                $q2 = "DELETE FROM Dashboard.ActuatorsAppsValues WHERE ActuatorsAppsValues.widgetName LIKE '" . $name . "_%" . "' AND ActuatorsAppsValues.username = '$user'";
                $r2 = mysqli_query($link, $q2);
                
                if($r2)
                {
                    $q3 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Config_widget_dashboard.id_metric = '$name' AND Config_widget_dashboard.creator = '$user'"; 
                    $r3 = mysqli_query($link, $q3);

                    if($r3)
                    {
                        mysqli_commit($link);
                        $response['result'] = 'Ok';
                    }
                    else 
                    {
                        mysqli_rollback($link);
                        $response['result'] = 'Ko';
                    }
                }
                else
                {
                    mysqli_rollback($link);
                    $response['result'] = 'Ko';
                }
            }
            else
            {
                mysqli_rollback($link);
                $response['result'] = 'Ko';
            }
            mysqli_autocommit($link, TRUE);
            break;
            
        default:
            break;

    }

    mysqli_close($link);
    echo json_encode($response);
   
   
   
   
   