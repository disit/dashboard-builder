<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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

    $envFileContent = parse_ini_file("conf/environment.ini"); 
    $activeEnv = $envFileContent["environment"]["value"];
   
    $filesList = scandir("../conf/");
    $j = 0;
    
    for($i = 0; $i < count($filesList); $i++)
    {
        if(($filesList[$i] != ".")&&($filesList[$i] != "..")&&($filesList[$i] != "environment.ini"))
        {
            $fileContent = parse_ini_file("../conf/" . $filesList[$i]);
           
            foreach($fileContent as $key => $value) 
            {
                if(($key != "fileDesc")&&($key != "customForm"))
                {
                    if(is_array($value))
                    {
                        $varName = $key;
                        $$varName = $fileContent[$key][$activeEnv];
                    }
                }
            }
           $j++;
        }
    }




