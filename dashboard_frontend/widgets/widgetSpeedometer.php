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

<script type="text/javascript">
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };


    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId) 
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetSpeedometer.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?> 
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>";
        var wsRetryActive, wsRetryTime, chartRef = null;
        var widgetProperties, fontSize, chartColor, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showTitle, showHeader, hasTimer, thresholdObject, styleParameters, metricType, metricData, pattern, udm,
            widgetParameters, paneObj, minGauge, maxGauge, shownValue, plotOptionsObj, sizeRowsWidget, originalMetricType, alarmSet, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor,
            plotBandObj, plotBandSet, hasNegativeValues, metricName, widgetTitle, countdownRef, udmObj, timeToReload, 
            chart, widgetParameters, sm_based, rowParameters, sm_field, webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer");
        var hasThresholds = false;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var needWebSocket = false;
        
        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null, null);
            }
        });
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", "black");
        });
        
        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", widgetHeaderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, "<?= sanitizeTitle($_REQUEST['title_w']) ?>", "<?= escapeForJS($_REQUEST['frame_color_w']) ?>", "<?= escapeForJS($_REQUEST['headerFontColor']) ?>", false, null, null, /*null,*/ null, null, null);
            }
        });
		
	$(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
        });
        
        //Definizioni di funzione specifiche del widget
        
        function populateWidget()
        {
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        pattern = /Percentuale\//;
                        metricType = metricData.data[0].commit.author.metricType;
                        hasNegativeValues = parseInt(metricData.data[0].commit.author.hasNegativeValues);
                        
                        if(pattern.test(metricType))
                        {
                            minGauge = 0;
                            maxGauge = parseInt(metricType.substring(12));
                            if(metricData.data[0].commit.author.quant_perc1 !== null)
                            {
                                shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.quant_perc1).toFixed(1));
                            }
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    /*if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                       shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    else
                                    {
                                       shownValue = 0;
                                    }
                                    
                                    if(hasNegativeValues === 0)
                                    {
                                       minGauge = 0;
                                    }
                                    else
                                    {
                                       minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                    }
                           
                                    
                                    if(minGauge > 0)
                                    {
                                       minGauge = 0;
                                    }
                                    maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10);*/
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    
                                    if(fromGisExternalContent)
                                    {
                                        if(shownValue >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue*1.4);
                                        }

                                        maxGauge = Math.floor(Math.abs(shownValue*1.7));
                                    }
                                    else
                                    {
                                        if(hasNegativeValues === 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                        }

                                        if(minGauge > 0)
                                        {
                                           minGauge = 0;
                                        }

                                        maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10); 
                                    }
                                    break;

                                case "Float":
                                    /*if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    else
                                    {
                                       shownValue = 0;
                                    }
                                    
                                    if(hasNegativeValues === 0)
                                    {
                                       minGauge = 0;
                                    }
                                    else
                                    {
                                       minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                    }
                                    
                                    if(minGauge > 0)
                                    {
                                       minGauge = 0;
                                    }
                                    
                                    maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10);*/
                                    if(metricData.data[0].commit.author.value_num !== null)
                                    {
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    
                                    if(fromGisExternalContent)
                                    {
                                        if(shownValue >= 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue*1.4);
                                        }

                                        maxGauge = Math.floor(Math.abs(shownValue*1.7));
                                    }
                                    else
                                    {
                                        if(hasNegativeValues === 0)
                                        {
                                           minGauge = 0;
                                        }
                                        else
                                        {
                                           minGauge = Math.floor(shownValue - (Math.random() * 25) - 10);
                                        }

                                        if(minGauge > 0)
                                        {
                                           minGauge = 0;
                                        }

                                        maxGauge = Math.floor(shownValue + (Math.random() * 25) + 10); 
                                    }
                                    break;

                                case "Percentuale":
                                    minGauge = 0;
                                    maxGauge = 100;
                                    if(metricData.data[0].commit.author.value_perc1 !== null)
                                    {
                                        udm = "%";
                                        shownValue = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }
                        
                        //innerRadius = "96%";
                        //outerRadius = "100%";

                        // Set max value as in more options
                        if (styleParameters) {
                            if (styleParameters['setMaxValue'] != null) {
                                if (styleParameters['setMaxValue'] != "") {
                                    maxGauge = parseFloat(styleParameters['setMaxValue']);
                                }
                            }
                            if (styleParameters['setMinValue'] != null) {
                                if (styleParameters['setMinValue'] != "") {
                                    minGauge = parseFloat(styleParameters['setMinValue']);
                                }
                            }
                        }

                        //Controllo tipo metrica non compatibile col widget
                        if((shownValue !== null) && (minGauge !== null) && (maxGauge !== null))
                        {
                           if(hasThresholds)
                           {
                              //Applicazione soglie
                              plotBandSet = [];
                              plotBandObj = null;//Va resettato ad ogni iterazione, per evitare di inserire bande colorate fuori scala e rovinare il widget
                              
                              var op, op1, op2 = null;
                              
                              for(var i = 0; i < thresholdObject.length; i++) 
                              {
                                 //Semiretta sinistra
                                 if(((thresholdObject[i].op === "less")||(thresholdObject[i].op === "lessEqual"))&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    if(thresholdObject[i].op === "less")
                                    {
                                       op = "<";
                                    }
                                    else
                                    {
                                       op = "<=";
                                    }

                                    if(thresholdObject[i].thr1 >= maxGauge)
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: maxGauge, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }
                                    else
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: thresholdObject[i].thr1, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }
                                 }
                                 
                                 //Semiretta destra
                                 if(((thresholdObject[i].op === "greater")||(thresholdObject[i].op === "greaterEqual"))&&(parseFloat(thresholdObject[i].thr1) <= maxGauge))
                                 {
                                    if(thresholdObject[i].thr1 <= minGauge)
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: maxGauge, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                       };
                                    }
                                    else
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: maxGauge, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }
                                 }
                                 
                                 //Uguale a
                                 if((thresholdObject[i].op === "equal")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                    var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       

                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minLine, 
                                       to: maxLine, 
                                       //innerRadius: innerRadius,
                                       //outerRadius: outerRadius
                                    };
                                 }
                                 
                                 //Diverso da
                                 if((thresholdObject[i].op === "notEqual")&&(parseFloat(thresholdObject[i].thr1) <= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                 {
                                    var minLine = parseFloat(thresholdObject[i].thr1) - Math.abs(maxGauge - minGauge)*0.0025;
                                    var maxLine = parseFloat(thresholdObject[i].thr1) + Math.abs(maxGauge - minGauge)*0.0025;       

                                    plotBandObj  = {
                                       color: thresholdObject[i].color, 
                                       from: minLine, 
                                       to: maxLine, 
                                       //innerRadius: innerRadius,
                                       //outerRadius: outerRadius
                                    };                                  
                                 }
                                 
                                 //Intervallo bi-limitato
                                 if(((thresholdObject[i].op === "intervalOpen")||(thresholdObject[i].op === "intervalClosed")||(thresholdObject[i].op === "intervalLeftOpen")||(thresholdObject[i].op === "intervalRightOpen"))&&(!((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].thr2) <= minGauge)))&&(!((parseFloat(thresholdObject[i].thr1) >= maxGauge)&&(parseFloat(thresholdObject[i].thr2) >= maxGauge))))
                                 {
                                    //Sforamento inferiore
                                    if((parseFloat(thresholdObject[i].thr1) <= minGauge)&&(parseFloat(thresholdObject[i].max) < maxGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: thresholdObject[i].thr2, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }

                                    //Sforamento superiore
                                    if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) >= minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: maxGauge, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }

                                    //Sforamento da ambo i lati
                                    if((parseFloat(thresholdObject[i].thr2) >= maxGauge)&&(parseFloat(thresholdObject[i].thr1) <= minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: minGauge, 
                                          to: maxGauge, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }

                                    //Nessun sforamento
                                    if((parseFloat(thresholdObject[i].thr2) < maxGauge)&&(parseFloat(thresholdObject[i].thr1) > minGauge))
                                    {
                                       plotBandObj  = {
                                          color: thresholdObject[i].color, 
                                          from: thresholdObject[i].thr1, 
                                          to: thresholdObject[i].thr2, 
                                          //innerRadius: innerRadius,
                                          //outerRadius: outerRadius
                                      };
                                    }
                                    //In tutti gli altri casi (cioé con la banda interamente sopra il massimo o interamente sotto il minimo) non disegnamo banda colorata.
                                 }
                                 
                                 if(plotBandObj !== null)
                                 {
                                    plotBandSet.push(plotBandObj);
                                 }
                              }
                           }
                           else
                           {
                              //Nessuna soglia, usiamo un colore di default
                              plotBandSet = [{
                                 from: minGauge,
                                 to: maxGauge,
                                 color: chartColor,
                                 id: "noThrPlotBand"
                              }];
                           }
                           
                           //NON CANCELLARE - Da riusare quando ripristiniamo il blink ad allarme attivo.
                            /*if((threshold === null) || (thresholdEval === null))
                            {
                                //In questo caso non mostriamo soglia d'allarme.
                                threshold = 0;

                                //Per qualsiasi combinazione non prevista impostiamo un unico colore (verde)
                                plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];

                                if((limSup1 === null) && (limSup2 === null))
                                {
                                    plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];
                                }
                                else
                                {
                                    if(limSup1 !== null)
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: limSup1,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup1,
                                            to: maxGauge,
                                            color: chartColors[1],
                                            thickness: thicknessVal
                                        }
                                        ];
                                    }
                                    if((limSup1 !== null) && (limSup2 !== null))
                                    {
                                        plotBandSet = [
                                        {  
                                            from: minGauge,
                                            to: limSup1,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup1,
                                            to: limSup2,
                                            color: chartColors[1],
                                            thickness: thicknessVal
                                        },
                                        {  
                                            from: limSup2,
                                            to: maxGauge,
                                            color: chartColors[2],
                                            thickness: thicknessVal
                                        }        
                                        ];
                                    }
                                }
                            }
                            else
                            {
                                //Distinguiamo in base all'operatore di confronto
                                switch(thresholdEval)
                                {
                                    //Allarme attivo se il valore attuale è sotto la soglia
                                    case '<':
                                        if(shownValue < threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            },        
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è sopra la soglia
                                    case '>':
                                        if(shownValue > threshold)
                                        {
                                           //Allarme
                                           //alarmSet = true;
                                        }

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: threshold,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }, 
                                            {
                                                from: threshold,
                                                to: maxGauge,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;

                                    //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.01%)
                                    case '=':
                                        delta = Math.abs(shownValue - threshold);
                                        deltaPerc = ((delta / threshold)*100);

                                        if(deltaPerc <= 0.01)
                                        {
                                            //Allarme
                                            //alarmSet = true;
                                        }

                                        var incAlr = parseInt(threshold*0.05);
                                        var infAlr = threshold-incAlr;
                                        var supAlr = parseInt(threshold) + incAlr;

                                        plotBandSet = [
                                            {
                                                from: minGauge,
                                                to: infAlr,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: infAlr,
                                                to: supAlr,
                                                color: chartColors[2],
                                                thickness: thicknessVal
                                            },
                                            {
                                                from: supAlr,
                                                to: maxGauge,
                                                color: chartColors[0],
                                                thickness: thicknessVal
                                            }
                                        ];
                                        break;    

                                    //Non gestiamo altri operatori 
                                    default:
                                        threshold = 0;
                                        plotBandSet = [{
                                            from: minGauge,
                                            to: maxGauge,
                                            color: chartColors[0],
                                            thickness: thicknessVal
                                        }];
                                        break;
                                 }
                            }*/

                                plotOptionsObj = {
                                    gauge : {
                                        dial : {
                                            baseWidth: 2,
                                            topWidth: 1
                                        }
                                    }
                                };

                                paneObj = [{
                                    startAngle: -135,
                                    endAngle: 135,
                                    size: '98%',
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
                                }];

                                dataLabelsObj = {
                                    enabled: true,
                                    style: {
                                        fontWeight: 'bold',
                                        fontSize: '12px',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                        "textOutline": "1px 1px contrast",
                                        fontFamily: 'Verdana'
                                    },
                                    borderWidth: 0,
                                    //y: parseInt($("#" + widgetName + "_div").prop("offsetHeight") - widgetHeaderHeight)*0.4,
                                    format: '<div style="text-align:center"><span>{y}</span>'
                                };

                                yAxisObj = {
                                    min: minGauge,
                                    max: maxGauge,
                                    minorTickInterval: 'auto',
                                    minorTickWidth: 1,
                                    minorTickLength: 4,
                                    minorTickPosition: 'outside',
                                    minorTickColor: '#666',
                                    tickPixelInterval: 30,
                                    tickWidth: 2,
                                    tickPosition: 'outside',
                                    tickLength: 6,
                                    //tickInterval: 2,
                                    tickColor: '#666',
                                    labels: {
                                        step: 2,
                                        //rotation: 'auto',
                                        distance: -20,
                                        style: {
                                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.20)",
                                            "textOutline": "1px 1px contrast",
                                            fontFamily: 'Verdana'
                                        }
                                    },
                                    title: udmObj,
                                    plotBands: plotBandSet 
                                };             

                            if(firstLoad !== false)
                            {
                                showWidgetContent(widgetName);
                            }
                            else
                            {
                                elToEmpty.empty();
                            }
                            
                            if(metricType === "Testuale")
                            {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                            }
                            else
                            {
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                                
                                //Disegno del diagramma
                                chartRef = Highcharts.chart('<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer', {
                                    credits: {
                                        enabled: false
                                    },
                                    chart: {
                                        type: 'gauge',
                                        backgroundColor: 'transparent',
                                        plotBackgroundColor: null,
                                        plotBackgroundImage: null,
                                        plotBorderWidth: 0,
                                        plotShadow: false,
                                        events: {
                                            load: function () {
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartColorMenuItem").trigger('chartCreated');
                                            }
                                        }
                                    },
                                    //NON RIMUOVERE        
                                    title: {
                                        text: ''
                                    },
                                    pane: paneObj,
                                    plotOptions: plotOptionsObj,
                                    yAxis: yAxisObj,
                                    series: [{
                                        data: [shownValue],
                                        tooltip: {
                                            enabled: false
                                        },
                                        dataLabels: dataLabelsObj
                                    }],
                                    exporting: {
                                        enabled: false
                                    }
                                });
                            }
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                           $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                    }  
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                    }
                }
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
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
                widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
            },
            async: true,
            dataType: 'json',
            success: function(widgetData) 
            {
                showTitle = widgetData.params.showTitle;
                widgetContentColor = widgetData.params.color_w;
                fontSize = widgetData.params.fontSize;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                widgetTitle = widgetData.params.title_w;
                widgetHeaderColor = widgetData.params.frame_color_w;
                widgetHeaderFontColor = widgetData.params.headerFontColor;
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize; 
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor; 
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize; 
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                sm_based = widgetData.params.sm_based;
                rowParameters = widgetData.params.rowParameters;
                sm_field = widgetData.params.sm_field;
                
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
                    widgetTitle = widgetData.params.title_w;
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
        
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

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

                udm = "<?= escapeForJS($_REQUEST['udm']) ?>";
                sizeRowsWidget = parseInt("<?= escapeForJS($_REQUEST['size_rows']) ?>");

                if(widgetParameters !== null && widgetParameters !== undefined)
                {
                    if(widgetParameters.hasOwnProperty("thresholdObject"))
                    {
                       thresholdObject = widgetParameters.thresholdObject; 
                       hasThresholds = true;
                    }
                }

                if(fromGisExternalContent)
                {
                    urlToCall = "<?= $superServiceMapProxy; ?>api/v1/?serviceUri=" + fromGisExternalContentServiceUri + "&format=json";

                    $.ajax({
                        url: urlToCall,
                        type: "GET",
                        data: {},
                        async: true,
                        dataType: 'json',
                        success: function(geoJsonServiceData) 
                        {
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').show();

                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').off('click');
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').click(function(){
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
                            if(udm !== null)
                            {
                                udmObj = {
                                    text: udm
                                }
                            }
                            populateWidget(); 
                        }
                    });
                }
                else
                {
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                    
                    switch(sm_based)
                    {
                        case 'yes':
                            $.ajax({
                                url: "<?= $superServiceMapProxy?>"+rowParameters,
                                type: "GET",
                                data: {},
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                   /* var originalMetricType = data.Service.features[0].properties.realtimeAttributes[sm_field].data_type;
                                    udm = data.Service.features[0].properties.realtimeAttributes[sm_field].value_unit;*/
                                    if (data.Service) {
                                        var originalMetricType = data.Service.features[0].properties.realtimeAttributes[sm_field].data_type;
                                        //    udm = data.Service.features[0].properties.realtimeAttributes[sm_field].value_unit;
                                    } else if (data.Sensor) {
                                        var originalMetricType = data.Sensor.features[0].properties.realtimeAttributes[sm_field].data_type;
                                        //   udm = data.Sensor.features[0].properties.realtimeAttributes[sm_field].value_unit;
                                    }
                                    
                                    metricData = {  
                                        data:[  
                                           {  
                                              commit:{  
                                                 author:{  
                                                    IdMetric_data: sm_field,
                                                    computationDate: null,
                                                    value_num:null,
                                                    value_perc1: null,
                                                    value_perc2: null,
                                                    value_perc3: null,
                                                    value_text: null,
                                                    quant_perc1: null,
                                                    quant_perc2: null,
                                                    quant_perc3: null,
                                                    tot_perc1: null,
                                                    tot_perc2: null,
                                                    tot_perc3: null,
                                                    series: null,
                                                    descrip: sm_field,
                                                    metricType: null,
                                                    threshold:null,
                                                    thresholdEval:null,
                                                    field1Desc: null,
                                                    field2Desc: null,
                                                    field3Desc: null,
                                                    hasNegativeValues: "1"
                                                 }
                                              }
                                           }
                                        ]
                                    };
                                    
                                    switch(originalMetricType)
                                    {
                                        case "float":
                                            metricData.data[0].commit.author.metricType = "Float";
                                            metricData.data[0].commit.author.value_num = parseFloat(data.realtime.results.bindings[0][sm_field].value);
                                            break;
                                            
                                        case "integer":
                                            metricData.data[0].commit.author.metricType = "Intero";
                                            metricData.data[0].commit.author.value_num = parseInt(data.realtime.results.bindings[0][sm_field].value);
                                            break;
                                            
                                        default:
                                            metricData.data[0].commit.author.metricType = "Testuale";
                                            metricData.data[0].commit.author.value_text = data.realtime.results.bindings[0][sm_field].value;
                                            break;    
                                    }
                                    
                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    if(udm !== null)
                                    {
                                        udmObj = {
                                            text: udm
                                        }
                                    }
                                    populateWidget();
                                },
                                error: function(errorData)
                                {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    if(firstLoad !== false)
                                    {
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });
                            break;

                        case 'no':
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
                                    if(udm !== null)
                                    {
                                        udmObj = {
                                            text: udm
                                        }
                                    }
                                    populateWidget();
                                    
                                    if(needWebSocket)
                                    {
                                        openWs();
                                    }
                                },
                                error: function(errorData)
                                {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    if(firstLoad !== false)
                                    {
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });
                            break;

                        case 'myPersonalData':
                            $.ajax({
                                url: "../controllers/myPersonalDataProxy.php?variableName=" + sm_field + "&last=1",
                                type: "GET",
                                data: {},
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    if(parseFloat(data[0].variableValue) !== 'NaN')
                                    {
                                        originalMetricType = 'float';
                                    }
                                    else
                                    {
                                        if(parseInt(data[0].variableValue) !== 'NaN')
                                        {
                                            originalMetricType = 'integer';
                                        }
                                        else
                                        {
                                            originalMetricType = 'string';
                                        }
                                    }

                                    udm = data[0].variableUnit;

                                    metricData = {  
                                        data:[  
                                           {  
                                              commit:{  
                                                 author:{  
                                                    IdMetric_data: sm_field,
                                                    computationDate: null,
                                                    value_num:null,
                                                    value_perc1: null,
                                                    value_perc2: null,
                                                    value_perc3: null,
                                                    value_text: null,
                                                    quant_perc1: null,
                                                    quant_perc2: null,
                                                    quant_perc3: null,
                                                    tot_perc1: null,
                                                    tot_perc2: null,
                                                    tot_perc3: null,
                                                    series: null,
                                                    descrip: sm_field,
                                                    metricType: null,
                                                    threshold:null,
                                                    thresholdEval:null,
                                                    field1Desc: null,
                                                    field2Desc: null,
                                                    field3Desc: null,
                                                    hasNegativeValues: "1"
                                                 }
                                              }
                                           }
                                        ]
                                    };

                                    switch(originalMetricType)
                                    {
                                        case "float":
                                            metricData.data[0].commit.author.metricType = "Float";
                                            metricData.data[0].commit.author.value_num = parseFloat(data[0].variableValue);
                                            break;

                                        case "integer":
                                            metricData.data[0].commit.author.metricType = "Intero";
                                            metricData.data[0].commit.author.value_num = parseInt(data[0].variableValue);
                                            break;

                                        default:
                                            metricData.data[0].commit.author.metricType = "Testuale";
                                            metricData.data[0].commit.author.value_text = data[0].variableValue;
                                            break;    
                                    }

                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    if(udm !== null)
                                    {
                                        udmObj = {
                                            text: udm
                                        }
                                    }
                                    populateWidget();
                                },
                                error: function(errorData)
                                {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    if(firstLoad !== false)
                                    {
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });
                            break;

                        case 'myKPI':
                            $.ajax({
                                url: "../controllers/myKpiProxy.php",
                                type: "GET",
                                data: {
                                    myKpiId: rowParameters,
                                    //    action: "getValueUnit"
                                    last: 1
                                },
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(parseFloat(data[0].value) !== 'NaN')
                                    {
                                        originalMetricType = 'float';
                                    }
                                    else
                                    {
                                        if(parseInt(data[0].value) !== 'NaN')
                                        {
                                            originalMetricType = 'integer';
                                        }
                                        else
                                        {
                                            originalMetricType = 'string';
                                        }
                                    }

                                    udm = data[0].variableUnit;

                                    metricData = {
                                        data:[
                                            {
                                                commit:{
                                                    author:{
                                                        IdMetric_data: sm_field,
                                                        computationDate: null,
                                                        value_num:null,
                                                        value_perc1: null,
                                                        value_perc2: null,
                                                        value_perc3: null,
                                                        value_text: null,
                                                        quant_perc1: null,
                                                        quant_perc2: null,
                                                        quant_perc3: null,
                                                        tot_perc1: null,
                                                        tot_perc2: null,
                                                        tot_perc3: null,
                                                        series: null,
                                                        descrip: sm_field,
                                                        metricType: null,
                                                        threshold:null,
                                                        thresholdEval:null,
                                                        field1Desc: null,
                                                        field2Desc: null,
                                                        field3Desc: null,
                                                        hasNegativeValues: "1"
                                                    }
                                                }
                                            }
                                        ]
                                    };

                                    switch(originalMetricType)
                                    {
                                        case "float":
                                            metricData.data[0].commit.author.metricType = "Float";
                                            metricData.data[0].commit.author.value_num = parseFloat(data[0].value);
                                            break;

                                        case "integer":
                                            metricData.data[0].commit.author.metricType = "Intero";
                                            metricData.data[0].commit.author.value_num = parseInt(data[0].value);
                                            break;

                                        default:
                                            metricData.data[0].commit.author.metricType = "Testuale";
                                            metricData.data[0].commit.author.value_text = data[0].value;
                                            break;
                                    }

                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    populateWidget();
                                },
                                error: function(errorData)
                                {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    if(firstLoad !== false)
                                    {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });
                            break;

                    }
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
                            webSocket.removeEventListener('close', wsClosed);
                            webSocket.removeEventListener('open', openWsConn);
                            webSocket.removeEventListener('message', manageIncomingWsMsg);
                            webSocket.close();
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
                                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

                                var newValue = msgObj.newValue;
                                var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];       
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

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
                    resizeWidget();
                     $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
                });	

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").off('updateFrequency');
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('updateFrequency', function(event){
                        clearInterval(countdownRef);
                        timeToReload = event.newTimeToReload;
                        countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);  
                });

                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);    
                
                
                
            },
            error: function(errorData)
            {
        
            }
        });
    });//Fine document.ready            
</script>
<div class="widget" id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 
        
