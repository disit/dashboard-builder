<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
   
    include('process-form.php');
?>

<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <!--<link rel="stylesheet" type="text/css" href="../css/new/jquery.gridster.css">-->
    <link rel="stylesheet" href="../css/style_widgets.css" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <!--<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>-->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <script src="../js/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>
    <!--<script src="../js/new/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>-->

    <!-- CKEditor --> 
    <!--<script src="http://cdn.ckeditor.com/4.5.10/standard/ckeditor.js"></script>-->
    <script src="../js/ckeditor/ckeditor.js"></script>
    <link rel="stylesheet" href="../js/ckeditor/skins/moono/editor.css">
    
     <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>
    
    <!-- JQUERY UI -->
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>-->
    <script src="../js/jqueryUi/jquery-ui.js"></script>
    
    <!-- Font awesome icons -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <!-- Bootstrap colorpicker -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    
    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>
    
    <!-- Highcharts -->
    <!--<script src="http://code.highcharts.com/highcharts.js"></script>-->
    <!--<script src="http://code.highcharts.com/modules/exporting.js"></script>-->
    <!--<script src="https://code.highcharts.com/highcharts-more.js"></script>-->
    <!--<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>-->
    <!--<script src="https://code.highcharts.com/highcharts-3d.js"></script>-->  
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>
    
    <!-- Bootstrap editable tables -->
    <link href="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>
    
    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet"/>
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>
    
    <!-- OpenLayers -->
    <!-- <script src="https://openlayers.org/api/OpenLayers.js"></script>-->
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css"
   integrity="sha512-07I2e+7D8p6he1SIM+1twR5TIrhUQn9+I6yjqD53JQjFiMf8EtC93ty0/5vJTZGF8aAocvHYNEDJajGdNx1IsQ=="
   crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"
   integrity="sha512-A7vV8IFfih/D732iSSKi20u/ooOfj/AGehOKq0f4vLT1Zr2Y+RX7C+w8A1gaSasGtRUZpF/NZgzSAu4/Gc41Lg=="
   crossorigin=""></script>
    
   <!-- Dot dot dot -->
   <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>
   
    <script src="../js/widgetsCommonFunctions.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js" type="text/javascript" charset="utf-8"></script>
</head>

<body> 
    <?php
        if(canEditDashboard() == false)
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
    ?>
   
         <div id="editDashboardMenu" class="container-fluid centerWithFlex">
            <div class="mainMenuItemContainer">
               <a id="link_add_widget" href="#" data-toggle="modal" data-target="#modal-add-widget">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/add.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     add a<br>widget
                  </div>
               </a>
            </div>
            <div class="mainMenuItemContainer">
               <a id ="link_modifyDash" href="#" data-toggle="modal" data-target="#modal-modify-dashboard">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/edit.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     dashboard<br>properties
                  </div>
               </a>
            </div>
            <div class="mainMenuItemContainer">
               <a id ="link_save_configuration" href="#">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/save.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     save widgets'<br>position
                  </div>
               </a>
            </div>
            <div class="mainMenuItemContainer">
               <a id ="link_duplicate_dash" href="#" data-toggle="modal" data-target="#modal-duplicate-dashboard">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/duplicate.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     duplicate<br>dashboard
                  </div>
               </a>
            </div>
            <div class="mainMenuItemContainer">
               <a id="dashboardViewLink" href="#" target="blank">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/view.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     dashboard<br>view
                  </div>
               </a>
            </div>
            <div class="mainMenuItemContainer">
               <a id ="link_exit" href="#">
                  <div class="mainMenuIconContainer centerWithFlex">
                     <img src="../img/editDashMenu/exit.png" width="25" height="25">
                  </div>
                  <div class="mainMenuTextContainer centerWithFlex">
                     back to<br>main menu
                  </div>
               </a>
            </div>
         </div> 
    
    <div id="wrapper-dashboard-cfg">
        <!-- New header -->
        <nav id="navbarDashboard" class="navbar navbar-inverse navbar-fixed-top noBorder" role="navigation">
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
        
        
        
        <div id="pageWrapperCfg">
            <div class="container-fluid">
                
                <div id="container-widgets-cfg" class="gridster"> 
                    <ul id="containerUl"></ul>  
                </div>
                <div id="logos" class="footerLogos">
                    <a title="Twitter" href="http://www.twitter.com" target="_new" class="footerLogo"><i class='fa fa-twitter'></i></a>
                    <a title="Twitter vigilance" href="http://www.disit.org/tv" target="_new" class="footerLogo"><i class='fa fa-eye'></i></a>
                    <a title="Disit" href="http://www.disit.org" target="_new" class="footerLogo"><img src="../img/disitLogo.png" /></a>
                </div>
        </div><!-- Fine container-fluid -->
    </div><!-- Fine pageWrapperCfg  -->

    <!-- Modale aggiunta widget -->
    <div class="modal fade" id="modal-add-widget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modalDialogWidget" role="document" id="adding01">
            <div class="modal-content modalContentWidgetForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new widget to dashboard</h4>
                </div>
                <div id="modal-add-widget-body" class="modal-body">
                    <form id="form-setting-widget" class="form-horizontal" name="form-setting-widget" role="form" method="post" action="" data-toggle="validator"> <!-- novalidate -->
                        <div class="well wellCustom2left" id="metricAndWidgetChoice">
                            <legend class="legend-form-group">Metric and widget choice</legend> <!-- class="noMarginBottom" -->
                            <div id="metricAndWidgetChoiceContent" class="form-group">
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
                                
                                <div id ="inputComuneRow" class="row">    
                                <label for="inputComuneWidget" class="col-md-4 control-label">Context</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control autocomplete" id="inputComuneWidget" name="inputComuneWidget" novalidate>
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

                                <input type="hidden" id="firstFreeRowInput" name="firstFreeRowInput">
                                <input type="hidden" id="schedulerName" name="schedulerName">
                                <input type="hidden" id="host" name="host">
                                <input type="hidden" id="user" name="user">
                                <input type="hidden" id="pass" name="pass">
                                <input type="hidden" id="jobArea" name="jobArea">
                                <input type="hidden" id="jobGroup" name="jobGroup">
                                <input type="hidden" id="jobName" name="jobName">
                                <input type="hidden" id="serviceUri" name="serviceUri">
                                <input type="hidden" id="hospitalList" name="hospitalList">
                            </div>
                        </div>
                        
                        <div class="well wellCustom2right" style="height: 416px" id="genericWidgetPropertiesDiv">
                            <legend class="legend-form-group">Generic widget properties</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label id="titleLabel" for="inputTitleWidget" class="col-md-2 control-label addWidgetParLabel">Title</label> 
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputTitleWidget" name="inputTitleWidget">
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
                                    <label for="inputFontSize" class="col-md-2 control-label addWidgetParLabel">Content<br/>font size</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFontSize" name="inputFontSize" >
                                    </div>
                                    
                                    <label for="inputFontColor" class="col-md-3 control-label addWidgetParLabel">Content font color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFontColor" name="inputFontColor" value="#000000">
                                            <span class="input-group-addon"><i id="widgetFontColor"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputFrameColorWidget" class="col-md-2 control-label addWidgetParLabel">Header <br/>color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFrameColorWidget" name="inputFrameColorWidget" value="#eeeeee" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    <label for="inputHeaderFontColorWidget" class="col-md-3 control-label addWidgetParLabel">Header text color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputHeaderFontColorWidget" name="inputHeaderFontColorWidget" value="#000000" required>
                                            <span class="input-group-addon"><i id="widgetHeaderFontColor"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-IntTemp-Widget" class="col-md-2 control-label addWidgetParLabel">Period</label>
                                    <div class="col-md-3">
                                        <select name="select-IntTemp-Widget" class="form-control" id="select-IntTemp-Widget" required>
                                            <option value="Nessuno">No</option>
                                            <option value="4 Ore">4 Hours</option>
                                            <option value="12 Ore">12 Hours</option>
                                            <option value="Giornaliera">Day</option>
                                            <option value="Settimanale">Week</option>
                                            <option value="Mensile">Month</option>
                                            <option value="Annuale">Year</option>       
                                        </select>
                                    </div>
                                    <label for="inputFreqWidget" class="col-md-3 control-label addWidgetParLabel">Refresh rate (s)</label>

                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFreqWidget" name="inputFreqWidget" placeholder="" required>
                                    </div>
                                </div>                     
                                <div class="row">
                                    <label for="inputSizeRowsWidget" class="col-md-2 control-label addWidgetParLabel">Height</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputSizeRowsWidget" name="inputSizeRowsWidget" placeholder="">                                            
                                        </select>
                                    </div>
                                    <label for="inputSizeColumnsWidget" class="col-md-3 control-label addWidgetParLabel">Width</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="inputSizeColumnsWidget" name="inputSizeColumnsWidget" placeholder="">                                           
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputUdmWidget" class="col-md-2 control-label addWidgetParLabel">U/M</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputUdmWidget" name="inputUdmWidget">
                                    </div>
                                    
                                    <label for="inputUdmWidget" class="col-md-3 control-label addWidgetParLabel">U/M position</label>
                                    <div class="col-md-3">
                                        <select name="inputUdmPosition" class="form-control" id="inputUdmPosition" required>
                                            <option value="next">Next to value</option>
                                            <option value="below">Below value</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="well wellCustom2left" id="specificWidgetPropertiesDiv">
                            <legend class="legend-form-group">Specific widget properties</legend>
                            <div class="form-group">
                                <!-- NON CANCELLARE -->
                                <input type="hidden" id="parameters" name="parameters">
                                <!-- NON CANCELLARE -->
                                <input type="hidden" id="barsColors" name="barsColors">
                            </div>
                        </div>
                        
                        <div class="well wellCustom2right" id="infoTextareaDiv">
                            <legend class="legend-form-group">Widget detailed informations</legend>
                            <div class="row">
                                <label for="infoMainSelect" class="col-sm-1 control-label">Target</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoMainSelect" name="infoMainSelect">       
                                    </select>                                                                     
                                </div>
                                <label for="infoAxisSelect" class="col-sm-1 control-label">Set</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoAxisSelect" name="infoAxisSelect">       
                                    </select>                                                                     
                                </div>
                                <label for="infoFieldSelect" class="col-sm-1 control-label">Field</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoFieldSelect" name="infoFieldSelect">       
                                    </select>                                                                     
                                </div> 
                            </div>
                            <div id="widgetInfoCkEditorTitleRow" class="row centerWithFlex">
                                <h5 id="widgetInfoCkEditorTitle"></h5>
                            </div>
                            <input type="hidden" id="infoNamesJsonFirstAxis" name="infoNamesJsonFirstAxis">
                            <input type="hidden" id="infoNamesJsonSecondAxis" name="infoNamesJsonSecondAxis">
                        </div>
                        
                        <div class="well wellCustomFooter">
                            <button id="button_add_widget" name="add_widget" type="submit" class="btn btn-primary widgetFormButton">Create</button>
                            <button id="button_reset_add_widget" class="btn btn-danger widgetFormButton widgetFormButtonMargin" type="reset">Reset</button>
                            <button id="button_close_popup" type="button" class="btn btn-default widgetFormButton widgetFormButtonMargin" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> 
    <!-- Modale modifica dashboard -->
    <div class="modal fade" id="modal-modify-dashboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <!-- Vecchio codice -->
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
                                <label for="widgetsBorders" class="col-md-4 control-label">Widgets borders</label>
                                <div class="col-md-6">
                                    <select name="widgetsBorders" class="form-control" id="widgetsBorders" required>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <label for="inputWidgetsBordersColor" class="col-md-4 control-label">Widgets border color</label>
                                <div class="col-md-6">
                                    <div class="input-group color-choice">
                                        <input type="text" class="form-control" id="inputWidgetsBordersColor" name="inputWidgetsBordersColor" required>
                                        <span class="input-group-addon"><i id="inputPickerWidgetsBorderColor"></i></span>
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
                                <label for="pixelWidth" class="col-md-4 control-label">Resulting width (px)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="pixelWidth" name="pixelWidth" disabled>
                                </div>
                                <label for="percentWidth" class="col-md-4 control-label">Percent width occupied on your monitor (fullscreen)</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="percentWidth" name="percentWidth" disabled>
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
        <div class="modal-dialog modalDialogWidget" role="document" id="modify01">
            <div class="modal-content modalContentWidgetForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modify widget</h4>
                </div>
                <div class="modal-body">
                    <form id="form-modify-widget" class="form-horizontal" name="form-modify-widget" role="form" method="post" action="" data-toggle="validator">
                        <!-- Nuovo codice -->
                        <div class="well wellCustom2left" id="metricAndWidgetChoiceM">
                            <legend class="legend-form-group">Metric and widget choice</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label for="metricWidgetM" class="col-md-4 control-label">Metric</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="metricWidgetM" name="metricWidgetM" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="inputNameWidgetM" class="col-md-4 control-label">Widget name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="inputNameWidgetM" name="inputNameWidgetM" placeholder="Title" readonly>                                 
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-widget-m" class="col-md-4 control-label">Widget type</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <select name="select-widget-m" class="form-control" id="select-widget-m" required>
                                            </select>
                                            <div id="mod-n-metrcis-widget" class="input-group-addon"></div>
                                        </div>   
                                    </div>
                                </div>   
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
                                    <label for="urlWidgetM" class="col-md-4 control-label">Widget link</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="urlWidgetM" name="urlWidgetM">
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
                                <input type="hidden" id="serviceUriM" name="serviceUriM">
                                <input type="hidden" id="hospitalListM" name="hospitalListM">
                            </div>
                        </div>
                        <div class="well wellCustom2right" style="height: 386px" id="genericWidgetPropertiesDivM">
                            <legend class="legend-form-group">Generic widget properties</legend>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputTitleWidgetM" class="col-md-2 control-label addWidgetParLabel">Title</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputTitleWidgetM" name="inputTitleWidgetM">
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
                                    <label for="inputFontSizeM" class="col-md-2 addWidgetParLabel control-label">Content<br/>font size</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputFontSizeM" name="inputFontSizeM">
                                    </div>
                                    <label for="inputFontColorM" class="col-md-3 addWidgetParLabel control-label">Content font color</label>
                                    <div class="col-md-3">
                                        <div id="widgetFontColorPickerContainerM" class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputFontColorM" name="inputFontColorM">
                                            <span class="input-group-addon"><i id="widgetFontColorM"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-frameColor-Widget-m" class="col-md-2 addWidgetParLabel control-label">Header color</label>
                                    <div class="col-md-3">
                                        <div id="widgetFrameColorPickerContainer" class="input-group"> <!-- color-choice -->
                                            <input type="text" class="form-control demo-1 demo-auto" id="select-frameColor-Widget-m" name="select-frameColor-Widget-m" required> <!-- value="#eeeeee" -->
                                            <span class="input-group-addon"><i id="color_fm"></i></span>
                                        </div>
                                    </div>
                                    <label for="inputHeaderFontColorWidgetM" class="col-md-3 control-label addWidgetParLabel">Header text color</label>
                                    <div class="col-md-3">
                                        <div class="input-group color-choice">
                                            <input type="text" class="form-control demo-1 demo-auto" id="inputHeaderFontColorWidgetM" name="inputHeaderFontColorWidgetM" required>
                                            <span class="input-group-addon"><i id="widgetHeaderFontColorM"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="select-IntTemp-Widget-m" class="col-md-2 addWidgetParLabel control-label">Period</label>
                                    <div class="col-md-3">
                                        <select name="select-IntTemp-Widget-m" class="form-control" id="select-IntTemp-Widget-m">
                                            <option value="Nessuno">No</option>                                           
                                            <option value="4 Ore">4 Hours</option>
                                            <option value="12 Ore">12 Hours</option>
                                            <option value="Giornaliera">Day</option>
                                            <option value="Settimanale">Week</option>
                                            <option value="Mensile">Month</option>
                                            <option value="Annuale">Year</option>   
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
                                <div class="row">
                                    <label for="inputUdmWidgetM" class="col-md-2 control-label addWidgetParLabel">U/M</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="inputUdmWidgetM" name="inputUdmWidgetM">
                                    </div>
                                    
                                    <label for="inputUdmPositionM" class="col-md-3 control-label addWidgetParLabel">U/M position</label>
                                    <div class="col-md-3">
                                        <select name="inputUdmPositionM" class="form-control" id="inputUdmPositionM" required>
                                            <option value="next">Next to value</option>
                                            <option value="below">Below value</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="well wellCustom2left" id="specificParamsM">
                            <legend class="legend-form-group">Specific widget properties</legend>
                            <!--NON CANCELLARE -->
                            <input type="hidden" id="parametersM" name="parametersM">
                            <!--NON CANCELLARE -->
                            <input type="hidden" id="barsColorsM" name="barsColorsM">
                        </div>
                        
                        
                        <div class="well wellCustom2right" id="infoTextareaDivM">
                            <legend class="legend-form-group">Widget detailed informations</legend>
                            <div class="row">
                                <label for="infoMainSelectM" class="col-sm-1 control-label">Target</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoMainSelectM" name="infoMainSelectM">       
                                    </select>                                                                     
                                </div>
                                <label for="infoAxisSelectM" class="col-sm-1 control-label">Set</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoAxisSelectM" name="infoAxisSelectM">       
                                    </select>                                                                     
                                </div>
                                <label for="infoFieldSelectM" class="col-sm-1 control-label">Field</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="infoFieldSelectM" name="infoFieldSelectM">       
                                    </select>                                                                     
                                </div> 
                            </div>
                            <div id="widgetInfoCkEditorTitleRowM" class="row centerWithFlex">
                                <h5 id="widgetInfoCkEditorTitleM"></h5>
                            </div>
                            <input type="hidden" id="infoNamesJsonFirstAxisM" name="infoNamesJsonFirstAxisM">
                            <input type="hidden" id="infoNamesJsonSecondAxisM" name="infoNamesJsonSecondAxisM">
                        </div>
                        
                        
                        <div class="well wellCustomFooter">
                            <button id="button_modify_widget" name="modify_widget" class="btn btn-primary widgetFormButton" type="submit">Modify</button>
                            <button id="closeModifyWidgetBtn" type="button" class="btn btn-default widgetFormButton widgetFormButtonMargin" data-dismiss="modal">Close</button>   
                        </div>
                    </form>
                </div>
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
        <!-- Modale informazioni widget -->
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
        </div>    
        <!-- Modale informazioni campi widget -->
        <div class="modal fade" tabindex="-1" id="modalWidgetFieldsInfo" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" id="info01"> 
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="modalWidgetFieldsInfoTitle"></h4>
                    </div>
                    <div class="modal-body">
                        <form id="modalWidgetFieldsInfoForm" class="form-horizontal" name="modalWidgetFieldsInfoForm" role="form" method="post" action="" data-toggle="validator">
                            <div id="modalWidgetFieldsInfoContent"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>   
        
            <script type='text/javascript'>
                var gridster, num_cols, indicatore, datoTitle, datoSubtitle, datoColor, datoWidth, datoRemains, nuovaDashboard, headerFontSize, headerModFontSize, subtitleFontSize, clockFontSizeMod, dashboardName, logoFilename, logoLink, temp, widgetsArray, nomeComune, metricType = null;
                var array_metrics = new Array();
                var informazioni = new Array();
                var elencoScheduler = new Array();
                var elencoJobsGroupsPerScheduler = [[],[],[]];
                var firstFreeRow = 1;
                var comuniLammaArray = new Array();
                
                $("#button_close_popup").click(function()
                {
                    $('#infoNamesJsonFirstAxis').val('');
                    $('#infoNamesJsonSecondAxis').val('');
                    $('#infoTextareaDiv').children('.cke').remove();
                    $('#infoTextareaDiv').children('textarea').remove();
                });
                
                $("#closeModifyWidgetBtn").click(function()
                {
                    $('#infoTextareaDivM').children('.cke').remove();
                    $('#infoTextareaDivM').children('textarea').remove();
                });
                
                $(document).ready(function ()
                {
                    var widgetsBorders, widgetsBordersColor, hospitalList = null;
                    var editorsArray = new Array();
                    var editorsArrayM = new Array();
                    $.fn.editable.defaults.mode = 'inline';
                    
                    var dashboardId = "<?= $_SESSION['dashboardId'] ?>";
                    var dashboardIdEncoded = window.btoa(dashboardId);
                    $("#dashboardViewLink").attr("href", "../view/index.php?iddasboard=" + dashboardIdEncoded);
                    
                    
                    var iconH = $("div.mainMenuItemContainer").height();
                    $("div.mainMenuIconContainer").css("height", iconH + "px");
                    
                    $.ajax({
                        //url: "http://servicemap.km4city.org/WebAppGrafo/api/v1/?selection=43.81224123241114;11.25284173812866&requestFrom=user&categories=First_aid&maxResults=100&maxDists=180&format=json&uid=a6d838155294cd8f7801f406397ebb7dcea06036507913bed20c7ada179367f5&lang=it&geometry=true",
                        //url: "http://servicemap.km4city.org/WebAppGrafo/api/v1/?selection=43.81224123241114;11.25284173812866&categories=First_aid&maxResults=100&maxDists=200&format=json&lang=it",
                        url: "<?=$serviceMapUrlPrefix?>api/v1/?selection=43.81224123241114;11.25284173812866&categories=First_aid&maxResults=100&maxDists=200&format=json&lang=it",
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                           hospitalList = data;
                        },
                        error: function (data)
                        {
                           console.log("Error retrieving hospital list");
                           console.log(JSON.stringify(data));
                        }
                     });
                    
                    $('#inputWidthDashboard').on('input',function(e)
                    {
                       var cols = parseInt($('#inputWidthDashboard').val());
                       var isNotNumber = isNaN(cols);
                       if(isNotNumber)
                       {
                           $('#pixelWidth').val("");
                           $('#percentWidth').val("");
                       }
                       else
                       {
                           var px = parseInt(cols*78 + 10);
                           var percent = parseInt(px/screen.width*100);
                           $('#pixelWidth').css("background-color", "white");
                           $('#percentWidth').css("background-color", "white");
                           $('#pixelWidth').val(px + " px");
                           $('#percentWidth').val(percent + " %");
                       }
                    });
                    
                    $.ajax({
                        url: "http://192.168.0.206:8890/sparql?query=select+distinct+%3Fn+%7B%0D%0A%3Fs+a+km4c%3AMunicipality.%0D%0A%3Fs+foaf%3Aname+%3Fn%0D%0A%7D+order+by+%3Fn&format=json",
                        type: "GET",
                        async: true,
                        dataType: 'jsonp',
                        success: function (data) {
                            for(var i = 0; i < data.results.bindings.length; i++)
                            {
                                comuniLammaArray.push(data.results.bindings[i].n.value);
                            }
                        },
                        error: function (data) {
                            console.log("Errore nella chiamata Sparql per reperimento comuni Lamma");
                        }
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
                                widgetsBorders = data[i].widgetsBorders;
                                widgetsBordersColor = data[i].widgetsBordersColor;
                               
                                var wrapperWidth = parseInt(data[i].width) + 40;
                                $("#wrapper-dashboard-cfg").css("width", wrapperWidth);
                                $("#container-widgets-cfg").css("width", data[i].width);
                                $("#logos-cfg").css("width", data[i].width);
                                $("#wrapper-dashboard-cfg").css("margin", "0 auto");
                                $("#navbarDashboard").css("background-color", data[i].color_header);
                                
                                var headerFontColor = data[i].headerFontColor;
                                headerFontSize = data[i].headerFontSize;
                                subtitleFontSize = parseInt(data[i].headerFontSize * 0.22);
                                if(subtitleFontSize < 20)
                                {
                                    subtitleFontSize = 20;
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
                                        subtitleModFontSize = parseInt((subtitleFontSize*0.8));    
                                    }
                                    else
                                    {

                                        if(a > 320)
                                        {
                                            headerModFontSize = parseInt((headerFontSize*0.75));
                                            subtitleModFontSize = parseInt((subtitleFontSize*0.65));
                                        }
                                        else
                                        {
                                            headerModFontSize = parseInt((headerFontSize*0.55));
                                            subtitleModFontSize = parseInt((subtitleFontSize*0.45));
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
                                if((data[i].subtitle_header === "") || (data[i].subtitle_header == null) ||(typeof data[i].subtitle_header === 'undefined') || (data[i].subtitle_header.match(whiteSpaceRegex)))
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
                                
                                if(logoFilename !== null)
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
                                    };
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
                                $("#widgetsBorders").val(widgetsBorders);
                                $("#inputWidgetsBordersColor").val(widgetsBordersColor);
                                $("#inputPickerWidgetsBorderColor").css("background-color", widgetsBordersColor);
                                
                                
                                var cols = parseInt($('#inputWidthDashboard').val());
                                var isNotNumber = isNaN(cols);
                                if(isNotNumber)
                                {
                                   $('#pixelWidth').val("");
                                   $('#percentWidth').val("");
                                }
                                else
                                {
                                   var px = parseInt(cols*78 + 10);
                                   var percent = parseInt(px/screen.width*100);
                                   $('#pixelWidth').css("background-color", "white");
                                   $('#percentWidth').css("background-color", "white");
                                   $('#pixelWidth').val(px + " px");
                                   $('#percentWidth').val(percent + " %");
                                }
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
                                $('#pageWrapperCfg').css("background-color", external_color);
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
                                async: false,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    widgetsArray = data;
                                    for (var j = 0; j < widgetsArray.length; j++) 
                                    {
                                        informazioni[j] = widgetsArray[j].message_widget;
                                    }
                                    
                                    jQuery("#container-widgets-cfg ul").gridster({
                                        widget_base_dimensions: [76, 38],
                                        widget_margins: [1, 1],
                                        min_cols: num_cols,
                                        max_size_x: 30,
                                        max_rows: 50,
                                        extra_rows: 40,
                                        draggable: {
                                            //ignore_dragging: true,
                                            stop: function(){
                                                firstFreeRow = 1;
                                                $(".gridsterCell").each(function() 
                                                {
                                                    temp = parseInt(parseInt($(this).attr('data-row')) + parseInt($(this).attr('data-sizey')));
                                                    if(temp > firstFreeRow)
                                                    {
                                                        firstFreeRow = temp;
                                                    }
                                                });
                                            }
                                        },
                                        serialize_params: function (w, wgd) {
                                            return {
                                                id: w.attr('id'),
                                                col: wgd.col,
                                                row: wgd.row,
                                                size_x: wgd.size_x,
                                                size_y: wgd.size_y
                                            }
                                        }, 
                                    });
                                    
                                    if (widgetsArray.length > 0) 
                                    {
                                        gridster = $("#container-widgets-cfg ul").gridster().data('gridster');
                                        for (var i = 0; i < widgetsArray.length; i++)
                                        {
                                            var name_w = data[i]['name_widget'];
                                            var time = 0;
                                            if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "Mensile") 
                                            {
                                                time = "30/DAY";
                                            } 
                                            else if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "Annuale") 
                                            {
                                                time = "365/DAY";
                                            } 
                                            else if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "Settimanale") 
                                            {
                                                time = "7/DAY";
                                            } 
                                            else if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "Giornaliera") 
                                            {
                                                time = "1/DAY";
                                            } 
                                            else if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "4 Ore") 
                                            {
                                                time = "4/HOUR";
                                            } 
                                            else if (data[i]['temporal_range_widget'] !== "" && widgetsArray[i]['temporal_range_widget'] === "12 Ore") 
                                            {
                                                time = "12/HOUR";
                                            }
                                            
                                            temp = parseInt(parseInt(widgetsArray[i]['n_row_widget']) + parseInt(widgetsArray[i]['size_rows_widget']));
                                            if(temp > firstFreeRow)
                                            {
                                                firstFreeRow = temp;
                                            }
                                            
                                            gridster.add_widget('<li id="' + name_w + '" class="gridsterCell"></li>', widgetsArray[i]['size_columns_widget'], widgetsArray[i]['size_rows_widget'], widgetsArray[i]['n_column_widget'], widgetsArray[i]['n_row_widget']);
                                           
                                            var type_metric = new Array();
                                            var source_metric = new Array();
                                            var info_message = new Array();
                                            for (var k = 0; k < widgetsArray[i]['metrics_prop'].length; k++) 
                                            {
                                                type_metric.push(widgetsArray[i]['metrics_prop'][k]['type_metric']);
                                                source_metric.push(widgetsArray[i]['metrics_prop'][k]['source_metric']);
                                                info_message.push(informazioni[i]);
                                            }

                                            $("#containerUl").find("li#" + name_w).load("../widgets/" + encodeURIComponent(widgetsArray[i]['source_file_widget']) + "?name=" + encodeURIComponent(name_w) + "&hostFile=config" + "&idWidget=" + encodeURIComponent(data[i]['id_widget']) + "&metric=" + encodeURIComponent(data[i]['id_metric_widget']) +
                                                    "&freq=" + encodeURIComponent(widgetsArray[i]['frequency_widget']) + "&title=" + encodeURIComponent(widgetsArray[i]['title_widget']) + "&color=" + encodeURIComponent(widgetsArray[i]['color_widget']) + "&source=" + encodeURIComponent(source_metric) +
                                                    "&fontColor=" + encodeURIComponent(widgetsArray[i]['fontColor']) + "&fontSize=" + encodeURIComponent(widgetsArray[i]['fontSize']) + "&type_metric=" + encodeURIComponent(type_metric) + "&tmprange=" + encodeURIComponent(time) + "&city=" + encodeURIComponent(widgetsArray[i]['municipality_widget']) + "&info=" + encodeURIComponent(info_message) + "&link_w=" + encodeURIComponent(widgetsArray[i]['link_w']) + "&frame_color=" + encodeURIComponent(widgetsArray[i]['frame_color']) +
                                                    "&headerFontColor=" + encodeURIComponent(widgetsArray[i]['headerFontColor']) + "&numCols=" + encodeURIComponent(num_cols) + "&sizeX=" + encodeURIComponent(widgetsArray[i]['size_columns_widget']) + "&sizeY=" + encodeURIComponent(widgetsArray[i]['size_rows_widget']) + "&controlsPosition=" + encodeURIComponent(widgetsArray[i]['controlsPosition']) + "&zoomControlsColor=" + encodeURIComponent(widgetsArray[i]['zoomControlsColor']) + "&showTitle=" + encodeURIComponent(widgetsArray[i]['showTitle']) + "&controlsVisibility=" + encodeURIComponent(widgetsArray[i]['controlsVisibility']) + "&zoomFactor=" + encodeURIComponent(widgetsArray[i]['zoomFactor']) + "&defaultTab=" + encodeURIComponent(widgetsArray[i]['defaultTab']) + "&scaleX=" + encodeURIComponent(widgetsArray[i]['scaleX']) + "&scaleY=" + encodeURIComponent(widgetsArray[i]['scaleY']), function () {
                                                $(this).find(".icons-modify-widget").css("display", "inline");
                                                $(this).find(".modifyWidgetGenContent").css("display", "block");
                                                $(this).find(".pcCountdownContainer").css("display", "none");
                                                $(this).find(".iconsModifyPcWidget").css("display", "flex");
                                                $(this).find(".iconsModifyPcWidget").css("align-items", "center");
                                                $(this).find(".iconsModifyPcWidget").css("justify-content", "flex-end");
                                            });
                                        }
                                    }
                                    //Applicazione bordi dei widgets
                                    if(widgetsBorders === 'yes')
                                    {
                                        $(".gridster .gs_w").css("border", "1px solid " + widgetsBordersColor);
                                    }
                                    else
                                    {
                                        $(".gridster .gs_w").css("border", "none");
                                    }
                                    $('#firstFreeRowInput').val(firstFreeRow);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
                                    $('#pageWrapperCfg').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
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
                    });
                    
                    $('#link_modifyDash').click(function ()
                    {
                        if($('#headerLogoImg').css("display") !== 'none')
                        {
                            $('#dashboardLogoLinkInput').removeAttr('disabled');
                        }
                    });
                    
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
                    
                    //Listener modifica dashboard attuale
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
                        
                        //NON RIAGGIUNGERLO, INDUCE BUG LOGOLINK NULLO
                        /*if(formData.get('dashboardLogoLinkInput') === '')
                        {
                            formData.set('dashboardLogoLinkInput', logoLink);
                        }*/
                        
                        if(formData.get('inputWidgetsBordersColor') === '') 
                        {
                            formData.set('inputWidgetsBordersColor', '#dddddd');
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
                                console.log("Indicatore: " + indicatore);
                            },
                            error: function (jqXHR, textStatus, errorThrown) 
                            {
                                alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
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

                                //24/02/2017 - Workaround per impedire di associare il widget StateRideAtaf a metriche diverse dalla sua e per impedire di associare il widget SmartDS a metriche diverse dalla sua
                                if(array_metrics[i].type === 'Percentuale')
                                {
                                    for(var z = array_metrics[i].widgets.length - 1; z >= 0; z--)
                                    {
                                        if((array_metrics[i].widgets[z].id_type_widget === 'widgetStateRideAtaf')&&(array_metrics[i].id !== 'Bus_State_Lines'))
                                        {
                                            array_metrics[i].widgets.splice(z, 1);
                                        }
                                    }
                                    
                                    for(var z = array_metrics[i].widgets.length - 1; z >= 0; z--)
                                    {
                                        if((array_metrics[i].widgets[z].id_type_widget !== 'widgetStateRideAtaf')&&(array_metrics[i].widgets[z].id_type_widget !== 'widgetPieChart')&&(array_metrics[i].id === 'Bus_State_Lines'))
                                        {
                                            array_metrics[i].widgets.splice(z, 1);
                                        } 
                                    }
                                    
                                    for(var z = array_metrics[i].widgets.length - 1; z >= 0; z--)
                                    {
                                        if((array_metrics[i].widgets[z].id_type_widget === 'widgetSmartDS')&&(array_metrics[i].id !== 'SmartDS_Process'))
                                        {
                                            array_metrics[i].widgets.splice(z, 1);
                                        } 
                                    }
                                    
                                    for(var z = array_metrics[i].widgets.length - 1; z >= 0; z--)
                                    {
                                        if((array_metrics[i].widgets[z].id_type_widget !== 'widgetSmartDS')&&(array_metrics[i].widgets[z].id_type_widget !== 'widgetPieChart')&&(array_metrics[i].id === 'SmartDS_Process'))
                                        {
                                            array_metrics[i].widgets.splice(z, 1);
                                        } 
                                    }
                                }
                                    
                                $('#select-metric').append('<option value="' + array_metrics[i]['id'] + '">' + array_metrics[i]['id'] + '</option>');
                            }
                            $('#select-metric').val(-1);

                        },
                        error: function (jqXHR, textStatus, errorThrown) 
                        {
                            alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
                        }
                    });

                    $('#modal-add-widget').on('shown.bs.modal', function () {
                        udpate_select_metric();
                    });

                    //Listener cambio metrica in aggiunta widget
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
                                metricType = array_metrics[j]['type'];
                                value_text += "Data Area: " + array_metrics[j]['area'] + ".\n";
                                value_text += "Data Source: " + array_metrics[j]['source'] + ".\n";
                                value_text += "Refresh rate: " + array_metrics[j]['freq'] + " s\n";
                                value_text += "Status: " + array_metrics[j]['status'] + ".";
                                $("#textarea-metric-c").val(value_text);
                                $("#inputFreqWidget").val(array_metrics[j]['freq']);
                                $("#inputTitleWidget").val(array_metrics[j]['descShort']);

                                if ($("#select-widget").is(':enabled')) 
                                {
                                    $("#select-widget").find('option').remove().end();
                                    for (var k = 0; k < array_metrics[j]['widgets'].length; k++) 
                                    {
                                        $("#select-widget").append('<option>' + array_metrics[j]['widgets'][k]['id_type_widget'] + '</option>');
                                    }
                                    
                                    //02/02/2017: commentato per testing, introduce bug sui ckeditor delle info
                                    update_select_widget();
                                    //$("#select-widget").val(-1);
                                }
                            }
                        }
                    }).change();
                    
                    
                    $('#inputSchedulerWidget').change(update_select_scheduler = function () 
                    {
                        if($('#inputSchedulerWidget').css("display") !== "none")
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
                                    if(data[0].id === "none")
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

                                    //$('#pageWrapperCfg').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
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
                       if($('#inputJobsAreasWidget').css("display") !== "none")
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
                        if($('#inputJobsGroupsWidget').css("display") !== "none")
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
                        if($('#inputJobsNamesWidget').css("display") !== "none")
                        {
                            $('#jobName').val($('#inputJobsNamesWidget').val());
                        }
                    }).change();
                    
                    //Listener aggiunta widget per cambio widget selezionato
                    $('#select-widget').change(update_select_widget = function () 
                    {
                        var dimMapRaw, dimMap = null;
                        
                            //Quando si cambia il widget selezionato di default le righe delle dimensioni devono essere vuote per impedire che si accumulino valori. Tanto si rimepie ad ogni change
                            $("#inputSizeRowsWidget").empty();
                            $("#inputSizeColumnsWidget").empty();
                            
                            $("#inputComuneRow").hide();
                            $("#inputUdmPosition").val(-1);
                            
                            $("#inputFirstAidRow").hide();
                            $("#inputFirstAidRow").val(-1);

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
                                if (array_metrics[i]['id'] === str) {
                                    for (var k = 0; k < array_metrics[i]['widgets'].length; k++) 
                                    {
                                        if (array_metrics[i]['widgets'][k]['id_type_widget'] === str2) 
                                        {
                                            //creare un elenco di dimensioni minime e massime di colonne dei widget
                                            var minR = parseInt(array_metrics[i]['widgets'][k]['size_rows_widget']);
                                            var maxR = parseInt(array_metrics[i]['widgets'][k]['max_rows_widget']);
                                            var minC = parseInt(array_metrics[i]['widgets'][k]['size_columns_widget']);
                                            var maxC = parseInt(array_metrics[i]['widgets'][k]['max_columns_widget']);
                                            var range_value = parseInt(array_metrics[i]['widgets'][k]['numeric_range']);
                                            dimMapRaw = (array_metrics[i]['widgets'][k]['dimMap']);
                                            if((dimMapRaw !== null) && (dimMapRaw !== "null") && (typeof dimMapRaw !== "undefined") && (dimMapRaw !== ""))
                                            {
                                                dimMap = JSON.parse(dimMapRaw);
                                            }


                                            if(dimMap === null)
                                            {
                                                $("#inputSizeRowsWidget").off();
                                                if (minR === maxR)
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
                                                if (minC === maxC)
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

                                            if (array_metrics[i]['widgets'][k]['color_widgetOption'] === 0) 
                                            {
                                                $('#inputColorWidget').attr('readonly', true);
                                                if ((array_metrics[i]['colorDefault'] !== null) && (array_metrics[i]['colorDefault'].length !== 0)) 
                                                {
                                                    $('.color-choice').colorpicker('setValue', array_metrics[i]['colorDefault']);
                                                }
                                            } 
                                            else 
                                            {
                                                $('#inputColorWidget').attr('readonly', false);
                                            }

                                            if ((str2 === "widgetTimeTrend") || (str2 === "widgetTimeTrendCompare")) 
                                            {
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
                               case "widgetNetworkAnalysis":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select name="addWidgetGeolocationWidgets" class="form-control" id="addWidgetGeolocationWidgets" multiple></select>');

                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    var targetsJson = [];

                                    $("li.gs_w").each(function(){
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          widgetsNumber++;
                                       }
                                    });

                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       $('#addWidgetGeolocationWidgets').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetsJson = [];
                                          }
                                          else
                                          {
                                             targetsJson = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetsJson));
                                       });
                                    }
                                    break;  
                               
                                case "widgetResources":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select name="addWidgetGeolocationWidgets" class="form-control" id="addWidgetGeolocationWidgets" multiple></select>');

                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    var targetsJson = [];

                                    $("li.gs_w").each(function(){
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          widgetsNumber++;
                                       }
                                    });

                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       $('#addWidgetGeolocationWidgets').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetsJson = [];
                                          }
                                          else
                                          {
                                             targetsJson = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetsJson));
                                       });
                                    }
                                    break;  
                                
                                 case "widgetEvacuationPlans":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="addWidgetGeolocationWidgets" name="addWidgetGeolocationWidgets"></select>');
                                    
                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    //JSON degli eventi da mostrare su ogni widget target di questo widget events: privo di eventi all'inizio
                                    var targetEventsJson = {};
                                    
                                    $("li.gs_w").each(function()
                                    {
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          targetEventsJson[widgetId] = new Array(); 
                                          widgetsNumber++;
                                       }
                                    });
                                    
                                    $("#parameters").val(JSON.stringify(targetEventsJson));
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newSelect.show();
                                       newSelect.val(-1);
                                       newLabel = $('<label for="addWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       var eventTypeSelect = $('<select name="addWidgetEventTypes" class="form-control" id="addWidgetEventTypes" multiple></select>');
                                       
                                       eventTypeSelect.append('<option value="approved">Approved</option>');
                                       eventTypeSelect.append('<option value="closed">Closed</option>');
                                       eventTypeSelect.append('<option value="in_progress">In progress</option>');
                                       eventTypeSelect.append('<option value="proposed">Proposed</option>');
                                       eventTypeSelect.append('<option value="rejected">Rejected</option>');
                                       
                                       eventTypeSelect.val(-1);
                                       newFormRow.append(newLabel);
                                       newInnerDiv.append(eventTypeSelect);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.hide();
                                       newInnerDiv.hide();
                                       
                                       $('#addWidgetEventTypes').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetEventTypes').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = [];
                                          }
                                          else
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetEventsJson));
                                       });
                                       
                                       $("#addWidgetGeolocationWidgets").change(function(){
                                          newLabel.show();
                                          newInnerDiv.show();
                                          $('#addWidgetEventTypes').selectpicker('val', targetEventsJson[$("#addWidgetGeolocationWidgets").val()]);
                                       });
                                    }
                                    break;  
                               
                                 case "widgetAlarms":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="addWidgetGeolocationWidgets" name="addWidgetGeolocationWidgets"></select>');
                                    
                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    //JSON degli eventi da mostrare su ogni widget target di questo widget events: privo di eventi all'inizio
                                    var targetEventsJson = {};
                                    
                                    $("li.gs_w").each(function()
                                    {
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          targetEventsJson[widgetId] = new Array(); 
                                          widgetsNumber++;
                                       }
                                    });
                                    
                                    $("#parameters").val(JSON.stringify(targetEventsJson));
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newSelect.show();
                                       newSelect.val(-1);
                                       newLabel = $('<label for="addWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       var eventTypeSelect = $('<select name="addWidgetEventTypes" class="form-control" id="addWidgetEventTypes" multiple></select>');
                                       for(var key in alarmTypes)
                                       {
                                          eventTypeSelect.append('<option value="' + key + '">' + alarmTypes[key].desc + '</option>');
                                       }
                                       
                                       eventTypeSelect.val(-1);
                                       newFormRow.append(newLabel);
                                       newInnerDiv.append(eventTypeSelect);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.hide();
                                       newInnerDiv.hide();
                                       
                                       $('#addWidgetEventTypes').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetEventTypes').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = [];
                                          }
                                          else
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetEventsJson));
                                       });
                                       
                                       $("#addWidgetGeolocationWidgets").change(function(){
                                          newLabel.show();
                                          newInnerDiv.show();
                                          $('#addWidgetEventTypes').selectpicker('val', targetEventsJson[$("#addWidgetGeolocationWidgets").val()]);
                                       });
                                    }
                                    break; 
                               
                                 case "widgetTrafficEvents":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="addWidgetGeolocationWidgets" name="addWidgetGeolocationWidgets"></select>');
                                    
                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    //JSON degli eventi da mostrare su ogni widget target di questo widget events: privo di eventi all'inizio
                                    var targetEventsJson = {};
                                    
                                    $("li.gs_w").each(function()
                                    {
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          targetEventsJson[widgetId] = new Array(); 
                                          widgetsNumber++;
                                       }
                                    });
                                    
                                    $("#parameters").val(JSON.stringify(targetEventsJson));
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newSelect.show();
                                       newSelect.val(-1);
                                       newLabel = $('<label for="addWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       var eventTypeSelect = $('<select name="addWidgetEventTypes" class="form-control" id="addWidgetEventTypes" multiple></select>');
                                       var eventTypeNum = null;
                                       for(var key in trafficEventTypes)
                                       {
                                          eventTypeNum = key.replace("type", "");
                                          eventTypeSelect.append('<option value="' + eventTypeNum + '">' + trafficEventTypes[key].desc + '</option>');
                                       }
                                       
                                       /*eventTypeSelect.append('<option value="1">Incident</option>');
                                       eventTypeSelect.append('<option value="25">Road works</option>');
                                       eventTypeSelect.append('<option value="others">Others</option>');*/
                                       eventTypeSelect.val(-1);
                                       newFormRow.append(newLabel);
                                       newInnerDiv.append(eventTypeSelect);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.hide();
                                       newInnerDiv.hide();
                                       
                                       $('#addWidgetEventTypes').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetEventTypes').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = [];
                                          }
                                          else
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetEventsJson));
                                       });
                                       
                                       $("#addWidgetGeolocationWidgets").change(function(){
                                          newLabel.show();
                                          newInnerDiv.show();
                                          $('#addWidgetEventTypes').selectpicker('val', targetEventsJson[$("#addWidgetGeolocationWidgets").val()]);
                                       });
                                    }
                                    break;
      
                                 case "widgetFirstAid":
                                    var currentParams, i, k, currentFieldIndex, currentSeriesIndex = null;
                                    var thrTables1 = new Array();
                                    var thrTables2 = new Array();
                                    
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#inputFirstAidRow').show();
                                    $('#addWidgetFirstAidHospital').attr("disabled", false);
                                    $('#addWidgetFirstAidHospital').prop("required", true);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    
                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Visualizzazione campi specifici per questo widget
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    newLabel = $('<label for="addWidgetFirstAidMode" class="col-md-2 control-label">Visualization mode</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="addWidgetFirstAidMode" name="addWidgetFirstAidMode" required></select>');
                                    newSelect.append('<option value="singleSummary">Single hospital - Totals only</option>');
                                    newSelect.append('<option value="singleDetails">Single hospital - Details</option>');
                                    newSelect.append('<option value="hospitalsOverview">Multiple hospitals overview</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    var hospitalSelect = '<label for="addWidgetFirstAidHospital" class="col-md-2 control-label">First aid</label>' +
                                    '<div class="col-md-3">' +
                                        '<div class="input-group">' +
                                            '<select name="addWidgetFirstAidHospital" class="form-control" id="addWidgetFirstAidHospital">' + 
                                            '</select>' +
                                        '</div>' +
                                    '</div>';
                            
                                    var multipleHospitalSelect = '<label for="addWidgetFirstAidHospitals" class="col-md-2 control-label">First aids</label>' +
                                            '<div class="col-md-3">' +
                                                '<div class="input-group">' +
                                                    '<select name="addWidgetFirstAidHospitals" class="form-control" id="addWidgetFirstAidHospitals" multiple>' + 
                                                    '</select>' +
                                                '</div>' +
                                            '</div>';
                                    
                                    newFormRow.append(hospitalSelect);
                                    $('label[for=addWidgetFirstAidHospital]').show();
                                    $('#addWidgetFirstAidHospital').parent().parent().show();
                                    
                                    newFormRow.append(multipleHospitalSelect);
                                    $('label[for=addWidgetFirstAidHospitals]').hide();
                                    $('#addWidgetFirstAidHospitals').parent().parent().hide();
                                    
                                    var hospitalName, hospitalUrl = null;
                                    
                                    for(var i = 0; i < hospitalList.Services.features.length; i++)
                                    {
                                       hospitalName = hospitalList.Services.features[i].properties.name;
                                       hospitalUrl = hospitalList.Services.features[i].properties.serviceUri;
                                       
                                       hospitalName = hospitalName.replace("PRONTO SOCCORSO", "PS");
                                       hospitalName = hospitalName.replace("PRIMO INTERVENTO", "PI");
                                       hospitalName = hospitalName.replace("AZIENDA OSPEDALIERA", "AO");
                                       hospitalName = hospitalName.replace("PRESIDIO OSPEDALIERO", "PO");
                                       hospitalName = hospitalName.replace("ISTITUTO DI PUBBLICA ASSISTENZA", "IPA");
                                       hospitalName = hospitalName.replace("ASSOCIAZIONE DI PUBBLICA ASSISTENZA", "APA");
                                       hospitalName = hospitalName.replace("OSPEDALE DI", "");
                                       hospitalName = hospitalName.replace("OSPEDALE DEL", "");
                                       hospitalName = hospitalName.replace("OSPEDALE DELL'", "");
                                       hospitalName = hospitalName.replace("OSPEDALE DELLA", "");
                                       hospitalName = hospitalName.replace("DELL'OSPEDALE", "");
                                       hospitalName = hospitalName.replace("OSPEDALE", "");
                                       hospitalName = hospitalName.replace("ITALIANA", "");

                                       if($("#addWidgetFirstAidHospital").find('option[value="' + hospitalUrl + '"]').length <= 0)
                                       {
                                          $("#addWidgetFirstAidHospital").append('<option value="' + hospitalUrl + '">' + hospitalName + '</option>');
                                       }
                                       
                                       if($("#addWidgetFirstAidHospitals").find('option[value="' + hospitalUrl + '"]').length <= 0)
                                       {
                                          $("#addWidgetFirstAidHospitals").append('<option value="' + hospitalUrl + '">' + hospitalName + '</option>');
                                       }
                                    }

                                    $("#serviceUri").val($("#addWidgetFirstAidHospital").val());
                                    
                                    var series = {  
                                       "firstAxis":{  
                                          "desc":"Priority",
                                          "labels":[  
                                             "Red code",
                                             "Yellow code",
                                             "Green code",
                                             "Blue code",
                                             "White code"
                                          ]
                                       },
                                       "secondAxis":{  
                                          "desc":"Status",
                                          "labels":[  
                                             "Totals"
                                          ],
                                          "series":[]
                                       }
                                    };
                                    
                                    $("#addWidgetFirstAidMode").change(function()
                                    {
                                       $("#infoMainSelect").val(-1);
                                       
                                       switch($(this).val())
                                       {
                                          case "singleSummary": 
                                                $("#serviceUri").val($("#addWidgetFirstAidHospital").val());
                                                $('label[for=addWidgetFirstAidHospitals]').hide();
                                                $('#addWidgetFirstAidHospitals').parent().parent().hide();
                                                $('label[for=addWidgetFirstAidHospital]').show();
                                                $('#addWidgetFirstAidHospital').parent().parent().show();
                                                series = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Status",
                                                      "labels":[
                                                         "Totals"
                                                      ],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                $('label[for="alrAxisSel"]').hide();
                                                $("#alrAxisSel").hide();
                                                $('label[for="alrFieldSel"]').hide();
                                                $("#alrFieldSel").hide();
                                                $("#alrAxisSel").empty();
                                                $("#alrAxisSel").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                $("#alrAxisSel").val(-1);
                                                $("#addWidgetRangeTableContainer").hide();
                                                $("#alrThrSel").val("no");
                                                $("#alrThrSel").attr("disabled", false);
                                                $("#alrThrSel").prop("required", true);
                                                
                                                $("#showTableFirstCell").val(-1);
                                                $("#showTableFirstCell").prop("required", false);
                                                $("#showTableFirstCell").attr("disabled", true);

                                                $("#tableFirstCellFontSize").val("");
                                                $("#tableFirstCellFontSize").prop("required", false);
                                                $("#tableFirstCellFontSize").attr("disabled", true);	

                                                $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#tableFirstCellFontColor").val("");
                                                $("#tableFirstCellFontColor").prop("required", false);
                                                $("#tableFirstCellFontColor").attr("disabled", true);	

                                                $("#rowsLabelsFontSize").val("");
                                                $("#rowsLabelsFontSize").prop("required", false);
                                                $("#rowsLabelsFontSize").attr("disabled", true);	

                                                $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#rowsLabelsFontColor").val("");
                                                $("#rowsLabelsFontColor").prop("required", false);
                                                $("#rowsLabelsFontColor").attr("disabled", true);

                                                $("#widgetRowsLabelsBckColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#rowsLabelsBckColor").val("");
                                                $("#rowsLabelsBckColor").prop("required", false);
                                                $("#rowsLabelsBckColor").attr("disabled", true);
                                                break;
                                                
                                          case "singleDetails":
                                                $("#serviceUri").val($("#addWidgetFirstAidHospital").val());
                                                $('label[for=addWidgetFirstAidHospitals]').hide();
                                                $('#addWidgetFirstAidHospitals').parent().parent().hide();
                                                $('label[for=addWidgetFirstAidHospital]').show();
                                                $('#addWidgetFirstAidHospital').parent().parent().show();
                                                series = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Status",
                                                      "labels":[  
                                                         "Addressed",
                                                         "Waiting",
                                                         "In visit",
                                                         "In observation",
                                                         "Totals"
                                                      ],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                $('label[for="alrAxisSel"]').hide();
                                                $("#alrAxisSel").hide();
                                                $('label[for="alrFieldSel"]').hide();
                                                $("#alrFieldSel").hide();
                                                $("#alrAxisSel").empty();
                                                $("#alrAxisSel").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                //$("#alrAxisSel").append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                                $("#alrAxisSel").val(-1);
                                                $("#addWidgetRangeTableContainer").hide();
                                                $("#alrThrSel").val("no");
                                                $("#alrThrSel").attr("disabled", false);
                                                $("#alrThrSel").prop("required", true);
                                                
                                                $("#showTableFirstCell").val("yes");
                                                $("#showTableFirstCell").prop("required", true);
                                                $("#showTableFirstCell").attr("disabled", false);

                                                $("#tableFirstCellFontSize").val("10");
                                                $("#tableFirstCellFontSize").prop("required", true);
                                                $("#tableFirstCellFontSize").attr("disabled", false);	

                                                $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#tableFirstCellFontColor").val("#000000");
                                                $("#tableFirstCellFontColor").prop("required", true);
                                                $("#tableFirstCellFontColor").attr("disabled", false);	

                                                $("#rowsLabelsFontSize").val("10");
                                                $("#rowsLabelsFontSize").prop("required", true);
                                                $("#rowsLabelsFontSize").attr("disabled", false);	

                                                $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#rowsLabelsFontColor").val("#000000");
                                                $("#rowsLabelsFontColor").prop("required", true);
                                                $("#rowsLabelsFontColor").attr("disabled", false);

                                                $("#widgetRowsLabelsBckColor").parent().parent().parent().colorpicker("setValue","#FFFFFF");
                                                $("#rowsLabelsBckColor").val("#FFFFFF");
                                                $("#rowsLabelsBckColor").prop("required", true);
                                                $("#rowsLabelsBckColor").attr("disabled", false);
                                                break;      
                                          
                                          case "hospitalsOverview":
                                                $("#serviceUri").val("");
                                                $('label[for=addWidgetFirstAidHospital]').hide();
                                                $('#addWidgetFirstAidHospital').parent().parent().hide();
                                                $('label[for=addWidgetFirstAidHospitals]').show();
                                                $('#addWidgetFirstAidHospitals').parent().parent().show();
                                                $('#addWidgetFirstAidHospitals').selectpicker({
                                                   width: 110
                                                });
                                                
                                                series = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Hospital",
                                                      "labels":[],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                $('#addWidgetFirstAidHospitals').on('changed.bs.select', function (e) 
                                                {
                                                   $("#hospitalList").val(JSON.stringify($(this).val()));
                                                   
                                                   var labelsString = $('button[data-id="addWidgetFirstAidHospitals"] span').eq(0).html().replace(/  /g, " ");
                                                   var labels = labelsString.split(", ");
                                                   series.secondAxis.labels = labels;
                                                   showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                                });
                                                
                                                $('label[for="alrAxisSel"]').hide();
                                                $("#alrAxisSel").hide();
                                                $('label[for="alrFieldSel"]').hide();
                                                $("#alrFieldSel").hide();
                                                $("#alrAxisSel").empty();
                                                $("#alrAxisSel").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                $("#alrAxisSel").append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                                $("#alrAxisSel").val(-1);
                                                $("#addWidgetRangeTableContainer").hide();
                                                $("#alrThrSel").val("no");
                                                $("#alrThrSel").attr("disabled", true);
                                                
                                                $("#showTableFirstCell").val("yes");
                                                $("#showTableFirstCell").prop("required", true);
                                                $("#showTableFirstCell").attr("disabled", false);

                                                $("#tableFirstCellFontSize").val("10");
                                                $("#tableFirstCellFontSize").prop("required", true);
                                                $("#tableFirstCellFontSize").attr("disabled", false);	

                                                $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#tableFirstCellFontColor").val("#000000");
                                                $("#tableFirstCellFontColor").prop("required", true);
                                                $("#tableFirstCellFontColor").attr("disabled", false);	

                                                $("#rowsLabelsFontSize").val("10");
                                                $("#rowsLabelsFontSize").prop("required", true);
                                                $("#rowsLabelsFontSize").attr("disabled", false);	

                                                $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#rowsLabelsFontColor").val("#000000");
                                                $("#rowsLabelsFontColor").prop("required", true);
                                                $("#rowsLabelsFontColor").attr("disabled", false);

                                                $("#widgetRowsLabelsBckColor").parent().parent().parent().colorpicker("setValue","#FFFFFF");
                                                $("#rowsLabelsBckColor").val("#FFFFFF");
                                                $("#rowsLabelsBckColor").prop("required", true);
                                                $("#rowsLabelsBckColor").attr("disabled", false);
                                                
                                             break;
                                       }
                                       
                                       //Distruzione e ricostruzione dei CKEDITOR perché a seconda del tipo di visualizzazione si mostra o si nasconde la possibilità di aggiungere info sul secondo asse
                                       showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                       
                                       //Distruzione thr tables
                                       thrTables1 = new Array();
                                       thrTables2 = new Array();

                                       //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                       setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());

                                       //Costruzione THRTables vuote
                                       buildEmptyThrTables();
                                    });

                                    $("#addWidgetFirstAidHospital").change(function()
                                    {
                                       $("#serviceUri").val($(this).val());
                                    });
                                    
                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());
                                    
                                    //Costruzione THRTables vuote
                                    buildEmptyThrTables();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    //Select show first cell
                                    newLabel = $('<label for="showTableFirstCell" class="col-md-2 control-label">Show first cell</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="showTableFirstCell" name="showTableFirstCell" required></select>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    $("#showTableFirstCell").val(-1);
                                    $("#showTableFirstCell").prop("required", false);
                                    $("#showTableFirstCell").attr("disabled", true);
                                    
                                    //Nuova riga
                                    //First cell font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="tableFirstCellFontSize" class="col-md-2 control-label">First cell font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="tableFirstCellFontSize" name="tableFirstCellFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    $("#tableFirstCellFontSize").val("");
                                    $("#tableFirstCellFontSize").prop("required", false);
                                    $("#tableFirstCellFontSize").attr("disabled", true);
                                    
                                    //First cell font color
                                    newLabel = $('<label for="tableFirstCellFontColor" class="col-md-2 control-label">First cell font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="tableFirstCellFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableFirstCellFontColor" name="tableFirstCellFontColor" required><span class="input-group-addon"><i id="widgetFirstCellFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#tableFirstCellFontColorContainer').show();
                                    $('#tableFirstCellFontColor').show();
                                    $("#widgetFirstCellFontColor").css('display', 'block');
                                    $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                    $("#tableFirstCellFontColor").val("");
                                    $("#tableFirstCellFontColor").prop("required", false);
                                    $("#tableFirstCellFontColor").attr("disabled", true);
                                    
                                    
                                    //Nuova riga
                                    //Rows labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsFontSize" class="col-md-2 control-label">Rows labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSize" name="rowsLabelsFontSize" value="10" required> ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    $("#rowsLabelsFontSize").val("");
                                    $("#rowsLabelsFontSize").prop("required", false);
                                    $("#rowsLabelsFontSize").attr("disabled", true);
                                    
                                    //Rows labels font color
                                    newLabel = $('<label for="rowsLabelsFontColor" class="col-md-2 control-label">Rows labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColor" name="rowsLabelsFontColor" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsFontColorContainer').show();
                                    $('#rowsLabelsFontColor').show();
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: "#eeeeee"});
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                    $("#rowsLabelsFontColor").val("");
                                    $("#rowsLabelsFontColor").prop("required", false);
                                    $("#rowsLabelsFontColor").attr("disabled", true);
                                    
                                    //Nuova riga
                                    //Cols labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="colsLabelsFontSize" class="col-md-2 control-label">Cols labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="colsLabelsFontSize" name="colsLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Cols labels font color
                                    newLabel = $('<label for="colsLabelsFontColor" class="col-md-2 control-label">Cols labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColor" name="colsLabelsFontColor" required><span class="input-group-addon"><i id="widgetColsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsFontColorContainer').show();
                                    $('#colsLabelsFontColor').show();
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Rows labels background color
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsBckColor" class="col-md-2 control-label">Rows labels background color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsBckColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsBckColor" name="rowsLabelsBckColor" required><span class="input-group-addon"><i id="widgetRowsLabelsBckColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsBckColorContainer').show();
                                    $('#rowsLabelsBckColor').show();
                                    $("#widgetRowsLabelsBckColor").css('display', 'block');
                                    $("#widgetRowsLabelsBckColor").parent().parent().parent().colorpicker({color: "#FFFFFF"});
                                    $("#rowsLabelsBckColor").val("");
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                    $("#rowsLabelsFontColor").val("");
                                    $("#rowsLabelsBckColor").prop("required", false);
                                    $("#rowsLabelsBckColor").attr("disabled", true);
                                    
                                    //Nuova riga
                                    //Table borders
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="tableBorders" class="col-md-2 control-label">Table borders</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="tableBorders" name="tableBorders"></select>');
                                    newSelect.append('<option value="no">No borders</option>');
                                    newSelect.append('<option value="horizontal">Horizontal borders only</option>');
                                    newSelect.append('<option value="all">All borders</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Table borders color
                                    newLabel = $('<label for="tableBordersColor" class="col-md-2 control-label">Table borders color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="tableBordersColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableBordersColor" name="tableBordersColor" required><span class="input-group-addon"><i id="widgetTableBordersColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#tableBordersColorContainer').show();
                                    $('#tableBordersColor').show();
                                    $("#widgetTableBordersColor").css('display', 'block');
                                    $("#widgetTableBordersColor").parent().parent().parent().colorpicker({color: "#EEEEEE"});
                                    
                                    //Nuova riga
                                    //Set thresholds
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val('no');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                    $('#alrThrSel').change(alrThrSelListener);
                                    
                                    //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                    newLabel = $('<label for="alrAxisSel" class="col-md-2 control-label">Thresholds target set</label>');
                                    newSelect = $('<select class="form-control" id="alrAxisSel" name="alrAxisSel"></select>');
                                    newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                    //newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                    newSelect.val(-1);
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Nuova riga
                                    //Threshold field select
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                    newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Contenitore per tabella delle soglie
                                    addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                    addWidgetRangeTableContainer.hide(); 

                                    //Funzione che crea e mostra i ckeditors per i campi info del widget
                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                    
                                    removeWidgetProcessGeneralFields("addWidget");
                                 break;
      
      
                                 case "widgetRadarSeries":    
                                    var currentParams, i, k, currentFieldIndex, currentSeriesIndex, row, cell, descName = null;
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var seriesString = metricData.data[0].commit.author.series;
                                    var series = jQuery.parseJSON(seriesString);
                                    
                                    //Costruzione THRTable vuota
                                    var thrTable = $("<table class='thrRangeTableRadar table table-bordered'></table>");
                                    row = $('<tr></tr>');
                                    cell = $('<td><a href="#"><i class="fa fa-plus" style="font-size:24px;color:#337ab7"></i></a></td>');
                                    row.append(cell);
                                    cell = $('<td>Color</td>');
                                    row.append(cell);
                                    cell = $('<td>Short description</td>');
                                    row.append(cell);
                                    
                                    //Colonne per i limiti sup di ogni campo
                                    for(var i in series.firstAxis.labels)
                                    {
                                        if(series.firstAxis.labels[i].length > 8)
                                        {
                                            descName = series.firstAxis.labels[i].substr(0,8) + "...";
                                        }
                                        else
                                        {
                                            descName = series.firstAxis.labels[i];
                                        }

                                        cell = $('<td class="boundDesc"><b>' + descName + '</b><br/>limit</td>');
                                        row.append(cell);
                                    }
                                    
                                    thrTable.append(row);
                                    
                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobalsRadar(null, thrTable, series, $('#select-widget').val());
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();
                                    
                                    
                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Proprietà specifiche
                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Rows labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsFontSize" class="col-md-2 control-label">X-Axis labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSize" name="rowsLabelsFontSize" value="10" required> ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Rows labels font color
                                    newLabel = $('<label for="rowsLabelsFontColor" class="col-md-2 control-label">X-Axis labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColor" name="rowsLabelsFontColor" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsFontColorContainer').show();
                                    $('#rowsLabelsFontColor').show();
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Cols labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="colsLabelsFontSize" class="col-md-2 control-label">Y-Axis labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="colsLabelsFontSize" name="colsLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Cols labels font color
                                    newLabel = $('<label for="colsLabelsFontColor" class="col-md-2 control-label">Y-Axis labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColor" name="colsLabelsFontColor" required><span class="input-group-addon"><i id="widgetColsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsFontColorContainer').show();
                                    $('#colsLabelsFontColor').show();
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Data labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabelsFontSize" class="col-md-2 control-label">Data labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="dataLabelsFontSize" name="dataLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Data labels font color
                                    newLabel = $('<label for="dataLabelsFontColor" class="col-md-2 control-label">Data labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="dataLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColor" name="dataLabelsFontColor" required><span class="input-group-addon"><i id="widgetDataLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#dataLabelsFontColorContainer').show();
                                    $('#dataLabelsFontColor').show();
                                    $("#widgetDataLabelsFontColor").css('display', 'block');
                                    $("#widgetDataLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Legend font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="legendFontSize" class="col-md-2 control-label">Legend font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="legendFontSize" name="legendFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Legend font color
                                    newLabel = $('<label for="legendFontColor" class="col-md-2 control-label">Legend font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="legendFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColor" name="legendFontColor" required><span class="input-group-addon"><i id="widgetLegendFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#legendFontColorContainer').show();
                                    $('#legendFontColor').show();
                                    $("#widgetLegendFontColor").css('display', 'block');
                                    $("#widgetLegendFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Grid lines width
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="gridLinesWidth" class="col-md-2 control-label">Grid lines width</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="gridLinesWidth" name="gridLinesWidth" value="1" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Grid lines color
                                    newLabel = $('<label for="gridLinesColor" class="col-md-2 control-label">Grid lines color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="gridLinesColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="gridLinesColor" name="gridLinesColor" required><span class="input-group-addon"><i id="widgetGridLinesColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#gridLinesColorContainer').show();
                                    $('#gridLinesColor').show();
                                    $("#widgetGridLinesColor").css('display', 'block');
                                    $("#widgetGridLinesColor").parent().parent().parent().colorpicker({color: "#e6e6e6"});
                                    
                                    //Nuova riga
                                    //Lines width
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="linesWidth" class="col-md-2 control-label">Lines width</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="linesWidth" name="linesWidth" value="1" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Lines colors
                                    newLabel = $('<label for="barsColorsSelect" class="col-md-2 control-label">Lines colors</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="barsColorsSelect" name="barsColorsSelect"></select>');
                                    newSelect.append('<option value="auto">Automatic</option>');
                                    newSelect.append('<option value="manual">Manual</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Contenitore per tabella dei colori
                                    barsColorsTableContainer = $('<div id="barsColorsTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(barsColorsTableContainer);
                                    barsColorsTableContainer.hide();

                                    //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                    var colorsTable, newRow, newCell = null;
                                    var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                    var colorsArray = new Array();

                                    function updateWidgetBarSeriesColors(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray[index] = newColor;
                                        $("#barsColors").val(JSON.stringify(colorsArray));
                                    }

                                    colorsTable = $("<table id ='colorsTable' class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                    for(var i in series.secondAxis.labels)
                                    {
                                        newRow = $('<tr></tr>');
                                        newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                        newRow.append(newCell);
                                        newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                        newRow.append(newCell);
                                        newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                        newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColors); 
                                        colorsArray.push(defaultColorsArray[i%10]);
                                        colorsTable.append(newRow);
                                    }

                                    $("#barsColors").val(JSON.stringify(colorsArray));
                                    $('#barsColorsTableContainer').append(colorsTable);

                                    $('#barsColorsSelect').change(function () 
                                    {
                                        if($('#barsColorsSelect').val() === "manual")
                                        {
                                            $('#barsColorsTableContainer').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainer').hide();
                                        }
                                    });


                                    //Codice di creazione soglie
                                    //Nuova riga
                                    //Set thresholds
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val('no');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Thresholds lines width
                                    newLabel = $('<label for="alrThrLinesWidth" class="col-md-2 control-label">Thresholds lines width</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="alrThrLinesWidth" name="alrThrLinesWidth" value="1" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newInput.hide();
                                    
                                    //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                    $('#alrThrSel').change(alrThrSelListenerRadar);
                                    
                                    //Contenitore per tabella delle soglie
                                    addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row  thrRangeTableRadarContainer"></div>');  //rowCenterContent
                                    $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                    addWidgetRangeTableContainer.hide();
                                    break;
        
                                case "widgetLineSeries": case "widgetCurvedLineSeries":
                                    var currentParams, i, k, currentFieldIndex, currentSeriesIndex = null;
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var seriesString = metricData.data[0].commit.author.series;
                                    var series = jQuery.parseJSON(seriesString);
                                    var thrTables1 = new Array();
                                    var thrTables2 = new Array(); 
                                    
                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());
                                    
                                    //Costruzione THRTables vuote
                                    buildEmptyThrTables();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                        
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Proprietà specifiche
                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);

                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //X-Axis dataset
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="xAxisDataset" class="col-md-2 control-label">X-Axis dataset</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="xAxisDataset" name="xAxisDataset" required>');
                                    newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                    newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Line width
                                    newLabel = $('<label for="lineWidth" class="col-md-2 control-label">Line width</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="lineWidth" name="lineWidth" value="2" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Nuova riga
                                    //X-Axis labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsFontSize" class="col-md-2 control-label">X-Axis labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSize" name="rowsLabelsFontSize" value="10" required> ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //X-Axis labels font color
                                    newLabel = $('<label for="rowsLabelsFontColor" class="col-md-2 control-label">X-Axis labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColor" name="rowsLabelsFontColor" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsFontColorContainer').show();
                                    $('#rowsLabelsFontColor').show();
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Y-Axis labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="colsLabelsFontSize" class="col-md-2 control-label">Y-Axis labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="colsLabelsFontSize" name="colsLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Y-Axis labels font color
                                    newLabel = $('<label for="colsLabelsFontColor" class="col-md-2 control-label">Y-Axis labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColor" name="colsLabelsFontColor" required><span class="input-group-addon"><i id="widgetColsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsFontColorContainer').show();
                                    $('#colsLabelsFontColor').show();
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Data labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabelsFontSize" class="col-md-2 control-label">Data labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="dataLabelsFontSize" name="dataLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Data labels font color
                                    newLabel = $('<label for="dataLabelsFontColor" class="col-md-2 control-label">Data labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="dataLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColor" name="dataLabelsFontColor" required><span class="input-group-addon"><i id="widgetDataLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#dataLabelsFontColorContainer').show();
                                    $('#dataLabelsFontColor').show();
                                    $("#widgetDataLabelsFontColor").css('display', 'block');
                                    $("#widgetDataLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Legend font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="legendFontSize" class="col-md-2 control-label">Legend font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="legendFontSize" name="legendFontSize" value="10" required>  ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Legend font color
                                    newLabel = $('<label for="legendFontColor" class="col-md-2 control-label">Legend font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="legendFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColor" name="legendFontColor" required><span class="input-group-addon"><i id="widgetLegendFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#legendFontColorContainer').show();
                                    $('#legendFontColor').show();
                                    $("#widgetLegendFontColor").css('display', 'block');
                                    $("#widgetLegendFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Lines colors
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="barsColorsSelect" class="col-md-2 control-label">Lines colors</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="barsColorsSelect" name="barsColorsSelect"></select>');
                                    newSelect.append('<option value="auto">Automatic</option>');
                                    newSelect.append('<option value="manual">Manual</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Chart type
                                    newLabel = $('<label for="chartType" class="col-md-2 control-label">Chart type</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="chartType" name="chartType"></select>');
                                    newSelect.append("<option value='lines'>Simple lines</option>");
                                    newSelect.append("<option value='area'>Area lines</option>");
                                    newSelect.append("<option value='stacked'>Stacked area lines</option>");
                                    newSelect.val("lines");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Nuova riga
                                    //Data labels
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabels" class="col-md-2 control-label">Data labels</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="dataLabels" name="dataLabels"> ');
                                    newSelect.append('<option value="no">No data labels</option>');
                                    newSelect.append('<option value="value">Value only</option>');
                                    newSelect.append('<option value="full">Field name and value</option>');
                                    newSelect.val("value");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Contenitore per tabella dei colori
                                    barsColorsTableContainer = $('<div id="barsColorsTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(barsColorsTableContainer);
                                    barsColorsTableContainer.hide();

                                    //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                    var colorsTable, newRow, newCell = null;
                                    var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                    var colorsArray = new Array();
                                    
                                    function updateXAxisSelect()
                                    {
                                        var colorsTarget = null;
                                        colorsTable.find('tr').remove();
                                        colorsTable.append("<tr><td>Series</td><td>Color</td></tr>");
                                        
                                        $('#alrAxisSel').empty();
                                    
                                        if($("#xAxisDataset").val() === series.firstAxis.desc)
                                        {
                                            //Grafico non trasposto
                                            colorsTarget = series.secondAxis.labels;
                                            $('#alrAxisSel').append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        }
                                        else
                                        {
                                            //Grafico trasposto
                                            colorsTarget = series.firstAxis.labels;
                                            $('#alrAxisSel').append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                        }
                                        
                                        alrAxisSelListener();

                                        for(var i in colorsTarget)
                                        {
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + colorsTarget[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColors); 
                                            colorsArray.push(defaultColorsArray[i%10]);
                                            colorsTable.append(newRow);
                                        }
                                        $('label[for="alrFieldSel"]').hide();
                                        $('#alrFieldSel').hide();
                                        $('#alrFieldSel').parent().hide();
                                        $('label[for="alrAxisSel"]').hide();
                                        $('#alrAxisSel').hide();
                                        $('#alrAxisSel').parent().hide();
                                        $('#alrThrSel').val("no");
                                    }
            
                                    function updateWidgetBarSeriesColors(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray[index] = newColor;
                                        $("#barsColors").val(JSON.stringify(colorsArray));
                                    }
                                    
                                    $("#xAxisDataset").off();
                                    $("#xAxisDataset").on("change", updateXAxisSelect);

                                    colorsTable = $("<table id ='colorsTable' class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                    
                                    updateXAxisSelect();

                                    $("#barsColors").val(JSON.stringify(colorsArray));
                                    $('#barsColorsTableContainer').append(colorsTable);

                                    $('#barsColorsSelect').change(function() 
                                    {
                                        if($('#barsColorsSelect').val() === "manual")
                                        {
                                            $('#barsColorsTableContainer').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainer').hide();
                                        }
                                    });

                                    //Codice costruzione soglie
                                    //Nuova riga
                                    //Set thresholds
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val('no');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                    $('#alrThrSel').change(alrThrSelListener);
                                    
                                    //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                    newLabel = $('<label for="alrAxisSel" class="col-md-2 control-label">Thresholds target set</label>');
                                    newSelect = $('<select class="form-control" id="alrAxisSel" name="alrAxisSel"></select>');
                                    if($("#xAxisDataset").val() === series.firstAxis.desc)
                                    {
                                        //No trasposizione
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                    }
                                    else
                                    {
                                        //Trasposizione
                                        newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                    }
                                    alrAxisSelListener();
                                    newSelect.val(-1);
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Nuova riga
                                    //Threshold field select
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                    newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Threshold look&feel
                                    newLabel = $('<label for="alrLook" class="col-md-2 control-label">Thresholds look&feel</label>');
                                    newSelect = $('<select class="form-control" id="alrLook" name="alrLook">');
                                    newSelect.append('<option value="none">Tooltip only</option>');
                                    newSelect.append('<option value="lines">Lines</option>');
                                    newSelect.append('<option value="areas">Background areas</option>');
                                    newSelect.val("lines");
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Contenitore per tabella delle soglie
                                    addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                    addWidgetRangeTableContainer.hide();
                                    break;
        
                                case "widgetScatterSeries":
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Proprietà specifiche
                                    var currentParams = null;
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var seriesString = metricData.data[0].commit.author.series;
                                    var series = jQuery.parseJSON(seriesString);
                                    var thrTables1 = new Array();
                                    var thrTables2 = new Array();
                                    var i, k, currentFieldIndex, currentSeriesIndex = null;

                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget').val());

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);

                                    removeWidgetProcessGeneralFields("addWidget");
                                    removeWidgetTableListeners("addWidget");  removeWidgetBarSeriesListeners("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $("label[for='rowsLabelsFontSize']").parent().show();
                                    $("label[for='rowsLabelsFontSize']").show();
                                    $("#rowsLabelsFontSize").show();

                                    $("label[for='rowsLabelsFontColor']").show();
                                    $("#rowsLabelsFontColor").show();
                                    //Questa falla con css sennò la mostra inline e non viene colorato!
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().colorpicker({color: '#000000'});

                                    $("label[for='colsLabelsFontSize']").parent().show();
                                    $("label[for='colsLabelsFontSize']").show();
                                    $("#colsLabelsFontSize").show();

                                    $("label[for='colsLabelsFontColor']").show();
                                    $("#colsLabelsFontColor").show();
                                    //Questa falla con css sennò la mostra inline e non viene colorato!
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().colorpicker({color: '#000000'});

                                    $("label[for='dataLabelsFontSize']").parent().show();
                                    $("label[for='dataLabelsFontSize']").show();
                                    $("#dataLabelsFontSize").show();

                                    $("label[for='dataLabelsFontColor']").show();
                                    $("#dataLabelsFontColor").show();
                                    //Questa falla con css sennò la mostra inline e non viene colorato!
                                    $("#widgetDataLabelsFontColor").css('display', 'block');
                                    $("#widgetDataLabelsFontColor").parent().parent().colorpicker({color: '#000000'});

                                    $("label[for='legendFontSize']").parent().show();
                                    $("label[for='legendFontSize']").show();
                                    $("#legendFontSize").show();

                                    $("label[for='legendFontColor']").show();
                                    $("#legendFontColor").show();
                                    //Questa falla con css sennò la mostra inline e non viene colorato!
                                    $("#widgetLegendFontColor").css('display', 'block');
                                    $("#widgetLegendFontColor").parent().parent().colorpicker({color: '#000000'});

                                    $("label[for='barsColorsSelect']").html("Markers colors");
                                    $("label[for='barsColorsSelect']").parent().show();
                                    $("label[for='barsColorsSelect']").show();
                                    $("#barsColorsSelect").val("auto");
                                    $("#barsColorsSelect").show();

                                    $("label[for='chartType']").show();
                                    $("#chartType").val("horizontal");
                                    $("#chartType").show();
                                    
                                    $("label[for='dataLabels']").parent().show();
                                    $("label[for='dataLabels']").show();
                                    $("#dataLabels").val("value");
                                    $("#dataLabels").show();
                                    
                                    $("label[for='dataLabelsRotation']").show();
                                    $("#dataLabelsRotation").val("horizontal");
                                    $("#dataLabelsRotation").show();
                                    
                                    $("#xAxisDataset").off();
                                    
                                    $("#xAxisDataset").prop('required', false);
                                    $("#lineWidth").prop('required', false);
                                    $("#alrLook").prop('required', false);

                                    //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                    var colorsTable, newRow, newCell = null;
                                    var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                    var colorsArray = new Array();

                                    function updateWidgetBarSeriesColors(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray[index] = newColor;
                                        $("#barsColors").val(JSON.stringify(colorsArray));
                                    }

                                    colorsTable = $("<table id ='colorsTable' class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                    for(var i in series.secondAxis.labels)
                                    {
                                        newRow = $('<tr></tr>');
                                        newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                        newRow.append(newCell);
                                        newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                        newRow.append(newCell);
                                        newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                        newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColors); 
                                        colorsArray.push(defaultColorsArray[i%10]);
                                        colorsTable.append(newRow);
                                    }

                                    $("#barsColors").val(JSON.stringify(colorsArray));

                                    $('#barsColorsTableContainer').append(colorsTable);

                                    $('#barsColorsSelect').change(function () 
                                    {
                                        if($('#barsColorsSelect').val() === "manual")
                                        {
                                            $('#barsColorsTableContainer').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainer').hide();
                                        }
                                    });

                                    $("label[for='alrThrFlag']").parent().show();
                                    $('#alrThrFlag').val("no");
                                    $('#alrThrFlag').show();
                                    $("label[for='alrThrFlag']").show();

                                    //Codice di creazione soglie
                                    //Popolamento del select per gli assi
                                    $('#alrAxisSel').empty();
                                    $('#alrAxisSel').append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");

                                    //Costruzione THRTables vuote
                                    buildEmptyThrTables();

                                    //Listener per settaggio/desettaggio soglie
                                    $('#alrThrFlag').change(alrThrFlagListener);

                                    //Listener per settaggio/desettaggio campi in base ad asse selezionato
                                    $('#alrAxisSel').change(alrAxisSelListener);

                                    //Listener per selezione campo
                                    $('#alrFieldSel').change(alrFieldSelListener);
                                    //Fine proprietà specifiche del widget
                                    break;
        
                                case "widgetBarSeries":
                                    var currentParams, i, k, currentFieldIndex, currentSeriesIndex = null;
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var seriesString = metricData.data[0].commit.author.series;
                                    var series = jQuery.parseJSON(seriesString);
                                    var thrTables1 = new Array();
                                    var thrTables2 = new Array();
                                    
                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());
                                    
                                    //Costruzione THRTables vuote
                                    buildEmptyThrTables();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Proprietà specifiche
                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Rows labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsFontSize" class="col-md-2 control-label">Rows labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSize" name="rowsLabelsFontSize" value="10" required> ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Rows labels font color
                                    newLabel = $('<label for="rowsLabelsFontColor" class="col-md-2 control-label">Rows labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColor" name="rowsLabelsFontColor" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsFontColorContainer').show();
                                    $('#rowsLabelsFontColor').show();
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Cols labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="colsLabelsFontSize" class="col-md-2 control-label">Cols labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="colsLabelsFontSize" name="colsLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Cols labels font color
                                    newLabel = $('<label for="colsLabelsFontColor" class="col-md-2 control-label">Cols labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColor" name="colsLabelsFontColor" required><span class="input-group-addon"><i id="widgetColsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsFontColorContainer').show();
                                    $('#colsLabelsFontColor').show();
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Data labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabelsFontSize" class="col-md-2 control-label">Data labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="dataLabelsFontSize" name="dataLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Data labels font color
                                    newLabel = $('<label for="dataLabelsFontColor" class="col-md-2 control-label">Data labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="dataLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColor" name="dataLabelsFontColor" required><span class="input-group-addon"><i id="widgetDataLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#dataLabelsFontColorContainer').show();
                                    $('#dataLabelsFontColor').show();
                                    $("#widgetDataLabelsFontColor").css('display', 'block');
                                    $("#widgetDataLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Legend font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="legendFontSize" class="col-md-2 control-label">Legend font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="legendFontSize" name="legendFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Legend font color
                                    newLabel = $('<label for="legendFontColor" class="col-md-2 control-label">Legend font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="legendFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColor" name="legendFontColor" required><span class="input-group-addon"><i id="widgetLegendFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#legendFontColorContainer').show();
                                    $('#legendFontColor').show();
                                    $("#widgetLegendFontColor").css('display', 'block');
                                    $("#widgetLegendFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Bars colors
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="barsColorsSelect" class="col-md-2 control-label">Bars colors</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="barsColorsSelect" name="barsColorsSelect"></select>');
                                    newSelect.append('<option value="auto">Automatic</option>');
                                    newSelect.append('<option value="manual">Manual</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Chart type
                                    newLabel = $('<label for="chartType" class="col-md-2 control-label">Chart type</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="chartType" name="chartType"></select>');
                                    newSelect.append('<option value="horizontal">Horizontal bars</option>');
                                    newSelect.append('<option value="horizontalStacked">Horizontal stacked bars</option>');
                                    newSelect.append('<option value="vertical">Vertical bars</option>');
                                    newSelect.append('<option value="verticalStacked">Vertical stacked bars</option>');
                                    newSelect.val("horizontal");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Nuova riga
                                    //Data labels
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabels" class="col-md-2 control-label">Data labels</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="dataLabels" name="dataLabels">');
                                    newSelect.append('<option value="no">No data labels</option>');
                                    newSelect.append('<option value="value">Value only</option>');
                                    newSelect.append('<option value="full">Field name and value</option>');
                                    newSelect.val("value");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Data labels rotation
                                    newLabel = $('<label for="dataLabelsRotation" class="col-md-2 control-label">Data labels rotation</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="dataLabelsRotation" name="dataLabelsRotation">');
                                    newSelect.append('<option value="horizontal">Horizontal</option>');
                                    newSelect.append('<option value="verticalAsc">Vertical ascending</option>');
                                    newSelect.append('<option value="verticalDesc">Vertical descending</option>');
                                    newSelect.val("horizontal");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Contenitore per tabella dei colori
                                    barsColorsTableContainer = $('<div id="barsColorsTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(barsColorsTableContainer);
                                    barsColorsTableContainer.hide();

                                    //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                    var colorsTable, newRow, newCell = null;
                                    var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                    var colorsArray = new Array();

                                    function updateWidgetBarSeriesColors(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray[index] = newColor;
                                        $("#barsColors").val(JSON.stringify(colorsArray));
                                    }

                                    colorsTable = $("<table id ='colorsTable' class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                    for(var i in series.secondAxis.labels)
                                    {
                                        newRow = $('<tr></tr>');
                                        newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                        newRow.append(newCell);
                                        newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                        newRow.append(newCell);
                                        newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                        newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColors); 
                                        colorsArray.push(defaultColorsArray[i%10]);
                                        colorsTable.append(newRow);
                                    }

                                    $("#barsColors").val(JSON.stringify(colorsArray));
                                    $('#barsColorsTableContainer').append(colorsTable);

                                    $('#barsColorsSelect').change(function () 
                                    {
                                        if($('#barsColorsSelect').val() === "manual")
                                        {
                                            $('#barsColorsTableContainer').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainer').hide();
                                        }
                                    });


                                    //Codice di creazione soglie
                                    //Nuova riga
                                    //Set thresholds
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val('no');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                    $('#alrThrSel').change(alrThrSelListener);
                                    
                                    //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                    newLabel = $('<label for="alrAxisSel" class="col-md-2 control-label">Thresholds target set</label>');
                                    newSelect = $('<select class="form-control" id="alrAxisSel" name="alrAxisSel"></select>');
                                    newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                    newSelect.val(-1);
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Nuova riga
                                    //Threshold field select
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                    newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Contenitore per tabella delle soglie
                                    addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                    addWidgetRangeTableContainer.hide();
                                    break;

                                case "widgetTable":
                                    var currentParams, i, k, currentFieldIndex, currentSeriesIndex = null;
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var seriesString = metricData.data[0].commit.author.series;
                                    var series = jQuery.parseJSON(seriesString);
                                    var thrTables1 = new Array();
                                    var thrTables2 = new Array();
                                    
                                    //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                    setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());
                                    
                                    //Costruzione THRTables vuote
                                    buildEmptyThrTables();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("10");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    //Proprietà specifiche del widget
                                    //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Visualizzazione campi specifici per questo widget
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Select show first cell
                                    newFormRow = $('<div class="row"></div>');
                                    newLabel = $('<label for="showTableFirstCell" class="col-md-2 control-label">Show first cell</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="showTableFirstCell" name="showTableFirstCell" required></select>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Nuova riga
                                    //First cell font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="tableFirstCellFontSize" class="col-md-2 control-label">First cell font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="tableFirstCellFontSize" name="tableFirstCellFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //First cell font color
                                    newLabel = $('<label for="tableFirstCellFontColor" class="col-md-2 control-label">First cell font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="tableFirstCellFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableFirstCellFontColor" name="tableFirstCellFontColor" required><span class="input-group-addon"><i id="widgetFirstCellFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#tableFirstCellFontColorContainer').show();
                                    $('#tableFirstCellFontColor').show();
                                    $("#widgetFirstCellFontColor").css('display', 'block');
                                    $("#widgetFirstCellFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Rows labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsFontSize" class="col-md-2 control-label">Rows labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSize" name="rowsLabelsFontSize" value="10" required> ');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Rows labels font color
                                    newLabel = $('<label for="rowsLabelsFontColor" class="col-md-2 control-label">Rows labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColor" name="rowsLabelsFontColor" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsFontColorContainer').show();
                                    $('#rowsLabelsFontColor').show();
                                    $("#widgetRowsLabelsFontColor").css('display', 'block');
                                    $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Cols labels font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="colsLabelsFontSize" class="col-md-2 control-label">Cols labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="colsLabelsFontSize" name="colsLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Cols labels font color
                                    newLabel = $('<label for="colsLabelsFontColor" class="col-md-2 control-label">Cols labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColor" name="colsLabelsFontColor" required><span class="input-group-addon"><i id="widgetColsLabelsFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsFontColorContainer').show();
                                    $('#colsLabelsFontColor').show();
                                    $("#widgetColsLabelsFontColor").css('display', 'block');
                                    $("#widgetColsLabelsFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Rows labels background color
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="rowsLabelsBckColor" class="col-md-2 control-label">Rows labels background color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="rowsLabelsBckColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsBckColor" name="rowsLabelsBckColor" required><span class="input-group-addon"><i id="widgetRowsLabelsBckColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#rowsLabelsBckColorContainer').show();
                                    $('#rowsLabelsBckColor').show();
                                    $("#widgetRowsLabelsBckColor").css('display', 'block');
                                    $("#widgetRowsLabelsBckColor").parent().parent().parent().colorpicker({color: "#FFFFFF"});
                                    
                                    
                                    //Cols labels background color
                                    newLabel = $('<label for="colsLabelsBckColor" class="col-md-2 control-label">Cols labels background color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="colsLabelsBckColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsBckColor" name="colsLabelsBckColor" required><span class="input-group-addon"><i id="widgetColsLabelsBckColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#colsLabelsBckColorContainer').show();
                                    $('#colsLabelsBckColor').show();
                                    $("#widgetColsLabelsBckColor").css('display', 'block');
                                    $("#widgetColsLabelsBckColor").parent().parent().parent().colorpicker({color: "#FFFFFF"});
                                    
                                    //Nuova riga
                                    //Table borders
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="tableBorders" class="col-md-2 control-label">Table borders</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="tableBorders" name="tableBorders"></select>');
                                    newSelect.append('<option value="no">No borders</option>');
                                    newSelect.append('<option value="horizontal">Horizontal borders only</option>');
                                    newSelect.append('<option value="all">All borders</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Table borders color
                                    newLabel = $('<label for="tableBordersColor" class="col-md-2 control-label">Table borders color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="tableBordersColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableBordersColor" name="tableBordersColor" required><span class="input-group-addon"><i id="widgetTableBordersColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#tableBordersColorContainer').show();
                                    $('#tableBordersColor').show();
                                    $("#widgetTableBordersColor").css('display', 'block');
                                    $("#widgetTableBordersColor").parent().parent().parent().colorpicker({color: "#EEEEEE"});
                                    
                                    //Nuova riga
                                    //Set thresholds
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val('no');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                    $('#alrThrSel').change(alrThrSelListener);
                                    
                                    //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                    newLabel = $('<label for="alrAxisSel" class="col-md-2 control-label">Thresholds target set</label>');
                                    newSelect = $('<select class="form-control" id="alrAxisSel" name="alrAxisSel"></select>');
                                    newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                    newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                    newSelect.val(-1);
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Nuova riga
                                    //Threshold field select
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                    newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.hide();
                                    newInnerDiv.hide();
                                    newSelect.hide();
                                    
                                    //Contenitore per tabella delle soglie
                                    addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                    $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                    addWidgetRangeTableContainer.hide(); 

                                    //Funzione che crea e mostra i ckeditors per i campi info del widget
                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                    
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetProcess":
                                    $("label[for='inputComuneWidget']").css("display", "");
                                    $("#bckColorLabel").html("Background color");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputUrlWidget').attr('disabled', false);
                                    $('#inputUrlWidget').prop('required', true);
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $("#schedulerRow").show();
                                    $("label[for='inputSchedulerWidget']").show();
                                    $('#inputSchedulerWidgetDiv').show();
                                    $('#inputSchedulerWidgetGroupDiv').show();
                                    $('#inputSchedulerWidget').show();
                                    $('#inputSchedulerWidget').prop('selectedIndex', -1);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFontSize').val("");
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();
                                    

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

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
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan = null;
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputUrlWidget').attr("disabled", "true");
                                    $('#inputUrlWidget').prop("required", "false");
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $('#inputTitleWidget').val('');
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputTitleWidget').attr('disabled', true);
                                    $('#inputTitleWidget').prop('required', false);
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
                                    $('#inputFontSize').prop('required', false);  
                                    $("#widgetFontColor").parent().parent().colorpicker({color: "#eeeeee"});
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#inputFontColor').prop('required', false);
                                    $('#inputHeaderFontColorWidget').attr('disabled', true);
                                    $('#inputHeaderFontColorWidget').prop('required', false);
                                    $('#inputHeaderFontColorWidget').val("");
                                    $('#widgetHeaderFontColor').css("background-color", "#eeeeee");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    
                                    //Nuova riga
                                    //Default tab
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="inputDefaultTab" class="col-md-2 control-label">Default tab</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputDefaultTab" name="inputDefaultTab"></select>');
                                    newSelect.append('<option value="0">General</option>');
                                    newSelect.append('<option value="1">Meteo</option>');
                                    newSelect.append('<option value="-1">None (automatic switch)</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Meteo tab font size
                                    newLabel = $('<label for="meteoTabFontSize" class="col-md-2 control-label">Meteo tab font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="meteoTabFontSize" name="meteoTabFontSize"></input>');
                                    newInput.val("10");
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Nuova riga
                                    //General tab font size
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="genTabFontSize" class="col-md-2 control-label">General tab font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="genTabFontSize" name="genTabFontSize"></input>');
                                    newInput.val("12");
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //General tab font color
                                    newLabel = $('<label for="genTabFontColor" class="col-md-2 control-label">General tab font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="genTabFontColorContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="genTabFontColor" name="genTabFontColor" required><span class="input-group-addon"><i id="widgetGenTabFontColor"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    $('#genTabFontColorContainer').show();
                                    $('#genTabFontColor').show();
                                    $("#genTabFontColor").css('display', 'block');
                                    $("#widgetGenTabFontColor").parent().parent().colorpicker({color: "#000000"});
                                    $("#genTabFontColor").prop("required", true);
                                    $("#genTabFontColor").attr("disabled", false);
                                    
                                    $('#inputComuneWidget').attr('disabled', true);
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetButton":
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Button text");
                                    $("#bckColorLabel").html("Button color");
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').val(14);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
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
                                    $('#inputHeaderFontColorWidget').attr('disabled', true);
                                    $('#inputHeaderFontColorWidget').prop('required', false);
                                    $('#inputHeaderFontColorWidget').val("");
                                    $('#widgetHeaderFontColor').css("background-color", "#eeeeee");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();
                                    $('#inputComuneWidget').attr('disabled', true);

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select name="addWidgetGeolocationWidgets" class="form-control" id="addWidgetGeolocationWidgets" multiple></select>');

                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    var targetsJson = [];

                                    $("li.gs_w").each(function(){
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          widgetsNumber++;
                                       }
                                    });

                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       $('#addWidgetGeolocationWidgets').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetsJson = [];
                                          }
                                          else
                                          {
                                             targetsJson = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetsJson));
                                       });
                                    }
                                    break;

                                case "widgetSingleContent":
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", true);
                                    $('#inputUdmWidget').attr("disabled", false);
                                    $('#inputUdmPosition').prop("required", true);
                                    $('#inputUdmPosition').attr("disabled", false);
                                    $('#inputUdmWidget').val("");
                                    $("#inputUdmPosition").val("next");
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetGenericContent":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetBarContent":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("16");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetColunmContent":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("20");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetGaugeChart":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontSize').prop('required', false);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetPieChart":
                                    var metricId = $('#select-metric').val();
                                    var metricData = getMetricData(metricId);
                                    var series, seriesString, table1Shown, table2Shown, valuePerc, newHiddenColors, colorsTable1, colorsTable2, colorsArray1, colorsArray2, descriptions, defaultColorsArray, newColorsTableContainer, newColorsTable1Container, newColorsTable2Container, newRow, newCell, valuesNum, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer, thrSimpleTables, thrTables1, thrTables2, currentParams, i, k, currentFieldIndex, currentSeriesIndex = null;
                                    
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontSize').prop('required', false);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#EEEEEE");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        showInfoWCkeditors($('#select-widget').val(), editorsArray, null);
                                    }
                                    else
                                    {
                                        seriesString = metricData.data[0].commit.author.series;
                                        series = jQuery.parseJSON(seriesString);
                                        showInfoWCkeditors($('#select-widget').val(), editorsArray, series);
                                    }
                                
                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Legend font size e font color
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="legendFontSize" class="col-md-2 control-label">Legend font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="legendFontSize" name="legendFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    newLabel = $('<label for="legendFontColor" class="col-md-2 control-label">Legend font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<div id="legendFontColorPickerContainer" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColorPicker" name="legendFontColorPicker" required><span class="input-group-addon"><i id="widgetLegendFontColorPicker"></i></span></div>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#widgetLegendFontColorPicker").css('display', 'block');
                                    $("#widgetLegendFontColorPicker").parent().parent().parent().colorpicker({color: "#000000"});
                                    
                                    //Nuova riga
                                    //Datalabels distance (se Percentuale) oppure Datalabels (se Series)
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        newLabel = $('<label for="dataLabelsDistance" class="col-md-2 control-label">Data labels distance</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsDistance" name="dataLabelsDistance" value="15" required>');
                                        newInnerDiv.append(newInput);
                                    }
                                    else if(metricType === 'Series')
                                    {
                                        newLabel = $('<label for="dataLabels" class="col-md-2 control-label">Data labels for <i>' + series.firstAxis.desc + '</i></label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="dataLabels" name="dataLabels"></select>');
                                        newSelect.append('<option value="no">No data labels</option>');
                                        newSelect.append('<option value="value">Value only</option>');
                                        newSelect.append('<option value="full">Field name and value</option>');
                                        newSelect.val("value");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                    }
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Data labels font size
                                    newLabel = $('<label for="dataLabelsFontSize" class="col-md-2 control-label">Data labels font size</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="dataLabelsFontSize" name="dataLabelsFontSize" value="10" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Nuova riga
                                    //Data labels distances per widget series
                                    if(metricType === 'Series')
                                    {
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        //Data labels distance per anello più interno
                                        newLabel = $('<label for="dataLabelsDistance1" class="col-md-2 control-label">Data labels distance for <i>' + series.secondAxis.desc + '</i></label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsDistance1" name="dataLabelsDistance1" value="-30" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Data labels distance per anello più esterno
                                        newLabel = $('<label for="dataLabelsDistance2" class="col-md-2 control-label">Data labels distance for <i>' + series.firstAxis.desc + '</i></label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsDistance2" name="dataLabelsDistance2" value="-30" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                    }
                                    
                                    //Nuova riga
                                    //Data labels font color
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="dataLabelsFontColor" class="col-md-2 control-label">Data labels font color</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInputGroup = $('<div class="input-group color-choice">');
                                    newInput = $('<input type="text" class="form-control" id="dataLabelsFontColor" name="dataLabelsFontColor" value="#000000" required>');
                                    newSpan = $('<span class="input-group-addon"><i id="widgetDataLabelsFontColor"></i></span>');
                                    newInputGroup.append(newInput);
                                    newInputGroup.append(newSpan);
                                    newInnerDiv.append(newInputGroup);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newInnerDiv.colorpicker({color: '#000000'});
                                    
                                    //Inner radius 1
                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        newLabel = $('<label for="innerRadius1" class="col-md-2 control-label">Inner radius (%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="innerRadius1" name="innerRadius1" value="0" required>');
                                    }
                                    else if(metricType === 'Series')
                                    {
                                        newLabel = $('<label for="innerRadius1" class="col-md-2 control-label">Inner radius for <i>' + series.secondAxis.desc + '</i> (%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="innerRadius1" name="innerRadius1" value="15" required>');
                                    }
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Nuova riga
                                    //Questi raggi sono solo per la versione series
                                    if(metricType === 'Series')
                                    {
                                       //Outer radius 1
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        newLabel = $('<label for="outerRadius1" class="col-md-2 control-label">Outer radius for <i>' + series.secondAxis.desc + '</i> (%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="outerRadius1" name="outerRadius1" value="50" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);

                                        //Inner radius 2
                                        newLabel = $('<label for="innerRadius2" class="col-md-2 control-label">Inner radius for <i>' + series.firstAxis.desc + '</i> (%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="innerRadius2" name="innerRadius2" value="50" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv); 
                                    }
                                    
                                    //Nuova riga
                                    //Start angle
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="startAngle" class="col-md-2 control-label">Start angle (°)</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="startAngle" name="startAngle" value="0" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //End angle
                                    newLabel = $('<label for="endAngle" class="col-md-2 control-label">End angle (°)</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="endAngle" name="endAngle" value="360" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Nuova riga
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    //Center Y
                                    newLabel = $('<label for="centerY" class="col-md-2 control-label">Diagram center Y(%)</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="centerY" name="centerY" value="50" required>');
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Select per colori automatici/manuali
                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        newLabel = $('<label for="colorsSelect1" class="col-md-2 control-label">Slices colors</label>');
                                    }
                                    else if(metricType === 'Series')
                                    {
                                        newLabel = $('<label for="colorsSelect1" class="col-md-2 control-label">Slices colors for <i>' + series.secondAxis.desc + '</i></label>');
                                    }
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="colorsSelect1" name="colorsSelect1"></select>');
                                    newSelect.append('<option value="auto">Automatic</option>');
                                    newSelect.append('<option value="manual">Manual</option>');
                                    newSelect.val("auto");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    
                                    //Campi hidden per i colori
                                    newHiddenColors = $('<input type="hidden" id="colors1" name="colors1">');
                                    $("#specificWidgetPropertiesDiv").append(newHiddenColors);
                                    newHiddenColors = $('<input type="hidden" id="colors2" name="colors2">');
                                    $("#specificWidgetPropertiesDiv").append(newHiddenColors);

                                    //Div contenitore per la tabella dei colori
                                    newColorsTableContainer = $('<div id="colorsTableContainer" class="row rowCenterContent" style="width: 100%; margin-left: 0px"></div>');
                                    newColorsTable1Container = $('<div id="colorsTable1Container" style="width: 100%; float: left"></div>');
                                    newColorsTable2Container = $('<div id="colorsTable2Container" style="width: 100%; float: left"></div>');
                                    newColorsTableContainer.append(newColorsTable1Container);
                                    
                                    function updateWidgetPieChartColors1(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray1[index] = newColor;
                                        $("#colors1").val(JSON.stringify(colorsArray1));
                                    }
                                    
                                    function updateWidgetPieChartColors2(e, params)
                                    {
                                        var newColor = $(this).colorpicker('getValue');
                                        var index = parseInt($(this).parents('tr').index() - 1);
                                        colorsArray2[index] = newColor;
                                        $("#colors2").val(JSON.stringify(colorsArray2));
                                    }
                                    
                                    defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                    colorsArray1 = [];
                                    colorsArray2 = [];
                                    
                                    //Aggiunta al form dei campi per parametri specifici
                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        //Form per dati tradizionali percentuali
                                        colorsTable1 = $("<table id='colorsTable1' class='table table-bordered table-condensed thrRangeTable' style='float:left'><tr><td>Field</td><td>Color</td></tr></table>");
                                        
                                        valuePerc = [];
                                        descriptions = [];
                                        valuePerc[0] = metricData.data[0].commit.author.value_perc1;
                                        valuePerc[1] = metricData.data[0].commit.author.value_perc2;
                                        valuePerc[2] = metricData.data[0].commit.author.value_perc3;
                                        
                                        //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                        if((valuePerc[0] !== null) && (valuePerc[1] !== null) && (valuePerc[2] !== null))
                                        {
                                            valuesNum = 3;
                                            descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                            descriptions[1] = metricData.data[0].commit.author.field2Desc;
                                            descriptions[2] = metricData.data[0].commit.author.field3Desc;
                                        }
                                        else if((valuePerc[0] !== null) && (valuePerc[1] !== null) && (valuePerc[2] === null))
                                        {
                                            valuesNum = 2;
                                            descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                            descriptions[1] = metricData.data[0].commit.author.field2Desc;
                                        }
                                        else if((valuePerc[0] !== null) && (valuePerc[1] === null) && (valuePerc[2] === null))
                                        {
                                            valuesNum = 1;
                                            descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                        }
                                        
                                        //Costruzione tabella dei colori iniziale
                                        for(var i = 0; i < valuesNum; i++)
                                        {
                                            colorsArray1[i] = defaultColorsArray[i];
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + descriptions[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            newRow.find('div.colorPicker').colorpicker({color: colorsArray1[i]});
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1);
                                            colorsTable1.append(newRow);
                                        }
                                        
                                        if(valuesNum === 1)
                                        {
                                            colorsArray1[1] = defaultColorsArray[1];
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>Complementary</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            newRow.find('div.colorPicker').colorpicker({color: colorsArray1[1]});
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1);
                                            colorsTable1.append(newRow);
                                        }
                                        
                                        $("#colors1").val(JSON.stringify(colorsArray1));    
                                        newColorsTable1Container.append(colorsTable1);
                                        colorsTable1.hide();
                                        newColorsTable1Container.hide();
                                        newColorsTableContainer.hide();
                                    }
                                    else if(metricType === 'Series')
                                    {
                                        //Nuova riga
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        newLabel = $('<label for="colorsSelect2" class="col-md-2 control-label">Slices colors for <i>' + series.firstAxis.desc + '</i></label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="colorsSelect2" name="colorsSelect2"></select>');
                                        newSelect.append('<option value="auto">Automatic</option>');
                                        newSelect.append('<option value="manual">Manual</option>');
                                        newSelect.val("auto");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newColorsTableContainer.append(newColorsTable2Container);
                                        
                                        //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                        colorsTable1 = $("<table id='colorsTable1' class='table table-bordered table-condensed thrRangeTable' style='width: 100%;'><tr><td>Fields of set <b>" + series.secondAxis.desc + "</b></td><td>Color</td></tr></table>");
                                        for(var i = 0; i < series.secondAxis.labels.length; i++)
                                        {
                                            colorsArray1[i] = defaultColorsArray[i];
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            newRow.find('div.colorPicker').colorpicker({color: colorsArray1[i]});
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1);
                                            colorsTable1.append(newRow);
                                        }
                                        
                                        //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                        colorsTable2 = $("<table id='colorsTable2' class='table table-bordered table-condensed thrRangeTable' style='width: 100%;'><tr><td>Fields of set <b>" + series.firstAxis.desc + "</b></td><td>Color</td></tr></table>");
                                        for(var i = 0; i < series.firstAxis.labels.length; i++)
                                        {
                                            colorsArray2[i] = defaultColorsArray[i];
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + series.firstAxis.labels[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            newRow.find('div.colorPicker').colorpicker({color: colorsArray2[i]});
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors2);
                                            colorsTable2.append(newRow);
                                        }
                                        
                                        $("#colors1").val(JSON.stringify(colorsArray1));    
                                        newColorsTable1Container.append(colorsTable1);
                                        $("#colors2").val(JSON.stringify(colorsArray2));    
                                        newColorsTable2Container.append(colorsTable2);
                                        colorsTable1.hide();
                                        colorsTable2.hide();
                                        newColorsTable1Container.hide();
                                        newColorsTable2Container.hide();
                                        newColorsTableContainer.hide();
                                    }
                                    
                                    $("#specificWidgetPropertiesDiv").append(newColorsTableContainer);
                                    
                                    table1Shown = false;
                                    table2Shown = false;

                                    $('#colorsSelect1').change(function () 
                                    {
                                        if($('#colorsSelect1').val() === "manual")
                                        {
                                            $('#colorsTableContainer').css("display", "block");
                                            $('#colorsTable1Container').css("display", "block");
                                            colorsTable1.css("display", "");//CRASH
                                            table1Shown = true;
                                        }
                                        else
                                        {
                                            colorsTable1.css("display", "none");
                                            $('#colorsTable1Container').css("display", "none");
                                            table1Shown = false;
                                            if(table2Shown === false)
                                            {
                                                $('#colorsTableContainer').css("display", "none");
                                            }
                                        }
                                    });
                                    
                                    if(metricType === 'Series')
                                    {
                                        $('#colorsSelect2').change(function () 
                                        {
                                            if($('#colorsSelect2').val() === "manual")
                                            {
                                                $('#colorsTableContainer').css("display", "block");
                                                $('#colorsTable2Container').css("display", "block");
                                                colorsTable2.css("display", "");
                                                table2Shown = true;
                                            }
                                            else
                                            {
                                                colorsTable2.css("display", "none");
                                                $('#colorsTable2Container').css("display", "none");
                                                table2Shown = false;
                                                if(table1Shown === false)
                                                {
                                                    $('#colorsTableContainer').css("display", "none");
                                                }
                                            }
                                        });
                                    }
                                    
                                    //Codice costruzione soglie
                                    if(metricType.indexOf('Percentuale') >= 0)
                                    {
                                        //Tabelle editabili delle soglie
                                        thrSimpleTables = new Array();
        
                                        setSimpleGlobals(null, thrSimpleTables, valuePerc, descriptions);
                                        
                                        //Costruzione THRTables vuote
                                        buildEmptySimpleThrTables();
        
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newSelect.val("no");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSel').change(alrThrSelListenerSimple);
                                        
                                        //Threshold field select
                                        newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                        $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                        addWidgetRangeTableContainer.hide();
                                    }
                                    else if(metricType === 'Series')
                                    {
                                        //Tabelle editabilidelle soglie
                                        thrTables1 = new Array();
                                        thrTables2 = new Array();

                                        //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                        setGlobals(null, thrTables1, thrTables2, series, $('#select-widget').val());

                                        //Costruzione THRTables vuote
                                        buildEmptyThrTables();
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        newLabel = $('<label for="alrThrSel" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSel" name="alrThrSel" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newSelect.val("no");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSel').change(alrThrSelListener);

                                        //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                        newLabel = $('<label for="alrAxisSel" class="col-md-2 control-label">Thresholds target set</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrAxisSel" name="alrAxisSel"></select>');
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        newSelect.val(-1);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();

                                        //Nuova riga
                                        //Threshold field select
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificWidgetPropertiesDiv").append(newFormRow);
                                        newLabel = $('<label for="alrFieldSel" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSel" name="alrFieldSel">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainer = $('<div id="addWidgetRangeTableContainer" class="row rowCenterContent"></div>');
                                        $("#specificWidgetPropertiesDiv").append(addWidgetRangeTableContainer);
                                        addWidgetRangeTableContainer.hide();
                                    }
                                    break;

                                case "widgetSce":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontSize').prop('required', false);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetSmartDS":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("16");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontColor').val("#000000");
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetTimeTrend":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("11");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontColor').val("#666666");
                                    $('#widgetFontColor').css("background-color", "#666666");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').attr('disabled', false);
                                    $('#select-IntTemp-Widget').prop('required', true);
                                    $('#select-IntTemp-Widget').val("4 Ore");
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetTimeTrendCompare":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
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
                                    $('#select-IntTemp-Widget').val("4 Ore");
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetEvents":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("12");
                                    $('#inputFontSize').attr('disabled', false);
                                    $('#inputFontSize').prop('required', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");

                                    //Visualizzazione campi specifici per questo widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                    //Nuova riga
                                    //Target widgets geolocation
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    
                                    newLabel = $('<label for="addWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="addWidgetGeolocationWidgets" name="addWidgetGeolocationWidgets"></select>');
                                    
                                    var widgetId, widgetTitle = null;
                                    var widgetsNumber = 0;
                                    //JSON degli eventi da mostrare su ogni widget target di questo widget events: privo di eventi all'inizio
                                    var targetEventsJson = {};
                                    
                                    $("li.gs_w").each(function(){
                                       //if($(this).attr("id").includes("EventsGeoLocation"))
                                       if($(this).attr("id").includes("ExternalContent"))
                                       {
                                          widgetId = $(this).attr("id");
                                          widgetTitle = $(this).find("div.titleDiv").html();
                                          newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                          targetEventsJson[widgetId] = new Array(); 
                                          widgetsNumber++;
                                       }
                                    });
                                    
                                    $("#parameters").val(JSON.stringify(targetEventsJson));
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newInnerDiv.append(newSelect);
                                    }
                                    else
                                    {
                                       newInnerDiv.append("None");
                                    }
                                    
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    
                                    if(widgetsNumber > 0)
                                    {
                                       newSelect.show();
                                       newSelect.val(-1);
                                       newLabel = $('<label for="addWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       var eventTypeSelect = $('<select name="addWidgetEventTypes" class="form-control" id="addWidgetEventTypes" multiple></select>');
                                       eventTypeSelect.append('<option value="Altri eventi">Altri eventi</option>');
                                       eventTypeSelect.append('<option value="Aperture straordinarie, visite guidate">Aperture straordinarie, visite guidate</option>');
                                       eventTypeSelect.append('<option value="Estate Fiorentina">Estate Fiorentina</option>');
                                       eventTypeSelect.append('<option value="Fiere, mercati">Fiere, mercati</option>');
                                       eventTypeSelect.append('<option value="Film festival">Film festival</option>');
                                       eventTypeSelect.append('<option value="Mostre">Mostre</option>');
                                       eventTypeSelect.append('<option value="Musica classica, opera e balletto">Musica classica, opera e balletto</option>');
                                       eventTypeSelect.append('<option value="Musica rock, jazz, pop, contemporanea">Musica rock, jazz, pop, contemporanea</option>');
                                       eventTypeSelect.append('<option value="News">News</option>');
                                       eventTypeSelect.append('<option value="Readings, Conferenze, Convegni">Readings, Conferenze, Convegni</option>');
                                       eventTypeSelect.append('<option value="Readings, incontri letterari, conferenze">Readings, incontri letterari, conferenze</option>');
                                       eventTypeSelect.append('<option value="Sport">Sport</option>');
                                       eventTypeSelect.append('<option value="Teatro">Teatro</option>');
                                       eventTypeSelect.append('<option value="Tradizioni popolari">Tradizioni popolari</option>');
                                       eventTypeSelect.append('<option value="Walking">Walking</option>');
                                       eventTypeSelect.val(-1);
                                       newFormRow.append(newLabel);
                                       newInnerDiv.append(eventTypeSelect);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.hide();
                                       newInnerDiv.hide();
                                       
                                       $('#addWidgetEventTypes').selectpicker({
                                          width: 110
                                       });
                                       
                                       $('#addWidgetEventTypes').on('changed.bs.select', function (e) 
                                       {
                                          if($(this).val() === null)
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = [];
                                          }
                                          else
                                          {
                                             targetEventsJson[$("#addWidgetGeolocationWidgets").val()] = $(this).val();
                                          }
                                          $("#parameters").val(JSON.stringify(targetEventsJson));
                                       });
                                       
                                       $("#addWidgetGeolocationWidgets").change(function(){
                                          newLabel.show();
                                          newInnerDiv.show();
                                          $('#addWidgetEventTypes').selectpicker('val', targetEventsJson[$("#addWidgetGeolocationWidgets").val()]);
                                       });
                                    }
                                    break;

                                case "widgetTrendMentions":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    
                                    //Nuova riga
                                    //Default tab
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="inputDefaultTab" class="col-md-2 control-label">Default tab</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputDefaultTab" name="inputDefaultTab"></select>');
                                    newSelect.append('<option value="0">Trends</option>');
                                    newSelect.append('<option value="1">Quotes</option>');
                                    newSelect.append('<option value="-1">None (automatic switch)</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetPrevMeteo":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $("#inputComuneRow").show();
                                    $('#inputComuneWidget').val("");
                                    $('#inputComuneWidget').attr("placeholder", "Type city name's first letters");
                                    $("label[for='inputComuneWidget']").show();
                                    $("label[for='inputComuneWidget']").text("City (autosuggestion)");
                                    $('#inputComuneWidget').show();
                                    $('#inputComuneWidget').attr('disabled', false);
                                    $('#inputComuneWidget').attr('autocomplete', 'on');
                                    $('#inputComuneWidget').prop('required', true);
                                    
                                    jQuery.ui.autocomplete.prototype._resizeMenu = function () 
                                    {
                                        var ul = this.menu.element;
                                        ul.outerWidth(this.element.outerWidth());
                                    };
                                    
                                    $('#inputComuneWidget').autocomplete({
                                        source: comuniLammaArray
                                    });
                                    
                                    $('#inputUrlWidget').attr('disabled', false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("#000000");
                                    $('#inputFontColor').attr('disabled', false);
                                    $('#inputFontColor').prop('required', true);
                                    $('#widgetFontColor').css("background-color", "#000000");
                                    $("#widgetFontColor").parent().parent().parent().colorpicker({color: "#000000"});
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();
                                    

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetServiceMap":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    /*$("#inputComuneRow").css("display", "");
                                    $('#inputComuneWidget').val("");
                                    $("label[for='inputComuneWidget']").css("display", "");
                                    $("label[for='inputComuneWidget']").text("Context");
                                    $('#inputComuneWidget').css("display", "");
                                    $('#inputComuneWidget').attr('disabled', false);*/
                                    $('#inputUrlWidget').attr('disabled', true);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', true);
                                    $('#inputFreqWidget').val("");
                                    $('#inputFreqWidget').prop('required', false);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetSpeedometer":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    /*$("#inputComuneRow").css("display", "");
                                    $('#inputComuneWidget').val("");
                                    $("label[for='inputComuneWidget']").text("Context");
                                    $("label[for='inputComuneWidget']").css("display", "");
                                    $('#inputComuneWidget').css("display", "");
                                    $('#inputComuneWidget').attr('disabled', true);*/
                                    $('#inputUrlWidget').attr('disabled', false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputZoomControlsColor').attr('disabled', true);
                                    $('#inputZoomControlsColor').prop('required', false);
                                    $('#widgetZoomControlsColor').css("background-color", "#EEEEEE");
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetStateRideAtaf":
                                    $('#inputTitleWidget').val('');
                                    /*$("#inputComuneRow").css("display", "");
                                    $('#inputComuneWidget').val("");
                                    $("label[for='inputComuneWidget']").css("display", "");
                                    $("label[for='inputComuneWidget']").text("Context");
                                    $('#inputComuneWidget').css("display", "");
                                    $('#inputComuneWidget').attr('disabled', true);*/
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').attr('disabled', false);
                                    $('#inputUrlWidget').prop('required', false);
                                    $("#titleLabel").html("Title");
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', false);
                                    $('#inputFreqWidget').val(60);
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    //Nuova riga
                                    //Default tab
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="inputDefaultTab" class="col-md-2 control-label">Default tab</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputDefaultTab" name="inputDefaultTab"></select>');
                                    newSelect.append('<option value="0">Stato</option>');
                                    newSelect.append('<option value="1">Linee monitorate</option>');
                                    newSelect.append('<option value="2">Dati</option>');
                                    newSelect.append('<option value="-1">None (automatic switch)</option>');
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;

                                case "widgetExternalContent":
                                    $('#inputTitleWidget').val('');
                                    $('#inputUrlWidget').val('');
                                    $('#inputUrlWidget').prop("required", true);
                                    $('#inputUrlWidget').attr("disabled", false);
                                    $("#titleLabel").html("Title"); 
                                    $("#bckColorLabel").html("Background color");
                                    $('#inputColorWidget').attr('disabled', false);
                                    $('#inputColorWidget').attr('required', true);
                                    $('#inputColorWidget').val("#eeeeee");
                                    $('#color_widget').css("background-color", "#eeeeee");
                                    $('#inputFontSize').val("");
                                    $('#inputFontSize').attr('disabled', true);
                                    $('#inputFontColor').val("");
                                    $('#inputFontColor').attr('disabled', true);
                                    $('#widgetFontColor').css("background-color", "#eeeeee");
                                    $('#link_help_modal-add-widget').css("display", "");
                                    $('#inputFrameColorWidget').attr('disabled', false);
                                    $('#inputFrameColorWidget').val('#eeeeee');
                                    $('#inputFrameColorWidget').prop('required', false);
                                    $('#select-IntTemp-Widget').val(-1);
                                    $('#select-IntTemp-Widget').attr('disabled', true);
                                    $('#select-IntTemp-Widget').prop('required', false);
                                    $('#inputFreqWidget').attr('disabled', true);
                                    $('#inputFreqWidget').val("");
                                    $('#inputFreqWidget').prop('required', false);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();
                                    
                                    //Nuova riga
                                    //Zoom controls visibility
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="inputControlsVisibility" class="col-md-2 control-label">Zoom controls visibility</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputControlsVisibility" name="inputControlsVisibility"></select>');
                                    newSelect.append('<option value="alwaysVisible">Always visible</option>');
                                    newSelect.append('<option value="hidden">Hidden</option>');
                                    newSelect.val("alwaysVisible");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Zoom factor
                                    newLabel = $('<label for="inputZoomFactor" class="col-md-2 control-label">Zoom factor (%)</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newInput = $('<input type="text" class="form-control" id="inputZoomFactor" name="inputZoomFactor" required>');
                                    newInput.val(100);
                                    newInput.attr('disabled', true);
                                    newInnerDiv.append(newInput);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newInput.show();
                                    
                                    //Nuova riga
                                    //Zoom controls position
                                    newFormRow = $('<div class="row"></div>');
                                    $("#specificWidgetPropertiesDiv").append(newFormRow);
                                    newLabel = $('<label for="inputControlsPosition" class="col-md-2 control-label">Zoom controls position</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputControlsPosition" name="inputControlsPosition"></select>');
                                    newSelect.append('<option value="topLeft">Top left</option>');
                                    newSelect.append('<option value="topCenter">Top center</option>');
                                    newSelect.append('<option value="topRight">Top right</option>');
                                    newSelect.append('<option value="middleRight">Middle right</option>');
                                    newSelect.append('<option value="bottomRight">Bottom right</option>');
                                    newSelect.append('<option value="bottomMiddle">Bottom middle</option>');
                                    newSelect.append('<option value="bottomLeft">Bottom left</option>');
                                    newSelect.append('<option value="middleLeft">Middle left</option>');
                                    newSelect.val("topLeft");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Title visibility
                                    newLabel = $('<label for="inputShowTitle" class="col-md-2 control-label">Title visibility</label>');
                                    newInnerDiv = $('<div class="col-md-3"></div>');
                                    newSelect = $('<select class="form-control" id="inputShowTitle" name="inputShowTitle"></select>');
                                    newSelect.append('<option value="yes">Yes</option>');
                                    newSelect.append('<option value="no">No</option>');
                                    newSelect.val("yes");
                                    newInnerDiv.append(newSelect);
                                    newFormRow.append(newLabel);
                                    newFormRow.append(newInnerDiv);
                                    newLabel.show();
                                    newInnerDiv.show();
                                    newSelect.show();
                                    
                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
                                    break;    

                                default:
                                    $('#inputUrlWidget').val('');
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
                                    $('#inputFreqWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').attr('disabled', false);
                                    $('#inputHeaderFontColorWidget').prop('required', true);
                                    $('#inputHeaderFontColorWidget').val("#000000");
                                    $('#widgetHeaderFontColor').css("background-color", "#000000");
                                    $('#inputUdmWidget').prop("required", false);
                                    $('#inputUdmWidget').attr("disabled", true);
                                    $('#inputUdmPosition').prop("required", false);
                                    $('#inputUdmPosition').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').attr("disabled", true);
                                    $('#addWidgetFirstAidHospital').prop("required", false);
                                    $('#addWidgetFirstAidHospital').val(-1);
                                    $('#inputFirstAidRow').hide();

                                    showInfoWCkeditors($('#select-widget').val(), editorsArray, null);

                                    //Parametri specifici del widget
                                    $('#specificWidgetPropertiesDiv .row').remove();

                                    $('#inputComuneWidget').attr('disabled', false);

                                    //Rimozione eventuali campi del subform general per widget process
                                    removeWidgetProcessGeneralFields("addWidget");
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
                                    alert('Widgets Configuration was saved successfully');
                                    window.location.reload(true);
                                } else {
                                    alert('Error: Repeat the saving of Widget Copnfiguration');
                                    window.location.reload(true);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $('#pageWrapperCfg').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
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
                        var dashboardParams = {};
                        dashboardParams['sourceDashboardName'] = $('#nameCurrentDashboard').val();
                        dashboardParams['sourceDashboardAuthorName'] = "<?= $_SESSION['dashboardAuthorName'] ?>";
                        dashboardParams['newDashboardName'] = $('#nameNewDashboard').val();
                        
                        $.ajax({
                            url: "duplicate_dash.php",
                            data: {dashboardDuplication: dashboardParams},
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

                    //Listener cancellazione widget
                    $(document).on('click', '.icon-remove-widget', function () {
                        var result_remove = window.confirm("Are you sure you want to permanently delete the selected widget?");
                        
                        if(result_remove) 
                        {
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
                        $("#inputComuneRowM").hide();
                        
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
                                $("#inputHeaderFontColorWidgetM").val(data['headerFontColor']);
                                $("#widgetHeaderFontColorM").css("background-color", data['headerFontColor']);
                                $("#inputFreqWidgetM").val(data['frequency_widget']);
                                $("#inputNameWidgetM").val(data['name_widget']);
                                $("#inputComuneWidgetM").val(data['municipality_metric_widget']);
                                $('#select-IntTemp-Widget-m').val(data['temporal_range_widget']);
                                $("#mod-n-metrcis-widget").text("max " + data['number_metrics_widget'] + " metrics");
                                $("#textarea-metric-widget-m").val('');
                                $("#inputUdmWidgetM").val(data['udm']);
                                $("#urlWidgetM").val(data['url']);
                                var paramsRaw = data['param_w'];
                                var styleParamsRaw = data['styleParameters'];
                                var serviceUri = data['serviceUri'];
                                var viewMode = data['viewMode'];
                                
                                var parameters, styleParameters, currentParams, infoJson = null;
                                
                                var infoJsonRaw = data['infoJson'];
                                var info_mess = data['info_mess'];
                                
                                if(paramsRaw !== 'undefined')
                                {
                                    parameters = JSON.parse(paramsRaw);
                                    currentParams = parameters;
                                }
                                
                                if(infoJsonRaw !== null)
                                {
                                    infoJson = JSON.parse(infoJsonRaw);
                                }
                                
                                for (var i = 0; i < data['metrics_prop'].length; i++) 
                                {
                                    var value_text_widget = $("#textarea-metric-widget-m").val();
                                    value_text_widget += "Name: " + data['metrics_prop'][i]['id_metric'] + ".\n";
                                    value_text_widget += "Description: " + data['metrics_prop'][i]['descrip_metric_widget'] + ".\n";
                                    value_text_widget += "Metric typology: " + data['metrics_prop'][i]['type_metric_widget'] + ".\n";
                                    metricType = data['metrics_prop'][i]['type_metric_widget'];
                                    value_text_widget += "Data Area: " + data['metrics_prop'][i]['area_metric_widget'] + ".\n";
                                    value_text_widget += "Data Source: " + data['metrics_prop'][i]['source_metric_widget'] + ".\n";
                                    value_text_widget += "Status: " + data['metrics_prop'][i]['status_metric_widget'] + ".";
                                    if (i !== ((data['metrics_prop'].length) - 1)) 
                                    {
                                        value_text_widget += "\n\n";
                                    }
                                    $("#textarea-metric-widget-m").val(value_text_widget);
                                }
                                
                                switch($('#select-widget-m').val())
                                {
                                   case "widgetNetworkAnalysis":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan = null;
                                    
                                       //Target widgets geolocation
                                       newFormRow = $('<div class="row"></div>');
                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select name="editWidgetGeolocationWidgets" class="form-control" id="editWidgetGeolocationWidgets" multiple></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetsJson));
                                       
                                       //console.log($("#parametersM").val());
                                       
                                       $("li.gs_w").each(function(){
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          $('#editWidgetGeolocationWidgets').selectpicker({
                                             width: 110
                                          });
                                          
                                          $('#editWidgetGeolocationWidgets').selectpicker('val', targetsJson);

                                          $('#editWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetsJson = [];
                                             }
                                             else
                                             {
                                                targetsJson = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetsJson));
                                          });
                                       }
                                       break;
                                   
                                    case "widgetResources":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan = null;
                                    
                                       //Target widgets geolocation
                                       newFormRow = $('<div class="row"></div>');
                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select name="editWidgetGeolocationWidgets" class="form-control" id="editWidgetGeolocationWidgets" multiple></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetsJson));
                                       
                                       //console.log($("#parametersM").val());
                                       
                                       $("li.gs_w").each(function(){
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          $('#editWidgetGeolocationWidgets').selectpicker({
                                             width: 110
                                          });
                                          
                                          $('#editWidgetGeolocationWidgets').selectpicker('val', targetsJson);

                                          $('#editWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetsJson = [];
                                             }
                                             else
                                             {
                                                targetsJson = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetsJson));
                                          });
                                       }
                                       break;
                                    
                                    case "widgetEvacuationPlans":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                       //Nuova riga
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);

                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select class="form-control" id="editWidgetGeolocationWidgets" name="editWidgetGeolocationWidgets"></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetEventsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetEventsJson));

                                       $("li.gs_w").each(function()
                                       {
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          newSelect.show();
                                          newSelect.val(-1);
                                          newLabel = $('<label for="editWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                          newInnerDiv = $('<div class="col-md-3"></div>');
                                          var eventTypeSelect = $('<select name="editWidgetEventTypes" class="form-control" id="editWidgetEventTypes" multiple></select>');
                                          
                                          eventTypeSelect.append('<option value="approved">Approved</option>');
                                          eventTypeSelect.append('<option value="closed">Closed</option>');
                                          eventTypeSelect.append('<option value="in_progress">In progress</option>');
                                          eventTypeSelect.append('<option value="proposed">Proposed</option>');
                                          eventTypeSelect.append('<option value="rejected">Rejected</option>');
                                          
                                          eventTypeSelect.val(-1);
                                          newFormRow.append(newLabel);
                                          newInnerDiv.append(eventTypeSelect);
                                          newFormRow.append(newInnerDiv);
                                          newLabel.hide();
                                          newInnerDiv.hide();

                                          $('#editWidgetEventTypes').selectpicker({
                                             width: 110
                                          });

                                          $('#editWidgetEventTypes').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = [];
                                             }
                                             else
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetEventsJson));
                                          });

                                          $("#editWidgetGeolocationWidgets").change(function(){
                                             newLabel.show();
                                             newInnerDiv.show();
                                             $('#editWidgetEventTypes').selectpicker('val', targetEventsJson[$("#editWidgetGeolocationWidgets").val()]);
                                          });
                                       }
                                       break;
                                       
                                    case "widgetAlarms":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                       //Nuova riga
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);

                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select class="form-control" id="editWidgetGeolocationWidgets" name="editWidgetGeolocationWidgets"></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetEventsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetEventsJson));

                                       $("li.gs_w").each(function()
                                       {
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          newSelect.show();
                                          newSelect.val(-1);
                                          newLabel = $('<label for="editWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                          newInnerDiv = $('<div class="col-md-3"></div>');
                                          var eventTypeSelect = $('<select name="editWidgetEventTypes" class="form-control" id="editWidgetEventTypes" multiple></select>');
                                          
                                          for(var key in alarmTypes)
                                          {
                                             eventTypeSelect.append('<option value="' + key + '">' + alarmTypes[key].desc + '</option>');
                                          }
                                          
                                          eventTypeSelect.val(-1);
                                          newFormRow.append(newLabel);
                                          newInnerDiv.append(eventTypeSelect);
                                          newFormRow.append(newInnerDiv);
                                          newLabel.hide();
                                          newInnerDiv.hide();

                                          $('#editWidgetEventTypes').selectpicker({
                                             width: 110
                                          });

                                          $('#editWidgetEventTypes').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = [];
                                             }
                                             else
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetEventsJson));
                                          });

                                          $("#editWidgetGeolocationWidgets").change(function(){
                                             newLabel.show();
                                             newInnerDiv.show();
                                             $('#editWidgetEventTypes').selectpicker('val', targetEventsJson[$("#editWidgetGeolocationWidgets").val()]);
                                          });
                                       }
                                       break;
                                   
                                    case "widgetTrafficEvents":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                       //Nuova riga
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);

                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select class="form-control" id="editWidgetGeolocationWidgets" name="editWidgetGeolocationWidgets"></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetEventsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetEventsJson));

                                       $("li.gs_w").each(function()
                                       {
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          newSelect.show();
                                          newSelect.val(-1);
                                          newLabel = $('<label for="editWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                          newInnerDiv = $('<div class="col-md-3"></div>');
                                          var eventTypeSelect = $('<select name="editWidgetEventTypes" class="form-control" id="editWidgetEventTypes" multiple></select>');
                                          
                                          for(var key in trafficEventTypes)
                                          {
                                             eventTypeNum = key.replace("type", "");
                                             eventTypeSelect.append('<option value="' + eventTypeNum + '">' + trafficEventTypes[key].desc + '</option>');
                                          }
                                          
                                          /*eventTypeSelect.append('<option value="1">Incident</option>');
                                          eventTypeSelect.append('<option value="25">Road works</option>');
                                          eventTypeSelect.append('<option value="others">Others</option>');*/
                                          eventTypeSelect.val(-1);
                                          newFormRow.append(newLabel);
                                          newInnerDiv.append(eventTypeSelect);
                                          newFormRow.append(newInnerDiv);
                                          newLabel.hide();
                                          newInnerDiv.hide();

                                          $('#editWidgetEventTypes').selectpicker({
                                             width: 110
                                          });

                                          $('#editWidgetEventTypes').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = [];
                                             }
                                             else
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetEventsJson));
                                          });

                                          $("#editWidgetGeolocationWidgets").change(function(){
                                             newLabel.show();
                                             newInnerDiv.show();
                                             $('#editWidgetEventTypes').selectpicker('val', targetEventsJson[$("#editWidgetGeolocationWidgets").val()]);
                                          });
                                       }
                                       break;
         
         
                                    case "widgetFirstAid":
                                       var series = JSON.parse(data['lastSeries']);
                                      
                                       var thrTables1 = new Array();
                                       var thrTables2 = new Array();
                                       var i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, thrSeries, newFormRow, 
                                           newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer, newSeries = null;
                                        
                                       //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                       setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                        
                                       //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                       buildThrTablesForEditWidget();
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                        
                                       showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        
                                       if(styleParamsRaw !== null) 
                                       {
                                          styleParameters = JSON.parse(styleParamsRaw);
                                       } 
                                      
                                       $('#link_help_modal-add-widget-m').css("display", "");
                                       $('#inputComuneWidgetM').attr('disabled', true);
                                       $("label[for='inputTitleWidgetM']").html("Title");
                                       $("label[for='inputColorWidgetM']").html("Background color");
                                       $('#inputTitleWidgetM').attr('disabled', false);
                                       $('#select-frameColor-Widget-m').attr('disabled', false);
                                       $('#select-frameColor-Widget-m').prop('required', true);
                                       $('#select-IntTemp-Widget-m').attr('disabled', true);
                                       $('#select-IntTemp-Widget-m').prop('required', false);
                                       $('#inputFreqWidgetM').prop('disabled', false);
                                       $("#urlWidgetM").prop('disabled', false);
                                       $('#inputFontSizeM').prop('required', true);
                                       $('#inputFontSizeM').prop('disabled', false);
                                       $('#inputFontColorM').prop('required', true);
                                       $('#inputFontColorM').prop('disabled', false);
                                       $('#urlWidgetM').attr('disabled', false);
                                       $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                       $('#inputHeaderFontColorWidgetM').prop('required', true);
                                       $('#inputUdmWidgetM').prop("required", false);
                                       $('#inputUdmWidgetM').attr("disabled", true);
                                       $('#inputUdmWidgetM').val("");
                                       $('#inputUdmPositionM').prop("required", false);
                                       $('#inputUdmPositionM').attr("disabled", true);
                                       $('#inputUdmPositionM').val(-1);
                                       $('#inputFirstAidRowM').show();
                                        
                                       removeWidgetProcessGeneralFields("editWidget");
                                        
                                       //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                       $('#specificParamsM .row').remove();
                                        
                                       //Visualizzazione campi specifici per questo widget
                                        
                                       //Nuova riga
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);

                                       newLabel = $('<label for="editWidgetFirstAidMode" class="col-md-2 control-label">Visualization mode</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select class="form-control" id="editWidgetFirstAidMode" name="editWidgetFirstAidMode" required></select>');
                                       newSelect.append('<option value="singleSummary">Single hospital - Totals only</option>');
                                       newSelect.append('<option value="singleDetails">Single hospital - Details</option>');
                                       newSelect.append('<option value="hospitalsOverview">Multiple hospitals overview</option>');
                                       newSelect.val(viewMode);
                                       newInnerDiv.append(newSelect);
                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.show();
                                       newInnerDiv.show();
                                       newSelect.show();

                                       var hospitalSelect = '<label for="editWidgetFirstAidHospital" class="col-md-2 control-label">First aid</label>' +
                                       '<div class="col-md-3">' +
                                           '<div class="input-group">' +
                                               '<select id="editWidgetFirstAidHospital" name="editWidgetFirstAidHospital" class="form-control">' + 
                                               '</select>' +
                                           '</div>' +
                                       '</div>';

                                       var multipleHospitalSelect = '<label for="editWidgetFirstAidHospitals" class="col-md-2 control-label">First aids</label>' +
                                               '<div class="col-md-3">' +
                                                   '<div class="input-group">' +
                                                       '<select name="editWidgetFirstAidHospitals" class="form-control" id="editWidgetFirstAidHospitals" multiple>' + 
                                                       '</select>' +
                                                   '</div>' +
                                               '</div>';
                                       
                                       newFormRow.append(hospitalSelect);
                                       newFormRow.append(multipleHospitalSelect);
                                       
                                       $('label[for=editWidgetFirstAidHospital]').hide();
                                       $('#editWidgetFirstAidHospital').parent().parent().hide();
                                       $('label[for=editWidgetFirstAidHospitals]').hide();
                                       $('#editWidgetFirstAidHospitals').parent().parent().hide();

                                       var hospitalName, hospitalUrl = null;

                                       for(var i = 0; i < hospitalList.Services.features.length; i++)
                                       {
                                          hospitalName = hospitalList.Services.features[i].properties.name;
                                          hospitalUrl = hospitalList.Services.features[i].properties.serviceUri;
                                          
                                          hospitalName = hospitalName.replace("PRONTO SOCCORSO", "PS");
                                          hospitalName = hospitalName.replace("PRIMO INTERVENTO", "PI");
                                          hospitalName = hospitalName.replace("AZIENDA OSPEDALIERA", "AO");
                                          hospitalName = hospitalName.replace("PRESIDIO OSPEDALIERO", "PO");
                                          hospitalName = hospitalName.replace("ISTITUTO DI PUBBLICA ASSISTENZA", "IPA");
                                          hospitalName = hospitalName.replace("ASSOCIAZIONE DI PUBBLICA ASSISTENZA", "APA");
                                          hospitalName = hospitalName.replace("OSPEDALE DI", "");
                                          hospitalName = hospitalName.replace("OSPEDALE DEL", "");
                                          hospitalName = hospitalName.replace("OSPEDALE DELL'", "");
                                          hospitalName = hospitalName.replace("OSPEDALE DELLA", "");
                                          hospitalName = hospitalName.replace("DELL'OSPEDALE", "");
                                          hospitalName = hospitalName.replace("OSPEDALE", "");
                                          hospitalName = hospitalName.replace("ITALIANA", "");
                                          
                                          if($("#editWidgetFirstAidHospital").find('option[value="' + hospitalUrl + '"]').length <= 0)
                                          {
                                             $("#editWidgetFirstAidHospital").append('<option value="' + hospitalUrl + '">' + hospitalName + '</option>');
                                          }
                                          
                                          if($("#editWidgetFirstAidHospitals").find('option[value="' + hospitalUrl + '"]').length <= 0)
                                          {
                                             $("#editWidgetFirstAidHospitals").append('<option value="' + hospitalUrl + '">' + hospitalName + '</option>');
                                          }
                                       }
                                       
                                       if((viewMode === 'singleSummary')||(viewMode === 'singleDetails'))
                                       {
                                          $('label[for=editWidgetFirstAidHospital]').show();
                                          $('#editWidgetFirstAidHospital').parent().parent().show(); 
                                          $('#editWidgetFirstAidHospital').attr("disabled", false);
                                          $('#editWidgetFirstAidHospital').prop("required", true);
                                          $('#editWidgetFirstAidHospital').val(serviceUri);
                                          $("#serviceUriM").val(serviceUri);
                                          $("#hospitalListM").val("");
                                          $("#editWidgetFirstAidHospital").change(function()
                                          {
                                             $("#serviceUriM").val($(this).val());
                                          });
                                       }
                                       else
                                       {
                                          var selectedHospitals = JSON.parse(data['hospitalList']);
                                          $("#serviceUriM").val("");
                                          $("#hospitalListM").val(data['hospitalList']);
                                          $('label[for=editWidgetFirstAidHospitals]').show();
                                          $('#editWidgetFirstAidHospitals').parent().parent().show();
                                          $('#editWidgetFirstAidHospitals').selectpicker({
                                             width: 110
                                          });
                                          $('#editWidgetFirstAidHospitals').selectpicker('val', selectedHospitals);
                                          
                                          $('#editWidgetFirstAidHospitals').on('changed.bs.select', function (e) {
                                             $("#hospitalListM").val(JSON.stringify($(this).val()));
                                             var labelsString = $('button[data-id="editWidgetFirstAidHospitals"] span').eq(0).html().replace(/  /g, " ");
                                                   
                                             var localInfoJson = {
                                                "firstAxis":
                                                {
                                                   "Red_code":"",
                                                   "Yellow_code":"",
                                                   "Green_code":"",
                                                   "Blue_code":"",
                                                   "White_code":""
                                                },
                                                "secondAxis": {}
                                             };

                                             var localSeries = {  
                                                "firstAxis":{  
                                                   "desc":"Priority",
                                                   "labels":[  
                                                      "Red code",
                                                      "Yellow code",
                                                      "Green code",
                                                      "Blue code",
                                                      "White code"
                                                   ]
                                                },
                                                "secondAxis":{  
                                                   "desc":"Hospital",
                                                   "labels":[],
                                                   "series":[]
                                                }
                                             };

                                             if(labelsString !== "Nothing selected")
                                             {
                                                var labels = labelsString.split(", ");
                                                var infoLabel = null;

                                                for(var i = 0; i < labels.length; i++)
                                                {
                                                   localSeries.secondAxis.labels[i] = labels[i];
                                                }

                                                for(var j = 0; j < localSeries.secondAxis.labels.length; j++)
                                                {
                                                   infoLabel = localSeries.secondAxis.labels[j].replace(/ /g, "_");
                                                   infoJson.secondAxis[infoLabel] = "";
                                                }
                                             }

                                             showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, localSeries, localInfoJson, data['info_mess']);
                                          });
                                       }
                                       
                                       $("#editWidgetFirstAidMode").change(function()
                                       {
                                          $("#infoMainSelectM").val(-1);
                                          $("#widgetInfoCkEditorTitleM").empty();
                                          $("#widgetInfoCkEditorTitleM").hide();
                                          
                                          switch($(this).val())
                                          {
                                             case "singleSummary": 
                                                $('label[for=editWidgetFirstAidHospitals]').hide();
                                                $('#editWidgetFirstAidHospitals').parent().parent().hide();
                                                $('label[for=editWidgetFirstAidHospital]').show();
                                                $('#editWidgetFirstAidHospital').parent().parent().show();
                                                
                                                $('#editWidgetFirstAidHospital').val(-1);
                                                $("#hospitalListM").val("");
                                                
                                                $("#editWidgetFirstAidHospital").change(function()
                                                {
                                                   $("#serviceUriM").val($(this).val());
                                                });
                                                   
                                                newSeries = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Status",
                                                      "labels":[  
                                                         "Totals"
                                                      ],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                infoJson = {
                                                   "firstAxis":
                                                   {
                                                      "Red_code":"",
                                                      "Yellow_code":"",
                                                      "Green_code":"",
                                                      "Blue_code":"",
                                                      "White_code":""
                                                   },
                                                   "secondAxis":
                                                   {
                                                      "Totals":""
                                                   }
                                                };
                                                
                                                showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, newSeries, infoJson, data['info_mess']);

                                                $('label[for="alrAxisSelM"]').hide();
                                                $("#alrAxisSelM").hide();
                                                $('label[for="alrFieldSelM"]').hide();
                                                $("#alrFieldSelM").hide();
                                                $("#alrAxisSelM").empty();
                                                $("#alrAxisSelM").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                $("#alrAxisSelM").val(-1);
                                                $("#addWidgetRangeTableContainerM").hide();
                                                $("#alrThrSelM").attr("disabled", false);
                                                $("#alrThrSelM").prop("required", true);
                                                $("#alrThrSelM").val("no");
                                                
                                                $("#showTableFirstCellM").val(-1);
                                                $("#showTableFirstCellM").prop("required", false);
                                                $("#showTableFirstCellM").attr("disabled", true);

                                                $("#tableFirstCellFontSizeM").val("");
                                                $("#tableFirstCellFontSizeM").prop("required", false);
                                                $("#tableFirstCellFontSizeM").attr("disabled", true);	

                                                $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#tableFirstCellFontColorM").val("");
                                                $("#tableFirstCellFontColorM").prop("required", false);
                                                $("#tableFirstCellFontColorM").attr("disabled", true);	

                                                $("#rowsLabelsFontSizeM").val("");
                                                $("#rowsLabelsFontSizeM").prop("required", false);
                                                $("#rowsLabelsFontSizeM").attr("disabled", true);	

                                                $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#rowsLabelsFontColorM").val("");
                                                $("#rowsLabelsFontColorM").prop("required", false);
                                                $("#rowsLabelsFontColorM").attr("disabled", true);

                                                $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                                $("#rowsLabelsBckColorM").val("");
                                                $("#rowsLabelsBckColorM").attr("disabled", true);
                                                break;

                                             case "singleDetails":
                                                console.log("Passato a single details");
                                                $('label[for=editWidgetFirstAidHospitals]').hide();
                                                $('#editWidgetFirstAidHospitals').parent().parent().hide();
                                                $('label[for=editWidgetFirstAidHospital]').show();
                                                $('#editWidgetFirstAidHospital').parent().parent().show();

                                                $('#editWidgetFirstAidHospital').val(-1);
                                                $("#hospitalListM").val("");

                                                $("#editWidgetFirstAidHospital").change(function()
                                                {
                                                   $("#serviceUriM").val($(this).val());
                                                });

                                                newSeries = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Status",
                                                      "labels":[  
                                                         "Addressed",
                                                         "Waiting",
                                                         "In visit",
                                                         "In observation",
                                                         "Totals"
                                                      ],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                infoJson = {
                                                   "firstAxis":
                                                    {
                                                       "Red_code":"",
                                                       "Yellow_code":"",
                                                       "Green_code":"",
                                                       "Blue_code":"",
                                                       "White_code":""
                                                    },
                                                    "secondAxis":
                                                    {
                                                       "Addressed":"",
                                                       "Waiting":"",
                                                       "In_visit":"",
                                                       "In_observation":"",
                                                       "Totals":""
                                                    }
                                                };
                                                
                                                showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, newSeries, infoJson, data['info_mess']);

                                                $('label[for="alrAxisSelM"]').hide();
                                                $("#alrAxisSelM").hide();
                                                $('label[for="alrFieldSelM"]').hide();
                                                $("#alrFieldSelM").hide();
                                                $("#alrAxisSelM").empty();
                                                $("#alrAxisSelM").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                //$("#alrAxisSelM").append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                                $("#alrAxisSelM").val(-1);
                                                $("#addWidgetRangeTableContainerM").hide();
                                                $("#alrThrSelM").attr("disabled", false);
                                                $("#alrThrSelM").prop("required", true);
                                                $("#alrThrSelM").val("no");
                                                
                                                $("#showTableFirstCellM").val(-1);
                                                $("#showTableFirstCellM").prop("required", false);
                                                $("#showTableFirstCellM").attr("disabled", true);

                                                $("#tableFirstCellFontSizeM").val("10");
                                                $("#tableFirstCellFontSizeM").prop("required", true);
                                                $("#tableFirstCellFontSizeM").attr("disabled", false);	

                                                $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#tableFirstCellFontColorM").val("#000000");
                                                $("#tableFirstCellFontColorM").prop("required", true);
                                                $("#tableFirstCellFontColorM").attr("disabled", false);	

                                                $("#rowsLabelsFontSizeM").val("10");
                                                $("#rowsLabelsFontSizeM").prop("required", true);
                                                $("#rowsLabelsFontSizeM").attr("disabled", false);	

                                                $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#rowsLabelsFontColorM").val("#000000");
                                                $("#rowsLabelsFontColorM").prop("required", true);
                                                $("#rowsLabelsFontColorM").attr("disabled", false);

                                                $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker("setValue","#FFFFFF");
                                                $("#rowsLabelsBckColorM").val("#FFFFFF");
                                                $("#rowsLabelsBckColorM").prop("required", true);
                                                $("#rowsLabelsBckColorM").attr("disabled", false);
                                                break;      

                                             case "hospitalsOverview":
                                                $('label[for=editWidgetFirstAidHospital]').hide();
                                                $('#editWidgetFirstAidHospital').parent().parent().hide();
                                                $('label[for=editWidgetFirstAidHospitals]').show();
                                                $('#editWidgetFirstAidHospitals').parent().parent().show();
                                                $('#editWidgetFirstAidHospitals').selectpicker('deselectAll');
                                                $('#editWidgetFirstAidHospitals').selectpicker('destroy');
                                                $('#editWidgetFirstAidHospitals').selectpicker({
                                                   width: 110
                                                });

                                                newSeries = {  
                                                   "firstAxis":{  
                                                      "desc":"Priority",
                                                      "labels":[  
                                                         "Red code",
                                                         "Yellow code",
                                                         "Green code",
                                                         "Blue code",
                                                         "White code"
                                                      ]
                                                   },
                                                   "secondAxis":{  
                                                      "desc":"Hospital",
                                                      "labels":[],
                                                      "series":[]
                                                   }
                                                };
                                                
                                                infoJson = {
                                                   "firstAxis":
                                                   {
                                                      "Red_code":"",
                                                      "Yellow_code":"",
                                                      "Green_code":"",
                                                      "Blue_code":"",
                                                      "White_code":""
                                                   },
                                                   "secondAxis": {}
                                                };

                                                $("#serviceUriM").val("");

                                                $('#editWidgetFirstAidHospitals').off('changed.bs.select');
                                                $('#editWidgetFirstAidHospitals').on('changed.bs.select', function (e) 
                                                {
                                                   $("#hospitalListM").val(JSON.stringify($(this).val()));
                                                   
                                                   var labelsString = $('button[data-id="editWidgetFirstAidHospitals"] span').eq(0).html().replace(/  /g, " ");
                                                   
                                                   infoJson = {
                                                      "firstAxis":
                                                      {
                                                         "Red_code":"",
                                                         "Yellow_code":"",
                                                         "Green_code":"",
                                                         "Blue_code":"",
                                                         "White_code":""
                                                      },
                                                      "secondAxis": {}
                                                   };
                                                   
                                                   var localSeries = {  
                                                      "firstAxis":{  
                                                         "desc":"Priority",
                                                         "labels":[  
                                                            "Red code",
                                                            "Yellow code",
                                                            "Green code",
                                                            "Blue code",
                                                            "White code"
                                                         ]
                                                      },
                                                      "secondAxis":{  
                                                         "desc":"Hospital",
                                                         "labels":[],
                                                         "series":[]
                                                      }
                                                   };
                                                   
                                                   if(labelsString !== "Nothing selected")
                                                   {
                                                      var labels = labelsString.split(", ");
                                                      var infoLabel = null;

                                                      for(var i = 0; i < labels.length; i++)
                                                      {
                                                         localSeries.secondAxis.labels[i] = labels[i];
                                                      }

                                                      for(var j = 0; j < localSeries.secondAxis.labels.length; j++)
                                                      {
                                                         infoLabel = localSeries.secondAxis.labels[j].replace(/ /g, "_");
                                                         infoJson.secondAxis[infoLabel] = "";
                                                      }
                                                   }
                                                   
                                                   showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, localSeries, infoJson, data['info_mess']);
                                                });
                                                
                                                showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, newSeries, infoJson, data['info_mess']);

                                                $('label[for="alrAxisSelM"]').hide();
                                                $("#alrAxisSelM").hide();
                                                $('label[for="alrFieldSelM"]').hide();
                                                $("#alrFieldSelM").hide();
                                                $("#alrAxisSelM").empty();
                                                $("#alrAxisSelM").append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                                $("#alrAxisSelM").append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                                $("#alrAxisSelM").val(-1);
                                                $("#addWidgetRangeTableContainerM").hide();
                                                $("#alrThrSelM").val("no");
                                                $("#alrThrSelM").attr("disabled", true);
                                                $("#alrThrSelM").prop("required", false);

                                                $("#showTableFirstCellM").val(-1);
                                                $("#showTableFirstCellM").prop("required", false);
                                                $("#showTableFirstCellM").attr("disabled", true);

                                                $("#tableFirstCellFontSizeM").val("10");
                                                $("#tableFirstCellFontSizeM").prop("required", true);
                                                $("#tableFirstCellFontSizeM").attr("disabled", false);	

                                                $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#tableFirstCellFontColorM").val("#000000");
                                                $("#tableFirstCellFontColorM").prop("required", true);
                                                $("#tableFirstCellFontColorM").attr("disabled", false);	

                                                $("#rowsLabelsFontSizeM").val("10");
                                                $("#rowsLabelsFontSizeM").prop("required", true);
                                                $("#rowsLabelsFontSizeM").attr("disabled", false);	

                                                $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker("setValue","#000000");
                                                $("#rowsLabelsFontColorM").val("#000000");
                                                $("#rowsLabelsFontColorM").prop("required", true);
                                                $("#rowsLabelsFontColorM").attr("disabled", false);

                                                $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker("setValue","#FFFFFF");
                                                $("#rowsLabelsBckColorM").val("#FFFFFF");
                                                $("#rowsLabelsBckColorM").prop("required", true);
                                                $("#rowsLabelsBckColorM").attr("disabled", false);
                                                break;
                                          }
                                          
                                          //Distruzione thr tables pregresse
                                          thrTables1 = new Array();
                                          thrTables2 = new Array();

                                          //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                          setGlobals(null, thrTables1, thrTables2, newSeries, $('#select-widget-m').val());

                                          //Costruzione THRTables vuote
                                          buildEmptyThrTables();
                                       });
 
                                        //Nuova riga
                                        //Select show first cell
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="showTableFirstCellM" class="col-md-2 control-label">Show first cell</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="showTableFirstCellM" name="showTableFirstCellM" required></select>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#showTableFirstCellM").val(-1);
                                           $("#showTableFirstCellM").prop("required", false);
                                           $("#showTableFirstCellM").attr("disabled", true);
                                        }
                                        
                                        //First cell font size
                                        //Nuova riga
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="tableFirstCellFontSizeM" class="col-md-2 control-label">First cell font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="tableFirstCellFontSizeM" name="tableFirstCellFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#tableFirstCellFontSizeM").val("");
                                           $("#tableFirstCellFontSizeM").prop("required", false);
                                           $("#tableFirstCellFontSizeM").attr("disabled", true);
                                        }
                                        
                                        //First cell font color
                                        newLabel = $('<label for="tableFirstCellFontColorM" class="col-md-2 control-label">First cell font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="tableFirstCellFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableFirstCellFontColorM" name="tableFirstCellFontColorM" required><span class="input-group-addon"><i id="widgetFirstCellFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#tableFirstCellFontColorContainerM').show();
                                        $('#tableFirstCellFontColorM').show();
                                        $("#widgetFirstCellFontColorM").css('display', 'block');
                                        $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker({color: styleParameters.tableFirstCellFontColor});
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                           $("#tableFirstCellFontColorM").val("");
                                           $("#tableFirstCellFontColorM").prop("required", false);
                                           $("#tableFirstCellFontColorM").attr("disabled", true);
                                        }
                                        
                                        //Nuova riga
                                        //Rows labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsFontSizeM" class="col-md-2 control-label">Rows labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSizeM" name="rowsLabelsFontSizeM" required> ');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#rowsLabelsFontSizeM").val("");
                                           $("#rowsLabelsFontSizeM").prop("required", false);
                                           $("#rowsLabelsFontSizeM").attr("disabled", true);
                                        }

                                        //Rows labels font color
                                        newLabel = $('<label for="rowsLabelsFontColorM" class="col-md-2 control-label">Rows labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColorM" name="rowsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsFontColorContainerM').show();
                                        $('#rowsLabelsFontColorM').show();
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');
                                        $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#rowsLabelsFontColorM").val("");
                                           $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                           $("#rowsLabelsFontColorM").val("");
                                           $("#rowsLabelsFontColorM").prop("required", false);
                                           $("#rowsLabelsFontColorM").attr("disabled", true);
                                        }

                                        //Nuova riga
                                        //Cols labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="colsLabelsFontSizeM" class="col-md-2 control-label">Cols labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="colsLabelsFontSizeM" name="colsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Cols labels font color
                                        newLabel = $('<label for="colsLabelsFontColorM" class="col-md-2 control-label">Cols labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColorM" name="colsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetColsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsFontColorContainerM').show();
                                        $('#colsLabelsFontColorM').show();
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        $("#widgetColsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                    
                                        //Nuova riga
                                        //Rows labels background color
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsBckColorM" class="col-md-2 control-label">Rows labels background color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsBckColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsBckColorM" name="rowsLabelsBckColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsBckColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsBckColorContainerM').show();
                                        $('#rowsLabelsBckColorM').show();
                                        $("#widgetRowsLabelsBckColorM").css('display', 'block');
                                        $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsBckColor});
                                        if(viewMode === 'singleSummary')
                                        {
                                           $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker("setValue","#eeeeee");
                                           $("#rowsLabelsBckColorM").val("");
                                           $("#rowsLabelsBckColorM").prop("required", false);
                                           $("#rowsLabelsBckColorM").attr("disabled", true);	
                                        }
                                        
                                        //Nuova riga
                                        //Table borders
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="tableBordersM" class="col-md-2 control-label">Table borders</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="tableBordersM" name="tableBordersM"></select>');
                                        newSelect.append('<option value="no">No borders</option>');
                                        newSelect.append('<option value="horizontal">Horizontal borders only</option>');
                                        newSelect.append('<option value="all">All borders</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Table borders color
                                        newLabel = $('<label for="tableBordersColorM" class="col-md-2 control-label">Table borders color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="tableBordersColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableBordersColorM" name="tableBordersColorM" required><span class="input-group-addon"><i id="widgetTableBordersColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#tableBordersColorContainerM').show();
                                        $('#tableBordersColorM').show();
                                        $("#widgetTableBordersColorM").css('display', 'block');
                                        $("#widgetTableBordersColorM").parent().parent().parent().colorpicker({color: styleParameters.tableBordersColor});

                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#showTableFirstCellM").val(styleParameters.showTableFirstCell);
                                            $("#tableFirstCellFontSizeM").val(styleParameters.tableFirstCellFontSize);
                                            $("#tableFirstCellFontColorM").val(styleParameters.tableFirstCellFontColor);
                                            $("#widgetFirstCellFontColorM").css("background-color", styleParameters.tableFirstCellFontColor);
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#rowsLabelsBckColorM").val(styleParameters.rowsLabelsBckColor);
                                            $("#widgetRowsLabelsBckColorM").css("background-color", styleParameters.rowsLabelsBckColor);
                                            $("#colsLabelsBckColorM").val(styleParameters.colsLabelsBckColor);
                                            $("#widgetColsLabelsBckColorM").css("background-color", styleParameters.colsLabelsBckColor);
                                            $("#tableBordersM").val(styleParameters.tableBorders);
                                            $("#tableBordersColorM").val(styleParameters.tableBordersColor);
                                            $("#widgetTableBordersColorM").css("background-color", styleParameters.tableBordersColor);
                                        }
                                        
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        if(viewMode === 'hospitalsOverview')
                                        {
                                          $("#alrThrSelM").val("no");
                                          $("#alrThrSelM").attr("disabled", true);
                                          $("#alrThrSelM").prop("required", false);
                                        }
                                        else
                                        {
                                           $("#alrThrSelM").attr("disabled", false);
                                           $("#alrThrSelM").prop("required", true);
                                           if(currentParams === null)
                                           {
                                              $('#alrThrSelM').val("no");
                                              $("label[for='alrAxisSelM']").hide();
                                              $('#alrAxisSelM').val(-1);
                                              $('#alrAxisSelM').hide();
                                              $('#parametersM').val('');
                                           }
                                           else
                                           {
                                              //ESPOSIZIONE DEI CAMPI
                                              $('#alrThrSelM').val("yes");
                                              $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                              $("label[for='alrAxisSelM']").show();
                                              $('#alrAxisSelM').parent().show();
                                              $('#alrAxisSelM').show();
                                              $('#alrAxisSelM').change(alrAxisSelMListener);
                                              $("label[for='alrFieldSelM']").show();
                                              $('#alrFieldSelM').parent().show();
                                              $('#alrFieldSelM').show();
                                              //POPOLAMENTO DELLA SELECT DEI CAMPI
                                              $('#alrAxisSelM').trigger("change");
                                              $('#alrFieldSelM').change(alrFieldSelMListener);
                                              $('#addWidgetRangeTableContainerM').show();
                                              $('#parametersM').val(JSON.stringify(currentParams));
                                           }
                                        }
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSelM').change(alrThrFlagMListener);
                                        
                                        //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                        newLabel = $('<label for="alrAxisSelM" class="col-md-2 control-label">Thresholds target set</label>');
                                        newSelect = $('<select class="form-control" id="alrAxisSelM" name="alrAxisSelM"></select>');
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        if((viewMode !== 'singleSummary') && (viewMode !== 'singleDetails'))
                                        {
                                           newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                        }
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Nuova riga
                                        //Threshold field select
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                        addWidgetRangeTableContainerM.hide();
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrSelM').val("no");
                                            $("label[for='alrAxisSelM']").hide();
                                            $('#alrAxisSelM').val(-1);
                                            $('#alrAxisSelM').hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrSelM').val("yes");
                                            $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                            $("label[for='alrAxisSelM']").show();
                                            $('#alrAxisSelM').parent().show();
                                            $('#alrAxisSelM').show();
                                            $('#alrAxisSelM').change(alrAxisSelMListener);
                                            $("label[for='alrFieldSelM']").show();
                                            $('#alrFieldSelM').parent().show();
                                            $('#alrFieldSelM').show();
                                            //POPOLAMENTO DELLA SELECT DEI CAMPI
                                            $('#alrAxisSelM').trigger("change");
                                            $('#alrFieldSelM').change(alrFieldSelMListener);
                                            $('#addWidgetRangeTableContainerM').show();
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                        }
                                       break;
         
                                    case "widgetRadarSeries":
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString = metricData.data[0].commit.author.series;
                                        var series = jQuery.parseJSON(seriesString);
                                        var thrSeries, i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, colorsTable, newRow, 
                                            newCell, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer, 
                                            barsColorsTableContainerM, index, newThrObj, fieldName, descName = null;
                                        var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                        var colorsArray = new Array();
                                        
                                        //Costruzione THRTable
                                        var thrTable = $("<table class='thrRangeTableRadar table table-bordered'></table>"); 
                                        
                                        //Costruzione intestazione tabella
                                        firstRow = $('<tr></tr>');
                                        cell = $('<td><a href="#"><i class="fa fa-plus" style="font-size:24px;color:#337ab7"></i></a></td>');
                                        cell.find('i.fa-plus').click(addThrRangeRadarM);
                                        firstRow.append(cell);
                                        cell = $('<td class="colorHeader">Color</td>'); 
                                        firstRow.append(cell);
                                        cell = $('<td><span style="display: block; width: 75px">Short description</span></td>');
                                        firstRow.append(cell);
                                        thrTable.append(firstRow);
                                        
                                        for(var i in series.firstAxis.labels)
                                        {
                                            if(series.firstAxis.labels[i].length > 8)
                                            {
                                                descName = series.firstAxis.labels[i].substr(0,8) + "...";
                                            }
                                            else
                                            {
                                                descName = series.firstAxis.labels[i];
                                            }
                                            
                                            cell = $('<td class="boundDesc"><b>' + descName + '</b><br/>limit</td>');
                                            firstRow.append(cell);
                                        }

                                        //Costruzione del corpo della tabella
                                        if(currentParams !== null)
                                        {
                                            for(j = 0; j < currentParams.thresholdArray.length; j++)
                                            {
                                                index = j;

                                                //Aggiunta record alla thrTable
                                                newTableRow = $('<tr></tr>');

                                                //Cella con pulsante rimozione riga
                                                newTableCell = $('<td><a><i class="fa fa-close" data-index="' + index + '" data-field="delBtn" style="font-size:24px;color:red"></i></a></td>');
                                                newTableCell.find('i').click(delThrRangeRadarM);
                                                newTableRow.append(newTableCell);

                                                //Cella per color picker
                                                newTableCell = $('<td><div style="width: 130px" class="input-group colorPicker" data-index="' + index + '" + data-field="color"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newTableRow.append(newTableCell);
                                                newTableRow.find('div.colorPicker').colorpicker({color: currentParams.thresholdArray[j].color});
                                                newTableRow.find('div.colorPicker').on('hidePicker', updateParamsRadarM);                
                                                newTableRow.append(newTableCell);

                                                //Cella per short description
                                                newTableCell = $('<td class="descValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="desc" class="toBeEdited">' + currentParams.thresholdArray[j].desc + '</a></td>');
                                                newTableCell.find('a').editable();
                                                newTableRow.append(newTableCell);

                                                //Celle per gli upper bound per ogni campo
                                                for(i = 0; i < series.firstAxis.labels.length; i++)
                                                {
                                                    fieldName = series.firstAxis.labels[i];
                                                    newTableCell = $('<td class="boundValue"><a href="#" data-mode="popup" data-index="' + index + '" data-field="' + fieldName + '" class="toBeEdited">' + currentParams.thresholdArray[j][fieldName] + '</a></td>');
                                                    newTableCell.find('a').editable();
                                                    newTableRow.append(newTableCell);
                                                }

                                                newTableRow.find('a.toBeEdited').on('save', updateParamsRadarM);
                                                thrTable.append(newTableRow);
                                            }
                                        }
                                        
                                        //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                        setGlobalsRadar(currentParams, thrTable, series, $('#select-widget-m').val());
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                        }
                                        
                                        //AGGIORNA CODICE DA QUI, E' ANCORA IL VECCHIO COPIA/INCOLLA DEGLI ALTRI SERIES
                                        //$("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $("label[for='inputComuneWidgetM']").text("Context");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                        $('#specificParamsM .row').remove();
                                        
                                        //Nuova riga
                                        //Rows labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsFontSizeM" class="col-md-2 control-label">X-Axis labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSizeM" name="rowsLabelsFontSizeM" required> ');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Rows labels font color
                                        newLabel = $('<label for="rowsLabelsFontColorM" class="col-md-2 control-label">X-Axis labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColorM" name="rowsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsFontColorContainerM').show();
                                        $('#rowsLabelsFontColorM').show();
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');
                                        $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});

                                        //Nuova riga
                                        //Cols labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="colsLabelsFontSizeM" class="col-md-2 control-label">Y-Axis labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="colsLabelsFontSizeM" name="colsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Cols labels font color
                                        newLabel = $('<label for="colsLabelsFontColorM" class="col-md-2 control-label">Y-Axis labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColorM" name="colsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetColsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsFontColorContainerM').show();
                                        $('#colsLabelsFontColorM').show();
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        $("#widgetColsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Data labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsFontSizeM" class="col-md-2 control-label">Data labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsFontSizeM" name="dataLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Data labels font color
                                        newLabel = $('<label for="dataLabelsFontColorM" class="col-md-2 control-label">Data labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="dataLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColorM" name="dataLabelsFontColorM" required><span class="input-group-addon"><i id="widgetDataLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#dataLabelsFontColorContainerM').show();
                                        $('#dataLabelsFontColorM').show();
                                        $("#widgetDataLabelsFontColorM").css('display', 'block');
                                        $("#widgetDataLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Legend font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="legendFontSizeM" class="col-md-2 control-label">Legend font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="legendFontSizeM" name="legendFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Legend font color
                                        newLabel = $('<label for="legendFontColorM" class="col-md-2 control-label">Legend font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="legendFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColorM" name="legendFontColorM" required><span class="input-group-addon"><i id="widgetLegendFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#legendFontColorContainerM').show();
                                        $('#legendFontColorM').show();
                                        $("#widgetLegendFontColorM").css('display', 'block');
                                        $("#widgetLegendFontColorM").parent().parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                        
                                        //Nuova riga
                                        //Grid lines width
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="gridLinesWidthM" class="col-md-2 control-label">Grid lines width</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="gridLinesWidthM" name="gridLinesWidthM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Grid lines color
                                        newLabel = $('<label for="gridLinesColorM" class="col-md-2 control-label">Grid lines color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="gridLinesColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="gridLinesColorM" name="gridLinesColorM" required><span class="input-group-addon"><i id="widgetGridLinesColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#gridLinesColorContainerM').show();
                                        $('#gridLinesColorM').show();
                                        $("#widgetGridLinesColorM").css('display', 'block');
                                        $("#widgetGridLinesColorM").parent().parent().parent().colorpicker({color: styleParameters.gridLinesColor});
                                        
                                        //Nuova riga
                                        //Lines width
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="linesWidthM" class="col-md-2 control-label">Lines width</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="linesWidthM" name="linesWidthM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();
                                        
                                        //Lines colors
                                        newLabel = $('<label for="barsColorsSelectM" class="col-md-2 control-label">Lines colors</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="barsColorsSelectM" name="barsColorsSelectM"></select>');
                                        newSelect.append('<option value="manual">Manual</option>');
                                        newSelect.append('<option value="auto">Automatic</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        $('#barsColorsSelectM').change(function() 
                                        {
                                            if($('#barsColorsSelectM').val() === "manual")
                                            {
                                                $('#barsColorsTableContainerM').show();
                                            }
                                            else
                                            {
                                                $('#barsColorsTableContainerM').hide();
                                            }
                                        });
                                        
                                        newSelect.show();
                                        
                                        //Parametri specifici del widget
                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                            $("label[for='legendFontSizeM']").parent().show();
                                            $("#dataLabelsFontSizeM").val(styleParameters.dataLabelsFontSize);
                                            $("#dataLabelsFontColorM").val(styleParameters.dataLabelsFontColor);
                                            $("#gridLinesWidthM").val(styleParameters.gridLinesWidth);
                                            $("#linesWidthM").val(styleParameters.linesWidth);
                                            $("#widgetDataLabelsFontColorM").css("background-color", styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                            $("#legendFontSizeM").val(styleParameters.legendFontSize);
                                            $("#legendFontColorM").val(styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").css("background-color", styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                            $("#barsColorsSelectM").val(styleParameters.barsColorsSelect);
                                        }
                                        
                                        //Contenitore per tabella dei colori
                                        barsColorsTableContainerM = $('<div id="barsColorsTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(barsColorsTableContainerM);
                                        
                                        function updateWidgetBarSeriesColorsM(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray[index] = newColor;
                                            $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        }
                                        
                                        colorsTable = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                        for(var i in series.secondAxis.labels)
                                        {
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            //Se l'attuale impostazione è per colori automatici, costruiamo JSON e tabella GUI con impostazioni di default, altrimenti con colori da DB
                                            if(styleParameters.barsColorsSelect === 'auto')
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                                colorsArray.push(defaultColorsArray[i%10]);
                                            }
                                            else
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: styleParameters.barsColors[i]});
                                                colorsArray.push(styleParameters.barsColors[i]);
                                            }
                                            
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColorsM); 
                                            colorsTable.append(newRow);
                                        }
                                        
                                        $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        $('#barsColorsTableContainerM').append(colorsTable);
                                        
                                        //Per prima visualizzazione in edit
                                        if($('#barsColorsSelectM').val() === "manual")
                                        {
                                            $('#barsColorsTableContainerM').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainerM').hide();
                                        } 
                                        
                                        //Codice di creazione soglie
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Thresholds lines width
                                        newLabel = $('<label for="alrThrLinesWidthM" class="col-md-2 control-label">Thresholds lines width</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="alrThrLinesWidthM" name="alrThrLinesWidthM" required>');
                                        newInput.val(styleParameters.alrThrLinesWidth);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newInput.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row thrRangeTableRadarContainer"></div>');
                                        addWidgetRangeTableContainerM.append(thrTable);
                                        $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                        addWidgetRangeTableContainerM.hide();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSelM').change(alrThrSelListenerRadarM);
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrSelM').val("no");
                                            $("label[for='alrThrLinesWidthM']").hide();
                                            $('#alrThrLinesWidthM').parent().hide();
                                            $('#alrThrLinesWidthM').hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrSelM').val("yes");
                                            $("label[for='alrThrLinesWidthM']").show();
                                            $('#alrThrLinesWidthM').parent().show();
                                            $('#alrThrLinesWidthM').show();
                                            $('#addWidgetRangeTableContainerM').show();
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                        }
                                        
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
            
                                    case "widgetLineSeries": case "widgetCurvedLineSeries":
                                        var thrTables1 = new Array();
                                        var thrTables2 = new Array();
                                        var thrSeries, i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, colorsTable, newRow, newCell, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer, barsColorsTableContainerM = null;
                                        var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                        var colorsArray = new Array();    
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString = metricData.data[0].commit.author.series;
                                        var series = jQuery.parseJSON(seriesString);
                                        
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                        setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                        
                                        //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                        buildThrTablesForEditWidget();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                        }
                                        
                                        //$("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $("label[for='inputComuneWidgetM']").text("Context");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                        $('#specificParamsM .row').remove();
                                        
                                        //Visualizzazione campi specifici per questo widget
                                        //Nuova riga
                                        //X-Axis dataset
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="xAxisDatasetM" class="col-md-2 control-label">X-Axis dataset</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="xAxisDatasetM" name="xAxisDatasetM" required>');
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Line width
                                        newLabel = $('<label for="lineWidthM" class="col-md-2 control-label">Line width</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="lineWidthM" name="lineWidthM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();
                                        
                                        //Nuova riga
                                        //X-Axis labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsFontSizeM" class="col-md-2 control-label">X-Axis labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSizeM" name="rowsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //X-Axis labels font color
                                        newLabel = $('<label for="rowsLabelsFontColorM" class="col-md-2 control-label">X-Axis labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColorM" name="rowsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsFontColorContainerM').show();
                                        $('#rowsLabelsFontColorM').show();
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');
                                        $("#widgetRowsLabelsFontColor").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Y-Axis labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="colsLabelsFontSizeM" class="col-md-2 control-label">Y-Axis labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="colsLabelsFontSizeM" name="colsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Y-Axis labels font color
                                        newLabel = $('<label for="colsLabelsFontColorM" class="col-md-2 control-label">Y-Axis labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColorM" name="colsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetColsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsFontColorContainerM').show();
                                        $('#colsLabelsFontColorM').show();
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        $("#widgetColsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Data labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsFontSizeM" class="col-md-2 control-label">Data labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsFontSizeM" name="dataLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Data labels font color
                                        newLabel = $('<label for="dataLabelsFontColorM" class="col-md-2 control-label">Data labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="dataLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColorM" name="dataLabelsFontColorM" required><span class="input-group-addon"><i id="widgetDataLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#dataLabelsFontColorContainerM').show();
                                        $('#dataLabelsFontColorM').show();
                                        $("#widgetDataLabelsFontColorM").css('display', 'block');
                                        $("#widgetDataLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Legend font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="legendFontSizeM" class="col-md-2 control-label">Legend font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="legendFontSizeM" name="legendFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Legend font color
                                        newLabel = $('<label for="legendFontColorM" class="col-md-2 control-label">Legend font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="legendFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColorM" name="legendFontColorM" required><span class="input-group-addon"><i id="widgetLegendFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#legendFontColorContainerM').show();
                                        $('#legendFontColorM').show();
                                        $("#widgetLegendFontColorM").css('display', 'block');
                                        $("#widgetLegendFontColorM").parent().parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                        
                                        //Nuova riga
                                        //Lines colors
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="barsColorsSelectM" class="col-md-2 control-label">Lines colors</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="barsColorsSelectM" name="barsColorsSelectM"></select>');
                                        newSelect.append('<option value="auto">Automatic</option>');
                                        newSelect.append('<option value="manual">Manual</option>');
                                        newSelect.val(styleParameters.barsColorsSelect);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        $('#barsColorsSelectM').change(function() 
                                        {
                                            if($('#barsColorsSelectM').val() === "manual")
                                            {
                                                $('#barsColorsTableContainerM').show();
                                            }
                                            else
                                            {
                                                $('#barsColorsTableContainerM').hide();
                                            }
                                        });

                                        //Chart type
                                        newLabel = $('<label for="chartTypeM" class="col-md-2 control-label">Chart type</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="chartTypeM" name="chartTypeM"></select>');
                                        newSelect.append("<option value='lines'>Simple lines</option>");
                                        newSelect.append("<option value='area'>Area lines</option>");
                                        newSelect.append("<option value='stacked'>Stacked area lines</option>");
                                        newSelect.val(styleParameters.chartType);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Nuova riga
                                        //Data labels
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsM" class="col-md-2 control-label">Data labels</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="dataLabelsM" name="dataLabelsM">');
                                        newSelect.append('<option value="no">No data labels</option>');
                                        newSelect.append('<option value="value">Value only</option>');
                                        newSelect.append('<option value="full">Field name and value</option>');
                                        newSelect.val(styleParameters.dataLabels);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Parametri specifici del widget
                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#xAxisDatasetM").val(styleParameters.xAxisDataset);
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                            $("#dataLabelsFontSizeM").val(styleParameters.dataLabelsFontSize);
                                            $("#dataLabelsFontColorM").val(styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").css("background-color", styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                            $("#legendFontSizeM").val(styleParameters.legendFontSize);
                                            $("#legendFontColorM").val(styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").css("background-color", styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                            $("#barsColorsSelectM").val(styleParameters.barsColorsSelect);
                                            $("#chartTypeM").val(styleParameters.chartType);
                                            $("#dataLabelsM").val(styleParameters.dataLabels);
                                            $("#lineWidthM").val(styleParameters.lineWidth);
                                            $("#alrLookM").val(styleParameters.alrLook);
                                        }
                                        
                                        //Contenitore per tabella dei colori
                                        barsColorsTableContainerM = $('<div id="barsColorsTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(barsColorsTableContainerM);
                                        
                                        function updateWidgetBarSeriesColorsM(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray[index] = newColor;
                                            $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        }
                                        
                                        colorsTable = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                        updateXAxisSelectM();
                                        
                                        function updateXAxisSelectM()
                                        {
                                            var colorsTarget = null;

                                            colorsTable.find('tr').remove();
                                            colorsTable.append("<tr><td>Series</td><td>Color</td></tr>");
                                            
                                            $('#alrAxisSelM').empty();

                                            if($("#xAxisDatasetM").val() === series.firstAxis.desc)
                                            {
                                                //Grafico non trasposto
                                                colorsTarget = series.secondAxis.labels;
                                                $('#alrAxisSelM').append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                            }
                                            else
                                            {
                                                //Grafico trasposto
                                                colorsTarget = series.firstAxis.labels;
                                                $('#alrAxisSelM').append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                            }
                                            
                                            alrAxisSelMListener();

                                            for(var i in colorsTarget)
                                            {
                                                newRow = $('<tr></tr>');
                                                newCell = $('<td>' + colorsTarget[i]+ '</td>');
                                                newRow.append(newCell);
                                                newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newRow.append(newCell);
                                                
                                                //Se l'attuale impostazione è per colori automatici, costruiamo JSON e tabella GUI con impostazioni di default, altrimenti con colori da DB
                                                if(styleParameters.barsColorsSelect === 'auto')
                                                {
                                                    newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                                    colorsArray.push(defaultColorsArray[i%10]);
                                                }
                                                else
                                                {
                                                    newRow.find('div.colorPicker').colorpicker({color: styleParameters.barsColors[i]});
                                                    colorsArray.push(styleParameters.barsColors[i]);
                                                }
                                                
                                                newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColorsM); 
                                                colorsTable.append(newRow);
                                            }
                                        }

                                        $("#xAxisDatasetM").off();
                                        $("#xAxisDatasetM").on("change", updateXAxisSelectM);
                                        $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        $('#barsColorsTableContainerM').append(colorsTable);
                                        //Per prima visualizzazione in edit
                                        $('#barsColorsSelectM').trigger("change"); 
                                        
                                        //Codice di creazione soglie
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                        newLabel = $('<label for="alrAxisSelM" class="col-md-2 control-label">Thresholds target set</label>');
                                        newSelect = $('<select class="form-control" id="alrAxisSelM" name="alrAxisSelM"></select>');
                                        if($("#xAxisDatasetM").val() === series.firstAxis.desc)
                                        {
                                            //No trasposizione
                                            newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        }
                                        else
                                        {
                                            //Trasposizione
                                            newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                        }
                                        newSelect.val(-1);
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Nuova riga
                                        //Threshold field select
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Threshold look&feel
                                        newLabel = $('<label for="alrLookM" class="col-md-2 control-label">Thresholds look&feel</label>');
                                        newSelect = $('<select class="form-control" id="alrLookM" name="alrLookM">');
                                        newSelect.append('<option value="none">Tooltip only</option>');
                                        newSelect.append('<option value="lines">Lines</option>');
                                        newSelect.append('<option value="areas">Background areas</option>');
                                        newSelect.val(styleParameters.alrLook);
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                        addWidgetRangeTableContainerM.hide();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSelM').change(alrThrFlagMListener);
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrSelM').val("no");
                                            $("label[for='alrAxisSelM']").hide();
                                            $('#alrAxisSelM').val(-1);
                                            $('#alrAxisSelM').hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrSelM').val("yes");
                                            $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                            $("label[for='alrAxisSelM']").show();
                                            $('#alrAxisSelM').parent().show();
                                            $('#alrAxisSelM').show();
                                            $("label[for='alrFieldSelM']").show();
                                            $('#alrFieldSelM').parent().show();
                                            $('#alrFieldSelM').show();
                                            $("label[for='alrLookM']").show();
                                            $('#alrLookM').parent().show();
                                            $('#alrLookM').show();
                                            $('#alrLookM').val(styleParameters.alrLook);
                                            //POPOLAMENTO DELLA SELECT DEI CAMPI
                                            alrAxisSelMListener();
                                            $('#addWidgetRangeTableContainerM').show();
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                            //Listener per settaggio/desettaggio campi in base ad asse selezionato
                                            $('#alrAxisSelM').change(alrAxisSelMListener);
                                            //Listener per selezione campo
                                            $('#alrFieldSelM').change(alrFieldSelMListener);
                                        }
                                        break;
            
                                    case "widgetScatterSeries":
                                        //$("#inputComuneRowM").css("display", "");
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        //Parametri specifici del widget
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                            $("label[for='legendFontSizeM']").parent().show();
                                            $("#dataLabelsFontSizeM").val(styleParameters.dataLabelsFontSize);
                                            $("#dataLabelsFontColorM").val(styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").css("background-color", styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                            $("#legendFontSizeM").val(styleParameters.legendFontSize);
                                            $("#legendFontColorM").val(styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").css("background-color", styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                            $("#barsColorsSelectM").val(styleParameters.barsColorsSelect);
                                            $("#chartTypeM").val(styleParameters.chartType);
                                            $("#dataLabelsM").val(styleParameters.dataLabels);
                                            $("#dataLabelsRotationM").val(styleParameters.dataLabelsRotation);
                                        }
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                        $('#specificParamsM .row').remove();
                                        
                                        //Visualizzazione campi specifici per questo widget
                                        $("label[for='rowsLabelsFontSizeM']").parent().show();
                                        $("label[for='rowsLabelsFontSizeM']").show();
                                        $("#rowsLabelsFontSizeM").show();

                                        $("label[for='rowsLabelsFontColorM']").show();
                                        $("#rowsLabelsFontColorM").show();
                                        //Questa falla con css sennò la mostra inline e non viene colorato!
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');

                                        $("label[for='colsLabelsFontSizeM']").parent().show();
                                        $("label[for='colsLabelsFontSizeM']").show();
                                        $("#colsLabelsFontSizeM").show();

                                        $("label[for='colsLabelsFontColorM']").show();
                                        $("#colsLabelsFontColorM").show();
                                        //Questa falla con css sennò la mostra inline e non viene colorato!
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        
                                        $("label[for='dataLabelsFontSizeM']").parent().show();
                                        $("label[for='dataLabelsFontSizeM']").show();
                                        $("#dataLabelsFontSizeM").show();

                                        $("label[for='dataLabelsFontColorM']").show();
                                        $("#dataLabelsFontColorM").show();
                                        //Questa falla con css sennò la mostra inline e non viene colorato!
                                        $("#widgetDataLabelsFontColorM").css('display', 'block');
                                        $("#widgetDataLabelsFontColorM").colorpicker({color: '#000000'});
                                        
                                        $("label[for='legendFontSizeM']").parent().show();
                                        $("label[for='legendFontSizeM']").show();
                                        $("#legendFontSizeM").show();

                                        $("label[for='legendFontColorM']").show();
                                        $("#legendFontColorM").show();
                                        //Questa falla con css sennò la mostra inline e non viene colorato!
                                        $("#widgetLegendFontColorM").css('display', 'block');
                                        
                                        $("label[for='barsColorsSelectM']").html("Markers colors");
                                        $("label[for='barsColorsSelectM']").parent().show();
                                        $("label[for='barsColorsSelectM']").show();
                                        $("#barsColorsSelectM").show();
                                        
                                        $("label[for='chartTypeM']").show();
                                        $("#chartTypeM").show();
                                        
                                        $("label[for='dataLabelsM']").parent().show();
                                        $("label[for='dataLabelsM']").show();
                                        $("#dataLabelsM").show();
                                        
                                        $("label[for='dataLabelsRotationM']").show();
                                        $("#dataLabelsRotationM").show();
                                        
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString = metricData.data[0].commit.author.series;
                                        var series = jQuery.parseJSON(seriesString);
                                        var thrTables1 = new Array();
                                        var thrTables2 = new Array();
                                        var thrSeries = null;
                                        var i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex = null;
                                        var colorsTable, newRow, newCell = null;
                                        var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                        var colorsArray = new Array();
                                        
                                        setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                        
                                        function updateWidgetBarSeriesColorsM(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray[index] = newColor;
                                            $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        }
                                        
                                        colorsTable = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                        for(var i in series.secondAxis.labels)
                                        {
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            //Se l'attuale impostazione è per colori automatici, costruiamo JSON e tabella GUI con impostazioni di default, altrimenti con colori da DB
                                            if(styleParameters.barsColorsSelect === 'auto')
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                                colorsArray.push(defaultColorsArray[i%10]);
                                            }
                                            else
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: styleParameters.barsColors[i]});
                                                colorsArray.push(styleParameters.barsColors[i]);
                                            }
                                            
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColorsM); 
                                            colorsTable.append(newRow);
                                        }
                                        
                                        $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        
                                        $('#barsColorsTableContainerM').append(colorsTable);
                                        
                                        
                                        $('#barsColorsSelectM').change(function () 
                                        {
                                            if($('#barsColorsSelectM').val() === "manual")
                                            {
                                                $('#barsColorsTableContainerM').show();
                                            }
                                            else
                                            {
                                                $('#barsColorsTableContainerM').hide();
                                            }
                                        });
                                        
                                        //Per prima visualizzazione in edit
                                        $('#barsColorsSelectM').trigger("change"); 
                                        
                                        //Mostriamo il campo per settare o meno delle soglie
                                        $("label[for='alrThrFlagM']").parent().show();
                                        $("label[for='alrThrFlagM']").show();
                                        $('#alrThrFlagM').show();
                                        
                                        //Popolamento del select per gli assi
                                        $('#alrAxisSelM').empty();
                                        $('#alrAxisSelM').append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        
                                        //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                        buildThrTablesForEditWidget();
                                        
                                        //Listener per settaggio/desettaggio soglie
                                        $('#alrThrFlagM').change(alrThrFlagMListener);
                                        
                                        //Listener per settaggio/desettaggio campi in base ad asse selezionato
                                        $('#alrAxisSelM').change(alrAxisSelMListener);
                                        
                                        //Listener per selezione campo
                                        $('#alrFieldSelM').change(alrFieldSelMListener);
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrFlagM').val("no");
                                            $('#alrAxisSelM').val(-1);
                                            $('#alrAxisSelM').hide();
                                            $("label[for='alrAxisSelM']").hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrFlagM').val("yes");
                                            $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                            $("label[for='alrFieldSelM']").parent().show();
                                            $('#alrAxisSelM').show();
                                            $("label[for='alrAxisSelM']").show();
                                            $('#alrFieldSelM').show();
                                            $("label[for='alrFieldSelM']").show();
                                            $("label[for='alrRangeColorM']").parent().show();
                                            $('#alrRangeColorM').show();
                                            $("label[for='alrRangeColorM']").show();
                                            $('#addWidgetRangeTableContainerM').show();
                                            
                                            //POPOLAMENTO DELLA SELECT COI CAMPI
                                            $('#alrAxisSelM').trigger("change");
                                            
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                        }
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        break;
            
                                    case "widgetBarSeries":
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString = metricData.data[0].commit.author.series;
                                        var series = jQuery.parseJSON(seriesString);
                                        var thrTables1 = new Array();
                                        var thrTables2 = new Array();
                                        var thrSeries,i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, colorsTable, newRow, newCell, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer, barsColorsTableContainerM = null;
                                        var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                        var colorsArray = new Array();
                                        
                                        //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                        setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                        
                                        //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                        buildThrTablesForEditWidget();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                        }
                                        
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                        $('#specificParamsM .row').remove();
                                        
                                        //Nuova riga
                                        //Rows labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsFontSizeM" class="col-md-2 control-label">Rows labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSizeM" name="rowsLabelsFontSizeM" required> ');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Rows labels font color
                                        newLabel = $('<label for="rowsLabelsFontColorM" class="col-md-2 control-label">Rows labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColorM" name="rowsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsFontColorContainerM').show();
                                        $('#rowsLabelsFontColorM').show();
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');
                                        $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});

                                        //Nuova riga
                                        //Cols labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="colsLabelsFontSizeM" class="col-md-2 control-label">Cols labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="colsLabelsFontSizeM" name="colsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Cols labels font color
                                        newLabel = $('<label for="colsLabelsFontColorM" class="col-md-2 control-label">Cols labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColorM" name="colsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetColsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsFontColorContainerM').show();
                                        $('#colsLabelsFontColorM').show();
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        $("#widgetColsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Data labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsFontSizeM" class="col-md-2 control-label">Data labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsFontSizeM" name="dataLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Data labels font color
                                        newLabel = $('<label for="dataLabelsFontColorM" class="col-md-2 control-label">Data labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="dataLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="dataLabelsFontColorM" name="dataLabelsFontColorM" required><span class="input-group-addon"><i id="widgetDataLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#dataLabelsFontColorContainerM').show();
                                        $('#dataLabelsFontColorM').show();
                                        $("#widgetDataLabelsFontColorM").css('display', 'block');
                                        $("#widgetDataLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                        
                                        //Nuova riga
                                        //Legend font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="legendFontSizeM" class="col-md-2 control-label">Legend font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="legendFontSizeM" name="legendFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Legend font color
                                        newLabel = $('<label for="legendFontColorM" class="col-md-2 control-label">Legend font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="legendFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColorM" name="legendFontColorM" required><span class="input-group-addon"><i id="widgetLegendFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#legendFontColorContainerM').show();
                                        $('#legendFontColorM').show();
                                        $("#widgetLegendFontColorM").css('display', 'block');
                                        $("#widgetLegendFontColorM").parent().parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                        
                                        //Nuova riga
                                        //Bars colors
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="barsColorsSelectM" class="col-md-2 control-label">Bars colors</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="barsColorsSelectM" name="barsColorsSelectM"></select>');
                                        newSelect.append('<option value="manual">Manual</option>');
                                        newSelect.append('<option value="auto">Automatic</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        $('#barsColorsSelectM').change(function() 
                                        {
                                            if($('#barsColorsSelectM').val() === "manual")
                                            {
                                                $('#barsColorsTableContainerM').show();
                                            }
                                            else
                                            {
                                                $('#barsColorsTableContainerM').hide();
                                            }
                                        });

                                        //Chart type
                                        newLabel = $('<label for="chartTypeM" class="col-md-2 control-label">Chart type</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="chartTypeM" name="chartTypeM"></select>');
                                        newSelect.append('<option value="horizontal">Horizontal bars</option>');
                                        newSelect.append('<option value="horizontalStacked">Horizontal stacked bars</option>');
                                        newSelect.append('<option value="vertical">Vertical bars</option>');
                                        newSelect.append('<option value="verticalStacked">Vertical stacked bars</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Nuova riga
                                        //Data labels
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsM" class="col-md-2 control-label">Data labels</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="dataLabelsM" name="dataLabelsM">');
                                        newSelect.append('<option value="no">No data labels</option>');
                                        newSelect.append('<option value="value">Value only</option>');
                                        newSelect.append('<option value="full">Field name and value</option>');
                                        newSelect.val("value");
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Data labels rotation
                                        newLabel = $('<label for="dataLabelsRotation" class="col-md-2 control-label">Data labels rotation</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="dataLabelsRotation" name="dataLabelsRotation"> ');
                                        newSelect.append('<option value="horizontal">Horizontal</option>');
                                        newSelect.append('<option value="verticalAsc">Vertical ascending</option>');
                                        newSelect.append('<option value="verticalDesc">Vertical descending</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Parametri specifici del widget
                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                            $("label[for='legendFontSizeM']").parent().show();
                                            $("#dataLabelsFontSizeM").val(styleParameters.dataLabelsFontSize);
                                            $("#dataLabelsFontColorM").val(styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").css("background-color", styleParameters.dataLabelsFontColor);
                                            $("#widgetDataLabelsFontColorM").parent().parent().colorpicker({color: styleParameters.dataLabelsFontColor});
                                            $("#legendFontSizeM").val(styleParameters.legendFontSize);
                                            $("#legendFontColorM").val(styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").css("background-color", styleParameters.legendFontColor);
                                            $("#widgetLegendFontColorM").parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                            $("#barsColorsSelectM").val(styleParameters.barsColorsSelect);
                                            $("#chartTypeM").val(styleParameters.chartType);
                                            $("#dataLabelsM").val(styleParameters.dataLabels);
                                            $("#dataLabelsRotationM").val(styleParameters.dataLabelsRotation);
                                        }
                                        
                                        //Contenitore per tabella dei colori
                                        barsColorsTableContainerM = $('<div id="barsColorsTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(barsColorsTableContainerM);
                                        
                                        function updateWidgetBarSeriesColorsM(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray[index] = newColor;
                                            $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        }
                                        
                                        colorsTable = $("<table class='table table-bordered table-condensed thrRangeTable'><tr><td>Series</td><td>Color</td></tr></table>");
                                        for(var i in series.secondAxis.labels)
                                        {
                                            newRow = $('<tr></tr>');
                                            newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                            newRow.append(newCell);
                                            newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                            newRow.append(newCell);
                                            //Se l'attuale impostazione è per colori automatici, costruiamo JSON e tabella GUI con impostazioni di default, altrimenti con colori da DB
                                            if(styleParameters.barsColorsSelect === 'auto')
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: defaultColorsArray[i%10]});
                                                colorsArray.push(defaultColorsArray[i%10]);
                                            }
                                            else
                                            {
                                                newRow.find('div.colorPicker').colorpicker({color: styleParameters.barsColors[i]});
                                                colorsArray.push(styleParameters.barsColors[i]);
                                            }
                                            
                                            newRow.find('div.colorPicker').on('changeColor', updateWidgetBarSeriesColorsM); 
                                            colorsTable.append(newRow);
                                        }
                                        
                                        $("#barsColorsM").val(JSON.stringify(colorsArray));
                                        $('#barsColorsTableContainerM').append(colorsTable);
                                        
                                        //Per prima visualizzazione in edit
                                        if($('#barsColorsSelectM').val() === "manual")
                                        {
                                            $('#barsColorsTableContainerM').show();
                                        }
                                        else
                                        {
                                            $('#barsColorsTableContainerM').hide();
                                        } 
                                        
                                        //Codice di creazione soglie
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                        newLabel = $('<label for="alrAxisSelM" class="col-md-2 control-label">Thresholds target set</label>');
                                        newSelect = $('<select class="form-control" id="alrAxisSelM" name="alrAxisSelM"></select>');
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        newSelect.val(-1);
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Nuova riga
                                        //Threshold field select
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                        addWidgetRangeTableContainerM.hide();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSelM').change(alrThrFlagMListener);
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrSelM').val("no");
                                            $("label[for='alrAxisSelM']").hide();
                                            $('#alrAxisSelM').val(-1);
                                            $('#alrAxisSelM').hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrSelM').val("yes");
                                            $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                            $("label[for='alrAxisSelM']").show();
                                            $('#alrAxisSelM').parent().show();
                                            $('#alrAxisSelM').show();
                                            $("label[for='alrFieldSelM']").show();
                                            $('#alrFieldSelM').parent().show();
                                            $('#alrFieldSelM').show();
                                            //POPOLAMENTO DELLA SELECT DEI CAMPI
                                            alrAxisSelMListener();
                                            $('#addWidgetRangeTableContainerM').show();
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                            //Listener per settaggio/desettaggio campi in base ad asse selezionato
                                            $('#alrAxisSelM').change(alrAxisSelMListener);
                                            //Listener per selezione campo
                                            $('#alrFieldSelM').change(alrFieldSelMListener);
                                        }
                                        
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
            
                                    case "widgetTable":
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString = metricData.data[0].commit.author.series;
                                        var series = jQuery.parseJSON(seriesString);
                                        var thrTables1 = new Array();
                                        var thrTables2 = new Array();
                                        var i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, thrSeries, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                        
                                        //Funzione di settaggio dei globals per il file dashboard_configdash.js
                                        setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                        
                                        //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                        buildThrTablesForEditWidget();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, data['info_mess']);
                                        
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                        }
                                        
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $("#urlWidgetM").prop('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        //RIMOZIONE CAMPI PER TUTTI GLI ALTRI WIDGET
                                        $('#specificParamsM .row').remove();
                                        
                                        //Visualizzazione campi specifici per questo widget
                                        //Nuova riga
                                        //Select show first cell
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="showTableFirstCellM" class="col-md-2 control-label">Show first cell</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="showTableFirstCellM" name="showTableFirstCellM" required></select>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //First cell font size
                                        //Nuova riga
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="tableFirstCellFontSizeM" class="col-md-2 control-label">First cell font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="tableFirstCellFontSizeM" name="tableFirstCellFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();
                                        
                                        //First cell font color
                                        newLabel = $('<label for="tableFirstCellFontColorM" class="col-md-2 control-label">First cell font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="tableFirstCellFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableFirstCellFontColorM" name="tableFirstCellFontColorM" required><span class="input-group-addon"><i id="widgetFirstCellFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#tableFirstCellFontColorContainerM').show();
                                        $('#tableFirstCellFontColorM').show();
                                        $("#widgetFirstCellFontColorM").css('display', 'block');
                                        $("#widgetFirstCellFontColorM").parent().parent().parent().colorpicker({color: styleParameters.tableFirstCellFontColor});
                                        
                                        //Nuova riga
                                        //Rows labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsFontSizeM" class="col-md-2 control-label">Rows labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="rowsLabelsFontSizeM" name="rowsLabelsFontSizeM" required> ');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Rows labels font color
                                        newLabel = $('<label for="rowsLabelsFontColorM" class="col-md-2 control-label">Rows labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsFontColorM" name="rowsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsFontColorContainerM').show();
                                        $('#rowsLabelsFontColorM').show();
                                        $("#widgetRowsLabelsFontColorM").css('display', 'block');
                                        $("#widgetRowsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsFontColor});

                                        //Nuova riga
                                        //Cols labels font size
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="colsLabelsFontSizeM" class="col-md-2 control-label">Cols labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="colsLabelsFontSizeM" name="colsLabelsFontSizeM" required>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Cols labels font color
                                        newLabel = $('<label for="colsLabelsFontColorM" class="col-md-2 control-label">Cols labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsFontColorM" name="colsLabelsFontColorM" required><span class="input-group-addon"><i id="widgetColsLabelsFontColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsFontColorContainerM').show();
                                        $('#colsLabelsFontColorM').show();
                                        $("#widgetColsLabelsFontColorM").css('display', 'block');
                                        $("#widgetColsLabelsFontColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsFontColor});
                                    
                                        //Nuova riga
                                        //Rows labels background color
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="rowsLabelsBckColorM" class="col-md-2 control-label">Rows labels background color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="rowsLabelsBckColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="rowsLabelsBckColorM" name="rowsLabelsBckColorM" required><span class="input-group-addon"><i id="widgetRowsLabelsBckColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#rowsLabelsBckColorContainerM').show();
                                        $('#rowsLabelsBckColorM').show();
                                        $("#widgetRowsLabelsBckColorM").css('display', 'block');
                                        $("#widgetRowsLabelsBckColorM").parent().parent().parent().colorpicker({color: styleParameters.rowsLabelsBckColor});


                                        //Cols labels background color
                                        newLabel = $('<label for="colsLabelsBckColorM" class="col-md-2 control-label">Cols labels background color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="colsLabelsBckColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="colsLabelsBckColorM" name="colsLabelsBckColorM" required><span class="input-group-addon"><i id="widgetColsLabelsBckColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#colsLabelsBckColorContainerM').show();
                                        $('#colsLabelsBckColorM').show();
                                        $("#widgetColsLabelsBckColorM").css('display', 'block');
                                        $("#widgetColsLabelsBckColorM").parent().parent().parent().colorpicker({color: styleParameters.colsLabelsBckColor});
                                        
                                        //Nuova riga
                                        //Table borders
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="tableBordersM" class="col-md-2 control-label">Table borders</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="tableBordersM" name="tableBordersM"></select>');
                                        newSelect.append('<option value="no">No borders</option>');
                                        newSelect.append('<option value="horizontal">Horizontal borders only</option>');
                                        newSelect.append('<option value="all">All borders</option>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Table borders color
                                        newLabel = $('<label for="tableBordersColorM" class="col-md-2 control-label">Table borders color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="tableBordersColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="tableBordersColorM" name="tableBordersColorM" required><span class="input-group-addon"><i id="widgetTableBordersColorM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        $('#tableBordersColorContainerM').show();
                                        $('#tableBordersColorM').show();
                                        $("#widgetTableBordersColorM").css('display', 'block');
                                        $("#widgetTableBordersColorM").parent().parent().parent().colorpicker({color: styleParameters.tableBordersColor});

                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#showTableFirstCellM").val(styleParameters.showTableFirstCell);
                                            $("#tableFirstCellFontSizeM").val(styleParameters.tableFirstCellFontSize);
                                            $("#tableFirstCellFontColorM").val(styleParameters.tableFirstCellFontColor);
                                            $("#widgetFirstCellFontColorM").css("background-color", styleParameters.tableFirstCellFontColor);
                                            $("#rowsLabelsFontSizeM").val(styleParameters.rowsLabelsFontSize);
                                            $("#rowsLabelsFontColorM").val(styleParameters.rowsLabelsFontColor);
                                            $("#widgetRowsLabelsFontColorM").css("background-color", styleParameters.rowsLabelsFontColor);
                                            $("#colsLabelsFontSizeM").val(styleParameters.colsLabelsFontSize);
                                            $("#colsLabelsFontColorM").val(styleParameters.colsLabelsFontColor);
                                            $("#widgetColsLabelsFontColorM").css("background-color", styleParameters.colsLabelsFontColor);
                                            $("#rowsLabelsBckColorM").val(styleParameters.rowsLabelsBckColor);
                                            $("#widgetRowsLabelsBckColorM").css("background-color", styleParameters.rowsLabelsBckColor);
                                            $("#colsLabelsBckColorM").val(styleParameters.colsLabelsBckColor);
                                            $("#widgetColsLabelsBckColorM").css("background-color", styleParameters.colsLabelsBckColor);
                                            $("#tableBordersM").val(styleParameters.tableBorders);
                                            $("#tableBordersColorM").val(styleParameters.tableBordersColor);
                                            $("#widgetTableBordersColorM").css("background-color", styleParameters.tableBordersColor);
                                        }
                                        
                                        //Nuova riga
                                        //Set thresholds
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newSelect.val('no');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                        $('#alrThrSelM').change(alrThrFlagMListener);
                                        
                                        //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                        newLabel = $('<label for="alrAxisSelM" class="col-md-2 control-label">Thresholds target set</label>');
                                        newSelect = $('<select class="form-control" id="alrAxisSelM" name="alrAxisSelM"></select>');
                                        newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                        newSelect.append("<option value='" + series.secondAxis.desc + "'>" + series.secondAxis.desc + "</option>");
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Nuova riga
                                        //Threshold field select
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                        newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.hide();
                                        newInnerDiv.hide();
                                        newSelect.hide();
                                        
                                        //Contenitore per tabella delle soglie
                                        addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                        $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                        addWidgetRangeTableContainerM.hide();
                                        
                                        if(currentParams === null)
                                        {
                                            $('#alrThrSelM').val("no");
                                            $("label[for='alrAxisSelM']").hide();
                                            $('#alrAxisSelM').val(-1);
                                            $('#alrAxisSelM').hide();
                                            $('#parametersM').val('');
                                        }
                                        else
                                        {
                                            //ESPOSIZIONE DEI CAMPI
                                            $('#alrThrSelM').val("yes");
                                            $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                            $("label[for='alrAxisSelM']").show();
                                            $('#alrAxisSelM').parent().show();
                                            $('#alrAxisSelM').show();
                                            $('#alrAxisSelM').change(alrAxisSelMListener);
                                            $("label[for='alrFieldSelM']").show();
                                            $('#alrFieldSelM').parent().show();
                                            $('#alrFieldSelM').show();
                                            //POPOLAMENTO DELLA SELECT DEI CAMPI
                                            $('#alrAxisSelM').trigger("change");
                                            $('#alrFieldSelM').change(alrFieldSelMListener);
                                            $('#addWidgetRangeTableContainerM').show();
                                            $('#parametersM').val(JSON.stringify(currentParams));
                                        }
                                        break;
            
                                    case "widgetProcess":
                                        //In questo caso mostriamo tutti i campi ad hoc del Process, sicuramente valorizzati in modifica
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, data['info_mess']);
                                        
                                        $("#xAxisDatasetM").prop('required', false);
                                        $("#lineWidthM").prop('required', false);
                                        $("#alrLookM").prop('required', false);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
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
                                        
                                        //Campo scheduler
                                        $("#schedulerRowM").css("display", "");
                                        $("label[for='inputSchedulerWidgetM']").css("display", "");
                                        $('#inputSchedulerWidgetDivM').css("display", "");
                                        $('#inputSchedulerWidgetGroupDivM').css("display", "");
                                        $('#inputSchedulerWidgetM').css("display", "");
                                        $('#inputSchedulerWidgetM').val(parameters.schedulerName);
                                        $('#inputSchedulerWidgetM').trigger("change");
                                        
                                        //Job areas solo se presente
                                        if(parameters.jobArea !== "")
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
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw);
                                        }
                                       
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').val('');
                                        $('#inputTitleWidgetM').attr('disabled', true);
                                        $('#inputTitleWidgetM').prop('required', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val("");
                                        $("#widgetFontColorM").parent().parent().colorpicker({color: "#eeeeee"});
                                        $("#widgetFontColorM").css("background-color", "#eeeeee");
                                        $('#inputFontColorM').val("");
                                        $('#inputFontColorM').attr('disabled', true);
                                        $('#inputFontColorM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').prop('required', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').prop('required', false);
                                        $('#inputHeaderFontColorWidgetM').val("");
                                        $('#widgetHeaderFontColorM').css("background-color", "#eeeeee");
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        //Nuova riga
                                        //Default tab
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="inputDefaultTabM" class="col-md-2 control-label">Default tab</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputDefaultTabM" name="inputDefaultTabM"></select>');
                                        newSelect.append('<option value="0">General</option>');
                                        newSelect.append('<option value="1">Meteo</option>');
                                        newSelect.append('<option value="-1">None (automatic switch)</option>');
                                        newSelect.val(data['defaultTab']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Meteo tab font size
                                       newLabel = $('<label for="meteoTabFontSizeM" class="col-md-2 control-label">Meteo tab font size</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newInput = $('<input type="text" class="form-control" id="meteoTabFontSizeM" name="meteoTabFontSizeM"></input>');
                                       //newInput.val("10");
                                       newInnerDiv.append(newInput);
                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.show();
                                       newInnerDiv.show();
                                       newInput.show();

                                       //Nuova riga
                                       //General tab font size
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel = $('<label for="genTabFontSizeM" class="col-md-2 control-label">General tab font size</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newInput = $('<input type="text" class="form-control" id="genTabFontSizeM" name="genTabFontSizeM"></input>');
                                       //newInput.val("12");
                                       newInnerDiv.append(newInput);
                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.show();
                                       newInnerDiv.show();
                                       newInput.show();

                                       //General tab font color
                                       newLabel = $('<label for="genTabFontColorM" class="col-md-2 control-label">General tab font color</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newInput = $('<div id="genTabFontColorContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="genTabFontColorM" name="genTabFontColorM" required><span class="input-group-addon"><i id="widgetGenTabFontColorM"></i></span></div>');
                                       newInnerDiv.append(newInput);
                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       newLabel.show();
                                       newInnerDiv.show();
                                       $('#genTabFontColorContainerM').show();
                                       $('#genTabFontColorM').show();
                                       $("#genTabFontColorM").css('display', 'block');
                                       $("#genTabFontColorM").prop("required", true);
                                       $("#genTabFontColorM").attr("disabled", false);
                                       
                                        if(styleParamsRaw !== null) 
                                        {
                                            $("#meteoTabFontSizeM").val(styleParameters.meteoTabFontSize);
                                            $("#genTabFontSizeM").val(styleParameters.genTabFontSize);
                                            $("#widgetGenTabFontColorM").parent().parent().colorpicker({color: styleParameters.genTabFontColor});
                                        }
                                        
                                        break;
                                        
                                    case "widgetButton":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').val('');
                                        $('#inputFreqWidgetM').prop('disabled', true);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').prop('required', false);
                                        $('#inputHeaderFontColorWidgetM').val("");
                                        $('#widgetHeaderFontColorM').css("background-color", "#eeeeee");
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                       //Nuova riga
                                       //Target widgets geolocation
                                       newFormRow = $('<div class="row"></div>');
                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select name="editWidgetGeolocationWidgets" class="form-control" id="editWidgetGeolocationWidgets" multiple></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetsJson));
                                       
                                       //console.log($("#parametersM").val());
                                       
                                       $("li.gs_w").each(function(){
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          $('#editWidgetGeolocationWidgets').selectpicker({
                                             width: 110
                                          });
                                          
                                          $('#editWidgetGeolocationWidgets').selectpicker('val', targetsJson);

                                          $('#editWidgetGeolocationWidgets').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetsJson = [];
                                             }
                                             else
                                             {
                                                targetsJson = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetsJson));
                                          });
                                       }
                                       break;
                                        
                                    case "widgetGenericContent":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;        
                                        
                                    case "widgetSingleContent":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').attr('disabled', false);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontColorM').attr('disabled', false);
                                        $('#inputFontColorM').prop('required', true);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", true);
                                        $('#inputUdmWidgetM').attr("disabled", false);
                                        $('#inputUdmPositionM').prop("required", true);
                                        $('#inputUdmPositionM').attr("disabled", false);
                                        $("#inputUdmPositionM").val(data['udmPos']);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetBarContent":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetColunmContent":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetGaugeChart":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetPieChart":
                                        var metricId = $('#metricWidgetM').val();
                                        var metricData = getMetricData(metricId);
                                        var seriesString, series, newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, newHiddenColors, newColorsTableContainer, newColorsTable1Container, newColorsTable2Container, colorsTable1M, colorsTable2M, valuesNum, newRow, newCell, thrSimpleTables, thrTables1, thrTables2, i, j, k, min, max, color, newTableRow, newTableCell, currentFieldIndex, currentSeriesIndex, thrSeries, colorsTable = null;
                                        var defaultColorsArray = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'];
                                        var colorsArray1M = [];
                                        var colorsArray2M = [];
                                        var table1Shown = false;
                                        var table2Shown = false;
                                        
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').val("");
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontColorM').val("");
                                        $('#inputFontColorM').attr('disabled', true);
                                        $('#inputFontColorM').prop('required', false);
                                        $('#widgetFontColorM').css("background-color", "#EEEEEE");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        }
                                        else
                                        {
                                            seriesString = metricData.data[0].commit.author.series;
                                            series = jQuery.parseJSON(seriesString);
                                            showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, series, infoJson, info_mess);
                                        }
                                        
                                        if(styleParamsRaw !== null) 
                                        {
                                            styleParameters = JSON.parse(styleParamsRaw); 
                                        }
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        removeWidgetProcessGeneralFields("editWidget");
                                        
                                        //Legend font size e font color
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="legendFontSizeM" class="col-md-2 control-label">Legend font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="legendFontSizeM" name="legendFontSizeM" required>');
                                        newInput.val(styleParameters.legendFontSize);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel = $('<label for="legendFontColorM" class="col-md-2 control-label">Legend font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<div id="legendFontColorPickerContainerM" class="input-group"><input type="text" class="form-control demo-1 demo-auto" id="legendFontColorPickerM" name="legendFontColorPickerM" required><span class="input-group-addon"><i id="widgetLegendFontColorPickerM"></i></span></div>');
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        $("#widgetLegendFontColorPickerM").css('display', 'block');
                                        $("#widgetLegendFontColorPickerM").parent().parent().parent().colorpicker({color: styleParameters.legendFontColor});
                                        
                                        //Nuova riga
                                        //Datalabels distance (se Percentuale) oppure Datalabels (se Series)
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            newLabel = $('<label for="dataLabelsDistanceM" class="col-md-2 control-label">Data labels distance</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="dataLabelsDistanceM" name="dataLabelsDistanceM" required>');
                                            newInput.val(styleParameters.dataLabelsDistance);
                                            newInnerDiv.append(newInput);
                                        }
                                        else if(metricType === 'Series')
                                        {
                                            newLabel = $('<label for="dataLabelsM" class="col-md-2 control-label">Data labels for <i>' + series.firstAxis.desc + '</i></label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newSelect = $('<select class="form-control" id="dataLabelsM" name="dataLabelsM"></select>');
                                            newSelect.append('<option value="no">No data labels</option>');
                                            newSelect.append('<option value="value">Value only</option>');
                                            newSelect.append('<option value="full">Field name and value</option>');
                                            newSelect.val(styleParameters.dataLabels);
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                        }
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Data labels font size
                                        newLabel = $('<label for="dataLabelsFontSizeM" class="col-md-2 control-label">Data labels font size</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsFontSizeM" name="dataLabelsFontSizeM" required>');
                                        newInput.val(styleParameters.dataLabelsFontSize);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Nuova riga
                                        //Data labels distances per widget series
                                        if(metricType === 'Series')
                                        {
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            //Data labels distance per anello più interno
                                            newLabel = $('<label for="dataLabelsDistance1M" class="col-md-2 control-label">Data labels distance for <i>' + series.secondAxis.desc + '</i></label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="dataLabelsDistance1M" name="dataLabelsDistance1M" required>');
                                            newInput.val(styleParameters.dataLabelsDistance1);
                                            newInnerDiv.append(newInput);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);

                                            //Data labels distance per anello più esterno
                                            newLabel = $('<label for="dataLabelsDistance2M" class="col-md-2 control-label">Data labels distance for <i>' + series.firstAxis.desc + '</i></label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="dataLabelsDistance2M" name="dataLabelsDistance2M" required>');
                                            newInput.val(styleParameters.dataLabelsDistance2);
                                            newInnerDiv.append(newInput);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                        }
                                        
                                        //Nuova riga
                                        //Data labels font color
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="dataLabelsFontColorM" class="col-md-2 control-label">Data labels font color</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInputGroup = $('<div class="input-group color-choice">');
                                        newInput = $('<input type="text" class="form-control" id="dataLabelsFontColorM" name="dataLabelsFontColorM" required>');
                                        newSpan = $('<span class="input-group-addon"><i id="widgetDataLabelsFontColorM"></i></span>');
                                        newInputGroup.append(newInput);
                                        newInputGroup.append(newSpan);
                                        newInnerDiv.append(newInputGroup);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newInnerDiv.colorpicker({color: styleParameters.dataLabelsFontColor});
                                        
                                        //Inner radius 1
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            newLabel = $('<label for="innerRadius1M" class="col-md-2 control-label">Inner radius (%)</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="innerRadius1M" name="innerRadius1M" required>');
                                            newInput.val(styleParameters.innerRadius1);
                                        }
                                        else if(metricType === 'Series')
                                        {
                                            newLabel = $('<label for="innerRadius1M" class="col-md-2 control-label">Inner radius for <i>' + series.secondAxis.desc + '</i> (%)</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="innerRadius1M" name="innerRadius1M" required>');
                                            newInput.val(styleParameters.innerRadius1);
                                        }
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Nuova riga
                                        //Questi raggi sono solo per la versione series
                                        if(metricType === 'Series')
                                        {
                                           //Outer radius 1
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            newLabel = $('<label for="outerRadius1M" class="col-md-2 control-label">Outer radius for <i>' + series.secondAxis.desc + '</i> (%)</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="outerRadius1M" name="outerRadius1M" required>');
                                            newInput.val(styleParameters.outerRadius1);
                                            newInnerDiv.append(newInput);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);

                                            //Inner radius 2
                                            newLabel = $('<label for="innerRadius2M" class="col-md-2 control-label">Inner radius for <i>' + series.firstAxis.desc + '</i> (%)</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInput = $('<input type="text" class="form-control" id="innerRadius2M" name="innerRadius2M" required>');
                                            newInput.val(styleParameters.innerRadius2);
                                            newInnerDiv.append(newInput);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv); 
                                        }
                                        
                                        //Nuova riga
                                        //Start angle
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="startAngleM" class="col-md-2 control-label">Start angle (°)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="startAngleM" name="startAngleM" required>');
                                        newInput.val(styleParameters.startAngle);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);

                                        //End angle
                                        newLabel = $('<label for="endAngleM" class="col-md-2 control-label">End angle (°)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="endAngleM" name="endAngleM" required>');
                                        newInput.val(styleParameters.endAngle);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Nuova riga
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        //Center Y
                                        newLabel = $('<label for="centerYM" class="col-md-2 control-label">Diagram center Y(%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="centerYM" name="centerYM" required>');
                                        newInput.val(styleParameters.centerY);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        
                                        //Select per colori automatici/manuali
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            newLabel = $('<label for="colorsSelect1M" class="col-md-2 control-label">Slices colors</label>');
                                        }
                                        else if(metricType === 'Series')
                                        {
                                            newLabel = $('<label for="colorsSelect1M" class="col-md-2 control-label">Slices colors for <i>' + series.secondAxis.desc + '</i></label>');
                                        }
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="colorsSelect1M" name="colorsSelect1M"></select>');
                                        newSelect.append('<option value="auto">Automatic</option>');
                                        newSelect.append('<option value="manual">Manual</option>');
                                        newSelect.val(styleParameters.colorsSelect1);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);

                                        //Campo hidden per i colori
                                        newHiddenColors = $('<input type="hidden" id="colors1M" name="colors1M">');
                                        $("#specificParamsM").append(newHiddenColors);
                                        newHiddenColors = $('<input type="hidden" id="colors2M" name="colors2M">');
                                        $("#specificParamsM").append(newHiddenColors);

                                        //Div contenitore per la tabella dei colori
                                        newColorsTableContainer = $('<div id="colorsTableContainerM" class="row rowCenterContent" style="width: 100%; margin-left: 0px;"></div>');
                                        newColorsTable1Container = $('<div id="colorsTable1ContainerM" style="width: 100%; float: left;"></div>');
                                        newColorsTable2Container = $('<div id="colorsTable2ContainerM" style="width: 100%; float: left;"></div>');
                                        newColorsTableContainer.append(newColorsTable1Container);
                                        
                                        function updateWidgetPieChartColors1M(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray1M[index] = newColor;
                                            $("#colors1M").val(JSON.stringify(colorsArray1M));
                                        }
                                        
                                        function updateWidgetPieChartColors2M(e, params)
                                        {
                                            var newColor = $(this).colorpicker('getValue');
                                            var index = parseInt($(this).parents('tr').index() - 1);
                                            colorsArray2M[index] = newColor;
                                            $("#colors2M").val(JSON.stringify(colorsArray2M));
                                        }
                                        
                                        //Aggiunta al form dei campi per parametri specifici
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            $("#specificParamsM").append(newColorsTableContainer);
                                            
                                            newColorsTable1Container.hide();
                                            newColorsTableContainer.hide();
                                            
                                            //Form per dati tradizionali percentuali
                                            var valuePerc = [];
                                            var descriptions = [];
                                            valuePerc[0] = metricData.data[0].commit.author.value_perc1;
                                            valuePerc[1] = metricData.data[0].commit.author.value_perc2;
                                            valuePerc[2] = metricData.data[0].commit.author.value_perc3;
                                            
                                            colorsTable1M = $("<table class='table table-bordered table-condensed thrRangeTable' style='width: 100%;'><tr><td>Field</td><td>Color</td></tr></table>");
                                            
                                            //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                            if((valuePerc[0] !== null) && (valuePerc[1] !== null) && (valuePerc[2] !== null))
                                            {
                                                valuesNum = 3;
                                                descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                                descriptions[1] = metricData.data[0].commit.author.field2Desc;
                                                descriptions[2] = metricData.data[0].commit.author.field3Desc;
                                            }
                                            else if((valuePerc[0] !== null) && (valuePerc[1] !== null) && (valuePerc[2] === null))
                                            {
                                                valuesNum = 2;
                                                descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                                descriptions[1] = metricData.data[0].commit.author.field2Desc;
                                            }
                                            else if((valuePerc[0] !== null) && (valuePerc[1] === null) && (valuePerc[2] === null))
                                            {
                                                valuesNum = 1;
                                                descriptions[0] = metricData.data[0].commit.author.field1Desc;
                                            }
                                            
                                            //Costruzione tabella dei colori iniziale
                                            for(var i = 0; i < valuesNum; i++)
                                            {
                                                colorsArray1M[i] = styleParameters.colors1[i];
                                                newRow = $('<tr></tr>');
                                                //newCell = $('<td>Value ' + parseInt(i+1) + '</td>');
                                                newCell = $('<td>' + descriptions[i] + '</td>');
                                                newRow.append(newCell);
                                                newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newRow.append(newCell);
                                                newRow.find('div.colorPicker').colorpicker({color: colorsArray1M[i]});
                                                newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1M);
                                                colorsTable1M.append(newRow);
                                            }
                                            
                                            if(valuesNum === 1)
                                            {
                                                colorsArray1M[1] = styleParameters.colors1[1];
                                                newRow = $('<tr></tr>');
                                                newCell = $('<td>Complementary</td>');
                                                newRow.append(newCell);
                                                newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newRow.append(newCell);
                                                newRow.find('div.colorPicker').colorpicker({color: colorsArray1M[1]});
                                                newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1M);
                                                colorsTable1M.append(newRow);
                                            }
                                            
                                            $("#colors1M").val(JSON.stringify(colorsArray1M));    
                                            newColorsTable1Container.append(colorsTable1M);   
                                            colorsTable1M.hide();
                                        }
                                        else if(metricType === 'Series')
                                        {
                                            //Nuova riga
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            newLabel = $('<label for="colorsSelect2M" class="col-md-2 control-label">Slices colors for <i>' + series.firstAxis.desc + '</i></label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newSelect = $('<select class="form-control" id="colorsSelect2M" name="colorsSelect2M"></select>');
                                            newSelect.append('<option value="auto">Automatic</option>');
                                            newSelect.append('<option value="manual">Manual</option>');
                                            newSelect.val(styleParameters.colorsSelect2);
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);

                                            newColorsTableContainer.append(newColorsTable2Container);
                                            
                                            $("#specificParamsM").append(newColorsTableContainer);
                                            
                                            newColorsTable1Container.hide();
                                            newColorsTable2Container.hide();
                                            newColorsTableContainer.hide();

                                            //Form per dati Series
                                            colorsTable1M = $("<table class='table table-bordered table-condensed thrRangeTable' style='width: 100%;'><tr><td>Fields of set <b>" + series.secondAxis.desc + "</b></td><td>Color</td></tr></table>");
                                            colorsTable2M = $("<table class='table table-bordered table-condensed thrRangeTable' style='width: 100%;'><tr><td>Fields of set <b>" + series.firstAxis.desc + "</b></td><td>Color</td></tr></table>");
                                        
                                            //Costruiamo la tabella dei colori e il corrispondente JSON una tantum e mostriamola/nascondiamola a seconda di cosa sceglie l'utente, per non perdere eventuali colori immessi in precedenza.
                                            for(var i = 0; i < series.secondAxis.labels.length; i++)
                                            {
                                                colorsArray1M[i] = styleParameters.colors1[i];
                                                newRow = $('<tr></tr>');
                                                newCell = $('<td>' + series.secondAxis.labels[i] + '</td>');
                                                newRow.append(newCell);
                                                newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newRow.append(newCell);
                                                newRow.find('div.colorPicker').colorpicker({color: colorsArray1M[i]});
                                                newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors1M);
                                                colorsTable1M.append(newRow);
                                            }
                                            
                                            for(var i = 0; i < series.firstAxis.labels.length; i++)
                                            {
                                                colorsArray2M[i] = styleParameters.colors2[i];
                                                newRow = $('<tr></tr>');
                                                newCell = $('<td>' + series.firstAxis.labels[i] + '</td>');
                                                newRow.append(newCell);
                                                newCell = $('<td><div class="input-group colorPicker"><input type="text" class="form-control"><span class="input-group-addon"><i class="thePicker"></i></span></div></td>');
                                                newRow.append(newCell);
                                                newRow.find('div.colorPicker').colorpicker({color: colorsArray2M[i]});
                                                newRow.find('div.colorPicker').on('changeColor', updateWidgetPieChartColors2M);
                                                colorsTable2M.append(newRow);
                                            }
                                            
                                            $("#colors1M").val(JSON.stringify(colorsArray1M));    
                                            newColorsTable1Container.append(colorsTable1M);
                                            $("#colors2M").val(JSON.stringify(colorsArray2M));    
                                            newColorsTable2Container.append(colorsTable2M);
                                            colorsTable1M.hide();
                                            colorsTable2M.hide();
                                        }
                                        
                                        if($("#colorsSelect1M").val() === 'manual')
                                        {
                                            $('#colorsTableContainerM').css("display", "block");
                                            $('#colorsTable1ContainerM').css("display", "block");
                                            colorsTable1M.css("display", "");
                                            table1Shown = true;
                                        }
                                        else
                                        {
                                            colorsTable1M.css("display", "none");
                                            $('#colorsTable1ContainerM').css("display", "none");
                                            table1Shown = false;
                                            if(table2Shown === false)
                                            {
                                                $('#colorsTableContainerM').css("display", "none");
                                            }
                                        }
                                        
                                        if(metricType === 'Series')
                                        {
                                            if($("#colorsSelect2M").val() === 'manual')
                                            {
                                                $('#colorsTableContainerM').css("display", "block");
                                                $('#colorsTable2ContainerM').css("display", "block");
                                                colorsTable2M.css("display", "");
                                                table2Shown = true;
                                            }
                                            else
                                            {
                                                colorsTable2M.css("display", "none");
                                                $('#colorsTable2ContainerM').css("display", "none");
                                                table2Shown = false;
                                                if(table1Shown === false)
                                                {
                                                    $('#colorsTableContainerM').css("display", "none");
                                                }
                                            }
                                        }
                                        
                                        $('#colorsSelect1M').change(function () 
                                        {
                                            if($('#colorsSelect1M').val() === "manual")
                                            {
                                                $('#colorsTableContainerM').css("display", "block");
                                                $('#colorsTable1ContainerM').css("display", "block");
                                                colorsTable1M.css("display", "");
                                                table1Shown = true;
                                            }
                                            else
                                            {
                                                colorsTable1M.css("display", "none");
                                                $('#colorsTable1ContainerM').css("display", "none");
                                                table1Shown = false;
                                                if(table2Shown === false)
                                                {
                                                    $('#colorsTableContainerM').css("display", "none");
                                                }
                                            }
                                        });

                                        if(metricType === 'Series')
                                        {
                                            $('#colorsSelect2M').change(function () 
                                            {
                                                if($('#colorsSelect2M').val() === "manual")
                                                {
                                                    $('#colorsTableContainerM').css("display", "block");
                                                    $('#colorsTable2ContainerM').css("display", "block");
                                                    colorsTable2M.css("display", "");
                                                    table2Shown = true;
                                                }
                                                else
                                                {
                                                    colorsTable2M.css("display", "none");
                                                    $('#colorsTable2ContainerM').css("display", "none");
                                                    table2Shown = false;
                                                    if(table1Shown === false)
                                                    {
                                                        $('#colorsTableContainerM').css("display", "none");
                                                    }
                                                }
                                            });
                                        }
                                        
                                        //Codice costruzione soglie
                                        if(metricType.indexOf('Percentuale') >= 0)
                                        {
                                            //Tabelle editabili delle soglie
                                            thrSimpleTables = new Array();

                                            setSimpleGlobals(currentParams, thrSimpleTables, valuePerc, descriptions);

                                            //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                            buildThrTablesForEditWidgetSimple();

                                            //Nuova riga
                                            //Set thresholds
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                            newSelect.append('<option value="yes">Yes</option>');
                                            newSelect.append('<option value="no">No</option>');
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                            newLabel.show();
                                            newInnerDiv.show();
                                            newSelect.show();

                                            //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                            $('#alrThrSelM').change(alrThrFlagMListenerSimple);

                                            //Threshold field select
                                            newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                            newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                            newLabel.hide();
                                            newInnerDiv.hide();
                                            newSelect.hide();

                                            //Contenitore per tabella delle soglie
                                            addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                            $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                            addWidgetRangeTableContainerM.hide();
                                            
                                            if(currentParams === null)
                                            {
                                                $('#alrThrSelM').val("no");
                                                $('#parametersM').val('');
                                            }
                                            else
                                            {
                                                //ESPOSIZIONE DEI CAMPI
                                                $('#alrThrSelM').val("yes");
                                                $("label[for='alrFieldSelM']").show();
                                                $('#alrFieldSelM').parent().show();
                                                $('#alrFieldSelM').show();
                                                //POPOLAMENTO DELLA SELECT DEI CAMPI
                                                for(var i = 0; i < descriptions.length; i++)
                                                {
                                                    $('#alrFieldSelM').append("<option value='" + descriptions[i] + "'>" + descriptions[i] + "</option>");
                                                }
                                                $('#alrFieldSelM').val(-1);
                                                $('#addWidgetRangeTableContainerM').show();
                                                $('#parametersM').val(JSON.stringify(currentParams));
                                                //Listener per selezione campo
                                                $('#alrFieldSelM').change(alrFieldSelMListenerSimple);
                                            }
                                        }
                                        else if(metricType === 'Series')
                                        {
                                            thrTables1 = new Array();
                                            thrTables2 = new Array();
                                            setGlobals(currentParams, thrTables1, thrTables2, series, $('#select-widget-m').val());
                                            
                                            //Costruzione THRTables dai parametri provenienti da DB (vuote se non ci sono soglie per quel campo, anche nel caso di nessuna soglia settata in assoluto
                                            buildThrTablesForEditWidget();
                                            
                                            //Codice di creazione soglie
                                            //Nuova riga
                                            //Set thresholds
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            newLabel = $('<label for="alrThrSelM" class="col-md-2 control-label">Set thresholds</label>');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newSelect = $('<select class="form-control" id="alrThrSelM" name="alrThrSelM" required>');
                                            newSelect.append('<option value="yes">Yes</option>');
                                            newSelect.append('<option value="no">No</option>');
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                            newLabel.show();
                                            newInnerDiv.show();
                                            newSelect.show();

                                            //Threshold target select - Questa select viene nascosta o mostrata a seconda che nella "Set thresholds" si selezioni yes o no.
                                            newLabel = $('<label for="alrAxisSelM" class="col-md-2 control-label">Thresholds target set</label>');
                                            newSelect = $('<select class="form-control" id="alrAxisSelM" name="alrAxisSelM"></select>');
                                            newSelect.append("<option value='" + series.firstAxis.desc + "'>" + series.firstAxis.desc + "</option>");
                                            newSelect.val(-1);
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                            newLabel.hide();
                                            newInnerDiv.hide();
                                            newSelect.hide();

                                            //Nuova riga
                                            //Threshold field select
                                            newFormRow = $('<div class="row"></div>');
                                            $("#specificParamsM").append(newFormRow);
                                            newLabel = $('<label for="alrFieldSelM" class="col-md-2 control-label">Thresholds target field</label>');
                                            newSelect = $('<select class="form-control" id="alrFieldSelM" name="alrFieldSelM">');
                                            newInnerDiv = $('<div class="col-md-3"></div>');
                                            newInnerDiv.append(newSelect);
                                            newFormRow.append(newLabel);
                                            newFormRow.append(newInnerDiv);
                                            newLabel.hide();
                                            newInnerDiv.hide();
                                            newSelect.hide();

                                            //Contenitore per tabella delle soglie
                                            addWidgetRangeTableContainerM = $('<div id="addWidgetRangeTableContainerM" class="row rowCenterContent"></div>');
                                            $("#specificParamsM").append(addWidgetRangeTableContainerM);
                                            addWidgetRangeTableContainerM.hide();

                                            //Listener per settaggio/desettaggio soglie relativo alla select "Set thresholds"
                                            $('#alrThrSelM').change(alrThrFlagMListener);

                                            if(currentParams === null)
                                            {
                                                $('#alrThrSelM').val("no");
                                                $("label[for='alrAxisSelM']").hide();
                                                $('#alrAxisSelM').val(-1);
                                                $('#alrAxisSelM').hide();
                                                $('#parametersM').val('');
                                            }
                                            else
                                            {
                                                //ESPOSIZIONE DEI CAMPI
                                                $('#alrThrSelM').val("yes");
                                                $('#alrAxisSelM').val(currentParams.thresholdObject.target);
                                                $("label[for='alrAxisSelM']").show();
                                                $('#alrAxisSelM').parent().show();
                                                $('#alrAxisSelM').show();
                                                $("label[for='alrFieldSelM']").show();
                                                $('#alrFieldSelM').parent().show();
                                                $('#alrFieldSelM').show();
                                                //POPOLAMENTO DELLA SELECT DEI CAMPI
                                                alrAxisSelMListener();
                                                $('#addWidgetRangeTableContainerM').show();
                                                $('#parametersM').val(JSON.stringify(currentParams));
                                                //Listener per settaggio/desettaggio campi in base ad asse selezionato
                                                $('#alrAxisSelM').change(alrAxisSelMListener);
                                                //Listener per selezione campo
                                                $('#alrFieldSelM').change(alrFieldSelMListener);
                                            }
                                        }
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetSce":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetSmartDS":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputComuneWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        if(data['municipality_metric_widget'] === null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetTimeTrend":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').attr('required', true);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                    
                                    case "widgetTimeTrendCompare":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-frameColor-Widget-m').val("4 Ore");
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').attr('required', true);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        if(data['municipality_metric_widget'] === null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetEvents":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', true);
                                        $('#inputFontSizeM').prop('required', true);
                                        $('#inputFontSizeM').prop('disabled', false);
                                        $('#inputFontColorM').val('');
                                        $('#inputFontColorM').prop('required', false);
                                        $('#inputFontColorM').prop('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', true);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                       //Rimozione eventuali campi del subform general per widget process
                                       removeWidgetProcessGeneralFields("editWidget");
                                       
                                       //Parametri specifici del widget
                                       $('#specificParamsM .row').remove();
                                       var newFormRow, newLabel, newInnerDiv, newInputGroup, newSelect, newInput, newSpan, addWidgetRangeTableContainer = null;
                                    
                                       //Nuova riga
                                       newFormRow = $('<div class="row"></div>');
                                       $("#specificParamsM").append(newFormRow);

                                       newLabel = $('<label for="editWidgetGeolocationWidgets" class="col-md-2 control-label">Available geolocation widgets</label>');
                                       newInnerDiv = $('<div class="col-md-3"></div>');
                                       newSelect = $('<select class="form-control" id="editWidgetGeolocationWidgets" name="editWidgetGeolocationWidgets"></select>');

                                       var widgetId, widgetTitle = null;
                                       var widgetsNumber = 0;
                                       
                                       //JSON degli eventi da mostrare su ogni widget target di questo widget events
                                       var targetEventsJson = currentParams;
                                       $("#parametersM").val(JSON.stringify(targetEventsJson));

                                       $("li.gs_w").each(function(){
                                          //if($(this).attr("id").includes("EventsGeoLocation"))
                                          if($(this).attr("id").includes("ExternalContent"))
                                          {
                                             widgetId = $(this).attr("id");
                                             widgetTitle = $(this).find("div.titleDiv").html();
                                             newSelect.append('<option value="' + widgetId + '">' + widgetTitle + '</option>');
                                             widgetsNumber++;
                                          }
                                       });

                                       if(widgetsNumber > 0)
                                       {
                                          newInnerDiv.append(newSelect);
                                       }
                                       else
                                       {
                                          newInnerDiv.append("None");
                                       }

                                       newFormRow.append(newLabel);
                                       newFormRow.append(newInnerDiv);
                                       $("#specificParamsM").append(newFormRow);
                                       newLabel.show();
                                       newInnerDiv.show();

                                       if(widgetsNumber > 0)
                                       {
                                          newSelect.show();
                                          newSelect.val(-1);
                                          newLabel = $('<label for="editWidgetEventTypes" class="col-md-2 control-label">Events to show on selected map</label>');
                                          newInnerDiv = $('<div class="col-md-3"></div>');
                                          var eventTypeSelect = $('<select name="editWidgetEventTypes" class="form-control" id="editWidgetEventTypes" multiple></select>');
                                          eventTypeSelect.append('<option value="Altri eventi">Altri eventi</option>');
                                          eventTypeSelect.append('<option value="Aperture straordinarie, visite guidate">Aperture straordinarie, visite guidate</option>');
                                          eventTypeSelect.append('<option value="Estate Fiorentina">Estate Fiorentina</option>');
                                          eventTypeSelect.append('<option value="Fiere, mercati">Fiere, mercati</option>');
                                          eventTypeSelect.append('<option value="Film festival">Film festival</option>');
                                          eventTypeSelect.append('<option value="Mostre">Mostre</option>');
                                          eventTypeSelect.append('<option value="Musica classica, opera e balletto">Musica classica, opera e balletto</option>');
                                          eventTypeSelect.append('<option value="Musica rock, jazz, pop, contemporanea">Musica rock, jazz, pop, contemporanea</option>');
                                          eventTypeSelect.append('<option value="News">News</option>');
                                          eventTypeSelect.append('<option value="Readings, Conferenze, Convegni">Readings, Conferenze, Convegni</option>');
                                          eventTypeSelect.append('<option value="Readings, incontri letterari, conferenze">Readings, incontri letterari, conferenze</option>');
                                          eventTypeSelect.append('<option value="Sport">Sport</option>');
                                          eventTypeSelect.append('<option value="Teatro">Teatro</option>');
                                          eventTypeSelect.append('<option value="Tradizioni popolari">Tradizioni popolari</option>');
                                          eventTypeSelect.append('<option value="Walking">Walking</option>');
                                          eventTypeSelect.val(-1);
                                          newFormRow.append(newLabel);
                                          newInnerDiv.append(eventTypeSelect);
                                          newFormRow.append(newInnerDiv);
                                          newLabel.hide();
                                          newInnerDiv.hide();

                                          $('#editWidgetEventTypes').selectpicker({
                                             width: 110
                                          });

                                          $('#editWidgetEventTypes').on('changed.bs.select', function (e) 
                                          {
                                             if($(this).val() === null)
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = [];
                                             }
                                             else
                                             {
                                                targetEventsJson[$("#editWidgetGeolocationWidgets").val()] = $(this).val();
                                             }
                                             $("#parametersM").val(JSON.stringify(targetEventsJson));
                                          });

                                          $("#editWidgetGeolocationWidgets").change(function(){
                                             newLabel.show();
                                             newInnerDiv.show();
                                             $('#editWidgetEventTypes').selectpicker('val', targetEventsJson[$("#editWidgetGeolocationWidgets").val()]);
                                          });
                                       }
                                        break;
                                        
                                    case "widgetTrendMentions":
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#inputComuneWidgetM').attr("disabled", true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        //Nuova riga
                                        //Default tab
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="inputDefaultTabM" class="col-md-2 control-label">Default tab</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputDefaultTabM" name="inputDefaultTabM"></select>');
                                        newSelect.append('<option value="0">Trends</option>');
                                        newSelect.append('<option value="1">Quotes</option>');
                                        newSelect.append('<option value="-1">None (automatic switch)</option>');
                                        newSelect.val(data['defaultTab']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetPrevMeteo":
                                        $("#inputComuneRowM").show();
                                        $("label[for='inputComuneWidgetM']").css("display", "");
                                        $("label[for='inputComuneWidgetM']").text("City (autosuggestion)");
                                        $('#inputComuneWidgetM').css("display", "");
                                        $('#inputComuneWidgetM').attr('disabled', false);
                                        $('#inputComuneWidgetM').attr('autocomplete', 'on');
                                        $('#inputComuneWidgetM').prop('required', true);
                                        jQuery.ui.autocomplete.prototype._resizeMenu = function () {
                                            var ul = this.menu.element;
                                            ul.outerWidth(this.element.outerWidth());
                                          };
                                        $('#inputComuneWidgetM').autocomplete({
                                            source: comuniLammaArray
                                        });
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
                                        $("label[for='inputTitleWidgetM']").html("Title");
                                        $("label[for='inputColorWidgetM']").html("Background color");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').prop('required', false);
                                        $('#inputFontSizeM').prop('required', false);
                                        $('#inputFontSizeM').prop('disabled', true);
                                        $('#inputFontSizeM').val('');
                                        $('#inputFontColorM').prop('required', true);
                                        $('#inputFontColorM').prop('disabled', false);
                                        $('#widgetFontColorM').css("background-color", "#" + data['fontColor']);
                                        $('#select-frameColor-Widget-m').attr('disabled', false);
                                        $('#select-frameColor-Widget-m').prop('required', false);
                                        $('#select-frameColor-Widget-m').val('');
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputComuneWidgetM').attr("disabled", false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetServiceMap":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').val("");
                                        $('#inputFreqWidgetM').prop('disabled', true);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', true);
                                        $('#urlWidgetM').prop('required', false);
                                        $('#urlWidgetM').val('');
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        /*if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }*/
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break; 
                                        
                                    case "widgetSpeedometer":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#urlWidgetM').prop('required', false);
                                        $('#urlWidgetM').val('');
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetStateRideAtaf":
                                        //$("#inputComuneRowM").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").css("display", "");
                                        //$("label[for='inputComuneWidgetM']").text("Context");
                                        //$('#inputComuneWidgetM').css("display", "");
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', true);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        //Nuova riga
                                        //Default tab
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="inputDefaultTabM" class="col-md-2 control-label">Default tab</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputDefaultTabM" name="inputDefaultTabM"></select>');
                                        newSelect.append('<option value="0">Stato</option>');
                                        newSelect.append('<option value="1">Linee monitorate</option>');
                                        newSelect.append('<option value="2">Dati</option>');
                                        newSelect.append('<option value="-1">None (automatic switch)</option>');
                                        newSelect.val(data['defaultTab']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;
                                        
                                    case "widgetExternalContent":
                                        $('#inputUrlWidgetM').attr('disabled', false);
                                        $('#inputUrlWidgetM').prop('required', true);
                                        $("#titleLabelM").html("Title"); 
                                        $("#bckColorLabelM").html("Background color");
                                        $('#inputColorWidgetM').val("");
                                        $('#inputColorWidgetM').attr('disabled', false);
                                        $('#inputColorWidgetM').attr('required', true);
                                        $('#color_widget_M').css("background-color", "#eeeeee");
                                        $('#inputFontSizeM').val("");
                                        $('#inputFontSizeM').attr('disabled', true);
                                        $('#inputFontColorM').val("");
                                        $('#inputFontColorM').attr('disabled', true);
                                        $('#widgetFontColorM').css("background-color", "#eeeeee");
                                        $('#link_help_modal-add-widgetM').css("display", "");
                                        $('#inputFrameColorWidgetM').attr('disabled', false);
                                        $('#inputFrameColorWidgetM').val('#eeeeee');
                                        $('#inputFrameColorWidgetM').prop('required', false);
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').attr('disabled', true);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').attr('disabled', true);
                                        $('#inputFreqWidgetM').val("");
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);

                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        
                                        //Nuova riga
                                        //Zoom controls visibility
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="inputControlsVisibilityM" class="col-md-2 control-label">Zoom controls visibility</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputControlsVisibilityM" name="inputControlsVisibilityM"></select>');
                                        newSelect.append('<option value="alwaysVisible">Always visible</option>');
                                        newSelect.append('<option value="hidden">Hidden</option>');
                                        newSelect.val(data['controlsVisibility']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Zoom factor - Lo si edita soltanto dai controlli grafici
                                        newLabel = $('<label for="inputZoomFactorM" class="col-md-2 control-label">Zoom factor (%)</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newInput = $('<input type="text" class="form-control" id="inputZoomFactorM" name="inputZoomFactorM" required>');
                                        newInput.val(data['zoomFactor']*100);
                                        newInput.attr('disabled', true);
                                        newInnerDiv.append(newInput);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newInput.show();

                                        //Nuova riga
                                        //Zoom controls position
                                        newFormRow = $('<div class="row"></div>');
                                        $("#specificParamsM").append(newFormRow);
                                        newLabel = $('<label for="inputControlsPositionM" class="col-md-2 control-label">Zoom controls position</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputControlsPositionM" name="inputControlsPositionM"></select>');
                                        newSelect.append('<option value="topLeft">Top left</option>');
                                        newSelect.append('<option value="topCenter">Top center</option>');
                                        newSelect.append('<option value="topRight">Top right</option>');
                                        newSelect.append('<option value="middleRight">Middle right</option>');
                                        newSelect.append('<option value="bottomRight">Bottom right</option>');
                                        newSelect.append('<option value="bottomMiddle">Bottom middle</option>');
                                        newSelect.append('<option value="bottomLeft">Bottom left</option>');
                                        newSelect.append('<option value="middleLeft">Middle left</option>');
                                        newSelect.val(data['controlsPosition']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();

                                        //Title visibility
                                        newLabel = $('<label for="inputShowTitleM" class="col-md-2 control-label">Title visibility</label>');
                                        newInnerDiv = $('<div class="col-md-3"></div>');
                                        newSelect = $('<select class="form-control" id="inputShowTitleM" name="inputShowTitleM"></select>');
                                        newSelect.append('<option value="yes">Yes</option>');
                                        newSelect.append('<option value="no">No</option>');
                                        newSelect.val(data['showTitle']);
                                        newInnerDiv.append(newSelect);
                                        newFormRow.append(newLabel);
                                        newFormRow.append(newInnerDiv);
                                        newLabel.show();
                                        newInnerDiv.show();
                                        newSelect.show();
                                        
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
                                        break;       
                                    
                                    default:
                                        $('#link_help_modal-add-widget-m').css("display", "");
                                        $('#inputTitleWidgetM').attr('disabled', false);
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
                                        $('#select-IntTemp-Widget-m').val(-1);
                                        $('#select-IntTemp-Widget-m').prop('disabled', false);
                                        $('#select-IntTemp-Widget-m').prop('required', false);
                                        $('#inputFreqWidgetM').prop('disabled', false);
                                        $('#inputFreqWidgetM').prop('required', false);
                                        $('#urlWidgetM').attr('disabled', false);
                                        $('#inputComuneWidgetM').attr("disabled", false);
                                        $('#inputHeaderFontColorWidgetM').attr('disabled', false);
                                        $('#inputHeaderFontColorWidgetM').prop('required', true);
                                        $('#inputUdmWidgetM').prop("required", false);
                                        $('#inputUdmWidgetM').attr("disabled", true);
                                        $('#inputUdmWidgetM').val("");
                                        $('#inputUdmPositionM').prop("required", false);
                                        $('#inputUdmPositionM').attr("disabled", true);
                                        $('#inputUdmPositionM').val(-1);
                                        if(data['municipality_metric_widget'] == null) 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', true);
                                        } 
                                        else 
                                        {
                                            $('#inputComuneWidgetM').attr('disabled', false);
                                        }
                                        
                                        showInfoWCkeditorsM($('#select-widget-m').val(), editorsArrayM, null, null, info_mess);
                                        
                                        //Parametri specifici del widget
                                        $('#specificParamsM .row').remove();
                                        //Rimozione eventuali campi del subform general per widget process
                                        removeWidgetProcessGeneralFields("editWidget");
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
                                    //Div parametri: vecchio codice commentato in attesa di introdurre il nuovo form
                                    //$("#value_range_m").hide();
                                } 
                                else if (data['metrics_prop'][0]['timeRangeOption_metric_widget'] == 1) 
                                {
                                    //Div parametri: vecchio codice commentato in attesa di introdurre il nuovo form
                                    /*$("#value_range_m").show();
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
                                    }*/
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
                                $('#pageWrapperCfg').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
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
                       if($('#inputJobsAreasWidgetM').css("display") !== "none")
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

                    //Handler informazioni generali widget
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
                    //fine handler

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
                    /*$(function () {
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
                    });*/
                    
                   //}//Fine else

                });
        </script>	
    </body>
</html>
       
