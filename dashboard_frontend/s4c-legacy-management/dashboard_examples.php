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

    <title>Snap4City Dashboards Examples</title>

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
   <?php include "../cookie_banner/cookie-banner.php"; ?>
    <div class="container-fluid">
        <?php include "sessionExpiringPopup.php" ?>

        <div class="row mainRow">
            <?php include "../s4c-legacy-management/mainMenu.php" ?>
            <div class="col-xs-12 col-md-10" id="mainCnt">
                <div class="row hidden-md hidden-lg">
                    <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                        <?php include "mobMainMenuClaim.php" ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">
                        Snap4City Dashboards Examples
                    </div>
                    <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt">
                        <?php include "../s4c-legacy-management/mobMainMenu.php" ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="mainContentCnt" style='background-color: rgba(138, 159, 168, 1)'>
                        <div class="row">
                            <table border="0" class="col-xs-10 col-xs-offset-1">
                                <tbody>
                                    <tr>
                                        <td data-step-id="dashboard-control-room"><img src="../img/dashboard_examples/01_control-room_res.png" width="100%" alt="" /></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="dashboard-roma-air-quality">
                                            <p><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=MjcyNg==" target="_blank"><img src="../img/dashboard_examples/03_roma-air-quality_res.png" width="100%" alt=""></a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="dashboard-traffic-flow"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=MjY1MQ==" target="_blank"><img src="../img/dashboard_examples/02_traffic-flow_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="dashboard-3d"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=MjM2MA==" target="_blank"><img src="../img/dashboard_examples/04_3d_res.png" width="100%" alt="" align="right"></a></td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="dashboard-whatif"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=MjE5MA==" target="_blank"><img src="../img/dashboard_examples/05_whatif_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="dashboard-alerting"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=MzA0OQ==" target="_blank"><img src="../img/dashboard_examples/06_alerting_res.png" width="100%" alt="" align="right"></a></td>
                                    </tr>
                                    <tr>
                                        <td data-step-id="dashboard-smartlight-parking"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=Mjc2Mg==" target="_blank"><img src="../img/dashboard_examples/07_smartlight_parking_home_res.png" width="100%" alt="" align="right"></a></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td data-step-id="dashboard-custom-widgets"><a href="https://www.snap4city.org/dashboardSmartCity/view/index.php?iddasboard=Mjk0NQ==#" target="_blank"><img src="../img/dashboard_examples/08_custom_widgets_res.png" width="100%" alt="" align="right"></a></td>
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
         //   resetTimeout: 1000 * 60 * 60 * 12 // 12 hour as ms. if left blank the default is 24h
            resetTimeout: 1000 * 60 * 5
        });
    });
</script>

</html>