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

    if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])) 
    {
        header("Content-type: application/json");
        $host = addslashes($_REQUEST['host']);
        $user = addslashes($_REQUEST['user']);
        $pass = addslashes($_REQUEST['pass']); 
        $action = addslashes($_REQUEST['action']);
        $dbName = 'quartz';
        
        $conn = mysqli_connect($host, $user, $pass) or die("Failed to connect to server");
        mysqli_set_charset($conn, 'utf8');
        mysqli_select_db($conn, $dbName);
        
        switch ($action)
        {
            case "getJobGroupsForScheduler":
                $query = "SELECT DISTINCT(JOB_GROUP) FROM QRTZ_JOB_DETAILS";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $resultArray = array();
                if($result->num_rows > 0) 
                {
                    while ($row = mysqli_fetch_array($result)) 
                    {
                        $record = array(
                            "id" => $row['JOB_GROUP']
                        );
                        array_push($resultArray, $record);
                    }
                }
                else 
                {
                    $record = array(
                            "id" => "none"
                        );
                    array_push($resultArray, $record);
                }
                mysqli_close($conn);
                echo json_encode($resultArray);
                break;
                
            case "getJobNamesForJobGroup":
                $jobGroup = addslashes($_REQUEST['jobGroup']); 
                $query = "SELECT JOB_NAME FROM QRTZ_JOB_DETAILS WHERE JOB_GROUP = '$jobGroup'";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $resultArray = array();
                if($result->num_rows > 0) 
                {
                    while ($row = mysqli_fetch_array($result)) 
                    {
                        $record = array(
                            "jobName" => $row['JOB_NAME']
                        );
                        array_push($resultArray, $record);
                    }
                }
                else 
                {
                    $record = array(
                            "jobName" => "none"
                        );
                    array_push($resultArray, $record);
                }
                mysqli_close($conn);
                echo json_encode($resultArray);
                break;    
            
            
            case "getJobGroupsForJobArea":
                $keyword = addslashes($_REQUEST['keyword']);
                $query = "SELECT DISTINCT(JOB_GROUP) FROM QRTZ_JOB_DETAILS WHERE JOB_GROUP LIKE '%$keyword%'";
                $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
                $resultArray = array();
                if($result->num_rows > 0) 
                {
                    while ($row = mysqli_fetch_array($result)) 
                    {
                        $record = array(
                            "jobGroup" => $row['JOB_GROUP']
                        );
                        array_push($resultArray, $record);
                    }
                }
                else 
                {
                    $record = array(
                            "jobGroup" => "none"
                        );
                    array_push($resultArray, $record);
                }
                mysqli_close($conn);
                echo json_encode($resultArray);
                break;
            
            default:
                echo 'Action ' . $action . 'is not valid';
                break;
        }
    }