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
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('../config.php');
include('../TourRepository.php');
//session_start();

checkSession('Public');

$tourRepo = new TourRepository($host, $username, $password, $dbname);
?>

<!DOCTYPE html>
<html class="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Snap4City Scenarios Examples</title>
    
    <script type="text/javascript">
       const setTheme = (theme) => {
       document.documentElement.className = theme;
       localStorage.setItem('theme', theme);
       }
       const getTheme = () => {
       const theme = localStorage.getItem('theme');
       theme && setTheme(theme);
       }
       getTheme();
    </script>

    <!-- Bootstrap Core CSS -->
      <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
      <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Font awesome icons -->
     <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
   
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">

    <!-- Custom CSS -->
     <link href="../css/s4c-css/s4c-dashboard.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
     <link href="../css/s4c-css/s4c-iotApplications.css?v=a" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/css/shepherd.min.css">
    <!-- <link rel="stylesheet" href="../css/shepherd.min.css"> -->
    <link href="../css/s4c-css/s4c-snapTour.css" rel="stylesheet">
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

        <div class="mainContainer">
         <div class="menuFooter-container">
           <?php include "mainMenu.php" ?>
           <?php include "footer.php" ?>
         </div>
            <div class="col-xs-12 col-md-10" id="mainCnt">
                <!-- MOBILE MENU -->
                  <!-- <div class="row hidden-md hidden-lg">
                      <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                          <?php include "mobMainMenuClaim.php" ?>
                      </div>
                  </div> -->
                <div class="row header-container">
                   <div id="mobLogo"><?php include "logoS4cSVG.php"; ?></div>
                    <div id="headerTitleCnt">
                        Snap4City Scenarios Examples
                    </div>
                    <div class="user-menu-container">
                      <?php include "loginPanel.php" ?>
                    </div>
                    <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt">
                        <?php include "mobMainMenu.php" ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="mainContentCnt">
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

<?php } else {
    include('../s4c-legacy-management/scenario_examples.php');
}
?>