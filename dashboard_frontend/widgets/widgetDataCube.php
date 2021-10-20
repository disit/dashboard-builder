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
<style type="text/css">

.hide {
    max-height: 0 !important;
}

.dropdown{
    border: 0.1em solid black;
    width: 10em;
    margin-bottom: 1em;
}

.dropdown .title{
    margin: .7em;   
    width: 100%;
}

.dropdown .title .fa-angle-right{
    float: right;
    margin-right: 2em;
    transition: transform .3s;
}

.dropdown .menu{
    transition: max-height .5s ease-out;
    max-height: 20em;
    overflow: hidden;
    background: rgba(255,255,255);
}

.dropdown .menu .option{
    margin: .3em .3em .3em .3em;
    margin-top: 0.3em;
    background: rgba(255,255,255);
}

.dropdown .menu .option:hover{
    background: rgba(0,0,0,0.2);
}

.pointerCursor:hover{
    cursor: pointer;
}

.rotate-90{
    transform: rotate(90deg);
}
</style>
<!--
<script type="text/javascript">
    delete Highcharts;
</script>

<script src="../js/highcharts-9/highcharts.js"></script>
<script src="../js/highcharts-9/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/streamgraph.js"></script>
<script src ="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>  -->
<!--- <script src ="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>  
    <script src ="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>--->
<script src ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>  
<script type='text/javascript'>


var <?= $_REQUEST['name_w'] ?>_hyperCube = [];
var virtualCut = [];
var streamCut = [];
var <?= $_REQUEST['name_w'] ?>_model = '3d';
var <?= $_REQUEST['name_w'] ?>_select = null;
var <?= $_REQUEST['name_w'] ?>_units = [];
var <?= $_REQUEST['name_w'] ?>_loaded = false;

    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetCurvedLineSeries.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>  
        var dateChoice = null;
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
        var seriesDataArray = [];
        var metricData, metricType, series, styleParameters, timeRange, gridLineColor, chartAxesColor, chartType, index, highchartsChartType, chartSeriesObject, legendWidth, xAxisCategories, rowParameters, aggregationGetData, getDataFinishCount, xAxisType,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, fontSize, fontColor, chartColor, dataLabelsFontSize, chartLabelsFontSize, dataLabelsFontColor, chartLabelsFontColor, appId, flowId, nrMetricType,
            widgetHeight, lineWidth, xAxisTitle, smField, metricName, widgetTitle, countdownRef, widgetParameters, thresholdsJson, infoJson, xAxisFormat, yAxisType, infoJson, idMetric = null;
        var serviceUri = "";
        var editLabels = "";
        var valueUnit = null;
        var utcOption = false;
        var rowParamLength = null;
        var dataOriginV = null;
        var upperTimeLimitISOTrimmed = null;
        //    this["timeNavCount_"+widgetName] = 0;
        var timeNavCount = 0;
        var fromGisExternalContentRangePrevious = null;
        var fromGisExternalContentServiceUriPrevious = null;
        var fromGisExternalContentFieldPrevious = null;
        var dataFut = null;
        var upLimit, upperTime = null;
        var now = new Date();
        var nowUTC = now.toUTCString();
        var isoDate = new Date(nowUTC).toISOString();
        
        var pattern = /Percentuale\//;
        console.log("Entrato in widgetDataCube --> " + widgetName); 
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

        $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker({
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            }
        })

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

        function truncateStackedSerie(serie, timeRange) {

            var truncatedSerie = [];
            var truncatedMillis = null;

          /*  switch(timeRange) {
                case "Annuale":

                    for (let n = 0; n < serie.length; n++) {
                        truncatedMillis = moment(serie[n][0]).hours(0).minutes(0).seconds(0).milliseconds(0).valueOf();
                        truncatedSerie[n] = [truncatedMillis, serie[n][1]];
                    }

                    break;

                case "lines":
                    break;

                default:
                    break;
            }*/

            for (let n = 0; n < serie.length; n++) {
             //   truncatedMillis = moment(serie[n][0]).hours(0).minutes(0).seconds(0).milliseconds(0).valueOf();
                truncatedMillis = moment(serie[n][0]).milliseconds(0).valueOf();
                truncatedSerie[n] = [truncatedMillis, serie[n][1]];
            }

            return truncatedSerie;

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

            if (infoJson != "fromTracker" || fromGisExternalContent === true) {
                var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv').css("width", "3.5%");
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_countdownContainerDiv').css("width", "3%");
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("color", widgetHeaderFontColor);
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                }

            }

	    }

        function drawDiagram(timeDomain, xAxisFormat, yAxisFormat)
        {
            if(timeDomain)
            {
                if (xAxisFormat == null) {
                    xAxisType = 'datetime';
                } else if (xAxisFormat == "timestamp") {
                    xAxisType = 'datetime';
                } else if (xAxisFormat == "Numeric") {
                    xAxisType = 'numeric';
                }
                xAxisCategories = null;
            }
            else
            {
                xAxisType = null;
            }

            if (yAxisFormat == null) {
                yAxisType = "linear";
            } else if (yAxisFormat == "logarithmic") {
                yAxisType = "logarithmic";
            } else {
                yAxisType = "linear";
            }

            let yAxisText = null;
            if (styleParameters.yAxisLabel != null) {
                yAxisText = styleParameters.yAxisLabel;
            } else {
                if (chartSeriesObject.valueUnit != null) {
                    yAxisText = chartSeriesObject.valueUnit;
                }

                if (yAxisType == "logarithmic") {
                    yAxisText = yAxisText + " (logarithmic)";
                }
            }

            const timezone = new Date().getTimezoneOffset()

            Highcharts.setOptions({
                global: {
                    timezoneOffset: timezone
                }
            });

            if (chartSeriesObject.length!=0) {
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
            //    if (chartSeriesObject[0].data.length > 0) {
                if (<?= $_REQUEST['name_w'] ?>_model=='cut'){
                
                
                Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                    chart: {
                                marginTop: 50,
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
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
                                //    color: chartLabelsFontColor,
                                    color: styleParameters.rowsLabelsFontColor,
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
                                },
                                formatter: function() {
                                    return Highcharts.dateFormat('%e %b %Y %H:%M:%S', this.value);
                                }
                            }
                        },
                    yAxis: {
                        min: 0,
                        title: {
                                //text: null
                                text: <?= $_REQUEST['name_w'] ?>_select,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
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

                            var chartItemIdx = chartSeriesObject.findIndex(el => el.name === this.series.name);
                            var dateLine = null;
                            if (styleParameters.xAxisFormat == "numeric" && rowParameters[chartItemIdx].metricHighLevelType == "Dynamic") {
                                dateLine = "";
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton").hide();
                            } else {
                                dateLine = '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>';
                            }

                            if (rangeOnThisField) {
                                if ((desc !== null) && (desc !== '')) {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                        dateLine +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';
                                } else {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                        dateLine +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                }
                            } else {
                              //  return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                              //      dateLine +
                              //      '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                    dateLine;
                            }
                        }
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
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
                    series: virtualCut
                });
}
if (<?= $_REQUEST['name_w'] ?>_model=='stream'){
    var colors = Highcharts.getOptions().colors;
Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {

    chart: {
        type: 'streamgraph',
        marginBottom: 100,
        marginTop: 50,
        zoomType: 'x'
    },

    // Make sure connected countries have similar colors
    colors: [
        colors[0],
        colors[1],
        colors[2],
        colors[3],
        colors[4],
        // East Germany, West Germany and Germany
        Highcharts.color(colors[5]).brighten(0.2).get(),
        Highcharts.color(colors[5]).brighten(0.1).get(),

        colors[5],
        colors[6],
        colors[7],
        colors[8],
        colors[9],
        colors[0],
        colors[1],
        colors[3],
        // Soviet Union, Russia
        Highcharts.color(colors[2]).brighten(-0.1).get(),
        Highcharts.color(colors[2]).brighten(-0.2).get(),
        Highcharts.color(colors[2]).brighten(-0.3).get()
    ],

    title: {
        floating: true,
        align: 'left',
        text: ''
    },
    subtitle: {
        floating: true,
        align: 'left',
        y: 30,
        text: ''
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

                                var chartItemIdx = chartSeriesObject.findIndex(el => el.name === this.series.name);
                                var dateLine = null;
                                if (styleParameters.xAxisFormat == "numeric" && rowParameters[chartItemIdx].metricHighLevelType == "Dynamic") {
                                    dateLine = "";
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton").hide();
                                } else {
                                    dateLine = '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>';
                                }

                                if (rangeOnThisField) {
                                    if ((desc !== null) && (desc !== '')) {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            dateLine +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';
                                    } else {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            dateLine +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                    }
                                } else {
                                  //  return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                  //      dateLine +
                                  //      '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                        dateLine;
                                }
                            }
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
                                //    color: chartLabelsFontColor,
                                    color: styleParameters.rowsLabelsFontColor,
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
        visible: false,
        startOnTick: false,
        endOnTick: false,
        title: {
                                //text: null
                                text: <?= $_REQUEST['name_w'] ?>_select,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
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
    

    legend: {
        enabled: true
    },

    plotOptions: {
        series: {
            label: {
                minFontSize: 5,
                maxFontSize: 15,
                style: {
                    color: 'rgba(255,255,255,0.75)'
                }
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

    // Data parsed with olympic-medals.node.js
    series: streamCut,

    exporting: {
        sourceWidth: 800,
        sourceHeight: 600
    }

});

}
if (<?= $_REQUEST['name_w'] ?>_model=='3d'){
    var graphDepth = 100*(chartSeriesObject.length+1);
                Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
    chart: {
                marginTop: 50,
        type: 'area',
        options3d: {
            enabled: true,
            alpha: 15,
            beta: 30,
            depth: graphDepth
        }
    },
    title: {
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
                                //    color: chartLabelsFontColor,
                                    color: styleParameters.rowsLabelsFontColor,
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
                        min: 0,
                        title: {
                                //text: null
                                text: <?= $_REQUEST['name_w'] ?>_select,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
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

                                var chartItemIdx = chartSeriesObject.findIndex(el => el.name === this.series.name);
                                var dateLine = null;
                                if (styleParameters.xAxisFormat == "numeric" && rowParameters[chartItemIdx].metricHighLevelType == "Dynamic") {
                                    dateLine = "";
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton").hide();
                                } else {
                                    dateLine = '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>';
                                }

                                if (rangeOnThisField) {
                                    if ((desc !== null) && (desc !== '')) {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            dateLine +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>';
                                    } else {
                                        return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            dateLine +
                                            '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>';
                                    }
                                } else {
                                  //  return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                  //      dateLine +
                                  //      '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>';
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.series.name + '</b>: <b>' + this.y + '</b><br/>' +
                                        dateLine;
                                }
                            }
                        },
    plotOptions: {
        area: {
            depth: 100,
            marker: {
                enabled: false
            },
            states: {
                inactive: {
                    enabled: false
                }
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
    series: chartSeriesObject
});

}

             /*   } else {

                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                    //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();

                }*/
            } else {

                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();

            }

        }

        function getUpperTimeLimit(timeRange, timeCount, timestamp=null) {
            let hours = 0;
            switch(timeRange) {
                case "Annuale":
                    hours = 365*24*timeCount;
                    break;

                case "Semestrale":
                    hours = 180*24*timeCount;
                    break;

                case "Mensile":
                    hours = 30*24*timeCount;
                    break;

                case "Settimanale":
                    hours = 7*24*timeCount;
                    break;

                case "Giornaliera":
                    hours = 24*timeCount;
                    break;

                case "12 Ore":
                    hours = 12*timeCount;
                    break;

                case "4 Ore":
                    hours = 4*timeCount;
                    break;
            }
            var now = new Date();
            if (timestamp!=null)
                now = new Date(timestamp);
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimit = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            let upperTimeLimitUTC = new Date(upperTimeLimit).toUTCString();
            let upperTimeLimitISO = new Date(upperTimeLimitUTC).toISOString();
            let upperTimeLimitISOTrim = upperTimeLimitISO.substring(0, isoDate.length - 5);
            return upperTimeLimitISOTrim;
            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
        }

        function convertFromMomentToTime(momentDate) {
            var momentDateTime = momentDate.format();
            //  momentDateTime = momentDateTime.replace("T", " ");
            var plusIndexLocal = momentDateTime.indexOf("+");
            momentDateTime = momentDateTime.substr(0, plusIndexLocal);
            var convertedDateTime = momentDateTime;
            return convertedDateTime;
        }

        function convertDataFromTimeNavToDm(originalData, field)
        {
            var singleOriginalData, singleData, convertedDate, futureDate = null;
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
                                            IdMetric_data: null, //Si puÃ² lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            futureDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si puÃ² lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si puÃ² lasciare null, non viene usato dal widget
                                        },
                                        range_dates: 0//Si puÃ² lasciare null, non viene usato dal widget
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

                                // TIME-ZONE CONVERSION
                                var localTimeZone = moment.tz.guess();
                                var momentDateTime = moment(convertedDate);
                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                localDateTime = localDateTime.replace("T", " ");
                                var plusIndexLocal = localDateTime.indexOf("+");
                                localDateTime = localDateTime.substr(0, plusIndexLocal);

                                convertedDate = convertedDate.replace("T", " ");
                                var plusIndex = convertedDate.indexOf("+");
                                convertedDate = convertedDate.substr(0, plusIndex);
                                if (singleOriginalData[field] && singleOriginalData[field].hasOwnProperty("valueDate")) {
                                    futureDate = singleOriginalData[field].valueDate.replace("T", " ");
                                } else if (singleOriginalData.hasOwnProperty("dateObserved")) {
                                    futureDate = singleOriginalData["dateObserved"].value.replace("T", " ");
                                } else if (singleOriginalData.hasOwnProperty("measuredTime")) {
                                    futureDate = singleOriginalData["measuredTime"].value.replace("T", " ");
                                }
                                var plusIndexFuture = futureDate.indexOf("+");
                                futureDate = futureDate.substr(0, plusIndexFuture);
                                var momentDateTimeFuture = moment(futureDate);
                                var localDateTimeFuture = momentDateTimeFuture.tz(localTimeZone).format();
                                localDateTimeFuture = localDateTimeFuture.replace("T", " ");
                                var plusIndexLocalFuture = localDateTimeFuture.indexOf("+");
                                localDateTimeFuture = localDateTimeFuture.substr(0, plusIndexLocalFuture);
                                if (localDateTime == "") {
                                    singleData.commit.author.computationDate = convertedDate;
                                    singleData.commit.author.futureDate = futureDate;
                                } else {
                                    singleData.commit.author.computationDate = localDateTime;
                                    singleData.commit.author.futureDate = localDateTimeFuture;

                                }

                                if(singleOriginalData[field] !== undefined) {
                                    if (!isNaN(parseFloat(singleOriginalData[field].value))) {
                                        singleData.commit.author.value = parseFloat(singleOriginalData[field].value);
                                    } else {
                                        originalDataNotNumeric++;
                                        continue;
                                    }
                                } else {
                                    originalDataNotNumeric++;
                                    continue;
                                }

                                convertedData.data.push(singleData);
                            }

                            if (convertedData.data.length > 0) {
                                return convertedData;
                            } else {
                                convertedData.data.push(singleData)
                                return convertedData;
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
            else
            {
                return false;
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
        
        function parseKPIData(aggregationSeries)
        {
            var roundedVal, singleSample, sampleTime, seriesSingleObj = null;
            singleSeriesData=[];
            utcOption = false;
                        if((aggregationSeries.metricType === "Percentuale")||(pattern.test(aggregationSeries.metricType)))
                        {
                            for(var j = 0; j < aggregationSeries.data.length; j++)
                            {
                                roundedVal = parseFloat(aggregationSeries.data[j].value_perc1);
                                roundedVal = Number(roundedVal.toFixed(2));
                                sampleTime = parseInt(new Date(aggregationSeries.data[j].computationDate).getTime() + 7200000);
                              //  sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                singleSample = [sampleTime, roundedVal];
                                singleSeriesData.push(singleSample);
                            }
                        }
                        else
                        {
                            switch(aggregationSeries.metricType)
                            {
                                case "Intero":
                                    for(var j = 0; j < aggregationSeries.data.length; j++)
                                    {
                                        roundedVal = parseInt(aggregationSeries.data[j].value_num);
                                        sampleTime = parseInt(new Date(aggregationSeries.data[j].computationDate).getTime() + 7200000);
                                     //   sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                        singleSample = [sampleTime, roundedVal];
                                        singleSeriesData.push(singleSample);
                                    }
                                    break;

                                case "Float":
                                    for(var j = 0; j < aggregationSeries.data.length; j++)
                                    {
                                        roundedVal = parseFloat(aggregationSeries.data[j].value_num);
                                        roundedVal = Number(roundedVal.toFixed(2));
                                        sampleTime = parseInt(new Date(aggregationSeries.data[j].computationDate).getTime() + 7200000);
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
                            name: aggregationSeries.metricShortDesc,
                            data: singleSeriesData,
                            //color: styleParameters.barsColors[i],
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
                                    //fontSize: styleParameters.dataLabelsFontSize + "px",
                                    //color: styleParameters.dataLabelsFontColor,
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                }
                            }
                        };

                        return seriesSingleObj;
        }

        function parseDynamicData(aggregationSeries){
            var roundedVal, singleSample, sampleTime, seriesSingleObj = null;
            //   utcOption = false;
                         let extractedData = {};
                         if (timeNavCount != 0) {
                             extractedData.values = aggregationSeries.data;
                         } else {
                             extractedData.values = rowParameters[i].values;
                         }
                         extractedData.metricType = rowParameters[i].metricType;
                         extractedData.metricId = rowParameters[i].metricId;
                         extractedData.metricName = rowParameters[i].metricName;
                    //     extractedData.measuredTime = rowParameters[i].measuredTime;
                         extractedData.metricValueUnit = rowParameters[i].metricValueUnit;

                         seriesDataArray.push(extractedData);

                        var objName = null;
                     /*   if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationSeries.metricName;
                            }
                        } else {
                            objName = aggregationSeries.metricName;
                        }*/

                        if (aggregationSeries.label) {
                            objName = aggregationSeries.label;
                        } else {
                            objName = aggregationSeries.metricName;
                        }

                    /*     if (rowParameters.length === seriesDataArray.length) {
                             // DO FINAL SERIALIZATION
                             serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr)
                         }  */

                         let timeSlicedData = [];
                         let millisToSubtract = null;
                         switch (timeRange) {
                             case "4 Ore":
                                 millisToSubtract = 4 * 60 * 60 * 1000;
                                 break;
                             case "12 Ore":
                                 millisToSubtract = 12 * 60 * 60 * 1000;
                                 break;
                             case "Giornaliera":
                                 millisToSubtract = 24 * 60 * 60 * 1000;
                                 break;
                             case "Settimanale":
                                 millisToSubtract = 7 * 24 * 60 * 60 * 1000;
                                 break;
                             case "Mensile":
                                 millisToSubtract = 30 * 24 * 60 * 60 * 1000;
                                 break;
                             case "Semestrale":
                                 millisToSubtract = 180 * 24 * 60 * 60 * 1000;
                                 break;
                             case "Annuale":
                                 millisToSubtract = 365 * 24 * 60 * 60 * 1000;
                                 break;
                         }
                         let currDate = new Date();
                         let currMillis = currDate.getTime();
                         if (timeNavCount != 0) {
                             if (upperTime != null) {
                                 currMillis = new Date(upperTime).getTime();
                             }
                         }

                         if (extractedData.values) {
                             if (xAxisFormat != "numeric") {
                                 for (let n = 0; n < extractedData.values.length; n++) {
                                     let timestamp = extractedData.values[n][0];
                                     if (timestamp >= currMillis - millisToSubtract) {
                                         timeSlicedData.push(extractedData.values[n]);
                                     }
                                 }
                             } else {
                                 timeSlicedData = extractedData.values;
                             }
                         } else {
                             timeSlicedData = [];
                         }

                         if (timeSlicedData.length != 0) {
                             seriesSingleObj = {
                                 showInLegend: true,
                                 name: objName,
                                 //    data: extractedData.values,
                                 data: timeSlicedData,
                                 //color: styleParameters.barsColors[i],
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
                         }

                         if (extractedData.metricValueUnit != null) {
                             chartSeriesObject.valueUnit = extractedData.metricValueUnit;
                         }

                         return seriesSingleObj;

                     //    }
        }

        function parseMyKPIData(aggregationSeries){
            var roundedVal, singleSample, sampleTime, seriesSingleObj = null;
            singleSeriesData=[];
            utcOption = false;
                        var smPayload = aggregationSeries.data;
                        var smField = aggregationSeries.smField;
                        smPayload = JSON.parse(smPayload);

                        var resultsArray = smPayload;

                        var objName = null;
                    /*    if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationSeries.metricName;
                            }
                        } else {
                            objName = aggregationSeries.metricName;
                        }*/

                        if (aggregationSeries.label!=aggregationSeries.metricName+' - '+aggregationSeries.valueType) {
                            objName = aggregationSeries.label;
                        } else {
                            objName = aggregationSeries.metricName;
                        }

                        for(var j = 0; j < resultsArray.length; j++)
                        {
                            newVal = resultsArray[j].value;
                            addSampleToTrend = true;
                        //    newTime = resultsArray[j].insertTime;
                            newTime = resultsArray[j].dataTime;
                            //chartSeriesObject.valueUnit = "";

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

                        if (stackingOption == "normal") {
                            singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                        }

                        seriesSingleObj = {
                            showInLegend: true,
                            name: objName,
                            data: singleSeriesData,
                            //color: styleParameters.barsColors[i],
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

                        /*if (aggregationSeries.metricValueUnit != null) {
                            chartSeriesObject.valueUnit = aggregationSeries.metricValueUnit;
                        }*/
            //return seriesSingleObj;
            return {device: aggregationSeries.metricName, valueType: smField, valueUnit: aggregationSeries.metricValueUnit, timeSeries: singleSeriesData};

        }

        function parseSensorData(aggregationSeries,smPayload,smField,properties){
            var roundedVal, singleSample, sampleTime, seriesSingleObj = null;
            singleSeriesData=[];
            utcOption = false;
                        //var smPayload = aggregationSeries.data;
                        //var smField = aggregationSeries.smField;
                        //smPayload = JSON.parse(smPayload);
                        //chartSeriesObject.valueUnit = "";

                    /*    var objName = null;
                        if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationSeries.metricName;
                            }
                        } else {
                            objName = aggregationSeries.metricName;
                        }*/

                        /*if (aggregationSeries.label) {
                            objName = aggregationSeries.label;
                        } else {
                            objName = aggregationSeries.metricName + " - " + smField;
                        }*/

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

                            if (stackingOption == "normal") {
                                singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                            }

                            
                        }
                        else
                        {
                            if(smPayload.hasOwnProperty('realtime') && smPayload.realtime.results!=null)
                            {
                                if (typeof smPayload.realtime.results.bindings[0][smField] !== 'undefined'){
                                    if(smPayload.realtime.hasOwnProperty('results'))
                                    {
                                        var resultsArray = smPayload.realtime.results.bindings;
                                        var newVal, newTime = null;
                                        for(var j = 0; j < resultsArray.length; j++)
                                        {
                                            if(resultsArray[j].hasOwnProperty(smField)) {
                                                newVal = resultsArray[j][smField].value;
                                                addSampleToTrend = true;
                                            }

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

                                        if (stackingOption == "normal") {
                                            singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                                        }

                                    }

                                }
                                else{
                                    singleSeriesData = null;
                                }
                            }
                        }

                        //single object goes here


                        /*if (smPayload.Service != null) {
                            if (smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                chartSeriesObject.valueUnit = smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit;
                            }
                        } else if (smPayload.Sensor != null) {
                            if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                chartSeriesObject.valueUnit = smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit;
                            }
                        }*/

                        var device = aggregationSeries.metricName;

                        if (properties.realtimeAttributes[smField] != null) {
                            var valueType = properties.realtimeAttributes[smField].value_type;
                            var valueUnit = properties.realtimeAttributes[smField].value_unit;
                        }

                        return {device: device, valueType: valueType, valueUnit: valueUnit, timeSeries: singleSeriesData};
        }

        function searchDeviceInRowParameters(device){
        //    console.log(rowParameters);
            for (var metricParam of rowParameters){
                if (metricParam.metricName==device){
                    if (metricParam.label!=undefined)
                        return metricParam.label;
                }
            }
            return device;
        }
        function searchColorDeviceInStyleParameters(device){
         //   console.log(styleParameters);
            for (var i in rowParameters){
                var metricParam = rowParameters[i];
                if (metricParam.metricName==device){
                    return styleParameters['barsColors'][i];
                }
            }
            return device;
        }

        function loadHyperCube(){

            if (<?= $_REQUEST['name_w'] ?>_select==""||<?= $_REQUEST['name_w'] ?>_select==null){
                <?= $_REQUEST['name_w'] ?>_select = <?= $_REQUEST['name_w'] ?>_units[0];
                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').innerHTML=<?= $_REQUEST['name_w'] ?>_select+'<i class="fa fa-angle-right"></i>';
            }

            chartSeriesObject = [];
            var colored = [];
        //    console.log(styleParameters);
            for (var point of <?= $_REQUEST['name_w'] ?>_hyperCube){

                objName = searchDeviceInRowParameters(point.device) + " - " + point.valueType + ' - ' + point.valueUnit;
                seriesSingleObj = {
                    showInLegend: true,
                //    name: aggregationSeries.metricName,
                    name: objName,
                    data: point.timeSeries,
                    //color: styleParameters.barsColors[i],
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

                if (point.valueUnit==<?= $_REQUEST['name_w'] ?>_select && point.timeSeries.length!=0){ // qui avviene il taglio
                    if (!colored.includes(point.device)){
                        seriesSingleObj.color = searchColorDeviceInStyleParameters(point.device);
                        colored.push(point.device);
                    }
                    chartSeriesObject.push(seriesSingleObj);
                    virtualCut = [];
                    var t0 = 0; // a caso
                    for (var point of <?= $_REQUEST['name_w'] ?>_hyperCube){
                        if (point.valueUnit==<?= $_REQUEST['name_w'] ?>_select){
                            if (point.timeSeries[t0]) {
                                if (!isNaN(point.timeSeries[t0][1]))
                                    virtualCut.push({
                                        name: point.device + " - " + point.valueType + ' - ' + point.valueUnit,
                                        data: [point.timeSeries[t0]]
                                    });
                                else
                                    chartSeriesObject = []; // There are NaN values
                            }
                        }
                    }

                    streamCut = [];
                    for (var point of <?= $_REQUEST['name_w'] ?>_hyperCube){
                        if (point.valueUnit==<?= $_REQUEST['name_w'] ?>_select){
                            streamCut.push({name:point.device + " - " + point.valueType + ' - ' + point.valueUnit, data: point.timeSeries});
                        }
                    }
                }

            }

        }

        function buildSeriesFromAggregationData(timeRange)
        {
            <?= $_REQUEST['name_w'] ?>_hyperCube = [];
            
             for(var i = 0; i < aggregationGetData.length; i++)
             {
                
                 switch(aggregationGetData[i].metricHighLevelType)
                 {
                    case "KPI":
                        
                        chartSeriesObject.push(parseKPIData(aggregationGetData[i]));

                        break;

                    case "Dynamic":
                      
                        chartSeriesObject.push(parseDynamicData(aggregationGetData[i]));

                        break;

                    case "MyKPI":
                        
                        var point = parseMyKPIData(aggregationGetData[i]);
                        var u = point.valueUnit;
                                var existing = document.getElementById(u+'_<?= $_REQUEST['name_w'] ?>');
                                if (existing==null){
                                    var optionDiv = document.createElement('div'); 
                                    optionDiv.classList.add('option');
                                    optionDiv.innerHTML = u;
                                    optionDiv.id = u+'_<?= $_REQUEST['name_w'] ?>';
                                    var parent  = document.getElementById('<?= $_REQUEST['name_w'] ?>_options');
                                    parent.appendChild(optionDiv);
                                    optionDiv.addEventListener('click',handleOptionSelected);
                                    <?= $_REQUEST['name_w'] ?>_units.push(u);
                                }
                        if (point.timeSeries != null) <?= $_REQUEST['name_w'] ?>_hyperCube.push(point);

                        break;

                    case "IoT Device Variable":
                    case "IoT Device":
                    case "Data Table Variable":
                    case "Data Table Device":
                    case "Mobile Device Variable":
                    case "Mobile Device":
                    case "Sensor Device":
                    case "Sensor":

                        var smPayload = JSON.parse(aggregationGetData[i].data);

                        var properties;

                        if (smPayload.hasOwnProperty('Service'))
                            properties = smPayload.Service.features[0].properties;
                        if (smPayload.hasOwnProperty('Sensor'))
                            properties = smPayload.Sensor.features[0].properties;

                        var attributes;

                        if (aggregationGetData[i].smField == "")
                        {
                            attributes = properties.realtimeAttributes;
                            for (var smField in attributes){

                                var point = parseSensorData(aggregationGetData[i],smPayload,smField, properties);

                                var u = point.valueUnit;
                                var existing = document.getElementById(u+'_<?= $_REQUEST['name_w'] ?>');
                                if (existing==null){
                                    var optionDiv = document.createElement('div'); 
                                    optionDiv.classList.add('option');
                                    optionDiv.innerHTML = u;
                                    optionDiv.id = u+'_<?= $_REQUEST['name_w'] ?>';
                                    var parent  = document.getElementById('<?= $_REQUEST['name_w'] ?>_options');
                                    parent.appendChild(optionDiv);
                                    optionDiv.addEventListener('click',handleOptionSelected);
                                    <?= $_REQUEST['name_w'] ?>_units.push(u);
                                }

                                if (point.timeSeries != null) <?= $_REQUEST['name_w'] ?>_hyperCube.push(point);

                            }
                        }
                        else
                        {
                            smField = aggregationGetData[i].smField;
                            var point = parseSensorData(aggregationGetData[i],smPayload,smField, properties);

                            var u = point.valueUnit;
                            var existing = document.getElementById(u+'_<?= $_REQUEST['name_w'] ?>');
                                if (existing==null){
                                    var optionDiv = document.createElement('div'); 
                                    optionDiv.classList.add('option');
                                    optionDiv.innerHTML = u;
                                    optionDiv.id = u+'_<?= $_REQUEST['name_w'] ?>';
                                    var parent  = document.getElementById('<?= $_REQUEST['name_w'] ?>_options');
                                    parent.appendChild(optionDiv);
                                    optionDiv.addEventListener('click',handleOptionSelected);
                                    <?= $_REQUEST['name_w'] ?>_units.push(u);
                                }

                            if (point.timeSeries != null) <?= $_REQUEST['name_w'] ?>_hyperCube.push(point);

                        }

                        

                        break;
                        
                    
                        default:
                            console.log("Default");
                            break;
                 }
            }


            loadHyperCube();
            return null; 
        }
        
        function convertTime(timestamp){
            var tzoffset = (new Date()).getTimezoneOffset() * 60000;
            var date = new Date(timestamp-tzoffset);

            date = date.toISOString().substr(0,19);

            return date;
        }

        function populateWidget(fromAggregate, localTimeRange, timeNavDirection, timeCount)
        {

            // Reset Time Navigation
        /*    if (fromGisExternalContentRangePrevious !== fromGisExternalContentRange || fromGisExternalContentFieldPrevious != fromGisExternalContentField || fromGisExternalContentServiceUriPrevious != fromGisExternalContentServiceUri) {
                timeNavCount = 0;
                timeCount = 0;
                fromGisExternalContentRangePrevious = fromGisExternalContentRange;
                fromGisExternalContentFieldPrevious = fromGisExternalContentField;
                fromGisExternalContentServiceUriPrevious = fromGisExternalContentServiceUri;
                dataFut = null;
                upLimit = null;
            }*/

            if(fromAggregate)
            {
                setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                
                aggregationGetData = [];
                getDataFinishCount = 0;

                if (rowParameters.length == null) {
                    rowParamLength = 0;
                } else {
                    rowParamLength = rowParameters.length;
                }

                for(var i = 0; i < rowParamLength; i++)
                {
                    aggregationGetData[i] = false;
                }

                for(var i = 0; i < rowParamLength; i++)
                {
                    upperTime = getUpperTimeLimit(localTimeRange, timeCount, dateChoice);
                    /*if (dateChoice){
                        upperTime = convertTime(dateChoice);
                    }*/
                    if (rowParamLength >= 1) {
                        dataOriginV = JSON.stringify(rowParameters[i]);
                    } else {
                        dataOriginV = JSON.stringify(rowParameters);
                    }
                    index = i;
                    $.ajax({
                        url: "../controllers/aggregationSeriesProxy.php",
                        type: "POST",
                        data: 
                        {
                            dataOrigin: dataOriginV,
                            index: i,
                            timeRange: localTimeRange,
                            field: rowParameters[i].smField,
                            upperTime: upperTime
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
                            if(getDataFinishCount === rowParamLength)
                            {
                                widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);
                                legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                editLabels = styleParameters.editDeviceLabels;
                                buildSeriesFromAggregationData(localTimeRange);

                                metricLabels = getMetricLabelsForBarSeries(rowParameters);
                            //    deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
                                for (let n = 0; n < chartSeriesObject.length; n++) {
                                    if (chartSeriesObject[n] != null) {
                                        deviceLabels[n] = chartSeriesObject[n].name;
                                    }
                                }
                            //    let mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
                            /*    if (editLabels) {
                                    series = serializeDataForSeries(metricLabels, deviceLabels, editLabels);
                                } else {*/
                                    series = serializeDataForSeries(metricLabels, deviceLabels);
                             //   }

                                if (styleParameters.xAxisLabel != null) {
                                    xAxisTitle = styleParameters.xAxisLabel;
                                }
                             /*   if (xAxisFormat) {
                                    if (xAxisFormat == "timestamp") {
                                        xAxisTitle = "DateTime";
                                    } else if (xAxisFormat == "numeric") {
                                        xAxisTitle = "Numeric Values";
                                    }
                                } else {
                                    xAxisTitle = "DateTime";
                                }*/

                              //  if(firstLoad !== false || timeNavCount != 0)
                              //  {
                                    showWidgetContent(widgetName);
                                 //   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                              //  }
                              /*  else
                                {
                                    elToEmpty.empty();
                                //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                }*/

                            //    if (!serviceUri) {
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
                            //    }

                                let drawFlag = false;
                                for (let n = 0; n< chartSeriesObject.length; n++) {
                                    if (chartSeriesObject[n] != null) {
                                        if (chartSeriesObject[n].data.length > 0) {
                                            drawFlag = true;
                                        }
                                    }
                                }
                                if (drawFlag === true) {
                                    drawDiagram(true, xAxisFormat, yAxisType);
                                } else {
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                    //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                                if (timeNavCount < 0) {
                                    if (moment(upperTime).isBefore(moment(dataFut))) {

                                    } else {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                    }
                                }
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

                            drawDiagram(false, xAxisFormat, yAxisType);
                            if (timeNavCount < 0) {
                                if (moment(upperTime).isBefore(moment(dataFut))) {

                                } else {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                }
                            }
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

        $("#" + widgetName + "_timeTrendPrevBtn").off("click").click(function () {
            //  alert("PREV Clicked!");
            timeNavCount++;
            if(timeNavCount == 0) {

                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    //    rowParameters = JSON.parse(rowParameters);
                    //    timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, "minus", timeNavCount);
                    //    populateWidget(true, timeRange);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                    //    populateWidget(false, null);
                }

                //   if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "Sensor Device" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Device" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeURI(rowParameters[k].serviceUri);
                        field = rowParameters[k].smField;
                        if (rowParameters != null) {
                        //    if (rowParameters.includes("https:")) {
                                $.ajax({
                                    url: urlKBToBeCalled,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (originalData) {
                                        var stopFlag = 1;
                                        var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                        if (convertedData) {
                                            if (convertedData.data.length > 0) {
                                                var localTimeZone = moment.tz.guess();
                                                var momentDateTime = moment();
                                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                localDateTime = localDateTime.replace("T", " ");
                                                var plusIndexLocal = localDateTime.indexOf("+");
                                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                var localTimeZoneString = "";
                                                if (localDateTime == "") {
                                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                } else {
                                                    localTimeZoneString = localTimeZone;
                                                }
                                                if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
                                                    dataFut = (convertedData.data[0].commit.author.futureDate);
                                                    if (moment(dataFut).isAfter(momentDateTime)) {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                    } else {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                    }
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                }
                                            } else {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data) {
                                        //  showWidgetContent(widgetName);
                                        //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Errore in chiamata prima API");
                                        console.log(JSON.stringify(data));
                                    }
                                });
                        /*    } else {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                            }*/
                        } else {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                        }

                    }
                }
          /*  } else if (timeNavCount < 0 && $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").is(":hidden")) {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
            } else {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();*/
            } else {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                setupLoadingPanel(widgetName, widgetContentColor, true);
                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    //    rowParameters = JSON.parse(rowParameters);
                    //    timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, "minus", timeNavCount);
                    //    populateWidget(true, timeRange);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                    //    populateWidget(false, null);
                }
            }
        //    populateWidget(timeRange, null, "minus", timeNavCount);
        });

        $("#" + widgetName + "_timeTrendNextBtn").off("click").click(function () {
            //   alert("NEXT Clicked!");
            timeNavCount--;
            if(timeNavCount == 0) {

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                if(idMetric === 'AggregationSeries' || idMetric.includes("NR_"))
                {
                //    rowParameters = JSON.parse(rowParameters);
                 //   timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, "plus", timeNavCount);
                    //    populateWidget(true, timeRange);
                }
                else
                {
                    populateWidget(false, null, "plus", timeNavCount);
                    //    populateWidget(false, null);
                }

                //    if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "Sensor Device" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Device" ||  rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeURI(rowParameters[k].serviceUri);
                        field = rowParameters[k].smField;
                        if (rowParameters != null) {
                          //  if (rowParameters.includes("https:")) {
                                $.ajax({
                                    url: urlKBToBeCalled,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (originalData) {
                                        var stopFlag = 1;
                                        var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                        if (convertedData) {
                                            if (convertedData.data.length > 0) {
                                                var localTimeZone = moment.tz.guess();
                                                var momentDateTime = moment();
                                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                localDateTime = localDateTime.replace("T", " ");
                                                var plusIndexLocal = localDateTime.indexOf("+");
                                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                var localTimeZoneString = "";
                                                if (localDateTime == "") {
                                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                } else {
                                                    localTimeZoneString = localTimeZone;
                                                }
                                                if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
                                                    dataFut = (convertedData.data[0].commit.author.futureDate);
                                                    if (moment(dataFut).isAfter(momentDateTime)) {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                    } else {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                    }
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                }
                                            } else {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data) {
                                        //  showWidgetContent(widgetName);
                                        //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Errore in chiamata prima API");
                                        console.log(JSON.stringify(data));
                                    }
                                });
                        /*    } else {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                            }   */
                        } else {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                        }
                    }
                }
            } else {

                setupLoadingPanel(widgetName, widgetContentColor, true);
                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    //    rowParameters = JSON.parse(rowParameters);
                    //   timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, "plus", timeNavCount);
                    //    populateWidget(true, timeRange);
                } else {
                    populateWidget(false, null, "plus", timeNavCount);
                    //    populateWidget(false, null);
                }
            }
        //    populateWidget(timeRange, null, "plus", timeNavCount, dataFut);

        });

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
                infoJson = widgetData.params.infoJson;
                idMetric =  widgetData.params.id_metric;

                //    if ((sm_based === "myKPI" || sm_based === "no") && fromGisExternalContent != true) {
                //    if ((sm_based === "no" || infoJson === "fromTracker") && fromGisExternalContent != true) {
                if (infoJson === "fromTracker" && fromGisExternalContent != true) {
                    $("#" + widgetName + "_timeControlsContainer").hide();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                } else {
                    $("#" + widgetName + "_timeControlsContainer").show();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                }
                
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
                    <?= $_REQUEST['name_w'] ?>_select = styleParameters['defaultUnit'];
                    document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').innerHTML=<?= $_REQUEST['name_w'] ?>_select+'<i class="fa fa-angle-right"></i>';
                    xAxisFormat = styleParameters.xAxisFormat;
                    yAxisType = styleParameters.yAxisType;
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
                    case '3d':
                        <?= $_REQUEST['name_w'] ?>_model = "3d";
                        break;

                    case 'cut':
                        <?= $_REQUEST['name_w'] ?>_model = "cut";
                        break;

                    case 'stream':
                        <?= $_REQUEST['name_w'] ?>_model="stream";
                        break;    

                    default:
                        <?= $_REQUEST['name_w'] ?>_model="3d";
                        break;
                }

                if (timeRange == null || timeRange == undefined) {
                    timeRange = widgetData.params.temporal_range_w;
                }

                if(idMetric === 'AggregationSeries' || idMetric.includes("NR_"))
                {
                    rowParameters = JSON.parse(rowParameters);
                    timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, null, timeNavCount);
                //    populateWidget(true, timeRange);
                }
                else
                {
                    populateWidget(false, null, null, timeNavCount);
                //    populateWidget(false, null);
                }

                // Hide Next Button at first instantiation
                if(timeNavCount == 0) {
                    if (rowParameters != null) {
                        for (let k = 0; k < rowParameters.length; k++) {
                            if (rowParameters[k].metricHighLevelType == "Sensor Device" || rowParameters[k].metricHighLevelType == "Sensor Device" || rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Device" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                                let urlKBToBeCalled = "";
                                let field = "";
                                let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                                urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeURI(rowParameters[k].serviceUri);
                                field = rowParameters[k].smField;

                                $.ajax({
                                    url: urlKBToBeCalled,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (originalData) {
                                        var stopFlag = 1;
                                        var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                        if (convertedData) {
                                            if (convertedData.data.length > 0) {
                                                var localTimeZone = moment.tz.guess();
                                                var momentDateTime = moment();
                                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                localDateTime = localDateTime.replace("T", " ");
                                                var plusIndexLocal = localDateTime.indexOf("+");
                                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                var localTimeZoneString = "";
                                                if (localDateTime == "") {
                                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                } else {
                                                    localTimeZoneString = localTimeZone;
                                                }
                                                if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
                                                    dataFut = (convertedData.data[0].commit.author.futureDate);
                                                    if (moment(dataFut).isAfter(momentDateTime)) {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                    } else {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                    }
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                }
                                            } else {
                                                showWidgetContent(widgetName);
                                            //    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                        //    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data) {
                                        //  showWidgetContent(widgetName);
                                        //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Errore in chiamata prima API");
                                        console.log(JSON.stringify(data));
                                    }
                                });
                            } else {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                            }
                        }
                    }
                }

                // Modify width to show newly implemented PREV and NEXT buttons
                var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv').css("width", "3.5%");
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_countdownContainerDiv').css("width", "3%");
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("color", widgetHeaderFontColor);
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
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
            timeRange = event.newTimeRange;
            populateWidget(true, event.newTimeRange, null, 0);
         //   populateWidget(true, event.newTimeRange);
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


           function toggleClass(elem,className){
              if (elem.className.indexOf(className) !== -1){
                elem.className = elem.className.replace(className,'');
              }
              else{
                elem.className = elem.className.replace(/\s+/g,' ') +   ' ' + className;
              }

              return elem;
            }

            function toggleDisplay(elem){
              const curDisplayStyle = elem.style.display;           

              if (curDisplayStyle === 'none' || curDisplayStyle === ''){
                elem.style.display = 'block';
              }
              else{
                elem.style.display = 'none';
              }

            }

            function handleOptionSelected(e){
              toggleClass(e.target.parentNode, 'hide');         

              const id = e.target.id;
              const newValue = e.target.textContent + ' ' + '<i id="<?= $_REQUEST['name_w'] ?>_angle" class="fa fa-angle-right"></i>';
              const titleElem = document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle'); //document.querySelector('.dropdown .title');

              titleElem.innerHTML = newValue;

              //trigger custom event
              //document.querySelector('.dropdown .title').dispatchEvent(new Event('change'));
                //setTimeout is used so transition is properly shown
              <?= $_REQUEST['name_w'] ?>_select = e.target.textContent.trimEnd();
              //populateWidget(true, timeRange, "minus", timeNavCount);
              loadHyperCube();
              drawDiagram(true, xAxisFormat, yAxisType);
            }

            function handleTitleChange(e){
              
            }

            function clear(){
                dateChoice = null;
                $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').val='';
            }

            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker().on('dp.show',function(){
                $('.media').css({'overflow':'visible', 'z-index':'1000000'});
            }).on('dp.hide',function(){
                $('.media').css({'overflow':'hidden'});
            })

            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker().on('dp.change', function (e) {  
                var date = $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date();
                dateChoice = date;
                timeNavCount = 0;
                    populateWidget(true, timeRange, null, 0);
                    loadHyperCube();
                    drawDiagram(true, xAxisFormat, yAxisType);
            });
            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").clear()
            var buttonCut = document.getElementById('<?= $_REQUEST['name_w'] ?>_cut');
                buttonCut.onclick = function(){
                    if (<?= $_REQUEST['name_w'] ?>_model=='3d')
                        <?= $_REQUEST['name_w'] ?>_model = 'cut';
                    else if (<?= $_REQUEST['name_w'] ?>_model=='cut')
                        <?= $_REQUEST['name_w'] ?>_model = '3d';
                    else
                        <?= $_REQUEST['name_w'] ?>_model = 'cut';

                    loadHyperCube();
                    drawDiagram(true, xAxisFormat, yAxisType);
                };
                var buttonStream = document.getElementById('<?= $_REQUEST['name_w'] ?>_stream');
                buttonStream.onclick = function(){
                    if (<?= $_REQUEST['name_w'] ?>_model=='3d')
                        <?= $_REQUEST['name_w'] ?>_model = 'stream';
                    else if (<?= $_REQUEST['name_w'] ?>_model=='stream')
                        <?= $_REQUEST['name_w'] ?>_model = '3d';
                    else
                        <?= $_REQUEST['name_w'] ?>_model = 'stream';
                    loadHyperCube();
                    drawDiagram(true, xAxisFormat, yAxisType);
                };

                if (<?= $_REQUEST['name_w'] ?>_loaded==false){

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('click', function (e) {
                  const dropdown = e.currentTarget.parentNode;
                  const menu = dropdown.querySelector('.menu');

                  toggleClass(menu,'hide');
               });

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('change', function (e) {
                    <?= $_REQUEST['name_w'] ?>_select = e.target.textContent.trimEnd();
                    //populateWidget(true, timeRange, "minus", timeNavCount);
                    loadHyperCube();
                    drawDiagram(true, xAxisFormat, yAxisType);
                });
                <?= $_REQUEST['name_w'] ?>_loaded = true;
            }

    });

</script>

<!-- <div class="widget" id="<?= $_REQUEST['name_w'] ?>_div" style="overflow: visible !important;"> -->
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

            <div style="position: relative;">
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
             <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
                    <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
                </div>
<div class="widget-dropbdown" style="position: absolute; ">
                                <div class='dropdown' style="float: left;width: 20%;">
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_droptitle' class='dropdown-title title pointerCursor'>Select unit<i id="<?= $_REQUEST['name_w'] ?>_angle" class="fa fa-angle-right"></i></div>
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_options' class='menu pointerCursor hide'></div>

                        </div>
                                <div class ="form-group" style="float: left;width:30%;">  
                                <div class ='input-group date' id='<?= $_REQUEST['name_w'] ?>_datetimepicker'>  
                                  <input type ='text' class="form-control" /> 
                                  <span class ="input-group-addon">  
                                    <span class ="glyphicon glyphicon-calendar"></span>  
                                  </span>  
                                </div> 
                              </div>
                              
                         
                            <button id='<?= $_REQUEST['name_w'] ?>_cut' style="float: left;width:25%; padding:0.6em 0em;">Toggle Time Slice</button>
                            <button id='<?= $_REQUEST['name_w'] ?>_stream' style="float: left;width:25%; padding:0.6em 0em;">Toggle Stream Graph</button>
                        
                 </div>
        </div>	
</div> 


