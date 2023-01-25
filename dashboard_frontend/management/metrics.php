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
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

    include('../config.php');
    include('process-form.php');
    //session_start();
    
    checkSession('ToolAdmin');

?>

<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>
        
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

        
        <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>

        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="../js/bootstrap.min.js"></script>

        <!-- Custom Core JavaScript -->
        <script src="../js/bootstrap-colorpicker.min.js"></script>

        <!-- Bootstrap toggle button -->
       <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
       <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

       <!-- Bootstrap table -->
       <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
       <script src="../boostrapTable/dist/bootstrap-table.js"></script>
       <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
       <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

       <!-- Font awesome icons -->
         <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
       
         <!-- Custom CSS -->
         <link href="../css/s4c-css/s4c-dashboard.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
         <link href="../css/s4c-css/s4c-dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
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
                        <div id="headerTitleCnt">Metrics</div>
                        <div class="user-menu-container">
                          <?php include "loginPanel.php" ?>
                        </div>
                        <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.Descriptions";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $dashboardsQt = $row['qt'];
                                               echo $row['qt'];
                                            }
                                            else
                                            {
                                                $dashboardsQt = "-";
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        metrics
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.Descriptions WHERE status = 'Attivo'";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $dashboardsActiveQt = $row['qt'];
                                               echo $row['qt'];
                                            }
                                            else
                                            {
                                                $dashboardsActiveQt = "-";
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        active
                                    </div>
                                </div>
                            </div>
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="list_metrics" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modale di creazione di una metrica-->
        <div class="modal fade" id="modalAddMetric" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add new metric
                </div>
                <form id="addMetricForm" name="addMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">  
                <div id="addMetricModalBody" class="modal-body modalBody">
                    <ul id="addMetricModalTabs" class="nav nav-tabs nav-justified">
                        <li id="addMetricGeneralTabBtn" class="active"><a data-toggle="tab" href="#addMetricGeneralTab">General</a></li>
                        <li id="addMetricQueryTabBtn"><a data-toggle="tab" href="#addMetricQueryTab">Datasources & queries</a></li>
                    </ul>
                  <div class="modal_wrapper">
                    <div class="tab-content">
                        <!-- General tab -->
                        <div id="addMetricGeneralTab" class="tab-pane fade in active">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" id="metricName" name="metricName" class="modalInputTxt" required>
                                    </div>
                                    <div class="modalFieldLabelCnt">Metric name</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="shortDescription" id="shortDescription" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Short description</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="4" name="fullDescription" id="fullDescription" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Full description</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="resultType" id="resultType">
                                            <option value="Testuale">Text</option>
                                            <option value="Percentuale">Percent</option>
                                            <option value="Series">Table</option>
                                            <option value="Intero">Integer</option>
                                            <option value="Float">Float</option>
                                            <option value="Percentuale/285">Percent/285</option>
                                            <option value="Percentuale/83">Percent/83</option>
                                            <option value="Percentuale/757">Percent/757</option>
                                            <option value="isAlive">Web server status</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Result type</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="hidden" name="updateFrequency" id="updateFrequency" value="60000" /> <!-- class="modalInputTxt" -->
                                        <div id="updateFrequencyHourContainer" class="input-group spinner">
                                            <input name="updateFrequencyHour" id="updateFrequencyHour" value="0 h" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencyHourUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencyHourDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                        <div id="updateFrequencyMinContainer" class="input-group spinner">
                                            <input name="updateFrequencyMin" id="updateFrequencyMin" value="1 m" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencyMinUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencyMinDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                        <div id="updateFrequencySecContainer" class="input-group spinner">
                                            <input name="updateFrequencySec" id="updateFrequencySec" value="0 s" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencySecUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencySecDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Update frequency</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSourceType" id="dataSourceType">
                                            <option value="none">Not specified</option>
                                            <option value="SPARQL">Sparql</option>
                                            <option value="SQL">SQL</option>
                                            <option value="isAlive">Web server (status)</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source type</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="hasNegativeValues" id="hasNegativeValues">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Negative values</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <!--<input type="text" class="modalInputTxt" name="process" id="process" required> -->
                                        <select class="modalInputTxt" name="process" id="process">
                                            <option value="DashboardProcess">Main process</option>
                                            <option value="HttpProcess">Web servers tester</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Ingestion agent</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <!--<input type="text" class="modalInputTxt" name="sameDataAlarmCount" id="sameDataAlarmCount" value="5" required>-->
                                        <div class="input-group spinner">
                                            <input name="sameDataAlarmCount" id="sameDataAlarmCount" type="text" class="form-control" value="Not active" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="sameDataAlarmCountUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="sameDataAlarmCountDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Same data alarm count</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="storingData" id="storingData">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Storing data</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="modalFieldCnt">
                                            <input type="hidden" name="oldDataEvalTime" id="oldDataEvalTime" />
                                            <div id="oldDataEvalTimeHourContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeHour" id="oldDataEvalTimeHour" value="0 h" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeHourUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeHourDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                            <div id="oldDataEvalTimeMinContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeMin" id="oldDataEvalTimeMin" value="0 m" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeMinUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeMinDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                            <div id="oldDataEvalTimeSecContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeSec" id="oldDataEvalTimeSec" value="Not active" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeSecUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeSecDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Old data evaluation time</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="processType" id="processType">
                                            <option value="JVNum1">Numeric</option>
                                            <option value="JVPerc">Percent</option>
                                            <option value="JVTable">Matrix</option>
                                            <option value="API">API</option>
                                            <option value="JVRidesAtaf">Ataf rides (specific)</option>
                                            <option value="JVSceOnNodes">Sce on nodes (specific)</option>
                                            <option value="jVPark">Parks (specific)</option>
                                            <option value="JVWifiOp">Wifi operative (specific)</option>
                                            <option value="JVSmartDs">Smart Ds (specific)</option>
                                            <option value="JVTwRet">Tweets/Retweets (specific)</option>
                                            <option value="none">Not defined</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Ingestion agent method</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="timeRange" id="timeRange">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Time range</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="cityContext" id="cityContext">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">City context</div>
                                </div>
                            </div>
                        </div>

                        <!-- Query tab / Server tab-->
                        <div id="addMetricQueryTab" class="tab-pane fade">
                            <div id="addMetricQueryTabQueryRow" class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSource" id="dataSource">
                                            <option value="none">None</option>
                                            <?php 
                                                $link = mysqli_connect($host, $username, $password);
                                                mysqli_select_db($link, $dbname);

                                                $q1 = "SELECT Id FROM Dashboard.DataSource";
                                                $r1 = mysqli_query($link, $q1);

                                                if($r1)
                                                {
                                                    while($row = $r1->fetch_assoc())
                                                    {
                                                        echo '<option value="' . $row['Id'] . '">' . $row['Id'] . '</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source 1</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSource2" id="dataSource2">
                                            <option value="none">None</option>
                                            <?php 
                                                $link = mysqli_connect($host, $username, $password);
                                                mysqli_select_db($link, $dbname);

                                                $q1 = "SELECT Id FROM Dashboard.DataSource";
                                                $r1 = mysqli_query($link, $q1);

                                                if($r1)
                                                {
                                                    while($row = $r1->fetch_assoc())
                                                    {
                                                        echo '<option value="' . $row['Id'] . '">' . $row['Id'] . '</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source 2</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="dataSourceDescription" id="dataSourceDescription" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source(s) description</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="dataArea" id="dataArea" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Data area(s) description</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="8" name="query" id="query" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Query 1</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="8" name="query2" id="query2" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Query 2</div>
                                </div>
                            </div>
                            
                            <div id="addMetricQueryTabServerRow" class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="serverTestHttpMethod" id="serverTestHttpMethod">
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                            <option value="PUT">PUT</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">HTTP call method</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="serverTestToken" id="serverTestToken" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Token</div>
                                </div>
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="serverTestUrl" id="serverTestUrl" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">URL</div>
                                </div>
                            </div>   
                        </div>
                    </div>
                    
                    <div class="row" id="addMetricLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Adding metric, please wait</div>
                    </div>
                    <div class="row" id="addMetricLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="addMetricOkMsg">
                        <div class="col-xs-12 centerWithFlex">Metric added successfully</div>
                    </div>
                    <div class="row" id="addMetricOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="addMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error adding metric</div>
                    </div>
                    <div class="row" id="addMetricKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                </div>
                <div id="addMetricModalFooter" class="modal-footer">
                  <button type="button" id="addMetricCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="addMetricConfirmBtn" name="addMetricConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
                </form>    
              </div>
            </div>
        </div>

        <!-- Modal di modifica di una metrica-->
        <div class="modal fade" id="modalEditMetric" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Update metric
                </div>
                <form id="editMetricForm" name="editMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                <input type="hidden" id="metricId" name="metricId" />  
                <div id="editMetricModalBody" class="modal-body modalBody">
                    <ul id="editMetricModalTabs" class="nav nav-tabs nav-justified">
                        <li id="editMetricGeneralTabBtn" class="active"><a data-toggle="tab" href="#editMetricGeneralTab">General</a></li>
                        <li id="editMetricQueryTabBtn"><a data-toggle="tab" href="#editMetricQueryTab">Datasources & queries</a></li>
                    </ul>
                    <div class="modal_wrapper">
                    <div class="tab-content">
                        <div id="editMetricGeneralTab" class="tab-pane fade in active">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" id="metricNameM" name="metricNameM" class="modalInputTxt" required>
                                    </div>
                                    <div class="modalFieldLabelCnt">Metric name</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="shortDescriptionM" id="shortDescriptionM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Short description</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="4" name="fullDescriptionM" id="fullDescriptionM" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Full description</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="resultTypeM" id="resultTypeM">
                                            <option value="Testuale">Text</option>
                                            <option value="Percentuale">Percent</option>
                                            <option value="Series">Table</option>
                                            <option value="Intero">Integer</option>
                                            <option value="Float">Float</option>
                                            <option value="Percentuale/285">Percent/285</option>
                                            <option value="Percentuale/83">Percent/83</option>
                                            <option value="Percentuale/757">Percent/757</option>
                                            <option value="isAlive">Web server status</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Result type</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="hidden" name="updateFrequencyM" id="updateFrequencyM" />
                                        <div id="updateFrequencyHourContainer" class="input-group spinner">
                                            <input name="updateFrequencyHourM" id="updateFrequencyHourM" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencyHourMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencyHourMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                        <div id="updateFrequencyMinContainer" class="input-group spinner">
                                            <input name="updateFrequencyMinM" id="updateFrequencyMinM" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencyMinMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencyMinMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                        <div id="updateFrequencySecContainer" class="input-group spinner">
                                            <input name="updateFrequencySecM" id="updateFrequencySecM" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="updateFrequencySecMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="updateFrequencySecMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Update frequency</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSourceTypeM" id="dataSourceTypeM">
                                            <option value="none">Not specified</option>
                                            <option value="SPARQL">Sparql</option>
                                            <option value="SQL">SQL</option>
                                            <option value="isAlive">Web server (status)</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source type</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="hasNegativeValuesM" id="hasNegativeValuesM">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Negative values</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="processM" id="processM">
                                            <option value="DashboardProcess">Main process</option>
                                            <option value="HttpProcess">Web servers tester</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Ingestion agent</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group spinner">
                                            <input name="sameDataAlarmCountM" id="sameDataAlarmCountM" type="text" class="form-control" readonly="true">
                                            <div class="input-group-btn-vertical">
                                              <button id="sameDataAlarmCountMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                              <button id="sameDataAlarmCountMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Same data alarm count</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="storingDataM" id="storingDataM">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Storing data</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="modalFieldCnt">
                                            <input type="hidden" name="oldDataEvalTimeM" id="oldDataEvalTimeM" />
                                            <div id="oldDataEvalTimeHourContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeHourM" id="oldDataEvalTimeHourM" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeHourMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeHourMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                            <div id="oldDataEvalTimeMinContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeMinM" id="oldDataEvalTimeMinM" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeMinMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeMinMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                            <div id="oldDataEvalTimeSecContainer" class="input-group spinner">
                                                <input name="oldDataEvalTimeSecM" id="oldDataEvalTimeSecM" type="text" class="form-control" readonly="true">
                                                <div class="input-group-btn-vertical">
                                                  <button id="oldDataEvalTimeSecMUp" class="btn btn-default" type="button"><i class="fa fa-caret-up"></i></button>
                                                  <button id="oldDataEvalTimeSecMDown" class="btn btn-default" type="button"><i class="fa fa-caret-down"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Old data evaluation time</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="processTypeM" id="processTypeM">
                                            
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Ingestion agent method</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="timeRangeM" id="timeRangeM">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Time range</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="cityContextM" id="cityContextM">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">City context</div>
                                </div>
                            </div>
                        </div>    
                        <div id="editMetricQueryTab" class="tab-pane fade in">
                            <div class="row" id="editMetricQueryTabQueryRow">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSourceM" id="dataSourceM">
                                            <option value="none">None</option>
                                            <?php 
                                                $link = mysqli_connect($host, $username, $password);
                                                mysqli_select_db($link, $dbname);

                                                $q1 = "SELECT Id FROM Dashboard.DataSource";
                                                $r1 = mysqli_query($link, $q1);

                                                if($r1)
                                                {
                                                    while($row = $r1->fetch_assoc())
                                                    {
                                                        echo '<option value="' . $row['Id'] . '">' . $row['Id'] . '</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source 1</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSource2M" id="dataSource2M">
                                            <option value="none">None</option>
                                            <?php 
                                                $link = mysqli_connect($host, $username, $password);
                                                mysqli_select_db($link, $dbname);

                                                $q1 = "SELECT Id FROM Dashboard.DataSource";
                                                $r1 = mysqli_query($link, $q1);

                                                if($r1)
                                                {
                                                    while($row = $r1->fetch_assoc())
                                                    {
                                                        echo '<option value="' . $row['Id'] . '">' . $row['Id'] . '</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source 2</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="dataSourceDescriptionM" id="dataSourceDescriptionM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Data source(s) description</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="dataAreaM" id="dataAreaM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Data area(s) description</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="8" name="queryM" id="queryM" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Query 1</div>
                                </div>
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <textarea class="modalInputTxtArea" rows="8" name="query2M" id="query2M" required></textarea> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Query 2</div>
                                </div>
                            </div>
                            <div id="editMetricQueryTabServerRow" class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="serverTestHttpMethodM" id="serverTestHttpMethodM">
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                            <option value="PUT">PUT</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">HTTP call method</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="serverTestTokenM" id="serverTestTokenM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Token</div>
                                </div>
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="serverTestUrlM" id="serverTestUrlM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">URL</div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="row" id="editMetricLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Updating metric, please wait</div>
                    </div>
                    <div class="row" id="editMetricLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="editMetricOkMsg">
                        <div class="col-xs-12 centerWithFlex">Metric updated successfully</div>
                    </div>
                    <div class="row" id="editMetricOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="editMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error updating metric</div>
                    </div>
                    <div class="row" id="editMetricKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                </div>
                <div id="editMetricModalFooter" class="modal-footer">
                  <button type="button" id="editMetricCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="editMetricConfirmBtn" name="editMetricConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
                </form>    
              </div>
            </div>
        </div>
        
        <!-- Modal di conferma cancellazione  metrica-->
        <div class="modal fade" id="modalDelMetric" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Delete metric
            </div>
            <form id="delMetricForm" name="delMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator"> 
                <input type="hidden" id="metricIdToDel" name="metricIdToDel" />
                <input type="hidden" id="metricToDelActive" name="metricToDelActive" />
                <div class="modal_wrapper">
                <div id="delMetricModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div id="delMetricNameMsg" class="col-xs-12 modalCell">
                            <div class="modalDelMsg col-xs-12 centerWithFlex">
                                Do you want to confirm cancellation of the following metric?
                            </div>
                            <div id="metricNameToDel"  class="modalDelObjName col-xs-12 centerWithFlex"></div> 
                        </div>
                    </div>
                    <div class="row" id="delMetricOkMsg">
                        <div class="col-xs-12 centerWithFlex" id="succesMsg">Metric deleted successfully</div>
                    </div>
                    <div class="row" id="delMetricOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="delMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex" id="errorMsg">Error deleting metric</div>
                    </div>
                    <div class="row" id="delMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex" id="errorIcon"><div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div></div>
                    </div>
                </div>
            </div>
                <div id="delMetricModalFooter" class="modal-footer">
                  <button type="button" id="delMetricCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="delMetricBtn" name="delMetricBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
            </form>
          </div>
        </div>
    </div>
        
    </body>
</html>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        var increaseTimeout, increaseInterval = null;
        var spinnerHoldValue = 1000;
        var spinnerIntervalValue = 75;
        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        
        setInterval(function(){
            var now = parseInt(new Date().getTime() / 1000);
            var difference = sessionEndTime - now;
            
            if(difference === 300)
            {
                $('#sessionExpiringPopupTime').html("5 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if(difference === 120)
            {
                $('#sessionExpiringPopupTime').html("2 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if((difference > 0)&&(difference <= 60))
            {
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                $('#sessionExpiringPopupTime').html(difference + " seconds");
            }
            
            if(difference <= 0)
            {
                location.href = "logout.php?sessionExpired=true";
            }
        }, 1000);
        
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            
            if($(window).width() < 992)
            {
                $('#list_metrics').bootstrapTable('hideColumn', 'description_short');
                $('#list_metrics').bootstrapTable('hideColumn', 'metricType');
                $('#list_metrics').bootstrapTable('hideColumn', 'source');
                $('#list_metrics').bootstrapTable('hideColumn', 'dataSource');
                $('#list_metrics').bootstrapTable('hideColumn', 'status');
            }
            else
            {
                $('#list_metrics').bootstrapTable('showColumn', 'description_short');
                $('#list_metrics').bootstrapTable('showColumn', 'metricType');
                $('#list_metrics').bootstrapTable('showColumn', 'source');
                $('#list_metrics').bootstrapTable('showColumn', 'dataSource');
                $('#list_metrics').bootstrapTable('showColumn', 'status');
            }
        });
        
        $('#mainMenuCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }
        
        function resetAddMetricModal()
        {
            $('#addMetricQueryTabBtn a').html("Datasources & queries");
            $('#modalAddMetric #metricName').val("");
            $('#modalAddMetric #shortDescription').val("");
            $('#modalAddMetric #fullDescription').val("");
            $('#modalAddMetric #resultType').val("Testuale");
            $('#modalAddMetric #updateFrequencyHour').val("0 h");
            $('#modalAddMetric #updateFrequencyMin').val("1 m");
            $('#modalAddMetric #updateFrequencySec').val("0 s");
            $('#modalAddMetric #updateFrequency').val("60000");
            $('#modalAddMetric #updateFrequencyHourContainer').show();
            $('#modalAddMetric #updateFrequencyMinContainer').show();
            $('#modalAddMetric #updateFrequencySecContainer').show();
            $('#modalAddMetric #updateFrequencySecContainer').css("width", "33%");
            $('#modalAddMetric #dataSourceType').val("none");
            $('#modalAddMetric #hasNegativeValues').val("1");
            $('#modalAddMetric #process').val("DashboardProcess");
            $('#modalAddMetric #sameDataAlarmCount').val("Not active");
            $('#modalAddMetric #storingData').val("1");
            $('#modalAddMetric #oldDataEvalTimeHour').val("0 h");
            $('#modalAddMetric #oldDataEvalTimeMin').val("0 m");
            $('#modalAddMetric #oldDataEvalTimeSec').val("Not active");
            $('#modalAddMetric #oldDataEvalTime').val("Not active");
            $('#modalAddMetric #oldDataEvalTimeHourContainer').hide();
            $('#modalAddMetric #oldDataEvalTimeMinContainer').hide();
            $('#modalAddMetric #oldDataEvalTimeSecContainer').show();
            $('#modalAddMetric #oldDataEvalTimeSecContainer').css("width", "100%");
            $('#modalAddMetric #processType').empty();
            $('#modalAddMetric #processType').append('<option value="JVNum1">Numeric</option>');
            $('#modalAddMetric #processType').append('<option value="JVPerc">Percent</option>');
            $('#modalAddMetric #processType').append('<option value="JVTable">Table</option>');
            $('#modalAddMetric #processType').append('<option value="API">API</option>');
            $('#modalAddMetric #processType').append('<option value="JVRidesAtaf">ATAF rides (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="JVSceOnNodes">Sce on nodes (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="jVPark">Parkings (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="JVWifiOp">Wifi operatives (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="JVSmartDs">SmartDs (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="JVTwRet">Tweets/Retweets (specific)</option>');
            $('#modalAddMetric #processType').append('<option value="none">Not defined</option>');
            $('#modalAddMetric #processType').val("JVNum1");
            $('#modalAddMetric #timeRange').val("0");
            $('#modalAddMetric #cityContext').val("0");
            $('#modalAddMetric #addMetricQueryTabQueryRow').show();
            $('#modalAddMetric #addMetricQueryTabServerRow').hide();
            $('#modalAddMetric #dataSource').val("none");
            $('#modalAddMetric #dataSource2').val("none");
            $('#modalAddMetric #dataSourceDescription').val("");
            $('#modalAddMetric #dataArea').val("");
            $('#modalAddMetric #query').val("");
            $('#modalAddMetric #query2').val("");
            $('#modalAddMetric #serverTestHttpMethod').val("GET");
            $('#modalAddMetric #serverTestToken').val("");
            $('#modalAddMetric #serverTestUrl').val("");
            $('#addMetricGeneralTabBtn a').trigger('click');
            $('#addMetricLoadingMsg').hide();
            $('#addMetricLoadingIcon').hide();
            $('#addMetricOkMsg').hide();
            $('#addMetricOkIcon').hide();
            $('#addMetricKoMsg').hide();
            $('#addMetricKoIcon').hide();
        }
        
        function resetEditMetricModal()
        {
            console.log("Form edit resettato");
            $('#editMetricQueryTabBtn a').html("Datasources & queries");
            $('#modalEditMetric #metricNameM').val("");
            $('#modalEditMetric #shortDescriptionM').val("");
            $('#modalEditMetric #fullDescriptionM').val("");
            $('#modalEditMetric #resultTypeM').val("Testuale");
            $('#modalEditMetric #updateFrequencyHourM').val("0 h");
            $('#modalEditMetric #updateFrequencyMinM').val("1 m");
            $('#modalEditMetric #updateFrequencySecM').val("0 s");
            $('#modalEditMetric #updateFrequencyM').val("60000");
            $('#modalEditMetric #updateFrequencyHourContainer').show();
            $('#modalEditMetric #updateFrequencyMinContainer').show();
            $('#modalEditMetric #updateFrequencySecContainer').show();
            $('#modalEditMetric #updateFrequencySecContainer').css("width", "33%");
            $('#modalEditMetric #dataSourceTypeM').val("none");
            $('#modalEditMetric #hasNegativeValuesM').val("1");
            $('#modalEditMetric #processM').val("DashboardProcess");
            $('#modalEditMetric #sameDataAlarmCountM').val("Not active");
            $('#modalEditMetric #storingDataM').val("1");
            $('#modalEditMetric #oldDataEvalTimeHourM').val("0 h");
            $('#modalEditMetric #oldDataEvalTimeMinM').val("0 m");
            $('#modalEditMetric #oldDataEvalTimeSecM').val("Not active");
            $('#modalEditMetric #oldDataEvalTimeM').val("Not active");
            $('#modalEditMetric #oldDataEvalTimeHourContainer').hide();
            $('#modalEditMetric #oldDataEvalTimeMinContainer').hide();
            $('#modalEditMetric #oldDataEvalTimeSecContainer').show();
            $('#modalEditMetric #oldDataEvalTimeSecContainer').css("width", "100%");
            $('#modalEditMetric #processTypeM').empty();
            $('#modalEditMetric #processTypeM').append('<option value="JVNum1">Numeric</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVPerc">Percent</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVTable">Table</option>');
            $('#modalEditMetric #processTypeM').append('<option value="API">API</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVRidesAtaf">ATAF rides (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVSceOnNodes">Sce on nodes (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="jVPark">Parkings (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVWifiOp">Wifi operatives (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVSmartDs">SmartDs (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="JVTwRet">Tweets/Retweets (specific)</option>');
            $('#modalEditMetric #processTypeM').append('<option value="none">Not defined</option>');
            $('#modalEditMetric #processTypeM').val("JVNum1");
            $('#modalEditMetric #timeRangeM').val("0");
            $('#modalEditMetric #cityContextM').val("0");
            $('#modalEditMetric #editMetricQueryTabQueryRow').show();
            $('#modalEditMetric #editMetricQueryTabServerRow').hide();
            $('#modalEditMetric #dataSourceM').val("none");
            $('#modalEditMetric #dataSource2M').val("none");
            $('#modalEditMetric #dataSourceDescriptionM').val("");
            $('#modalEditMetric #dataAreaM').val("");
            $('#modalEditMetric #queryM').val("");
            $('#modalEditMetric #query2M').val("");
            $('#modalEditMetric #serverTestHttpMethodM').val("GET");
            $('#modalEditMetric #serverTestTokenM').val("");
            $('#modalEditMetric #serverTestUrlM').val("");
            $('#editMetricGeneralTabBtn a').trigger('click');
            $('#editMetricLoadingMsg').hide();
            $('#editMetricLoadingIcon').hide();
            $('#editMetricOkMsg').hide();
            $('#editMetricOkIcon').hide();
            $('#editMetricKoMsg').hide();
            $('#editMetricKoIcon').hide();
        }
        
        $('#addMetricCancelBtn').click(resetAddMetricModal);
        $('#editMetricCancelBtn').click(resetEditMetricModal);
        
        $('#process').change(function()
        {
            $('#processType').empty();
            
            switch($(this).val())
            {
                case "DashboardProcess":
                    $('#processType').append('<option value="JVNum1">Numeric</option>');
                    $('#processType').append('<option value="JVPerc">Percent</option>');
                    $('#processType').append('<option value="JVTable">Table</option>');
                    $('#processType').append('<option value="API">API</option>');
                    $('#processType').append('<option value="JVRidesAtaf">ATAF rides (specific)</option>');
                    $('#processType').append('<option value="JVSceOnNodes">Sce on nodes (specific)</option>');
                    $('#processType').append('<option value="jVPark">Parkings (specific)</option>');
                    $('#processType').append('<option value="JVWifiOp">Wifi operatives (specific)</option>');
                    $('#processType').append('<option value="JVSmartDs">SmartDs (specific)</option>');
                    $('#processType').append('<option value="JVTwRet">Tweets/Retweets (specific)</option>');
                    $('#processType').append('<option value="none">Not defined</option>');
                    $('#addMetricQueryTabBtn a').html("Datasources & queries");
                    $('#addMetricQueryTabServerRow').hide();
                    $('#addMetricQueryTabQueryRow').show();
                    break;
                    
                case "HttpProcess":
                    $('#processType').append('<option value="checkStatus">Web server status</option>');
                    //$('#processType').append('<option value="responseTime">Web server response time</option>');
                    $('#addMetricQueryTabBtn a').html("Server");
                    $('#addMetricQueryTabQueryRow').hide();
                    $('#addMetricQueryTabServerRow').show();
                    break;
            }
        });
        
        $('#processM').change(function(){
            $('#processTypeM').empty();

            switch($(this).val())
            {
                case "DashboardProcess":
                    $('#processTypeM').append('<option value="JVNum1">Numeric</option>');
                    $('#processTypeM').append('<option value="JVPerc">Percent</option>');
                    $('#processTypeM').append('<option value="JVTable">Table</option>');
                    $('#processTypeM').append('<option value="API">API</option>');
                    $('#processTypeM').append('<option value="JVRidesAtaf">ATAF rides (specific)</option>');
                    $('#processTypeM').append('<option value="JVSceOnNodes">Sce on nodes (specific)</option>');
                    $('#processTypeM').append('<option value="jVPark">Parkings (specific)</option>');
                    $('#processTypeM').append('<option value="JVWifiOp">Wifi operatives (specific)</option>');
                    $('#processTypeM').append('<option value="JVSmartDs">SmartDs (specific)</option>');
                    $('#processTypeM').append('<option value="JVTwRet">Tweets/Retweets (specific)</option>');
                    $('#processTypeM').append('<option value="none">Not defined</option>');
                    $('#editMetricQueryTabBtn a').html("Datasources & queries");
                    $('#editMetricQueryTabServerRow').hide();
                    $('#editMetricQueryTabQueryRow').show();
                    break;

                case "HttpProcess":
                    $('#processTypeM').append('<option value="checkStatus">Web server status</option>');
                    //$('#processTypeM').append('<option value="responseTime">Web server response time</option>');
                    $('#editMetricQueryTabBtn a').html("Server");
                    $('#editMetricQueryTabQueryRow').hide();
                    $('#editMetricQueryTabServerRow').show();
                    break;
            }
        });
        
        /*$('#sameDataAlarmCountUp').on('click', function() 
        {
            if(($('#sameDataAlarmCount').val() === "Not active"))
            {
               $('#sameDataAlarmCount').val(1);   
            }
            else
            {
                $('#sameDataAlarmCount').val(parseInt($('#sameDataAlarmCount').val()) + 1);
            }
        });*/
                    
        $('#sameDataAlarmCountUp').mousedown(function(){
            //Singolo incremento del valore
            sameDataAlarmCountUp();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(sameDataAlarmCountUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#sameDataAlarmCountUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function sameDataAlarmCountUp()
        {
            if(($('#sameDataAlarmCount').val() === "Not active"))
            {
               $('#sameDataAlarmCount').val(1);   
            }
            else
            {
                $('#sameDataAlarmCount').val(parseInt($('#sameDataAlarmCount').val()) + 1);
            }
        }                     
        
        /*$('#sameDataAlarmCountDown').on('click', function() 
        {
           if(((parseInt($('#sameDataAlarmCount').val()) - 1) <= 0)||($('#sameDataAlarmCount').val() === "Not active"))
           {
               $('#sameDataAlarmCount').val("Not active");
           }
           else
           {
               $('#sameDataAlarmCount').val(parseInt($('#sameDataAlarmCount').val()) - 1);
           }
        });*/
                    
        $('#sameDataAlarmCountDown').mousedown(function(){
            //Singolo incremento del valore
            sameDataAlarmCountDown();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(sameDataAlarmCountDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#sameDataAlarmCountDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function sameDataAlarmCountDown()
        {
           if(((parseInt($('#sameDataAlarmCount').val()) - 1) <= 0)||($('#sameDataAlarmCount').val() === "Not active"))
           {
               $('#sameDataAlarmCount').val("Not active");
           }
           else
           {
               $('#sameDataAlarmCount').val(parseInt($('#sameDataAlarmCount').val()) - 1);
           }
        }             
        
        /*$('#sameDataAlarmCountMUp').on('click', function() 
        {
            if(($('#sameDataAlarmCountM').val() === "Not active"))
            {
               $('#sameDataAlarmCountM').val(1);   
            }
            else
            {
                $('#sameDataAlarmCountM').val(parseInt($('#sameDataAlarmCountM').val()) + 1);
            }
        });*/
                    
        $('#sameDataAlarmCountMUp').mousedown(function(){
            //Singolo incremento del valore
            sameDataAlarmCountMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(sameDataAlarmCountMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#sameDataAlarmCountMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function sameDataAlarmCountMUp()
        {
            if(($('#sameDataAlarmCountM').val() === "Not active"))
            {
               $('#sameDataAlarmCountM').val(1);   
            }
            else
            {
                $('#sameDataAlarmCountM').val(parseInt($('#sameDataAlarmCountM').val()) + 1);
            }
        }            
              
        /*$('#sameDataAlarmCountMDown').on('click', function() 
        {
           if(((parseInt($('#sameDataAlarmCountM').val()) - 1) <= 0)||($('#sameDataAlarmCountM').val() === "Not active"))
           {
               $('#sameDataAlarmCountM').val("Not active");
           }
           else
           {
               $('#sameDataAlarmCountM').val(parseInt($('#sameDataAlarmCountM').val()) - 1);
           }
        });*/
        
        $('#sameDataAlarmCountMDown').mousedown(function(){
            //Singolo incremento del valore
            sameDataAlarmCountMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(sameDataAlarmCountMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#sameDataAlarmCountMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function sameDataAlarmCountMDown()
        {
           if(((parseInt($('#sameDataAlarmCountM').val()) - 1) <= 0)||($('#sameDataAlarmCountM').val() === "Not active"))
           {
               $('#sameDataAlarmCountM').val("Not active");
           }
           else
           {
               $('#sameDataAlarmCountM').val(parseInt($('#sameDataAlarmCountM').val()) - 1);
           }
        }
        
        /*$('#updateFrequencySecUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencySec').val().replace(" s", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencySec').val("0 s");   
            }
            else
            {
                $('#updateFrequencySec').val(parseInt(parseInt(currentVal) + 1) + " s");
            }
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencySecUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencySecUp();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencySecUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencySecUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencySecUp()
        {
            var currentVal = $('#updateFrequencySec').val().replace(" s", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencySec').val("0 s");   
            }
            else
            {
                $('#updateFrequencySec').val(parseInt(parseInt(currentVal) + 1) + " s");
            }
            updateUpdateFrequency();
        }            
        
        /*$('#updateFrequencySecDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencySec').val().replace(" s", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencySec').val("59 s");   
            }
            else
            {
                $('#updateFrequencySec').val(parseInt(parseInt(currentVal) - 1) + " s");
            }
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencySecDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencySecDown();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencySecDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencySecDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencySecDown()
        {
            var currentVal = $('#updateFrequencySec').val().replace(" s", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencySec').val("59 s");   
            }
            else
            {
                $('#updateFrequencySec').val(parseInt(parseInt(currentVal) - 1) + " s");
            }
            updateUpdateFrequency();
        }            
        
        /*$('#updateFrequencyMinUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencyMin').val().replace(" m", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencyMin').val("0 m");   
            }
            else
            {
                $('#updateFrequencyMin').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencyMinUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyMinUp();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencyMinUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencyMinUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencyMinUp()
        {
            var currentVal = $('#updateFrequencyMin').val().replace(" m", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencyMin').val("0 m");   
            }
            else
            {
                $('#updateFrequencyMin').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateUpdateFrequency();
        }              
        
        /*$('#updateFrequencyMinDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencyMin').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencyMin').val("59 m");   
            }
            else
            {
                $('#updateFrequencyMin').val(parseInt(parseInt(currentVal) - 1) + " m");
            }
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencyMinDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyMinDown();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencyMinDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencyMinDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencyMinDown()
        {
            var currentVal = $('#updateFrequencyMin').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencyMin').val("59 m");   
            }
            else
            {
                $('#updateFrequencyMin').val(parseInt(parseInt(currentVal) - 1) + " m");
            }
            updateUpdateFrequency();
        }             
        
        /*$('#updateFrequencyHourUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencyHour').val().replace(" h", "");
            $('#updateFrequencyHour').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencyHourUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyHourUp();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencyHourUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencyHourUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencyHourUp()
        {
            var currentVal = $('#updateFrequencyHour').val().replace(" h", "");
            $('#updateFrequencyHour').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateUpdateFrequency();
        }              
        
        /*$('#updateFrequencyHourDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencyHour').val().replace(" h", "");
            if((currentVal !== "0"))
            {
               $('#updateFrequencyHour').val(parseInt(parseInt(currentVal) - 1) + " h");
            }
            updateUpdateFrequency();
        });*/
                    
        $('#updateFrequencyHourDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyHourDown();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencyHourDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#updateFrequencyHourDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function updateFrequencyHourDown()
        {
            var currentVal = $('#updateFrequencyHour').val().replace(" h", "");
            if((currentVal !== "0"))
            {
               $('#updateFrequencyHour').val(parseInt(parseInt(currentVal) - 1) + " h");
            }
            updateUpdateFrequency();
        }            
        
        function updateUpdateFrequency()
        {
            var currentSec = parseInt($('#updateFrequencySec').val().replace(" s", ""));
            var currentMin = parseInt($('#updateFrequencyMin').val().replace(" m", ""));
            var currentHour = parseInt($('#updateFrequencyHour').val().replace(" m", ""));
            
            var updateFrequency = (currentSec + currentMin*60 + currentHour*3600)*1000;
            $('#updateFrequency').val(updateFrequency);
        }
        
        /*$('#updateFrequencySecMUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencySecM').val().replace(" s", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencySecM').val("0 s");   
            }
            else
            {
                $('#updateFrequencySecM').val(parseInt(parseInt(currentVal) + 1) + " s");
            }
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencySecMUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencySecMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(updateFrequencySecMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencySecMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                    clearTimeout(increaseTimeout);
                    increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                    clearInterval(increaseInterval);
                    increaseInterval = null;
            }
        });

        function updateFrequencySecMUp()
        {
            var currentVal = $('#updateFrequencySecM').val().replace(" s", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencySecM').val("0 s");   
            }
            else
            {
                $('#updateFrequencySecM').val(parseInt(parseInt(currentVal) + 1) + " s");
            }
            updateUpdateFrequencyM();
        }              
        
        /*$('#updateFrequencySecMDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencySecM').val().replace(" s", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencySecM').val("59 s");   
            }
            else
            {
                $('#updateFrequencySecM').val(parseInt(parseInt(currentVal) - 1) + " s");
            }
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencySecMDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencySecMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(updateFrequencySecMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencySecMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function updateFrequencySecMDown()
        {
            var currentVal = $('#updateFrequencySecM').val().replace(" s", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencySecM').val("59 s");   
            }
            else
            {
                $('#updateFrequencySecM').val(parseInt(parseInt(currentVal) - 1) + " s");
            }
            updateUpdateFrequencyM();
        }              
        
        /*$('#updateFrequencyMinMUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencyMinM').val().replace(" m", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencyMinM').val("0 m");   
            }
            else
            {
                $('#updateFrequencyMinM').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencyMinMUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyMinMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(updateFrequencyMinMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencyMinMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function updateFrequencyMinMUp()
        {
            var currentVal = $('#updateFrequencyMinM').val().replace(" m", "");
            if((currentVal === "59"))
            {
               $('#updateFrequencyMinM').val("0 m");   
            }
            else
            {
                $('#updateFrequencyMinM').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateUpdateFrequencyM();
        }              
        
        /*$('#updateFrequencyMinMDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencyMinM').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencyMinM').val("59 m");   
            }
            else
            {
                $('#updateFrequencyMinM').val(parseInt(parseInt(currentVal) - 1) + " m");
            }
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencyMinMDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyMinMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(updateFrequencyMinMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencyMinMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function updateFrequencyMinMDown()
        {
            var currentVal = $('#updateFrequencyMinM').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#updateFrequencyMinM').val("59 m");   
            }
            else
            {
                $('#updateFrequencyMinM').val(parseInt(parseInt(currentVal) - 1) + " m");
            }
            updateUpdateFrequencyM();
        }            
        
        /*$('#updateFrequencyHourMUp').on('click', function() 
        {
            var currentVal = $('#updateFrequencyHourM').val().replace(" h", "");
            $('#updateFrequencyHourM').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencyHourMUp').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyHourMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(updateFrequencyHourMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencyHourMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function updateFrequencyHourMUp()
        {
            var currentVal = $('#updateFrequencyHourM').val().replace(" h", "");
            $('#updateFrequencyHourM').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateUpdateFrequencyM();
        }            
        
        /*$('#updateFrequencyHourMDown').on('click', function() 
        {
            var currentVal = $('#updateFrequencyHourM').val().replace(" h", "");
            if((currentVal !== "0"))
            {
               $('#updateFrequencyHourM').val(parseInt(parseInt(currentVal) - 1) + " h");
            }
            updateUpdateFrequencyM();
        });*/
                    
        $('#updateFrequencyHourMDown').mousedown(function(){
            //Singolo incremento del valore
            updateFrequencyHourMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(updateFrequencyHourMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#updateFrequencyHourMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function updateFrequencyHourMDown()
        {
            var currentVal = $('#updateFrequencyHourM').val().replace(" h", "");
            if((currentVal !== "0"))
            {
               $('#updateFrequencyHourM').val(parseInt(parseInt(currentVal) - 1) + " h");
            }
            updateUpdateFrequencyM();
        }            
        
        function updateUpdateFrequencyM()
        {
            var currentSec = parseInt($('#updateFrequencySecM').val().replace(" s", ""));
            var currentMin = parseInt($('#updateFrequencyMinM').val().replace(" m", ""));
            var currentHour = parseInt($('#updateFrequencyHourM').val().replace(" m", ""));
            
            var updateFrequencyM = (currentSec + currentMin*60 + currentHour*3600)*1000;
            $('#updateFrequencyM').val(updateFrequencyM);
        }
        
        //Old data eval time: risponditori di evento
        /*$('#oldDataEvalTimeSecUp').on('click', function() 
        {
            if(($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeHour').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSec').val() !== "59 s"))
                {
                   if($('#oldDataEvalTimeSec').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSec').val("1 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) + 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSec').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSec').val() === "59 s"))
                {
                   $('#oldDataEvalTimeSec').val("0 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSec').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSec').val("1 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) + 1) + " s");   
                    }
                }
            }
            
            updateOldDataEvalTime();
        });*/
                    
        $('#oldDataEvalTimeSecUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeSecUp();
            
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeSecUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeSecUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeSecUp()
        {
            if(($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeHour').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSec').val() !== "59 s"))
                {
                   if($('#oldDataEvalTimeSec').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSec').val("1 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) + 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSec').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSec').val() === "59 s"))
                {
                   $('#oldDataEvalTimeSec').val("0 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSec').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSec').val("1 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) + 1) + " s");   
                    }
                }
            }
            
            updateOldDataEvalTime();
        }
        
        /*$('#oldDataEvalTimeSecDown').on('click', function() 
        {
            if(($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeHour').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSec').val() !== "1 s"))
                {
                   if($('#oldDataEvalTimeSec').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSec').val("59 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) - 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSec').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSec').val() === "0 s"))
                {
                   $('#oldDataEvalTimeSec').val("59 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSec').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSec').val("59 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSec').val((parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", ""))) - 1) + " s");
                    }
                } 
            }
            updateOldDataEvalTime();
        });*/
                    
        $('#oldDataEvalTimeSecDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeSecDown();
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeSecDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeSecDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeSecDown()
        {
            if(($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeHour').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSec').val() !== "1 s"))
                {
                   if($('#oldDataEvalTimeSec').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSec').val("59 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSec').val(parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", "")) - 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSec').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSec').val() === "0 s"))
                {
                   $('#oldDataEvalTimeSec').val("59 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSec').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSec').val("59 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSec').val((parseInt(parseInt($('#oldDataEvalTimeSec').val().replace(" s", ""))) - 1) + " s");
                    }
                } 
            }
            updateOldDataEvalTime();
        }            
        
        /*$('#oldDataEvalTimeMinUp').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeMin').val().replace(" m", "");
            if((currentVal === "59"))
            {
               if(($('#oldDataEvalTimeHour').val() === "0 h")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMin').val("0 m");  
               }
            }
            else
            {
                $('#oldDataEvalTimeMin').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateOldDataEvalTime();
        });*/
                    
        $('#oldDataEvalTimeMinUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeMinUp();
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeMinUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeMinUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeMinUp()
        {
            var currentVal = $('#oldDataEvalTimeMin').val().replace(" m", "");
            if((currentVal === "59"))
            {
               if(($('#oldDataEvalTimeHour').val() === "0 h")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMin').val("0 m");  
               }
            }
            else
            {
                $('#oldDataEvalTimeMin').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateOldDataEvalTime();
        }                 
        
        /*$('#oldDataEvalTimeMinDown').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeMin').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#oldDataEvalTimeMin').val("59 m");   
            }
            else
            {
               if(($('#oldDataEvalTimeHour').val() === "0 h")&&($('#oldDataEvalTimeMin').val() === "1 m")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMin').val(parseInt(parseInt(currentVal) - 1) + " m");  
               }
            }
            updateOldDataEvalTime();
        });*/
                    
        $('#oldDataEvalTimeMinDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeMinDown();
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeMinDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeMinDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeMinDown()
        {
            var currentVal = $('#oldDataEvalTimeMin').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#oldDataEvalTimeMin').val("59 m");   
            }
            else
            {
               if(($('#oldDataEvalTimeHour').val() === "0 h")&&($('#oldDataEvalTimeMin').val() === "1 m")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMin').val(parseInt(parseInt(currentVal) - 1) + " m");  
               }
            }
            updateOldDataEvalTime();
        }            
        
        /*$('#oldDataEvalTimeHourUp').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeHour').val().replace(" h", "");
            $('#oldDataEvalTimeHour').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateOldDataEvalTime();
        });*/
                    
        $('#oldDataEvalTimeHourUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeHourUp();
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeHourUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeHourUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeHourUp()
        {
            var currentVal = $('#oldDataEvalTimeHour').val().replace(" h", "");
            $('#oldDataEvalTimeHour').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateOldDataEvalTime();
        }                
        
        /*$('#oldDataEvalTimeHourDown').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeHour').val().replace(" h", "");
            
            if((currentVal !== "0"))
            {
               $('#oldDataEvalTimeHour').val(parseInt(parseInt(currentVal) - 1) + " h");
               if(($('#oldDataEvalTimeHour').val() === '0 h')&&($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active"); 
               }
            }
            
            updateOldDataEvalTime();
        });*/
        
        $('#oldDataEvalTimeHourDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeHourDown();
            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeHourDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     
        
        $('#oldDataEvalTimeHourDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }
            
            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });
        
        function oldDataEvalTimeHourDown()
        {
            var currentVal = $('#oldDataEvalTimeHour').val().replace(" h", "");
            
            if((currentVal !== "0"))
            {
               $('#oldDataEvalTimeHour').val(parseInt(parseInt(currentVal) - 1) + " h");
               if(($('#oldDataEvalTimeHour').val() === '0 h')&&($('#oldDataEvalTimeMin').val() === "0 m")&&($('#oldDataEvalTimeSec').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSec').val("Not active"); 
               }
            }
            
            updateOldDataEvalTime();
        }    
        
        updateOldDataEvalTime();
        
        function updateOldDataEvalTime()
        {
            var currentSec, currentMin, currentHour, oldDataEvalTime = null;
            if($('#oldDataEvalTimeSec').val() === "Not active")
            {
                oldDataEvalTime = "Not active";
                $('#oldDataEvalTimeMin').val("0 m");
                $('#oldDataEvalTimeHour').val("0 h");
                $('#addMetricGeneralTab #oldDataEvalTimeHourContainer').hide();
                $('#addMetricGeneralTab #oldDataEvalTimeMinContainer').hide();
                $('#addMetricGeneralTab #oldDataEvalTimeSecContainer').css("width", "100%");
            }
            else
            {
                currentSec = parseInt($('#oldDataEvalTimeSec').val().replace(" s", ""));
                currentMin = parseInt($('#oldDataEvalTimeMin').val().replace(" m", ""));
                currentHour = parseInt($('#oldDataEvalTimeHour').val().replace(" m", ""));
                oldDataEvalTime = (currentSec + currentMin*60 + currentHour*3600)*1000;
                
                $('#addMetricGeneralTab #oldDataEvalTimeHourContainer').show();
                $('#addMetricGeneralTab #oldDataEvalTimeMinContainer').show();
                $('#addMetricGeneralTab #oldDataEvalTimeSecContainer').css("width", "33%");
            }
            
            $('#oldDataEvalTime').val(oldDataEvalTime);
        }
        
        //Old data eval time M: risponditori di evento
        /*$('#oldDataEvalTimeSecMUp').on('click', function() 
        {
            if(($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeHourM').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSecM').val() !== "59 s"))
                {
                   if($('#oldDataEvalTimeSecM').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSecM').val("1 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) + 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSecM').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSecM').val() === "59 s"))
                {
                   $('#oldDataEvalTimeSecM').val("0 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSecM').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSecM').val("1 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) + 1) + " s");   
                    }
                }
            }
            
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeSecMUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeSecMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(oldDataEvalTimeSecMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeSecMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeSecMUp()
        {
            if(($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeHourM').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSecM').val() !== "59 s"))
                {
                   if($('#oldDataEvalTimeSecM').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSecM').val("1 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) + 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSecM').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSecM').val() === "59 s"))
                {
                   $('#oldDataEvalTimeSecM').val("0 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSecM').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSecM').val("1 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) + 1) + " s");   
                    }
                }
            }
            
            updateOldDataEvalTimeM();
        }            
        
        /*$('#oldDataEvalTimeSecMDown').on('click', function() 
        {
            if(($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeHourM').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSecM').val() !== "1 s"))
                {
                   if($('#oldDataEvalTimeSecM').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSecM').val("59 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) - 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSecM').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSecM').val() === "0 s"))
                {
                   $('#oldDataEvalTimeSecM').val("59 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSecM').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSecM').val("59 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSecM').val((parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", ""))) - 1) + " s");
                    }
                } 
            }
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeSecMDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeSecMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(oldDataEvalTimeSecMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeSecMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeSecMDown()
        {
            if(($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeHourM').val() === "0 h"))
            {
                if(($('#oldDataEvalTimeSecM').val() !== "1 s"))
                {
                   if($('#oldDataEvalTimeSecM').val() === "Not active")
                   {
                       $('#oldDataEvalTimeSecM').val("59 s");      
                   }
                   else
                   {
                       $('#oldDataEvalTimeSecM').val(parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", "")) - 1) + " s");   
                   }
                }
                else
                {
                    $('#oldDataEvalTimeSecM').val("Not active");      
                }
            }
            else
            {
                if(($('#oldDataEvalTimeSecM').val() === "0 s"))
                {
                   $('#oldDataEvalTimeSecM').val("59 s");   
                }
                else
                {
                    if($('#oldDataEvalTimeSecM').val() === "Not active")
                    {
                        $('#oldDataEvalTimeSecM').val("59 s");      
                    }
                    else
                    {
                        $('#oldDataEvalTimeSecM').val((parseInt(parseInt($('#oldDataEvalTimeSecM').val().replace(" s", ""))) - 1) + " s");
                    }
                } 
            }
            updateOldDataEvalTimeM();
        }            
        
        /*$('#oldDataEvalTimeMinMUp').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeMinM').val().replace(" m", "");
            if((currentVal === "59"))
            {
               if(($('#oldDataEvalTimeHourM').val() === "0 h")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMinM').val("0 m");  
               }
            }
            else
            {
                $('#oldDataEvalTimeMinM').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeMinMUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeMinMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(oldDataEvalTimeMinMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeMinMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeMinMUp()
        {
            var currentVal = $('#oldDataEvalTimeMinM').val().replace(" m", "");
            if((currentVal === "59"))
            {
               if(($('#oldDataEvalTimeHourM').val() === "0 h")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMinM').val("0 m");  
               }
            }
            else
            {
                $('#oldDataEvalTimeMinM').val(parseInt(parseInt(currentVal) + 1) + " m");
            }
            updateOldDataEvalTimeM();
        }            
        
        /*$('#oldDataEvalTimeMinMDown').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeMinM').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#oldDataEvalTimeMinM').val("59 m");   
            }
            else
            {
               if(($('#oldDataEvalTimeHourM').val() === "0 h")&&($('#oldDataEvalTimeMinM').val() === "1 m")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMinM').val(parseInt(parseInt(currentVal) - 1) + " m");  
               }
            }
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeMinMDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeMinMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(oldDataEvalTimeMinMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeMinMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeMinMDown()
        {
            var currentVal = $('#oldDataEvalTimeMinM').val().replace(" m", "");
            if((currentVal === "0"))
            {
               $('#oldDataEvalTimeMinM').val("59 m");   
            }
            else
            {
               if(($('#oldDataEvalTimeHourM').val() === "0 h")&&($('#oldDataEvalTimeMinM').val() === "1 m")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active");
               }
               else
               {
                  $('#oldDataEvalTimeMinM').val(parseInt(parseInt(currentVal) - 1) + " m");  
               }
            }
            updateOldDataEvalTimeM();
        }            
        
        /*$('#oldDataEvalTimeHourMUp').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeHourM').val().replace(" h", "");
            $('#oldDataEvalTimeHourM').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeHourMUp').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeHourMUp();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                increaseInterval = setInterval(oldDataEvalTimeHourMUp, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeHourMUp').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeHourMUp()
        {
            var currentVal = $('#oldDataEvalTimeHourM').val().replace(" h", "");
            $('#oldDataEvalTimeHourM').val(parseInt(parseInt(currentVal) + 1) + " h");
            updateOldDataEvalTimeM();
        }            
        
        /*$('#oldDataEvalTimeHourMDown').on('click', function() 
        {
            var currentVal = $('#oldDataEvalTimeHourM').val().replace(" h", "");
            
            if((currentVal !== "0"))
            {
               $('#oldDataEvalTimeHourM').val(parseInt(parseInt(currentVal) - 1) + " h");
               if(($('#oldDataEvalTimeHourM').val() === '0 h')&&($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active"); 
               }
            }
            
            updateOldDataEvalTimeM();
        });*/
                    
        $('#oldDataEvalTimeHourMDown').mousedown(function(){
            //Singolo incremento del valore
            oldDataEvalTimeHourMDown();

            //Incremento automatico al mantenimento pressione
            increaseTimeout = setTimeout(function(){
                    increaseInterval = setInterval(oldDataEvalTimeHourMDown, spinnerIntervalValue);
            }, spinnerHoldValue);
        });     

        $('#oldDataEvalTimeHourMDown').mouseup(function(){
            if(increaseTimeout !== null)
            {
                clearTimeout(increaseTimeout);
                increaseTimeout = null;
            }

            if(increaseInterval !== null)
            {
                clearInterval(increaseInterval);
                increaseInterval = null;
            }
        });

        function oldDataEvalTimeHourMDown()
        {
            var currentVal = $('#oldDataEvalTimeHourM').val().replace(" h", "");
            
            if((currentVal !== "0"))
            {
               $('#oldDataEvalTimeHourM').val(parseInt(parseInt(currentVal) - 1) + " h");
               if(($('#oldDataEvalTimeHourM').val() === '0 h')&&($('#oldDataEvalTimeMinM').val() === "0 m")&&($('#oldDataEvalTimeSecM').val() === "0 s"))
               {
                  $('#oldDataEvalTimeSecM').val("Not active"); 
               }
            }
            
            updateOldDataEvalTimeM();
        }            
        
        function updateOldDataEvalTimeM()
        {
            var currentSec, currentMin, currentHour, oldDataEvalTime = null;
            if($('#oldDataEvalTimeSecM').val() === "Not active")
            {
                oldDataEvalTime = "Not active";
                $('#oldDataEvalTimeMinM').val("0 m");
                $('#oldDataEvalTimeHourM').val("0 h");
                $('#editMetricGeneralTab #oldDataEvalTimeHourContainer').hide();
                $('#editMetricGeneralTab #oldDataEvalTimeMinContainer').hide();
                $('#editMetricGeneralTab #oldDataEvalTimeSecContainer').css("width", "100%");
            }
            else
            {
                currentSec = parseInt($('#oldDataEvalTimeSecM').val().replace(" s", ""));
                currentMin = parseInt($('#oldDataEvalTimeMinM').val().replace(" m", ""));
                currentHour = parseInt($('#oldDataEvalTimeHourM').val().replace(" m", ""));
                oldDataEvalTime = (currentSec + currentMin*60 + currentHour*3600)*1000;
                
                $('#editMetricGeneralTab #oldDataEvalTimeHourContainer').show();
                $('#editMetricGeneralTab #oldDataEvalTimeMinContainer').show();
                $('#editMetricGeneralTab #oldDataEvalTimeSecContainer').css("width", "33%");
            }
            
            $('#oldDataEvalTimeM').val(oldDataEvalTime);
        }
        
        var tableFirstLoad = true;
        buildMainTable(false);
        
        $('#addMetricConfirmBtn').off("click");
        $('#addMetricConfirmBtn').click(function(){
            $('#addMetricModalTabs').hide();
            $('#modalAddMetric div.modalCell').hide();
            $('#addMetricModalFooter').hide();
            $('#addMetricLoadingMsg').show();
            $('#addMetricLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    addMetricType: true,
                    metricName: $('#metricName').val(),
                    shortDescription: $('#shortDescription').val(),
                    dataArea: $('#dataArea').val(),
                    fullDescription: $('#fullDescription').val(),
                    resultType: $('#resultType').val(),
                    updateFrequency: $('#updateFrequency').val(),
                    processType: $('#processType').val(),
                    cityContext: $('#cityContext').val(),
                    timeRange: $('#timeRange').val(),
                    storingData: $('#storingData').val(),
                    dataSourceType: $('#dataSourceType').val(),
                    dataSource: $('#dataSource').val(),
                    dataSource2: $('#dataSource2').val(),
                    dataSourceDescription: $('#dataSourceDescription').val(),
                    query: $('#query').val(),
                    query2: $('#query2').val(),
                    sameDataAlarmCount: $('#sameDataAlarmCount').val(),
                    hasNegativeValues: $('#hasNegativeValues').val(),
                    oldDataEvalTime: $('#oldDataEvalTime').val(),
                    process: $('#process').val(),
                    serverTestHttpMethod: $('#serverTestHttpMethod').val(),
                    serverTestToken: $('#serverTestToken').val(),
                    serverTestUrl: $('#serverTestUrl').val()
                },
                type: "POST",
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error adding metric type");
                        console.log(data);
                        $('#addMetricLoadingMsg').hide();
                        $('#addMetricLoadingIcon').hide();
                        $('#addMetricKoMsg').show();
                        $('#addMetricKoIcon').show();
                        setTimeout(function(){
                            $('#addMetricKoMsg').hide();
                            $('#addMetricKoIcon').hide();
                            $('#addMetricModalTabs').show();
                            $('#modalAddMetric div.modalCell').show();
                            $('#addMetricModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#addMetricLoadingMsg').hide();
                        $('#addMetricLoadingIcon').hide();
                        $('#addMetricOkMsg').show();
                        $('#addMetricOkIcon').show();
                                                 
                        $('#dashboardTotNumberCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotNumberCnt .pageSingleDataCnt').html()) + 1);
                        $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) + 1);
                                  
                        setTimeout(function(){
                            $('#modalAddMetric').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                /*$('#addMetricOkMsg').hide();
                                $('#addMetricOkIcon').hide();
                                $('#metricName').val("");
                                $('#shortDescription').val("");
                                $('#dataArea').val("");
                                $('#fullDescription').val("");
                                $('#resultType').val("");
                                $('#updateFrequency').val("");
                                $('#processType').val("none");
                                $('#cityContext').val("0");
                                $('#timeRange').val("0");
                                $('#storingData').val("1");
                                $('#dataSourceType').val("NULL");
                                $('#dataSource').val("none");
                                $('#dataSource2').val("none");
                                $('#dataSourceDescription').val("");
                                $('#query').val("");
                                $('#query2').val("");
                                $('#sameDataAlarmCount').val("");
                                $('#hasNegativeValues').val("0");
                                $('#oldDataEvalTime').val("");
                                $('#process').val("");*/
                                $('#addMetricModalTabs').show();
                                $('#modalAddMetric div.modalCell').show();
                                $('#addMetricModalFooter').show();
                                resetAddMetricModal();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    $('#addMetricLoadingMsg').hide();
                    $('#addMetricLoadingIcon').hide();
                    $('#addMetricKoMsg').show();
                    $('#addMetricKoIcon').show();
                    setTimeout(function(){
                        $('#addMetricKoMsg').hide();
                        $('#addMetricKoIcon').hide();
                        $('#addMetricModalTabs').show();
                        $('#modalAddMetric div.modalCell').show();
                        $('#addMetricModalFooter').show();
                    }, 3000);
                    console.log("Error adding metric type");
                    console.log(errorData);
                }
            });  
        });
        
        $('#editMetricConfirmBtn').off("click");
        $('#editMetricConfirmBtn').click(function(){
            $('#editMetricModalTabs').hide();
            $('#modalEditMetric div.modalCell').hide();
            $('#editMetricModalFooter').hide();
            $('#editMetricLoadingMsg').show();
            $('#editMetricLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    editMetricType: true,
                    metricId: $('#metricId').val(),
                    metricNameM: $('#metricNameM').val(),
                    shortDescriptionM: $('#shortDescriptionM').val(),
                    dataAreaM: $('#dataAreaM').val(),
                    fullDescriptionM: $('#fullDescriptionM').val(),
                    resultTypeM: $('#resultTypeM').val(),
                    updateFrequencyM: $('#updateFrequencyM').val(),
                    processTypeM: $('#processTypeM').val(),
                    cityContextM: $('#cityContextM').val(),
                    timeRangeM: $('#timeRangeM').val(),
                    storingDataM: $('#storingDataM').val(),
                    dataSourceTypeM: $('#dataSourceTypeM').val(),
                    dataSourceM: $('#dataSourceM').val(),
                    dataSource2M: $('#dataSource2M').val(),
                    dataSourceDescriptionM: $('#dataSourceDescriptionM').val(),
                    queryM: $('#queryM').val(),
                    query2M: $('#query2M').val(),
                    sameDataAlarmCountM: $('#sameDataAlarmCountM').val(),
                    hasNegativeValuesM: $('#hasNegativeValuesM').val(),
                    oldDataEvalTimeM: $('#oldDataEvalTimeM').val(),
                    processM: $('#processM').val(),
                    serverTestHttpMethodM: $('#serverTestHttpMethodM').val(),
                    serverTestTokenM: $('#serverTestTokenM').val(),
                    serverTestUrlM: $('#serverTestUrlM').val()
                },
                type: "POST",
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error updating metric type");
                        console.log(data);
                        $('#editMetricLoadingMsg').hide();
                        $('#editMetricLoadingIcon').hide();
                        $('#editMetricKoMsg').show();
                        $('#editMetricKoIcon').show();
                        setTimeout(function(){
                            $('#editMetricKoMsg').hide();
                            $('#editMetricKoIcon').hide();
                            $('#editMetricModalTabs').show();
                            $('#modalEditMetric div.modalCell').show();
                            $('#editMetricModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#editMetricLoadingMsg').hide();
                        $('#editMetricLoadingIcon').hide();
                        $('#editMetricOkMsg').show();
                        $('#editMetricOkIcon').show();

                        setTimeout(function(){
                            $('#modalEditMetric').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#editMetricOkMsg').hide();
                                $('#editMetricOkIcon').hide();
                                /*$('#metricNameM').val("");
                                $('#shortDescriptionM').val("");
                                $('#dataAreaM').val("");
                                $('#fullDescriptionM').val("");
                                $('#resultTypeM').val("");
                                $('#updateFrequencyM').val("");
                                $('#processTypeM').val("none");
                                $('#cityContextM').val("0");
                                $('#timeRangeM').val("0");
                                $('#storingDataM').val("1");
                                $('#dataSourceTypeM').val("NULL");
                                $('#dataSourceM').val("none");
                                $('#dataSource2M').val("none");
                                $('#dataSourceDescriptionM').val("");
                                $('#queryM').val("");
                                $('#query2M').val("");
                                $('#sameDataAlarmCountM').val("");
                                $('#hasNegativeValuesM').val("0");
                                $('#oldDataEvalTimeM').val("");
                                $('#processM').val("");*/
                                $('#editMetricModalTabs').show();
                                $('#modalEditMetric div.modalCell').show();
                                $('#editMetricModalFooter').show();
                                resetEditMetricModal();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    $('#editMetricLoadingMsg').hide();
                    $('#editMetricLoadingIcon').hide();
                    $('#editMetricKoMsg').show();
                    $('#editMetricKoIcon').show();
                    setTimeout(function(){
                        $('#editMetricKoMsg').hide();
                        $('#editMetricKoIcon').hide();
                        $('#editMetricModalTabs').show();
                        $('#modalEditMetric div.modalCell').show();
                        $('#editMetricModalFooter').show();
                    }, 3000);
                    console.log("Error updating metric type");
                    console.log(errorData);
                }
            });  
        });
        
        function buildMainTable(destroyOld)
        {
            if(destroyOld)
            {
                $('#list_metrics').bootstrapTable('destroy');
                tableFirstLoad = true;
            }
            
            var descVisibile = true;
            var typeVisible = true;
            var sourceVisibile = true;
            var datasourceVisibile = true;
            var statusVisibile = true;

            if($(window).width() < 992)
            {
                descVisibile = false;
                typeVisible = false; 
                sourceVisibile = false;
                datasourceVisibile = false;
                statusVisibile = false;
            }

            $.ajax({
                url: "get_data.php",
                data: {action: "getMetricList"},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    //console.log(JSON.stringify(data));
                    $('#list_metrics').bootstrapTable({
                        columns: [{
                            field: 'IdMetric',
                            title: 'Name',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                var maxL = 50;
                                if($(window).width() < 992)
                                {
                                    maxL = 15;
                                }
                                
                                if(value !== null)
                                {
                                    if(value.length > maxL)
                                    {
                                       return value.substr(0, maxL) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                        //      cellStyle: function(value, row, index, field) {
                        //         var fontSize = "1em"; 
                        //         if($(window).width() < 992)
                        //         {
                        //             fontSize = "0.9em";
                        //         } 
                        //          
                        //         if(index%2 !== 0)
                        //         {
                        //             return {
                        //                 classes: null,
                        //                 css: {
                        //                     "color": "rgba(51, 64, 69, 1)", 
                        //                     "font-size": fontSize,
                        //                     "font-weight": "bold",
                        //                     "background-color": "rgb(230, 249, 255)",
                        //                     "border-top": "none"
                        //                 }
                        //             };
                        //         }
                        //         else
                        //         {
                        //             return {
                        //                 classes: null,
                        //                 css: {
                        //                     "color": "rgba(51, 64, 69, 1)", 
                        //                     "font-size": fontSize,
                        //                     "font-weight": "bold",
                        //                     "background-color": "white",
                        //                     "border-top": "none"
                        //                 }
                        //             };
                        //         }
                        //     }
                        // }, {
                            field: 'description_short',
                            title: 'Description',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: descVisibile,
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                //"background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                        }, {
                            field: 'metricType',
                            title: 'Type',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: typeVisible,
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                //"background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                        },
                        {
                            field: 'source',
                            title: 'Source',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: sourceVisibile,
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                // "background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                        },
                        {
                            field: 'dataSource',
                            title: 'Data source',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: datasourceVisibile,
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                // "background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                        },
                        {
                            field: 'status',
                            title: "Status",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: statusVisibile,
                            formatter: function(value, row, index)
                            {
                                if(value === 'Non Attivo')
                                {
                                    return '<input type="checkbox" data-toggle="toggle" class="changeMetricStatus">';
                                }
                                else
                                {
                                    return '<input type="checkbox" checked data-toggle="toggle" class="changeMetricStatus">';
                                }

                            },
                                cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                // "background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                        },
                        {
                            title: "",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if((row.process === 'HttpProcess')&&(row.processType === 'responseTime'))
                                {
                                    return '<button type="button" class="undeletableMetricBtn">uneditable</button>';
                                }
                                else
                                {
                                    return '<button type="button" class="editDashBtn">edit</button>';
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: 'blueRow',
                                        css: {
                                            // "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: 'whiteRow',
                                        css: {
                                            // "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        },
                        {
                            title: "",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if((row.process === 'HttpProcess')&&(row.processType === 'responseTime'))
                                {
                                    return '<button type="button" class="undeletableMetricBtn">undeletable</button>';
                                }
                                else
                                {
                                    return '<button type="button" class="delDashBtn">del</button>';
                                }
                            },
                                    cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: 'blueRow',
                                            css: {
                                                // "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: 'whiteRow',
                                            css: {
                                                // "background-color": "white",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                }
                     }],
                        data: data,
                        search: true,
                        pagination: true,
                        pageSize: 10,
                        locale: 'en-US',
                        searchAlign: 'left',
                        uniqueId: "id",
                        striped: false,
                        searchTimeOut: 60,
                        classes: "table table-no-bordered",
                        onPostBody: function()
                        {
                            if(tableFirstLoad)
                            {
                                //Caso di primo caricamento della tabella
                                tableFirstLoad = false;
                                var addMetricDiv = $('<div class="pull-right"><i id="link_add_metric" data-toggle="modal" data-target="#modalAddMetric" class="fa-solid fa-circle-plus"></i></div>');
                                $('div.fixed-table-toolbar').append(addMetricDiv);
                                addMetricDiv.css("margin-top", "10px");
                                addMetricDiv.find('i.fa-plus-square').off('hover');
                                addMetricDiv.find('i.fa-plus-square').hover(function(){
                                    $(this).css('color', '#e37777');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', 'var(--orange-)');
                                    $(this).css('cursor', 'normal');
                                });

                                //$('#list_metrics thead').css("background", "rgba(0, 162, 211, 1)");
                                //$('#list_metrics thead').css("color", "white");
                                //$('#list_metrics thead').css("font-size", "1em");
                            }
                            else
                            {
                                //Casi di cambio pagina
                            }

                            //Istruzioni da eseguire comunque
                            $('#list_metrics').css("border-bottom", "none");
                            $('span.pagination-info').hide();

                            $('#list_metrics button.editDashBtn').off('hover');
                            $('#list_metrics button.editDashBtn').hover(function(){
                                $(this).css('background-color', 'var(--orange-)');
                                $(this).parents('tr').find('td').eq(0).css({'background' : 'var(--orange-)', 'color' : 'var(--text-color-)'});
                            }, 
                            function(){
                                $(this).css('background-color', 'var(--blue-)');
                                $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                            });

                            $('#list_metrics button.delDashBtn').off('hover');
                            $('#list_metrics button.delDashBtn').hover(function(){
                                $(this).css('background-color', 'var(--orange-)');
                                $(this).parents('tr').find('td').eq(0).css({'background' : 'var(--orange-)', 'color' : 'var(--text-color-)'});
                            }, 
                            function(){
                                $(this).css('background-color', '#e37777');
                                $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                            });

                            $('#list_metrics input.changeMetricStatus').bootstrapToggle({
                                on: "On",
                                off: "Off",
                                onstyle: "primary",
                                offstyle: "default",
                                size: "mini"
                            });

                            $('#list_metrics tbody input.changeMetricStatus').off('change');
                            $('#list_metrics tbody input.changeMetricStatus').change(function() {
                                if($(this).prop('checked') === false)
                                {
                                    var newStatus = 'Non Attivo';
                                }
                                else
                                {
                                    var newStatus = 'Attivo';
                                }

                                $.ajax({
                                    url: "process-form.php",
                                    data: {
                                        updateMetricStatus: true,
                                        metricId: $(this).parents('tr').attr('data-uniqueid'),
                                        newStatus: newStatus
                                    },
                                    type: "POST",
                                    async: true,
                                    success: function(data)
                                    {
                                        if(data !== "Ok")
                                        {
                                            console.log("Error updating metric status");
                                            console.log(data);
                                            alert("Error updating metric status");
                                            location.reload();
                                        }
                                        else
                                        {
                                            if($('#dashboardTotActiveCnt .pageSingleDataCnt').html() !== "-")
                                            {
                                                if(newStatus === 'Non Attivo')
                                                {
                                                    $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                                }
                                                else
                                                {
                                                    $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) + 1);
                                                }
                                            }
                                        }
                                    },
                                    error: function(errorData)
                                    {
                                        console.log("Error updating metric status");
                                        console.log(errorData);
                                        alert("Error updating metric status");
                                        location.reload();
                                    }
                                });
                            });

                            $('#list_metrics tbody button.editDashBtn').off('click');
                            $('#list_metrics tbody button.editDashBtn').click(function() 
                            {
                                var metricId = $(this).parents('tr').attr('data-uniqueid');

                                $.ajax({
                                    url: "get_data.php",
                                    data: {
                                        metricId: metricId, 
                                        action: "get_param_metrics"
                                    },
                                    type: "GET",
                                    async: true,
                                    dataType: 'json',
                                    success: function (data)
                                    {
                                        if(data.result === 'Ok')
                                        {
                                            $('#metricNameM').val(data.metricData.IdMetric);
                                            $('#shortDescriptionM').val(data.metricData.description_short);
                                            $('#fullDescriptionM').val(data.metricData.description);
                                            $('#resultTypeM').val(data.metricData.metricType);
                                            var updateFrequencyMs = parseInt(data.metricData.frequency);
                                            var updateFrequencyFromDb = updateFrequencyMs / 1000;
                                            var updateFrequencyHours = Math.floor(updateFrequencyFromDb / 3600);
                                            var updateFrequencyMinutes = Math.floor((updateFrequencyFromDb - (updateFrequencyHours*3600))/60);
                                            var updateFrequencySeconds = updateFrequencyFromDb - (updateFrequencyHours*3600) - (updateFrequencyMinutes*60);
                                            $('#updateFrequencyHourM').val(updateFrequencyHours + " h");
                                            $('#updateFrequencyMinM').val(updateFrequencyMinutes + " m");
                                            $('#updateFrequencySecM').val(updateFrequencySeconds + " s");
                                            $('#updateFrequencyM').val(data.metricData.frequency);
                                            $('#cityContextM').val(data.metricData.municipalityOption);
                                            $('#timeRangeM').val(data.metricData.timeRangeOption);
                                            $('#storingDataM').val(data.metricData.storingData);
                                            $('#dataSourceTypeM').val(data.metricData.queryType);
                                            var dataSources = data.metricData.dataSource.split("|");
                                            
                                            if(data.metricData.sameDataAlarmCount === null)
                                            {
                                                $('#sameDataAlarmCountM').val("Not active");
                                            }
                                            else
                                            {
                                                $('#sameDataAlarmCountM').val(data.metricData.sameDataAlarmCount);
                                            }
                                            
                                            if(data.metricData.oldDataEvalTime === null)
                                            {
                                                $('#oldDataEvalTimeSecM').val("Not active");
                                            }
                                            else
                                            {
                                                var oldDataEvalTimeMs = parseInt(data.metricData.oldDataEvalTime);
                                                var oldDataEvalTimeFromDb = oldDataEvalTimeMs / 1000;
                                                var oldDataEvalTimeHours = Math.floor(oldDataEvalTimeFromDb / 3600);
                                                var oldDataEvalTimeMinutes = Math.floor((oldDataEvalTimeFromDb - (oldDataEvalTimeHours*3600))/60);
                                                var oldDataEvalTimeSeconds = oldDataEvalTimeFromDb - (oldDataEvalTimeHours*3600) - (oldDataEvalTimeMinutes*60);
                                                $('#oldDataEvalTimeHourM').val(oldDataEvalTimeHours + " h");
                                                $('#oldDataEvalTimeMinM').val(oldDataEvalTimeMinutes + " m");
                                                $('#oldDataEvalTimeSecM').val(oldDataEvalTimeSeconds + " s");
                                            }
                                            
                                            updateOldDataEvalTimeM();
                                            
                                            $('#hasNegativeValuesM').val(data.metricData.hasNegativeValues);
                                            $('#processM').val(data.metricData.process);
                                            $('#metricId').val(metricId);
                                            
                                            switch(data.metricData.process)
                                            {
                                                case "DashboardProcess":
                                                    $('#processTypeM').append('<option value="JVNum1">Numeric</option>');
                                                    $('#processTypeM').append('<option value="JVPerc">Percent</option>');
                                                    $('#processTypeM').append('<option value="JVTable">Table</option>');
                                                    $('#processTypeM').append('<option value="API">API</option>');
                                                    $('#processTypeM').append('<option value="JVRidesAtaf">ATAF rides (specific)</option>');
                                                    $('#processTypeM').append('<option value="JVSceOnNodes">Sce on nodes (specific)</option>');
                                                    $('#processTypeM').append('<option value="jVPark">Parkings (specific)</option>');
                                                    $('#processTypeM').append('<option value="JVWifiOp">Wifi operatives (specific)</option>');
                                                    $('#processTypeM').append('<option value="JVSmartDs">SmartDs (specific)</option>');
                                                    $('#processTypeM').append('<option value="JVTwRet">Tweets/Retweets (specific)</option>');
                                                    $('#processTypeM').append('<option value="none">Not defined</option>');
                                                    break;

                                                case "HttpProcess":
                                                    $('#processTypeM').append('<option value="checkStatus">Web server status</option>');
                                                    //$('#processTypeM').append('<option value="responseTime">Web server response time</option>');
                                                    break;
                                            }
                                            $('#processTypeM').val(data.metricData.processType);
                                            
                                            if((data.metricData.process === 'HttpProcess')&&(data.metricData.processType === 'checkStatus'))
                                            {
                                                $('#editMetricQueryTabBtn a').html("Server");
                                                $('#editMetricQueryTabQueryRow').hide();
                                                $('#editMetricQueryTabServerRow').show();
                                                
                                                var httpCallJson = JSON.parse(data.metricData.query);
                                                $('#serverTestHttpMethodM').val(httpCallJson.method);
                                                $('#serverTestTokenM').val(httpCallJson.token);
                                                $('#serverTestUrlM').val(httpCallJson.url);
                                            }
                                            else
                                            {
                                                $('#editMetricQueryTabBtn a').html("Datasources & queries");
                                                $('#editMetricQueryTabServerRow').hide();
                                                $('#editMetricQueryTabQueryRow').show();
                                                if(dataSources.length > 1)
                                                {
                                                    $('#dataSourceM').val(dataSources[0]);
                                                    $('#dataSource2M').val(dataSources[1]);
                                                }
                                                else
                                                {
                                                    $('#dataSourceM').val(dataSources[0]);
                                                    $('#dataSource2M').val("none");
                                                }
                                                
                                                $('#dataSourceDescriptionM').val(data.metricData.source);
                                                $('#dataAreaM').val(data.metricData.area);

                                                if(data.metricData.query !== null)
                                                {
                                                    $('#queryM').val(data.metricData.query);
                                                }

                                                if(data.metricData.query2 !== null)
                                                {
                                                    $('#query2M').val(data.metricData.query2);
                                                }
                                            }
                                            
                                            $('#modalEditMetric').modal('show');
                                        }
                                        else
                                        {
                                            console.log("Error retrieving metric data");
                                            console.log(JSON.stringify(errorData));
                                            alert("Error retrieving metric data");
                                        }

                                    },
                                    error: function(errorData)
                                    {
                                        console.log("Error retrieving metric data");
                                        console.log(JSON.stringify(errorData));
                                        alert("Error retrieving metric data");
                                    }
                                });
                            });

                            $('#list_metrics tbody button.delDashBtn').off('click');
                            $('#list_metrics tbody button.delDashBtn').on('click', function () {
                                $('#metricIdToDel').val($(this).parents('tr').attr('data-uniqueid'));
                                $('#metricNameToDel').html($(this).parents('tr').find('td').eq(0).text());
                                $('#metricToDelActive').val($(this).parents('tr').find('td').eq(5).find('input').prop("checked"));
                                $('#modalDelMetric').modal('show');
                            });
                        }
                    });
                    
                    $('#delMetricBtn').off("click");
                    $('#delMetricBtn').click(function(){
                        $('#delMetricModalFooter').hide();
                        $.ajax({
                            url: "process-form.php",
                            data: {
                                deleteMetric: true,
                                metricId: $('#metricIdToDel').val()
                            },
                            type: "POST",
                            async: true,
                            success: function(data)
                            {
                                if(data === "Ok")
                                {
                                    $('#delMetricMsg').hide();
                                    $('#delMetricNameMsg').hide();
                                    $('#delMetricOkMsg').show();
                                    $('#delMetricOkIcon').show();

                                    //$('#list_metrics').bootstrapTable('removeByUniqueId', $('#metricIdToDel').val());
                                    buildMainTable(true);
                                    if($('#metricToDelActive').val() === "true")
                                    {
                                        $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                    }

                                    setTimeout(function(){
                                       $('#modalDelMetric').modal('hide');
                                       setTimeout(function(){
                                           $('#delMetricOkMsg').hide();
                                           $('#delMetricOkIcon').hide();
                                           $('#delMetricMsg').show();
                                           $('#delMetricNameMsg').show();
                                           $('#delMetricModalFooter').show();
                                       }, 300);
                                    }, 2000);
                                }
                                else
                                {
                                    console.log("Error deleting metric");
                                    console.log(data);
                                    $('#delMetricMsg').hide();
                                    $('#delMetricNameMsg').hide();
                                    $('#delMetricKoMsg').show();
                                    $('#delMetricKoIcon').show();
                                    setTimeout(function(){
                                       $('#modalDelMetric').modal('hide');
                                       setTimeout(function(){
                                           $('#delMetricKoMsg').hide();
                                           $('#delMetricKoIcon').hide();
                                           $('#delMetricMsg').show();
                                           $('#delMetricNameMsg').show();
                                           $('#delMetricModalFooter').show();
                                       }, 300);
                                    }, 2000);
                                }
                            },
                            error: function(errorData)
                            {
                                console.log("Error updating metric status");
                                console.log(errorData);
                                $('#delMetricMsg').hide();
                                $('#delMetricNameMsg').hide();
                                $('#delMetricKoMsg').show();
                                $('#delMetricKoIcon').show();
                                setTimeout(function(){
                                   $('#modalDelMetric').modal('hide');
                                   setTimeout(function(){
                                       $('#delMetricKoMsg').hide();
                                       $('#delMetricKoIcon').hide();
                                       $('#delMetricMsg').show();
                                       $('#delMetricNameMsg').show();
                                       $('#delMetricModalFooter').show();
                                   }, 300);
                                }, 2000);
                            }
                        });
                    });
                }
            });
        }
        
    });
</script>

<?php } else {
    include('../s4c-legacy-management/metrics.php');
}
?>