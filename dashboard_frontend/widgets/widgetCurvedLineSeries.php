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
                eventLog("Returned the following ERROR in widgetCurvedLineSeries.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>  
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
    //    console.log("CurvedLineSeries: " + widgetName);
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
	var showHeader = null;
        var addSampleToTrend = null;
        var metricData, metricType, series, styleParameters, timeRange, gridLineColor, chartAxesColor, chartType, index, highchartsChartType, chartSeriesObject, legendWidth, xAxisCategories, rowParameters, aggregationGetData, getDataFinishCount, xAxisType,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, fontSize, fontColor, chartColor, dataLabelsFontSize, chartLabelsFontSize, dataLabelsFontColor, chartLabelsFontColor, appId, flowId, nrMetricType,
            widgetHeight, lineWidth, xAxisTitle, smField, metricName, widgetTitle, countdownRef, widgetParameters, thresholdsJson, infoJson = null;
        var serviceUri = "";
        var editLabels = "";
        var valueUnit = null;
        
        var pattern = /Percentuale\//;
        console.log("Entrato in widgetCurvedLineSeries --> " + widgetName); 
        var unitsWidget = [[
                'millisecond', // unit name
                [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] // allowed multiples
            ], [
                'second',
                [1, 2, 5]
            ], [
                'minute',
                [1, 3, 5]
            ], [
                'hour',
                [1, 2, 3, 4, 5, 7]
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
                null
            ]];
        
        //Definizioni di funzione specifiche del widget
        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var info = null;
            
            if(styleParameters.xAxisDataset === series.firstAxis.desc)
            {
                //Grafico non trasposto
                info = infoJson.firstAxis[id];
            }
            else
            {
                //Grafico trasposto
                info = infoJson.secondAxis[id];
            }
            
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
            var info = null;
            
            if(styleParameters.xAxisDataset === series.firstAxis.desc)
            {
                //Grafico non trasposto
                info = infoJson.secondAxis[id];
            }
            else
            {
                //Grafico trasposto
                info = infoJson.firstAxis[id];
            }

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
                    format = this.series.name_w + ': ' + this.y;
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
                
                //Non trasposto
                if(styleParameters.xAxisDataset === series.firstAxis.desc)
                {
                    for(var i in series.secondAxis.series) 
                    {
                        if (xAxisLabelsEdit != null) {
                            seriesName = xAxisLabelsEdit[i];
                        } else {
                            seriesName = series.secondAxis.labels[i];
                        }
                        seriesValues = series.secondAxis.series[i];

                        if((styleParameters.barsColorsSelect === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            singleObject = {
                                name_w: seriesName,
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
                                name_w: seriesName,
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
                else//Trasposto
                {
                    for (i = 0; i < series.firstAxis.labels.length; i++) 
                    {
                        if (xAxisLabelsEdit != null) {
                            seriesName = xAxisLabelsEdit[i];
                        } else {
                            seriesName = series.secondAxis.labels[i];
                        }
                        seriesArray = [];
                        zonesArray = [];

                        for (var j in series.secondAxis.series) 
                        {
                            seriesArray[j] = series.secondAxis.series[j][i];
                        }
                        
                        if((styleParameters.barsColorsSelect === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            singleObject = {
                                name_w: seriesName,
                                data: seriesArray,
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
                                name_w: seriesName,
                                data: seriesArray,
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

            }
            return chartSeriesObject;
        }
        
        //Metodo di aggiunta dei tasti info, di disegno delle soglie e di completamento dei dropdown delle legende
        function onDraw()
        {
            var dropDownElement, infoIcon, l, trasposto = null;
            
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartColorMenuItem").trigger('chartCreated');
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartPlaneColorMenuItem").trigger('chartCreated');
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartLabelsColorMenuItem").trigger('chartCreated');
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartAxesColorMenuItem").trigger('chartCreated');
            
            //Gestori della pressione del pulsante info per i campi    
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=x]').on("click", showModalFieldsInfoFirstAxis);
            
            //Append degli elementi info alle label della legenda
            
            if(infoJson !== null)
            {
                var count = 0;
                $('#<?= $_REQUEST['name_w'] ?>_chartContainer').find('div.highcharts-legend .highcharts-legend-item span').each(function() 
                {
                    label = $(this).html();
                    id = label.replace(/\s/g, '_');
                    
                    if(styleParameters.xAxisDataset === series.firstAxis.desc)
                    {
                        //Grafico non trasposto
                        singleInfo = infoJson.secondAxis[id];
                        trasposto = false;
                    }
                    else
                    {
                        //Grafico trasposto
                        singleInfo = infoJson.firstAxis[id];
                        trasposto = true;
                    }

                    //if(singleInfo !== '')
                    if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=y]').on("click", showModalFieldsInfoSecondAxis);
            
            
            //Disegno delle soglie
            var thresholdObject = null;
            
            var ticks = this.yAxis[0].ticks;   
            var yVal, yValOld, yPix, yPixOld, tick, i, x0, x1, l, halfL, labelL, halfLabelL, labelX, labelY, labelText, labelObj, margin, rectH = null; 

            var tickPositions = this.xAxis[0].tickPositions;

            x0 = this.xAxis[0].toPixels(this.xAxis[0].tickPositions[0]);
            x1 = this.xAxis[0].toPixels(this.xAxis[0].tickPositions[1]);
            l = Math.abs(x1 - x0);

            for (var i = 0; i < tickPositions.length; i++)
            {
                if(i < tickPositions.length - 1)
                {
                    x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                    x1 = this.xAxis[0].toPixels(tickPositions[parseInt(i+1)]);
                }
                else
                {
                    x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                    x1 = x0 + l;
                }

                x0 = x0 - l/2;
                x1 = x1 - l/2;

                if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                {
                    if(thresholdsJson.thresholdObject.firstAxis.desc === styleParameters.xAxisDataset)
                    {
                        thresholdObject = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries;
                    }
                    else
                    {
                        thresholdObject = thresholdsJson.thresholdObject.secondAxis.fields[i].thrSeries;
                    }
                    
                    if(thresholdObject.length > 0)
                    {
                        for(var j = 0; j < thresholdObject.length; j++)
                        {
                            switch(styleParameters.alrLook)
                            {
                                case "none":
                                    break;

                                case "lines":
                                    yVal = thresholdObject[j].max;
                                    yPix = this.yAxis[0].toPixels(yVal);

                                    this.renderer.path(['M',x0,yPix,'L',x1,yPix])
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

                                    labelY = yPix + 12;
                                    labelX = x0;

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
                                        zIndex: 4,
                                        rotation: 0
                                    }).add();

                                    break;

                                case "areas":
                                    yValOld = thresholdObject[j].min;
                                    yVal = thresholdObject[j].max;
                                    yPix = this.yAxis[0].toPixels(yVal);
                                    yPixOld = this.yAxis[0].toPixels(yValOld);
                                    rectH = Math.abs(yPix - yPixOld);
                                    var tcolor = new tinycolor (thresholdObject[j].color);
                                    var rgbColor = tcolor.toRgbString();
                                    var hslColor = tcolor.toHsl();
                                    hslColor.l = hslColor.l + 0.3;
                                    var hslString = "hsl(" + hslColor.h + ", " + hslColor.s*100 + "%, " + hslColor.l*100 + "%)";

                                    this.renderer.rect(x0,yPix, l, rectH, 0)
                                    .attr({
                                        'stroke-width': 0,
                                        stroke: hslString,
                                        fill: hslString,
                                        zIndex: 0
                                    })
                                    .add();

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

                                    labelY = yPix + 14;
                                    labelX = x0;

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
                                        zIndex: 4,
                                        rotation: 0
                                    }).add();
                                    break;

                                default:
                                    break;    
                            }
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
            
            var index = 0;
            var distanceFromTop, distanceFromBottom, legendHeight, dropClass, axis = null;
            var wHeight = $("#<?= $_REQUEST['name_w'] ?>_div").height();
            
            //Applicazione dei menu a comparsa sulle labels che hanno già ricevuto il caret (freccia) dall'esecuzione del metodo getXAxisCategories
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                if(trasposto === false)
                {
                    axis = thresholdsJson.thresholdObject.firstAxis;
                }
                else
                {
                    axis = thresholdsJson.thresholdObject.secondAxis;
                }
        
                //thresholdsJson.thresholdObject.firstAxis.fields.forEach(function(field)
                axis.fields.forEach(function(field)
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
                    
                    //Su questo widget il menu lo facciamo comparire sempre verso l'alto
                    dropClass = 'dropup';
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend").addClass(dropClass);
                    index++;
                });
            }
        }
        
        function getXAxisCategories(series, widgetHeight)
        {
            var finalLabels, label, newLabel, id, singleInfo, dropClass, legendHeight = null;
            var isSimpleLabel = true;
            
            finalLabels = [];
            
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
            {
                var thresholdObject = thresholdsJson.thresholdObject;                
            }
            
            if(series !== null)
            {
                //Non trasposto
                if(styleParameters.xAxisDataset === series.firstAxis.desc)
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
                else//Trasposto
                {
                    for(var i = 0; i < series.secondAxis.labels.length; i++)
                    {
                        if(infoJson !== null)
                        {
                            label = series.secondAxis.labels[i];
                            id = label.replace(/\s/g, '_');

                            singleInfo = infoJson.secondAxis[id];

                            //Aggiunta pulsante info
                            //if(singleInfo !== '')
                            if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                            {
                                //Aggiunta legenda sulle soglie
                                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                                {
                                    if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
                                    {
                                        if(thresholdsJson.thresholdObject.secondAxis.fields[i].thrSeries.length > 0)
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
                                        if(thresholdsJson.thresholdObject.secondAxis.fields[i].thrSeries.length > 0)
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
                
            }
            return finalLabels;
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
	}

        function drawDiagram(timeDomain)
        {
            if(timeDomain)
            {
                xAxisType = 'datetime';
                xAxisCategories = null;
            }
            else
            {
                xAxisType = null;
            }

            let yAxisText = null;
            if (chartSeriesObject.valueUnit != null) {
                yAxisText = chartSeriesObject.valueUnit;
            }

            if (chartSeriesObject[0] != null) {
                if (chartSeriesObject[0].data.length > 0) {

                    Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                        chart: {
                            type: highchartsChartType,
                            backgroundColor: 'transparent',
                            //Funzione di applicazione delle soglie
                            events: {
                                load: onDraw
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
                            type: xAxisType,
                            //    units: unitsWidget,
                            gridLineWidth: 0,
                            lineColor: chartAxesColor,
                            categories: xAxisCategories,
                            title: {
                                align: 'high',
                                offset: 20,
                                text: xAxisTitle,
                                rotation: 0,
                                y: 5,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.rowsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: chartLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            },
                            labels: {
                                enabled: true,
                                useHTML: false,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.rowsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    color: chartLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            }
                        },
                        yAxis: {
                            lineWidth: 1,
                            lineColor: chartAxesColor,
                            gridLineWidth: 1,
                            gridLineColor: gridLineColor,
                            gridZIndex: 0,
                            title: {
                                //text: null
                                text: yAxisText
                            },
                            labels: {
                                overflow: 'justify',
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    color: chartLabelsFontColor,
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
                            pointFormatter: function () {
                                var field = this.series.name_w;
                                var thresholdObject, desc, min, max, color, label, index, target, message,
                                    valueSource = null;
                                var rangeOnThisField = false;

                                if ((thresholdsJson !== null) && (thresholdsJson !== undefined) && (thresholdsJson !== 'undefined') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                    if (thresholdsJson.thresholdObject.firstAxis.desc === styleParameters.xAxisDataset) {
                                        target = thresholdsJson.thresholdObject.firstAxis;
                                        valueSource = this.y;
                                    } else {
                                        target = thresholdsJson.thresholdObject.secondAxis;
                                        valueSource = this.y;
                                    }

                                    if (target.fields.length > 0) {
                                        if (this.category.indexOf('thrLegend') > 0) {
                                            label = this.category.substring(this.category.indexOf('<span class="inline">'));
                                            label = label.replace('<span class="inline">', '');
                                            label = label.replace('</span>', '');
                                            label = label.replace('<b class="caret">', '');
                                            label = label.replace('</b></a>', '');
                                            label = label.replace('<ul class="dropdown-menu thrLegend">', '');//Lascialo così
                                            label = label.replace('<ul class="dropdown-menu">', '');
                                            label = label.replace('</ul></div>', '');
                                        } else {
                                            if (this.category.indexOf('<span>') > 0) {
                                                label = this.category.substring(this.category.indexOf('<span>'));
                                                label = label.replace("<span>", "");
                                                label = label.replace("</span>", "");
                                            } else {
                                                label = this.category;
                                            }
                                        }

                                        for (var i in target.fields) {
                                            if (label === target.fields[i].fieldName) {
                                                if (target.fields[i].thrSeries.length > 0) {
                                                    for (var j in target.fields[i].thrSeries) {
                                                        if ((parseFloat(valueSource) >= target.fields[i].thrSeries[j].min) && (parseFloat(valueSource) < target.fields[i].thrSeries[j].max)) {
                                                            desc = target.fields[i].thrSeries[j].desc;
                                                            min = target.fields[i].thrSeries[j].min;
                                                            max = target.fields[i].thrSeries[j].max;
                                                            color = target.fields[i].thrSeries[j].color;
                                                            rangeOnThisField = true;
                                                        }
                                                    }
                                                } else {
                                                    message = "This value doesn't belong to any of the defined ranges";
                                                }
                                            }
                                        }
                                    } else {
                                        rangeOnThisField = false;
                                        message = "No range defined on this field";
                                    }
                                } else {
                                    rangeOnThisField = false;
                                    message = "No range defined on this field";
                                }

                                if (rangeOnThisField) {
                                    if ((desc !== null) && (desc !== '')) {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';
                                    } else {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                    }
                                } else {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                                }
                            }
                        },
                        plotOptions: {
                            series: {
                                groupPadding: 0.1,
                                pointPadding: 0,
                                stacking: stackingOption,
                                states: {
                                    hover: {
                                        enabled: false
                                    }
                                }
                            },
                            spline: {
                                events: {
                                    //legendItemClick: function(){ return false;}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                                },
                                lineWidth: lineWidth
                            },
                            areaspline: {
                                events: {
                                    //legendItemClick: function(){ return false;}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                                },
                                lineWidth: lineWidth
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
                            symbolPadding: 5,
                            symbolWidth: 5,
                            itemStyle: {
                                fontFamily: 'Montserrat',
                                fontSize: styleParameters.legendFontSize + "px",
                                color: chartLabelsFontColor,
                                "text-align": "center",
                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: chartSeriesObject
                    });

                } else {

                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                    //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();

                }
            } else {

                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();

            }

        }
        
        function compareSeriesData(a, b)
        {
            var x = a[0];
            var y = b[0];
            
            if(x < y)
            {
                return -1
            }
            else
            {
                if(x > y)
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
        }
        
        function buildSeriesFromAggregationData()
        {
            var roundedVal, singleSeriesData, singleSample, sampleTime, seriesSingleObj = null;
            chartSeriesObject = [];
            
             for(var i = 0; i < aggregationGetData.length; i++)
             {
                singleSeriesData = [];
                
                 switch(aggregationGetData[i].metricHighLevelType)
                 {
                    case "KPI":
                        if((aggregationGetData[i].metricType === "Percentuale")||(pattern.test(aggregationGetData[i].metricType)))
                        {
                            for(var j = 0; j < aggregationGetData[i].data.length; j++)
                            {
                                roundedVal = parseFloat(aggregationGetData[i].data[j].value_perc1);
                                roundedVal = Number(roundedVal.toFixed(2));
                                sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                              //  sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                singleSample = [sampleTime, roundedVal];
                                singleSeriesData.push(singleSample);
                            }
                        }
                        else
                        {
                            switch(aggregationGetData[i].metricType)
                            {
                                case "Intero":
                                    for(var j = 0; j < aggregationGetData[i].data.length; j++)
                                    {
                                        roundedVal = parseInt(aggregationGetData[i].data[j].value_num);
                                        sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                                     //   sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                        singleSample = [sampleTime, roundedVal];
                                        singleSeriesData.push(singleSample);
                                    }
                                    break;

                                case "Float":
                                    for(var j = 0; j < aggregationGetData[i].data.length; j++)
                                    {
                                        roundedVal = parseFloat(aggregationGetData[i].data[j].value_num);
                                        roundedVal = Number(roundedVal.toFixed(2));
                                        sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                                    //    sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                        singleSample = [sampleTime, roundedVal];
                                        singleSeriesData.push(singleSample);
                                    }
                                    break;

                                //I testuali NON li aggiungiamo al grafico
                                default:
                                    break;
                            }
                        }

                        seriesSingleObj = {
                            showInLegend: true,
                            name: aggregationGetData[i].metricShortDesc,
                            data: singleSeriesData,
                            color: styleParameters.barsColors[i],
                            dataLabels: {
                                useHTML: false,
                                enabled: false,
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

                        chartSeriesObject.push(seriesSingleObj);
                        break;

                    case "MyKPI":

                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);

                        var resultsArray = smPayload;

                        var objName = null;
                        if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }

                        for(var j = 0; j < resultsArray.length; j++)
                        {
                            newVal = resultsArray[j].value;
                            addSampleToTrend = true;
                            newTime = resultsArray[j].insertTime;
                            chartSeriesObject.valueUnit = "";

                            if((newVal.trim() !== '')&&(addSampleToTrend))
                            {
                                roundedVal = parseFloat(newVal);
                                roundedVal = Number(roundedVal.toFixed(2));
                                //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                sampleTime = parseInt(new Date(newTime).getTime());
                                singleSample = [sampleTime, roundedVal];
                                singleSeriesData.push(singleSample);
                            }
                        }

                        seriesSingleObj = {
                            showInLegend: true,
                            name: objName,
                            data: singleSeriesData,
                            color: styleParameters.barsColors[i],
                            dataLabels: {
                                useHTML: false,
                                enabled: false,
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

                        if (aggregationGetData[i].metricValueUnit != null) {
                            chartSeriesObject.valueUnit = aggregationGetData[i].metricValueUnit;
                        }

                        chartSeriesObject.push(seriesSingleObj);

                        break;

                    case "Sensor":
                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);
                        chartSeriesObject.valueUnit = "";

                        var objName = null;
                        if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }

                        if(smPayload.hasOwnProperty('trends'))
                        {
                            var resultsArray = smPayload.predictions;
                            var newVal, newDay, newHour = null;

                            for(var j = 0; j < resultsArray.length; j++)
                            {

                                for(var key in resultsArray[j])
                                {
                                    if(key !== 'datePrediction')
                                    {
                                        newVal = resultsArray[j][key];
                                    }
                                }
                                newTime = resultsArray[j].datePrediction;

                                if(newVal.trim() !== '')
                                {
                                    roundedVal = parseFloat(newVal);
                                    roundedVal = Number(roundedVal.toFixed(2));
                                    //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                    sampleTime = parseInt(new Date(newTime).getTime());
                                    singleSample = [sampleTime, roundedVal];
                                    singleSeriesData.push(singleSample);
                                }
                            }

                            seriesSingleObj = {
                                showInLegend: true,
                            //    name: aggregationGetData[i].metricName,
                                name: objName,
                                data: singleSeriesData,
                                color: styleParameters.barsColors[i],
                                dataLabels: {
                                    useHTML: false,
                                    enabled: false,
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

                            chartSeriesObject.push(seriesSingleObj);
                        }
                        else
                        {
                            if(smPayload.hasOwnProperty('realtime'))
                            {
                                if(smPayload.realtime.hasOwnProperty('results'))
                                {
                                    var resultsArray = smPayload.realtime.results.bindings;
                                    var newVal, newTime = null;
                                    for(var j = 0; j < resultsArray.length; j++)
                                    {
                                        newVal = resultsArray[j][smField].value;
                                        addSampleToTrend = true;

                                        if(resultsArray[j].hasOwnProperty("updating"))
                                        {
                                            newTime = resultsArray[j].updating.value;
                                        }
                                        else
                                        {
                                            if(resultsArray[j].hasOwnProperty("measuredTime"))
                                            {
                                                newTime = resultsArray[j].measuredTime.value;
                                            }
                                            else
                                            {
                                                if(resultsArray[j].hasOwnProperty("instantTime"))
                                                {
                                                    newTime = resultsArray[j].instantTime.value;
                                                }
                                                else
                                                {
                                                    addSampleToTrend = false;
                                                }
                                            }
                                        }

                                        if((newVal.trim() !== '')&&(addSampleToTrend))
                                        {
                                            roundedVal = parseFloat(newVal);
                                            roundedVal = Number(roundedVal.toFixed(2));
                                            //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                            sampleTime = parseInt(new Date(newTime).getTime());
                                            singleSample = [sampleTime, roundedVal];
                                            singleSeriesData.push(singleSample);
                                        }
                                    }

                                    seriesSingleObj = {
                                        showInLegend: true,
                                        name: objName,
                                        data: singleSeriesData,
                                        color: styleParameters.barsColors[i],
                                        dataLabels: {
                                            useHTML: false,
                                            enabled: false,
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

                                    chartSeriesObject.push(seriesSingleObj);
                                }

                            }
                        }

                        if (smPayload.Service != null) {
                            if (smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                chartSeriesObject.valueUnit = smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit;
                            }
                        } else if (smPayload.Sensor != null) {
                            if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                chartSeriesObject.valueUnit = smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit;
                            }
                        }

                        //console.log(aggregationGetData);
                        break;
                    
                    //Poi si aggiungeranno altri casi
                    default:
                        console.log("Default");
                        break;
                 }
            }
            return null; 
        }
        
        function populateWidget(fromAggregate, localTimeRange)
        {
            if(fromAggregate)
            {
                setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                
                aggregationGetData = [];
                getDataFinishCount = 0;

                for(var i = 0; i < rowParameters.length; i++)
                {
                    aggregationGetData[i] = false;
                }

                for(var i = 0; i < rowParameters.length; i++)
                {
                    index = i;
                    $.ajax({
                        url: "../controllers/aggregationSeriesProxy.php",
                        type: "POST",
                        data: 
                        {
                            dataOrigin: JSON.stringify(rowParameters[i]),
                            index: i,
                            timeRange: localTimeRange,
                            field: smField
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            aggregationGetData[data.index] = data;
                            getDataFinishCount++;
                            var deviceLabels = [];
                            var metricLabels = [];

                            //Popoliamo il widget quando sono arrivati tutti i dati
                            if(getDataFinishCount === rowParameters.length)
                            {
                                widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);
                                legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                editLabels = styleParameters.editDeviceLabels;
                                buildSeriesFromAggregationData();

                                metricLabels = getMetricLabelsForBarSeries(rowParameters);
                            //    deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
                                for (let n = 0; n < chartSeriesObject.length; n++) {
                                    deviceLabels[n] = chartSeriesObject[n].name;
                                }
                            //    let mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
                                series = serializeDataForSeries(metricLabels, deviceLabels);

                                if(firstLoad !== false)
                                {
                                    showWidgetContent(widgetName);
                                 //   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                }
                                else
                                {
                                    elToEmpty.empty();
                                //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
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
                                            var stopFlag = 1;
                                        },
                                        error: function (errorData) {
                                          /*  metricData = null;
                                            console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                                            console.log(JSON.stringify(errorData)); */
                                        }
                                    });
                                }

                                drawDiagram(true);
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
                        //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                    });
                }
            }
            else
            {
                $.ajax({
                    url: getMetricDataUrl,
                    type: "GET",
                    data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
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

                            //Disegno del grafico
                            chartSeriesObject = getChartSeriesObject(series);
                            legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                            xAxisCategories = getXAxisCategories(series, widgetHeight);

                            //Non trasposto
                            if(styleParameters.xAxisDataset === series.firstAxis.desc)
                            {
                                xAxisTitle = series.firstAxis.desc;
                            }
                            else//Trasposto
                            {
                                xAxisTitle = series.secondAxis.desc;
                            }

                            if(firstLoad !== false)
                            {
                                showWidgetContent(widgetName);
                            //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                            }
                            else
                            {
                                elToEmpty.empty();
                            //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                            }

                            drawDiagram(false);
                        }
                        else
                        {
                           showWidgetContent(widgetName);
                           $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                           $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                        //   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        } 
                    },
                    error: function()
                    {
                        metricData = null;
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        showWidgetContent(widgetName);
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                    //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                    }
                });
            }
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
                gridLineColor = widgetData.params.chartPlaneColor;
                chartAxesColor = widgetData.params.chartAxesColor;
                
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
                lineWidth = styleParameters.lineWidth;
                
                switch(chartType)
                {
                    case 'lines':
                        stackingOption = null;
                        highchartsChartType = 'spline';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;

                    case 'area':
                        stackingOption = null;
                        highchartsChartType = 'areaspline';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;

                    case 'stacked':
                        stackingOption = 'normal';
                        highchartsChartType = 'areaspline';
                        dataLabelsAlign = 'center';
                        dataLabelsVerticalAlign = 'middle';
                        dataLabelsY = 0;
                        break;    

                    default:
                        stackingOption = null;    
                        highchartsChartType = 'spline';
                        dataLabelsAlign = 'center';
                        break;
                }
                
                if(widgetData.params.id_metric === 'AggregationSeries')
                {
                    rowParameters = JSON.parse(rowParameters);
                    timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange);
                }
                else
                {
                    populateWidget(false, null);
                }
            },
            error: function(errorData)
            {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
            //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
            }
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").off('changeTimeRangeEvent');
        $("#<?= $_REQUEST['name_w'] ?>").on('changeTimeRangeEvent', function(event){
            populateWidget(true, event.newTimeRange);
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
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
        <!--    <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>    -->
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 