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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId) 
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
        ?> 
        //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)    
        var widgetName = "<?= $_REQUEST['name_w'] ?>";       
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var wsRetryActive, wsRetryTime = null;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues, 
            descriptions, threshold, thresholdEval, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            widgetParameters, sizeRowsWidget, desc, plotLinesArray, value, day, dayParts, timeParts, date, maxValue, nInterval, alarmSet, plotLineObj, metricName, 
            widgetTitle, countdownRef,widgetOriginalBorderColor, convertedData, serviceMapTimeRange, unitsWidget, webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var range = "<?= $_REQUEST['temporal_range_w'] ?>"; 
        var seriesData = [];
        var valuesData = [];
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var showHeader = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            showHeader = false;
        }
        else
        {
           showHeader = true;
        } 
        
        if(url === "null")
        {
            url = null;
        }
        
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= $_REQUEST['id_metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
            widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
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
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });
        
        $(document).off('mouseOverTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverTimeTrendFromExternalContentGis_' + widgetName, function(event) 
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
        
        $(document).off('mouseOutTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutTimeTrendFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('showTimeTrendFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, event.range, event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalTimeTrendFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_REQUEST['frame_color_w'] ?>", "<?= $_REQUEST['headerFontColor'] ?>", false, null, null, null, null, null, null);
            }
        });
		
	$(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
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
        
        function drawDiagram(metricData, timeRange, seriesName, fromSelector)
        {   
            if(metricData.data.length > 0)
            {
                desc = metricData.data[0].commit.author.descrip;
                metricType = '<?= $_REQUEST['id_metric']?>';
                
                for(var i = 0; i < metricData.data.length; i++) 
                {
                    day = metricData.data[i].commit.author.computationDate;

                    if((metricData.data[i].commit.author.value !== null) && (metricData.data[i].commit.author.value !== "")) 
                    {
                        value = parseFloat(parseFloat(metricData.data[i].commit.author.value).toFixed(1));
                        flagNumeric = true;
                    } 
                    else if((metricData.data[i].commit.author.value_perc1 !== null) && (metricData.data[i].commit.author.value_perc1 !== "")) 
                    {
                        if(value >= 100) 
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(0));
                        } 
                        else 
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(1));
                        }
                        flagNumeric = true;
                    }

                    dayParts = day.substring(0, day.indexOf(' ')).split('-');
                    
                    if(fromSelector)
                    {
                        timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                        
                        if((timeRange === '1/DAY') || (timeRange.includes("HOUR"))) 
                        {
                            unitsWidget = [['millisecond', 
                            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] 
                            ], [
                                'second',
                                [1, 2, 5, 10, 15, 30]
                            ], [
                                'minute',
                                [1, 2, 5, 10, 15, 30]
                            ], [
                                'hour',
                                [1, 2, 3, 4, 6, 8, 12]
                            ], [
                                'day',
                                [1]
                            ], [
                                'week',
                                [1]
                            ], [
                                'month',
                                [1]
                                //[1, 3, 4, 6, 8, 10, 12]
                            ], [
                                'year',
                                null
                            ]];
                            date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                            console.log("Sample time from ServiceMap: " + dayParts[0] + "-" + (dayParts[1])+ "-" + dayParts[2] + " " + timeParts[0] + ":" + timeParts[1]);
                        }
                        else 
                        {
                            unitsWidget = [['millisecond',  
                                [1] 
                            ], [
                                'second',
                                [1, 30]
                            ], [
                                'minute',
                                [1, 30]
                            ], [
                                'hour',
                                [1, 6]
                            ], [
                                'day',
                                [1]
                            ], [
                                'week',
                                [1]
                            ], [
                                'month',
                                [1]
                            ], [
                                'year',
                                [1]
                            ]];
                            date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], timeParts[0]);
                            console.log("Sample time from ServiceMap: " + dayParts[0] + "-" + (dayParts[1])+ "-" + dayParts[2] + " - " + timeParts[0]);
                        }
                        timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                        date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                    }
                    else
                    {
                        unitsWidget = [['millisecond', 
                            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] 
                        ], [
                            'second',
                            [1, 2, 5, 10, 15, 30]
                        ], [
                            'minute',
                            [1, 2, 5, 10, 15, 30]
                        ], [
                            'hour',
                            [1, 2, 3, 4, 6, 8, 12]
                        ], [
                            'day',
                            [1]
                        ], [
                            'week',
                            [1]
                        ], [
                            'month',
                            [1]
                            //[1, 3, 4, 6, 8, 10, 12]
                        ], [
                            'year',
                            null
                        ]];
                        if((timeRange === '1/DAY') || (timeRange.includes("HOUR"))) 
                        {
                            timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                            date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                        }
                        else 
                        {
                            date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]);
                        }
                    }
                    
                    seriesData.push([date, value]);
                    valuesData.push(value);
                }

                maxValue = Math.max.apply(Math, valuesData);
                nInterval = parseFloat((maxValue / 4).toFixed(1));

                if(flagNumeric && (thresholdObject!== null))
                {
                   plotLinesArray = []; 
                   var op, op1, op2 = null;        

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
                               y: 12
                            }
                         };
                         plotLinesArray.push(plotLineObj);
                      }
                      else
                      {
                         //Semiretta destra
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
                                  text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                               }
                            };
                            plotLinesArray.push(plotLineObj);
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
                                     text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                                  }
                               };
                               plotLinesArray.push(plotLineObj);
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
                                        text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                                     }
                                  };
                                  plotLinesArray.push(plotLineObj);
                               }
                               else
                               {
                                  //Intervallo bi-limitato
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
                                        text: thresholdObject[i].desc + " " + op1 + " " + thresholdObject[i].thr1
                                     }
                                  };
                                  plotLinesArray.push(plotLineObj);

                                  plotLineObj = {
                                     color: thresholdObject[i].color, 
                                     dashStyle: 'shortdash', 
                                     value: parseFloat(thresholdObject[i].thr2), 
                                     width: 1,
                                     zIndex: 5,
                                     label: {
                                        text: thresholdObject[i].desc + " " + op2 + " " + thresholdObject[i].thr2,
                                        y: 12
                                     }
                                  };
                                  plotLinesArray.push(plotLineObj);
                               }
                            }
                         }
                      }
                   }

                    //Non cancellare, da recuperare quando ripristini il blink in caso di allarme
                    /*delta = Math.abs(value - threshold);

                    //Distinguiamo in base all'operatore di confronto
                    switch(thresholdEval)
                    {
                       //Allarme attivo se il valore attuale è sotto la soglia
                       case '<':
                           if(value < threshold)
                           {
                              //alarmSet = true;
                           }
                           break;

                       //Allarme attivo se il valore attuale è sopra la soglia
                       case '>':
                           if(value > threshold)
                           {
                              //alarmSet = true;
                           }
                           break;

                       //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                       case '=':
                           deltaPerc = (delta / threshold)*100;
                           if(deltaPerc < 0.01)
                           {
                               //alarmSet = true;
                           }
                           break;    

                       //Non gestiamo altri operatori 
                       default:
                           break;
                    }*/
                }

                if(firstLoad !== false)
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                }
                else
                {
                    elToEmpty.empty();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                }
                
                if(metricType === "isAlive") 
                {
                    //Calcolo del vettore delle zones
                    var myZonesArray = [];
                    
                    var newZoneItem = null;
                    var areaColor = null;
                    for(var i=1; i < seriesData.length; i++)
                    {
                        
                        switch(seriesData[i-1][1]){
                            case 2:
                                areaColor='#ff0000'; 
                                break;
                                
                             case 4:
                                 areaColor='#f96f06';
                                 break;
                                 
                             case 6:
                                 areaColor='#ffcc00';
                                 break;
                            
                            case 8:
                                areaColor='#00cc00';
                                break;
                
                       }   
                       if(i < seriesData.length-1)
                        {                                            
                            newZoneItem = {
                                value: seriesData[i][0],
                                color: areaColor
                            };
                        }
                        else
                        {
                            newZoneItem = {
                                color: areaColor
                            };
                        }
                        
                        myZonesArray.push(newZoneItem);
                    }
                  
                    //Disegno del diagramma
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts({
                        credits: {
                            enabled: false
                        },
                        chart: {
                            backgroundColor: '<?= $_REQUEST['color_w'] ?>',
                            type: 'area' 
                        },
                        exporting: {
                            enabled: false
                        },
                        title: {
                            text: ''
                        },
                         
                        xAxis: {
                            type: 'datetime',
                            units: unitsWidget,
                            labels: {
                                enabled: true,
                                style: {
                                    fontFamily: 'Verdana',
                                    color: fontColor,
                                    fontSize: fontSize + "px",
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                    "textOutline": "1px 1px contrast"
                                }

                            }
                        },

                        yAxis: {
                            title: {
                                text: ''
                            },
                            min: 0,
                            max: 8,
                            tickInterval: nInterval,
                            plotLines: plotLinesArray,
                            labels: {
                                enabled: true,
                                style: {
                                    fontFamily: 'Verdana',
                                    color: fontColor,
                                    fontSize: fontSize + "px",
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                    "textOutline": "1px 1px contrast"
                                },
                                formatter: function () {
                                    switch (this.value)
                                    {
                                        case 2:
                                            return "Time out";
                                            break;

                                        case 4:
                                            return "Error";
                                            break;

                                        case 6:
                                            return "Token not found";
                                            break;
                                        case 8:
                                            return "Ok";
                                            break;

                                        default:
                                            return null;
                                            break;
                                    }
                                    return this.value;
                                }

                            }
                        },
                        tooltip: {
                            valueSuffix: ''
                        },
                         
                        series: [{
                                showInLegend: false,
                                name: seriesName,
                                data: seriesData,
                                step: 'left',
                                zoneAxis: 'x',
                                zones: myZonesArray
                            }]
                   
                    });
                } 
                else 
                {
                    //Disegno del diagramma
                    
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts({
                        credits: {
                            enabled: false
                        },
                        chart: {
                            backgroundColor: '<?= $_REQUEST['color_w'] ?>',
                            type: 'spline'
                            //type: 'areaspline'
                        },
                        plotOptions: {
                            spline: {
                                
                            }
                            /*areaspline: {
                                color: '#FF0000',
                                fillColor: '#ffb3b3'
                            },
                            
                            series: {
                                lineWidth: 2
                            }*/
                        },
                        exporting: {
                            enabled: false
                        },
                        title: {
                            text: ''
                        },
                               
                        xAxis: {
                            type: 'datetime',
                            units: unitsWidget,
                            labels: {
                                enabled: true,
                                style: {
                                    fontFamily: 'Verdana',
                                    color: fontColor,
                                    fontSize: fontSize + "px",
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                    "textOutline": "1px 1px contrast"
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: ''
                            },
                            min: 0,
                            max: maxValue,
                            tickInterval: nInterval,
                            plotLines: plotLinesArray,
                            labels: {
                                enabled: true,
                                style: {
                                    fontFamily: 'Verdana',
                                    color: fontColor,
                                    fontSize: fontSize + "px",
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                    "textOutline": "1px 1px contrast"
                                }
                            }
                        },
                        tooltip: 
                        {
                            valueSuffix: ''
                        },
                        series: [{
                                showInLegend: false,
                                name: seriesName,
                                data: seriesData/*,
                                fillColor: {
                                    linearGradient: {
                                        x1: 0,
                                        y1: 0,
                                        x2: 0,
                                        y2: 0
                                    },
                                    stops: [
                                        [0, '#ffb3b3'],
                                        [1, Highcharts.Color('#ffb3b3').setOpacity(0).get('rgba')]
                                    ]
                                }*/
                            }]
                    });
                }
                
                //Versione precedente - Disegno del diagramma
                /*$('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts({
                    credits: {
                        enabled: false
                    },
                    chart: {
                        backgroundColor: '<?= $_REQUEST['color_w'] ?>',
                        type: 'spline'
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        type: 'datetime',
                        units: unitsWidget,
                        labels: {
                            enabled: true,
                            style: {
                                fontFamily: 'Verdana',
                                color: fontColor,
                                fontSize: fontSize + "px",
                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                "textOutline": "1px 1px contrast"
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: ''
                        },
                        min: 0,
                        max: maxValue,
                        tickInterval: nInterval,
                        plotLines: plotLinesArray,
                        labels: {
                            enabled: true,
                            style: {
                                fontFamily: 'Verdana',
                                color: fontColor,
                                fontSize: fontSize + "px",
                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                "textOutline": "1px 1px contrast"
                            }
                        }
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    series: [{
                            showInLegend: false,
                            name: '<?= $_REQUEST['id_metric'] ?>',
                            data: seriesData
                        }]
                });*/
    
            }
            else
            {
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
        }
        
        function convertDataFromSmToDm(originalData, field)
        {
            var singleOriginalData, singleData, convertedDate = null;
            var convertedData = {
                data: []
            };
            
            var originalDataWithNoTime = 0;
            var originalDataNotNumeric = 0;
            
            if(originalData.hasOwnProperty("realtime"))
            {
                if(originalData.realtime.hasOwnProperty("results"))
                {
                    if(originalData.realtime.results.hasOwnProperty("bindings"))
                    {
                        if(originalData.realtime.results.bindings.length > 0)
                        {
                            for(var i = 0; i < originalData.realtime.results.bindings.length; i++)
                            {
                                singleData = {
                                    commit: {
                                        author: {
                                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si può lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si può lasciare null, non viene usato dal widget
                                        },
                                        range_dates: 0//Si può lasciare null, non viene usato dal widget
                                    }
                                };

                                singleOriginalData = originalData.realtime.results.bindings[i];
                                if(singleOriginalData.hasOwnProperty("updating"))
                                {
                                    convertedDate = singleOriginalData.updating.value;
                                }
                                else
                                {
                                    if(singleOriginalData.hasOwnProperty("measuredTime"))
                                    {
                                        convertedDate = singleOriginalData.measuredTime.value;
                                    }
                                    else
                                    {
                                        if(singleOriginalData.hasOwnProperty("instantTime"))
                                        {
                                            convertedDate = singleOriginalData.instantTime.value;
                                        }
                                        else
                                        {
                                            originalDataWithNoTime++;
                                            continue;
                                        }
                                    }
                                }

                                convertedDate = convertedDate.replace("T", " ");
                                var plusIndex = convertedDate.indexOf("+");
                                convertedDate = convertedDate.substr(0, plusIndex);
                                singleData.commit.author.computationDate = convertedDate;
                                
                                if(!isNaN(parseFloat(singleOriginalData[field].value)))
                                {
                                    singleData.commit.author.value = parseFloat(singleOriginalData[field].value);
                                }
                                else
                                {
                                    //console.log("Categoria dato: " + field + " - Indice campione non numerico: " + i);
                                    originalDataNotNumeric++;
                                    continue;
                                }

                                convertedData.data.push(singleData);
                            }

                            return convertedData;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        //Ordinamento dei dati in ordine temporale crescente
        function convertedDataCompare(a, b) 
        {
            var dateA = new Date(a.commit.author.computationDate);
            var dateB = new Date(b.commit.author.computationDate);
            if(dateA < dateB)
            {
                return -1;
            }
            else
            {
                if(dateA > dateB)
                {
                    return 1;
                }
                else
                {
                    return 0;
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
        
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
        
        //Nuova versione
        if(('<?= $_REQUEST['styleParameters'] ?>' !== "")&&('<?= $_REQUEST['styleParameters'] ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= $_REQUEST['styleParameters'] ?>');
        }
        
        if('<?= $_REQUEST['parameters'] ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= $_REQUEST['parameters'] ?>');
        }
        
        if(widgetParameters !== null && widgetParameters !== undefined)
        {
            if(widgetParameters.hasOwnProperty("thresholdObject"))
            {
               thresholdObject = widgetParameters.thresholdObject; 
            }
        }
        
        sizeRowsWidget = parseInt('<?= $_REQUEST['size_rows'] ?>');
        
        if(fromGisExternalContent)
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

            switch(fromGisExternalContentRange)
            {
                case "4/HOUR":
                    serviceMapTimeRange = "fromTime=4-hour";
                    break;

                case "1/DAY":
                    serviceMapTimeRange = "fromTime=1-day";
                    break;

                case "7/DAY":
                    serviceMapTimeRange = "fromTime=7-day";
                    break;

                case "30/DAY":
                    serviceMapTimeRange = "fromTime=30-day";
                    break;     

                default:
                    serviceMapTimeRange = "fromTime=1-day";
                    break;
            }

            $.ajax({
                url: "<?php echo $serviceMapUrlForTrendApi; ?>" + "?serviceUri=" + fromGisExternalContentServiceUri + "&" + serviceMapTimeRange,
                type: "GET",
                data: {},
                async: true,
                dataType: 'json',
                success: function(originalData) 
                {
                    //console.log(JSON.stringify(data));
                    var convertedData = convertDataFromSmToDm(originalData, fromGisExternalContentField);
                    if(convertedData)
                    {
                        if(convertedData.data.length > 0)
                        {
                            drawDiagram(convertedData, fromGisExternalContentRange, fromGisExternalContentField, true);
                        }
                        else
                        {
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            console.log("Dati non disponibili da Service Map");
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        console.log("Dati non disponibili da Service Map");
                    }
                    //console.log(JSON.stringify(convertDataFromSmToDm(originalData, fromGisExternalContentField)));

                },
                error: function (data)
                {
                    showWidgetContent(widgetName);
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    console.log("Errore in scaricamento dati da Service Map");
                    console.log(JSON.stringify(data));
                }
            });
        }
        else
        {
            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();

            $.ajax({
                url: "../widgets/getDataMetricsForTimeTrend.php",
                data: {"IdMisura": ['<?= $_REQUEST['id_metric'] ?>'], "time": "<?= $_REQUEST['time'] ?>", "compare": 0},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(metricData) 
                {   
                    drawDiagram(metricData, '<?= $_REQUEST['time'] ?>', '<?= $_REQUEST['id_metric'] ?>', false);
                },
                error: function(errorData)
                {
                    showWidgetContent(widgetName);
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    console.log("Errore in chiamata di getDataMetricsForTimeTrend.php.");
                    console.log(JSON.stringify(errorData));
                }
            });
        }
        
        //Web socket 
        openWs = function(e)
        {
            console.log("Widget " + widgetTitle + " is trying to open WebSocket");
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
                console.log("Widget " + widgetTitle + " could not connect to WebSocket");
                wsClosed();
            }
        };
        
        manageIncomingWsMsg = function(msg)
        {
            console.log("Widget " + widgetTitle + " got new data from WebSocket: \n" + msg.data);
            var msgObj = JSON.parse(msg.data);

            switch(msgObj.msgType)
            {
                case "newNRMetricData":
                    if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                    {
                        webSocket.close();
                        clearInterval(countdownRef);
                        <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);
                    }
                    break;

                default:
                    console.log("Received: " + msg.data);
                    break;
            }
        };
        
        openWsConn = function(e)
        {
            console.log("Widget " + widgetTitle + " connected successfully to WebSocket");
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                metricName: encodeURIComponent(metricName)
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
            console.log("Widget " + widgetTitle + " got WebSocket closed");
            
            webSocket.removeEventListener('close', wsClosed);
            webSocket.removeEventListener('open', openWsConn);
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            webSocket = null;
            if(wsRetryActive === 'yes')
            {
                console.log("Widget " + widgetTitle + " will retry WebSocket reconnection in " + parseInt(wsRetryTime) + "s");
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }	
        };
        
        //Per ora non usata
        wsError = function(e)
        {
            console.log("Widget " + widgetTitle + " got WebSocket error: " + e);
        };
        
        openWs();
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId); 
    });//Fine document ready 
</script>


<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
		<?php include '../widgets/widgetCtxMenu.php'; ?>
		
        <!--<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
            <div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
               <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
            </div>    
            <div id="<?= $_REQUEST['name_w'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>-->
        
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
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
