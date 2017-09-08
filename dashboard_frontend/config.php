<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
$production = true; 

if($production)
{
  $host = "localhost";
  $appUrl = "http://dashboard.km4city.org/dashboardSmartCity";
  $serviceMapUrlPrefix = "http://servicemap.disit.org/WebAppGrafo/";
  $internalServiceMapUrlPrefix = "http://...";
  $internalTwitterVigilanceHost = "http://...";
  $internalServiceMapUrlPrefix = "http://192.168.0.206:8080/WebAppGrafo/";
  $internalTwitterVigilanceHost = "http://192.168.0.50";
  $notificatorUrl = "http://.../notificator/restInterface.php";
  $notificatorLink = "http://.../";
}
else
{
  $host = "localhost";
  $appUrl = "http://localhost/temp";
  $serviceMapUrlPrefix = "http://servicemap.disit.org/WebAppGrafo/";
  $internalServiceMapUrlPrefix = "http://.../WebAppGrafo/";
  $internalTwitterVigilanceHost = "http://...";
  $notificatorUrl = "http://localhost/notificator/restInterface.php";
  $notificatorLink = "http://localhost/notificator/";
  /*$serviceMapUrlPrefix = "http://www.disit.org/ServiceMap/";
}




