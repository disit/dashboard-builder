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

    $link = new mysqli($host, $username, $password, $dbname);
    $id = $_GET['IdMisura'];
    
    if($link->connect_error) 
    {
        die("Connection failed: " . $link->connect_error);
    }
    else
    {
        if(!isset($_GET['param']) || ($_GET['param'] != "myKPI")) {
            ini_set('max_execution_time', 1200);

            if (!$link->set_charset("utf8")) {
                exit();
            }

            $metricName = mysqli_real_escape_string($link, $_GET['IdMisura'][0]);

            $q1 = "SELECT * FROM Dashboard.Descriptions WHERE IdMetric = '$metricName'";
            $r1 = mysqli_query($link, $q1);

            if ($r1) {
                if (mysqli_num_rows($r1) > 0) {
                    $metricType = "shared";
                } else {
                    $q2 = "SELECT * FROM Dashboard.NodeRedMetrics WHERE name = '$metricName'";
                    $r2 = mysqli_query($link, $q2);

                    if ($r2) {
                        if (mysqli_num_rows($r2) > 0) {
                            $metricType = "personal";
                        }
                    }
                }
            }

            $rangedays = array();
            $hourMin = '';

            if (isset($_GET['time'])) {
                list($v, $unit) = explode("/", $_GET['time']);

                if (($v == 1 && $unit == "DAY") || $unit == "HOUR") {
                    $hourMin = ",hour(computationDate),minute(computationDate) ";
                }

                if ($unit == 'DAY') {
                    $where = " AND computationDate>=date(now())-interval " . ($v - 1) . " $unit";
                    $having1 = "";
                } else {
                    $where = "AND computationDate>=now()-interval " . $v . " $unit";
                    $having1 = "";
                }

                array_push($rangedays, array($where, $having1));

                if (isset($_GET['compare']) && ($_GET['compare'] == 1)) {
                    if ($unit == 'DAY') {
                        $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) - 1) . " $unit AND date(computationDate)<date(now())-interval " . ($v - 1) . " $unit";
                        $having2 = "";
                    } else {
                        $where = "AND computationDate>=now()-interval " . $v . " HOUR - INTERVAL 1 DAY AND computationDate<now()-interval 1 DAY";
                        $having2 = "";
                    }
                    array_push($rangedays, array($where, $having2));
                }
            } else {
                $having1 = '';
                array_push($rangedays, array("", $having1));
            }

            $rows = array();
            $i = -1;

            foreach ($id as $idValue) {
                $idValue = mysqli_real_escape_string($link, $idValue);
                foreach ($rangedays as $rangedaysValue) {
                    $rangedaysValue[0] = mysqli_real_escape_string($link, $rangedaysValue[0]);
                    $rangedaysValue[1] = mysqli_real_escape_string($link, $rangedaysValue[1]);

                    $i++;

                    if ($metricType == "shared") {
                        if (isset($_GET['lowerDateTime']) && isset($_GET['upperDateTime'])) {
                            $lowerDateTime = $_GET['lowerDateTime'];
                            $upperDateTime = $_GET['upperDateTime'];
                            $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(description_short) as descrip FROM Dashboard.Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='" . $idValue . "' AND computationDate>='". $lowerDateTime ."' AND computationDate<='". $upperDateTime ."' GROUP BY date(computationDate)$hourMin  $rangedaysValue[1];";
                        } else {
                            $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(description_short) as descrip FROM Dashboard.Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='" . $idValue . "' $rangedaysValue[0] GROUP BY date(computationDate)$hourMin  $rangedaysValue[1]";
                        }
                    } else {
                        if ($metricType == "personal") {
                            if (isset($_GET['lowerDateTime']) && isset($_GET['upperDateTime'])) {
                                $lowerDateTime = $_GET['lowerDateTime'];
                                $upperDateTime = $_GET['upperDateTime'];
                                $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(shortDesc) as descrip FROM Dashboard.Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data='" . $idValue . "' AND computationDate>='". $lowerDateTime ."' AND computationDate<='". $upperDateTime ."' GROUP BY date(computationDate)$hourMin  $rangedaysValue[1];";
                            } else {
                                $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(shortDesc) as descrip FROM Dashboard.Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data='" . $idValue . "' $rangedaysValue[0] GROUP BY date(computationDate)$hourMin  $rangedaysValue[1]";
                            }
                        }
                    }

                    $result = $link->query($sql);

                    while ($r = mysqli_fetch_assoc($result)) {
                        $rows[] = array('commit' => array('author' => $r, 'range_dates' => $i));
                    }
                    $data = array('data' => $rows, 'metricType' => $metricType);
                }
            }

            $data_json = json_encode($data);
            $link->close();
            echo($data_json);
        } else {


            // TO DO MY KPI PROXY !!!


        }
    }
    
