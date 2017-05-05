<?php

/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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

    include('process-form.php'); // Includes Login Script
?>

<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <!--<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>-->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>-->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
</head>

<body>
    <?php
        if(!isset($_SESSION['isAdmin']))
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
        else if(($_SESSION['isAdmin'] != 1)&&($_SESSION['isAdmin'] != 2))
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
                <a class="navbar-brand" href="index.html">Metrics Management</a>
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
                        <a href="../management/dashboard_mng.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard Builder</a>
                    </li>
                    <?php
                        if(isset($_SESSION['isAdmin']))
                        {
                            if(($_SESSION['isAdmin'] == 1) || ($_SESSION['isAdmin'] == 2))
                            {
                                echo '<li class="active"><a href="../management/metrics_mng.php" id="link_metric_mng"><i class="fa fa-fw fa-dashboard"></i> Metrics</a></li>';
                                echo '<li><a href="../management/widgets_mng.php" id="link_widgets_mng"><i class="fa fa-fw fa-dashboard"></i> Widgets</a></li>';
                                echo '<li><a href="../management/dataSources_mng.php" id="link_sources_mng"><i class="fa fa-fw fa-dashboard"></i>Sources</a></li>';
                                echo '<li><a href="../management/dashboard_register.php" id="link_user_register"><i class="fa fa-fw fa-dashboard"></i> Users</a></li>'; 
                            }
                        }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <br/>Metrics Overview
                        </h1>
                            <?php
                                if(isset($_SESSION['isAdmin']))
                                {
                                    if(($_SESSION['isAdmin'] == 1) || ($_SESSION['isAdmin'] == 2))
                                    {
                                        echo '<nav id="modify-bar-dashboard" class="navbar navbar-default">';
                                        echo '<div class="container-fluid">';
                                        echo '<div class="navbar-header">';
                                        echo '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">';
                                        echo '<span class="sr-only">Toggle navigation</span>';
                                        echo '<span class="icon-bar"></span>';
                                        echo '<span class="icon-bar"></span>';
                                        echo '<span class="icon-bar"></span>';
                                        echo '</button>';
                                        echo'</div>';
                                        echo '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
                                        echo '<ul class="nav navbar-nav">';
                                        echo '<li class="active"><a id="link_add_metric" href="#" data-toggle="modal" data-target="#modal-add-metric"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Metric <span class="sr-only">(current)</span></a></li>';                           
                                        echo '<li><a id ="link_help" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></li>';
                                        echo '</ul>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</nav>';
                                    }
                                }
                            ?>
                    </div>
                </div>
                
                
                <!-- tabella -->
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> Metrics</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="list_metrics" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Area</th>
                                            <th>Source</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!--fine tabella -->
            </div>
        </div>
    </div>
    <!-- Modal di creazione di una metrica-->
    <div class="modal fade" id="modal-add-metric" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="dialog-metric">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new Metric</h4>
                </div>
                <div class="modal-body">
                    <!--<form id="new_metric" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">-->
                    <form id="form-new-metric" class="form-horizontal" name="add_new_metric" role="form" method="post" action="" data-toggle="validator">
                        <div class="tabbable"> <!-- Only required for left/right tabs -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1" data-toggle="tab">General</a></li>
                                <li><a href="#tab2" data-toggle="tab">Data acquisition</a></li>
                                <li><a href="#tab3" data-toggle="tab">Threshold alarm</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Name</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="nameMetric" required>                                 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Description</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control textarea-metric" rows="3" name="descriptionMetric"></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Short Description</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="descriptionShortMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Data Area</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="areaMetric" id="areaMetric">
                                                    <option>Mobilità</option>
                                                    <option>Intrattenimento</option>
                                                    <option>Statistiche</option>
                                                    <option>Social Network</option>
                                                    <option>Meteo</option>
                                                    <option>Network</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Source</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sourceMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Type Results</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="typeMetric" id="metricTypeMetric">
                                                    <option>Intero</option>
                                                    <option>Map</option>
                                                    <option>Float</option>
                                                    <option>Testuale</option>
                                                    <option>Percentuale</option>
                                                    <option>Percentuale/50</option>
                                                    <option>Percentuale/285</option>
                                                    <option>Percentuale/424</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Frequency of calculation (milliseconds)</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="frequencyMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Context</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" class="checkStato" name="contextMetric" value="1" id="contextMetric"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Time Range</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" class="checkStato" name="timeRangeMetric" value="1" id="timeRangeMetric"/>
                                            </div>
                                        </div>     
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Storing Data</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" class="checkStato" name="storingDataMetric" value="1" id="storingDataMetric"/>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Acquisition Modality</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="queryTypeMetric" id="queryTypeMetric">
                                                    <option>null</option>
                                                    <option>SQL</option>    
                                                    <option>SPARQL</option>
                                                </select>                                 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <div class="form-group"> 
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Process Java String</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="processTypeMetric" id="processType">
                                                    <option>API</option>
                                                    <option>JVPerc</option>
                                                    <option>JVNum1</option>
                                                    <option>JVRidesAtaf</option>
                                                    <option>jVPark</option>
                                                    <option>JVSceOnNodes</option>
                                                    <option>JVSmartDs</option>
                                                    <option>JVTwRet</option>
                                                    <option>JVWifiOp</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Data Source</label>
                                            <div class="col-md-6">
                                                <!--
                                                <input type="text" class="form-control" name="dataSourceMetric" id="dataSourceMetric">
                                                -->
                                                <select class="form-control" name="dataSourceMetric" id="dataSourceMetric">
                                                    <option>API</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row"  id="row-des-datasource">
                                            <label for="#" class="col-md-4 control-label">Data Source Description</label>
                                            <div class="col-md-6">
                                                <textarea id="descrizioneDataSource" class="form-control textarea-metric" rows="3" readonly></textarea> 
                                            </div>                                      
                                        </div>       
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label" id="label-query-url">Query</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control textarea-metric" name="queryMetric" id="queryMetric" rows="3"></textarea>
                                                <button type="button" id="button_query_test" name="test_query" class="btn btn-primary test_button_query" value="">Test Query</button>
                                            </div>
                                        </div>
                                        <!--dati visibili soolo con JVPerc settato -->
                                        <div class="row" id="row2-datasources2" hidden>
                                            <label for="#" class="col-md-4 control-label">Data Source 2</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="dataSourceMetric2" id="dataSourceMetric2"> 
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row" id="row2-descrizioneDataSource2" hidden>
                                            <label for="#" class="col-md-4 control-label">Data Source Description 2</label>
                                            <div class="col-md-6">
                                                <textarea id="descrizioneDataSource2" class="form-control textarea-metric" rows="3" readonly></textarea> 
                                            </div>                                      
                                        </div>
                                        <div class="row" id="row-query2" hidden>
                                            <label for="#" class="col-md-4 control-label">Query 2</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control textarea-metric" name="queryMetric2" id="queryMetric2" rows="3"></textarea>
                                                <button type="button" id="button_query_test2" name="test_query2" class="btn btn-primary test_button_query" value="">Test Query</button>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Threshold Alarm value</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="thresholdMetric">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Evaluation Criterion</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="thresholdEvalMetric" id="thresholdEvalMetric">
                                                    <option>null</option>
                                                    <option>=</option>
                                                    <option>></option>
                                                    <option><</option>
                                                    <option>!=</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Number Evaluation</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="thresholdEvalCountMetric">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="#" class="col-md-4 control-label">Alarm Time Threshold</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="thresholdTimeMetric"> 
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" id="button_add_new_metric" name="add_new_metric" class="btn btn-primary">Add</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal di modifica di una metrica-->
<div class="modal fade" id="modal-modify-metric" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" id="dialog-metric-m">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel-m">Modify metric</h4>
            </div>
            <div class="modal-body">
                <form id="modify_metric" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                    <div class="tabbable"> <!-- Only required for left/right tabs -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1m" data-toggle="tab">General</a></li>
                            <li><a href="#tab2m" data-toggle="tab">Data acquisition</a></li>
                            <li><a href="#tab3m" data-toggle="tab">Threshold alarm</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1m">
                                <div class="form-group">
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Name</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" class="modify-nameMetric" name="modify-nameMetric" id="modify-nameMetric" readonly>                                 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Description</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control textarea-metric" rows="3" name="modify-descriptionMetric" id="modify-descriptionMetric"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Short Description</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="modify-descriptionShortMetric" id="modify-descriptionShortMetric"> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Data Area</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-areaMetric" id="modify-areaMetric">
                                                <option>Mobilità</option>
                                                <option>Intrattenimento</option>
                                                <option>Statistiche</option>
                                                <option>Social Network</option>
                                                <option>Meteo</option>
                                                <option>Network</option>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Source</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="modify-sourceMetric" id="modify-sourceMetric"> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Result type</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-typeMetric" id="modify-typeMetric">
                                                    <option>Intero</option>
                                                    <option>Map</option>
                                                    <option>Float</option>
                                                    <option>Testuale</option>
                                                    <option>Percentuale</option>
                                                    <option>Percentuale/50</option>
                                                    <option>Percentuale/285</option>
                                                    <option>Percentuale/424</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Frequency of calculation (milliseconds)</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="modify-frequencyMetric" class="modify-frequencyMetric" id="modify-frequencyMetric"> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">context</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" name="modify-contextMetric" id="modify-contextMetric" class="checkStato" value="1"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Time Range</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" class="checkStato" name="modify-timeRangeMetric" id="modify-timeRangeMetric" value="1"/>
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Storing Data</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" class="checkStato" name="modify-storingDataMetric" id="modify-storingDataMetric" value="1"/> 
                                        </div>
                                    </div>                                    
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Acquisition Modality</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-queryTypeMetric" id="modify-queryTypeMetric">
                                                <option>null</option>
                                                    <option>SQL</option>    
                                                    <option>SPARQL</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab2m">
                                <div class="form-group">
                                    <!-- Dati da spostare -->
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Process Java String</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-processTypeMetric" id="modify-processType">
                                                <option>API</option>
                                                    <option>JVPerc</option>
                                                    <option>JVNum1</option>
                                                    <option>JVRidesAtaf</option>
                                                    <option>jVPark</option>
                                                    <option>JVSceOnNodes</option>
                                                    <option>JVSmartDs</option>
                                                    <option>JVTwRet</option>
                                                    <option>JVWifiOp</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" id="row-mod-datasource">
                                        <label for="#" class="col-md-4 control-label">Data Source</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-dataSourceMetric" id="modify-dataSourceMetric">
                                                <option>API</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" id="row-mod-des-datasource">
                                        <label for="#" class="col-md-4 control-label">Data Source description</label>
                                        <div class="col-md-6">
                                            <textarea id="modify-descrizioneDataSource" class="form-control textarea-metric" rows="3" readonly></textarea> 
                                        </div>
                                    </div>
                                    <!-- fine dati da spostare -->
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label" id="label-query-url-m">Query</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control textarea-metric" name="modify-queryMetric" id="modify-queryMetric" rows="3"></textarea>
                                            <!--
                                            <button type="button" data-dismiss="modal"  class="btn btn-primary" id="button_query_test_M">Test Query</button>
                                            -->
                                            <button type="button" id="button_query_test_M" name="test_query" class="btn btn-primary test_button_query">Test Query</button>

                                        </div>
                                    </div>
                                    <!--Righe visibili sono in caso Process JAVA sia JVPerc-->
                                    <div class="row" id="row2-modify-datasources2" hidden>
                                        <label for="#" class="col-md-4 control-label">Data Source 2</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-datasourceMetric2" id="modify-datasourcesMetric2">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" id="row2-modify-decription-datasources2" hidden>
                                        <label for="#" class="col-md-4 control-label">Data Source Description 2</label>
                                        <div class="col-md-6">
                                            <textarea id="modify-descrizioneDataSource2" class="form-control textarea-metric" rows="3" readonly></textarea> 
                                        </div>  
                                    </div>
                                    <div class="row" id="row-modify-query2" hidden>
                                        <label for="#" class="col-md-4 control-label">Query 2</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control textarea-metric" name="modify-queryMetric2" id="modify-queryMetric2" rows="3"></textarea> 
                                            <button type="button" id="button_query2_test_M" name="test_query2" class="btn btn-primary test_button_query">Test Query</button>
                                        </div>
                                    </div>



                                </div>
                            </div>
                            <div class="tab-pane" id="tab3m">
                                <div class="form-group">
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Threshold Alarm value</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="modify-thresholdMetric" id="modify-thresholdMetric" value="0"> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Evaluation Criterion</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="modify-thresholdEvalMetric" id="modify-thresholdEvalMetric">
                                                <option>null</option>
                                                <option>=</option>
                                                <option>></option>
                                                <option><</option>
                                                <option>!=</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Number Evaluation</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="modify-thresholdEvalCountMetric" name="modify-thresholdEvalCountMetric" value="0"> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="#" class="col-md-4 control-label">Alarm Time Threshold</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="modify-thresholdTime" name="modify-thresholdTime" value="0"> 
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" id="button_modify_metric" name="modify_metric" class="btn btn-primary">Modify</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Fine del menù di modifica della metrica -->
<!-- Menù cancellazione della metrica-->
<div class="modal fade" id="modal-delete-metric" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete selected metric</h4>
            </div>
            <div class="modal-body">
                <form id="form-delete-metric" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                    <div id="delete_message"><p>After the confirmation will not be possible to restore the deleted metric. Are you sure you want to proceed?</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="button_delete_metric" name="delete_metric" class="btn btn-primary">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- FIne del menù di cancellazione della metrica -->
<!-- Inizio Modifica dello stato -->
<div class="modal fade" id="modal-modify-metric-status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modify metric status</h4>
            </div>
            <div class="modal-body">
                <form id="form-modify-metric-status" class="form-horizontal" role="form" method="post" action="" data-toggle="validator">
                    <div id="status_message"><p>Are you sure you want to change this metric status?</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="button_modify_metric_status" name="modify-status" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Fine modifca dello stato -->
<script type='text/javascript'>

    Array.prototype.contains = function (v) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] === v)
                return true;
        }
        return false;
    };

    Array.prototype.unique = function () {
        var arr = [];
        for (var i = 0; i < this.length; i++) {
            if (!arr.contains(this[i])) {
                arr.push(this[i]);
            }
        }
        return arr;
    };


    $(document).ready(function () 
    {
        var array_metrics = new Array();
        var array_dataSources = new Array();
        var array_widget = new Array();

        //array dei menù
        var list_area = new Array();
        var list_processType = new Array();
        var list_EvalMetric = new Array();
        var list_queryType = new Array();
        var list_metricType = new Array();
        var list_dataSource = new Array();
        var list_widget = new Array();

        //Variabili  Datasource
        var ds_url;
        var ds_database;
        var ds_username;
        var ds_password;
        var ds_databaseType;

        //Variabili  Datasource
        var ds_urlQ2;
        var ds_databaseQ2;
        var ds_usernameQ2;
        var ds_passwordQ2;
        var ds_databaseTypeQ2;

        $.ajax({
            url: "get_data.php",
            data: {action: "get_metrics"},
            type: "GET",
            async: true,
            contentType: "charset=iso-8859-1",
            dataType: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++)
                {
                    array_metrics[i] = {desc: data[i]['descMetric'],
                        descShort: data[i]['descShortMetric'],
                        id: data[i]['idMetric'],
                        type: data[i]['typeMetric'],
                        area: data[i]['areaMetric'],
                        source: data[i]['sourceMetric'],
                        status: data[i]['statusMetric'],
                        processType: data[i]['processTypeMetric'],
                        queryType: data[i]['queryTypeMetric'],
                        //metricType: data[i]['metricTypeMetric'],
                        dataSource: data[i]['dataSourceMetric'],
                        metricType: data[i]['typeMetric'],
                        eval: data[i]['thresholdEvalMetric']};

                    //carica gli array
                    list_area[i] = array_metrics[i]['area'];
                    list_processType[i] = array_metrics[i]['processType'];
                    list_EvalMetric[i] = array_metrics[i]['eval'];
                    list_metricType[i] = array_metrics[i]['metricType'];
                    list_queryType[i] = array_metrics[i]['queryType'];
                    list_dataSource[i] = array_metrics[i]['dataSource'];
                    //console.log(list_dataSource[i]);

                    //Lista delle metriche
                    if (array_metrics[i]['status'] == 'Attivo') {
                        $('#list_metrics tbody').append('<tr><td class="name_met">' + array_metrics[i]['id'] + '</td><td>' + array_metrics[i]['desc'] + '</td><td>' + array_metrics[i]['area'] + '</td><td>' + array_metrics[i]['source'] + '</td><td><a class="icon-status-metric" href="#" data-toggle="modal" data-target="#modal-modify-metric-status"><input type="checkbox" class="checkStato" name="stato" value=1 checked /></a></td><td><div class="icons-modify-metric"><a class="icon-cfg-metric" href="#" data-toggle="modal" data-target="#modal-modify-metric" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div><div class="icons-delete-metric"><a class="icon-delete-metric" href="#" data-toggle="modal" data-target="#modal-delete-metric"><span class="glyphicon glyphicon-remove glyphicon-delete-metric" aria-hidden="true" style="float:right;"></span></a></div></td></tr>');
                    } else {
                        $('#list_metrics tbody').append('<tr><td class="name_met">' + array_metrics[i]['id'] + '</td><td>' + array_metrics[i]['desc'] + '</td><td>' + array_metrics[i]['area'] + '</td><td>' + array_metrics[i]['source'] + '</td><td><a class="icon-status-metric" href="#" data-toggle="modal" data-target="#modal-modify-metric-status"><input type="checkbox" class="checkStato" name="stato" value=0/></a></td><td><div class="icons-modify-metric"><a class="icon-cfg-metric" href="#" data-toggle="modal" data-target="#modal-modify-metric" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div><div class="icons-delete-metric"><a class="icon-delete-metric" href="#" data-toggle="modal" data-target="#modal-delete-metric"><span class="glyphicon glyphicon-remove glyphicon-delete-metric" aria-hidden="true" style="float:right;"></span></a></div></td></tr>');
                    }

                    //lista metriche da associare
                    $('#list-Metric').append('<option>' + array_metrics[i]['id'] + '</option>');

                }


                //caricamento elenco widget
                $.ajax({
                    url: "get_data.php",
                    data: {action: "get_widget"},
                    type: "GET",
                    async: true,
                    //contentType: "charset=utf-8",
                    dataType: 'json',
                    success: function (data) {
                        //console.log(data.length);
                        for (var i = 0; i < data.length; i++)
                        {
                            array_widget[i] = {
                                type_W: data[i]['type_widget'],
                                source_w: data[i]['source_widget']
                            };
                            //console.log(array_widget[i]['type']);
                            //list_widget[i] = array_widget[i]['type_W'];
                            list_widget[i] = array_widget[i]['source_w'];
                            //console.log(list_widget[i]);                 
                        }
                        var lunghezza = list_widget.length;
                        console.log(lunghezza);
                        list_widget_unique = list_widget.unique();
                        for (var i = 0; i < list_widget_unique.length; i++) {
                            $('#list-widget-types').append('<option>' + list_widget_unique[i] + '</option>');
                        }
                        //
                    }
                });



                //caricamento dei dati nelle liste
                /*
                 list_area_unique = list_area.unique();
                 for (var i = 0; i < list_area_unique.length; i++) {
                 $('#modify-areaMetric').append('<option>' + list_area_unique[i] + '</option>');
                 $('#areaMetric').append('<option>' + list_area_unique[i] + '</option>');
                 }
                 */
                 /*
                list_processType_unique = list_processType.unique();
                for (var i = 0; i < list_processType_unique.length; i++) {
                    $('#processType').append('<option>' + list_processType_unique[i] + '</option>');
                    $('#modify-processType').append('<option>' + list_processType_unique[i] + '</option>');
                }
                */
                /*
                 list_EvalMetric_unique = list_EvalMetric.unique();
                 for (var i = 0; i < list_EvalMetric_unique.length; i++) {
                 $('#thresholdEvalMetric').append('<option>' + list_EvalMetric_unique[i] + '</option>');
                 $('#modify-thresholdEvalMetric').append('<option>' + list_EvalMetric_unique[i] + '</option>');
                 }
                 */
                 /*
                list_queryType_unique = list_queryType.unique();
                for (var i = 0; i < list_queryType_unique.length; i++) {
                    $('#queryTypeMetric').append('<option>' + list_queryType_unique[i] + '</option>');
                    $('#modify-queryTypeMetric').append('<option>' + list_queryType_unique[i] + '</option>');
                }
                */
                /*
                list_metricType_unique = list_metricType.unique();
                for (var i = 0; i < list_metricType_unique.length; i++) {
                    $('#metricTypeMetric').append('<option>' + list_metricType_unique[i] + '</option>');
                    $('#modify-typeMetric').append('<option>' + list_metricType_unique[i] + '</option>');
                }
                */
                //fine delle liste nei menù


                //modifica di una metrica esistente
                $('.icons-modify-metric').on('click', function () {
                    var name_metric_m = $(this).parent().parent().find('.name_met').text();
                    $("#modify-descrizioneDataSource2").val('');
                    console.log('Il nome della metrica è: ' + name_metric_m);
                    //svuola datasource ogni volta che si apre il menù
                    //$("#modify-dataSourceMetric").empty();

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

                            //$("#modify-queryMetric").text(data['query_metric']);
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
                                //Nel caso in cui l'utente non usi una query, ma un'api bisogna modificare le impostazioni in modo che inserisca una url al posto della query.
                                //$('#row-mod-datasource').hide();
                                $('#row-mod-des-datasource').hide();
                                $('#label-query-url').text("Url");
                                $('#button_query_test').hide();
                            } else {
                                //$('#row-mod-datasource').show();
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
                            console.log("Stringa ds:   " + stringa_ds);
                            //var array_stringa = str0.split("|");
                            var array_stringa = stringa_ds.split("|");
                            //$("#modify-dataSourceMetric").val(data['dataSource_metric']);
                            //caricamento dei dati in descrizione datasources.
                            console.log("Lunghezza stringa_ds:   " + array_stringa.length);
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
                            //fine valore default
                            $("#modify-thresholdMetric").val(data['threshold_metric']);
                            $("#modify-thresholdEvalMetric").val(data['thresholdEval_metric']);
                            $("#modify-thresholdEvalCountMetric").val(data['thresholdEvalCount_metric']);
                            $("#modify-thresholdTime").val(data['thresholdTime_metric']);
                            // $("#modify-storingDataMetric").text('');
                        }
                    });

                });

                //prendere i dati della tabella datasource
                $.ajax({
                    url: "get_data.php",
                    data: {action: "get_dataSource"},
                    type: "GET",
                    async: true,
                    //contentType: "charset=iso-8859-1",
                    // contentType: 'utf-8',
                    datatype: 'json',
                    success: function (data) {
                        for (var i = 0; i < data.length; i++) {
                            console.log(data.length);
                            array_dataSources[i] = {
                                id: data[i]['idDataS'],
                                url: data[i]['urlDataS'],
                                database: data[i]['databaseDS'],
                                userName: data[i]['usernameDS'],
                                passWord: data[i]['passwordDS'],
                                databaseType: data[i]['databaseTypeDS']
                            };
                            $('#dataSourceMetric').append('<option>' + array_dataSources[i]['id'] + '</option>');
                            $('#modify-dataSourceMetric').append('<option>' + array_dataSources[i]['id'] + '</option>');
                            $('#modify-datasourcesMetric2').append('<option>' + array_dataSources[i]['id'] + '</option>');
                            $('#dataSourceMetric2').append('<option>' + array_dataSources[i]['id'] + '</option>');
                        }
                    }
                });

                //se il datasourceMetric in AGGIUNGI metrica è uguale ad API
                $('#processType').change(function () {
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
                });

                //se il datasourceMetric in MODIFICA metrica è uguale ad API
                $('#modify-processType').change(function () {
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
                });

                //visualizzare i dati del datasource
                //Aggiunta metrica
                $('#dataSourceMetric').change(function () {
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
                    //$('#descrizioneDataSource').val("Informazioni: " + str);                   
                });


                //visualizza dati del datasources su query2 (aggiunta metrica)
                $('#dataSourceMetric2').change(function () {
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
                });
                //

                //modifca metrica
                $('#modify-dataSourceMetric').change(function () {
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
                            //$('#descrizioneDataSource').val("URL: " + array_dataSources[j]['url']); 
                            $('#modify-descrizioneDataSource').val("Informazioni: " + ds_textM);
                        }
                    }
                    ;
                    //$('#modify-descrizioneDataSource').val("Informazioni: " + strM);                   
                });

                //visualizza dati del datasources su query2 (modifica metrica)
                $('#modify-datasourcesMetric2').change(function () {
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
                            //$('#descrizioneDataSource').val("URL: " + array_dataSources[j]['url']); 
                            $('#modify-descrizioneDataSource2').val("Informazioni: " + ds_textMQ2);
                        }
                    }
                    ;
                    //$('#modify-descrizioneDataSource').val("Informazioni: " + strM);                   
                });
                //

                //modifca dello stato
                $('.icon-status-metric').on('click', function () {
                    var name_metric_status = $(this).parent().parent().find('.name_met').text();
                    $("#button_modify_metric_status").attr('value', name_metric_status);

                });


                //eliminazione di una metrica esistente
                $('.icons-delete-metric').on('click', function () {
                    var name_metric_delete = $(this).parent().parent().find('.name_met').text();
                    console.log('Nome metrica da eliminare:  ' + name_metric_delete);
                    $('#button_delete_metric').attr('value', name_metric_delete);

                });

                //test della query su test_query.php
                $('#button_query_test').on('click', function () {
                    var query_selezionata = $('#queryMetric').val();
                    var mod_acquisizione = $('#queryTypeMetric').val();
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

                });

                //test della query modifica su test_query.php
                $('#button_query_test_M').on('click', function () {
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

                });

                //test della della query2 sul menù di aggiunta
                $('#button_query_test2').on('click', function () {
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

                });

                //test della query2 nel menù di modifica
                $('#button_query2_test_M').on('click', function () {
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

                });

                //caricamento dati sul menù di Aggiunta metrica
                $('#link_add_metric').on('click', function () {
                    console.log('sono un evento che si avvia al click di "aggiunta metrica"');
                    //$('#dataSourceMetric').empty();
                    var str1 = "";
                    //str =  $('#dataSourceMetric option:selected').text();
                    str1 = $('#dataSourceMetric').val();
                    //$('#dataSourceMetric2').empty();
                    console.log('Il valore della datasource metric è ' + str1);
                    var ds_text = "";
                    for (var j = 0; j < array_dataSources.length; j++) {
                        if (array_dataSources[j]['id'] == str1) {
                            ds_url = array_dataSources[j]['url'];
                            ds_database = array_dataSources[j]['database'];
                            ds_username = array_dataSources[j]['userName'];
                            ds_password = array_dataSources[j]['passWord'];
                            ds_databaseType = array_dataSources[j]['databaseType'];
                            ds_text += "url: " + array_dataSources[j]['url'] + ".\n";
                            ds_text += "database: " + array_dataSources[j]['database'] + ".\n";
                            ds_text += "database Type: " + array_dataSources[j]['databaseType'] + ".\n";
                            //$('#descrizioneDataSource').val("URL: " + array_dataSources[j]['url']); 
                            $('#descrizioneDataSource').val(ds_text);
                        }
                    }
                    ;
                });
                //fine 


                //rendere visibile query2
                $('#processType').change(function () {
                    var verificaQ2 = $('#processType option:selected').text();
                    if (verificaQ2 == 'JVPerc') {
                        $('#row-query2').show();
                        $('#row2-datasources2').show();
                        $('#row2-descrizioneDataSource2').show();

                    }
                });

                $('#modify-processType').change(function () {
                    var verificaQ2 = $('#modify-processType option:selected').text();
                    if (verificaQ2 == 'JVPerc') {
                        $('#row-modify-query2').show();
                        $('#row2-modify-datasources2').show();
                        $('#row2-modify-decription-datasources2').show();
                    }
                });

                //

                //test query da modificare
                $('#button_query_test_M').on('click', function () {
                    var query_selezionata = $('#modify-queryMetric').val();
                    $('button_query_test_M').attr('value', query_selezionata);
                    //inserimento chiamata ajax
                });


                //descrizione delle metrice sul menù d'associazione
                $('#list-Metric').change(function () {
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
                });

            }
        });


    });
</script>
</body>
</html>