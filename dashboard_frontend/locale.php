<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
 require_once __DIR__.'/phpCompatibility.php';  
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
    $set_languages0 = is_array($obj) && isset($obj['languages']) && dashboardIsList($obj['languages']) ? $obj['languages'] : [];
    $set_languages = [];
    //converti Array
    $cont = count($set_languages0);
    for ($i = 0; $i < $cont; $i++) {
        //echo($i);
        if (is_array($set_languages0[$i]) && isset($set_languages0[$i]['code']) && is_string($set_languages0[$i]['code'])) {
            $set_languages[] = $set_languages0[$i]['code'];
        }
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
        if (isset($_COOKIE['lang']) && validLocale($_COOKIE['lang'], $set_languages)){
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
