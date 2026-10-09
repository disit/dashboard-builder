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

    include '../config.php'; 
    error_reporting(E_ERROR | E_NOTICE);
    date_default_timezone_set('Europe/Rome');

    session_start(); 
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    mysqli_set_charset($link, 'utf8mb4');

// Language detection
    $curr = '';

    // 1. Give precedence to lang_dash from session
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['lang_dash']) && $_SESSION['lang_dash'] !== '') {
        $curr = $_SESSION['lang_dash'];
    }
    // 2. Else, give precedence to lang_dash from cookie
    elseif (isset($_COOKIE['lang_dash']) && $_COOKIE['lang_dash'] !== '') {
        $curr = $_COOKIE['lang_dash'];
        // If found in cookie, ensure it's also in session for consistency during the current request
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }
    // 3. If lang_dash is still not set, use lang from session
    elseif (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['lang']) && $_SESSION['lang'] !== '') {
        $curr = $_SESSION['lang'];
        // Since lang_dash was not set, we use lang and promote it to lang_dash
        $_SESSION['lang_dash'] = $curr;
    }
    // 4. Else, use lang from cookie
    elseif (isset($_COOKIE['lang']) && $_COOKIE['lang'] !== '') {
        $curr = $_COOKIE['lang'];
        // Since lang_dash was not set, we use lang and promote it to lang_dash
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }
    // 5. Final fallback: use $curr_lang if available
    elseif (isset($curr_lang) && $curr_lang !== '') {
        $curr = $curr_lang;
        // Since lang_dash was not set, we use $curr_lang and promote it to lang_dash
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['lang_dash'] = $curr;
        }
    }// Pass PHP language to JS

    // Translations only if the language selector is enabled for the dashboard of this widget
    $langSelectorVisible = 'no';
    try {
        $widgetNameLang = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
        $rLang = mysqli_query($link, "SELECT Config_dashboard.langSelectorVisible FROM Dashboard.Config_widget_dashboard " .
            "JOIN Dashboard.Config_dashboard ON Config_widget_dashboard.id_dashboard = Config_dashboard.Id " .
            "WHERE Config_widget_dashboard.name_w = '$widgetNameLang'");
        if ($rLang && ($rowLang = mysqli_fetch_assoc($rLang)) && $rowLang['langSelectorVisible'] === 'yes') {
            $langSelectorVisible = 'yes';
        }
    } catch (\Throwable $e) {
        // langSelectorVisible column not yet created (DB not migrated): selector disabled
        $langSelectorVisible = 'no';
    }
    // Session no longer needed: release the lock so that the widgets requests can run in parallel
    session_write_close();

    header("Cache-Control: private, " . ($langSelectorVisible === 'yes' ? "no-cache" : "max-age=$cacheControlMaxAge"));

    $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
    
    $response = [];

    $q = "SELECT * FROM Dashboard.Config_widget_dashboard " . 
         "LEFT JOIN Dashboard.Widgets " .    
         "ON Config_widget_dashboard.type_w = Widgets.id_type_widget " .   
         "WHERE name_w = '$widgetName'";
    $r = mysqli_query($link, $q);

    if($r && mysqli_num_rows($r)>0) {
        $response['params'] = mysqli_fetch_assoc($r);       
        $response['detail'] = 'Ok';
    } else { //check if it is from datainspector
        $q = "SELECT * FROM Dashboard.DataInspector " . 
             "LEFT JOIN Dashboard.Widgets " .    
             "ON DataInspector.type_w = Widgets.id_type_widget " .   
             "WHERE name_w = '$widgetName'";
        $r = mysqli_query($link, $q);

        if($r && mysqli_num_rows($r)>0) {
            $response['params'] = mysqli_fetch_assoc($r);       
            $response['detail'] = 'Ok';
        }
        else {
            $response['detail'] = 'Ko';
        }
    }

    $response['geoServerUrl'] = $geoServerUrl;
    $response['heatmapUrl'] = $heatmapUrl;

    if($response['geoServerUrl'] == null) $response['geoServerUrl'] = "https://wmsserver.snap4city.org/";
    if($response['heatmapUrl'] == null) $response['heatmapUrl'] = "https://heatmap.snap4city.org/";

    if(isset($_REQUEST['t1']) && isset($_REQUEST['t2'])){
        $response['t1'] = $_REQUEST['t1'];
        $response['t2'] = $_REQUEST['t2'];
    }
    
    // Load only the keys used by the translation block below, not the whole language catalog.
    $translations = [];
    if (!empty($curr) && $langSelectorVisible === 'yes' && isset($response['params'])) {
        $params = $response['params'];
        $translationKeys = [];
        $addTranslationKey = function ($text) use (&$translationKeys) {
            if (is_scalar($text)) {
                $key = is_string($text) ? $text : (string) (int) $text;
                $translationKeys[$key] = $key;
            }
        };
        if (isset($params['title_w'])) {
            $addTranslationKey($params['title_w']);
        }
        $serviceUri = isset($params['serviceUri']) && is_string($params['serviceUri']) ? json_decode($params['serviceUri'], true) : null;
        foreach (['firstAxis', 'secondAxis'] as $axis) {
            if (isset($serviceUri[$axis]['labels']) && is_array($serviceUri[$axis]['labels'])) {
                foreach ($serviceUri[$axis]['labels'] as $label) {
                    $addTranslationKey($label);
                }
            }
        }
        $rowParameters = isset($params['rowParameters']) && is_string($params['rowParameters']) ? json_decode($params['rowParameters'], true) : null;
        if (is_array($rowParameters)) {
            foreach ($rowParameters as $rp) {
                foreach (['label', 'metricLabel', 'metricType'] as $field) {
                    if (isset($rp[$field])) {
                        $addTranslationKey($rp[$field]);
                    }
                }
                if (isset($rp['smField']) && is_string($rp['smField']) && $rp['smField'] !== '') {
                    $addTranslationKey($rp['smField']);
                }
            }
        }
        $styleParameters = isset($params['styleParameters']) && is_string($params['styleParameters']) ? json_decode($params['styleParameters'], true) : null;
        if (isset($styleParameters['editDeviceLabels']) && is_array($styleParameters['editDeviceLabels'])) {
            foreach ($styleParameters['editDeviceLabels'] as $label) {
                $addTranslationKey($label);
            }
        }
        if (!empty($translationKeys)) {
            $keysEsc = [];
            foreach ($translationKeys as $key) {
                $keysEsc[] = "'" . mysqli_real_escape_string($link, $key) . "'";
            }
            $langEsc = mysqli_real_escape_string($link, $curr);
            $q = "SELECT menuText, translatedText FROM multilanguage WHERE LOWER(TRIM(language)) = LOWER(TRIM('" . $langEsc . "')) AND TRIM(translatedText) <> '' AND menuText IN (" . implode(',', $keysEsc) . ")";
            $queryResultMulti = mysqli_query($link, $q);
            if ($queryResultMulti) {
                while ($rowMulti = mysqli_fetch_assoc($queryResultMulti)) {
                    $translations[$rowMulti['menuText']] = $rowMulti['translatedText'];
                }
            }
        }
    }

    // --- Start: Translation of Widget Parameters ---
    if (!empty($translations) && isset($response['params'])) {
        $params = $response['params'];
        
        // Translate the main title of the widget
        if (isset($params['title_w']) && isset($translations[$params['title_w']])) {
            $response['params']['title_w'] = $translations[$params['title_w']];
        }

         // Translate parameters within 'serviceUri'
         if (isset($params['serviceUri'])) {
             // Already decoded while collecting translation keys above.
            // if ($serviceUri) {
            //    echo "<pre>First Axis:\n";
            //    print_r($serviceUri['firstAxis'] ?? []);
            //    echo "\nSecond Axis:\n";
            //    print_r($serviceUri['secondAxis'] ?? []);
            //    echo "</pre>";
            // }
             if ($serviceUri && is_array($serviceUri)) {
                 if (isset($serviceUri['firstAxis']['labels']) && is_array($serviceUri['firstAxis']['labels'])) {
                     foreach ($serviceUri['firstAxis']['labels'] as $key => $label) {
                         if (isset($translations[$label])) {
                             $serviceUri['firstAxis']['labels'][$key] = $translations[$label];
                         }
                     }
                 }
                 if (isset($serviceUri['secondAxis']['labels']) && is_array($serviceUri['secondAxis']['labels'])) {
                     foreach ($serviceUri['secondAxis']['labels'] as $key => $label) {
                         if (isset($translations[$label])) {
                             $serviceUri['secondAxis']['labels'][$key] = $translations[$label];
                         }
                     }
                 }
                 $response['params']['serviceUri'] = json_encode($serviceUri);
             }
         }
        // Translate parameters within 'rowParameters'
        if (isset($params['rowParameters'])) {
            // Already decoded while collecting translation keys above.
            if ($rowParameters && is_array($rowParameters)) {
                foreach ($rowParameters as $key => $rp) {
                    if (isset($rp['label']) && isset($translations[$rp['label']])) {
                        $rowParameters[$key]['label'] = $translations[$rp['label']];
                    } else if (isset($rp['metricLabel']) && isset($translations[$rp['metricLabel']])) {
                        $rowParameters[$key]['metricLabel'] = $translations[$rp['metricLabel']];
                    } else if (isset($rp['metricType']) && isset($translations[$rp['metricType']])) {
                        $rowParameters[$key]['metricLabel'] = $translations[$rp['metricType']];
                    } else if (isset($rp['smField']) && is_string($rp['smField']) && $rp['smField'] !== '' && isset($translations[$rp['smField']])) {
                        // Display-only fallback: leave smField and all data-matching keys unchanged.
                        $rowParameters[$key]['metricLabel'] = $translations[$rp['smField']];
                    }
                    // if (isset($rp['metricName']) && isset($translations[$rp['metricName']])) {
                    //     $rowParameters[$key]['metricName'] = $translations[$rp['metricName']];
                    // }
                }
                $response['params']['rowParameters'] = json_encode($rowParameters);
            }
        }

        if (isset($params['styleParameters'])) {
            // Already decoded while collecting translation keys above.
            if ($styleParameters && isset($styleParameters['editDeviceLabels'])) {
                foreach ($styleParameters['editDeviceLabels'] as $key => $editLabel) {
                    if (isset($translations[$editLabel])) {
                        $styleParameters['editDeviceLabels'][$key] = $translations[$editLabel];
                    }
                }
                $response['params']['styleParameters'] = json_encode($styleParameters);
            }
        }
        // if (isset($params['wizardRowIds'])) {
        //     $wizardRowIds = json_decode($params['wizardRowIds'], true);
        //     if($wizardRowIds){
        //         foreach ($wizardRowIds as $key => $row){
        //             if(isset($row['low_level_type']) && isset($translations[$row['low_level_type']])){
        //                 $wizardRowIds[$key]['low_level_type'] = $translations[$row['low_level_type']];
        //             }
        //         }
        //         $response['params']['wizardRowIds'] = json_encode($wizardRowIds);
        //     }
        // }
    }
    // --- End: Translation of Widget Parameters ---

    echo json_encode($response);