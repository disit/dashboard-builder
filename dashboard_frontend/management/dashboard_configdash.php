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
<!DOCTYPE html>
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
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <link rel="stylesheet" href="../css/style_widgets.css" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Custom Fonts -->
    <!--<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">-->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery -->
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <script src="../js/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>

    <!-- CKEditor --> 
    <script src="http://cdn.ckeditor.com/4.5.10/standard/ckeditor.js"></script>
    
     <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
</head>

<body> 
    <div id="wrapper-dashboard-cfg">
        <!-- New header -->
        <nav id="navbarDashboard" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div id="navbarDashboardHeader">
                <div class="dashboardHeaderLeft">
                    <div id="dashboardTitle"></div>
                    <div id="dashboardSubtitle"></div>
                </div>
            </div>
            <div id="headerLogo">
                <img id="headerLogoImg"/>
            </div>
            <div id="clock"><?php include('../widgets/time.php'); ?></div>    
        </nav>
        <br/><br/><br/><br/><br/><br/>
        <div id="page-wrapper">
            <div class="container-fluid">
                <nav id="modify-bar-dashboard" class="navbar navbar-default">
                    <div class="container-fluid">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li class="active"><a title="Add widget link" id="link_add_widget" href="#" data-toggle="modal" data-target="#modal-add-widget"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Widget <span class="sr-only">(current)</span></a></li>
                                <li class="active"><a title="Modify dashboard link" id ="link_modifyDash" href="#" data-toggle="modal" data-target="#modal-modify-dashboard"><span class="glyphicon glyphicon-cog" aria-hidden="true">Modifica <span class="sr-only">(current)</span></span></a></li>
                                <li><a title="Save configuration link" id ="link_save_configuration" href="#"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span></a></li>
                                <li><a title="Duplicate dash link" id ="link_duplicate_dash" href="#" data-toggle="modal" data-target="#modal-duplicate-dashboard"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span></a></li>
                                <li><a title="Exit application link" id ="link_exit" href="#"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
                                <li><a title="Help link" id ="link_help" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></li>
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>

                <div id="container-widgets-cfg" class="gridster">
                    <ul>

                    </ul>  

                </div>
                <div id="logos-cfg" class='logos-bar'>
                    <div id="logos_twitter-cfg" class="logos_twitter-bar">
                        <span><img src='img/logo_twitter.png' alt="Twitter logo"></img><div id="twitter_t"></div></span>
                    </div>
                    <div id="logos_twitter_ret-cfg" class="logos_twitter_ret-bar">
                        <span><img src='img/retweet.png' alt="Twitter retweet logo"></img><div id="twitter_ret"><?php include("../widgets/widgetTwitter.php"); ?></div></span>
                    </div>

                    <div id="link_twitter_vig-cfg" class="link_twitter_vig-bar"><span><a title="Twitter vigilance link" href='http://www.disit.org/tv/' target='_new'>Twitter Vigilance</a></span></div>
                    <a id="logo_disit-cfg" class="logo_disit-bar" href="http://www.disit.org/" target="_new"><img src="img/logo.jpg" alt="DISIT lab logo"/></a>	
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>

        <!-- /#page-wrapper -->

    </div>

    <!-- Modale aggiunta widget -->
    <div class="modal fade" id="modal-add-widget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="adding01">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new widget to dashboard</h4>
                </div>
                <div id="modal-add-widget-body" class="modal-body">
                    <form id="form-setting-widget" class="form-horizontal" name="form-setting-widget" role="form" method="post" action="" data-toggle="validator" novalidate>
                        <div class="form-group">
                            <div class="alert alert-info info-modal" role="alert">Click the <b>Add</b> button to associate one or more metrics to the widget</div>

                            <div class="row">
                                <label for="select-metric" class="col-md-4 control-label">Metric</label>

                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-control" id="select-metric" name="select-metric" required></select> 
                                        <span class="input-group-btn">
                                            <button id="button_add_metric_widget" class="btn btn-default" name="button_add_metric_widget" type="button">Add</button>                                            
                                        </span>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <label for="textarea-metric" class="col-md-4 control-label">Metric description</label>
                                <div class="col-md-6">
                                    <textarea id ="textarea-metric-c" class="form-control textarea-metric" name="textarea-metric" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div id ="schedulerRow" class="row" style="display: none">
                                <label for="inputSchedulerWidget" class="col-md-4 control-label" style="display: none">Scheduler</label>
                                <div id="inputSchedulerWidgetDiv" class="col-md-6" style="display: none">
                                    <div id="inputSchedulerWidgetGroupDiv"class="input-group" style="display: none">
                                        <select class="form-control" id="inputSchedulerWidget" name="inputSchedulerWidget" style="display: none"></select>
                                    </div>
                                </div>
                            </div>    
                                
                            
                            <div id ="jobsAreasRow" class="row" style="display: none">    
                            <label for="inputJobsAreasWidget" class="col-md-4 control-label" style="display: none">Jobs areas</label>
                                <div id="inputJobsAreasWidgetDiv" class="col-md-6" style="display: none">
                                    <div id="inputJobsAreasWidgetGroupDiv"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsAreasWidget" name="inputJobsAreasWidget" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div id ="jobsGroupsRow" class="row" style="display: none">    
                            <label for="inputJobsGroupsWidget" class="col-md-4 control-label" style="display: none">Jobs groups</label>
                                <div id="inputJobsGroupsWidgetDiv" class="col-md-6" style="display: none">
                                    <div id="inputJobsGroupsWidgetGroupDiv"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsGroupsWidget" name="inputJobsGroupsWidget" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            
                           
                            <div id ="jobsNamesRow" class="row" style="display: none">    
                            <label for="inputJobsNamesWidget" class="col-md-4 control-label" style="display: none">Jobs names</label>
                                <div id="inputJobsNamesWidgetDiv" class="col-md-6" style="display: none">
                                    <div id="inputJobsNamesWidgetGroupDiv"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsNamesWidget" name="inputJobsNamesWidget" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" id="schedulerName" name="schedulerName">
                            <input type="hidden" id="host" name="host">
                            <input type="hidden" id="user" name="user">
                            <input type="hidden" id="pass" name="pass">
                            <input type="hidden" id="jobArea" name="jobArea">
                            <input type="hidden" id="jobGroup" name="jobGroup">
                            <input type="hidden" id="jobName" name="jobName">
                            
                            <div id ="inputComuneRow" class="row">    
                                <label for="inputComuneWidget" class="col-md-4 control-label">Context</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="inputComuneWidget" name="inputComuneWidget" novalidate>
                                        <div class="input-group-btn">
                                            <button type="button" id="link_help_modal-add-widget" class="btn btn-default link_help_modal-add-widget" aria-label="Help">
                                                <span class="glyphicon glyphicon-question-sign"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            
                            <div class="row">
                                <label for="select-widget" class="col-md-4 control-label">Widget type</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select name="select-widget" class="form-control" id="select-widget" required>
                                        </select>
                                        <div id="add-n-metrcis-widget" class="input-group-addon"></div>
                                    </div>    
                                </div>
                            </div>    
                            <div class="row">
                                <label for="textarea-selected-metrics" class="col-md-4 control-label">Selected metrics</label>

                                <div class="col-md-6">
                                    <textarea id ="textarea-selected-metrics" class="form-control textarea-metric" name="textarea-selected-metrics" rows="2" readonly required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <label for="inputUrlWidget" class="col-md-4 control-label">Widget link</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputUrlWidget" name="inputUrlWidget">
                                </div>

                            </div>
                            <div class="row">
                                <label for="textarea-information-metrics" class="col-md-4 control-label">Informations</label>
                            </div>
                            <div class="col-md-6">
                                <!-- textarea per le informazioni delle metriche -->
                                <textarea id ="textarea-information-metrics" class="ckeditor" name="textarea-information-metrics" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="well wellCustom">
                            <legend class="legend-form-group">Widget properties</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label id="titleLabel" for="inputTitleWidget" class="col-md-2 control-label addWidgetParLabel">Title</label> <!-- col-md-offset-1 -->
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputTitleWidget" name="inputTitleWidget" required>
                                    </div>
                                    
                                    <label id="bckColorLabel" for="inputColorWidget" class="col-md-3 control-label addWidgetParLabel">Background color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputColorWidget" name="inputColorWidget" value="#FFFFFF" required>
                                            <span class="input-group-addon"><i id="color_widget"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputFontSize" class="col-md-2 control-label addWidgetParLabel">Font size</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFontSize" name="inputFontSize" >
                                    </div>
                                    
                                    <label for="inputFontColor" class="col-md-3 control-label addWidgetParLabel">Font color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFontColor" name="inputFontColor" value="#000000">
                                            <span class="input-group-addon"><i id="widgetFontColor"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputFrameColorWidget" class="col-md-2 control-label addWidgetParLabel">Title color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFrameColorWidget" name="inputFrameColorWidget" value="#eeeeee" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    
                                    <label for="inputColorWidget" class="col-md-3 control-label addWidgetParLabel">Units of measure</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputUdmWidget" name="inputUdmWidget">
                                    </div>  
                                </div>
                                <div class="row">
                                    <label for="select-IntTemp-Widget" class="col-md-2 control-label addWidgetParLabel">Period</label>
                                    <div class="col-md-3">
                                        <select name="select-IntTemp-Widget" class="form-control" id="select-IntTemp-Widget" required>
                                            <option value="Nessuno">No</option>
                                            <option value="4 Ore">4 Hours</option>
                                            <option value="12 Ore">12 Hours</option>
                                            <option value="Giornaliera">Daily</option>
                                            <option value="Settimanale">Weekly</option>
                                            <option value="Mensile">Monthly</option>
                                            <option value="Annuale">Annually</option>       
                                        </select>
                                    </div>
                                    <label for="inputFreqWidget" class="col-md-3 control-label addWidgetParLabel">Refresh rate (s)</label>

                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFreqWidget" name="inputFreqWidget" placeholder="" required>
                                    </div>
                                </div>                     
                                <div class="row">
                                    <label for="input-Column" class="col-md-2 control-label addWidgetParLabel">Height</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputSizeRowsWidget" name="inputSizeRowsWidget" placeholder="">                                            
                                        </select>
                                    </div>
                                    <label for="input-rows" class="col-md-3 control-label addWidgetParLabel">Width</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputSizeColumnsWidget" name="inputSizeColumnsWidget" placeholder="">                                           
                                        </select>
                                    </div>
                                </div>       
                            </div>
                        </div>

                        <div class="well wellCustom" id="value_range" hidden>
                            <legend class="legend-form-group">Numeric range values</legend>
                            <div class="form-group">
                                <div class="row" >
                                    <label for="input-min_range" class="col-md-3 control-label">Min. Range</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="input-min_range" name="input-min_range" placeholder="">                                                                        
                                    </div>
                                    <label for="input-max_range" class="col-md-2 control-label">Max. Range</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="input-max_range" name="input-max_range" placeholder="">                                        
                                    </div>        
                                </div>
                                <textarea id ="textarea-range-value" name="textarea-range-value" rows="2">{}</textarea>

                                <div class="col-md-3">
                                    <button id="create_param_json" class="btn btn-primary btn-sm" type="button">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="button_reset_add_widget" class="btn btn-danger pull-left" type="reset">Reset</button>
                            <button id="button_close_popup" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="button_add_widget" name="add_widget" class="btn btn-primary" type="submit">Create</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>   
    
    <!-- Modale modifica dashboard -->
    <div class="modal fade" id="modal-modify-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="dash01">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabelDas">Modify current dashboard</h4>
                </div>
                <div id="modal-modify-dashboard-body" class="modal-body">
                    <form id="form-modify-dashboard" enctype="multipart/form-data" class="form-horizontal" name="form-setting-dashboard" role="form" action="" data-toggle="validator"> <!--  method="post" -->
                        <div class="well">
                            <legend class="legend-form-group">Header</legend>
                            <div class="form-group">
                                <label for="inputTitleDashboard" class="col-md-4 control-label">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputTitleDashboard" name="inputTitleDashboard"  placeholder="Title">
                                </div>
                                <label for="inputSubTitleDashboard" class="col-md-4 control-label">Subtitle</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputSubTitleDashboard" name="inputSubTitleDashboard" placeholder="Subtitle">
                                </div>                     
                                <!-- Altre colonne -->
                                <label for="inputDashCol" class="col-md-4 control-label">Header Color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputDashCol" name="inputDashCol" value="#5367ce">
                                        <span class="input-group-addon"><i id="color_h"></i></span>
                                    </div>
                                </div>
                                <label for="headerFontSize" class="col-md-4 control-label">Header font size (pt)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="headerFontSize" name="headerFontSize">
                                </div>
                                <label for="headerFontColor" class="col-md-4 control-label">Header font color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="headerFontColor" name="headerFontColor">
                                        <span class="input-group-addon"><i id="color_hf"></i></span>
                                    </div>
                                </div>
                                <label for="inputDashBckCol" class="col-md-4 control-label">Background Color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputDashBckCol" name="inputDashBckCol" value="#eeeeee">
                                        <span class="input-group-addon"><i id="color_b"></i></span>
                                    </div>
                                </div>
                                <label for="inputDashExtCol" class="col-md-4 control-label">External Frame Color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputDashExtCol" name="inputDashExtCol" value="#ffffff">
                                        <span class="input-group-addon"><i id="color_e"></i></span>
                                    </div>
                                </div>
                                <label for="dashboardLogoInput" class="col-md-4 control-label">Dashboard logo</label>
                                <div class="col-md-6">
                                    <input id="dashboardLogoInput" name="dashboardLogoInput" type="file" class="filestyle form-control" data-badge="false" data-input ="true" data-size="sm" data-buttonName="btn-primary" data-buttonText="Choose file">
                                </div>
                                <label for="dashboardLogoLinkInput" class="col-md-4 control-label">Dashboard logo link</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="dashboardLogoLinkInput" name="dashboardLogoLinkInput" disabled>
                                </div>
                                <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                            </div>
                        </div>
                        <div class="well">
                            <legend class="legend-form-group">Measures</legend>
                            <div class="form-group">
                                <label for="inputWidthDashboard" class="col-md-4 control-label">Width (columns)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputWidthDashboard" name="inputWidthDashboard" placeholder="Width" >
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="button_close_popup_modify_dashboard" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="button_modify_dashboard" name="mod_dash" class="btn btn-primary" type="button" data-dismiss="modal">Modify</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div> 
    <!-- Fine editor modifica Dashboard -->

    <!-- Modale duplicazione dashboard -->
    <div class="modal fade" id="modal-duplicate-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="duplicate01">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Duplicate current Dashbaord</h4>
                </div>
                <div class="modal-body">
                    <form id="form-duplicate-dashboard" class="form-horizontal" name="form-saveAs-dashboard" role="form" method="post" action="" data-toggle="validator">
                        <div class="form-group">
                            <label for="select-dashboard" class="col-md-4 col-md-offset-1 control-label">Current Dashboard name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="nameCurrentDashboard" name="NameDashboard" placeholder="Name Current Dashboard" readonly>
                            </div>                      
                        </div>
                        <div class="form-group">
                            <label for="select-dashboard" class="col-md-4 col-md-offset-1 control-label">Save as...</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="nameNewDashboard" name="NameNewDashboard" placeholder="Name new Dashboard">
                            </div>                      
                        </div>
                </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="button_duplicate_dashboard" data-dismiss="modal" name="modify_dashboard" class="btn btn-primary">Duplicate</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modale modifica widget -->
    <div class="modal fade" id="modal-modify-widget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" id="modify01">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modify widget</h4>
                </div>
                <div class="modal-body">
                    <form id="form-modify-widget" class="form-horizontal" name="form-modify-widget" role="form" method="post" action="" data-toggle="validator">
                        <div class="form-group">
                            <div class="row">
                                <label for="inputNameWidgetM" class="col-md-4 control-label">Widget name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="inputNameWidgetM" name="inputNameWidgetM" placeholder="Title" readonly>                                 
                                </div>
                            </div>
                            <div class="row">
                                <label for="metricWidgetM" class="col-md-4 control-label">Metric</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="metricWidgetM" name="metricWidgetM" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <label for="textarea-metric-widget-m" class="col-md-4 control-label">Metric description</label>
                                <div class="col-md-6">
                                    <textarea id ="textarea-metric-widget-m" class="form-control textarea-metric" name="textarea-metric-widget-m" rows="3"></textarea>
                                </div>
                            </div>
                            <div id="schedulerRowM" class="row" style="display: none">
                                <label for="inputSchedulerWidgetM" class="col-md-4 control-label" style="display: none">Scheduler</label>
                                <div id="inputSchedulerWidgetDivM" class="col-md-6" style="display: none">
                                    <div id="inputSchedulerWidgetGroupDivM"class="input-group" style="display: none">
                                        <select class="form-control" id="inputSchedulerWidgetM" name="inputSchedulerWidgetM" style="display: none"></select>
                                    </div>
                                </div>
                            </div>    
                            <div id="jobsAreasRowM" class="row" style="display: none">    
                            <label for="inputJobsAreasWidgetM" class="col-md-4 control-label" style="display: none">Jobs areas</label>
                                <div id="inputJobsAreasWidgetDivM" class="col-md-6" style="display: none">
                                    <div id="inputJobsAreasWidgetGroupDivM"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsAreasWidgetM" name="inputJobsAreasWidgetM" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            <div id="jobsGroupsRowM" class="row" style="display: none">    
                            <label for="inputJobsGroupsWidgetM" class="col-md-4 control-label" style="display: none">Jobs groups</label>
                                <div id="inputJobsGroupsWidgetDivM" class="col-md-6" style="display: none">
                                    <div id="inputJobsGroupsWidgetGroupDivM"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsGroupsWidgetM" name="inputJobsGroupsWidgetM" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            <div id="jobsNamesRowM" class="row" style="display: none">    
                            <label for="inputJobsNamesWidgetM" class="col-md-4 control-label" style="display: none">Jobs names</label>
                                <div id="inputJobsNamesWidgetDivM" class="col-md-6" style="display: none">
                                    <div id="inputJobsNamesWidgetGroupDivM"class="input-group" style="display: none">
                                        <select class="form-control" id="inputJobsNamesWidgetM" name="inputJobsNamesWidgetM" style="display: none"></select>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" id="schedulerNameM" name="schedulerNameM">
                            <input type="hidden" id="hostM" name="hostM">
                            <input type="hidden" id="userM" name="userM">
                            <input type="hidden" id="passM" name="passM">
                            <input type="hidden" id="jobAreaM" name="jobAreaM">
                            <input type="hidden" id="jobGroupM" name="jobGroupM">
                            <input type="hidden" id="jobNameM" name="jobNameM">

                            <div id="inputComuneRowM" class="row">
                                <label for="inputComuneWidgetM" class="col-md-4 control-label">Context</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="inputComuneWidgetM" name="inputComuneWidgetM">
                                        <div class="input-group-btn">
                                            <button type="button" id="link_help_modal-add-widget-m" class="btn btn-default link_help_modal-add-widget" aria-label="Help">
                                                <span class="glyphicon glyphicon-question-sign"></span>
                                            </button>
                                        </div>

                                    </div>
                                </div> 
                            </div>
                            
                            <div class="row">
                                <label for="select-widget-m" class="col-md-4 control-label">Type</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select name="select-widget-m" class="form-control" id="select-widget-m" required>
                                        </select>
                                        <div id="mod-n-metrcis-widget" class="input-group-addon"></div>
                                    </div>   
                                </div>
                            </div>   
                            <!-- Url metrica da modificare-->
                            <div class="row">
                                <label for="urlWidgetM" class="col-md-4 control-label">Widget link</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="urlWidgetM" name="urlWidgetM">
                                </div>
                            </div>
                            <!-- inserisci informazione sulle metriche -->
                            <div class="row">
                                <label for="textarea-info-widget-m" class="col-md-4 control-label">Informations</label>
                            </div>

                            <div class="col-md-6">
                                <textarea id ="textareaInfoWidgetM" class="form-control textarea-metric" name="textareaInfoWidgetM" rows="3" placeholder="your default text">abcd</textarea>                                

                            </div>

                        </div>
                        <div class="well wellCustom">
                            <legend class="legend-form-group">Widget properties</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputTitleWidgetM" class="col-md-2 control-label addWidgetParLabel">Title</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputTitleWidgetM" name="inputTitleWidgetM" required>
                                    </div>
                                    <label for="inputColorWidgetM" class="col-md-3 addWidgetParLabel control-label">Backgound color</label>
                                    <div class="col-md-3">
                                        <div id="widgetColorPickerContainer" class="input-group"> <!-- color-choice -->
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputColorWidgetM" name="inputColorWidgetM" required>  <!-- value="#FFFFFF" -->
                                            <span class="input-group-addon"><i id="color_widget_M"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputFontSizeM" class="col-md-2 addWidgetParLabel control-label">Font size</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFontSizeM" name="inputFontSizeM">
                                    </div>
                                    <label for="inputFontColorM" class="col-md-3 addWidgetParLabel control-label">Font color</label>
                                    <div class="col-md-3">
                                        <div id="widgetFontColorPickerContainerM" class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFontColorM" name="inputFontColorM">
                                            <span class="input-group-addon"><i id="widgetFontColorM"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-frameColor-Widget-m" class="col-md-2 addWidgetParLabel control-label">Title color</label>
                                    <div class="col-md-3">
                                        <div id="widgetFrameColorPickerContainer" class="input-group"> <!-- color-choice -->
                                            <input type="text" class="form-control demo-1 demo-auto" id="select-frameColor-Widget-m" name="select-frameColor-Widget-m" required> <!-- value="#eeeeee" -->
                                            <span class="input-group-addon"><i id="color_fm"></i></span>
                                        </div>
                                    </div>
                                    <label for="inputFreqWidgetM" class="col-md-3 addWidgetParLabel control-label">Units of measure</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputUdmM" name="inputUdmM" placeholder="">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-IntTemp-Widget-m" class="col-md-2 addWidgetParLabel control-label">Period</label>
                                    <div class="col-md-3">
                                        <select name="select-IntTemp-Widget-m" class="form-control" id="select-IntTemp-Widget-m">
                                            <option value="Nessuno">No</option>                                           
                                            <option value="4 Ore">4 Hours</option>
                                            <option value="12 Ore">12 Hours</option>
                                            <option value="Giornaliera">Daily</option>
                                            <option value="Settimanale">Weekly</option>
                                            <option value="Mensile">Monthly</option>
                                            <option value="Annuale">Annually</option>   
                                        </select>
                                    </div>
                                    <label for="inputFreqWidgetM" class="col-md-3 addWidgetParLabel control-label">Refresh rate (s)</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFreqWidgetM" name="inputFreqWidgetM" placeholder="" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="input-rows-m" class="col-md-2 addWidgetParLabel control-label">Height</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputRows-m" name="inputRows-m" placeholder="">                                            
                                        </select>
                                    </div>
                                    <label for="input-Column" class="col-md-3 addWidgetParLabel control-label">Width</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputColumn-m" name="inputColumn-m" placeholder="">                                            
                                        </select>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="well" id="value_range_m" hidden>
                            <legend class="legend-form-group">Numeric Range Values</legend>
                            <div class="form-group">
                                <div class="row" >
                                    <label for="input-min_range_m" class="col-md-3 control-label">Min. Range</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="input-min_range_m" name="input-min_range_m" placeholder="">                                                                        
                                    </div>
                                    <label for="input-max_range_m" class="col-md-2 control-label">Max. Range</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="input-max_range_m" name="input-max_range_m" placeholder="">                                        
                                    </div>        

                                </div>
                                <textarea id ="textarea-range-value_m" name="textarea-range-value_m" rows="2">{}</textarea>


                                <div class="col-md-3">
                                    <button id="create_param_json-m" class="btn btn-primary btn-sm" type="button">Modify</button>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="button_modify_widget" name="modify_widget" class="btn btn-primary" type="submit">Modify</button>
                </div>
                </form>

            </div>
        </div>
</div>
        <!-- Modal -->
        <div id="modal-info-context" class="modal fade bs-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel_info-context">
            <div class="modal-dialog modal-sm">
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                        </button> <h4 class="modal-title" id="mySmallModalLabel_info-context">Information</h4>
                    </div> 
                    <div class="modal-body">In <b> Context field </b> enter the Municipality or District.</div> 
                </div>

            </div>
        </div>
        <!-- Modal per visualizzazione dei commenti -->
        <div class="modal fade" tabindex="-1" id="dialog-information-widget" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" id="info01"> 
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="titolo_info">Description:</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-information-widget" class="form-horizontal" name="form-information-widget" role="form" method="post" action="" data-toggle="validator">
                            <div id="contenuto_infomazioni"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
            <!-- Fine aggiunta del modal -->
            <script type='text/javascript'>
                var array_metrics = new Array();
                var num_cols;
                var indicatore;
                var datoTitle;
                var datoSubtitle;
                var datoColor;
                var datoWidth;
                var datoRemains;
                var informazioni = new Array();
                var nuovaDashboard;
                var elencoScheduler = new Array();
                var elencoJobsGroupsPerScheduler = [[],[],[]];
                var headerFontSize = null;
                var headerModFontSize = null;
                var subtitleFontSize = null;
                var subtitleModFontSize = null;
                var clockFontSizeMod = null;
                var dashboardName = null;
                var logoFilename = null;
                var logoLink = null;
                
                $(document).ready(function (){
                    CKEDITOR.replace('textareaInfoWidgetM', {
                        allowedContent: true,
                        language: 'en',
                        width: '500',
                        height: '100'
                    });

                    $.ajax({
                        url: "get_data.php",
                        data: {action: "get_param_dashboard"},
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) {
                            for (var i = 0; i < data.length; i++)
                            {
                                $("#headerLogoImg").css("display", "none");
                                dashboardName = data[i].name_dashboard;
                                logoFilename = data[i].logoFilename;
                                logoLink = data[i].logoLink;
                                
                                var wrapperWidth = parseInt(data[i].width) + 40;
                                $("#wrapper-dashboard-cfg").css("width", wrapperWidth);
                                $("#container-widgets-cfg").css("width", data[i].width);
                                $("#logos-cfg").css("width", data[i].width);
                                $("#wrapper-dashboard-cfg").css("margin", "0 auto");
                                $("#navbarDashboard").css("background-color", data[i].color_header);
                                
                                var headerFontColor = data[i].headerFontColor;
                                headerFontSize = data[i].headerFontSize;
                                subtitleFontSize = parseInt(data[i].headerFontSize * 0.25);
                                if(subtitleFontSize < 24)
                                {
                                    subtitleFontSize = 24;
                                }
                                
                                $("#dashboardTitle").css("font-size", headerFontSize + "pt");
                                $("#dashboardTitle").css("color", headerFontColor);
                                $("#dashboardTitle").text(data[i].title_header);
                                $("#clock").css("color", headerFontColor);
                                
                                var a = $('#dashboardTitle').prop("offsetWidth");
                                var b = $("#clock").prop("offsetWidth");

                                if(a > 912)
                                {
                                    headerModFontSize = headerFontSize;
                                    subtitleModFontSize = subtitleFontSize;
                                }
                                else
                                {
                                    if(a > 768)
                                    {
                                        headerModFontSize = parseInt((headerFontSize*0.9));
                                        subtitleModFontSize = parseInt((subtitleFontSize*0.9));    
                                    }
                                    else
                                    {

                                        if(a > 320)
                                        {
                                            headerModFontSize = parseInt((headerFontSize*0.75));
                                            subtitleModFontSize = parseInt((subtitleFontSize*0.75));
                                        }
                                        else
                                        {
                                            headerModFontSize = parseInt((headerFontSize*0.55));
                                            subtitleModFontSize = parseInt((subtitleFontSize*0.55));
                                        }
                                    }
                                }
                                if(b > 288)
                                {
                                    clockFontSizeMod = 18;
                                }
                                else
                                {
                                    if(b > 217)
                                    {
                                        clockFontSizeMod = parseInt((18*0.8));
                                    }
                                    else
                                    {
                                        if(b >= 188)
                                        {
                                            clockFontSizeMod = parseInt((18*0.7));
                                        }
                                        else
                                        {
                                            if(b >= 136)
                                            {
                                                clockFontSizeMod = parseInt((18*0.55));
                                            }
                                            else
                                            {
                                                clockFontSizeMod = parseInt((18*0.43));
                                            }
                                        }

                                    }
                                }

                                $("#dashboardTitle").css("font-size", headerModFontSize + "pt");
                                $("#dashboardTitle").css("color", headerFontColor);
                                $("#dashboardTitle").text(data[i].title_header);
                                $("#clock").css("color", headerFontColor);
                                $("#clock").css("font-size", clockFontSizeMod + "pt");

                                var whiteSpaceRegex = '^[ t]+';
                                if((data[i].subtitle_header == "") || (data[i].subtitle_header == null) ||(typeof data[i].subtitle_header == 'undefined') ||(data[i].subtitle_header.match(whiteSpaceRegex)))
                                {
                                    $("#dashboardTitle").css("height", "100%");
                                    $("#dashboardSubtitle").css("display", "none");
                                }
                                else
                                {
                                    $("#dashboardTitle").css("height", "70%");
                                    $("#dashboardSubtitle").css("height", "30%");
                                    $("#dashboardSubtitle").css("display", "");
                                    $("#dashboardSubtitle").css("font-size", subtitleModFontSize + "pt");
                                    $("#dashboardSubtitle").css("color", headerFontColor);
                                    $("#dashboardSubtitle").text(data[i].subtitle_header);
                                }
                                
                                if(logoFilename != null)
                                {
                                    $("#headerLogoImg").prop("src", "../img/dashLogos/" + dashboardName + "/" + logoFilename);
                                    $("#headerLogoImg").prop("alt", "Dashboard logo");
                                    var img = new Image();
                                    img.src = "../img/dashLogos/" + dashboardName + "/" + logoFilename;
                                    img.onload = function()
                                    {
                                        if((logoLink !== null) && (logoLink !== ''))
                                        {
                                           var logoImage = $('#headerLogoImg');
                                           var logoLinkElement = $('<a href="' + logoLink + '" target="_blank" class="pippo">'); 
                                           logoImage.wrap(logoLinkElement);
                                           $('#dashboardLogoLinkInput').val(logoLink);
                                        }
                                        logoWidth = $('#headerLogoImg').width();
                                        logoHeight = $('#headerLogoImg').height();                                
                                        $("#headerLogoImg").css("display", "");
                                        $('#dashboardLogoLinkInput').removeAttr('disabled');
                                    }
                                }
                                
                                //aggiunta delle impostazioni della Dashboard nel Menu Modify
                                $("#inputTitleDashboard").attr("placeholder", data[i].title_header);
                                $("#inputTitleDashboard").val(data[i].title_header);
                                $("#headerFontColor").val(data[i].headerFontColor);
                                $('#color_hf').css("background-color", data[i].headerFontColor);
                                $("#headerFontSize").val(data[i].headerFontSize);
                                $("#inputSubTitleDashboard").attr("placeholder", data[i].subtitle_header);
                                $("#inputSubTitleDashboard").val(data[i].subtitle_header);
                                $("#inputDashCol").attr("value", data[i].color_header);
                                $("#inputWidthDashboard").val(data[i].num_columns);
                                $("#myModalLabelDas").text("Modify current Dashboard: " + data[i].name_dashboard);
                                $("#inputDashCol").val(data[i].color_header);
                                $("#remainsWidthDashboard").val(data[i].remains_width);
                                //Attribuisci nome dell'attuale Dashboard al menu di duplicazione
                                $('#nameCurrentDashboard').val(data[i].name_dashboard);
                                //fine duplicazione
                                indicatore = data[i].name_dashboard;
                                datoTitle = data[i].title_header;
                                datoSubtitle = data[i].subtitle_header;
                                datoColor = data[i].color_header;
                                num_cols = data[i].num_columns;

                                
                                if (data[i].color_background === '' || !data[i].color_background) {
                                    dato_back = '#eeeeee';
                                } else {
                                    dato_back = data[i].color_background;
                                }

                                if (data[i].external_frame_color === '' || !data[i].external_frame_color) {
                                    external_color = '#ffffff';
                                } else {
                                    external_color = data[i].external_frame_color;
                                }
                                $('body').css("background-color", external_color);
                                $('#page-wrapper').css("background-color", external_color);
                                $('#inputDashExtCol').val(external_color);
                                $('.logos-bar').css("background-color", external_color);
                                $('#color_e').css("background-color", external_color);
                                $('#container-widgets-cfg').css("background-color", dato_back);
                                $('#inputDashBckCol').attr("value", dato_back);
                                $('#inputDashBckCol').val(dato_back);
                                $('#container-widgets-cfg').css("border-top-color", dato_back);
                                $('#container-widgets-cfg').css("border-color", dato_back);
                                $('#color_b').css("background-color", dato_back);
                                $('#color_h').css("background-color", datoColor);
                            }


                            $.ajax({
                                url: "get_data.php",
                                data: {action: "get_widgets_dashboard"},
                                type: "GET",
                                async: true,
                                dataType: 'json',
                                success: function (data) {
                                    var gridster;
                                    for (var j = 0; j < data.length; j++) 
                                    {
                                        informazioni[j] = data[j].message_widget;
                                    }

                                    jQuery(function () {
                                        jQuery("#container-widgets-cfg ul").gridster({
                                            widget_margins: [1, 1],
                                            //widget_base_dimensions: [156, 77],
                                            widget_base_dimensions: [76, 38],
                                            min_cols: num_cols,
                                            max_size_x: 20,
                                            max_rows: 30,
                                            extra_rows: 40,
                                            draggable: {ignore_dragging: true},
                                            serialize_params: function ($w, wgd) {
                                                return {
                                                    /* add element ID to data*/
                                                    id: $w.attr('id'),
                                                    col: wgd.col,
                                                    row: wgd.row,
                                                    size_x: wgd.size_x,
                                                    size_y: wgd.size_y
                                                }
                                            }
                                        });

                                    });

                                    if (data.length > 0) {
                                        gridster = $("#container-widgets-cfg ul").gridster().data('gridster');
                                        for (var i = 0; i < data.length; i++)
                                        {
                                            var name_w = data[i]['name_widget'];
                                            var time = 0;
                                            if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "Mensile") {
                                                time = "30/DAY";
                                            } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "Annuale") {
                                                time = "365/DAY";
                                            } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "Settimanale") {
                                                time = "7/DAY";
                                            } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "Giornaliera") {
                                                time = "1/DAY";
                                            } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "4 Ore") {
                                                time = "4/HOUR";
                                            } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "12 Ore") {
                                                time = "12/HOUR";
                                            }
                                            var widget = ['<li id="' + name_w + '"></li>', data[i]['size_columns_widget'], data[i]['size_rows_widget'], data[i]['n_column_widget'], data[i]['n_row_widget']];                                            
                                            gridster.add_widget.apply(gridster, widget);

                                            var type_metric = new Array();
                                            var source_metric = new Array();
                                            var info_message = new Array();
                                            for (var k = 0; k < data[i]['metrics_prop'].length; k++) 
                                            {
                                                type_metric.push(data[i]['metrics_prop'][k]['type_metric']);
                                                source_metric.push(data[i]['metrics_prop'][k]['source_metric']);
                                                info_message.push(informazioni[i]);
                                            }

                                            $("#container-widgets-cfg ul").find("li#" + name_w).load("../widgets/" + encodeURIComponent(data[i]['source_file_widget']) + "?name=" + encodeURIComponent(name_w) + "&metric=" + encodeURIComponent(data[i]['id_metric_widget']) +
                                                    "&freq=" + encodeURIComponent(data[i]['frequency_widget']) + "&title=" + encodeURIComponent(data[i]['title_widget']) + "&color=" + encodeURIComponent(data[i]['color_widget']) + "&source=" + encodeURIComponent(source_metric) +
                                                    "&fontColor=" + encodeURIComponent(data[i]['fontColor']) + "&fontSize=" + encodeURIComponent(data[i]['fontSize']) + "&type_metric=" + encodeURIComponent(type_metric) + "&city=" + "&tmprange=" + encodeURIComponent(time) + "&city=" + encodeURIComponent(data[i]['municipality_widget']) + "&info=" + encodeURIComponent(info_message) + "&link_w=" + encodeURIComponent(data[i]['link_w']) + "&frame_color=" + encodeURIComponent(data[i]['frame_color']), function () {
                                                $(this).find(".icons-modify-widget").css("display", "inline");
                                                $(this).find(".modifyWidgetGenContent").css("display", "block");
                                                $(this).find(".pcCountdownContainer").css("display", "none");
                                                $(this).find(".iconsModifyPcWidget").css("display", "flex");
                                                $(this).find(".iconsModifyPcWidget").css("align-items", "center");
                                                $(this).find(".iconsModifyPcWidget").css("justify-content", "flex-end");
                                            });
                                        }
                                    }
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
                                    $('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                                    console.log('jqXHR:');
                                    console.log(jqXHR);
                                    console.log('textStatus:');
                                    console.log(textStatus);
                                    console.log('errorThrown:');
                                    console.log(errorThrown);
                                }
                            });
                        }
                    });
                    
                    $('#dashboardLogoInput').change(function ()
                    {
                        $('#dashboardLogoLinkInput').removeAttr('disabled');
                    }
                    );
                    
                    $('#link_modifyDash').click(function ()
                    {
                        if($('#headerLogoImg').css("display") !== 'none')
                        {
                            $('#dashboardLogoLinkInput').removeAttr('disabled');
                        }
                    }    
                    );
                    
                    $(window).resize(function() 
                    {
                        var a = $('#dashboardTitle').prop("offsetWidth");
                        var b = $("#clock").prop("offsetWidth");

                        if(a > 912)
                        {
                            headerModFontSize = headerFontSize;
                            subtitleModFontSize = subtitleFontSize;
                        }
                        else
                        {
                            if(a > 768)
                            {
                                headerModFontSize = parseInt((headerFontSize*0.9));
                                subtitleModFontSize = parseInt((subtitleFontSize*0.9));    
                            }
                            else
                            {
                                if(a > 320)
                                {
                                    headerModFontSize = parseInt((headerFontSize*0.75));
                                    subtitleModFontSize = parseInt((subtitleFontSize*0.75));
                                }
                                else
                                {
                                    headerModFontSize = parseInt((headerFontSize*0.55));
                                    subtitleModFontSize = parseInt((subtitleFontSize*0.55));
                                }
                            }
                        }
                        if(b > 288)
                        {
                            clockFontSizeMod = 18;
                        }
                        else
                        {
                            if(b > 217)
                            {
                                clockFontSizeMod = parseInt((18*0.8));
                            }
                            else
                            {
                                if(b >= 188)
                                {
                                    clockFontSizeMod = parseInt((18*0.7));
                                }
                                else
                                {
                                    if(b >= 136)
                                    {
                                        clockFontSizeMod = parseInt((18*0.55));
                                    }
                                    else
                                    {
                                        clockFontSizeMod = parseInt((18*0.43));
                                    }
                                }

                            }
                        }
                        $("#dashboardTitle").css("font-size", headerModFontSize + "pt");
                        $("#dashboardSubtitle").css("font-size", subtitleModFontSize + "pt");
                        $("#clock").css("font-size", clockFontSizeMod + "pt");
                    });
                    
                    //Reperimento elenco degli schedulers
                    $.ajax({
                        url: "get_data.php",
                        data: {action: "getSchedulers"},
                        type: "GET",
                        async: false,
                        dataType: 'json',
                        success: function (data) {
                            for(var i = 0; i < data.length; i++) 
                            {
                                elencoScheduler[i] = data[i];
                                $('#inputSchedulerWidget').append('<option>' + elencoScheduler[i].name + '</option>');
                                $('#inputSchedulerWidgetM').append('<option>' + elencoScheduler[i].name + '</option>');
                            }
                        }
                    });
                    
                    //Modifica della dashboard attuale
                    $('#button_modify_dashboard').click(function() 
                    {
                        var subtitleSize = null;
                        var form = $('form').get(1);
                        var formData = new FormData(form);
                        
                        if(formData.get('inputTitleDashboard') === '') 
                        {
                            formData.set('inputTitleDashboard', datoTitle);
                        } 
                        
                        if(formData.get('headerFontSize') === '')
                        {
                            formData.set('headerFontSize', 45);
                            subtitleSize = 24;
                        }
                        else
                        {
                            if(formData.get('headerFontSize') > 45)
                            {
                               headerFontSize = 45; 
                               subtitleSize = 24;
                               $("#headerFontSize").val(45);
                               formData.set('headerFontSize', headerFontSize); 
                            }
                            else
                            {
                                subtitleSize = parseInt(parseInt(formData.get('headerFontSize'))*0.25);
                                if(subtitleSize < 24)
                                {
                                    subtitleSize = 24;
                                }
                            }
                        }
                        
                        if(formData.get('headerFontColor') === '')
                        {
                            formData.set('headerFontColor', '#ffffff');
                        }

                        if(formData.get('inputSubTitleDashboard') === '') 
                        {
                            formData.set('inputSubTitleDashboard', datoSubtitle);
                        }
                        
                        if(formData.get('inputDashCol') === '') 
                        {
                            formData.set('inputDashCol', datoColor);
                        } 
                        
                        if(formData.get('inputWidthDashboard') === '') 
                        {
                            formData.set('inputWidthDashboard', num_cols);
                        } 

                        if(formData.get('inputDashBckCol') === '') 
                        {
                            formData.set('inputDashBckCol', dato_back);
                        } 

                        if (formData.get('inputDashExtCol') === '') 
                        {
                            formData.set('inputDashExtCol', external_color);
                        } 
                        
                        if(formData.get('dashboardLogoLinkInput') === '')
                        {
                            formData.set('dashboardLogoLinkInput', logoLink);
                        }
                        
                        if(formData.get('remainsWidthDashboard') === '') 
                        {
                            formData.set('remainsWidthDashboard', datoRemains);
                        }
                        
                        formData.set('ident', indicatore);
                        
                        jQuery.ajax({
                            url: 'save_config_dash.php',
                            data: formData,
                            processData: false,
                            contentType: false,  
                            type: 'POST',
                            success: function(data)
                            {
                                location.reload(true);
                            }
                        });
                    });

                    $.ajax({
                        url: "get_data.php",
                        data: {action: "get_metrics"},
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            for (var i = 0; i < data.length; i++)
                            {
                                array_metrics[i] = {desc: data[i]['descMetric'],
                                    descShort: data[i]['descShortMetric'],
                                    id: data[i]['idMetric'],
                                    type: data[i]['typeMetric'],
                                    area: data[i]['areaMetric'],
                                    source: data[i]['sourceMetric'],
                                    freq: data[i]['freqMetric'],
                                    status: data[i]['statusMetric'],
                                    widgets: data[i]['widgets'],
                                    municipalityOption: data[i]['municipalityOptionMetric'],
                                    timeRangeOption: data[i]['timeRangeOptionMetric'],
                                    colorDefault: data[i]['colorDefaultMetric']};

                                $('#select-metric').append('<option>' + array_metrics[i]['id'] + '</option>');
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');

                        }
                    });

                    $('#modal-add-widget').on('shown.bs.modal', function () {
                        udpate_select_metric();
                    });

                    $('#select-metric').change(udpate_select_metric = function () 
                    {
                        var str = "";
                        $("#select-metric option:selected").each(function () {
                            str += $(this).text();
                        });
                        var value_text = "";
                        for (var j = 0; j < array_metrics.length; j++) 
                        {
                            if (array_metrics[j]['id'] == str) 
                            {
                                value_text += "Description: " + array_metrics[j]['desc'] + ".\n";
                                value_text += "Metric Typology: " + array_metrics[j]['type'] + ".\n";
                                value_text += "Data Area: " + array_metrics[j]['area'] + ".\n";
                                value_text += "Data Source: " + array_metrics[j]['source'] + ".\n";
                                value_text += "Refresh rate: " + array_metrics[j]['freq'] + " s\n";
                                value_text += "Status: " + array_metrics[j]['status'] + ".";
                                $("#textarea-metric-c").val(value_text);
                                $("#inputFreqWidget").val(array_metrics[j]['freq']);
                                $("#inputTitleWidget").val(array_metrics[j]['descShort']);

                                if ($("#select-widget").is(':enabled')) {
                                    $("#select-widget").find('option').remove().end();
                                    for (var k = 0; k < array_metrics[j]['widgets'].length; k++) {
                                        $("#select-widget").append('<option>' + array_metrics[j]['widgets'][k]['id_type_widget'] + '</option>');
                                        update_select_widget();
                                    }
                                }
                                
                                //Nuovo codice per form personalizzato: espanderlo con altri case via via che si implementa il form personalizzato.
                                /*switch($('#select-metric').val())
                                {
                                    case "Process":
                                        $("#inputComuneRow").css("display", "");
                                        $("label[for='inputComuneWidget']").css("display", "");
                                        $("#bckColorLabel").html("Background color");
                                        $('#inputComuneWidget').css("display", "");
                                        $('#link_help_modal-add-widget').css("display", "");
                                        if (array_metrics[j]['municipalityOption'] == 0) 
                                        {
                                            $('#inputComuneWidget').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidget').attr('disabled', false);
                                            $('#inputComuneWidget').prop('required', true);
                                        }
                                        $("#schedulerRow").css("display", "");
                                        $("label[for='inputSchedulerWidget']").css("display", "");
                                        $('#inputSchedulerWidgetDiv').css("display", "");
                                        $('#inputSchedulerWidgetGroupDiv').css("display", "");
                                        $('#inputSchedulerWidget').css("display", "");
                                        $('#inputSchedulerWidget').prop('selectedIndex', -1);
                                        
                                        //Reperimento elenco dei jobs groups per scheduler
                                        for(var i = 0; i < elencoScheduler.length; i++)
                                        {
                                            $.ajax({
                                                url: "getJobs.php",
                                                data: {action: "getJobGroupsForScheduler", host: elencoScheduler[i].ip, user: elencoScheduler[i].user, pass: elencoScheduler[i].pass},
                                                type: "POST",
                                                async: false,
                                                dataType: 'json',
                                                success: function (data) 
                                                {
                                                    for(var j = 0; j < data.length; j++) 
                                                    {
                                                        elencoJobsGroupsPerScheduler[i][j] = data[j].id;
                                                    }
                                                }
                                            });
                                        }
                                        break;
                                    
                                    case "ProtezioneCivile":
                                        $("#inputComuneRow").css("display", "");
                                        $("label[for='inputComuneWidget']").css("display", "");
                                        $('#inputComuneWidget').css("display", "");
                                        $('#link_help_modal-add-widget').css("display", "");
                                        $('#inputUrlWidget').val('http://protezionecivile.comune.fi.it/');
                                        $('#inputTitleWidget').val('');
                                        $("#bckColorLabel").html("Background color");
                                        $('#inputTitleWidget').attr('disabled', true);
                                        $('#inputTitleWidget').prop('required', false);
                                        $('#inputUdmWidget').attr('disabled', true);
                                        $('#inputUdmWidget').prop('required', false);
                                        $('#select-IntTemp-Widget').attr('disabled', true);
                                        $('#select-IntTemp-Widget').prop('required', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRow").css("display", "");
                                        $("label[for='inputSchedulerWidget']").css("display", "none");
                                        $('#inputSchedulerWidgetDiv').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                        $('#inputSchedulerWidget').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRow').css("display", "none");
                                        $("label[for='inputJobsAreasWidget']").css("display", "none");
                                        $('#inputJobsAreasWidgetDiv').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsAreasWidget').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRow').css("display", "none");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsGroupsWidget').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRow').css("display", "none");
                                        $("label[for='inputJobsNamesWidget']").css("display", "none");
                                        $('#inputJobsNamesWidgetDiv').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsNamesWidget').css("display", "none");
                                        
                                        if (array_metrics[j]['municipalityOption'] == 0) 
                                        {
                                            $('#inputComuneWidget').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidget').attr('disabled', false);
                                            $('#inputComuneWidget').prop('required', true);
                                        }
                                        break;
                                        
                                    case "Button":
                                        $('#inputUrlWidget').val('');
                                        $("#inputComuneRow").css("display", "");
                                        $("label[for='inputComuneWidget']").css("display", "");
                                        $('#inputComuneWidget').css("display", "");
                                        $("#titleLabel").html("Button text");
                                        $("#bckColorLabel").html("Button color");
                                        $('#inputFontSize').val(14);
                                        $('#inputFontColor').val("#000000");
                                        $('#widgetFontColor').css("background-color", "#000000");
                                        $('#link_help_modal-add-widget').css("display", "");
                                        $('#inputUdmWidget').attr('disabled', true);
                                        $('#inputFrameColorWidget').attr('disabled', true);
                                        $('#inputFrameColorWidget').val('');
                                        $('#inputFrameColorWidget').prop('required', false);
                                        $('#select-IntTemp-Widget').attr('disabled', true);
                                        $('#select-IntTemp-Widget').prop('required', false);
                                        $('#inputFreqWidget').attr('disabled', true);
                                        $('#inputFreqWidget').val('');
                                        $('#inputFreqWidget').prop('required', false);
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRow").css("display", "");
                                        $("label[for='inputSchedulerWidget']").css("display", "none");
                                        $('#inputSchedulerWidgetDiv').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                        $('#inputSchedulerWidget').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRow').css("display", "none");
                                        $("label[for='inputJobsAreasWidget']").css("display", "none");
                                        $('#inputJobsAreasWidgetDiv').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsAreasWidget').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRow').css("display", "none");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsGroupsWidget').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRow').css("display", "none");
                                        $("label[for='inputJobsNamesWidget']").css("display", "none");
                                        $('#inputJobsNamesWidgetDiv').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsNamesWidget').css("display", "none");
                                        
                                        if (array_metrics[j]['municipalityOption'] == 0) 
                                        {
                                            $('#inputComuneWidget').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidget').attr('disabled', false);
                                            $('#inputComuneWidget').prop('required', true);
                                        }
                                        break;
                                        
                                    case "Button":
                                        $('#inputUrlWidget').val('');
                                        $("#inputComuneRow").css("display", "");
                                        $("label[for='inputComuneWidget']").css("display", "");
                                        $('#inputComuneWidget').css("display", "");
                                        $("#titleLabel").html("Button text");
                                        $("#bckColorLabel").html("Button color");
                                        $('#inputFontSize').val(14);
                                        $('#inputFontColor').val("#000000");
                                        $('#widgetFontColor').css("background-color", "#000000");
                                        $('#link_help_modal-add-widget').css("display", "");
                                        $('#inputUdmWidget').attr('disabled', true);
                                        $('#inputFrameColorWidget').attr('disabled', true);
                                        $('#inputFrameColorWidget').val('');
                                        $('#inputFrameColorWidget').prop('required', false);
                                        $('#select-IntTemp-Widget').attr('disabled', true);
                                        $('#select-IntTemp-Widget').prop('required', false);
                                        $('#inputFreqWidget').attr('disabled', true);
                                        $('#inputFreqWidget').val('');
                                        $('#inputFreqWidget').prop('required', false);
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRow").css("display", "");
                                        $("label[for='inputSchedulerWidget']").css("display", "none");
                                        $('#inputSchedulerWidgetDiv').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                        $('#inputSchedulerWidget').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRow').css("display", "none");
                                        $("label[for='inputJobsAreasWidget']").css("display", "none");
                                        $('#inputJobsAreasWidgetDiv').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsAreasWidget').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRow').css("display", "none");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsGroupsWidget').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRow').css("display", "none");
                                        $("label[for='inputJobsNamesWidget']").css("display", "none");
                                        $('#inputJobsNamesWidgetDiv').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsNamesWidget').css("display", "none");
                                        
                                        if (array_metrics[j]['municipalityOption'] == 0) 
                                        {
                                            $('#inputComuneWidget').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidget').attr('disabled', false);
                                            $('#inputComuneWidget').prop('required', true);
                                        }
                                        break;    
                                    
                                    default:
                                        $('#inputUrlWidget').val('');
                                        $("#inputComuneRow").css("display", "");
                                        $("label[for='inputComuneWidget']").css("display", "");
                                        $('#inputComuneWidget').css("display", "");
                                        $('#inputFontSize').val("");
                                        $('#inputFontColor').val("");
                                        $('#widgetFontColor').css("background-color", "#eeeeee");
                                        $("#titleLabel").html("Title");
                                        $("#bckColorLabel").html("Background color");
                                        $('#link_help_modal-add-widget').css("display", "");
                                        $('#inputUdmWidget').attr('disabled', false);
                                        $('#inputFrameColorWidget').attr('disabled', false);
                                        $('#inputFrameColorWidget').val('#eeeeee');
                                        $('#inputFrameColorWidget').prop('required', false);
                                        $('#select-IntTemp-Widget').attr('disabled', false);
                                        $('#select-IntTemp-Widget').prop('required', false);
                                        $('#inputFreqWidget').attr('disabled', false);
                                        $('#inputFreqWidget').val(60);
                                        $('#inputFreqWidget').prop('required', true)
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRow").css("display", "");
                                        $("label[for='inputSchedulerWidget']").css("display", "none");
                                        $('#inputSchedulerWidgetDiv').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                        $('#inputSchedulerWidget').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRow').css("display", "none");
                                        $("label[for='inputJobsAreasWidget']").css("display", "none");
                                        $('#inputJobsAreasWidgetDiv').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsAreasWidget').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRow').css("display", "none");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsGroupsWidget').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRow').css("display", "none");
                                        $("label[for='inputJobsNamesWidget']").css("display", "none");
                                        $('#inputJobsNamesWidgetDiv').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsNamesWidget').css("display", "none");
                                        
                                        if (array_metrics[j]['municipalityOption'] == 0) 
                                        {
                                            $('#inputComuneWidget').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidget').attr('disabled', false);
                                            $('#inputComuneWidget').prop('required', true);
                                        }
                                        break;
                                }*/  //Fine switch
                            }
                        }
                    }).change();
                    
                    
                    $('#inputSchedulerWidget').change(update_select_scheduler = function () 
                    {
                        if($('#inputSchedulerWidget').css("display") != "none")
                        {
                            var selectedIndex = $('#inputSchedulerWidget').prop('selectedIndex');
                            switch(selectedIndex)
                            {
                                case 0:
                                    $('#inputUrlWidget').val('http://192.168.0.69/sce/');
                                    break;
                                    
                                case 1:
                                    $('#inputUrlWidget').val('http://192.168.0.23/sce/');
                                    break;
                                    
                                case 2:
                                    $('#inputUrlWidget').val('http://192.168.0.71/sce/');
                                    break;
                            }
                            $("#schedulerName").val(elencoScheduler[selectedIndex].name);
                            $("#host").val(elencoScheduler[selectedIndex].ip);
                            $("#user").val(elencoScheduler[selectedIndex].user);
                            $("#pass").val(elencoScheduler[selectedIndex].pass);
                            $.ajax({
                                url: "get_data.php",
                                data: {action: "getJobAreas", schedulerId: elencoScheduler[selectedIndex].id},
                                type: "GET",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    $('#inputJobsAreasWidget').empty();
                                    $('#inputJobsGroupsWidget').empty();
                                    $('#inputJobsNamesWidget').empty();
                                    if(data[0].id == "none")
                                    {
                                        //Nessuna JOBS AREA
                                        $('#jobsAreasRow').css("display", "none");
                                        $("label[for='inputJobsAreasWidget']").css("display", "none");
                                        $('#inputJobsAreasWidgetDiv').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsAreasWidget').css("display", "none");
                                        
                                        //Caricamento dei jobs groups per lo scheduler selezionato.
                                        for(var i = 0; i < elencoJobsGroupsPerScheduler[selectedIndex].length; i++)
                                        {
                                            $('#inputJobsGroupsWidget').append('<option>' + elencoJobsGroupsPerScheduler[selectedIndex][i] + '</option>');
                                        }
                                        
                                        //Viene mostrato il campo per i jobs groups dello scheduler selezionato
                                        $('#jobsGroupsRow').css("display", "");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "");
                                        $('#inputJobsGroupsWidget').css("display", "");
                                        $('#inputJobsGroupsWidget').prop('selectedIndex', -1);
                                        
                                        //Viene mostrato il campo dei jobs names per il group selezionato
                                        $('#jobsNamesRow').css("display", "");
                                        $('#inputJobsNamesWidgetDiv').css("display", "");
                                        $('#inputJobsNamesWidgetGroupDiv').css("display", "");
                                        $("label[for='inputJobsNamesWidget']").css("display", "");
                                        $('#inputJobsNamesWidget').css("display", "");
                                        $('#inputJobsNamesWidget').prop('selectedIndex', -1);
                                    }
                                    else
                                    {
                                        //Ramo con le jobs areas
                                        
                                        //Non mostrare i job groups per scheduler
                                        $('#jobsGroupsRow').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                        $('#inputJobsGroupsWidget').css("display", "none");
                                        
                                        //Mostrare le JOBS AREAS
                                        $('#jobsAreasRow').css("display", "");
                                        $("label[for='inputJobsAreasWidget']").css("display", "");
                                        $('#inputJobsAreasWidgetDiv').css("display", "");
                                        $('#inputJobsAreasWidgetGroupDiv').css("display", "");
                                        $('#inputJobsAreasWidget').css("display", "");
                                        for(var i = 0; i < data.length; i++)
                                        {
                                            $('#inputJobsAreasWidget').append('<option>' + data[i]['name'] + '</option>');
                                        }
                                        $('#inputJobsAreasWidget').prop("selectedIndex", -1);
                                        
                                        //Viene mostrato il campo per i jobs groups della job area selezionata
                                        $('#jobsGroupsRow').css("display", "");
                                        $('#inputJobsGroupsWidgetGroupDiv').css("display", "");
                                        $('#inputJobsGroupsWidgetDiv').css("display", "");
                                        $("label[for='inputJobsGroupsWidget']").css("display", "");
                                        $('#inputJobsGroupsWidget').css("display", "");
                                        $('#inputJobsGroupsWidget').prop('selectedIndex', -1);
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) 
                                {

                                    //$('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                                    console.log('jqXHR:');
                                    console.log(jqXHR);
                                    console.log('textStatus:');
                                    console.log(textStatus);
                                    console.log('errorThrown:');
                                    console.log(errorThrown);
                                    }

                            });    
                        }
                    }).change();
                    
                    
                    $('#inputJobsAreasWidget').change(update_select_scheduler = function ()
                    {
                       if($('#inputJobsAreasWidget').css("display") != "none")
                       {
                           $("#jobArea").val($('#inputJobsAreasWidget').val());
                           $('#inputJobsGroupsWidget').empty();
                           var selectedSchedulerIndex = $('#inputSchedulerWidget').prop('selectedIndex');
                           var areaSelected = $('#inputJobsAreasWidget').val();
                           var keyword = null;
                           switch(areaSelected)
                           {
                                case "Stato linee ATAF":
                                   keyword = "linea";
                                   break;
                                   
                                case "Check RT":
                                   keyword = "check";
                                   break;
                                   
                                case "Eventi a Firenze":
                                   keyword = "Eventi";
                                   break;
                                   
                                case "Parcheggi":
                                   keyword = "parcheggi";
                                   break;
                                   
                                case "Previsioni meteo":
                                   keyword = "meteo";
                                   break;
                                   
                                case "Sensori":
                                   keyword = "sensori";
                                   break;   
                           }
                           $.ajax({
                                url: "getJobs.php",
                                data: {action: "getJobGroupsForJobArea", 
                                       host: elencoScheduler[selectedSchedulerIndex].ip, 
                                       user: elencoScheduler[selectedSchedulerIndex].user, 
                                       pass: elencoScheduler[selectedSchedulerIndex].pass,
                                       keyword: keyword
                                   },
                                type: "POST",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    for(var j = 0; j < data.length; j++) 
                                    {
                                        $('#inputJobsGroupsWidget').append('<option>' + data[j].jobGroup + '</option>');
                                    }
                                    $('#inputJobsGroupsWidget').prop('selectedIndex', -1);
                                    //Viene mostrato il campo dei jobs names per il group selezionato
                                    $('#jobsNamesRow').css("display", "");
                                    $('#inputJobsNamesWidgetDiv').css("display", "");
                                    $('#inputJobsNamesWidgetGroupDiv').css("display", "");
                                    $("label[for='inputJobsNamesWidget']").css("display", "");
                                    $('#inputJobsNamesWidget').css("display", "");
                                    $('#inputJobsNamesWidget').prop('selectedIndex', -1);
                                }
                            });
                           
                       }
                    }).change();
                    
                    
                    $('#inputJobsGroupsWidget').change(update_select_scheduler = function () 
                    {
                        if($('#inputJobsGroupsWidget').css("display") != "none")
                        {
                            $('#inputJobsNamesWidget').empty();
                            var selectedScheduler = $('#inputSchedulerWidget').prop('selectedIndex');
                            var selectedGroup = $('#inputJobsGroupsWidget').val();
                            $("#jobGroup").val(selectedGroup);
                            
                            //Reperimento elenco dei jobs names per il job group selezionato
                            $.ajax({
                                url: "getJobs.php",
                                data: {
                                    action: "getJobNamesForJobGroup", 
                                    host: elencoScheduler[selectedScheduler].ip, 
                                    user: elencoScheduler[selectedScheduler].user, 
                                    pass: elencoScheduler[selectedScheduler].pass,
                                    jobGroup: selectedGroup
                                },
                                type: "POST",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    for(var j = 0; j < data.length; j++) 
                                    {
                                        $('#inputJobsNamesWidget').append('<option>' + data[j].jobName + '</option>');
                                    }
                                    $('#inputJobsNamesWidget').prop('selectedIndex', -1);
                                },
                                error: function (jqXHR, textStatus, errorThrown) 
                                {
                                    console.log('jqXHR:');
                                    console.log(jqXHR);
                                    console.log('textStatus:');
                                    console.log(textStatus);
                                    console.log('errorThrown:');
                                    console.log(errorThrown);
                                }
                            });
                        }
                    }).change();
                    
                   
                   $('#inputJobsNamesWidget').change(update_select_scheduler = function () 
                    {
                        if($('#inputJobsNamesWidget').css("display") != "none")
                        {
                            $('#jobName').val($('#inputJobsNamesWidget').val());
                        }
                    }).change();
                    
                    /*Listener aggiunta nuovo widget per cambio widget selezionato*/
                    $('#select-widget').change(update_select_widget = function () 
                    {
                        var dimMapRaw = null;
                        var dimMap = null;
                        
                        //quando si cambia il widget selezionato di default le righe delle dimensioni devono essere vuote per impedire che si accumulino valori. Tanto si rimepie ad ogni change
                        $("#inputSizeRowsWidget").empty();
                        $("#inputSizeColumnsWidget").empty();
                        
                        var str = "";
                        $("#select-metric option:selected").each(function () {
                            str += $(this).text();
                        });

                        var str2 = "";
                        $("#select-widget option:selected").each(function () {
                            str2 += $(this).text();
                        });

                        for (var i = 0; i < array_metrics.length; i++) 
                        {
                            if (array_metrics[i]['id'] == str) {
                                for (var k = 0; k < array_metrics[i]['widgets'].length; k++) 
                                {
                                    if (array_metrics[i]['widgets'][k]['id_type_widget'] == str2) 
                                    {
                                        //creare un elenco di dimensioni minime e massime di colonne dei widget
                                        var minR = parseInt(array_metrics[i]['widgets'][k]['size_rows_widget']);
                                        var maxR = parseInt(array_metrics[i]['widgets'][k]['max_rows_widget']);
                                        var minC = parseInt(array_metrics[i]['widgets'][k]['size_columns_widget']);
                                        var maxC = parseInt(array_metrics[i]['widgets'][k]['max_columns_widget']);
                                        var range_value = parseInt(array_metrics[i]['widgets'][k]['numeric_range']);
                                        dimMapRaw = (array_metrics[i]['widgets'][k]['dimMap']);
                                        if((dimMapRaw != null) && (dimMapRaw != "null") && (typeof dimMapRaw != "undefined") && (dimMapRaw != ""))
                                        {
                                            dimMap = JSON.parse(dimMapRaw);
                                        }
                                        
                                        if (range_value == 1) {
                                            $("#value_range").show();
                                        } else {
                                            $("#value_range").hide();
                                        }


                                        if(dimMap == null)
                                        {
                                            $("#inputSizeRowsWidget").off();
                                            if (minR == maxR)
                                            {
                                                $("#inputSizeRowsWidget").append('<option>' + minR + '</option>');
                                            } 
                                            else
                                            {
                                                for (var n = minR; n <= maxR; n++)
                                                {
                                                    $("#inputSizeRowsWidget").append('<option>' + n + '</option>');
                                                }

                                            }
                                            if (minC == maxC)
                                            {
                                                $("#inputSizeColumnsWidget").append('<option>' + minC + '</option>');
                                            } 
                                            else
                                            {
                                                for (var p = minC; p <= maxC; p++)
                                                {
                                                    $("#inputSizeColumnsWidget").append('<option>' + p + '</option>');
                                                }
                                            }
                                        }
                                        else
                                        {
                                            var z;
                                            var selectedIndex = null;
                                            $("#inputSizeRowsWidget").empty();
                                            $("#inputSizeColumnsWidget").empty();
                                            for(z = 0; z < dimMap.dimMap.length; z++)
                                            {
                                                $("#inputSizeRowsWidget").append('<option>' + dimMap.dimMap[z].rows + '</option>');
                                            }
                                            
                                            $("#inputSizeColumnsWidget").append('<option>' + dimMap.dimMap[0].cols + '</option>');
                                            
                                            $("#inputSizeRowsWidget").off();
                                            
                                            $("#inputSizeRowsWidget").change(function() {
                                                selectedIndex = $("#inputSizeRowsWidget").prop("selectedIndex");
                                                $("#inputSizeColumnsWidget").empty();
                                                $("#inputSizeColumnsWidget").append('<option>' + dimMap.dimMap[selectedIndex].cols + '</option>');
                                            });
                                            
                                        }
                                        
                                        $("#add-n-metrcis-widget").text("max " + array_metrics[i]['widgets'][k]['number_metrics_widget'] + " metrics");
                                        //aggiunta dell'istruzione per inviare dati sul messaggio informativo al database e al file process-form.php
                                        //$("#textarea-information-metrics").text();


                                        if (array_metrics[i]['widgets'][k]['color_widgetOption'] == 0) {
                                            $('#inputColorWidget').attr('readonly', true);
                                            if ((array_metrics[i]['colorDefault'] != null) && (array_metrics[i]['colorDefault'].length != 0)) {
                                                $('.color-choice').colorpicker('setValue', array_metrics[i]['colorDefault']);
                                            }
                                        } else {
                                            $('#inputColorWidget').attr('readonly', false);

                                        }

                                        if ((str2 == "widgetTimeTrend") || (str2 == "widgetTimeTrendCompare")) {
                                            $('#select-IntTemp-Widget').empty();
                                            $('#select-IntTemp-Widget').append('<option value="4 Ore">4 Hours</option>');
                                            $('#select-IntTemp-Widget').append('<option value="12 Ore">12 Hours</option>');
                                            $('#select-IntTemp-Widget').append('<option value="Giornaliera">Daily</option>');
                                            $('#select-IntTemp-Widget').append('<option value="Settimanale">Weekly</option>');
                                            $('#select-IntTemp-Widget').append('<option value="Mensile">Monthly</option>');
                                            $('#select-IntTemp-Widget').append('<option value="Annuale">Annually</option>');
                                        } 
                                        else 
                                        {
                                            $('#select-IntTemp-Widget').empty();
                                            $('#select-IntTemp-Widget').append('<option value="Nessuno">No</option>');
                                        }
                                    }
                                }
                            }
                        }//Fine del for
                        
                        //Nuovo codice per form personalizzato: espanderlo con altri case via via che si implementa il form personalizzato.
                        switch($('#select-widget').val())
                        {
                            case "widgetProcess":
                                $("#inputComuneRow").css("display", "");
                                $('#inputTitleWidget').prop('required', true);
                                $("label[for='inputComuneWidget']").css("display", "");
                                $("#bckColorLabel").html("Background color");
                                $('#inputComuneWidget').css("display", "");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUrlWidget').attr('disabled', false);
                                $('#inputUrlWidget').prop('required', true);
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "");
                                $('#inputSchedulerWidgetDiv').css("display", "");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "");
                                $('#inputSchedulerWidget').css("display", "");
                                $('#inputSchedulerWidget').prop('selectedIndex', -1);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);

                                //Reperimento elenco dei jobs groups per scheduler
                                for(var i = 0; i < elencoScheduler.length; i++)
                                {
                                    $.ajax({
                                        url: "getJobs.php",
                                        data: {action: "getJobGroupsForScheduler", host: elencoScheduler[i].ip, user: elencoScheduler[i].user, pass: elencoScheduler[i].pass},
                                        type: "POST",
                                        async: false,
                                        dataType: 'json',
                                        success: function (data) 
                                        {
                                            for(var j = 0; j < data.length; j++) 
                                            {
                                                elencoJobsGroupsPerScheduler[i][j] = data[j].id;
                                            }
                                        }
                                    });
                                }
                                break;

                            case "widgetProtezioneCivile":
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUrlWidget').val('http://protezionecivile.comune.fi.it/');
                                $("#titleLabel").html("Title");
                                $('#inputTitleWidget').val('');
                                $("#bckColorLabel").html("Background color");
                                $('#inputTitleWidget').attr('disabled', true);
                                $('#inputTitleWidget').prop('required', false);
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputUdmWidget').prop('required', false);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val('60');
                                $('#inputFreqWidget').prop('required', true);
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");

                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                
                                $('#inputComuneWidget').attr('disabled', true);
                                break;

                            case "widgetButton":
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Button text");
                                $("#bckColorLabel").html("Button color");
                                $('#inputFontSize').val(14);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').val('');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', true);
                                $('#inputFreqWidget').val('');
                                $('#inputFreqWidget').prop('required', false);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                            
                            case "widgetSingleContent":
                                $('#inputUrlWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetGenericContent":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetBarContent":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("16");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetColunmContent":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("20");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetGaugeChart":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontSize').prop('required', false);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetPieChart":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("13");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontSize').prop('required', true);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetSce":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontSize').prop('required', false);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetSmartDS":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("16");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontColor').val("#000000");
                                $('#widgetFontColor').css("background-color", "#000000");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetTimeTrend":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("11");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontColor').val("#666666");
                                $('#widgetFontColor').css("background-color", "#666666");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', false);
                                $('#select-IntTemp-Widget').prop('required', true);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetTimeTrendCompare":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("11");
                                $('#inputFontSize').attr('disabled', false);
                                $('#inputFontColor').val("#666666");
                                $('#widgetFontColor').css("background-color", "#666666");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', false);
                                $('#select-IntTemp-Widget').prop('required', true);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                $('#inputComuneWidget').attr('disabled', true);
                                break;
                                
                            case "widgetEvents":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputUrlWidget').attr('disabled', true);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;
                                
                            case "widgetTrendMentions":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputUrlWidget').attr('disabled', true);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;
                                
                            case "widgetPrevMeteo":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputUrlWidget').attr('disabled', false);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;
                                
                            case "widgetServiceMap":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', false);
                                $('#inputUrlWidget').attr('disabled', true);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;
                                
                            case "widgetSpeedometer":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputUrlWidget').attr('disabled', false);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputUdmWidget').val('');
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;
                                
                            case "widgetStateRideAtaf":
                                $('#inputTitleWidget').val('');
                                $('#inputTitleWidget').prop('required', true);
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputComuneWidget').attr('disabled', true);
                                $('#inputUrlWidget').attr('disabled', false);
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#inputFontSize').val("");
                                $('#inputFontSize').attr('disabled', true);
                                $('#inputFontColor').val("");
                                $('#inputFontColor').attr('disabled', true);
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', true);
                                $('#inputUdmWidget').val('');
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').val(-1);
                                $('#select-IntTemp-Widget').attr('disabled', true);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true);
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                break;    

                            default:
                                $('#inputUrlWidget').val('');
                                $("#inputComuneRow").css("display", "");
                                $("label[for='inputComuneWidget']").css("display", "");
                                $('#inputComuneWidget').css("display", "");
                                $('#inputFontSize').val("");
                                $('#inputFontColor').val("");
                                $('#widgetFontColor').css("background-color", "#eeeeee");
                                $("#titleLabel").html("Title");
                                $("#bckColorLabel").html("Background color");
                                $('#link_help_modal-add-widget').css("display", "");
                                $('#inputUdmWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').attr('disabled', false);
                                $('#inputFrameColorWidget').val('#eeeeee');
                                $('#inputFrameColorWidget').prop('required', false);
                                $('#select-IntTemp-Widget').attr('disabled', false);
                                $('#select-IntTemp-Widget').prop('required', false);
                                $('#inputFreqWidget').attr('disabled', false);
                                $('#inputFreqWidget').val(60);
                                $('#inputFreqWidget').prop('required', true)
                                //Rimozione menu SCHEDULER
                                $("#schedulerRow").css("display", "");
                                $("label[for='inputSchedulerWidget']").css("display", "none");
                                $('#inputSchedulerWidgetDiv').css("display", "none");
                                $('#inputSchedulerWidgetGroupDiv').css("display", "none");
                                $('#inputSchedulerWidget').css("display", "none");
                                //Rimozione menu JOBS AREA
                                $('#jobsAreasRow').css("display", "none");
                                $("label[for='inputJobsAreasWidget']").css("display", "none");
                                $('#inputJobsAreasWidgetDiv').css("display", "none");
                                $('#inputJobsAreasWidgetGroupDiv').css("display", "none");
                                $('#inputJobsAreasWidget').css("display", "none");
                                //Rimozione menu JOB GROUPS
                                $('#jobsGroupsRow').css("display", "none");
                                $("label[for='inputJobsGroupsWidget']").css("display", "none");
                                $('#inputJobsGroupsWidgetDiv').css("display", "none");
                                $('#inputJobsGroupsWidgetGroupDiv').css("display", "none");
                                $('#inputJobsGroupsWidget').css("display", "none");
                                //Rimozione menu JOB NAMES
                                $('#jobsNamesRow').css("display", "none");
                                $("label[for='inputJobsNamesWidget']").css("display", "none");
                                $('#inputJobsNamesWidgetDiv').css("display", "none");
                                $('#inputJobsNamesWidgetGroupDiv').css("display", "none");
                                $('#inputJobsNamesWidget').css("display", "none");
                                
                                $('#inputComuneWidget').attr('disabled', false);
                                break;
                        }//Fine switch

                    }).change();

                    $('#button_add_metric_widget').click(function () 
                    {
                        if ($('#select-metric').val()) 
                        {
                            var value_selected = $('#select-metric').val();
                            for (var i = 0; i < array_metrics.length; i++) 
                            {
                                if (array_metrics[i]['id'] == ($("#select-metric option:selected").val())) 
                                {
                                    for (var k = 0; k < array_metrics[i]['widgets'].length; k++) 
                                    {
                                        if (array_metrics[i]['widgets'][k]['id_type_widget'] == ($("#select-widget option:selected").val())) 
                                        {
                                            if ($('#textarea-selected-metrics').val()) 
                                            {
                                                var content = $('#textarea-selected-metrics').val();
                                                if ((content.split("+")).length == array_metrics[i]['widgets'][k]['number_metrics_widget']) 
                                                {
                                                    alert("Maximum number of metric for widget reached!!!");
                                                } 
                                                else 
                                                {
                                                    content += "+" + value_selected;
                                                    $('#textarea-selected-metrics').val(content);
                                                }
                                            } 
                                            else 
                                            {
                                                $('#textarea-selected-metrics').val(value_selected);
                                            }

                                            if (array_metrics[i]['widgets'][k]['number_metrics_widget'] > 1) 
                                            {
                                                $('#select-metric').empty();
                                                for (var j = 0; j < array_metrics.length; j++) 
                                                {
                                                    for (var l = 0; l < array_metrics[j]['widgets'].length; l++) 
                                                    {
                                                        if (array_metrics[j]['widgets'][l]['id_type_widget'] == ($("#select-widget option:selected").val())) 
                                                        {
                                                            $('#select-metric').append('<option>' + array_metrics[j]['id'] + '</option>');
                                                        }
                                                    }
                                                }
                                                $('#select-widget').prop('disabled', true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });

                    $('.link_help_modal-add-widget').click(function () {
                        $('#modal-info-context').modal('show');
                    });

                    //Salvataggio dashboard
                    $('#link_save_configuration').click(function () {
                        var gridster_actual = $(".gridster ul").gridster().data('gridster');
                        var widgets = JSON.stringify(gridster_actual.serialize());
                        $.ajax({
                            url: "save_config_widgets.php",
                            data: {configuration_widgets: widgets},
                            type: "POST",
                            async: true,
                            success: function (data) {
                                if (data == 1) {
                                    alert('Dashboard configuration was saved successfully');
                                    window.location.reload(true);
                                } else {
                                    alert('Error: repeat saving of dashboard configuration');
                                    window.location.reload(true);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                                console.log('jqXHR:');
                                console.log(jqXHR);
                                console.log('textStatus:');
                                console.log(textStatus);
                                console.log('errorThrown:');
                                console.log(errorThrown);
                            }
                        });
                    });


                    //Duplicazione della dashboard
                    $('#button_duplicate_dashboard').click(function () {
                        var param_duplicate_dash = {};
                        param_duplicate_dash['nomeDashAttuale'] = $('#nameCurrentDashboard').val();
                        param_duplicate_dash['nomeDashDuplicata'] = $('#nameNewDashboard').val();
                        var paramNuovaDash = param_duplicate_dash;
                        $.ajax({
                            url: "duplicate_dash.php",
                            data: {duplication_dashboard: paramNuovaDash},
                            type: "POST",
                            async: true,
                            success: function (data) 
                            {
                                alert(data);
                            }
                        });
                    });



                    $('#link_exit').click(function () {
                        var result_exit = window.confirm("Are you sure you want to exit without saving the dashboard configuration?");
                        if (result_exit) {
                            window.location.href = "dashboard_mng.php";
                        }
                    });

                    $('#button_reset_add_widget').click(function () 
                    {
                        $('#select-widget').removeAttr('disabled');
                        $('#select-widget').find('option').remove();
                        $("#add-n-metrcis-widget").empty();
                        for (var i = 0; i < array_metrics.length; i++) {
                            $('#select-metric').append('<option>' + array_metrics[i]['id'] + '</option>');
                        }
                        udpate_select_metric();
                    });


                    $(document).on('click', '.icon-remove-widget', function () {
                        var result_remove = window.confirm("Are you sure you want to permanently delete the selected widget?");
                        if (result_remove) {
                            var name_widget_r = $(this).parents('li').attr('id');
                            var op = "remove";
                            window.location.href = "update_widget.php?nameWidget=" + name_widget_r + "&operation=" + op;
                        }
                    });

                   
                    //Listener modifica widget
                    $(document).on('click', '.icon-cfg-widget', function () 
                    {
                        var name_widget_m = $(this).parents('li').attr('id');
                        var dimMapRaw = null;
                        var dimMap = null;
                        $("#inputColumn-m").empty();
                        $("#inputRows-m").empty();
                        
                        $.ajax({
                            url: "get_data.php",
                            data: {widget_to_modify: name_widget_m, action: "get_param_widget"},
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            success: function (data) 
                            {
                                $("#metricWidgetM").val(data['id_metric_widget'].replace(/\+/g, ", "));
                                $("#select-widget-m").find('option').remove().end().append('<option>' + data['widgets_metric'] + '</option>');
                                $('#select-widget-m').val(data['type_widget']);
                                $("#inputTitleWidgetM").val(data['title_widget']);
                                $("#inputFontSizeM").val(data['fontSize']);
                                $("#inputFontColorM").val(data['fontColor']);
                                $("#widgetFontColorM").css("background-color", data['fontColor']);
                                $("#widgetFontColorPickerContainerM").colorpicker('setValue', data['fontColor']);
                                $("#inputFreqWidgetM").val(data['frequency_widget']);
                                $("#inputNameWidgetM").val(data['name_widget']);
                                $("#inputComuneWidgetM").val(data['municipality_metric_widget']);
                                $('#select-IntTemp-Widget-m').val(data['temporal_range_widget']);
                                $("#mod-n-metrcis-widget").text("max " + data['number_metrics_widget'] + " metrics");
                                $("#textarea-metric-widget-m").val('');
                                $("#inputUdmM").val(data['udm']);
                                $("#urlWidgetM").val(data['url']);
                                console.log(data['url']);
                                var paramsRaw = data['param_w'];
                                var parameters = null;
                                if(paramsRaw) 
                                {
                                    parameters = JSON.parse(paramsRaw);
                                } 
                                
                                //Informazione sulle metriche.
                                CKEDITOR.instances.textareaInfoWidgetM.setData(data['info_mess']);
                                
                                //Nuovo codice per form personalizzato: espanderlo con altri case via via che si implementa il form personalizzato.
                                switch($('#select-widget-m').val())
                                {
                                    case "widgetProcess":
                                        //In questo caso mostriamo tutti i campi ad hoc del Process, sicuramente valorizzati in modifica
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#inputUdmM').prop('required', false);
                                        $('#inputUdmM').val('');
                                        $('#inputUdmM').prop('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Reperimento elenco dei jobs groups per scheduler
                                        for(var i = 0; i < elencoScheduler.length; i++)
                                        {
                                            //elencoJobsGroupsPerScheduler[i].splice(0,elencoJobsGroupsPerScheduler[i].length);
                                            $.ajax({
                                                url: "getJobs.php",
                                                data: {action: "getJobGroupsForScheduler", host: elencoScheduler[i].ip, user: elencoScheduler[i].user, pass: elencoScheduler[i].pass},
                                                type: "POST",
                                                async: false,
                                                dataType: 'json',
                                                success: function (data) 
                                                {
                                                    for(var j = 0; j < data.length; j++) 
                                                    {
                                                        elencoJobsGroupsPerScheduler[i][j] = data[j].id;
                                                    }
                                                }
                                            });
                                        }
                                        
                                        //Campo scheduler
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "");
                                        $('#inputSchedulerWidgetDivM').css("display", "");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "");
                                        $('#inputSchedulerWidgetM').css("display", "");
                                        $('#inputSchedulerWidgetM').val(parameters.schedulerName);
                                        $('#inputSchedulerWidgetM').trigger("change");
                                        
                                        //Job areas solo se presente
                                        if(parameters.jobArea != "")
                                        {
                                            $('#jobsAreasRowM').css("display", "");
                                            $("label[for='inputJobsAreasWidgetM']").css("display", "");
                                            $('#inputJobsAreasWidgetDivM').css("display", "");
                                            $('#inputJobsAreasWidgetGroupDivM').css("display", "");
                                            $('#inputJobsAreasWidgetM').css("display", "");
                                            $('#inputJobsAreasWidgetM').val(parameters.jobArea);
                                            $('#inputJobsAreasWidgetM').trigger("change");
                                        }
                                        else
                                        {
                                            $('#jobsAreasRowM').css("display", "none");
                                            $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                            $('#inputJobsAreasWidgetDivM').css("display", "none");
                                            $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                            $('#inputJobsAreasWidgetM').css("display", "none");
                                        }
                                        
                                        //Job group
                                        $('#jobsGroupsRowM').css("display", "");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "");
                                        $('#inputJobsGroupsWidgetM').css("display", "");
                                        $('#inputJobsGroupsWidgetM').val(parameters.jobGroup);
                                        $('#inputJobsGroupsWidgetM').trigger("change");
                                        
                                        //Job name
                                        $('#jobsNamesRowM').css("display", "");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "");
                                        $('#inputJobsNamesWidgetDivM').css("display", "");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "");
                                        $('#inputJobsNamesWidgetM').css("display", "");
                                        $('#inputJobsNamesWidgetM').val(parameters.jobName);
                                        $('#inputJobsNamesWidgetM').trigger("change");
                                        break;
                                        
                                    case "widgetProtezioneCivile":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').val('');
                                        $('#inputTitleWidgetM').attr('disabled', true);
                                        $('#inputTitleWidgetM').prop('required', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', true);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').val('');
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#urlWidgetM').attr('disabled', false);
                                         
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetButton":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Button text");
                                        $("label[for='inputColorWidgetM']").html("Button color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').attr('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', true);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').val('');
                                        $('#inputFreqWidgetM').prop('disabled', true);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetGenericContent":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;        
                                        
                                    case "widgetSingleContent":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', false);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetBarContent":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetColunmContent":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetGaugeChart":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetPieChart":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetSce":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetSmartDS":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetTimeTrend":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').attr('required', true);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                    
                                    case "widgetTimeTrendCompare":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').attr('required', true);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetEvents":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#inputComuneWidgetM').attr("disabled", true);
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetTrendMentions":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputComuneWidgetM').attr("disabled", true);
                                        
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetPrevMeteo":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', false);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputComuneWidgetM').attr("disabled", false);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetServiceMap":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').prop('required', false);
                                        $('#urlWidgetM').val('');
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break; 
                                        
                                    case "widgetSpeedometer":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', true);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#urlWidgetM').prop('required', false);
                                        $('#urlWidgetM').val('');
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                        
                                    case "widgetStateRideAtaf":
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#inputUdmM').attr('disabled', true);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', true);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#urlWidgetM').prop('required', false);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;    
                                    
                                    default:
                                        $("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#inputTitleWidgetM').prop('required', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#inputUdmM').attr('disabled', false);
                                        $('#inputUdmM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputComuneWidgetM').attr("disabled", false);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione menu SCHEDULER
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "none");
                                        $('#inputSchedulerWidgetDivM').css("display", "none");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "none");
                                        $('#inputSchedulerWidgetM').css("display", "none");
                                        //Rimozione menu JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        //Rimozione menu JOB GROUPS
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        //Rimozione menu JOB NAMES
                                        $('#jobsNamesRowM').css("display", "none");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "none");
                                        $('#inputJobsNamesWidgetDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsNamesWidgetM').css("display", "none");
                                        break;
                                }
                                
                                
                                dimMapRaw = data['dimMap'];
                                
                                if((dimMapRaw != null) && (dimMapRaw != "null") && (typeof dimMapRaw != "undefined") && (dimMapRaw != ""))
				{
			             dimMap = JSON.parse(dimMapRaw);
				}
                                
                                if(dimMap == null)
                                {
                                    $("#inputRows-m").off();
                                    var min_col_m = parseInt(data['min_col']);                               
                                    var max_col_m = parseInt(data['max_col']);
                                    var min_row_m = parseInt(data['min_row']);
                                    var max_row_m = parseInt(data['max_row']);

                                    if (min_row_m === max_row_m)
                                    {
                                        $("#inputRows-m").append('<option>' + max_row_m + '</option>');
                                        $("#inputRows-m").val(data['size_rows_widget']);
                                    } 
                                    else
                                    {
                                        for (var c = min_row_m; c <= max_row_m; c++)
                                        {
                                            $("#inputRows-m").append('<option>' + c + '</option>');
                                        }
                                        $("#inputRows-m").val(data['size_rows_widget']);
                                    }
                                    
                                    if (min_col_m === max_col_m)
                                    {
                                        $("#inputColumn-m").append('<option>' + min_col_m + '</option>');
                                        $("#inputColumn-m").val(data['size_columns_widget']);
                                    } 
                                    else
                                    {
                                        for (var v = min_col_m; v <= max_col_m; v++)
                                        {
                                            $("#inputColumn-m").append('<option>' + v + '</option>');
                                        }
                                        $("#inputColumn-m").val(data['size_columns_widget']);
                                    }
                                    
                                }
                                else
                                {
                                    //Codice per dim vincolate.
                                    var z;
                                    var selectedIndex = null;
                                    
                                    $("#inputRows-m").empty();
                                    $("#inputRows-m").empty();
                                    
                                    for(z = 0; z < dimMap.dimMap.length; z++)
                                    {
                                        $("#inputRows-m").append('<option>' + dimMap.dimMap[z].rows + '</option>');
                                    }
                                    
                                    $("#inputRows-m").val(data['size_rows_widget']);
                                    $("#inputColumn-m").append('<option>' + data['size_columns_widget'] + '</option>');
                                    $("#inputColumn-m").val(data['size_columns_widget']);

                                    $("#inputRows-m").off();

                                    $("#inputRows-m").change(function() {
                                            selectedIndex = $("#inputRows-m").prop("selectedIndex");
                                            $("#inputColumn-m").empty();
                                            $("#inputColumn-m").append('<option>' + dimMap.dimMap[selectedIndex].cols + '</option>');
                                    });
                                }  
                                
                                $("#urlWidgetM").val(data['url']);
                                
                                //Impostazion colore principale del widget
                                if((!data['color_widget']) || (data['color_widget'] == ""))
                                {
                                    $("#inputColorWidgetM").val("#ffffff");
                                    $("#color_widget_M").css("background-color", "#ffffff");
                                    $("#widgetColorPickerContainer").colorpicker('setValue', '#ffffff');
                                } 
                                else 
                                {
                                    $("#inputColorWidgetM").val(data['color_widget']);
                                    $("#color_widget_M").css("background-color", data['color_widget']);
                                    $("#widgetColorPickerContainer").colorpicker('setValue', data['color_widget']);
                                }
                                
                                //Impostazion colore del frame del widget
                                if((!data['frame_color']) || (data['frame_color'] == ""))
                                {
                                    $("#select-frameColor-Widget-m").val("#eeeeee");
                                    $("#color_fm").css("background-color", "#eeeeee");
                                    $("#widgetFrameColorPickerContainer").colorpicker('setValue', '#eeeeee');
                                } 
                                else 
                                {
                                    $("#select-frameColor-Widget-m").val(data['frame_color']);
                                    $("#color_fm").css("background-color", data['frame_color']);
                                    $("#widgetFrameColorPickerContainer").colorpicker('setValue', data['frame_color']);
                                }
                                
                                //dimensioni range
                                if (data['metrics_prop'][0]['timeRangeOption_metric_widget'] == 0) 
                                {
                                    $("#value_range_m").hide();
                                } 
                                else if (data['metrics_prop'][0]['timeRangeOption_metric_widget'] == 1) 
                                {
                                    $("#value_range_m").show();
                                    $("#textarea-range-value_m").val(data['param_w']);
                                    var dividi_json = data['param_w'];
                                    if (dividi_json) 
                                    {
                                        var obj = JSON.parse(dividi_json);
                                        $('#input-min_range_m').val(obj['rangeMin']);
                                        $('#input-max_range_m').val(obj['rangeMax']);
                                    } 
                                    else 
                                    {
                                        $("#textarea-range-value_m").empty();
                                        $('#input-min_range_m').val('');
                                        $('#input-max_range_m').val('');
                                    }
                                }


                                for (var i = 0; i < data['metrics_prop'].length; i++) 
                                {
                                    var value_text_widget = $("#textarea-metric-widget-m").val();
                                    value_text_widget += "Name: " + data['metrics_prop'][i]['id_metric'] + ".\n";
                                    value_text_widget += "Description: " + data['metrics_prop'][i]['descrip_metric_widget'] + ".\n";
                                    value_text_widget += "Metric typology: " + data['metrics_prop'][i]['type_metric_widget'] + ".\n";
                                    value_text_widget += "Data Area: " + data['metrics_prop'][i]['area_metric_widget'] + ".\n";
                                    value_text_widget += "Data Source: " + data['metrics_prop'][i]['source_metric_widget'] + ".\n";
                                    value_text_widget += "Status: " + data['metrics_prop'][i]['status_metric_widget'] + ".";
                                    if (i != ((data['metrics_prop'].length) - 1)) 
                                    {
                                        value_text_widget += "\n\n";
                                    }
                                    $("#textarea-metric-widget-m").val(value_text_widget);
                                }

                                if (data['color_widgetOption_widget'] == 0) 
                                {
                                    $('#inputColorWidgetM').attr('readonly', true);
                                } 
                                else 
                                {
                                    $('#inputColorWidgetM').attr('readonly', false);
                                }
                                
                                $('#modal-modify-widget').modal('show');
                            },
                            error: function (jqXHR, textStatus, errorThrown) 
                            {
                                $('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                                console.log('jqXHR:');
                                console.log(jqXHR);
                                console.log('textStatus:');
                                console.log(textStatus);
                                console.log('errorThrown:');
                                console.log(errorThrown);
                            }
                        });
                    });
                    
                    //Listener in modifica widget per il menu degli scheduler che carica le jobs areas e/o i jobs groups 
                    $('#inputSchedulerWidgetM').change(update_select_scheduler = function () 
                    {
                        if($('#inputSchedulerWidgetM').css("display") != "none")
                        {
                            var selectedIndex = $('#inputSchedulerWidgetM').prop('selectedIndex');
                            $("#schedulerNameM").val(elencoScheduler[selectedIndex].name);
                            $("#hostM").val(elencoScheduler[selectedIndex].ip);
                            $("#userM").val(elencoScheduler[selectedIndex].user);
                            $("#passM").val(elencoScheduler[selectedIndex].pass);
                            $.ajax({
                                url: "get_data.php",
                                data: {action: "getJobAreas", schedulerId: elencoScheduler[selectedIndex].id},
                                type: "GET",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    $('#inputJobsAreasWidgetM').empty();
                                    $('#inputJobsGroupsWidgetM').empty();
                                    $('#inputJobsNamesWidgetM').empty();
                                    if(data[0].id == "none")
                                    {
                                        //Nessuna JOBS AREA
                                        $('#jobsAreasRowM').css("display", "none");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "none");
                                        $('#inputJobsAreasWidgetDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsAreasWidgetM').css("display", "none");
                                        
                                        //Caricamento dei jobs groups per lo scheduler selezionato.
                                        $('#inputJobsGroupsWidgetM').empty();
                                        for(var i = 0; i < elencoJobsGroupsPerScheduler[selectedIndex].length; i++)
                                        {
                                            $('#inputJobsGroupsWidgetM').append('<option>' + elencoJobsGroupsPerScheduler[selectedIndex][i] + '</option>');
                                        }
                                        
                                        //Viene mostrato il campo per i jobs groups dello scheduler selezionato
                                        $('#jobsGroupsRowM').css("display", "");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "");
                                        $('#inputJobsGroupsWidgetM').css("display", "");
                                        $('#inputJobsGroupsWidgetM').prop('selectedIndex', -1);
                                        
                                        //Viene mostrato il campo dei jobs names per il group selezionato
                                        $('#jobsNamesRowM').css("display", "");
                                        $('#inputJobsNamesWidgetDivM').css("display", "");
                                        $('#inputJobsNamesWidgetGroupDivM').css("display", "");
                                        $("label[for='inputJobsNamesWidgetM']").css("display", "");
                                        $('#inputJobsNamesWidgetM').css("display", "");
                                        $('#inputJobsNamesWidgetM').prop('selectedIndex', -1);
                                    }
                                    else
                                    {
                                        //Ramo con le jobs areas
                                        
                                        //Non mostrare i job groups per scheduler
                                        $('#jobsGroupsRowM').css("display", "none");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "none");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "none");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "none");
                                        $('#inputJobsGroupsWidgetM').css("display", "none");
                                        
                                        //Mostrare le JOBS AREAS
                                        $('#jobsAreasRowM').css("display", "");
                                        $("label[for='inputJobsAreasWidgetM']").css("display", "");
                                        $('#inputJobsAreasWidgetDivM').css("display", "");
                                        $('#inputJobsAreasWidgetGroupDivM').css("display", "");
                                        $('#inputJobsAreasWidgetM').css("display", "");
                                        $('#inputJobsAreasWidgetM').empty();
                                        for(var i = 0; i < data.length; i++)
                                        {
                                            $('#inputJobsAreasWidgetM').append('<option>' + data[i]['name'] + '</option>');
                                        }
                                        $('#inputJobsAreasWidgetM').prop("selectedIndex", -1);
                                        
                                        //Viene mostrato il campo per i jobs groups della job area selezionata
                                        $('#jobsGroupsRowM').css("display", "");
                                        $('#inputJobsGroupsWidgetGroupDivM').css("display", "");
                                        $('#inputJobsGroupsWidgetDivM').css("display", "");
                                        $("label[for='inputJobsGroupsWidgetM']").css("display", "");
                                        $('#inputJobsGroupsWidgetM').css("display", "");
                                        $('#inputJobsGroupsWidgetM').prop('selectedIndex', -1);
                                    }

                                },
                                error: function (jqXHR, textStatus, errorThrown) 
                                {

                                    //$('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                                    console.log('jqXHR:');
                                    console.log(jqXHR);
                                    console.log('textStatus:');
                                    console.log(textStatus);
                                    console.log('errorThrown:');
                                    console.log(errorThrown);
                                    }

                            });    
                        }
                    }).change();
                    
                    //Listener in modifica widget per il menu delle job areas che carica i jobs groups 
                    $('#inputJobsAreasWidgetM').change(update_select_scheduler = function ()
                    {
                       if($('#inputJobsAreasWidgetM').css("display") != "none")
                       {
                           $('#inputJobsGroupsWidgetM').empty();
                           var selectedSchedulerIndex = $('#inputSchedulerWidgetM').prop('selectedIndex');
                           var areaSelected = $('#inputJobsAreasWidgetM').val();
                           $("#jobAreaM").val(areaSelected);
                           var keyword = null;
                           switch(areaSelected)
                           {
                                case "Stato linee ATAF":
                                   keyword = "linea";
                                   break;
                                   
                                case "Check RT":
                                   keyword = "check";
                                   break;
                                   
                                case "Eventi a Firenze":
                                   keyword = "Eventi";
                                   break;
                                   
                                case "Parcheggi":
                                   keyword = "parcheggi";
                                   break;
                                   
                                case "Previsioni meteo":
                                   keyword = "meteo";
                                   break;
                                   
                                case "Sensori":
                                   keyword = "sensori";
                                   break;   
                           }
                           $.ajax({
                                url: "getJobs.php",
                                data: {action: "getJobGroupsForJobArea", 
                                       host: elencoScheduler[selectedSchedulerIndex].ip, 
                                       user: elencoScheduler[selectedSchedulerIndex].user, 
                                       pass: elencoScheduler[selectedSchedulerIndex].pass,
                                       keyword: keyword
                                   },
                                type: "POST",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    $('#inputJobsGroupsWidgetM').empty();
                                    for(var j = 0; j < data.length; j++) 
                                    {
                                        $('#inputJobsGroupsWidgetM').append('<option>' + data[j].jobGroup + '</option>');
                                    }
                                    $('#inputJobsGroupsWidgetM').prop('selectedIndex', -1);
                                    //Viene mostrato il campo dei jobs names per il group selezionato
                                    $('#jobsNamesRowM').css("display", "");
                                    $('#inputJobsNamesWidgetDivM').css("display", "");
                                    $('#inputJobsNamesWidgetGroupDivM').css("display", "");
                                    $("label[for='inputJobsNamesWidgetM']").css("display", "");
                                    $('#inputJobsNamesWidgetM').css("display", "");
                                    $('#inputJobsNamesWidgetM').prop('selectedIndex', -1);
                                }
                            });
                           
                       }
                    }).change();
                    
                    //Listener in modifica widget per il menu dei job groups che carica i jobs names 
                    $('#inputJobsGroupsWidgetM').change(update_select_scheduler = function () 
                    {
                        if($('#inputJobsGroupsWidgetM').css("display") != "none")
                        {
                            $('#inputJobsNamesWidgetM').empty();
                            var selectedScheduler = $('#inputSchedulerWidgetM').prop('selectedIndex');
                            var selectedGroup = $('#inputJobsGroupsWidgetM').val();
                            $("#jobGroupM").val(selectedGroup);
                            //Reperimento elenco dei jobs names per il job group selezionato
                            $.ajax({
                                url: "getJobs.php",
                                data: {
                                    action: "getJobNamesForJobGroup", 
                                    host: elencoScheduler[selectedScheduler].ip, 
                                    user: elencoScheduler[selectedScheduler].user, 
                                    pass: elencoScheduler[selectedScheduler].pass,
                                    jobGroup: selectedGroup
                                },
                                type: "POST",
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    $('#inputJobsNamesWidgetM').empty();
                                    for(var j = 0; j < data.length; j++) 
                                    {
                                        $('#inputJobsNamesWidgetM').append('<option>' + data[j].jobName + '</option>');
                                    }
                                    $('#inputJobsNamesWidgetM').prop('selectedIndex', -1);
                                },
                                error: function (jqXHR, textStatus, errorThrown) 
                                {
                                    console.log('jqXHR:');
                                    console.log(jqXHR);
                                    console.log('textStatus:');
                                    console.log(textStatus);
                                    console.log('errorThrown:');
                                    console.log(errorThrown);
                                }
                            });
                        }
                    }).change();
                    
                   //Listener in modifica widget per il menu dei job names che valorizza il campo di form col job name scelto
                   $('#inputJobsNamesWidgetM').change(update_select_scheduler = function () 
                    {
                        if($('#inputJobsNamesWidgetM').css("display") != "none")
                        {
                            $('#jobNameM').val($('#inputJobsNamesWidgetM').val());
                        }
                    }).change();

                    //crea json con il range dei valori
                    $(document).on('click', '#create_param_json', function () {
                        var min_range = $('#input-min_range').val();
                        var max_range = $('#input-max_range').val();
                        var json_range;
                        json_range = JSON.stringify({'rangeMin': min_range, 'rangeMax': max_range});
                        $('#textarea-range-value').text(json_range);
                    });

                    $(document).on('click', '#create_param_json-m', function () {
                        var min_range_m = $('#input-min_range_m').val();
                        var max_range_m = $('#input-max_range_m').val();
                        var json_range_m;
                        json_range_m = '{' + '"rangeMin":"' + min_range_m + '", "rangeMax":"' + max_range_m + '"' + '}';
                        $('#textarea-range-value_m').val(json_range_m);
                    });

                    //evento che dovrebbe verificarsi cliccando sull'icona delle informazioni
                    $(document).on('click', '.info_source', function () {
                        var name_widget_m = $(this).parents('li').attr('id');
                        $.ajax({
                            url: "get_data.php",
                            data: {widget_info: name_widget_m, action: "get_info_widget"},
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                $('#titolo_info').text(data['title_widget']);
                                $('#contenuto_infomazioni').html(data['info_mess']);
                                $('#dialog-information-widget').modal('show');
                                $('#dialog-information-widget').css({
                                    'vertical-align': 'middle',
                                    'position': 'absolute',
                                    'top': '10%'
                                });
                            }
                        });
                    });
                    //fine evento informazioni

                    $(function() 
                    {
                        $('.color-choice').colorpicker();
                        $("#widgetColorPickerContainer").colorpicker();
                        $("#widgetFrameColorPickerContainer").colorpicker();
                    });

                    $('#form-setting-widget').submit(function () {
                        $('#select-widget').removeAttr('disabled');
                    });


                    //aggiunta draggable

                    $(function () {
                        $('#adding01').draggable();
                    });
                    $(function () {
                        $('#modify01').draggable();
                    });
                    $(function () {
                        $('#dash01').draggable();
                    });
                    $(function () {
                        $('#duplicate01').draggable();
                    });
                    $(function () {
                        $('#info01').draggable();
                    });



                    //CKeditor
                    CKEDITOR.replace('textarea-information-metrics', {
                        //customConfig: '/custom/ckeditor_config.js',
                        allowedContent: true,
                        language: 'en',
                        width: '500',
                        height: '100'
                    });
                    //fine CKeditor

                });
            </script>	
            </body>
            </html>

