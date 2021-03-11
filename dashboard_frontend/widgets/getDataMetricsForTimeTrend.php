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


if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
} else {
    if (!isset($_GET['param']) || ($_GET['param'] != "myKPI")) {
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
            if (isset($_GET['compareperiod'][0]) && isset($_GET['timeCount'])) {
                switch ($_GET['compareperiod'][0]) {
                    case "previous week starting Monday":
                        $previousMonday = daytoMonday();
                        $where = "AND date(computationDate)>=date(now()) - interval " . $previousMonday . " DAY -interval " . ($v * $_GET['timeCount']) . " $unit  AND date(computationDate)<date(now()) - interval " . $previousMonday . " DAY + interval " . $v . " $unit - interval " . ($v*$_GET['timeCount']) . " DAY";;
                        $having2 = "";
                        break;
                    case "previous month - day 1":
                        //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day -interval " . ($_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day -interval " . ($_GET['timeCount'] - 1) . " MONTH";
                        $where = " AND computationDate>=date(now()) - interval (dayofmonth(now()) - 1) day - interval " . ($_GET['timeCount']) . " MONTH AND computationDate<date(now()) - interval (dayofmonth(now())) day - interval " . ($_GET['timeCount'] - 1) . " MONTH";
                        $having1 = "";
                        break;
                    case "same month prev year - day 1":
                        $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day -interval " . ($_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now())  day -interval " . ($_GET['timeCount'] - 1) . " MONTH";
                        $having1 = "";
                        break;
                    case "previous 6 months - day 1 of prev 6 months":
                        //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*6*$_GET['timeCount'] - (6*$v/180)) . " MONTH";
                        $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((5*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*6*$_GET['timeCount'] - (7*$v/180)) . " MONTH";
                        $having1 = "";     
                        break; 
                    case "same 6 months of prev year - day 1":
                        //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*6*$_GET['timeCount'] - (6*$v/180)) . " MONTH";
                        $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((5*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*6*$_GET['timeCount'] - (7*$v/180)) . " MONTH";
                        $having1 = "";
                        break;
                    case "same 6 months of prev year - day 1 month 1 or 7":
                        $monthto1or7 = getMonthto1or7();
                        //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*$v/180) + 6*$_GET['timeCount'] + $monthto1or7) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*6*$_GET['timeCount'] + $monthto1or7 - (6*$v/180)) . " MONTH";
                        $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . (6*$_GET['timeCount'] + $monthto1or7) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (6*$_GET['timeCount'] + $monthto1or7 - (6*$v/180) ) . " MONTH";
                        $having1 = "";
                        break;
                    case "previous year - month 1":
                        $where = " AND computationDate>=MAKEDATE(year(now()),1) - interval " . ($_GET['timeCount']) . " YEAR AND computationDate<MAKEDATE(year(now()),1) - interval " . ($_GET['timeCount']) . " YEAR + interval 1 year";
                        $having1 = "";
                        break;
                    case "previous year - day 1 of prev year":
                        $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((12*$v/365) + 12*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (2*12*$_GET['timeCount'] - (12*$v/365)) . " MONTH";
                        $having1 = "";
                        break;
                    default:
                        if ($unit == 'DAY') {
                            $where = " AND computationDate>=date(now())-interval " . (($v - 1) + ($v * $_GET['timeCount'])) . " $unit AND computationDate<now()-interval " . ($v * $_GET['timeCount']) . " $unit";
                            $having1 = "";
                        } else {
                            $where = "AND computationDate>=now()-interval " . ($v + ($v * $_GET['timeCount'])) . " $unit AND computationDate<=now()-interval " . ($v * $_GET['timeCount']) . " $unit";
                            $having1 = "";
                        }
                        break;
                }
            } else {
                if ($unit == 'DAY') {
                    $where = " AND computationDate>=date(now())-interval " . ($v - 1) . " $unit";
                    $having1 = "";
                } else {
                    $where = "AND computationDate>=now()-interval " . $v . " $unit";
                    $having1 = "";
                }
            }

                array_push($rangedays, array($where, $having1));

            if (isset($_GET['compare']) && ($_GET['compare'] == 1)) {
                if (isset($_GET['compareperiod'][0]) && isset($_GET['timeCount'])) {
                //    eventLog($_GET['compareperiod'][0]);
                //    eventLog($v);
                //    eventLog($unit);
                    switch ($_GET['compareperiod'][0]) {
                        case "4 previous hours":
                            $where = "AND computationDate>=now()-interval " . ((2 * $v) + ($v * $_GET['timeCount'])) . " HOUR AND computationDate<now()-interval " . ($v + ($_GET['timeCount'] * $v)) . " HOUR";
                            $having = "";
                            //eventLog("entrato in case 4 previous hours");
                            break;
                        case "4 hours day before":
                            $where = "AND computationDate>=now()-interval " . ($v + ($v * $_GET['timeCount'])) . " HOUR - INTERVAL 1 DAY AND computationDate<now()-interval 1 DAY - interval " . ($v * $_GET['timeCount']). " HOUR";
                            $having2 = "";
                            //eventLog("entrato in case 4 hours day before");
                            break;
                        case "12 previous hours":
                            $where = "AND computationDate>=now()-interval " . ((2 * $v) + ($v * $_GET['timeCount'])) . " HOUR AND computationDate<now()-interval " . ($v + ($_GET['timeCount'] * $v)) . " HOUR";
                            $having2 = "";
                            //eventLog("entrato in case 12 previous hours");
                            break;
                        case "12 hours day before":
                            $where = "AND computationDate>=now()-interval " . ($v + ($v * $_GET['timeCount'])) . " HOUR - INTERVAL 1 DAY AND computationDate<now()-interval 1 DAY -interval " . ($v * $_GET['timeCount']). " HOUR";
                            $having2 = "";
                            //eventLog("entrato in case 12 hours day before");
                            break;
                        case "previous day":
                            $where = "AND date(computationDate)>=date(now())-interval " . (((2 * $v) - 1) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . (($v - 1) + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous day");
                            break;
                        case "same day previous week":
                            $where = "AND date(computationDate)>=date(now())-interval " . (((7 * $v)) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . (((7 * $v) - 1) + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case same day previous week");
                            break;
                        case 'same day previous month':
                            $where = "AND date(computationDate)>=date(now())-interval " . (((30 * $v)) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . (((30 * $v) - 1) + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case same day previous month");
                            break;
                        case "previous week":
                            $where = "AND date(computationDate)>=date(now())-interval " . (((2 * $v) - 1 ) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . (($v - 1) + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous week");
                            break;
                        case "previous week starting Monday":
                            $previousMonday = daytoMonday();
                            $where = "AND date(computationDate)>=date(now()) - interval " . ($previousMonday+7) . " DAY -interval " . ($v * $_GET['timeCount']) . " $unit  AND date(computationDate)<date(now()) - interval " . $previousMonday . " DAY -interval " . ($v * $_GET['timeCount']) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous week starting Monday");
                            break;
                        case "previous month":
                            $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . ($v + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous month");
                            break;
                        case "previous month - day 1":
                            //$where = "AND date(computationDate)>=date(now())- interval dayofmonth(now()) - 1 day - interval " . ($v / 30 * $_GET['timeCount']) . " MONTH - interval 1 month AND date(computationDate)<date(now())- interval dayofmonth(now()) - 1 day - interval " . ($v / 30 * $_GET['timeCount']) . " MONTH";
                            $where = "AND date(computationDate)>=date(now())- interval dayofmonth(now()) - 1 day - interval " . ($v / 30 * $_GET['timeCount']) . " MONTH - interval 1 month AND date(computationDate)<date(now())- interval dayofmonth(now()) day - interval " . ($v / 30 * $_GET['timeCount']) . " MONTH";
                            $having2 = "";
                            //eventLog("entrato in case previous month - day 1");
                            break;
                        case "same month prev year - day 1":
                            $where = "AND date(computationDate)>=date(now())- interval dayofmonth(now()) - 1 day - interval 1 year - interval " . ($v / 30 * $_GET['timeCount']) . " MONTH AND date(computationDate)<date(now())- interval dayofmonth(now())  day - interval 1 year + interval 1 month - interval " . ($v / 30 * $_GET['timeCount'] ) . " MONTH";
                            $having2 = "";
                            eventLog("entrato in case same month prev year - day 1");
                            break;
                        case "previous 6 months":
                            $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . ($v + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous 6 month");
                            break;
                        case "previous 6 months - day 1 of prev 6 months":
                            //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*2*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . ((6*$v/180) + 6*$_GET['timeCount']) . " MONTH";
                            $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . (((6*2-1)*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . ((5*$v/180) + 6*$_GET['timeCount']) . " MONTH";
                            $having1 = "";
                            break;
                        case "same 6 months of prev year - day 1":
                            //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . (((3*6)*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (6*$_GET['timeCount'] + (2*6*$v/180)) . " MONTH";
                            $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . (((3*6-1)*$v/180) + 6*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (6*$_GET['timeCount'] + (2*6-1*$v/180)) . " MONTH";
                            $having1 = "";
                            break;
                        case "same 6 months of prev year - day 1 month 1 or 7":
                            $monthto1or7 = getMonthto1or7();
                            //$where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*2*$v/180) + 6*$_GET['timeCount'] + $monthto1or7) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . ((6*$v/180) + 6*$_GET['timeCount'] + $monthto1or7) . " MONTH";
                            $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((6*2*$v/180) + 6*$_GET['timeCount'] + $monthto1or7) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . (6*$_GET['timeCount'] + $monthto1or7 + (6*$v/180)) . " MONTH";
                            $having1 = "";
                            break;
                        case "previous year":
                            $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) + ($v * $_GET['timeCount'])) . " $unit AND date(computationDate)<date(now())-interval " . ($v + ($v * $_GET['timeCount'])) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case previous year");
                            break;
                        case "previous year - day 1 of prev year":
                            $where = " AND computationDate>=date(now()) - interval dayofmonth(now()) - 1 day - interval " . ((12*2*$v/365) + 12*$_GET['timeCount']) . " MONTH AND computationDate<date(now())- interval dayofmonth(now()) - 1 day - interval " . ((12*$v/365) + 12*$_GET['timeCount']) . " MONTH";
                            $having1 = "";
                            break;
                        case "previous year - month 1":
                            $where = " AND computationDate>=MAKEDATE(year(now()),1) - interval " . (1+$_GET['timeCount']) . " YEAR AND computationDate<MAKEDATE(year(now()),1) - interval " . (1+$_GET['timeCount']) . " YEAR + interval 1 year";
                            $having1 = "";
                            break;
                        default:
                            $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) - 1) . " $unit AND date(computationDate)<date(now())-interval " . ($v - 1) . " $unit";
                            $having2 = "";
                            //eventLog("entrato in case default");
                            break;
                    }
                } else {

                    if ($unit == 'DAY') {
                        $where = "AND date(computationDate)>=date(now())-interval " . ((2 * $v) - 1) . " $unit AND date(computationDate)<date(now())-interval " . ($v - 1) . " $unit";
                        $having2 = "";
                    } else {
                        $where = "AND computationDate>=now()-interval " . $v . " HOUR - INTERVAL 1 DAY AND computationDate<now()-interval 1 DAY";
                        $having2 = "";
                    }
                }
                array_push($rangedays, array($where, $having2));
            }
        } else {
            $having1 = '';
            array_push($rangedays, array("", $having1));
        }
        //eventLog($where);
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
                        $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(description_short) as descrip FROM Dashboard.Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='" . $idValue . "' AND computationDate>='" . $lowerDateTime . "' AND computationDate<='" . $upperDateTime . "' GROUP BY date(computationDate)$hourMin  $rangedaysValue[1];";
                    } else {
                        $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(description_short) as descrip FROM Dashboard.Data INNER JOIN Descriptions ON Data.IdMetric_data=Descriptions.IdMetric where Data.IdMetric_data='" . $idValue . "' $rangedaysValue[0] GROUP BY date(computationDate)$hourMin  $rangedaysValue[1]";
                    }
                } else {
                    if ($metricType == "personal") {
                        if (isset($_GET['lowerDateTime']) && isset($_GET['upperDateTime'])) {
                            $lowerDateTime = $_GET['lowerDateTime'];
                            $upperDateTime = $_GET['upperDateTime'];
                            $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(shortDesc) as descrip FROM Dashboard.Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data='" . $idValue . "' AND computationDate>='" . $lowerDateTime . "' AND computationDate<='" . $upperDateTime . "' GROUP BY date(computationDate)$hourMin  $rangedaysValue[1];";
                        } else {
                            $sql = "SELECT max(IdMetric_data) as IdMetric_data, max(computationDate) as computationDate, max(value_perc1) as value_perc1, MAX(value_num)as value, max(shortDesc) as descrip FROM Dashboard.Data INNER JOIN NodeRedMetrics ON Data.IdMetric_data=NodeRedMetrics.name where Data.IdMetric_data='" . $idValue . "' $rangedaysValue[0] GROUP BY date(computationDate)$hourMin  $rangedaysValue[1]";
                        }
                    }
                }
                //eventLog($sql);
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
        //eventLog($data_json);
    } else {


        // TO DO MY KPI PROXY !!!
    }
}

function findMonday($d = "", $format = "Y-m-d") {
    if ($d == "")
        $d = date("Y-m-d");
    $delta = date("w", strtotime($d)) - 1;
    if ($delta < 0)
        $delta = 6;
    return date($format, mktime(0, 0, 0, date('m'), date('d') - $delta, date('Y')));
}

function daytoMonday() {
    $i = 0;
    while (date('D', mktime(0, 0, 0, date('m'), date('d') - $i, date('y'))) != "Mon") {
        $i++;
    }
    return $i;
}

function getMonthto1or7() {
// restituisce i mesi passati da gennaio o da luglio
    $month = date('m');
    $i=0;
    if($month <=6){
        $i=$month-1;
    }else{
        $i=$month-7;
    }
    return $i;
}
