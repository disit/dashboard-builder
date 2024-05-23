<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>
<!-- <script src ="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script> -->
<script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type='text/javascript'>
var <?= $_REQUEST['name_w'] ?>_loaded = false;
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
            chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor, appId, flowId, nrMetricType, gridLineColor, chartAxesColor,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, xAxisType, xAxisCategories, chartSeriesObject,
                stackingOption, widgetHeight, metricName, widgetTitle, countdownRef, widgetParameters, rowParameters, thresholdsJson, infoJson, xAxisTitle, smField, groupByAttr = null;
        var seriesDataArray = [];
        var serviceUri = "";
        var webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var code, connections = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            showHeader = false;
        }
        else
        {
            showHeader = true;
        }
        //////
		$(document).off('showRadarSeriesFromExternalContent_' + widgetName);
        $(document).on('showRadarSeriesFromExternalContent_' + widgetName, function(event){


            //////////
            console.log(event);
            if (event.event == 'set_time'){         
                                console.log(event.passedData);
                            if ((event.passedData == null)||(event.passedData.length === 0)){
                                var rows1=[];
                                $.ajax({
                                        url: "../controllers/getWidgetParams.php",
                                        type: "GET",
                                        data: {
                                            widgetName: "<?= $_REQUEST['name_w'] ?>"
                                        },
                                        async: true,
                                        dataType: 'json',
                                        success: function(widgetData) {
                                            rows1 = JSON.parse(widgetData.params.rowParameters);
                                            rowParameters = rows1;
                                            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date(event.datetime); 
                                            var date = $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date();
                                        }
                                    });
                                    //////////////////////////////
                                    let newId = '';
                                            var events = [];
                                            var times = []; 
                                                    if(localStorage.getItem("events") == null){
                                                            newId = "RadarSerieSelectTime";        
                                                            events.push("RadarSerieSelectTime1");
                                                            times.push(dateChoice);
                                                            localStorage.setItem("events", JSON.stringify(events));
                                                            localStorage.setItem("times", JSON.stringify(times));
                                                            $('#BIMenuCnt').append('<div id="RadarSerieSelectTime1" class="row" data-selected="false"></div>');
                                                            $('#RadarSerieSelectTime1').append('<div class="col-md-12 orgMenuSubItemCnt">RadarSerieSelectTime1</div>' );
                                                    }
                                                            events = JSON.parse(localStorage.getItem("events"));
                                                            times = JSON.parse(localStorage.getItem("times"));
                                                            console.log(events.length);
                                                            var count_events = events.length;
                                                                let j=1;
                                                                for(var e=0; e<count_events; e++){
                                                                if(events[e].includes("RadarSerieSelectTime")){
                                                                    j++;
                                                                }
                                                                    
                                                                    newId = "RadarSerieSelectTime"+j;
                                                                    if(!events.includes(newId)){
                                                                    events.push(newId);
                                                                    times.push(dateChoice);
                                                                    $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                                                    $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                                                                    localStorage.setItem("times", JSON.stringify(times));
                                                                    localStorage.setItem("events", JSON.stringify(events));
                                                                    }
                                                                }
                                                            $('#'+newId).on( "click", function() {
                                                                var events = JSON.parse(localStorage.getItem("events"));
                                                                var times = JSON.parse(localStorage.getItem("times"));
                                                                for(var e =0; e<events.length; e++){

                                                                if(events[e].includes("RadarSerieSelectTime")){
                                                                var widgets = JSON.parse(localStorage.getItem("widgets"));
                                                                var index = JSON.parse(localStorage.getItem("events")).indexOf(newId);
                                                                var curr_data = times[index];
                                                                        for(var w in widgets){
                                                                            if(widgets[w] != null){
                                                                                var new_currDate = new Date(curr_data);
                                                                                $('#'+widgets[w]+'_datetimepicker').data("DateTimePicker").date(new_currDate); 
                                                                                var date1 = $('#'+widgets[w]+'_datetimepicker').data("DateTimePicker").date();
                                                                                set_time(date1);
                                                                                //populateWidget(null, date1);
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            });
                                                $('.orgMenuSubItemCnt').mouseover(function() {
                                                $('.orgMenuSubItemCnt').css('cursor', 'pointer');
                                              });
                                            //////////////////////////////
                            }else{
                                $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date(event.datetime); 
                            }
                            timeNavCount = 0;
                                set_time(event.datetime);
                                populateWidget(event.datetime);            
                        }
                    /////////

            if(localStorage.getItem("widgets") == null){
                var widgets = [];
                widgets.push(widgetName);
                localStorage.setItem("widgets", JSON.stringify(widgets));
            }
            else{
                var widgets = JSON.parse(localStorage.getItem("widgets"));
                if(!widgets.includes(widgetName)){
                    widgets.push(widgetName);
                    localStorage.setItem("widgets", JSON.stringify(widgets));
                }
            }


            clearInterval(countdownRef);
            //$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
            //<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, event.range, event.marker, event.mapRef);
            if(encodeURIComponent(metricName) === encodeURIComponent(metricName)) {
                $("#" + widgetName + "_loading").css("display", "block");
            
            
                if(localStorage.getItem("passedData") == null || localStorage.getItem("passedData") == "[object Object]"){
                    var init = [];
                    var firstEl = {};
                    firstEl.passedData = event.passedData;
                    firstEl.name = widgetName;
                    if (localStorage.getItem("events") == null) {
                        firstEl.eventIndex = 0;
                    } else {
                        firstEl.eventIndex = JSON.parse(localStorage.getItem("events")).length - 1;
                    }
                    init.push(firstEl);
                    localStorage.setItem("passedData", JSON.stringify(init));
                }
                else{
                    var newEl = {};
                    newEl.passedData = event.passedData;
                    newEl.name = widgetName;
                    if (localStorage.getItem("events") == null) {
                        newEl.eventIndex = 0;
                    } else {
                        newEl.eventIndex = JSON.parse(localStorage.getItem("events")).length - 1;
                    }
                    var oldElement = JSON.parse(localStorage.getItem("passedData"));
                    oldElement.push(newEl);
                    localStorage.setItem("passedData", JSON.stringify(oldElement));
                    console.log(localStorage)
                }
                rowParameters = event.passedData;
                populateWidget(null, true);
            }
		});
		
        $(document).off('reloadPreviousContent_' + widgetName);
        $(document).on('reloadPreviousContent_' + widgetName, function(event){
            var passedData = JSON.parse(localStorage.getItem("passedData"));
            var j = 0;
            var t = -1;
            while(passedData[j].eventIndex <= event.index && j < passedData.length - 1){
                if(passedData[j].name === widgetName){
                    t = j;
                }
                j = j+1;
            }
            if(t == -1){
                $('body').trigger({
                    type: "resetContent_"+widgetName
                });
            }
            else{
                rowParameters = passedData[t].passedData;
                populateWidget();
            }
        })

        $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker({
            showTodayButton: true,
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            }
        })

		/////
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

        console.log("Entrato in widgetRadarSeries --> " + widgetName);

        function serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode) {

            var deviceLabels = [];
            var metricLabels = [];
            var auxLabels = [];
            let mappedSeriesDataArray = [];

            metricLabels = getMetricLabelsForBarSeries(rowParameters);
            deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
            if (groupByAttr != null) {
                //if (groupByAttr == "metrics") {
                if (groupByAttr == "value type") {
                    flipFlag = false;
                    //    } else if (groupByAttr == "device") {
                } else if (groupByAttr == "value name") {
                    flipFlag = true;
                }
            } else {
                flipFlag = false;
            }
            if (flipFlag !== true) {
                mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
            } else {
                mappedSeriesDataArray = buildBarSeriesArrayMap2(seriesDataArray);
                auxLabels = metricLabels;
                metricLabels = deviceLabels;
                deviceLabels = auxLabels;
            }
            series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels, flipFlag);

            xAxisCategories = metricLabels.slice();

            widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

            chartSeriesObject = getChartSeriesObject(series, editLabels, fromCode);
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
        
        function getChartSeriesObject(series, xAxisLabelsEdit, fromCode)
        {
            var chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, zonesObject, zonesArray, inf, sup, i = null;
            
            if(series !== null)
            {
                chartSeriesObject = [];
                
                var seriesArray = null;
                    
                    for (var i in series.secondAxis.series) 
                    {
                        if (xAxisLabelsEdit != null) {
                            if (xAxisLabelsEdit.length == series.secondAxis.labels.length && !fromCode) {
                                seriesName = xAxisLabelsEdit[i];
                            } else {
                                // flipped case
                                seriesName = series.secondAxis.labels[i];
                            }
                        } else {
                            seriesName = series.secondAxis.labels[i];
                        }
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

        function filterArray(test_array) {
            // Remove NaN, null, undefined and empty elements in test_array
            var index = -1,
                arr_length = test_array ? test_array.length : 0,
                resIndex = -1,
                result = [];

            while (++index < arr_length) {
                var value = test_array[index];

                if (value) {
                    result[++resIndex] = value;
                }
            }

            return result;
        }

        function adjustArrayForHighcharts(test_array) {
            // Replace NaN, undefined and empty elements with null in test_array
            var index = -1,
                arr_length = test_array ? test_array.length : 0,
                resIndex = -1,
                result = [],
                resultString = "";

            while (++index < arr_length) {
                var value = test_array[index];

                if (index == 0) {
                    if (value) {
                        result[++resIndex] = value;
                        resultString = value.toString();
                    } else {
                        result[++resIndex] = null;
                        resultString = null;
                    }
                } else if (index == arr_length - 1) {
                    if (value) {
                        result[++resIndex] = value;
                        resultString = resultString + ', ' + value.toString();
                    } else {
                        result[++resIndex] = null;
                        resultString = resultString + ', ' + null;
                    }
                } else {
                    if (value) {
                        result[++resIndex] = value;
                        resultString = resultString + ', ' + value.toString();
                    } else {
                        result[++resIndex] = null;
                        resultString = resultString + ', ' + null;
                    }
                }
            }

            return resultString;
        }

	    function drawDiagram(timeDomain) {

            if(timeDomain)
            {
                xAxisType = 'datetime';
                xAxisCategories = null;
            }
            else
            {
                xAxisType = null;
            }

            // GP - Different scale yaxis
            let yaxisOpts = "[";
            let yaxisOptsJson = null;
            let yaxisMin = [];
            let yaxisMax = [];
            let orderedSeriesForMinMax = [];

            for( let m = 0; m < chartSeriesObject[0].data.length; m++) {

                orderedSeriesForMinMax[m] = [];

                for (let n = 0; n < chartSeriesObject.length; n++) {

                    orderedSeriesForMinMax[m].push(chartSeriesObject[n].data[m]);

                }

                if (filterArray(orderedSeriesForMinMax[m]).length > 0) {
                    let currentMinVal = Math.min.apply(null, filterArray(orderedSeriesForMinMax[m]));
                    //    let currentMaxVal = Math.max(chartSeriesObject[n].data);
                    let currentMaxVal = Math.max.apply(null, filterArray(orderedSeriesForMinMax[m]));
                    // increase yaxis range of 20% with respect to max and min series values
                    yaxisMin[m] = Math.min(0, currentMinVal - currentMinVal * 0.2);
                    yaxisMax[m] = currentMaxVal + currentMaxVal * 0.2;
                    if (isNaN(yaxisMin[m])) {
                        yaxisMin[m] = 0;
                    }
                    if (isNaN(yaxisMax[m])) {
                        yaxisMax[m] = 0;
                    }
                } else {
                    yaxisMin[m] = 0;
                    yaxisMax[m] = 10;
                }

                if (m == chartSeriesObject[0].data.length - 1) {
                    yaxisOpts = yaxisOpts + '{"gridLineInterpolation": "polygon", "lineWidth": 0, "gridZIndex": 0, "gridLineColor": "' + styleParameters.gridLinesColor + '", "gridLineWidth": ' + styleParameters.gridLinesWidth + ', "title": {"text": null}, "labels": {"overflow": "justify", "style": {"fontFamily": "Montserrat", "fontSize": "' + styleParameters.colsLabelsFontSize + 'px", "fontWeight": "bold", "color": "' + styleParameters.colsLabelsFontColor + '", "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"}}, "min": ' + yaxisMin[m] + ', "max": ' + yaxisMax[m] + '}]';
                } else {
                    yaxisOpts = yaxisOpts + '{"gridLineInterpolation": "polygon", "lineWidth": 0, "gridZIndex": 0, "gridLineColor": "' + styleParameters.gridLinesColor + '", "gridLineWidth": ' + styleParameters.gridLinesWidth + ', "title": {"text": null}, "labels": {"overflow": "justify", "style": {"fontFamily": "Montserrat", "fontSize": "' + styleParameters.colsLabelsFontSize + 'px", "fontWeight": "bold", "color": "' + styleParameters.colsLabelsFontColor + '", "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"}}, "min": ' + yaxisMin[m] + ', "max": ' + yaxisMax[m] + '}, '
                }

            }

            yaxisOptsJson = JSON.parse(yaxisOpts);

            Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                chart: {
                    type: 'line',
                    polar: true,
                    backgroundColor: 'transparent',
                    parallelCoordinates: true,
                    parallelAxes: {
                        labels: {
                            style: {
                                color: '#999999'
                            }
                        },
                        gridLineWidth: 1,
                        lineWidth: 2,
                        showFirstLabel: false,
                        showLastLabel: true
                    },
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
                        useHTML: false,
                        style: {
                            fontFamily: 'Montserrat',
                            fontSize: styleParameters.rowsLabelsFontSize + "px",
                            fontWeight: 'bold',
                            color: styleParameters.rowsLabelsFontColor,
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                        }
                    }
                },
                yAxis: yaxisOptsJson,
             //   yAxis: {
                //    gridLineInterpolation: 'polygon',
                //    lineWidth: 0,
                //    gridZIndex: 0,
                //    gridLineColor: styleParameters.gridLinesColor,
                //    gridLineWidth: styleParameters.gridLinesWidth,
                //    title: {
                //        text: null
                //    },
                //    labels: {
                //        overflow: 'justify',
                //        style: {
                //            fontFamily: 'Montserrat',
                //            fontSize: styleParameters.colsLabelsFontSize + "px",
                //            fontWeight: 'bold',
                //            color: styleParameters.colsLabelsFontColor,
                 //           "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                //        }
                 //   }
              //  },
                tooltip: {
                    style: {
                        fontFamily: 'Montserrat',
                        fontSize: 12 + "px",
                        color: 'black',
                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.15)",
                        "z-index": 5
                    },
                    useHTML: false,
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
                        var field = this.series.name;
                        var thresholdObject, desc, min, max, color, fieldName, index, message = null;
                        var rangeOnThisField = false;
                        var dataStringInPopup = "";
                        var dateMessage = "";
                        var valueUnitInPopup = "";

                        for (var n = 0; n < seriesDataArray.length; n++) {
                            if (seriesDataArray[n].metricName == this.series.name && seriesDataArray[n].metricType == this.category) {
                                dataStringInPopup = seriesDataArray[n].measuredTime;
                              //  if(seriesDataArray[n].metricValueUnit != null) {
                                    valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                              //  }
                            } else if (styleParameters.editDeviceLabels) {
                                if (seriesDataArray[n].metricName == series.secondAxis.labels[styleParameters.editDeviceLabels.indexOf(field)] && seriesDataArray[n].metricType == this.category) {
                                    dataStringInPopup = seriesDataArray[n].measuredTime;
                                  //  if(seriesDataArray[n].metricValueUnit != null) {
                                        valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                                  //  }
                                }
                            }
                        }

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
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                                }
                                                else
                                                {
                                                    return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: < <b>' + max + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
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
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                                }
                                                else
                                                {
                                                    return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: < <b>' + dataStringInPopup + '</b><br/>';
                                                }
                                            }
                                            else if((i === (elementUpperBounds.length - 1))&&(parseFloat(this.y) >= max))
                                            {
                                                return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value higher than the greatest upper bound<br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    //Non sono stati definiti range
                                    return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> No thresholds defined<br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                }


                            }
                            else
                            {
                                //Non sono stati definiti range
                                return '<span style="color:' + this.color + '">\u25CF</span> ' + ' <b>' + fieldName + '</b> @ <b>' + this.series.name + '</b><br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value: <b>' + this.y + '</b><br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> No thresholds defined<br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                            }
                        }
                    }				
                },
                plotOptions: {
                    line: {
                        lineWidth: styleParameters.linesWidth,
                        events: {
                         /*   legendItemClick: function(){
                                return false;
                            }*/
                        },
                    },
                    series: {
                        events: {
                            legendItemClick: function () {
                                if(styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                    var selectedData = {};
                                    selectedData.event = "legendItemClick";
                                    selectedData.layers = [];
                                    selectedData.metrics = [];
                                    let selected = this.data[0].series.name;
                                    for (var m in this.data) {
                                        selectedData.metrics[m] = this.data[m].category;
                                    }
                                    for (var it in this.chart.legend.allItems) {
                                        selectedData.layers[it] = {};
                                        selectedData.layers[it].name = this.chart.legend.allItems[it].name;
                                        selectedData.layers[it].visible = this.chart.legend.allItems[it].visible;
                                        if (this.chart.legend.allItems[it].name == selected && this.chart.legend.allItems[it].visible == true) {   //FIX ME
                                            selectedData.layers[it].visible = false;
                                        }
                                        if (this.chart.legend.allItems[it].name == selected && this.chart.legend.allItems[it].visible == false) {
                                            selectedData.layers[it].visible = true;
                                        }
                                    }

                                    let j=1;
                                    if(localStorage.getItem("events") == null){

                                        var events = [];
                                        events.push("RadarLegendClick1");
                                        localStorage.setItem("events", JSON.stringify(events));
                                    }
                                    else{
                                        var events = JSON.parse(localStorage.getItem("events"));
                                        for(var e in events){
                                            if(events[e].slice(0,16) == "RadarLegendClick")
                                                j = j+1;
                                        }
                                        events.push("RadarLegendClick" + j);
                                        localStorage.setItem("events", JSON.stringify(events));
                                    }

                                    let newId = "RadarLegendClick"+j;
                                    $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                    $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>');
                                    $('#'+newId).on( "click", function() {
                                        var widgets = JSON.parse(localStorage.getItem("widgets"));
                                        var index = JSON.parse(localStorage.getItem("events")).indexOf(newId);
                                        for(var w in widgets){
                                            if(widgets[w] != null){
                                                $('body').trigger({
                                                    type: "reloadPreviousContent_"+widgets[w],
                                                    index: index
                                                });
                                            }
                                        }
                                    });
                                    $( '#'+newId ).mouseover(function() {
                                        $('#'+newId).css('cursor', 'pointer');
                                    });
                                    if (connections != null) {
                                        selectedData.connections = connections;
                                    }
                                    selectedDataJson = JSON.stringify(selectedData);
                                    try {
                                        execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson);
                                    } catch(e) {
                                        console.log("Error in JS function from legend click on " + widgetName);
                                    }
                                }
                            }
                        },
                        states: {
                            hover: {
                             //   enabled: false
                                lineWidth: styleParameters.linesWidth
                            },
                            inactive: {
                                lineWidth: 1
                            }
                        },
						cursor: 'pointer',
						 point: {
							 events: {
								click: function() {
									if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code != null && code != "null") {
										//console.log(this);
										//
										//
										/*var data_execute =[];
										data_execute['value'] = this.y;
										data_execute['metricname']=this.series.name;
										data_execute['metricType']=this.category;
										data_execute['date']=seriesDataArray[0].measuredTime;   */

										selectedData = {};
                                        selectedData.event = "click";
                                        selectedData.value = {};
                                        selectedData.value.metricType = this.category;
                                        selectedData.value.metricName = this.series.name;
                                        if (connections != null) {
                                            selectedData.connections = connections;
                                        }
                                        selectedDataJson = JSON.stringify(selectedData);
                                        try {
                                            execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson);
                                            let j=1;
                                            if(localStorage.getItem("events") == null){

                                                var events = [];
                                                events.push("RadarClick1");
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }
                                            else{
                                                var events = JSON.parse(localStorage.getItem("events"));
                                                for(var e in events){
                                                    if(events[e].slice(0,10) == "RadarClick")
                                                        j = j+1;
                                                }
                                                events.push("RadarClick" + j);
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }

                                            let newId = "RadarClick"+j;
                                            $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                            $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                                            $('#'+newId).on( "click", function() {
                                                var widgets = JSON.parse(localStorage.getItem("widgets"));
                                                var index = JSON.parse(localStorage.getItem("events")).indexOf(newId);
                                                for(var w in widgets){
                                                    if(widgets[w] != null){
                                                        $('body').trigger({
                                                            type: "reloadPreviousContent_"+widgets[w],
                                                            index: index
                                                        });
                                                    }
                                                }
                                            });
                                            $( '#'+newId ).mouseover(function() {
                                                $('#'+newId).css('cursor', 'pointer');
                                            });
                                        } catch(e) {
                                            console.log("Error in JS function from click on " + widgetName);
                                        }
									}
									//alert ('Category: '+ this.category +', value: '+ this.y);
								}
							}
						}
                    }
                },
                legend: {
                    enabled: true,
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

        function populateWidget(fromAggregate, fromCode) {
            seriesDataArray = [];
			var fromDate = null;
			if ((fromAggregate != null)&&(fromAggregate != '')){
				date = new Date(fromAggregate);
				 var y = date.getFullYear();
				 var m = date.getMonth() + 1; 
					if (m < 10){
						m = '0' +m;
					}				 
				 var d = date.getDate();
				 if (d < 10){
						d = '0' +d;
					}	
				 var h = date.getHours();
				 if (h < 10){
						h = '0' +h;
					}
				 var s = date.getMinutes();
				 if (s < 10){
						s = '0' +s;
					}
				 fromDate = y +'-'+m+'-'+d+'T'+h+':'+s+':00';
				 //console.log(fromDate);
			}

            let aggregationFlag = false;
            if (rowParameters != null) {
                if (rowParameters[0].metricHighLevelType == "Sensor" || rowParameters[0].metricHighLevelType == "MyKPI" || rowParameters[0].metricHighLevelType == "IoT Device Variable" || rowParameters[0].metricHighLevelType == "Data Table Variable" || rowParameters[0].metricHighLevelType == "Mobile Device Variable") {
                    aggregationFlag = true;
                }
            }

            //    if (widgetData.params.id_metric === 'AggregationSeries' || aggregationFlag === true || widgetData.params.id_metric.includes("NR_"))				
            if (metricName === 'AggregationSeries' || aggregationFlag === true || nrMetricType != null) {
            //    rowParameters = JSON.parse(rowParameters);
                aggregationGetData = [];
                getDataFinishCount = 0;
                //     var editLabels = (JSON.parse(widgetData.params.styleParameters)).editDeviceLabels;
                var editLabels = styleParameters.editDeviceLabels;
		// var editLabels = undefined;				// MORGANTI 
                for (var i = 0; i < rowParameters.length; i++) {
                    aggregationGetData[i] = false;
                }

                for (var i = 0; i < rowParameters.length; i++) {
                    let dataOrigin = rowParameters[i].metricHighLevelType;
                    switch (dataOrigin) {
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

                        case "IoT Device Variable":
                        case "Data Table Variable":
                        case "Mobile Device Variable":
                        case "Sensor":
                            var timeRange = null;
                            var urlToCall = "";
                            var xlabels = [];
                            let smUrl = "";
							var fromDate_url = '';
							if (fromDate != null){
								fromDate_url = '&toTime='+fromDate;
							}
                            if (rowParameters[i].metricId.split("serviceUri=").length > 1) {
                                smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId.split("serviceUri=")[1]+fromDate_url;
                            } else {
                                smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId+fromDate_url;
                            }
                            //    metricType = "Float";
							
                            if ("<?= $_REQUEST['timeRange']?>") {
                                if ("<?= $_REQUEST['timeRange'] ?>" != 'last' && "<?= $_REQUEST['timeRange'] ?>" != "") {
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

                            getSmartCitySensorValues(rowParameters, i, smUrl, null, true, function (extractedData) {

                                if (extractedData) {
                                    seriesDataArray.push(extractedData);
                                } else {
                                    console.log("Dati Smart City non presenti");
                                    seriesDataArray.push(undefined);
                                }
                                //if (endFlag === true) {
                                // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                if (rowParameters.length === seriesDataArray.length) {
                                    // DO FINAL SERIALIZATION
                                    serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode);
                                }

                            });
                            break;

                        case "Dynamic":
                            let extractedData = {};
                            extractedData.value = rowParameters[i].value;
                            extractedData.metricType = rowParameters[i].metricType;
                            extractedData.metricId = rowParameters[i].metricId;
                            extractedData.metricName = rowParameters[i].metricName;
                            extractedData.measuredTime = rowParameters[i].measuredTime;
                            extractedData.metricValueUnit = rowParameters[i].metricValueUnit;

                            seriesDataArray.push(extractedData);

                            if (rowParameters.length === seriesDataArray.length) {
                                // DO FINAL SERIALIZATION
                                serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode)
                            }

                            break;

                        case "MyKPI":

                            //    var convertedData = getMyKPIValues(rowParameters[i].metricId);
							var fromDate_url = null;
							if (fromDate != null){
								fromDate_url = '&to='+fromDate;
							}
                            let aggregationCell = [];
                            var xlabels = [];
                            let kpiMetricName = rowParameters[i].metricName;
                            let kpiMetricType = rowParameters[i].metricType;
                            if (rowParameters[i].metricId.includes("datamanager/api/v1/poidata/")) {
                                rowParameters[i].metricId = rowParameters[i].metricId.split("datamanager/api/v1/poidata/")[1];
                            }

                            //getMyKPIValues(rowParameters, i, null, 1, function (extractedData) {
							getMyKPIValues(rowParameters, i, fromDate_url, 1, function (extractedData) {
							    let countEmpty = 0;
                                if (extractedData) {
                                    if (Object.keys(extractedData).length > 0) {
                                        seriesDataArray.push(extractedData);
                                    } else {
                                        countEmpty++;
                                    }
                                } else {
                                    console.log("Dati Smart City non presenti");
                                    seriesDataArray.push(undefined);
                                }
                                //if (endFlag === true) {
                                // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                if (rowParameters.length === seriesDataArray.length - countEmpty) {
                                    // DO FINAL SERIALIZATION
                                    serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode)
                                }

                            });
                            break;

                    }
                }
            } else {
                $.ajax({
                    url: getMetricDataUrl,
                    type: "GET",
                    data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                //    data: {"IdMisura": [widgetData.params.id_metric]},
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                        metricData = data;
                        $("#" + widgetName + "_loading").css("display", "none");

                        if (metricData.data.length !== 0) {
                            metricType = metricData.data[0].commit.author.metricType;
                            series = JSON.parse(metricData.data[0].commit.author.series);

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
                        } else {
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
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
            var seriesObj = {
                firstAxis:{
                    //desc:"Metrics",
                    desc:"value type",
                    labels:[]
                },
                secondAxis:{
                    //   "desc":"Values",
                    "desc":"value name",
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
        
        //Codice core del widget
        if(url === "null")
        {
            url = null;
        }
        
    /*    if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
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
        }*/
        
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
                serviceUri = widgetData.params.serviceUri;
				code = widgetData.params.code;
                connections = widgetData.params.connections;

                if (nrMetricType != null) {
                    openWs();
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
                    groupByAttr = styleParameters['groupByAttr'];
                }

                if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code != null && code != "null") {
                    var text_ck_area = document.createElement("text_ck_area");
                    text_ck_area.innerHTML = code;
                    var newInfoDecoded = text_ck_area.innerText;
                    newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");

                    var elem = document.createElement('script');
                    elem.type = 'text/javascript';
                    // elem.id = "<?= $_REQUEST['name_w'] ?>_code";
                    // elem.src = newInfoDecoded;
                    elem.innerHTML = newInfoDecoded;
                    try {
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                    } catch(e) {
                        console.log("Error in appending JS function on " + widgetName);
                    }
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

           /*     switch(chartType)
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
                }*/
                rowParameters = widgetData.params.rowParameters;	// MORGANTI
                if(typeof rowParameters == 'string')			// MORGANTI
                	rowParameters = JSON.parse(rowParameters);

                populateWidget(null);

            // GP FINE SECONDA SOLUZIONE ------------------------------------------------------------
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

        //Web socket
        openWs = function(e)
        {
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
                //echo 'webSocket = new SharedWebSocket(widgetName, metricName, "' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';
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
                wsClosed();
            }
        };

        manageIncomingWsMsg = function(msg)
        {
            var msgObj = JSON.parse(msg.data);

            switch(msgObj.msgType)
            {
                case "newNRMetricData":
                    if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                    {
                        //    <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

                        var newValue = msgObj.newValue;
                    //    var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];
                    //    point.update(newValue);

                        rowParameters = newValue;
                        populateWidget(null);

                    }
                    break;

                default:
                    break;
            }
        };

        openWsConn = function(e)
        {
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                metricName: encodeURIComponent(metricName),
                widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
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
            webSocket.removeEventListener('close', wsClosed);
            webSocket.removeEventListener('open', openWsConn);
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            webSocket = null;
            if(wsRetryActive === 'yes')
            {
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }
        };

        //Per ora non usata
        wsError = function(e)
        {

        };
		$(document).off('resetContent_' + widgetName);
        $(document).on('resetContent_' + widgetName, function(){
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
                    rowParameters = JSON.parse(widgetData.params.rowParameters);
                    populateWidget();
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
                    //populateWidget(true, timeRange, null, 0);
					var timeRange = dateChoice;
                    //console.log(date);
                    set_time(date);
					populateWidget(date);
                    //loadHyperCube();
                    //drawDiagram(true, xAxisFormat, yAxisType);
            });
            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").clear()

                if (<?= $_REQUEST['name_w'] ?>_loaded==false){

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('click', function (e) {
                  const dropdown = e.currentTarget.parentNode;
                  const menu = dropdown.querySelector('.menu');

                  toggleClass(menu,'hide');
               });

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('change', function (e) {
                    <?= $_REQUEST['name_w'] ?>_select = e.target.textContent.trimEnd();
                    //populateWidget(true, timeRange, "minus", timeNavCount);
                    //loadHyperCube();
                    //drawDiagram(true, xAxisFormat, yAxisType);
                });
                <?= $_REQUEST['name_w'] ?>_loaded = true;
            }

		///////////////////
            function set_time(timestamp){        
                try {
                    execute_<?= $_REQUEST['name_w'] ?>(timestamp); 
                } catch(e) {
                        console.log("Error in JS function time selection"); 
                }
           }
        ///////////////////////////
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
		<!-- -->
		<div style="position: relative;">
		 <div class="widget-dropbdown" style="position: absolute;">
                                <div class='dropdown' style="float: left;width: 20%; padding-left: 5%">
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_droptitle' class='dropdown-title title pointerCursor'></div>
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_options' class='menu pointerCursor hide'></div>

                        </div>
                                <div class ="form-group" style="float: left;width:30%; ">  
                                <div class ='input-group date' id='<?= $_REQUEST['name_w'] ?>_datetimepicker'>
                                  <input type ='text' class="form-control" />
                                  <span class ="input-group-addon">
                                    <span class ="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                              
                         <!--
                            <button id='<?= $_REQUEST['name_w'] ?>_cut' style="float: left;width:25%; padding:0.6em 0em;">Toggle Time Slice</button>
                            <button id='<?= $_REQUEST['name_w'] ?>_stream' style="float: left;width:25%; padding:0.6em 0em;">Toggle Stream Graph</button>
							-->

                 </div>
			</div>
		<!-- -->
    </div>
	<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div> 