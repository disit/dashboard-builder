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
    <link href="../css/bootstrap.min.css" rel="stylesheet">


    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <link rel="stylesheet" href="../css/style_widgets.css" type="text/css" />
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

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Gridster -->
    <script src="../js/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>

    <!-- Custom Core JavaScript -->
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">

    <script type='text/javascript'>
        var array_metrics = new Array();
        var headerFontSize = null;
        var headerModFontSize = null;
        var subtitleFontSize = null;
        var subtitleModFontSize = null;
        var dashboardName = null;
        var logoFilename = null;
        var logoLink = null;
        var clockFontSizeMod = null;
        var logoWidth = null;
        var logoHeight = null;
        
        $(document).ready(function () {
            $.ajax({
                url: "../management/get_data.php",
                data: {action: "get_param_dashboard", id_dashb: <?= base64_decode($_GET['iddasboard']) ?>},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) {
                    var num_cols;
                    for (var i = 0; i < data.length; i++)
                    {
                        dashboardName = data[i].name_dashboard;
                        logoFilename = data[i].logoFilename;
                        logoLink = data[i].logoLink;
                        $("#headerLogoImg").css("display", "none");
                        var wrapperWidth = parseInt(data[i].width) + 40;
                        $("#wrapper-dashboard").css("width", wrapperWidth);
                        $("#logos").css("width", data[i].width);
                        $("#container-widgets").css("width", data[i].width);
                        $("#wrapper-dashboard").css("margin", "0 auto");
                        $("#navbarDashboard").css("background-color", data[i].color_header);
                        //sfondo
                        $("body").css("background-color", data[i].external_frame_color);
                        $("#page-wrapper").css("background-color", data[i].external_frame_color);
                        $(".logos-bar").css("background-color", data[i].external_frame_color);
                        $("#container-widgets").css("background-color", data[i].color_background);
                        $("#container-widgets").css("border-top-color",data[i].color_background);
                       
                        headerFontSize = data[i].headerFontSize;
                        subtitleFontSize = parseInt(data[i].headerFontSize * 0.25);
                        if(subtitleFontSize < 24)
                        {
                            subtitleFontSize = 24;
                        }
                        var headerFontColor = data[i].headerFontColor;
                        
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
                            $("#dashboardSubtitle").css("font-size", subtitleModFontSize + "pt");
                            $("#dashboardSubtitle").css("display", "");
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
                                }
                                logoWidth = $('#headerLogoImg').width();
                                logoHeight = $('#headerLogoImg').height();                                
                                $("#headerLogoImg").css("display", "");
                            }
                        }
            
                        num_cols = data[i].num_columns;
                        num_rows = data[i].num_rows;
                    }
                    
                    jQuery(function () { 
                        jQuery(".gridster ul").gridster({
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
                        }).data('gridster').disable();
                        ;

                    });

                    $.ajax({
                        url: "../management/get_data.php",
                        data: {action: "get_widgets_dashboard", id_dashb: <?= base64_decode($_GET['iddasboard']) ?>},
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) {
                            if (data.length > 0) {
                                gridster = $("#container-widgets ul").gridster().data('gridster');

                                for (var i = 0; i < data.length; i++)
                                {
                                    var name_w = data[i]['name_widget'];
                                    var widgetId = data[i]['Id_w'];
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
                                    for (var k = 0; k < data[i]['metrics_prop'].length; k++) {
                                        type_metric.push(data[i]['metrics_prop'][k]['type_metric']);
                                        source_metric.push(data[i]['metrics_prop'][k]['source_metric']);
                                    }
                                    $("#container-widgets ul").find("li#" + name_w).load("../widgets/" + encodeURIComponent(data[i]['type_widget']) + ".php?name=" + encodeURIComponent(name_w) + "&metric=" + encodeURIComponent(data[i]['id_metric_widget']) +
                                            "&freq=" + encodeURIComponent(data[i]['frequency_widget']) + "&title=" + encodeURIComponent(data[i]['title_widget']) + "&color=" + encodeURIComponent(data[i]['color_widget']) + "&source=" + "&info=" + encodeURIComponent(data[i]['message_widget']) + encodeURIComponent(source_metric) +
                                            "&type_metric=" + encodeURIComponent(type_metric) + "&city=" + "&tmprange=" + encodeURIComponent(time) + "&city=" + encodeURIComponent(data[i]['municipality_widget']) + "&link_w=" + encodeURIComponent(data[i]['link_w']) + "&frame_color="+encodeURIComponent(data[i]['frame_color']) + "&udm=" + encodeURIComponent(data[i]['udm']) + "&fontSize=" + encodeURIComponent(data[i]['fontSize']) + "&fontColor=" + encodeURIComponent(data[i]['fontColor']));

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


                },
                error: function (jqXHR, textStatus, errorThrown) 
                {
                    alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
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
        });

    </script>

</head>

<body>
    <div id="wrapper-dashboard">
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
                <div id="container-widgets" class="gridster">
                    <ul></ul>    
                </div>
                <div id='logos' class="logos-bar">
                    <div id ="logos_twitter" class ="logos_twitter-bar">
                        <span><img src='../management/img/logo_twitter.png'></img><div id="twitter_t" alt="Twitter logo"></div></span>
                    </div>
                    <div id="logos_twitter_ret" class="logos_twitter_ret-bar">
                        <span><img src='../management/img/retweet.png' alt="Twitter retweet logo"></img><div id="twitter_ret"><?php include("../widgets/widgetTwitter.php"); ?></div></span>
                    </div>
                    <div id="link_twitter_vig" class="link_twitter_vig-bar"><span><a title = "Twitter vigilance link" class ="link_twitter_vig-bar" href='http://www.disit.org/tv/' target='_new'>Twitter Vigilance</a></span></div>
                    <a id="logo_disit" class="logo_disit-bar" href="http://www.disit.org/" target="_new"><img src="../management/img/logo.jpg" alt="DISIT lab logo"/></a>	
                </div>
            </div>
        </div>
        <!-- /#page-wrapper -->
        <!-- /#modal dei commenti-->
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
    </div>
</body>
</html>

