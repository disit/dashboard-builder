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

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$sourceDashId = mysqli_real_escape_string($link, $_REQUEST['sourceDashboardId']);
if (checkVarType($sourceDashId, "integer") === false) {
    eventLog("Returned the following ERROR in duplicate_dash.php for sourceDashId = ".$sourceDashId.": ".$sourceDashId." is not an integer as expected. Exit from script.");
    exit();
};
$newDashboardTitle = mysqli_real_escape_string($link, filter_input(INPUT_POST,'newDashboardTitle', FILTER_SANITIZE_STRING));
$widgetsMap = [];

$query0 = "SELECT Config_dashboard.logoFilename FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = '$sourceDashId'";
$result0 = mysqli_query($link, $query0);

if($result0)
{
   $row0 = mysqli_fetch_array($result0);
   $sourceDashLogoFilename = $row0['logoFilename'];
}
else
{
   echo "originalDashRecordQueryKo";
   exit();
}
 
$query1 = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id= $sourceDashId";
$result1 = mysqli_query($link, $query1) or die(mysqli_error($link));

if($result1) 
{
    $row1 = mysqli_fetch_assoc($result1);
    
    unset($row1['Id']);
    $row1['name_dashboard'] = $newDashboardTitle;
    $row1['title_header'] = $newDashboardTitle;
    $row1['user'] = $_SESSION['loggedUsername'];
    
    $query2 = "INSERT INTO Dashboard.Config_dashboard(";        
    $newQueryFields = "";
    $newQueryValues = "";

    $count = 0;
    foreach($row1 as $key => $value) 
    {
        if($count == 0)
        {
            $newQueryFields = $key;
            $newQueryValues = returnManagedStringForDb($value);
        }
        else
        {
            $newQueryFields = $newQueryFields . ", " . $key;
            
            if($key == 'subtitle_header')
            {
                $newQueryValues = $newQueryValues . ", '$value'";
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
    
    //$file = fopen("C:\dashboardLog.txt", "w");
    //fwrite($file, "Query2: " . $query2 . "\n");    
    
    mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);                

     $result2 = mysqli_query($link, $query2);


     if($result2)
     {
         $title = $row1['name_dashboard'];
         $newId = mysqli_insert_id($link);;

         //Salvataggio su API ownership
         if(isset($_SESSION['refreshToken']))
         {
             $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
             $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

             $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
             $accessToken = $tkn->access_token;
             $_SESSION['refreshToken'] = $tkn->refresh_token;

             $callBody = ["elementId" => $newId, "elementType" => "DashboardID", "elementName" => $title];

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
                 //Non facciamo niente di specifico in caso di mancata risposta dell'hostù
                 $stopFlag = 1;
             }
         }

         if ($callResultArray['elementId']) {

             $clonedDashId = mysqli_insert_id($link);

             $query3 = "SELECT AUTO_INCREMENT AS MaxId FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
             $result3 = mysqli_query($link, $query3);
             if ($result3) {
                 $row3 = mysqli_fetch_array($result3);
                 $maxId = $row3['MaxId'];

                 $query4 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$sourceDashId'";
                 $result4 = mysqli_query($link, $query4);

                 if ($result4) {
                     while ($row4 = mysqli_fetch_assoc($result4)) {
                         $sourceWidgetName = $row4['name_w'];
                         $clonedWidgetId = $maxId;

                         //Costruzione del nome del widget clonato
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

                         unset($row4['Id']);

                         $row4['name_w'] = $clonedWidgetName;
                         $row4['id_dashboard'] = $clonedDashId;
                         $row4['creator'] = $_SESSION['loggedUsername'];

                         $widgetsMap[$sourceWidgetName] = $clonedWidgetName;

                         $query5 = "INSERT INTO Dashboard.Config_widget_dashboard(";
                         $newQueryFields = "";
                         $newQueryValues = "";

                         $count = 0;
                         foreach ($row4 as $key => $value) {
                             if ($count == 0) {
                                 $newQueryFields = $key;
                                 $newQueryValues = returnManagedStringForDb($value);
                             } else {
                                 if ($key == "parameters" && $row4['type_w'] == "widgetMap") {
                                     $newQueryFields = $newQueryFields . ", " . $key;
                                     $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb(escapeForSQL($value, $link));
                                 } else {
                                     $newQueryFields = $newQueryFields . ", " . $key;
                                     $newQueryValues = $newQueryValues . ", " . returnManagedStringForDb($value);
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
                             mysqli_rollback($link);
                             echo "Ko";
                             exit();
                         } else {
                             $maxId++;
                         }
                     }

                     mysqli_commit($link);

                     //Aggiornamento dei campi dei parametri per interazioni cross widget
                     foreach ($widgetsMap as $originalW => $clonedW) {
                         $updParamsQ = "UPDATE Dashboard.Config_widget_dashboard " .
                             "SET parameters = REPLACE(parameters, '$originalW', '$clonedW') " .
                             "WHERE id_dashboard = $clonedDashId";

                         $updParamsR = mysqli_query($link, $updParamsQ);
                     }

                     mysqli_commit($link);

                     //Copia logo della dashboard
                     if (file_exists("../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename)) {
                         $originalLogo = "../img/dashLogos/dashboard" . $sourceDashId . "/" . $sourceDashLogoFilename;
                         $uploadFolder = "../img/dashLogos/dashboard" . $clonedDashId . "/";

                         if (!file_exists("../img/dashLogos/")) {
                             mkdir("../img/dashLogos/");
                         }

                         if (!file_exists($uploadFolder)) {
                             mkdir($uploadFolder);
                         }

                         if (is_dir($uploadFolder)) {
                             $clonedLogo = "../img/dashLogos/dashboard" . $clonedDashId . "/" . $sourceDashLogoFilename;
                             copy($originalLogo, $clonedLogo);
                         }
                     }

                     //Copia screenshot della dashboard
                     if (file_exists("../img/dashScr/dashboard" . $sourceDashId . "/lastDashboardScr.png")) {
                         $originalScr = "../img/dashScr/dashboard" . $sourceDashId . "/lastDashboardScr.png";
                         $uploadFolder = "../img/dashScr/dashboard" . $clonedDashId . "/";

                         if (!file_exists("../img/dashScr/")) {
                             mkdir("../img/dashScr/");
                         }

                         if (!file_exists($uploadFolder)) {
                             mkdir($uploadFolder);
                         }

                         if (is_dir($uploadFolder)) {
                             $clonedScr = "../img/dashScr/dashboard" . $clonedDashId . "/lastDashboardScr.png";
                             copy($originalScr, $clonedScr);
                         }
                     }

                     echo "Ok";
                     exit();
                 } else {
                     mysqli_rollback($link);
                     echo "Ko";
                     exit();
                 }
             } else {
                 mysqli_rollback($link);
                 echo "Ko";
                 exit();
             }
         }
     }
     else
     {
        mysqli_rollback($link);
        echo "Ko";
        exit();
     }
}