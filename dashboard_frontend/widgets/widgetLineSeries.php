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
        var metricName = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        var widgetProperties, metricData, metricType, series, styleParameters, legendHeight, chartType, highchartsChartType, dataLabelsRotation, 
            dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, widgetHeight, 
            xAxisDataset, lineWidth, xAxisTitle, metricName, widgetTitle, countdownRef = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
		showHeader = false;
	}
	else
	{
		showHeader = true;
	}
        
        //Definizioni di funzione specifiche del widget
        
        //Funzione di calcolo ed applicazione dell'altezza della tabella
        function setTableHeight()
        {
            var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
            $("#<?= $_GET['name'] ?>_chartContainer").css("height", height);
        }
        
        
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = jQuery.parseJSON(widgetProperties.param.parameters);
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = jQuery.parseJSON(widgetProperties.param.infoJson);
            return infoJson;
        }
        
        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var infoJson = getInfoJson();
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
            var infoJson = getInfoJson();
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
                    format = this.series.name + ': ' + this.y;
                    break;
                    
                default:
                    format = this.y;
                    break;    
            }
            
            //test = '<div style="background-color: red">' + format + '</div>';
            //return test;
            return format;
        }
        
        function getChartSeriesObject(series)
        {
            var chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, zonesObject, zonesArray, inf, sup, i = null;
            
            if(series !== null)
            {
                chartSeriesObject = [];
                
                var seriesArray = null;
                
                if(styleParameters.xAxisDataset === series.firstAxis.desc)
                {
                    for (var i in series.secondAxis.series) 
                    {
                        seriesName = series.secondAxis.labels[i];
                        seriesValues = series.secondAxis.series[i];

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
                                        fontFamily: 'Verdana',
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
                                        fontFamily: 'Verdana',
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
                else
                {
                    for (i = 0; i < series.firstAxis.labels.length; i++) 
                    {
                        seriesName = series.firstAxis.labels[i];
                        seriesArray = [];
                        zonesArray = [];

                        for (var j in series.secondAxis.series) 
                        {
                            seriesArray[j] = series.secondAxis.series[j][i];
                        }
                        
                        if((styleParameters.barsColorsSelect === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            singleObject = {
                                name: seriesName,
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
                                        fontFamily: 'Verdana',
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
                                        fontFamily: 'Verdana',
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
            //Gestori della pressione del pulsante info per i campi    
            $('#<?= $_GET['name'] ?>_chartContainer i.fa-info-circle[data-axis=x]').on("click", showModalFieldsInfoFirstAxis);
            
            //Append degli elementi info alle label della legenda
            var infoJson = getInfoJson();
            
            if(infoJson !== null)
            {
                var count = 0;
                $('#<?= $_GET['name'] ?>_chartContainer').find('div.highcharts-legend .highcharts-legend-item span').each(function() 
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
            
            $('#<?= $_GET['name'] ?>_chartContainer i.fa-info-circle[data-axis=y]').on("click", showModalFieldsInfoSecondAxis);
            
            
            //Disegno delle soglie
            var thresholdsJson = getThresholdsJson();
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

                if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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
                                        fontFamily: 'Verdana',
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
                                        fontFamily: 'Verdana',
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
            var wHeight = $("#<?= $_GET['name'] ?>_div").height();
            
            //Applicazione dei menu a comparsa sulle labels che hanno già ricevuto il caret (freccia) dall'esecuzione del metodo getXAxisCategories
            if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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

                        dropDownElement.css("font", "bold 10px Verdana");
                        dropDownElement.find("i").css("font-size", "12px");
                        $("#<?= $_GET['name'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend ul").append(dropDownElement);
                        //$("div.thrLegend").eq(index).find("ul").append(dropDownElement);
                    });

                    //Su questo widget il menu lo facciamo comparire sempre verso l'alto
                    dropClass = 'dropup';
                    $("#<?= $_GET['name'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend").addClass(dropClass);
                    //$("div.thrLegend").eq(index).addClass(dropClass);
                    index++;
                });
            }
        }
        
        function getXAxisCategories(series, widgetHeight)
        {
            var finalLabels, label, newLabel, id, singleInfo, dropClass, legendHeight = null;
            var infoJson = getInfoJson();
            var thresholdsJson = getThresholdsJson();
            var isSimpleLabel = true;
            
            finalLabels = [];
            
            if(thresholdsJson !== null)
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
                                    if((thresholdsJson !== null)&&(thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries.length > 0))
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
                                //Aggiunta legenda sulle soglie
                                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                                {
                                    if((thresholdsJson !== null)&&(thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries.length > 0))
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
                                    if((thresholdsJson !== null)&&(thresholdsJson.thresholdObject.secondAxis.fields[i].thrSeries.length > 0))
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
                                //Aggiunta legenda sulle soglie
                                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                                {
                                    if((thresholdsJson !== null)&&(thresholdsJson.thresholdObject.secondAxis.fields[i].thrSeries.length > 0))
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

                            //Aggiunta nuova label al vettore delle labels
                            finalLabels[i] = newLabel;
                        }
                    }
                }
            }
            return finalLabels;
        }
        
        
        //Fine definizioni di funzione  
        
        //Codice core del widget
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
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_GET['name'] ?>_content").hide();
                <?= $_GET['name'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });
        
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
        if(widgetProperties !== null)
        {
            /*Inizio codice ad hoc basato sulle proprietà del widget*/
            var styleParametersString = widgetProperties.param.styleParameters;
            styleParameters = jQuery.parseJSON(styleParametersString);
            chartType = styleParameters.chartType;
            lineWidth = styleParameters.lineWidth;
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            
            switch(chartType)
            {
                case 'lines':
                    stackingOption = null;
                    highchartsChartType = 'line';
                    dataLabelsAlign = 'center';
                    dataLabelsVerticalAlign = 'middle';
                    dataLabelsY = 0;
                    break;
                    
                case 'area':
                    stackingOption = null;
                    highchartsChartType = 'area';
                    dataLabelsAlign = 'center';
                    dataLabelsVerticalAlign = 'middle';
                    dataLabelsY = 0;
                    break;
                    
                case 'stacked':
                    stackingOption = 'normal';
                    highchartsChartType = 'area';
                    dataLabelsAlign = 'center';
                    dataLabelsVerticalAlign = 'middle';
                    dataLabelsY = 0;
                    break;    
                    
                default:
                    stackingOption = null;    
                    highchartsChartType = 'line';
                    dataLabelsAlign = 'center';
                    break;
            }
            
            
            //Fine codice ad hoc basato sulle proprietà del widget
            
            metricData = getMetricData(metricName);
            if(metricData.data.length !== 0)
            {
                metricType = metricData.data[0].commit.author.metricType;
                series = JSON.parse(metricData.data[0].commit.author.series);
                
                widgetHeight = parseInt($("#<?= $_GET['name'] ?>_chartContainer").height() + 25);
                
                //Disegno del grafico
                var chartSeriesObject = getChartSeriesObject(series);
                var legendWidth = $("#<?= $_GET['name'] ?>_content").width();
                var xAxisCategories = getXAxisCategories(series, widgetHeight);
                
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
                    $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                    $("#<?= $_GET['name'] ?>_chartContainer").show();
                    $("#<?= $_GET['name'] ?>_table").show();
                }
                else
                {
                    elToEmpty.empty();
                    $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                    $("#<?= $_GET['name'] ?>_chartContainer").show();
                    $("#<?= $_GET['name'] ?>_table").show();
                }
                
                $(function () {
                    Highcharts.chart('<?= $_GET['name'] ?>_chartContainer', {
                        chart: {
                            type: highchartsChartType,
                            backgroundColor: widgetContentColor,
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
                            categories: xAxisCategories,
                            title: {
                                align: 'high',
                                offset: 20,
                                text: xAxisTitle,
                                rotation: 0,
                                y: 5,
                                style: {
                                    fontFamily: 'Verdana',
                                    fontSize: styleParameters.rowsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.rowsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            },
                            labels: {
                               enabled: true,
                               useHTML: true,
                               /*formatter: function () {
                                   var valueFormatted = this.value.replace(/ /g, '<br />');
                                   return valueFormatted;
                                   
                                   //this.chart.xAxis[0]
                                   
                                   //return '<i class="fa fa-info-circle handPointer" style="font-size: ' + rowsLabelsFontSize + 'px; color: ' + rowsLabelsFontColor + '"></i> ' + valueFormatted;
                               },*/
                               style: {
                                    fontFamily: 'Verdana',
                                    fontSize: styleParameters.rowsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    color: styleParameters.rowsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            }
                        },
                        yAxis: {
                            gridZIndex: 0,
                            title: {
                                text: null
                            },
                            labels: {
                                overflow: 'justify',
                                style: {
                                    fontFamily: 'Verdana',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            }
                        },
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
                            pointFormatter: function()
                            {
                                var field = this.series.name;
                                var thresholdsJson = getThresholdsJson();
                                var thresholdObject, desc, min, max, color, label, index, target, message, valueSource = null;
                                var rangeOnThisField = false;
                                
                                if((thresholdsJson !== null) && (thresholdsJson !== 'undefined'))
                                {
                                    if(thresholdsJson.thresholdObject.firstAxis.desc === styleParameters.xAxisDataset)
                                    {
                                        target = thresholdsJson.thresholdObject.firstAxis;
                                        valueSource = this.y;
                                    }
                                    else
                                    {
                                        target = thresholdsJson.thresholdObject.secondAxis;
                                        valueSource = this.y;
                                    }
                                    
                                    if(target.fields.length > 0)
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
                                        
                                        for(var i in target.fields)
                                        {
                                            if(label === target.fields[i].fieldName)
                                            {
                                                if(target.fields[i].thrSeries.length > 0) 
                                                {
                                                    for(var j in target.fields[i].thrSeries)
                                                    {
                                                        //if((parseFloat(this.y) >= target.fields[i].thrSeries[j].min)&&(parseFloat(this.y) < target.fields[i].thrSeries[j].max))
                                                        if((parseFloat(valueSource) >= target.fields[i].thrSeries[j].min)&&(parseFloat(valueSource) < target.fields[i].thrSeries[j].max))
                                                        {
                                                            desc = target.fields[i].thrSeries[j].desc;
                                                            min = target.fields[i].thrSeries[j].min;
                                                            max = target.fields[i].thrSeries[j].max;
                                                            color = target.fields[i].thrSeries[j].color;
                                                            rangeOnThisField = true;
                                                            
                                                            /*console.log("Valore in esame: " + parseFloat(valueSource));
                                                            console.log("Campo di soglia: " + target.fields[i].fieldName);
                                                            console.log("Min: " + min);
                                                            console.log("Max: " + max);
                                                            console.log("Desc: " + desc);*/
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    message = "This value doesn't belong to any of the defined ranges";
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        rangeOnThisField = false;
                                        message = "This value doesn't belong to any of the defined ranges";
                                    }
                                }
                                else
                                {
                                    rangeOnThisField = false;
                                    message = "This value doesn't belong to any of the defined ranges";
                                }
                                
                                
                                if(rangeOnThisField)
                                {
                                    if((desc !== null)&&(desc !== ''))
                                    {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';   
                                    }
                                    else
                                    {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' + 
                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                    }
                                }
                                else
                                {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
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
                            line: {
                                events: {
                                    legendItemClick: function(){ return false;/*legendItemClickValue;*/}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                                },
                                lineWidth: lineWidth
                            }
                        },
                        legend: {
                            useHTML: true,
                            labelFormatter: function () {
                                return this.name;
                            },
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            floating: false,
                            borderWidth: 0,
                            itemDistance: 24,
                            backgroundColor: widgetContentColor,
                            shadow: false,
                            //width: legendWidth,
                            symbolPadding: 5,
                            symbolWidth: 5,
                            itemStyle: {
                                fontFamily: 'Verdana',
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
                });
                
                
            }
            else
            {
               showWidgetContent(widgetName);
               $("#<?= $_GET['name'] ?>_chartContainer").hide();
               $("#<?= $_GET['name'] ?>_table").hide(); 
               $('#<?= $_GET['name'] ?>_noDataAlert').show();
            }        
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        //Fine del codice core del widget
    });
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
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 