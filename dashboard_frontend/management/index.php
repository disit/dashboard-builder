<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
include('../config.php');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$currDom = $_SERVER['HTTP_HOST'];

$domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
$r = mysqli_query($link, $domQ);
$url = "";
$title = "";
if ($r) {
  if (mysqli_num_rows($r) > 0) {
    $row = mysqli_fetch_assoc($r);
    if(isset($row['publicLandingPageUrl']) && $row['publicLandingPageUrl']) 
      $url = $row['publicLandingPageUrl'];
    else
      $url = $row['landingPageUrl'];
    $title = $row['landingPageTitle'];
  }
}
if(strpos($url, "http://")===0 || strpos($url, "https://")===0) {
  header("location: iframeApp.php?linkUrl=".urlencode($url)."&pageTitle=".urlencode($title)."&fromSubmenu=false");
} else {
  header("location: $url");  
}
exit();
?>
