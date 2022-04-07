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

  include '../config.php';
  header('Access-Control-Allow-Origin: *');

  $link = mysqli_connect($host, $username, $password);
  mysqli_select_db($link, $dbname);
  //error_reporting(E_ERROR | E_NOTICE);

  if(!$link->set_charset("utf8")) 
  {
     exit();
  }
  $cond = '1';
  $users = '';
  if(isset($_GET['org']))
    $cond = 'organizationName=\''.mysqli_real_escape_string($link, filter_input(INPUT_GET, 'org',FILTER_SANITIZE_STRING)).'\'';
  if (isset($_GET['includeUsers'])){
        $includeUsers = strtolower($_GET['includeUsers']);
      if ($includeUsers=='true'){
          $users = ' ,users';
      }
  }
  $query = "SELECT organizationName,kbUrl, gpsCentreLatLng, zoomLevel".$users." FROM Organizations WHERE ".$cond;
  $result = mysqli_query($link, $query);
  $response = [];
  if($result) {
      $i = 0;
    while($row=mysqli_fetch_assoc($result)) {
      //$response[]=$row;
        $response[$i]['organizationName']=$row['organizationName'];
        $response[$i]['kbUrl']=$row['kbUrl'];
        $response[$i]['gpsCentreLatLng']=$row['gpsCentreLatLng'];
        $response[$i]['zoomLevel']=$row['zoomLevel'];
        if (isset($_GET['includeUsers'])){
            $array_users=[];
            if (isset($row['users'])){
                $array_users=explode(",", $row['users']);
            }
           $response[$i]['users']=$array_users;
        }
        $i++;
    }
  }
   
  echo json_encode($response);