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
if (!isset($_SESSION)) {
    session_start();
  }

if (!empty($_REQUEST["updateHour"])) {

    $link = mysqli_connect($host, $username, $password);
    $dashId = escapeForSQL($_GET['dashId'], $link);
    if (checkVarType($dashId, "integer") === false) {
        eventLog("Returned the following ERROR in dashDailyAccessController.php for dashId = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script.");
        exit();
    };
    mysqli_select_db($link, $dbname);
    $queryAccess = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = '$dashId' ORDER BY date DESC;";
    $resultAccess = mysqli_query($link, $queryAccess);

    $currentDate = date("Y-m-d");
   // $currentDate = '2018-07-21';
    
    if ($resultAccess) {
        if (mysqli_num_rows($resultAccess) > 0) {

            $rowAcc = mysqli_fetch_array($resultAccess);
            if ($rowAcc['date'] === $currentDate) {     // CHECK ON LAST DATE
                // $dashboardWidgets = [];
                $queryUpdate = "UPDATE Dashboard.IdDashDailyAccess SET nMinutesPerDay = nMinutesPerDay + 1 WHERE IdDashboard = $dashId AND date = '$currentDate';";
                $resultUpdate = mysqli_query($link, $queryUpdate);

            } else {
                $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                    "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
                $resultInsert = mysqli_query($link, $queryInsert);
            }
        } else {
            $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
            $resultInsert = mysqli_query($link, $queryInsert);
        }

    } else {
        $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
            "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
        $resultInsert = mysqli_query($link, $queryInsert);
    }
    //user monitoring part

    $logFile = 'dailyaccess.log';
    if ($resourcesconsumptionHost) {
        $link2 = mysqli_connect($resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort);
        if (!isset($_SESSION['loggedUsername'])) {
            $userMonitoring = 'false';
            $logMessage = date("Y-m-d H:i:s") . " - WARN - No username.\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        } else {
            $enc_username = encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
            $queryAccessUser = "SELECT * FROM " .$resourcesconsumptionDb. ".daily_dashboard_accesses WHERE IdDashboard = $dashId AND UserID = '$enc_username' ORDER BY date DESC;";
            $resultAccessUser = mysqli_query($link2, $queryAccessUser);
            $logMessage = date("Y-m-d H:i:s") . " - INFO - user: " . $enc_username . " usermonitoring: " . $userMonitoring . "\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
        if ($userMonitoring == 'true') {
            if ($resultAccessUser) {
                if (mysqli_num_rows($resultAccessUser) > 0) {

                    $rowAcc = mysqli_fetch_array($resultAccessUser);
                    if ($rowAcc['date'] === $currentDate) {
                        // $dashboardWidgets = [];
                        $queryUpdate = "UPDATE " .$resourcesconsumptionDb. ".daily_dashboard_accesses SET nMinutesPerDay = nMinutesPerDay + 1 WHERE IdDashboard = $dashId AND date = '$currentDate' AND UserID = '$enc_username'";
                        $resultUpdate = mysqli_query($link2, $queryUpdate);

                    } else {
                        $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                            "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
                        $resultInsert = mysqli_query($link2, $queryInsert);
                    }
                } else {
                    $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                        "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
                    $resultInsert = mysqli_query($link2, $queryInsert);
                }

            } else {
            $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                    "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
                $resultInsert = mysqli_query($link2, $queryInsert);
            }
        }
    }
}
if (!empty($_REQUEST["updateAccess"])) {

    $link = mysqli_connect($host, $username, $password);
    $dashId = escapeForSQL($_GET['dashId'], $link);
    mysqli_select_db($link, $dbname);
    $queryAccess = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = $dashId ORDER BY date DESC;";
    $resultAccess = mysqli_query($link, $queryAccess);
    $currentDate = date("Y-m-d");
    //$currentDate = '2018-07-21';

    if($resultAccess) {
        if (mysqli_num_rows($resultAccess) > 0) {

            $rowAcc = mysqli_fetch_array($resultAccess);
            if ($rowAcc['date'] === $currentDate) {     // CHECK ON LAST DATE
            // $dashboardWidgets = [];
                $queryUpdate = "UPDATE Dashboard.IdDashDailyAccess SET nAccessPerDay = nAccessPerDay + 1 WHERE IdDashboard = $dashId AND date = '$currentDate';";
                $resultUpdate = mysqli_query($link, $queryUpdate);

            } else {
                $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                    "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
                $resultInsert = mysqli_query($link, $queryInsert);
            }
        } else {
            $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
            $resultInsert = mysqli_query($link, $queryInsert);
        }
    } else {
        $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
            "(IdDashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
        $resultInsert = mysqli_query($link, $queryInsert);
    }

    //user monitoring part
    $logFile = 'dailyaccess.log';
    if ($resourcesconsumptionHost) {
        $link2 = mysqli_connect($resourcesconsumptionHost, $resourcesconsumptionUser, $resourcesconsumptionPassword, $resourcesconsumptionDb, $resourcesconsumptionPort);
        if (!isset($_SESSION['loggedUsername'])) {
            $userMonitoring = 'false';
            $logMessage = date("Y-m-d H:i:s") . " - WARN - No username.\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        } else {
            $enc_username = encryptOSSL($_SESSION['loggedUsername'], $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
            $queryAccessUser = "SELECT * FROM " .$resourcesconsumptionDb. ".daily_dashboard_accesses WHERE IdDashboard = $dashId AND UserID = '$enc_username' ORDER BY date DESC;";
            $resultAccessUser = mysqli_query($link2, $queryAccessUser);
            $logMessage = date("Y-m-d H:i:s") . " - INFO - user access: " . $enc_username . " usermonitoring: " . $userMonitoring . "\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
        if ($userMonitoring == 'true') {
            if ($resultAccessUser) {
                if (mysqli_num_rows($resultAccessUser) > 0) {

                    $rowAcc = mysqli_fetch_array($resultAccessUser);
                    if ($rowAcc['date'] === $currentDate) {     // CHECK ON LAST DATE
                        // $dashboardWidgets = [];
                        $queryUpdate = "UPDATE " .$resourcesconsumptionDb. ".daily_dashboard_accesses SET nAccessPerDay = nAccessPerDay + 1 WHERE IdDashboard = $dashId AND date = '$currentDate' AND UserID = '$enc_username'";
                        $resultUpdate = mysqli_query($link2, $queryUpdate);

                    } else {
                        $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                            "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
                        $resultInsert = mysqli_query($link2, $queryInsert);
                    }
                } else {
                    $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                        "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
                    $resultInsert = mysqli_query($link2, $queryInsert);
                }
            } else {
                $queryInsert = "INSERT INTO " .$resourcesconsumptionDb. ".daily_dashboard_accesses " .
                    "(IdDashboard, UserID, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$enc_username', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nAccessPerDay = nAccessPerDay + 1;";
                $resultInsert = mysqli_query($link2, $queryInsert);
            }
        }
    }
}

