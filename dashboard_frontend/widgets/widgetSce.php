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
<script type='text/javascript'>
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000',
    };
    var colore_frame = "<?= $_GET['frame_color'] ?>";
    var nome_wid = "<?= $_GET['name'] ?>_div";
    $("#<?= $_GET['name'] ?>_div").css({'background-color':colore_frame});


    var list_metrics = "<?= $_GET['metric'] ?>";
    var metrics = list_metrics.split('+');
    var list_type_metrics = "<?= $_GET['type_metric'] ?>";
    var type_metrics = list_type_metrics.split(',');
    
    $(document).ready(function <?= $_GET['name'] ?>() 
    {
        $('#<?= $_GET['name'] ?>_desc').width('70%');  
        $('#<?= $_GET['name'] ?>_desc').html('<span><a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a><div id="<?= $_GET['name'] ?>_desc_text" class="desc_text" title="<?= preg_replace('/_/', ' ', $_GET['title']) ?>"><?= preg_replace('/_/', ' ', $_GET['title']) ?></div></span>');
        $('#<?= $_GET['name'] ?>_desc_text').css("width", "90%");
        $('#<?= $_GET['name'] ?>_desc_text').css("height", "100%");
        $("#<?= $_GET['name'] ?>_loading").css("background-color", '<?= $_GET['color'] ?>');
        $('.loadingTextDiv').css("font-size", "13px");
        $('.loadingTextDiv p').css("text-align", "center");
        $('.loadingIconDiv i').css("font-size", "20px");
        
        var value = null;
        var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
        $("#table_<?= $_GET['name'] ?>").css("height", height);
        
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        
        var link_w = "<?= $_GET['link_w'] ?>";
        var divChartContainer = $('#table_<?= $_GET['name'] ?>');
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        
        $('#measure_<?= $_GET['name'] ?>_value_cpu').html("<div style='width: 100%; height: 50%; display: flex; align-items: flex-end; justify-content: center; text-align: center;'><p style='text-align: center; font-size: 13px;'>Loading data, please wait</p></div><div style='width: 100%; height: 50%; display: flex; align-items: baseline; justify-content: center; text-align: center;'><i class='fa fa-spinner fa-spin' style='font-size: 20px'></i></div>");
        $('#<?= $_GET['name'] ?>_value_ram_container').css("height", "80%");
        $('#<?= $_GET['name'] ?>_value_ram_container').css("width", "100%");
        $('#<?= $_GET['name'] ?>_value_ram_round_container').css("display", "none");
        $('#<?= $_GET['name'] ?>_value_jobs_container').css("height", "80%");
        $('#<?= $_GET['name'] ?>_value_jobs_container').css("width", "100%");
        $('#<?= $_GET['name'] ?>_value_jobs_round_container').css("display", "none");
        
        //Estrazione dei parametri del widget
        $.ajax({
            url: "../widgets/getParametersWidgets.php",
            type: "GET",
            data: {"nomeWidget": ["<?= $_GET['name'] ?>"]},
            async: true,
            dataType: 'json',
            success: function (msg) {
                var parametri = msg.param.parameters;
                var contenuto = jQuery.parseJSON(parametri);
                var sizeRowsWidget = parseInt(msg.param.size_rows);
                var idMetric = msg.param.id_metric;
                var metricsToShow = [];
                var descCpu = null;
                var descRam = null;
                var descJobs = null;
                var alarmIntervals = [];
                var fontRatio = null;
                var fontRatioValue = null;
                var fontRatioValueDesc = null;
                var circleHeight = null;
                
                circleHeight = parseInt($("#<?= $_GET['name'] ?>_value_ram_container").height()*0.9);
                $("#<?= $_GET['name'] ?>_value_ram_round_container").css("width", circleHeight);
                $("#<?= $_GET['name'] ?>_value_ram_round_container").css("height", circleHeight);
                $("#<?= $_GET['name'] ?>_value_jobs_round_container").css("width", circleHeight);
                $("#<?= $_GET['name'] ?>_value_jobs_round_container").css("height", circleHeight);
                fontRatio = parseInt((sizeRowsWidget / 4)*60);
                fontRatio = fontRatio.toString() + "%";
                fontRatioValue = parseInt((sizeRowsWidget / 4)*90);
                fontRatioValue = fontRatioValue.toString() + "%";
                fontRatioValueDesc = parseInt((sizeRowsWidget / 4)*40);
                fontRatioValueDesc = fontRatioValueDesc.toString() + "%";
                $("#measure_<?= $_GET['name'] ?>_desc_cpu").css("font-size", fontRatio);
                $("#measure_<?= $_GET['name'] ?>_desc_cpu").css("font-family", "Verdana");
                $("#measure_<?= $_GET['name'] ?>_desc_cpu").css("color", fontColor);
                $("#measure_<?= $_GET['name'] ?>_desc_ram").css("font-size", fontRatio);
                $("#measure_<?= $_GET['name'] ?>_desc_ram").css("font-family", "Verdana");
                $("#measure_<?= $_GET['name'] ?>_desc_ram").css("color", fontColor);
                $("#measure_<?= $_GET['name'] ?>_desc_jobs").css("font-size", fontRatio);
                $("#measure_<?= $_GET['name'] ?>_desc_jobs").css("font-family", "Verdana");
                $("#measure_<?= $_GET['name'] ?>_desc_jobs").css("color", fontColor);
                $("#measure_<?= $_GET['name'] ?>_ram_value_p").css("font-size", fontRatioValue);
                $("#measure_<?= $_GET['name'] ?>_jobs_value_p").css("font-size", fontRatioValue);
                $("#measure_<?= $_GET['name'] ?>_ram_desc_p").css("font-size", fontRatioValueDesc);
                $("#measure_<?= $_GET['name'] ?>_jobs_desc_p").css("font-size", fontRatioValueDesc);
                
                if(idMetric.indexOf("Sce_CPU") !== -1)
                {
                    metricsToShow["Sce_CPU"] = true;
                }
                else
                {
                    metricsToShow["Sce_CPU"] = false;
                }
                
                if(idMetric.indexOf("Sce_Mem") !== -1)
                {
                    metricsToShow["Sce_Mem"] = true;
                }
                else
                {
                    metricsToShow["Sce_Mem"] = false;
                }
                
                if(idMetric.indexOf("Sce_Job_Day") !== -1)
                {
                    metricsToShow["Sce_Job_Day"] = true;
                }
                else
                {
                    metricsToShow["Sce_Job_Day"] = false;
                }
                
                $.ajax({
                    url: "../widgets/getDataMetrics.php",
                    data: {"IdMisura": metrics},
                    type: "GET",
                    async: true,
                    dataType: 'json',
                    success: function (msg) {
                        var threshold = [];
                        var thresholdEval = [];
                        var alarmCount = 0;
                        var alarms = [];
                        var desc2Blink = [];
                        var paneObj = null;
                        var dataLabelsObj = null;
                        var ramValueClass = null;
                        var ramUdmClass = null;
                        var jobsValueClass = null;
                        var jobsUdmClass = null;

                        date_agg = msg.data[0].commit.author.computationDate;
                        
                        alarms["Sce_CPU"] = false;
                        alarms["Sce_Mem"] = false;
                        alarms["Sce_Job_Day"] = false;

                        for (var i = 0; i < msg.data.length; i++) 
                        {
                            var udm = "";
                            var pattern = /Percentuale\//;
                            desc = msg.data[i].commit.author.IdMetric_data;
                            
                            switch(sizeRowsWidget)
                            {
                                case 4:
                                    paneObj = {
                                            startAngle: -135,
                                            endAngle: 135,
                                            size: '120%',
                                            center: ['50%', '52%'],
                                            background: [{
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#FFF'],
                                                            [1, '#333']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#333'],
                                                            [1, '#FFF']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    // default background
                                                }, {
                                                    backgroundColor: '#DDD',
                                                    borderWidth: 0,
                                                    outerRadius: '100%',
                                                    innerRadius: '100%'
                                                }]
                                        };
                                    dataLabelsObj = {
                                                enabled: true,
                                                borderWidth: 0,
                                                style: {
                                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                                                    fontSize: '13px',
                                                    fontFamily: 'Verdana'
                                                },
                                                x: -24,
                                                y: 50,
                                                formatter: function () {
                                                    var val = this.y;
                                                    return value + udm; 
                                                }
                                            };
                                    break;

                                case 5:
                                    paneObj = {
                                            startAngle: -135,
                                            endAngle: 135,
                                            size: '110%',
                                            center: ['50%', '52%'],
                                            background: [{
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#FFF'],
                                                            [1, '#333']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#333'],
                                                            [1, '#FFF']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    // default background
                                                }, {
                                                    backgroundColor: '#DDD',
                                                    borderWidth: 0,
                                                    outerRadius: '100%',
                                                    innerRadius: '100%'
                                                }]
                                        };
                                    dataLabelsObj = {
                                                enabled: true,
                                                borderWidth: 0,
                                                style: {
                                                    //fontWeight:'bold',
                                                    fontSize: '18px'
                                                },
                                                y: 70,
                                                formatter: function () {
                                                    var val = this.y;
                                                    return val + udm; 
                                                }
                                            };
                                    break;
                                    
                                case 6:
                                    paneObj = {
                                            startAngle: -130,
                                            endAngle: 130,
                                            size: '105%',
                                            center: ['50%', '52%'],
                                            background: [{
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#FFF'],
                                                            [1, '#333']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#333'],
                                                            [1, '#FFF']
                                                        ]
                                                    },
                                                    borderWidth: 1,
                                                    outerRadius: '100%'
                                                }, {
                                                    // default background
                                                }, {
                                                    backgroundColor: '#DDD',
                                                    borderWidth: 0,
                                                    outerRadius: '100%',
                                                    innerRadius: '100%'
                                                }]
                                        };
                                    dataLabelsObj = {
                                                enabled: true,
                                                borderWidth: 0,
                                                style: {
                                                    //fontWeight:'bold',
                                                    fontSize: '24px'
                                                },
                                                y: 82,
                                                formatter: function () {
                                                    var val = this.y;
                                                    return val + udm; 
                                                }
                                            };
                                    break;

                                default:
                                    paneObj = {
                                            startAngle: -130,
                                            endAngle: 130,
                                            size: '122%',
                                            center: ['50%', '50%'],
                                            background: [{
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#FFF'],
                                                            [1, '#333']
                                                        ]
                                                    },
                                                    borderWidth: 0,
                                                    outerRadius: '100%'
                                                }, {
                                                    backgroundColor: {
                                                        linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                                        stops: [
                                                            [0, '#333'],
                                                            [1, '#FFF']
                                                        ]
                                                    },
                                                    borderWidth: 1,
                                                    outerRadius: '100%'
                                                }, {
                                                    // default background
                                                }, {
                                                    backgroundColor: '#DDD',
                                                    borderWidth: 0,
                                                    outerRadius: '100%',
                                                    innerRadius: '100%'
                                                }]
                                        };
                                    dataLabelsObj = {
                                                enabled: true,
                                                borderWidth: 0,
                                                style: {
                                                    //fontWeight:'bold',
                                                    fontSize: '12px'
                                                },
                                                y: 40,
                                                formatter: function () {
                                                    var val = this.y;
                                                    return val + " " + udm; 
                                                }
                                            };
                                    break;
                            }
                            
                            switch(desc)
                            {
                                    case "Sce_CPU":
                                    value = msg.data[i].commit.author.value_perc1;
                                    value = parseFloat(parseFloat(value).toFixed(10)*100);
                                    value = Math.round(value * 10) / 10;
                                    udm = "%";
                                    threshold[i] = msg.data[i].commit.author.threshold;
                                    thresholdEval[i] = msg.data[i].commit.author.thresholdEval;
                                    $('#measure_<?= $_GET['name'] ?>_value_cpu').empty();
                                    $('#measure_<?= $_GET['name'] ?>_value_cpu').highcharts({
                                        credits: {
                                            enabled: false
                                        },
                                        chart: {
                                            type: 'gauge',
                                            backgroundColor: '<?= $_GET['color'] ?>',
                                            plotBackgroundColor: null,
                                            plotBackgroundImage: null,
                                            plotBorderWidth: 0,
                                            plotShadow: false,
                                        },
                                        plotOptions: {
                                         gauge : {
                                            dial : {
                                               baseWidth: 2,
                                               topWidth: 1
                                            }
                                         }
                                        },    
                                        title: {
                                            text: ''
                                        },
                                        pane: paneObj,
                                        yAxis: {
                                            min: 0,
                                            max: 100,
                                            minorTickInterval: 'auto',
                                            minorTickWidth: 1,
                                            minorTickLength: 5,
                                            minorTickPosition: 'inside',
                                            minorTickColor: '#666',
                                            tickPixelInterval: 20,
                                            tickWidth: 1,
                                            tickPosition: 'inside',
                                            tickLength: 7,
                                            //tickInterval: 2,
                                            tickColor: '#666',
                                            labels: {
                                                enabled: false,
                                                step: 10,
                                                rotation: 'auto'
                                            },
                                            plotBands: [{
                                                from: 0,
                                                to: 70,
                                                color: colors.GREEN,
                                                thickness: 7
                                            },
                                            {
                                                from: 70,
                                                to: 85,
                                                color: colors.ORANGE,
                                                thickness: 7
                                            },
                                            {
                                                from: 85,
                                                to: 100,
                                                color: colors.RED,
                                                thickness: 7
                                            },
                                            ]
                                        },
                                        series: [{
                                            name: '',
                                            data: [value],
                                            tooltip: {
                                                valueSuffix: ''
                                            },
                                            dataLabels: dataLabelsObj,
                                        }],
                                        exporting: {
                                            enabled: false
                                        }
                                    });
                                    
                                    //Valutazione allarme
                                    if((threshold[i] !== null) && (thresholdEval[i] !== null))
                                    {
                                        delta = Math.abs(value - threshold[i]);
                                        //Distinguiamo in base all'operatore di confronto
                                        switch(thresholdEval[i])
                                        {
                                           //Allarme attivo se il valore attuale è sotto la soglia
                                           case '<':
                                               if(value < threshold[i])
                                               {
                                                  alarms["Sce_CPU"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è sopra la soglia
                                           case '>':
                                               if(value > threshold[i])
                                               {
                                                  alarms["Sce_CPU"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                                           case '=':
                                               deltaPerc = (delta / threshold[i])*100;
                                               if(deltaPerc < 0.01)
                                               {
                                                   alarms["Sce_CPU"] = true;
                                                   alarmCount++;
                                               }
                                               break;    

                                           //Non gestiamo altri operatori 
                                           default:
                                               break;
                                        }//Close switch
                                    }//close if
                                    break;

                                case "Sce_Mem":
                                    $('#<?= $_GET['name'] ?>_value_ram_container .loading').css("display", "none");
                                    $('#<?= $_GET['name'] ?>_value_ram_container').addClass("sceValueRoundContainer");
                                    $('#<?= $_GET['name'] ?>_value_ram_round_container').css("display", "");
                                    value = msg.data[i].commit.author.value_perc1;//ATTENZIONE, DOVREBBERO VENIRE DA value_num! ERRATA SCRITTURA DEL BATCH.
                                    value = parseFloat(value / (1024 * 1024 * 1024)).toFixed(1);
                                    udm = "GB";
                                    $("#measure_<?= $_GET['name'] ?>_ram_value_p").html(value);
                                    $("#measure_<?= $_GET['name'] ?>_ram_desc_p").html(udm);
                                    
                                    threshold[i] = msg.data[i].commit.author.threshold;
                                    thresholdEval[i] = msg.data[i].commit.author.thresholdEval;
                                    //Valutazione allarme
                                    if((threshold[i] !== null) && (thresholdEval[i] !== null))
                                    {
                                        delta = Math.abs(value - threshold[i]);
                                        //Distinguiamo in base all'operatore di confronto
                                        switch(thresholdEval[i])
                                        {
                                           //Allarme attivo se il valore attuale è sotto la soglia
                                           case '<':
                                               if(value < threshold[i])
                                               {
                                                  alarms["Sce_Mem"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è sopra la soglia
                                           case '>':
                                               if(value > threshold[i])
                                               {
                                                  alarms["Sce_Mem"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                                           case '=':
                                               deltaPerc = (delta / threshold[i])*100;
                                               if(deltaPerc < 0.01)
                                               {
                                                   alarms["Sce_Mem"] = true;
                                                   alarmCount++;
                                               }
                                               break;    

                                           //Non gestiamo altri operatori 
                                           default:
                                               break;
                                        }//Close switch
                                    }//close if
                                    break;

                                case "Sce_Job_Day":
                                    $('#<?= $_GET['name'] ?>_value_jobs_container .loading').css("display", "none");
                                    $('#<?= $_GET['name'] ?>_value_jobs_container').addClass("sceValueRoundContainer");
                                    $('#<?= $_GET['name'] ?>_value_jobs_round_container').css("display", "");
                                    value = msg.data[i].commit.author.value_num;
                                    udm = "JOBS"
                                    $("#measure_<?= $_GET['name'] ?>_jobs_value_p").html(value);
                                    $("#measure_<?= $_GET['name'] ?>_jobs_desc_p").html(udm);
                                    threshold[i] = msg.data[i].commit.author.threshold;
                                    thresholdEval[i] = msg.data[i].commit.author.thresholdEval;
                                    //Valutazione allarme
                                    if((threshold[i] !== null) && (thresholdEval[i] !== null))
                                    {
                                        delta = Math.abs(value - threshold[i]);
                                        //Distinguiamo in base all'operatore di confronto
                                        switch(thresholdEval[i])
                                        {
                                           //Allarme attivo se il valore attuale è sotto la soglia
                                           case '<':
                                               if(value < threshold[i])
                                               {
                                                  alarms["Sce_Job_Day"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è sopra la soglia
                                           case '>':
                                               if(value > threshold[i])
                                               {
                                                  alarms["Sce_Job_Day"] = true;
                                                  alarmCount++;
                                               }
                                               break;

                                           //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                                           case '=':
                                               deltaPerc = (delta / threshold[i])*100;
                                               if(deltaPerc < 0.01)
                                               {
                                                   alarms["Sce_Job_Day"] = true;
                                                   alarmCount++;
                                               }
                                               break;    

                                           //Non gestiamo altri operatori 
                                           default:
                                               break;
                                        }//Close switch
                                    }//close if
                                    break;

                                default:
                                    break;
                            }//Close switch
                        }//Close for.
                        
                        var blinkFunction = function(blinkTarget)
                        {
                            blinkTarget.toggleClass("desc_text_alr"); 
                        };
                        
                        if(alarms["Sce_CPU"])
                        {
                            descCpu = $("#measure_<?= $_GET['name'] ?>_desc_cpu");
                            descCpu.css({transition: "background 1.8s ease-in-out"});
                            descCpu.css({webkitTransition: "background 1.8s ease-in-out"});
                            descCpu.css({msTransition: "background 1.8s ease-in-out"});
                            alarmIntervals["Sce_CPU"] = setInterval(blinkFunction, 1000, descCpu);
                        }
                        if(alarms["Sce_Mem"])
                        {
                            descRam = $("#measure_<?= $_GET['name'] ?>_desc_ram");
                            descRam.css({transition: "background 1.8s ease-in-out"});
                            descRam.css({webkitTransition: "background 1.8s ease-in-out"});
                            descRam.css({msTransition: "background 1.8s ease-in-out"});
                            alarmIntervals["Sce_Mem"] = setInterval(blinkFunction, 1000, descRam);
                        }
                        if(alarms["Sce_Job_Day"])
                        {
                            descJobs = $("#measure_<?= $_GET['name'] ?>_desc_jobs");
                            descJobs.css({transition: "background 1.8s ease-in-out"});
                            descJobs.css({webkitTransition: "background 1.8s ease-in-out"});
                            descJobs.css({msTransition: "background 1.8s ease-in-out"});
                            alarmIntervals["Sce_Job_Day"] = setInterval(blinkFunction, 1000, descJobs);
                        }
                        
                        $("#<?= $_GET['name'] ?>_date_update").html("Latest Update: " + date_agg);
                        $("#table_<?= $_GET['name'] ?>").css({backgroundColor: '<?= $_GET['color'] ?>'});
                        $("#<?= $_GET['name'] ?>_date_update").css({backgroundColor: '<?= $_GET['color'] ?>'});
                        
                        if (link_w.trim()) 
                        {
                            if(linkElement.length === 0)
                            {
                               linkElement = $("<a id='<?= $_GET['name'] ?>_link_w' href='<?= $_GET['link_w'] ?>' target='_blank' class='elementLink2'>");
                               divChartContainer.wrap(linkElement); 
                            }
                        }
                        
                        $('#source_<?= $_GET['name'] ?>').on('click', function () {
                            $('#dialog_<?= $_GET['name'] ?>').show();

                        });

                        $('#close_popup_<?= $_GET['name'] ?>').on('click', function () {

                            $('#dialog_<?= $_GET['name'] ?>').hide();

                        });

                        var counter = <?= $_GET['freq'] ?>;
                        var countdown = setInterval(function () {
                            $("#countdown_<?= $_GET['name'] ?>").text(counter);
                            counter--;
                            if (counter > 60) {
                                $("#countdown_<?= $_GET['name'] ?>").text(Math.floor(counter / 60) + "m");
                            } else {
                                $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                            }
                            if (counter === 0) {
                                $("#countdown_<?= $_GET['name'] ?>").text(counter + "s");
                                clearInterval(countdown);
                                if(alarms["Sce_CPU"])
                                {
                                    clearInterval(alarmIntervals["Sce_CPU"]);
                                }
                                if(alarms["Sce_Mem"])
                                {
                                    clearInterval(alarmIntervals["Sce_Mem"]);
                                }
                                if(alarms["Sce_Job_Day"])
                                {
                                    clearInterval(alarmIntervals["Sce_Job_Day"]);
                                }
                                setTimeout(<?= $_GET['name'] ?>, 1000);
                            }
                        }, 1000);

                    }//Close success: function(msg)
                });  //Close $.ajax   GETDATAMETRICS  
    
    
                }
        });//FINE GETPARAMETERSWIDGETS       
});//Close $(document).ready   
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id="<?= $_GET['name'] ?>_desc" class="desc"></div><div class="icons-modify-widget"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div><div id="countdown_<?= $_GET['name'] ?>" class="countdown"></div>
        <div id="table_<?= $_GET['name'] ?>" class="table-widget"> 
            <div id="measure_<?= $_GET['name'] ?>_cpu" class="sceSingleContainer">
                <div id="measure_<?= $_GET['name'] ?>_desc_cpu" class="sceDesc">cpu</div> 
                <div id="measure_<?= $_GET['name'] ?>_value_cpu" class="sceValueCpu"></div>
            </div>    
            <div id="measure_<?= $_GET['name'] ?>_ram" class="sceSingleContainer">
                <div id="measure_<?= $_GET['name'] ?>_desc_ram" class="sceDesc">ram</div> 
                    <div id="<?= $_GET['name'] ?>_value_ram_container">
                        <div id="<?= $_GET['name'] ?>_value_ram_round_container" class="sceValueRoundDiv">
                            <div id="measure_<?= $_GET['name'] ?>_ram_value_div" class="sceValue">
                                <p id="measure_<?= $_GET['name'] ?>_ram_value_p" class="sceValueP"></p>
                            </div>
                            <div id="measure_<?= $_GET['name'] ?>_ram_desc_div" class="sceValueDesc">
                                <p id="measure_<?= $_GET['name'] ?>_ram_desc_p" class="sceDescP"></p> 
                            </div>
                        </div>
                        <div class="loading loadingTextDiv"><p>Loading data, please wait</p></div><div class="loading loadingIconDiv"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
            </div>
            <div id="measure_<?= $_GET['name'] ?>_jobs" class="sceSingleContainer"> 
                <div id="measure_<?= $_GET['name'] ?>_desc_jobs" class="sceDesc">daily jobs</div> 
                    <div id="<?= $_GET['name'] ?>_value_jobs_container">
                        <div id="<?= $_GET['name'] ?>_value_jobs_round_container" class="sceValueRoundDiv">
                            <div id="measure_<?= $_GET['name'] ?>_jobs_value_div" class="sceValue">
                                <p id="measure_<?= $_GET['name'] ?>_jobs_value_p" class="sceValueP"></p>
                            </div>
                            <div id="measure_<?= $_GET['name'] ?>_jobs_desc_div" class="sceValueDesc">
                                <p id="measure_<?= $_GET['name'] ?>_jobs_desc_p" class="sceDescP"></p> 
                            </div>
                        </div>
                        <div class="loading loadingTextDiv"><p>Loading data, please wait</p></div><div class="loading loadingIconDiv"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
            </div>
        </div>
        <!-- <div id="<?= $_GET['name'] ?>_date_update" class="date_agg"></div> -->
    </div>
</div>    