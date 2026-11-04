<?php
    /* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

   include '../config.php';
require_once __DIR__.'/widgetDatabase.php';
$widgetDatabaseGuard = dashboardWidgetDatabaseBegin();
try {

   
   session_start(); 
   error_reporting(E_ERROR | E_NOTICE);
   $esbLink = mysqli_connect($esbHost, $esbDbUsr, $esbDbPwd) or die("Failed to connect to server");
   mysqli_select_db($esbLink, $esbDbName);
   
   if(!$esbLink->set_charset("utf8")) 
   {
        exit();
   }
   
   if(isset($_REQUEST["operation"]))
   {
      switch($_REQUEST["operation"])
      {
         case "getTrafficEvents":
            $choosenOption = $_REQUEST['choosenOption'];
            $time = $_REQUEST['time'];
            $timeUdm = $_REQUEST['timeUdm'];
            $eventsBound = $_REQUEST['events'];
            
            $events = [];
            
            if($choosenOption == "time")
            {
               $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.traffic.TrafficInformation' AND type = 'DEFINITION' AND STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') >= (NOW() - INTERVAL " . $time . " " . $timeUdm . ") ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC"; 
            }
            else if($choosenOption == "time_sismic")
            {
               $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.traffic.TrafficInformation' AND type = 'DEFINITION' AND event_time >= (NOW() - INTERVAL " . $time . " " . $timeUdm . ") AND payload LIKE '%\"source\":\"cdfipm\"%' ORDER BY event_time DESC"; 
            }
            else
            {
               $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.traffic.TrafficInformation' AND type = 'DEFINITION' ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC LIMIT " . $eventsBound; 
            }
            
            $result = mysqli_query($esbLink, $query);
            
            if($result) 
            {
               while($row = mysqli_fetch_assoc($result)) 
               {
                  $eventRow = [];
                  $eventRow['id'] = $row['id'];
                  $eventRow['created'] = $row['created'];
                  $eventRow['data_id'] = $row['data_id'];
                  $eventRow['data_type'] = $row['data_type'];
                  $eventRow['event_time'] = $row['event_time'];
                  $eventRow['payload'] = $row['payload'];
                  $payload = json_decode($row['payload'], true);
                  if(isset($payload['coords']) && $payload['coords']['latitude']==0 && $payload['coords']['longitude']==0) {
                    $payload['coords']['latitude']=43.7691;
                    $payload['coords']['longitude']=11.2562;
                    //unset($payload['coords']);
                    $eventRow['payload'] = json_encode($payload);
                  }
                  $eventRow['payload_class'] = $row['payload_class'];
                  $eventRow['sender_id'] = $row['sender_id'];
                  $eventRow['type'] = $row['type'];
                  $events[$eventRow['id']] = $eventRow;
               }
               $result = json_encode($events);
               echo $result;
            }
            else
            {
               echo "queryKo";
            }
            break;
            
         case "getAlarms":
            $alarms = [];
            $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.alarm.Alarm' AND type = 'DEFINITION' ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC"; //AND event_time >= (NOW() - INTERVAL 2 DAY)
            $result = mysqli_query($esbLink, $query);

            if($result) 
            {
               while($row = mysqli_fetch_assoc($result)) 
               {
                  $alarmRow = [];
                  $alarmRow['id'] = $row['id'];
                  $alarmRow['created'] = $row['created'];
                  $alarmRow['data_id'] = $row['data_id'];
                  $alarmRow['data_type'] = $row['data_type'];
                  $alarmRow['event_time'] = $row['event_time'];
                  $alarmRow['payload'] = $row['payload'];
                  $alarmRow['payload_class'] = $row['payload_class'];
                  $alarmRow['sender_id'] = $row['sender_id'];
                  $alarmRow['type'] = $row['type'];
                  $alarms[$alarmRow['id']] = $alarmRow;
               }
               $result = json_encode($alarms);
               echo $result;
            }
            else
            {
               echo "queryKo";
            }
            //echo json_encode($alarms);
            break;
            
         case "getEvacuationPlans":
            $plans = [];
            $plansQueryFailed = false;
            
            $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.evac.EvacuationPlan' AND type = 'DEFINITION' ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC";
            $result = mysqli_query($esbLink, $query);
            if($result) 
            {
               while($row = mysqli_fetch_assoc($result)) 
               {
                  $dataId = $row['data_id'];
                  $planRow['id'] = $row['id'];
                  $planRow['data_id'] = $row['data_id'];
                  $planRow['event_time'] = $row['event_time'];
                  $planRow['originalPayload'] = $row['payload'];
                  
                  $query2 = "SELECT payload AS lastPayload FROM resolute_events WHERE data_id = '$dataId' AND data_type = 'org.resolute_eu.esb.model.evac.EvacuationPlan' AND type = 'UPDATE' ORDER BY created DESC LIMIT 1";
                  $result2 = mysqli_query($esbLink, $query2);
                  
                  if(mysqli_num_rows($result2) > 0)
                  {
                     while($row2 = mysqli_fetch_assoc($result2)) 
                     {
                        // MySQL 5.6 has no JSON_EXTRACT: decode the latest update in PHP.
                        $lastPayload = $row2['lastPayload'];
                        if ($lastPayload === null) { $planRow['lastStatus'] = ''; continue; }
                        $statusPayload = json_decode($lastPayload);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                           $plansQueryFailed = true;
                           break;
                        }
                        $lastStatus = is_object($statusPayload) && property_exists($statusPayload, 'evacuation_plan_status')
                           ? json_encode($statusPayload->evacuation_plan_status, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION) : '';
                        $planRow['lastStatus'] = str_replace('"', '', $lastStatus);
                     }
                     if ($plansQueryFailed) break;
                  }
                  else
                  {
                     $planRow['lastStatus'] = "PROPOSED";
                  }
                  
                  $plans[$planRow['id']] = $planRow;
               }
               if ($plansQueryFailed) {
                  // Emit one legacy failure, never a partial list of plans.
                  echo "queryKo";
               } else {
                  $result = json_encode($plans);
                  echo $result;
               }
            }
            else
            {
               echo "queryKo";
            }
            break;
            
         case "getResources":
            $resources = [];
            $query = "SELECT a.*, b.payload AS payload, b.type AS type FROM (SELECT data_id as dataId, MAX(event_time) AS eventTime FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.resource.Resource' GROUP BY data_id) AS a LEFT JOIN resolute_events AS b ON a.eventTime = b.event_time ORDER BY STR_TO_DATE(b.event_time, '%Y-%m-%d %H:%i:%s') DESC";
            $result = mysqli_query($esbLink, $query);

            if($result) 
            {
               while($row = mysqli_fetch_assoc($result)) 
               {
                  $resourceRow = [];
                  $resourceRow['data_id'] = $row['dataId'];
                  $resourceRow['event_time'] = $row['eventTime'];
                  $resourceRow['payload'] = $row['payload'];
                  $resourceRow['type'] = $row['type'];
                  $resources[$resourceRow['data_id']] = $resourceRow;
               }
               $result = json_encode($resources);
               echo $result;
            }
            else
            {
               echo "queryKo";
            }
            break;
            
         case "getNetworkAnalysis":
            $analysis = [];
            $query = "SELECT * FROM resolute.resolute_events WHERE data_type = 'org.resolute_eu.esb.model.net.NetworkAnalysis' AND type = 'DEFINITION' ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC LIMIT 1"; //AND event_time >= (NOW() - INTERVAL 2 DAY)
            $result = mysqli_query($esbLink, $query);

            if($result) 
            {
               while($row = mysqli_fetch_assoc($result)) 
               {
                  $analysis['id'] = $row['id'];
                  $analysis['created'] = $row['created'];
                  $analysis['data_id'] = $row['data_id'];
                  $analysis['data_type'] = $row['data_type'];
                  $analysis['event_time'] = $row['event_time'];
                  $analysis['payload'] = $row['payload'];
                  $analysis['payload_class'] = $row['payload_class'];
                  $analysis['sender_id'] = $row['sender_id'];
                  $analysis['type'] = $row['type'];
               }
               $result = json_encode($analysis);
               echo $result;
            }
            else
            {
               echo "queryKo";
            }
            break;   
            
      }//Fine switch
   }


} catch (\mysqli_sql_exception $widgetDatabaseError) {
    dashboardWidgetDatabaseFailure($widgetDatabaseError,isset($link)?$link:(isset($esbLink)?$esbLink:null),$widgetDatabaseGuard,'json');
} catch (\DashboardWidgetIconDirectoryException $widgetDirectoryError) {
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,503,'icon_directory_unavailable','Widget icon directory is unavailable.');
} catch (\PDOException $widgetPdoError) {
    error_log('Widget PostgreSQL operation failed at '.basename($widgetPdoError->getFile()).':'.$widgetPdoError->getLine());
    dashboardWidgetDatabaseHttpFailure($widgetDatabaseGuard,500,'database_error','PostgreSQL database operation failed.');
} finally {
    dashboardWidgetDatabaseEnd($widgetDatabaseGuard);
}
