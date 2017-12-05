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
    var parameters = {};
    var barColors = {};
    var barColors = {};

    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId) 
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
        var widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern,  
            udm, delta, rangeMin, rangeMax, widgetParameters, alarmSet, metricName, widgetTitle, countdownRef, urlToCall, geoJsonServiceData = null;
        var metricName = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var barColors = new Array();
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
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
        
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= $_GET['metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
            widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
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
        
        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName)&&(event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= $_GET['name'] ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= $_GET['name'] ?>_header").css("background", event.color1);
            $("#<?= $_GET['name'] ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_GET['name'] ?>_header").css("color", "black");
        });
        
        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_GET['name'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_GET['name'] ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_GET['frame_color'] ?>", "<?= $_GET['headerFontColor'] ?>", false, null, null, null, null, null, null);
            }
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
                            
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();

                            //Disegno del diagramma
                            $('#<?= $_GET['name'] ?>_chartContainer').highcharts({
                                credits: {
                                    enabled: false
                                },
                                exporting: {
                                    enabled: false
                                },
                                chart: {
                                    type: 'column',
                                    backgroundColor: '<?= $_GET['color'] ?>',
                                    spacingBottom: 10,
                                    spacingTop: 10
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
                                                fontFamily: 'Verdana',
                                                fontWeight: 'bold',
                                                fontSize: fontSize + "px",
                                                "textOutline": "1px 1px contrast",
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.3)"
                                            }
                                        }
                                    }
                                },
                                series: [
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: barColors[1],
                                        data: seriesComplData,
                                        pointWidth: 600
                                    },
                                    {
                                        showInLegend: false,
                                        name: '<?= $_GET['metric'] ?>',
                                        color: barColors[0],
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
                               $("#<?= $_GET['name'] ?>_chartContainer").hide();
                               $('#<?= $_GET['name'] ?>_noDataAlert').show();
                            }
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                           $("#<?= $_GET['name'] ?>_chartContainer").hide();
                           $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_GET['name'] ?>_chartContainer").hide();
                       $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                   $("#<?= $_GET['name'] ?>_chartContainer").hide();
                   $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            } 
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
        $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
        //widgetProperties = getWidgetProperties(widgetName);
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) 
            {
                widgetProperties = data;
                
                if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    widgetParameters = widgetProperties.param.parameters;

                    if(widgetParameters !== null)
                    {
                        widgetParameters = JSON.parse(widgetProperties.param.parameters);
                        if(widgetParameters.hasOwnProperty("thresholdObject"))
                        {
                          thresholdObject = widgetParameters.thresholdObject; 
                        }

                        if((widgetParameters.rangeMin !== null) && (widgetParameters.rangeMin !== "") && (typeof widgetParameters.rangeMin !== "undefined"))
                        {
                            rangeMin = widgetParameters.rangeMin;
                        }

                        if((widgetParameters.rangeMax !== null) && (widgetParameters.rangeMax !== "") && (typeof widgetParameters.rangeMax !== "undefined"))
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

                    //Fine eventuale codice ad hoc basato sulle proprietà del widget
                    if(fromGisExternalContent)
                    {
                        if((fromGisFakeId !== null) && (fromGisFakeId !== 'null') && (fromGisFakeId !== undefined))
                        {
                            urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + fromGisFakeId;
                        }
                        else
                        {
                            urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + fromGisExternalContentServiceUri + "&format=json";
                        }

                        $.ajax({
                            url: urlToCall,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function(geoJsonServiceData) 
                            {
                                $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').hide();
                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').show();

                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').off('click');
                                $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').click(function(){
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
                        $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').hide();
                        $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').show();
                        manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
                        metricData = getMetricData(metricName);
                        populateWidget();
                    }
                }
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    console.log(JSON.stringify(errorData));
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_GET['name'] ?>_chartContainer").hide();
                       $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function(errorData)
            {
               console.log("Errore in caricamento proprietà widget");
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               {
                  $("#<?= $_GET['name'] ?>_chartContainer").hide();
                  $('#<?= $_GET['name'] ?>_noDataAlert').show();
               }
            },
            complete: function()
            {
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            }
        });
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
               <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
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
            <div id="<?= $_GET['name'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_GET['name'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_GET['name'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 