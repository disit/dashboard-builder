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
    var parameters = {};
    var barColors = {};
    var barColors = {};

    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId) 
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetColumnContent.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?> 
                
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var showTitle, hasTimer, showHeader, widgetContentColor, chartColor, widgetHeaderColor, widgetHeaderFontColor, fontSize, fontColor, timeToReload, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern,  
            udm, delta, rangeMin, rangeMax, widgetParameters, alarmSet, metricName, widgetTitle, countdownRef, urlToCall, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor,
            geoJsonServiceData, chartRef, webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var barColors = new Array();
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var wsRetryActive, wsRetryTime = null;
        
        var needWebSocket = false;
        
        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName)&&(event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", "black");
        });
        
        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, "<?= sanitizeTitle($_REQUEST['title_w']) ?>", "<?= escapeForJS($_REQUEST['frame_color_w']) ?>", "<?= escapeForJS($_REQUEST['headerFontColor']) ?>", false, null, null, null, null, null, null);
            }
        });
		
	$(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        });
        
        //Definizioni di funzione specifiche del widget
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function populateWidget()
        {
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        var pattern = /Percentuale\//;
                        var metricType = metricData.data[0].commit.author.metricType;
                        var seriesMainData = [];
                        var seriesComplData = [];
                        var plotLinesObj = [];
                        var minGauge, maxGauge, yAxisObj, shownValue, shownValueCompl, plotLineObj = null;
                        var udm = "";
                        var op = null;        
                        
                        //Costruzione delle plot lines delle soglie
                        if(thresholdObject !== null)
                        {
                           for(var i in thresholdObject) 
                           {
                              //Semiretta sinistra
                              if((thresholdObject[i].op === "less")||(thresholdObject[i].op === "lessEqual"))
                              {
                                 if(thresholdObject[i].op === "less")
                                 {
                                    op = "<";
                                 }
                                 else
                                 {
                                    op = "<=";
                                 }
                                 
                                 plotLineObj = {
                                    color: thresholdObject[i].color, 
                                    dashStyle: 'shortdash', 
                                    value: parseFloat(thresholdObject[i].thr1), 
                                    width: 1,
                                    zIndex: 5,
                                    label: {
                                       text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1,
                                       rotation: 0,
                                       y: 12   
                                    }
                                 };
                                 plotLinesObj.push(plotLineObj);
                              }
                              else
                              {
                                 if((thresholdObject[i].op === "greater")||(thresholdObject[i].op === "greaterEqual"))
                                 {
                                    if(thresholdObject[i].op === "greater")
                                    {
                                       op = ">";
                                    }
                                    else
                                    {
                                       op = ">=";
                                    }
                                    
                                    //Semiretta destra
                                    plotLineObj = {
                                       color: thresholdObject[i].color, 
                                       dashStyle: 'shortdash', 
                                       value: parseFloat(thresholdObject[i].thr1), 
                                       width: 1,
                                       zIndex: 5,
                                       label: {
                                          text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1,
                                          rotation: 0
                                       }
                                    };
                                    plotLinesObj.push(plotLineObj);
                                 }
                                 else
                                 {
                                    //Valore uguale a
                                    if(thresholdObject[i].op === "equal")
                                    {
                                       op = "=";
                                       plotLineObj = {
                                          color: thresholdObject[i].color, 
                                          dashStyle: 'shortdash', 
                                          value: parseFloat(thresholdObject[i].thr1), 
                                          width: 1,
                                          zIndex: 5,
                                          label: {
                                             text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1,
                                             rotation: 0,
                                             y: 12 
                                          }
                                       };
                                       plotLinesObj.push(plotLineObj);
                                    }
                                    else
                                    {
                                       //Valore diverso da
                                       if(thresholdObject[i].op === "notEqual")
                                       {
                                          op = "!=";
                                          plotLineObj = {
                                             color: thresholdObject[i].color, 
                                             dashStyle: 'shortdash', 
                                             value: parseFloat(thresholdObject[i].thr1), 
                                             width: 1,
                                             zIndex: 5,
                                             label: {
                                                text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1,
                                                rotation: 0,
                                                y: 12 
                                             }
                                          };
                                          plotLinesObj.push(plotLineObj);
                                       }
                                       else
                                       {
                                          //Intervallo bi-limitato
                                          var op1, op2 = null;
                                          switch(thresholdObject[i].op)
                                          {
                                             case "intervalOpen":
                                                op1 = ">";
                                                op2 = "<";
                                                break;

                                             case "intervalClosed":
                                                op1 = ">=";
                                                op2 = "<=";
                                                break;

                                             case "intervalLeftOpen":
                                                op1 = ">";
                                                op2 = "<=";
                                                break;

                                             case "intervalRightOpen":
                                                op1 = ">=";
                                                op2 = "<";
                                                break;   
                                          }


                                          plotLineObj = {
                                             color: thresholdObject[i].color, 
                                             dashStyle: 'shortdash', 
                                             value: parseFloat(thresholdObject[i].thr1), 
                                             width: 1,
                                             zIndex: 5,
                                             label: {
                                                text: thresholdObject[i].desc + " " + op1 + " " + thresholdObject[i].thr1,
                                                rotation: 0
                                             }
                                          };
                                          plotLinesObj.push(plotLineObj);

                                          plotLineObj = {
                                             color: thresholdObject[i].color, 
                                             dashStyle: 'shortdash', 
                                             value: parseFloat(thresholdObject[i].thr2), 
                                             width: 1,
                                             zIndex: 5,
                                             label: {
                                                text: thresholdObject[i].desc + " " + op2 + " " + thresholdObject[i].thr2,
                                                rotation: 0,
                                                y: 12   
                                             }
                                          };
                                          plotLinesObj.push(plotLineObj);
                                       }
                                    }
                                 }
                              }
                           }
                        }
                        
                        if(pattern.test(metricType))
                        {
                            minGauge = 0;

                            if(metricData.data[0].commit.author.value_perc1 !== null)
                            {
                                maxGauge = 100;
                                udm = "%";
                                shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                if(shownValue > 100)
                                {
                                    shownValue = 100;
                                }
                                shownValueCompl = maxGauge - shownValue;
                                shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                            }
                            else
                            {
                                maxGauge = parseInt(metricType.substring(12));
                                var part = parseFloat(parseFloat(metricData.data[0].commit.author.quant_perc1).toFixed(1));
                                shownValue = (part / maxGauge)*100;
                                shownValueCompl = maxGauge - shownValue;
                                shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                            }

                            yAxisObj = {
                                    visible: true,
                                    offset: 0,
                                    min: minGauge,
                                    max: maxGauge,
                                    //tickInterval: 25,
                                    tickPosition: 'inside',
                                    plotLines: plotLineObj,
                                    title: {
                                        text: ''
                                    },
                                    labels: {
                                        style: {
                                            fontFamily: 'Montserrat'
                                        }
                                    }
                            };
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((rangeMin !== null) && (rangeMax !== null) && (rangeMin !== undefined) && (rangeMax !== undefined))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);
                                    }
                                    else
                                    {
                                        if(parseInt(metricData.data[0].commit.author.value_num) >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = parseInt(metricData.data[0].commit.author.value_num)*1.4;
                                        }

                                        maxGauge = Math.abs(parseInt(metricData.data[0].commit.author.value_num)*1.7);
                                    }

                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseInt(shownValueCompl);
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            tickPosition: 'inside',
                                            plotLines: plotLineObj,
                                            title: {
                                                text: ''
                                            },
                                            labels: {
                                                style: {
                                                    fontFamily: 'Montserrat'
                                                }
                                            }
                                        };
                                    }
                                    break;

                                case "Float":
                                    if((rangeMin !== null) && (rangeMax !== null) && (rangeMin !== undefined) && (rangeMax !== undefined))
                                    {
                                        minGauge = parseInt(rangeMin);
                                        maxGauge = parseInt(rangeMax);    
                                    }
                                    else
                                    {
                                        if(parseInt(metricData.data[0].commit.author.value_num) >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = parseInt(metricData.data[0].commit.author.value_num)*1.4;
                                        }

                                        maxGauge = Math.abs(parseInt(metricData.data[0].commit.author.value_num)*1.7);
                                    }

                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            //tickInterval: 25,
                                            tickPosition: 'inside',
                                            plotLines: plotLinesObj,
                                            title: {
                                                text: ''
                                            },
                                            labels: {
                                                style: {
                                                    fontFamily: 'Montserrat'
                                                }
                                            }    
                                        };
                                    }
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    if(metricData.data[0].commit.author.value_perc1 !== null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                        if(shownValue > 100)
                                        {
                                            shownValue = 100;
                                        }
                                        shownValueCompl = maxGauge - shownValue;
                                        shownValueCompl = parseFloat(parseFloat(shownValueCompl).toFixed(1));
                                        yAxisObj = {
                                            visible: true,
                                            offset: 0,
                                            min: minGauge,
                                            max: maxGauge,
                                            tickInterval: 25,
                                            tickPosition: 'inside',
                                            plotLines: plotLinesObj,
                                            title: {
                                                text: ''
                                            },
                                            labels: {
                                                style: {
                                                    fontFamily: 'Montserrat'
                                                }
                                            }
                                        };
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }

                        if((shownValue !== null) && (shownValue !== undefined) && (minGauge !== null) && (minGauge !== undefined) && (maxGauge !== null) && (maxGauge !== undefined))
                        {   
                           //26/07/2017 - NON CANCELLARE, DA AGGIORNARE QUANDO RIPRISTINIAMO IL BLINK DELLA TESTATA IN CASO DI ALLARME.
                            /*if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme e mostriamo main verde.
                                plotLineObj = null;
                            }
                            else
                            {
                                delta = Math.abs(shownValue - threshold);
                                //Distinguiamo in base all'operatore di confronto

                                switch(thresholdEval)
                                {
                                   //Allarme attivo se il valore attuale è sotto la soglia
                                   case '<':
                                       if(shownValue < threshold)
                                       {
                                          //Allarme
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è sopra la soglia
                                   case '>':
                                       if(shownValue > threshold)
                                       {
                                          //Allarme
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1%)
                                   case '=':
                                       if(delta <= 0.1)
                                       {
                                           //Allarme
                                           alarmSet = true;
                                       }
                                       break;    

                                   //Non gestiamo altri operatori 
                                   default:
                                       break;
                                }
                            } */   

                            //var desc = metricData.data[0].commit.author.descrip;
                            seriesMainData.push(['Green', shownValue]);
                            seriesComplData.push(['Red', shownValueCompl]);

                            yAxisObj = {
                                visible: true,
                                offset: 0,
                                min: minGauge,
                                max: maxGauge,
                                tickPosition: 'inside',
                                plotLines: plotLinesObj,
                                title: {
                                    text: ''
                                },
                                labels: {
                                    style: {
                                        fontFamily: 'Montserrat'
                                    }
                                }
                            };

                            if(firstLoad !== false)
                            {
                                showWidgetContent(widgetName);
                            }
                            else
                            {
                                elToEmpty.empty();    
                            }
                            
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();

                            //Disegno del diagramma
                            chartRef = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                                credits: {
                                    enabled: false
                                },
                                exporting: {
                                    enabled: false
                                },
                                chart: {
                                    type: 'column',
                                    backgroundColor: 'transparent',
                                    spacingBottom: 10,
                                    spacingTop: 10,
                                    events: {
                                        load: function () {
                                            $("#<?= $_REQUEST['name_w'] ?>_chartColorMenuItem").trigger('chartCreated');
                                        }
                                    }
                                },
                                title: {
                                    text: ''
                                },
                                xAxis: {
                                    visible: false
                                },
                                yAxis: yAxisObj,
                                tooltip: {
                                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                                    shared: true,
                                    enabled: false
                                },
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            formatter: function () {
                                                return this.y + udm;
                                            },
                                            enabled: true,
                                            color: fontColor,
                                            style: {
                                                fontFamily: 'Montserrat',
                                                //fontWeight: 'bold',
                                                fontSize: fontSize + "px",
                                                "textOutline": "0px 0px contrast",
                                                /*"text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"*/
                                            }
                                        }
                                    }
                                },
                                series: [
                                    {
                                        showInLegend: false,
                                        name: '<?= escapeForJS($_REQUEST['id_metric']) ?>',
                                        color: chartColor,
                                        data: seriesMainData,
                                        pointWidth: 600
                                    }
                                    ]
                            }); //FINE HIGHCHARTS
                        }
                        else
                        {
                            showWidgetContent(widgetName);
                            if(firstLoad !== false)
                            {
                               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                               $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                           $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                           $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                       $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                   $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            } 
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            var bodyHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - widgetHeaderHeight);
            $("#" + widgetName + "_loading").css("height", bodyHeight + "px");
            $("#" + widgetName + "_content").css("height", bodyHeight + "px");
        }
        //Fine definizioni di funzione 
        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= $_REQUEST['name_w'] ?>"
            },
            async: true,
            dataType: 'json',
            success: function(widgetData) 
            {
                showTitle = widgetData.params.showTitle;
                widgetContentColor = widgetData.params.color_w;
                fontSize = widgetData.params.fontSize;
                fontColor = widgetData.params.fontColor;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                widgetTitle = widgetData.params.title_w;
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize; 
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor; 
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize; 
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                
                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                {
                    showHeader = false;
                }
                else
                {
                    showHeader = true;
                } 
                
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
                    widgetHeaderColor = widgetData.params.frame_color_w;
                    widgetHeaderFontColor = widgetData.params.headerFontColor;
                }
                else
                {
                    metricName = metricNameFromDriver;
                    widgetTitleFromDriver.replace(/_/g, " ");
                    widgetTitleFromDriver.replace(/\'/g, "&apos;");
                    widgetTitle = widgetTitleFromDriver;
                    $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                    widgetHeaderColor = widgetHeaderColorFromDriver;
                    widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
                }
                
                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                if(firstLoad === false)
                {
                    showWidgetContent(widgetName);
                }
                else
                {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }

                //Nuova versione
                if(('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "")&&('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null"))
                {
                    styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
                }

                if('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0)
                {
                    widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
                }

                if(widgetParameters !== null && widgetParameters !== undefined)
                {
                    if(widgetParameters.hasOwnProperty("thresholdObject"))
                    {
                      thresholdObject = widgetParameters.thresholdObject; 
                    }

                    if((widgetParameters.rangeMin !== null) && (widgetParameters.rangeMin !== "") && (widgetParameters.rangeMin !== undefined))
                    {
                         rangeMin = widgetParameters.rangeMin;
                    }

                     if((widgetParameters.rangeMax !== null) && (widgetParameters.rangeMax !== "") && (widgetParameters.rangeMax !== undefined))
                     {
                         rangeMax = widgetParameters.rangeMax;
                     }

                     //I colori andranno spostati in styleParameters appena esponiamo la loro gestione sul form add/edit widget
                     if((widgetParameters.color1 !== null) && (widgetParameters.color1 !== "") && (typeof widgetParameters.color1 !== "undefined")) 
                     {
                         barColors[0] = widgetParameters.color1;
                     }
                     else
                     {
                         barColors[0] = colors.GREEN; 
                     }

                     if((widgetParameters.color2 !== null) && (widgetParameters.color2 !== "") && (typeof widgetParameters.color2 !== "undefined")) 
                     {
                         barColors[1] = widgetParameters.color2;
                     }
                     else
                     {
                         barColors[1] = colors.LOW_YELLOW;
                     }
                }
                else
                {
                    barColors[0] = colors.GREEN;
                    barColors[1] = colors.LOW_YELLOW;
                }

                if(fromGisExternalContent)
                {
                    if((fromGisFakeId !== null) && (fromGisFakeId !== 'null') && (fromGisFakeId !== undefined))
                    {
                        urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + fromGisFakeId;
                    }
                    else
                    {
                        urlToCall = "<?php echo $superServiceMapUrlPrefix; ?>api/v1/?serviceUri=" + fromGisExternalContentServiceUri + "&format=json";
                    }

                    $.ajax({
                        url: urlToCall,
                        type: "GET",
                        data: {},
                        async: true,
                        dataType: 'json',
                        success: function(geoJsonServiceData) 
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').hide();
                            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').show();

                            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').click(function(){
                                if($(this).attr('data-onMap') === 'false')
                                {
                                    if(fromGisMapRef.hasLayer(fromGisMarker))
                                    {
                                        fromGisMarker.fire('click');
                                    }
                                    else
                                    {
                                        fromGisMapRef.addLayer(fromGisMarker);
                                        fromGisMarker.fire('click');
                                    } 
                                    $(this).attr('data-onMap', 'true');
                                    $(this).html('near_me');
                                    $(this).css('color', 'white');
                                    $(this).css('text-shadow', '2px 2px 4px black');
                                }
                                else
                                {
                                    fromGisMapRef.removeLayer(fromGisMarker);
                                    $(this).attr('data-onMap', 'false');
                                    $(this).html('navigation');
                                    $(this).css('color', '#337ab7');
                                    $(this).css('text-shadow', 'none');
                                }
                            });

                            metricData = {  
                                "data":[  
                                   {  
                                      "commit":{  
                                         "author":{  
                                            "IdMetric_data": fromGisExternalContentField,
                                            "computationDate": null,
                                            "value_num":null,
                                            "value_perc1": null,
                                            "value_perc2": null,
                                            "value_perc3": null,
                                            "value_text": null,
                                            "quant_perc1": null,
                                            "quant_perc2": null,
                                            "quant_perc3": null,
                                            "tot_perc1": null,
                                            "tot_perc2": null,
                                            "tot_perc3": null,
                                            "series": null,
                                            "descrip": fromGisExternalContentField,
                                            "metricType": null,
                                            "threshold":null,
                                            "thresholdEval":null,
                                            "field1Desc": null,
                                            "field2Desc": null,
                                            "field3Desc": null,
                                            "hasNegativeValues": "1"
                                         }
                                      }
                                   }
                                ]
                            };

                            var fatherNode = null;
                            if(geoJsonServiceData.hasOwnProperty("BusStop"))
                            {
                                fatherNode = geoJsonServiceData.BusStop;
                            }
                            else
                            {
                                if(geoJsonServiceData.hasOwnProperty("Sensor"))
                                {
                                    fatherNode = geoJsonServiceData.Sensor;
                                }
                                else
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

                            var numberPattern = /^-?\d*\.?\d+$/;
                            var integerPattern = /^[+\-]?\d+$/;

                            if(numberPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                            {
                                if(integerPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                                {
                                    metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                    metricData.data[0].commit.author.metricType = "Intero"; 
                                }
                                else
                                {
                                    metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                    metricData.data[0].commit.author.metricType = "Float"; 
                                }
                            }
                            else
                            {
                                metricData.data[0].commit.author.value_text = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                metricData.data[0].commit.author.metricType = "Testuale";
                            }
                        },
                        error: function(errorData)
                        {
                            console.log("Error in data retrieval");
                            console.log(JSON.stringify(errorData));
                        },
                        complete: function()
                        {
                            populateWidget(); 
                        }
                    });
                }
                else
                {
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();

                    $.ajax({
                        url: getMetricDataUrl,
                        type: "GET",
                        data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            metricData = data;
                            needWebSocket = metricData.data[0].needWebSocket;
                            $("#" + widgetName + "_loading").css("display", "none");
                            $("#" + widgetName + "_content").css("display", "block");
                            populateWidget();
                            
                            if(needWebSocket)
                            {
                                openWs();
                            }  
                        },
                        error: function()
                        {
                            metricData = null;
                            console.log("Error in data retrieval");
                            console.log(JSON.stringify(errorData));
                            if(firstLoad !== false)
                            {
                               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                               $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                               $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            }
                        }
                    });
                }

                //Web socket 
                openWs = function(e)
                {
                    try
                    {
                        <?php
                            $genFileContent = parse_ini_file("../conf/environment.ini");
                            $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                            $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                            $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                            $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                            $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                            $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
                            $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
                            echo 'wsRetryActive = "' . $wsRetryActive . '";';
                            echo 'wsRetryTime = ' . $wsRetryTime . ';';
                            echo 'webSocket = new WebSocket("' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';
                        ?>

                        webSocket.addEventListener('open', openWsConn);
                        webSocket.addEventListener('close', wsClosed);

                        setTimeout(function(){
                            webSocket.close();
                            webSocket.removeEventListener('close', wsClosed);
                            webSocket.removeEventListener('open', openWsConn);
                            webSocket.removeEventListener('message', manageIncomingWsMsg);
                            webSocket = null;
                        }, (timeToReload - 2)*1000);
                    }
                    catch(e)
                    {
                        wsClosed();
                    }
                };

                manageIncomingWsMsg = function(msg)
                {
                    var msgObj = JSON.parse(msg.data);

                    switch(msgObj.msgType)
                    {
                        case "newNRMetricData":
                            if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                            {
                                var newValue = msgObj.newValue;
                                var point = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().series[0].points[0];       
                                point.update(newValue);                         
                            }
                            break;

                        default:
                            break;
                    }
                };

                openWsConn = function(e)
                {
                    var wsRegistration = {
                        msgType: "ClientWidgetRegistration",
                        userType: "widgetInstance",
                        metricName: encodeURIComponent(metricName),
                        widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
                      };
                      webSocket.send(JSON.stringify(wsRegistration));

                      setTimeout(function(){
                          webSocket.removeEventListener('close', wsClosed);
                          webSocket.close();
                      }, (timeToReload - 2)*1000);

                    webSocket.addEventListener('message', manageIncomingWsMsg);
                };

                wsClosed = function(e)
                {
                    webSocket.removeEventListener('close', wsClosed);
                    webSocket.removeEventListener('open', openWsConn);
                    webSocket.removeEventListener('message', manageIncomingWsMsg);
                    webSocket = null;
                    if(wsRetryActive === 'yes')
                    {
                        setTimeout(openWs, parseInt(wsRetryTime*1000));
                    }		
                };

                //Per ora non usata
                wsError = function(e)
                {
                    
                };
                $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                    resizeWidget();
                     $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
                });	

                $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
                $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                    clearInterval(countdownRef);
                    timeToReload = event.newTimeToReload;
                    countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
                });

                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            },
            error: function(errorData)
            {
        
            }
        });
        
        
        
        
    });//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 