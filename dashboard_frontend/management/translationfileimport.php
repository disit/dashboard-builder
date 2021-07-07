<?php

include '../config.php';
require '../sso/autoload.php';
include 'simpleXLSX.php';

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
    if ($role_session_active == 'RootAdmin'){
    ////
    $action = $_REQUEST['action'];
    //$import_lang = $_REQUEST['import_lang'];
    $lang = '';
    if (isset($_REQUEST['lang'])) {
        $lang = $_REQUEST['lang'];
    }
    //echo('$lang:' . $lang);
    // echo($import_lang);
    //
        if (isset($_FILES['file']['name'])) {
        //$fileLang = $_FILES['import_lang'];
        $fileName = $_FILES['file']['name'];
        $fileType = $_FILES['file']['type'];
        $fileError = $_FILES['file']['error'];
        $fileContent = file_get_contents($_FILES['file']['tmp_name']);
///
//XLS
        if (strpos($fileName, '.xlsx') !== false) {
            $json = xlsToJson($_FILES['file']['tmp_name']);
            //echo($json);
        }
        ////
        //
        if (strpos($fileName, '.csv') !== false) {
            $json = csvToJson($_FILES['file']['tmp_name']);
            //echo($json);
        }
        $test_key = json_decode($json);
        //$keys = array_keys(json_decode($val1, true));
        $arr_ref = array();
        $arr_tr = array();
        //$keys
        //CONVERSIONE DEL JSON
        $count = count($test_key);
        //
        //
        $er_ind = 0;
        $crr_ind = 0;
        if (isset($test_key[0]->{'Reference Text'}) && $test_key[0]->{'Translated Text'}) {
            // do something
            for ($i = 0; $i < $count; $i++) {
                $ref = ($test_key[$i]->{'Reference Text'});
                $trans = ($test_key[$i]->{'Translated Text'});
                //echo($ref.', '.$trans.'<br />');
                //
            $query = 'SELECT DISTINCT * FROM multilanguage WHERE menuText="' . $ref . '" AND  language="' . $lang . '";';

                $result = mysqli_query($link, $query);
                $response_errors = array();
                $response_corrected = array();
                //echo($query);
                //$i = 0;
                //if ($result) {
                //  if (mysqli_num_rows($result) > 0) {
                // print_r($result);
                $ref = mysqli_real_escape_string($link, $ref);
                $ref = filter_var($ref, FILTER_SANITIZE_STRING);
                //
                $lang = mysqli_real_escape_string($link, $lang);
                $lang = filter_var($lang, FILTER_SANITIZE_STRING);
                //
                $trans = mysqli_real_escape_string($link, $trans);
                $trans = filter_var($trans, FILTER_SANITIZE_STRING);
                //////////
                $query0 = "UPDATE multilanguage
                    SET 
                        translatedText='" . $trans . "'
                    WHERE menuText='" . $ref . "' AND  language='" . $lang . "';";
                //echo($query0);
                $result0 = mysqli_query($link, $query0);
                $message = 'correct';
                //
                $response_corrected[$crr_ind] = $ref;
                $crr_ind++;
                //array_push($response_errors, $ref);
                //echo ($message);
                ////////////
                /* } else {
                  echo('NO');
                  $message = 'Not found';
                  $response_errors[$er_ind] = $ref;
                  $er_ind++;
                  //echo ($message);
                  } */
                /* } else {
                  $message = 'Error';
                  $response_errors[$er_ind] = $ref;
                  $er_ind++;
                  //$response['message']=$message;
                  //$response['errors']=$response_errors;
                  //echo ($message);
                  } */
                //
            }
        } else {
            ///*****///
            $message = 'Not valid keys';
            //$response = $message;
            ///****///
        }
        //fclose($fp);
        $response = $message;
        //$response['errors'] = $response_errors;
        //$response['corrected'] = $response_corrected;
        $json_response = $message;
        echo($json_response);
        // header('location:translationmanager.php');
        //exit;
///
    } else {
        //echo('void');
        $response = 'not file';
        // $response['errors'] = '';
        //$response['corrected'] = '';
        $json_response = $message;
        echo($json_response);
    }

    //nothing
    }else{
        $message = 'not authorizated user';
        $json_response = $message;
        echo($json_response);
    }
}else{
   $message = 'not authorizated user';
        $json_response = $message;
        echo($json_response);
}

function csvToJson($fname) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }

    //read csv headers
    $key = fgetcsv($fp, "1024", ";");

    // parse csv rows into array
    $json = array();
    while ($row = fgetcsv($fp, "1024", ";")) {
        $json[] = array_combine($key, $row);
    }

    // release file handle
    fclose($fp);

    // encode array to json
    return json_encode($json);
}

function xlsToJson($fname) {
    $fp = '';
    if ($xlsx = SimpleXLSX::parse($fname)) {
        $fp = ($xlsx->rows());
    } else {
        echo SimpleXLSX::parseError();
    }
    //
    $json = array();
    $key = $fp[0];
    $lung = count($fp);
    for ($i = 1; $i < $lung; $i++) {
        $row['Reference Text'] = $fp[$i][0];
        $row['Translated Text'] = $fp[$i][1];
        $json[] = array_combine($key, $row);

        //$json[$i] = $row;
    }

    //print_r($fp);
    return json_encode($json);
}

?>