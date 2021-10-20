<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
        $fromQuery = "FROM Dashboard.DashboardWizard WHERE (high_level_type = 'POI' or high_level_type REGEXP 'Model') and organizations LIKE '%" . $organization . "%';";

        $queryNature = "SELECT DISTINCT nature " . $fromQuery;
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

        $querySubNature = "SELECT DISTINCT sub_nature " . $fromQuery;
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
            $response['detail'] = 'subNatureQuery_KO';
        }


    /*    $queryLowLevelType = "SELECT DISTINCT low_level_type FROM Dashboard.DashboardWizard WHERE high_level_type = 'POI' and organizations LIKE '%" . $organization . "%';";
        $rLowLevelType = mysqli_query($link, $queryLowLevelType);

        if($rLowLevelType)
        {
            $resultLowLevelType = [];
            while($rowLowLevelType = mysqli_fetch_assoc($rLowLevelType))
            {
                $lowLevelType = $rowLowLevelType['low_level_type'];
                array_push($resultLowLevelType, $lowLevelType);
            }
            $response['lowLevelType'] = $resultLowLevelType;

        } else {
            $response['detail'] = 'lowLevelTypeQuery_KO';
        }   */


        $queryValueName = "SELECT DISTINCT value_name " . $fromQuery;
        $rValueName = mysqli_query($link, $queryValueName);

        if($rValueName)
        {
            $resultValueName = [];
            while($rowValueName = mysqli_fetch_assoc($rValueName))
            {
                $valueName = $rowValueName['value_name'];
                array_push($resultValueType, $valueName);
            }
            $response['valueName'] = $resultValueName;

        } else {
            $response['detail'] = 'valueNameQuery_KO';
        }


        $queryValueType = "SELECT DISTINCT value_type " . $fromQuery;
        $rValueType = mysqli_query($link, $queryValueType);

        if($rValueType)
        {
            $resultValueType = [];
            while($rowValueType = mysqli_fetch_assoc($rValueType))
            {
                $valueType = $rowValueType['value_type'];
                array_push($resultValueType, $valueType);
            }
            $response['valueType'] = $resultValueType;

        } else {
            $response['detail'] = 'valueTypeQuery_KO';
        }


        $queryBroker = "SELECT DISTINCT broker_name " . $fromQuery;
        $rBroker = mysqli_query($link, $queryBroker);

        if($rBroker)
        {
            $resultBroker = [];
            while($rowBroker = mysqli_fetch_assoc($rBroker))
            {
                $broker = $rowBroker['broker_name'];
                array_push($resultBroker, $broker);
            }
            $response['broker_name'] = $resultBroker;

        } else {
            $response['detail'] = 'brokerQuery_KO';
        }


        $queryUnit= "SELECT DISTINCT unit " . $fromQuery;
        $rUnit = mysqli_query($link, $queryUnit);

        if($rUnit)
        {
            $resultUnit = [];
            while($rowUnit = mysqli_fetch_assoc($rUnit))
            {
                $unit = $rowUnit['unit'];
                array_push($resultUnit, $unit);
            }
            $response['unit'] = $resultUnit;

        } else {
            $response['detail'] = 'unitQuery_KO';
        }


        $queryHealthiness= "SELECT DISTINCT healthiness " . $fromQuery;
        $rHealthiness = mysqli_query($link, $queryHealthiness);

        if($rHealthiness)
        {
            $resultHealthiness = [];
            while($rowHealthiness = mysqli_fetch_assoc($rHealthiness))
            {
                $healthiness = $rowHealthiness['healthiness'];
                array_push($resultHealthiness, $healthiness);
            }
            $response['healthiness'] = $resultHealthiness;

        } else {
            $response['detail'] = 'healthinessQuery_KO';
        }


        $queryOwnership = "SELECT DISTINCT ownership " . $fromQuery;
        $rOwnership = mysqli_query($link, $queryOwnership);

        if($rOwnership)
        {
            $resultOwnership = [];
            while($rowOwnership = mysqli_fetch_assoc($rOwnership))
            {
                $ownership = $rowOwnership['ownership'];
                array_push($resultOwnership, $ownership);
            }
            $response['ownership'] = $resultOwnership;

        } else {
            $response['detail'] = 'ownershipQuery_KO';
        }

        $queryValueUnit = "SELECT DISTINCT value_unit " . $fromQuery;
        $rValueUnit = mysqli_query($link, $queryValueUnit);

        if($rValueUnit)
        {
            $resultValueUnit = [];
            while($rowValueUnit = mysqli_fetch_assoc($rValueUnit))
            {
                $valueUnit = $rowValueUnit['ownership'];
                array_push($resultValueUnit, $valueUnit);
            }
            $response['value_unit'] = $resultValueUnit;

        } else {
            $response['detail'] = 'valueUnitQuery_KO';
        }


     //   $response['detail'] = 'OK_Nature_AND_SubNature';
        $response['detail'] = 'All Queries OK!';

    } else {
        $response['detail'] = 'KO_User_Not_Auth';
    }

    echo json_encode($response);
}