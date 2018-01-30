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
    include('../config.php');
    include('process-form.php');
    session_start();
    
    if(!isset($_SESSION['loggedRole']))
    {
        header("location: unauthorizedUser.php");
    }
    else if($_SESSION['loggedRole'] != "ToolAdmin")
    {
        header("location: unauthorizedUser.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard Management System</title>

        <!-- Bootstrap Core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">

        
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
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
    </head>
    <body class="guiPageBody">
        <div class="container-fluid">
            <?php include "sessionExpiringPopup.php" ?>
            <div class="row mainRow">
                <?php include "mainMenu.php" ?>
                <div class="col-xs-12 col-md-10" id="mainCnt">
                    <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            Dashboard Management System
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Metrics</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
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
        
        <!-- Modal di creazione di una metrica-->
        <div class="modal fade" id="modalAddMetric" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add new metric
                </div>
                <form id="addMetricForm" name="addMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">  
                <div id="addMetricModalBody" class="modal-body modalBody">
                    <ul id="addMetricModalTabs" class="nav nav-tabs nav-justified">
                        <li class="active"><a data-toggle="tab" href="#addMetricGeneralTab">General</a></li>
                        <li><a data-toggle="tab" href="#addMetricQueryTab">Datasources & queries</a></li>
                    </ul>

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
                                        <input type="text" class="modalInputTxt" name="updateFrequency" id="updateFrequency" value="60000" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Update frequency (ms)</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="resultType" id="resultType" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Result type</div>
                                </div>
                                
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSourceType" id="dataSourceType">
                                            <option value="none">Not specified</option>
                                            <option value="SQL">SQL</option>
                                            <option value="SPARQL">Sparql</option>
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
                                        <input type="text" class="modalInputTxt" name="process" id="process" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Process name</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="sameDataAlarmCount" id="sameDataAlarmCount" value="5" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Same data alarm count</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="oldDataEvalTime" id="oldDataEvalTime" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Old data evaluation time (ms)</div>
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
                                        <select class="modalInputTxt" name="processType" id="processType">
                                            <option value="JVNum1">Numeric (JVNum1)</option>
                                            <option value="JVPerc">Percent (JVPerc)</option>
                                            <option value="JVTable">Matrix (JVTable)</option>
                                            <option value="API">API</option>
                                            <option value="JVRidesAtaf">JVRidesAtaf (specific)</option>
                                            <option value="JVSceOnNodes">JVSceOnNodes (specific)</option>
                                            <option value="jVPark">jVPark (specific)</option>
                                            <option value="JVWifiOp">JVWifiOp (specific)</option>
                                            <option value="JVSmartDs">JVSmartDs (specific)</option>
                                            <option value="JVTwRet">JVTwRet (specific)</option>
                                            <option value="none">Not defined</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Process computation method</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="timeRange" id="timeRange">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
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

                        <!-- Query tab -->
                        <div id="addMetricQueryTab" class="tab-pane fade">
                            <div class="row">
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
                        <li class="active"><a data-toggle="tab" href="#editMetricGeneralTab">General</a></li>
                        <li><a data-toggle="tab" href="#editMetricQueryTab">Datasources & queries</a></li>
                    </ul>
                    
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
                                        <input type="text" class="modalInputTxt" name="updateFrequencyM" id="updateFrequencyM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Update frequency (ms)</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="resultTypeM" id="resultTypeM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Result type</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select class="modalInputTxt" name="dataSourceTypeM" id="dataSourceTypeM">
                                            <option value="none">Not specified</option>
                                            <option value="SQL">SQL</option>
                                            <option value="SPARQL">Sparql</option>
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
                                        <input type="text" class="modalInputTxt" name="processM" id="processM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Process name</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="sameDataAlarmCountM" id="sameDataAlarmCountM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Same data alarm count</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="oldDataEvalTimeM" id="oldDataEvalTimeM" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Old data evaluation time (ms)</div>
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
                                        <select class="modalInputTxt" name="processTypeM" id="processTypeM">
                                            <option value="JVNum1">Numeric (JVNum1)</option>
                                            <option value="JVPerc">Percent (JVPerc)</option>
                                            <option value="JVTable">Matrix (JVTable)</option>
                                            <option value="API">API</option>
                                            <option value="JVRidesAtaf">JVRidesAtaf (specific)</option>
                                            <option value="JVSceOnNodes">JVSceOnNodes (specific)</option>
                                            <option value="jVPark">jVPark (specific)</option>
                                            <option value="JVWifiOp">JVWifiOp (specific)</option>
                                            <option value="JVSmartDs">JVSmartDs (specific)</option>
                                            <option value="JVTwRet">JVTwRet (specific)</option>
                                            <option value="none">Not defined</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Process computation method</div>
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
                            <div class="row">
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
        
        $('#link_metric_mng .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #link_metric_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #link_metric_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
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
                    process: $('#process').val()
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
                                $('#addMetricOkMsg').hide();
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
                                $('#process').val("");
                                $('#addMetricModalTabs').show();
                                $('#modalAddMetric div.modalCell').show();
                                $('#addMetricModalFooter').show();
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
                    processM: $('#processM').val()
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
                                $('#metricNameM').val("");
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
                                $('#processM').val("");
                                $('#editMetricModalTabs').show();
                                $('#modalEditMetric div.modalCell').show();
                                $('#editMetricModalFooter').show();
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
                             cellStyle: function(value, row, index, field) {
                                var fontSize = "1em"; 
                                if($(window).width() < 992)
                                {
                                    fontSize = "0.9em";
                                } 
                                 
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "color": "rgba(51, 64, 69, 1)", 
                                            "font-size": fontSize,
                                            "font-weight": "bold",
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "color": "rgba(51, 64, 69, 1)", 
                                            "font-size": fontSize,
                                            "font-weight": "bold",
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        }, {
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
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                                return '<button type="button" class="editDashBtn">edit</button>';
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "white",
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
                                return '<button type="button" class="delDashBtn">del</button>';
                            },
                                    cellStyle: function(value, row, index, field) {
                                    if(index%2 !== 0)
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "rgb(230, 249, 255)",
                                                "border-top": "none"
                                            }
                                        };
                                    }
                                    else
                                    {
                                        return {
                                            classes: null,
                                            css: {
                                                "background-color": "white",
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
                        searchTimeOut: 250,
                        classes: "table table-hover table-no-bordered",
                        onPostBody: function()
                        {
                            if(tableFirstLoad)
                            {
                                //Caso di primo caricamento della tabella
                                tableFirstLoad = false;
                                var addMetricDiv = $('<div class="pull-right"><i id="link_add_metric" data-toggle="modal" data-target="#modalAddMetric" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                $('div.fixed-table-toolbar').append(addMetricDiv);
                                addMetricDiv.css("margin-top", "10px");
                                addMetricDiv.find('i.fa-plus-square').off('hover');
                                addMetricDiv.find('i.fa-plus-square').hover(function(){
                                    $(this).css('color', '#e37777');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', '#ffcc00');
                                    $(this).css('cursor', 'normal');
                                });

                                $('#list_metrics thead').css("background", "rgba(0, 162, 211, 1)");
                                $('#list_metrics thead').css("color", "white");
                                $('#list_metrics thead').css("font-size", "1em");
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
                                $(this).css('background-color', '#ffcc00');
                                $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                            }, 
                            function(){
                                $(this).css('background-color', 'rgb(69, 183, 175)');
                                $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                            });

                            $('#list_metrics button.delDashBtn').off('hover');
                            $('#list_metrics button.delDashBtn').hover(function(){
                                $(this).css('background-color', '#ffcc00');
                                $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
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
                                            $('#dataAreaM').val(data.metricData.area);
                                            $('#fullDescriptionM').val(data.metricData.description);
                                            $('#processTypeM').val(data.metricData.processType);
                                            $('#resultTypeM').val(data.metricData.metricType);
                                            $('#updateFrequencyM').val(data.metricData.frequency);
                                            $('#cityContextM').val(data.metricData.municipalityOption);
                                            $('#timeRangeM').val(data.metricData.timeRangeOption);
                                            $('#storingDataM').val(data.metricData.storingData);
                                            $('#dataSourceTypeM').val(data.metricData.queryType);
                                            var dataSources = data.metricData.dataSource.split("|");
                                            
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
                                            
                                            if(data.metricData.query !== null)
                                            {
                                                $('#queryM').val(data.metricData.query);
                                            }
                                            
                                            if(data.metricData.query2 !== null)
                                            {
                                                $('#query2M').val(data.metricData.query2);
                                            }
                                            
                                            $('#sameDataAlarmCountM').val(data.metricData.sameDataAlarmCount);
                                            $('#oldDataEvalTimeM').val(data.metricData.oldDataEvalTime);
                                            $('#hasNegativeValuesM').val(data.metricData.hasNegativeValues);
                                            $('#processM').val(data.metricData.process);
                                            $('#metricId').val(metricId);
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

                                    $('#list_metrics').bootstrapTable('removeByUniqueId', $('#metricIdToDel').val());
                                    if($('#metricToDelActive').val() === "true")
                                    {
                                        console.log("Vero");
                                        $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                    }
                                    else
                                    {
                                        console.log("Falso");
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