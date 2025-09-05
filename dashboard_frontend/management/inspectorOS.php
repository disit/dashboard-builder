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

include_once('../config.php');
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('process-form.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");

//session_start();
checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR);

$lastUsedColors = null;
/*    $dashId = $_REQUEST['dashboardId'];
  $q = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashId'";
  $r = mysqli_query($link, $q);

  if($r)
  {
  $row = mysqli_fetch_assoc($r);

  if($row['deleted'] === 'yes')
  {
  header("Location: ../view/dashboardNotAvailable.php");
  exit();
  }
  else
  {
  $lastUsedColors = json_decode($row['lastUsedColors']);
  }
  } */
?>
<!DOCTYPE HTML>
<html class="dark">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DataInspector</title>
    
    

    <!-- Bootstrap Core CSS -->
    <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php
    echo time();
    ?>" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/chat.css" type="text/css" />

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <!-- jQuery -->

    <script src="../js/jquery-1.10.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Bootstrap Multiselect -->
    <script src="../js/bootstrap-multiselect_1.js"></script>
    <link href="../css/bootstrap-multiselect_1.css" rel="stylesheet">

    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/Select-1.2.5/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/Select-1.2.5/css/select.dataTables.min.css">

    <!-- Select2-->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>  -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">

    <!-- Gridster -->
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <script src="../js/jquery.gridsterMod.js" type="text/javascript" charset="utf-8"></script>

    <!-- New Gridster -->
    <!--<link rel="stylesheet" type="text/css" href="../newGridster/dist/jquery.gridster.css">
    <script src="../newGridster/dist/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>-->

    <!-- CKEditor -->
    <script src="../js/ckeditor/ckeditor.js"></script>
    <link rel="stylesheet" href="../js/ckeditor/skins/moono/editor.css">

    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <!-- JQUERY UI -->
    <!--<script src="../js/jqueryUi/jquery-ui.js"></script>


    <!-- Bootstrap colorpicker -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>

    <!-- Highcharts -->
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>

    <!-- Bootstrap editable tables -->
    <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>

    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>

    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet" />
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>

    <!-- Moment -->
    <script type="text/javascript" src="../moment/moment.js"></script>

    <!-- Bootstrap datetimepicker -->
    <script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="../datetimepicker/build/css/bootstrap-datetimepicker.min.css">

    <!-- Bootstrap toggle button -->
    <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

    <!-- html2canvas -->
    <script type="text/javascript" src="../js/html2canvas.js"></script>

    <!-- Leaflet -->
    <!-- Versione locale: 1.3.1 -->
    <link rel="stylesheet" href="../leafletCore/leaflet.css" />
    <script src="../leafletCore/leaflet.js"></script>

    <!-- Leaflet Wicket: libreria per parsare i file WKT -->
    <script src="../wicket/wicket.js"></script>
    <script src="../wicket/wicket-leaflet.js"></script>

    <!-- Leaflet Zoom Display -->
    <script src="../js/leaflet.zoomdisplay-src.js"></script>
    <link href="../css/leaflet.zoomdisplay.css" rel="stylesheet" />

    <!-- Dot dot dot -->
    <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>

    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet" />

    <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=<?php
    echo time();
    ?>">

    <!-- Text fill -->
    <script src="../js/jquery.textfill.min.js"></script>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
    
    <!-- Custom CSS -->
    <?php include "theme-switcher.php"?>
    
    <link href="../css/widgetCtxMenu_1.css?v=<?php

    echo time();
    ?>" rel="stylesheet">
    <link href="../css/widgetDimControls_1.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/widgetHeader_1.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <script src="../js/widgetsCommonFunctions.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <link href="../css/chat.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <script src="../js/bootstrap-ckeditor-.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>

</head>

<style type="text/css">
    .left {
        float: left;
    }

    .right {
        float: right;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 82px;
        height: 20px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #DBDBDB;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: blue;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(62px);
        -ms-transform: translateX(62px);
        transform: translateX(62px);
    }
    /*------ ADDED CSS ---------*/

    .fixMapon {
        display: none;
    }

    .fixMapon,
    .fixMapoff {
        color: white;
        position: absolute;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
    }

    input:checked+ .slider .on {
        display: block;
    }

    input:checked + .slider .off {
        display: none;
    }
    /*--------- END --------*/
    /* Rounded sliders */

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .dropdown1 {

        overflow:scroll;
        height: 200px;
    }
    
    .ellipsis {
    max-width: 40px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}
</style>

<body style="overflow-y: hidden !important">
  <?php include "../cookie_banner/cookie-banner.php"; ?>

    <!-- Inizio dei modali -->
    <!-- Modale wizard -->
    <div class="modal-content modalContentWizardForm">
        <!--   <div class="modalHeader centerWithFlex">
              &nbsp;&nbsp;&nbsp;
            </div>  -->

        <div id="addWidgetWizardLabelBody" class="body">
            <?php
            $synMode = intval($_REQUEST["synMode"]); include "addWidgetWizardInclusionCodeOS.php"; 
            ?>

            <div>
                <div id="left" class="left">
                    <?php
                    include "../widgets/widgetSingleContent_1.php";
                    ?>

                </div>
                <div id="right" class="right">
                    <?php
                    include "../widgets/widgetTimeTrend_1.php";
                    ?>

                </div>
            </div>
        </div>
    </div>
    <!-- Fine modal content -->

    <!--</div> <!-- Fine modal dialog -->
    <!--</div><!-- Fine modale -->
    <!-- Fine modale wizard -->

    <div id="changeMetricCnt">
        <table id="changeMetricTable" class="addWidgetWizardTable table table-striped dt-responsive nowrap">
            <thead class="widgetWizardColTitle">
                <tr>
                    <th id="hihghLevelTypeColTitle" class="widgetWizardTitleCell" data-cellTitle="HighLevelType"><?= _("High-Level Type")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Nature"><?= _("Nature")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="SubNature"><?= _("Subnature")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueType"><?= _("Value Type")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueName"><?= _("Value Name")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"><?= _("Instance URI")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="DataType"><?= _("Data Type")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastDate"><?= _("Last Date")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastValue"><?= _("Last Value")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Healthiness"><?= _("Healthiness")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri"><?= _("Instance URI")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Parameters"><?= _("Parameters")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Id"><?= _("Id")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastCheck"><?= _("Last Check")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="GetInstances"></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Ownership"><?= _("Ownership")?></th>
                </tr>
            </thead>
        </table>

        <h6>Selected</h6>

        <table id="changeMetricSelectedRowsTable" class="addWidgetWizardTableSelected table table-striped dt-responsive nowrap">
            <thead class="widgetWizardColTitle">
                <tr>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueType"><?= _("Value Type")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueName"><?= _("Value Name")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastValue"><?= _("Last Value")?></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Remove"><?= _("Remove")?></th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Fine dei modali -->
    <!-- MODALE HEALTHINESS -->
    <?php
//if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "Manager")) {
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "Manager")) {
        echo ('<div class="modal fade bd-example-modal-lg" id="healthiness-modal" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg dataSourcesDetails">
                            <div class="modal-content modal-lg">
                                <div class="modal-header centerWithFlex"><b>Data sources Details</b></div>          
                            <div class="modal-body modalBody" role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul id="datasourcesTabsContainer" class="nav nav-tabs nav-justified" role="tablist">
                                        <li role="presentation" id="tab1" class="nav-item active"><a href="#uploadTab" aria-controls="uploadTab" role="tab" data-toggle="tab">Device</a>
                                        </li>
                                        <li role="presentation" id="tab2" class="nav-item"><a href="#browseTab" aria-controls="browseTab" role="tab" data-toggle="tab">Values</a>
                                        </li>
                                        <li role="presentation" id="tab6" class="nav-item"><a href="#HealthinessTab" aria-controls="healthTab" role="tab" data-toggle="tab">Healthiness</a>
                                        </li>
                                        <li role="presentation" id="tab3" class="nav-item"><a href="#processTab" aria-controls="processTab" role="tab" data-toggle="tab">Process</a>
                                        </li>
                                        <li role="presentation" id="tab4" class="nav-item"><a href="#imageTab" aria-controls="imageTab" role="tab" data-toggle="tab">Image</a>
                                        </li>
                                        <li role="presentation" id="tab5" class="nav-item"><a href="#ownerTab" aria-controls="ownerTab" role="tab" data-toggle="tab">Licensing</a>
                                        </li>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin")) {
        echo(' <li role="presentation" id="tab7" class="nav-item"><a href="#userTab" aria-controls="userTab" role="tab" data-toggle="tab">User</a></li>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "Manager")) {
        echo(' 
            
            <li role="presentation" id="tab8" class="nav-item"><a href="#reportTab" aria-controls="reportTab" role="tab" data-toggle="tab">Report</a>
                                        </li>
                                        </ul>
                                    <!-- Tab panes -->
                                    <div class="modal_wrapper">
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="uploadTab">
                                                    <div class="modal-body">
                                                            <div class="input-group"><span class="input-group-addon">GPS Coordinates: </span><input id="gps" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">High-Level Type: </span><input id="name_highLevel_type" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Nature: </span><input id="name_Nature" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Subnature: </span><input id="name_Subnature" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Value Name: </span><input id="data-unique_name_id" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Value Type: </span><input id="data-low_level_type" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Data Type: </span><input id="data_unit" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Value Unit: </span><input id="value_unit" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Device ServiceURI or Data ID: </span><input id="data-get_instances" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Sensor ServiceURI or Data ID: </span><input id="data-get_sensor_instances" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Datasource: </span><input id="data_source" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Ownership: </span><input id="ownership" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Organizations: </span><input id="organization" type="text" class="form-control" readonly/></div><br />
                                                            <div>
                                                                <span id="sm_link" ></span>
                                                                <span id="ms_link" ></span>
                                                                <span id="iot_link" ></span>
                                                                <span id="time_trand_link" ></span>
                                                                <span id="dash_link"></span>
                                                                <span id="pd_link" ></span>
                                                                <span id="arcgis_link" ></span>
                                                                <span id="heatmap_link" ></span>
                                                                <span id="list_dashboard_link"></span>
                                                                <span id="list_kpi_dash"></span>
                                                            </div>
                                                    </div>
                                          </div>
                                          <div role="tabpanel" class="tab-pane" id="HealthinessTab">
                                                    <div class="modal-body">
                                                            
                                                            <div class="input-group"><span class="input-group-addon">Healthiness Criteria: </span><input id="healthiness_c" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Delay: </span><input id="delay" type="text" class="form-control" readonly/></div><br />
                                                            
                                                            <div class="input-group"><span class="input-group-addon">Period: </span><input id="period" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Last Update: </span><input id="last_check_health" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Healthiness Criteria 1:</span><span class="input-group-addon"><span id="s1_date"></span><i class="fa fa-circle" aria-hidden="true" style="pointer-events:none;" id="status_1"></i></span><input id="Status_h" type="text" class="form-control" value="" readonly/></div><br />
                                                            <div class="input-group" id="input_ch2"></div>
                                                    </div>
                                          </div>
                                        <div role="tabpanel" class="tab-pane" id="reportTab">
                                                    <div class="modal-body">
                                                    <div id="admin_report_config">
                                                    </div>
                                                    <span id="report_link" >
                                                          <!-- <a href="#" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Download Report</a>-->
                                                           <!--<button type="button" id="button_link" class="btn btn-primary">Download Report</button>-->
                                                    </span>
                                                    </div>
                                    </div>
                                        <div role="tabpanel" class="tab-pane" id="browseTab">
                                                        <div class="modal-body">


                                                            <div class="input-group"><span class="input-group-addon">Last Date: </span><input id="last_date" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Last Value: </span><input id="last_value" type="text" class="form-control" readonly/></div><br />
                                                                <table id="healthiness_table" class="addWidgetWizardTable table table-striped dt-responsive nowrap" style="display:none;">
                                                                    <thead class="widgetWizardColTitle">
                                                                        <tr>
                                                                            <th class="widgetWizardTitleCell">Value Name</th>
                                                                            <th class="widgetWizardTitleCell">Healthy</th>
                                                                            <th class="widgetWizardTitleCell">Delay (s)</th>
                                                                            <th class="widgetWizardTitleCell">Reason</th>
                                                                            <th class="widgetWizardTitleCell">Healthiness Criteria</th>
                                                                            <th class="widgetWizardTitleCell">Refresh Rate (s)</th>
                                                                            <th class="widgetWizardTitleCell">Data type</th>
                                                                            <th class="widgetWizardTitleCell">Value type</th>
                                                                            <th class="widgetWizardTitleCell">Value Unit</th>
                                                                            <th class="widgetWizardTitleCell">Value</th>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin")) {
        echo('<th class="widgetWizardTitleCell">Time Trend</th>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "Manager")) {
        echo('</tr>
                                                                    </thead>
                                                                    <tbody style="background-color: #F5F5F5">
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="processTab">
                                                        <div class="modal-body">
                                                            <!-- data graph -->
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Process Name Static: </span><input id="processnameStatic" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Knowledge Base IP: </span><input id="kbIp" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Disces IP: </span><input id="disces_ip" type="text" class="form-control" readonly/></div><br class="etl_sensor"/>
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Disces Process file path: </span><input id="processPath" type="text" class="form-control" readonly/></div><br class="etl_sensor" />
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Phoenix table: </span><input id="phoenixTable" type="text" class="form-control" readonly/></div><br class="etl_sensor" />
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Graph Uri: </span><input id="graph_uri" type="text" class="form-control" readonly/></div><br class="etl_sensor" />
                                                                    <div class="input-group etl_sensor"><span class="input-group-addon">Job Name: </span><input id="job_name" type="text" class="form-control" readonly/></div><br class="etl_sensor" />
                                                                    <div class="input-group iot_sensor"><span class="input-group-addon">IoT Broker: </span><input id="iotBroker" type="text" class="form-control" readonly/></div><br class="iot_sensor" />
                                                                    <div class="input-group iot_sensor"><span class="input-group-addon">Iot Device: </span><input id="iotDevice" type="text" class="form-control" readonly/></div><br class="iot_sensor" />
                                                                    <div class="input-group"><span class="input-group-addon">Device Set name: </span><input id="setname" type="text" class="form-control" readonly/></div><br />
                                                                    <div>
                                                                        <span  id="kb_link" style="float: left;"></span><span id="Kbase_link" style="margin-right: 10 px;"></span>
                                                                        <span  id="disces_link" style="float: left;" class="etl_sensor"></span><span id="disces_link" style="padding-right: 10 px;"></span>
                                                                        <span  id="iotDir_link" style="float: left;"></span><span id="iotDir_link" style="padding-right: 10 px;"></span>
                                                                        <span  id="listETL_link" style="float: left;"></span><span id="listETL_link" style="padding-right: 10 px;"></span>
                                                                        <span  id="broker_link" style="float: left;"></span><span id="broker_link" style="padding-right: 10 px;"></span>
                                                                    </div>
                                                            <!-- -->
                                                        </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="imageTab">
                                                        <div class="modal-body">
                                                            <!-- data graph -->
                                                            <div id="inspector_image"></div>
                                                            <form id="form_img" enctype="multipart/form-data" action="loadSensorImage.php" method="POST" accept-charset="UTF-8" target="uploadframe">
                                                            <input id="id_row" type="text" name="id_row" hidden></input>
                                                            <div id="upload_image">
                                                            </div>
                                                            </form>
                                                            <!-- -->
                                                        </div>
                                                   </div>
                                       <div role="tabpanel" class="tab-pane" id="ownerTab">
                                                        <div class="modal-body">
                                                            <!-- data graph -->
                                                            <!-- -->
                <div id="licenceLabel" class="input-group" style="display:none;"><span class="input-group-addon" >Licence: </span><input id="licence_hidden" type="text" class="form-control licence_par" hidden readonly/></div><br />
                <div class="panel panel-default" id="panel_lic" style="background-color: #EEE"><div class="panel-heading licence_text" style="background-color: #EEE" >Licence:</div><div class="panel-body" id="licence" style="background-color: #EEE"></div></div>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin")) {
        echo('<div id="ownerLabel" class="input-group licence_tab_div"><span class="input-group-addon">Provider: </span><input id="owner" type="text" class="form-control licence_par" readonly/></div><br />
                                                                    <div id="addressLabel" class="input-group licence_tab_div"><span class="input-group-addon">Address: </span><input id="address" type="text" class="form-control licence_par" readonly/></div><br />
                                                                    <div id="mailLabel" class="input-group licence_tab_div"><span class="input-group-addon">E-mail: </span><input id="mail" type="text" class="form-control licence_par" readonly/></div><br />
                                                                    <div id="personLabel" class="input-group licence_tab_div"><span class="input-group-addon">Reference Person: </span><input id="person" type="text" class="form-control licence_par" readonly/></div><br />
                                                                    <div id="telephoneLabel" class="input-group licence_tab_div"><span class="input-group-addon">Telephone: </span><input id="telephone" type="text" class="form-control licence_par" readonly/></div><br />
                                                                    <div id="webLabel" class="input-group licence_tab_div"><span class="input-group-addon">Website: </span><input id="web" type="text" class="form-control licence_par" readonly/></div><br />');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "Manager")) {
        echo (' 
                                                            <div id="div_edit_licence">
                                                            </div>
                                                            <div id="div_edit_licence_confirm">
                                                            </div>
                                                       <!-- -->
                                                        </div>
                                                   </div>');
    }
    if ($_SESSION['loggedRole'] == "RootAdmin") {
        echo(' <div role="tabpanel" class="tab-pane" id="userTab">
                                                        <div class="modal-body">
                                                                <div id="creatorLabel" class="input-group"><span class="input-group-addon">User Creator: </span><input id="creator" type="text" class="form-control" readonly/></div><br />
                                                                <div id="publicLabel" class="input-group"><span class="input-group-addon">Status: </span><input id="public" type="text" class="form-control" readonly/></div><br />
                                                                <div id="mailCLabel" class="input-group"><span class="input-group-addon">E-mail creator: </span><input id="mailC" type="text" class="form-control" readonly/></div><br />
                                                        </div>
                                                        
                                                </div>');
    }
    if (($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "Manager")) {
        echo('</div></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn cancelBtn cancelView" id="close_healthiness_modal" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                        </div>
                    </div>');
    } else {
        //nothing
    }
    ?>
    <!-- Modal Are you sure? -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_edit">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit Parameter")?></h5>
                </div>
                <div class="modal-body">
                   <?= _("Are you sure do you want edit parameters from this tab?")?> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="close_conf_edit"><?= _("Close")?></button>
                    <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm")?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form enctype="multipart/form-data" action="editInspectorData.php" method="POST" accept-charset="UTF-8">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" id="button_conf_edit2">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit Parameter")?></h5>

                    </div>
                    <div id="edit_lic_mod" style="display:none;">
                        <input id="id_row_hlt" type="text" name="id_row_hlt"></input>
                        <input id="id_hlt" type="text" name="id_hlt"></input>
                        <input id="id_mod" type="text" name="id_mod"></input>
                        <input id="mod_lic" type="text"  name="mod_lic"></input>
                        <input id="mod_prov" type="text"  name="mod_prov"></input>
                        <input id="mod_add"  type="text"  name="mod_add"></input>
                        <input id="mod_mail"  type="text"  name="mod_mail"></input>
                        <input id="mod_tel"  type="text"  name="mod_tel"></input>
                        <input id="mod_web"  type="text"  name="mod_web"></input>
                        <input id="mod_ref"  type="text"  name="mod_ref"></input>
                    </div>
                    <div class="modal-body">
                        <div id="check_errors"></div>
                        <?= _("Are you sure do you want confirm?")?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close_conf_edit2" ><?= _("Close")?></button>
                        <button type="submit" class="btn btn-primary" id="conf_edit2"><?= _("Confirm")?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- -->
    <iframe width="0" height="0" border="0" name="uploadframe" id="uploadframe" hidden></iframe>
    <!-- -->
    <script type='text/javascript'>
        if ($(window).width() < 1200) {
            $('#right').css('float', 'left');
        }
        if ($(window).width() < 1534) {
            var width = $(window).width() - 20;
            width = width + 'px';
            $('#widgetWizardTableContainer').css('width', width);
            $('#widgetWizardTable').css('width', width);
        }
        if ($(window).width() < 1200) {
            var margin = document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
            var margin = margin.substring(0, margin.length - 2);
            var width = $(window).width() - (parseInt(margin) * 2);
            var headerwidth = width - 60.75;
            var widthpx = width + 'px';
            var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
            $('#timetrend').css('width', widthpx);

            $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', widthpx);

            $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', widthpx);

            $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', Math.floor(headerwidth / width * 100) + "%");
            $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left", widthpx);
        }

        //fa-circle
        //$(document).on('click', '.fa-circle, button.dt', function () {
        $(document).on('click', 'button.dt', function () {
            //
            var cl = $(this).attr('style');
            var cl2 = cl.split('font-size:16px;color:');
            var color_cicle = cl2[1];
            console.log(color_cicle);
            //var id0 = $(this).parent().parent().html();
            var high_level = $(this).parent().parent().attr('data-high_level_type');
            var name_Nature = $(this).parent().parent().attr('data-nature');
            var name_Subnature = $(this).parent().parent().attr('data-sub_nature');
            var data_unique_name_id = $(this).parent().parent().attr('data-unique_name_id');
            //var data_unique_name_id = $(this).parent().parent().attr('data-low_level_type');
            //
            var data_unit = $(this).parent().parent().attr('data-unit');
            var data_last_value = $(this).parent().parent().attr('data-last_value');
            var data_latitude = $(this).parent().parent().attr('data-latitude');
            var data_longitude = $(this).parent().parent().attr('data-longitude');
            var parameters = $(this).parent().parent().attr('data-parameters');
            var data_instance_uri = $(this).parent().parent().attr('data-instance_uri');
            var data_organizations = $(this).parent().parent().attr('data-organizations');
            var data_low_level_type = $(this).parent().parent().attr('data-low_level_type');
            var data_get_instances = $(this).parent().parent().attr('data-get_instances');
            var data_get_sensor_instances = $(this).parent().parent().attr('data-get_sensor_instances');
            var last_date = $(this).parent().parent().attr('last_date');
            var ownership = $(this).parent().parent().attr('ownership');
            var icon = $(this).parent().parent().attr('icon');
            var id_row_db = $(this).parent().parent().attr('data-rowid');
            var id_row = $(this).parent().parent().attr('data-unique_name_id');
            var last_check = $(this).parent().parent().get(0).children[9].innerText;
            var data_valueunit = $(this).parent().parent().attr('data-valueunit');
            //var data_value_name = $(this).parent().parent().attr('ValueName');
            var data_value_name = $(this).parent().parent().get(0).children[5].innerText;
            var data_value_type = $(this).parent().parent().get(0).children[6].innerText;
            var var_broker = $(this).parent().parent().get(0).children[4].innerText;
            var model_device = $(this).parent().parent().get(0).children[3].innerText;
            console.log('var_broker: '+var_broker);
            var data_source = "";
            //https://iot-app.snap4city.org/nodered/nrxe49k
//
            var role_session_active = "<?= $_SESSION['loggedRole']; ?>";
            if (role_session_active === 'RootAdmin') {
                //
                $('#div_edit_licence').html('<span  id="edit_licence" style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" data-toggle="modal" role="button" style="margin-right: 10px;" id="edit_button_lic" data-target="#confirmModal"><?= _("Edit parameters")?></a></span>');
                //confirmModal
                //$('#div_edit_licence').html('<span  id="edit_licence" style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" role="button" style="margin-right: 10px;" id="confirmModal">Edit parameters</a></span>');
                $('.licence_tab_div').show();
                //
            } else {
                $('#div_edit_licence').empty();
                //$('.licence_tab_div').hide();
            }
//
            $('#name_highLevel_type').val(high_level);
            $('#data_source').val(data_source);
            $('#name_Nature').val(name_Nature);
            $('#name_Subnature').val(name_Subnature);
            //$('#data-unique_name_id').val(data_unique_name_id);
            $('#id_row_hlt').val(name_Subnature);
            $('#iotDevice').val(model_device);
            $('#iotBroker').val(var_broker);
            //
             $('#data-unique_name_id').val(data_value_name);
             $('#data-low_level_type').val(data_value_type);
              $('#value_unit').val(data_valueunit);
            //users
            // $('#public').val();
            //$('#creator').val();
            //$('#mailC').val();
            //licence
            //$('#person').val();
            var hour_option = "";
            if (role_session_active === 'RootAdmin') {
                hour_option = '<option value="hourly"><?=_("Hourly")?></option>';
            }else{
                hour_option = '';
            }
            //$('#web').val();
            ////////////////////////
            if (role_session_active === 'RootAdmin') {
                  $('#admin_report_config').html('<div class="panel panel-default"><div class="panel-heading"><?= _("Define Report"); ?></div><div class="panel-body"><input type="text" id="job" style="display:none;"></input><label for="activation"><?=_("Activation:")?></label><input id="activation" type="checkbox"></br><label for="periods"><?= _("Periodicity:")?></label><select id="periods"><option value="undefined"></option><option value="monthly"><?= _("Monthly")?></option><option value="quarterly"><?= _("Quarterly")?></option>'+hour_option+'</select></div><div class="panel-footer"><button class="btn btn-primary" role="button" id="modify_report" style="margin-right: 10px;"><?= _("Confirm")?></button></div></div>');
             }else{
                 $('#admin_report_config').empty();
             }
            ////////////////
            
            //
             
            //
            //if (high_level == 'Sensor' || high_level == 'Sensor-Actuator') {
             if ((high_level === 'Sensor')||(high_level == 'Sensor-Actuator')||(high_level === 'Data Table Device')||(high_level === 'Data Table Model')||(high_level === 'Data Table Variable')||(high_level === 'IoT Device')||(high_level === 'IoT Device Model')||(high_level === 'IoT Device Variable')||(high_level === 'Mobile Device')||(high_level === 'Mobile Device Model')||(high_level === 'Mobile Device Variable')||(high_level === 'Sensor Device')) {
                $('#data-get_instances').val(data_get_instances);
                if (data_get_sensor_instances !== null) {
                    if (data_low_level_type !== '') {
                        $('#data-get_sensor_instances').val(data_get_instances + '/' + data_low_level_type);
                    } else {
                        $('#data-get_sensor_instances').val(data_get_instances);
                    }
                } else {
                    $('#data-get_sensor_instances').val();
                }
            } else if (high_level == 'MyKPI' || high_level == 'MyPOI') {
                //https://www.snap4city.org/mypersonaldata/" + "api/v1/kpidata/" + kpiId + "/activities";
                let correctGetInstancesStr = "";
                if (data_get_instances.includes("datamanager/api/v1/poidata/")) {
                    correctGetInstancesStr = data_get_instances.split("datamanager/api/v1/poidata/")[1];
                } else {
                    correctGetInstancesStr = data_get_instances;
                }
                $('#data-get_instances').val(correctGetInstancesStr);
                if (data_low_level_type !== '') {
                    $('#data-get_sensor_instances').val(correctGetInstancesStr + '/' + data_low_level_type);
                } else {
                    $('#data-get_sensor_instances').val(correctGetInstancesStr);
                }
            } else {
                $('#data-get_instances').val('');
                $('#data-get_sensor_instances').val('');
            }
            $('#data_unit').val(data_unit);
            $('#last_date').val(last_date);
            $('#ownership').val(ownership);
            //$('#id_row').val(data_unique_name_id);
            $('#id_row').val(id_row);
            if (data_latitude) {
                $('#gps').val(data_latitude + ', ' + data_longitude);
            }
            $('#last_value').val(data_last_value);
            //$('#data-low_level_type').val(data_low_level_type);
            $('#data-low_level_type').val(data_value_type);
            $('#organization').val(data_organizations);

            //CONTROLLI SUL TIPO
            $('#tab3').hide();
            //
            if ((parameters !== null) && (typeof parameters !== 'undefined') && (parameters !== undefined)) {
                if (parameters.includes('http')) {
                    var check_para = parameters.includes('http');
                }
                var sm = parameters.replace('&format=json', '&format=html');
            }
            //***//
            switch (high_level) {
                case 'Complex Event':
                case 'wfs':
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    var icon = '../img/dataInspectorIcons/data-inspector.png';
                    $('#inspector_image').append('<img src="' + icon + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    break;
                case 'External Service':
                    $("#healthiness_table").hide();
                    $('#data_source').val('Special Process');
                    $('#input_ch2').empty();
                    $('#sm_link').append('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to External Service")?></a>');
                    //
                    //icon
                    var url = '../img/externalServices/' + icon;
                    $.get(url)
                            .done(function () {
                                // exists code 
                                url = '../img/externalServices/' + icon;
                                $('#inspector_image').append('<img src="' + url + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                            }).fail(function () {
                        // not exists code
                        url = '../img/dataInspectorIcons/data-inspector.png';
                        $('#inspector_image').append('<img src="' + url + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    });
                    //
                    break;
                case 'Heatmap':
                    $("#healthiness_table").hide();
                    $('#input_ch2').show();
                    $('#input_ch2').html('<span class="input-group-addon"><?=_("Healthiness Criteria 2:")?></span><span class="input-group-addon"><span id="s2_date"></span><i class="fa fa-circle" aria-hidden="true" style="pointer-events:none;" id="status_health"></i></span><input id="Status_2" type="text" class="form-control" value="" readonly/>');
                    break;
                case 'POI':
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $(".sensor_own").hide();
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    $('#data_source').val('Datagate or Loaded by Triples (ETL)');
                    var icon = '../img/dataInspectorIcons/data-inspector.png';
                    var func_dash = function_dashKpi(parameters);
                    //$('#pd_link').append('<a href="'+pd_external_link+'" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to '+high_level+'</a>');
                    $('#inspector_image').append('<img src="' + icon + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    break;
                case 'KPI':
                    $(".etl_sensor").show();
                    $(".iot_sensor").hide();
                    $('#input_ch2').empty();
                    $(".sensor_own").show();
                    $('#tab3').show();
                    $("#healthiness_table").hide();
                    $('#data_source').val('Km4cityRTData');
                    var func_dash = function_dashKpi(parameters);
                    $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    $('#sm_link').html('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Service Map")?></a>');
                    break;
                case 'MyKPI':
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $(".sensor_own").show();
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    var dataTypeMyKpi = data_unit.split('-');
                    $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    if (parameters.includes('datamanager/api/v1/poidata/')) {
                        var param2 = parameters.split('datamanager/api/v1/poidata/');
                        //$('#data_source').val(param2[1]);
                        var func_dash = function_dashKpi(param2[1]);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + param2[1] + "&operation=values&dataType=" + dataTypeMyKpi[0];
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                        //$('#pd_link').append('<a href="https://www.snap4city.org/mypersonaldata/api/v1/kpidata/'+ parameters + '/activities" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoTApp List</a>');
                    } else {
                        $('#data_source').val(parameters);
                        var func_dash = function_dashKpi(parameters);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + parameters + "&operation=values&dataType=" + dataTypeMyKpi[0];
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                        //$('#pd_link').append('<a href="https://www.snap4city.org/mypersonaldata/api/v1/kpidata/'+ parameters + '/activities" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoTApp List</a>');
                        //$('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                    }
                    //
                    //$('#iot_link').html('<a href="#" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT App</a>');
                    //
                    break;
                case 'MyData':
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $(".sensor_own").hide();
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    var dataTypeMyKpi = data_unit.split('-');
                    $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    if (parameters.includes('datamanager/api/v1/poidata/')) {
                        var param2 = parameters.split('datamanager/api/v1/poidata/');
                        //$('#data_source').val(param2[1]);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + param2[1] + "&operation=values&dataType=" + dataTypeMyKpi[0];
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                    } else {
                        $('#data_source').val(parameters);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + parameters + "&operation=values&dataType=" + dataTypeMyKpi[0];
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                    }
                    break;
                case 'MyPOI':
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $(".sensor_own").hide();
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    if (parameters.includes('datamanager/api/v1/poidata/')) {
                        var param2 = parameters.split('datamanager/api/v1/poidata/');
                        //$('#data_source').val(param2[1]);
                        var func_dash = function_dashKpi(param2[1]);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + param2[1] + "&operation=values&dataType=integer";
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                    } else {
                        $('#data_source').val(parameters);
                        var func_dash = function_dashKpi(parameters);
                        var pd_external_link = "https://www.snap4city.org/mypersonaldata/?kpiId=" + parameters + "&operation=values&dataType=integer";
                        $('#pd_link').append('<a href="' + pd_external_link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to ' + high_level + '</a>');
                    }
                    break;
                case 'Sensor':
                case 'Data Table Device':
                case 'Data Table Model':
                case 'Data Table Variable':
                case 'IoT Device':
                case 'IoT Device Model':
                case 'IoT Device Variable':
                case 'IoT Device':
                case 'Mobile Device':
                case 'Mobile Device Model':
                case 'Mobile Device Variable':
                case 'Sensor Device':
                    $('#tab3').show();
                    $("#healthiness_table").show();
                    $('#sm_link').html('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Service Map")?></a>');

                    //if ((parameters.includes("iot/")) || (name_Subnature.includes("IoT/")) || (name_Nature.includes("IOT/"))) {
                    if ((parameters.includes("iot/")) || (name_Subnature.includes("IoT/")) || (name_Nature.includes("IOT/"))||(var_broker !=='')) {
                        $('#data_source').val("IoT");
                        $(".iot_sensor").show();
                        $(".etl_sensor").hide();
                        $(".sensor_own").show();
                        //
                        var list_linkDev = function_dev(data_unique_name_id);
                        //
                        //$('#input_ch2').show();
                        //var iot_sensor_ip = "<?= $iot_sensor ?>";  
                        // iot_sensor_ip = '';                          
                        //var url_iot= "" + iot_sensor_ip + "/management/iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dvalue.php%253FshowFrame%3Dfalse&linkId=saLink&pageTitle=IOT%20Sensors%20and%20Actuators&fromSubmenu=iotDir2Link";
                        //var url_iotBrok = "" + iot_sensor_ip + "/management/iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dcontextbroker.php%253FshowFrame%3Dfalse&linkId=sab3Link&pageTitle=IOT%20Brokers&fromSubmenu=iotDir2Link";
                        //var url_iot = "iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dvalue.php%253FshowFrame%3Dfalse&linkId=saLink&pageTitle=IOT%20Sensors%20and%20Actuators&fromSubmenu=iotDir2Link";
                        //var url_iotBrok = "iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dcontextbroker.php%253FshowFrame%3Dfalse&linkId=sab3Link&pageTitle=IOT%20Brokers&fromSubmenu=iotDir2Link";
                        //$('#disces_link').append('<a href="' + url_iot + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT Directoy</a>');
                        //var url_iot = '';
                        //var url_iotBrok ='';
                        //var test = check_iot(url_iot);
                        $('#broker_link').show();
                        var url_iot = "<?= $iot_directory ?>";
                        var url_iotBrok = "<?= $iot_directory ?>";
                        //https://www.snap4city.org/iotdirectorytest/management/ssoLogin.php?redirect=contextbroker.php%3FshowFrame=false
                        //
                        $('#iot_link').html('<a href="' + url_iot + 'ssoLogin.php?redirect=value.php%3FshowFrame=false" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to IoT Directory")?></a>');
                        $('#broker_link').html('<a href="' + url_iotBrok + 'ssoLogin.php?redirect=contextbroker.php%3FshowFrame=false" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to IoT Broker")?></a>');
                    } else {
                        $('#data_source').val("ETL");
                        $(".etl_sensor").show();
                        $(".iot_sensor").hide();
                        $('#broker_link').hide();
                    }
                    if (data_unit === 'sensor_map') {
                        $('#input_ch2').hide();
                    } else {
                        $('#input_ch2').show();
                    }
                    break;
                case 'Sensor-Actuator':
                    $('#tab3').show();
                    $("#healthiness_table").show();
                    $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    if ((parameters.includes("iot")) || (name_Subnature.includes("IoT")) || (name_Nature.includes("IOT"))) {
                        $('#data_source').val("IoT");
                        $(".iot_sensor").show();
                        $(".etl_sensor").hide();
                        $('#input_ch2').empty();
                        //var iot_sensor_ip = "<?= $iot_sensor ?>";
                        //iot_sensor_ip = ''; 
                        //var url_iot = "iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dvalue.php%253FshowFrame%3Dfalse&linkId=saLink&pageTitle=IOT%20Sensors%20and%20Actuators&fromSubmenu=iotDir2Link";
                        //var url_iotBrok = "iframeApp.php?linkUrl=https%3A%2F%2Fwww.snap4city.org%2Fiotdirectorytest%2Fmanagement%2FssoLogin.php%3Fredirect%3Dcontextbroker.php%253FshowFrame%3Dfalse&linkId=sab3Link&pageTitle=IOT%20Brokers&fromSubmenu=iotDir2Link";
                        var url_iot = "<?= $iot_directory ?>";
                        var url_iotBrok = "<?=$iot_directory ?>";
                        //var test = check_iot(url_iot);
                        $('#broker_link').show();
                        $('#broker_link').html('<a href="' + url_iotBrok + 'ssoLogin.php?redirect=contextbroker.php%3FshowFrame=false" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to IoT Broker")?></a>');
                        //$('#disces_link').append('<a href="' + url_iot + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT Directoy</a>');
                    } else {
                        $('#data_source').val("ETL");
                        $(".etl_sensor").show();
                        $(".iot_sensor").hide();
                        $('#broker_link').hide();
                    }
                    break;
                case 'MicroApplication':
                    //
                    $("#healthiness_table").hide();
                    $('#input_ch2').empty();
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $('#broker_link').hide();
                    var url = '../img/microApplications/' + icon;
                    $.get(url)
                            .done(function () {
                                // exists code 
                                $('#inspector_image').append('<img src="../img/microApplications/' + icon + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                            }).fail(function () {
                        // not exists code
                        $('#inspector_image').append('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    });
                    $('#data_source').val('Direct Input');
                    var id_microserv = 'microApplications.php?linkId=microApplicationsLink&fromSubmenu=false&pageTitle=Micro+Applications&sorts[sub_nature]=1&queries[search]=' + id_row;
                    if (id_row) {
                        $('#ms_link').html('<a href="' + id_microserv + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?= _("Link to MicroApplication")?></a>');
                    }
                    break;
                case 'Special Widget':
                    $('#tab3').hide();
                    $('#input_ch2').empty();
                    $(".etl_sensor").hide();
                    $(".iot_sensor").hide();
                    $('#broker_link').hide();
                    $("#healthiness_table").hide();
                    $('#data_source').val('Special Process');
                    $('#inspector_image').html('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    break;
                case 'Dashboard-IOT App':
                    $("#healthiness_table").hide();
                    $('#input_ch2').empty();
                    $('#data_source').val(name_Nature);
                    var das = function_dashboard(data_get_instances);
                    //$('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    $('#inspector_image').html('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    //$('#dash_link').html('<a href="' + das + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    break;
                default:
                    $('#input_ch2').empty();
                    $("#healthiness_table").hide();
                    break;
            }
            //***//
            var iot_device = "<?= $iot_directory
    ?>";
            //
            switch (name_Nature) {
                case 'From Dashboard to IOT Device':
                    var das = function_dashboard(data_get_instances);
                    //var test = check_iot(iot_device);
                    $('#sm_link').html('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Service Map")?></a>');
                    //$('#iot_link').html('<a href="' + iot_device + 'ssoLogin.php?redirect=devices.php%3FshowFrame=false" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT Device</a>');
                    //$('#dash_link').html('<a href="' + das + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    $('#disces_link').empty();
                    $('#broker_link').show();
                    $(".etl_sensor").hide();
                    $(".sensor_own").hide();
                    break;
                case 'From IOT Device to KB':
                    //
                    //var test = check_iot(iot_device);
                    //$('#iot_link').html('<a href="' + iot_device + 'ssoLogin.php?redirect=devices.php%3FshowFrame=false" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT Device</a>');
                    $('#sm_link').html('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Service Map")?></a>');
                    $('#disces_link').empty();
                    $(".etl_sensor").hide();
                    $(".sensor_own").hide();
                    $('#broker_link').show();
                    break;
                case 'From Dashboard to IOT App':
                    var das = function_dashboard(data_get_instances);
                    var test = check_iot(data_get_instances);
                    //$('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT App</a>');
                    //$('#dash_link').html('<a href="' + das + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    $(".etl_sensor").hide();
                    $(".sensor_own").hide();
                    $('#broker_link').hide();
                    break;
                case 'From IOT Application to Dashboard':
                    var das = function_dashboard(data_get_instances);
                    var test = check_iot(data_get_instances);
                    //$('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT App</a>');
                    //$('#dash_link').html('<a href="' + das + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    $('#disces_link').empty();
                    $(".sensor_own").hide();
                    $('#broker_link').hide();
                    break;
                case 'From IOT App to Dashboard':
                    var das = function_dashboard(data_get_instances);
                    var test = check_iot(data_get_instances);
                    //$('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT App</a>');
                    //$('#dash_link').html('<a href="' + das + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Dashboard</a>');
                    $('#disces_link').empty();
                    $(".sensor_own").hide();
                    $('#broker_link').hide();
                    break;
                case 'From IOT App to IOT Device':
                    var das = function_dashboard(data_get_instances);
                    var test = check_iot(data_get_instances);
                    //$('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT App</a>');
                    //$('#dash_link').html('<a href="' + iot_device + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to IoT Device</a>');
                    $('#sm_link').html('<a href="' + sm + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Service Map")?></a>');
                    $('#disces_link').empty();
                    $(".sensor_own").hide();
                    $('#broker_link').hide();
                    break;
            }
            //***//

            // if ((check_para) || (high_level === 'KPI') || (high_level === 'Special Widget')) {
            //***//
            //***//http://
            var url_parameters = parameters + '&healthiness=true';
            var role_session_active = "<?= $_SESSION['loggedRole']; ?>";
            var ds = $('#data_source').val();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                dataType: 'json',
                url: 'getServiceData.php',
                data: {
                    type: high_level,
                    service: url_parameters,
                    value: data_unique_name_id,
                    data_get_instances: data_get_instances,
                    id_row: id_row,
                    data_source: ds,
                    rb_row: id_row_db,
                    subnature: name_Subnature
                },
                success: function (data) {
                    /*DECODIFICARE data.healthiness*/
                    //if ((high_level === 'Sensor') || (high_level === 'Sensor-Actuator') || (high_level === 'KPI') || (high_level === 'MyPOI') || (high_level === 'MyKPI')) {
                    var json_data = JSON.stringify(data.healthiness);
                    var value_td = "";
                    var obj = Object.values(data);
                    var obj2 = "";
                    var keys2 = "";
                    var key3 = "";
                    var key4 = "";
                    var key5 = "";
                    var measured_time = "";
                    var key_icon0 = JSON.stringify(data.icon);
                    var key_icon = key_icon0;
                    if (key_icon !== undefined) {
                        var res = key_icon.replace('"', '');
                    } else {
                        var res = "";
                    }
                    var res1 = res.replace('"', '');
                    var key_icon = res1;

                    if (key_icon.includes(".")) {
                        $('#inspector_image').html('<img src="../img/sensorImages/' + id_row + '/' + key_icon + '" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    } else {
                        $('#inspector_image').html('<img src="../img/dataInspectorIcons/data-inspector.png" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">');
                    }
                    if ((high_level !== 'KPI') && (high_level !== 'MyKPI') && (high_level !== 'MyPOI')) {
                        if ((data.healthiness !== null) && (data.healthiness !== undefined)) {
                            console.log('data.healthiness: ' + data.healthiness);
                            obj2 = Object.values(data.healthiness);
                            keys2 = Object.keys(data.healthiness);
                        }

                        if ((data.Service !== null) && (data.Service !== undefined)) {
                            if (data.Service.features) {
                                key4 = Object.values(data.Service.features[0]);
                                key5 = (key4[2].realtimeAttributes);
                            }
                        }

                    } else {
                        key3 = "";
                        $('#data_source').val(dataSource);
                        if (data.Graph_Uri === "") {
                            $('#tab3').hide();
                        } else {
                            $('#tab3').show();
                        }
                    }

                    var fromTime = '';
                    var toTime = '';



                    //serviceUri
                    var process_name_ST = data.process_name_ST;
                    var processPath = data.process_path;
                    var kbIp = data.KB_Ip;
                    var mail = data.mail;
                    var phoenixTable = data.phoenix_table;
                    var owner = data.owner;
                    var licence = data.licence;
                    var web = data.webpage;
                    var address = data.address;
                    var graph_uri = data.Graph_Uri;
                    var telephone = data.telephone;
                    var disces_ip = data.disces_ip;
                    var total_data = data.disces_data;
                    var dataSource = data.dataSource;
                    var ownership_content = data.ownership_content;
                    var healthiness = data.HealthinessCriteria;
                    var period = data.period;
                    var organization = data.organization;
                    var device_id = data.device_id;
                    var broker = data.broker;
                    var creator = data.creator;
                    var ref_pers = data.reference_person;
                    var p_name = data.process_name;
                    //
                    var value_name=data.value_name;
                    var value_type=data.value_type;
                    var value_unit=data.value_unit;

                    //++++++++++++++//
                    if (data.total_ETL) {
                        var total_ETL = data.total_ETL;
                        if (total_ETL > 0) {
                            var lun = total_ETL;
                            var list_result = "";
                            if (data.list_ETL) {
                                for (var i = 0; i < lun; i++) {
                                    if ((data.list_ETL[i] !== undefined) && (data.list_ETL[i] !== 'undefined')) {
                                        list_result = list_result + "<li><a href='#'>" + data.list_ETL[i] + "</a></li>";
                                    }
                                }
                                //console.log(data.list_ETL[0]);
                                $('#listETL_link').html('<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?=_("List of Devices")?><span class="caret"></span></button><ul class="dropdown-menu scrollable-menu dropdown1" role="menu">' + list_result + '</ul></div>');
                            }
                        }
                    }
                    //+++++++++///
                    //
                    //HARSH
                    //var creator_dec = decryptOSSL(creator);
                    //var period = data.value_refresh_rate;
                    kbIp = kbIp.replace('http://', '');
                    $('#processnameStatic').val(process_name_ST);
                    $('#processPath').val(processPath);
                    $('#kbIp').val(kbIp);
                    $('#mail').val(mail);
                    $('#phoenixTable').val(phoenixTable);
                    $('#licence').html(licence);
                    $('.licence_text').text('Licence (on:' + p_name + '):');
                    $('#setname').val(data.device_set_name);
                    /*
                     CKEDITOR.replace('licence', {
                     allowedContent: true,
                     //config.readOnly: true;
                     //extraPlugins : 'stylesheetparser',
                     contentsCss : 'ckeditor/MicroService.css',
                     language: 'en',
                     width: '100%',
                     height: '100%',
                     //toolbar: 'Full',
                     //enterMode : CKEDITOR.ENTER_BR,
                     //shiftEnterMode: CKEDITOR.ENTER_P
                     });
                     CKEDITOR.instances['licence'].config.readOnly = true;
                     CKEDITOR.instances['licence'].setData(licence);*/
                    //
                    //alert('data_unit:  '+data_unit+'; high_level: '+high_level);
                    //$('#licence').text(licence);
                    $('#licence_hidden').val(licence);
                    //$('#licence').attr('value',licence);
                    $('#owner').val(owner);
                    $('#address').val(address);
                    $('#graph_uri').val(graph_uri);
                    $('#telephone').val(telephone);
                    $('#disces_ip').val(disces_ip);
                    $('#web').val(web);
                    /////
                    //data_valueunit
                    $('#public').val();
                    $('#creator').val(creator);
                    $('#mailC').val();
                    //licence
                    $('#person').val(ref_pers);
                    $('#web').val();
                    //////
                    $('#status_1').css('color', color_cicle);
                    var date_ch1 = data.ch1;
                    if (color_cicle === '#33cc33') {
                        $('#Status_h').val("    (" + date_ch1 + ")   true");
                    } else {
                        $('#Status_h').val("    (" + date_ch1 + ")   false");
                    }

                    //////
                    if ((high_level === 'MyKPI') || (high_level === 'MyPOI')) {
                        $('#data_source').val(dataSource);
                    }
                    //$('#period').val(period);
                    // $('#healthinessCriteria').val(healthiness);
                    //if((ownership_content !== "")&&(ownership_content !== null)){
                    //$('#iotDevice').val(device_id);
                    //$('#iotDevice').val(model_device);
                    //$('#iotBroker').val(var_broker);
                    //$('#owner').val(ownership_content.username);
                    //}
                    if (organization !== "") {
                        $('#organization').val(organization);
                    }
                    //
                    var count_nh = 0;
                    var count_h = 0;
                    var job_name = data.jobName;
                    var job_group = data.jobGroup;
                    var disces_ip_test = data.ip_disc;
                    if ((job_name !== "") || (job_group !== "")) {
                        var link = 'http://' + disces_ip_test + '/sce/newJob.php?jobName=' + job_name + '&jobGroup=' + job_group;
                    } else {
                        var link = 'http://' + disces_ip_test + '/sce/';
                    }

                    $('#job_name').val(job_name);
                    var color_selected = '';
                    for (var y = 0; y < obj2.length; y++) {
                        var name = keys2[y];

                        var value_unit_td = "";
                        var data_type_td = "";
                        var healthiness_criteria = "";
                        var value_refresh_rate = "";
                        var dealy = "";
                        var healt_value = "";
                        var value_type_td = "";

                        if (key5[name]) {
                            value_unit_td = key5[name]['value_unit'];
                            data_type_td = key5[name]['data_type'];
                            healthiness_criteria = key5[name]['healthiness_criteria'];
                            value_refresh_rate = key5[name]['value_refresh_rate'];
                            healt_value = String(obj2[y]['healthy']);
                            value_type_td = key5[name]['value_type'];
                        } else {
                            value_unit_td = "";
                            data_type_td = "";
                            healthiness_criteria = "";
                            value_refresh_rate = "";
                            healt_value = "";
                            value_type_td = "";
                        }
                        /************/
                        if ((data.realtime !== null)) {
                            if (typeof data.realtime.results !== "undefined") {
                                if (typeof data.realtime.results.bindings !== "undefined") {
                                    if ((Object.values(data.realtime.results.bindings).length > 0) && (Object.values(data.realtime.results.bindings) !== null)) {
                                        key3 = Object.values(data.realtime.results.bindings);
                                    } else {
                                        key3 = "";
                                    }
                                    //
                                    if (key3[0][name]) {
                                        value_td = key3[0][name]['value'];
                                    } else {
                                        value_td = "";
                                    }
                                    if (key3[0]['measuredTime']['value']) {
                                        measured_time = key3[0]['measuredTime']['value'];
                                    }
                                    //
                                } else {
                                    key3 = data.realtime.results;
                                    //
                                    if (key3[0][name]) {
                                        value_td = key3[0][name]['value'];
                                    } else {
                                        value_td = "";
                                    }
                                    if (key3[0]['measuredTime']['value']) {
                                        measured_time = key3[0]['measuredTime']['value'];
                                    }
                                    //
                                }
                            } else {
                                key3 = "";
                            }
                        } else {
                            //key3 = data.realtime.results;
                            key3 = "";
                        }
                        if ((last_date !== "") && (last_date !== null) && (typeof last_date !== "undefined")) {
                            toTime = last_date.replace(" ", "T");
                            toTime = toTime.replace("+01:00", "");
                            fromTime = new Date(last_date);
                            var date = fromTime.getFullYear() + '-' + fromTime.getMonth() + '-' + fromTime.getDate();
                            var time = addZero(fromTime.getHours()) + ":" + addZero(fromTime.getMinutes()) + ":" + addZero(fromTime.getSeconds());
                            fromTime = date + 'T' + time;
                        } else {
                            //toTime =last_date.replace(" ","T");
                            //measured_time
                            toTime = measured_time.replace(" ", "T");
                            toTime = toTime.replace("+01:00", "");
                            fromTime = new Date(measured_time);
                            var date = fromTime.getFullYear() + '-' + fromTime.getMonth() + '-' + fromTime.getDate();
                            var time = addZero(fromTime.getHours()) + ":" + addZero(fromTime.getMinutes()) + ":" + addZero(fromTime.getSeconds());
                            fromTime = date + 'T' + time;
                            //SETTARE IL FORMATO//
                        }
                        /***********/
                        var time_trend_link = "";
                        if ((fromTime !== '') && (toTime !== '')) {
                            //time_trend_link = '<a href="https://www.snap4city.org/sensor-validate/index.php?serviceUri='+data_get_instances+'&fromTime='+fromTime+'&toTime='+toTime+'&metric='+keys2[y]+'" target= "_blank" role="button" class="btn btn-xs editDashBtnCard">VIEW</a>';
                            time_trend_link = '<a type="button" class="viewDashBtn" href="https://www.snap4city.org/sensor-validate/indexML.php?serviceUri=' + data_get_instances + '&fromTime=' + fromTime + '&toTime=' + toTime + '&metric=' + keys2[y] + '" target= "_blank"> VIEW </a>';
                        }
                        dealy = obj2[y]['delay'];
                        var icn_h = "";
                        if (healt_value === 'true') {
                            icn_h = '<i class="fa fa-circle" aria-hidden="true" style="pointer-events: none; color:#33cc33;"></i>';
                            count_h = count_h + 1;
                        } else {
                            icn_h = '<i class="fa fa-circle" aria-hidden="true" style="pointer-events: none; color: red;"></i>';
                            count_nh = count_nh + 1;
                        }
                        if (("<?= $_SESSION['loggedRole'] ?>" == "RootAdmin") || ("<?= $_SESSION['loggedRole'] ?>" == "ToolAdmin"))
                        {
                            $('#healthiness_table tbody').append('<tr><td title="'+name+'">' + name + '</td><td class="ellipsis">' + icn_h + '</td><td class="ellipsis" title="'+obj2[y]['delay']+'">' + obj2[y]['delay'] + '</td><td class="ellipsis" title="'+obj2[y]['reason']+'">' + obj2[y]['reason'] + '</td><td class="ellipsis" title="'+healthiness_criteria+'">' + healthiness_criteria + '</td><td class="ellipsis" title="'+value_refresh_rate+'">' + value_refresh_rate + '</td><td class="ellipsis" title="' + data_type_td + '">' + data_type_td + '</td><td class="ellipsis" title="' + value_type_td + '">'+value_type_td+'</td><td class="ellipsis" title="' + value_unit_td + '">' + value_unit_td + '</td><td class="ellipsis" title="' + value_td + '">' + value_td + '</td><td class="ellipsis">' + time_trend_link + '</td></tr>');
                        } else {
                            $('#healthiness_table tbody').append('<tr><td title="'+name+'">' + name + '</td><td class="ellipsis">' + icn_h + '</td><td class="ellipsis" title="'+obj2[y]['delay']+'">' + obj2[y]['delay'] + '</td><td class="ellipsis" title="'+obj2[y]['reason']+'">' + obj2[y]['reason'] + '</td><td class="ellipsis" title="'+healthiness_criteria+'">' + healthiness_criteria + '</td><td class="ellipsis" title="'+value_refresh_rate+'">' + value_refresh_rate + '</td><td class="ellipsis" title="' + data_type_td + '">' + data_type_td + '</td><td class="ellipsis" title="' + value_type_td + '">'+value_type_td+'</td><td class="ellipsis" title="' + value_unit_td + '">' + value_unit_td + '</td><td class="ellipsis" title="' + value_td + '">' + value_td + '</td></tr>');
                        }
                        if (name == data_low_level_type) {
                            color_selected = healt_value;
                            console.log('color_selected: ' + color_selected);
                            //ARPAT_QA_AR-ACROPOLI
                        }
                        //
                    }
                    /////////////////////
                    $('#healthiness_c').val(healthiness_criteria);
                    $('#period').val(value_refresh_rate);
                    $('#delay').val(dealy);
                    ////
                    if ((high_level === 'Sensor')||(high_level === 'Data Table Device')||(high_level === 'Data Table Model')||(high_level === 'Data Table Variable')||(high_level === 'IoT Device')||(high_level === 'IoT Device Model')||(high_level === 'IoT Device Variable')||(high_level === 'Mobile Device')||(high_level === 'Mobile Device Model')||(high_level === 'Mobile Device Variable')||(high_level === 'Sensor Device')) {
                        if (data_unit !== 'sensor_map') {
                            var ch2_content = '<span class="input-group-addon">Healthiness Criteria 2:</span><span class="input-group-addon"><span id="s2_date"></span><i class="fa fa-circle" aria-hidden="true" style="pointer-events:none;" id="status_health"></i></span><input id="Status_2" type="text" class="form-control" value="" readonly/>';
                            $('#input_ch2').html(ch2_content);
                            $('#input_ch2').show();
                            var ch2 = toTime;
                            //alert('Sensor_map!');
                            if (count_nh > 0) {
                                //var ch2 = measured_time;
                                $('#Status_2').val("    (" + ch2 + ")   " + 'true');
                                $('#status_health').css('color', '#33cc33');
                            } else {
                                $('#Status_2').val("    (" + ch2 + ")   " + 'false');
                                $('#status_health').css('color', 'red');
                            }
                            //
                            //COlore CH1  -- Valore
                            if (color_selected == 'true') {
                                $('#status_1').css('color', '#33cc33');
                                $('#Status_h').val("    (" + date_ch1 + ")   true");
                            } else if (color_selected == 'false') {
                                $('#status_1').css('color', 'red');
                                $('#Status_h').val("    (" + date_ch1 + ")   false");

                                //
                            }
                            //
                        } else {
                            $('#input_ch2').empty();
                            $('#input_ch2').hide();

                        }
                    }
                    //UPLOAD IMAGE//upload_image
                    //upload_image
                    if (role_session_active === 'RootAdmin') {
                        $('#upload_image').html('<div id="uplaod" class="input-group mb-3"><div class="input-group iot_sensor"><div class="input-group-prepend"><span class="input-group-text" id="inputGroup-sizing-default"><?=_("Upload")?></span></div><input type="file" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" id="uploadField" name="uploadField"/><div class="input-group-append"><button class="btn btn-primary uploadImageClass" role="button" id="upload_command" style="margin-right: 10px;"><?=_("Upload Image")?></button></div></div></div>');
                        //$('#div_edit_licence').html('<span  id="edit_licence" style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" role="button" style="margin-right: 10px;">Edit paramters</a></span>');
                    }
                    ///////
                    //}

                    //TEST//

                    var link_kb = graph_uri;
                    //
                    var etl = $('#data_source').val();
                    if ((total_data > 0) && ($('#disces_link').empty()) && (etl === 'ETL')) {
                        $('#disces_link').html('<a href="' + link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Disces")?></a>');
                    }
                    if (etl === 'IoT') {
                        $('#disces_link').html('<a href="' + link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Disces")?></a>');
                    }
                    if ((parameters !== null) && (typeof parameters !== 'undefined') && (parameters !== undefined)) {
                    if (parameters.includes('http')) {
                        $('#kb_link').html('<a href="' + parameters + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Knowledge Base")?></a>');
                    }
                    }
                    //$('#kb_link').html('<a href="' + data_get_instances + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Knowledge Base</a>');
                    //
                }
            });
            if (high_level === 'wfs') {
                if ($('#data_source').val() === '') {
                    $('#data_source').val(parameters);
                }
                $('#arcgis_link').html('<a href="' + parameters + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to ArcGIS")?></a>');

            }
            if (high_level === 'Heatmap') {
                var heatmap_manger = "<?= $resource_manager; ?>";
                $('#heatmap_link').html('<a href="' + heatmap_manger + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Heatmap")?></a>');
            }
            //
            //  } else {
            //     $('#healthiness_table tbody').html('<tr><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td></tr>');
            // }
            if (("<?= $_SESSION['loggedRole'] ?>" == "AreaManager" || "<?= $_SESSION['loggedRole'] ?>" == "Manager") && ownership == "public") {
                $('#owner').hide();
                $('#ownerLabel').hide();
                $('#address').hide();
                $('#addressLabel').hide();
                $('#mail').hide();
                $('#mailLabel').hide();
                $('.licence_par').attr("readonly", true);
            } else {
                $('#owner').show();
                $('#ownerLabel').show();
                $('#address').show();
                $('#addressLabel').show();
                $('#mail').show();
                $('#mailLabel').show();
            }
//////
$.ajax({
                    async: true,
                    type: 'GET',
                    url: 'report_scheduler.php',
                    data: {
                        action: 'list',
                        type: 'Devices',
                        service: data_unique_name_id 
                    },
                    success: function (data) {
                        console.log('SUCCESS');
                        console.log('Status data:   '+data);
                        var value = JSON.parse(data);
                        console.log(value.status);
                        console.log(value.period);
                        console.log(value.folder);
                        console.log(value.link);
                        console.log(value.job);
                        //
                        var link = value.link;
                        //
                        if (value.status === "Yes"){
						//$('#report_link').html('<button type="button" id="button_link" class="btn btn-primary">Download Report</button>');
                                                if (link !==""){
                                                     $('#report_link').html('<a class="btn btn-primary" href="'+link+'" role="button" target="_blank"><?=_("Download Report")?></a>');
                                                }
                                                if (role_session_active === 'RootAdmin') {
                                                    $('#activation').prop('checked', true);
                                                    $('#job').val(value.job);
                                                }
                                            }else{
                                               $('#report_link').empty();
                                                 if (role_session_active === 'RootAdmin') {
                                                    $('#activation').prop('checked', false);
                                                }
                                            }
			if ((value.period === 'monthly')||(value.period === 'quarterly')){
                            $('#periods').val(value.period);
                        }
                        //
                        if (role_session_active === 'RootAdmin') {
                         if (value.period === 'hourly'){
                             $('#periods').val(value.period);
                         }       
                        }
                    }
                });
//////
            $('#healthiness-modal').modal('show');

        });

        $(document).on('click', '#edit_button_lic', function () {

            //$('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" role="button" style="margin-right: 10px;"  data-target="#exampleModal">Confirm</a></span>');
            //$('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" id="confirm_modify" role="button" style="margin-right: 10px;" data-target="#exampleModal2" data-toggle="modal">Confirm</a></span>');
            //$('#edit_button_lic').hide();
            console.log('edit_button_lic');
            $('#confirmModal').show();
            //$('.licence_par').attr("readonly", false);
            //
        });
        
        //modify_report
$(document).on('click', '#modify_report', function () {
    var activation =  $('#activation').prop('checked');
    var periods = $('#periods').val();
    var jobs = $('#job').val();
    var data_unique_name_id =  $('#data-unique_name_id').val();
	var org = $('#organization').val();
    var device_name = $('#iotDevice').val();
	var complete_string = org+":"+data_unique_name_id+":"+device_name;
	console.log ('complete string new theme:'+complete_string);
    console.log ('activation: '+activation+' periods:'+periods);
    //
                $.ajax({
                    async: true,
                    type: 'GET',
                    url: 'report_scheduler.php',
                    data: {
                        action: 'edit',
                        type: 'Devices',
                        activation: activation,
                        periods: periods,
                        service: complete_string,
                        jobs: jobs
                    },
                    success: function (data) {
                        console.log('SUCCESS');
                        console.log('Status data:   '+data);
                        var value = JSON.parse(data);
                        var code = value.code;
                        console.log(code);
                        var test_message = '';
                        if (code =='200'){
                             test_message = 'Operation Successfully executed';
                        }else if (code == '404'){
                             test_message = 'Error During operation Execution';
                        }else{
                             test_message = 'Error During operation Execution';
                        }
                        setTimeout(function () {
                    $('#healthiness-modal').modal('hide');
                    $('#healthiness_table tbody').empty();
                    $('#processnameStatic').val('');
                    $('#kbIp').val('');
                    $('#phoenixTable').val('');
                    $('#graph_uri').val('');
                    $('#mail').val('');
                    $('#licence').val('');
                    $('#owner').val('');
                    $('#address').val('');
                    $('#processPath').val('');
                    $('#tab7').removeClass("active");
                    $('#tab6').removeClass("active");
                    $('#tab5').removeClass("active");
                    $('#tab4').removeClass("active");
                    $('#tab2').removeClass("active");
                    $('#tab3').removeClass("active");
                    $('#tab1').addClass("active");
                    $('#imageTab').removeClass("active");
                    $('#ownerTab').removeClass("active");
                    $('#processTab').removeClass("active");
                    $('#HealthinessTab').removeClass("active");
                    $('#browseTab').removeClass("active");
                    $('#userTab').removeClass("active");
                    $('#reportTab').removeClass("active");
                    $('#uploadTab').addClass("active");
                    $('#disces_link').empty();
                    $('#listETL_link').empty();
                    $('#kb_link').empty();
                    $('#sm_link').empty();
                    $('#disces_ip').val('');
                    $('#organization').val('');
                    $('#inspector_image').empty();
                    $('#upload_image').empty();
                    $('#ms_link').empty();
                    $('#iot_link').empty();
                    $('#pd_link').empty();
                    $('#job_name').empty();
                    $('#dash_link').empty();
                    $('#time_trand_link').empty();
                    $('#iotDevice').empty();
                    $('#iotBroker').empty();
                    $('#owner').empty();
                    $('#id_row').val('');
                    $('#arcgis_link').empty();
                    $('#heatmap_link').empty();
                    $('#healthiness_c').val('');
                    $('#period').val('');
                    $('#v_type').val('');
                    $('#telephone').empty();
                    $('#web').empty();
                    $('#s1_date').empty();
                    $('#s2_date').empty();
                    $('#input_ch2').empty();
                    //$('#attr_type').val('');
                    $('#setname').val('');
                    $('#list_kpi_dash').empty();
                    $('#broker_link').empty();
                    $('#list_dashboard_link').empty();
                    $('#delay').val('');
                    $('#last_check_health').val('');
                    $('#status_1').css('color', 'black');
                    $('#status_health').css('color', 'black');
                    $('#Status_h').val('');
                    $('#Status_2').val('');
                    $('.licence_par').attr("readonly", true);
                    $('#div_edit_licence_confirm').empty();
                    $('.licence_text').text('Licence:    ');
                    //
                    $('#public').empty();
                    $('#creator').empty();
                    $('#mailC').empty();
                    //licence
                    $('#person').empty();
                    $('#web').empty();
                     $('#admin_report_config').empty();
                    //
                    alert(test_message);
                }, 1000);
                        //
                       
                    }
                });
    //
});
        $(document).on('click', '#confirm_modify', function () {

            //$('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" role="button" style="margin-right: 10px;"  data-target="#exampleModal">Confirm</a></span>');
            $('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" id="confirm_modify" role="button" style="margin-right: 10px;" data-target="#exampleModal2" data-toggle="modal"><?=_("Confirm")?></a></span>');
            $('#edit_button_lic').hide();
            $('.licence_par').attr("readonly", false);
            //
        });

        $(document).on('click', '#close_conf_edit', function () {
            $('#confirmModal').modal('hide');
            $('.licence_par').attr("readonly", true);
        });
        $(document).on('click', '#button_conf_edit', function () {
            $('#confirmModal').modal('hide');
            $('.licence_par').attr("readonly", true);
        });
        //close_conf_edit

        //$(document).on('click','#conf_edit', function(){
        $('#conf_edit').click(function () {
            //$('#confirmModal').removeClass("modal-backdrop")
            //$('#edit_button_lic').hide();
            $('#confirmModal').hide();
            $('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" id="confirm_modify" role="button" style="margin-right: 10px;" data-target="#exampleModal2" data-toggle="modal"><?=_("Confirm")?></a></span>');
            $('#edit_button_lic').hide();
            //$('#confirmModal').fadeOut();
            //var c_lic =$('#licence').val();
            var c_lic = $('#licence_hidden').val();
            $('.licence_par').attr("readonly", false);
            CKEDITOR.replace('licence', {
                allowedContent: true,
                //extraPlugins : 'stylesheetparser',
                contentsCss: 'ckeditor/MicroService.css',
                language: 'en',
                width: '100%',
                height: '100%',
                toolbar: 'Full',
                //enterMode : CKEDITOR.ENTER_BR,
                //shiftEnterMode: CKEDITOR.ENTER_P
            });
            CKEDITOR.instances['licence'].setData(c_lic);
            //$('#div_edit_licence_confirm').html('<span style="float: left;"></span><span style="margin-right: 10 px;"><a class="btn btn-primary" role="button" style="margin-right: 10px;"  data-target="#confirmModal">Confirm</a></span>');
            //
        });

        $(document).on('click', '#upload_command', function () {
            //
            var uploadField = $('#uploadField').val();
            //
            var name = uploadField.split(".")[1];
            //
            if ((name === 'jpeg') || (name === 'jpg') || (name === 'png') || (name === 'gif')) {

                //var name = a.split(".")[0];
                setTimeout(function () {
                    $('#healthiness-modal').modal('hide');
                    $('#healthiness_table tbody').empty();
                    $('#processnameStatic').val('');
                    $('#kbIp').val('');
                    $('#phoenixTable').val('');
                    $('#graph_uri').val('');
                    $('#mail').val('');
                    $('#licence').val('');
                    $('#owner').val('');
                    $('#address').val('');
                    $('#processPath').val('');
                    $('#tab7').removeClass("active");
                    $('#tab6').removeClass("active");
                    $('#tab5').removeClass("active");
                    $('#tab4').removeClass("active");
                    $('#tab2').removeClass("active");
                    $('#tab3').removeClass("active");
                    $('#tab1').addClass("active");
                    $('#imageTab').removeClass("active");
                    $('#ownerTab').removeClass("active");
                    $('#processTab').removeClass("active");
                    $('#HealthinessTab').removeClass("active");
                    $('#browseTab').removeClass("active");
                    $('#userTab').removeClass("active");
                    $('#reportTab').removeClass("active");
                    $('#uploadTab').addClass("active");
                    $('#disces_link').empty();
                    $('#listETL_link').empty();
                    $('#kb_link').empty();
                    $('#sm_link').empty();
                    $('#disces_ip').val('');
                    $('#organization').val('');
                    $('#inspector_image').empty();
                    $('#upload_image').empty();
                    $('#ms_link').empty();
                    $('#iot_link').empty();
                    $('#pd_link').empty();
                    $('#job_name').empty();
                    $('#dash_link').empty();
                    $('#time_trand_link').empty();
                    $('#iotDevice').empty();
                    $('#iotBroker').empty();
                    $('#owner').empty();
                    $('#id_row').val('');
                    $('#arcgis_link').empty();
                    $('#heatmap_link').empty();
                    $('#healthiness_c').val('');
                    $('#period').val('');
                    $('#v_type').val('');
                    $('#telephone').empty();
                    $('#web').empty();
                    $('#s1_date').empty();
                    $('#s2_date').empty();
                    $('#input_ch2').empty();
                    //$('#attr_type').val('');
                    $('#setname').val('');
                    $('#list_kpi_dash').empty();
                    $('#broker_link').empty();
                    $('#list_dashboard_link').empty();
                    $('#delay').val('');
                    $('#last_check_health').val('');
                    $('#status_1').css('color', 'black');
                    $('#status_health').css('color', 'black');
                    $('#Status_h').val('');
                    $('#Status_2').val('');
                    $('.licence_par').attr("readonly", true);
                    $('#div_edit_licence_confirm').empty();
                    $('.licence_text').text('Licence:    ');
                    $('#report_link').empty();
                    //
                    $('#public').empty();
                    $('#creator').empty();
                    $('#mailC').empty();
                    //licence
                    $('#person').empty();
                    $('#web').empty();
                     $('#admin_report_config').empty();
                     $('#data-unique_name_id').val('');
                     $('#value_unit').val('');
                    //
                    alert('Image uploaded');
                }, 1000);
            } else {
                alert('Error: File is not. an Image');
            }
        });

        //$('#confirm_modify').click(function(){
        $(document).on('click', '#confirm_modify', function () {
            //
            //var v_lic = $('#licence').val();
            var v_own = $('#owner').val();
            var v_addr = $('#address').val();
            var v_mail = $('#mail').val();
            var v_tel = $('#telephone').val();
            var v_per = $('#person').val();
            var v_web = $('#web').val();
            var v_id = $('#data-unique_name_id').val();
            //var v_irrow = $('#').val();
            var id_row_hlt = $('#name_highLevel_type').val();
            //alert(v_id);
            //<input id="id_hlt" type="text" name="id_htl"></input>
            //<input id="id_row_hlt" type="text" name="id_row_hlt"></input>
            var v_lic = CKEDITOR.instances['licence'].getData();
            //CKEDITOR.instances['licence'].config.readOnly = false;
            //
            $('#mod_lic').val(v_lic);
            $('#mod_prov').val(v_own);
            $('#mod_add').val(v_addr);
            $('#mod_mail').val(v_mail);
            $('#mod_tel').val(v_tel);
            $('#mod_ref').val(v_per);
            $('#id_mod').val(v_id);
            $('#mod_web').val(v_web);
            $('#id_hlt').val(id_row_hlt);
            //$('#id_row_hlt').val(id_row_hlt);
            var mail_mail_check = $('#mail').val();
            var web_check = $('#web').val();
            var valid_mail = true;
            var valid_url = true;
            var error_text_url = '';
            var error_text_mail = '';
            //Parameter Email o Website not valid
            if ((mail_mail_check !== "") && (mail_mail_check !== null)) {
                valid_mail = validaEmail(mail_mail_check.trim());
                $('#mod_mail').val(v_mail.trim());
            }
            //
            if ((web_check !== "") && (web_check !== null)) {
                valid_url = validURL(web_check.trim());
                $('#mod_web').val(v_web.trim());
            }
            //
            if ((valid_url === false) || (valid_mail === false)) {
                if (valid_url === false) {
                    error_text_url = " <b>website</b> Parameter not valid ";
                }
                if (valid_mail === false) {
                    error_text_mail = " <b>email</b> Parameter not valid ";
                }
                $('#check_errors').html('<div class="panel panel-danger"><div class="panel-heading"><?=_("Not Valid parameters")?></div><div class="panel-body">' + error_text_url + '<br/>' + error_text_mail + '</div></div>');
                $('#conf_edit2').prop('disabled', true);
            }
        });

        $(document).on('click', '#close_conf_edit2', function () {
            $('#exampleModal2').modal('hide');
            $('#check_errors').empty();
            $('#conf_edit2').prop('disabled', false);
        });
        $(document).on('click', '#button_conf_edit2', function () {
            $('#exampleModal2').modal('hide');
            $('#check_errors').empty();
            $('#conf_edit2').prop('disabled', false);
        });
        
        

        
        
        $('#confirmModal').on('hidden.bs.modal', function () {
            console.log('close');
        });
        $(document).on('click', '#close_healthiness_modal', function () {
            //$('#healthiness-modal').on('hidden.bs.modal', function () {
            $('#input_ch2').empty();
            $('#exampleModal2').fadeOut();
            $('#confirmModal').fadeOut();
            $('.modal').modal('hide')
            $('#healthiness_table tbody').empty();
            $('#processnameStatic').val('');
            $('#kbIp').val('');
            $('#phoenixTable').val('');
            $('#graph_uri').val('');
            $('#mail').val('');
            $('#licence').val('');
            $('#owner').val('');
            $('#address').val('');
            $('#processPath').val('');
            $('#licenceLabel').empty('');
            $('#licence').val('');
            $('#panel_lic').html('<div class="panel-heading licence_text" style="background-color: #EEE" ><?=_("Licence:")?></div><div class="panel-body" id="licence" style="background-color: #EEE"></div>');
            //
            $('#check_errors').empty();
            $('#conf_edit2').prop('disabled', false);
            $('#tab7').removeClass("active");
            $('#tab6').removeClass("active");
            $('#tab5').removeClass("active");
            $('#tab4').removeClass("active");
            $('#tab2').removeClass("active");
            $('#tab3').removeClass("active");
            $('#tab8').removeClass("active");
            $('#tab1').addClass("active");
            $('#imageTab').removeClass("active");
            $('#ownerTab').removeClass("active");
            $('#HealthinessTab').removeClass("active");
            $('#processTab').removeClass("active");
            $('#browseTab').removeClass("active");
            $('#uploadTab').addClass("active");
            $('#userTab').removeClass("active");
            $('#reportTab').removeClass("active");
            $('#disces_link').empty();
            $('#listETL_link').empty();
            $('#kb_link').empty();
            $('#sm_link').empty();
            $('#disces_ip').val('');
            $('#organization').val('');
            $('#inspector_image').empty();
            $('#upload_image').empty();
            $('#ms_link').empty();
            $('#iot_link').empty();
            $('#pd_link').empty();
            $('#job_name').empty();
            $('#dash_link').empty();
            $('#time_trand_link').empty();
            $('#iotDevice').empty();
            $('#iotBroker').empty();
            $('#owner').empty();
            $('#id_row').val('');
            $('#arcgis_link').empty();
            $('#heatmap_link').empty();
            //$('#healthinessCriteria').val('');
            $('.licence_text').text('Licence:    ');
            $('#healthiness_c').val('');
            $('#period').val('');
            $('#v_type').val('');
            //$('#attr_type').val('');
            $('#delay').val('');
            $('#last_check_health').val('');
            $('#status_1').css('color', 'black');
            $('#status_health').css('color', 'black');
            $('#Status_h').val('');
            $('#Status_2').val('');
            $('#telephone').val('');
            $('#web').val('');
            $('#report_link').empty();
            $('.licence_par').attr("readonly", true);
            //
            $('#setname').val('');
            $('#list_kpi_dash').empty();
            $('#broker_link').empty();
            $('#div_edit_licence_confirm').empty();
            $('#list_dashboard_link').empty();
            $('#s1_date').empty();
            $('#s2_date').empty();
            $('#public').empty();
            $('#creator').val('');
            $('#mailC').val('');
            //licence
            $('#person').val('');
            $('#web').val('');
            $('#value_unit').val('');
            $('#data-unique_name_id').val('');
             $('#admin_report_config').empty();
        });


        $('#healthiness-modal').on('hidden.bs.modal', function (e) {
            $('#exampleModal2').fadeOut();
            $('#confirmModal').fadeOut();
            $('.modal').modal('hide')
            $('#healthiness_table tbody').empty();
            $('#processnameStatic').val('');
            $('#kbIp').val('');
            $('#phoenixTable').val('');
            $('#graph_uri').val('');
            $('#mail').val('');
            $('#licence').val('');
            $('#owner').val('');
            $('#address').val('');
            $('#processPath').val('');
            $('#licenceLabel').empty('');
            $('#licence').val('');
            $('#panel_lic').html('<div class="panel-heading licence_text" style="background-color: #EEE" ><?=_("Licence:")?></div><div class="panel-body" id="licence" style="background-color: #EEE"></div>');
            //
            $('#check_errors').empty();
            $('#conf_edit2').prop('disabled', false);
            $('#tab7').removeClass("active");
            $('#tab6').removeClass("active");
            $('#tab5').removeClass("active");
            $('#tab4').removeClass("active");
            $('#tab2').removeClass("active");
            $('#tab3').removeClass("active");
            $('#tab8').removeClass("active");
            $('#tab1').addClass("active");
            $('#imageTab').removeClass("active");
            $('#ownerTab').removeClass("active");
            $('#HealthinessTab').removeClass("active");
            $('#processTab').removeClass("active");
            $('#browseTab').removeClass("active");
            $('#uploadTab').addClass("active");
            $('#userTab').removeClass("active");
            $('#reportTab').removeClass("active");
            $('#disces_link').empty();
            $('#listETL_link').empty();
            $('#kb_link').empty();
            $('#sm_link').empty();
            $('#disces_ip').val('');
            $('#organization').val('');
            $('#inspector_image').empty();
            $('#upload_image').empty();
            $('#ms_link').empty();
            $('#iot_link').empty();
            $('#pd_link').empty();
            $('#job_name').empty();
            $('#dash_link').empty();
            $('#time_trand_link').empty();
            $('#iotDevice').empty();
            $('#iotBroker').empty();
            $('#owner').empty();
            $('#id_row').val('');
            $('#arcgis_link').empty();
            $('#heatmap_link').empty();
            //$('#healthinessCriteria').val('');
            $('.licence_text').text('Licence:    ');
            $('#healthiness_c').val('');
            $('#period').val('');
            $('#v_type').val('');
            //$('#attr_type').val('');
            $('#delay').val('');
            $('#last_check_health').val('');
            $('#status_1').css('color', 'black');
            $('#status_health').css('color', 'black');
            $('#Status_h').val('');
            $('#Status_2').val('');
            $('#telephone').val('');
            $('#web').val('');
            $('.licence_par').attr("readonly", true);
            //
            $('#input_ch2').empty();
            $('#setname').val('');
            $('#list_kpi_dash').empty();
            $('#broker_link').empty();
            $('#div_edit_licence_confirm').empty();
            $('#list_dashboard_link').empty();
            $('#s1_date').empty();
            $('#s2_date').empty();
            $('#public').empty();
            $('#creator').val('');
            $('#mailC').val('');
            //licence
            $('#person').val('');
            $('#web').val('');
             $('#admin_report_config').empty();
            $('#data-unique_name_id').val('');
            $('#value_unit').val('');
            console.log('dismiss');
        });
        // do something...
//});

        $(window).resize(function () {
            if ($(window).width() < 1200) {
                $('#right').css('float', 'left');

            }

            if ($(window).width() > 1200) {
                $('#right').css('float', 'right');
            }
            if ($(window).width() < 1534) {
                var width = $(window).width() - 20;
                width = width + 'px';
                $('#widgetWizardTableContainer').css('width', width);
                $('#widgetWizardTable').css('width', width);
            }
            if ($(window).width() < 1200) {
                var margin = document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
                var margin = margin.substring(0, margin.length - 2);
                var width = $(window).width() - (parseInt(margin) * 2);
                var headerwidth = width - 60.75;
                var widthpx = width + 'px';
                var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
                $('#timetrend').css('width', widthpx);

                $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', widthpx);

                $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', widthpx);

                $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', Math.floor(headerwidth / width * 100) + "%");
                $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left", widthpx);
            }
            if ($(window).width() > 1200) {
                $('#timetrend').css('width', '1200px');
                $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', '1200px');
                $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', '1200px');
                $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', '95%');
                $('#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt').css('left', '1200px');
            }
            if ($(window).width() > 1534) {
                $('#widgetWizardTableContainer').css('width', '1534px');
                $('#widgetWizardTable').css('width', '1534px');
            }

        });

        function validURL(str) {
            var regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
            if (regexp.test(str)) {
                console.log('str:   ' + str);
                console.log('corretto');
                return true;
            } else
            {
                console.log('str:   ' + str);
                console.log('no corretto');
                return false;
            }
            //return pattern.test(str);
        }


        function validaEmail(email) {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            if (emailReg.test(email)) {
                console.log('str:   ' + email);
                console.log('corretto');
                return true;
            } else {
                console.log('str:   ' + email);
                console.log('no corretto');
                return false;
            }
        }


        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }



        function check_iot(link) {
            console.log('Link: ' + link);
            var string_l = link.length;
            console.log('string_l:     ' + string_l);
            if (string_l < 64) {
                if (link.startsWith("nr")) {
                    $('#iot_link').html('<a href="https://iot-app.snap4city.org/nodered/' + link + '" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;"><?=_("Link to Iot App")?></a>');
                }
            }
            //https://iot-app.snap4city.org/nodered/
        }

        function function_dashKpi(value_name) {
            //
            if ((value_name !== 'undefined') && (value_name !== undefined) && (value_name !== null)) {
                var id_kpi = value_name;
                var res = value_name.split("/");
                var id_kpi = value_name;
                var res = value_name.split("/");
                var l = res.lenght;
                if (l > 0) {
                    id_kpi = value_name[l];
                } else {
                    id_kpi = value_name;
                }
                console.log('FUNZIONE KPI_DASH');
                //
                //var id_kpi =value_name;
                $.ajax({
                    async: true,
                    type: 'GET',
                    // url: 'getServiceData.php',
                    url: 'getkpilist.php',
                    data: {
                        type: 'DashKpi',
                        service: id_kpi
                    },
                    success: function (data) {
                        console.log('SUCCESS');
                        var json_data = data.dashboards;
                        //console.log(data);
                        if (data !== 'null') {
                            var json_data = JSON.parse(data);
                            //console.log(json_data.dashboards);
                            var result = json_data.dashboards;
                            var link_dash = "";
                            var len = 0;
                            len = Object.keys(result).length;
                            //console.log('len: '+len);
                            if (len > 0) {
                                for (var i = 0; i < len; i++) {
                                    //var array_dash = result[i]['dashboardId'];
                                    var array_dash_Name = "";
                                    if (result[i]['dashboardName'] !== null) {
                                        array_dash_Name = result[i]['dashboardName'];
                                        //console.log(array_dash_Name);
                                        //console.log(JSON.Parse(array_dash_Name));
                                        //var json_data = JSON.stringify(array_dash);
                                        //
                                        //
                                        var arr_l = array_dash_Name.length;
                                        if (arr_l > 0) {
                                            //var json_data_name = JSON.stringify(array_dash_Name);
                                            //var json_data_name = Object.entries(array_dash_Name);
                                            //console.log(json_data_name);
                                            for (var z = 0; z < arr_l; z++) {
                                                //arr_l
                                                var ind = "https://iot-app.snap4city.org/nodered/" + array_dash_Name[z];
                                                link_dash = link_dash + '<li><a href="' + ind + '" Target= "_blank"><?=_("Link to IotApp")?> ' + array_dash_Name[z] + '</a></li>';
                                            }
                                        }
                                    } else {
                                        link_dash = link_dash + '<li><a href="#" Target= "_blank"><?=_("Link to IotApp Not founded")?></a></li>';
                                    }
                                    //alert(json_data);
                                }
                            } else {
                                console.log('FAILED');
                                link_dash = '<li><a href="#"><?=_("No dashboards connected")?></a></li>';
                            }
                            var content = '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?=_("List of IotApp")?><span class="caret"></span></button><ul class="dropdown-menu">' + link_dash + '</ul></div>';
                            $('#list_kpi_dash').html(content);
                        }
                    }
                });
            } else {
                var link_dash = '<li><a href="#" Target= "_blank"><?=_("Link to IotApp Not founded")?></a></li>';
                var content = '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?=_("List of IotApp")?><span class="caret"></span></button><ul class="dropdown-menu">' + link_dash + '</ul></div>';
                $('#list_kpi_dash').html(content);
            }
        }
        
        function function_dev(unique_id){
                    //console.log('FUNCTION DEV: '+unique_id);
                    //CONTROLLI SULL'UNIQUE ID
                    var n = unique_id.includes(":");
                    console.log(n);
                    //
                    if (n){
                        //
                    }else{
                        var iotBroker = $('#iotBroker').val();
                        var organization = $('#organization').val();
                        unique_id = organization+':'+iotBroker+':'+unique_id;
                    }
                     $.ajax({
                    async: true,
                    type: 'GET',
                    // url: 'getServiceData.php',
                    url: 'getkpilist.php',
                    data: {
                        type: 'Devices',
                        service: unique_id
                    },
                    success: function (data) {
                        console.log('SUCCESS');
                        var json_data = data.dashboards;
                        //console.log(data);
                        if (data !== 'null') {
                            var json_data = JSON.parse(data);
                            //console.log(json_data.dashboards);
                            var result = json_data.dashboards;
                            var link_dash = "";
                            var len = 0;
                            len = Object.keys(result).length;
                            //console.log('len: '+len);
                            if (len > 0) {
                                for (var i = 0; i < len; i++) {
                                    //var array_dash = result[i]['dashboardId'];
                                    var array_dash_Name = "";
                                    if (result[i]['dashboardName'] !== null) {
                                        array_dash_Name = result[i]['dashboardName'];
                                        //console.log(array_dash_Name);
                                        //console.log(JSON.Parse(array_dash_Name));
                                        //var json_data = JSON.stringify(array_dash);
                                        //
                                        //
                                        var arr_l = array_dash_Name.length;
                                        if (arr_l > 0) {
                                            //var json_data_name = JSON.stringify(array_dash_Name);
                                            //var json_data_name = Object.entries(array_dash_Name);
                                            //console.log(json_data_name);
                                            for (var z = 0; z < arr_l; z++) {
                                                //arr_l
                                                var ind = "https://iot-app.snap4city.org/nodered/" + array_dash_Name[z];
                                                link_dash = link_dash + '<li><a href="' + ind + '" Target= "_blank"><?=_("Link to Iot Apps")?> ' + array_dash_Name[z] + '</a></li>';
                                            }
                                        }
                                    } else {
                                        link_dash = link_dash + '<li><a href="#" Target= "_blank"><?=_("Link to IotApp Not founded")?></a></li>';
                                    }
                                    //alert(json_data);
                                }
                            } else {
                                console.log('FAILED');
                                link_dash = '<li><a href="#"><?=_("No dashboards connected")?></a></li>';
                            }
                            var content = '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?=_("List of Iot Apps")?><span class="caret"></span></button><ul class="dropdown-menu">' + link_dash + '</ul></div>';
                            $('#list_kpi_dash').html(content);
                        }
                    }
                });
        }

        function function_dashboard(value_name) {
            var value_name2 = value_name;
            console.log('value_name2  ' + value_name2);
            if (value_name2.includes(":")) {
                var res = value_name2.split(":");
                var ind = "";
                var name_wid = "";
                var result = "";
                /***/
                name_wid = res[2];
                /***/
            } else {
                name_wid = value_name2;
            }
            $.ajax({
                async: false,
                type: 'GET',
                //url: url_parameters,
                dataType: 'json',
                url: 'getServiceData.php',
                data: {
                    //type: 'From Dashboard to IOT Device',
                    type: 'Dashboard list',
                    service: name_wid
                },
                success: function (data) {
                    //var json_data = JSON.stringify(data.name);
                    var json_data = data.name;
                    result = json_data;
                    //Inizio_List

                    //var array_dash = data.dashboards[0]['dashboardId'];
                    //var json_data = JSON.stringify(array_dash);
                    //dashboardName
                    var json_data = data.dashboards;
                    result = json_data;
                    var link_dash = "";
                    var len = result.length;
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var array_dash = data.dashboards[i]['dashboardId'];
                            var array_dash_Name = data.dashboards[i]['dashboardName'];
                            var json_data = JSON.stringify(array_dash);
                            var json_data_name = JSON.stringify(array_dash_Name);
                            var ind = "../view/index.php?iddasboard=" + btoa(array_dash);
                            link_dash = link_dash + '<li><a href="' + ind + '" Target= "_blank">Link to dashboard ' + json_data_name + '</a></li>';
                            //alert(json_data);
                        }
                    } else {
                        link_dash = '<li><a href="#"><?_("No dashboards connected")?></a></li>';
                    }
                    //result = array_dash;
                    //var content = '<div class="btn-group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Link to Dashboard List</button><div class="dropdown-menu"><div class="link-list-wrapper"><ul class="link-list"><li><a class="list-item" href="#"><span>Azione 1</span></a></li></ul></div></div></div>';

                    var content = '<div class="dropdown"><button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?=_("List of Dashboard")?><span class="caret"></span></button><ul class="dropdown-menu">' + link_dash + '</ul></div>';

                    $('#list_dashboard_link').html(content);
                    //FIne List

                    return result;
                }
            });
            /*
             * @type String
             */
            //
            /*
             if ((result !== "") || (result !== 'no') || (result !== undefined)) {
             ind = "../view/index.php?iddasboard=" + btoa(result);
             } else {
             ind = "dashboards.php";
             }
             if (result === 'no') {
             ind = "dashboards.php";
             }
             return 	ind;*/
        }
    </script>
</body>

</html>

<?php } else {
    include('../s4c-legacy-management/inspectorOS.php');
}
?>