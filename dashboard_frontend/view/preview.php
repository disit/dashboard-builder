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

include "../config.php";
require "../sso/autoload.php";

use Jumbojett\OpenIDConnectClient;

session_start();
header("Access-Control-Allow-Origin: *");
error_reporting(E_ERROR);

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

            $coordinates = '0,0';
            $org = 'Organization';
            if (isset($_REQUEST['organization'])){
                $org = $_REQUEST['organization']; 
            }
            if (($org != '')&&($org != null)){        
                $query = 'SELECT gpsCentreLatLng FROM Organizations WHERE organizationName = "'.$org.'";';
                $result = mysqli_query($link, $query);
               if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                        if($row['gpsCentreLatLng']){
                             $coordinates = $row['gpsCentreLatLng'];
                        }
                     }
                }
               
            }
?>
<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    
    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>

    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=1687510654" rel="stylesheet">
    <link href="../css/widgetHeader.css?v=1687510654" rel="stylesheet">
    <link href="../css/dashboardView.css?v=1687510654" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link href="../css/widgetCtxMenu.css?v=1687510654" rel="stylesheet">
    <link rel="stylesheet" href="../css/style_widgets.css?v=1687510654" type="text/css" />
    <link href="../css/widgetDimControls.css?v=1687510654" rel="stylesheet">
    <link href="../css/chat.css?v=1687510654" rel="stylesheet">
    <link href="../css/dashboard_configdash.css?v=1687510654" rel="stylesheet">
    
    <!-- Material icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <script src="../js/jquery.gridsterMod.js" type="text/javascript" charset="utf-8"></script>
    <!--<link rel="stylesheet" type="text/css" href="../newGridster/dist/jquery.gridster.css">
    <script src="../newGridster/dist/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>-->

    <script src="../js/highcharts-9/code/highcharts.js"></script>
    <script src="../js/highcharts-9/code/modules/exporting.js"></script>
    <script src="../js/highcharts-9/code/highcharts-more.js"></script>
    <script src="../js/highcharts-9/code/modules/parallel-coordinates.js"></script>
    <script src="../js/highcharts-9/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts-9/code/highcharts-3d.js"></script>
    <script src="../js/highcharts-9/code/modules/streamgraph.js"></script>
    
    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Leaflet -->
    <!-- Versione locale: 1.3.1 --> 
    <link rel="stylesheet" href="../leafletCore/leaflet.css" />
    <script src="../leafletCore/leaflet.js"></script>
    <script src="../js/OMS-leaflet/oms.min.js"></script>    <!-- OverlappingMarkerSpider for Leaflet -->
   
   <!-- Leaflet marker cluster plugin -->
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.css" />
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.Default.css" />
   <script src="../leaflet-markercluster/leaflet.markercluster-src.js" type="text/javascript" charset="utf-8"></script>
   
   <!-- Leaflet Wicket: libreria per parsare i file WKT --> 
   <script src="../wicket/wicket.js"></script> 
   <script src="../wicket/wicket-leaflet.js"></script>

    <!-- Leaflet Zoom Display -->
    <script src="../js/leaflet.zoomdisplay-src.js"></script>
    <link href="../css/leaflet.zoomdisplay.css" rel="stylesheet"/>
   
   <!-- Dot dot dot -->
   <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>
   
    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet"/>
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>
    
    <!-- Moment -->
    <script type="text/javascript" src="../moment/moment.js"></script>
    
    <!-- html2canvas -->
    <script type="text/javascript" src="../js/html2canvas.js"></script>
    
    <!-- Bootstrap datetimepicker -->
    <script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="../datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    
    <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=1687510654">
    
    <!-- Text fill -->
    <script src="../js/jquery.textfill.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <script src="../js/widgetsCommonFunctions.js?v=1687510654" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=1687510654" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=1687510654" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=1687510654" type="text/javascript" charset="utf-8"></script>
    
    <!--OpenLayers -->
    <script src="ol/ol.js"></script>
    <link rel="stylesheet" href="ol/ol.css" />
    
<!-- Cristiano : Dynamic Routing -->
    <!--- Leaflet.drawer plugin -->
    <script src="../js/dynamic_routing/leaflet.draw.js"></script>
    <script src="../js/dynamic_routing/Leaflet.draw.drag-src.js"></script>
    <link rel="stylesheet" href="../css/dynamic_routing/leaflet.draw.css"/>
    <!-- Leaflet Control Geocoder -->
    <link rel="stylesheet" href="../css/dynamic_routing/Control.Geocoder.css" />
    <script src="../js/dynamic_routing/Control.Geocoder.js"></script>
    <!-- GH Leaflet Routing Machine plugin -->
    <link rel="stylesheet" href="../css/dynamic_routing/leaflet-routing-machine.css" />
  <!--  <script src="../js/dynamic_routing/leaflet-routing-machine.js"></script>    -->
    <script src="../js/dynamic_routing/corslite.min.js"></script>
<!-- End Cristiano -->

	<!-- MS> WidgetSelectorTech is based on Fancytree -->	
	<link href="../js/skin-win8/ui.fancytree.css" rel="stylesheet">
	<script src="../js/fancytree/jquery.fancytree.ui-deps.js"></script>
	<script src="../js/fancytree/jquery.fancytree.js"></script>
	<!-- <MS -->

    <!-- Hijrah Date -->
    <script type="text/javascript" src="../js/hijrah-date.js"></script>
  <!--  <script src="https://rawgithub.com/miladjafary/highcharts-plugins/master/js/jalali.js"></script>    -->
    <script src="../js/highcharts-localization.js"></script>

    <!-- New WS -->
   <script src="https://www.snap4city.org/synoptics/socket.io/socket.io.js"></script>

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
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.scrollResize.min.js"></script>
<style>
     body {
        margin: 0;
        padding: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
     }

    .mapContainer{
        position: absolute;
        top: 1%;
        left: 1%;
        width: 90%;
        height: 90%;
        }

        .map2dContainer{
            margin: 0;
            padding: 0;
            width: 95%;
            height: 95%;
        }
</style>
    <script type='text/javascript'>
        var array_metrics = new Array();
        var headerFontSize, headerModFontSize, subtitleFontSize, subtitleModFontSize, dashboardId, dashboardName, dashboardOrg, dashboardOrgKbUrl, logoFilename, logoLink,
            clockFontSizeMod, logoWidth, logoHeight, headerVisible = null;
    
        var dashboardZoomEventHandler = function(event)
        {
            document.body.style.zoom = event.data;
        };

        window.addEventListener('message', dashboardZoomEventHandler, false);    
        
        $(document).ready(function () 
        {
            var embedWidget, embedWidgetPolicy, headerVisible, wrapperWidth, dashBckImg, useBckImg, dashboardViewMode, gridster, gridsterCellW, gridsterCellH, widgetsContainerWidth, num_cols = null;
            var firstLoad = true;
            var loggedUserFirstAttempt = true;
            var myGpsActive, myGpsPeriod, myGpsInterval, globalDashboardTitle = null, backOverlayOpacity = null;
            var embedPreview = "false";
            var loggedUsername = "userrootadmin";
            var scaleFactorFlag, extra_rows_gridster, max_rows_gridster = null;
            var scaleFactorW = 1;
            var scaleFactorH = 1;
            var newScaledGridsterCellW = 26;
            var newScaledGridsterCellH = 13;
            var widgetMargins = [1, 1];
            var infoMsgPopupFlag = false;
            var infoMsgText = null;

            $('#open_BIMenu').hide();
            $('#orgMenu').hide();
            $('#orgMenuCnt a.mainMenuLink').attr('data-submenuVisible', 'false');
            $('#orgMenuCnt a.orgMenuSubItemLink').hide();
            $('.linkgen').css('text-decoration', 'none');
            $('#dashboardViewHeaderContainer').remove();
                $('#preview_widgetCtxMenuBtnCnt').remove();
                $('#preview_dimControls').remove();

            
            $('#chatBtn').click(function(){
                if($(this).attr("data-status") === 'closed')
                {
                    $(this).attr("data-status", 'open');
                    $('#chatContainer').show();
                    console.log("Show");
                }
                else
                {
                    $(this).attr("data-status", 'closed');
                    $('#chatContainer').hide();
                    console.log("Hide");
                }
            });
            
            $('#chatBtnNew').click(function(){
                if($(this).attr("data-status") === 'closed')
                {
                    $(this).attr("data-status", 'open');
                    $('#chatContainer').show();
                    console.log("Show");
                }
                else
                {
                    $(this).attr("data-status", 'closed');
                    $('#chatContainer').hide();
                    console.log("Hide");
                }
            });
            //READ PARAMETERS
            
            
                                
            // Fullscreen: passargli sempre il documentElement 
            $('#fullscreenButton').click(function(){
                if(document.documentElement.requestFullscreen) 
                {
                    document.documentElement.requestFullscreen();
                } 
                else if(document.documentElement.mozRequestFullScreen) 
                {
                    document.documentElement.mozRequestFullScreen();
                }
                else if(document.documentElement.webkitRequestFullScreen) 
                {
                    document.documentElement.webkitRequestFullScreen();
                } 
                else if(document.documentElement.msRequestFullscreen) 
                {
                    document.documentElement.msRequestFullscreen();
                }
                $('#fullscreenButton').hide();
                $('#restorescreenButton').show();
            });
            
            $('#restorescreenButton').click(function(){
                if(document.exitFullscreen) 
                {
                    document.exitFullscreen();
                } 
                else if(document.webkitExitFullscreen) 
                {
                    document.webkitExitFullscreen();
                } 
                else if(document.mozCancelFullScreen) 
                {
                    document.mozCancelFullScreen();
                } 
                else if(document.msExitFullscreen) 
                {
                    document.msExitFullscreen();
                }
                $('#restorescreenButton').hide();
                $('#fullscreenButton').show();
            });

            $('.mainMenuLink').click(function(event){
         //  $('.linkgen').click(function(event){
                event.preventDefault();
                var pageTitle = $(this).attr('data-pageTitle');
                var linkId = $(this).attr('id');
                var linkUrl = $(this).attr('data-linkUrl');

                if($(this).attr('data-linkUrl') === 'submenu')
                {
                    $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                        $(this).removeClass('orgMenuItemCntActive');
                    });
                    $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                    if($(this).attr('data-submenuVisible') === 'false')
                    {
                        $(this).attr('data-submenuVisible', 'true');
                     //   $('.orgMenuSubItemCnt').css( "display", "block" );
                        $('.orgMenuSubItemCnt[data-fatherMenuIdDiv='+ $(this).attr('id') + ']').css( "display", "block" );
                    //    $(this).find('.orgMenuSubItemCnt').css( "display", "block" ); // data-fatherMenuIdDiv
                        $('.orgMenuSubItemCnt a.orgMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').show();
                        $(this).find('.submenuIndicator').removeClass('fa-caret-down');
                        $(this).find('.submenuIndicator').addClass('fa-caret-up');
                    }
                    else
                    {
                        $(this).attr('data-submenuVisible', 'false');
                        $('.orgMenuSubItemCnt a.orgMenuSubItemLink[data-fatherMenuId=' + $(this).attr('id') + ']').hide();
                    //    $('.orgMenuSubItemCnt').css( "display", "none" );
                        $('.orgMenuSubItemCnt[data-fatherMenuIdDiv='+ $(this).attr('id') + ']').css( "display", "none" );
                     //   $(this).find('.orgMenuSubItemCnt').css( "display", "none" );
                        $(this).find('.submenuIndicator').removeClass('fa-caret-up');
                        $(this).find('.submenuIndicator').addClass('fa-caret-down');
                    }
                }
                else
                {
                    $('#orgMenuCnt a.orgMenuSubItemLink').hide();

                    $('#orgMenuCnt a.orgMenuSubItemLink').each(function(i){
                        $(this).attr('data-submenuVisible', 'false');
                    });
                    switch($(this).attr('data-openMode'))
                    {
                        case "iframe":
                            $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                                $(this).removeClass('orgMenuItemCntActive');
                            });
                            $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                            if($(this).attr('data-externalApp') === 'yes')
                            {
                                location.href = "iframeApp.php?linkUrl=" + encodeURIComponent(linkUrl);
                            }
                            break;

                        case "newTab":
                            var newTab = window.open($(this).attr('data-linkurl'));
                            if(newTab)
                            {
                                newTab.focus();
                            }
                            else
                            {
                                alert('Please allow popups for this website');
                            }
                            break;

                        case "samePage":
                            $('#orgMenuCnt .orgMenuItemCnt').each(function(i){
                                $(this).removeClass('orgMenuItemCntActive');
                            });
                            $(this).find('div.orgMenuItemCnt').addClass("orgMenuItemCntActive");
                            location.href = $(this).attr('data-linkurl');
                            break;
                    }
                }

                var mainMenuScrollableCntHeight = parseInt($('#orgMenuCnt').outerHeight() - $('#headerClaimCnt').outerHeight() - $('#orgMenuCnt .mainMenuUsrCnt').outerHeight() - 30);
                $('#mainMenuScrollableCnt').css("height", parseInt(mainMenuScrollableCntHeight + 0) + "px");
                $('#mainMenuScrollableCnt').css("overflow-y", "auto");
            });

            $(window).resize(function(){
                $('#clock').textfill({
                    maxFontPixels: 24
                });
                
                $('#fullscreenBtnContainer').textfill({
                    maxFontPixels: 32
                });
                
                $('#dashboardTitle').textfill({
                    maxFontPixels: -20
                });
                
                $('#dashboardSubtitle').textfill({
                    maxFontPixels: -20
                });
                
        
                switch(dashboardViewMode)
                {
                    case "fixed":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = 1872;
                        }

                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else {
                                gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                                gridsterCellH = gridsterCellW/2;
                                //    widgetsContainerWidth = 1872;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;    
                        
                    case "alwaysResponsive":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW/2;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                            gridsterCellH = gridsterCellW/2;
                            //    widgetsContainerWidth = 1872;
                            widgetsContainerWidth = num_cols * (gridsterCellW);
                        }
                        break;
                        
                    default:
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = 1872;
                        }
                        break;    
                }
                
                $('#dashboardViewWidgetsContainer').css('width', widgetsContainerWidth + "px");
                $('div.footerLogos').css('margin-right', ($('body').width() - widgetsContainerWidth)/2);

                gridster.resize_widget_dimensions({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                //    widget_margins: [1, 1]
                    widget_margins: widgetMargins
                });
                                
                $('li.gs_w').trigger({
                    type: "resizeWidgets"
                }); 
            });
            
            //Definizioni di funzione

            function loadDashboard(dashboardParams)
            {
                var minEmbedDim, autofitAlertFontSize;            
                globalDashboardTitle = 'Preview Map';
                dashBckImg = null;
                useBckImg = "no";
                backOverlayOpacity = "0";
                infoMsgPopupFlag = null;
                infoMsgText = null;
              
///////////////////////////
dashboardWidgets = [
   {
      "Id":"0",
      //"name_w":"w_Map_1545_widgetMap10072",
      "name_w":"preview",
      "id_dashboard": "0",
      "id_type_widget":"widgetMap",
      "time":0,
      "embedWidget":false,
      "embedWidgetPolicy":"manual",
      "hostFile":"index"
   }
];
///////
                if (infoMsgPopupFlag == "yes" && infoMsgText != null && infoMsgText != '') {
                    $("#msgInfoModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                //    $('#msgInfoModal').css("vertical-align", "middle");
                 //   $('#msgInfoModal .modalHeader').html(infoMsgText);
                    $('#msgInfoModalBodyView').css('text-align','center');
                    $('#msgInfoModalBodyView').html(infoMsgText);
                  //  $('#msgInfoModalText').val(infoMsgText);

                    $('#msgInfoModal .modal-dialog').css("vertical-align", "middle")

                    $('#msgInfoModal').modal('show');
                }
                
                if((dashBckImg !== null)&&(useBckImg === 'yes'))
                {
                    //$('#dashBckCnt').css('background-image', 'url("../img/dashBackgrounds/dashboard' + "1545" + '/' + dashBckImg + '")');
                    $('#dashBckOverlay').show();
                    $('#dashBckOverlay').css('background-color', 'rgba(0,0,0,' + backOverlayOpacity + ')');
                } 
                else
                {
                    $('#dashBckOverlay').hide();
                }

                if( navigator.userAgent.match(/Android/i)
                    || navigator.userAgent.match(/webOS/i)
                    || navigator.userAgent.match(/iPhone/i)
                    || navigator.userAgent.match(/iPad/i)
                    || navigator.userAgent.match(/iPod/i)
                    || navigator.userAgent.match(/BlackBerry/i)
                    || navigator.userAgent.match(/Windows Phone/i)) {

                    $('#dashBckCnt').css('width', '100%');
                    $('#dashBckCnt').css('height', '100%');

                }

                if('yes' === 'yes')
                {
                    if(window.self !== window.top)
                    {
                        if(('manual' === 'auto')||(('manual' !== 'auto')&&('no' === 'yes')))
                        {
                            $('#autofitAlert').css("width", $(window).width());
                            $('#autofitAlert').css("height", $(window).height());
                            $('#autofitAlertMsgContainer').css("height", $(window).height()*0.45);
                            $('#autofitAlertIconContainer').css("height", $(window).height()*0.55);
                            
                            if($(window).height() < $(window).width())
                            {
                                minEmbedDim = $(window).height();
                            }
                            else
                            {
                                minEmbedDim = $(window).width();
                            }
                            
                            if((minEmbedDim > 0) && (minEmbedDim < 300))
                            {
                                autofitAlertFontSize = 16;
                            }
                            else
                            {
                                if((minEmbedDim >= 300) && (minEmbedDim < 600))
                                {
                                    autofitAlertFontSize = 24;
                                }
                                else
                                {
                                    if((minEmbedDim >= 600) && (minEmbedDim < 900))
                                    {
                                        autofitAlertFontSize = 32;
                                    }
                                    else
                                    {
                                        autofitAlertFontSize = 36;
                                    }
                                }
                            }
                            
                            $('#autofitAlertMsgContainer').css("font-size", autofitAlertFontSize + "px");
                            $('#autofitAlertIconContainer i.fa-spin').css("font-size", autofitAlertFontSize*2 + "px");
                            
                            $('#autofitAlert').show();
                        }
                    }
                }
                
                $('body').removeClass("dashboardViewBodyAuth");
                
                //dashboardId = 1547;
                dashboardId = '';
                dashboardName = 'Map Preview';
                dashboardOrg = 'Organization';
                logoFilename = null;
                logoLink = null;
                headerVisible = "0";
                dashboardViewMode = "fixed";
                $("#headerLogoImg").css("display", "none");
               // $("#dashboardViewHeaderContainer").css("background-color", dashboardParams.color_header);

                $.ajax({
                    url: "../controllers/getOrganizationParameters.php",
                    data: {
                        action: "getSpecificOrgParameters",
                        param: dashboardOrg
                    },
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                        dashboardOrgKbUrl = data.orgKbUrl;
                    },
                    error: function (errorData) {
                        console.log("Errore in reperimento parametri Org specifica: ");
                        console.log(JSON.stringify(errorData));
                    }
                });

                //Sfondo
                $("body").css("background-color", "#FFFFFF");
                $("#dashboardViewWidgetsContainer").css("background-color", "#FFFFFF");
                var headerFontColor = "black";
                var headerFontSize = "28";
                
                $("#dashboardTitle").css("color", headerFontColor);
                //$('#chatBtn').css("color", $('#dashboardTitle').css('color'));
                $("#dashboardTitle span").text('Preview Map');
                $("#clock").css("color", headerFontColor);
                $('#fullscreenBtnContainer').css("color", headerFontColor);
                
                $('#clock').textfill({
                    maxFontPixels: -20
                });
                
                $('#fullscreenBtnContainer').textfill({
                    maxFontPixels: 32
                });

                var whiteSpaceRegex = '^[ t]+';
                    $("#dashboardTitle").css("height", "100%");
                    $("#dashboardSubtitle").css("display", "none");

                
                $('#dashboardTitle').textfill({
                    maxFontPixels: -20
                });
                
                $('#dashboardSubtitle').textfill({
                    maxFontPixels: -20
                });

                if(logoFilename !== null)
                {
                    $("#headerLogoImg").prop("src", "../img/dashLogos/dashboard" + dashboardId + "/" + logoFilename);
                    $("#headerLogoImg").prop("alt", "Dashboard logo");
                    $("#headerLogoImg").show();
                    if((logoLink !== null) && (logoLink !== ''))
                    {
                       var logoImage = $('#headerLogoImg');
                       var logoLinkElement = $('<a href="' + logoLink + '" target="_blank" class="pippo">'); 
                       logoImage.wrap(logoLinkElement); 
                    }
                }

                num_cols = "72";
                switch(dashboardViewMode)
                {
                    case "fixed":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = 1872;
                        }

                        break;
                        
                    case "smallResponsive":
                        if($(window).width() > 768)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "mediumResponsive":
                        if($(window).width() > 992)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;
                        
                    case "largeResponsive":
                        if($(window).width() > 1200)
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = 76;
                                gridsterCellH = 38;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                // MOD GRID
                                gridsterCellW = newScaledGridsterCellW;
                                gridsterCellH = newScaledGridsterCellH;
                                widgetsContainerWidth = 1872;
                            }
                        }
                        else
                        {
                            if (scaleFactorFlag == null) {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                            } else if (scaleFactorFlag == 'yes') {
                                gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols);
                                gridsterCellH = gridsterCellW / 2;
                                widgetsContainerWidth = num_cols * (gridsterCellW);
                            }
                        }
                        break;     
                        
                    case "alwaysResponsive":
                        if (scaleFactorFlag == null) {
                            gridsterCellW = Math.floor(parseInt($('body').width() * 0.98) / num_cols) - 2;
                            gridsterCellH = gridsterCellW / 2;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else {
                            gridsterCellW = Math.floor(parseInt($('body').width()*0.98) / num_cols);
                            gridsterCellH = gridsterCellW/2;
                        //    widgetsContainerWidth = 1872;
                            widgetsContainerWidth = num_cols * (gridsterCellW);
                        }
                        break;
                        
                    default:
                        if (scaleFactorFlag == null) {
                            gridsterCellW = 76;
                            gridsterCellH = 38;
                            widgetsContainerWidth = num_cols * (gridsterCellW + 2);
                        } else if (scaleFactorFlag == 'yes') {
                            // MOD GRID
                            gridsterCellW = newScaledGridsterCellW;
                            gridsterCellH = newScaledGridsterCellH;
                            widgetsContainerWidth = 1872;
                        }
                        break;    
                }
                
                $('#dashboardViewWidgetsContainer').css('width', widgetsContainerWidth + "px");
                $('div.footerLogos').css('margin-right', ($('body').width() - widgetsContainerWidth)/2);
                
               if(window.self === window.top)
               {
                    //Controllo mostrare/nascondere header su view principale
                    if(headerVisible === '1')
                    {
                      // $("#dashboardViewHeaderContainer").show();
                       $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                    }
                    else
                    {
                       $("#dashboardViewHeaderContainer").hide();
                       $('#dashboardViewWidgetsContainer').css('margin-top', "0px");
                    } 
               }
               else
               {
                    //Controllo mostrare/nascondere header in modalità embedded
                    if('manual' === 'auto')
                    {
                        $("#dashboardViewHeaderContainer").hide();
                        $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                    }
                    else
                    {
                        if('yes' === 'no')
                        {
                            $("#dashboardViewHeaderContainer").hide();
                            $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                        }
                        else
                        {
                           // $("#dashboardViewHeaderContainer").show();
                            $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                        }

                        if('yes' === 'no')
                        {
                            $(".footerNavRow").hide();
                            $("#horizontalFooterLine").hide();
                         //   $("#dashboardViewHeaderContainer").css("margin-bottom", "0px");
                        }
                        else
                        {
                            $(".footerNavRow").show();
                            $("#horizontalFooterLine").show();
                         //   $('#dashboardViewWidgetsContainer').css('margin-top', ($('#dashboardViewHeaderContainer').height() + 15) + "px");
                        }
                    }
                    $("#logos a.footerLogo").hide();
                    $("#logos #embedAutoLogoContainer").show();
               }

                if (scaleFactorFlag == null) {
                    widgetMargins = [1, 1];
                    extra_rows_gridster = 100;
                    max_rows_gridster = 100;
                } else if (scaleFactorFlag == 'yes') {
                    widgetMargins = [0, 0];
                    extra_rows_gridster = 100 * scaleFactorH;
                    max_rows_gridster = 100 * scaleFactorH;
                }

                gridster = $("#gridsterUl").gridster({
                    widget_base_dimensions: [gridsterCellW, gridsterCellH],
                 //   widget_margins: [1, 1],
                    widget_margins: widgetMargins,
                    min_cols: num_cols,
                    max_size_x: 200,
                //    max_rows: 100,
                    max_rows: max_rows_gridster,
                //    extra_rows: 100,
                    extra_rows: extra_rows_gridster,
                    draggable: {ignore_dragging: false},
                    serialize_params: function ($w, wgd){
                        return {
                            id: $w.attr('id'),
                            col: wgd.col,
                            row: wgd.row,
                            size_x: wgd.size_x,
                            size_y: wgd.size_y
                        };
                    }
                }).data('gridster').disable();//Fine creazione Gridster
                localStorage.clear();
                //console.log(dashboardWidgets.length);
                for(var i = 0; i < dashboardWidgets.length; i++)
                {
                    var widget = ['<li data-widgetType="widgetMap" data-widgetId="map" id="map"></li>', 55, 67, 1, 1];

                    gridster.add_widget.apply(gridster, widget);
                    
                    if(('yes' === 'yes')&&(window.self !== window.top))
                    {
                        embedWidget = true;
                    }
                    else
                    {
                        embedWidget = false;
                    }
                    embedWidgetPolicy = 'manual';
                    ////////
                    var coords = '<?= $coordinates ?>';
                        if (coords !==''){
                            var coordVar = coords.split(',');
                            latitudine = parseFloat(coordVar[0].replace(/\s/g, ''));
                            longitudine = parseFloat(coordVar[1].replace(/\s/g, ''));
                         } else{
                            latitudine = "43.7492731811147";
                            longitudine = "11.211547851562502";
                         }
                     ///
                    dashboardWidgets[i].time = 0;
                    dashboardWidgets[i].embedWidget = embedWidget;
                    dashboardWidgets[i].embedWidgetPolicy = embedWidgetPolicy;
                    dashboardWidgets[i].hostFile = 'config';
                    dashboardWidgets[i].latLng = '['+latitudine+','+longitudine+']';
                    //
                    $("li#map").css('border', '1px solid ' + dashboardWidgets[i].borderColor);

                   //$("#gridsterUl").find("li#" + dashboardWidgets[i]['name_w']).load("../widgets/" + encodeURIComponent(dashboardWidgets[i]['type_w']) + ".php", dashboardWidgets[i]);
                   $("#gridsterUl").find("li#map").load("../widgets/widgetMap.php", dashboardWidgets[i]);
                   //var content_grid = $("#gridsterUl").html('<li data-widgettype="widgetMap" data-widgetid="map" id="map" data-col="1" data-row="1" data-sizex="55" data-sizey="67" class="gs_w" style="display: list-item; opacity: 0; border: 1px solid rgb(255, 255, 255);"></li>');
                   //console.log(content_grid);

                }//Fine del secondo for
                
                
                if(window.self !== window.top)
                {
                    if('manual' === 'auto')
                    {
                        //Cambia logo se embedded in sito diverso dal dashboard manager
                        if(!document.referrer.includes(window.self.location.host)||((embedPreview === 'true')&&(document.referrer.includes(window.self.location.host))))
                        {
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("width", $('#wrapper-dashboard').css("width"));
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("background-color", $('#container-widgets').css("background-color"));
                            $('#page-wrapper #embedAutoLogoContainer').css("display", "flex");
                            $('#page-wrapper #embedAutoLogoContainer').css("align-items", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("justify-content", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("margin-left", "10px");
                        }
                        
                        $('#wrapper-dashboard').css("width", $('#wrapper-dashboard').width() - 40);
                        $('#page-wrapper div.container-fluid').css('padding-left', '0px');
                        $('#page-wrapper div.container-fluid').css('padding-right', '0px');
                        
                        var widthRatio, heightRatio, iframeW, iframeH, iframeCase = null;
                        
                        //Il timeout serve per consentire a Gridster il caricamento degli widget, purtroppo Gridster non innesca eventi in tal senso
                        setTimeout(function(){
                            if($(window).width() < $('#wrapper-dashboard').width())
                            {
                                iframeW = '0';
                            }
                            else
                            {
                               iframeW = '1';
                            }

                            if($(window).height() < $('#wrapper-dashboard').height())
                            {
                                iframeH = '0';
                            }
                            else
                            {
                                iframeH = '1';
                            }

                            iframeCase = iframeW + iframeH;
                            
                           // console.log("iframeCase: " + iframeCase);
                            
                            switch(iframeCase)
                            {
                                case '00':
                                    widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');

                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;
                                    
                                case '01':
                                    widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 17) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');

                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;    

                                case '10':
                                    widthRatio = parseInt($(window).width() + 17) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() + 0) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');
                                    var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;
                                    
                                case '11':
                                    widthRatio = parseInt($(window).width() + 0) / $('#wrapper-dashboard').width();
                                    heightRatio = parseInt($(window).height() - 5) / $('#wrapper-dashboard').height();
                                    $('body').css('overflow', 'hidden');
                                    var gapX = parseInt(($(window).width() - $('#wrapper-dashboard').width())/2);
                                    $('#wrapper-dashboard').css("-ms-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-webkit-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("-moz-transform-origin", '0 0');
                                    $('#wrapper-dashboard').css("transform-origin", '0 0');
                                    $('#wrapper-dashboard').css('-ms-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-webkit-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('-moz-transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    $('#wrapper-dashboard').css('transform', 'translateX(-' + gapX + 'px) scale(' + widthRatio + ', ' + heightRatio + ')');
                                    break;    
                            }
                            $('#autofitAlert').hide();
                        }, 2500);
                    }
                    else
                    {
                        //Cambia logo se embedded in sito diverso dal dashboard manager
                        if(!document.referrer.includes(window.self.location.host)||((embedPreview === 'true')&&(document.referrer.includes(window.self.location.host))))
                        {
                            $('#page-wrapper #embedAutoLogoContainer').css("width", $('#container-widgets').css("width"));
                            $('#page-wrapper div.container-fluid div.footerLogos').hide();
                            $('#page-wrapper #embedAutoLogoContainer').css("background-color", $('#container-widgets').css("background-color"));
                            $('#page-wrapper #embedAutoLogoContainer').css("display", "flex");
                            $('#page-wrapper #embedAutoLogoContainer').css("align-items", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("justify-content", "flex-start");
                            $('#page-wrapper #embedAutoLogoContainer').css("margin-left", "10px");
                        }
                        
                       
                    }
                }
                else
                {
                    function sendCurrentPosition(position)
                    {
                        console.log("sendCurrentPosition OK");

                        $.ajax({
                            url: "../management/nrSendGpsProxy.php",
                            type: "POST",
                            data: {
                                httpRelativeUrl: encodeURI(globalDashboardTitle),
                                dashboardTitle: encodeURI(globalDashboardTitle),
                                gpsData: JSON.stringify({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    accuracy:  position.coords.accuracy,
                                    altitude:  position.coords.altitude,
                                    altitudeAccuracy:  position.coords.altitudeAccuracy,
                                    heading:  position.coords.heading,
                                    speed:  position.coords.speed
                                })
                            },
                            async: true,
                            dataType: 'json',
                            success: function(data)
                            {
                                console.log("Data sent to test GPS OK");
                                console.log(JSON.stringify(data));
                            },
                            error: function(errorData)
                            {
                                console.log("Data sent to test GPS KO");
                                console.log(JSON.stringify(errorData));
                            }
                        });
                    }
                    
                    function sendCurrentPositionError(obj)
                    {
                        console.log("Get current position KO: " + obj.message);
                    }
                    
                    myGpsActive = "no";myGpsPeriod = 30;                                                                                                                    
                    if(myGpsActive === 'yes')
                    {
                        myGpsInterval = setInterval(function(){
                            if(navigator.geolocation) 
                            {
                                navigator.geolocation.getCurrentPosition(sendCurrentPosition, sendCurrentPositionError);
                            } 
                            else 
                            { 
                                //console.log("Navigator not available");
                            }
                        }, parseInt(parseInt(myGpsPeriod)*1000))
                    }
                    else
                    {
                        //console.log("Navigator not active");
                    }
                }
                
            }
            
            function authUser()
            { 
                //////
                var dashboardParams = [];
                                $('body').removeClass("dashboardViewBodyAuth");
                                $('#authFormDarkBackground').hide();
                                $('#authFormContainer').hide();
                                $("#dashboardViewMainContainer").show();
                                loadDashboard(dashboardParams);

                ////////
            }
            //Fine definizioni di funzione
            
            //Main
            authUser();
            //myVar = setInterval("updateFunction()", 60*1000);    // Firing access count every 1 MINUTE

        });
        ////////////
        //window.onload = function() {
            window.addEventListener('load', function() {
            var urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('heatmap')) {
                    var heatmap_parameter = "<?= $_GET['heatmap']  ?>";
                    if (urlParams.has('layers')){
                        var layers = "<?= $_GET['layers'] ?>"; 
                        var organization = "Organization";
                        //
                        if (urlParams.has('organization')){
                               organization = "<?= $_GET['organization'] ?>"; 
                        }
                        //console.log('organization: '+organization);
                        //
                        if ((heatmap_parameter !== "")&&(layers !=="")){
                                            var passedParams = {
                                                    "desc": heatmap_parameter,
                                                    "color1": "#33cc33",
                                                    "color2": "#adebad"
                                                };
                                                var coordsAndType = "https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&layers="+layers;
                                                console.log(coordsAndType);
                                                setTimeout(function() {
                                                                try {
                                                                        $('body').trigger({
                                                                            type: "addHeatmap",
                                                                            target: 'preview',
                                                                            passedData: coordsAndType,
                                                                            passedParams: passedParams
                                                                        });
                                                                    } catch (error) {
                                                                        console.error(error);    
                                                                }
                                                                //
                                                                   //var mapContainers = document.getElementById('preview_map');
                                                                   var mapContainers = document.getElementsByClassName('mapContainer');
                                                                    var mapContainer = mapContainers[0];
                                                                    // Inizializzare la mappa
                                                                    var map = L.map(mapContainer);
                                                                    // Aggiungere un controllo per adattare la mappa all'ampiezza della finestra del browser
                                                                    function fitMapToWindow() {
                                                                    var mapWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                                                                    var mapHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                                                                    mapContainer.style.width = mapWidth + 'px';
                                                                    mapContainer.style.height = mapHeight + 'px';
                                                                    map.invalidateSize();
                                                                    // Inizializzare la mappa
                                                                    //console.log(mapContainers);
                                                                    }
                                                                    // Chiamare la funzione fitMapToWindow all'avvio e in caso di ridimensionamento della finestra
                                                                    fitMapToWindow();
                                                                    window.addEventListener('resize', fitMapToWindow);
                                                                    var zoomLevel = 10;
                                                                    var latitudine = 43.866244;
                                                                    var longitudine = 11.417668;
                                                                    ////////
                                                                    var coords = '<?= $coordinates ?>';
                                                                    if (coords !==''){
                                                                        var coordVar = coords.split(',');
                                                                        latitudine = parseFloat(coordVar[0].replace(/\s/g, ''));
                                                                        longitudine = parseFloat(coordVar[1].replace(/\s/g, ''));
                                                                    }  
                                                                    // 
                                                                    try {
                                                                        new Event('resize');
                                                                        const resizeEvent = document.createEvent('Event');
                                                                        resizeEvent.initEvent('resize', true, true);
                                                                        window.innerWidth = '100%';
                                                                        window.innerHeight = '100%';
                                                                        window.dispatchEvent(resizeEvent);
                                                                        map.setView([latitudine, longitudine], zoomLevel);                                          
                                                                    } catch (error) {
                                                                                console.error('error trigger:');
                                                                                console.error(error);    
                                                                }
                                                               
                                            }, 500);
                                    }
                                  }
                                    }else if(urlParams.has('OD')){
                                        if (urlParams.has('layers')){
                                            var OD_parameter = "<?= $_GET['OD']  ?>";
                                                    var layers = "<?= $_GET['layers'] ?>";                   
                                                    if ((OD_parameter !== "")&&(layers !=="")){
                                                            
                                                            var coordsAndType = "https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&layers="+layers;
                                                            //
                                                            var passedParams = {
                                                                                "desc": OD_parameter,
                                                                                "color1": "#33cc33",
                                                                                "color2": "#adebad"
                                                                            };
                                                            //
                                                            setTimeout(function() {
                                                            if (OD_parameter !== ""){
                                                                                $.event.trigger({
                                                                                type: "addOD",
                                                                                target: '',
                                                                                passedData: coordsAndType,
                                                                                passedParams: passedParams
                                                                            });
                                                                            //
                                                                    var mapContainers = document.getElementsByClassName('mapContainer');
                                                                    var mapContainer = mapContainers[0];
                                                                    // Inizializzare la mappa
                                                                    var map = L.map(mapContainer);
                                                                    // Aggiungere un controllo per adattare la mappa all'ampiezza della finestra del browser
                                                                    function fitMapToWindow() {
                                                                    var mapWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                                                                    var mapHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                                                                    mapContainer.style.width = mapWidth + 'px';
                                                                    mapContainer.style.height = mapHeight + 'px';
                                                                    map.invalidateSize();
                                                                    }
                                                                    // Chiamare la funzione fitMapToWindow all'avvio e in caso di ridimensionamento della finestra
                                                                    fitMapToWindow();
                                                                    window.addEventListener('resize', fitMapToWindow);
                                                                    var zoomLevel = 10;
                                                                    var latitudine = 0;
                                                                    var longitudine = 0;
                                                                    var coords = '<?= $coordinates ?>';
                                                                    if (coords !==''){
                                                                        var coordVar = coords.split(',');
                                                                        latitudine = parseFloat(coordVar[0].replace(/\s/g, ''));
                                                                        longitudine = parseFloat(coordVar[1].replace(/\s/g, ''));
                                                                    }  
                                                                    // 
                                                                    try {
                                                                        new Event('resize');
                                                                        const resizeEvent = document.createEvent('Event');
                                                                        resizeEvent.initEvent('resize', true, true);
                                                                        window.innerWidth = '100%';
                                                                        window.innerHeight = '100%';
                                                                        window.dispatchEvent(resizeEvent);
                                                                        map.setView([latitudine, longitudine], zoomLevel);                                          
                                                                    } catch (error) {
                                                                                console.error('error trigger:');
                                                                                console.error(error);    
                                                                }
                                                                    ////////
                                                                    // Impostare la vista della mappa con il valore di zoom
                                                                    //
                                                                //map.setView([latitudine, longitudine], zoomLevel);
                                                               // var mapLayers = map.getLayers();
                                                                //

                                                                
                                                            }
                                                            //
                                                        }, 200);
                                                        }
                                                    
                                                    }
                                    }else{

                                    }
                      });
    </script>
</head>
<body>
      <div id="sessionExpiringPopup">
    <div class="row">
        <div class="col-xs-12 centerWithFlex" id="sessionExpiringPopupIcon">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 centerWithFlex" id="sessionExpiringPopupMsg">
            Session will expire in
        </div>
    </div>  
    <div class="row">
        <div class="col-xs-12 centerWithFlex" id="sessionExpiringPopupTime">
        </div>
    </div>
</div>

    <div id="dashBckCnt">
       <div id="dashBckOverlay">
        
       </div>             
    </div>
    <div id="dashboardViewMainContainer" class="container-fluid">
        <nav id="dashboardViewHeaderContainer" class="navbar navbar-fixed-top" role="navigation">
            <div id="fullscreenBtnContainer" data-status="normal">
                <span id="spanCnt">
                    <i id="fullscreenButton" class="fa fa-window-maximize"></i>
                    <i id="restorescreenButton" class="fa fa-window-restore"></i>
                            <i id="orgMenuButton" class="fa fa-bars" style="display:none"></i><div id=orgMenuCnt"><div id="orgMenu" data-shown="false" class="applicationCtxMenu fullCtxMenu container-fluid dashboardCtxMenu">
                            <div class="row fullCtxMenuRow quitRow" data-selected="false">
                                <div class="col-md-12 orgMenuItemCnt">
                                    <i class="fa fa-mail-reply"></i>&nbsp;&nbsp;&nbsp;Quit&nbsp;&nbsp;&nbsp;
                                  <!--  <div class="col-xs-2 fullCtxMenuIcon centerWithFlex"><i class="fa fa-mail-reply"></i></div>
                                    <div class="col-xs-10 fullCtxMenuTxt">Quit</div>    -->
                                </div>
                            </div>
                        </div>
                    </div>  
                </span>
            </div>
            <div id="dashboardViewTitleAndSubtitleContainer">
                <div id="dashboardTitle">
                    <span contenteditable="false"></span>
                </div>
                <div id="dashboardSubtitle">
                    <span></span>
                </div>            
            </div>
            <div id="headerLogo">
                <img id="headerLogoImg"/>
               <i class="fa fa-comment-o" id="chatBtn" data-status="closed" style="display: none"></i> <!-- style="display: none !important" -->
               <i class="fa fa-comment" id="chatBtnNew" data-status="closed" style="display: none"></i>
                <!--<i class="fas fa-bullhorn" id="chatBtn" data-status="closed" style="display: none"></i>-->
                <!--<i class="fas fa-bullhorn" style="font-size:48px;display: none" id="chatBtnNew" data-status="closed"></i>-->
            </div>
            <div id="clock">
                <span id="tick2"></span>

<script>
    function updateTime() 
    {
        var now = new Date();
        var days = new Array();
        var months = new Array();
        
        days[0] = "Sun";
        days[1] = "Mon";
        days[2] = "Tue";
        days[3] = "Wed";
        days[4] = "Thu";
        days[5] = "Fri";
        days[6] = "Sat";
        
        months[0] = "Jan";
        months[1] = "Feb";
        months[2] = "Mar";
        months[3] = "Apr";
        months[4] = "May";
        months[5] = "Jun";
        months[6] = "Jul";
        months[7] = "Aug";
        months[8] = "Sep";
        months[9] = "Oct";
        months[10] = "Nov";
        months[11] = "Dec";
      
        if(!document.all && !document.getElementById)
        {
           return;
        }
            
        var timeContainer = document.getElementById ? document.getElementById("tick2") : document.all.tick2;
        
        var day = days[now.getDay()];
        var month = months[now.getMonth()];
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();
        
        if(hours <= 9)
        {
           hours = "0" + hours;
        }
            
        if(minutes <= 9)
        {
           minutes = "0" + minutes;
        }
            
        if(seconds <= 9)
        {
           seconds = "0" + seconds;
        }
            
        var ctime = day + " " + now.getDate() + " " + month + " " + hours + ":" + minutes + ":" + seconds;
        //timeContainer.innerHTML = ctime;
        $('#dashboardViewHeaderContainer').remove();
        $('#preview_widgetCtxMenuBtnCnt').remove();
        $('#preview_dimControls').remove();
        
        setTimeout("updateTime()", 100);
    }
    
    
    updateTime();
    
</script></span>
            </div>
        </nav>
        
        <div id="dashboardViewWidgetsContainer" class="gridster">
            <ul id="gridsterUl"></ul>            
        </div>

        <hr id="horizontalFooterLine" style="height:1px;width:75%;border:none;color:#333;background-color:#333;margin-bottom:-10px;" />
<div class="footerNavRow">
    <div id="firstColumnFooter" class="footerNavColumn">
        <!-- empty -->
    </div>
    <div id="footerPolicyId" class="footerNavColumn">
        <ul class="menu nav">
            <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/node/49" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Privacy Policy</a></li>
            <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/node/48" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Cookies Policy</a></li>
            <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/legal" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Terms and Conditions</a></li>
            <li class="footerNavMenu"><a href="https://www.snap4city.org/drupal/contact" target="_blank" style="font-size:13px;color:black;font-weight: bold;" title="">Contact us</a></li>
        </ul>
    </div>
    <div id="footerLogoId" class="footerNavColumn">
        <div style="width:68%;float:right;">
            <a title="Disit" href="https://www.snap4city.org" target="_new" class="footerLogo"><img src="https://dashboard.km4city.org/img/applicationLogos/disitLogoTransparent.png" alt="Mountains" style="width:100%"></a>
        </div>
    </div>
</div>
        <script type="text/javascript">
            // Get URL
            var url = window.location.href;
            // Get DIV
            var msg0 = document.getElementById('horizontalFooterLine');
            var msg1 = document.getElementById('firstColumnFooter');
            var msg2 = document.getElementById('footerPolicyId');
            var msg3 = document.getElementById('footerLogoId');
            // Check if URL contains the keyword
            if( url.search('embedPolicy=') > 0 ) {
                // Display the message
                msg0.style.display = "none";
           //     msg1.style.display = "none";
                msg2.style.display = "none";
            //    msg3.style.margin = "auto";
            //    msg3.style.width = "50%";

            }
        </script>

    </div>
    
    <div id="authFormDarkBackground">
        <div class="row">
            <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>
        </div>
        
        <div class="row">
            <div id="authFormContainer" class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                <div class="col-xs-12" id="loginFormTitle" style="margin-top: 15px">
                   Restricted access dashboard
                </div>
            </div>
        </div>
    </div> 
    
        </div> <!-- Fine modal dialog -->
    </div>
    
    <div id="autofitAlert">
        <div class="row">
            <div id="autofitAlertMsgContainer" class="col-xs-12">
               Auto refit in progress, please wait                    
            </div>                     
        </div>
        <div class="row">
            <div id="autofitAlertIconContainer" class="col-xs-12">
               <i class="fa fa-circle-o-notch fa-spin"></i>                    
            </div>                     
        </div>                        
    </div>
</body>
</html>
