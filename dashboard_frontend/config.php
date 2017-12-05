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

$username = "user";
$password = "password";
$dbname = "Dashboard";
$esbHost = "localhost";
$esbDbUsr = "user";
$esbDbPwd = "passord";
$esbDbName = "resolute";
$ldapServer = 'localhost';
$ldapPort = 389;
$ldapTool = "Dashboard";
$smtpHost = "localhost";
$smtpAuth = "false";
$emailFromAddress = "me@email.com";
$emailFromName = "DISIT Dashboard Management System"; 
$notificatorApiUsr = "alarmManager";
$notificatorApiPwd = "passwd";
$notificatorAppName = "Dashboard+Manager";
$googleFontsApi = "https://www.googleapis.com/webfonts/v1/webfonts";
$googleApiForSingleFont = "https://fonts.googleapis.com/css?family=";
$googleApiKey = "..."; //used for fonts
$bannerMeteoAlertUrl = "http://www.cfr.toscana.it/banner/banner_cfr.php";
$pcGeneralBulletinUrl = "http://protezionecivile.comune.fi.it/?cat=5&feed=json";

$production = true; 

if($production)
{
  $host = "localhost";
  $appUrl = "http://localhost";
  $appHost = "localhost";
  $serviceMapUrlPrefix = "https://servicemap.disit.org/WebAppGrafo/";
  $internalServiceMapUrlPrefix = "";
  $internalTwitterVigilanceHost = "";
  $notificatorUrl = ""; //"http://localhost/notificator/restInterface.php";
  $notificatorLink = "http://localhost/notificator/";
  $cacheControlMaxAge = 1800;
  $sessionDuration = 14400;
}
else
{
  $host = "localhost";
  $appUrl = "http://localhost";
  $appHost = "localhost";
  $serviceMapUrlPrefix = "https://servicemap.disit.org/WebAppGrafo/";
  $internalServiceMapUrlPrefix = "";
  $internalTwitterVigilanceHost = "";
  $notificatorUrl = ""; //"http://localhost/notificator/www/restInterface.php";
  $notificatorLink = "http://localhost/notificator/www";
  $cacheControlMaxAge = 30;
  $sessionDuration = 3600;
}
