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

include "../config.php";
require "../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;
header('Access-Control-Allow-Origin: *');

session_start();
ini_set("max_execution_time", 0);
//error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$url = $heatmapInsert; // http://heatmap-api:8080/insertArray
$info_heatmap = []; // Inizializza l'array per evitare avvisi

$processLoaderURL = $endprocessloader . "getOneSpecific.php";

$DEBUG = false;
$DEBUG_FILE = '/var/www/html/dashboardSmartCity/api/heatmapCreate_debug.log';

$EARTH_RADIUS = 6378137; // Earth's radius in meters
$PRECISION = 6;
$MAX_HEATMAP_CELL_NUM = 5000;
$USE_SIMPLE = FALSE;

if (isset($_POST["accessToken"])) {
    $bearerToken = $_POST['accessToken'];
	$check_connection = $ssoUserinfoEndpoint;
	// Inizializza una sessione cURL
    $ch0 = curl_init();
    // Imposta l'URL dell'API
    curl_setopt($ch0, CURLOPT_URL, $check_connection);
    curl_setopt($ch0, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $bearerToken
    ));
    curl_setopt($ch0, CURLOPT_RETURNTRANSFER, true);
    // Esegui la richiesta cURL
    $response = curl_exec($ch0);
    // Controlla se ci sono errori
    if(curl_errno($ch0)) {
        //echo 'Errore cURL: ' . curl_error($ch);
        $output_message['code'] = '400';
        $output_message['message'] = 'Errore cURL: ' . curl_error($ch0);
        $output_message['responseState'] = "ko";
        echo json_encode($output_message);
        die();
    }

    if (isset($_POST["data"]) && isset($_POST["heatmap_name"]) && isset($_POST["heatmap_name"]) && isset($_POST["time"])) {
        logDebug("Getting parameters... \n");
        $data = $_POST["data"];
        $heatmap_name = $_POST["heatmap_name"];
        $metric_name = $_POST["metric_name"];
        $dateObserved = $_POST["time"];

        $completed_val = 1;
        if(isset($_POST["completed_val"])){
            $completed_val = $_POST["completed_val"];
        };

        $vectorFieldDeviceName = $data["vectorFieldDeviceName"];
        $latBase = (float) $data["latBase"];
        $lonBase = (float) $data["lonBase"];
        $deltaRow = (float) $data["deltaRow"];
        $deltaCol = (float) $data["deltaCol"];
        $numRow = (int) $data["numRow"];
        $numCol = (int) $data["numCol"];
        $bearingAngle = (float) $data["bearingAngle"];   
        $useNoDataValue = (bool) $data["useNoDataValue"];
        $NO_DATA_VAL = -9999;
        if($useNoDataValue){
            $NO_DATA_VAL = (float) $data["noDataValue"];
        }

        logDebug("Values: " . $heatmap_name . " " . $metric_name . " " . $dateObserved . " " . " " . $vectorFieldDeviceName . " " .
            $latBase . " " . $lonBase . " " . $deltaRow . " " . $deltaCol . " " . 
            $numRow . " " . $numCol . " " . $bearingAngle . " " . $useNoDataValue . " " . $NO_DATA_VAL . "\n");

        // get magnitude from DB
        $mag = getMagnitudeData($dateObserved, $processLoaderURL, $vectorFieldDeviceName, $bearerToken);
        logDebug("Retrieved magnitude: " . $mag[0][0] . " ... " . $mag[$numRow-1][$numCol-1] . "\n");
        logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");

        logDebug("Start heatmap creation... \n");

        
        $subsampling = findSubsamplingFactor($numRow, $numCol, $MAX_HEATMAP_CELL_NUM);
        $vectorFieldGridSubSampled = generateSubSampledGrid($latBase, $lonBase, $bearingAngle, $deltaRow, $deltaCol, $numRow, $numCol, $mag, $subsampling);
        $vectorFieldGridSubSampledLat = $vectorFieldGridSubSampled[0];
        $vectorFieldGridSubSampledLon = $vectorFieldGridSubSampled[1];
        $magSubSampled = $vectorFieldGridSubSampled[2];
        unset($vectorFieldGridSubSampled);;
        
        logDebug("Vector field grid generated[size=" . count($vectorFieldGridSubSampledLat) . " x " . count($vectorFieldGridSubSampledLat[0]) . "]: (" . 
                $vectorFieldGridSubSampledLat[0][0] . ", " . $vectorFieldGridSubSampledLon[0][0] . ") - (" . 
                $vectorFieldGridSubSampledLat[count($vectorFieldGridSubSampledLat)-1][count($vectorFieldGridSubSampledLat[0])-1] . ", " . 
                $vectorFieldGridSubSampledLon[count($vectorFieldGridSubSampledLat)-1][count($vectorFieldGridSubSampledLat[0])-1] . ")\n"); 
        logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");
        
        $latBaseSubSampled = $vectorFieldGridSubSampledLat[0][0];
        $lonBaseSubSampled = $vectorFieldGridSubSampledLon[0][0];
        $numRowSubSampled = count($vectorFieldGridSubSampledLat);
        $numColSubSampled = count($vectorFieldGridSubSampledLat[0]);
        $deltaRowSubSampled = 2*getDistanceInMeters($latBase, $lonBase, $latBaseSubSampled, $lonBaseSubSampled)*sqrt(2);
        if($deltaRowSubSampled == 0){
            $deltaRowSubSampled = min($deltaCol, $deltaRow);
        }
        logDebug("deltaRowSubSampled = " . $deltaRowSubSampled . " \n");

        // create heatmap
        if($USE_SIMPLE){
            $shift = $deltaRowSubSampled*0.75*sqrt(2);
            $numHeatmapCells = $numRowSubSampled*$numColSubSampled;
            $heatmapCellsLat = new SplFixedArray($numHeatmapCells);
            $heatmapCellsLon = new SplFixedArray($numHeatmapCells);
            $heatmapValues = new SplFixedArray($numHeatmapCells);
            logDebug("Output strucure initialized!\n");
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");

            // $linestrings = [];

            $countCell = 0;
            for ($row = 0; $row < count($magSubSampled); $row++) {
                if($row % 10 == 0){
                    logDebug("\tWorking on cell at row: " . $row . "/" . $numRowSubSampled . "\n");
                }
                for ($col = 0; $col < count($magSubSampled[$row]); $col++) {                
                    $center = [
                        'lat' => $vectorFieldGridSubSampledLat[$row][$col],
                        'lon' => $vectorFieldGridSubSampledLon[$row][$col]
                    ];
                    //$vertex = movePoint($center['lat'], $center['lon'], $deltaRow*0.75*sqrt(2), $bearingAngle - 135);
                    $vertex = movePoint($center['lat'], $center['lon'], $deltaRowSubSampled*0.75*sqrt(2), $bearingAngle - 225);

                    $alphaRad = deg2rad($bearingAngle); // Convert angle to radians

                    // Convert center point to 3D Cartesian coordinates
                    $centerLatRad = deg2rad($center['lat']);
                    $centerLonRad = deg2rad($center['lon']);
                    $centerX = cos($centerLatRad) * cos($centerLonRad);
                    $centerY = cos($centerLatRad) * sin($centerLonRad);
                    $centerZ = sin($centerLatRad);

        
                    $vertexLatRad = deg2rad($vertex['lat']);
                    $vertexLonRad = deg2rad($vertex['lon']);
                    $vertexX = cos($vertexLatRad) * cos($vertexLonRad);
                    $vertexY = cos($vertexLatRad) * sin($vertexLonRad);
                    $vertexZ = sin($vertexLatRad);

                    // Translate vertex relative to the center
                    $relX = $vertexX - $centerX;
                    $relY = $vertexY - $centerY;
                    $relZ = $vertexZ - $centerZ;

                    // Apply rotation around the Z-axis of the local tangent plane
                    $rotX = $relX * cos($alphaRad) - $relY * sin($alphaRad);
                    $rotY = $relX * sin($alphaRad) + $relY * cos($alphaRad);
                    $rotZ = $relZ; // No change in height for this rotation

                    // Translate back to global coordinates
                    $newX = $rotX + $centerX;
                    $newY = $rotY + $centerY;
                    $newZ = $rotZ + $centerZ;

                    // Convert back to latitude and longitude
                    $newLonRad = atan2($newY, $newX);
                    $newLatRad = atan2($newZ, sqrt($newX * $newX + $newY * $newY));

                    $rotatedVertex = [
                        'lat' => rad2deg($newLatRad),
                        'lon' => rad2deg($newLonRad),
                    ];

                    // Convert back to latitude and longitude
                    $heatmapCellsLon[$countCell] = $rotatedVertex['lon']; //$centerLonRad + $xRot / cos($centerLatRad);
                    $heatmapCellsLat[$countCell] = $rotatedVertex['lat']; //$centerLatRad + $yRot;
                    $heatmapValues[$countCell] = $magSubSampled[$row][$col];

                    $countCell = $countCell + 1;
                }
            }

            logDebug("Heatmap cells generated: " . count($heatmapCellsLat) . "/" . 
                count($heatmapCellsLon) . "/" . count($heatmapValues) . "\n");
        } else {
            // get min-max range of the vectorFieldGrid
            $movedSourcePoint = movePoint($latBase, $lonBase, $deltaRow * sqrt(2) / 2, $bearingAngle - 135);
            $vectorFieldGrid = generateGrid($movedSourcePoint['lat'], $movedSourcePoint['lon'], $bearingAngle, $deltaRow, $deltaCol, $numRow + 1, $numCol + 1);  
            $vectorFieldGridLat = $vectorFieldGrid[0];
            $vectorFieldGridLon = $vectorFieldGrid[1];
            unset($vectorFieldGrid);
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");

            $minLat = INF;
            $minLon = INF;
            $maxLat = -INF;
            $maxLon = -INF;
            foreach ($vectorFieldGridLat as $row) {
                foreach ($row as $point) {
                    $minLat = min($minLat, $point);                
                    $maxLat = max($maxLat, $point);
                }
            }
            foreach ($vectorFieldGridLon as $row) {
                foreach ($row as $point) {
                    $minLon = min($minLon, $point);
                    $maxLon = max($maxLon, $point);
                }
            }
            logDebug(">>>> Min-Max: (" . $minLat . ", " . $minLon . ") - (" . $maxLat . ", " . $maxLon . ")\n");
            unset($vectorFieldGridLat);
            unset($vectorFieldGridLon);
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");

            // 1. Generate a lat/lon aligned grid (i.e. with bearing=0) oversampling the vector field positions
            // Calculate minimum difference based on subsampling
            $boundingPolygon = [
                [$minLat, $minLon],
                [$minLat, $maxLon],
                [$maxLat, $maxLon],
                [$maxLat, $minLon]
            ];
            
            // Get span lengths in meters
            $spanLat = getDistanceInMeters($minLat, $minLon, $maxLat, $minLon);
            $spanLon = getDistanceInMeters($minLat, $minLon, $minLat, $maxLon);
            // Calculate number of cells
            $minDiff = $deltaRowSubSampled; //min($deltaCol, $deltaRow);
            $numCellLat = ceil($spanLat / $minDiff) + 1;
            $numCellLon = ceil($spanLon / $minDiff) + 1;
            logDebug("Initial cell nums: numCellLat=" . $numCellLat . ", numCellLon=" . $numCellLon . "\n");
            $go_deeper = TRUE;
            while($go_deeper){
                if($numCellLat*$numCellLon >= $MAX_HEATMAP_CELL_NUM){
                    $go_deeper = FALSE;
                } else {
                    $minDiff = $minDiff * 0.9;
                    $numCellLat = ceil($spanLat / $minDiff) + 1;
                    $numCellLon = ceil($spanLon / $minDiff) + 1;
                    logDebug("Augmented cell nums: numCellLat=" . $numCellLat . ", numCellLon=" . $numCellLon . ", minDiff=" . $minDiff . "\n");
                }
            }
            logDebug("Final cell nums: numCellLat=" . $numCellLat . ", numCellLon=" . $numCellLon . ", minDiff=" . $minDiff . "\n");

            $shift = $minDiff*0.75*sqrt(2);
            // Move source point for heatmap grid (assuming 'bearingAngle' is updated)
            $heatmapSourcePoint = movePoint($minLat, $minLon, $shift, 225-$bearingAngle); //$bearingAngle - 135 + 180);    
            logDebug(">>>>>>> Heatmap params: minDiff=" . $minDiff . ", spanLat=" . $spanLat . ", spanLon=" . $spanLon .  
                    ", numCellLat=" . $numCellLat . ", numCellLon=" . $numCellLon . ", point=(". $heatmapSourcePoint['lat'] . ", " . $heatmapSourcePoint['lon'] . ")\n");
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");
            
            // Generate heatmap grid (assuming 'generateGrid' function is defined)
            $heatmapGrid = generateGrid($heatmapSourcePoint['lat'], $heatmapSourcePoint['lon'], 0, $minDiff, $minDiff, $numCellLat, $numCellLon);
            $heatmapGridLat = $heatmapGrid[0];
            $heatmapGridLon = $heatmapGrid[1];
            unset($heatmapGrid);
            logDebug(">>>>>>> Heatmap grid generated[size=" . $numCellLat . " x " . $numCellLon . "]: (" . 
                    $heatmapGridLat[0][0] . ", " . $heatmapGridLon[0][0] . ") - (" . 
                    $heatmapGridLat[$numCellLat-1][$numCellLon-1] . ", " . $heatmapGridLon[$numCellLat-1][$numCellLon-1] . ")\n"); 
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");

            $numHeatmapCells = (count($heatmapGridLat))*(count($heatmapGridLat[0]));
            $heatmapCellsLat = new SplFixedArray($numHeatmapCells);
            $heatmapCellsLon = new SplFixedArray($numHeatmapCells);
            $heatmapValues = new SplFixedArray($numHeatmapCells);
            logDebug(">>>>>>> Output strucure created!\n");
            logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb]\n");
            $countCell = 0;
            for ($row = 0; $row < count($heatmapGridLat); $row++) {
                for ($col = 0; $col < count($heatmapGridLat[$row]); $col++) {
                    $heatmapPoint = [
                        $heatmapGridLat[$row][$col], 
                        $heatmapGridLon[$row][$col]
                    ];
                    $heatmapValue = $NO_DATA_VAL;
                    $heatmapValueFound = false;
                    $LinearIndex = ($row*(count($heatmapGridLat[$row]))) + $col;
                    if($LinearIndex % 100 == 0){
                        logDebug("[Memory used: " . memory_get_usage()/(1024*1024) . " Mb] - heatmap cells generation: " 
                            . $LinearIndex . "/" . $numHeatmapCells . "\n");
                    }
                    
                    if (isPointInsidePolygon($heatmapPoint, $boundingPolygon)) {
                        // Find the vector cell containing the heatmap point
                        for ($rowV = 0; $rowV < count($vectorFieldGridSubSampledLat) - 1; $rowV++) {
                            for ($colV = 0; $colV < count($vectorFieldGridSubSampledLat[$rowV]) - 1; $colV++) {
                                $vectorCell = [
                                    [$vectorFieldGridSubSampledLat[$rowV][$colV], $vectorFieldGridSubSampledLon[$rowV][$colV]],
                                    [$vectorFieldGridSubSampledLat[$rowV + 1][$colV], $vectorFieldGridSubSampledLon[$rowV + 1][$colV]],
                                    [$vectorFieldGridSubSampledLat[$rowV + 1][$colV + 1], $vectorFieldGridSubSampledLon[$rowV + 1][$colV + 1]],
                                    [$vectorFieldGridSubSampledLat[$rowV][$colV + 1], $vectorFieldGridSubSampledLon[$rowV][$colV + 1]],
                                ];
                                if(isPointInsidePolygon($heatmapPoint, $vectorCell)) {
                                    $heatmapValue = $magSubSampled[$rowV][$colV];
                                    $heatmapValueFound = true;
                                    break; 
                                }
                                unset($vectorCell);
                            }
                        }
                    }

                    if($heatmapValueFound){
                        if($useNoDataValue){
                            if ($heatmapValue != $NO_DATA_VAL) {
                                $heatmapCellsLat[$countCell] = $heatmapPoint[0];
                                $heatmapCellsLon[$countCell] = $heatmapPoint[1];
                                $heatmapValues[$countCell] = $heatmapValue;
                                $countCell = $countCell + 1;
                            }
                        } else {
                            $heatmapCellsLat[$countCell] = $heatmapPoint[0];
                            $heatmapCellsLon[$countCell] = $heatmapPoint[1];
                            $heatmapValues[$countCell] = $heatmapValue;
                            $countCell = $countCell + 1;
                        }
                    }

                    unset($heatmapPoint);
                }
            }
            $heatmapCellsLat->setSize($countCell);
            $heatmapCellsLon->setSize($countCell);
            $heatmapValues->setSize($countCell);
            logDebug(">>>>>>> heatmap cells generated: " . count($heatmapCellsLat) . "/" . 
                count($heatmapCellsLon) . "/" . count($heatmapValues) . "\n");
        }

        logDebug("Sending response... \n");
        $info_heatmap['responseState'] = 'ok';
        $info_heatmap['status'] = 'Heatmap generation completed';
        $info_heatmap['cellsLat'] = $heatmapCellsLat;
        $info_heatmap['cellsLon'] = $heatmapCellsLon;
        $info_heatmap['values'] = $heatmapValues;
        $info_heatmap['minDiff'] = ($shift*2)/sqrt(2);
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json");
        echo json_encode($info_heatmap);
        logDebug("ALL DONE! \n");
    } else {
        header("HTTP/1.1 403 Forbidden");
        $info_heatmap["responseState"] = "Required parameter 'data' missing";
        echo json_encode($info_heatmap);
        die();
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $info_heatmap["responseState"] = "Unauthorized request.";
    echo json_encode($info_heatmap);
    die();
}

function logDebug($string){    
    global $DEBUG_FILE;
    global $DEBUG;
    if($DEBUG){
        $datetimeString = (new DateTime())->format('Y-m-d H:i:s');
        file_put_contents($DEBUG_FILE, "[" . $datetimeString . "] " . $string, FILE_APPEND);
    }
}

// FUNCTIONS ////////////////////////////////////////////////////////////////////
function getMagnitudeData($dateObserved, $processLoaderURL, $vectorFieldDeviceName, $bearerToken){
    $dateObserved_DB = explode('.', $dateObserved)[0];
    $dateObserved_DB = str_replace('T', ' ', $dateObserved_DB);
    $DBGetURL = $processLoaderURL . "?suri=" . $vectorFieldDeviceName . "&dateObserved=" . urlencode($dateObserved_DB) . "&accessToken=" . $bearerToken;    
    logDebug(">>>>>>>>>>>> Getting magnitude from DB. URL: " . $DBGetURL . "\n");

    $DBCURL = curl_init();
    curl_setopt($DBCURL, CURLOPT_URL, $DBGetURL);
    curl_setopt($DBCURL, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearerToken
    ));
    curl_setopt($DBCURL, CURLOPT_RETURNTRANSFER, 1);

    $DBCURLResponse = curl_exec($DBCURL); // TODO HANDLE ERRORS

    $jsonString = $DBCURLResponse;
    $DBData = json_decode($jsonString, true);
    $BigData = json_decode($DBData[0]['data'], true);
    // logDebug(">>>>>>> DB Response: " . $DBCURLResponse . "\n");
    //logDebug(">>>>>>> DB JSON decoded: " . $DBData . "\n");
    //logDebug(">>>>>>> Retrieved data: " . $BigData . "\n");
    $mag = array_to_float($BigData['grandidati']['magnitude'], true); // TODO CONTROLLA
    unset($DBCURLResponse);
    unset($jsonString);
    unset($DBData);
    unset($BigData);
    return $mag;
}

// Function to convert values to float recursively
function array_to_float($array) {
    foreach ($array as &$value) {
        if (is_array($value)) {
            $value = array_to_float($value); 
        } elseif (is_string($value) && is_numeric($value)) {
            $value = (float)$value; 
        }
    }
    return $array;
}

function toRadians($degrees) {
    return $degrees * M_PI / 180;
}

function toDegrees($radians) {
    return $radians * 180 / M_PI;
}

function movePoint($lat, $lon, $distance, $angle) {
    global $EARTH_RADIUS;

    // Convert inputs to radians
    $latRad = deg2rad($lat);
    $lonRad = deg2rad($lon);
    $angleRad = deg2rad($angle);

    // Calculate new latitude
    $newLatRad = asin(
        sin($latRad) * cos($distance / $EARTH_RADIUS) +
        cos($latRad) * sin($distance / $EARTH_RADIUS) * cos($angleRad)
    );

    // Calculate new longitude
    $newLonRad = $lonRad + atan2(
        sin($angleRad) * sin($distance / $EARTH_RADIUS) * cos($latRad),
        cos($distance / $EARTH_RADIUS) - sin($latRad) * sin($newLatRad)
    );

    // Convert back to degrees
    $newLat = rad2deg($newLatRad);
    $newLon = rad2deg($newLonRad);

    return ['lat' => $newLat, 'lon' => $newLon];
}

function destinationPoint($lat, $lon, $distance, $bearing) {
    global $EARTH_RADIUS;
    // Convert input values to radians
    $radLat = deg2rad($lat);
    $radLon = deg2rad($lon);
    $radBearing = deg2rad($bearing);

    // Calculate the new latitude
    $newLat = asin(
        sin($radLat) * cos($distance / $EARTH_RADIUS) +
        cos($radLat) * sin($distance / $EARTH_RADIUS) * cos($radBearing)
    );

    // Calculate the new longitude
    $newLon = $radLon + atan2(
        sin($radBearing) * sin($distance / $EARTH_RADIUS) * cos($radLat),
        cos($distance / $EARTH_RADIUS) - sin($radLat) * sin($newLat)
    );

    // Convert results back to degrees
    // $dPoint = new SplFixedArray($numX);
    // $dPoint['lat' => rad2deg($newLat), 'lon' => rad2deg($newLon)];
    return ['lat' => rad2deg($newLat), 'lon' => rad2deg($newLon)];
}

function generateSubSampledGrid($initialLat, $initialLon, $bearing, $deltaX, $deltaY, $numX, $numY, $values, $subsamplingFactor) {
    global $PRECISION;

    // Ensure the subsampling factor is a power of 2
    if (($subsamplingFactor & ($subsamplingFactor - 1)) !== 0) {
        throw new InvalidArgumentException("Subsampling factor must be a power of 2.");
    }

    // Initialize the grid
    $gridLat = new SplFixedArray($numX);
    $gridLon = new SplFixedArray($numX);

    // Direction perpendicular to the bearing for the Y-axis
    $bearingY = ($bearing + 90) % 360;

    // Loop through rows (X-axis)
    for ($r = 0; $r < $numX; $r++) {
        $rowLat = new SplFixedArray($numY);
        $rowLon = new SplFixedArray($numY);

        // Loop through columns (Y-axis)
        for ($c = 0; $c < $numY; $c++) {
            $xOffset = $r * $deltaX;
            $yOffset = $c * $deltaY;

            // Step along the X-axis
            $xPoint = destinationPoint($initialLat, $initialLon, $xOffset, $bearing);

            // Step along the Y-axis from the X-axis point
            $gridPoint = destinationPoint($xPoint['lat'], $xPoint['lon'], $yOffset, $bearingY);

            // Round the lat/lon values
            $rowLat[$c] = round($gridPoint['lat'], $PRECISION);
            $rowLon[$c] = round($gridPoint['lon'], $PRECISION);
        }

        $gridLat[$r] = $rowLat;
        $gridLon[$r] = $rowLon;
    }

    // Perform subsampling using the subsampling factor
    $subsampledGridLat = [];
    $subsampledGridLon = [];
    $subsampledValues = [];
    $newNumX = ceil($numX / $subsamplingFactor);
    $newNumY = ceil($numY / $subsamplingFactor);

    for ($r = 0; $r < $newNumX; $r++) {
        $rowLat = [];
        $rowLon = [];
        $rowValues = [];

        for ($c = 0; $c < $newNumY; $c++) {
            $sumLat = 0;
            $sumLon = 0;
            $sumValues = 0;
            $count = 0;

            // Iterate through the cells in the subsampling block
            for ($i = 0; $i < $subsamplingFactor; $i++) {
                for ($j = 0; $j < $subsamplingFactor; $j++) {
                    $origR = $r * $subsamplingFactor + $i;
                    $origC = $c * $subsamplingFactor + $j;

                    if ($origR < $numX && $origC < $numY) {
                        $sumLat += $gridLat[$origR][$origC];
                        $sumLon += $gridLon[$origR][$origC];
                        $sumValues += $values[$origR][$origC];
                        $count++;
                    }
                }
            }

            // Compute the averages
            if ($count > 0) {
                $rowLat[] = round($sumLat / $count, $PRECISION);
                $rowLon[] = round($sumLon / $count, $PRECISION);
                $rowValues[] = round($sumValues / $count, $PRECISION);
            }
        }

        $subsampledGridLat[] = $rowLat;
        $subsampledGridLon[] = $rowLon;
        $subsampledValues[] = $rowValues;
    }

    return [$subsampledGridLat, $subsampledGridLon, $subsampledValues];
}

function generateGrid($initialLat, $initialLon, $bearing, $deltaX, $deltaY, $numX, $numY) {
    global $PRECISION;
    // Initialize the grid
    // Note two different matrices (array of array) are made for lat and lon to reduce the 
    // memory footprint that is extremelly inferior w.r.t. have a single matrix with in each cell
    // an array of two floats (i.e., lat and lon)
    $gridLat = new SplFixedArray($numX); // [];
    $gridLon = new SplFixedArray($numX);
    // Direction perpendicular to the bearing for the Y-axis
    $bearingY = ($bearing + 90) % 360;

    // Loop through rows (X-axis)
    for ($r = 0; $r < $numX; $r++) {
        $rowLat = new SplFixedArray($numY); // [];
        $rowLon = new SplFixedArray($numY); // [];
        // Loop through columns (Y-axis)
        for ($c = 0; $c < $numY; $c++) {
            $xOffset = $r * $deltaX;
            $yOffset = $c * $deltaY;

            // Step along the X-axis
            $xPoint = destinationPoint($initialLat, $initialLon, $xOffset, $bearing);

            // Step along the Y-axis from the X-axis point
            $gridPoint = destinationPoint($xPoint['lat'], $xPoint['lon'], $yOffset, $bearingY);

            $gridPoint['lat'] = round($gridPoint['lat'], $PRECISION);
            $gridPoint['lon'] = round($gridPoint['lon'], $PRECISION);
             
            $rowLat[$c] = round($gridPoint['lat'], $PRECISION);
            $rowLon[$c] = round($gridPoint['lon'], $PRECISION);

            unset($xPoint);
            unset($gridPoint);
        }
        $gridLat[$r] = $rowLat;
        $gridLon[$r] = $rowLon;
    }
    return [$gridLat, $gridLon];
}

// function generateGrid($initialLat, $initialLon, $bearing, $deltaX, $deltaY, $numX, $numY) {
//     global $PRECISION;
//     // Initialize the grid
//     $grid = new SplFixedArray($numX); // [];
//     // Direction perpendicular to the bearing for the Y-axis
//     $bearingY = ($bearing + 90) % 360;

//     // Loop through rows (X-axis)
//     for ($r = 0; $r < $numX; $r++) {
//         $row = new SplFixedArray($numY); // [];
//         // Loop through columns (Y-axis)
//         for ($c = 0; $c < $numY; $c++) {
//             $xOffset = $r * $deltaX;
//             $yOffset = $c * $deltaY;

//             // Step along the X-axis
//             $xPoint = destinationPoint($initialLat, $initialLon, $xOffset, $bearing);

//             // Step along the Y-axis from the X-axis point
//             $gridPoint = destinationPoint($xPoint['lat'], $xPoint['lon'], $yOffset, $bearingY);

//             $gridPoint['lat'] = round($gridPoint['lat'], $PRECISION);
//             $gridPoint['lon'] = round($gridPoint['lon'], $PRECISION);
             
//             $row[$c] = $gridPoint;

//             unset($xPoint);
//             unset($gridPoint);
//         }
//         $grid[$r] = $row;
//     }
//     return $grid;
// }

function findSubsamplingFactor($numRow, $numCol, $max_num){
    $numPoints = $numRow*$numCol;
    $too_big = TRUE;
    $subsampling = 1;
    logDebug("Testing subsampling = " . $subsampling . "\n");
    if($numPoints > $max_num){
        $subsampling = 4;
        while($too_big){
            logDebug("Testing subsampling = " . $subsampling . "\n");
            if($numPoints/($subsampling*$subsampling) > $max_num){
                $subsampling = $subsampling * 2;
            }else{
                $too_big = FALSE;
                logDebug("Testing subsampling = " . $subsampling . " => FOUND!\n");
            }
        }
    }
    return $subsampling;
}

function getDistanceInMeters($lat1, $lon1, $lat2, $lon2) {
    global $EARTH_RADIUS;
    $dLat = toRadians($lat2 - $lat1); 
    $dLon = toRadians($lon2 - $lon1); 
    $radLat1 = toRadians($lat1);
    $radLat2 = toRadians($lat2);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($radLat1) * cos($radLat2) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $EARTH_RADIUS * $c;
}

function isPointInsidePolygon($point, $polygon) {
    list($lat, $lon) = $point;
    $inside = false;

    for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
        list($lat1, $lon1) = $polygon[$i];
        list($lat2, $lon2) = $polygon[$j];

        $intersect = 
        ($lon1 > $lon) !== ($lon2 > $lon) &&
        $lat < (($lat2 - $lat1) * ($lon - $lon1)) / ($lon2 - $lon1) + $lat1;

        if ($intersect) {
            $inside = !$inside;
        }
    }

    return $inside;
}

function generateHeatmapGridCell($heatmapGrid, $vectorCells, $vectorCellMagnitudes, $NO_DATA_VAL, $boundingPolygon) {
    $heatmapCells = [];
    $heatmapValues = [];
    for ($row = 0; $row < count($heatmapGrid) - 1; $row++) {
        for ($col = 0; $col < count($heatmapGrid[$row]) - 1; $col++) {
            $heatmapPoint = [$heatmapGrid[$row][$col]['lat'], $heatmapGrid[$row][$col]['lon']];
            $heatmapValue = $NO_DATA_VAL;

            if (isPointInsidePolygon($heatmapPoint, $boundingPolygon)) {
                // Find the vector cell containing the heatmap point
                foreach ($vectorCells as $vi => $vectorCell) {
                    if (isPointInsidePolygon($heatmapPoint, $vectorCell)) {
                        $heatmapValue = $vectorCellMagnitudes[$vi];
                        break; 
                    }
                }
            }
            // If heatmapValue is not NO_DATA_VAL, add it to the arrays
            if ($heatmapValue != $NO_DATA_VAL) {
                $heatmapCells[] = $heatmapPoint;
                $heatmapValues[] = $heatmapValue;
            }
        }
    }

    return ['cells' => $heatmapCells, 'values' => $heatmapValues];
}

?>
