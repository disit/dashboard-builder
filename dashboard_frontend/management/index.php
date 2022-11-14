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

if (!isset($_SESSION)) {
  session_start();
}

if(isset($_GET['switchNewLayout'])) {

  if ($_SESSION['isPublic']) {
    $cookie_name = "layout";
    if ($_GET['switchNewLayout'] == "true") {
      $cookie_value = "new_layout";
    } else {
      $cookie_value = "legacy_layout";
    }
    setcookie($cookie_name, $cookie_value, time() + (86400), "/"); // 86400 = 1 day
  } else if (isset($_COOKIE['layout'])) {
    $cookie_name = "layout";
    if ($_GET['switchNewLayout'] == "true") {
      $cookie_value = "new_layout";
    } else {
      $cookie_value = "legacy_layout";
    }
    setcookie($cookie_name, $cookie_value, time() + (86400), "/"); // 86400 = 1 day
  }

  if (isset($_SESSION['newLayout'])) {
    if ($_GET['switchNewLayout'] == "false") {
      $_SESSION['newLayout'] = false;
    } else {
      $_SESSION['newLayout'] = true;
    }
  } else {
    $_SESSION['newLayout'] = true;
  }
}

exit();
?>
