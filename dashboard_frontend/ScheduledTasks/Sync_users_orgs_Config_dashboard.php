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

function udate($format, $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

include '../config.php';

error_reporting(E_ERROR);
//mysqli_report(MYSQLI_REPORT_ALL) ;

$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, $dbname);

$startTime = new DateTime('now', new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting SCRIPT: Synchronize users and organization in Config_dashboard at: ".$start_time_ok."\n");

$startTime = new DateTime('now', new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$genFileContent = parse_ini_file("../conf/environment.ini");
$env = $genFileContent['environment']['value'];

$count = 1;

//$query = "SELECT * FROM Dashboard.Config_dashboard WHERE deleted != 'yes' AND (organizations IS NULL OR organizations = 'Other') ORDER BY id DESC;";
$query = "SELECT * FROM Dashboard.Config_dashboard WHERE deleted != 'yes' ORDER BY id DESC;";
$rs = mysqli_query($link, $query);
$result = [];
if($rs) {

    while($row = mysqli_fetch_assoc($rs)) 
    {
        $dashUser = $row['user'];
        $dashId = $row['Id'];
        $dashTitle = $row['name_dashboard'];

        $ldapUsername = "cn=" . $dashUser . "," . $ldapBaseDN;
        // URI form avoids the deprecated host/port overload on PHP 8.5.
        $ldapUris = [];
        foreach (preg_split('/\s+/', trim($ldapServer)) as $ldapHost) {
            if (strpos($ldapHost, '://') !== false) {
                $ldapUris[] = $ldapHost;
                continue;
            }
            if (filter_var($ldapHost, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
                $ldapHost = '[' . $ldapHost . ']';
            }
            if (!preg_match('/:\d+$/', $ldapHost)) $ldapHost .= ':' . $ldapPort;
            $ldapUris[] = 'ldap://' . $ldapHost;
        }
        $ds = ldap_connect(implode(' ', $ldapUris));
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $bind = ldap_bind($ds);
        $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
        if (is_null($organization)) {
            $organization = "None";
            $organizationName = "Other";
        } else if ($organization == "") {
            $organization = "None";
            $organizationName = "Other";
        } else {
            $organizationName = $organization;
        }

     //   $organizationName = $organizationName . "__org";
        
        $queryUpdt = "UPDATE Dashboard.Config_dashboard SET organizations = '" . $organizationName . "' WHERE Id = ". $dashId;
        $updtRes = mysqli_query($link, $queryUpdt);
        if ($updtRes != false) {
            echo($count . " - Synchronized DASHBOARD Id : " . $dashId . " (" . $dashTitle . ") BY USER: " . $dashUser . " with ORGANIZATION: " . $organizationName . "\n");
        } else {
            echo($count . " - ERROR IN Synchronizing DASHBOARD Id : " . $dashId . " (" . $dashTitle . ") BY USER: " . $dashUser . " with ORGANIZATION: " . $organizationName . "\n");
        }
        $count++;
    }
}

$endTime = new DateTime('now', new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End SCRIPT Synchronize users and organization in Config_dashboard at: ".$end_time_ok);