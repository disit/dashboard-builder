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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
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
                
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
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
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters, sizeRowsWidget, desc, plotLinesArray, value, day, dayParts, timeParts, date, maxValue1, maxValue2, nInterval,
            valueAtt, valuePrec, alarmSet, plotLineObj, metricName, widgetTitle, countdownRef = null;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var range = "<?= $_REQUEST['time'] ?>"; 
        var seriesData1 = [];
        var valuesData1 = [];
        var seriesData2 = [];
        var valuesData2 = [];
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
		var showHeader = null;
			
			if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
		{
			showHeader = false;
		}
		else
		{
			showHeader = true;
		} 
        
        var unitsWidget = [['millisecond', // unit name
            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] // allowed multiples
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
            [1, 3, 4, 6, 8, 10, 12]
        ], [
            'year',
            null
        ]];
        
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
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
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
        
        if(widgetParameters !== null && widgetParameters !== undefined)
        {
            if(widgetParameters.hasOwnProperty("thresholdObject"))
            {
               thresholdObject = widgetParameters.thresholdObject; 
            }
        }
        
        sizeRowsWidget = parseInt('<?= $_REQUEST['size_rows'] ?>');
        
        $.ajax({
            url: "../widgets/getDataMetricsForTimeTrend.php",
            data: {"IdMisura": [metricName], "time": "<?= $_REQUEST['time'] ?>", "compare": 1},
            type: "GET",
            async: true,
            dataType: 'json',
            success: function(metricData) 
            {       
                if(metricData.data.length > 0)
                {   
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
                            if (value >= 100) 
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
                        timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');

                        if(metricData.data[i].commit.range_dates === 0) 
                        {
                            if (range === '1/DAY' || range.split("/")[1] === "HOUR")
                            {
                                seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], timeParts[0], timeParts[1]), value]);
                            } 
                            else
                            {
                                seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]), value]);
                            }

                            valuesData1.push(value);
                        }
                        else 
                        {
                            if(range === '30/DAY') 
                            {
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1], dayParts[2]), value]);
                            } 
                            else if (range === '7/DAY') 
                            {
                                seriesData2.push([Date.UTC(dayParts[0], parseInt(dayParts[1]) - 1, parseInt(dayParts[2]) + 7), value]);
                            } 
                            else if (range === '365/DAY') 
                            {
                                seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, dayParts[1] - 1, dayParts[2]), value]);
                            } 
                            else if (range === '1/DAY' || range === '4/HOUR' || range === '12/HOUR') 
                            {
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 1, timeParts[0], timeParts[1]), value]);
                            }

                            valuesData2.push(value);
                        }
                    }

                    maxValue1 = parseFloat((Math.max.apply(Math, valuesData1)).toFixed(1));
                    maxValue2 = parseFloat((Math.max.apply(Math, valuesData2)).toFixed(1));
                    nInterval = parseFloat((Math.max(maxValue1, maxValue2) / 4).toFixed(1));

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


                    }

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
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts({
                        credits: {
                            enabled: false
                        },
                        chart: {
                            backgroundColor: '<?= $_REQUEST['color_w'] ?>',
                            type: 'spline',
                            style: {
                                fontFamily: 'Verdana'
                            }

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
                            max: Math.max(maxValue1, maxValue2),
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
                            //valueSuffix: 'Valori',
                            formatter: function () {
                                var val2 = Highcharts.dateFormat('%a %d %b %H:%M:%S', this.x);
                                var valX = new Date(this.x);
                                var nameSerie = this.series.name;
                                var mod = range;

                                if(nameSerie === 'Previous') 
                                {
                                    if (mod === '1/DAY') 
                                    {
                                        valueAtt = valX.getDate();
                                        valuePrec = valX.getDate() - 1;
                                        valX.setDate(valuePrec);
                                    } 
                                    else if (mod === '30/DAY')
                                    {
                                        valueAtt = valX.getMonth();
                                        valuePrec = valX.getMonth() - 1;
                                        valX.setMonth(valuePrec);
                                    } 
                                    else if (mod === '7/DAY') 
                                    {
                                        valueAtt = valX.getDate();
                                        valuePrec = valX.getDate() - 7;
                                        valX.setDate(valuePrec);
                                    } 
                                    else if (mod === '365/DAY') 
                                    {
                                        valueAtt = valX.getYear();
                                        valuePrec = valX.getYear() - 1;
                                        valX.setYear(valuePrec);
                                    } 
                                    else if (mod === '4/HOUR') 
                                    {
                                        valueAtt = valX.getHours();
                                        valuePrec = valX.getHours() - 4;
                                        valX.setHours(valuePrec);
                                    } 
                                    else if (mod === '12/HOUR') 
                                    {
                                        valueAtt = valX.getHours();
                                        valuePrec = valX.getHours() - 12;
                                        valX.setHours(valuePrec);
                                    }
                                }
                                return Highcharts.dateFormat('%A, %b %d, %H:%M', valX.getTime()) + '<br><span style="color:'+ this.color + '">\u25CF</span> ' + nameSerie + ': <b>' + this.y + '</b>';
                            },
                            headerFormat: '<b>{series.name}</b><br>',
                            pointFormat: '{point.x:%e. %b}: {point.y:.2f}'
                        },
                        plotOptions: {
                            series: {
                                events: 
                                {
                                    legendItemClick: function () {
                                        return false;
                                    }
                                },
                                lineWidth: 2
                            }
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'middle',
                            borderWidth: 0,
                            margin: 2,
                            itemStyle: {
                                    fontFamily: 'Verdana',
                                    color: fontColor,
                                    fontSize: fontSize + "px",
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                                    "textOutline": "1px 1px contrast"
                                }
                        },
                        series: [{
                                showInLegend: true,
                                name: "Previous",
                                data: seriesData2,
                                color: "#CECECE",
                                zIndex: 999999

                            }, {
                                showInLegend: true,
                                name: "Current",
                                data: seriesData1,
                                //color: "#7cb5ea",
                                color: "#33ccff",
                                zIndex: 999999
                            }]
                    });
                }
                else
                {
                    console.log("Chiamata di getDataMetricsForTimeTrend.php OK ma nessun dato restituito.");
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                       $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                       $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function(errorData)
            {
                console.log("Errore in chiamata di getDataMetricsForTimeTrend.php.");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                   $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            }
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });//Fine document ready   
    
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
		<?php include '../widgets/widgetCtxMenu.php'; ?>
        <!--<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
            <div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
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