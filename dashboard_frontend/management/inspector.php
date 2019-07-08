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
   
   include('process-form.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
   
   if(!isset($_SESSION))
   {
       session_start();
   }

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
    }   */
    
    
?>
<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DataInspector</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php echo time();?>" type="text/css" />
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
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

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
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet"/>
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
    <link href="../css/leaflet.zoomdisplay.css" rel="stylesheet"/>
    
   <!-- Dot dot dot -->
   <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>
   
   <!-- Bootstrap slider -->
   <script src="../bootstrapSlider/bootstrap-slider.js"></script>
   <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>
   
   <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=<?php echo time();?>">
    
    <!-- Text fill -->
    <script src="../js/jquery.textfill.min.js"></script> 
    
    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
   <link href="../css/widgetCtxMenu_1.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/widgetDimControls_1.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/widgetHeader_1.css?v=<?php echo time();?>" rel="stylesheet">
    <script src="../js/widgetsCommonFunctions.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <link href="../css/chat.css?v=<?php echo time();?>" rel="stylesheet">
    <script src="../js/bootstrap-ckeditor-.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    
</head>

<style type="text/css">
    .left{
        float:left;
    }
    .right{
        float: right;
    }
    
.switch {
  position: relative;
  display: inline-block;
  width: 82px;
  height: 20px;
}

.switch input {display:none;}

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
.fixMapon
{
  display: none;
}

.fixMapon, .fixMapoff
{
  color: white;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
  left: 50%;
  font-size: 10px;
  font-family: Verdana, sans-serif;
}

input:checked+ .slider .on
{display: block;}

input:checked + .slider .off
{display: none;}

/*--------- END --------*/

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;}
</style>
<body style="overflow-y: hidden !important">

    <!-- Inizio dei modali --> 
    <!-- Modale wizard -->
            <div class="modal-content modalContentWizardForm">
             <!--   <div class="modalHeader centerWithFlex">
                  &nbsp;&nbsp;&nbsp;
                </div>  -->
            
                <div id="addWidgetWizardLabelBody" class="body">
                    <?php include "addWidgetWizardInclusionCode2.php" ?>
                    
                    <div>
                        <div id="left" class="left">
                            <?php include "../widgets/widgetSingleContent_1.php"; ?>
                            
                            
                            
                        </div>
                        <div id="right" class="right">
                            <?php include "../widgets/widgetTimeTrend_1.php"; ?>
                            
                        </div>
                    </div>
                </div>
            </div>    <!-- Fine modal content -->
    
        <!--</div> <!-- Fine modal dialog -->
    <!--</div><!-- Fine modale -->
    <!-- Fine modale wizard -->
    
    
    
    <div id="changeMetricCnt">
        <table id="changeMetricTable" class="addWidgetWizardTable table table-striped dt-responsive nowrap"> 
            <thead class="widgetWizardColTitle">
                <tr>  
                    <th id="hihghLevelTypeColTitle" class="widgetWizardTitleCell" data-cellTitle="HighLevelType">High-Level Type</th>  
                    <th class="widgetWizardTitleCell" data-cellTitle="Nature">Nature</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="SubNature">Subnature</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueType">Value Type</th>   
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueName">Value Name</th>      
                    <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri">Instance URI</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="DataType">Data Type</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastDate">Last Date</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastValue">Last Value</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Healthiness">Healthiness</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="InstanceUri">Instance URI</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Parameters">Parameters</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Id">Id</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="LastCheck">Last Check</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="GetInstances"></th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Ownership">Ownership</th>
                </tr>  
            </thead>
        </table>
        
        <h6>Selected</h6>
        
        <table id="changeMetricSelectedRowsTable" class="addWidgetWizardTableSelected table table-striped dt-responsive nowrap"> 
            <thead class="widgetWizardColTitle">
                <tr>
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueType">Value Type</th>   
                    <th class="widgetWizardTitleCell" data-cellTitle="ValueName">Value Name</th>      
                    <th class="widgetWizardTitleCell" data-cellTitle="LastValue">Last Value</th>
                    <th class="widgetWizardTitleCell" data-cellTitle="Remove">Remove</th>
                </tr>  
            </thead>
        </table>
    </div>
    
    <!-- Fine dei modali -->
    <!-- MODALE HEALTHINESS -->
    <?php
    if(($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin")){
        echo('<div class="modal fade bd-example-modal-lg" id="healthiness-modal" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="background-color: rgba(108, 135, 147, 1);">
                            <div class="modal-content modal-lg" style="background-color: rgba(108, 135, 147, 1);">
                                <div class="modal-header" style="background-color: #576c75; color: white;"><b>Data sources Details</b></div>           
                            <div role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist" style="background-color: rgba(108, 135, 147, 1);">
                                        <li role="presentation" id="tab1" class="active"><a href="#uploadTab" aria-controls="uploadTab" role="tab" data-toggle="tab" style="background-color: rgba(108, 135, 147, 1);">Device</a>
                                        </li>
                                        <li role="presentation" id="tab2"><a href="#browseTab" aria-controls="browseTab" role="tab" data-toggle="tab" style="background-color: rgba(108, 135, 147, 1);">Values</a>
                                        </li>
                                        <li role="presentation" id="tab3"><a href="#processTab" aria-controls="processTab" role="tab" data-toggle="tab" style="background-color: rgba(108, 135, 147, 1);">Process</a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="uploadTab">
                                                    <div class="modal-body">
                                                            <div class="input-group"><span class="input-group-addon">GPS Coordinates: </span><input id="gps" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">High-Level Type: </span><input id="name_highLevel_type" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Nature: </span><input id="name_Nature" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Subnature: </span><input id="name_Subnature" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Value Name: </span><input id="data-unique_name_id" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Datasource: </span><input id="data_source" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Ownership: </span><input id="ownership" type="text" class="form-control" readonly/></div>
                                                    </div>
                                          </div>
                                        <div role="tabpanel" class="tab-pane" id="browseTab">
                                                        <div class="modal-body">
                                                            <div class="input-group"><span class="input-group-addon">Value Type: </span><input id="data-low_level_type" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Data Type: </span><input id="data_unit" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Last Date: </span><input id="last_date" type="text" class="form-control" readonly/></div><br />
                                                            <div class="input-group"><span class="input-group-addon">Last Value: </span><input id="last_value" type="text" class="form-control" readonly/></div><br />
                                                                <table id="healthiness_table" class="addWidgetWizardTable table table-striped dt-responsive nowrap">
                                                                    <thead class="widgetWizardColTitle">
                                                                        <tr>
                                                                            <th class="widgetWizardTitleCell">Value Type</th>
                                                                            <th class="widgetWizardTitleCell">Healthy</th>
                                                                            <th class="widgetWizardTitleCell">Delay (s)</th>
                                                                            <th class="widgetWizardTitleCell">Reason</th>
                                                                            <th class="widgetWizardTitleCell">Healthiness Criteria</th>
                                                                            <th class="widgetWizardTitleCell">Refresh Rate (s)</th>
                                                                            <th class="widgetWizardTitleCell">Data type</th>
                                                                            <th class="widgetWizardTitleCell">Unit</th>
                                                                            <th class="widgetWizardTitleCell">Value</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody style="background-color: #F5F5F5">
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="processTab">
                                                        <div class="modal-body">
                                                            <!-- data graph -->
                                                                    <div class="input-group"><span class="input-group-addon">Process Name Static: </span><input id="processnameStatic" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Knowledge Base IP: </span><input id="kbIp" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Disces IP: </span><input id="disces_ip" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Disces Process file path: </span><input id="processPath" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Phoenix table: </span><input id="phoenixTable" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Graph Uri: </span><input id="graph_uri" type="text" class="form-control" readonly/></div><br />
                                                                    <!-- -->
                                                                    <div class="input-group"><span class="input-group-addon">Licence: </span><input id="licence" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Owner: </span><input id="owner" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Address: </span><input id="address" type="text" class="form-control" readonly/></div><br />
                                                                    <div class="input-group"><span class="input-group-addon">Mail: </span><input id="mail" type="text" class="form-control" readonly/></div><br />
                                                                    <!-- -->
                                                                    <div>
                                                                        <div id="kb_link" style="float: left;"></div><div id="Kbase_link" style="margin-right: 10 px;"></div>
                                                                        <div id="disces_link" style="float: left;"></div><div id="disces_link" style="padding-right: 10 px;"></div>
                                                                        <div id="sm_link" style="float: left;"></div><div id="sm_link" style="padding-right: 10 px;"></div>
                                                                    </div>
                                                            <!-- -->
                                                        </div>
                                        </div>
                                </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn cancelBtn cancelView" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                        </div>
                    </div>');
    }else{
                        //nothing
    }
?>
    <!-- -->
    <script type='text/javascript'>
    if($(window).width()< 1200){
        $('#right').css('float','left');           
    }
    if($(window).width()< 1534){
        var width = $(window).width()-20;
        width=width+'px';
        $('#widgetWizardTableContainer').css('width',width);
        $('#widgetWizardTable').css('width',width);
    }
    if($(window).width()< 1200){
        var margin = document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
        var margin=margin.substring(0, margin.length-2);
        var width = $(window).width()-(parseInt(margin)*2);
        var headerwidth = width-60.75;
        var widthpx=width+'px';
        var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
        $('#timetrend').css('width',widthpx);

        $('#DCTemp1_24_widgetTimeTrend6351_div').css('width',widthpx);

        $('#DCTemp1_24_widgetTimeTrend6351_header').css('width',widthpx);

        $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width',  Math.floor(headerwidth/width*100) + "%");
        $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left",widthpx);
   }
           
    //fa-circle
    $(document).on('click','.fa-circle',function(){
            //
            
            //var id0 = $(this).parent().parent().html();
            var high_level = $(this).parent().parent().attr('data-high_level_type');
            var name_Nature =  $(this).parent().parent().attr('data-nature');
            var name_Subnature = $(this).parent().parent().attr('data-sub_nature');
            var data_unique_name_id = $(this).parent().parent().attr('data-unique_name_id');
            var data_unit = $(this).parent().parent().attr('data-unit');
            var data_last_value = $(this).parent().parent().attr('data-last_value');
            var data_latitude =$(this).parent().parent().attr('data-latitude');
            var data_longitude =$(this).parent().parent().attr('data-longitude');
            var parameters =$(this).parent().parent().attr('data-parameters');
            var data_instance_uri=$(this).parent().parent().attr('data-instance_uri');
            var data_organizations = $(this).parent().parent().attr('data-organizations');
            var data_low_level_type = $(this).parent().parent().attr('data-low_level_type');
            var data_get_instances = $(this).parent().parent().attr('data-get_instances');
            var last_date = $(this).parent().parent().attr('last_date');
            var ownership = $(this).parent().parent().attr('ownership');
            var data_source;
            if((parameters.includes("iot"))||(name_Subnature.includes("IoT"))){
                data_source = "IoT";
            }else{
                data_source="ETL";
            }
            $('#name_highLevel_type').val(high_level);
            $('#data_source').val(data_source);
            $('#name_Nature').val(name_Nature);
            $('#name_Subnature').val(name_Subnature);
            $('#data-unique_name_id').val(data_unique_name_id);
            $('#data_unit').val(data_unit);
            $('#last_date').val(last_date);
            $('#ownership').val(ownership);
            $('#gps').val(data_longitude+', '+data_latitude);
            $('#last_value').val(data_last_value);
            $('#data-low_level_type').val(data_low_level_type);
           // $('#data-get_instances').val(data_get_instances);
            //console.log('high_level: '+high_level);
            //alert(parameters);
            //if(high_level === 'Sensor'){
                var serviceUrivalue = "";
                var check_para = parameters.includes('http');
                    if(check_para){
                            var url_parameters = parameters+'&healthiness=true';                    
                            $.ajax({
                                    async: true,
                                    type: 'GET',
                                    //url: url_parameters,
                                    dataType: 'json',
                                    url: 'getServiceData.php',
                                    data:{service:url_parameters},
                                    success: function (data) {
                                        var json_data = JSON.stringify(data.healthiness);
                                        console.log(json_data);
                                        var obj = Object.values(data);
                                        var obj2 = Object.values(data.healthiness);
                                       // console.log(obj[2][data_low_level_type]);
                                       var keys2 = Object.keys(data.healthiness);
                                       var key4 = Object.values(data.Service.features[0]);
                                       var key5 =(key4[2].realtimeAttributes);
                                       //
                                       /*
                                       var serviceUri0 = Object.values(key4[2]);
                                        var serviceUri = serviceUri0[0];
                                       var res = serviceUri.split("/");
                                       var l = res.length;
                                       serviceUrivalue = res[l-1];
                                        */
                                       //
                                       var key3 = Object.values(data.realtime.results.bindings);
                                       var value_td = "";
                                        /*DECODIFICARE data.healthiness*/
                                        for(var y=0; y<obj2.length; y++){
                                            var name = keys2[y];
                                            if (key3[0][name]){
                                                value_td = key3[0][name]['value'];
                                                //key3[0][name]['value']
                                            }else{
                                                value_td = ""; 
                                            }
                                            var value_unit_td = "";
                                            var data_type_td = "";
                                            var healthiness_criteria = "";
                                            var value_refresh_rate = "";
                                            if (key5[name]){
                                                value_unit_td = key5[name]['value_unit'];
                                                data_type_td = key5[name]['data_type'];
                                                healthiness_criteria= key5[name]['healthiness_criteria'];
                                                value_refresh_rate = key5[name]['value_refresh_rate'];
                                            }else{
                                                value_unit_td = "";
                                                data_type_td = "";
                                                healthiness_criteria = "";
                                                value_refresh_rate = "";
                                            }
                                            $('#healthiness_table tbody').append('<tr><td>'+keys2[y]+'</td><td>'+obj2[y]['healthy']+'</td><td>'+obj2[y]['delay']+'</td><td>'+obj2[y]['reason']+'</td><td>'+healthiness_criteria+'</td><td>'+value_refresh_rate+'</td><td>'+data_type_td+'</td><td>'+value_unit_td+'</td><td>'+value_td+'</td></tr>');
                                        }
                                        //serviceUri
                                        var process_name_ST = data.process_name_ST;
                                        var processPath = data.process_path;
                                        var kbIp = data.KB_Ip;
                                        var mail = data.mail;
                                        var phoenixTable=data.phoenix_table;
                                        var owner=data.owner;
                                        var licence=data.licence;
                                        var address = data.address;
                                        var graph_uri = data.Graph_Uri;
                                        var telephone = data.telephone;
                                        var disces_ip= data.disces_ip;
                                        var total_data = data.disces_data;
                                        //var healthiness = data.healthiness_criteria;
                                        //var period = data.value_refresh_rate;
                                        $('#processnameStatic').val(process_name_ST);
                                        $('#processPath').val(processPath);
                                        $('#kbIp').val(kbIp);
                                        $('#mail').val(mail);
                                        $('#phoenixTable').val(phoenixTable);
                                        $('#licence').val(licence);
                                        $('#owner').val(owner);
                                        $('#address').val(address);
                                        $('#graph_uri').val(graph_uri);
                                        $('#telephone').val(telephone);
                                        $('#disces_ip').val(disces_ip);
                                        //$('#period').val(period);
                                       // $('#healthinessCriteria').val(healthiness);
                                        //
                                        var job_name = data.jobName;
                                        var job_group = data.jobGroup;
                                        var disces_ip_test = data.ip_disc;
                                        var link='http://'+disces_ip_test+'/sce/newJob.php?jobName='+job_name+'&jobGroup='+job_group;
                                        //TEST//
                                        var sm = parameters.replace('&format=json','&format=html');
                                        var link_kb=graph_uri;
                                        //
                                        if((total_data > 0)&&($('#disces_link').empty())){
                                        $('#disces_link').append('<a href="'+link+'" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Disces</a>');
                                        }
                                        if((data.Graph_Uri) && $('#kb_link').empty()){
                                            $('#kb_link').append('<a href="'+link_kb+'" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Knowledge Base</a>');
                                        }
                                        if($('#sm_link').empty()){
                                         $('#sm_link').append('<a href="'+sm+'" Target= "_blank" class="btn btn-primary" role="button" style="margin-right: 10px;">Link to Service Map</a>');
                                     }
    //
                            }
                         });   
                               //
                        }else{
                              $('#healthiness_table tbody').html('<tr><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td></tr>');  
                        } 
                $('#healthiness-modal').modal('show');
            /*}else{
                    $('#healthiness_table tbody').html('<tr><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td><td>Not available</td>Not available<td></td></tr>'); 
            }*/
            //alert(id0);
            //$('#healthiness-modal').modal('show'); 
            //alert("Healthiness: "+id0);
                //                 
	});	
    
    $(document).on('click','#modify_healthness',function(){
        alert('modify healthiness');
    });
    
    $(document).on('click','.cancelView',function(){
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
        $('#tab2').removeClass("active");
        $('#tab3').removeClass("active");
        $('#tab1').addClass("active");
        $('#processTab').removeClass("active");
        $('#browseTab').removeClass("active");
        $('#uploadTab').addClass("active");
        $('#disces_link').empty();
        $('#kb_link').empty();
        $('#sm_link').empty();
        $('#disces_ip').val('');
        //$('#period').val('');
        //$('#healthinessCriteria').val('');
    });
    
    $(window).resize(function(){
           if($(window).width()< 1200){
               $('#right').css('float','left');
               
           }
           
           if($(window).width()> 1200){
               $('#right').css('float','right');
           }
           if($(window).width()< 1534){
               var width = $(window).width()-20;
               width=width+'px';
               $('#widgetWizardTableContainer').css('width',width);
               $('#widgetWizardTable').css('width',width);
           }
           if($(window).width()< 1200){
               var margin=document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
               var margin=margin.substring(0, margin.length-2);
               var width = $(window).width()-(parseInt(margin)*2);
               var headerwidth = width-60.75;
               var widthpx=width+'px';
               var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
               $('#timetrend').css('width',widthpx);

               $('#DCTemp1_24_widgetTimeTrend6351_div').css('width',widthpx);
               
               $('#DCTemp1_24_widgetTimeTrend6351_header').css('width',widthpx);
               
               $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width',  Math.floor(headerwidth/width*100) + "%");
               $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left",widthpx);
           }
           if($(window).width()> 1200){
               $('#timetrend').css('width','1200px');
               $('#DCTemp1_24_widgetTimeTrend6351_div').css('width','1200px');
               $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', '1200px');
               $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', '95%');
               $('#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt').css('left', '1200px');
           }
           if($(window).width()> 1534){
               $('#widgetWizardTableContainer').css('width','1534px');
               $('#widgetWizardTable').css('width','1534px');
           }
           
       });       
    </script>
</body>
</html>
       

