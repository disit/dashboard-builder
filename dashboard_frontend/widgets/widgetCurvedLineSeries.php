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

<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
<link rel="stylesheet" href="../js/jqueryUi/jquery-ui.css">
<!-- <link rel="stylesheet" href="../css/datePickerStyle.css"> -->
<script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type='text/javascript'>
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
        var metricData, metricType, series, styleParameters, timeRange, gridLineColor, chartAxesColor, chartType, index, highchartsChartType, chartSeriesObject, legendWidth, xAxisCategories, rowParameters, aggregationGetData, getDataFinishCount, xAxisType,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, fontSize, fontColor, chartColor, dataLabelsFontSize, chartLabelsFontSize, dataLabelsFontColor, chartLabelsFontColor, appId, flowId, nrMetricType,
            widgetHeight, lineWidth, xAxisTitle, smField, metricName, widgetTitle, countdownRef, widgetParameters, thresholdsJson, infoJson, xAxisFormat, yAxisType, infoJson, idMetric = null;
        var serviceUri = "";
        var editLabels = "";
        var valueUnit = null;
        var seriesDataArray = [];
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
        var errorsLog = null;
        var typicaltrend = null;
        var trendType = null;
        var trendDate = null;
        var TTTDate = null;
        var dayhourview = null;
        var computationType = null;
        var counterday = 1;
        var currWeekDay = null;
        var webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var areaOpacity = null;
        var chart = null;
        var idYAxis = null;
        var code = null;
        var yAxisMin, yAxisMax, secondaryYAxisMin, secondaryYAxisMax = null;
        var timeNavigationButtonClick = null;

        //var trendType = 'monthWeek';
        //var trendType = 'dayHour';
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
                    populateWidget(true, timeRange, null, timeNavCount, null);
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
        $(document).off('showCurvedLinesFromExternalContent_' + widgetName);
        $(document).on('showCurvedLinesFromExternalContent_' + widgetName, function(event)
        {
            if (event.event == 'set_time'){         
                                //console.log(event.passedData);
                if ((event.passedData == null)||(event.passedData.length === 0)){
                    var rows1=[];
                    //////STORAGE///
                    $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date(event.datetime);
                    var date = $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date();
                    dateChoice = date;
                    //console.log(dateChoice);
                    var events = [];
                    var times = [];
                    //////////////////////////////
                    let j=1;
                    if(localStorage.getItem("events") == null){
                        newId = "CurvedLineSelectTime";
                        events.push("CurvedLineSelectTime");
                        times.push(dateChoice);
                        localStorage.setItem("events", JSON.stringify(events));
                        localStorage.setItem("times", JSON.stringify(times));
                    }else{
                       // var events = [];
                        //    var times = [];
                        events = JSON.parse(localStorage.getItem("events"));
                        times = JSON.parse(localStorage.getItem("times"));
                        for(var e in events){
                            //if(events[e].slice(0,20) == "CurvedLineSelectTime"){
                            if(events[e].includes("CurvedLineSelectTime")){
                                j = j+1;
                            }
                            newId = "CurvedLineSelectTime"+j;
                            events.push("CurvedLineSelectTime" + j);
                            times.push(dateChoice);

                            localStorage.setItem("times", JSON.stringify(times));
                            localStorage.setItem("events", JSON.stringify(events));
                            //console.log(times);
                            //console.log(events);
                        }
                    }

                    if(event.targetWidget === widgetName) {
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
                    }
                    $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                        $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                        $('#'+newId).on( "click", function() {
                            //console.log(localStorage);
                            //if(events[e].slice(0,20) == "CurvedLineSelectTime"){
                            if(events[e].includes("CurvedLineSelectTime")){
                            var widgets = JSON.parse(localStorage.getItem("widgets"));
                            var index = JSON.parse(localStorage.getItem("events")).indexOf(newId);
                            var curr_data = times[index];
                            console.log(widgets);
                                for(var w in widgets){
                                   // console.log(widgets[w]);
                                    //console.log(index);
                                    //console.log(curr_data);
                                    if(widgets[w] != null){
                                        $('#'+widgets[w]+'_datetimepicker').data("DateTimePicker").date(curr_data);
                                        var date1 = $('#'+widgets[w]+'_datetimepicker').data("DateTimePicker").date();
                                        set_time(date1);
                                        console.log(date1);
                                        //populateWidget(date1);
                                    }
                                }
                            }else{

                            }
                        });
                    $('.orgMenuSubItemCnt').mouseover(function() {
                        $('.orgMenuSubItemCnt').css('cursor', 'pointer');
                    });
                    //////////////////////////////
                    ///////FINHE STORAGE
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
                }else{
                    $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date(event.datetime);
                }
                timeNavCount = 0;
                set_time(event.datetime);
                populateWidget(event.datetime);
            }
            if(event.passedData != null && event.t1 == null && event.t2 == null){     //nuovi valori da mostrare da memorizzare nel localstorage
                if(event.passedData[0].metricHighLevelType == 'Dynamic'){
                    localStorage.setItem(widgetName, JSON.stringify(event.passedData));
                }
            }
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
            if(event.targetWidget === widgetName)
            {

                clearInterval(countdownRef);
            //    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
            //    <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, event.range, event.marker, event.mapRef);

                var newValue = null;
                if (event.passedData != null && event.command != "resize") {
                    newValue = event.passedData;
                    rowParameters = newValue;
                }
                //    var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];
                //    point.update(newValue);

                if(idMetric === 'AggregationSeries' || nrMetricType != null)
                {
                    //    rowParameters = JSON.parse(rowParameters);
                    if (typicaltrend == 'Yes') {
                        if (rowParameters != null && rowParameters.length > 1) {
                            rowParameters.splice(1, rowParameters.length - 1);
                        }
                        if (trendType == 'dayHour') {
                            //rowParameters = rowParameters.replace('[', '');
                            //rowParameters = rowParameters.replace(']', '');
                            //var row = '[';
                            //for (var i = 0; i < 7; i++) {
                            //    if (i < 6) {
                            //        row += rowParameters + ', ';
                            //    } else {
                            //        row += rowParameters + ']';
                            //    }
                            //}
                            //rowParameters = row;
                            for (var k = 1; k < 7; k++) {
                                rowParameters.push(rowParameters[k - 1]);
                            }
                        }
                    }
                    //if(event.t1 != null && event.t2 != null){
                    if((event.t1 != null && event.t2 != null)&&((event.event !== "reset zoom"))){      //zoom nel caso dynamic, si popola il widget solo con i valori interni alla finestra temporale
                        var oldRowParam = JSON.parse(localStorage.getItem(widgetName))
                        if(oldRowParam[0].metricHighLevelType == "Dynamic"){
                            var newRowParam = [];
                            for(var p in oldRowParam){
                                newRowParam[p] = {};
                                newRowParam[p].metricName = oldRowParam[p].metricName;
                                newRowParam[p].metricHighLevelType = oldRowParam[p].metricHighLevelType;
                                newRowParam[p].metricId = oldRowParam[p].metricId;
                                newRowParam[p].metricType = oldRowParam[p].metricType;
                                newRowParam[p].metricValueUnit = oldRowParam[p].metricValueUnit;
                                newRowParam[p].smField = oldRowParam[p].smField;
                                newRowParam[p].values = [];
                                for(var el in oldRowParam[p].values){
                                    var firstDate = new Date(event.t1)
                                    var secondDate = new Date(event.t2)
                                    var lower = firstDate.getTime()
                                    var upper = secondDate.getTime()
                                    if(oldRowParam[p].values[el][0] >= lower && oldRowParam[p].values[el][0] <= upper) 
                                    newRowParam[p].values.push(oldRowParam[p].values[el]);
                                }
                            }
                            event.t1 = null;
                            event.t2 = null;
                            rowParameters = newRowParam;
                        }
                                
                        if(localStorage.getItem("passedData") == null || localStorage.getItem("passedData") == "[object Object]"){
                            var init = [];
                            var firstEl = {};
                            firstEl.passedData = JSON.parse(localStorage.getItem(widgetName));
                            firstEl.name = widgetName;
                            firstEl.t1 = event.t1;
                            firstEl.t2 = event.t2;
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
                            newEl.passedData = JSON.parse(localStorage.getItem(widgetName));
                            newEl.name = widgetName;
                            newEl.t1 = event.t1;
                            newEl.t2 = event.t2;
                            if (localStorage.getItem("events") == null) {
                                newEl.eventIndex = 0;
                            } else {
                                newEl.eventIndex = JSON.parse(localStorage.getItem("events")).length - 1;
                            }
                            var oldElement = JSON.parse(localStorage.getItem("passedData"));
                            oldElement.push(newEl);
                            localStorage.setItem("passedData", JSON.stringify(oldElement));
                        }
                        //if (event.event !== "reset zoom"){
                        populateWidget(true, timeRange, null, timeNavCount, null, event.t1, event.t2);
                        //}
                    }
                    else{
                        if(localStorage.getItem("passedData") == null){
                            var init = [];
                            var firstEl = {};
                            firstEl.passedData = event.passedData;
                            firstEl.name = widgetName;
                            if (localStorage.getItem("events") == null) {
                                firstEl.eventIndex = 0;
                            } else {
                                firstEl.eventIndex = JSON.parse(localStorage.getItem("events")).length;
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
                                newEl.eventIndex = JSON.parse(localStorage.getItem("events")).length;
                            }
                            var oldElement = JSON.parse(localStorage.getItem("passedData"));
                            oldElement.push(newEl);
                            localStorage.setItem("passedData", JSON.stringify(oldElement));
                        }
                        if (event.event !== "reset zoom") {
                            populateWidget(true, timeRange, null, timeNavCount, null, event.t1, event.t2);
                        } else {
                            populateWidget(true, timeRange, null, timeNavCount, null, null, null);
                        }
                    }

                    //    timeRange = widgetData.params.temporal_range_w;
                    
                }
                else
                {
                    populateWidget(false, null, null, timeNavCount, null, event.t1, event.t2);
                }

            }
        });
	
	$(document).off('reloadPreviousContent_' + widgetName);
        $(document).on('reloadPreviousContent_' + widgetName, function(event){
            var passedData = JSON.parse(localStorage.getItem("passedData"));
            var j = 0;
            var t = -1;
            console.log(passedData, event);
            console.log(JSON.parse(localStorage.getItem("passedData")));
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
                populateWidget(true, timeRange, null, timeNavCount, null, passedData[t].t1, passedData[t].t2);
            }
        });	
		$('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker({
            showTodayButton: true,
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            },
            sideBySide: true
        })
        
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

        function getDayOfWeek(date) {
            const dayOfWeek = new Date(date).getDay();
            return isNaN(dayOfWeek) ? null :
                ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][dayOfWeek];
        }
        
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
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                }

            }

	    }

        function drawDiagram(timeDomain, xAxisFormat, yAxisFormat, subtitle)
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
                if (trendType == 'monthWeek') {
                    xAxisType = 'category';
                }
                xAxisCategories = null;
            } else
            {
                xAxisType = null;
            }
            //if (typicaltrend == 'Yes') {
            //var subtitle = 'Prova Sottotitolo';
            //    var subtitle = TTTDate; 
            //    subtitle = subtitle.replace(/&from=/," ");
            //    subtitle = subtitle.replace(/&to=/," --> ");
            //    subtitle = subtitle + '(' + computationType + ')';
            //    subtitle = subtitle.replace(/&computationType=/," ");
            //} else {
            //    var subtitle = '';
            //}

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

            if (chartSeriesObject != null) {
            //    if (chartSeriesObject[0].data.length > 0) {
                if (areaOpacity == null) {
                    if (styleParameters.areaChartOpacityM) {
                        areaOpacity = styleParameters.areaChartOpacityM;
                    } else {
                        areaOpacity = 0.75;
                    }
                }

                if (styleParameters.secondaryYAxisM && styleParameters.secondaryYAxisM == 'yes') {
                    // SECONDARY Y-AXIS
                    var secondaryAxisLabel = null;
                    if (styleParameters.secondaryYAxisLab) {
                        secondaryAxisLabel = styleParameters.secondaryYAxisLab;
                    } else {
                        secondaryAxisLabel = "";
                    }
                    if  (styleParameters.secondaryYAxisMin) {
                        secondaryYAxisMin = styleParameters.secondaryYAxisMin;
                    }
                    if  (styleParameters.secondaryYAxisMax) {
                        secondaryYAxisMax = styleParameters.secondaryYAxisMax;
                    }
                    if  (styleParameters.yAxisMin) {
                        yAxisMin = styleParameters.yAxisMin;
                    }
                    if  (styleParameters.yAxisMax) {
                        yAxisMax = styleParameters.yAxisMax;
                    }
                    chart = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                        chart: {
                            zoomType: 'x',
                            type: highchartsChartType,
                            backgroundColor: 'transparent',
                            //Funzione di applicazione delle soglie
                            events: {
                                load: onDraw,
                                selection: function (event) {
                                    if (event.xAxis && styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                        minX = event.xAxis[0].min;
                                        maxX = event.xAxis[0].max;
                                        //alert("Min: " + minX + ";\nMax: " + maxX + ";\nsURI: " + rowParameters + ";\nmetric name: " + this.series[0].name);
                                        var data_list = this.series[0].processedXData;
                                        var data_list_n = data_list.length;
                                        var min_pos =0;
                                        var max_pos =data_list_n;
                                        for(var i =0; i< data_list_n-1; i++){
                                            if ((minX > data_list[i])&&(minX < data_list[i+1])){
                                                min_pos = i+1;
                                            }

                                        }
                                        for (var i =data_list_n; i> 0; i--){
                                            if ((maxX < data_list[i])&&(maxX > data_list[i-1])){
                                                max_pos = i-1;
                                            }

                                        }

                                        var param1 = "Min: " + this.series[0].processedYData[min_pos] + "<br>Max: " + this.series[0].processedYData[max_pos];
                                        // var sUri = getServiceUri(rowParameters);
                                        var param = {
                                            "event" : "zoom",
                                            "t1" : minX,
                                            "t2" : maxX,
                                            "series": rowParameters,
                                        //    "metricName": this.series[0].name
                                        }
                                        if(styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code){
                                            let j=1;
                                            if(localStorage.getItem("events") == null){

                                                var events = [];
                                                events.push("CurvedLinesZoom1");
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }
                                            else{
                                                var events = JSON.parse(localStorage.getItem("events"));
                                                for(var e in events){
                                                    if(events[e].slice(0,15) == "CurvedLinesZoom")
                                                        j = j+1;
                                                }
                                                events.push("CurvedLinesZoom" + j);
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }

                                            let newId = "CurvedLinesZoom"+j;
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

                                            try {
                                                execute_<?= $_REQUEST['name_w'] ?>(param);
                                            } catch(e) {
                                                console.log("Error in JS function from time zoom on " + widgetName);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        time: {
                            useUTC: utcOption
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
                            text: subtitle
                        },

                        xAxis: {
                            type: xAxisType,
                            uniqueNames: false,
                            //type: 'datetime',
                            //tickAmount: 24,
                            //tickInterval: 3600 * 1000,
                            //minTickInterval: 3600 * 1000,
                            //lineWidth: 1,
                            //dateTimeLabelFormats: {
                            //    day: '%H:%M'
                            //},
                            //type: 'datetime',
                            //    units: unitsWidget,
                            gridLineWidth: 0,
                            lineColor: chartAxesColor,
                            categories: xAxisCategories,
                            title: {
                                align: 'high',
                                offset: 20,
                                text: xAxisTitle,
                                rotation: 0,
                                //y: 5,		// GP Questo non era commentato prima di TTT2
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
                            },
                            events: {
                                setExtremes: function(event) {
                                    if (!event.min && !event.max) {
                                        var param = {
                                            "event": "reset zoom",
                                            //"t1" : event.target.dataMin,
                                            //"t2" : event.target.dataMax,
                                            "t1": null,
                                            "t2": null,
                                            "series":rowParameters
                                        }
                                        //
                                        try {
                                            execute_<?= $_REQUEST['name_w'] ?>(param);
                                        } catch(e) {
                                            console.log("Error in JS function from time zoom on " + widgetName);
                                        }
                                        //
                                    }
                                }
                            }
                        },
                        yAxis: [{
                            min: yAxisMin,
                            max: yAxisMax,
                            type: yAxisType,
                            lineWidth: 1,
                            lineColor: chartAxesColor,
                            gridLineWidth: 1,
                            gridLineColor: gridLineColor,
                            gridZIndex: 0,
                            title: {
                                //text: null
                                text: yAxisText,
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
                        {
                            min: secondaryYAxisMin,
                            max: secondaryYAxisMax,
                            type: yAxisType,
                            lineWidth: 1,
                            lineColor: chartAxesColor,
                            gridLineWidth: 1,
                            gridLineColor: gridLineColor,
                            gridZIndex: 0,
                            title: {
                                //text: null
                                text: secondaryAxisLabel,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            },
                            opposite: true,
                        }],
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
                            //pointFormatter: function () {
                            formatter: function () {
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
                                                            //min = target.fields[i].thrSeries[j].min;
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
                                    if (trendType == 'monthWeek') {
                                        var arraydays = ['Monday of 1st Week', 'Tuesday of 1st Week', 'Wednesday of 1st Week', 'Thursday of 1st Week', 'Friday of 1st Week', 'Saturday of 1st Week', 'Sunday of 1st Week', 'Monday of 2nd Week', 'Tuesday of 2nd Week', 'Wednesday of 2nd Week', 'Thursday of 2nd Week', 'Friday of 2nd Week', 'Saturday of 2nd Week', 'Sunday of 2nd Week', 'Monday of 3rd Week', 'Tuesday of 3rd Week', 'Wednesday of 3rd Week', 'Thursday of 3rd Week', 'Friday of 3rd Week', 'Saturday of 3rd Week', 'Sunday of 3rd Week', 'Monday of 4th Week', 'Tuesday of 4th Week', 'Wednesday of 4th Week', 'Thursday of 4th Week', 'Friday of 4th Week', 'Saturday of 4th Week', 'Sunday of 4th Week'];
                                        dateLine = arraydays[this.x];

                                    } else {
                                        if (trendType == 'dayHour' && dayhourview == 'dayview') {
                                            //dateLine='';
                                            dateLine = new Date(this.x).toString().substring(4, 31);
                                        } else {
                                            dateLine = '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>';
                                        }
                                    }
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
                            series: {
                                fillOpacity: areaOpacity,
                                connectNulls: true,
                                groupPadding: 0.1,
                                pointPadding: 0,
                                stacking: stackingOption,
                                states: {
                                    hover: {
                                        enabled: false
                                    },
                                    inactive: {
                                        lineWidth: 1
                                    }
                                },
                                point: {
                                    events: {
                                        mouseOver: function(jqEvent){
                                            if(styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code !== null) {
                                                if (this.graphic) {
                                                    this.graphic.element.style.cursor = 'pointer';
                                                }
                                            }
                                        },
                                        click: function () {
                                            selectedX = this.category;
                                            //alert('Category: ' + this.category + ', value: ' + this.y);
                                            //lettura code//
                                            var param1 = this.y;
                                            // var sUri = getServiceUri(rowParameters);
                                            // var param = new Array(minX, maxX, sUri, this.series[0].name);
                                            var param = {
                                                "event" : "click",
                                                "t1" : this.x,
                                                "t2" : this.y,
                                                "series": rowParameters,
                                                //    "metricName": this.series.name
                                            }
                                            if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                                try {
                                                    execute_<?= $_REQUEST['name_w'] ?>(param);
                                                } catch(e) {
                                                    console.log("Error in JS function from time zoom on " + widgetName);
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            spline: {
                                events: {
                                    legendItemClick: function(){
                                        var len_rowParameters= rowParameters.length;
                                        var leg = this.chart.legend.allItems;
                                        var currname = this.name;
                                        ////////////////
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
                                            events.push("CurvedLineLegendClick1");
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }
                                        else{
                                            var events = JSON.parse(localStorage.getItem("events"));
                                            for(var e in events){
                                                //console.log(events[e]);
                                                if(events[e].slice(0,14) == "CurvedLineLegendClick")
                                                    j = j+1;
                                            }
                                            events.push("CurvedLineLegendClick" + j);
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }
                                        let newId = "CurvedLineLegendClick"+j;
                                        $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                        $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                                        $('#'+newId).on( "click", function() {
                                        /*    let eventIndex = JSON.parse(localStorage.events).indexOf(newId);
                                            var selectedDataJson = JSON.stringify(JSON.parse(localStorage.passedData)[eventIndex]);
                                            execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson); */
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
									    selectedDataJson = JSON.stringify(selectedData);
                                    
										if (styleParameters.enableCKEditor == "ckeditor" && code) {
                                            try {
                                                execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson);
                                            } catch(e) {
                                                console.log("Error in JS function from click on " + widgetName);
                                            }
                                        }
									
									}//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
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
                    // NO SECONDARY Y-AXIS
                    if  (styleParameters.yAxisMin) {
                        yAxisMin = styleParameters.yAxisMin;
                    }
                    if  (styleParameters.yAxisMax) {
                        yAxisMax = styleParameters.yAxisMax;
                    }
                    chart = Highcharts.chart('<?= $_REQUEST['name_w'] ?>_chartContainer', {
                        chart: {
                            zoomType: 'x',
                            type: highchartsChartType,
                            backgroundColor: 'transparent',
                            //Funzione di applicazione delle soglie
                            events: {
                                load: onDraw,
                                selection: function (event) {
                                    if (event.xAxis && styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                        minX = event.xAxis[0].min;
                                        maxX = event.xAxis[0].max;
                                        //alert("Min: " + minX + ";\nMax: " + maxX + ";\nsURI: " + rowParameters + ";\nmetric name: " + this.series[0].name);
                                        var data_list = this.series[0].processedXData;
                                        var data_list_n = data_list.length;
                                        var min_pos =0;
                                        var max_pos =data_list_n;
                                        for(var i =0; i< data_list_n-1; i++){
                                            if ((minX > data_list[i])&&(minX < data_list[i+1])){
                                                min_pos = i+1;
                                            }

                                        }
                                        for (var i =data_list_n; i> 0; i--){
                                            if ((maxX < data_list[i])&&(maxX > data_list[i-1])){
                                                max_pos = i-1;
                                            }

                                        }

                                        var param1 = "Min: " + this.series[0].processedYData[min_pos] + "<br>Max: " + this.series[0].processedYData[max_pos];
                                        // var sUri = getServiceUri(rowParameters);
                                        var param = {
                                            "t1" : minX,
                                            "t2" : maxX,
                                            "series": rowParameters,
                                        //    "metricName": this.series[0].name
                                        }
                                        if(styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code){
                                            let j=1;
                                            if(localStorage.getItem("events") == null){

                                                var events = [];
                                                events.push("CurvedLinesZoom1");
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }
                                            else{
                                                var events = JSON.parse(localStorage.getItem("events"));
                                                for(var e in events){
                                                    if(events[e].slice(0,15) == "CurvedLinesZoom")
                                                        j = j+1;
                                                }
                                                events.push("CurvedLinesZoom" + j);
                                                localStorage.setItem("events", JSON.stringify(events));
                                            }
                                            
                                            let newId = "CurvedLinesZoom"+j;
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

                                            try {
                                            	execute_<?= $_REQUEST['name_w'] ?>(param);
                                            } catch(e) {
                                            	console.log("Error in JS function from time zoom on " + widgetName);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        time: {
                            useUTC: utcOption
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
                            text: subtitle
                        },

                        xAxis: {
                            type: xAxisType,
                            uniqueNames: false,
                            //type: 'datetime',
                            //tickAmount: 24,
                            //tickInterval: 3600 * 1000,
                            //minTickInterval: 3600 * 1000,
                            //lineWidth: 1,
                            //dateTimeLabelFormats: {
                            //    day: '%H:%M'
                            //},
                            //type: 'datetime',
                            //    units: unitsWidget,
                            gridLineWidth: 0,
                            lineColor: chartAxesColor,
                            categories: xAxisCategories,
                            title: {
                                align: 'high',
                                offset: 20,
                                text: xAxisTitle,
                                rotation: 0,
                                //y: 5,		// GP Questo non era commentato prima di TTT2
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
                            },
							events: {
									setExtremes: function(event) {
									  if (!event.min && !event.max) {
										var param = {
											"event": "reset zoom",
                                            //"t1" : event.target.dataMin,
                                            //"t2" : event.target.dataMax,
                                            "t1": null,
                                            "t2": null,
											"series":rowParameters
                                        }
										//
										try {
                                            	execute_<?= $_REQUEST['name_w'] ?>(param);
                                            } catch(e) {
                                            	console.log("Error in JS function from time zoom on " + widgetName);
                                            }
										//
									  }
									}
								  }
                        },
                        yAxis: {
                            min: yAxisMin,
                            max: yAxisMax,
                            type: yAxisType,
                            lineWidth: 1,
                            lineColor: chartAxesColor,
                            gridLineWidth: 1,
                            gridLineColor: gridLineColor,
                            gridZIndex: 0,
                            title: {
                                //text: null
                                text: yAxisText,
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
                            //pointFormatter: function () {
                            formatter: function () {
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
                                                            //min = target.fields[i].thrSeries[j].min;
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
                                    if (trendType == 'monthWeek') {
                                        var arraydays = ['Monday of 1st Week', 'Tuesday of 1st Week', 'Wednesday of 1st Week', 'Thursday of 1st Week', 'Friday of 1st Week', 'Saturday of 1st Week', 'Sunday of 1st Week', 'Monday of 2nd Week', 'Tuesday of 2nd Week', 'Wednesday of 2nd Week', 'Thursday of 2nd Week', 'Friday of 2nd Week', 'Saturday of 2nd Week', 'Sunday of 2nd Week', 'Monday of 3rd Week', 'Tuesday of 3rd Week', 'Wednesday of 3rd Week', 'Thursday of 3rd Week', 'Friday of 3rd Week', 'Saturday of 3rd Week', 'Sunday of 3rd Week', 'Monday of 4th Week', 'Tuesday of 4th Week', 'Wednesday of 4th Week', 'Thursday of 4th Week', 'Friday of 4th Week', 'Saturday of 4th Week', 'Sunday of 4th Week'];
                                        dateLine = arraydays[this.x];

                                    } else {
                                        if (trendType == 'dayHour' && dayhourview == 'dayview') {
                                            //dateLine='';
                                            dateLine = new Date(this.x).toString().substring(4, 31);
                                        } else {
                                            dateLine = '<span style="color:' + this.color + '">\u25CF</span><b> ' + new Date(this.x).toString().substring(0, 31) + '</b><br/>';
                                        }
                                    }
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
                            series: {
                                fillOpacity: areaOpacity,
                                connectNulls: true,
                                groupPadding: 0.1,
                                pointPadding: 0,
                                stacking: stackingOption,
                                states: {
                                    hover: {
                                        enabled: false
                                    },
                                    inactive: {
                                        lineWidth: 1
                                    }
                                },
								point: {
                                    events: {
                                        mouseOver: function(jqEvent){
                                            if(styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code !== null) {
                                                if (this.graphic) {
                                                    this.graphic.element.style.cursor = 'pointer';
                                                }
                                            }
                                        },
                                        click: function () {
                                            selectedX = this.category;
                                            //alert('Category: ' + this.category + ', value: ' + this.y);
											//lettura code//
											var param1 = this.y;
                                            // var sUri = getServiceUri(rowParameters);
                                            // var param = new Array(minX, maxX, sUri, this.series[0].name);
                                            var param = {
                                                "event": "click",
                                                "t1" : this.x,
                                                "t2" : this.y,
                                                "series": rowParameters,
                                            //    "metricName": this.series.name
                                            }
                                            if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                                try {
                                                    execute_<?= $_REQUEST['name_w'] ?>(param);
                                                } catch(e) {
                                                    console.log("Error in JS function from click on " + widgetName);
                                                }
                                            }
                                        }
                                    }
                                }
								
                            },
                            spline: {
                                events: {
                                    legendItemClick: function(){
                                        var len_rowParameters= rowParameters.length;
                                        var leg = this.chart.legend.allItems;
                                        var currname = this.name;
                                        ////////////////
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
                                            events.push("CurvedLineLegendClick1");
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }
                                        else{
                                            var events = JSON.parse(localStorage.getItem("events"));
                                            for(var e in events){
                                                //console.log(events[e]);
                                                if(events[e].slice(0,21) == "CurvedLineLegendClick")
                                                    j = j+1;
                                            }
                                            events.push("CurvedLineLegendClick" + j);
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }
                                        let newId = "CurvedLineLegendClick"+j;
                                        $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                        $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                                        $('#'+newId).on( "click", function() {
                                        /*    let eventIndex = JSON.parse(localStorage.events).indexOf(newId);
                                            var selectedDataJson = JSON.stringify(JSON.parse(localStorage.passedData)[eventIndex]);
                                            execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson); */
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
                                        selectedDataJson = JSON.stringify(selectedData);

                                        if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code) {
                                            try {
                                                execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson);
                                            } catch(e) {
                                                console.log("Error in JS function from click on " + widgetName);
                                            }
                                        }
									
									}
									//Per ora disabilitiamo la funzione show/hide perché interferisce con gli handler dei tasti info
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
                }

            /*        if (styleParameters.secondaryYAxisM && styleParameters.secondaryYAxisM == 'yes') {
                        var secondaryAxisLabel = null;
                        if (styleParameters.secondaryYAxisLab) {
                            secondaryAxisLabel = styleParameters.secondaryYAxisLab;
                        } else {
                            secondaryAxisLabel = "";
                        }
                        chart.addAxis({
                            id: 2,
                            type: yAxisType,
                            lineWidth: 1,
                            lineColor: chartAxesColor,
                            gridLineWidth: 1,
                            gridLineColor: gridLineColor,
                            gridZIndex: 0,
                            title: {
                                //text: null
                                text: secondaryAxisLabel,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.colsLabelsFontSize + "px",
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    color: styleParameters.colsLabelsFontColor,
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.25)"
                                }
                            },
                            opposite: true,
                        }, false);
                    }   */

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

        function getChoiceTimeNavCount(dateChoice, timeRange) {
            let hours = 0;
            switch(timeRange) {
                case "10 Anni":
                    hours = 10*365*24;
                    break;

                case "2 Anni":
                    hours = 2*365*24;
                    break;

                case "Annuale":
                    hours = 365*24;
                    break;

                case "Semestrale":
                    hours = 180*24;
                    break;

                case "Mensile":
                    hours = 30*24;
                    break;

                case "Settimanale":
                    hours = 7*24;
                    break;

                case "Giornaliera":
                    hours = 24;
                    break;

                case "12 Ore":
                    hours = 12;
                    break;

                case "4 Ore":
                    hours = 4;
                    break;
            }
            return Math.floor((moment.duration(moment().diff(dateChoice)).asHours())/hours);
        }

        function getUpperTimeLimit(timeRange, timeCount) {
            let hours = 0;
            switch(timeRange) {
                case "10 Anni":
                    hours = 10*365*24*timeCount;
                    break;

                case "2 Anni":
                    hours = 2*365*24*timeCount;
                    break;

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
            let now = new Date();
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
                                if (singleOriginalData[field] != null) {
                                    if (singleOriginalData[field].hasOwnProperty("valueDate")) {
                                        futureDate = singleOriginalData[field].valueDate.replace("T", " ");
                                        var plusIndexFuture = futureDate.indexOf("+");
                                        futureDate = futureDate.substr(0, plusIndexFuture);
                                        var momentDateTimeFuture = moment(futureDate);
                                        var localDateTimeFuture = momentDateTimeFuture.tz(localTimeZone).format();
                                        localDateTimeFuture = localDateTimeFuture.replace("T", " ");
                                        var plusIndexLocalFuture = localDateTimeFuture.indexOf("+");
                                        localDateTimeFuture = localDateTimeFuture.substr(0, plusIndexLocalFuture);
                                    }
                                }
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
        
        function buildSeriesFromAggregationData(timeRange)
        {
            var roundedVal, singleSeriesData, singleSample, sampleTime, seriesSingleObj = null;
            chartSeriesObject = [];
            counterday = 1;
            for (var i = 0; i < aggregationGetData.length; i++)
            {
                singleSeriesData = [];

            //    if (styleParameters.secondaryYAxisM && styleParameters.secondaryYAxisM == 'yes') {
                if (styleParameters.secondaryYAxisM != null && styleParameters.secondaryYAxisM == 'yes' && (rowParameters != null && rowParameters[i] != null && rowParameters[i].secYAx != null && rowParameters[i].secYAx == "secondary")) {
                    idYAxis = 1;
                } else {
                    idYAxis = 0;
                }
                
                 switch(aggregationGetData[i].metricHighLevelType)
                 {
                    case "KPI":
                        utcOption = true;
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
                            yAxis: idYAxis,
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

                    case "Dynamic":
                      //   utcOption = false;
                         let extractedData = {};
                         if (timeNavCount != 0) {
                             extractedData.values = aggregationGetData[i].data;
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
                                objName = aggregationGetData[i].metricName;
                            }
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName;
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
                             case "2 Anni":
                                 millisToSubtract = 2 * 365 * 24 * 60 * 60 * 1000;
                                 break;
                             case "10 Anni":
                                 millisToSubtract = 10 * 365 * 24 * 60 * 60 * 1000;
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
                                 color: styleParameters.barsColors[i],
                                 yAxis: idYAxis,
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

                         chartSeriesObject.push(seriesSingleObj);

                     //    }

                         break;

                    case "MyKPI":
                        utcOption = false;
                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);

                        var resultsArray = smPayload;

                        var objName = null;
                    /*    if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName + " - " + smField;
                        }

                        for(var j = 0; j < resultsArray.length; j++)
                        {
                            newVal = resultsArray[j].value;
                            addSampleToTrend = true;
                        //    newTime = resultsArray[j].insertTime;
                            newTime = resultsArray[j].dataTime;
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

                        if (stackingOption == "normal") {
                            singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                        }

                        if (singleSeriesData.length > 0) {
                            seriesSingleObj = {
                                showInLegend: true,
                                name: objName,
                                data: singleSeriesData,
                                color: styleParameters.barsColors[i],
                                yAxis: idYAxis,
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
                        }

                        break;

                    case "IoT Device Variable":
                    case "Data Table Variable":
                    case "Mobile Device Variable":
                    case "Sensor":
                        utcOption = false;
                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);
                        chartSeriesObject.valueUnit = "";

                    /*    var objName = null;
                        if (editLabels != null) {
                            if (editLabels.length > 0) {
                                objName = editLabels[i];
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName + " - " + smField;
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

                            if (stackingOption == "normal") {
                                singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                            }

                            seriesSingleObj = {
                                showInLegend: true,
                            //    name: aggregationGetData[i].metricName,
                                name: objName,
                                data: singleSeriesData,
                                color: styleParameters.barsColors[i],
                                yAxis: idYAxis,
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
                                        newVal = null;
                                        if (resultsArray[j][smField] != null) {
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

                                        if (newVal != null) {
                                            if ((newVal.trim() !== '') && (addSampleToTrend)) {
                                                roundedVal = parseFloat(newVal);
                                                roundedVal = Number(roundedVal.toFixed(2));
                                                //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                                sampleTime = parseInt(new Date(newTime).getTime());
                                                singleSample = [sampleTime, roundedVal];
                                                singleSeriesData.push(singleSample);
                                            }
                                        }
                                    }

                                    if (stackingOption == "normal") {
                                        singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                                    }

                                    seriesSingleObj = {
                                        showInLegend: true,
                                        name: objName,
                                        data: singleSeriesData,
                                        color: styleParameters.barsColors[i],
                                        yAxis: idYAxis,
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

                            } else {
                                currWeekDay = getDayOfWeek(trendDate);
                                if (smPayload.length > 0) {
                                    //for (var x = 0; x < smPayload.length; x++)
                                    for (var x = 0; x < 1; x++)
                                    {
                                        if (smPayload[x].hasOwnProperty('trendType')) {
                                            if (smPayload[x].hasOwnProperty('typicalDays')) {
                                                if (smPayload[x].typicalDays.hasOwnProperty('Monday')) {
                                                    if (dayhourview == 'weekview'){
                                                        var newTime, newVal = null;
                                                        newTime = new Date(trendDate);
                                                        switch (counterday)
                                                        {
                                                            case 1:
                                                                var resultsArray = smPayload[x].typicalDays.Monday;
                                                                objName = 'Monday';
                                                                counterday += 1;
                                                                var newDateTime = new Date(newTime);
                                                                var day = newDateTime.getDay() || 7;
                                                                if (day !== 1)
                                                                    newDateTime.setHours(-24 * (day - 1));
                                                                break;
                                                            case 2:
                                                                var resultsArray = smPayload[x].typicalDays.Tuesday;
                                                                objName = 'Tuesday';
                                                                counterday += 1;
                                                                break;
                                                            case 3:
                                                                var resultsArray = smPayload[x].typicalDays.Wednesday;
                                                                objName = 'Wednesday';
                                                                counterday += 1;
                                                                break;
                                                            case 4:
                                                                var resultsArray = smPayload[x].typicalDays.Thursday;
                                                                objName = 'Thursday';
                                                                counterday += 1;
                                                                break;
                                                            case 5:
                                                                var resultsArray = smPayload[x].typicalDays.Friday;
                                                                objName = 'Friday';
                                                                counterday += 1;
                                                                break;
                                                            case 6:
                                                                var resultsArray = smPayload[x].typicalDays.Saturday;
                                                                objName = 'Saturday';
                                                                counterday += 1;
                                                                break;
                                                            case 7:
                                                                var resultsArray = smPayload[x].typicalDays.Sunday;
                                                                objName = 'Sunday';
                                                                counterday += 1;
                                                                break;
                                                            default:
                                                                var resultsArray = smPayload[x].typicalDays.Monday;
                                                                counterday += 1;
                                                                break;
                                                        }

                                                        if (objName == currWeekDay) {
                                                            objName = objName + " *";
                                                        }

                                                        //newTime.setDate(newDateTime.getDate() + (counterday-2));
                                                        //newTime = new Date(newTime).toISOString();
                                                        var newTime3 = new Date(newDateTime);
                                                        newTime3.setDate(newDateTime.getDate() + (counterday-2));
                                                        newTime = new Date(newTime3.getTime()-(newTime3.getTimezoneOffset() * 60000)).toISOString();
                                                        
                                                        var kk = 0;
                                                        for (var j = 0; j < resultsArray.length; j++) {
                                                            newVal = resultsArray[j];
                                                            if (kk < 10) {
                                                                newTime = newTime.substring(0, isoDate.length - 14) + 'T0' + kk + ':00:00';
                                                                //newTime = newTime.setDate(newTime.getDate() + (counterday-1)) + 'T0' + kk + ':00:00'
                                                            } else {
                                                                newTime = newTime.substring(0, isoDate.length - 14) + 'T' + kk + ':00:00';
                                                            }
                                                            kk++;
                                                            roundedVal = parseFloat(newVal);
                                                            roundedVal = Number(roundedVal.toFixed(2));
                                                            sampleTime = parseInt(new Date(newTime).getTime());
                                                            //sampleTime = newTime;
                                                            singleSample = [sampleTime, roundedVal];
                                                            singleSeriesData.push(singleSample);
                                                        }
                                                    }else{
                                                        switch (counterday)
                                                        {
                                                            case 1:
                                                                var resultsArray = smPayload[x].typicalDays.Monday;
                                                                objName = 'Monday';
                                                                counterday += 1;
                                                                break;
                                                            case 2:
                                                                var resultsArray = smPayload[x].typicalDays.Tuesday;
                                                                objName = 'Tuesday';
                                                                counterday += 1;
                                                                break;
                                                            case 3:
                                                                var resultsArray = smPayload[x].typicalDays.Wednesday;
                                                                objName = 'Wednesday';
                                                                counterday += 1;
                                                                break;
                                                            case 4:
                                                                var resultsArray = smPayload[x].typicalDays.Thursday;
                                                                objName = 'Thursday';
                                                                counterday += 1;
                                                                break;
                                                            case 5:
                                                                var resultsArray = smPayload[x].typicalDays.Friday;
                                                                objName = 'Friday';
                                                                counterday += 1;
                                                                break;
                                                            case 6:
                                                                var resultsArray = smPayload[x].typicalDays.Saturday;
                                                                objName = 'Saturday';
                                                                counterday += 1;
                                                                break;
                                                            case 7:
                                                                var resultsArray = smPayload[x].typicalDays.Sunday;
                                                                objName = 'Sunday';
                                                                counterday += 1;
                                                                break;
                                                            default:
                                                                var resultsArray = smPayload[x].typicalDays.Monday;
                                                                counterday += 1;
                                                                break;
                                                        }

                                                        if (objName == currWeekDay) {
                                                            objName = objName + " *";
                                                        }

                                                        var newVal, newTime = null;
                                                        var kk = 0;
                                                        for (var j = 0; j < resultsArray.length; j++) {
                                                            newVal = resultsArray[j];
                                                            if (kk < 10) {
                                                                newTime = trendDate + 'T0' + kk + ':00:00'
                                                            } else {
                                                                newTime = trendDate + 'T' + kk + ':00:00'
                                                            }
                                                            kk++;
                                                            roundedVal = parseFloat(newVal);
                                                            roundedVal = Number(roundedVal.toFixed(2));
                                                            sampleTime = parseInt(new Date(newTime).getTime());
                                                            //sampleTime = newTime;
                                                            singleSample = [sampleTime, roundedVal];
                                                            singleSeriesData.push(singleSample);
                                                        }
                                                    }
                                                }
                                            } else {
                                                if (smPayload[x].hasOwnProperty('typicalMonthD')) {
                                                    var resultsArray = smPayload[x].typicalMonthD;
                                                    var newVal, newTime = null;
                                                    var kk = 1;
                                                    for (var j = 0; j < resultsArray.length; j++) {
                                                        newVal = resultsArray[j];
                                                        trendDate = new Date(trendDate);
                                                        var newTime = new Date(trendDate.getFullYear(), trendDate.getMonth(), kk);
                                                        //var newTime = new Date(now).toISOString();
                                                        //newTime = newTime.substring(0, newTime.length - 5);
                                                        kk++;

                                                        roundedVal = parseFloat(newVal);
                                                        roundedVal = Number(roundedVal.toFixed(2));
                                                        sampleTime = parseInt(new Date(newTime).getTime());
                                                        singleSample = [sampleTime, roundedVal];
                                                        singleSeriesData.push(singleSample);
                                                    }
                                                } else {
                                                    var resultsArray = smPayload[x].typicalMonthWeek;
                                                    var newVal, newTime = null;
                                                    for (var j = 0; j < resultsArray.length; j++) {
                                                        newVal = resultsArray[j];
                                                        roundedVal = parseFloat(newVal);
                                                        roundedVal = Number(roundedVal.toFixed(2));
                                                        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                        if (j < 7) {
                                                            sampleTime = days[j];
                                                        } else {
                                                            sampleTime = days[j - (7 * Math.floor(j / 7))];
                                                        }
                                                        singleSample = [sampleTime, roundedVal];
                                                        singleSeriesData.push(singleSample);
                                                    }
                                                }

                                            }
                                        }
                                    }
                                    seriesSingleObj = {
                                        showInLegend: true,
                                        name: objName,
                                        data: singleSeriesData,
                                        color: styleParameters.barsColors[i],
                                        yAxis: idYAxis,
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
                            if (smPayload.Service.features[0].properties.realtimeAttributes[smField] != null) {
                                if (smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                    chartSeriesObject.valueUnit = smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit;
                                }
                            }
                        } else if (smPayload.Sensor != null) {
                            if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField] != null) {
                                if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                    chartSeriesObject.valueUnit = smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit;
                                }
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
            for (var i = 0; i < chartSeriesObject.length; i++) {
                chartSeriesObject[i].data = chartSeriesObject[i].data.sort((a, b) => a[0] - b[0])
            }
            return null; 
        }
        
        function populateWidget(fromAggregate, localTimeRange, timeNavDirection, timeCount, dateInFuture, t1, t2)
        {
			//console.log('fromAggregate: '+fromAggregate);
			//console.log('localTimeRange:'+localTimeRange);
			//console.log('timeCount: '+timeCount);
			var limit_data = "";
			//console.log(dateChoice);
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
            errorsLog = null;

            if (fromAggregate) {
                setupLoadingPanel(widgetName, widgetContentColor, firstLoad);

                aggregationGetData = [];
                getDataFinishCount = 0;
                if (rowParameters != undefined) {
                    if (rowParameters.length == null) {
                        rowParamLength = 0;
                    } else {
                        rowParamLength = rowParameters.length;
                    }
                }
                if(t1 != null && t2 != null && JSON.parse(localStorage.getItem(widgetName))[0].metricHighLevelType != "Dynamic"){
                    rowParameters = JSON.parse(localStorage.getItem(widgetName));
                    
                    if(rowParameters.length == undefined)   
                        rowParamLength = 1;
                    else 
                        rowParamLength = rowParameters.length;
                    for(var i = 0; i < rowParamLength; i++){
                        aggregationGetData[i] = false;
                    }
                    let hour1 = parseInt(t1.slice(11, 13)) + 1;
                    let hour2 = parseInt(t2.slice(11, 13)) + 1;
                    if (hour1 > 9) {
                        t1 = t1.slice(0, 11) + hour1.toString() + t1.slice(13, 19);
                    } else {
                        t1 = t1.slice(0, 12) + hour1.toString() + t1.slice(13, 19);
                    }
                    if (hour2 > 9) {
                        t2 = t2.slice(0, 11) + hour2.toString() + t2.slice(13, 19);
                    } else {
                        t2 = t2.slice(0, 12) + hour2.toString() + t2.slice(13, 19);
                    }
                    for (var i = 0; i < rowParamLength; i++) {
                        if (rowParameters.length >= 1) {
                            dataOriginV = JSON.stringify(rowParameters[i]);
                        } else {
                            dataOriginV = JSON.stringify(rowParameters);
                        }
                        index = i;
                        if (typicaltrend == "Yes" && (typicaltrend == null || typicaltrend == '' || trendDate == null || trendDate == '')) {
                            typicaltrend = ' ';
                        }
                        $.ajax({
                            url: "../controllers/aggregationSeriesProxy.php",
                            type: "POST",
                            data:
                                {
                                    dataOrigin: dataOriginV,
                                    upperTime: t2,
                                    lowerTime: t1,
                                    index: i,
                                    trendtype: trendType,
                                    trenddate: trendDate,
                                    tttdate: TTTDate,
                                    computationType: computationType,
                                    field: dataOriginV.smField,
                                    timeRange: localTimeRange,
                                    typicaltrend: typicaltrend
                                },
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data.index != null) {
                                    aggregationGetData[data.index] = data;
                                    if (data.metricHighLevelType == "Sensor" || data.metricHighLevelType == "IoT Device Variable" || data.metricHighLevelType == "Data Table Variable" || data.metricHighLevelType == "Mobile Device Variable") {
                                        if (data.data == null || JSON.parse(data.data).realtime == null || JSON.parse(data.data).realtime.results == null) {
                                            if (errorsLog != null) {
                                                errorsLog = errorsLog + "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                            } else {
                                                errorsLog = "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                            }
                                        } else {
                                            errorsLog = data.result + "; ";
                                        }
                                    } else if (data.metricHighLevelType == "MyKPI") {
                                        if (data.data != null) {
                                            if (JSON.parse(data.data).length == 0) {
                                                if (errorsLog != null) {
                                                    errorsLog = errorsLog + "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                                } else {
                                                    errorsLog = "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                                }
                                            } else {
                                                errorsLog = data.result + "; ";
                                            }
                                        }
                                    }
                                } else {
                                    if (errorsLog != null) {
                                        errorsLog = errorsLog + " " + data.result + "; ";
                                    } else {
                                        errorsLog = data.result + "; ";
                                    }
                                }
                                getDataFinishCount++;
                                var deviceLabels = [];
                                var metricLabels = [];
                                var LabelInterval = null;

                                //    if (JSON.parse(data.data).length !== 0) {
                                if (typicaltrend == 'Yes') {
                                    if (JSON.parse(data.data).length !== 0) {
                                        LabelInterval = JSON.parse(data.data)[0].deviceName + " - " + JSON.parse(data.data)[0].valueName + " - " + trendType + ':  ' + JSON.parse(data.data)[0].from + ' --> ' + JSON.parse(data.data)[0].to + '(' + JSON.parse(data.data)[0].computationType + ')';
                                    }
                                }
                                //Popoliamo il widget quando sono arrivati tutti i dati
                                if (getDataFinishCount === rowParamLength) {
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
                                    for (let n = 0; n < chartSeriesObject.length; n++) {
                                        if (chartSeriesObject[n] != null) {
                                            if (chartSeriesObject[n].data.length > 0) {
                                                drawFlag = true;
                                            }
                                        }
                                    }
                                    if (drawFlag === true) {
                                        drawDiagram(true, xAxisFormat, yAxisType, LabelInterval);
                                    } else {
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                        $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                        //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                        if (errorsLog != null) {
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText').text(errorsLog);
                                        } else {
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText').text("No Data Available in the Selected Time-Range.");
                                        }
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                    if (timeNavCount < 0) {
                                        if (moment(upperTime).isBefore(moment(dataFut))) {

                                        } else {
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                        }
                                    }
                                    if (typicaltrend == 'Yes') {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton").show();
                                    }
                                }
                                /*  } else{
                                    showWidgetContent(widgetName);
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                //   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                            }*/
                            },
                            error: function (errorData) {
                                console.log(errorData);
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

                } else {
                    localStorage.setItem(widgetName, JSON.stringify(rowParameters));

                    for (var i = 0; i < rowParamLength; i++) {
                        aggregationGetData[i] = false;
                    }
                    var metridId = '';
                    var fromDate = '';
                    for (var i = 0; i < rowParamLength; i++) {
                        //yyyy-mm-ddThh:mm:ss
                        metridId = rowParameters[i].metricId;

                        if ((dateChoice != null) && (dateChoice != '') && !timeNavigationButtonClick) {
                            fromDate = '';
                            var date = new Date(dateChoice);
                            var y = date.getFullYear();
                            var m = date.getMonth() + 1;
                            if (m < 10) {
                                m = '0' + m;
                            }
                            var d = date.getDate();
                            if (d < 10) {
                                d = '0' + d;
                            }
                            var h = date.getHours();
                            if (h < 10) {
                                h = '0' + h;
                            }
                            var s = date.getMinutes();
                            if (s < 10) {
                                s = '0' + s;
                            }
                            upperTime = y + '-' + m + '-' + d + 'T' + h + ':' + s + ':00';
                            //console.log('fromDate: '+fromDate);
                            //rowParameters[i].metricId = metridId +'&toTime='+fromDate;
                        } else {
                            upperTime = getUpperTimeLimit(localTimeRange, timeCount);
                        }

                        if (rowParamLength >= 1) {
                            dataOriginV = JSON.stringify(rowParameters[i]);
                        } else {
                            dataOriginV = JSON.stringify(rowParameters);
                        }
                        index = i;
                        if (typicaltrend == "Yes" && (typicaltrend == null || typicaltrend == '' || trendDate == null || trendDate == '')) {
                            typicaltrend = ' ';
                        }
                        $.ajax({
                            url: "../controllers/aggregationSeriesProxy.php",
                            type: "POST",
                            data:
                                {
                                    dataOrigin: dataOriginV,
                                    index: i,
                                    timeRange: localTimeRange,
                                    field: rowParameters[i].smField,
                                    upperTime: upperTime,
                                    // lowerTime: t1,
                                    typicaltrend: typicaltrend,
                                    trendtype: trendType,
                                    trenddate: trendDate,
                                    tttdate: TTTDate,
                                    computationType: computationType
                                },
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data.index != null) {
                                    aggregationGetData[data.index] = data;
                                    if (data.metricHighLevelType == "Sensor" || data.metricHighLevelType == "IoT Device Variable" || data.metricHighLevelType == "Data Table Variable" || data.metricHighLevelType == "Mobile Device Variable") {
                                        if (data.data == null || JSON.parse(data.data).realtime == null || JSON.parse(data.data).realtime.results == null) {
                                            if (errorsLog != null) {
                                                errorsLog = errorsLog + "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                            } else {
                                                errorsLog = "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                            }
                                        } else {
                                            errorsLog = data.result + "; ";
                                        }
                                    } else if (data.metricHighLevelType == "MyKPI") {
                                        if (data.data != null) {
                                            if (JSON.parse(data.data).length == 0) {
                                                if (errorsLog != null) {
                                                    errorsLog = errorsLog + "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                                } else {
                                                    errorsLog = "No Data Available in the Selected Time-Range for: " + data.label + "; ";
                                                }
                                            } else {
                                                errorsLog = data.result + "; ";
                                            }
                                        }
                                    }
                                } else {
                                    if (errorsLog != null) {
                                        errorsLog = errorsLog + " " + data.result + "; ";
                                    } else {
                                        errorsLog = data.result + "; ";
                                    }
                                }
                                getDataFinishCount++;
                                var deviceLabels = [];
                                var metricLabels = [];
                                var LabelInterval = null;

                                //    if (JSON.parse(data.data).length !== 0) {
                                if (typicaltrend == 'Yes') {
                                    if (JSON.parse(data.data).length !== 0) {
                                        LabelInterval = JSON.parse(data.data)[0].deviceName + " - " + JSON.parse(data.data)[0].valueName + " - " + trendType + ':  ' + JSON.parse(data.data)[0].from + ' --> ' + JSON.parse(data.data)[0].to + '(' + JSON.parse(data.data)[0].computationType + ')';
                                    }
                                }
                                //Popoliamo il widget quando sono arrivati tutti i dati
                                if (getDataFinishCount === rowParamLength) {
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
                                    //
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
                                    for (let n = 0; n < chartSeriesObject.length; n++) {
                                        if (chartSeriesObject[n] != null) {
                                            if (chartSeriesObject[n].data.length > 0) {
                                                drawFlag = true;
                                            }
                                        }
                                    }
                                    if (drawFlag === true) {
                                        drawDiagram(true, xAxisFormat, yAxisType, LabelInterval);
                                    } else {
                                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                        $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                        //    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                        if (errorsLog != null) {
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText').text(errorsLog);
                                        } else {
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText').text("No Data Available in the Selected Time-Range.");
                                        }
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                    if (timeNavCount < 0) {
                                        if (moment(upperTime).isBefore(moment(dataFut))) {

                                        } else {
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                        }
                                    }
                                    if (typicaltrend == 'Yes') {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton").show();
                                    }
                                }
                                /*  } else{
                                      showWidgetContent(widgetName);
                                      $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                    //   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }*/
                            },
                            error: function (errorData) {
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

        // $("#" + widgetName + "_timeTrendPrevBtn").off("click");
        $("#" + widgetName + "_timeTrendPrevBtn").on("click").click(function () {
            //  alert("PREV Clicked!");
            timeNavigationButtonClick = true;
            timeNavCount++;
            errorsLog = null;
            if(timeNavCount == 0) {

                if (idMetric === 'AggregationSeries' || nrMetricType != null) {
                    populateWidget(true, timeRange, "minus", timeNavCount);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                }

                //   if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeServiceUri(rowParameters[k].serviceUri);
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
                                             //   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                        //    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                         //   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
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
                if (idMetric === 'AggregationSeries' || nrMetricType != null) {
                    populateWidget(true, timeRange, "minus", timeNavCount);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                }
            }
            timeNavigationButtonClick = false;
        });

        //$("#" + widgetName + "_timeTrendNextBtn").off("click");
        $("#" + widgetName + "_timeTrendNextBtn").on("click").click(function () {
            //   alert("NEXT Clicked!");
            timeNavigationButtonClick = true;
            timeNavCount--;
            errorsLog = null;
            if(timeNavCount == 0) {

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                if(idMetric === 'AggregationSeries' || nrMetricType != null)
                {
                    populateWidget(true, timeRange, "plus", timeNavCount);
                }
                else
                {
                    populateWidget(false, null, "plus", timeNavCount);
                }

                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeServiceUri(rowParameters[k].serviceUri);
                        field = rowParameters[k].smField;
                        if (rowParameters != null) {
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
                                         //   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    } else {
                                        showWidgetContent(widgetName);
                                     //   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                     //   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
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
            } else {

                setupLoadingPanel(widgetName, widgetContentColor, true);
                if (idMetric === 'AggregationSeries' || nrMetricType != null) {
                    populateWidget(true, timeRange, "plus", timeNavCount);
                } else {
                    populateWidget(false, null, "plus", timeNavCount);
                }
            }
            timeNavigationButtonClick = false;
        });

        $("#" + widgetName + "_datepicker").hide();

        $("#" + widgetName + "_calendarBtn").off("click").click(function () {
            $("#" + widgetName + "_datepicker").children(0).css("background-color", "#ffffff");
            $("#" + widgetName + "_datepicker").find("table").css("background-color", "#ffffff");
            $("#" + widgetName + "_datepicker").show();
        });

        $("#" + widgetName + "_datepicker").datepicker({
            dateFormat: 'yyyy-mm-dd',
            onSelect: function (dateText, inst) {

                if (inst.currentMonth > 8 && parseInt(inst.currentDay) > 9) {
                    var dateAsString = inst.currentYear + '-' + parseInt(inst.currentMonth + 1) + '-' + inst.currentDay;
                } else {
                    if (inst.currentMonth > 8 && parseInt(inst.currentDay) < 10) {
                        var dateAsString = inst.currentYear + '-' + parseInt(inst.currentMonth + 1) + '-0' + inst.currentDay;
                    } else {
                        if (inst.currentMonth < 9 && parseInt(inst.currentDay) > 9) {
                            var dateAsString = inst.currentYear + '-0' + parseInt(inst.currentMonth + 1) + '-' + inst.currentDay;
                        } else {

                            var dateAsString = inst.currentYear + '-0' + parseInt(inst.currentMonth + 1) + '-0' + inst.currentDay;
                        }
                    }
                }
                trendDate = dateAsString;
                TTTDate = "";
                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_"))
                {
                    //    rowParameters = JSON.parse(rowParameters);
                    //   timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, "plus", timeNavCount);
                    //    populateWidget(true, timeRange);
                } else
                {
                    populateWidget(false, null, "plus", timeNavCount);
                    //    populateWidget(false, null);

                }
                $("#" + widgetName + "_datepicker").hide();
            }
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
                idMetric = widgetData.params.id_metric;
                typicaltrend = widgetData.params.TypicalTimeTrend;
                trendType = widgetData.params.TrendType;
                trendDate = widgetData.params.ReferenceDate;
                TTTDate = widgetData.params.TTTDate;
                dayhourview = widgetData.params.dayhourview;
                computationType = widgetData.params.computationType;
				code = widgetData.params.code;

                if (nrMetricType != null) {
                    openWs();
                }

                if (infoJson === "fromTracker" && fromGisExternalContent != true) {
                    $("#" + widgetName + "_timeControlsContainer").hide();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                    $("#" + widgetName + "_calendarContainer").hide();
                } else {
                    $("#" + widgetName + "_timeControlsContainer").show();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                    if (typicaltrend == 'Yes') {
                        $("#" + widgetName + "_calendarContainer").show();
                    } else {
                        $("#" + widgetName + "_calendarContainer").hide();
                    }
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
                    xAxisFormat = styleParameters.xAxisFormat;
                    yAxisType = styleParameters.yAxisType;
                }

                //////lettura code
                if (styleParameters.enableCKEditor && styleParameters.enableCKEditor == "ckeditor" && code != null && code != "null") {
                    var text_ck_area = document.createElement("text_ck_area");
                    text_ck_area.innerHTML = code;
                    var newInfoDecoded = text_ck_area.innerText;
                    newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");

                    var elem = document.createElement('script');
                    elem.type = 'text/javascript';
                    elem.innerHTML = newInfoDecoded;
                    try {
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                    } catch(e) {
                        console.log("Error in appending JS function to DOM on " + widgetName);
                    }
                    //

                    //
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

                if (timeRange == null || timeRange == undefined) {
                    timeRange = widgetData.params.temporal_range_w;
                }

                if(idMetric === 'AggregationSeries' || nrMetricType != null)
                {
                    if (rowParameters) {
                        if(typeof rowParameters == 'string')
                            rowParameters = JSON.parse(rowParameters);
                        if (typicaltrend == 'Yes') {
                            if (rowParameters != null && rowParameters.length > 1) {
                                rowParameters.splice(1, rowParameters.length - 1);
                            }
                            if (trendType == 'dayHour') {
                                //rowParameters = rowParameters.replace('[', '');
                                //rowParameters = rowParameters.replace(']', '');
                                //var row = '[';
                                //for (var i = 0; i < 7; i++) {
                                //    if (i < 6) {
                                //        row += rowParameters + ', ';
                                //    } else {
                                //        row += rowParameters + ']';
                                //    }
                                //}
                                //rowParameters = row;
                                for (var k = 1; k < 7; k++) {
                                    rowParameters.push(rowParameters[k - 1]);
                                }
                            }
                        }

                        timeRange = widgetData.params.temporal_range_w;
                        populateWidget(true, timeRange, null, timeNavCount);
                    }
                }
                else
                {
                    populateWidget(false, null, null, timeNavCount);
                }

                // Hide Next Button at first instantiation
                if(timeNavCount == 0) {
                    if (rowParameters != null) {
                        for (let k = 0; k < rowParameters.length; k++) {
                            if (rowParameters[k].metricHighLevelType == "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                                let urlKBToBeCalled = "";
                                let field = "";
                                let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                                urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + encodeServiceUri(rowParameters[k].serviceUri);
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
                                             //   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
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
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                    $("#" + widgetName + "_calendarContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_calendarButton').css("padding-right", "0px");
                }
								///////////
				if (styleParameters.calendarM){
					if (styleParameters.calendarM == 'yes'){
					$('#<?= $_REQUEST['name_w'] ?>_datetimepicker_cotainer').show();
					}else{
						$('#<?= $_REQUEST['name_w'] ?>_datetimepicker_cotainer').hide();
					}		
				}else{
					$('#<?= $_REQUEST['name_w'] ?>_datetimepicker_cotainer').hide();					
				}
				////////////////

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
                        if(idMetric === 'AggregationSeries' || nrMetricType != null)
                        {
                        //    rowParameters = JSON.parse(rowParameters);
                            if (typicaltrend == 'Yes') {
                                if (rowParameters != null && rowParameters.length > 1) {
                                    rowParameters.splice(1, rowParameters.length - 1);
                                }
                                if (trendType == 'dayHour') {
                                    //rowParameters = rowParameters.replace('[', '');
                                    //rowParameters = rowParameters.replace(']', '');
                                    //var row = '[';
                                    //for (var i = 0; i < 7; i++) {
                                    //    if (i < 6) {
                                    //        row += rowParameters + ', ';
                                    //    } else {
                                    //        row += rowParameters + ']';
                                    //    }
                                    //}
                                    //rowParameters = row;
                                    for (var k = 1; k < 7; k++) {
                                        rowParameters.push(rowParameters[k - 1]);
                                    }
                                }
                            }

                        //    timeRange = widgetData.params.temporal_range_w;
                            populateWidget(true, timeRange, null, timeNavCount);
                        }
                        else
                        {
                            populateWidget(false, null, null, timeNavCount);
                        }

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

        $("#<?= $_REQUEST['name_w'] ?>").off('changeTimeRangeEvent');
        $("#<?= $_REQUEST['name_w'] ?>").on('changeTimeRangeEvent', function(event){
            timeRange = event.newTimeRange;
            populateWidget(true, event.newTimeRange, null, 0);
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
				//dateChoice = $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').val();
                //timeNavCount = 0;
                timeNavCount = getChoiceTimeNavCount(dateChoice, timeRange);
                if(timeNavCount != 0) {
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                }
                populateWidget(true, timeRange, null, timeNavCount);
                set_time(date);
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
		///////////////////
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
		<!-- -->
		<div style="position: relative;">
		 <div class="widget-dropbdown" style="position: absolute; max-width: 33%; width: auto">
                                <div style="float: left;width: 20%; padding-left: 5%">
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_droptitle' class='dropdown-title title pointerCursor'></div>
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_options' class='menu pointerCursor hide'></div>

                        </div>
								<div id='<?= $_REQUEST['name_w'] ?>_datetimepicker_cotainer' class ="form-group" style="float: left;width:100%;" hidden>  
                                <div class ='input-group date' id='<?= $_REQUEST['name_w'] ?>_datetimepicker' data-date-container='#<?= $_REQUEST['name_w'] ?>_datetimepicker_cotainer'>
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