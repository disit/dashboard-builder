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

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];
////
    $action = $_REQUEST['action'];
//Selezionare
    if ($role_session_active == 'RootAdmin') {
        if ($action == 'get_data') {
            $lang_query = '';
            $idquery = '';
            if (isset($_REQUEST['lang'])) {
//
                $lang0 = mysqli_real_escape_string($link, $_REQUEST['lang']);
                $lang = filter_var($lang0, FILTER_SANITIZE_STRING);
//
                if (($lang !== "") && ($lang !== null)) {
                    $lang_query = ' WHERE language="' . $lang . '"';
                }
            }
            if (isset($_REQUEST['id_el'])) {
//$id_el0 = mysqli_real_escape_string($link, $_REQUEST['$id_el']);
                $id_el = $_REQUEST['id_el'];
                if (($id_el !== "") && ($id_el !== null)) {
                    $idquery = ' WHERE id="' . $id_el . '"';
                }
            }
            $query = 'SELECT DISTINCT * FROM multilanguage ' . $lang_query . ' ' . $idquery . ';';
//echo($query);
            $result = mysqli_query($link, $query);
            $response = "";
            $i = 0;
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $response[$i]['id'] = $row['id'];
                        $response[$i]['menuText'] = $row['menuText'];
                        $response[$i]['language'] = $row['language'];
                        $response[$i]['translatedText'] = $row['translatedText'];
                        $i++;
                    }
// array_push($dashboard_list, $row);
                }
                echo json_encode($response);
                mysqli_close($link);
            }
        } else if ($action == 'add') {
//add
//
            //$reference = $_POST['reference'];
            $reference0 = mysqli_real_escape_string($link, $_REQUEST['reference']);
            $reference = filter_var($reference0, FILTER_SANITIZE_STRING);
//
//$icon_e = $_POST['icon_e'];
            $icon_e0 = mysqli_real_escape_string($link, $_REQUEST['icon_e']);
            $icon_e = filter_var($icon_e0, FILTER_SANITIZE_STRING);
//
//$translate = $_POST['translate'];
            $translate0 = mysqli_real_escape_string($link, $_POST['translate']);
            $translate = filter_var($translate0, FILTER_SANITIZE_STRING);
//
            $query_check1 = 'SELECT DISTINCT count(*) AS count FROM multilanguage WHERE language="' . $icon_e . '" AND menuText="' . $reference . '";';
            $result_check1 = mysqli_query($link, $query_check1);
            if (mysqli_num_rows($result_check1) > 0) {
                while ($row = mysqli_fetch_assoc($result_check1)) {
                    $response = $row['count'];
                    if ($response == 0) {
                        $query = "INSERT INTO multilanguage (menuText, language, translatedText)
                            VALUES ('" . $reference . "', '" . $icon_e . "', '" . $translate . "')";
                        $result = mysqli_query($link, $query);
                        if ($result) {
                            $message = 'OK';
                            echo ($message);
                        } else {
                            $message = 'Error';
                            echo ($message);
                        }
                    } else {
                        $message = 'Duplicate';
                        echo ($message);
                    }
                }
//notingn
// echo ('trovato:'.$response[$y].'<br />');
            }
// mysqli_close($link);
//                
        } else if ($action == 'edit') {
//
//$id = $_POST['id_element'];
            $id0 = mysqli_real_escape_string($link, $_REQUEST['id_element']);
            $id = filter_var($id0, FILTER_SANITIZE_STRING);

//$reference = $_POST['reference'];
            $reference0 = mysqli_real_escape_string($link, $_REQUEST['reference']);
            $reference = filter_var($reference0, FILTER_SANITIZE_STRING);

//$icon_e = $_POST['icon_e'];
            $icon_e0 = mysqli_real_escape_string($link, $_REQUEST['icon_e']);
            $icon_e = filter_var($icon_e0, FILTER_SANITIZE_STRING);

//$translate = $_POST['translate'];
            $translate0 = mysqli_real_escape_string($link, $_REQUEST['translate']);
            $translate = filter_var($translate0, FILTER_SANITIZE_STRING);
//

            $query = "UPDATE multilanguage
                    SET menuText='" . $reference . "',
                        language='" . $icon_e . "',
                        translatedText='" . $translate . "'
                    WHERE id='" . $id . "'";
            $result = mysqli_query($link, $query);
            mysqli_close($link);
//
        } else if ($action == 'download') {
//menulist
            header("Content-Type: application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=Export.csv");
            header("Content-Type: application/force-download");
            header("Content-Transfer-Encoding: UTF-8");
            header("Pragma: no-cache");
            header("Expires: 0");
//$fp = fopen('file.csv', 'w');
            $name = tempnam(sys_get_temp_dir(), 'csv');
            $fp = fopen($name, 'w');
//$fp = tmpfile();
//
            //
        $query_list = "SELECT DISTINCT menuText FROM multilanguage";
            $result_list = mysqli_query($link, $query_list);
//
            $csv_fields = array();
//$csv_fields['Refrence Text']='Refrence Text';
            $obj = json_decode($localizations, true);
            $languages = $obj['languages'];
            $tot_leng = count($languages);
            $headers[0] = 'Reference Text';
            $ind = 1;
            if ($tot_leng > 0) {
                for ($i = 0; $i < $tot_leng; $i++) {
                    $lang_list[$i] = $obj['languages'][$i]['code'];
                    $headers[$ind] = $obj['languages'][$i]['code'];
                    $ind++;
                }
            }
//
//

            $count = 0;
            fputcsv($fp, $csv_fields['Reference Text']);
            foreach ($result_list as $fields) {
                $menuText = $fields['menuText'];
                $csv_fields[$count]['Reference Text'] = $menuText;

                foreach ($lang_list as $lang) {
//
                    $query_element = "SELECT translatedText FROM multilanguage WHERE menuText='" . $menuText . "' AND language='" . $lang . "'";
                    $result_element = mysqli_query($link, $query_element);
//
// print_r($result_element);
                    if (mysqli_num_rows($result_element) > 0) {
                        while ($row = mysqli_fetch_assoc($result_element)) {
                            $csv_fields[$count][$lang] = $row['translatedText'];
                        }
                    } else {
                        $csv_fields[$count][$lang] = null;
                    }
//
                }
//fputcsv($fp, $fields);
                $count++;
            }
            $json = json_encode($csv_fields);
//
//echo($json);
            $data = json_decode($json, true);
//
            fputcsv($fp, $headers);
//
            foreach ($data as $row) {

                fputcsv($fp, $row);
            }

//echo($name);
            fclose($fp);
//echo ('"data1","data2" \n "data1","data2" ');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename('exportmenu.csv'));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
//header('Content-Length: ' . filesize($fp));
            readfile($name);
//echo($fp);
////
//
        } else if ($action == 'import') {
//
            $lang0 = mysqli_real_escape_string($link, $_REQUEST['lang']);
            $lang = filter_var($lang0, FILTER_SANITIZE_STRING);

            $select0 = mysqli_real_escape_string($link, $_REQUEST['select']);
            $select = filter_var($select0, FILTER_SANITIZE_STRING);
//
//
            $query = "SELECT DISTINCT text FROM " . $select . " WHERE text NOT IN (SELECT menuText FROM multilanguage WHERE language='" . $lang . "');";
            $result = mysqli_query($link, $query);
            $response = "";
//echo($query);
            $i = 0;
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $response[$i] = $row['text'];
//
                        $query1 = "INSERT INTO multilanguage (menuText, language) VALUES ('" . $response[$i] . "', '" . $lang . "')";
                        $result1 = mysqli_query($link, $query1);
//
                        $i++;
                    }
//print_r($response);
// array_push($dashboard_list, $row);
                } else {
                    $message = 'Duplicate';
                    echo ($message);
                }
            } else {
                $message = 'Error';
                echo ($message);
            }
//
        } else if ($action == 'delete_downloadfile') {
//
        } else {
            
        }
    } else {
        echo('Not Authorized User');
    }
//unlink('file.csv');
} else {
//nothing
    $message = 'not authorizated user';
        $json_response = $message;
        echo($json_response);
}
////
?>