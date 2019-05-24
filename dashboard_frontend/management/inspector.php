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
    error_reporting(E_ERROR | E_NOTICE);

    $lastUsedColors = null;
    $dashId = $_REQUEST['dashboardId'];
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
    }   
    
    
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
       

