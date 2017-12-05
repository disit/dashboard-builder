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
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<script type='text/javascript'>
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };
    
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_GET['title'];
        ?>
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, sizeRowsWidget, widgetColor, fontSize,rowColor, rowColorRgb, value, sizeRowsWidget,
            idMetric, descCpu, descRam, descJobs, fontRatio, fontRatioValue, fontRatioValueDesc, circleHeight,
            alarmCount, paneObj, dataLabelsObj, ramValueClass, ramUdmClass, jobsValueClass, jobsUdmClass = null;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        //Specifiche per questo widget
        var metricsList = "<?= $_GET['metric'] ?>";
        var metrics = metricsList.split('+');
        var metricsTypesList = "<?= $_GET['type_metric'] ?>";
        var metricsTypes = metricsTypesList.split(',');
        var metricsToShow = [];
        var alarmIntervals = [];
        
        if(url === "null")
        {
            url = null;
        }
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
		showHeader = false;
	}
	else
	{
		showHeader = true;
	}
        
        //Definizioni di funzione specifiche del widget
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== ''))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            widgetParameters = widgetProperties.param.parameters;
            idMetric = widgetProperties.param.id_metric;
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            
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
                        threshold = [];
                        thresholdEval = [];
                        alarmCount = 0;
                        var alarms = [];
                        var desc2Blink = [];

                        computationDate = msg.data[0].commit.author.computationDate;
                        
                        alarms["Sce_CPU"] = false;
                        alarms["Sce_Mem"] = false;
                        alarms["Sce_Job_Day"] = false;

                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                        }
                        
                        $('#measure_<?= $_GET['name'] ?>_value_cpu').html("<div style='width: 100%; height: 50%; display: flex; align-items: flex-end; justify-content: center; text-align: center;'><p style='text-align: center; font-size: 13px;'>Loading data, please wait</p></div><div style='width: 100%; height: 50%; display: flex; align-items: baseline; justify-content: center; text-align: center;'><i class='fa fa-spinner fa-spin' style='font-size: 20px'></i></div>");
                        $('#<?= $_GET['name'] ?>_value_ram_container').css("height", "80%");
                        $('#<?= $_GET['name'] ?>_value_ram_container').css("width", "100%");
                        $('#<?= $_GET['name'] ?>_value_ram_round_container').css("display", "none");
                        $('#<?= $_GET['name'] ?>_value_jobs_container').css("height", "80%");
                        $('#<?= $_GET['name'] ?>_value_jobs_container').css("width", "100%");
                        $('#<?= $_GET['name'] ?>_value_jobs_round_container').css("display", "none");

                        sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                        
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
                                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                                    "textOutline": "1px 1px contrast",
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
                                            plotShadow: false
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
                                            }
                                            ]
                                        },
                                        series: [{
                                            name: '',
                                            data: [value],
                                            tooltip: {
                                                valueSuffix: ''
                                            },
                                            dataLabels: dataLabelsObj
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
                                    udm = "JOBS";
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
                        
                        
                        if(alarms["Sce_CPU"])
                        {
                            $("#measure_<?= $_GET['name'] ?>_desc_cpu").find(".alarmDivGc").addClass("alarmDivGcActive");
                        }
                        if(alarms["Sce_Mem"])
                        {
                            $("#measure_<?= $_GET['name'] ?>_desc_ram").find(".alarmDivGc").addClass("alarmDivGcActive");  
                        }
                        if(alarms["Sce_Job_Day"])
                        {
                            $("#measure_<?= $_GET['name'] ?>_desc_jobs").find(".alarmDivGc").addClass("alarmDivGcActive");
                        }
                        
                    },//Close success: function(msg)
                    error: function(){
                        showWidgetContent(widgetName);
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
            });//Close $.ajax GETDATAMETRICS  
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });//Fine document ready       

</script>
<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_GET['name'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_GET['name'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>
        
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="content">
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer" style="font-size: 28px;"> 
                <div id="measure_<?= $_GET['name'] ?>_cpu" class="sceSingleContainer">
                    <div id="measure_<?= $_GET['name'] ?>_desc_cpu" class="sceDesc"><div class="alarmDivGc">cpu</div></div> 
                    <div id="measure_<?= $_GET['name'] ?>_value_cpu" class="sceValueCpu"></div>
                </div>    
                <div id="measure_<?= $_GET['name'] ?>_ram" class="sceSingleContainer">
                    <div id="measure_<?= $_GET['name'] ?>_desc_ram" class="sceDesc"><div class="alarmDivGc">ram</div></div> 
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
                    <div id="measure_<?= $_GET['name'] ?>_desc_jobs" class="sceDesc"><div class="alarmDivGc">daily jobs</div></div> 
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
        </div>
    </div>	
</div> 