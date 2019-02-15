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

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR | E_NOTICE);
//error_reporting(E_ALL);

if(!$link->set_charset("utf8"))
{
    exit();
}

$response = [];
$response2 = [];
$response3 = [];
$count = 0;
$count2 = 0;

if(isset($_REQUEST['date']))
{
    if (isset($_REQUEST['idDash'])) {
        //  if($_REQUEST['apiPwd'] == $notificatorApiPwd)
        //  {
        $idDash = mysqli_real_escape_string($link, $_REQUEST['idDash']);
        $date = mysqli_real_escape_string($link, $_REQUEST['date']);

        $query = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = '$idDash' AND date = '$date';";
        $result = mysqli_query($link, $query);

        if ($result == false) {
            $response['detail'] = "Response Ko";
            $errmsg = mysql_errno() . ' ' . mysql_error();
            $response['message'] = "Errore: ".$errmsg;
        }
        else
        {
            if(mysqli_num_rows($result) > 0)
            {
                $row = $result->fetch_assoc();
                $response['detail'] = "Response Ok";
                $response['nAccessesPerDay'] = $row["nAccessPerDay"];
                $response['nMinutesPerDay'] = $row["nMinutesPerDay"];
            } else {
                $response['detail'] = "No Results Found";
                $response['nAccessesPerDay'] = "0";
                $response['nMinutesPerDay'] = "0";
            }
        }
        echo json_encode($response);
    } else {

        $date = mysqli_real_escape_string($link, $_REQUEST['date']);
        $query2 = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE date = '$date';";
        $result2 = mysqli_query($link, $query2);

        if ($result2 == false) {
            $response2[0]['detail'] = "Response Ko";
            $errmsg2 = mysql_errno() . ' ' . mysql_error();
            $response2[0]['message'] = "Errore: " . $errmsg2;
        } else {
            if (mysqli_num_rows($result2) > 0) {
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $response2[$count]['idDashboard'] = $row2["IdDashboard"];
                    $response2[$count]['nAccessesPerDay'] = $row2["nAccessPerDay"];
                    $response2[$count]['nMinutesPerDay'] = $row2["nMinutesPerDay"];
                    $count++;
                }
            }
        }
        echo json_encode($response2);
    }
} else if (isset($_REQUEST['idDash'])) {
    //  if($_REQUEST['apiPwd'] == $notificatorApiPwd)
    //  {
    $idDash = mysqli_real_escape_string($link, $_REQUEST['idDash']);
    $date = mysqli_real_escape_string($link, $_REQUEST['date']);

    $query3 = "SELECT * FROM Dashboard.IdDashDailyAccess WHERE IdDashboard = '$idDash' ORDER BY date DESC;";
    $result3 = mysqli_query($link, $query3);

    if ($result3 == false) {
        $response3[0]['detail'] = "Response Ko";
        $errmsg3 = mysql_errno() . ' ' . mysql_error();
        $response3[0]['message'] = "Errore: " . $errmsg3;
    } else {
        if (mysqli_num_rows($result3) > 0) {
            while ($row3 = mysqli_fetch_assoc($result3)) {
                $response3[$count2]['Day'] = $row3["date"];
                $response3[$count2]['nAccessesPerDay'] = $row3["nAccessPerDay"];
                $response3[$count2]['nMinutesPerDay'] = $row3["nMinutesPerDay"];
                $count2++;
            }
        }
    }
    echo json_encode($response3);
} else {
    $response['detail'] = "Missing Input Params";
    echo json_encode($response);
}

mysqli_close($link);
