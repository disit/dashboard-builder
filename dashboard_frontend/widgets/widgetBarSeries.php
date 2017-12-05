<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
        var elToEmpty = $("#<?= $_GET['name'] ?>_table");
        var url = "<?= $_GET['link_w'] ?>"; 
        var widgetProperties, metricData, metricType, series, styleParameters, legendHeight, chartType, highchartsChartType, 
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, 
            widgetHeight, metricName, widgetTitle, countdownRef = null;
        var headerHeight = 25;      
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';
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
        
        //Definizioni di funzione specifiche del widget
        
        //Funzione di calcolo ed applicazione dell'altezza della tabella
        function setTableHeight()
        {
            var height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
            $("#<?= $_GET['name'] ?>_table").css("height", height);
        }
        
        
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson()
        {
            var thresholdsJson = jQuery.parseJSON(widgetProperties.param.parameters);
            return thresholdsJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
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
            var chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, zonesObject, zonesArray, inf, sup, i = null;
            
            if(series !== null)
            {
                chartSeriesObject = [];
                
                var seriesArray = null;
                    
                    for (var i in series.secondAxis.series) 
                    {
                        seriesName = series.secondAxis.labels[i];
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
            return chartSeriesObject;
        }
        
        //Metodo di aggiunta dei tasti info, di disegno delle soglie e di completamento dei dropdown delle legende
        function onDraw()
        {
            var dropDownElement, infoIcon, l = null;
            //Gestori della pressione del pulsante info per i campi    
            $('#<?= $_GET['name'] ?>_chartContainer i.fa-info-circle[data-axis=x]').on("click", showModalFieldsInfoFirstAxis);
            
            //Append degli elementi info alle label della legenda
            var infoJson = getInfoJson();
            
            if((infoJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                var count = 0;
                $('#<?= $_GET['name'] ?>_chartContainer').find('div.highcharts-legend .highcharts-legend-item span').each(function() 
                {
                    label = $(this).html();
                    id = label.replace(/\s/g, '_');
                    singleInfo = infoJson.secondAxis[id]; 

                    if(singleInfo !== '')
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
            
            var thresholdsJson = getThresholdsJson();
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

                    for (var i = 0; i < tickPositions.length; i++)
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

                        if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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
                                        fontFamily: 'Verdana',
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

                    for (var i = 0; i < tickPositions.length; i++)
                    {
                        if(i < tickPositions.length - 1)
                        {
                            x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                            x1 = this.xAxis[0].toPixels(tickPositions[parseInt(i+1)]);
                            l = Math.abs(x1 - x0);
                        }
                        else
                        {
                            x0 = this.xAxis[0].toPixels(tickPositions[parseInt(i)]);
                            x1 = x0 + l;
                        }

                        halfL = l / 2;
                        margin = l * 0.1;

                        x0 = x0 - halfL + margin;
                        x1 = x1 - halfL - margin;

                        if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            thresholdObject = thresholdsJson.thresholdObject.firstAxis.fields[i].thrSeries;
                            if(thresholdObject.length > 0)
                            {
                                //console.log("Soglie applicabili: " + JSON.stringify(thresholdObject));

                                for(var j = 0; j < thresholdObject.length; j++)
                                {
                                    yVal = thresholdObject[j].max;

                                    yPix = this.yAxis[0].toPixels(yVal);//Funziona bene: traduci così i valor reali delle soglie!!!
                                    
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

                                    //Algoritmo empirico per il calcolo della posizione della label in base alla sua larghezza
                                    if(labelText.length <= 4)
                                    {
                                        labelY = yPix + labelL + 6;
                                    }
                                    else if(labelText.length <= 6)
                                    {
                                        labelY = yPix + labelL + 3;
                                    }
                                    else if(labelText.length <= 7)
                                    {
                                        labelY = yPix + labelL + 1;
                                    }
                                    else
                                    {
                                        labelY = yPix + labelL - 8;
                                    }

                                    labelX = x0 - 5;

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
                                        zIndex: 3,
                                        rotation: 270
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
            var wHeight = $("#<?= $_GET['name'] ?>_div").height();
            
            //Applicazione dei menu a comparsa sulle labels che hanno già ricevuto il caret (freccia) dall'esecuzione del metodo getXAxisCategories
            if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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

                        dropDownElement.css("font", "bold 10px Verdana");
                        dropDownElement.find("i").css("font-size", "12px");
                        $("#<?= $_GET['name'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend ul").append(dropDownElement);
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
                    
                    $("#<?= $_GET['name'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(index).find("div.thrLegend").addClass(dropClass);
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
            
            if((thresholdsJson !== null)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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
            
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            
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
                            //categories: series.firstAxis.labels,
                            categories: xAxisCategories,
                            title: {
                                align: 'high',
                                offset: 20,
                                text: series.firstAxis.desc,
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
                            min: 0,
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
                                var thresholdObject, desc, min, max, color, label, index, message = null;
                                var rangeOnThisField = false;
                                
                                if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
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
                                stacking: stackingOption
                            },
                            bar: {
                                events: {
                                    legendItemClick: function(){ return false;/*legendItemClickValue;*/}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                                }
                            },
                            column: {
                                events: {
                                    legendItemClick: function(){ return false; /*legendItemClickValue;*/}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
                                }
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