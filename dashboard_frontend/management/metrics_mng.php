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
?>

<html lang="en">
<head>    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
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
</head>

<body>
    <?php
        if(!isset($_SESSION['loggedRole']))
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
        else if($_SESSION['loggedRole'] != "ToolAdmin")
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
    ?>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Dashboard Management System</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <?php
                    if(isset($_SESSION['loggedUsername']))
                    {
                        echo '<li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>' . $_SESSION["loggedUsername"] . '</a></li>';
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li>
                        <a href="../management/dashboard_mng.php"> Dashboards management</a>
                    </li>
                    <?php
                        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li><a class="internalLink" href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
                           }
                           
                           if($_SESSION['loggedRole'] == "ToolAdmin")
                           {
                                echo '<li class="active"><a class="internalLink" href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li><a class="internalLink" href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                           }
                           
                           if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
                           {
                              echo '<li><a class="internalLink" href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools management</a></li>';
                           }
                        }
                    ?>
                    <li>
                        <a href="<?php echo $notificatorLink?>" target="blank"> Notificator</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row" style="margin-top: 50px">
                    <div class="col-xs-12 centerWithFlex mainPageTitleContainer">
                        Metrics
                    </div>
                </div>
                
                <!-- Tabella delle metriche-->
                <div class="row">
                    <div class="col-xs-12">
                        <table id="list_metrics"></table> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal di creazione di una metrica-->
    <div class="modal fade" id="modal-add-metric" tabindex="-1" role="dialog" aria-labelledby="addMetricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="addMetricModalLabel">Add new metric</h5>
            </div>
            <form id="addMetricForm" name="addMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">  
            <div id="addMetricModalBody" class="modal-body">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Metric name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="metricName" name="metricName" class="form-control" required>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Short description</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="shortDescription" required> 
                            </div>
                        </div>
                       <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data area</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataArea" id="dataArea" required>
                                    <option>Mobilità</option>
                                    <option>Intrattenimento</option>
                                    <option>Statistiche</option>
                                    <option>Social Network</option>
                                    <option>Meteo</option>
                                    <option>Network</option>
                                    <option>Altro</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="addUserFormSubfieldContainer">Full description</div>
                            <div class="addUserFormSubfieldContainer">
                               <textarea class="form-control textarea-metric" rows="2" name="fullDescription" required></textarea> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Process computation method</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="processType" id="processType">
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
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Result type</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="resultType" id="resultType" required>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Update frequency (ms)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="updateFrequency" required> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">City context</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="cityContext" id="cityContext">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Time range</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="timeRange" id="timeRange">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Storing data</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="storingData" id="storingData">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source type</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataSourceType" id="dataSourceType">
                                    <option value="NULL">Not specified</option>
                                    <option value="SQL">SQL</option>
                                    <option value="SPARQL">Sparql</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataSource" id="dataSource">
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
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source description</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="dataSourceDescription" required> 
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="addUserFormSubfieldContainer">Query</div>
                            <div class="addUserFormSubfieldContainer">
                               <textarea class="form-control" rows="2" name="query" required></textarea> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Same data alarm count</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="sameDataAlarmCount"> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Old data evaluation time (ms)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="oldDataEvalTime"> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Negative values</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="hasNegativeValues" id="hasNegativeValues">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Process name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="process">
                            </div> 
                        </div>
                    </div>
            </div>
            <div id="addmetricModalFooter" class="modal-footer">
              <button type="button" id="addNewUserCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" id="add_new_metric" name="add_new_metric" class="btn btn-primary internalLink">Confirm</button>
            </div>
            </form>   
          </div>
        </div>
    </div>
    
    <!-- Modal di modifica di una metrica-->
    <div class="modal fade" id="modalEditMetric" tabindex="-1" role="dialog" aria-labelledby="editMetricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="editMetricModalLabel">Edit metric</h5>
            </div>
            <form id="editMetricForm" name="editMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">  
            <input type="hidden" id="metricId" name="metricId" />    
            <div id="editMetricModalBody" class="modal-body">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Metric name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="metricNameM" name="metricNameM" class="form-control" required>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Short description</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" id="shortDescriptionM" name="shortDescriptionM" required> 
                            </div>
                        </div>
                       <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data area</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataAreaM" id="dataAreaM" required>
                                    <option>Mobilità</option>
                                    <option>Intrattenimento</option>
                                    <option>Statistiche</option>
                                    <option>Social Network</option>
                                    <option>Meteo</option>
                                    <option>Network</option>
                                    <option>Altro</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="addUserFormSubfieldContainer">Full description</div>
                            <div class="addUserFormSubfieldContainer">
                               <textarea class="form-control textarea-metric" rows="2" name="fullDescriptionM" id="fullDescriptionM" required></textarea> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Process computation method</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="processTypeM" id="processTypeM">
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
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Result type</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="resultTypeM" id="resultTypeM" required>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Update frequency (ms)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="updateFrequencyM" id="updateFrequencyM" required> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">City context</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="cityContextM" id="cityContextM">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Time range</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="timeRangeM" id="timeRangeM">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Storing data</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="storingDataM" id="storingDataM">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source type</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataSourceTypeM" id="dataSourceTypeM">
                                    <option value="none">Not specified</option>
                                    <option value="SQL">SQL</option>
                                    <option value="SPARQL">Sparql</option>
                                </select>
                            </div> 
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="dataSourceM" id="dataSourceM">
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
                        </div>
                        <div class="col-xs-4">
                            <div class="addUserFormSubfieldContainer">Data source description</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="dataSourceDescriptionM" id="dataSourceDescriptionM" required> 
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="addUserFormSubfieldContainer">Query</div>
                            <div class="addUserFormSubfieldContainer">
                               <textarea class="form-control" rows="2" name="queryM" id="queryM"></textarea> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Same data alarm count</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="sameDataAlarmCountM" id="sameDataAlarmCountM"> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Old data evaluation time (ms)</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="oldDataEvalTimeM" id="oldDataEvalTimeM"> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Negative values</div>
                            <div class="addUserFormSubfieldContainer">
                                <select class="form-control" name="hasNegativeValuesM" id="hasNegativeValuesM">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select> 
                            </div> 
                        </div>
                        <div class="col-xs-3">
                            <div class="addUserFormSubfieldContainer">Process name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" class="form-control" name="processM" id="processM">
                            </div> 
                        </div>
                    </div>
            </div>
            <div id="addmetricModalFooter" class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" id="modify_metric" name="modify_metric" class="btn btn-primary internalLink">Confirm</button>
            </div>
            </form>   
          </div>
        </div>
    </div>
    
    <!-- Modal di conferma cancellazione di una metrica-->
    <div class="modal fade" id="modalDelMetric" tabindex="-1" role="dialog" aria-labelledby="delMetricModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header centerWithFlex">
              <h5 class="modal-title" id="delMetricModalLabel">Delete metric</h5>
            </div>
            <form id="delMetricForm" name="delMetricForm" role="form" method="post" action="process-form.php" data-toggle="validator">  
                <input type="hidden" id="metricIdToDel" name="metricIdToDel" />    
                <div id="delMetricModalBody" class="modal-body">
                    <div class="row" id="delMetricMsg">
                        <div class="col-xs-12 centerWithFlex">
                            Are you sure you want to delete the following metric?
                        </div>
                    </div>
                    <div class="row" id="delMetricNameMsg">
                        <div class="col-xs-12 centerWithFlex" id="metricNameToDel"></div>
                    </div>
                    <div class="row" id="delMetricOkMsg">
                        <div class="col-xs-12 centerWithFlex" id="succesMsg">Metric deleted successfully</div>
                    </div>
                    <div class="row" id="delMetricOkIcon">
                        <div class="col-xs-12 centerWithFlex" id="succesIcon"><i class="fa fa-smile-o" style="font-size:48px"></i></div>
                    </div>
                    <div class="row" id="delMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex" id="errorMsg">Error deleting metric</div>
                    </div>
                    <div class="row" id="delMetricKoMsg">
                        <div class="col-xs-12 centerWithFlex" id="errorIcon"><div class="col-xs-12 centerWithFlex" id="succesIcon"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>
                    </div>
                </div>
                <div id="delMetricModalFooter" class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="button" id="delMetricBtn" name="delMetricBtn" class="btn btn-primary">Confirm</button>
                </div>
            </form>   
          </div>
        </div>
    </div>


<script type='text/javascript'>
    $(document).ready(function () 
    {
        var tableFirstLoad = true;
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
                        }
                    }, {
                        field: 'description_short',
                        title: 'Description',
                        sortable: true,
                        valign: "middle",
                        align: "center",
                        halign: "center",
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
                        }
                    }, {
                        field: 'metricType',
                        title: 'Type',
                        sortable: true,
                        valign: "middle",
                        align: "center",
                        halign: "center",
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
                        }
                    },
                    {
                        field: 'source',
                        title: 'Source',
                        sortable: true,
                        valign: "middle",
                        align: "center",
                        halign: "center",
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
                        }
                    },
                    {
                        field: 'dataSource',
                        title: 'Data source',
                        sortable: true,
                        valign: "middle",
                        align: "center",
                        halign: "center",
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
                        }
                    },
                    {
                        field: 'status',
                        title: "Status",
                        align: "center",
                        valign: "middle",
                        align: "center",
                        halign: "center",
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
                            
                        }
                    },
                    {
                        title: "Edit",
                        align: "center",
                        valign: "middle",
                        align: "center",
                        halign: "center",
                        formatter: function(value, row, index)
                        {
                            return '<span class="glyphicon glyphicon-cog"></span>'; 
                        }
                    },
                    {
                        title: "Delete",
                        align: "center",
                        valign: "middle",
                        align: "center",
                        halign: "center",
                        formatter: function(value, row, index)
                        {
                            return '<span class="glyphicon glyphicon-remove"></span>'; 
                        }
                 }],
                    data: data,
                    search: true,
                    pagination: true,
                    pageSize: 10,
                    locale: 'en-US',
                    searchAlign: 'left',
                    uniqueId: "id",
                    striped: true,
                    onPostBody: function()
                    {
                        if(tableFirstLoad)
                        {
                            //Caso di primo caricamento della tabella
                            tableFirstLoad = false;
                            console.log("Primo caricamento");
                            var addMetricDiv = $('<div class="pull-right"><i id="link_add_metric" data-toggle="modal" data-target="#modal-add-metric" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                            $('div.fixed-table-toolbar').append(addMetricDiv);
                            addMetricDiv.css("margin-top", "10px");
                            addMetricDiv.find('i.fa-plus-square').off('hover');
                            addMetricDiv.find('i.fa-plus-square').hover(function(){
                                $(this).css('color', 'red');
                                $(this).css('cursor', 'pointer');
                            }, 
                            function(){
                                $(this).css('color', '#ffcc00');
                                $(this).css('cursor', 'normal');
                            });
                        }
                        else
                        {
                            //Casi di cambio pagina
                            console.log("Cambio pagina");
                        }
                        
                        //Istruzioni da eseguire comunque
                        $('#list_metrics span.glyphicon-cog').css('color', '#337ab7');
                        $('#list_metrics span.glyphicon-cog').css('font-size', '20px');
                        
                        $('#list_metrics span.glyphicon-cog').off('hover');
                        $('#list_metrics span.glyphicon-cog').hover(function(){
                            $(this).css('color', '#ffcc00');
                            $(this).css('cursor', 'pointer');
                        }, 
                        function(){
                            $(this).css('color', '#337ab7');
                            $(this).css('cursor', 'normal');
                        });
                        
                        $('#list_metrics span.glyphicon-remove').css('color', 'red');
                        $('#list_metrics span.glyphicon-remove').css('font-size', '20px');
                        
                        $('#list_metrics span.glyphicon-remove').off('hover');
                        $('#list_metrics span.glyphicon-remove').hover(function(){
                            $(this).css('color', '#ffcc00');
                            $(this).css('cursor', 'pointer');
                        }, 
                        function(){
                            $(this).css('color', 'red');
                            $(this).css('cursor', 'normal');
                        });
                        
                        $('#list_metrics input.changeMetricStatus').bootstrapToggle({
                            on: "On",
                            off: "Off",
                            onstyle: "primary",
                            offstyle: "default",
                            size: "small"
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
                        
                        $('#list_metrics tbody span.glyphicon-cog').off('click');
                        $('#list_metrics tbody span.glyphicon-cog').click(function() 
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
                                        $('#dataSourceM').val(data.metricData.dataSource);
                                        $('#dataSourceDescriptionM').val(data.metricData.source);
                                        if(data.metricData.query !== null)
                                        {
                                            $('#queryM').val(data.metricData.query);
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
                        
                        $('#list_metrics tbody span.glyphicon-remove').off('click');
                        $('#list_metrics tbody span.glyphicon-remove').on('click', function () {
                            $('#metricIdToDel').val($(this).parents('tr').attr('data-uniqueid'));
                            $('#metricNameToDel').html($(this).parents('tr').find('td').eq(0).text());
                            $('#modalDelMetric').modal('show');
                        });
                    }
                });
                
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
                
        

        
                
                    /*var name_metric_m = $(this).parent().parent().find('.name_met').text();
                    $("#modify-descrizioneDataSource2").val('');

                    $.ajax({
                        url: "get_data.php",
                        data: {metric_to_modify: name_metric_m, action: "get_param_metrics"},
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) {
                            $('#dataSourceMetric2').empty();
                            $("#modify-nameMetric").val(data['id_metric']);
                            $("#modify-descriptionMetric").text(data['descritpion_metric']);
                            $("#modify-descriptionShortMetric").val(data['description_short_metric']);
                            $("#modify-areaMetric").val(data['area_metric']);
                            $("#modify-sourceMetric").val(data['source_metric']);
                            $("#modify-typeMetric").val(data['metricType_metric']);
                            $("#modify-frequencyMetric").val(data['frequency_metric']);
                            if (data['municipalityOption_metric'] == 1) {
                                $("#modify-contextMetric").prop('checked', true);
                            } else {
                                $("#modify-contextMetric").prop('checked', false);
                            }
                            ;
                            if (data['timeRangeOption_metric'] == 1) {
                                $("#modify-timeRangeMetric").prop('checked', true);
                            } else {
                                $("#modify-timeRangeMetric").prop('checked', false);
                            }
                            ;
                            if (data['storingData_metric'] == 1) {
                                $("#modify-storingDataMetric").prop('checked', true);
                            } else {
                                $("#modify-storingDataMetric").prop('checked', false);
                            }
                            ;
                            $("#modify-queryTypeMetric").val(data['queryType_metric']);
                            var query_ds = data['query_metric'];
                            var array_query = query_ds.split("|");
                            $("#modify-queryMetric").val(array_query[0]);
                            $("#modify-queryMetric2").val(array_query[1]);

                            if (data['processType_metric'] == "JVPerc") {
                                $('#row2-modify-datasources2').show();
                                $('#row-modify-query2').show();
                                $('#row2-modify-decription-datasources2').show();
                            } else {
                                $('#row2-modify-datasources2').hide();
                                $('#row-modify-query2').hide();
                                $('#row2-modify-decription-datasources2').hide();
                                $('#dataSourceMetric2').empty();
                            }

                            if (data['processType_metric'] == "API") {
                                $('#row-mod-des-datasource').hide();
                                $('#label-query-url').text("Url");
                                $('#button_query_test').hide();
                            } else {
                                $('#row-mod-des-datasource').show();
                                $('#label-query-url').text("Query");
                                $('#button_query_test').show();
                            }

                            $("#modify-queryMetric2").text(data['query2_metric']);
                            $("#modify-processType").val(data['processType_metric']);

                            //valore default                            
                            var str0 = $('#modify-dataSourceMetric option:selected').text();
                            var ds_text0 = "";
                            var ds_text02 = "";
                            var stringa_ds = data['dataSource_metric'];
                            var array_stringa = stringa_ds.split("|");
                            $("#modify-dataSourceMetric").val(array_stringa[0]);
                            $("#modify-datasourcesMetric2").val(array_stringa[1]);
                            //caricmaneto dati nel descrizione datasources
                            //
                            for (var j = 0; j < array_dataSources.length; j++) {
                                if (array_dataSources[j]['id'] == array_stringa[0]) {
                                    ds_url = array_dataSources[j]['url'];
                                    ds_database = array_dataSources[j]['database'];
                                    ds_username = array_dataSources[j]['userName'];
                                    ds_password = array_dataSources[j]['passWord'];
                                    ds_databaseType = array_dataSources[j]['databaseType'];
                                    ds_text0 += "url: " + array_dataSources[j]['url'] + ".\n";
                                    ds_text0 += "database: " + array_dataSources[j]['database'] + ".\n";
                                    ds_text0 += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                                    //$('#descrizioneDataSource').val("URL: " + array_dataSources[j]['url']);
                                    ds_text0 += "\n";
                                    $('#modify-descrizioneDataSource').val(ds_text0);
                                }
                            }
                            //
                            for (var a = 0; a < array_dataSources.length; a++) {
                                if (array_dataSources[a]['id'] == array_stringa[1]) {
                                    ds_urlQ2 = array_dataSources[a]['url'];
                                    ds_databaseQ2 = array_dataSources[a]['database'];
                                    ds_usernameQ2 = array_dataSources[a]['userName'];
                                    ds_passwordQ2 = array_dataSources[a]['passWord'];
                                    ds_databaseTypeQ2 = array_dataSources[a]['databaseType'];
                                    ds_text02 += "url: " + array_dataSources[a]['url'] + ".\n";
                                    ds_text02 += "database: " + array_dataSources[a]['database'] + ".\n";
                                    ds_text02 += "database Type: " + array_dataSources[a]['databaseType'] + ".\n";
                                    //$('#descrizioneDataSource').val("URL: " + array_dataSources[j]['url']);
                                    ds_text02 += "\n";
                                    $('#modify-descrizioneDataSource2').val(ds_text02);
                                }
                            }


                            if (str0 == "") {
                                $("#modify-dataSourceMetric").append('<option>' + data['dataSource_metric'] + '</option>');
                                $("#modify-dataSourceMetric").val(data['dataSource_metric']);
                            }
                        }
                    });*/

                //se il datasourceMetric in AGGIUNGI metrica è uguale ad API
                /*$('#processType').change(function () {
                    if ($('#processType').val() == "API") {
                        $('#row-des-datasource').hide();
                        $('#label-query-url').text("Url");
                        $('#button_query_test').hide();
                        $('#row2-datasources2').hide();
                        $('#row2-descrizioneDataSource2').hide();
                        $('#row-query2').hide();
                        $('#dataSourceMetric2').empty();
                    } else {
                        $('#button_query_test').show();
                        $('#label-query-url').text("Query");
                        $('#row-des-datasource').show();
                    }
                });*/

                //se il datasourceMetric in MODIFICA metrica è uguale ad API
                /*$('#modify-processType').change(function () {
                    if ($('#modify-processType').val() == "API") {
                        $('#row-mod-des-datasource').hide();
                        $('#label-query-url-m').text("Url");
                        $('#button_query_test_M').hide();
                        $('#row2-modify-datasources2').hide();
                        $('#row2-modify-decription-datasources2').hide();
                        $('#row-modify-query2').hide();
                        $('#modify-datasourceMetric2').empty();
                        $('#modify-descrizioneDataSources2').empty();
                    } else {
                        $('#row-mod-des-datasource').show();
                        $('#label-query-url-m').text("Query");
                        $('#button_query_test_M').show()
                    }
                });*/

                //visualizzare i dati del datasource
                //Aggiunta metrica
                /*$('#dataSourceMetric').change(function () {
                    var str = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    str = $('#dataSourceMetric option:selected').text();
                    var ds_text = "";
                    for (var j = 0; j < array_dataSources.length; j++) {
                        if (array_dataSources[j]['id'] == str) {
                            ds_url = array_dataSources[j]['url'];
                            ds_database = array_dataSources[j]['database'];
                            ds_username = array_dataSources[j]['userName'];
                            ds_password = array_dataSources[j]['passWord'];
                            ds_databaseType = array_dataSources[j]['databaseType'];
                            ds_text += "url: " + array_dataSources[j]['url'] + ".\n";
                            ds_text += "database: " + array_dataSources[j]['database'] + ".\n";
                            ds_text += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                            $('#descrizioneDataSource').val(ds_text);
                        }
                    }
                    ;                
                });*/


                //visualizza dati del datasources su query2 (aggiunta metrica)
                /*$('#dataSourceMetric2').change(function () {
                    var strQ2 = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    strQ2 = $('#dataSourceMetric2 option:selected').text();
                    var ds_textQ2 = "";
                    for (var j = 0; j < array_dataSources.length; j++) {
                        if (array_dataSources[j]['id'] == strQ2) {
                            ds_urlQ2 = array_dataSources[j]['url'];
                            ds_databaseQ2 = array_dataSources[j]['database'];
                            ds_usernameQ2 = array_dataSources[j]['userName'];
                            ds_passwordQ2 = array_dataSources[j]['passWord'];
                            ds_databaseTypeQ2 = array_dataSources[j]['databaseType'];
                            ds_textQ2 += "url: " + array_dataSources[j]['url'] + ".\n";
                            ds_textQ2 += "database: " + array_dataSources[j]['database'] + ".\n";
                            ds_textQ2 += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                            $('#descrizioneDataSource2').val(ds_textQ2);
                        }
                    }
                    ;
                });*/

                //modifca metrica
                /*$('#modify-dataSourceMetric').change(function () {
                    var strM = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    strM = $('#modify-dataSourceMetric option:selected').text();
                    var ds_textM = "";
                    for (var j = 0; j < array_dataSources.length; j++) {
                        if (array_dataSources[j]['id'] == strM) {
                            ds_url = array_dataSources[j]['url'];
                            ds_database = array_dataSources[j]['database'];
                            ds_username = array_dataSources[j]['userName'];
                            ds_password = array_dataSources[j]['passWord'];
                            ds_databaseType = array_dataSources[j]['databaseType'];
                            ds_textM += "url: " + array_dataSources[j]['url'] + ".\n";
                            ds_textM += "database: " + array_dataSources[j]['database'] + ".\n";
                            ds_textM += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                            $('#modify-descrizioneDataSource').val("Informazioni: " + ds_textM);
                        }
                    }                
                });*/

                //visualizza dati del datasources su query2 (modifica metrica)
                /*$('#modify-datasourcesMetric2').change(function () {
                    var strMQ2 = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    strMQ2 = $('#modify-datasourcesMetric2 option:selected').text();
                    var ds_textMQ2 = "";
                    for (var j = 0; j < array_dataSources.length; j++) {
                        if (array_dataSources[j]['id'] == strMQ2) {
                            ds_urlQ2 = array_dataSources[j]['url'];
                            ds_databaseQ2 = array_dataSources[j]['database'];
                            ds_usernameQ2 = array_dataSources[j]['userName'];
                            ds_passwordQ2 = array_dataSources[j]['passWord'];
                            ds_databaseTypeQ2 = array_dataSources[j]['databaseType'];
                            ds_textMQ2 += "url: " + array_dataSources[j]['url'] + ".\n";
                            ds_textMQ2 += "database: " + array_dataSources[j]['database'] + ".\n";
                            ds_textMQ2 += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                            $('#modify-descrizioneDataSource2').val("Informazioni: " + ds_textMQ2);
                        }
                    }                 
                });*/

                //modifca dello stato
                /*$('.icon-status-metric').on('click', function () {
                    var name_metric_status = $(this).parent().parent().find('.name_met').text();
                    $("#button_modify_metric_status").attr('value', name_metric_status);

                });*/


                

                //test della query su test_query.php
                /*$('#button_query_test').on('click', function () {
                    var query_selezionata = $('#queryMetric').val();
                    var mod_acquisizione = $('#queryTypeMetric').val();
                    var url_datasource = ds_url;
                    var database_datasource = ds_database;
                    var user_datasource = ds_username;
                    var pass_datasource = ds_password;
                    var dataType_datasource = ds_databaseType;
                    //Dati ricavati da "ds_text"
                    $('#button_query_test').attr('value', query_selezionata);
                    $.ajax({
                        url: "test_query.php",
                        async: true,
                        type: "GET",
                        data: {
                            valore_query: query_selezionata,
                            tipo_acquisizione: mod_acquisizione,
                            urlDS: url_datasource,
                            usernameDS: user_datasource,
                            databaseDS: database_datasource,
                            passwordDS: pass_datasource,
                            databaseTypeDS: dataType_datasource
                        },
                        success: function (data) {
                            console.log('Test sulla query');
                            alert(data);
                        }
                    });
                });*/

                //test della query modifica su test_query.php
                /*$('#button_query_test_M').on('click', function () {
                    var query_selezionata = $('#modify-queryMetric').val();
                    var mod_acquisizione = $('#modify-queryTypeMetric').val();
                    var url_datasource = ds_url;
                    var database_datasource = ds_database;
                    var user_datasource = ds_username;
                    var pass_datasource = ds_password;
                    var dataType_datasource = ds_databaseType;
                    console.log("Query: " + query_selezionata);
                    console.log("Mod Acquisizione: " + mod_acquisizione);
                    console.log("URL: " + url_datasource);
                    console.log("DataBase: " + database_datasource);
                    console.log("username: " + user_datasource);
                    console.log("password: " + pass_datasource);
                    console.log("DataType: " + dataType_datasource);
                    $('#button_query_test_M').attr('value', query_selezionata);
                    $.ajax({
                        url: "test_query.php",
                        async: true,
                        type: "GET",
                        data: {
                            valore_query: query_selezionata,
                            tipo_acquisizione: mod_acquisizione,
                            urlDS: url_datasource,
                            usernameDS: user_datasource,
                            databaseDS: database_datasource,
                            passwordDS: pass_datasource,
                            databaseTypeDS: dataType_datasource
                        },
                        success: function (data) {
                            console.log('operazione riuscita');
                            alert(data);
                        }

                    });

                });*/

                //test della della query2 sul menù di aggiunta
                /*$('#button_query_test2').on('click', function () {
                    var query_selezionata = $('#queryMetric2').val();
                    var mod_acquisizione = $('#queryTypeMetric').val();
                    var url_datasource = ds_urlQ2;
                    var database_datasource = ds_databaseQ2;
                    var user_datasource = ds_usernameQ2;
                    var pass_datasource = ds_passwordQ2;
                    var dataType_datasource = ds_databaseTypeQ2;
                    console.log("Query: " + query_selezionata);
                    console.log("Mod Acquisizione: " + mod_acquisizione);
                    console.log("URL: " + url_datasource);
                    console.log("DataBase: " + database_datasource);
                    console.log("username: " + user_datasource);
                    console.log("password: " + pass_datasource);
                    console.log("DataType: " + dataType_datasource);
                    $('#button_query_test2').attr('value', query_selezionata);
                    $.ajax({
                        url: "test_query.php",
                        async: true,
                        type: "GET",
                        data: {
                            valore_query: query_selezionata,
                            tipo_acquisizione: mod_acquisizione,
                            urlDS: url_datasource,
                            usernameDS: user_datasource,
                            databaseDS: database_datasource,
                            passwordDS: pass_datasource,
                            databaseTypeDS: dataType_datasource
                        },
                        success: function (data) {
                            console.log('operazione riuscita');
                            alert(data);
                        }

                    });

                });*/

                //test della query2 nel menù di modifica
                /*$('#button_query2_test_M').on('click', function () {
                    var query_selezionata = $('#modify-queryMetric2').val();
                    var mod_acquisizione = $('#modify-queryTypeMetric').val();
                    var url_datasource = ds_urlQ2;
                    var database_datasource = ds_databaseQ2;
                    var user_datasource = ds_usernameQ2;
                    var pass_datasource = ds_passwordQ2;
                    var dataType_datasource = ds_databaseTypeQ2;
                    console.log("Query: " + query_selezionata);
                    console.log("Mod Acquisizione: " + mod_acquisizione);
                    console.log("URL: " + url_datasource);
                    console.log("DataBase: " + database_datasource);
                    console.log("username: " + user_datasource);
                    console.log("password: " + pass_datasource);
                    console.log("DataType: " + dataType_datasource);
                    $('#button_query2_test_M').attr('value', query_selezionata);
                    $.ajax({
                        url: "test_query.php",
                        async: true,
                        type: "GET",
                        data: {
                            valore_query: query_selezionata,
                            tipo_acquisizione: mod_acquisizione,
                            urlDS: url_datasource,
                            usernameDS: user_datasource,
                            databaseDS: database_datasource,
                            passwordDS: pass_datasource,
                            databaseTypeDS: dataType_datasource
                        },
                        success: function (data) {
                            console.log('operazione riuscita');
                            alert(data);
                        }

                    });

                });*/
 

                //rendere visibile query2
                /*$('#processType').change(function () {
                    var verificaQ2 = $('#processType option:selected').text();
                    if (verificaQ2 == 'JVPerc') {
                        $('#row-query2').show();
                        $('#row2-datasources2').show();
                        $('#row2-descrizioneDataSource2').show();

                    }
                });*/

                /*$('#modify-processType').change(function () {
                    var verificaQ2 = $('#modify-processType option:selected').text();
                    if (verificaQ2 == 'JVPerc') {
                        $('#row-modify-query2').show();
                        $('#row2-modify-datasources2').show();
                        $('#row2-modify-decription-datasources2').show();
                    }
                });*/

                //test query da modificare
                /*$('#button_query_test_M').on('click', function () {
                    var query_selezionata = $('#modify-queryMetric').val();
                    $('button_query_test_M').attr('value', query_selezionata);
                });*/


                //descrizione delle metrice sul menù d'associazione
                /*$('#list-Metric').change(function () {
                    var strMetric = "";
                    var textMetric = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    strMetric = $('#list-Metric option:selected').text();
                    for (var i = 0; i < array_metrics.length; i++) {
                        if (strMetric == array_metrics[i]['id']) {
                            textMetric = "Description metric: " + array_metrics[i]['desc'] + "\n";
                            textMetric += "Status: " + array_metrics[i]['status'] + "\n";
                            textMetric += "Type metric: " + array_metrics[i]['type'] + "\n";
                            textMetric += "Type Query: " + array_metrics[i]['queryType'] + "\n";
                            textMetric += "Process Java: " + array_metrics[i]['processType'] + "\n";
                            textMetric += "Data Source: " + array_metrics[i]['dataSource'] + "\n";

                        }
                    }
                    $('#infometrics').val(textMetric);
                });*/
    });
</script>
</body>
</html>