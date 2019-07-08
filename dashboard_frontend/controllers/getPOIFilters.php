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

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername']) || $_SESSION['isPublic'] === true)
{

    if(isset($_SESSION['refreshToken']))
    {
        $organization = escapeForSQL($_GET['org'], $link);

        $queryNature = "SELECT DISTINCT nature FROM Dashboard.DashboardWizard WHERE high_level_type = 'POI' and organizations LIKE '%" . $organization . "%';";
        $rNature = mysqli_query($link, $queryNature);

        if($rNature)
        {
            $resultNature = [];
            while($rowNature = mysqli_fetch_assoc($rNature))
            {
                $nature = $rowNature['nature'];
                array_push($resultNature, $nature);
            }
            $response['nature'] = $resultNature;

        } else {
            $response['detail'] = 'natureQuery_KO';
        }

        $querySubNature = "SELECT DISTINCT sub_nature FROM Dashboard.DashboardWizard WHERE high_level_type = 'POI' and organizations LIKE '%" . $organization . "%';";
        $rSubNature = mysqli_query($link, $querySubNature);

        if($rSubNature)
        {
            $resultSubNature = [];
            while($rowSubNature = mysqli_fetch_assoc($rSubNature))
            {
                $sub_nature = $rowSubNature['sub_nature'];
                array_push($resultSubNature, $sub_nature);
            }
            $response['sub_nature'] = $resultSubNature;

        } else {
            $response['detail'] = 'natureQuery_KO';
        }

        $response['detail'] = 'OK_Nature_AND_SubNature';

    } else {
        $response['detail'] = 'KO_User_Not_Auth';
    }

    echo json_encode($response);
}