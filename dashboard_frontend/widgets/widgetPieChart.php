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

<script type="text/javascript">
    
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
        var defaultColorsArray = ['#ffcc00', '#ff9933', '#ff3300', '#ff3399', '#6666ff', '#0066ff', '#00ccff', '#00ffff', '#00ff00', '#009900'];        
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
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, 
            pattern, totValues, shownValues, descriptions, udm, threshold, thresholdEval, stopsArray, 
            delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength, metricName, widgetTitle, countdownRef,
            innerRadius1 = null;
        var colors = [];
        var metricName = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
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
                $("#" + widgetName + "_legendContainer1").empty();
                $("#" + widgetName + "_legendContainer2").empty();	
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });
        
        //Definizioni di funzione specifiche del widget
    
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
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
        
        function drawDiagram (id, seriesObj, pieObj){
            $(id).highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    backgroundColor: '<?= $_GET['color'] ?>',
                    options3d: {
                        enabled: false,
                        alpha: 45,
                        beta: 0
                    },
                    events: {
                        load: onDraw
                    }           
                },
                title: {
                    text: ''
                },
                plotOptions: {
                    pie: pieObj
                },
                series: seriesObj,
                legend: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                }     
            });
        };
        
        function labelsFormat()
        {
            var format, test = null;
        
            switch(styleParameters.dataLabels)
            {
                case "no":
                    format = "";
                    break;
                    
                case "value":
                    format = this.y;
                    break;
                    
                case "full":
                    format = this.series.name + ': ' + this.y;
                    break;
                    
                default:
                    format = this.y;
                    break;    
            }
            
            return format;
        }
        
        function getChartSeriesObject(series)
        {
            var totals, chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, seriesArray, zonesObject, zonesArray, inf, sup, i, innerSize, outerSize, numberOfCircs, chartWidth, increment, color = null;
            
            if(series !== null)
            {
                chartSeriesObject = [];
                numberOfCircs = series.secondAxis.series.length;
                chartWidth = ($('#<?= $_GET['name'] ?>_div').height() - 40)*0.86;
                
                //Primo cerchio con le categorie del secondo asse
                seriesName = series.secondAxis.desc;
                seriesValues = [];
                
                totals = [];
                
                for(var i = 0; i < series.secondAxis.labels.length; i++)
                {
                    totals[i] = 0;
                    for(var j = 0; j < series.secondAxis.series[i].length; j++)
                    {
                        totals[i] = totals[i] + series.secondAxis.series[i][j];
                    }
                }
                
                for(var i = 0; i < series.secondAxis.labels.length; i++)
                {
                    if((styleParameters.colorsSelect1 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                    {
                        color = styleParameters.colors1[i];
                    }
                    else
                    {
                        color = defaultColorsArray[i%10];
                    }
        
                    seriesValues.push({
                        name: series.secondAxis.labels[i],
                        color: color,
                        y: totals[i]
                    });
                }
                
                //Calcolo dei diametri delle circonferenze
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    innerSize = chartWidth * parseFloat(parseFloat(styleParameters.innerRadius1)/100);
                    outerSize = chartWidth * parseFloat(parseFloat(styleParameters.outerRadius1)/100);
                }
                else
                {
                    innerSize = chartWidth * parseFloat(0.2);
                    outerSize = chartWidth * parseFloat(0.5);
                }
                
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    dataLabelsDistance1 = parseInt(styleParameters.dataLabelsDistance1);
                }
                else
                {
                    dataLabelsDistance1 = -30;
                }
                
                singleObject = {
                    type: 'pie',
                    name: seriesName,
                    data: seriesValues,
                    size: outerSize,
                    innerSize: innerSize,
                    showInLegend: false,
                    borderWidth: 1,
                    tooltip: {
                        headerFormat: null,
                        pointFormat: "<span style='color:{point.color}'>\u25CF</span> {series.name}: <b>{point.name}</b><br/>"
                    },
                    dataLabels: {
                        useHTML: false,
                        enabled: true,
                        inside: true,
                        distance: dataLabelsDistance1,
                        formatter: function(){
                            switch(styleParameters.dataLabels)
                            {
                                case 'no':
                                    return null;
                                    break;
            
                                case 'value': 
                                    return this.point.name;
                                    break;
                                    
                                case 'full': 
                                    return this.point.name;
                                    break;    
                                    
                                default:
                                    return this.point.name;
                                    break;
                            }
                        },
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: styleParameters.dataLabelsFontSize + "px",
                            color: styleParameters.dataLabelsFontColor,
                            fontWeight: 'bold',
                            fontStyle: 'italic',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                            "textOutline": "1px 1px contrast"
                        }
                    }
                };
                
                //Workaround temporaneo per far vedere pie tradizionali con più di 3 fette
                if(seriesValues.length > 1)
                {
                    chartSeriesObject.push(singleObject);
                }
                
                //Secondo cerchio con le subcategorie prese dal primo asse
                seriesName = series.firstAxis.desc;
                seriesValues = [];
                
                for(var i = 0; i < series.secondAxis.series.length; i++)
                {
                    for(var j = 0; j < series.secondAxis.series[i].length; j++)
                    {
                        if((styleParameters.colorsSelect2 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            color = styleParameters.colors2[j];
                        }
                        else
                        {
                            color = defaultColorsArray[j%10];
                        }
            
                        seriesValues.push({
                            name: series.firstAxis.labels[j],
                            color: color,
                            y: series.secondAxis.series[i][j]
                        });
                    }
                }
                
                //Calcolo dei diametri delle circonferenze
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    innerSize = chartWidth * parseFloat(parseFloat(styleParameters.innerRadius2)/100);
                }
                else
                {
                    innerSize = chartWidth * parseFloat(0.5);
                }
                
                outerSize = parseFloat(chartWidth * 1);
                
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    dataLabelsDistance2 = parseInt(styleParameters.dataLabelsDistance2);
                }
                else
                {
                    dataLabelsDistance2 = -30;
                }
                
                singleObject = {
                    type: 'pie',
                    name: seriesName,
                    data: seriesValues,
                    size: outerSize,
                    innerSize: innerSize,
                    showInLegend: false,
                    tooltip: {
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: 12 + "px",
                            color: 'black',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.15)",
                            "z-index": 5
                        },
                        backgroundColor: {
                            linearGradient: [0, 0, 0, 60],
                            stops: [
                                [0, '#FFFFFF'],
                                [1, '#E0E0E0']
                            ]
                        },
                        //headerFormat: '<span style="font-size: 10px">{point.key}</span><br/>'
                        headerFormat: null,
                        pointFormatter: function()
                        {
                            var field = this.series.name;
                            var thresholdsJson = getThresholdsJson();
                            var temp, thresholdObject, desc, min, max, color, label, index, message = null;
                            var rangeOnThisField = false;
                            
                            if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                            {
                                temp = JSON.parse(thresholdsJson);
                                thresholdObject = temp.thresholdObject.firstAxis.fields;

                                if(thresholdObject.length > 0)
                                {
                                    label = this.name;

                                    for(var i in thresholdObject)
                                    {
                                        if(label === thresholdObject[i].fieldName)
                                        {
                                            if(thresholdObject[i].thrSeries.length > 0) 
                                            {
                                                for(var j in thresholdObject[i].thrSeries)
                                                {
                                                    if((parseFloat(this.y) >= thresholdObject[i].thrSeries[j].min)&&(parseFloat(this.y) < thresholdObject[i].thrSeries[j].max))
                                                    {
                                                        desc = thresholdObject[i].thrSeries[j].desc;
                                                        min = thresholdObject[i].thrSeries[j].min;
                                                        max = thresholdObject[i].thrSeries[j].max;
                                                        color = thresholdObject[i].thrSeries[j].color;
                                                        rangeOnThisField = true;
                                                    }
                                                    else
                                                    {
                                                        message = "This value doesn't belong to any of the defined ranges";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                rangeOnThisField = false;
                                                message = "No range defined on this field";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    rangeOnThisField = false;
                                    message = "No range defined on this field";
                                }
                            }
                            else
                            {
                                rangeOnThisField = false;
                                message = "No range defined on this field";
                            }

                            if(rangeOnThisField)
                            {
                                if((desc !== null)&&(desc !== ''))
                                {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';   
                                }
                                else
                                {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                }
                            }
                            else
                            {
                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                       '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                            }      
                        }
                    },
                    dataLabels: {
                        useHTML: false,
                        enabled: true,
                        inside: true,
                        distance: dataLabelsDistance2,
                        formatter: function(){
                            switch(styleParameters.dataLabels)
                            {
                                case 'no':
                                    return null;
                                    break;
            
                                case 'value':
                                    return this.y;
                                    break;
                                    
                                case 'full':
                                    return this.point.name + ": " + this.y;
                                    break;
                                    
                                default:
                                    return this.y;
                                    break;
                            }
                        },
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: styleParameters.dataLabelsFontSize + "px",
                            color: styleParameters.dataLabelsFontColor,
                            fontWeight: 'bold',
                            fontStyle: 'italic',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                            "textOutline": "1px 1px contrast"
                        }
                    }
                };
                
                chartSeriesObject.push(singleObject);
            }
            return chartSeriesObject;
        } 
        
        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var infoJson = getInfoJson();
            var info = infoJson.firstAxis[id];
            
            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);

            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });
            
            $('#modalWidgetFieldsInfo').modal('show');
        }
        
        function showModalFieldsInfoSecondAxis()
        {
            var infoJson = getInfoJson();
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var info = infoJson.secondAxis[id];

            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);

            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });
            
            $('#modalWidgetFieldsInfo').modal('show');
        }
        
        //Disegno ad hoc della legenda, con inserimento dei pulsanti info e dei menu a comparsa delle legende sulle soglie nel caso delle serie.
        function onDraw()
        {
            var colorContainer, labelContainer, infoContainer, infoIcon, label, id, singleInfo, item, thresholdObject, dropDownElement = null;
            var infoJson = getInfoJson();
            var thresholdsJson = getThresholdsJson();
            
            if((thresholdsJson !== null) && (thresholdsJson !== 'undefined'))
            {
                thresholdObject = JSON.parse(thresholdsJson);
            }
            
            if(metricType.indexOf('Percentuale') >= 0)
            {
                for(var i = 0; i < descriptions.length; i++)
                {
                    label = descriptions[i];
                    
                    if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors1[i] + '"></div>');
                    }
                    else
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                    }
                    
                    //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie
                    if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                    {
                        if(thresholdObject.thresholdObject.fields[i]!== undefined)
                        {
                            if(thresholdObject.thresholdObject.fields[i].thrSeries.length > 0)
                            {
                                labelContainer =  $('<div class="legendLabelContainer thrLegend dropup">' + 
                                              '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' + 
                                                  '<ul class="dropdown-menu thrLegend">' +
                                                  '</ul>' +
                                              '</div>');       

                                thresholdObject.thresholdObject.fields[i].thrSeries.forEach(function(range) 
                                {
                                    if(range.desc !== '')
                                    {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    }
                                    else
                                    {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }
                                });
                            }
                            else
                            {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }
                        }
                        else
                        {
                            labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                        }          
                    }
                    else
                    {
                        labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                    }
                    labelContainer.css("font-size", styleParameters.legendFontSize + "px");
                    item = $('<div class="legendSingleContainer"></div>');
                    item.append(colorContainer);
                    item.append(labelContainer);
                    item.css("color", styleParameters.legendFontColor);
                    item.find('a').css("color", styleParameters.legendFontColor);
                    item.find('a.thrLegendElement').css("color", "black");
                    if(i < shownValues.length - 1)
                    {
                        item.css("margin-right", "10px");
                    }

                    item.css("display", "block");
                    $('#<?= $_GET['name'] ?>_legendContainer1').append(item);
                    
                    var parentLegendElement = $('#<?= $_GET['name'] ?>_legendContainer1').find("div.legendSingleContainer").eq(i);
                    var elementLeftPosition = parentLegendElement.position().left;
                    var widgetWidth = $("#<?= $_GET['name'] ?>_div").width();
                    var legendMargin = null;

                    if(elementLeftPosition > (widgetWidth / 2))
                    {
                        legendMargin = 200;
                    }
                    else
                    {
                        legendMargin = 0;
                    }

                    $("#<?= $_GET['name'] ?>_legendContainer1 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                    
                }
            }
            else if(metricType === 'Series')
            {
                for(var i = 0; i < series.secondAxis.labels.length; i++)
                {
                    label = series.secondAxis.labels[i];
                    
                    if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors1[i] + '"></div>');
                    }
                    else
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                    }

                    //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie - Qui per ora è inutile, non esistono soglie sull'anello più interno
                    if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                    {
                        if(thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.length > 0)
                        {
                            labelContainer =  $('<div class="legendLabelContainer thrLegend dropup">' + 
                                          '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' + 
                                              '<ul class="dropdown-menu thrLegend">' +
                                              '</ul>' +
                                          '</div>');       

                            thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.forEach(function(range) 
                            {
                                if(range.desc !== '')
                                {
                                    dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                }
                                else
                                {
                                    dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                }
                                dropDownElement.css("font", "bold 10px Verdana");
                                dropDownElement.find("i").css("font-size", "12px");
                                labelContainer.find("ul").append(dropDownElement);
                            });
                        }
                        else
                        {
                            labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                        }      
                    }
                    else
                    {
                        labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                    }

                    item = $('<div class="legendSingleContainer"></div>');

                    if(infoJson !== null)
                    {
                        id = label.replace(/\s/g, '_');
                        singleInfo = infoJson.secondAxis[id];

                        if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="y" data-label="' + label + '" style="font-size: 12px; margin-left: 3px"></i>');
                            infoIcon.css("color", styleParameters.legendFontColor);
                            infoContainer = $('<div class="legendInfoContainer"></div>');
                            infoContainer.append(infoIcon);
                            item.append(colorContainer);
                            item.append(infoContainer);
                            item.append(labelContainer);
                            infoIcon.on("click", showModalFieldsInfoSecondAxis);
                        }
                        else
                        {
                            item.append(colorContainer);
                            item.append(labelContainer);
                        }
                    }
                    else
                    {
                        item.append(colorContainer);
                        item.append(labelContainer);
                    }
                    
                    item.css("color", styleParameters.legendFontColor);
                    item.find('a').css("color", styleParameters.legendFontColor);
                    item.find('a.thrLegendElement').css("color", "black");
                    item.find('i.fa-info-circl').css("color", styleParameters.legendFontColor);

                    if(i < series.secondAxis.labels.length - 1)
                    {
                        item.css("margin-right", "10px");
                    }

                    item.css("display", "block");

                    //Workaround temporaneo per far vedere pie tradizionali con più di 3 fette
                    if(series.secondAxis.labels.length  > 1)
                    {
                        $('#<?= $_GET['name'] ?>_legendContainer1').append(item);
                        
                        var parentLegendElement = $('#<?= $_GET['name'] ?>_legendContainer1').find("div.legendSingleContainer").eq(i);
                        var elementLeftPosition = parentLegendElement.position().left;
                        var widgetWidth = $("#<?= $_GET['name'] ?>_div").width();
                        var legendMargin = null;

                        if(elementLeftPosition > (widgetWidth / 2))
                        {
                            legendMargin = 200;
                        }
                        else
                        {
                            legendMargin = 0;
                        }

                        $("#<?= $_GET['name'] ?>_legendContainer1 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                    }
                    else
                    {
                        $('#<?= $_GET['name'] ?>_legendContainer1').hide();
                        $('#<?= $_GET['name'] ?>_chartContainer').css("height", "93%");
                    }
                }

                for(var i = 0; i < series.firstAxis.labels.length; i++)
                {
                    label = series.firstAxis.labels[i];
                    
                    if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors2[i] + '"></div>');
                    }
                    else
                    {
                        colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                    }
                    
                    //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie
                    if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                    {
                        if(thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.length > 0)
                        {
                            labelContainer =  $('<div class="legendLabelContainer thrLegend dropup">' + 
                                          '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' + 
                                              '<ul class="dropdown-menu thrLegend">' +
                                              '</ul>' +
                                          '</div>');

                            thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.forEach(function(range) 
                            {
                                if(range.desc !== '')
                                {
                                    dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                }
                                else
                                {
                                    dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                }
                                dropDownElement.css("font", "bold 10px Verdana");
                                dropDownElement.find("i").css("font-size", "12px");
                                labelContainer.find("ul").append(dropDownElement);
                            });
                        }
                        else
                        {
                            labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                        }           
                    }
                    else
                    {
                        labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                    }

                    item = $('<div class="legendSingleContainer"></div>');

                    if(infoJson !== null)
                    {
                        id = label.replace(/\s/g, '_');
                        singleInfo = infoJson.firstAxis[id];

                        if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: 12px; color: black; margin-left: 3px"></i>'); //data-axis="y" data-label="' + $(this).html() + '" vanno rimessi?
                            infoIcon.css("color", styleParameters.legendFontColor);
                            infoContainer = $('<div class="legendInfoContainer"></div>');
                            infoContainer.append(infoIcon);
                            item.append(colorContainer);
                            item.append(infoContainer);
                            item.append(labelContainer);
                            infoIcon.on("click", showModalFieldsInfoFirstAxis);
                        }
                        else
                        {
                            item.append(colorContainer);
                            item.append(labelContainer);
                        }
                    }
                    else
                    {
                        item.append(colorContainer);
                        item.append(labelContainer);
                    }

                    item.css("color", styleParameters.legendFontColor);
                    item.find('a').css("color", styleParameters.legendFontColor);
                    item.find('a.thrLegendElement').css("color", "black");
                    item.find('i.fa-info-circle').css("color", styleParameters.legendFontColor);

                    if(i < series.secondAxis.labels.length - 1)
                    {
                        item.css("margin-right", "10px");
                    }

                    item.css("display", "block");

                    $('#<?= $_GET['name'] ?>_legendContainer2').append(item);
                    
                    var parentLegendElement = $('#<?= $_GET['name'] ?>_legendContainer2').find("div.legendSingleContainer").eq(i);
                    var elementLeftPosition = parentLegendElement.position().left;
                    var widgetWidth = $("#<?= $_GET['name'] ?>_div").width();
                    var legendMargin = null;

                    if(elementLeftPosition > (widgetWidth / 2))
                    {
                        legendMargin = 200;
                    }
                    else
                    {
                        legendMargin = 0;
                    }

                    $("#<?= $_GET['name'] ?>_legendContainer2 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
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
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            if((widgetProperties.param.parameters !== null) && (widgetProperties.param.parameters !== ''))
            {
                thresholdsJson = getThresholdsJson();
            }
            
            if((widgetProperties.param.infoJson !== null) && (widgetProperties.param.infoJson !== ''))
            {
                infoJson = getInfoJson();
            }
            
            styleParameters = getStyleParameters();
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            
            metricData = getMetricData(metricName);
            if(metricData !== null)
            {
                if(metricData.data[0] !== undefined)
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        metricType = metricData.data[0].commit.author.metricType;    

                        shownValues = [];
                        descriptions = [];
                        totValues = [];
                        dataObj = [];
                        seriesObj = [];

                        var startAngle = 90 - parseInt(styleParameters.startAngle);
                        var endAngle = 90 - parseInt(styleParameters.endAngle);

                        if(startAngle > endAngle)
                        {
                            var temp = startAngle;
                            startAngle = endAngle;
                            endAngle = temp;
                        }

                        var centerY = 100 - parseInt(styleParameters.centerY);

                        if(metricType.indexOf('Percentuale') >= 0)
                        {
                            //Diagramma sui valori value_perc1, value_perc2, value_perc3 
                            udm = "%";

                            if(metricData.data[0].commit.author.value_perc1 !== null)
                            {
                                if(widgetProperties.param.id_metric === 'SmartDS_Process')
                                {
                                    shownValues[0] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1*100).toFixed(1));
                                }
                                else
                                {
                                    shownValues[0] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(2));
                                }
                                descriptions[0] = metricData.data[0].commit.author.field1Desc;
                            }

                            if(metricData.data[0].commit.author.value_perc2 !== null)
                            {
                                if(widgetProperties.param.id_metric === 'SmartDS_Process')
                                {
                                    shownValues[1] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc2*100).toFixed(1));
                                }
                                else
                                {
                                    shownValues[1] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc2).toFixed(2));
                                }
                                descriptions[1] = metricData.data[0].commit.author.field2Desc;
                            }

                            if(metricData.data[0].commit.author.value_perc3 !== null)
                            {
                                if(widgetProperties.param.id_metric === 'SmartDS_Process')
                                {
                                    shownValues[2] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc3*100).toFixed(1));
                                }
                                else
                                {
                                    shownValues[2] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc3).toFixed(2));
                                }
                                descriptions[2] = metricData.data[0].commit.author.field3Desc;
                            }

                            pieObj = {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                startAngle: startAngle,
                                endAngle: endAngle,
                                center: ['50%', centerY + '%']
                            };

                            if(shownValues.length === 1)
                            {
                                shownValues[1] = parseFloat(parseFloat(100 - shownValues[0]).toFixed(2));
                                //descriptions[1] = 'Complementary';
                                var color0, color1, color, desc = null;

                                if((styleParameters.colorsSelect1 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                                {
                                    color0 = styleParameters.colors1[0];
                                    color1 = styleParameters.colors1[1];
                                }
                                else
                                {
                                    color0 = defaultColorsArray[0];
                                    color1 = defaultColorsArray[1];
                                }

                                dataObj[0] = {
                                    name: descriptions[0], 
                                    color: color0, 
                                    y: shownValues[0]
                                };
                                dataObj[1] = {
                                    //name: descriptions[1], 
                                    color: color1, 
                                    y: shownValues[1] 
                                };
                            }
                            else
                            {
                                for(var i = 0; i < shownValues.length; i++)
                                {
                                    if((styleParameters.colorsSelect1 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                                    {
                                        color = styleParameters.colors1[i];
                                    }
                                    else
                                    {
                                        color = defaultColorsArray[i%10];
                                    }

                                    desc = descriptions[i];

                                    dataObj[i] = { 
                                        name: desc,
                                        color: color, 
                                        y: shownValues[i]
                                    };
                                }
                            }

                            var dataLabelsDistance = parseInt(styleParameters.dataLabelsDistance);
                            
                            if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                            {
                                innerRadius1 = parseInt(styleParameters.innerRadius1);
                            }
                            else
                            {
                                innerRadius1 = 25;
                            }
                            
                            if(innerRadius1 > 100)
                            {
                                innerRadius1 = 100;
                            }
                            if(innerRadius1 < 0)
                            {
                                innerRadius1 = 0;
                            }

                            seriesObj.push({
                                data: dataObj,
                                dataLabels: {
                                    formatter: function(){
                                        return this.y + " " + udm;
                                    },
                                    distance: dataLabelsDistance,
                                    style: {
                                        fontFamily: 'Verdana',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                                        "textOutline": "1px 1px contrast"
                                    }
                                },
                                size: "100%",
                                innerSize: innerRadius1 + "%",
                                tooltip: {
                                    headerFormat: null,
                                    backgroundColor: {
                                        linearGradient: [0, 0, 0, 60],
                                        stops: [
                                            [0, '#FFFFFF'],
                                            [1, '#E0E0E0']
                                        ]
                                    },
                                    pointFormatter: function()
                                    {
                                        var field = this.series.name;
                                        var thresholdsJson = getThresholdsJson();
                                        var temp, thresholdObject, desc, min, max, color, label, index, message = null;
                                        var rangeOnThisField = false;

                                        if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                                        {
                                            temp = JSON.parse(thresholdsJson);
                                            thresholdObject = temp.thresholdObject.fields;

                                            if(thresholdObject.length > 0)
                                            {
                                                label = this.name;

                                                if(label === "Complementary")
                                                {
                                                    rangeOnThisField = false;
                                                    message = "No range defined on this field";
                                                }
                                                else
                                                {
                                                    for(var i in thresholdObject)
                                                    {
                                                        if(label === thresholdObject[i].fieldName)
                                                        {
                                                            if(thresholdObject[i].thrSeries.length > 0) 
                                                            {
                                                                for(var j in thresholdObject[i].thrSeries)
                                                                {
                                                                    if((parseFloat(this.y) >= thresholdObject[i].thrSeries[j].min)&&(parseFloat(this.y) < thresholdObject[i].thrSeries[j].max))
                                                                    {
                                                                        desc = thresholdObject[i].thrSeries[j].desc;
                                                                        min = thresholdObject[i].thrSeries[j].min;
                                                                        max = thresholdObject[i].thrSeries[j].max;
                                                                        color = thresholdObject[i].thrSeries[j].color;
                                                                        rangeOnThisField = true;
                                                                    }
                                                                    else
                                                                    {
                                                                        message = "This value doesn't belong to any of the defined ranges";
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
                                                                rangeOnThisField = false;
                                                                message = "No range defined on this field";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                rangeOnThisField = false;
                                                message = "No range defined on this field";
                                            }
                                        }
                                        else
                                        {
                                            rangeOnThisField = false;
                                            message = "No range defined on this field";
                                        }


                                        if(rangeOnThisField)
                                        {
                                            if((desc !== null)&&(desc !== ''))
                                            {
                                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';   
                                            }
                                            else
                                            {
                                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                            }
                                        }
                                        else
                                        {
                                            return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                                        }      
                                    }
                                }
                            });

                            //Per il caso semplice basta una sola riga per la legenda
                            $('#<?= $_GET['name'] ?>_legendContainer2').hide();
                            $('#<?= $_GET['name'] ?>_chartContainer').css('height', '93%');

                        }
                        else if(metricType === 'Series')
                        {
                            //Caso di pie sulle serie
                            series = JSON.parse(metricData.data[0].commit.author.series);
                            legendLength = series.secondAxis.labels.length;
                            seriesObj = getChartSeriesObject(series);

                            pieObj = {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                showInLegend: false,
                                startAngle: startAngle,
                                endAngle: endAngle,
                                center: ['50%', centerY + '%']
                            };
                            
                            $('#<?= $_GET['name'] ?>_chartContainer').css('height', '86%');
                        }

                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();
                            $("#<?= $_GET['name'] ?>_legendContainer1").show();
                            $("#<?= $_GET['name'] ?>_legendContainer2").show();
                        }
                        else
                        {
                            elToEmpty.empty();
                            $("#" + widgetName + "_legendContainer1").empty();
                            $("#" + widgetName + "_legendContainer2").empty();	
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();
                            $("#<?= $_GET['name'] ?>_legendContainer1").show();
                            $("#<?= $_GET['name'] ?>_legendContainer2").show();
                        }
                        drawDiagram("#<?= $_GET['name'] ?>_chartContainer", seriesObj, pieObj);
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $("#<?= $_GET['name'] ?>_chartContainer").hide();
                        $("#<?= $_GET['name'] ?>_legendContainer1").hide();
                        $("#<?= $_GET['name'] ?>_legendContainer2").hide();
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    $("#<?= $_GET['name'] ?>_chartContainer").hide();
                    $("#<?= $_GET['name'] ?>_legendContainer1").hide();
                    $("#<?= $_GET['name'] ?>_legendContainer2").hide();
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                } 
                //Fine eventuale codice ad hoc basato sui dati della metrica
            }
            else
            {
                showWidgetContent(widgetName);
                $("#<?= $_GET['name'] ?>_chartContainer").hide();
                $("#<?= $_GET['name'] ?>_legendContainer1").hide();
                $("#<?= $_GET['name'] ?>_legendContainer2").hide();
                $('#<?= $_GET['name'] ?>_noDataAlert').show();
            }        
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        
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
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainerPie"></div>
            <div id="<?= $_GET['name'] ?>_legendContainer1" class="legendContainer1"></div>
            <div id="<?= $_GET['name'] ?>_legendContainer2" class="legendContainer2"></div>
        </div>
    </div>	
</div> 

