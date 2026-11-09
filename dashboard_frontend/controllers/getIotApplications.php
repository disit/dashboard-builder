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

require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

include '../config.php';
error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if (isset($_SESSION['refreshToken'])) {
  $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
  $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

  $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

  $accessToken = $tkn->access_token;
  $_SESSION['refreshToken'] = $tkn->refresh_token;
  $response['access_token'] = $accessToken;

  if(isset($_REQUEST['download_all'])) {
    $downloadUrl = $iotAppApiBaseUrl."/v1/?op=download_nr_apps";

    // disable buffering
    while (ob_get_level() > 0) {
        ob_end_flush();
    }

    ob_implicit_flush(true);

    header('X-Accel-Buffering: no');
    header('Cache-Control: no-cache');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $downloadUrl);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken
    ));

    /*
     * Inoltra gli header della risposta remota.
     */
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $headerLine) {
        $length = strlen($headerLine);
        $headerLine = trim($headerLine);

        if ($headerLine === '') {
            return $length;
        }

        /*
         * Copia lo status HTTP remoto.
         */
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $headerLine, $matches)) {
            http_response_code((int) $matches[1]);
            return $length;
        }

        /*
         * Non inoltrare header hop-by-hop o ricalcolati.
         */
        if (preg_match(
            '/^(Connection|Keep-Alive|Proxy-Authenticate|' .
            'Proxy-Authorization|TE|Trailer|Transfer-Encoding|' .
            'Upgrade|Content-Length):/i',
            $headerLine
        )) {
            return $length;
        }

        header($headerLine, false);

        return $length;
    });

    /*
     * Inoltra ogni chunk della risposta appena arriva.
     */
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) {
        echo $chunk;

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();

        return strlen($chunk);
    });

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    /*
     * Nessun timeout globale: necessario per stream lunghi.
     */
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);

    $result = curl_exec($ch);

    if ($result === false) {
        $error = curl_error($ch);

        if (!headers_sent()) {
            http_response_code(502);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode(array(
            'error' => 'error calling application api',
            'details' => $error
        ));
    }

    curl_close($ch);
    exit;
  } else {
    $json = http_get($iotAppApiBaseUrl."/v1/?op=list&mtime=true&&accessToken=" . $accessToken);
    $validApplicationList = $json['httpcode'] == 200 && !empty($json['isList']);
    if ($validApplicationList) {
      foreach ($json['result'] as $candidate) {
        if (!is_array($candidate) || !isset($candidate['id'], $candidate['name'], $candidate['type']) ||
            !is_scalar($candidate['id']) || !is_string($candidate['name']) || !is_string($candidate['type']) ||
            ($candidate['type'] === 'edge' && !is_string($candidate['edgetype'] ?? null))) {
          $validApplicationList = false;
          break;
        }
      }
    }
    if ($validApplicationList) {
      $response['applications'] = array();
      $applicationQueryFailed = false;
      foreach ($json['result'] as $app) {
        $a = $app;
        $a['name'] = htmlspecialchars($a['name']);
        if($a['type']!='edge') {
          $icons = array('python'=>'dataAnalyticPythonIcon.png','plumber'=>'dataAnalyticIcon.png','portia'=>'portiaIcon.png','basic'=>'iotAppBasicIcon.png','advanced'=>'iotAppAdvIcon.png','basic-debug'=>'iotAppBasicDebugIcon.png','advanced-debug'=>'iotAppAdvDebugIcon.png');
          if(isset($icons[$a['type']])) {
            $a['icon'] = $icons[$a['type']];
          } else {
            $a['icon'] = $icons['advanced'];
          }
          if($a['type']=='plumber' || $a['type']=='python') {
            $a['iotapps'] = is_array($a['iotapps'] ?? null) ? implode(',', $a['iotapps']) : null;
          }
        } else {
          $os = explode('_', $a['edgetype']);
          $os = $os[0];
          if($os==='win32') 
            $a['icon'] = 'iotAppBasicPcIcon.png';
          else if($os=='android')
            $a['icon'] = 'iotAppBasicMobileIcon.png';
          else
            $a['icon'] = 'iotAppBasicRaspberryIcon.png';
        }
        $a['dashboards'] = array();
        //search for connected dashboards
        $q = "SELECT DISTINCT id_dashboard as dashboardId,title_header as dashboardName, user as dashboardAuthor FROM Dashboard.Config_widget_dashboard w JOIN Dashboard.Config_dashboard d ON d.Id=w.id_dashboard WHERE appId='$app[id]' AND d.deleted='no'";
        try {
          $r = mysqli_query($link, $q);
        } catch (\mysqli_sql_exception $databaseError) {
          $r = false;
        }
        if (!$r) {
          $applicationQueryFailed = true;
          break;
        }
        if($r)
        {
            while($row = mysqli_fetch_assoc($r))
            {
                $row['dashboardName'] = htmlspecialchars($row['dashboardName']);
                array_push($a['dashboards'], $row);
            }         
        }
        $response['applications'][] = $a;
      }
      if ($applicationQueryFailed) {
        unset($response['applications']);
        http_response_code(500);
        $response['detail'] = 'Ko';
        $response['error'] = 'Database query failed';
      } else {
        $response['detail'] = 'Ok';
      }
    } else {
      $response['detail'] = 'Ko';
      $response['error'] = $json['result'];
      if ($json['httpcode'] == 200 || !$json['httpcode']) {
        http_response_code(502);
        $response['error'] = 'Invalid application service response';
      }
    }
  }
} else {
  $response['detail'] = 'Ko';
  $response['error'] = 'no refresh token';
}
//$response['refresh_token'] = $_SESSION['refreshToken'];
echo json_encode($response);

function http_get($url) {
  $opts = array('http' =>
      array(
          'method' => 'GET',
      )
  );

  # Create the context
  $context = stream_context_create($opts);
  # Get the response (you can use this for GET)
  $result = file_get_contents($url, false, $context);
  //echo "result:$result\n";
  //var_dump($http_response_header);
  $httpCode = isset($http_response_header[0]) ? (explode(" ", $http_response_header[0])[1] ?? 0) : 0;
  return array("httpcode" => $httpCode, "result" => json_decode($result,true), "isList" => is_array(json_decode($result)));
}
