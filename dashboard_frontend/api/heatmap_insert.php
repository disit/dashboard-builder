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
$DEBUG = false;
$DEBUG_FILE = '/var/www/html/dashboardSmartCity/api/heatmapLoadByChunk_debug.log';

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

    if (isset($_POST["data"])) {
        logDebug(">>>>>>>>>>>> Ingesting Heatmap data by chunk in DB... \n");
        $interpolated_heatmap   = $_POST["data"]; // Ricevi i dati dall'input POST

        // Decodifica i dati in un array PHP se non lo sono già
        if (is_string($interpolated_heatmap)) {
            $interpolated_heatmap = json_decode($interpolated_heatmap, true); // true per ottenere un array
        }

        // Verifica che la decodifica sia andata a buon fine
        if (!is_array($interpolated_heatmap)) {
            die("Errore: dati non validi.");
        }
        // Itera sull'array e converte i valori di 'id' in interi
        foreach ($interpolated_heatmap as &$item) {
            if (isset($item["id"])) {
                $item["id"] = (int) $item["id"]; // Converte 'id' in un intero
            }
            if (isset($item["value"])) {
                $item["value"] = (float) $item["value"];
            }
            if (isset($item["latitude"])) {
                $item["latitude"] = (float) $item["latitude"];
            }
            if (isset($item["longitude"])) {
                $item["longitude"] = (float) $item["longitude"];
            }
            if (isset($item["clustered"])) {
                $item["clustered"] = (int) $item["clustered"];
            }
            if (isset($item["projection"])) {
                $item["projection"] = (int) $item["projection"];
            }
            if (isset($item["file"])) {
                $item["file"] = (int) $item["file"];
            }
            if (isset($item["xLength"])) {
                $item["xLength"] = (float) $item["xLength"];
            }
            if (isset($item["yLength"])) {
                $item["yLength"] = (float) $item["yLength"];
            }
        }

        // Codifica l'array aggiornato in JSON
        $request_body_json_string = json_encode($interpolated_heatmap);

        // Invia la richiesta con cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_json_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);

        $response = curl_exec($ch);

        // Controlla eventuali errori
        if ($response === false) {
            $error = curl_error($ch);
            die(json_encode([
                "responseState" => "ko",
                "status" => "error",
                "message" => "Errore cURL durante la prima chiamata POST: $error",
                "inputData" =>$interpolated_heatmap
            ]));
        } else {
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode !== 200) {
                die(json_encode([
                    "responseState" => "ko",
                    "status" => "error",
                    "message" => "Errore HTTP nella prima chiamata POST: Codice $httpcode",
                    "response" => $response,
                    "inputData" =>$interpolated_heatmap
                ]));
            }
        }
        if ($httpcode === 200) {
            logDebug(">>>>>>>>>>>> Heatmap data chunk inserted in DB \n");
            $info_heatmap["responseState"] = "ok";
            $info_heatmap["interpolation"] = [
                "POSTstatus" => "Interpolated chunk data saved correctly",
                "inputData" =>$interpolated_heatmap
            ];
        } else {
            $info_heatmap[
                "responseState"
            ] = "Errore nella prima chiamata POST: codice di stato $httpcode\n, response: $response_get";
            echo json_encode($info_heatmap);
        }
       
        // Restituisce $info_heatmap come JSON solo se contiene dati
        if (!empty($info_heatmap)) {
            echo json_encode($info_heatmap);
        }
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
    global $DEBUG;
    global $DEBUG_FILE;
    if($DEBUG){
        file_put_contents($DEBUG_FILE, $string, FILE_APPEND);
    }
}

// THINGS TO REMOVE ///


function latLonToUTM($lat, $lon) {
    // Costanti del sistema WGS84
    $a = 6378137.0; // Semi-asse maggiore (raggio equatoriale in metri)
    $f = 1 / 298.257223563; // Schiacciamento
    $k0 = 0.9996; // Fattore di scala

    // Calcolo della zona UTM
    $zone = (int) floor(($lon + 180) / 6) + 1;

    // Longitudine centrale della zona UTM
    $lon0 = ($zone - 1) * 6 - 180 + 3; // Gradi

    // Converto lat e lon in radianti
    $latRad = deg2rad($lat);
    $lonRad = deg2rad($lon);
    $lon0Rad = deg2rad($lon0);

    // Parametri derivati
    $e = sqrt(2 * $f - $f * $f); // Eccentricità
    $n = $a / sqrt(1 - $e * $e * sin($latRad) * sin($latRad));
    $t = tan($latRad) * tan($latRad);
    $c = ($e * cos($latRad)) ** 2 / (1 - $e * $e);
    $A = cos($latRad) * ($lonRad - $lon0Rad);

    // Calcolo delle coordinate UTM
    $M = $a * (
        (1 - $e * $e / 4 - 3 * $e ** 4 / 64 - 5 * $e ** 6 / 256) * $latRad
        - (3 * $e ** 2 / 8 + 3 * $e ** 4 / 32 + 45 * $e ** 6 / 1024) * sin(2 * $latRad)
        + (15 * $e ** 4 / 256 + 45 * $e ** 6 / 1024) * sin(4 * $latRad)
        - (35 * $e ** 6 / 3072) * sin(6 * $latRad)
    );

    $x = $k0 * $n * ($A + (1 - $t + $c) * $A ** 3 / 6 + (5 - 18 * $t + $t ** 2 + 72 * $c - 58 * $e ** 2) * $A ** 5 / 120) + 500000;
    $y = $k0 * ($M + $n * tan($latRad) * ($A ** 2 / 2 + (5 - $t + 9 * $c + 4 * $c ** 2) * $A ** 4 / 24 + (61 - 58 * $t + $t ** 2 + 600 * $c - 330 * $e ** 2) * $A ** 6 / 720));

    // Aggiusta la coordinata Y per l'emisfero Sud
    if ($lat < 0) {
        $y += 10000000; // Offset per l'emisfero sud
    }

    return [
        'easting' => round($x), // Arrotonda senza decimali
        'northing' => round($y), // Arrotonda senza decimali
        'zone' => $zone,
        'hemisphere' => $lat >= 0 ? 'N' : 'S'
    ];
}
?>
