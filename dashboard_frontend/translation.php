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
   
function translate_string($string, $language, $link) {
    $query = "SELECT translatedText FROM Dashboard.multilanguage WHERE language='" . $language . "' AND menuText='" . $string . "';";
    $result = mysqli_query($link, $query) or die(mysqli_error($link));

    $text = $string;
    $translatedText = "";
    if ($result) {
        $n = count($result);
        if ($n > 0){
            while($row = mysqli_fetch_assoc($result)){
                $translatedText = $row['translatedText'];
                if (($translatedText !== null)&&($translatedText !== "")) {
                    $text = $translatedText;
                } 
            }
        }
    }
    return($text);
}

//translate_string('Documentation and Articles', 'it_IT');
//echo($test);
?>

