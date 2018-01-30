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
   include '../config.php';
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
   
   //Va studiata una soluzione, per ora tolto error reporting
   error_reporting(0);
   
   $dashId = base64_decode($_REQUEST['iddasboard']);
   
   session_start();
   
    $link = mysqli_connect($host, $username, $password) or die();
    mysqli_select_db($link, $dbname);

    $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.Id = $dashId";
    $queryResult = mysqli_query($link, $query);
    
    if(isset($_REQUEST['embedPolicy']))
    {
        $embedPolicy = $_REQUEST['embedPolicy'];
    }
    else
    {
        $embedPolicy = 'manual';
    }
    
    if(isset($_REQUEST['autofit']))
    {
        $embedAutofit = $_REQUEST['autofit'];
    }
    else
    {
        $embedAutofit = 'no';
    }
    
    if(isset($_REQUEST['showHeader']))
    {
        $showHeaderEmbedded = $_REQUEST['showHeader'];
    }
    else
    {
        $showHeaderEmbedded = 'yes';
    }

    if($queryResult) 
    {
       if($queryResult->num_rows > 0) 
       {     
           while($row = mysqli_fetch_array($queryResult)) 
           {
              $embeddable = $row['embeddable'];
              $authorizedPages = $row['authorizedPagesJson'];
           }
       }
       else
       {
           $embeddable = 'no';
       }
    }
    else
    {
        $embeddable = 'no';
    }
    
   mysqli_close($link);
   
   if(isset($_SERVER['HTTP_REFERER']))
   {
       if((strpos($_SERVER['HTTP_REFERER'], "http://".$appHost) !== false)||(strpos($_SERVER['HTTP_REFERER'], "https://".$appHost) !== false))
       {
           //Caso embed in una dashboard e previewer: in questo caso dev'essere sempre possibile fare l'embed
           $embeddable = 'yes';
       }
       else
       {
            //Caso embed in pagina esterna
            if($embeddable == "no")
            {
                header('X-Frame-Options: DENY');
            }
            else
            {
                if(($authorizedPages != '')&&($authorizedPages != null)&&($authorizedPages != 'NULL'))
                {
                    $authorizedPages = json_decode($authorizedPages);
                    $isAuthorized = false;
                    for($i = 0; $i < count($authorizedPages); $i++)
                    {
                        if(strpos($_SERVER['HTTP_REFERER'], $authorizedPages[$i]) !== false)
                        {
                            $isAuthorized = true;
                            break;
                        }
                    }

                    if(!$isAuthorized)
                    {
                        header('X-Frame-Options: DENY');
                    }
                }
                else
                {
                    header('X-Frame-Options: DENY');
                }
            }
        }
   } 
   else 
   {
       //Va studiata una soluzione, per ora tolto error reporting
       /*if(strpos($_SERVER['HTTP_REFERER'], $appUrl) !== false)
       {
           $embeddable = 'no';
       } */
   }
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
    
    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>

    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?php echo time();?>" rel="stylesheet">
    <!--<link href="../css/pageTemplate.css?v=<?php echo time();?>" rel="stylesheet">-->
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php echo time();?>" type="text/css" />
    
    <!-- Material icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <script src="../js/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>

    <!-- Highcharts --> 
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>
    
    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css"
   integrity="sha512-07I2e+7D8p6he1SIM+1twR5TIrhUQn9+I6yjqD53JQjFiMf8EtC93ty0/5vJTZGF8aAocvHYNEDJajGdNx1IsQ=="
   crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"
   integrity="sha512-A7vV8IFfih/D732iSSKi20u/ooOfj/AGehOKq0f4vLT1Zr2Y+RX7C+w8A1gaSasGtRUZpF/NZgzSAu4/Gc41Lg=="
   crossorigin=""></script>
   
   <!-- Leaflet marker cluster plugin -->
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.css" />
   <link rel="stylesheet" href="../leaflet-markercluster/MarkerCluster.Default.css" />
   <script src="../leaflet-markercluster/leaflet.markercluster-src.js" type="text/javascript" charset="utf-8"></script>
   
   <!-- Dot dot dot -->
   <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>
   
    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet"/>
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>
    
    <!-- Moment -->
    <script type="text/javascript" src="../moment/moment.js"></script>
    
    <!-- Bootstrap datetimepicker -->
    <script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="../datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    
    <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=<?php echo time();?>">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <script src="../js/widgetsCommonFunctions.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php echo time();?>" type="text/javascript" charset="utf-8"></script>

    <script type='text/javascript'>
        var array_metrics = new Array();
        var headerFontSize, headerModFontSize, subtitleFontSize, subtitleModFontSize, dashboardId, dashboardName, logoFilename, logoLink, 
            clockFontSizeMod, logoWidth, logoHeight, headerVisible = null;
    
        var dashboardZoomEventHandler = function(event)
        {
            document.body.style.zoom = event.data;
        };

        window.addEventListener('message', dashboardZoomEventHandler, false);    
        
        $(document).ready(function () 
        {
            var widgetsBorders, widgetsBordersColor, embedWidget, embedWidgetPolicy, headerVisible, wrapperWidth = null;
            var firstLoad = true;
            var loggedUserFirstAttempt = true;
            
            var embedPreview = "<?php if(isset($_REQUEST['embedPreview'])){echo $_REQUEST['embedPreview'];}else{echo 'false';} ?>";
            
            var loginFormCntMargin = parseInt(($('#authFormDarkBackground').height() - $('#authFormContainer').height()) / 2);
            $('#authFormContainer').css("margin-top", loginFormCntMargin + "px");

            $(window).resize(function(){
                var loginFormCntMargin = parseInt(($('#authFormDarkBackground').height() - $('#authFormContainer').height()) / 2);
                $('#authFormContainer').css("margin-top", loginFormCntMargin + "px");
            });
            
            //Questo ti dice il grado di zoom
            //console.log("window.devicePixelRatio: " + window.devicePixelRatio.toFixed(2));
            
            /*$("#showHideHeader").off("click");
            $("#showHideHeader").click(function()
            {
               if(headerVisible === 1)
               {
                  $("#navbarDashboard").hide();
                  $("#navbarDashboard").css("margin-bottom", "0px");
                  $("#headerSpacer").hide();
                  $("#showHideHeader i").attr("class", "fa fa-expand");
                  $("#showHideHeader").attr("title", "Show dashboard header");
                  headerVisible = 0;
               }
               else
               {
                  $("#navbarDashboard").show();
                  $("#navbarDashboard").css("margin-bottom", "100px");
                  $("#headerSpacer").show();
                  $("#showHideHeader i").attr("class", "fa fa-compress");
                  $("#showHideHeader").attr("title", "Hide dashboard header");
                  headerVisible = 1;
               }
               
               $.ajax({
                  url: "../management/process-form.php",
                  data: {
                     showHideDashboardHeader: headerVisible, 
                     dashboardId: "<?= base64_decode($_GET['iddasboard']) ?>"
                  },
                  type: "POST",
                  async: true,
                  //dataType: 'json',
                  success: function (data)
                  {
                     //Non facciamo niente di specifico
                  },
                  error: function (data)
                  {
                     console.log("Ko");
                     console.log(data);
                  }
               });
            });*/
            
            //Definizioni di funzione
            function loadDashboard(dashboardParams, dashboardWidgets)
            {
                var num_cols, minEmbedDim, autofitAlertFontSize;
                
                if('<?php echo $embeddable; ?>' === 'yes')
                {
                    if(window.self !== window.top)
                    {
                        if(('<?php echo $embedPolicy; ?>' === 'auto')||(('<?php echo $embedPolicy; ?>' !== 'auto')&&('<?php echo $embedAutofit; ?>' === 'yes')))
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
                
                dashboardId = <?= base64_decode($_GET['iddasboard']) ?>;
                
                for(var i = 0; i < dashboardParams.length; i++)
                {
                    dashboardName = dashboardParams[i].name_dashboard;
                    logoFilename = dashboardParams[i].logoFilename;
                    logoLink = dashboardParams[i].logoLink;
                    headerVisible = dashboardParams[i].headerVisible;
                    widgetsBorders = dashboardParams[i].widgetsBorders;
                    widgetsBordersColor = dashboardParams[i].widgetsBordersColor;
                    $("#headerLogoImg").css("display", "none");
                    wrapperWidth = parseInt(dashboardParams[i].width) + 40;
                    //Le parti commentate sono da spunto per futura modifica che toglie i marginin dx e sx, ma questo sconvolge tutte le dashboard esistenti, si farà in futuro
                    $("#wrapper-dashboard").css("width", wrapperWidth);
                    $("#container-widgets").css("width", /*wrapperWidth*/dashboardParams[i].width);
                    $("#wrapper-dashboard").css("margin", "0 auto");
                    $("#navbarDashboard").css("background-color", dashboardParams[i].color_header);
                    
                    //Sfondo
                    $("body").css("background-color", dashboardParams[i].external_frame_color);
                    $("#page-wrapper").css("background-color", dashboardParams[i].external_frame_color);
                    $("#container-widgets").css("background-color", dashboardParams[i].color_background);
                    $("#container-widgets").css("border-top-color",dashboardParams[i].color_background);
                    
                    //$('#page-wrapper div.container-fluid').css('padding-left', '0px');
                    //$('#page-wrapper div.container-fluid').css('padding-right', '0px');

                    headerFontSize = dashboardParams[i].headerFontSize;
                    subtitleFontSize = parseInt(dashboardParams[i].headerFontSize * 0.22);
                    if(subtitleFontSize < 20)
                    {
                        subtitleFontSize = 20;
                    }
                    var headerFontColor = dashboardParams[i].headerFontColor;

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
                    $("#dashboardTitle").text(dashboardParams[i].title_header);
                    $("#clock").css("color", headerFontColor);
                    $("#clock").css("font-size", clockFontSizeMod + "pt");
                    
                    /*$('#dashboardHeaderMenuTab').css("position", "fixed");
                    $('#dashboardHeaderMenuTab').css("top", $('#navbarDashboard').height());
                    $('#dashboardHeaderMenuTab').css("left", $(document).width() - $('#dashboardHeaderMenuTab').width());
                    $('#dashboardHeaderMenuTab').css("color", headerFontColor);
                    $('#dashboardHeaderMenuTab').css("background-color", $('#navbarDashboard').css("background-color"));*/

                    var whiteSpaceRegex = '^[ t]+';
                    if((dashboardParams[i].subtitle_header === "") || (dashboardParams[i].subtitle_header === null) ||(typeof dashboardParams[i].subtitle_header === 'undefined') ||(dashboardParams[i].subtitle_header.match(whiteSpaceRegex)))
                    {
                        $("#dashboardTitle").css("height", "100%");
                        $("#dashboardSubtitle").css("display", "none");
                    }
                    else
                    {
                        $("#dashboardTitle").css("height", "70%");
                        $("#dashboardSubtitle").css("height", "30%");
                        $("#dashboardSubtitle").css("font-size", subtitleModFontSize + "pt");
                        $("#dashboardSubtitle").css("display", "");
                        $("#dashboardSubtitle").css("color", headerFontColor);
                        $("#dashboardSubtitle").text(dashboardParams[i].subtitle_header);
                    }

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

                    num_cols = dashboardParams[i].num_columns;
                    num_rows = dashboardParams[i].num_rows;
                }//Fine del primo for
               
               if(window.self === window.top)
               {
                    //Controllo mostrare/nascondere header su view principale
                    if(headerVisible === '1')
                    {
                       $("#navbarDashboard").show();
                       $("#navbarDashboard").css("margin-bottom", "100px");
                       $("#headerSpacer").show();
                       $("#showHideHeader i").attr("class", "fa fa-compress");
                       $("#showHideHeader").attr("title", "Hide dashboard header");
                    }
                    else
                    {
                       $("#navbarDashboard").hide();
                       $("#navbarDashboard").css("margin-bottom", "0px");
                       $("#headerSpacer").hide();
                       $("#showHideHeader i").attr("class", "fa fa-expand");
                       $("#showHideHeader").attr("title", "Show dashboard header");
                    } 
               }
               else
               {
                    //Controllo mostrare/nascondere header in modalità embedded
                    if('<?php echo $embedPolicy; ?>' === 'auto')
                    {
                        $('#navbarDashboard').hide();
                        $('#navbarDashboard').css("margin-bottom", "0px");
                        $('#headerSpacer').hide();
                    }
                    else
                    {
                        if('<?php echo $showHeaderEmbedded; ?>' === 'no')
                        {
                            $('#navbarDashboard').hide();
                            $('#navbarDashboard').css("margin-bottom", "0px");
                            $('#headerSpacer').hide();
                        }
                    }
               }
               
                var gridsterCellW = 76;
                var gridsterCellH = 38;
               
                jQuery(function (){ 
                    jQuery(".gridster ul").gridster({
                        widget_base_dimensions: [gridsterCellW, gridsterCellH],
                        widget_margins: [1, 1],
                        min_cols: num_cols,
                        max_size_x: 30,
                        max_rows: 50,
                        extra_rows: 50,
                        draggable: {ignore_dragging: true},
                        serialize_params: function ($w, wgd){
                            return {
                                id: $w.attr('id'),
                                col: wgd.col,
                                row: wgd.row,
                                size_x: wgd.size_x,
                                size_y: wgd.size_y
                            };
                        }
                    }).data('gridster').disable();
                });//Fine creazione Gridster

                var gridster = $("#container-widgets ul").gridster().data('gridster');

                for(var i = 0; i < dashboardWidgets.length; i++)
                {
                    var name_w = dashboardWidgets[i]['name_widget'];
                    var widgetId = dashboardWidgets[i]['Id_w'];
                    var time = 0;
                    if (dashboardWidgets[i]['temporal_range_widget'] !== "" && dashboardWidgets[i]['temporal_range_widget'] === "Mensile") 
                    {
                        time = "30/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_widget'] !== "" && dashboardWidgets[i]['temporal_range_widget'] === "Annuale") 
                    {
                        time = "365/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_widget'] !=="" && dashboardWidgets[i]['temporal_range_widget'] === "Settimanale") 
                    {
                        time = "7/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_widget'] !== "" && dashboardWidgets[i]['temporal_range_widget'] === "Giornaliera") 
                    {
                        time = "1/DAY";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_widget'] !== "" && dashboardWidgets[i]['temporal_range_widget'] === "4 Ore") 
                    {
                        time = "4/HOUR";
                    } 
                    else if (dashboardWidgets[i]['temporal_range_widget'] !== "" && dashboardWidgets[i]['temporal_range_widget'] === "12 Ore") 
                    {
                        time = "12/HOUR";
                    }
                    var widget = ['<li data-widgetId="' + dashboardWidgets[i]['id_widget'] + '" id="' + name_w + '"></li>', dashboardWidgets[i]['size_columns_widget'], dashboardWidgets[i]['size_rows_widget'], dashboardWidgets[i]['n_column_widget'], dashboardWidgets[i]['n_row_widget']];

                    gridster.add_widget.apply(gridster, widget);

                    var type_metric = new Array();
                    var source_metric = new Array();
                    for (var k = 0; k < dashboardWidgets[i]['metrics_prop'].length; k++) {
                        type_metric.push(dashboardWidgets[i]['metrics_prop'][k]['type_metric']);
                        source_metric.push(dashboardWidgets[i]['metrics_prop'][k]['source_metric']);
                    }
                    
                    if(('<?php echo $embeddable; ?>' === 'yes')&&(window.self !== window.top))
                    {
                        embedWidget = true;
                    }
                    else
                    {
                        embedWidget = false;
                    }
                    embedWidgetPolicy = '<?php echo $embedPolicy; ?>';

                    $("#container-widgets ul").find("li#" + name_w).load("../widgets/" + encodeURIComponent(dashboardWidgets[i]['type_widget']) + ".php?name=" + encodeURIComponent(name_w) + "&hostFile=index" + "&idWidget=" + encodeURIComponent(widgetId) + "&metric=" + encodeURIComponent(dashboardWidgets[i]['id_metric_widget']) + "&embedWidget=" + embedWidget + "&embedWidgetPolicy=" + embedWidgetPolicy +
                            "&freq=" + encodeURIComponent(dashboardWidgets[i]['frequency_widget']) + "&title=" + encodeURIComponent(dashboardWidgets[i]['title_widget']) + "&color=" + encodeURIComponent(dashboardWidgets[i]['color_widget']) + /*"&info=" + encodeURIComponent(dashboardWidgets[i]['message_widget']) +*/ "&source=" + encodeURIComponent(source_metric) +
                            "&type_metric=" + encodeURIComponent(type_metric) + "&tmprange=" + encodeURIComponent(time) + "&city=" + encodeURIComponent(dashboardWidgets[i]['municipality_widget']) + "&link_w=" + encodeURIComponent(dashboardWidgets[i]['link_w']) + "&frame_color="+encodeURIComponent(dashboardWidgets[i]['frame_color']) + 
                            "&udm=" + encodeURIComponent(dashboardWidgets[i]['udm']) + "&fontSize=" + encodeURIComponent(dashboardWidgets[i]['fontSize']) + "&fontColor=" + encodeURIComponent(dashboardWidgets[i]['fontColor']) +
                            "&headerFontColor=" + encodeURIComponent(dashboardWidgets[i]['headerFontColor']) + "&numCols=" + encodeURIComponent(num_cols) + "&sizeX=" + encodeURIComponent(dashboardWidgets[i]['size_columns_widget']) + "&sizeY=" + encodeURIComponent(dashboardWidgets[i]['size_rows_widget']) + "&controlsPosition=" + encodeURIComponent(dashboardWidgets[i]['controlsPosition']) + "&zoomControlsColor=" + encodeURIComponent(dashboardWidgets[i]['zoomControlsColor']) + "&showTitle=" + encodeURIComponent(dashboardWidgets[i]['showTitle']) + "&controlsVisibility=" + encodeURIComponent(dashboardWidgets[i]['controlsVisibility']) + "&zoomFactor=" + encodeURIComponent(dashboardWidgets[i]['zoomFactor']) + "&defaultTab=" + encodeURIComponent(dashboardWidgets[i]['defaultTab']) + "&scaleX=" + encodeURIComponent(dashboardWidgets[i]['scaleX']) + "&scaleY=" + encodeURIComponent(dashboardWidgets[i]['scaleY'])
                    );

                }//Fine del secondo for

                //Applicazione bordi dei widgets
                if(widgetsBorders === 'yes')
                {
                    $(".gridster .gs_w").css("border", "1px solid " + widgetsBordersColor);
                }
                else
                {
                    $(".gridster .gs_w").css("border", "none");
                }

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

                //Icona info
                $(document).on('click', '.info_source', function () {
                    var name_widget_m = $(this).parents('li').attr('id');
                    $.ajax({
                        url: "../management/get_data.php",
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
                
                if(('<?php echo $embeddable; ?>' === 'yes')&&(window.self !== window.top))
                {
                    if('<?php echo $embedPolicy; ?>' === 'auto')
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
                            
                            //console.log("iframeCase: " + iframeCase);
                            
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
                        
                        //Autofit in modalità manuale
                        if('<?php echo $embedAutofit; ?>' === 'yes')
                        {
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
                                }
                                $('#autofitAlert').hide();
                            }, 2500); 
                        }
                    }
                }
            }
            
            function authUser()
            {
                $.ajax({
                    url: "../management/getDashboardData.php",
                    //Lasciare il vecchio refuso "iddasboard" per non cambiare i link
                    data: 
                    { 
                        dashboardId: <?= base64_decode($_GET['iddasboard']) ?>,
                        username: $("#username").val(),
                        password: $("#password").val(),
                        loggedUserFirstAttempt: loggedUserFirstAttempt
                    },
                    type: "GET",
                    async: true,//LASCIARLA ASINCRONA.
                    dataType: 'json',
                    success: function (response) 
                    {  
                        switch(response.visibility)
                        {
                            case 'public':
                                $('body').removeClass("dashboardViewBodyAuth");
                                $('#authFormDarkBackground').hide();
                                $('#authFormContainer').hide();
                                $("#wrapper-dashboard").show();
                                loadDashboard(response.dashboardParams, response.dashboardWidgets);
                                break;

                            case 'author': case 'restrict':
                                $('body').addClass("dashboardViewBodyAuth");
                                $('#authFormDarkBackground').show();
                                $('#authFormContainer').show();
                                switch(response.detail)
                                {
                                    case "credentialsMissing":
                                        $("#wrapper-dashboard").hide();
                                        if(firstLoad === false)
                                        {
                                            $("#authFormMessage").html("Credentials missing");
                                        }
                                        else
                                        {
                                            $("#authFormMessage").html("");
                                            firstLoad = false;
                                        }
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#wrapper-dashboard").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user: please try again");
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkLoggedUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#wrapper-dashboard").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user logged to main application: please try again");
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "checkLoggedViewUserQueryKo":
                                        //Fallimento query controllo presenza utente
                                        $("#wrapper-dashboard").hide();
                                        $("#authFormMessage").html("Failure during DB query to check user logged to dashboard view: please try again");
                                        $("#authBtn").click(authUser);
                                        break;     
                                        
                                    case "userNotRegistered":
                                        $("#wrapper-dashboard").hide();
                                        $("#authFormMessage").html("User not registered or wrong username / password");
                                        $("#authBtn").click(authUser);
                                        break;
                                        
                                    case "Ok": 
                                        $('body').removeClass("dashboardViewBodyAuth");
                                        $('#authFormDarkBackground').hide();
                                        $('#authFormContainer').hide();
                                        $("#wrapper-dashboard").show();
                                        loadDashboard(response.dashboardParams, response.dashboardWidgets);
                                        
                                    if(response.context === "View")
                                    {
                                        $("#viewLogoutBtn").show();
                                        $("#viewLogoutBtn").click(function(){
                                            event.preventDefault();
                                            $("#logoutViewModal").modal('show');
                                        });

                                        $("#confirmLogoutBtn").click(function(event){
                                            $.ajax({
                                                url: "../management/sessionUpdate.php",
                                                data: {
                                                  sessionAction: 'closeViewSession',
                                                  dashboardId: <?= base64_decode($_GET['iddasboard']) ?>
                                                },
                                                type: "POST",
                                                async: false,
                                                dataType: 'json',
                                                success: function (data) 
                                                {
                                                    //console.log(JSON.stringify(data));
                                                    switch(data.detail)
                                                    {
                                                        case "Ok":
                                                            $("#logoutViewModalFooter").hide();
                                                            $("#logoutViewModalMsg").hide();
                                                            $("#logoutViewModalOk").show();
                                                            setTimeout(function(){
                                                                $("#logoutViewModal").modal('hide');
                                                                location.reload();
                                                            }, 2000);
                                                            break;

                                                        case "Ko":
                                                            $("#logoutViewModalMsg").hide();
                                                            $("#logoutViewModalFooter").hide();
                                                            $("#logoutViewModalKo").show();
                                                            setTimeout(function(){
                                                                $("#logoutViewModal").modal('hide');
                                                                $("#logoutViewModalKo").hide();
                                                                $("#logoutViewModalMsg").show();
                                                                $("#logoutViewModalFooter").show();
                                                            }, 2000);
                                                            break;
                                                    }
                                                },
                                                error: function (data)
                                                {
                                                    $("#logoutViewModalMsg").hide();
                                                    $("#logoutViewModalFooter").hide();
                                                    $("#logoutViewModalKo").show();
                                                    setTimeout(function(){
                                                        $("#logoutViewModal").modal('hide');
                                                        $("#logoutViewModalKo").hide();
                                                        $("#logoutViewModalMsg").show();
                                                        $("#logoutViewModalFooter").show();
                                                    }, 2000);
                                                    console.log("Error");
                                                    console.log(JSON.stringify(data));
                                                }
                                            });
                                        });
                                    }
                                    break;

                                case "Ko": 
                                    $("#wrapper-dashboard").hide();
                                    $("#authFormMessage").html("User not allowed to see this dashboard");
                                    $('body').addClass("dashboardViewBodyAuth");
                                    $('#authFormDarkBackground').show();
                                    $('#authFormContainer').show();
                                    $("#authBtn").click(authUser);        
                                    break;

                                case "loggedUserKo": 
                                    loggedUserFirstAttempt = false;
                                    $("#wrapper-dashboard").hide();
                                    $("#authFormMessage").html("Logged user not allowed to see this dashboard");
                                    $('body').addClass("dashboardViewBodyAuth");
                                    $('#authFormDarkBackground').show();
                                    $('#authFormContainer').show();
                                    $("#authBtn").click(authUser);        
                                    break;

                                case "loggedViewUserKo": 
                                    loggedUserFirstAttempt = false;
                                    $("#wrapper-dashboard").hide();
                                    $("#authFormMessage").html("User logged to dashboard view not allowed to see this dashboard");
                                    $('body').addClass("dashboardViewBodyAuth");
                                    $('#authFormDarkBackground').show();
                                    $('#authFormContainer').show();
                                    $("#authBtn").click(authUser);        
                                    break;    
                            }
                            break; 
                        }
                    },
                    error: function (data)
                    {
                        $("#wrapper-dashboard").hide();
                        $("#authFormContainer").hide();
                        $("#getVisibilityError").show();
                        console.log("Error: " + JSON.stringify(data));
                    }
                }); 
            }
            //Fine definizioni di funzione
            
            //Main
            authUser();
        });
    </script>
</head>

<body>
    <?php include "../management/sessionExpiringPopup.php" ?>
    <div id="getVisibilityError">
        <div id="wrapper">
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">Dashboard management system</a>
                </div>
            </nav>
        </div>   
        <br/><br/><br/><br/>
        <h1>Error!</h1>
        <p>Error while trying to get dashboard visibility: please try again</p>
    </div>
    
    <div id="authFormDarkBackground">
        <div class="row">
            <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>
        </div>
        
        <div class="row">
            <div id="authFormContainer" class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                <div class="col-xs-12" id="loginFormTitle" class="centerWithFlex" style="margin-top: 15px">
                   Restricted access dashboard
                </div>
                <form id="authForm" class="form-signin" role="form" method="post" action="">
                    <div class="col-xs-12" id="loginFormBody">
                        <div class="col-xs-12 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" id="username" name="username" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Username</div>
                        </div>
                        <div class="col-xs-12 modalCell">
                            <div class="modalFieldCnt">
                                <input type="password" class="modalInputTxt" id="password" name="password" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Password</div>
                        </div>
                        <div class="col-xs-12 modalCell">
                            <div id="authFormMessage"></div>
                        </div>
                        
                    </div>
                <div class="col-xs-12 centerWithFlex" id="loginFormFooter" style="margin-bottom: 15px">
                    <button type="reset" id="loginCancelBtn" class="btn cancelBtn" data-dismiss="modal">Reset</button>
                    <button type="button" id="authBtn" name="login" class="btn confirmBtn internalLink">Login</button>
                </div>
                </form>
            </div>
        </div>
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
    
    
    <div id="wrapper-dashboard">
        <!-- Header -->
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
        <!--<div id="dashboardHeaderMenu">
            <div class="dashboardHeaderMenuItem">
               show/hide header             
            </div>
            <div class="dashboardHeaderMenuItem">
               show/hide footer             
            </div>
        </div>
        <div id="dashboardHeaderMenuTab">
            menu            
        </div>-->
        <span id="headerSpacer"><br/><br/><br/><br/><br/><br/></span>
        
        <!-- page-wrapper -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div id="container-widgets" class="gridster">
                    <ul></ul>    
                </div>
                <div id="embedAutoLogoContainer">
                    <a title="Km4City" href="https://www.km4city.org" target="_new"><img id="embedAutoLogo" src="../img/PoweredByKm4City1Line.png" /></a>
                </div>
                <div id="logos" class="footerLogos">
                    <!--<a href="#" class="footerLogo" id="showHideHeader"><i class='fa fa-compress'></i></a>-->
                    <a title="Logout from this dashboard" href="#" class="footerLogo"><i id="viewLogoutBtn" class="fa fa-sign-out"></i></a>
                    <a title="Disit" href="https://www.disit.org" target="_new" class="footerLogo"><img src="../img/disitLogo.png" /></a>
                </div>
            </div>
        </div>
        
        <!-- modale informazioni generali del widget -->
        <div class="modal fade" tabindex="-1" id="dialog-information-widget" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" id="info01"> 
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="titolo_info">Descrizione:</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-information-widget" class="form-horizontal" name="form-information-widget" role="form" method="post" action="" data-toggle="validator">
                            <div id="contenuto_infomazioni"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
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
        
        <!-- Modale di conferma logout dashboard -->
        <div class="modal fade" id="logoutViewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Close this dashboard
                </div>
                <div id="delDsModalBody" class="modal-body modalBody">
                    <div class="row" id="logoutViewModalMsg">
                        <div class="col-xs-12 modalCell">
                            <div class="modalDelMsg col-xs-12 centerWithFlex">
                                Do you want to confirm logout from this dashboard? 
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="delDsName"></div> 
                        </div>
                    </div>
                    <div class="row" id="logoutViewModalOk">
                        <div class="col-xs-12 centerWithFlex">Logout correctly executed</div>
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="logoutViewModalKo">
                        <div class="col-xs-12 centerWithFlex">Logout not possibile, please try again</div>
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                <div id="logoutViewModalFooter" class="modal-footer">
                  <button type="button" id="discardLogoutBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="confirmLogoutBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
              </div>
            </div>
        </div>
        
        
        <!--<div class="modal fade" id="logoutViewModal" tabindex="-1" role="dialog" aria-labelledby="logoutViewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="logoutViewModalLabel">Close this dashboard</h5>
                </div>
                <div class="modal-body">
                   <div id="logoutViewModalMain" class="modalBodyInnerDiv">
                     <div class="row" style="width: 100%; float: left">
                       Do you want to confirm logout from this dashboard?       
                     </div>
                   </div>
                   <div id="logoutViewModalOk" class="modalBodyInnerDiv">
                       <div class="modalBodyInnerDiv">Logout correctly executed</div>
                       <div class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
                   </div>
                   <div id="logoutViewModalKo" class="modalBodyInnerDiv">
                       <div class="modalBodyInnerDiv">Logout not possibile, please try again</div>
                       <div class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
                   </div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="discardLogoutBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="button" id="confirmLogoutBtn" class="btn btn-primary">Logout</button>
                </div>
              </div>
            </div>
        </div>-->
        
        <!-- Modale impossibilità di apertura link in nuovo tab per widgetExternalContent -->
        <div class="modal fade" tabindex="-1" id="newTabLinkOpenImpossibile" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document"> 
                <div class="modal-content">
                    <div class="modal-header centerWithFlex">
                        <h4 class="modal-title">External content</h4>
                    </div>
                    <div class="modal-body">
                        <div id="newTabLinkOpenImpossibileMsg"></div>
                        <div id="newTabLinkOpenImpossibileIcon">
                             <i class="fa fa-frown-o"></i>           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modale cambio stato evacuation plan -->
        <div class="modal fade" tabindex="-1" id="modalChangePlanStatus" role="dialog" aria-labelledby="myModalLabel">
            <div id="modalChangePlanStatusDialog" class="modal-dialog modal-lg" role="document"> 
                <div class="modal-content">
                    <div id="modalChangePlanStatusModalTitle" class="modal-header centerWithFlex">
                        evacuation plan status management
                    </div>
                    <div id="modalChangePlanStatusMain" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-6 centerWithFlex modalChangePlanStatusLabel">
                                plan identifier
                            </div> 
                            <div class="col-sm-6 centerWithFlex modalChangePlanStatusLabel">
                                current approval status
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6 centerWithFlex" id="modalChangePlanStatusTitle" ></div> 
                           <div class="col-sm-4 col-sm-offset-1 centerWithFlex" id="modalChangePlanStatusStatus"></div>
                        </div>
                       
                        <div class="row">
                           <div class="col-sm-6 col-sm-offset-3 centerWithFlex modalChangePlanStatusLabel">
                               new approval status 
                           </div> 
                        </div>
                        <div class="row">
                           <div class="col-sm-4 col-sm-offset-4 centerWithFlex">
                               <select class="form-control" id="modalChangePlanStatusSelect" name="modalChangePlanStatusSelect" required></select> 
                           </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusWait" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                updating status, please wait
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-spinner fa-spin" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusOk" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-10 col-sm-offset-1 centerWithFlex">
                                status successfully updated
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-thumbs-o-up" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    <div id="modalChangePlanStatusKo" class="modal-body container-fluid">
                        <div class="row">
                            <div class="col-sm-12 centerWithFlex">
                                error while trying to send new status to server, please try again
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <i class="fa fa-thumbs-down" style="font-size:84px"></i>
                            </div> 
                        </div>
                    </div>
                    
                    <input type="hidden" id="modalChangePlanStatusPlanId" />
                    <input type="hidden" id="modalChangePlanStatusCurrentStatus" />
                   
                    <div id="modalChangePlanStatusFooter" class="modal-footer centerWithFlex">
                       <button type="button" class="btn btn-secondary" id="modalChangePlanStatusCancelBtn">cancel</button>
                       <button type="button" class="btn btn-primary" id="modalChangePlanStatusConfirmBtn">confirm</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>

