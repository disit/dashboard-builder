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
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
ini_set("max_execution_time", 0);
error_reporting(E_ERROR);
//if (isset($_SESSION['loggedUsername'])) {
/* * ************** */
//if(isset($_SESSION['refreshToken'])) {
$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$tkn = $oidc->refreshToken($_SESSION['refreshToken']);
$accessToken = $tkn->access_token;
$_SESSION['refreshToken'] = $tkn->refresh_token;

//error_reporting(E_ERROR);
$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//error_reporting(-1);
mysqli_select_db($link, $dbname);
if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];


    if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin") || ($role_session_active == "AreaManager") || ($role_session_active == "Manager")) {
        $login = $report_username;
        $password = $report_password;

        $action = $_REQUEST['action'];
        if ($action == 'list') {
            $url = $report_server . '/rest_v2/jobs';
            //
            // echo($url);
            $service = $_REQUEST['service'];
//LIST OF SCHEDULER//
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json'
                    )
            );
            $result = curl_exec($ch);
            curl_close($ch);

            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $array0 = json_decode($json, TRUE);
            $array = $array0['jobsummary'];
//
//print_r($result);
//
            $n = count($array);
//echo($n);
            $report_actived = array();
            $report_actived['status'] = "No";
            $report_actived['period'] = "No";
            $report_actived['folder'] = '';
            $report_actived['link'] = '';
            $report_actived['job'] = 0;
///
            for ($i = 0; $i < $n; $i++) {
                //echo('<br />');
                //echo($array[$i]['id']);
                $id = $array[$i]['id'];

                $url1 = $url . '/' . $id . '/';
                ///////
                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, $url1);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch1, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch1, CURLOPT_USERPWD, "$login:$password");
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                    'Content-Type:application/json'
                        )
                );
                $result1 = curl_exec($ch1);
                curl_close($ch1);
                $xml0 = simplexml_load_string($result1);
                $json0 = json_encode($xml0);
                $array00 = json_decode($json0, TRUE);
                //echo($json0);
                // var_dump($array00);
                if (array_key_exists('simpleTrigger', $array00)) {
                    $interval_unit = $array00['simpleTrigger']['recurrenceIntervalUnit'];
                    $interval = $array00['simpleTrigger']['recurrenceInterval'];
                    $count_mounths = 0;
                } elseif (array_key_exists('calendarTrigger', $array00)) {
                    $interval_unit = $array00['calendarTrigger']['daysType'];
                    $interval = $array00['calendarTrigger']['monthDays'];
                    $count_mounths = $array00['calendarTrigger']['months']['month'];
                } else {
                    $interval_unit = "";
                    $interval = "";
                    $count_mounths = 0;
                }

                //
                $folder = $array00['repositoryDestination']['folderURI'];
                //
                $count_months = count($count_mounths);
                //echo('$count_months: '.$count_months.'<br />');
                //$param = $array00['source']['parameters']['parameterValues']['entry'][0]['value'];
                $param = $array00['source']['parameters']['parameterValues']['entry']['value'];
                // echo('<br />');
                if (is_array($param)) {
                    $param = $param['item'];
                    //print_r($param);
                }
//$array = $array0['jobsummary'];
                if ($service == $param) {
                    // echo "Yes";
                    $report_actived['status'] = "Yes";
                    $report_actived['folder'] = $folder;
                    //SEARCH DATA
                    //$folder
                    //$url2 = $url . '/' . $id . '/';

                    $descr_rep = 'Monthly Device Report';
                    $url2 = $report_server . '/rest_v2/resources/?folderUri=' . $folder . '&sortBy=creationDate&type=file';
                    ///////
                    $ch2 = curl_init();
                    curl_setopt($ch2, CURLOPT_URL, $url2);
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch2, CURLOPT_USERPWD, "$login:$password");
                    curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                        'Content-Type:application/json'
                            )
                    );
                    $result2 = curl_exec($ch2);
                    curl_close($ch2);
                    //var_dump($result2);    
                    $xml2 = simplexml_load_string($result2);
                    $json2 = json_encode($xml2);
                    $array02 = json_decode($json2, TRUE);
                    //print_r($array02);
                    //
            $lenght = count($array02['resourceLookup']);
                    //echo('count: '.$lenght.' ');
                    if ($lenght > 0) {
                        /*                         * *** */
                        //$needle = $service;
                        $needle = preg_replace("/[^a-z0-9\_\-\.]/i", '', $service);
                        //
                        $ret = array_keys(array_filter($array02['resourceLookup'], function($var) use ($needle) {
                                    return strpos($var['label'], $needle) !== false;
                                }));
                        /*                         * * */
                        // $ret1 = count($ret);
                        //echo('$ret: '.$ret1.' ');
                        //
            $report_actived['link'] = $array02;
                        //
                        if (count($ret) > 0) {
                            $pos = $ret[0];
                            $uri0 = $array02['resourceLookup'][$pos]['uri'];
                            $uri = $report_server . '/fileview/fileview/' . $uri0;
                        } else {
                            $pos = 0;
                            $uri0 = $array02['resourceLookup'][$pos]['uri'];
                            $uri = "";
                            //echo($array02['resourceLookup']['uri']);
                            if (array_key_exists('uri', $array02['resourceLookup'])){
                                $uri0 = $array02['resourceLookup']['uri'];
                                 $uri = $report_server . '/fileview/fileview/' . $uri0;
                            }else{
                            $uri = "";
                            }
                            }
                        //$uri0 = $ret[0]['uri'];
                        // $uri0 = $array02['resourceLookup'][0]['uri'];
                    } else {
                        $uri = "";
                    }

                    $report_actived['link'] = $uri;
                    $report_actived['job'] = $id;
                    /////*******RICERCA DELL'ULTIMO JOB****//
                    //
            /*
                      if (($interval == $monthly_recurrenceInterval) && ($interval_unit == $monthly_recurrenceIntervalUnit)){
                      $report_actived['period'] = "monthly";
                      }
                      elseif (($interval == $quarterly_recurrenceInterval) && ($interval_unit == $quarterly_recurrenceIntervalUnit)){
                      $report_actived['period'] = "quarterly";
                      }else{
                      $report_actived['period'] = "error";
                      } */

                    if ($interval_unit == 'HOUR') {
                        $report_actived['period'] = "hourly";
                    } else {
                        if ($count_months == 12) {
                            $report_actived['period'] = "monthly";
                        } else if ($count_months == 4) {
                            $report_actived['period'] = "quarterly";
                        } else {
                            $report_actived['period'] = "error";
                        }
                    }
                    //
                } else {
                    //echo('No param');
                }

                /////
            }
            echo json_encode($report_actived);
        } else if ($action == 'edit') {
            // 
            if (($role_session_active == "RootAdmin")) {
                $activation = $_REQUEST['activation'];
                $periods = $_REQUEST['periods'];
                $service = $_REQUEST['service'];
                $httpcode = '';
                $report['activation'] = ($activation);
                $report['period'] = ($periods);
                $date001 = date("Y-m-d", strtotime('01-' . date('m', strtotime('+1 month')) . '-' . date('Y') . ' 00:00:00'));
                $report['service'] = ($service);
                $iterval = $monthly_recurrenceInterval;
                $baseout = $monthly_baseOutputFilename;
                $folder_uri_output = $folder_uri;
                if ($periods == 'monthly') {
                    $iterval = $monthly_recurrenceInterval;
                    $recurrenceIntervalUnit = $monthly_recurrenceIntervalUnit;
                    $baseout = $monthly_baseOutputFilename;
                    $date001 = date("Y-m-d", strtotime('01-' . date('m', strtotime('+1 month')) . '-' . date('Y') . ' 00:00:00'));
                    $array_months = array("1", "10", "11", "12", "2", "3", "4", "5", "6", "7", "8", "9");
                    $report_s = $monthly_report_model;
                $folder_uri_output =$monthly_folder_uri;
                } elseif ($periods == 'quarterly') {
                    $iterval = $quarterly_recurrenceInterval;
                    $baseout = $quarterly_baseOutputFilename;
                    $recurrenceIntervalUnit = $quarterly_recurrenceIntervalUnit;
                    $date001 = date("Y-m-d", strtotime('01-' . date('m', strtotime('+1 month')) . '-' . date('Y') . ' 00:00:00'));
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
                    $date001 = date("Y-m-d", strtotime('01-' . date('m', strtotime('+1 month')) . '-' . date('Y') . ' 00:00:00'));
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
                echo ("You are not authorized to access to this data!");
                exit;
            }
        } else {
            //NOTHING
        }
    } else {
        echo ("You are not authorized to access to this data!");
        exit;
    }
} else {
    echo ("You are not authorized to access to this data!");
    exit;
}
?>
