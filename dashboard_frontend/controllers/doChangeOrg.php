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

try {
	
	session_start();	
	include '../config.php'; 
    error_reporting(E_ERROR | E_NOTICE);
    date_default_timezone_set('Europe/Rome');
	
	if(!"RootAdmin" == $_SESSION["loggedRole"]) { header("HTTP/1.1 401 Unauthorized"); die(); }
	
	$link = mysqli_connect($host, $username, $password);
	mysqli_select_db($link, $dbname);
	
	$elmtType = mysqli_real_escape_string($link, $_GET['elmtType']);
	$elmtId = mysqli_real_escape_string($link, $_GET['elmtId']);
	$newOrg = mysqli_real_escape_string($link, $_GET['newOrg']);
	
	if("Synoptic" == $elmtType) $query = "UPDATE DashboardWizard SET organizations = '$newOrg' WHERE high_level_type = 'Synoptic' AND id = $elmtId";
	else if ("SynopticTemplate" == $elmtType) $query = "UPDATE SynopticTemplates SET organizations = '$newOrg' WHERE high_level_type = 'SynopticTemplate' AND id = $elmtId";
	else { mysqli_close($link); header("HTTP/1.1 400 Bad Request"); die(); }

	if(!mysqli_query($link, $query)) header("HTTP/1.1 500 Internal Server Error");
	
	mysqli_close($link);
	
	die();
	
}
catch(Exception $e) {	
	header("HTTP/1.1 500 Internal Server Error");
	die();
}
?>