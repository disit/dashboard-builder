<?php
 include 'config.php';
function validLocale($locale, $set_languages) {
    return in_array($locale, $set_languages);
    //return in_array($locale, ['en_US', 'it_IT', 'ja_JP', 'ar_SA','el_GR']);
}

function selectLanguage($localizations) {
    /**
     * Verifies if the given $locale is supported in the project
     * @param string $locale
     * @return bool
     */
    if (!isset($_SESSION)) {
        session_start();
    }

    $obj = json_decode($localizations, true);
    $set_languages0 = $obj['languages'];
    //converti Array
    $cont = count($set_languages0);
    for ($i = 0; $i < $cont; $i++) {
        //echo($i);
        $set_languages[$i] = $set_languages0[$i]['code'];
    }
    //print_r[$set_languages];
    //
    //$lang = 'en_US';
//setting the source/default locale, for informational purposes
//$lang = 'en_US';
    $browser_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $browser_explode = explode(",", $browser_language);
    $current_language = $browser_explode[0];
    $lang = $current_language;

    if (isset($_GET['lang']) && validLocale($_GET['lang'], $set_languages)) {
        // the locale can be changed through the query-string
        $lang = "";
        $lang = filter_var($_GET['lang'], FILTER_SANITIZE_STRING);    //you should sanitize this!
        $_SESSION['lang'] = $lang; //it's stored in a cookie so it can be reused
    } elseif (isset($_SESSION['lang']) && validLocale($_SESSION['lang'], $set_languages)) {
        // if the cookie is present instead, let's just keep it
        $lang = $_SESSION['lang']; //you should sanitize this!
    } /*elseif ($_SESSION['loggedRole']=='RootAdmin' && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        // default: look for the languages the browser says the user accepts
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        array_walk($langs, function (&$lang) {
            $lang = strtr(strtok($lang, ';'), ['-' => '_']);
        });
        foreach ($langs as $browser_lang) {
            if (validLocale($browser_lang, $set_languages)) {
                $lang = $browser_lang;
                break;
            }
        }
    }*/ else {
        /*$browser_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $browser_explode = explode(",", $browser_language);
        $current_language = $browser_explode[0];
        $lang = $current_language;*/
        $lang= 'en_US';
        $_SESSION['lang'] = $lang;
        if (isset($_COOKIE['lang'])){
            $_SESSION['lang'] = $_COOKIE['lang'];
        }
    }
    // echo($browser_language);
    //echo($lang);
    $lang1 = $lang;
    $lang .= ".utf8";
// here we define the global system locale given the found language
    putenv("LANG=$lang");
    putenv("LANGUAGE=$lang");

// this might be useful for date functions (LC_TIME) or money formatting (LC_MONETARY), for instance
    if (setlocale(LC_ALL, $lang) === FALSE) {
//  echo "locale $lang not available";
        //  exit;
    }
#echo $lang;
    bindtextdomain("messages", dirname(__FILE__) . "/locale");
    bind_textdomain_codeset("messages", 'UTF-8');
    textdomain("messages");

    $lang1 = str_replace("-", "_", $lang1);
    //echo($lang1);
    return ($lang1);
}

selectLanguage($localizations);
