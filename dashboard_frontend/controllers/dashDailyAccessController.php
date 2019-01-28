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

if (!empty($_REQUEST["updateHour"])) {

    $dashId = $_GET['dashId'];
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    $queryAccess = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = $dashId ORDER BY date DESC;";
    $resultAccess = mysqli_query($link, $queryAccess);
    $currentDate = date("Y-m-d");
   // $currentDate = '2018-07-21';

    if ($resultAccess) {
        if (mysqli_num_rows($resultAccess) > 0) {

            $rowAcc = mysqli_fetch_array($resultAccess);
            if ($rowAcc['date'] === $currentDate) {     // CHECK ON LAST DATE
                // $dashboardWidgets = [];
                $queryUpdate = "UPDATE Dashboard.IdDashDailyAccess SET nMinutesPerDay = nMinutesPerDay + 1 WHERE IdDashboard = $dashId;";
                $resultUpdate = mysqli_query($link, $queryUpdate);

            } else {
                // insert in mysql
                $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                    "(IdDashboard, name_dashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$nameDash', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
                $resultInsert = mysqli_query($link, $queryInsert);
            }
        } else {
            // insert in mysql
            $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
                "(IdDashboard, name_dashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$nameDash', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
            $resultInsert = mysqli_query($link, $queryInsert);
        }

    } else {
        // insert in mysql
        $queryInsert = "INSERT INTO Dashboard.IdDashDailyAccess " .
            "(IdDashboard, name_dashboard, date, nAccessPerDay, nMinutesPerDay) VALUES ('$dashId', '$nameDash', '$currentDate', 1, 0) ON DUPLICATE KEY UPDATE nMinutesPerDay = nMinutesPerDay + 1;";
        $resultInsert = mysqli_query($link, $queryInsert);
    }
}