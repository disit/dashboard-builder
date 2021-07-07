<?php
function translate_string($string, $lenguage, $link) {
include '../config.php';
//require '../sso/autoload.php';
//use Jumbojett\OpenIDConnectClient;


//$link = mysqli_connect($host, $username, $password) or die("failed to connect to server !!");
//mysqli_select_db($link, $dbname);
//echo($dbname);
//$test = translate_string('Settings', 'it_IT', $link);
//$lenguage = 'Settings';
//$string = 'it_IT';
//echo($test);
    $query = "SELECT translatedText FROM Dashboard.multilanguage WHERE language='" . $lenguage . "' AND menuText='" . $string . "';";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    //
    $text = $string;
    //print_r($result);
    $translatedText = "";
    if ($result) {
        $n = count($result);
        if ($n > 0){
        while($row = mysqli_fetch_assoc($result)){
            $translatedText = $row['translatedText'];
            if (($translatedText !== null)&&($translatedText !== "")) {
                $text = $translatedText;
                //return($text);
            } else {
                
               // return($text);
            }
        }
        }else{
            //return($text);  
        }
    } else {
        //return($text);
    }
    //
    return($text);
   // echo($text);
}

//translate_string('Documentation and Articles', 'it_IT');
//echo($test);
?>

