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

function isValidDateTimeString($str_dt, $str_dateformat, $str_timezone) {
    $date = DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
    return $date && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0;
}

function isValidDateTime ($str_dt, $str_dateformat) {
    return DateTime::createFromFormat($str_dateformat, $str_dt);
}

function validHex($hex) {
    return preg_match('/^#?(([a-f0-9]{3}){1,2})$/i', $hex);
}

function validColorCode($code) {
    //$pattern = "^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$";
    //$pattern = "/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i'";
    $pattern = "/(#[\d\w]+|\w+\((?:\d+%?(?:,\s)*){3}(?:\d*\.?\d+)?\))/i";
    eventlog("REGEXP Result: " . preg_match($pattern, $code));
    if (preg_match($pattern, $code) != 1) {
        return false;
    } else {
        return true;
    }
}

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

session_start();
checkSession('Manager');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    eventLog("Uploaded File: " . $_FILES["jsonFile"]["name"] . "; type: " . $_FILES["jsonFile"]["type"]);
    if(strpos($_FILES["jsonFile"]["type"], "json") == false) {
        eventLog('Import Error not JSON.');
        //mysqli_rollback($link);
        echo "Import Error: uploaded file must be a JSON.";
        exit();
    }
    if ($_FILES["jsonFile"]["error"] == UPLOAD_ERR_OK) {
        $jsonData = file_get_contents($_FILES["jsonFile"]["tmp_name"]);
        $data = json_decode($jsonData, true);
        if (is_null($data)) {
            eventLog('Import Error not VALID JSON.');
            //mysqli_rollback($link);
            echo "Import Error: not valid JSON.";
            exit();
        } else {
            if (is_null($data['Dashboard'])) {
                eventLog('Import Error not VALID Dashboard JSON.');
                //mysqli_rollback($link);
                echo "Import Error: not valid Dashboard JSON.";
                exit();
            //} else if (empty(trim($data['Dashboard']['color_header']))) {
            } else if (empty(trim($data['Dashboard']['color_header'])) || !validColorCode($data['Dashboard']['color_header'])) {
                eventLog('Import Error INVALID COLOR HEADER attribute.');
                //mysqli_rollback($link);
                echo "Import Error: invalid 'color_header' attribute.";
                exit();
            } else {
                if (empty(trim($data['Dashboard']['user']))) {
                    eventLog('Import Error INVALID USER.');
                    //mysqli_rollback($link);
                    echo "Import Error: invalid 'user' attribute.";
                    exit();
                } else {
                    if (empty(trim($data['Dashboard']['creation_date'])) ||
                        !isValidDateTime($data['Dashboard']["creation_date"], "Y-m-d H:i:s") ||
                        empty(trim($data['Dashboard']['last_edit_date'])) ||
                        !isValidDateTime($data['Dashboard']["last_edit_date"], "Y-m-d H:i:s")) {
                        eventLog('Import Error DASHBOARD NAME.');
                        //mysqli_rollback($link);
                        echo "Import Error: Invalid Date Time Format.";
                        exit();
                    } else {
                        if (empty(trim($_POST["nomeDashboard"]))) {
                            eventLog('Import Error DASHBOARD NAME.');
                            //mysqli_rollback($link);
                            echo "Import Error: Dashboard Name can not be empty.";
                            exit();
                        }
                    }
                }
            }
        }
        eventlog('Name_dashboard: ' . $data['Dashboard']['name_dashboard']);
        //eventLog('Import - Data: ' . $data);
        $userLog = $_SESSION['loggedUsername']; 
        $org = $_SESSION['loggedOrganization'];
        #eventLog('Import.php ' . 'host=' . $host . ' username=' . $username . ' password=' . $password . ' user=' . $userLog . ' dbname=' . $dbname);
        $dashboardData = $data['Dashboard'];
        $widgetData = $data['Widget'];

        $newDashboardTitle = $_POST["nomeDashboard"]; 
        
        $dashboardData['name_dashboard'] = $newDashboardTitle;
        $dashboardData['title_header'] = $newDashboardTitle;
        $dashboardData['user'] = $userLog;
        $dashboardData['organizations'] = $org;

        // connessione al db
        $link = mysqli_connect($host, $username, $password);
        mysqli_select_db($link, $dbname);

        $query2 = "INSERT INTO Dashboard.Config_dashboard(";        
        $newQueryFields = "";
        $newQueryValues = "";

        $count = 0;
        foreach($dashboardData as $key => $value) 
        {
            if($count == 0)
            {
                $newQueryFields = $key;
                if (strpos($value, '&#39;') !== false) {
                    $value_aux = html_entity_decode($value, ENT_QUOTES|ENT_HTML5);
                    $newQueryValues = returnManagedStringForDb(escapeForSQL($value_aux, $link));
                } else {
                    $newQueryValues = returnManagedStringForDb($value);
                }
            }
            else
            {
                $newQueryFields = $newQueryFields . ", " . $key;
                
                if ($key == 'subtitle_header')
                {
                    $newQueryValues = $newQueryValues . ", '$value'";
                } else if ($key == 'title_header') {
                    if (strpos($value, '&#39;') !== false) {
                        $value_aux = html_entity_decode($value, ENT_QUOTES|ENT_HTML5);
                        $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(escapeForSQL($value_aux, $link));
                    } else {
                        $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                    }
                }
                else
                {
                    $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                }
                
            }

            $count++;
        }

        $query2 = $query2 . $newQueryFields;
        $query2 = $query2 . ") VALUES(";
        $query2 = $query2 . $newQueryValues;
        $query2 = $query2 . ")";

        mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);                
        $result2 = mysqli_query($link, $query2);

        if($result2)
        {
            $title = $_POST["nomeDashboard"];
            $newId = mysqli_insert_id($link);

            //Salvataggio su API ownership
            if(isset($_SESSION['refreshToken']))
            {
                $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                $accessToken = $tkn->access_token;
                $_SESSION['refreshToken'] = $tkn->refresh_token;

                $callBody = ["elementId" => $newId, "elementType" => "DashboardID", "elementName" => $title];

                eventLog('$ownershipApiBaseUrl' . $ownershipApiBaseUrl);

                $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n",
                        'method'  => 'POST',
                        'timeout' => 30,
                        'content' => json_encode($callBody),
                        'ignore_errors' => true
                    )
                );

                try
                {
                    $context  = stream_context_create($options);
                    $callResult = @file_get_contents($apiUrl, false, $context);
                    $callResultArray = json_decode($callResult, true);
                }
                catch (Exception $ex)
                {
                    //Non facciamo niente di specifico in caso di mancata risposta dell'host
                    $stopFlag = 1;
                }
            }
            #mysqli_commit($link);

            if($callResultArray['elementId']){
                $clonedDashId = mysqli_insert_id($link); // uguale alla variabile $newId? 

                $query3 = "SELECT AUTO_INCREMENT AS MaxId FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
                $result3 = mysqli_query($link, $query3);

                if($result3)
                {
                    $row3 = mysqli_fetch_array($result3);
                    $maxId = $row3['MaxId'];

                    
                    foreach($widgetData as $key => $row4)
                    {
                        $sourceWidgetName = $row4['name_w'];
                        $clonedWidgetId = $maxId;

                        //Costruzione del nome del widget importato
                        switch ($row4['type_w']) {
                            case 'widgetSce':
                                //Sostituzione del vecchio Id widget col nuovo Id Widget
                                $clonedWidgetName = preg_replace('~widgetSce\d*~', 'widgetSce' . $clonedWidgetId, $row4['name_w']);
                                //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                break;

                            case 'widgetTimeTrend':
                                //Sostituzione del vecchio Id widget col nuovo Id Widget
                                $clonedWidgetName = preg_replace('~widgetTimeTrend\d*~', 'widgetTimeTrend' . $clonedWidgetId, $row4['name_w']);
                                //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                break;

                            case 'widgetTimeTrendCompare':
                                //Sostituzione del vecchio Id widget col nuovo Id Widget
                                $clonedWidgetName = preg_replace('~widgetTimeTrendCompare\d*~', 'widgetTimeTrendCompare' . $clonedWidgetId, $row4['name_w']);
                                //Sostituzione del vecchio Id dashboard col nuovo Id dashboard
                                $clonedWidgetName = preg_replace("/_\d+\_/", "_" . $clonedDashId . "_", $clonedWidgetName);
                                break;

                            default:
                                $clonedWidgetName = $row4['id_metric'] . '_' . $clonedDashId . '_' . $row4['type_w'] . $clonedWidgetId;
                                $clonedWidgetName = str_replace(':', '_', $clonedWidgetName);
                                $clonedWidgetName = "w_" . preg_replace('/\s+/', '_', $clonedWidgetName);
                                break;
                        }

                        $row4['name_w'] = $clonedWidgetName;
                        $row4['id_dashboard'] = $clonedDashId;
                        $row4['creator'] = $_SESSION['loggedUsername'];

                        $widgetsMap[$sourceWidgetName] = $clonedWidgetName; // ????

                        $query5 = "INSERT INTO Dashboard.Config_widget_dashboard(";
                        $newQueryFields = "";
                        $newQueryValues = "";


                        $count = 0;
                        foreach ($row4 as $key => $value) {
                            if ($count == 0) {
                                 $newQueryFields = $key;
                                 $newQueryValues = returnManagedStringForDb($value);
                            } else {
                                 $newQueryFields = $newQueryFields . ", " . $key;
                                if (($key == "parameters" && $row4['type_w'] == "widgetMap") || ($key == "parameters" && $row4['type_w'] == "widget3DMapDeck") || ($key == "styleParameters" && $row4['type_w'] == "widgetTable"))  {
                                 //    $newQueryFields = $newQueryFields . ", " . $key;
                                     $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(escapeForSQL($value, $link));
                                } else {
                                 //    $newQueryFields = $newQueryFields . ", " . $key;
                                    if ($key == "title_w") {
                                         $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(escapeForSQL($value, $link));
                                    } else if ($key == "code") {
                                        //in php one backslash is always eaten. So i duplicate before save it maintaing always one 
                                        if($value !== null){
                                            if(strpos($value, '\\') !== false){
                                                $value = str_replace('\\', '\\\\', $value);
                                            }
                                        }
                                         $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(addcslashes($value, "'"));
                                     }else {
                                         $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
                                    }
                                }
                            }

                            $count++;
                        }
                        $query5 = $query5 . $newQueryFields;
                        $query5 = $query5 . ") VALUES(";
                        $query5 = $query5 . $newQueryValues;
                        $query5 = $query5 . ")";

                        $result5 = mysqli_query($link, $query5);
                        if (!$result5) {
                            eventLog('Error DB query.');
                            mysqli_rollback($link);
                            echo "Ko";
                            exit();
                        } else {
                            $maxId++;
                        }
                    }// foreach

                    mysqli_commit($link);

                    //Aggiornamento dei campi dei parametri per interazioni cross widget
                    foreach ($widgetsMap as $originalW => $clonedW) {
                        $updParamsQ = "UPDATE Dashboard.Config_widget_dashboard " .
                            "SET parameters = REPLACE(parameters, '$originalW', '$clonedW') " .
                            "WHERE id_dashboard = $clonedDashId";

                        $updParamsR = mysqli_query($link, $updParamsQ);

                        $updCodeQ = "UPDATE Dashboard.Config_widget_dashboard " .
                             "SET code = REPLACE(code, '$originalW', '$clonedW') " .
                             "WHERE id_dashboard = $clonedDashId";

                         $updCodeR = mysqli_query($link, $updCodeQ);
                    }

                    mysqli_commit($link);

                    //Copia logo della dashboard 
                    // 
                    // if (file_exists("../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename)) {
                    //     $originalLogo = "../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename;
                    //     $uploadFolder = "../img/dashLogos/dashboard" . $clonedDashId . "/";

                    //     if (!file_exists("../img/dashLogos/")) {
                    //         mkdir("../img/dashLogos/");
                    //     }

                    //     if (!file_exists($uploadFolder)) {
                    //         mkdir($uploadFolder);
                    //     }

                    //     if (is_dir($uploadFolder)) {
                    //         $clonedLogo = "../img/dashLogos/dashboard" . $clonedDashId . "/" . $sourceDashLogoFilename;
                    //         copy($originalLogo, $clonedLogo);
                    //     }
                    // }

                    //Copia screenshot della dashboard
                    //  if (file_exists("../img/dashScr/dashboard" . $sourceDashId . "/lastDashboardScr.png")) {
                    //      $originalScr = "../img/dashScr/dashboard" . $sourceDashId . "/lastDashboardScr.png";
                    //      $uploadFolder = "../img/dashScr/dashboard" . $clonedDashId . "/";

                    //      if (!file_exists("../img/dashScr/")) {
                    //          mkdir("../img/dashScr/");
                    //      }

                    //      if (!file_exists($uploadFolder)) {
                    //          mkdir($uploadFolder);
                    //      }

                    //      if (is_dir($uploadFolder)) {
                    //          $clonedScr = "../img/dashScr/dashboard" . $clonedDashId . "/lastDashboardScr.png";
                    //          copy($originalScr, $clonedScr);
                    //      }
                    //  }

                     echo "Ok";
                     exit();
                
                } else {
                    eventLog('Error DB auto-increment.');
                    mysqli_rollback($link);
                    echo "Error DB auto-increment.";
                    exit();
                }
            }

        } else {
            eventLog('Error DB Insert.');
            mysqli_rollback($link);
            echo "Error DB Insert.";
            exit();
        } 

    }
}