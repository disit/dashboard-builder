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

function udate($format = 'u', $microT) {

    $timestamp = floor($microT);
    $milliseconds = round(($microT - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

include '../config.php';

error_reporting(E_ERROR);
//mysqli_report(MYSQLI_REPORT_ALL) ;

//$link = mysqli_connect("192.168.0.37", "root", "kodekode");
$link = mysqli_connect($host, $username, $password);
//error_reporting(E_ALL);
mysqli_select_db($link, "Dashboard");

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$start_time_ok = str_replace("T", " ", $start_scritp_time_string[0]);
echo("Starting SCRIPT: Check Widgets in Config_widget_dashboard for Wizard Row IDs at: ".$start_time_ok."\n");

$startTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$start_scritp_time = $startTime->format('c');
$start_scritp_time_string = explode("+", $start_scritp_time);
$lastCheck = str_replace("T", " ", $start_scritp_time_string[0]);

$genFileContent = parse_ini_file("../conf/environment.ini");
$env = $genFileContent['environment']['value'];

$count = 1;
$okRowIds = [];
$updateQueryAll = "";

//$query = "SELECT * FROM Dashboard.Config_dashboard WHERE deleted != 'yes' AND (organizations IS NULL OR organizations = 'Other') ORDER BY id DESC;";
$query = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE wizardRowIds IS NOT NULL AND wizardRowIds <> \"{}\" AND wizardRowIds <> \"[]\" AND wizardRowIds <> \"null\" ORDER BY id DESC;";
$rs = mysqli_query($link, $query);
$result = [];
if($rs) {

    while($row = mysqli_fetch_assoc($rs))
    {
        $widgetId = $row['Id'];
        $name_w = $row['name_w'];
        $wizardRowString = $row['wizardRowIds'];
        $wizardRow = json_decode($wizardRowString, true);
        $wizardRowKey = array_keys($wizardRow)[0];
        $wizardRowId = explode("row", $wizardRowKey)[1];

        $query2 = "SELECT * FROM Dashboard.DashboardWizard WHERE id = " . $wizardRowId;
        $rs2 = mysqli_query($link, $query2);
        $result2 = [];
        if($rs2) {

            if ($row2 = mysqli_fetch_assoc($rs2)) {
                if ($row2['high_level_type'] == $wizardRow[$wizardRowKey]['high_level_type'] && $row2['nature'] == $wizardRow[$wizardRowKey]['nature'] && $row2['sub_nature'] == $wizardRow[$wizardRowKey]['sub_nature'] && $row2['low_level_type'] == $wizardRow[$wizardRowKey]['low_level_type'] && $row2['unique_name_id'] == $wizardRow[$wizardRowKey]['unique_name_id'] && $row2['instance_uri'] == $wizardRow[$wizardRowKey]['instance_uri'] && $row2['get_instances'] == $wizardRow[$wizardRowKey]['get_instances'] && $row2['parameters'] == $wizardRow[$wizardRowKey]['parameters']) {
                    $stopFlag = 1;
                    array_push($okRowIds, $wizardRowId);
                } else {
                    $stopFlag = 2;
                    $query3 = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = '" . $wizardRow[$wizardRowKey]['high_level_type'] . "' AND nature = '" . $wizardRow[$wizardRowKey]['nature'] . "' AND sub_nature = '" . $wizardRow[$wizardRowKey]['sub_nature'] . "' AND low_level_type = '" . $wizardRow[$wizardRowKey]['low_level_type'] . "' AND unique_name_id = '" . $wizardRow[$wizardRowKey]['unique_name_id'] . "' AND instance_uri = '" . $wizardRow[$wizardRowKey]['instance_uri'] . "' AND get_instances = '" . $wizardRow[$wizardRowKey]['get_instances'] . "' AND parameters = '" . $wizardRow[$wizardRowKey]['parameters'] . "';";
                    $rs3 = mysqli_query($link, $query3);
                    $result3 = [];
                    if($rs3) {

                        if ($row3 = mysqli_fetch_assoc($rs3)) {
                            $newIdToChange = $row3['id'];
                            $newWizardRowString = '{"row' . $newIdToChange . '":{' . explode(":{", $wizardRowString)[1];
                        }
                    }

                    $queryUpdt = "UPDATE Dashboard.Config_widget_dashboard SET wizardRowIds = '" . $newWizardRowString . "' WHERE Id = ". $widgetId;
                    $updateQueryAll = $updateQueryAll . $queryUpdt . "\n";
                    echo($count . " - Synchronization NEEDED for WIDGET Id : " . $widgetId . " (" . $name_w . ") with WIZARD! NEED To CHANGE OLD wizardRowId # " . $wizardRowId . " with CORRECT UPDATED wizardRowId # " . $newIdToChange . "\n");

                    // ESEGUE UPDATE PER OGNI SINGOLA QUERY
                /*  $updtRes = mysqli_query($link, $queryUpdt);
                    if ($updtRes != false) {
                        echo($count . " - Synchronized WIDGET Id : " . $widgetId . " (" . $name_w . ") with WIZARD! Changed OLD wizardRowId # " . $wizardRowId . " with CORRECT UPDATED wizardRowId # " . $newIdToChange . "\n");
                    } else {
                        echo($count . " - ERROR IN Synchronizing WIDGET Id : " . $widgetId . " (" . $name_w . ")\n");
                    }*/

                }
            } else {
                $query3 = "SELECT * FROM Dashboard.DashboardWizard WHERE high_level_type = '" . $wizardRow[$wizardRowKey]['high_level_type'] . "' AND nature = '" . $wizardRow[$wizardRowKey]['nature'] . "' AND sub_nature = '" . $wizardRow[$wizardRowKey]['sub_nature'] . "' AND low_level_type = '" . $wizardRow[$wizardRowKey]['low_level_type'] . "' AND unique_name_id = '" . $wizardRow[$wizardRowKey]['unique_name_id'] . "' AND instance_uri = '" . $wizardRow[$wizardRowKey]['instance_uri'] . "' AND get_instances = '" . $wizardRow[$wizardRowKey]['get_instances'] . "' AND parameters = '" . $wizardRow[$wizardRowKey]['parameters'] . "';";
                $rs3 = mysqli_query($link, $query3);
                $result3 = [];
                if($rs3) {

                    if ($row3 = mysqli_fetch_assoc($rs3)) {
                        $newIdToChange = $row3['id'];
                        $newWizardRowString = '{"row' . $newIdToChange . '":{' . explode(":{", $wizardRowString)[1];
                    }
                }

                $queryUpdt = "UPDATE Dashboard.Config_widget_dashboard SET wizardRowIds = '" . $newWizardRowString . "' WHERE Id = ". $widgetId;
                $updateQueryAll = $updateQueryAll . $queryUpdt . "\n";
                echo($count . " - Synchronization NEEDED for WIDGET Id : " . $widgetId . " (" . $name_w . ") with WIZARD! NEED To CHANGE OLD wizardRowId # " . $wizardRowId . " with CORRECT UPDATED wizardRowId # " . $newIdToChange . "\n");
            }

        }

        $count++;
    }
}

$endTime = new DateTime(null, new DateTimeZone('Europe/Rome'));
$end_scritp_time = $endTime->format('c');
$end_scritp_time_string = explode("+", $end_scritp_time);
$end_time_ok = str_replace("T", " ", $end_scritp_time_string[0]);
echo("End SCRIPT Check Widgets in Config_widget_dashboard for Wizard Row IDs at: ".$end_time_ok);