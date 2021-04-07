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
include('../config.php');
include('../TourRepository.php');
session_start();

checkSession('Public');

$tourRepo = new TourRepository($host, $username, $password, $dbname);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Snap4City Scenarios Examples</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../css/dashboardList.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="../css/dashboardView.css?v=<?= time(); ?>" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/css/shepherd.min.css">
    <!-- <link rel="stylesheet" href="../css/shepherd.min.css"> -->
    <link href="../css/snapTour.css" rel="stylesheet">
    <style type="text/css">
        table {
            margin-top: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body class="guiPageBody">
    <div class="container-fluid">
        <?php include "sessionExpiringPopup.php" ?>

        <div class="row mainRow">
            <?php include "mainMenu.php" ?>
            <div class="col-xs-12 col-md-10" id="mainCnt">
                <div class="row hidden-md hidden-lg">
                    <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                        <?php include "mobMainMenuClaim.php" ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">
                        Snap4City Scenarios Examples
                    </div>
                    <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt">
                        <?php include "mobMainMenu.php" ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="mainContentCnt" style='background-color: rgba(138, 159, 168, 1)'>
                        <div class="row">
                            <table class="col-xs-10 col-xs-offset-1" border="0">
                                <tbody>
                                    <tr>
                                        <td data-step-id="scenario-control-room"><p><a href="https://www.snap4city.org/drupal/node/531" target="_blank"><img src="../img/scenario_examples/01scenario_res.png" width="100%" alt="" /></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="scenario-traffic-flow">
                                            <p><a href="https://www.snap4city.org/drupal/node/533" target="_blank"><img src="../img/scenario_examples/02scenario_res.png" width="100%" alt=""></a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="scenario-people-control"><a href="https://www.snap4city.org/drupal/node/518" target="_blank"><img src="../img/scenario_examples/03scenario_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="scenario-snap4altair"><a href="https://www.snap4city.org/drupal/node/546" target="_blank"><img src="../img/scenario_examples/04scenario_res.png" width="100%" alt="" align="right"></a></td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="scenario-env-and-quality"><a href="https://www.snap4city.org/drupal/node/530" target="_blank"><img src="../img/scenario_examples/05scenario_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="scenario-smart-parking"><a href="https://www.snap4city.org/drupal/node/712" target="_blank"><img src="../img/scenario_examples/06scenario_res.png" width="100%" alt="" align="right"></a></td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="scenario-smart-light"><a href="https://www.snap4city.org/drupal/node/713" target="_blank"><img src="../img/scenario_examples/07scenario_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="scenario-city-vs-industry"><a href="https://www.snap4city.org/drupal/node/547" target="_blank"><img src="../img/scenario_examples/08scenario_res.png" width="100%" alt="" align="right"></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../js/common_func.js"></script>
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/js/shepherd.min.js"></script>
<!-- <script src="../js/shepherd.min.js"></script> -->
<script src="../js/snapTour.js"></script>
<script>
    $(function() {
        initSessionExpiringPopup("<?= $_SESSION['sessionEndTime'] ?>");
        initPageContent();
        initMenu(<?= escapeForJS(sanitizeGetString('linkId')) ?>);
    });

    $(function() {
        const steps = JSON.parse('<?= serializeToJsonString($tourRepo->getTourSteps("preRegisterTour")) ?>');
        const session = JSON.parse('<?= serializeToJsonString($_SESSION) ?>');
        SnapTour.init(steps, {
            isPublic: session.isPublic,
        //    resetTimeout: 1000 * 60 * 60 * 12 // 12 hour as ms. if left blank the default is 24h
            resetTimeout: 1000 * 60 * 5
        });
    });
</script>

</html>