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
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
ini_set("max_execution_time", 0);
//if (isset($_SESSION['loggedUsername'])) {
/* * ************** */
//if(isset($_SESSION['refreshToken'])) {
$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
$accessToken = $tkn->access_token;
$_SESSION['refreshToken'] = $tkn->refresh_token;

error_reporting(E_ERROR);
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);


//new ACL check
include('../management/ACLInternal.php');
$report_auth = ACLAPI_check_auth(["auth_name" =>"Report Manager",
                    "preferred_username"=>$_SESSION["loggedUsername"]]);
$report_authorized = (isset($report_auth['authorized']) && $report_auth['authorized'] === true);
$auth_result = json_encode($report_auth);

if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];


    if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin") || ($role_session_active == "AreaManager") || ($role_session_active == "Manager")) {
        $login = $report_username;
        $password = $report_password;

        $action = $_REQUEST['action'];
        // TLDR: ACTION = LIST: get all jobs from jasperserver (/rest_v2/jobs), find the one for the device, get periodicity/report link, echo info back
        // example output: {"status":"Yes","period":"monthly","folder":"\/Report_device_monthly","link":"","job":"87"}
        if ($action === 'list') {
            $service = $_REQUEST['service'];
            // This one expects the jobsummary response from rest_v2/jobs to contain a <label> with Device_report_+ $service (eg. Organization:orion-1:trafficLight01)
            // so if future reports aren't working check that they put a label like that/add it/ edit this... 
            $targetLabel = "Device_report_" . $service;
        
            // Get list of job summaries
            $url = $report_server . '/rest_v2/jobs';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            $result = curl_exec($ch);
            curl_close($ch);
        
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array0 = json_decode($json, true);
            $rawJobs = $array0['jobsummary'] ?? [];
			$jobs = isset($rawJobs[0]) ? $rawJobs : [$rawJobs];
            // Find the job with matching label
            $targetJob = null;
            foreach ($jobs as $job) {
                if ($job['label'] === $targetLabel) {
                    $targetJob = $job;
                    break;
                }
            }
            // If no match by label, try matching by parameters (backward compatibility)
            if (!$targetJob) {
                foreach ($jobs as $job) {
                    $jobId = $job['id'];
                    $jobUrl = $url . '/' . $jobId . '/';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $jobUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    $jobDetails = curl_exec($ch);
                    curl_close($ch);

                    $xmlJob = simplexml_load_string($jobDetails);
                    $jsonJob = json_encode($xmlJob);
                    $details = json_decode($jsonJob, true);

                    // Check parameter match
                    $param = $details['source']['parameters']['parameterValues']['entry']['value'] ?? null;
                    if (is_array($param)) {
                        $param = $param['item'] ?? null;
                    }

                    if ($param === $service) {
                        $targetJob = $job;
                        break;
                    }
                }
            }
            $report_activated = [
                'status' => 'No',
                'period' => 'No',
                'folder' => '',
                'link' => '',
                'job' => 0
            ];
        
            if ($targetJob) {
                $report_activated['status'] = 'Yes';
                $jobId = $targetJob['id'];
                $report_activated['job'] = $jobId;
        
                // Fetch full job info
                $jobUrl = $url . '/' . $jobId . '/';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $jobUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                $jobDetails = curl_exec($ch);
                curl_close($ch);
        
                $xml = simplexml_load_string($jobDetails);
                $json = json_encode($xml);
                $details = json_decode($json, true);
        
                // Extract trigger info
                if (isset($details['simpleTrigger'])) {
                    $interval_unit = $details['simpleTrigger']['recurrenceIntervalUnit'];
                    $count_months = 0;
                } elseif (isset($details['calendarTrigger'])) {
                    $months = $details['calendarTrigger']['months']['month'];
                    $count_months = is_array($months) ? count($months) : 1;
                    $interval_unit = $details['calendarTrigger']['daysType'];
                } else {
                    $interval_unit = '';
                    $count_months = 0;
                }
        
                // Determine report period
                if ($interval_unit === 'HOUR') {
                    $report_activated['period'] = 'hourly';
                } elseif ($count_months === 12) {
                    $report_activated['period'] = 'monthly';
                } elseif ($count_months === 4) {
                    $report_activated['period'] = 'quarterly';
                } else {
                    $report_activated['period'] = 'error';
                }
        
                // Get report folder
                $folder = $details['repositoryDestination']['folderURI'] ?? '';
                $report_activated['folder'] = $folder;
        
                // Fetch resources in folder to build report link
                //$resourceUrl = $report_server . '/rest_v2/resources/?folderUri=' . urlencode($folder) . '&sortBy=creationDate&type=file';
                // this second resourceUrl is expecting file names with $service in it!
                $needle = preg_replace("/[^a-z0-9\_\-\.]/i", '', $service);
                $resourceUrl = $report_server . '/rest_v2/resources?q=' . urlencode($needle) . '&folderUri=' . urlencode($folder) . '&sortBy=creationDate&type=file';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $resourceUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                $resResult = curl_exec($ch);
                curl_close($ch);
        
                $xml = simplexml_load_string($resResult);
                $json = json_encode($xml);
                $resources = json_decode($json, true);
        
                
                $resourceList = $resources['resourceLookup'] ?? [];
        
                if (!empty($resourceList)) {
                    if (isset($resourceList[0])) {
                        // Multiple files in folder
                        $match = array_filter($resourceList, function ($r) use ($needle) {
                            return strpos($r['label'], $needle) !== false;
                        });
        
                        $fileUri = reset($match)['uri'] ?? $resourceList[0]['uri'];
                    } else {
                        // Single file
                        $fileUri = $resourceList['uri'] ?? '';
                    }
        
                    $report_activated['link'] = $fileUri ? ($report_server . '/fileview/fileview/' . $fileUri) : '';
                }
            }
            echo json_encode($report_activated);
        }else if ($action == 'edit') {
            // 
            if (($role_session_active == "RootAdmin") || $report_authorized === true) {
                $activation = $_REQUEST['activation'];
                $periods = $_REQUEST['periods'];
                if($periods == 'hourly' && $role_session_active != "RootAdmin"){
                    $periods = "monthly";
                }
                $service = $_REQUEST['service'];
                $httpcode = '';
                $report['activation'] = ($activation);
                $report['period'] = ($periods);
                $date001 = date('Y-m-01 00:00:00', strtotime('first day of next month'));
                $report['service'] = ($service);
                $iterval = $monthly_recurrenceInterval;
                $baseout = $monthly_baseOutputFilename;
                $folder_uri_output = $folder_uri;
                if ($periods == 'monthly') {
                    $iterval = $monthly_recurrenceInterval;
                    $recurrenceIntervalUnit = $monthly_recurrenceIntervalUnit;
                    $baseout = $monthly_baseOutputFilename;
                    $date001 = date('Y-m-01 00:00:00', strtotime('first day of next month'));
                    $array_months = array("1", "10", "11", "12", "2", "3", "4", "5", "6", "7", "8", "9");
                    $report_s = $monthly_report_model;
                $folder_uri_output =$monthly_folder_uri;
                } elseif ($periods == 'quarterly') {
                    $iterval = $quarterly_recurrenceInterval;
                    $baseout = $quarterly_baseOutputFilename;
                    $recurrenceIntervalUnit = $quarterly_recurrenceIntervalUnit;
                    $date001 = date('Y-m-01 00:00:00', strtotime('first day of next month'));
                    $array_months = array("1", "10", "4", "7");
                    $report_s = $quarterly_report_model;
                    $folder_uri_output = $quarterly_folder_uri;
                } elseif ($periods == 'hourly') {
                    $iterval = 1;
                    $baseout = $hourly_baseOutputFilename;
                    $recurrenceIntervalUnit = 'ALL';
                    //$date001 = date("Y-m-d", strtotime('01-' . date('m', strtotime('+1 month')) . '-' . date('Y') . ' 00:00:00'));
                    date_default_timezone_get('UTC');
                    $init_date = date("Y-m-d H:i:s");
                    //adattare alla TIME ZONE//
                    
                    $date001 = gmdate("Y-m-d  H:00:00",strtotime($init_date. " + 1hour "));
                    //$date001 ->setTimeZone('UTC');
                    //$init_date = date("Y-m-d H:i:s");
                    //$date001 = $init_date;
                    //$date002  = $init_date2 .' '.$date001
                    $array_months = array("1", "10", "11", "12", "2", "3", "4", "5", "6", "7", "8", "9");
                    $report_s = $hourly_report_model;
                    $folder_uri_output = $hourly_folder_uri;
                    //hourly
                } else {
                    $iterval = $monthly_recurrenceInterval;
                    $baseout = $monthly_baseOutputFilename;
                    $recurrenceIntervalUnit = $monthly_recurrenceIntervalUnit;
                    $date001 = date('Y-m-01 00:00:00', strtotime('first day of next month'));
                    $array_months = array("1", "10", "11", "12", "2", "3", "4", "5", "6", "7", "8", "9");
                    $report_s = $monthly_report_model;
                    $folder_uri_output = $folder_uri;
                }


                $service_sanitized = preg_replace("/[^a-z0-9\_\-\.]/i", '', $service);

                $name = $baseout . '_' . $service_sanitized . '_' . $periods;
                $result1 = "";
                $report['result'] = "";

                $description = 'Schedule Device' . $service . ' ' . $periods;
/////
                $source_data = array();
                $source_data['reportUnitURI'] = $report_s;
                $source_data['parameters'] = array(); 
                //$source_data['parameters']['parameterValues'] = "";
                $source_data['parameters']['parameterValues']['unique_id'][0] = $service;
//
                $json_source = json_encode($source_data);
//
                //$start_date = "2021-02-22 12:00";
                // $start_date = "2021-02-22T14:50:00.000+01:00";
//echo($date001);
                //$date = date('Y-m-d');
                $date = $date001;
                $time = date('H:i:s');
                //
                $time = '00:00:00';
                //$start_date = $date . 'T' . $time . '+01:00';
                $start_date = $date . ' ' . $time;
                //var_dump($start_date);
                $trgger_data = array();
                /*
                  $trgger_data['simpleTrigger'] = "";
                  $trgger_data['simpleTrigger'] = 1;
                  $trgger_data['simpleTrigger']['timezone'] = "Europe/Berlin";
                  $trgger_data['simpleTrigger']['startType'] = "2";
                  $trgger_data['simpleTrigger']['startDate'] = $start_date;
                  $trgger_data['simpleTrigger']['endDate'] = null;
                  $trgger_data['simpleTrigger']['occurrenceCount'] = "-1";
                  $trgger_data['simpleTrigger']['recurrenceInterval'] = $iterval;
                  $trgger_data['simpleTrigger']['recurrenceIntervalUnit'] = $recurrenceIntervalUnit;
                 */
                //$array_months= array("1", "10", "11", "12", "2", "3", "4","5", "6", "7", "8", "9");
                //
            /*
                  $trigger_data['calendarTrigger'] = "";
                  $trigger_data['calendarTrigger']['version'] = 0;
                  $trigger_data['calendarTrigger']['timezone'] = 'Etc/UTC';
                  $trigger_data['calendarTrigger']['startType'] = 2;
                  $trigger_data['calendarTrigger']['startDate'] = $start_date;
                  $trigger_data['calendarTrigger']['misfireInstruction'] = 0;
                  $trigger_data['calendarTrigger']['minutes'] = "0";
                  $trigger_data['calendarTrigger']['hours'] = "0";
                  $trigger_data['calendarTrigger']['daysType'] = $recurrenceIntervalUnit;
                  $trigger_data['calendarTrigger']['monthDays'] = "1";
                  $trigger_data['calendarTrigger']['months']['month'] = $array_months; */
                //$trgger_data['simpleTrigger']['recurrenceIntervalUnit'] = "MINUTE";

                if ($periods == 'hourly') {
                    $trigger_data['simpleTrigger'] = array();
                    $trigger_data['simpleTrigger']['version'] = 1;
                    $trigger_data['simpleTrigger']['timezone'] = "Etc/UTC";
                    $trigger_data['simpleTrigger']['startType'] = 2;
                    $trigger_data['simpleTrigger']['startDate'] = $date001;
                    $trigger_data['simpleTrigger']['endDate'] = null;
                    $trigger_data['simpleTrigger']['occurrenceCount'] = "-1";
                    $trigger_data['simpleTrigger']['recurrenceInterval'] = "1";
                    $trigger_data['simpleTrigger']['recurrenceIntervalUnit'] = "HOUR";
                } else {
                    $trigger_data['calendarTrigger'] = array();
                    $trigger_data['calendarTrigger']['version'] = 0;
                    $trigger_data['calendarTrigger']['timezone'] = 'Etc/UTC';
                    $trigger_data['calendarTrigger']['startType'] = 2;
                    $trigger_data['calendarTrigger']['startDate'] = $start_date;
                    $trigger_data['calendarTrigger']['misfireInstruction'] = 0;
                    $trigger_data['calendarTrigger']['minutes'] = "0";
                    $trigger_data['calendarTrigger']['hours'] = "0";
                    $trigger_data['calendarTrigger']['daysType'] = $recurrenceIntervalUnit;
                    $trigger_data['calendarTrigger']['monthDays'] = "1";
                    $trigger_data['calendarTrigger']['months']['month'] = $array_months;
                }

                //
                $json_trigger = json_encode($trgger_data);
////
                $data_job = array();
                $data_job['label'] = "Device_report_" . $service;
                $data_job['description'] = $description;
                $data_job['trigger'] = $trigger_data;
                $data_job['source'] = $source_data;
                $data_job['baseOutputFilename'] = $name;
                $data_job['outputTimeZone'] = "Etc/UTC";
                $data_job['repositoryDestination']['folderURI'] = $folder_uri_output;
                $data_job['repositoryDestination']['overwriteFiles'] = false;
                $data_job['repositoryDestination']['sequentialFilenames'] = true;
                $data_job['repositoryDestination']['saveToRepository'] = true;
                $data_job['repositoryDestination']['timestampPattern'] = 'yyyyMMddHHmm';
                $data_job['outputFormats']['outputFormat'][0] = "PDF";

                $json_data = json_encode($data_job);
//print_r($json_data);
///////////************////
                if ($activation === 'true') {

                    if (isset($_REQUEST['jobs'])) {
                        $job = $_REQUEST['jobs'];
                    } else {
                        $job = 0;
                    }

                    if ($job != 0) {
                        $code = $job;
                        //
                        $ch01 = curl_init();
                        $url02 = $report_server . '/rest_v2/jobs?id=' . $code;
                        curl_setopt($ch01, CURLOPT_URL, $url02);
                        curl_setopt($ch01, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch01, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($ch01, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        // curl_setopt($ch1, CURLOPT_POSTFIELDS, $json_data);
                        curl_setopt($ch01, CURLOPT_USERPWD, "$login:$password");
                        curl_setopt($ch01, CURLOPT_HTTPHEADER, array(
                            'Content-Type:application/json'
                                )
                        );
                        $result01 = curl_exec($ch01);
                        $httpcode0 = curl_getinfo($ch01, CURLINFO_HTTP_CODE);
                        //
                        echo curl_error($ch01);
                        //echo('HTTP CODE: '.$httpcode);
                    }
                    $ch1 = curl_init();
                    $url2 = $report_server . '/rest_v2/jobs';
                    //echo($url2);
                    curl_setopt($ch1, CURLOPT_URL, $url2);
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch1, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch1, CURLOPT_POSTFIELDS, $json_data);
                    curl_setopt($ch1, CURLOPT_USERPWD, "$login:$password");
                    curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                        'Content-Type:application/json'
                            )
                    );
                    $result1 = curl_exec($ch1);
                    $httpcode = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                    //
                    //   echo curl_error($ch1);
                    //echo('HTTP CODE: '$httpcode.$httpcode);
                    //
   // echo curl_error($ch1);
                    //var_dump($result1);
                    curl_close($ch1);
                } elseif ($activation === 'false') {
                    //DELETE
                    if (isset($_REQUEST['jobs'])) {
                        $job = $_REQUEST['jobs'];
                    } else {
                        $job = 0;
                    }

                    if ($job != 0) {
                        $code = $job;
                        //
                        $ch1 = curl_init();
                        $url2 = $report_server . '/rest_v2/jobs?id=' . $code;
                        curl_setopt($ch1, CURLOPT_URL, $url2);
                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($ch1, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        // curl_setopt($ch1, CURLOPT_POSTFIELDS, $json_data);
                        curl_setopt($ch1, CURLOPT_USERPWD, "$login:$password");
                        curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                            'Content-Type:application/json'
                                )
                        );
                        $result1 = curl_exec($ch1);
                        $httpcode = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                        //
                        echo curl_error($ch1);
                        //echo('HTTP CODE: '.$httpcode);
                    }
                } else {
                    
                }
                //
                $report['input'] = $data_job;
                $report['result'] = $result1;
                $report['code'] = $httpcode;
                echo json_encode($report);
////
            } else {
                http_response_code(401);
                echo ("You are not authorized to access to this data! ACL Result:" . $auth_result );
                exit;
            }
        } else {
            http_response_code(400);
            echo("No action found");
        }
    } else {
        http_response_code(401);
        echo ("You are not authorized to access to this data! ACL Result:" . $auth_result );
        exit;
    }
} else {
    http_response_code(401);
    echo ("You are not authorized to access to this data! ACL Result:" . $auth_result );
    exit;
}
?>
