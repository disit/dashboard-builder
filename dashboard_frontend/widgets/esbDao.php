<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
               $query = "SELECT * FROM resolute_events WHERE data_type = 'org.resolute_eu.esb.model.traffic.TrafficInformation' AND type = 'DEFINITION' AND event_time >= (NOW() - INTERVAL " . $time . " " . $timeUdm . ") ORDER BY STR_TO_DATE(event_time,'%Y-%m-%d %H:%i:%s') DESC"; 
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
            /*$fakeEvents = '{  
                "1":{  
                   "id":"1",
                   "created":"2017-10-25 09:12:07.870",
                   "data_id":"urn:rixf:it.swarco.resolute/trafficInformationId/1",
                   "data_type":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "event_time":"2017-10-25 09:12:07.870",
                   "payload":"{\"arc_ids\":[\"urn:rixf:it.swarco.resolute/arcId/RT04801727938ES\"],\"code\":\"1062652\",\"coords\":{\"latitude\":43.79784805,\"longitude\":11.21094064},\"id\":\"urn:rixf:it.swarco.resolute/trafficInformationId/1062652\",\"notes\":\"INCIDENTE A FIRENZE - VIA GARFAGNANA\",\"severity\":8,\"source\":\"1062652\",\"start_time\":1508923829000,\"stop_time\":1508923829000,\"sub_type_id\":\"urn:rixf:it.swarco.resolute/subTypeId/9\",\"type_id\":\"urn:rixf:it.swarco.resolute/typeId/1\",\"update_time\":1508923829000}",
                   "payload_class":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "sender_id":null,
                   "type":"DEFINITION"
                },
                "2":{  
                   "id":"2",
                   "created":"2017-10-24 17:07:40.870",
                   "data_id":"urn:rixf:it.swarco.resolute/trafficInformationId/3",
                   "data_type":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "event_time":"2017-10-24 17:07:40.870",
                   "payload":"{\"arc_ids\":[\"urn:rixf:it.swarco.resolute/arcId/RT04801727938ES\"],\"code\":\"1062652\",\"coords\":{\"latitude\":43.779585,\"longitude\":11.272084},\"id\":\"urn:rixf:it.swarco.resolute/trafficInformationId/1062652\",\"notes\":\"INCIDENTE A FIRENZE - VIA MASACCIO\",\"severity\":5,\"source\":\"1062652\",\"start_time\":1508870669000,\"stop_time\":1508870669000,\"sub_type_id\":\"urn:rixf:it.swarco.resolute/subTypeId/9\",\"type_id\":\"urn:rixf:it.swarco.resolute/typeId/1\",\"update_time\":1508870669000}",
                   "payload_class":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "sender_id":null,
                   "type":"DEFINITION"
                },
                "3":{  
                   "id":"3",
                   "created":"2017-10-24 15:12:25.870",
                   "data_id":"urn:rixf:it.swarco.resolute/trafficInformationId/3",
                   "data_type":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "event_time":"2017-10-24 15:12:25.870",
                   "payload":"{\"arc_ids\":[\"urn:rixf:it.swarco.resolute/arcId/RT04801727938ES\"],\"code\":\"1062652\",\"coords\":{\"latitude\":43.763904,\"longitude\":11.276618},\"id\":\"urn:rixf:it.swarco.resolute/trafficInformationId/1062652\",\"notes\":\"INCIDENTE A FIRENZE - VIA MASACCIO\",\"severity\":2,\"source\":\"1062652\",\"start_time\":1508857945000,\"stop_time\":1508857945000,\"sub_type_id\":\"urn:rixf:it.swarco.resolute/subTypeId/84\",\"type_id\":\"urn:rixf:it.swarco.resolute/typeId/25\",\"update_time\":1508857945000}",
                   "payload_class":"org.resolute_eu.esb.model.traffic.TrafficInformation",
                   "sender_id":null,
                   "type":"DEFINITION"
                }
             }';
             
            echo $fakeEvents;*/
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
                  
                  $query2 = "SELECT JSON_EXTRACT(payload, '$.evacuation_plan_status') AS lastStatus FROM resolute_events WHERE data_id = '$dataId' AND data_type = 'org.resolute_eu.esb.model.evac.EvacuationPlan' AND type = 'UPDATE' ORDER BY created DESC LIMIT 1";
                  $result2 = mysqli_query($esbLink, $query2);
                  
                  if(mysqli_num_rows($result2) > 0)
                  {
                     while($row2 = mysqli_fetch_assoc($result2)) 
                     {
                        $planRow['lastStatus'] = str_replace('"', '', $row2['lastStatus']);
                     }
                  }
                  else
                  {
                     $planRow['lastStatus'] = "PROPOSED";
                  }
                  
                  $plans[$planRow['id']] = $planRow;
               }
               $result = json_encode($plans);
               echo $result;
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

