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

include '../config.php';//Escape

function array_merge_custom($htlEntity, $natureEntity, $subNatureEntity) {
    $resArray = [];
    if (sizeof($htlEntity) > 1) {
        $resArray = $htlEntity;
    } else {
        array_push($resArray, $htlEntity);
    }
    if (sizeof($natureEntity) > 1) {
        $resArray = array_merge($resArray, $natureEntity);
    } else {
        array_push($resArray, $natureEntity);
    }
    if (sizeof($subNatureEntity) > 1) {
        $resArray = array_merge($resArray, $subNatureEntity);
    } else {
        array_push($resArray, $subNatureEntity);
    }
    return $resArray;
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

        $filesHlt = scandir($path . "/hlt");
        $filesHlt = array_diff(scandir($path . "/hlt"), array('.', '..'));
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

        $filesHltHeatmap = scandir($path . "/hlt/heatmap");
        $filesHltHeatmap = array_diff(scandir($path . "/hlt/heatmap"), array('.', '..'));
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

        $filesSpecial = scandir($path . "/special");
        $filesSpecial = array_diff(scandir($path . "/special"), array('.', '..'));
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

        $filesNat = scandir($path . "/nature");
        $filesNat = array_diff(scandir($path . "/nature"), array('.', '..'));
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

        $filesSubNat = scandir($path . "/subnature");
        $filesSubNat = array_diff(scandir($path . "/subnature"), array('.', '..'));
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
                $filesHlt = scandir($path . "/hlt/" . strtolower($hlt));
                $filesHlt = array_diff(scandir($path . "/hlt/" . strtolower($hlt)), array('.', '..'));
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
            $filesHlt = scandir($path . "/hlt");
            $filesHlt = array_diff(scandir($path . "/hlt"), array('.', '..'));
        }
        if (isset($_GET['nature']) && !empty($_GET['nature'])) {
            $nat = sanitizeGetString('nature');
         //   $filesNat = scandir($path . $nat);
         //   $filesNat = array_diff(scandir($path . $nat), array('.', '..'));
            $filesNat = "/nature/" . $nat; // . ".svg";
        } else {
            $filesNat = scandir($path . "/nature");
            $filesNat = array_diff(scandir($path . "/nature"), array('.', '..'));
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
            $filesSubNat = scandir($path . "/subnature");
            $filesSubNat = array_diff(scandir($path . "/subnature"), array('.', '..'));
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
