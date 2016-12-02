<?php
$username = "user";
$password = "password";
$dbname = "Dashboard";


$production=false; //commentare per test

if($production) {
  $host = "localhost";
  $serviceMapUrlPrefix = "http://servicemap.disit.org/WebAppGrafo/";
  $internalServiceMapUrlPrefix = "http://...";
  $internalTwitterVigilanceHost = "http://...";
}
else {
  $host = "localhost";
  $serviceMapUrlPrefix = "http://www.disit.org/ServiceMap/";
  $internalServiceMapUrlPrefix = "http://...";
  $internalTwitterVigilanceHost = "http://...";
}


