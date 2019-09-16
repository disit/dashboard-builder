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
                eventLog("Returned the following ERROR in widgetRadarSeries.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>  
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= escapeForJS($_REQUEST['color_w']) ?>';
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var chart, widgetProperties, metricData, metricType, series, styleParameters, legendHeight, chartType, highchartsChartType, 
                dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, 
                stackingOption, widgetHeight, metricName, widgetTitle, countdownRef, widgetParameters, thresholdsJson, infoJson = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
	{
		showHeader = false;
	}
	else
	{
		showHeader = true;
	}  
        
        //Definizioni di funzione specifiche del widget
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        /*function getThresholdsJson()
        {
            var thresholdsJson = jQuery.parseJSON(widgetProperties.param.parameters);
            return thresholdsJson;
        }*/
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        /*function getInfoJson()
        {
            var infoJson = jQuery.parseJSON(widgetProperties.param.infoJson);
            return infoJson;
        }*/
        
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
                                },
                                states: {
                                    hover: {
                                        enabled: false
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
                                },
                                states: {
                                    hover: {
                                        enabled: false
                                    }
                                }
                            };
                        }
                        chartSeriesObject.push(singleObject);
                    }
            }
            return chartSeriesObject;
        }
        
        function compareUpperBounds(a,b) 
        {
            if (a.value < b.value)
            {
                return -1;
            }
            else if (a.value > b.value)
            {
              return 1;
            }
            else
            {
               return 0;
            }
        }
        
        //Metodo di aggiunta dei tasti info, di disegno delle soglie e di completamento dei dropdown delle legende
        function onDraw()
        {
            var dropDownElement, infoIcon, l = null;
            
            //Disegno delle soglie tratteggiate
            drawThresholds(this);
            
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
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer i.fa-info-circle[data-axis=y]').on("click", showModalFieldsInfoSecondAxis);
            
            var index = 0;
            var label, elementUpperBounds, newUpperBound, dropDownElement, distanceFromTop, distanceFromBottom, legendHeight, dropClass = null;
            var wHeight = $("#<?= $_REQUEST['name_w'] ?>_div").height();
            
            //Applicazione dei menu a comparsa sulle labels che hanno già ricevuto il caret (freccia) dall'esecuzione del metodo getXAxisCategories
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                if(thresholdsJson.thresholdArray.length > 0)
                {
                    for(var i = 0; i < $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").length; i++)
                    {
                        label = $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(i).find("div.thrLegend a span.inline").html();
                        
                        elementUpperBounds = [];
                        
                        //Reperimento degli upper bounds per questo campo
                        for(var j = 0; j < thresholdsJson.thresholdArray.length; j++)
                        {
                            newUpperBound = {
                                color: thresholdsJson.thresholdArray[j].color,
                                value: parseInt(thresholdsJson.thresholdArray[j][label]),
                                desc: thresholdsJson.thresholdArray[j].desc
                            };
                            elementUpperBounds.push(newUpperBound);
                        }
                        
                        //Ordinamento crescente del vettore degli upper bounds
                        elementUpperBounds.sort(compareUpperBounds);
                        
                        //Aggiunta degli upper bound alla legenda
                        for(var k = 0; k < elementUpperBounds.length; k++)
                        {
                            var max = elementUpperBounds[k].value;
                            var desc = elementUpperBounds[k].desc;
                            var color = elementUpperBounds[k].color ;
                            
                            if(k === 0)
                            {
                                if(elementUpperBounds[k].desc !== '')
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + color + '"></div>&nbsp;<&nbsp;' + max + '&nbsp;&nbsp;<b>' + desc + '</b></a></li>');
                                }
                                else
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + color + '"></div>&nbsp;<&nbsp;' + max + '&nbsp;&nbsp;</a></li>');
                                }
                            }
                            else
                            {
                                var min = elementUpperBounds[k - 1].value;
                                
                                if(elementUpperBounds[k].desc !== '')
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + color + '"></div>&nbsp;' + min + ' <i class="fa fa-arrows-h"></i> ' + max + '&nbsp;&nbsp;<b>' + desc + '</b></a></li>');
                                }
                                else
                                {
                                    dropDownElement = $('<li><a href="#"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + color + '"></div>&nbsp;&nbsp;' + min + ' <i class="fa fa-arrows-h"></i> ' + max + '</a></li>');
                                }
                            }

                            var parentLegendElement = $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(i);
                            var elementLeftPosition = parentLegendElement.position().left;
                            var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                            var legendMargin = null;
                            
                            if(elementLeftPosition > (widgetWidth / 2))
                            {
                                legendMargin = 300;
                            }
                            else
                            {
                                legendMargin = 0;
                            }

                            dropDownElement.css("font", "bold 10px Montserrat");
                            dropDownElement.find("i").css("font-size", "12px");
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(i).find("div.thrLegend ul").append(dropDownElement);
                            dropClass = 'dropup';
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(i).find("div.thrLegend").addClass(dropClass);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer div.highcharts-xaxis-labels span[opacity='1']").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                        }
                    }
                }
            }
        }
        
        function getXAxisCategories(series, widgetHeight)
        {
            var finalLabels, label, newLabel, id, singleInfo, dropClass, legendHeight = null;
            var isSimpleLabel = true;
            
            finalLabels = [];
            
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
                        if((singleInfo !== '')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            //Aggiunta legenda sulle soglie
                            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined'))
                            {
                                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                                {
                                    if(thresholdsJson.thresholdArray.length > 0)
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
                                    if(thresholdsJson.thresholdArray.length > 0)
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
                    else
                    {
                        finalLabels[i] = series.firstAxis.labels[i];
                    }
                }
            }
            return finalLabels;
        }
        
        function drawThresholds(chartRef)
        {
            //Testing disegno soglie su poligono
            var i, j, color, desc, poligonVertexes = null;
            
            if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
            {
                for(i = 0; i < thresholdsJson.thresholdArray.length; i++)
                {
                    poligonVertexes = [];
                    
                    for(j = 0; j < series.firstAxis.labels.length; j++)
                    {
                        poligonVertexes[j] = parseInt(thresholdsJson.thresholdArray[i][series.firstAxis.labels[j]]);
                    }
                    
                    chartRef.addSeries({
                        name: 'Threshold - ' + thresholdsJson.thresholdArray[i].desc + ":" + thresholdsJson.thresholdArray[i][series.firstAxis.labels[j]],
                        data: poligonVertexes,
                        showInLegend: false,
                        dashStyle: 'LongDash',
                        lineWidth: styleParameters.alrThrLinesWidth,
                        color: thresholdsJson.thresholdArray[i].color,
                        states: {
                            hover: {
                                enabled: false
                            }
                        }
                    });
                }
            }
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
	}
        //Fine definizioni di funzione  
        
        //Codice core del widget
        if(url === "null")
        {
            url = null;
        }
        
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
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
            showHeader = event.showHeader;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
        });
        
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
        //addLink(widgetName, url, linkElement, divContainer, null);
        
        //Nuova versione
        if(('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "")&&('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
        }
        
        if('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
            thresholdsJson = widgetParameters;
        }
        
        if(('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null')&&('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== ''))
        {
            infoJson = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>');
        }
        
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
                    var chartSeriesObject = getChartSeriesObject(series);
                    var xAxisCategories = getXAxisCategories(series, widgetHeight);

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

                    chart = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                        chart: {
                            type: 'line',
                            polar: true,
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
                        //Vertici del poligono
                        xAxis: {
                            categories: xAxisCategories,
                            tickmarkPlacement: 'on',
                            lineWidth: 0,
                            gridLineColor: styleParameters.gridLinesColor,
                            gridLineWidth: styleParameters.gridLinesWidth,
                            title: {//Non mostriamolo, è brutto a vedersi
                                align: 'high',
                                offset: 0,
                                text: null,
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
                               useHTML: true,
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
                            gridLineInterpolation: 'polygon',
                            lineWidth: 0,
                            gridZIndex: 0,
                            gridLineColor: styleParameters.gridLinesColor,
                            gridLineWidth: styleParameters.gridLinesWidth,
                            title: {
                                text: null
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
                            useHTML: true,
                            backgroundColor: {
                                linearGradient: [0, 0, 0, 60],
                                stops: [
                                    [0, '#FFFFFF'],
                                    [1, '#E0E0E0']
                                ]
                            },
                            headerFormat: null,
                            pointFormatter: function()
                            {
                                var thresholdObject, desc, min, max, color, fieldName, index, message = null;
                                var rangeOnThisField = false;

                                if((this.series.name.indexOf("Threshold") >= 0)&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                                {
                                    //Tooltip di una soglia
                                    var i1 = this.series.name.indexOf("-") + 2;
                                    var i2 = this.series.name.indexOf(":");

                                    desc = this.series.name.substring(i1, i2);

                                    return '<b>Threshold</b><br/>' + 
                                           '<span style="color:' + this.color + '">\u25CF</span> Description: <b>' + desc  + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> Upper bound: <b>' + this.y  + '</b><br/>';
                                }
                                else
                                {
                                    //Tooltip di una serie di dati
                                    if(this.category.indexOf('thrLegend') > 0)
                                    {
                                        fieldName = this.category.substring(this.category.indexOf('<span class="inline">'));
                                        fieldName = fieldName.replace('<span class="inline">', '');
                                        fieldName = fieldName.replace('</span>', ''); 
                                        fieldName = fieldName.replace('<b class="caret">', '');
                                        fieldName = fieldName.replace('</b></a>', '');
                                        fieldName = fieldName.replace('<ul class="dropdown-menu thrLegend">', '');//Lascialo così
                                        fieldName = fieldName.replace('<ul class="dropdown-menu">', '');
                                        fieldName = fieldName.replace('</ul></div>', '');
                                    }
                                    else
                                    {
                                        if(this.category.indexOf('<span>') > 0)
                                        {
                                            fieldName = this.category.substring(this.category.indexOf('<span>'));
                                            fieldName = fieldName.replace("<span>", "");
                                            fieldName = fieldName.replace("</span>", "");
                                        }
                                        else
                                        {
                                            fieldName = this.category;
                                        }
                                    }

                                    if((thresholdsJson !== null)&&(thresholdsJson !== undefined)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                                    {
                                        if(thresholdsJson.thresholdArray.length > 0)
                                        {
                                            var elementUpperBounds = [];

                                            //Reperimento degli upper bounds per questo campo
                                            for(var j = 0; j < thresholdsJson.thresholdArray.length; j++)
                                            {
                                                var newUpperBound = {
                                                    color: thresholdsJson.thresholdArray[j].color,
                                                    value: parseInt(thresholdsJson.thresholdArray[j][fieldName]),
                                                    desc: thresholdsJson.thresholdArray[j].desc
                                                };
                                                elementUpperBounds.push(newUpperBound);
                                            }

                                            //Ordinamento crescente del vettore degli upper bounds
                                            elementUpperBounds.sort(compareUpperBounds);

                                            for(var i = 0; i < elementUpperBounds.length; i++)
                                            {
                                                max = elementUpperBounds[i].value;
                                                desc = elementUpperBounds[i].desc;

                                                if(i === 0)
                                                {
                                                    if(parseFloat(this.y) < max)
                                                    {
                                                        if((desc !== null)&&(desc !== ''))
                                                        {
                                                            return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: < <b>' + max + '</b><br/>' +
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';   
                                                        }
                                                        else
                                                        {
                                                            return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +  
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: < <b>' + max + '</b><br/>';
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    min = elementUpperBounds[i - 1].value;
                                                    if((parseFloat(this.y) >= min)&&(parseFloat(this.y) < max))
                                                    {
                                                        if((desc !== null)&&(desc !== ''))
                                                        {
                                                            return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';   
                                                        }
                                                        else
                                                        {
                                                            return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +  
                                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                                        }
                                                    }
                                                    else if((i === (elementUpperBounds.length - 1))&&(parseFloat(this.y) >= max))
                                                    {
                                                        return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +  
                                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value higher than the greatest upper bound<br/>';
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            //Non sono stati definiti range
                                            return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                                   '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' + 
                                                   '<span style="color:' + this.color + '">\u25CF</span> No thresholds defined<br/>';
                                        }


                                    }
                                    else
                                    {
                                        //Non sono stati definiti range
                                        return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' + 
                                               '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' + 
                                               '<span style="color:' + this.color + '">\u25CF</span> No thresholds defined<br/>';
                                    }
                                }
                            }
                        },
                        plotOptions: {
                            line: {
                                lineWidth: styleParameters.linesWidth,
                                events: {
                                    legendItemClick: function(){ 
                                        return false;
                                    } 
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
                            backgroundColor: 'transparent',
                            shadow: false,
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
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 