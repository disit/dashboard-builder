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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)   
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetBarSeries.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>  
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
     //   console.log("BarSeries: " + widgetName);
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_table");
        var metricData, metricType, series, index, styleParameters, chartSeriesObject, fontSize, fontColor, legendWidth, xAxisCategories, chartType, highchartsChartType, chartColor, dataLabelsFontColor, 
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, dataLabelsFontSize, chartLabelsFontSize, 
            widgetHeight, metricName, aggregationGetData, getDataFinishCount, widgetTitle, countdownRef, chartRef, widgetParameters, infoJson, thresholdsJson, rowParameters, chartLabelsFontColor, appId, flowId, nrMetricType = null;
        var headerHeight = 25;      
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
	var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
	var showHeader = null;
        var pattern = /Percentuale\//;
        var seriesDataArray = [];
        var serviceUri = "";

        console.log("Entrato in widgetBarSeries --> " + widgetName);

        //Definizioni di funzione specifiche del widget
        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
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
        
        function getChartSeriesObject(series, xAxisLabelsEdit)
        {
            var chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, zonesObject, zonesArray, inf, sup, i = null;
            
            if(series !== null)
            {
                chartSeriesObject = [];
                
                var seriesArray = null;
                    
                    for (var i in series.secondAxis.series) 
                    {
                        if (xAxisLabelsEdit != null) {
                            seriesName = xAxisLabelsEdit[i];
                        } else {
                            seriesName = series.secondAxis.labels[i];
                        }
                        seriesValues = series.secondAxis.series[i];

                        //if(styleParameters.barsColorsSelect === 'manual')
                        if((styleParameters.barsColorsSelect === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            singleObject = {
                                name: seriesName,
                                data: seriesValues,
                                color: styleParameters.barsColors[i],
                                dataLabels: {
                                    useHTML: false,
                                    enabled: true,
                                    inside: true,
                                    rotation: dataLabelsRotation,
                                    overflow: 'justify',
                                    crop: true,
                                    align: dataLabelsAlign,
                                    verticalAlign: dataLabelsVerticalAlign,
                                    y: dataLabelsY,
                                    formatter: labelsFormat,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                    }
                                }
                            };
                        }
                        else
                        {
                            singleObject = {
                                name: seriesName,
                                data: seriesValues,
                                dataLabels: {
                                    useHTML: false,
                                    enabled: true,
                                    inside: true,
                                    rotation: dataLabelsRotation,
                                    overflow: 'justify',
                                    crop: true,
                                    align: dataLabelsAlign,
                                    verticalAlign: dataLabelsVerticalAlign,
                                    y: dataLabelsY,
                                    formatter: labelsFormat,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                    }
                                }
                            };
                        }
                        chartSeriesObject.push(singleObject);
                    }
            }
            return chartSeriesObject;
        }
        
        function drawDiagram()
        {
            let yAxisText = null;
            if (seriesDataArray.length > 0) {
                yAxisText = seriesDataArray[0].metricValueUnit;
            }
            chartRef = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                chart: {
                    type: highchartsChartType,
                    backgroundColor: 'transparent',
                    //Funzione di applicazione delle soglie
                    events: {
                        load: onDraw,
                        //redraw: onDraw
                    }
                },
                //Per disabilitare il menu in alto a destra
                exporting: 
                { 
                    enabled: false 
                },
                //Non cancellare sennò ci mette il titolo di default
                title: {
                    text: ''
                },
                //Non cancellare sennò ci mette il sottotitolo di default
                subtitle: {
                    text: ''
                },

                xAxis: {
                    //categories: series.firstAxis.labels,
                    categories: xAxisCategories,
                    title: {
                        align: 'high',
                        offset: 20,
                        text: series.firstAxis.desc,
                        rotation: 0,
                        y: 5,
                        style: {
                            fontFamily: 'Montserrat',
                            fontSize: styleParameters.rowsLabelsFontSize + "px",
                            fontWeight: 'bold',
                            fontStyle: 'italic',
                            color: styleParameters.rowsLabelsFontColor,
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                        }
                    },
                    labels: {
                       enabled: true,
                       useHTML: false,
                       /*formatter: function () {
                           var valueFormatted = this.value.replace(/ /g, '<br />');
                           return valueFormatted;

                           //this.chart.xAxis[0]

                           //return '<i class="fa fa-info-circle handPointer" style="font-size: ' + rowsLabelsFontSize + 'px; color: ' + rowsLabelsFontColor + '"></i> ' + valueFormatted;
                       },*/
                       style: {
                            fontFamily: 'Montserrat',
                            fontSize: styleParameters.rowsLabelsFontSize + "px",
                            fontWeight: 'bold',
                            color: styleParameters.rowsLabelsFontColor,
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                        }
                    }
                },
                yAxis: {
                 //   min: 0,
                    gridZIndex: 0,
                    title: {
                    //    text: null
                        text: yAxisText
                    },
                    labels: {
                        overflow: 'justify',
                        style: {
                            fontFamily: 'Montserrat',
                            fontSize: styleParameters.colsLabelsFontSize + "px",
                            fontWeight: 'bold',
                            color: styleParameters.colsLabelsFontColor,
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontFamily: 'Montserrat',
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
                    pointFormatter: function()
                    {
                        var field = this.series.name;
                        var thresholdObject, desc, min, max, color, label, index, message = null;
                        var rangeOnThisField = false;
                        var dataStringInPopup = "";
                        var dateMessage = "";

                        for (var n = 0; n < seriesDataArray.length; n++) {
                            if (seriesDataArray[n].metricName == field) {
                                dataStringInPopup = seriesDataArray[n].measuredTime;
                            } else if (styleParameters.editDeviceLabels) {
                                if (seriesDataArray[n].metricName == series.secondAxis.labels[styleParameters.editDeviceLabels.indexOf(field)]) {
                                    dataStringInPopup = seriesDataArray[n].measuredTime;
                                }
                            }
                        }

                        if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            thresholdObject = thresholdsJson.thresholdObject.firstAxis.fields;

                            if(thresholdsJson.thresholdObject.firstAxis.fields.length > 0)
                            {
                                if(this.category.indexOf('thrLegend') > 0)
                                {
                                    label = this.category.substring(this.category.indexOf('<span class="inline">'));
                                    label = label.replace('<span class="inline">', '');
                                    label = label.replace('</span>', ''); 
                                    label = label.replace('<b class="caret">', '');
                                    label = label.replace('</b></a>', '');
                                    label = label.replace('<ul class="dropdown-menu thrLegend">', '');//Lascialo così
                                    label = label.replace('<ul class="dropdown-menu">', '');
                                    label = label.replace('</ul></div>', '');
                                }
                                else
                                {
                                    if(this.category.indexOf('<span>') > 0)
                                    {
                                        label = this.category.substring(this.category.indexOf('<span>'));
                                        label = label.replace("<span>", "");
                                        label = label.replace("</span>", "");
                                    }
                                    else
                                    {
                                        label = this.category;
                                    }
                                }

                                for(var i in thresholdsJson.thresholdObject.firstAxis.fields)
                                {
                                    if(label === thresholdsJson.thresholdObject.firstAxis.fields[i].fieldName)
                                    {
                                        if(thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries.length > 0) 
                                        {
                                            for(var j in thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries)
                                            {
                                                if((parseFloat(this.y) >= thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].min)&&(parseFloat(this.y) < thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].max))
                                                {
                                                    desc = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].desc;
                                                    min = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].min;
                                                    max = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].max;
                                                    color = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries[j].color;
                                                    rangeOnThisField = true;
                                                }
                                                else
                                                {
                                                    message = "This value doesn't belong to any of the defined ranges";
                                                    dateMessage = "Date: " + dataStringInPopup;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            rangeOnThisField = false;
                                            message = "No range defined on this field";
                                            dateMessage = "Date: " + dataStringInPopup;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                rangeOnThisField = false;
                                message = "No range defined on this field";
                                dateMessage = "Date: " + dataStringInPopup;
                            }
                        }
                        else
                        {
                            rangeOnThisField = false;
                            message = "No range defined on this field";
                            dateMessage = "Date: " + dataStringInPopup;
                        }


                        if(rangeOnThisField)
                        {
                            if((desc !== null)&&(desc !== ''))
                            {
                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + dateMessage + '<br/>';
                            }
                            else
                            {
                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                       '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                       '<span style="color:' + this.color + '">\u25CF</span> ' + dateMessage + '<br/>';
                            }
                        }
                        else
                        {
                            return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                   '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>' +
                                   '<span style="color:' + this.color + '">\u25CF</span> ' + dateMessage + '<br/>';
                        }
                    }
                },
                plotOptions: {
                    series: {
                        groupPadding: 0.1,
                        pointPadding: 0,
                        stacking: stackingOption
                    },
                    bar: {
                        events: {
                            //legendItemClick: function(){ return false;/*legendItemClickValue;*/}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                        }
                    },
                    column: {
                        events: {
                            //legendItemClick: function(){ return false; /*legendItemClickValue;*/}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                        }
                    }
                },
                legend: {
                    useHTML: false,
                    labelFormatter: function () {
                        return this.name;
                    },
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    floating: false,
                    borderWidth: 0,
                    itemDistance: 24,
                    backgroundColor: 'transparent',
                    shadow: false,
                    //width: legendWidth,
                    symbolPadding: 5,
                    symbolWidth: 5,
                    itemStyle: {
                        fontFamily: 'Montserrat',
                        fontSize: styleParameters.legendFontSize + "px",
                        color: styleParameters.legendFontColor,
                        "text-align": "center",
                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                    }
                },
                credits: {
                    enabled: false
                },
                series: chartSeriesObject
            });
        }
        
        //Metodo di aggiunta dei tasti info, di disegno delle soglie e di completamento dei dropdown delle legende
        function onDraw()
        {
            var dropDownElement, infoIcon, l = null;
            //Gestori della pressione del pulsante info per i campi    
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=x]').on("click", showModalFieldsInfoFirstAxis);
            
            //Append degli elementi info alle label della legenda
            if((infoJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                var count = 0;
                $('#<?= $_REQUEST['name_w'] ?>_chartContainer').find('div.highcharts-legend .highcharts-legend-item span').each(function() 
                {
                    label = $(this).html();
                    id = label.replace(/\s/g, '_');
                    singleInfo = infoJson.secondAxis[id]; 

                    if((singleInfo !== '')&&($(this).find('i.fa-info-circle').length !== 0))
                    {
                        infoIcon = '  <i class="fa fa-info-circle handPointer" data-axis="y" data-label="' + $(this).html() + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i>';
                        $(this).append(infoIcon);
                        count++;
                    }
                });
                
                if(count > 0)
                {
                    legendItemClickValue = false;
                }
                else
                {
                    legendItemClickValue = true;
                }
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=y]').off("click");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=y]').on("click", showModalFieldsInfoSecondAxis);
            
            var thresholdObject = null;
            
            switch(chartType)
            {
                case 'horizontal':
                    var ticks = this.xAxis[0].ticks,
                    tick, i;

                    var xVal, xPix = null;   
                    var y0, y1, ya, yb, l, halfL, labelL, halfLabelL, labelX, labelY, labelText, labelObj, margin = null; 

                    var tickPositions = this.xAxis[0].tickPositions;
                    var tickAmount = this.xAxis[0].tickAmount;
                    
                    var tickPositions = this.xAxis[0].tickPositions;
                    ya = this.xAxis[0].toPixels(tickPositions[0]);
                    yb = this.xAxis[0].toPixels(tickPositions[1]);
                    l = Math.abs(yb - ya);

                    for(var i = 0; i < tickPositions.length; i++)
                    {
                        if(i < tickPositions.length - 1)
                        {
                            y0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                            y1 = this.xAxis[0].toPixels(tickPositions[parseInt(i+1)]);
                        }
                        else
                        {
                            y0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                            y1 = y0 + l;
                        }

                        halfL = l / 2;
                        margin = l * 0.1;

                        y0 = y0 - halfL + margin;
                        y1 = y1 - halfL - margin;

                        if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            thresholdObject = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries;
                            
                            if(thresholdObject.length > 0)
                            {
                                for(var j = 0; j < thresholdObject.length; j++)
                                {
                                    xVal = thresholdObject[j].max;
                                    xPix = this.yAxis[0].toPixels(xVal);
                                    
                                    this.renderer.path(['M',xPix,y0,'L',xPix, y1])
                                    .attr({
                                        'stroke-width': 1,
                                        'stroke-linecap' : 'square',
                                        'stroke-dasharray' : '6,3', 
                                        stroke: thresholdObject[j].color,
                                        id: 'thr' + i + j,
                                        zIndex: 4
                                    }).add();

                                    //Calcolo empirico della larghezza di ogni label: una parola di 4 caratteri è larga 30px, quindi ogni carattere 7.5px
                                    if(thresholdObject[j].desc !== "")
                                    {
                                        labelText = thresholdObject[j].desc;
                                    }
                                    else
                                    {
                                        labelText = thresholdObject[j].max;
                                    }

                                    labelL = 7.5*labelText.length;
                                    halfLabelL = labelL / 2;

                                    //Algoritmo empirico per il calcolo della posizione della label in base alla sua larghezza
                                    if(labelText.length <= 4)
                                    {
                                        labelX = xPix - labelL - 6;
                                    }
                                    else if(labelText.length <= 6)
                                    {
                                        labelX = xPix - labelL - 3;
                                    }
                                    else if(labelText.length <= 7)
                                    {
                                        labelX = xPix - labelL - 1;
                                    }
                                    else
                                    {
                                        labelX = xPix - labelL + 8;
                                    }

                                    labelY = y0 - 4;

                                    labelObj = this.renderer.label(labelText, labelX, labelY, 'rect', labelX, labelY, false, true)
                                    .css({
                                        color: 'black',
                                        fontFamily: 'Montserrat',
                                        fontSize: 10 + "px",
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "textOutline": "1px 1px contrast",
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                    }).attr({
                                        stroke: thresholdObject[j].color,
                                        fill: thresholdObject[j].color,
                                        zIndex: 3
                                    }).add();
                                }
                            }
                            else
                            {
                                //console.log("Nessuna soglia, vettore esistente ma vuoto (bug)");
                            }
                        }
                        else
                        {
                            //console.log("Nessuna soglia, thresholdsJson nullo");
                        }
                    }
                    break;
                    
                case 'vertical':
                    var ticks = this.xAxis[0].ticks;   
                    var yVal, yPix, tick, i, x0, x1, l, halfL, labelL, halfLabelL, labelX, labelY, labelText, labelObj, margin = null; 

                    var tickPositions = this.xAxis[0].tickPositions;

                    if(tickPositions) {
                        for (var i = 0; i < tickPositions.length; i++) {
                            if (i < tickPositions.length - 1) {
                                x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                                x1 = this.xAxis[0].toPixels(tickPositions[parseInt(i + 1)]);
                                l = Math.abs(x1 - x0);
                            } else {
                                x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                                x1 = x0 + l;
                            }

                            halfL = l / 2;
                            margin = l * 0.1;

                            x0 = x0 - halfL + margin;
                            x1 = x1 - halfL - margin;

                            if ((thresholdsJson !== null) && (thresholdsJson !== undefined) && (thresholdsJson !== 'undefined') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                console.log("THR: " + thresholdsJson);

                                thresholdObject = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries;
                                if (thresholdObject.length > 0) {
                                    //console.log("Soglie applicabili: " + JSON.stringify(thresholdObject));

                                    for (var j = 0; j < thresholdObject.length; j++) {
                                        yVal = thresholdObject[j].max;

                                        yPix = this.yAxis[0].toPixels(yVal);//Funziona bene: traduci così i valor reali delle soglie!!!

                                        this.renderer.path(['M', x0, yPix, 'L', x1, yPix])
                                            .attr({
                                                'stroke-width': 1,
                                                'stroke-linecap': 'square',
                                                'stroke-dasharray': '6,3',
                                                stroke: thresholdObject[j].color,
                                                id: 'thr' + i + j,
                                                zIndex: 4
                                            }).add();

                                        //Calcolo empirico della larghezza di ogni label: una parola di 4 caratteri è larga 30px, quindi ogni carattere 7.5px
                                        if (thresholdObject[j].desc !== "") {
                                            labelText = thresholdObject[j].desc;
                                        } else {
                                            labelText = thresholdObject[j].max;
                                        }

                                        labelL = 7.5 * labelText.length;
                                        halfLabelL = labelL / 2;

                                        //Algoritmo empirico per il calcolo della posizione della label in base alla sua larghezza
                                        if (labelText.length <= 4) {
                                            labelY = yPix + labelL + 6;
                                        } else if (labelText.length <= 6) {
                                            labelY = yPix + labelL + 3;
                                        } else if (labelText.length <= 7) {
                                            labelY = yPix + labelL + 1;
                                        } else {
                                            labelY = yPix + labelL - 8;
                                        }

                                        labelX = x0 - 5;

                                        labelObj = this.renderer.label(labelText, labelX, labelY, 'rect', labelX, labelY, false, true)
                                            .css({
                                                color: 'black',
                                                fontFamily: 'Montserrat',
                                                fontSize: 10 + "px",
                                                fontWeight: 'bold',
                                                fontStyle: 'italic',
                                                "textOutline": "1px 1px contrast",
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                            }).attr({
                                                stroke: thresholdObject[j].color,
                                                fill: thresholdObject[j].color,
                                                zIndex: 3,
                                                rotation: 270
                                            }).add();
                                    }
                                } else {
                                    //console.log("Nessuna soglia, vettore esistente ma vuoto (bug)");
                                }
                            } else {
                                //console.log("Nessuna soglia, thresholdsJson nullo");
                            }
                        }
                    }
                    break;
                    
                 
                case 'horizontalStacked':
                    var tickPositions = this.xAxis[0].tickPositions;
                    var y0 = this.xAxis[0].toPixels(tickPositions[0]);
                    var y1 = this.xAxis[0].toPixels(tickPositions[1]);
                    l = Math.abs(y1 - y0);
                    //Non disegnamo soglie in questo caso
                    break;
                    
                case 'verticalStacked':
                    //Non disegnamo soglie in questo caso
                    break;    
                    
                default:    
                    break;
            }
            
            var index = 0;
            var distanceFromTop, distanceFromBottom, legendHeight, dropClass = null;
            var wHeight = $("#<?= $_REQUEST['name_w'] ?>_div").height();
            
            //Applicazione dei menu a comparsa sulle labels che hanno già ricevuto il caret (freccia) dall'esecuzione del metodo getXAxisCategories
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                thresholdsJson.thresholdObject.firstAxis.fields.forEach(function(field)
                {
                    field.thrSeries.forEach(function(range) 
                    {
                        if(range.desc !== '')
                        {
                            dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                        }
                        else
                        {
                            dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                        }

                        dropDownElement.css("font", "bold 10px Montserrat");
                        dropDownElement.find("i").css("font-size", "12px");
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend ul").append(dropDownElement);
                    });

                    switch(chartType)
                    {
                        case 'vertical': case 'verticalStacked':
                            dropClass = 'dropup';
                            break;

                        case 'horizontal': case 'horizontalStacked':
                            legendHeight = parseInt(field.thrSeries.length*20 + 10);
                            distanceFromTop = parseInt(25 + parseInt(parseInt(index)*l) + parseInt(l/2));
                            distanceFromBottom = wHeight - distanceFromTop;

                            if(distanceFromBottom <= legendHeight)
                            {
                                dropClass = 'dropup';
                            }
                            else
                            {
                                dropClass = 'dropdown';
                            }
                            break;
                    }
                    
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend").addClass(dropClass);
                    index++;
                });
            }
        }
        
        function getXAxisCategories(series, widgetHeight)
        {
            var finalLabels, label, newLabel, id, singleInfo, dropClass, legendHeight = null;
            var isSimpleLabel = true;
            
            infoJson = null;
            
            finalLabels = [];
            
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                var thresholdObject = thresholdsJson.thresholdObject; 
            }
            
            if(series !== null)
            {
                for(var i = 0; i < series.firstAxis.labels.length; i++)
                {
                    if(infoJson !== null)
                    {
                        label = series.firstAxis.labels[i];
                        id = label.replace(/\s/g, '_');
                        
                        singleInfo = infoJson.firstAxis[id];
                        
                        //Aggiunta pulsante info
                        
                        //if(singleInfo !== '')
                        if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            //Aggiunta legenda sulle soglie
                            if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                            {
                                if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
                                {
                                    if(thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries.length > 0)
                                    {
                                        newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i>  ' +
                                            '<div style="display: inline" class="thrLegend">' + 
                                            '<a href="#" data-toggle="dropdown" style="text-decoration: none; font-size: ' + styleParameters.rowsLabelsFontSize + ' ; color: ' + styleParameters.rowsLabelsFontColor + ';" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' + 
                                                '<ul class="dropdown-menu thrLegend">' +
                                                '</ul>' +
                                            '</div>';
                                    }
                                    else
                                    {
                                        newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                                    }
                                }
                                else
                                {
                                    newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                                }
                            }
                            else
                            {
                                newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                            }
                        }
                        else
                        {
                            //Aggiunta legenda sulle soglie
                            if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                            {
                                if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
                                {
                                    if(thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries.length > 0)
                                    {
                                        newLabel = '<div style="display: inline" class="thrLegend">' + 
                                            '<a href="#" data-toggle="dropdown" style="text-decoration: none; font-size: ' + styleParameters.rowsLabelsFontSize + ' ; color: ' + styleParameters.rowsLabelsFontColor + ';" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' + 
                                                '<ul class="dropdown-menu">' +
                                                '</ul>' +
                                            '</div>';
                                    }
                                    else
                                    {
                                        newLabel = label;
                                    }
                                }
                                else
                                {
                                    newLabel = label;
                                }
                            }
                            else
                            {
                                newLabel = label;
                            }
                        }
                        
                        //Aggiunta nuova label al vettore delle labels
                        finalLabels[i] = newLabel;
                    }
                }
            }
            return finalLabels;
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
	}
        
        function buildSeriesFromAggregationData()
        {
            var seriesObj = {  
                firstAxis:{  
                   desc:"Metrics",
                   labels:[]
                },
                secondAxis:{  
                   "desc":"Values",
                   "labels":[],
                   "series":[]
                }
             };
             
             for(var i = 0; i < aggregationGetData.length; i++)
             {
                 switch(aggregationGetData[i].metricHighLevelType)
                 {
                    case "KPI":
                        seriesObj.secondAxis.labels[i] = aggregationGetData[i].metricShortDesc;
                        var roundedVal = null;
                        if((aggregationGetData[i].metricType === "Percentuale")||(pattern.test(aggregationGetData[i].metricType)))
                        {
                            roundedVal = parseFloat(JSON.parse(aggregationGetData[i].data).value_perc1);
                            roundedVal = Number(roundedVal.toFixed(2));
                            seriesObj.secondAxis.series[i] = [roundedVal];
                        }
                        else
                        {
                            switch(aggregationGetData[i].metricType)
                            {
                                case "Intero":
                                    seriesObj.secondAxis.series[i] = [parseInt(JSON.parse(aggregationGetData[i].data).value_num)];
                                    break;

                                case "Float":
                                    roundedVal = parseFloat(JSON.parse(aggregationGetData[i].data).value_num);
                                    roundedVal = Number(roundedVal.toFixed(2));
                                    seriesObj.secondAxis.series[i] = [roundedVal];
                                    break;
                                
                                //I testuali NON li aggiungiamo al grafico
                                default:
                                    break;
                            }
                        }
                        break;
                    
                    //Poi si aggiungeranno altri casi
                    default:
                        break;
                 }
            }
            return seriesObj; 
        }
        //Fine definizioni di funzione  
        
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
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        });
        
        //Nuova versione
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
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize; 
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor; 
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize; 
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                appId = widgetData.params.appId;
                flowId = widgetData.params.flowId;
                nrMetricType = widgetData.params.nrMetricType;
                serviceUri = widgetData.params.serviceUri;
                    
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
                    widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
                    widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
                    rowParameters = widgetData.params.rowParameters;
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

                if((widgetData.params.styleParameters !== "")&&(widgetData.params.styleParameters !== "null"))
                {
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                }

                if(widgetData.params.parameters !== null)
                {
                    if(widgetData.params.parameters.length > 0)
                    {
                        widgetParameters = JSON.parse(widgetData.params.parameters);
                        thresholdsJson = widgetParameters;
                    }
                }
                    
                if((widgetData.params.infoJson !== 'null')&&(widgetData.params.infoJson !== ''))
                {
                    infoJson = JSON.parse(widgetData.params.infoJson);
                    //Patch per il resize, non mostriamo i pulsanti info per ora
                    infoJson = null;
                }

                chartType = styleParameters.chartType;

                switch(styleParameters.dataLabelsRotation)
                {
                    case 'horizontal':
                        dataLabelsRotation = 0;
                        break;

                    case 'verticalAsc':
                        dataLabelsRotation = 270;
                        break;

                    case 'verticalDesc':
                        dataLabelsRotation = 90;
                        break;

                    default:
                        dataLabelsRotation = 0;
                        break;
                }

                switch(chartType)
                {
                    case 'horizontal':
                        stackingOption = null;
                        highchartsChartType = 'bar';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;

                    case 'vertical':
                        stackingOption = null;
                        highchartsChartType = 'column';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;

                    case 'horizontalStacked':
                        stackingOption = 'normal';
                        highchartsChartType = 'bar';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;

                    case 'verticalStacked':
                        stackingOption = 'normal';
                        highchartsChartType = 'column';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = + 6;
                        break;     

                    default:
                        stackingOption = null;    
                        highchartsChartType = 'bar';
                        dataLabelsAlign = 'center';
                        break;
                }
                    
                if(widgetData.params.id_metric === 'AggregationSeries')
                {
                    rowParameters = JSON.parse(rowParameters);
                    aggregationGetData = [];
                    getDataFinishCount = 0;
                    var editLabels = (JSON.parse(widgetData.params.styleParameters)).editDeviceLabels;

                    for(var i = 0; i < rowParameters.length; i++)
                    {
                        aggregationGetData[i] = false;
                    }

                    for(var i = 0; i < rowParameters.length; i++)
                    {
                        let dataOrigin = rowParameters[i].metricHighLevelType;
                        switch(dataOrigin) {
                            case "KPI":
                                index = i;
                                $.ajax({
                                    url: "../controllers/aggregationSeriesProxy.php",
                                    type: "POST",
                                    data:
                                        {
                                            dataOrigin: JSON.stringify(rowParameters[i]),
                                            index: i
                                        },
                                    async: true,
                                    dataType: 'json',
                                    success: function (data) {
                                        aggregationGetData[data.index] = data;
                                        getDataFinishCount++;

                                        //Popoliamo il widget quando sono arrivati tutti i dati
                                        if (getDataFinishCount === rowParameters.length) {
                                            series = buildSeriesFromAggregationData();

                                            widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                                            chartSeriesObject = getChartSeriesObject(series);
                                            legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                            xAxisCategories = getXAxisCategories(series, widgetHeight);

                                            if (firstLoad !== false) {
                                                showWidgetContent(widgetName);
                                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                            } else {
                                                elToEmpty.empty();
                                                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                            }

                                            drawDiagram();
                                        }
                                    },
                                    error: function (errorData) {
                                        metricData = null;
                                        console.log("Error in data retrieval");
                                        console.log(JSON.stringify(errorData));
                                        showWidgetContent(widgetName);
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                        $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                    }
                                });
                                break;

                            case "Sensor":
                                var timeRange = null;
                                var urlToCall = "";
                                var xlabels = [];
                                var deviceLabels = [];
                                var metricLabels = [];
                                let smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId.split("serviceUri=")[1];
                            //    metricType = "Float";

                                if("<?= $_REQUEST['timeRange']?>") {
                                    if("<?= $_REQUEST['timeRange'] ?>" != 'last' && "<?= $_REQUEST['timeRange'] ?>" != "") {
                                      /*  switch("<?= $_REQUEST['timeRange'] ?>") {
                                            case "4 Ore":
                                                timeRange = "fromTime=4-hour";
                                                break;

                                            case "12 Ore":
                                                timeRange = "fromTime=12-hour";
                                                break;

                                            case "Giornaliera":
                                                timeRange = "fromTime=1-day";
                                                break;

                                            case "Settimanale":
                                                timeRange = "fromTime=7-day";
                                                break;

                                            case "Mensile":
                                                timeRange = "fromTime=30-day";
                                                break;

                                            case "Annuale":
                                                timeRange = "fromTime=365-day";
                                                break;
                                        }   */

                                        urlToCall = smUrl + "&" + timeRange;
                                    } else {
                                        urlToCall = smUrl;
                                    }
                                } else {
                                    urlToCall = smUrl;
                                }

                                getSmartCitySensorValues(rowParameters, i, smUrl, null, true, function(extractedData) {

                                    if(extractedData) {
                                        seriesDataArray.push(extractedData);
                                    }
                                    else
                                    {
                                        console.log("Dati Smart City non presenti");
                                        seriesDataArray.push(undefined);
                                    }
                                    //if (endFlag === true) {
                                    // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                    if (rowParameters.length === seriesDataArray.length) {
                                        let stopFlag = 1;
                                        // DO FINAL SERIALIZATION
                                        metricLabels = getMetricLabelsForBarSeries(rowParameters);
                                        deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
                                        let mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
                                        series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels);

                                        xAxisCategories = metricLabels.slice();

                                        widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                                        chartSeriesObject = getChartSeriesObject(series, editLabels);
                                        legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                    //    xAxisCategories = getXAxisCategories(series, widgetHeight);

                                        if(firstLoad !== false)
                                        {
                                            showWidgetContent(widgetName);
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        }
                                        else
                                        {
                                            elToEmpty.empty();
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        }

                                        if (!serviceUri) {
                                            $.ajax({
                                                url: "../widgets/updateBarSeriesParameters.php",
                                                type: "GET",
                                                data: {
                                                    widgetName: "<?= $_REQUEST['name_w'] ?>",
                                                    series: series
                                                },
                                                async: true,
                                                dataType: 'json',
                                                success: function (widgetData) {

                                                },
                                                error: function (errorData) {
                                                    metricData = null;
                                                    console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                                                    console.log(JSON.stringify(errorData));
                                                }
                                            });
                                        }
                                        drawDiagram();
                                    }

                                });
                                break;

                            case "MyKPI":

                            //    var convertedData = getMyKPIValues(rowParameters[i].metricId);
                                let aggregationCell = [];
                                var xlabels = [];
                                var deviceLabels = [];
                                var metricLabels = [];
                                let kpiMetricName =  rowParameters[i].metricName;
                                let kpiMetricType =  rowParameters[i].metricType;
                                getMyKPIValues(rowParameters, i, null, 1, function(extractedData) {

                                    if(extractedData) {
                                        seriesDataArray.push(extractedData);
                                    }
                                    else
                                    {
                                        console.log("Dati Smart City non presenti");
                                        seriesDataArray.push(undefined);
                                    }
                                    //if (endFlag === true) {
                                    // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                    if (rowParameters.length === seriesDataArray.length) {
                                        let stopFlag = 1;
                                        // DO FINAL SERIALIZATION
                                        metricLabels = getMetricLabelsForBarSeries(rowParameters);
                                    //    deviceLabels = getMyKpiLabelsForBarSeries(rowParameters);
                                        deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
                                        let mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
                                        series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels);

                                    /*    for(n = 0; n < seriesDataArray.length; n++) {
                                            if (!xlabels.includes(seriesDataArray[n].metricType)) {
                                                xlabels.push(seriesDataArray[n].metricType);
                                            }
                                        }   */

                                        xAxisCategories = metricLabels.slice();

                                        widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                                        chartSeriesObject = getChartSeriesObject(series, editLabels);
                                        legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                        //    xAxisCategories = getXAxisCategories(series, widgetHeight);

                                        if(firstLoad !== false)
                                        {
                                            showWidgetContent(widgetName);
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        }
                                        else
                                        {
                                            elToEmpty.empty();
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        }

                                        if (!serviceUri) {
                                            $.ajax({
                                                url: "../widgets/updateBarSeriesParameters.php",
                                                type: "GET",
                                                data: {
                                                    widgetName: "<?= $_REQUEST['name_w'] ?>",
                                                    series: series
                                                },
                                                async: true,
                                                dataType: 'json',
                                                success: function (widgetData) {

                                                },
                                                error: function (errorData) {
                                                    metricData = null;
                                                    console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                                                    console.log(JSON.stringify(errorData));
                                                }
                                            });
                                        }
                                        drawDiagram();
                                    }


                                });
                                break;

                        }
                    }
                }
                else
                {
                    $.ajax({
                        url: getMetricDataUrl,
                        type: "GET",
                        data: {"IdMisura": [widgetData.params.id_metric]},
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            metricData = data;
                            $("#" + widgetName + "_loading").css("display", "none");

                            if(metricData.data.length !== 0)
                            {
                                metricType = metricData.data[0].commit.author.metricType;
                                series = JSON.parse(metricData.data[0].commit.author.series);

                                widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                                chartSeriesObject = getChartSeriesObject(series);
                                legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                xAxisCategories = getXAxisCategories(series, widgetHeight);

                                if(firstLoad !== false)
                                {
                                    showWidgetContent(widgetName);
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                }
                                else
                                {
                                    elToEmpty.empty();
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                }

                                drawDiagram();
                            }
                            else
                            {
                               showWidgetContent(widgetName);
                               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                               $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                               $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            } 
                        },
                        error: function(errorData)
                        {
                            metricData = null;
                            console.log("Error in data retrieval");
                            console.log(JSON.stringify(errorData));
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                    });
                }
            },
            error: function(errorData)
            {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });	
        
        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
            clearInterval(countdownRef);
            timeToReload = event.newTimeToReload;
            countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        //Fine del codice core del widget
    });
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
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>No Data Available</p>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 