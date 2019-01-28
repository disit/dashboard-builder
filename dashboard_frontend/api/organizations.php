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

  include '../config.php';

  $link = mysqli_connect($host, $username, $password);
  mysqli_select_db($link, $dbname);
  //error_reporting(E_ERROR | E_NOTICE);

  if(!$link->set_charset("utf8")) 
  {
     exit();
  }
  $cond = '1';
  if(isset($_GET['org']))
    $cond = 'organizationName=\''.mysqli_real_escape_string($link, filter_input(INPUT_GET, 'org',FILTER_SANITIZE_STRING)).'\'';
  
  $query = "SELECT organizationName,kbUrl, gpsCentreLatLng, zoomLevel FROM Organizations WHERE ".$cond;
  $result = mysqli_query($link, $query);
  $response = [];
  if($result) {
    while($row=mysqli_fetch_assoc($result)) {
      $response[]=$row;
    }
  }
   
  echo json_encode($response);