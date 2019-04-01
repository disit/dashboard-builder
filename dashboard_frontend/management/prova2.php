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
</style>
<body style="overflow-x: hidden "> 
     
    <?php //include "sessionExpiringPopup.php" ?>
  <!--
    <input type="hidden" id="draggingWidget" value="false">
    
    <div id="dashBckCnt">
       <div id="dashBckOverlay">
        
       </div> 
    </div>
    
    <div id="editDashboardMenu">
        <div class="row">
  
            <a id="link_start_wizard" href="#" data-toggle="modal">                 
                <div class="col-xs-6 col-sm-3 col-lg-1 dashEditMenuItemCnt">
                    <div class="dashEditMenuIconCnt col-xs-2 centerWithFlex"><i class="fa fa-magic" style="color: #66efff"></i></div>
                    <div class="dashEditMenuTxtCnt col-xs-10 centerWithFlex">Wizard</div>
                </div> 
            </a>
       </div>
        </div>
	 -->
            <?php
                if ($_SESSION['loggedRole'] == "Manager"){
                   echo '<i id="chatBtn" data-status="closed"></i>';
                }else{
                    echo '<i class="fa fa-comment-o" id="chatBtn" data-status="closed" style="display: none"></i>';
                    echo '<i class="fa fa-exclamation-triangle" id="chatBtnError" data-status="closed" style="display: none"></i>';
                }
                ?>

    <!-- Inizio dei modali --> 
    <!-- Modale wizard -->
    <!--<div class="modal fade" id="addWidgetWizard2" tabindex="-1" role="dialog" aria-labelledby="addWidgetWizardLabel" aria-hidden="true">-->
        <!--<div class="modal-dialog" role="document">-->
            
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
                    <?php// include "../widgets/widgetSingleContent_1.php"; ?>
                    <?php// include "../widgets/widgetTimeTrend_1.php"; ?>
                </div>
            <!--    <div id="modalStartWizardFooter" class="modal-footer">
                   <div class="row">
                       
                        <div class="col-xs-8 col-xs-offset-2 centerWithFlex">
                            <button type="button" id="addWidgetWizardPrevBtn" name="addWidgetWizardPrevBtn" class="btn confirmBtn">Prev</button>
                            <button type="button" id="addWidgetWizardNextBtn" name="addWidgetWizardNextBtn" class="btn confirmBtn">Next</button>
                        </div>   
                       <div class="col-xs-8 col-xs-offset-2 centerWithFlex">
                            <button type="button" id="addWidgetWizardCancelBtn" class="btn cancelBtn"><a href="javascript:close_window();">Close</a></button>
                        </div>   
                   </div>  
                </div>-->
                
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
        
         
                
                
                
    //$("#addWidgetWizard2").modal("show");    
    /*
        var gridster, num_cols, indicatore, datoTitle, datoSubtitle, datoColor, datoWidth, datoRemains, nuovaDashboard, headerFontSize, headerModFontSize, subtitleFontSize, clockFontSizeMod, dashboardName, logoFilename, logoLink, temp, widgetsArray, nomeComune, metricType = null;
                var array_metrics = new Array();
                var array_metricsNR = new Array();
                var informazioni = new Array();
                var elencoScheduler = new Array();
                var elencoJobsGroupsPerScheduler = [[],[],[]];
                var firstFreeRow = 1;
                var comuniLammaArray = new Array();
                var dashboardWizardData = new Array();  // PANTALEO 28/03/2018
                var addWidgetConditionsArray = new Array();
                var editWidgetConditionsArray = null; //Lascialo così, il vettore viene istanziato ad ogni apertura di modale.
        */
       
       
       
              function close_window(){
           close();
       }
       
       $(window).resize(function(){
           if($(window).width()< 700){
               $('#right').css('float','left');
           }
           
           if($(window).width()> 700){
               $('#right').css('float','right');
           }
       })
    
       
       /*
        console.log("io2");
        $("#button_close_popup").click(function()
                {
                    location.reload();
                });
                
                $("#closeModifyWidgetBtn").click(function()
                {
                    location.reload();
                });
                
                function updateLastUsedColors(newColor)
                {
                    $('.lastUsedColorsRow').each(function(j){
                        if($(this).find('div.ctxMenuPaletteColor').eq(0).attr('data-color') !== newColor)
                        {
                            var lastIndex = parseInt($(this).find('div.ctxMenuPaletteColor').length - 1);
                            for(var i = lastIndex; i > 0; i--)
                            {
                                $(this).find('div.ctxMenuPaletteColor').eq(0).empty();
                                $(this).find('div.ctxMenuPaletteColor').eq(i).attr('data-color', $(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color'));
                                $(this).find('div.ctxMenuPaletteColor').eq(i).css('background-color', $(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color'));
                                if($(this).find('div.ctxMenuPaletteColor').eq(i-1).attr('data-color') === 'rgba(255, 255, 255, 0)')
                                {
                                    $(this).find('div.ctxMenuPaletteColor').eq(i).empty();
                                    $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadWhite"></div>');
                                    $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadGrey"></div>');
                                    $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadGrey"></div>');
                                    $(this).find('div.ctxMenuPaletteColor').eq(i).append('<div class="transQuadWhite"></div>');
                                } 
                            }

                            $(this).find('div.ctxMenuPaletteColor').eq(0).attr('data-color', newColor);
                            $(this).find('div.ctxMenuPaletteColor').eq(0).css('background-color', newColor);
                            if(newColor === 'rgba(255, 255, 255, 0)')
                            {
                                $(this).find('div.ctxMenuPaletteColor').eq(0).empty();
                                $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadWhite"></div>');
                                $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadGrey"></div>');
                                $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadGrey"></div>');
                                $(this).find('div.ctxMenuPaletteColor').eq(0).append('<div class="transQuadWhite"></div>');
                            }       
                        }
                    });
                }
        console.log("io3");
        /*
        $("#link_start_wizard").click(function()
         {
             $("#addWidgetWizard").modal("show");
         });
         */
        
       
            /*
            var hospitalList = null; 
                    var gisTargetCenterMapDiv = null; 
                    var gisTargetCenterMapDivRef = null; 
                    var gisTargetCenterMapDivRefM = null; 
                    var gisTargetCenterMapDivM = null;
                    var headerVisible = null;
                    var editorsArray = new Array();
                    var editorsArrayM = new Array();
                    var authorizedPages = null;
                    var embedViewUrl, sampleIframe = null;
                    var defaultFontFamilyArray = ["Auto", "Arial", "Calibri", "Comic Sans MS", "Courier New", "Digital", "Open Sans", "Times New Roman", "Verdana"];
                    var fontFamilyArray = [];
                    var queryFieldFromDb = null;
                    var internalDest = false;
                    var dashboardTitlesList = null;
                    var dashBckImg = null;
                    var useBckImg = null;
                    var backOverlayOpacity = null;
                    var changeMetricTable = null;
                    var draggedWidgetId, dashboardParams, dashboardWidgets, dashboardViewMode, gridsterCellW, gridsterCellH, widgetsContainerWidth, gridColor, addWidgetWizardMapRef = null;
                    var choosenWidgetMetricIconName = null;
                    var choosenWidgetType = null;
                    var mainWidget, targetWidget, unit, icon, mono_multi, widgetCategory = null;
                    $('#dashBckCnt').css("height", ($(window).height() - $('#dashboardViewHeaderContainer').height()) + "px");
                    
                    var changeMetricSelectedRows = {};
                    var firstRowId = null;
                    var changeMetricTableAlreadyLoaded = 0;
              */  
            
         /*   
            
            $("#link_start_wizard").click(function()
         {*/
            
             
/*        });
           */ 
       // });
       
        </script>
   </body>
</html>
       

<!--
<script type='text/javascript'>
    $(document).ready(function ()
    {
        var widgetWizardTable, widgetWizardSelectedRowsTable, addWidgetWizardMapRef, widgetWizardPageLength = null;
        var gisLayersOnMap = {};
        var widgetWizardSelectedRows = {};
        var widgetWizardSelectedUnits = [];
        var choosenWidgetIconName = null;
        var choosenDashboardTemplateName = null;
        var choosenDashboardTemplateIcon = null;
        var widgetWizardMapSelection = null;
        var currentSelectedRowsCounter = 0;
        var selectedTabIndex = 0;
        var firstTabIndex = 0;
        var tabsQt = 3;
        console.log("Entrato in inclusioncode3");

        
        //False se è violata, true se è rispettata
        var validityConditions = {
            dashTemplateSelected: false,
            widgetTypeSelected: false,
            brokerAndNrRowsTogether: true,
            atLeastOneRowSelected: false,
            actuatorFieldsEmpty: true,
            canProceed: false
        };
        
        function updateSelectedUnits(mode, deselectedUnit)
        {
            if(mode === 'add')
            {
                for(var key in widgetWizardSelectedRows)
                {
                    if(!widgetWizardSelectedUnits.includes(widgetWizardSelectedRows[key].unit))
                    {
                        widgetWizardSelectedUnits.push(widgetWizardSelectedRows[key].unit);
                        console.log("Unit added: " + widgetWizardSelectedRows[key].unit);
                    }
                    else
                    {
                        console.log("Unit already present: " + widgetWizardSelectedRows[key].unit);
                    }
                }
            }
            else
            {
                var countSelected = 0;
                
                for(var key in widgetWizardSelectedRows)
                {
                    if(widgetWizardSelectedRows[key].unit === deselectedUnit)
                    {
                        countSelected++;
                    }
                }
                
                console.log("Removal di tipo: " + deselectedUnit + " - Count: " + countSelected);
                
                if(countSelected === 0)
                {
                    var removeIndex = widgetWizardSelectedUnits.indexOf(deselectedUnit);
                    if(removeIndex !== -1)
                    {
                        widgetWizardSelectedUnits.splice(removeIndex, 1);
                        console.log("Unità aggiornate: " + widgetWizardSelectedUnits);
                    }
                }
            }
        }
        
        function countSelectedRows()
        {
            currentSelectedRowsCounter = Object.keys(widgetWizardSelectedRows).length; 
            $('#widgetWizardTableSelectedRowsCounter2').attr('data-selectedRows', currentSelectedRowsCounter);
            $('#widgetWizardTableSelectedRowsCounter2').html('Selected rows: ' + currentSelectedRowsCounter);
        }
        
        function checkTab1Conditions()
        {
            if((!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))&&(choosenDashboardTemplateName === 'fullyCustom'))
            {/*
                //Fully custom
                //Primo stadio: se non selezioni tipo di widget, bloccato
                if(validityConditions.widgetTypeSelected)
                {
                    if($('.addWidgetWizardIconClickClass2[data-selected="true"]').attr("data-widgetcategory") === "dataViewer")
                    {
                        //Data viewer: bastano widget type selezionato e righe selezionate
                        if(validityConditions.atLeastOneRowSelected)
                        {
                            $('#addWidgetWizardNextBtn2').removeClass('disabled');
                            $('#cTab2 a').attr("data-toggle", "tab");
                            $('#wizardTab1MsgCnt2').css('color', 'white');
                            $('#wizardTab1MsgCnt2').html("Selection is OK");
                        }
                        else
                        {
                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                            $('#cTab2 a').attr("data-toggle", "no");
                            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                            $('#wizardTab1MsgCnt2').html("You must select at least one row");
                        }
                    }
                    else
                    {
                        //Attuatori
                        if($('#actuatorTargetInstance2').val() === 'existent')
                        {
                            //Caso existent: va bene selezione righe e widget
                            if(validityConditions.atLeastOneRowSelected)
                            {
                                $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt2').css('color', 'white');
                                $('#wizardTab1MsgCnt2').html("Selection is OK");
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn2').addClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt2').html("You must select at least one row");
                            }
                        }
                        else
                        {
                            //Caso new
                            if(($('#actuatorTargetWizard2').val() === 'broker')||($('#actuatorTargetWizard2').val() === 'app'))
                            {
                                if($('#actuatorTargetWizard2').val() === 'broker')
                                {
                                    //Caso broker: controlliamo tutti i campi
                                    if(($('#actuatorEntityName2').val().trim() !== '')&&($('#actuatorValueType2').val().trim() !== '')&&($('#actuatorMinBaseValue2').val().trim() !== '')&&($('#actuatorMaxImpulseValue2').val().trim() !== ''))
                                    {
                                        $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                        $('#cTab2 a').attr("data-toggle", "tab");
                                        $('#wizardTab1MsgCnt2').css('color', 'white');
                                        $('#wizardTab1MsgCnt2').html("Selection is OK");
                                    }
                                    else
                                    {
                                        $('#addWidgetWizardNextBtn2').addClass('disabled');
                                        $('#cTab2 a').attr("data-toggle", "no");
                                        $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                        $('#wizardTab1MsgCnt2').html("Some of the new actuator fields are not filled correctly");
                                    }
                                }
                                else
                                {
                                    //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                    $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                    $('#cTab2 a').attr("data-toggle", "tab");
                                    $('#wizardTab1MsgCnt2').css('color', 'white');
                                    $('#wizardTab1MsgCnt2').html("Selection is OK");
                                }
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn2').addClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt2').html("You must select actuator target type");
                            }
                        }
                    }
                }
                else
                {
                    if ($('#modalAddDashboardWizardTemplateMsg2')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg2')[0].outerText != "Dashboard title OK") {
                        $('#addWidgetWizardNextBtn2').addClass('disabled');
                        $('#cTab2 a').attr("data-toggle", "no");
                        $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                        $('#wizardTab1MsgCnt2').html("You must select one widget type");
                    }
                }
            */}
            else
            {
                if(!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))
                {/*
                    //TUTTI I CASI DI DASHBOARD WIZARD ESCLUSA FULLY CUSTOM
                    //Dashboard template con tipo di widget preselezionato, controlliamo solo se c'è almeno una riga selezionata
                    if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-widgettype') !== 'any')
                    {
                        if(validityConditions.atLeastOneRowSelected)
                        {
                            $('#addWidgetWizardNextBtn2').removeClass('disabled');
                            $('#cTab2 a').attr("data-toggle", "tab");
                            $('#wizardTab1MsgCnt2').css('color', 'white');
                            $('#wizardTab1MsgCnt2').html("Selection is OK");
                        }
                        else
                        {
                            if ($('#modalAddDashboardWizardTemplateMsg2')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg2')[0].outerText != "Dashboard title OK") {
                                $('#addWidgetWizardNextBtn2').addClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt2').html("You must select at least one row");
                            }
                        }
                    }
                    else
                    {
                        //Dashboard template con tipo di widget LIBERO, albero dei controlli più articolato
                        
                        //Primo stadio: se non selezioni tipo di widget, bloccato
                        if(validityConditions.widgetTypeSelected)
                        {
                            //Events vs map: va bene selezione righe e widget
                            //if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'eventsVsMap')
                            if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-hasactuators') === 'false')
                            {
                                $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt2').css('color', 'white');
                                $('#wizardTab1MsgCnt2').html("Selection is OK");
                            }
                            else
                            {
                                //Casi iot
                                //if(($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'iotDevicesBroker')||($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-templatename') === 'iotApps'))
                                //{
                                    if($('#actuatorTargetInstance2').val() === 'existent')
                                    {
                                        //Caso existent: va bene selezione righe e widget
                                        if(validityConditions.atLeastOneRowSelected)
                                        {
                                            $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                            $('#cTab2 a').attr("data-toggle", "tab");
                                            $('#wizardTab1MsgCnt2').css('color', 'white');
                                            $('#wizardTab1MsgCnt2').html("Selection is OK");
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                                            $('#cTab2 a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt2').html("You must select at least one row");
                                        }
                                    }
                                    else
                                    {
                                        //Caso new
                                        if(($('#actuatorTargetWizard2').val() === 'broker')||($('#actuatorTargetWizard2').val() === 'app'))
                                        {
                                            if($('#actuatorTargetWizard2').val() === 'broker')
                                            {
                                                //Caso broker: controlliamo tutti i campi
                                                if(($('#actuatorEntityName2').val().trim() !== '')&&($('#actuatorValueType2').val().trim() !== '')&&($('#actuatorMinBaseValue2').val().trim() !== '')&&($('#actuatorMaxImpulseValue2').val().trim() !== ''))
                                                {
                                                    $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                                    $('#cTab2 a').attr("data-toggle", "tab");
                                                    $('#wizardTab1MsgCnt2').css('color', 'white');
                                                    $('#wizardTab1MsgCnt2').html("Selection is OK");
                                                }
                                                else
                                                {
                                                    $('#addWidgetWizardNextBtn2').addClass('disabled');
                                                    $('#cTab2 a').attr("data-toggle", "no");
                                                    $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                                    $('#wizardTab1MsgCnt2').html("Some of the new actuator fields are not filled correctly");
                                                }
                                            }
                                            else
                                            {
                                                //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                                $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                                $('#cTab2 a').attr("data-toggle", "tab");
                                                $('#wizardTab1MsgCnt2').css('color', 'white');
                                                $('#wizardTab1MsgCnt2').html("Selection is OK");
                                            }
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                                            $('#cTab2 a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt2').html("You must select actuator target type");
                                        }
                                    }
                                //}
                            }
                        }
                        else
                        {
                            if ($('#modalAddDashboardWizardTemplateMsg2')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg2')[0].outerText != "Dashboard title OK") {
                                $('#addWidgetWizardNextBtn2').addClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt2').html("You must select one widget type");
                            }
                        }
                    }
                */}
                else
                {
                    //Widget wizard
                    //Primo stadio: se non selezioni tipo di widget, bloccato
                    if(validityConditions.widgetTypeSelected)
                    {
                        if($('.addWidgetWizardIconClickClass2[data-selected="true"]').attr("data-widgetcategory") === "dataViewer")
                        {
                            //Data viewer: bastano widget type selezionato e righe selezionate
                            if(validityConditions.atLeastOneRowSelected)
                            {
                                $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "tab");
                                $('#wizardTab1MsgCnt2').css('color', 'white');
                                $('#wizardTab1MsgCnt2').html("Selection is OK");
                            }
                            else
                            {
                                $('#addWidgetWizardNextBtn2').addClass('disabled');
                                $('#cTab2 a').attr("data-toggle", "no");
                                $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                $('#wizardTab1MsgCnt2').html("You must select at least one row");
                            }
                        }
                        else
                        {
                            //Attuatori
                            if($('#actuatorTargetInstance2').val() === 'existent')
                            {
                                //Caso existent: va bene selezione righe e widget
                                if(validityConditions.atLeastOneRowSelected)
                                {
                                    $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                    $('#cTab2 a').attr("data-toggle", "tab");
                                    $('#wizardTab1MsgCnt2').css('color', 'white');
                                    $('#wizardTab1MsgCnt2').html("Selection is OK");
                                }
                                else
                                {
                                    $('#addWidgetWizardNextBtn2').addClass('disabled');
                                    $('#cTab2 a').attr("data-toggle", "no");
                                    $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                    $('#wizardTab1MsgCnt2').html("You must select at least one row");
                                }
                            }
                            else
                            {
                                //Caso new
                                if(($('#actuatorTargetWizard2').val() === 'broker')||($('#actuatorTargetWizard2').val() === 'app'))
                                {
                                    if($('#actuatorTargetWizard2').val() === 'broker')
                                    {
                                        //Caso broker: controlliamo tutti i campi
                                        if(($('#actuatorEntityName2').val().trim() !== '')&&($('#actuatorValueType2').val().trim() !== '')&&($('#actuatorMinBaseValue2').val().trim() !== '')&&($('#actuatorMaxImpulseValue2').val().trim() !== ''))
                                        {
                                            $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                            $('#cTab2 a').attr("data-toggle", "tab");
                                            $('#wizardTab1MsgCnt2').css('color', 'white');
                                            $('#wizardTab1MsgCnt2').html("Selection is OK");
                                        }
                                        else
                                        {
                                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                                            $('#cTab2 a').attr("data-toggle", "no");
                                            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                            $('#wizardTab1MsgCnt2').html("Some of the new actuator fields are not filled correctly");
                                        }
                                    }
                                    else
                                    {
                                        //Caso NodeRed: via libera, viene spiegato nel summary che non lo puoi fare
                                        $('#addWidgetWizardNextBtn2').removeClass('disabled');
                                        $('#cTab2 a').attr("data-toggle", "tab");
                                        $('#wizardTab1MsgCnt2').css('color', 'white');
                                        $('#wizardTab1MsgCnt2').html("Selection is OK");
                                    }
                                }
                                else
                                {
                                    $('#addWidgetWizardNextBtn2').addClass('disabled');
                                    $('#cTab2 a').attr("data-toggle", "no");
                                    $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                                    $('#wizardTab1MsgCnt2').html("You must select actuator target type");
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($('#modalAddDashboardWizardTemplateMsg2')[0].outerText != "Template chosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg2')[0].outerText != "Dashboard title OK") {
                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                            $('#chosecTab2 a').attr("data-toggle", "no");
                            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
                            $('#wizardTab1MsgCnt2').html("You must select one widget type");
                        }
                    }
                }
            }
        }
        
        if(location.href.includes("dashboard_configdash.php")||location.href.includes("prova2.php")||location.href.includes("dashboards.php")||location.href.includes("iframeApp.php"))
        {
            firstTabIndex = 1;
            selectedTabIndex = 1;
            
            $('#aTab2').hide(); 
            $('#mainFeat2').hide();
            
            $('#aTab2').removeClass('active');
            $('#bTab2').addClass('active');
            $('#mainFeat2').removeClass('active');
            $('#dataAndWidgets2').addClass('active');
            $('#addWidgetWizardNextBtn2').addClass('disabled');
            $('#cTab2 a').attr("data-toggle", "no");
            $('#wizardTab1MsgCnt2').css('color', 'rgb(243, 207, 88)');
            $('#wizardTab1MsgCnt2').html("You must select one widget type");
        }
        
        /*$('#wizardTabsContainer2.nav-tabs a[href="#mainFeat2"]').on('shown.bs.tab', function(event)
        {
            selectedTabIndex = 0;
            $('#addWidgetWizardPrevBtn2').addClass('disabled');
            $('#addWidgetWizardNextBtn2').removeClass('disabled');
            
            //Gestione pulsanti prev e next
            $('#addWidgetWizardPrevBtn2').off('click');
            $('#addWidgetWizardPrevBtn2').click(function()
            {
                if(selectedTabIndex > firstTabIndex)
                {
                    $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                }
            });

            $('#addWidgetWizardNextBtn2').off('click');
            $('#addWidgetWizardNextBtn2').click(function()
            {
                if(selectedTabIndex < parseInt(tabsQt - 1))
                {
                    $('.nav-tabs > .active').next('li').find('a').trigger('click');
                }
            });
        })*/
        
        $('#wizardTabsContainer2.nav-tabs a[href="#summary2"]').on('shown.bs.tab', function(event)
        {
            $('#wrongConditionsDiv2').empty();
            $('#summaryDiv2').empty();
            $('#createBtnDiv2').hide();
            $('#createBtnAlert2').show();
            var canBuildSummary = true;
            var summaryTable, summaryTableRow, instancesInfoTxt = null;
            
            selectedTabIndex = 2;
            
            $('#addWidgetWizardNextBtn2').addClass('disabled');
            $('#addWidgetWizardPrevBtn2').removeClass('disabled');
            $('#cTab2 a').attr("data-toggle", "tab");
            $('#bTab2 a').attr("data-toggle", "tab");
            
            if(!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))
            {
                $('#aTab2 a').attr("data-toggle", "tab");
            }
            
            //Gestione pulsanti prev e next
            $('#addWidgetWizardPrevBtn2').off('click');
            $('#addWidgetWizardPrevBtn2').click(function()
            {
                if(selectedTabIndex > firstTabIndex)
                {
                    $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                }
            });

            $('#addWidgetWizardNextBtn2').off('click');
            $('#addWidgetWizardNextBtn2').click(function()
            {
                if(selectedTabIndex < parseInt(tabsQt - 1))
                {
                    $('.nav-tabs > .active').next('li').find('a').trigger('click');
                }
            });
            
            if((!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))&&(choosenDashboardTemplateName === 'fullyCustom'))
            {/*
                if(!validityConditions.dashTemplateSelected)
                {
                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">No dashboard template selected</span></div></div>');
                    validityConditions.canProceed = false;
                    canBuildSummary = false;
                }
                else
                {
                    //Se non si seleziona né widget type né righe è la fully custom vuota
                    if((!validityConditions.widgetTypeSelected)&&(!validityConditions.atLeastOneRowSelected))
                    {
                        switch($('#inputTitleDashboardStatus2').val())
                        {
                            case 'empty':
                                $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title can\'t be empty</span></div></div>');
                                validityConditions.canProceed = false;
                                break;

                            case 'alreadyUsed':
                                $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title already in use</span></div></div>');
                                validityConditions.canProceed = false;
                                break;    
                                
                            case 'tooLong':
                                $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title longer than 300 chars</span></div></div>');
                                validityConditions.canProceed = false;    

                            default:
                                break;
                        }

                        if(validityConditions.canProceed === false)
                        {
                            canBuildSummary = false;
                        }
                        else
                        {
                            validityConditions.canProceed = true;
                            canBuildSummary = true;
                        }
                    }
                    else
                    {
                        //Se si seleziona almeno uno tra widget type e righe è la fully custom non vuota
                        if(!validityConditions.widgetTypeSelected)
                        {
                            $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">No widget type selected</span></div></div>');
                            validityConditions.canProceed = false;
                            canBuildSummary = false;
                        }
                        else
                        {
                            if(!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))
                            {
                                switch($('#inputTitleDashboardStatus2').val())
                                {
                                    case 'empty':
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title can\'t be empty</span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;

                                    case 'alreadyUsed':
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title already in use</span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;    
                                    
                                    case 'tooLong':
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title longer than 300 chars</span></div></div>');
                                        validityConditions.canProceed = false;
                                        break;    
                                    
                                    default:
                                        break;
                                }

                                if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance2').val() === 'existent'))||($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                                {
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You have to select at least one row from data sources table</span></div></div>');
                                    validityConditions.canProceed = false;
                                    canBuildSummary = false;
                                }
                                else
                                {
                                    if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'existent'))
                                    {
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You can\'t select rows from both IOT Apps and broker</span></div></div>');
                                        validityConditions.canProceed = false;
                                    }
                                    else
                                    {
                                        if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'new'))
                                        {
                                            $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Some fields for new device creation on broker are empty or wrongly filled</span></div></div>');
                                            validityConditions.canProceed = false;   
                                        }
                                        else
                                        {
                                            if($('#inputTitleDashboardStatus2').val() === 'ok')
                                            {
                                                validityConditions.canProceed = true; 
                                                canBuildSummary = true;
                                            }
                                            else
                                            {
                                                validityConditions.canProceed = false;   
                                                canBuildSummary = false;
                                            }
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance2').val() === 'existent'))||($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                                {
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You have to select at least one row</span></div></div>');
                                    validityConditions.canProceed = false;
                                    canBuildSummary = false;
                                }
                                else
                                {
                                    if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'existent'))
                                    {
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You can\'t select rows from both IOT Apps and broker</span></div></div>');
                                        validityConditions.canProceed = false;
                                    }
                                    else
                                    {
                                        if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'new'))
                                        {
                                            $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Some fields for new device creation on broker are empty or wrongly filled</span></div></div>');
                                            validityConditions.canProceed = false;   
                                        }
                                        else
                                        {
                                            validityConditions.canProceed = true;   
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            */}
            else
            {
                if((!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))&&(!validityConditions.dashTemplateSelected))
                {/*
                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">No dashboard template selected</span></div></div>');
                    validityConditions.canProceed = false;
                    canBuildSummary = false;
                */}
                else
                {
                    if(!validityConditions.widgetTypeSelected)
                    {
                        $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">No widget type selected</span></div></div>');
                        validityConditions.canProceed = false;
                        canBuildSummary = false;
                    }
                    else
                    {
                        if(!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))
                        {/*
                            switch($('#inputTitleDashboardStatus2').val())
                            {
                                case 'empty':
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title can\'t be empty</span></div></div>');
                                    validityConditions.canProceed = false;
                                    break;

                                case 'alreadyUsed':
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title already in use</span></div></div>');
                                    validityConditions.canProceed = false;
                                    break;   
                                    
                                case 'tooLong':
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title longer than 300 chars</span></div></div>');
                                    validityConditions.canProceed = false;    

                                default:
                                    break;
                            }

                            if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance2').val() === 'existent'))||($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                            {
                                $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You have to select at least one row from data sources table</span></div></div>');
                                validityConditions.canProceed = false;
                                canBuildSummary = false;
                            }
                            else
                            {
                                if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'existent'))
                                {
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You can\'t select rows from both IOT Apps and broker</span></div></div>');
                                    validityConditions.canProceed = false;
                                }
                                else
                                {
                                    if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'new'))
                                    {
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Some fields for new device creation on broker are empty or wrongly filled</span></div></div>');
                                        validityConditions.canProceed = false;   
                                    }
                                    else
                                    {
                                        if($('#inputTitleDashboardStatus2').val() === 'ok')
                                        {
                                            validityConditions.canProceed = true; 
                                            canBuildSummary = true;
                                        }
                                        else
                                        {
                                            validityConditions.canProceed = false;   
                                            canBuildSummary = false;
                                        }
                                    }
                                }
                            }
                        */}
                        else
                        {
                            if((!validityConditions.atLeastOneRowSelected)&&((($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetInstance2').val() === 'existent'))||($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'dataViewer')))
                            {
                                $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You have to select at least one row</span></div></div>');
                                validityConditions.canProceed = false;
                                canBuildSummary = false;
                            }
                            else
                            {
                                if((!validityConditions.brokerAndNrRowsTogether)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'existent'))
                                {
                                    $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">You can\'t select rows from both IOT Apps and broker</span></div></div>');
                                    validityConditions.canProceed = false;
                                }
                                else
                                {
                                    if((!validityConditions.actuatorFieldsEmpty)&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')&&($('#actuatorTargetWizard2').val() === 'broker')&&($('#actuatorTargetInstance2').val() === 'new'))
                                    {
                                        $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Some fields for new device creation on broker are empty or wrongly filled</span></div></div>');
                                        validityConditions.canProceed = false;   
                                    }
                                    else
                                    {
                                        validityConditions.canProceed = true;   
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if(validityConditions.canProceed)
            {
                $('#createBtnAlert2').hide();
                $('#createBtnDiv2').show();
            }
            else
            {
                $('#createBtnDiv2').hide();
                $('#createBtnAlert2').show();
            }
            
            var monoMulti = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-mono_multi');
            var widgetCategory = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetcategory');
            var mainWidget = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-mainwidget');
            var targetWidget = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-targetwidget');
            var widgetIcon = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-icon');
            var widgetDesc = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-content');
            var existentOrNew = $('#actuatorTargetInstance2').val();
            var brokerOrNr = $('#actuatorTargetWizard2').val();
            
            if(canBuildSummary)
            {
                if(!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))
                {/*
                    var localExtCnt = $('<div class="col-xs-4"></div>');
                    var dashInfoLbl = $('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><span class="summaryLbl">Dashboard template and title</span></div>');
                    var dashTitleCnt = $('<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + $('#inputTitleDashboard2').val() + '</div>');
                    var dashIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                    var dashIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                    var dashTemplateIconUrl = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"] div.modalAddDashboardWizardChoicePic').css('background-image');
                    dashIconCnt.css("background-image", dashTemplateIconUrl);
                    
                    localExtCnt.append(dashInfoLbl);
                    dashIconExtCnt.append(dashIconCnt)
                    localExtCnt.append(dashIconExtCnt);
                    localExtCnt.append(dashTitleCnt);
                    $('#summaryDiv2').append(localExtCnt);
                */}
                
                if((!location.href.includes("dashboard_configdash")&&!location.href.includes("prova2")&&!location.href.includes("dashboards")&&!location.href.includes("iframeApp"))&&(choosenDashboardTemplateName === 'fullyCustom'))
                {/*
                    var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Details</div>';
                    var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">A fully custom dashboard is created empty, so no widget details are available</div>';
                    $('#summaryDiv2').append(widgetInfoLbl);
                    $('#summaryDiv2').append(widgetInfoCnt); 
                */}
                else
                {
                    if(monoMulti === 'Mono')
                    {
                        //Casi mono - 1 widget per riga
                        if((widgetCategory === 'dataViewer')||((widgetCategory === 'actuator')&&(existentOrNew == 'existent')))
                        {
                            //Dataviewer OPPURE actuator on existent
                            var localExtCnt = $('<div class="col-xs-4"></div>');
                            var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Widget type details</div>';
                            var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>';
                            
                            localExtCnt.append(widgetInfoLbl);
                            localExtCnt.append(widgetInfoCnt);
                            $('#summaryDiv2').append(localExtCnt);
                            
                            var localExtCnt = $('<div class="col-xs-4"></div>');
                            var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Instances details</div>';
                            
                            summaryTable = $('<table id="summaryTable2"><thead><th>Widget</th><th>High-Level Type</th><th>Nature</th><th>Subnature</th><th>Value type</th><th>Value name</th><th>Data type</th></thead><tbody></tbody></table>');

                            var count = 0;
                            for(var key in widgetWizardSelectedRows)
                            {
                                if(widgetWizardSelectedRows[key].widgetCompatible)
                                {
                                    summaryTableRow = $('<tr class="summaryTableRow"><td><div class="iconsMonoSingleIcon"></div></td><td>' + widgetWizardSelectedRows[key].high_level_type + '</td><td>' + widgetWizardSelectedRows[key].nature + '</td><td>' + widgetWizardSelectedRows[key].sub_nature + '</td><td>' + widgetWizardSelectedRows[key].low_level_type + '</td><td>' + widgetWizardSelectedRows[key].unique_name_id + '</td><td>' + widgetWizardSelectedRows[key].unit + '</td></tr>');
                                    summaryTableRow.find('div.iconsMonoSingleIcon').css("background-image", "url(\"../img/widgetIcons/mono/" + widgetIcon + "\")");
                                    summaryTable.find('tbody').append(summaryTableRow);
                                    count++;
                                }
                                else
                                {
                                    //TBD - Aggiungere a righe non istanziate
                                }
                            }
                            
                            if(targetWidget === '')
                            {
                                instancesInfoTxt = count + " single instances of widget will be created";
                            }
                            else
                            {
                                if(targetWidget !== 'widgetTimeTrend')
                                {
                                    instancesInfoTxt = count + " instance(s) of main widget + 1 single instance of a driven target widget will be created";
                                }
                                else
                                {
                                    instancesInfoTxt = count + " couple(s) of widgets will be created: the first one of each couple will show last data value, the second one a value time trend";
                                }
                            }

                            var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + instancesInfoTxt + '</div>';

                            localExtCnt.append(instancesInfoLbl);
                            localExtCnt.append(instancesInfoCnt);
                            $('#summaryDiv2').append(localExtCnt);
                            
                            var localExtCnt = $('<div class="col-xs-12"></div>');
                            var tableLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Main widget(s) and relative data</div>';

                            localExtCnt.append(tableLbl);
                            localExtCnt.append(summaryTable);
                            $('#summaryDiv2').append(localExtCnt);
                        }
                        else
                        {
                            if((widgetCategory === 'actuator')&&(existentOrNew == 'new')&&(brokerOrNr === 'broker'))
                            {
                                //Actuator on new entity: widget + entity summary
                                var localExtCnt = $('<div class="col-xs-4"></div>');
                                var widgetInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl">Widget type details</div>');
                                var widgetInfoCnt = $('<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>');
                                var widgetIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                                var widgetIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                                widgetIconCnt.css("background-image", "url(\"../img/widgetIcons/mono/" + widgetIcon + "\")");
                                
                                localExtCnt.append(widgetInfoLbl);
                                localExtCnt.append(widgetInfoCnt);
                                widgetIconExtCnt.append(widgetIconCnt)
                                localExtCnt.append(widgetIconExtCnt);
                                $('#summaryDiv2').append(localExtCnt);
                                
                                var localExtCnt = $('<div class="col-xs-4"></div>');
                                var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Instances details</div>';
                                var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">One new device entity will be created on context broker and linked to new actuator on dashboard</div>';
                                localExtCnt.append(instancesInfoLbl);
                                localExtCnt.append(instancesInfoCnt);
                                $('#summaryDiv2').append(localExtCnt);
                                
                                var entityInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl">Device details</div>');
                                var deviceTable = $('<table id="summaryTable2"><thead><th>Property</th><th>Value</th></thead><tbody></tbody></table>');

                                var deviceTableRow = $('<tr class="summaryTableRow"><td>Device name</td><td>' + $('#actuatorEntityName2').val() + '</td></tr>');
                                deviceTable.find('tbody').append(deviceTableRow);

                                deviceTableRow = $('<tr class="summaryTableRow"><td>Value type</td><td>' + $('#actuatorValueType2').val() + '</td></tr>');
                                deviceTable.find('tbody').append(deviceTableRow);

                                switch(mainWidget)
                                {
                                    case "widgetImpulseButton":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Base value</td><td>' + $('#actuatorMinBaseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Impulse value</td><td>' + $('#actuatorMaxImpulseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;

                                    case "widgetOnOffButton":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Off value</td><td>' + $('#actuatorMinBaseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>On value</td><td>' + $('#actuatorMaxImpulseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;    

                                    case "widgetKnob":
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Min value</td><td>' + $('#actuatorMinBaseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        deviceTableRow = $('<tr class="summaryTableRow"><td>Max value</td><td>' + $('#actuatorMaxImpulseValue2').val() + '</td></tr>');
                                        deviceTable.find('tbody').append(deviceTableRow);
                                        break;    
                                }

                                
                                $('#summaryDiv2').append(widgetInfoCnt);
                                $('#summaryDiv2').append(entityInfoLbl);
                                $('#summaryDiv2').append(deviceTable);
                            }
                            else
                            {
                                if((widgetCategory === 'actuator')&&(existentOrNew == 'new')&&(brokerOrNr === 'app'))
                                {
                                    //Actuator on new NR: how to NodeRED
                                    var nrInfoLbl = $('<div class="col-xs-12 centerWithFlex summaryLbl">Instantiation instructions</div>');
                                    var nrInfoCnt = $('<div class="col-xs-12 widgetTypeDetails">At the moment it\'s not possible to instantiate a new actuator and its corrispondent block on a IOT personal application. In order to complete this task, please follow this flow: 1) Open NodeRED flow designer of a personal app of your choice<br>; 2) Add a new actuator block (geolocator, dimer, impulsive button, switch, keyboard); 3) Choose the dashboard where you want it to be (or create a new one) via block edit menu; 4) Deploy your application; 5) Open (or refresh) your dashboard of choice: actuator widget will be automatically be instantiated</div>');
                                    $('#summaryDiv2').append(nrInfoLbl);
                                    $('#summaryDiv2').append(nrInfoCnt);
                                    $('#createBtnDiv2').hide();
                                }
                            }
                        }
                    }
                    else
                    {
                        //Casi multi
                        var localExtCnt = $('<div class="col-xs-4"></div>');
                        var widgetInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Widget type details</div>';
                        var widgetIconExtCnt = $('<div class="col-xs-12 centerWithFlex"></div>');
                        var widgetIconCnt = $('<div class="singleWidgetIconCnt"></div>');
                        widgetIconCnt.css("background-image", "url(\"../img/widgetIcons/multi/" + widgetIcon + "\")");
                        var widgetInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + widgetDesc + '</div>';
                        
                        localExtCnt.append(widgetInfoLbl);
                        widgetIconExtCnt.append(widgetIconCnt);
                        localExtCnt.append(widgetIconExtCnt);
                        localExtCnt.append(widgetInfoCnt);
                        $('#summaryDiv2').append(localExtCnt);
                        
                        var tableLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Main widget and relative data</div>';

                        summaryTable = $('<table id="summaryTable2"><thead><th>High-Level Type</th><th>Nature</th><th>Subnature</th><th>Value type</th><th>Value name</th><th>Data type</th></thead><tbody></tbody></table>');

                        var count = 0;
                        for(var key in widgetWizardSelectedRows)
                        {
                            if(widgetWizardSelectedRows[key].widgetCompatible)
                            {
                                summaryTableRow = $('<tr class="summaryTableRow"><td>' + widgetWizardSelectedRows[key].high_level_type + '</td><td>' + widgetWizardSelectedRows[key].nature + '</td><td>' + widgetWizardSelectedRows[key].sub_nature + '</td><td>' + widgetWizardSelectedRows[key].low_level_type + '</td><td>' + widgetWizardSelectedRows[key].unique_name_id + '</td><td>' + widgetWizardSelectedRows[key].unit + '</td></tr>');
                                summaryTable.find('tbody').append(summaryTableRow);
                                count++;
                            }
                            else
                            {
                                //TBD - Aggiungere a righe non istanziate
                            }
                        }
                        
                        var localExtCnt = $('<div class="col-xs-4"></div>');
                        var instancesInfoLbl = '<div class="col-xs-12 centerWithFlex summaryLbl">Instances details</div>';

                        if(targetWidget === '')
                        {
                            instancesInfoTxt = "One single instance of widget will be created: it will handle all the " + count + " selected data sources";
                        }
                        else
                        {
                            instancesInfoTxt = "One single instance of main widget and one instance of each target widget will be created: the main widget will handle all the " + count + " selected data sources showing their data on the target widget(s)";
                        }

                        var instancesInfoCnt = '<div class="col-xs-12 centerWithFlex widgetTypeDetails">' + instancesInfoTxt + '</div>';
                        
                        localExtCnt.append(instancesInfoLbl);
                        localExtCnt.append(instancesInfoCnt);
                        $('#summaryDiv2').append(localExtCnt);
                        
                        var localExtCnt = $('<div class="col-xs-12"></div>');
                        localExtCnt.append(tableLbl);
                        localExtCnt.append(summaryTable);
                        $('#summaryDiv2').append(localExtCnt);
                    }
                }
                
                $('#wrongConditionsDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex" style="margin-top: 25px !important"><i class="fa fa-thumbs-o-up validityConditionIcon" style="font-size: 100px !important; color: white !important"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl" style="color: white !important;">Can proceed</span></div></div>');
            }
            else
            {
                $('#summaryDiv2').append('<div class="col-xs-12"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Summary is not available until you fix missing or wrong inputs</span></div></div>');
            }
        });
        
        $('#actuatorTargetInstance2').val("existent");
        $('#actuatorTargetWizard2').val(-1);
        
        $('#actuatorTargetInstance2').change(function()
        {
            if($(this).val() === 'new')
            {
                $('.hideIfActuatorNew').hide();
                $('#actuatorTargetCell2 .wizardActLbl').show();
                $('#actuatorTargetCell2 .wizardActInputCnt').show();
                
                if((!location.href.includes("dashboard_configdash.php")&&!location.href.includes("prova2.php")&&!location.href.includes("dashboards.php")&&!location.href.includes("iframeApp.php"))&&($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr("data-templatename") === 'iotDevicesBroker'))
                {/*
                    $('#actuatorTargetWizard2').val('broker');
                    $('#actuatorTargetWizard2').trigger('change');
                */}
                
                checkActuatorFieldsEmpty();
            }
            else
            {
                $('#actuatorTargetCell2 .wizardActLbl').hide();
                $('#actuatorTargetCell2 .wizardActInputCnt').hide();
                $('#actuatorEntityNameCell2 .wizardActLbl').hide();
                $('#actuatorEntityNameCell2 .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell2 .wizardActLbl').hide();
                $('#actuatorValueTypeCell2 .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell2 .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').hide();
                $('#actuatorTargetWizard2').val(-1);
                $('#actuatorEntityName2').val('');
                $('#actuatorValueType2').val('');
                $('#actuatorMinBaseValue2').val('');
                $('#actuatorMaxImpulseValue2').val('');
                
                $('.hideIfActuatorNew').show();
            }
            
            checkTab1Conditions();
        });
        
        $('#actuatorTargetWizard2').change(function()
        {
            if($(this).val() === 'broker')
            {
                $('#actuatorEntityNameCell2 .wizardActLbl').show();
                $('#actuatorEntityNameCell2 .wizardActInputCnt').show();
                $('#actuatorValueTypeCell2 .wizardActLbl').show();
                $('#actuatorValueTypeCell2 .wizardActInputCnt').show();
                $('#actuatorMinBaseValueCell2 .wizardActLbl').show();
                $('#actuatorMinBaseValueCell2 .wizardActInputCnt').show();
                $('#actuatorMaxBaseValueCell2 .wizardActLbl').show();
                $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').show();
                checkActuatorFieldsEmpty();
            }
            else
            {
                $('#actuatorEntityNameCell2 .wizardActLbl').hide();
                $('#actuatorEntityNameCell2 .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell2 .wizardActLbl').hide();
                $('#actuatorValueTypeCell2 .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell2 .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').hide();
            }
            
            checkTab1Conditions();
        });
        
        function checkBrokerAndNrRowsTogether()
        {
            var nrCount = 0;
            var brokerCount = 0;
            
            for(var key in widgetWizardSelectedRows)
            {
                if(widgetWizardSelectedRows[key].nature === 'From Dashboard to IOT Device')
                {
                    nrCount++;
                }
                
                if(widgetWizardSelectedRows[key].nature === 'From Dashboard to IOT App')
                {
                    brokerCount++;
                }        
            }
            
            if((nrCount > 0)&&(brokerCount > 0))
            {
                validityConditions.brokerAndNrRowsTogether = false;
            }
            else
            {
                validityConditions.brokerAndNrRowsTogether = true;
            }
        }
        
        function checkAtLeastOneRowSelected()
        {
            var count = 0;
            
            for(var key in widgetWizardSelectedRows)
            {
                count++;        
            }
            
            if(count > 0)
            {
                validityConditions.atLeastOneRowSelected = true;
            }
            else
            {
                validityConditions.atLeastOneRowSelected = false;
            }
        }
        
        $('#actuatorEntityName2').on('input', checkActuatorFieldsEmpty);
        $('#actuatorValueType2').on('input', checkActuatorFieldsEmpty);
        $('#actuatorMinBaseValue2').on('input', checkActuatorFieldsEmpty);
        $('#actuatorMaxImpulseValue2').on('input', checkActuatorFieldsEmpty);
        
        function checkActuatorFieldsEmpty()
        {
            var selectedWidgetType = $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-mainwidget');
            
            if(($('#actuatorTargetInstance2').val() === 'new')&&($('#actuatorTargetWizard2').val() === 'broker'))
            {
                switch(selectedWidgetType)
                {
                    case "widgetKnob":
                        if(($('#actuatorEntityName2').val() === '')||($('#actuatorValueType2').val() === '')||($('#actuatorMinBaseValue2').val() === '')||($('#actuatorMaxImpulseValue2').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;

                    case "widgetOnOffButton":
                        if(($('#actuatorEntityName2').val() === '')||($('#actuatorValueType2').val() === '')||($('#actuatorMinBaseValue2').val() === '')||($('#actuatorMaxImpulseValue2').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break; 

                    case "widgetImpulseButton":
                        if(($('#actuatorEntityName2').val() === '')||($('#actuatorValueType2').val() === '')||($('#actuatorMinBaseValue2').val() === '')||($('#actuatorMaxImpulseValue2').val() === ''))
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;

                    case "widgetNumericKeyboard":
                        if($('#actuatorEntityName2').val() === '')
                        {
                            validityConditions.actuatorFieldsEmpty = false;
                        }
                        else
                        {
                            validityConditions.actuatorFieldsEmpty = true;
                        }
                        break;  

                    default:
                        validityConditions.actuatorFieldsEmpty = true;
                        break;
                }
            }
            else
            {
                //Caso seconda select ancora a -1, non funziona!
                if(($('#actuatorTargetInstance2').val() === 'new')&&($('#actuatorTargetWizard2').val() === -1)/*&&($('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-widgetCategory') === 'actuator')*/)
                {
                    validityConditions.actuatorFieldsEmpty = false;
                }
                else
                {
                    validityConditions.actuatorFieldsEmpty = true;
                }
            }
            
            checkTab1Conditions();
        }
        
        function updateWidgetCompatibleRows()
        {
            var selectedWidget, snap4citytype, snap4citytypeArray, selectedRowUnits, count, globalCount, originalBckColor = null;
            //Repere tipi di dato gestiti dal widget
            if($('.addWidgetWizardIconClickClass2[data-selected="true"]').length > 0)
            {
                selectedWidget = $('.addWidgetWizardIconClickClass2[data-selected="true"]');
                snap4citytype = selectedWidget.attr('data-snap4citytype');
                snap4citytypeArray = snap4citytype.split(',');
                globalCount = 0;
                console.log("Selected Widget: " + $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-mainwidget'));
                console.log("Snap4CIty TYPE: " + snap4citytype);
                
                if(Object.keys(widgetWizardSelectedRows).length > 0)
                {
                    for(var key in widgetWizardSelectedRows)
                    {
                        selectedRowUnits = widgetWizardSelectedRows[key].unit.split(',');
                        console.log("Selected ROW UNITS: " + selectedRowUnits);
                        count = 0;
                        
                        originalBckColor = $('#widgetWizardSelectedRowsTable2 tbody tr[data-rowid=' + key.replace('row', '') + ']').css("background-color");
                        for(var j = 0; j < selectedRowUnits.length; j++)
                        {
                            selectedRowUnits[j] = selectedRowUnits[j].trim();

                            if(snap4citytype.includes(selectedRowUnits[j]))
                            {
                                count++;
                            }
                        }
                        
                        if(count > 0)
                        {
                            //Riga compatibile
                            widgetWizardSelectedRows[key].widgetCompatible = true;
                            $('#widgetWizardSelectedRowsTable2 tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", originalBckColor);
                        }
                        else
                        {
                            console.log("Riga Incompatibile !");
                            //Riga incompatibile
                            globalCount++;
                            widgetWizardSelectedRows[key].widgetCompatible = false;
                            $('#widgetWizardSelectedRowsTable2 tr[data-rowid=' + key.replace('row', '') + ']').css("background-color", "#ffb3b3");
                        }
                    }
                    
                    if(globalCount > 0)
                    {
                        $('#wizardNotCompatibleRowsAlert').show();
                    }
                    else
                    {
                        $('#wizardNotCompatibleRowsAlert').hide();
                    }
                }
            }
            else
            {
                //Se widget non selezionato le righe son sempre compatibili
                for(var key in widgetWizardSelectedRows)
                {
                    widgetWizardSelectedRows[key].widgetCompatible = true;
                    if($('#widgetWizardSelectedRowsTable2 tr[data-rowid=' + key.replace('row', '') + ']').hasClass('odd'))
                    {
                        $('#widgetWizardSelectedRowsTable2 tr[data-rowid=' + key.replace('row', '') + ']').css('background-color', '#f9f9f9');
                    }
                    else
                    {
                        $('#widgetWizardSelectedRowsTable2 tr[data-rowid=' + key.replace('row', '') + ']').css('background-color', '#ffffff');
                    }
                }
                $('#wizardNotCompatibleRowsAlert').hide();
            }
        }
        
        function updateIconsFromSelectedRows()
        {
            if(Object.keys(widgetWizardSelectedRows).length > 0)
            {
                $('.addWidgetWizardIconClickClass2').each(function (j) 
                {
                    var count = 0;

                    var snap4citytype = $(this).attr('data-snap4citytype');
                    var snap4citytypeArray = snap4citytype.split(',');
                    var selectedRowUnits = null;

                    for(var k = 0; k < snap4citytypeArray.length; k++)
                    {
                        for(var key in widgetWizardSelectedRows)
                        {
                            selectedRowUnits = widgetWizardSelectedRows[key].unit.split(',');
                            for(var j = 0; j < selectedRowUnits.length; j++)
                            {
                                selectedRowUnits[j] = selectedRowUnits[j].trim();

                                if (selectedRowUnits[j] === snap4citytypeArray[k].trim())
                                {
                                    count++;
                                }
                            }
                        }
                    }

                    if(count > 0)
                    {
                        $(this).show();
                    } 
                    else
                    {
                        $(this).hide();
                    }
                });
            }
            else
            {
                //Nessuna riga selezionata
                var unitSelectSnapshotItem = null, unitSelectSnapshot = [];
                
                $("#unitSelect2 option").each(function(i){
                    unitSelectSnapshotItem = {
                        selected: true,
                        value: $(this).attr('value')
                    };
                    
                    unitSelectSnapshot.push(unitSelectSnapshotItem);
                });
                
                updateIcons(unitSelectSnapshot);
            }
        }
        
        //Specifiche per caso widget wizard
        var unitSelect = null;
        var highLevelTypeSelectStartOptions = 0;
        var natureSelectStartOptions = 0;
        var subNatureSelectStartOptions = 0;
        var lowLevelTypeSelectStartOptions = 0;
        var unitSelectStartOptions = 0;
        var healthinessSelectStartOptions = 0;
        var ownershipSelectStartOptions = 0;

        var globalSqlFilter = [
            {
                "field": "high_level_type",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "nature",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "sub_nature",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "low_level_type",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "unique_name_id",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "instance_uri",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "unit",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "healthiness",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            },
            {
                "field": "ownership",
                "value": "",
                "active": "false",
                "selectedVals": [],
                "allSelected": true
            }
        ];

        function applyHighLevelTypeFilter() 
        {
            /*choosenWidgetIconName = null;
            widgetWizardSelectedRows = {};
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/
            
            var search = [];
            $.each($('#highLevelTypeSelect2 option:selected'), function () {
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#highLevelTypeSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[0].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[0].selectedVals = search;

            search = search.join('|');
            globalSqlFilter[0].value = search;
            if (search == '' && !globalSqlFilter[0].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(0).search(search, false, false).draw();
            globalSqlFilter[0].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++)
            {
                if (n !== 4 && n != 5)
                {
                    populateSelectMenus("high_level_type", search, $('#highLevelTypeSelect2'), "#highLevelTypeFilterColumn", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applyNatureFilter() 
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#natureSelect2 option:selected'), function () {   // CHANGE
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#natureSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[1].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[1].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[1].value = search;
            if (search == '' && !globalSqlFilter[1].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(1).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[1].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("nature", search, $('#natureSelect2'), "#natureFilterColumn", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applySubnatureFilter() 
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#subnatureSelect2 option:selected'), function () {   // CHANGE
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#subnatureSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[2].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[2].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[2].value = search;
            if (search == '' && !globalSqlFilter[2].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            if (search.charAt(0) == '|') {
                search = search.substring(1);
            }
            widgetWizardTable.column(2).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[2].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("sub_nature", search, $('#subnatureSelect2'), "#subnatureFilterColumn", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applyValueTypeFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#lowLevelTypeSelect2 option:selected'), function () {   // CHANGE
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#lowLevelTypeSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[3].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[3].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[3].value = search;
            if (search == '' && !globalSqlFilter[3].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(3).search(search, false, false).draw();     // CHANGE
            globalSqlFilter[3].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("low_level_type", search, $('#lowLevelTypeSelect2'), "#lowLevelTypeFilterColumn", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applyDataTypeFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#unitSelect2 option:selected'), function () {
                search.push($(this).val());
            });
            var nOptions = 0;
            $.each($('#unitSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[6].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[6].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[6].value = search;
            if (search == '' && !globalSqlFilter[6].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(6).search(search, false, false).draw();
            globalSqlFilter[6].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("unit", search, $('#unitSelect2'), "#unitFilterColumn", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applyHealthinessFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/

            var search = [];
            $.each($('#healthinessSelect2 option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#healthinessSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[7].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[7].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[7].value = search;
            if (search == '' && !globalSqlFilter[7].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(7).search(search, false, false).draw();
            globalSqlFilter[7].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("healthiness", search, $('#healthinessSelect2'), "#healthinessColumnFilter2", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        function applyOwnershipFilter()
        {
            /*widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.atLeastOneRowSelected = false;
            checkTab1Conditions();
            countSelectedRows();*/
            
            var search = [];
            $.each($('#ownershipSelect2 option:selected'), function () {
                search.push($(this).val());

            });
            var nOptions = 0;
            $.each($('#ownershipSelect2 option'), function () {
                nOptions++;
            });

            globalSqlFilter[8].allSelected = (search.length == nOptions);
            if (search.length == nOptions)
                search = [];
            globalSqlFilter[8].selectedVals = search;
            search = search.join('|');

            globalSqlFilter[8].value = search;
            if (search == '' && !globalSqlFilter[8].allSelected) {
                search = 'oiunqauhalknsufhvnoqwpnvfv';
            }
            widgetWizardTable.column(8).search(search, false, false).draw();
            globalSqlFilter[8].value = search;

            // Chiamata a funzione per popolare menù multi-select di filtraggio
            for (var n = 0; n < 9; n++) {
                if (n !== 4 && n != 5) {
                    populateSelectMenus("ownership", search, $('#ownershipSelect2'), "#ownershipColumnFilter2", n, false, true);
                }
            }
            
            checkTab1Conditions();
            countSelectedRows();
        }

        //Caricamento icone add widget wizard
        $.ajax({
            url: "../controllers/dashboardWizardController.php",
            type: "GET",
            data: {
                getDashboardWizardIcons: true
            },
            async: true,
            dataType: 'json',
            success: function (data)
            {
                var newIcon = null;
                var spanElement = null;

                for (i = 0; i < data.table.length; i++)
                {
                    if (data.table[i].mono_multi === 'Mono')
                    {
                        //ICONE MONO
                        newIcon = $('<div data-toggle="popover" data-placement="bottom" data-html="true" data-widgetCategory="' + data.table[i].widgetCategory + '" data-available="' + data.table[i].available + '" data-trigger="hover" data-selected="false" data-content="<span>' + data.table[i].description + '</span>" data-id="' + data.table[i].id + '" data-iconName="' + data.table[i].icon + '" data-mainWidget="' + data.table[i].mainWidget + '" data-targetWidget="' + data.table[i].targetWidget + '" data-snap4CityType="' + data.table[i].snap4CityType + '" data-icon="' + data.table[i].icon + '" data-mono_multi="' + data.table[i].mono_multi + '" data-description="' + data.table[i].description + '" class="iconsMonoSingleIcon addWidgetWizardIconClickClass2"></div>');
                        newIcon.css('background-image', 'url("../img/widgetIcons/mono/' + data.table[i].icon + '")');

                        $('.addWidgetWizardIconsCnt2').eq(0).append(newIcon);
                        $('[data-toggle="tooltip"]').tooltip();
                    } else
                    {
                        //ICONE MULTI
                        newIcon = $('<div data-toggle="popover" data-placement="bottom" data-html="true" data-widgetCategory="' + data.table[i].widgetCategory + '" data-available="' + data.table[i].available + '" data-trigger="hover" data-selected="false" data-content="<span>' + data.table[i].description + '</span>" data-selected="false" data-id="' + data.table[i].id + '" data-iconName="' + data.table[i].icon + '" data-mainWidget="' + data.table[i].mainWidget + '" data-targetWidget="' + data.table[i].targetWidget + '" data-snap4CityType="' + data.table[i].snap4CityType + '" data-icon="' + data.table[i].icon + '" data-mono_multi="' + data.table[i].mono_multi + '" data-description="' + data.table[i].description + '" class="iconsMonoMultiIcon addWidgetWizardIconClickClass2"></div>');
                        newIcon.css('background-image', 'url("../img/widgetIcons/multi/' + data.table[i].icon + '")');

                        $('.addWidgetWizardIconsCnt2').eq(1).append(newIcon);
                    }
                }

                $('[data-toggle="popover"]').popover();

                // GESTIONE CLICK ICONE DASHBOARD WIZARD
                $('.addWidgetWizardIconClickClass2').click(function ()
                {
                    var wasSelected = false;
                    
                    $('#widgetWizardActuatorFieldsRow2').hide();
                    $('#actuatorEntityNameCell2 .wizardActInputCnt').val('');
                    $('#actuatorValueTypeCell2 .wizardActInputCnt').val('');
                    $('#actuatorMinBaseValueCell2 .wizardActInputCnt').val('');
                    $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').val('');
                    $('.hideIfActuatorNew').show();

                    if($(this).attr('data-selected') === 'false')
                    {
                        validityConditions.widgetTypeSelected = true;
                        $('#actuatorTargetInstance2').val("existent");
                        $('#actuatorTargetWizard2').val(-1);
                        
                        switch($(this).attr('data-mainwidget'))
                        {
                            case "widgetKnob":
                                $('#actuatorMinBaseValueCell2 .wizardActLbl').html("Min value");
                                $('#actuatorMaxBaseValueCell2 .wizardActLbl').html("Max value");
                                $('#actuatorMinBaseValue2').val(0);
                                $('#actuatorMaxImpulseValue2').val(100);
                                break;
                                
                            case "widgetOnOffButton":
                                $('#actuatorMinBaseValueCell2 .wizardActLbl').html("Off value");
                                $('#actuatorMaxBaseValueCell2 .wizardActLbl').html("On value");
                                $('#actuatorMinBaseValue2').val("Off");
                                $('#actuatorMaxImpulseValue2').val("On");
                                break; 
                                
                            case "widgetImpulseButton":
                                $('#actuatorMinBaseValueCell2 .wizardActLbl').html("Base value");
                                $('#actuatorMaxBaseValueCell2 .wizardActLbl').html("Impulse value");
                                $('#actuatorMinBaseValue2').val("Off");
                                $('#actuatorMaxImpulseValue2').val("On");
                                break;
                                
                            case "widgetNumericKeyboard":
                                break;  
                                
                            default:
                                break;
                        }
                        
                        $('.addWidgetWizardIconClickClass2').each(function (i) {
                            $(this).attr('data-selected', 'false');
                            $(this).css('border', 'none');
                        });
                        $(this).attr('data-selected', 'true');
                        $(this).css('border', '1px solid rgba(0, 162, 211, 1)');
                        
                        if($(this).attr('data-widgetCategory') === 'actuator')
                        {
                            if(!location.href.includes("dashboard_configdash.php")&&!location.href.includes("prova2.php")&&!location.href.includes("dashboards.php")&&!location.href.includes("iframeApp.php"))
                            {/*
                                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr("data-templatename") === 'iotApps')
                                {
                                    $('#actuatorTargetInstance2').val('existent');
                                    $('#widgetWizardActuatorFieldsRow2').hide();
                                }
                                else
                                {
                                    $('#widgetWizardActuatorFieldsRow2').show();
                                }
                            */}
                            else
                            {
                                $('#widgetWizardActuatorFieldsRow2').show();
                            }
                        }
                        else
                        {
                            $('#widgetWizardActuatorFieldsRow2').hide();
                        }
 
                        choosenWidgetIconName = $(this).attr('data-icon');
                    } 
                    else
                    {
                        validityConditions.widgetTypeSelected = false;
                        $('#addWidgetWizardWidgetAvailableMsg2').html("");
                        $(this).attr('data-selected', 'false');
                        $(this).css('border', 'none');

                        choosenWidgetIconName = null;
                        
                        $('#widgetWizardActuatorFieldsRow2').hide();
                        
                        wasSelected = true;
                    }

                    var selected = $(this).attr('data-selected');
                    
                    console.log("choosenWidgetIconName: " + choosenWidgetIconName + " - Selected: " + selected + "Validity condition: " + validityConditions.widgetTypeSelected);

                    //LOGICA DI GESTIONE DEI CLICK
                    //Versione pregressa: al deselect dell'icona vengono "riticcati" tutti i tipi di dato in quel momento nel menu a tendina delle unit
                    globalSqlFilter[6].allSelected = (selected === "false");     

                    var unit = $(this).attr('data-snap4CityType');
                            
                    $('#unitSelect2').multiselect2('deselectAll', false);        
                    
                    var unitArray = null;
                    
                    if(selected === "true") 
                    {
                        unitArray = unit.split(',');

                        for(var k = 0; k < unitArray.length; k++) 
                        {
                            $('#unitSelect2').multiselect2('select', unitArray[k].trim());
                        }
                    }
                    else
                    {
                        unit = '';        
                        unitArray = []; 
                        unitSelect.multiselect2('selectAll', false);
                    }

                    var search = [];

                    for(k = 0; k < unitArray.length; k++) 
                    {
                        search.push(unitArray[k].trim());
                    }
                    
                    $.each($('#unitSelect2 option:selected'), function () {
                        if((!unit.includes($(this).val()))&&(search.indexOf($(this).val()) !== -1)) 
                        {
                            search.push(unit);
                        }
                    });
                    
                    var nOptions = 0;
                    $.each($('#unitSelect2 option'), function () {
                        nOptions++;
                    });

                    globalSqlFilter[6].allSelected = (search.length === nOptions);
                    if(search.length === nOptions)
                    {
                        search = [];
                    }
                    
                    globalSqlFilter[6].selectedVals = search;
                    search = search.join('|');
                    
                    widgetWizardTable.column(6).search(search, false, false).draw();
                    globalSqlFilter[6].value = search;
                    
                    if(!validityConditions.widgetTypeSelected)
                    {
                        globalSqlFilter[6].allSelected = true;
                    }
                    
                    // Chiamata a funzione per popolare menù multi-select di filtraggio
                    for(var n = 0; n < 9; n++) 
                    {
                        if(n !== 4 && n != 5) 
                        {
                            if(selected === "true")
                            {
                                populateSelectMenus("unit", search, unitSelect, "#unitFilterColumn", n, n === 6, false);
                            }
                            else
                            {
                                if(widgetWizardSelectedUnits.length > 0)
                                {
                                    populateSelectMenus("unit", search, unitSelect, "#unitFilterColumn", n, n === 6, false);
                                }
                                else
                                {
                                    populateSelectMenus("unit", search, unitSelect, "#unitFilterColumn", n, n === 6, true);
                                }
                            }
                        }
                    }
                    
                    if((wasSelected)&&($(this).attr('data-widgetCategory') === 'actuator'))
                    {
                        $('#actuatorTargetInstance2').val("existent");
                        $('#actuatorTargetWizard2').val(-1);
                        $('#actuatorTargetCell2 .wizardActLbl').hide();
                        $('#actuatorTargetCell2 .wizardActInputCnt').hide();
                        $('#actuatorEntityNameCell2 .wizardActLbl').hide();
                        $('#actuatorEntityNameCell2 .wizardActInputCnt').hide();
                        $('#actuatorValueTypeCell2 .wizardActLbl').hide();
                        $('#actuatorValueTypeCell2 .wizardActInputCnt').hide();
                        $('#actuatorMinBaseValueCell2 .wizardActLbl').hide();
                        $('#actuatorMinBaseValueCell2 .wizardActInputCnt').hide();
                        $('#actuatorMaxBaseValueCell2 .wizardActLbl').hide();
                        $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').hide();
                        //Reset campi custom attuatori
                        $('#actuatorEntityName2').val('');
                        $('#actuatorValueType2').val('');
                        $('.hideIfActuatorNew').show();
                    }
                    checkTab1Conditions();
                    updateWidgetCompatibleRows();
                });

            },
            error: function (errorData)
            {

            }
        });

        //Funzione che prepara icone custom su mappa in base a quelle di ServiceMap
        function addWidgetWizardCreateCustomMarker(feature, latlng) {
            if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                var mapPinImg = '../img/gisMapIcons/generic.png';
            } else {
                var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
            }
            var markerIcon = L.icon({
                iconUrl: mapPinImg,
                iconAnchor: [16, 37]
            });

            var marker = new L.Marker(latlng, {icon: markerIcon});

            marker.on('mouseover', function (event) {
                if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                    var hoverImg = '../img/gisMapIcons/over/generic_over.png';
                } else {
                    var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                }
                var hoverIcon = L.icon({
                    iconUrl: hoverImg
                });
                event.target.setIcon(hoverIcon);
            });

            marker.on('mouseout', function (event) {
                if (feature.properties.serviceType === 'IoTDevice_IoTSensor') {
                    var outImg = '../img/gisMapIcons/generic.png';
                } else {
                    var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                }
                var outIcon = L.icon({
                    iconUrl: outImg
                });
                event.target.setIcon(outIcon);
            });

            marker.on('click', function (event) {
                event.target.unbindPopup();
                newpopup = null;
                var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                var urlToCall, fake, fakeId = null;

                if (feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                } else
                {
                    urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json";
                    fake = false;
                }

                var latLngId = event.target.getLatLng().lat + "" + event.target.getLatLng().lng;
                latLngId = latLngId.replace(".", "");
                latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

                $.ajax({
                    url: urlToCall,
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function (geoJsonServiceData)
                    {
                        var fatherNode = null;
                        if (geoJsonServiceData.hasOwnProperty("BusStop"))
                        {
                            fatherNode = geoJsonServiceData.BusStop;
                        } else
                        {
                            if (geoJsonServiceData.hasOwnProperty("Sensor"))
                            {
                                fatherNode = geoJsonServiceData.Sensor;
                            } else
                            {
                                //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                fatherNode = geoJsonServiceData.Service;
                            }
                        }

                        var serviceProperties = fatherNode.features[0].properties;
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        fatherNode.features[0].properties.targetWidgets = feature.properties.targetWidgets;
                        fatherNode.features[0].properties.color1 = feature.properties.color1;
                        fatherNode.features[0].properties.color2 = feature.properties.color2;
                        targetWidgets = feature.properties.targetWidgets;
                        color1 = feature.properties.color1;
                        color2 = feature.properties.color2;

                        //Popup nuovo stile uguali a quelli degli eventi ricreativi
                        popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>';
                        popupText += '<div class="recreativeEventMapBtnContainer"><button data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Details</button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</button><button data-id="' + latLngId + '" class="recreativeEventMapContactsBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">RT data</button></div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';

                        popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable">';
                        //Intestazione
                        popupText += '<thead>';
                        popupText += '<th style="background: ' + color2 + '">Description</th>';
                        popupText += '<th style="background: ' + color2 + '">Value</th>';
                        popupText += '</thead>';

                        //Corpo
                        popupText += '<tbody>';

                        if (serviceProperties.hasOwnProperty('website'))
                        {
                            if ((serviceProperties.website !== '') && (serviceProperties.website !== undefined) && (serviceProperties.website !== 'undefined') && (serviceProperties.website !== null) && (serviceProperties.website !== 'null'))
                            {
                                if (serviceProperties.website.includes('http') || serviceProperties.website.includes('https'))
                                {
                                    popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                } else
                                {
                                    popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                }
                            } else
                            {
                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>Website</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('email'))
                        {
                            if ((serviceProperties.email !== '') && (serviceProperties.email !== undefined) && (serviceProperties.email !== 'undefined') && (serviceProperties.email !== null) && (serviceProperties.email !== 'null'))
                            {
                                popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                            } else
                            {
                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('address'))
                        {
                            if ((serviceProperties.address !== '') && (serviceProperties.address !== undefined) && (serviceProperties.address !== 'undefined') && (serviceProperties.address !== null) && (serviceProperties.address !== 'null'))
                            {
                                popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                            } else
                            {
                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>Address</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('civic'))
                        {
                            if ((serviceProperties.civic !== '') && (serviceProperties.civic !== undefined) && (serviceProperties.civic !== 'undefined') && (serviceProperties.civic !== null) && (serviceProperties.civic !== 'null'))
                            {
                                popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                            } else
                            {
                                popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('cap'))
                        {
                            if ((serviceProperties.cap !== '') && (serviceProperties.cap !== undefined) && (serviceProperties.cap !== 'undefined') && (serviceProperties.cap !== null) && (serviceProperties.cap !== 'null'))
                            {
                                popupText += '<tr><td>C.A.P.</td><td>' + serviceProperties.cap + '</td></tr>';
                            }
                        }

                        if (serviceProperties.hasOwnProperty('city'))
                        {
                            if ((serviceProperties.city !== '') && (serviceProperties.city !== undefined) && (serviceProperties.city !== 'undefined') && (serviceProperties.city !== null) && (serviceProperties.city !== 'null'))
                            {
                                popupText += '<tr><td>City</td><td>' + serviceProperties.city + '</td></tr>';
                            } else
                            {
                                popupText += '<tr><td>City</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>City</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('province'))
                        {
                            if ((serviceProperties.province !== '') && (serviceProperties.province !== undefined) && (serviceProperties.province !== 'undefined') && (serviceProperties.province !== null) && (serviceProperties.province !== 'null'))
                            {
                                popupText += '<tr><td>Province</td><td>' + serviceProperties.province + '</td></tr>';
                            }
                        }

                        if (serviceProperties.hasOwnProperty('phone'))
                        {
                            if ((serviceProperties.phone !== '') && (serviceProperties.phone !== undefined) && (serviceProperties.phone !== 'undefined') && (serviceProperties.phone !== null) && (serviceProperties.phone !== 'null'))
                            {
                                popupText += '<tr><td>Phone</td><td>' + serviceProperties.phone + '</td></tr>';
                            } else
                            {
                                popupText += '<tr><td>Phone</td><td>-</td></tr>';
                            }
                        } else
                        {
                            popupText += '<tr><td>Phone</td><td>-</td></tr>';
                        }

                        if (serviceProperties.hasOwnProperty('fax'))
                        {
                            if ((serviceProperties.fax !== '') && (serviceProperties.fax !== undefined) && (serviceProperties.fax !== 'undefined') && (serviceProperties.fax !== null) && (serviceProperties.fax !== 'null'))
                            {
                                popupText += '<tr><td>Fax</td><td>' + serviceProperties.fax + '</td></tr>';
                            }
                        }

                        if (serviceProperties.hasOwnProperty('note'))
                        {
                            if ((serviceProperties.note !== '') && (serviceProperties.note !== undefined) && (serviceProperties.note !== 'undefined') && (serviceProperties.note !== null) && (serviceProperties.note !== 'null'))
                            {
                                popupText += '<tr><td>Notes</td><td>' + serviceProperties.note + '</td></tr>';
                            }
                        }

                        if (serviceProperties.hasOwnProperty('agency'))
                        {
                            if ((serviceProperties.agency !== '') && (serviceProperties.agency !== undefined) && (serviceProperties.agency !== 'undefined') && (serviceProperties.agency !== null) && (serviceProperties.agency !== 'null'))
                            {
                                popupText += '<tr><td>Agency</td><td>' + serviceProperties.agency + '</td></tr>';
                            }
                        }

                        if (serviceProperties.hasOwnProperty('code'))
                        {
                            if ((serviceProperties.code !== '') && (serviceProperties.code !== undefined) && (serviceProperties.code !== 'undefined') && (serviceProperties.code !== null) && (serviceProperties.code !== 'null'))
                            {
                                popupText += '<tr><td>Code</td><td>' + serviceProperties.code + '</td></tr>';
                            }
                        }

                        popupText += '</tbody>';
                        popupText += '</table>';

                        if (geoJsonServiceData.hasOwnProperty('busLines'))
                        {
                            if (geoJsonServiceData.busLines.results.bindings.length > 0)
                            {
                                popupText += '<b>Lines: </b>';
                                for (var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++)
                                {
                                    popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';
                                }
                            }
                        }

                        popupText += '</div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                        if (serviceProperties.hasOwnProperty('description'))
                        {
                            if ((serviceProperties.description !== '') && (serviceProperties.description !== undefined) && (serviceProperties.description !== 'undefined') && (serviceProperties.description !== null) && (serviceProperties.description !== 'null'))
                            {
                                popupText += serviceProperties.description + "<br>";
                            } else
                            {
                                popupText += "No description available";
                            }
                        } else
                        {
                            popupText += 'No description available';
                        }

                        popupText += '</div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';

                        var hasRealTime = false;

                        if (geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if (!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;

                                popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';

                                if ((serviceClass.includes("Emergency")) && (serviceSubclass.includes("First aid")))
                                {
                                    //Tabella ad hoc per First Aid
                                    popupText += '<table id="' + latLngId + '" class="psPopupTable">';
                                    var series = {
                                        "firstAxis": {
                                            "desc": "Priority",
                                            "labels": [
                                                "Red code",
                                                "Yellow code",
                                                "Green code",
                                                "Blue code",
                                                "White code"
                                            ]
                                        },
                                        "secondAxis": {
                                            "desc": "Status",
                                            "labels": [],
                                            "series": []
                                        }
                                    };

                                    var dataSlot = null;

                                    measuredTime = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ").replace("Z", "");

                                    for (var i = 0; i < realTimeData.results.bindings.length; i++)
                                    {
                                        if (realTimeData.results.bindings[i].state.value.indexOf("estinazione") > 0)
                                        {
                                            series.secondAxis.labels.push("Addressed");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("ttesa") > 0)
                                        {
                                            series.secondAxis.labels.push("Waiting");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("isita") > 0)
                                        {
                                            series.secondAxis.labels.push("In visit");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("emporanea") > 0)
                                        {
                                            series.secondAxis.labels.push("Observation");
                                        }

                                        if (realTimeData.results.bindings[i].state.value.indexOf("tali") > 0)
                                        {
                                            series.secondAxis.labels.push("Totals");
                                        }

                                        dataSlot = [];
                                        dataSlot.push(realTimeData.results.bindings[i].redCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].yellowCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].greenCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].blueCode.value);
                                        dataSlot.push(realTimeData.results.bindings[i].whiteCode.value);

                                        series.secondAxis.series.push(dataSlot);
                                    }

                                    var colsQt = parseInt(parseInt(series.firstAxis.labels.length) + 1);
                                    var rowsQt = parseInt(parseInt(series.secondAxis.labels.length) + 1);

                                    for (var i = 0; i < rowsQt; i++)
                                    {
                                        var newRow = $("<tr></tr>");
                                        var z = parseInt(parseInt(i) - 1);

                                        if (i === 0)
                                        {
                                            //Riga di intestazione
                                            for (var j = 0; j < colsQt; j++)
                                            {
                                                if (j === 0)
                                                {
                                                    //Cella (0,0)
                                                    var newCell = $("<td></td>");

                                                    newCell.css("background-color", "transparent");
                                                } else
                                                {
                                                    //Celle labels
                                                    var k = parseInt(parseInt(j) - 1);
                                                    var colLabelBckColor = null;
                                                    switch (k)
                                                    {
                                                        case 0:
                                                            colLabelBckColor = "#ff0000";
                                                            break;

                                                        case 1:
                                                            colLabelBckColor = "#ffff00";
                                                            break;

                                                        case 2:
                                                            colLabelBckColor = "#66ff33";
                                                            break;

                                                        case 3:
                                                            colLabelBckColor = "#66ccff";
                                                            break;

                                                        case 4:
                                                            colLabelBckColor = "#ffffff";
                                                            break;
                                                    }

                                                    newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                                                    newCell.css("font-weight", "bold");
                                                    newCell.css("background-color", colLabelBckColor);
                                                }
                                                newRow.append(newCell);
                                            }
                                        } else
                                        {
                                            //Righe dati
                                            for (var j = 0; j < colsQt; j++)
                                            {
                                                k = parseInt(parseInt(j) - 1);
                                                if (j === 0)
                                                {
                                                    //Cella label
                                                    newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                    newCell.css("font-weight", "bold");
                                                } else
                                                {
                                                    //Celle dati
                                                    newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                                    if (i === (rowsQt - 1))
                                                    {
                                                        newCell.css('font-weight', 'bold');
                                                        switch (j)
                                                        {
                                                            case 1:
                                                                newCell.css('background-color', '#ffb3b3');
                                                                break;

                                                            case 2:
                                                                newCell.css('background-color', '#ffff99');
                                                                break;

                                                            case 3:
                                                                newCell.css('background-color', '#d9ffcc');
                                                                break;

                                                            case 4:
                                                                newCell.css('background-color', '#cceeff');
                                                                break;

                                                            case 5:
                                                                newCell.css('background-color', 'white');
                                                                break;
                                                        }
                                                    }
                                                }
                                                newRow.append(newCell);
                                            }
                                        }
                                        popupText += newRow.prop('outerHTML');
                                    }

                                    popupText += '</table>';
                                } else
                                {
                                    //Tabella nuovo stile
                                    popupText += '<table id="' + latLngId + '" class="gisPopupTable">';

                                    //Intestazione
                                    popupText += '<thead>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</th>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Value</th>';
                                    popupText += '<th colspan="5" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Buttons</th>';
                                    popupText += '</thead>';

                                    //Corpo
                                    popupText += '<tbody>';
                                    var dataDesc, dataVal, dataLastBtn, data4HBtn, dataDayBtn, data7DayBtn, data30DayBtn = null;
                                    for (var i = 0; i < realTimeData.head.vars.length; i++)
                                    {
                                        if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined'))
                                        {
                                            if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime'))
                                            {
                                                if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available'))
                                                {
                                                    //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                    dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                        return str.toUpperCase();
                                                    });
                                                    dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                    dataLastBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>value</button></td>';
                                                    data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                    dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                    data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                    data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                    popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + dataLastBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                }
                                            } else
                                            {
                                                measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                var now = new Date();
                                                var measuredTimeDate = new Date(measuredTime);
                                                rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
                                            }
                                        }
                                    }
                                    popupText += '</tbody>';
                                    popupText += '</table>';
                                    popupText += '<p><b>Keep data on target widget(s) after popup close: </b><input data-id="' + latLngId + '" type="checkbox" class="gisPopupKeepDataCheck" data-keepData="false"/></p>';
                                }

                                hasRealTime = true;
                            }
                        }

                        popupText += '</div>';

                        newpopup = L.popup({
                            closeOnClick: false, //Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0],
                            minWidth: 435,
                            maxWidth: 435
                        }).setContent(popupText);

                        event.target.bindPopup(newpopup).openPopup();

                        if (hasRealTime)
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                            $('span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                        } else
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                        }

                        $('button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function () {
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function () {
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function () {
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        if (hasRealTime)
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                        }

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function () {
                            $(this).css("outline", "0");
                        });

                        $('input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                        $('input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function () {
                            if ($(this).attr("data-keepData") === "false")
                            {
                                $(this).attr("data-keepData", "true");
                            } else
                            {
                                $(this).attr("data-keepData", "false");
                            }
                        });
                    },
                    error: function (errorData)
                    {
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        var serviceProperties = feature.properties;

                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        popupText = '<h3 class="gisPopupTitle">' + serviceProperties.name + '</h3>' +
                                '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '</p>' +
                                '<p><i>Data are limited due to an issue in their retrieval</i></p>';

                        event.target.bindPopup(popupText, {
                            offset: [15, 0],
                            minWidth: 215,
                            maxWidth: 600
                        }).openPopup();
                    }
                });
            });
            return marker;
        }

        // widgetWizardTable JS LOGIC ************************************************************************
        $('.checkWidgWizCol').change(function (e) {
            e.preventDefault();
            if ($(this).attr('data-fieldTitle') === "high_level_type") {
                var idx = 0;
            } else if ($(this).attr('data-fieldTitle') === "nature") {
                var idx = 1;
            } else if ($(this).attr('data-fieldTitle') === "sub_nature") {
                var idx = 2;
            } else if ($(this).attr('data-fieldTitle') === "low_level_type") {
                var idx = 3;
            } else if ($(this).attr('data-fieldTitle') === "unique_name_id") {
                var idx = 4;
            } else if ($(this).attr('data-fieldTitle') === "unit") {
                var idx = 6;
            } else if ($(this).attr('data-fieldTitle') === "last_date") {
                var idx = 7;
            } else if ($(this).attr('data-fieldTitle') === "last_value") {
                var idx = 8;
            } else if ($(this).attr('data-fieldTitle') === "healthiness") {
                var idx = 9;
            } else if ($(this).attr('data-fieldTitle') === "lastCheck") {
                var idx = 13;
            } else if ($(this).attr('data-fieldTitle') === "ownership") {
                var idx = 15;
            }
            if ($(this).is(":checked")) {
                // Get the column API object
                var column = widgetWizardTable.column(idx);
                // Toggle the visibility
                column.visible(!column.visible());
            } else {

                var column = widgetWizardTable.column(idx);
                column.visible(!column.visible());

            }
        });


        //$('#uniqueNameIdFilterColumn').append('<input id="widgetWIzardTableSearch" type="text" placeholder="Search Value Name" />');

        // Funzione per il popolamento del menù multi-select di filtraggio tabella widgetWIzardTable
        function populateSelectMenus(field, searchTerm, selectElement, columnFilterDivId, n, fromIconFlag, updateIconsFlag)
        {
            globalSqlFilter[n].active = "";
            var distinctField = "";

            if (n == 0) 
            {
                distinctField = "high_level_type";
            } 
            else if (n == 1) 
            {
                distinctField = "nature";
            } 
            else if (n == 2) 
            {
                distinctField = "sub_nature";
            } 
            else if (n == 3) 
            {
                distinctField = "low_level_type";
            } else if (n == 6) {
                distinctField = "unit";
            } else if (n == 7) {
                distinctField = "healthiness";
            } else if (n == 8) {
                distinctField = "ownership";
            }

            var nActive = 0;
            for (var i = 0, len = globalSqlFilter.length; i < len; i++) 
            {
                if (globalSqlFilter[i].value != "") {
                    nActive++;
                }
            }

            //if(distinctField !== field || nActive == 0) 
            if((distinctField !== field || nActive == 0)||fromIconFlag) 
            {
                /*if(fromIconFlag == false) 
                {*/
                    var whereString = "";


                    // FARE QUI COMPOSIZIONE FILTRO GLOBALE STRINGA  GUARDANDO QUALE NON E' FIELD !
                    for (i = 0; i < 9; i++) {
                        if (i !== 4 && i != 5) {
                            if ((i != n || nActive > 1)) {
                                var str = globalSqlFilter[i].value;
                                var auxArray = str.split("|");
                                var auxFilterString = "";
                                for (var j in auxArray) {
                                    if (auxArray[j] != '') {
                                        if (j != 0) {
                                            auxFilterString = auxFilterString + " OR " + globalSqlFilter[i].field + " LIKE '%" + auxArray[j] + "%'";
                                        } else {
                                            auxFilterString = globalSqlFilter[i].field + " LIKE '%" + auxArray[j] + "%'";
                                        }
                                    }
                                }

                                if (auxFilterString != '') {
                                    if (i != 0) {
                                        whereString = whereString + " AND (" + auxFilterString + ")";
                                    } else {
                                        whereString = whereString + "(" + auxFilterString + ")";
                                    }
                                }
                            }
                        }
                    }

                    $.ajax({
                        url: "../controllers/dashboardWizardController.php",
                        type: "GET",
                        async: true, 
                        dataType: 'json',
                        data:
                        {
                            filter: distinctField,
                            filterGlobal: whereString,
                            distinctField: distinctField 
                        },
                        success: function (data)
                        {
                            var dataNew = [];
                            var select = "";
                            if (distinctField === "high_level_type") {
                                select = $("#highLevelTypeSelect2");
                            } else if (distinctField === "nature") {
                                select = $("#natureSelect2");
                            } else if (distinctField === "sub_nature") {
                                select = $("#subnatureSelect2");
                            } else if (distinctField === "low_level_type") {
                                select = $("#lowLevelTypeSelect2");
                            } else if (distinctField === "unit") {
                                select = $("#unitSelect2");
                            } else if (distinctField === "healthiness") {
                                select = $("#healthinessSelect2");
                            } else if (distinctField === "ownership") {
                                select = $("#ownershipSelect2");
                            }

                            for (var x = 0; x < data.table.length; x++) 
                            {
                                if (x == 0) 
                                {
                                    select.children('option').remove().end();
                                }

                                var auxVar = data.table[x][Object.keys(data.table[x])[0]];
                                
                                options = '<option value="' + auxVar + '">' + auxVar + '</option>';
                                select.append(options);
                                
                                var selectedFlag = globalSqlFilter[n].allSelected || globalSqlFilter[n].selectedVals.includes(auxVar);
                                dataNew[x] = {label: auxVar, value: auxVar, selected: selectedFlag};
                            }
                            
                            if((n === 6)&&updateIconsFlag) 
                            {
                                updateIcons(dataNew);
                            }

                            select.multiselect2('dataprovider', dataNew);
                        }

                    });
                /*}
                else
                {
                    console.log("Icon flag false");
                }*/
            }
        }


        function updateIcons(data)
        {
            $('.addWidgetWizardIconClickClass2').each(function () {
                var snap4citytype = $(this).attr('data-snap4citytype');
                var snap4citytypeArray = snap4citytype.split(',');

                for (k = 0; k < snap4citytypeArray.length; k++) {
                    snap4citytypeArray[k] = snap4citytypeArray[k].trim();
                }

                var found = false;

                for (j = 0; j < snap4citytypeArray.length; j++) {

                    for (i = 0; i < data.length; i++)
                    {
                        if (data[i].selected === true)
                        {
                            if (data[i].value !== snap4citytypeArray[j])
                            {
                                // $(this).hide();

                            } else
                            {
                                found = true;
                                //  $(this).show();
                            }
                        } else
                        {
                            //Da verificare
                            //  $(this).hide();
                        }
                    }
                }

                if (found == true) {
                    $(this).show();
                } else {
                    $(this).hide();
                }

            });
        }
        ;

        //Handler del bottone di reset dei filtri
        function resetFilter()
        {
            widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-selected', false);
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.widgetTypeSelected = false;
            validityConditions.brokerAndNrRowsTogether = true;
            validityConditions.atLeastOneRowSelected = false;
            validityConditions.actuatorFieldsEmpty = true;
            validityConditions.canProceed = false;
            checkTab1Conditions();
            countSelectedRows();
            $('#actuatorEntityNameCell2 .wizardActInputCnt').val('');
            $('#actuatorValueTypeCell2 .wizardActInputCnt').val('');
            $('#actuatorMinBaseValueCell2 .wizardActInputCnt').val('');
            $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').val('');
            
            $('#widgetWizardTable2_filter input[type="search"]').val('');
            
            widgetWizardTable.search('').draw();
            widgetWizardSelectedRowsTable.search('').draw();
            
            validityConditions = {
                dashboardTitleOk: false,
                widgetTypeSelected: false,
                brokerAndNrRowsTogether: true,
                atLeastOneRowSelected: false,
                actuatorFieldsEmpty: true
            };

            for(var layerKey in gisLayersOnMap)
            {
                addWidgetWizardMapRef.removeLayer(gisLayersOnMap[layerKey]);
            }
            
            var selectedValsHighLevelType = [];
            var allSelectedHighLevelType = true;
            var searchValueHighLevelType = "";
            
            var selectedValsNature = [];
            var allSelectedNature = true;
            var searchValueNature = "";
            
            var selectedValsSubnature = [];
            var allSelectedSubnature = true;
            var searchValueSubnature = "";
            
            var selectedValsLowLevelType = [];
            var allSelectedLowLevelType = true;
            var searchValueLowLevelType = "";
            
            var selectedValsUnit = [];
            var allSelectedUnit = true;
            var searchValueUnit = "";
            
            var selectedValsHealth = [];
            var allSelectedHealth = true;
            var searchValueHealth = "";
            
            var selectedValsOwnership = [];
            var allSelectedOwnership = true;
            var searchValueOwnership = "";
            
            //Questo if distingue il caso in cui stiamo agendo sui template di dashboard
            if(!location.href.includes("dashboard_configdash.php")&&!location.href.includes("prova2.php")&&!location.href.includes("dashboards.php")&&!location.href.includes("iframeApp.php"))
            {/*
                //Gestione del preset high level type da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-highlevelsel') !== 'any')
                {
                    selectedValsHighLevelType = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-highlevelsel').split('|');
                    allSelectedHighLevelType = false;
                    searchValueHighLevelType = selectedValsHighLevelType.join('|');
                }
                
                //Gestione del preset nature da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-naturesel') !== 'any')
                {
                    selectedValsNature = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-naturesel').split('|');
                    allSelectedNature = false;
                    searchValueNature = selectedValsNature.join('|');
                }
                
                //Gestione del preset subnature da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-subnaturesel') !== 'any')
                {
                    selectedValsSubnature = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-subnaturesel').split('|');
                    allSelectedSubnature = false;
                    searchValueSubnature = selectedValsSubnature.join('|');
                }
                
                //Gestione del preset low level type da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypesel') !== 'any')
                {
                    selectedValsLowLevelType = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-valuetypesel').split('|');
                    allSelectedLowLevelType = false;
                    searchValueLowLevelType = selectedValsLowLevelType.join('|');
                }
                
                //Gestione del preset unit da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-datatypesel') !== 'any')
                {
                    selectedValsUnit = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-datatypesel').split('|');
                    allSelectedUnit = false;
                    searchValueUnit = selectedValsUnit.join('|');
                }
                
                //Gestione del preset healthiness da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-healthinesssel') !== 'any')
                {
                    selectedValsHealth = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-healthinesssel').split('|');
                    allSelectedHealth = false;
                    searchValueHealth = selectedValsHealth.join('|');
                }
                
                //Gestione del preset ownership da template dashboard
                if($('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-ownershipsel') !== 'any')
                {
                    selectedValsOwnership = $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').attr('data-ownershipsel').split('|');
                    allSelectedOwnership = false;
                    searchValueOwnership = selectedValsOwnership.join('|');
                }
            */}
            
            globalSqlFilter = [
                {
                    "field": "high_level_type",
                    "value": searchValueHighLevelType,
                    "active": "false",
                    "selectedVals": selectedValsHighLevelType,
                    "allSelected": allSelectedHighLevelType
                },
                {
                    "field": "nature",
                    "value": searchValueNature,
                    "active": "false",
                    "selectedVals": selectedValsNature,
                    "allSelected": allSelectedNature
                },
                {
                    "field": "sub_nature",
                    "value": searchValueSubnature,
                    "active": "false",
                    "selectedVals": selectedValsSubnature,
                    "allSelected": allSelectedSubnature
                },
                {
                    "field": "low_level_type",
                    "value": searchValueLowLevelType,
                    "active": "false",
                    "selectedVals": selectedValsLowLevelType,
                    "allSelected": allSelectedLowLevelType
                },
                {
                    "field": "unique_name_id",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "instance_uri",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unit",
                    "value": searchValueUnit,
                    "active": "false",
                    "selectedVals": selectedValsUnit,
                    "allSelected": allSelectedUnit
                },
                {
                    "field": "healthiness",
                    "value": searchValueHealth,
                    "active": "false",
                    "selectedVals": selectedValsHealth,
                    "allSelected": allSelectedHealth
                },
                {
                    "field": "ownership",
                    "value": searchValueOwnership,
                    "active": "false",
                    "selectedVals": selectedValsOwnership,
                    "allSelected": allSelectedOwnership
                }
            ];
            
            for(n = 0; n < 17; n++) 
            {
                switch(n)
                {
                    case 0:
                        widgetWizardTable.column(0).search(searchValueHighLevelType, false, false);
                        break;
                        
                    case 1:
                        widgetWizardTable.column(n).search(searchValueNature, true, false); 
                        break;
                        
                    case 2:
                        widgetWizardTable.column(n).search(searchValueSubnature, true, false); 
                        break;    
                        
                    case 3:
                        widgetWizardTable.column(n).search(searchValueLowLevelType, true, false); 
                        break;        
                        
                    case 6:
                        widgetWizardTable.column(n).search(searchValueUnit, true, false); 
                        break;    
                        
                    case 9:
                        widgetWizardTable.column(n).search(searchValueHealth, true, false); 
                        break;    
                        
                    case 15:
                        widgetWizardTable.column(n).search(searchValueOwnership, true, false); 
                        break;    
                        
                    default://Ci cadono anche 4 e 5
                        break;
                }
            }
            
            widgetWizardTable.draw();

            for(var n = 0; n < 9; n++) 
            {
                if (n !== 4 && n != 5) 
                {
                    populateSelectMenus("", "", null, "", n, false, true);
                }
            }
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        }//Fine funzione reset filter
        
        function resetFilterForced()
        {
            widgetWizardSelectedRows = {};
            choosenWidgetIconName = null;
            $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-selected', false);
            widgetWizardSelectedRowsTable.clear().draw(false);
            validityConditions.widgetTypeSelected = false;
            validityConditions.brokerAndNrRowsTogether = true;
            validityConditions.atLeastOneRowSelected = false;
            validityConditions.actuatorFieldsEmpty = true;
            validityConditions.canProceed = false;
            checkTab1Conditions();
            countSelectedRows();
            $('#actuatorEntityNameCell2 .wizardActInputCnt').val('');
            $('#actuatorValueTypeCell2 .wizardActInputCnt').val('');
            $('#actuatorMinBaseValueCell2 .wizardActInputCnt').val('');
            $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').val('');
            
            widgetWizardTable.search('').draw();
            widgetWizardSelectedRowsTable.search('').draw();
            
            validityConditions = {
                dashboardTitleOk: false,
                widgetTypeSelected: false,
                brokerAndNrRowsTogether: true,
                atLeastOneRowSelected: false,
                actuatorFieldsEmpty: true
            };

            for(var layerKey in gisLayersOnMap)
            {
                addWidgetWizardMapRef.removeLayer(gisLayersOnMap[layerKey]);
            }
            
            var selectedValsHighLevelType = [];
            var allSelectedHighLevelType = true;
            var searchValueHighLevelType = "";
            
            globalSqlFilter = [
                {
                    "field": "high_level_type",
                    "value": searchValueHighLevelType,
                    "active": "false",
                    "selectedVals": selectedValsHighLevelType,
                    "allSelected": allSelectedHighLevelType
                },
                {
                    "field": "nature",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "sub_nature",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "low_level_type",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unique_name_id",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "instance_uri",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "unit",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "healthiness",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                },
                {
                    "field": "ownership",
                    "value": "",
                    "active": "false",
                    "selectedVals": [],
                    "allSelected": true
                }
            ];
            
            selectedValsHighLevelType = selectedValsHighLevelType.join('|');
            
            for (n = 0; n < 17; n++) 
            {
                if((n != 4)&&(n != 5)) 
                {
                    widgetWizardTable.column(n).search("", true, false);
                }
            }
            
            widgetWizardTable.draw();

            for(var n = 0; n < 9; n++) 
            {
                if (n !== 4 && n != 5) 
                {
                    populateSelectMenus("", "", null, "", n, false, true);
                }
            }
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        }//Fine funzione reset filter

        widgetWizardPageLength = 8;

        //Creazione tabella GUI righe selezionate
        widgetWizardSelectedRowsTable = $('#widgetWizardSelectedRowsTable2').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "paging": true,
            "language": {search: ""},
            "pageLength": 8,
            aaSorting: [[0, 'desc']],
            "createdRow": function (row, data, index) {
                $(row).attr('data-rowId', data[11]);
                $(row).attr('data-widgetCompatible', data[12]);

                $(row).find('.widgetWizardSelectedRowsDelBtn').click(function ()
                {
                    var delesectedUnit = widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')].unit;
                    delete widgetWizardSelectedRows['row' + $(this).parents('tr').attr('data-rowid')];
                    
                    widgetWizardSelectedRowsTable.row('[data-rowid=' + $(this).parents('tr').attr('data-rowid') + ']').remove().draw(false);
                    $('#widgetWizardTable2 tbody tr[data-rowid=' + $(this).parents('tr').attr('data-rowid') + ']').removeClass('selected');
                    
                    checkAtLeastOneRowSelected();
                    checkBrokerAndNrRowsTogether();
                    checkTab1Conditions();
                    countSelectedRows();
                    updateWidgetCompatibleRows();
                    
                    updateSelectedUnits('remove', delesectedUnit);
                    
                    updateIconsFromSelectedRows();
                });
            },
            "columnDefs": [
                {
                    "targets": 11,
                    "searchable": false,
                    "render": function (data, type, row, meta) {
                        return '<i class="fa fa-close widgetWizardSelectedRowsDelBtn"></i>';
                    }
                }
            ]
        });

        //Creazione tabella GUI del wizard
        widgetWizardTable = $('#widgetWizardTable2').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "language": {search: ""},
            aaSorting: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "pageLength": widgetWizardPageLength,
            "ajax": {
                async: true, 
                url: "../controllers/dashboardWizardController.php?initWidgetWizard=true",
                data: {
                    dashUsername: "<?= $_SESSION['loggedUsername'] ?>",
                    dashUserRole: "<?= $_SESSION['loggedRole'] ?>"
                }
            },
            'createdRow': function (row, data, dataIndex) {
                $(row).attr('data-rowId', data[12]);
                $(row).attr('data-high_level_type', data[0]);
                $(row).attr('data-nature', data[1]);
                $(row).attr('data-sub_nature', data[2]);
                $(row).attr('data-low_level_type', data[3]);
                $(row).attr('data-unique_name_id', data[4]);
                $(row).attr('data-instance_uri', data[5]);
                $(row).attr('data-unit', data[6]);
                $(row).attr('data-servicetype', data[2]);
                $(row).attr('data-get_instances', data[14]);
                $(row).attr('data-sm_based', data[16]);
                $(row).attr('data-parameters', data[11]);
                $(row).attr('data-selected', 'false');
                $(row).attr('data-last_value', data[8]);
            },
            "columnDefs": [
                {
                    "targets": [5, 11, 12, 14],
                    "visible": false
                },
                {
                    "targets": 9,
                    "searchable": true,
                    "render": function (data, type, row, meta) {
                        var imageUrl = null;
                        if (row[9]) {
                            if (row[9] === 'true') {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#33cc33'></i>";
                            } else {
                                imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                            }

                        } else {
                            imageUrl = "<i class='fa fa-circle' style='font-size:16px;color:#ff3300'></i>";
                        }
                        return imageUrl;
                    }
                },
                {
                    "targets": 10,
                    "searchable": true,
                    "visible": false
                },
            ],
            initComplete: function () {
                console.log("initcomplete");

                // HIGH-LEVEL TYPE COLUMN
                this.api().columns([0]).every(function () {
                    console.log("api");
                    var select = $('<select id="highLevelTypeSelect2" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#highLevelTypeFilterColumn"))
                            .on('change', function () 
                            {
                                console.log("filter");
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#highLevelTypeSelect2 option:selected'), function () {
                                    search.push($(this).val());
                                });
                                var nOptions = 0;
                                $.each($('#highLevelTypeSelect2 option'), function () {
                                    nOptions++;
                                });
                                
                                globalSqlFilter[0].allSelected = (search.length == nOptions && nOptions == highLevelTypeSelectStartOptions);
                                if(search.length == nOptions && nOptions == highLevelTypeSelectStartOptions)
                                    search = [];
                                
                                globalSqlFilter[0].selectedVals = search;

                                search = search.join('|');
                                globalSqlFilter[0].value = search;
                                if (search == '' && !globalSqlFilter[0].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(0).search(search, false, false).draw();
                                globalSqlFilter[0].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) 
                                {
                                    if (n !== 4 && n != 5) 
                                    {
                                        populateSelectMenus("high_level_type", search, select, "#highLevelTypeFilterColumn", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    highLevelTypeSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "high_level_type"
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    options = '<option value="' + data.table[x].high_level_type + '" selected="selected">' + data.table[x].high_level_type + '</option>';
                                    select.append(options);
                                    highLevelTypeSelectStartOptions++;
                                }

                                $('#highLevelTypeSelect2').multiselect2({
                                    includeSelectAllOption: true,
                                    maxHeight: 165,
                                    onChange: function () {
                                    }
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');

                            });
                });

                // NATURE COLUMN
                this.api().columns([1]).every(function () {       // CHANGE
                    var column = this;
                    var select = $('<select id="natureSelect2" style="color: black;" multiple="multiple"></select>') 
                            .appendTo($("#natureFilterColumn"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#natureSelect2 option:selected'), function () {   // CHANGE
                                    search.push($(this).val());
                                });
                                var nOptions = 0;
                                $.each($('#natureSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[1].allSelected = (search.length == nOptions && nOptions == natureSelectStartOptions);
                                if (search.length == nOptions && nOptions == natureSelectStartOptions)
                                    search = [];
                                globalSqlFilter[1].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[1].value = search;
                                if (search == '' && !globalSqlFilter[1].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(1).search(search, false, false).draw(); 
                                globalSqlFilter[1].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("nature", search, select, "#natureFilterColumn", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    natureSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "nature"     // CHANGE
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    //   options += '<option value="' + data.table[x].nature + '">' + data.table[x].nature + '</option>';     // CHANGE

                                    options = '<option value="' + data.table[x].nature + '" selected="selected">' + data.table[x].nature + '</option>';
                                    //  $(option).appendTo(select);
                                    select.append(options);
                                    natureSelectStartOptions++;
                                }
                                $('#natureSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                            });
                    //   });

                });

                // SUBNATURE COLUMN
                this.api().columns([2]).every(function () {       // CHANGE

                    var column = this;
                    var select = $('<select id="subnatureSelect2" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#subnatureFilterColumn"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#subnatureSelect2 option:selected'), function () {   // CHANGE
                                    search.push($(this).val());

                                });
                                var nOptions = 0;
                                $.each($('#subnatureSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[2].allSelected = (search.length == nOptions && nOptions == subNatureSelectStartOptions);
                                if (search.length == nOptions && nOptions == subNatureSelectStartOptions)
                                    search = [];
                                globalSqlFilter[2].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[2].value = search;
                                if (search == '' && !globalSqlFilter[2].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                if (search.charAt(0) == '|') {
                                    search = search.substring(1);
                                }
                                widgetWizardTable.column(2).search(search, false, false).draw();     // CHANGE
                                globalSqlFilter[2].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("sub_nature", search, select, "#subnatureFilterColumn", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    subNatureSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "sub_nature"     // CHANGE
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    //   options += '<option value="' + data.table[x].nature + '">' + data.table[x].nature + '</option>';     // CHANGE

                                    options = '<option value="' + data.table[x].sub_nature + '" selected="selected">' + data.table[x].sub_nature + '</option>';         // CHANGE
                                    //  $(option).appendTo(select);
                                    select.append(options);
                                    subNatureSelectStartOptions++;
                                }
                                $('#subnatureSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                                ;
                            });
                    //   });

                });

                // LOW-LEVEL TYPE COLUMN
                this.api().columns([3]).every(function () {       // CHANGE

                    var column = this;
                    var select = $('<select id="lowLevelTypeSelect2" style="color: black;" multiple="multiple"></select>')    // CHANGE
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#lowLevelTypeFilterColumn"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#lowLevelTypeSelect2 option:selected'), function () {   // CHANGE
                                    search.push($(this).val());

                                });
                                var nOptions = 0;
                                $.each($('#lowLevelTypeSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[3].allSelected = (search.length == nOptions && nOptions == lowLevelTypeSelectStartOptions);
                                if (search.length == nOptions && nOptions == lowLevelTypeSelectStartOptions)
                                    search = [];
                                globalSqlFilter[3].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[3].value = search;
                                if (search == '' && !globalSqlFilter[3].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(3).search(search, false, false).draw();     // CHANGE
                                globalSqlFilter[3].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("low_level_type", search, select, "#lowLevelTypeFilterColumn", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    lowLevelTypeSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "low_level_type"     // CHANGE
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) 
                                {
                                    options = '<option value="' + data.table[x].low_level_type + '" selected="selected">' + data.table[x].low_level_type + '</option>';         // CHANGE
                                    /*if((data.table[x].low_level_type !== 'actuatorcanceller')&&(data.table[x].low_level_type !== 'actuatordeleted')&&(data.table[x].low_level_type !== 'actuatordeletiondate')&&(data.table[x].low_level_type !== 'creationdate')&&(data.table[x].low_level_type !== 'entitycreator')&&(data.table[x].low_level_type !== 'entitydesc'))
                                    {
                                        select.append(options);
                                        lowLevelTypeSelectStartOptions++;
                                    }*/
                                    
                                    select.append(options);
                                    lowLevelTypeSelectStartOptions++;
                                }
                                $('#lowLevelTypeSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                            });

                });

                // UNIT <-> DATA TYPE COLUMN
                this.api().columns([6]).every(function () {       // UNIT - DATA_TYPE

                    var column = this;
                    var select = $('<select id="unitSelect2" style="color: black;" multiple="multiple"></select>')
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#unitFilterColumn"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#unitSelect2 option:selected'), function () {
                                    search.push($(this).val());

                                });
                                var nOptions = 0;
                                $.each($('#unitSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[6].allSelected = (search.length == nOptions && nOptions == unitSelectStartOptions);
                                if (search.length == nOptions && nOptions == unitSelectStartOptions)
                                    search = [];
                                globalSqlFilter[6].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[6].value = search;
                                if (search == '' && !globalSqlFilter[6].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(6).search(search, false, false).draw();
                                globalSqlFilter[6].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("unit", search, select, "#unitFilterColumn", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();
                            });

                    unitSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "unit",
                                ajax: 'true'
                            },
                            function (data) {
                                var options = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    options = '<option value="' + data.table[x].unit + '" selected="selected">' + data.table[x].unit + '</option>';         // CHANGE
                                    select.append(options);
                                    unitSelectStartOptions++;
                                }
                                unitSelect = $('#unitSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                            });

                });

                // HEALTHINESS COLUMN
                this.api().columns([9]).every(function () {       // HEALTHINESS

                    var column = this;
                    var select = $('<select id="healthinessSelect2" style="color: black;" multiple="multiple"></select>')
                            //   .appendTo( $(column.footer()).empty() )
                            .appendTo($("#healthinessColumnFilter2"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#healthinessSelect2 option:selected'), function () {
                                    search.push($(this).val());

                                });
                                var nOptions = 0;
                                $.each($('#healthinessSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[7].allSelected = (search.length == nOptions && nOptions == healthinessSelectStartOptions);
                                if (search.length == nOptions && nOptions == healthinessSelectStartOptions)
                                    search = [];
                                globalSqlFilter[7].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[7].value = search;
                                if (search == '' && !globalSqlFilter[7].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(9).search(search, false, false).draw();
                                globalSqlFilter[7].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("healthiness", search, select, "#healthinessColumnFilter2", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    healthinessSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "healthiness",
                                ajax: 'true'
                            },
                            function (data) {
                                var options = '';
                                var attrib = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    if (data.table[x].healthiness === 'true') {
                                        attrib = 'healthy';
                                    } else {
                                        attrib = 'unhealthy';
                                    }
                                    options = '<option value="' + data.table[x].healthiness + '" selected="selected">' + data.table[x].healthiness + '</option>';         // CHANGE
                                    //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                    select.append(options);
                                    healthinessSelectStartOptions++;
                                }
                                unitSelect = $('#healthinessSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                    // enableFiltering: true
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                            });

                });

                // OWNERSHIP COLUMN
                this.api().columns([15]).every(function () 
                {     
                    var select = $('<select id="ownershipSelect2" style="color: black;" multiple="multiple"></select>')
                            .appendTo($("#ownershipColumnFilter2"))
                            .on('change', function () {
                                /*widgetWizardSelectedRows = {};
                                widgetWizardSelectedRowsTable.clear().draw(false);
                                validityConditions.atLeastOneRowSelected = false;
                                checkTab1Conditions();
                                countSelectedRows();*/

                                var search = [];
                                $.each($('#ownershipSelect2 option:selected'), function () {
                                    search.push($(this).val());

                                });
                                var nOptions = 0;
                                $.each($('#ownershipSelect2 option'), function () {
                                    nOptions++;
                                });

                                globalSqlFilter[8].allSelected = (search.length == nOptions && nOptions == ownershipSelectStartOptions);
                                if (search.length == nOptions && nOptions == ownershipSelectStartOptions)
                                    search = [];
                                globalSqlFilter[8].selectedVals = search;
                                search = search.join('|');

                                globalSqlFilter[8].value = search;
                                if (search == '' && !globalSqlFilter[8].allSelected) {
                                    search = 'oiunqauhalknsufhvnoqwpnvfv';
                                }
                                widgetWizardTable.column(15).search(search, false, false).draw();
                                globalSqlFilter[8].value = search;

                                // Chiamata a funzione per popolare menù multi-select di filtraggio
                                for (var n = 0; n < 9; n++) {
                                    if (n !== 4 && n != 5) {
                                        populateSelectMenus("ownership", search, select, "#ownershipColumnFilter2", n, false, true);
                                    }
                                }
                                
                                checkTab1Conditions();
                                countSelectedRows();

                            });

                    ownershipSelectStartOptions = 0;
                    $.getJSON('../controllers/dashboardWizardController.php?filterDistinct=true',
                            {
                                filter: "ownership",
                                ajax: 'true'
                            },
                            function (data) {
                                var options = '';
                                var attrib = '';
                                for (var x = 0; x < data.table.length; x++) {
                                    if (data.table[x].ownership === 'true') {
                                        attrib = 'healthy';
                                    } else {
                                        attrib = 'unhealthy';
                                    }
                                    options = '<option value="' + data.table[x].ownership + '" selected="selected">' + data.table[x].ownership + '</option>';         // CHANGE
                                    //    options = '<option value="' + attrib + '" selected="selected">' + attrib + '</option>';         // CHANGE
                                    select.append(options);
                                    ownershipSelectStartOptions++;
                                }
                                unitSelect = $('#ownershipSelect2').multiselect2({
                                    maxHeight: 165,
                                    includeSelectAllOption: true,
                                    // enableFiltering: true
                                }).multiselect2('selectAll', true).multiselect2('updateButtonText');
                            });

                });
            }
        });
        
        //Settaggio righe selezionate e incompatibili quando si cambia pagina della tabella selected rows
        $('#widgetWizardSelectedRowsTable2').on('draw.dt', function () {
            if(Object.keys(widgetWizardSelectedRows).length > 0)
            {
                $('#widgetWizardSelectedRowsTable2 tbody tr').each(function (i) {
                    var rowId = 'row' + $(this).attr('data-rowid');
                    if(widgetWizardSelectedRows[rowId].widgetCompatible)
                    {
                        if($(this).hasClass('odd'))
                        {
                            $(this).css("background-color", "#f9f9f9");
                        }
                        else
                        {
                            $(this).css("background-color", "#ffffff");
                        }

                        $(this).attr("data-widgetCompatible", "true");
                    }
                    else
                    {
                        $(this).css("background-color", "#ffb3b3");
                        $(this).attr("data-widgetCompatible", "false");
                    }
                });
            }
        });
        
        //Settaggio righe selezionate quando si cambia pagina
        $('#widgetWizardTable2').on('draw.dt', function () {
            $('#widgetWizardTable2 tbody tr').each(function (i) {
                var rowId = 'row' + $(this).attr('data-rowid');
                if (widgetWizardSelectedRows.hasOwnProperty(rowId))
                {
                    $(this).addClass('selected');
                    $(this).attr("data-selected", "true");
                }
            });
        });

        // GESTORE CLICK SU TABELLA PER SELEZIONARE LA RIGA. 
        $('#widgetWizardTable2 tbody').on('click', 'tr', function ()
        {
            //Evidenza grafica di riga selezionata
            if($(this).hasClass('selected'))
            {
                $(this).removeClass('selected');
                var delesectedUnit = widgetWizardSelectedRows['row' + $(this).attr('data-rowid')].unit;
                delete widgetWizardSelectedRows['row' + $(this).attr('data-rowid')];
                
                widgetWizardSelectedRowsTable.row('[data-rowid=' + $(this).attr('data-rowid') + ']').remove().draw(false);
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('remove', delesectedUnit);
            } 
            else
            {
                $(this).addClass('selected');
                widgetWizardSelectedRows['row' + $(this).attr('data-rowid')] = 
                {
                    high_level_type: $(this).attr('data-high_level_type'),
                    nature: $(this).attr('data-nature'),
                    sub_nature: $(this).attr('data-sub_nature'), //Questa è da mandare a ServiceMap
                    low_level_type: $(this).attr('data-low_level_type'), //Ora si chiama Value type
                    unique_name_id: $(this).attr('data-unique_name_id'), //Ora si chiama Value name
                    instance_uri: $(this).attr('data-instance_uri'),
                    unit: $(this).attr('data-unit'),
                    servicetype: $(this).attr('data-servicetype'),//Doppione?
                    sm_based: $(this).attr('data-sm_based'),
                    parameters: $(this).attr('data-parameters'),
                    widgetCompatible: true,
                    get_instances: $(this).attr('data-get_instances'),
                    last_value: $(this).attr('data-last_value')
                };
                
                widgetWizardSelectedRowsTable.row.add([
                    $(this).find('td').eq(0).html(),
                    $(this).find('td').eq(1).html(),
                    $(this).find('td').eq(2).html(),
                    $(this).find('td').eq(3).html(),
                    $(this).find('td').eq(4).html(),
                    $(this).find('td').eq(5).html(),
                    $(this).find('td').eq(6).html(),
                    $(this).find('td').eq(7).html(),
                    $(this).find('td').eq(8).html(),
                    $(this).find('td').eq(9).html(),
                    $(this).find('td').eq(10).html(),
                    $(this).attr('data-rowid'),
                    true
                ]).draw(false);
                
                //Aggiornamento unità selezionate
                updateSelectedUnits('add', null);
            }
            
            countSelectedRows();
            checkBrokerAndNrRowsTogether();
            checkAtLeastOneRowSelected();
            checkTab1Conditions();
            
            //Aggiunta/rimozione pins su mappa
            var bounds = addWidgetWizardMapRef.getBounds();
            var serviceType = $(this).attr("data-servicetype");
            var uniqueNameId = $(this).attr("data-unique_name_id");
            var instanceUri = $(this).attr("data-instance_uri");
            var getInstances = $(this).attr("data-get_instances");
            var northEastPointLat = bounds._northEast.lat;
            var northEastPointLng = bounds._northEast.lng;
            var southWestPointLat = bounds._southWest.lat;
            var southWestPointLng = bounds._southWest.lng;

            var showFlag = false;

            // CAMBIA COLORE (TOGGLE PER SELEZIONE/DESELEZIONE) on Click
            if($(this).attr("data-selected") === "false")
            {
                showFlag = true;
            } 
            else
            {
                showFlag = false;
            }

            if(showFlag == true)
            {
                if(instanceUri === "any + status") {
                    $.ajax({
                        url: "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=http://www.disit.org/km4city/resource/" + uniqueNameId + "&format=json&realtime=false",
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        success: function (geoData)
                        {
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                            } else
                            {
                                if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "any") {
                    $.ajax({
                        url: "https://servicemap.disit.org/WebAppGrafo/api/v1/?selection=" + southWestPointLat + ";" + southWestPointLng + ";" + northEastPointLat + ";" + northEastPointLng + "&categories=" + serviceType + "&format=json",
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        success: function (geoData)
                        {
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStops"))
                            {
                                fatherNode = geoData.BusStops;
                            } else
                            {
                                if (geoData.hasOwnProperty("SensorSites"))
                                {
                                    fatherNode = geoData.SensorSites;
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Services;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                } else if (instanceUri === "single_marker") {
                    $.ajax({
                        url: "https://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=" + getInstances + "&format=json&realtime=false",
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        data: {},
                        success: function (geoData)
                        {
                            var fatherNode = null;
                            if (geoData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoData.BusStop;
                            } else
                            {
                                if (geoData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoData.Sensor;
                                } else
                                {
                                    //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                    fatherNode = geoData.Service;
                                }
                            }

                            gisLayersOnMap[serviceType] = L.geoJSON(fatherNode, {
                                pointToLayer: addWidgetWizardCreateCustomMarker
                            }).addTo(addWidgetWizardMapRef);

                        },
                        error: function (data)
                        {
                            console.log("ERROR in retrieving GeoData by Km4City SmartCity API: " + JSON.stringify(data));
                        }
                    });
                    $(this).attr('data-selected', 'true');
                }
            } else
            {
                var stopFlag = 1;
                $(this).attr('data-selected', 'false');
                try
                {
                    gisLayersOnMap[serviceType].clearLayers();
                }
                catch(e)
                {
                    console.log("Colta eccezione mappa: " + e.message);
                }
            }

            updateIconsFromSelectedRows();
            updateWidgetCompatibleRows();
        });

        //Flusso main ************************************************************************

        //Associazione del click del bottone di reset filtro alla funzione corrispondente
        $("#resetButton2").click(resetFilter);

        //Creazione mappa e riarrangiamento a bruta forza delle opzioni di tabella in testa alla stessa nel div #widgetWizardTableCommandsContainer2
        setTimeout(function () {
            var fatherGeoJsonNode = null;
            var addWidgetWizardMapDiv = "addWidgetWizardMapCnt2";
            /*

            $("#link_start_wizard").click(function ()
            {*/
                choosenWidgetIconName = null;
                widgetWizardSelectedRows = {};
                widgetWizardSelectedRowsTable.clear().draw(false);
            /*});*/
            
            $("#link_start_wizard2").click(function ()
            {
                choosenWidgetIconName = null;
                widgetWizardSelectedRows = {};
                widgetWizardSelectedRowsTable.clear().draw(false);
            });

            /*$("#addWidgetWizard2").on('shown.bs.modal', function () {*/
                if($('#dataAndWidgets2').is(':visible'))
                {
                    try
                    {
                        addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(43.769710, 11.255751), 11);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                            maxZoom: 18,
                            closePopupOnClick: false
                        }).addTo(addWidgetWizardMapRef);
                        addWidgetWizardMapRef.attributionControl.setPrefix('');
                    } catch (e)
                    {
                        console.log("Mappa già istanziata");
                    }
                }
                
                /*$('.nav-tabs a[href="#dataAndWidgets2"]').on('shown.bs.tab', function () 
                {*/
                    selectedTabIndex = 1;
                    if(location.href.includes("dashboard_configdash.php")||location.href.includes("prova2.php")||location.href.includes("dashboards.php")||location.href.includes("iframeApp.php"))
                    {
                        $('#addWidgetWizardPrevBtn2').addClass('disabled');
                    }
                    else
                    {
                        $('#addWidgetWizardPrevBtn2').removeClass('disabled');
                    }
                    $('#addWidgetWizardNextBtn2').removeClass('disabled');
                    
                    //Gestione pulsanti prev e next
                    $('#addWidgetWizardPrevBtn2').off('click');
                    $('#addWidgetWizardPrevBtn2').click(function()
                    {
                        if(selectedTabIndex > firstTabIndex)
                        {
                            $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                        }
                    });

                    $('#addWidgetWizardNextBtn2').off('click');
                    $('#addWidgetWizardNextBtn2').click(function()
                    {
                        if(selectedTabIndex < parseInt(tabsQt - 1))
                        {
                            $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        }
                    });
                    
                    checkTab1Conditions();
                    
                    try
                    {
                        addWidgetWizardMapRef = L.map(addWidgetWizardMapDiv).setView(L.latLng(43.769710, 11.255751), 11);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                            maxZoom: 18,
                            closePopupOnClick: false
                        }).addTo(addWidgetWizardMapRef);
                        addWidgetWizardMapRef.attributionControl.setPrefix('');
                    } catch (e)
                    {
                        console.log("Mappa già istanziata");
                    }
                /*});*/
            /*});*/

            //Riarrangiamento a bruta forza delle opzioni di tabella in testa alla stessa nel div #widgetWizardTableCommandsContainer2
            $("#widgetWizardTable2_paginate").appendTo("#widgetWizardTableCommandsContainer2");
            $("#widgetWizardTable2_paginate").addClass("col-xs-12");
            $("#widgetWizardTable2_paginate").addClass("col-md-4");
            $('#widgetWizardTable2_filter').appendTo("#widgetWizardTableCommandsContainer2");
            $("#widgetWizardTable2_filter").addClass("col-xs-12");
            $("#widgetWizardTable2_filter").addClass("col-md-3");
            $("#widgetWizardTable2_filter input").attr("placeholder", "Search");
            $("#widgetWizardTable2_paginate .pagination").css("margin-top", "0px !important");
            $("#widgetWizardTable2_paginate .pagination").css("margin-bottom", "0px !important");

            $("#widgetWizardSelectedRowsTable2_paginate").appendTo("#widgetWizardSelectedRowsTableCommandsContainer2");
            $("#widgetWizardSelectedRowsTable2_paginate").addClass("col-xs-12");
            $("#widgetWizardSelectedRowsTable2_paginate").addClass("col-md-4");
            $("#widgetWizardSelectedRowsTable2_paginate").addClass("col-md-offset-5");
            $('#widgetWizardSelectedRowsTable2_filter').appendTo("#widgetWizardSelectedRowsTableCommandsContainer2");
            $("#widgetWizardSelectedRowsTable2_filter").addClass("col-xs-12");
            $("#widgetWizardSelectedRowsTable2_filter").addClass("col-md-3");
            $("#widgetWizardSelectedRowsTable2_filter input").attr("placeholder", "Search");
            $("#widgetWizardSelectedRowsTable2_paginate .pagination").css("margin-top", "0px !important");
            $("#widgetWizardSelectedRowsTable2_paginate .pagination").css("margin-bottom", "0px !important");
        }, 750);
        
        //Distinzione fra caso inclusione in dashboard_configdash.php e inclusione in dashboards.php
        //Caso dashboard_configdash.php
        if(location.href.includes("dashboard_configdash")||location.href.includes("prova2")||location.href.includes("dashboards")||location.href.includes("iframeApp"))
        {
            console.log("Creazione widgets");
            $('#addWidgetWizardConfirmBtn2').click(function ()
            {
                //Mandiamo solo le selected rows compatibili
                var widgetWizardSelectedRowsCompatible = {};
                
                for(var key in widgetWizardSelectedRows)
                {
                    if(widgetWizardSelectedRows[key].widgetCompatible)
                    {
                        widgetWizardSelectedRowsCompatible[key] = widgetWizardSelectedRows[key];
                    }
                }
                
                $('#modalAddWidgetWizardAvailabilityMsg').hide();
                widgetWizardMapSelection = addWidgetWizardMapRef.getBounds().getSouthWest().lat + ";" + addWidgetWizardMapRef.getBounds().getSouthWest().lng + ";" + addWidgetWizardMapRef.getBounds().getNorthEast().lat + ";" + addWidgetWizardMapRef.getBounds().getNorthEast().lng;

                $.ajax({
                    url: "../controllers/widgetAndDashboardInstantiator.php",
                    data: {
                        operation: "addWidget",
                        dashboardId: "<?php if (isset($_REQUEST['dashboardId'])) {echo $_REQUEST['dashboardId'];} else {echo 1;} ?>",
                        dashboardAuthorName: "<?php if (isset($_REQUEST['dashboardAuthorName'])){echo $_REQUEST['dashboardAuthorName'];} else {echo 1;} ?>",
                        dashboardEditorName: "<?php if (isset($_REQUEST['dashboardEditorName'])){echo $_REQUEST['dashboardEditorName'];}else{echo 1;} ?>",
                        dashboardTitle: '<?php if (isset($_REQUEST['dashboardTitle'])){echo $_REQUEST['dashboardTitle'];}else{echo 1;} ?>',
                        widgetType: choosenWidgetIconName,
                        actuatorTargetWizard: $('#actuatorTargetWizard2').val(),
                        actuatorTargetInstance: $('#actuatorTargetInstance2').val(),
                        actuatorEntityName: $('#actuatorEntityName2').val(),
                        actuatorValueType: $('#actuatorValueType2').val(),
                        actuatorMinBaseValue: $('#actuatorMinBaseValue2').val(),
                        actuatorMaxImpulseValue: $('#actuatorMaxImpulseValue2').val(),
                        widgetWizardSelectedRows: widgetWizardSelectedRowsCompatible,
                        selection: widgetWizardMapSelection,
                        mapCenterLat: addWidgetWizardMapRef.getCenter().lat,
                        mapCenterLng: addWidgetWizardMapRef.getCenter().lng,
                        mapZoom: addWidgetWizardMapRef.getZoom()
                    },
                    type: "POST",
                    async: true,
                    //dataType: 'json',
                    success: function (data)
                    {
                        if(data === 'Ok')
                        {
                            location.reload();
                        } else
                        {
                            alert("Error during dashboard update, please try again");
                            console.log(data);
                        }
                    },
                    error: function (errorData)
                    {
                        alert("Error during dashboard update, please try again");
                        console.log(errorData);
                    }
                });
            });
        }
        else//Caso dashboards.php
        {/*
            console.log("Creazione dashboard");
            
            $('.modalAddDashboardWizardChoiceCnt').click(function(i)
            {
                //In ogni caso nascondiamo campi per attuatori new e mostriamo tabelle
                $('#actuatorTargetCell2 .wizardActLbl').hide();
                $('#actuatorTargetCell2 .wizardActInputCnt').hide();
                $('#actuatorEntityNameCell2 .wizardActLbl').hide();
                $('#actuatorEntityNameCell2 .wizardActInputCnt').hide();
                $('#actuatorValueTypeCell2 .wizardActLbl').hide();
                $('#actuatorValueTypeCell2 .wizardActInputCnt').hide();
                $('#actuatorMinBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMinBaseValueCell2 .wizardActInputCnt').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActLbl').hide();
                $('#actuatorMaxBaseValueCell2 .wizardActInputCnt').hide();
                $('#actuatorTargetWizard2').val(-1);
                $('#actuatorEntityName2').val('');
                $('#actuatorValueType2').val('');
                $('#actuatorMinBaseValue2').val('');
                $('#actuatorMaxImpulseValue2').val('');
                $('#widgetWizardActuatorFieldsRow2').hide();
                $('.hideIfActuatorNew').show();
                
                //In ogni caso leviamo il bordino di widget selezionato a quello che eventualmente ce l'ha e deselezioniamola
                $('.addWidgetWizardIconClickClass2[data-selected=true]').css('border', 'none');
                $('.addWidgetWizardIconClickClass2[data-selected=true]').attr('data-selected', 'false');
                
                $('#wizardNotCompatibleRowsAlert').hide();
                
                if($(this).attr('data-selected') === 'false')
                {
                    if($(this).attr('data-widgettype') !== 'any')
                    {
                        $('.addWidgetWizardIconsCnt2').hide();
                        $('.dashTemplateHide').hide();
                    }
                    else
                    {
                        $('.addWidgetWizardIconsCnt2').show();
                        $('.dashTemplateHide').show();
                    }
                    
                    $('.modalAddDashboardWizardChoiceCnt').attr('data-selected', 'false');
                    $('.modalAddDashboardWizardChoiceCnt').removeClass('modalAddDashboardWizardChoiceCntSelected');
                    $(this).attr('data-selected', 'true');
                    $(this).addClass('modalAddDashboardWizardChoiceCntSelected');
                    choosenDashboardTemplateName = $(this).attr('data-templatename');
                    choosenDashboardTemplateIcon = $(this).attr('data-widgettype');
                    $('#dashboardTemplateStatus2').val('ok');
                    
                    if(($('#dashboardTemplateStatus2').val() === 'ok')&&($('#inputTitleDashboardStatus2').val() === 'ok'))
                    {
                        $('#bTab2 a').attr("data-toggle", "tab");
                        $('#addWidgetWizardNextBtn2').removeClass('disabled');
                    }
                    else
                    {
                        $('#bTab2 a').attr("data-toggle", "no");
                        if ($('#modalAddDashboardWizardTemplateMsg2')[0].outerText != "Template choosen OK" || $('#modalAddDashboardWizardTitleAlreadyUsedMsg2')[0].outerText != "Dashboard title OK") {
                            $('#addWidgetWizardNextBtn2').addClass('disabled');
                        }
                    }
                    
                    $('#modalAddDashboardWizardTemplateMsg2').css("color", "white");
                    $('#modalAddDashboardWizardTemplateMsg2 div.col-xs-12').html("Template choosen OK");
                    
                    //Qui dentro c'è la logica che preseleziona high_level_type, nature... in base al template di dashboard desiderato
                    resetFilter();
                    
                    //Selezione del tipo di widget
                    if($(this).attr("data-widgetType") !== 'any')
                    {
                        //Selezioniamo direttamente noi il tipo di widget, col click programmatico non ce la fa col tempo
                        choosenWidgetIconName = $(this).attr("data-widgetType");
                        $('.addWidgetWizardIconClickClass2[data-iconname="' + $(this).attr("data-widgetType") + '"]').attr('data-selected', true);
                        validityConditions.widgetTypeSelected = true;
                    }
                    
                    validityConditions.dashTemplateSelected = true;
                    
                    

                    if($(this).attr("data-highLevelTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(0).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(0).visible(false);
                    }
                    
                    if($(this).attr("data-natureVisible") === 'true')
                    {
                        widgetWizardTable.column(1).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(1).visible(false);
                    }
                    
                    if($(this).attr("data-subnatureVisible") === 'true')
                    {
                        widgetWizardTable.column(2).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(2).visible(false);
                    }
                    
                    if($(this).attr("data-valueTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(3).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(3).visible(false);
                    }
                    
                    if($(this).attr("data-valueNameVisible") === 'true')
                    {
                        widgetWizardTable.column(4).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(4).visible(false);
                    }
                    
                    if($(this).attr("data-dataTypeVisible") === 'true')
                    {
                        widgetWizardTable.column(6).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(6).visible(false);
                    }
                    
                    if($(this).attr("data-lastDateVisible") === 'true')
                    {
                        widgetWizardTable.column(7).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(7).visible(false);
                    }
                    
                    if($(this).attr("data-lastValueVisible") === 'true')
                    {
                        widgetWizardTable.column(8).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(8).visible(false);
                    }
                    
                    if($(this).attr("data-healthinessVisible") === 'true')
                    {
                        widgetWizardTable.column(9).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(9).visible(false);
                    }
                    
                    if($(this).attr("data-lastCheckVisible") === 'true')
                    {
                        widgetWizardTable.column(13).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(13).visible(false);
                    }
                    
                    if($(this).attr("data-ownershipVisible") === 'true')
                    {
                        widgetWizardTable.column(15).visible(true);
                    }
                    else
                    {
                        widgetWizardTable.column(15).visible(false);
                    }
                    
                    if(($(this).attr('data-highlevelsel').split('|').length > 1) || ($(this).attr('data-highlevelsel') === 'any'))
                    {
                        $('#highLevelTypeFilterColumn').show();
                    }
                    else
                    {
                        $('#highLevelTypeFilterColumn').hide();
                    }
                    
                }
                else
                {
                    $('.modalAddDashboardWizardChoiceCnt').attr('data-selected', 'false');
                    $('.modalAddDashboardWizardChoiceCnt').removeClass('modalAddDashboardWizardChoiceCntSelected');
                    $(this).attr('data-selected', 'false');
                    $(this).removeClass('modalAddDashboardWizardChoiceCntSelected');
                    choosenDashboardTemplateName = null;
                    choosenDashboardTemplateIcon = null;
                    choosenWidgetIconName = null;
                    $('.addWidgetWizardIconClickClass2').attr('data-selected', false);
                    $('.addWidgetWizardIconsCnt2').show();
                    $('.dashTemplateHide').show();
                    $('#dashboardTemplateStatus2').val('empty');
                    
                    if(($('#dashboardTemplateStatus2').val() === 'ok')&&($('#inputTitleDashboardStatus2').val() === 'ok'))
                    {
                        $('#bTab2 a').attr("data-toggle", "tab");
                        $('#addWidgetWizardNextBtn2').removeClass('disabled');
                    }
                    else
                    {
                        $('#bTab2 a').attr("data-toggle", "no");
                        $('#addWidgetWizardNextBtn2').addClass('disabled');
                    }
                    
                    $('#modalAddDashboardWizardTemplateMsg2').css("color", "rgb(243, 207, 88)");
                    $('#modalAddDashboardWizardTemplateMsg2 div.col-xs-12').html("You must choose one template");
                    
                    resetFilterForced();
                    validityConditions.dashTemplateSelected = false;
                    validityConditions.widgetTypeSelected = false;
                    checkActuatorFieldsEmpty();
                    checkAtLeastOneRowSelected();
                    checkBrokerAndNrRowsTogether();
                    switch($('#inputTitleDashboardStatus2').val())
                    {
                        case 'empty':
                            $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title can\'t be empty</span></div></div>');
                            validityConditions.canProceed = false;
                            break;

                        case 'alreadyUsed':
                            $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title already in use</span></div></div>');
                            validityConditions.canProceed = false;
                            break;
                            
                        case 'tooLong':
                            $('#wrongConditionsDiv2').append('<div class="col-xs-12 titleAlert"><div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle validityConditionIcon"></i></div><div class="col-xs-12 centerWithFlex"><span class="validityConditionLbl">Dashboard title longer than 300 chars</span></div></div>');
                            validityConditions.canProceed = false;
                            break;    

                        default:
                            $('.titleAlert').remove();
                            break;
                    }
                    
                    widgetWizardTable.column(0).visible(true);
                    widgetWizardTable.column(1).visible(true);
                    widgetWizardTable.column(2).visible(true);
                    widgetWizardTable.column(3).visible(true);
                    widgetWizardTable.column(4).visible(true);
                    widgetWizardTable.column(6).visible(true);
                    widgetWizardTable.column(7).visible(true);
                    widgetWizardTable.column(8).visible(true);
                    widgetWizardTable.column(9).visible(true);
                    widgetWizardTable.column(13).visible(true);
                    widgetWizardTable.column(15).visible(true);
                }
            });
            
            $('#addWidgetWizardConfirmBtn2').click(function ()
            {
                var myMapCenterLat, myMapCenterLng, myMapZoom = null;
                
                //Mandiamo solo le selected rows compatibili
                var widgetWizardSelectedRowsCompatible = {};
                
                for(var key in widgetWizardSelectedRows)
                {
                    if(widgetWizardSelectedRows[key].widgetCompatible)
                    {
                        widgetWizardSelectedRowsCompatible[key] = widgetWizardSelectedRows[key];
                    }
                }
                
                if((choosenDashboardTemplateName !== 'fullyCustom')||((choosenDashboardTemplateName === 'fullyCustom')&&(validityConditions.widgetTypeSelected)&&(validityConditions.atLeastOneRowSelected)))
                {
                    myMapCenterLat = addWidgetWizardMapRef.getCenter().lat;
                    myMapCenterLng = addWidgetWizardMapRef.getCenter().lng;
                    myMapZoom = addWidgetWizardMapRef.getZoom();
                }
                
                $('#modalAddWidgetWizardAvailabilityMsg').hide();

                $.ajax({
                    url: "../controllers/widgetAndDashboardInstantiator.php",
                    data: {
                        operation: "addDashboard",
                        dashboardTemplate: choosenDashboardTemplateName,
                        dashboardTitle: $('#inputTitleDashboard2').val(),
                        dashboardAuthorName: "<?php if (isset($_SESSION['loggedUsername'])){echo $_SESSION['loggedUsername'];} else {echo 1;} ?>",
                        dashboardEditorName: "<?php if (isset($_SESSION['loggedUsername'])){echo $_SESSION['loggedUsername'];}else{echo 1;} ?>",
                        widgetType: choosenWidgetIconName,
                        actuatorTargetWizard: $('#actuatorTargetWizard2').val(),
                        actuatorTargetInstance: $('#actuatorTargetInstance2').val(),
                        actuatorEntityName: $('#actuatorEntityName2').val(),
                        actuatorValueType: $('#actuatorValueType2').val(),
                        actuatorMinBaseValue: $('#actuatorMinBaseValue2').val(),
                        actuatorMaxImpulseValue: $('#actuatorMaxImpulseValue2').val(),
                        widgetWizardSelectedRows: widgetWizardSelectedRowsCompatible,
                        selection: widgetWizardMapSelection,
                        mapCenterLat: myMapCenterLat,
                        mapCenterLng: myMapCenterLng,
                        mapZoom: myMapZoom
                    },
                    type: "POST",
                    async: true,
                    dataType: 'json',
                    success: function (data)
                    {
                        if(data.detail === 'Ok')
                        {
                            if(data['detail'] === 'Ok')
                            {
                                location.href = "dashboards.php?linkId=dashboardsLink&newDashId=" + data['newDashId'] + "&newDashAuthor=" + "<?= $_SESSION['loggedUsername'] ?>" + "&newDashTitle=" + encodeURI($('#inputTitleDashboard2').val());
                            }
                            else
                            {
                                alert("Error during dashboard creation, please try again");
                            }
                        } 
                        else
                        {
                            alert("Error during dashboard creation, please try again");
                        }
                    },
                    error: function(errorData)
                    {
                        console.log("Error: " + errorData.callResult);
                        alert("Error during dashboard creation, please try again");
                    }
                });
            });
        */}
        
        //Gestione pulsanti prev e next
        $('#addWidgetWizardPrevBtn2').addClass('disabled');
        $('#addWidgetWizardNextBtn2').addClass('disabled');
        
        $('#addWidgetWizardPrevBtn2').off('click');
        $('#addWidgetWizardPrevBtn2').click(function()
        {
            if(selectedTabIndex > firstTabIndex)
            {
                $('.nav-tabs > .active').prev('li').find('a').trigger('click');
            }
        });

        $('#addWidgetWizardNextBtn2').off('click');
        $('#addWidgetWizardNextBtn2').click(function()
        {
            if(selectedTabIndex < parseInt(tabsQt - 1))
            {
                switch(selectedTabIndex)
                {
                    case 0:
                        if(($('#dashboardTemplateStatus2').val() === 'ok')&&($('#inputTitleDashboardStatus2').val() === 'ok'))
                        {
                            $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        }
                        break;
                        
                    case 1:
                        $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        break;

                    case 2:
                        $('.nav-tabs > .active').next('li').find('a').trigger('click');
                        break;
                        
                }
            }
        });
        /*
        $('#addWidgetWizard').on('hidden.bs.modal', function () 
        {
            if(location.href.includes("dashboard_configdash")||location.href.includes("prova2")||location.href.includes("dashboards")||location.href.includes("iframeApp"))
            {
                //Ritorno al primo tab
                $('#bTab2 a').trigger('click');
                
                //Deselect del widget selezionato (sennò con attuatori resetFilter e basta non sembra funzionare)
                $('.addWidgetWizardIconClickClass2[data-selected="true"]').trigger('click');
                
                //Reset tab widgets
                resetFilter();
            }
            else
            {
                //Ritorno al primo tab
                $('#aTab2 a').trigger('click');

                //Reset tab general properties dashboard (la cascata di eventi resetta il tab centrale)
                $('#inputTitleDashboard2').val('');
                $('#inputTitleDashboard2').trigger('input');
                $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').trigger('click');
                
                widgetWizardTable.search('').draw();
                widgetWizardSelectedRowsTable.search('').draw();
            }
            
            //Reset campi custom attuatori
            $('#actuatorEntityName2').val('');
            $('#actuatorValueType2').val('');
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        });
        */
        /*$('#addWidgetWizard2').on('hidden.bs.modal', function () 
        {*/
            if(location.href.includes("dashboard_configdash")||location.href.includes("prova2")||location.href.includes("dashboards")||location.href.includes("iframeApp"))
            {
                //Ritorno al primo tab
                $('#bTab2 a').trigger('click');
                
                //Deselect del widget selezionato (sennò con attuatori resetFilter e basta non sembra funzionare)
                $('.addWidgetWizardIconClickClass2[data-selected="true"]').trigger('click');
                
                //Reset tab widgets
                resetFilter();
            }
            else
            {
                //Ritorno al primo tab
                $('#aTab2 a').trigger('click');

                //Reset tab general properties dashboard (la cascata di eventi resetta il tab centrale)
                $('#inputTitleDashboard2').val('');
                $('#inputTitleDashboard2').trigger('input');
                $('.modalAddDashboardWizardChoiceCnt[data-selected="true"]').trigger('click');
                
                widgetWizardTable.search('').draw();
                widgetWizardSelectedRowsTable.search('').draw();
            }
            
            //Reset campi custom attuatori
            $('#actuatorEntityName2').val('');
            $('#actuatorValueType2').val('');
            
            //Rimozione avviso righe incompatibili
            $('#wizardNotCompatibleRowsAlert').hide();
        /*});*/
        
    });
</script>   
-->