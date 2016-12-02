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
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
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
    <link href="../css/dashboard_cfg.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <link rel="stylesheet" href="../css/style_widgetsKO.css" type="text/css" />


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


    <script type='text/javascript'>
        var array_metrics = new Array();
        var informazioni = new Array ();
        $(document).ready(function () {
            $.ajax({
                url: "get_data.php",
                data: {action: "get_param_dashboard"},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) {
                    var num_cols;
                    for (var i = 0; i < data.length; i++)
                    {
                        $("#wrapper-dashboard").css("width", data[i].width-data[i].remains_width);
                        //$("#container-widgets").css("padding-left", Math.floor(data[i].remains_width / 2));
                        $("#wrapper-dashboard").css("margin", "0 auto");
                        $("#navbar-dashboard").css("background-color", data[i].color_header);
                        $(".dashboard-title").text(data[i].title_header);
                        $(".dashboard-subtitle").text(data[i].subtitle_header);
                        num_cols = data[i].num_columns;
                        num_rows = data[i].num_rows;
                    }
                    jQuery(function () { //DOM Ready
                        jQuery(".gridster ul").gridster({
                            widget_margins: [1, 1],
                            widget_base_dimensions: [156, 77],
                            min_cols: num_cols,
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
                        }).data('gridster').disable();;
                    });
                    $.ajax({
                        url: "get_data.php",
                        data: {action: "get_widgets_dashboard"},
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) {
                            var gridster;
                            for (var j=0; j < data.length; j++){
                            informazioni[j] = data[j].message_widget;
                            //console.log(informazioni[j]);
                        }
                            
                            if (data.length > 0) {
                                gridster = $("#container-widgets ul").gridster().data('gridster');
                             
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
                                    } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "4Ore") {
                                        time = "4/HOUR";
                                    } else if (data[i]['temporal_range_widget'] != "" && data[i]['temporal_range_widget'] == "12 Ore") {
                                        time = "12/HOUR";
                                    }
                                    var widget = ['<li id="' + name_w + '"></li>', data[i]['size_rows_widget'], data[i]['size_columns_widget'], data[i]['n_column_widget'], data[i]['n_row_widget']];

                                    gridster.add_widget.apply(gridster, widget);

                                    var type_metric = new Array();
                                    var source_metric = new Array();
                                    var info_message = new Array();
                                    for (var k = 0; k < data[i]['metrics_prop'].length; k++) {
                                        type_metric.push(data[i]['metrics_prop'][k]['type_metric']);
                                        source_metric.push(data[i]['metrics_prop'][k]['source_metric']);
                                        info_message.push(informazioni[i]);
                                    }
                                    $("#container-widgets ul").find("li#" + name_w).load("../widgets/" + encodeURIComponent(data[i]['type_widget']) + ".php?name=" + encodeURIComponent(name_w) + "&metric=" + encodeURIComponent(data[i]['id_metric_widget']) +
                                            "&freq=" + encodeURIComponent(data[i]['frequency_widget']) + "&title=" + encodeURIComponent(data[i]['title_widget']) + "&color=" + encodeURIComponent(data[i]['color_widget']) + "&source=" + encodeURIComponent(source_metric) +
                                   
                                            "&type_metric=" + encodeURIComponent(type_metric) + "&city=" + "&tmprange=" + encodeURIComponent(time) + "&city=" + encodeURIComponent(data[i]['municipality_widget'])+ + "&frame_color="+encodeURIComponent(data[i]['frame_color']));
                                            
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
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');

                    /*$('#page-wrapper').html('<p>status code: ' + jqXHR.status + '</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>' + jqXHR.responseText + '</div>');
                     console.log('jqXHR:');
                     console.log(jqXHR);
                     console.log('textStatus:');
                     console.log(textStatus);
                     console.log('errorThrown:');
                     console.log(errorThrown);*/
                }
            });

        });

    </script>													
</head>
<body>
    <div id="wrapper-dashboard">
        <!-- Navigation -->
        <nav id="navbar-dashboard" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div id="navbar-dashboard-header" class="navbar-header">
                <div class="dashboard-header-left"><span id="span-dashboard-title"><a class="dashboard-title" href="#"></a><sub class="dashboard-subtitle"></sub></span></div>
            </div>
            <!-- Top Menu Items -->
            <div id="dashboard-header-links">
                <ul id="navbar-right-dashboard" class="nav navbar-right top-nav">
                    <li><a title="link_service_map" href="http://servicemap.disit.org/WebAppGrafo/mappa.jsp" style="text-decoration:none" target='_new'>ServiceMap,</a></li>
                    <li><a title="link_smartds" href="http://smartds.disit.org/" style="text-decoration:none; padding-left:5px;" target='_new'>SmartDS</a></li>
                </ul>
            </div>
            <div id="clock-dashboard" class="clock"><?php include('../widgets/time.php'); ?> </div>      
        </nav>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div id="container-widgets" class="gridster">
                    <ul>

                    </ul>    
                </div>
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
</body>
</html>

