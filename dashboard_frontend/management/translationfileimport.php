<?php

include '../config.php';
require '../sso/autoload.php';
include 'simpleXLSX.php';
require_once __DIR__ . '/../controllers/translationRegistration.php';

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
mysqli_set_charset($link, 'utf8mb4');
if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];
    session_write_close();
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
        try {
            if (strpos($fileName, '.xlsx') !== false) {
                $json = xlsToJson($_FILES['file']['tmp_name']);
            } elseif (strpos($fileName, '.csv') !== false) {
                $json = csvToJson($_FILES['file']['tmp_name']);
            } else {
                throw new \UnexpectedValueException('Unsupported file type');
            }
            $test_key = json_decode($json);
        } catch (\UnexpectedValueException $error) {
            // Keep the exact feedback tokens recognized by translationmanager.php.
            error_log('Translation file import invalid: ' . $error->getMessage());
            echo $error->getMessage() === 'Not valid keys' ? 'Not valid keys' : 'Error';
            exit;
        }
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
                // Short shared lock: cooperate with automatic translations and registration.
                $ref = filter_var($ref, FILTER_SANITIZE_STRING);
                $lang = filter_var($lang, FILTER_SANITIZE_STRING);
                $trans = filter_var($trans, FILTER_SANITIZE_STRING);
                try {
                    withTranslationRegistrationLock($link, function () use ($link, $ref, $trans, $lang) {
                        $statement = translationRegistrationStatement($link,
                            'UPDATE multilanguage SET translatedText=? WHERE menuText=? AND language=?',
                            [$trans, $ref, $lang]);
                        mysqli_stmt_close($statement);
                    });
                    $message = 'correct';
                } catch (\Throwable $error) {
                    error_log('Translation file import write unavailable');
                    $message = 'Error';
                    break;
                }
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
    if (!is_string($fname) || !is_file($fname) || !is_readable($fname) || !($fp = fopen($fname, 'r'))) {
        throw new \UnexpectedValueException('Cannot read import file');
    }
    try {
        $key = translationImportHeader(fgetcsv($fp, 0, ';', '"', '\\'));
        $json = array();
        $rowNumber = 1;
        while (($row = fgetcsv($fp, 0, ';', '"', '\\')) !== false) {
            $rowNumber++;
            $entry = translationImportRow($key, $row, $rowNumber);
            if ($entry !== null) $json[] = $entry;
        }
        return translationImportEncode($json);
    } finally {
        fclose($fp);
    }
}

function xlsToJson($fname) {
    if (!is_string($fname) || !is_file($fname) || !is_readable($fname)) {
        throw new \UnexpectedValueException('Cannot read import file');
    }
    $xlsx = SimpleXLSX::parse($fname);
    if (!$xlsx) throw new \UnexpectedValueException('Cannot parse XLSX file');
    $rows = $xlsx->rows();
    $key = translationImportHeader(is_array($rows) && isset($rows[0]) ? $rows[0] : null);
    $json = array();
    for ($i = 1; $i < count($rows); $i++) {
        $entry = translationImportRow($key, $rows[$i], $i + 1);
        if ($entry !== null) $json[] = $entry;
    }
    return translationImportEncode($json);
}

function translationImportEncode($rows) {
    $json = json_encode($rows);
    if ($json === false) throw new \UnexpectedValueException('Invalid text encoding');
    return $json;
}

function translationImportHeader($key) {
    if (!is_array($key) || count($key) !== 2 ||
        !in_array('Reference Text', $key, true) || !in_array('Translated Text', $key, true)) {
        throw new \UnexpectedValueException('Not valid keys');
    }
    return $key;
}

function translationImportRow($key, $row, $rowNumber) {
    // fgetcsv returns one null field for an empty line; it is not an import record.
    if (is_array($row) && count($row) === 1 && ($row[0] === null || trim($row[0]) === '')) return null;
    if (!is_array($row) || count($row) !== count($key)) {
        throw new \UnexpectedValueException('Invalid row ' . $rowNumber . ': expected 2 columns');
    }
    return array_combine($key, $row);
}

?>