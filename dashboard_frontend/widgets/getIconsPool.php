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

include '../config.php';//Escape
require_once __DIR__.'/widgetDatabase.php';
$widgetDatabaseGuard = dashboardWidgetDatabaseBegin();
try {


function dashboardWidgetIconScan($directory) {
    $files = @scandir($directory);
    if ($files === false) throw new \DashboardWidgetIconDirectoryException('Icon directory unavailable');
    return $files;
}

function array_merge_custom($htlEntity, $natureEntity, $subNatureEntity) {
    return array_merge(
        is_array($htlEntity) ? $htlEntity : [$htlEntity],
        is_array($natureEntity) ? $natureEntity : [$natureEntity],
        is_array($subNatureEntity) ? $subNatureEntity : [$subNatureEntity]
    );
}

$path = '../img/widgetSelectorIconsPool';
$delPattern = '/-white.svg$/';
$extImgPattern = '/.svg$/';
$response = [];
if(isset($_GET['action']) && !empty($_GET['action']))
{
    $action = $_GET['action'];
    //    $action = escapeForHTML($action);
    if($action == "getAll") {
        $filesHlt = [];
        $filesNat = [];
        $filesSubNat = [];

    /*    $filesHlt = scandir($path . "/hlt/heatmap");
        $filesHlt = array_diff(scandir($path . "/hlt" . strtolower($hlt)), array('.', '..'));
        $filesHlt = array_values($filesHlt);
        $origFilesHltLength = sizeof($filesHlt);
        $origLengthArray = sizeof($filesHlt);
        for ($n = 0; $n < $origLengthArray; $n++){
            $res = preg_match($delPattern, $filesHlt[$n]);
            if($res){
                unset($filesHlt[$n]);
            } else {
                //   $filesHlt[$n] = "/hlt/" . strtolower($hlt) . "/" . $filesHlt[$n];
                $filesHlt[$n] = "/hlt/" . strtolower($hlt) . "/" . pathinfo($filesHlt[$n], PATHINFO_FILENAME);
            }
        }*/

        $filesHlt = dashboardWidgetIconScan($path . "/hlt");
        $filesHlt = array_diff(dashboardWidgetIconScan($path . "/hlt"), array('.', '..'));
        $filesHlt = array_values($filesHlt);
        $origFilesHltLength = sizeof($filesHlt);
        for($i = 0; $i < $origFilesHltLength; $i++) {
            $res = preg_match($delPattern, $filesHlt[$i]);
            $res2 = preg_match($extImgPattern, $filesHlt[$i]);
            if($res || !$res2){
                unset($filesHlt[$i]);
            } else {
                $filesHlt[$i] = "/hlt/" . pathinfo($filesHlt[$i], PATHINFO_FILENAME);
            }
        }

        $filesHltHeatmap = dashboardWidgetIconScan($path . "/hlt/heatmap");
        $filesHltHeatmap = array_diff(dashboardWidgetIconScan($path . "/hlt/heatmap"), array('.', '..'));
        $filesHltHeatmap = array_values($filesHltHeatmap);
        $origFilesHltHeatmapLength = sizeof($filesHltHeatmap);
        for($i = 0; $i < $origFilesHltHeatmapLength; $i++) {
            $res = preg_match($delPattern, $filesHltHeatmap[$i]);
            $res2 = preg_match($extImgPattern, $filesHltHeatmap[$i]);
            if($res || !$res2){
                unset($filesHltHeatmap[$i]);
            } else {
                $filesHltHeatmap[$i] = "/hlt/heatmap/" . pathinfo($filesHltHeatmap[$i], PATHINFO_FILENAME);
            }
        }

        $filesSpecial = dashboardWidgetIconScan($path . "/special");
        $filesSpecial = array_diff(dashboardWidgetIconScan($path . "/special"), array('.', '..'));
        $filesSpecial = array_values($filesSpecial);
        $origFilesHltHeatmapLength = sizeof($filesSpecial);
        for($i = 0; $i < $origFilesHltHeatmapLength; $i++) {
            $res = preg_match($delPattern, $filesSpecial[$i]);
            $res2 = preg_match($extImgPattern, $filesSpecial[$i]);
            if($res || !$res2){
                unset($filesSpecial[$i]);
            } else {
                $filesSpecial[$i] = "/special/" . pathinfo($filesSpecial[$i], PATHINFO_FILENAME);
            }
        }

        $filesNat = dashboardWidgetIconScan($path . "/nature");
        $filesNat = array_diff(dashboardWidgetIconScan($path . "/nature"), array('.', '..'));
        $filesNat = array_values($filesNat);
        $origFilesNatLength = sizeof($filesNat);
        for($i = 0; $i < $origFilesNatLength; $i++) {
            $res = preg_match($delPattern, $filesNat[$i]);
            if($res){
                unset($filesNat[$i]);
            } else {
                $filesNat[$i] = "/nature/" . pathinfo($filesNat[$i], PATHINFO_FILENAME);
            }
        }

        $filesSubNat = dashboardWidgetIconScan($path . "/subnature");
        $filesSubNat = array_diff(dashboardWidgetIconScan($path . "/subnature"), array('.', '..'));
        $filesSubNat = array_values($filesSubNat);
        $origFilesSubNatLength = sizeof($filesSubNat);
        for($i = 0; $i < $origFilesSubNatLength; $i++) {
            $res = preg_match($delPattern, $filesSubNat[$i]);
            if($res){
                unset($filesSubNat[$i]);
            } else {
                $filesSubNat[$i] = "/subnature/" . pathinfo($filesSubNat[$i], PATHINFO_FILENAME);
            }
        }

        $allItems = array_merge($filesHlt, $filesHltHeatmap, $filesSpecial, $filesNat, $filesSubNat);
        $origLengthArray = sizeof($allItems);
        for ($n = 0; $n < $origLengthArray; $n++){
            $res = preg_match($delPattern, $allItems[$n]);
            if($res){
                unset($allItems[$n]);
            }
        }
        $response['allIconList'] = $allItems;
    } else if ($action == "getSuggested") {
        // DO SUGGESTED
        $filesHlt = [];
        $filesNat = [];
        $filesSubNat = [];

        if (isset($_GET['highLevelType']) && !empty($_GET['highLevelType'])) {
            $hlt = sanitizeGetString('highLevelType');
            if (strcasecmp($hlt, "heatmap") == 0) {
                $filesHlt = dashboardWidgetIconScan($path . "/hlt/" . strtolower($hlt));
                $filesHlt = array_diff(dashboardWidgetIconScan($path . "/hlt/" . strtolower($hlt)), array('.', '..'));
                $filesHlt = array_values($filesHlt);
                $origFilesHltLength = sizeof($filesHlt);
                $origLengthArray = sizeof($filesHlt);
                for ($n = 0; $n < $origLengthArray; $n++){
                    $res = preg_match($delPattern, $filesHlt[$n]);
                    if($res){
                        unset($filesHlt[$n]);
                    } else {
                     //   $filesHlt[$n] = "/hlt/" . strtolower($hlt) . "/" . $filesHlt[$n];
                        $filesHlt[$n] = "/hlt/" . strtolower($hlt) . "/" . pathinfo($filesHlt[$n], PATHINFO_FILENAME);
                    }
                }
            } else {
                $filesHlt = "/hlt/" . $hlt; // . ".svg";
            }
        } else {
            $filesHlt = dashboardWidgetIconScan($path . "/hlt");
            $filesHlt = array_diff(dashboardWidgetIconScan($path . "/hlt"), array('.', '..'));
        }
        if (isset($_GET['nature']) && !empty($_GET['nature'])) {
            $nat = sanitizeGetString('nature');
         //   $filesNat = scandir($path . $nat);
         //   $filesNat = array_diff(scandir($path . $nat), array('.', '..'));
            $filesNat = "/nature/" . $nat; // . ".svg";
        } else {
            $filesNat = dashboardWidgetIconScan($path . "/nature");
            $filesNat = array_diff(dashboardWidgetIconScan($path . "/nature"), array('.', '..'));
        }
        if (isset($_GET['subNature']) && !empty($_GET['subNature'])) {
            $subNat = sanitizeGetString('subNature');
        //    $filesSubNat = scandir($path . $subNat);
        //    $filesSubNat = array_diff(scandir($path . $subNat), array('.', '..'));
            if (isset($_GET['nature']) && !empty($_GET['subNature'])) {
                $nat = sanitizeGetString('nature');
                $filesSubNat = "/subnature/" . $nat . "_" . $subNat; // . ".svg";
            } else {

            }
        } else  {
            $filesSubNat = dashboardWidgetIconScan($path . "/subnature");
            $filesSubNat = array_diff(dashboardWidgetIconScan($path . "/subnature"), array('.', '..'));
        }
        $suggestedItems = array_merge_custom($filesHlt, $filesNat, $filesSubNat);
        $origLengthArray = sizeof($suggestedItems);
        for ($n = 0; $n < $origLengthArray; $n++){
            $res = preg_match($delPattern, $suggestedItems[$n]);
            if($res){
                unset($suggestedItems[$n]);
            }
        }
        $response['suggestedIconList'] = $suggestedItems;
    }
    echo json_encode($response);
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
